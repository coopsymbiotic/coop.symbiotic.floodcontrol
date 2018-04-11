<?php

use CRM_Floodcontrol_ExtensionUtil as E;

/**
 * Settings metadata file
 */
return [
  'floodcontrol_minimum_seconds_before_post' => [
    'group_name' => 'domain',
    'group' => 'floodcontrol',
    'name' => 'floodcontrol_minimum_seconds_before_post',
    'type' => 'Integer',
    'default' => 0,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Minimum seconds before post'),
    'description' => E::ts('Minimum number of seconds required to wait before submitting the form. If the visitor submits too quickly, they will be asked to try again due to vague timeout issues. Set to 0 to disable.'),
    'help_text' => '',
    'quick_form_type' => 'Element',
    'html_type' => 'Text',
  ],
  'floodcontrol_delay_spammers_seconds' => [
    'group_name' => 'domain',
    'group' => 'floodcontrol',
    'name' => 'floodcontrol_delay_spammers_seconds',
    'type' => 'Integer',
    'default' => 0,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Spammer delay'),
    'description' => E::ts('When a visitor was suspected of spamming, this delay slows down page loading by the given number of seconds. Set to 0 to disable.'),
    'help_text' => '',
    'quick_form_type' => 'Element',
    'html_type' => 'Text',
  ],
  'floodcontrol_max_success_count' => [
    'group_name' => 'domain',
    'group' => 'floodcontrol',
    'name' => 'floodcontrol_max_success_count',
    'type' => 'Integer',
    'default' => 0,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Maximum valid attempts'),
    'description' => E::ts('Maximum valid attempts in a given period. This means that they managed to get through floodcontrol, but if there are other errrors in the form, that would trigger a form reload and add to the number of attempts. It should not be too low. Set to 0 to disable.'),
    'help_text' => '',
    'quick_form_type' => 'Element',
    'html_type' => 'Text',
  ],
  'floodcontrol_max_success_period' => [
    'group_name' => 'domain',
    'group' => 'floodcontrol',
    'name' => 'floodcontrol_max_success_period',
    'type' => 'Integer',
    'default' => 0,
    'add' => '1.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('Maximum valid period'),
    'description' => E::ts('Period (in seconds) for which to define the valid attempts. Set to 0 to disable.'),
    'help_text' => '',
    'quick_form_type' => 'Element',
    'html_type' => 'Text',
  ],
];
