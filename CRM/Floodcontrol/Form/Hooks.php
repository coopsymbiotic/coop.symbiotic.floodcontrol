<?php

use CRM_Floodcontrol_ExtensionUtil as E;

class CRM_Floodcontrol_Form_Hooks {

  static protected $_protected_forms = [
    'CRM_Contribute_Form_Contribution_Main',
  ];

  /**
   * Generates the cache 'path' (key) for storing the information
   * on the form request. Currently uses a combination of the formName,
   * form ID and IP address.
   *
   * FIXME: it might be better to use the form qfKey if available?
   * All QuickForm forms have one and would better identify visitors,
   * especially if still using legacy IPv4 or proxy on a large network.
   */
  public static function getFormCachePath($formName, &$form) {
    $id = $form->getVar('_id');
    $cache_path = $formName . '_' . $id . '_' . ip_address();

    return $cache_path;
  }

  /**
   * Store the timestamp/user in the cache during form load.
   *
   * Called from @floodcontrol_civicrm_buildForm().
   */
  public static function buildForm($formName, &$form) {
    if (in_array($formName, self::$_protected_forms)) {
      $cache_path = self::getFormCachePath($formName, $form);
      $time = time();

      $data = CRM_Core_BAO_Cache::getItem('floodcontrol_contribution', $cache_path);

      if (empty($data)) {
        $data = [
          'time' => $time,
          'count' => 0,
        ];
      }

      CRM_Core_BAO_Cache::setItem($data, 'floodcontrol_contribution', $cache_path);
    }
  }

  /**
   * Validate that the form has been loaded at least 30 seconds ago.
   * If not, block the user for 10 seconds.
   *
   * Assuming it takes at least 10 seconds to fill in the simplest form, block
   * 10 seconds, reload and resubmit, a very caffeinated user should not be
   * stuck more than once (the 30 seconds starts from the initial form load and
   * is not reset when there are form errors).
   * 
   * FIXME: Make the 30 seconds requirement configurable?
   * FIXME: Make the 10 seconds timeout configurable? 
   *
   * Called from @floodcontrol_civicrm_validateForm().
   */
  public static function validateForm($formName, &$fields, &$files, &$form, &$errors) {
    if (in_array($formName, self::$_protected_forms)) {
      $cache_path = self::getFormCachePath($formName, $form);
      $time = time();

      $data = CRM_Core_BAO_Cache::getItem('floodcontrol_contribution', $cache_path);

      if ($time - $data['time'] < 30) {
        // Simulate a timeout and slow down the attacker
        sleep(10);

        $errors['qfKey'] = E::ts('There was a timeout while processing your request. Please wait a few seconds and try again.');

        // Increase before the reporterror email, because by default it's 0 anyway.
        $data['count']++;
        CRM_Core_BAO_Cache::setItem($data, 'floodcontrol_contribution', $cache_path);

        // If the 'reporterror' extension is enabled, send an email to admins.
        // This can be useful to detect false-positives.
        if (function_exists('reporterror_civicrm_handler')) {
          $variables = [
            'message' => 'floodcontrol',
            'body' => $data['count'] . ' attempts since @' . date('Y-m-d H:i:s', $data['time']),
          ];

          reporterror_civicrm_handler($variables);
        }
      }
      else {
        CRM_Core_BAO_Cache::deleteGroup('floodcontrol_contribution', $cache_path);
      }
    }
  }

}
