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
 * @package     local_infoprofe
 * @copyright   2019 MSINTEC <msintec.company@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';
require_once('forms.php');
require_once($CFG->dirroot.'/local/clean.php');
$c=new clean($_GET,$_POST);

require_login(0, false);


defined('MOODLE_INTERNAL') || die();
$context = context_system::instance();
if(!has_capability('mod/coframa_menu:modifymetadata', $context)){
  http_response_code(404);
  die(); 
}


$context = context_system::instance();
$PAGE->set_url("$CFG->wwwroot/local/coframa_menu/menumeta.php");
$PAGE->set_context($context);
$PAGE->set_title('Actualizacion Metadata');
$PAGE->set_heading('Actualizacion Metadata');
$PAGE->requires->jquery();
echo $OUTPUT->header();

if(!isset($_GET['mk'])) return;

  $id=$c->g('/([0-9])+/','mk');

  $mform = new meta_form(new moodle_url("/local/coframa_menu/meta_ajax.php"),array('id'=>$id));;
 
  //Form processing and displaying is done here
  if ($mform->is_cancelled()) {
    header("Location: {$_SERVER['HTTP_REFERER']}");
	exit;
      //Handle form cancel operation, if cancel button is present on form
  } else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
    //var_dump($fromform);

  } else {
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
   
    //Set default data (if any)
     //$mform->set_data($toform);
    //displays the form
    $mform->display();
    echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/functions.js"></script>';
    echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/cancel.js"></script>';

  }


echo $OUTPUT->footer();