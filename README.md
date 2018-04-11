CiviCRM flood control
=====================

Flood Control is a CiviCRM extension to limit the speed at which some forms
may be submitted. Currently, it mainly aims to protect contribution forms from
credit card fraud.

The implementation is based on a few CiviCRM blog posts and forums, notably this
one by xf33:  
https://civicrm.org/blog/xcf33/civicrm-flood-control-use-case-and-implementation

The strategies used by this extension may change over time. Currently the main
features are:

* Force the visitor to wait 30 seconds before submitting a form (assuming it
  usually takes at least 30 seconds to fill in a few fields, credit card details
  and click submit).
* If a visitor submits too quickly, it will simulate a 10 second timeout and
  invite them to try again (they shouldn't have to wait another 30 seconds,
  since we count from the initial form load time).
* The extension supports the [report error](https://github.com/mlutfy/ca.bidon.reporterror/)
  extension, so that admins can receive an email when someone gets blocked
  (a good way to debug false positives and eventually to block persistent abuse).

Known limitations:

* Only works with CiviCRM 'core' forms, it does not work with third-party
  modules such as 'webform'.

Considering this extension interferes with contribution forms, USE AT YOUR OWN
RISK. If you have persistent abuse/fraud on your contribution forms, contact us
at [Coop SymbioTIC](https://www.symbiotic.coop/en) or lookup a CiviCRM expert
near you (https://civicrm.org/partners-contributors).

To get the latest version of this extension:  
https://github.com/coopsymbiotic/coop.symbiotic.floodcontrol

Distributed under the terms of the GNU Affero General public license (AGPL).
See LICENSE.txt for details.

Installation
------------

Enable this extension in CiviCRM (Administer > System Settings > Manage Extensions).

There are currently limited configuration options for this extension. It enables
itself on all contribution forms.

Some configuration options are available at: /civicrm/admin/setting/floodcontrol?reset=1.


Requirements
------------

- CiviCRM 5.0 or later
- PHP 7 or later

Support
-------

Please post bug reports in the issue tracker of this project on github:  
https://github.com/coopsymbiotic/coop.symbiotic.floodcontrol

Paid support and consulting is available from Coop SymbioTIC:  
https://www.symbiotic.coop/en

Coop SymbioTIC provides fast and reliable turn-key CiviCRM hosting in Canada.

Copyright
---------

License: AGPL 3

Copyright (C) 2017 Mathieu Lutfy (mathieu@symbiotic.coop)  
https://www.symbiotic.coop/en
