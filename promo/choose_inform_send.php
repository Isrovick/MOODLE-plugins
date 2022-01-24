<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     local_promo
 * @copyright   2019 MSINTEC <msintec.company@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require('../../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();

require_login(0, false);

require_once('forms.php');
$PAGE->requires->jquery();

$context = context_system::instance();

if(!has_capability('mod/infoprofe:sendemails', $context)){
  http_response_code(404);
  die(); 
}

$PAGE->set_url("$CFG->wwwroot/local/promo/choose_inform_send.php");
$PAGE->set_context($context);

$PAGE->set_pagelayout('standard');
$PAGE->set_title('Enviado de informes');
$PAGE->set_heading('Enviado de informes');
$PAGE->requires->jquery();

echo $OUTPUT->header();

//include simplehtml_form.php
 
//Instantiate simplehtml_form 
  $mform = new send_category_form(new moodle_url("/local/infoprofe/inform_primini.php"));
  
  //Form processing and displaying is done here
  if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
  
    //Set default data (if any)
    //$mform->set_data($toform);
    //displays the form
    $mform->display();
    echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/promo/js/form_inform.js"></script>';
    echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/cancel.js"></script>';

  }

echo $OUTPUT->footer();