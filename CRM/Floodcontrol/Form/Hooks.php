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
   */
  public static function getFormCachePath($formName, &$form) {
    $id = $form->getVar('_id');
    $cache_path = $formName . '_' . $id . '_' . ip_address();

    return $cache_path;
  }

  /**
   * If enabled in the settings, adds reCaptcha on the form.
   */
  public static function addCaptchaOnErrors(&$form) {
    $add_captcha = Civi::settings()->get('floodcontrol_recaptcha');

    if (!$add_captcha) {
      return;
    }

    self::addCaptcha($form);
  }

  /**
   * Enables reCaptcha.
   */
  public static function addCaptcha(&$form) {
    $captcha = CRM_Utils_ReCAPTCHA::singleton();
    $captcha->add($form);
    $form->assign('isCaptcha', TRUE);
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
          'fail' => 0,
          'success' => 0,
        ];
      }

      CRM_Core_BAO_Cache::setItem($data, 'floodcontrol_contribution', $cache_path);

      // Enable reCaptcha
      // buildForm() is called before validate(), so this tends to kick-in after
      // the expected cut-off (if max_count_recaptcha=1, it will start on the 2nd submit)
      // but at least it's more obvious later when the visitor returns, the captcha
      // will be there on the first form load.
      $max_count_recaptcha = Civi::settings()->get('floodcontrol_max_success_recaptcha');

      if ($max_count_recaptcha && $data['success'] >= $max_count_recaptcha) {
        Civi::log()->warning("floodcontrol: enabling captcha [{$data['success']} success of $max_count_recaptcha]");
        self::addCaptcha($form);
      }
    }
  }

  /**
   * Validate that the form has been loaded at least X seconds ago.
   * If not, block the user for Y seconds.
   *
   * Assuming it takes at least X seconds to fill in the simplest form, block
   * 10 seconds, reload and resubmit, a very caffeinated user should not be
   * stuck more than once (the X seconds starts from the initial form load and
   * is not reset when there are form errors).
   *
   * Called from @floodcontrol_civicrm_validateForm().
   */
  public static function validateForm($formName, &$fields, &$files, &$form, &$errors) {
    if (!in_array($formName, self::$_protected_forms)) {
      return;
    }

    $minimum_seconds_before_post = Civi::settings()->get('floodcontrol_minimum_seconds_before_post');

    if (!$minimum_seconds_before_post) {
      return;
    }

    $cache_path = self::getFormCachePath($formName, $form);
    $time = time();

    $data = CRM_Core_BAO_Cache::getItem('floodcontrol_contribution', $cache_path);
    $seconds_since_first_load = $time - $data['time'];

    if ($seconds_since_first_load < $minimum_seconds_before_post) {
      Civi::log()->warning("floodcontrol: " . ip_address() . " FAIL $formName [{$data['fail']}], filled in $seconds_since_first_load seconds");

      // Simulate a timeout and slow down the attacker
      $slowdown = Civi::settings()->get('floodcontrol_delay_spammers_seconds');

      if ($slowdown) {
        sleep($slowdown);
      }

      $errors['qfKey'] = E::ts('There was a timeout while processing your request. Please wait a few seconds and try again.');

      // Increase before the reporterror email, because by default it's 0 anyway.
      $data['fail']++;
      CRM_Core_BAO_Cache::setItem($data, 'floodcontrol_contribution', $cache_path);

      // If the 'reporterror' extension is enabled, send an email to admins.
      // This can be useful to detect false-positives.
      if (function_exists('reporterror_civicrm_handler')) {
        $variables = [
          'message' => 'floodcontrol-1 ' . ip_address(),
          'body' => $data['fail'] . ' attempts since @' . date('Y-m-d H:i:s', $data['time']) . ' (' . $seconds_since_first_load . ')',
        ];

        reporterror_civicrm_handler($variables);
      }

      self::addCaptchaOnErrors($form);
    }
    else {
      $data['success']++;
      CRM_Core_BAO_Cache::setItem($data, 'floodcontrol_contribution', $cache_path);

      $max_count = Civi::settings()->get('floodcontrol_max_success_count');
      $max_period = Civi::settings()->get('floodcontrol_max_success_period');

      if (!$max_count || !$max_period) {
        Civi::log()->info("floodcontrol: " . ip_address() . " PASS [{$data['success']}] $formName, filled in $seconds_since_first_load seconds");
        return;
      }

      // If the visitor has succeeded more than X time without Y seconds.
      if ($data['success'] > $max_count && $seconds_since_first_load < $max_period) {
        Civi::log()->warning("floodcontrol: " . ip_address() . " FAIL $formName [{$data['fail']}], blocked because {$data['success']} success in $seconds_since_first_load seconds");
        $errors['qfKey'] = E::ts('There was a timeout while processing your request. Please wait a few seconds and try again.');

        // If the 'reporterror' extension is enabled, send an email to admins.
        // This can be useful to detect false-positives.
        if (function_exists('reporterror_civicrm_handler')) {
          $variables = [
            'message' => 'floodcontrol-2 ' . ip_address(),
            'body' => 'Blocked after ' . $data['success'] . ' attempts since @' . date('Y-m-d H:i:s', $data['time']) . ' (' . $seconds_since_first_load . ')',
          ];

          reporterror_civicrm_handler($variables);
        }

        // Simulate a timeout and slow down the attacker
        $slowdown = Civi::settings()->get('floodcontrol_delay_spammers_seconds');

        if ($slowdown) {
          sleep($slowdown);
        }

        self::addCaptchaOnErrors($form);

        return;
      }

      if ($seconds_since_first_load > $max_period) {
        // The period expired, reset floodcontrol
        // Ex: say we set max 5 attempts in 120 seconds,
        // the spammer tries 3 times within 250 seconds, then gets whitelisted forever,
        // since the 'time' of first load doesn't get reset.
        CRM_Core_BAO_Cache::deleteGroup('floodcontrol_contribution', $cache_path);
      }

      Civi::log()->info("floodcontrol: " . ip_address() . " PASS [{$data['success']}] $formName, filled in $seconds_since_first_load seconds");
    }
  }

}
