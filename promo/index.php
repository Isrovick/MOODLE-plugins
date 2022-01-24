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
if(!has_capability('mod/infoprofe:enroll', $context)){
  http_response_code(404);
  die(); 
}
require_login(0, false);

$PAGE->set_url("$CFG->wwwroot/local/promo/index.php");
$PAGE->set_context($context);
$PAGE->requires->jquery();
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Matriculacion de alumnos');
$PAGE->set_heading('Matriculacion de alumnos');
echo $OUTPUT->header();

//include simplehtml_form.php
require_once('forms.php');
 
//Instantiate simplehtml_form 
$mform = new simplehtml_form(new moodle_url("/local/promo/visualizacion.php"));
 
//Form processing and displaying is done here
if ($mform->is_cancelled()) {
  header("Location: {$_SERVER['HTTP_REFERER']}");
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
    //redirect("$CFG->wwwroot/local/promo/visualizacion.php?p=".$fromform->promo_id."&m=".$fromform->materias_id."&d=".$fromform->startime, 'Pronto sera redirigido a la pagina', 0);
  //In this case you process validated data. $mform->get_data() returns data posted in form.
} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.
 
  //Set default data (if any)
  //$mform->set_data($toform);
  //displays the form
  $mform->display();
  echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/promo/js/form_matr.js"></script>';
  echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/cancel.js"></script>';
}

echo $OUTPUT->footer();

