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
$context = context_system::instance();
if(!has_capability('mod/infoprofe:changeinformstatus', $context)){
  http_response_code(404);
  die(); 
}

require_login(0, false);

$context = context_system::instance();
$PAGE->set_url("$CFG->wwwroot/local/promo/choose_revision_inform.php");
$PAGE->set_context($context);
$PAGE->requires->jquery();
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Revision Notas e informes');


echo $OUTPUT->header();


//include simplehtml_form.php
require_once('forms.php');
 
//Instantiate simplehtml_form 
  $mform = new revision_category_form(new moodle_url("/local/promo/choose_course_revision_inform.php"));
  
  //Form processing and displaying is done here
  if ($mform->is_cancelled()) {
   //Handle form cancel operation, if cancel button is present on form
   //In this case you process validated data. $mform->get_data() returns data posted in form.
  } else {

    $mform->display();
    echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/cancel.js"></script>';

    
  }

echo $OUTPUT->footer();