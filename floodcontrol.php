<?php

require_once 'floodcontrol.civix.php';
use CRM_Floodcontrol_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function floodcontrol_civicrm_config(&$config) {
  _floodcontrol_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function floodcontrol_civicrm_xmlMenu(&$files) {
  _floodcontrol_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function floodcontrol_civicrm_install() {
  _floodcontrol_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function floodcontrol_civicrm_postInstall() {
  _floodcontrol_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function floodcontrol_civicrm_uninstall() {
  _floodcontrol_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function floodcontrol_civicrm_enable() {
  _floodcontrol_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function floodcontrol_civicrm_disable() {
  _floodcontrol_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function floodcontrol_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _floodcontrol_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function floodcontrol_civicrm_managed(&$entities) {
  _floodcontrol_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function floodcontrol_civicrm_angularModules(&$angularModules) {
  _floodcontrol_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_buildForm().
 */
function floodcontrol_civicrm_buildForm($formName, &$form) {
  CRM_Floodcontrol_Form_Hooks::buildForm($formName, $form);
}

/**
 * Implements hook_civicrm_postProcess().
 */
function floodcontrol_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  CRM_Floodcontrol_Form_Hooks::validateForm($formName, $fields, $files, $form, $errors);
}
