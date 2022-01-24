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

$PAGE->set_url("$CFG->wwwroot/local/promo/desmatricular_materias.php");
$PAGE->set_context($context);

$PAGE->set_pagelayout('standard');
$PAGE->set_title('Restablecer matricula de alumno');
$PAGE->requires->jquery();
$PAGE->set_heading("Proceso de Anulacion de matricula");

echo $OUTPUT->header();
echo $OUTPUT->heading("Los siguientes usuarios fueron desmatriculados de las siguientes materias:");

$table = new html_table();
$table->width = "95%";
$columns = array('Materia','Usuario');
foreach ($columns as $column) {
  $strtitle = $column;
  $columnicon = ' ';
  $table->head[] = '<a href="">'.$strtitle.'</a>'.$columnicon;
  $table->align[] = 'left';
}


foreach($_POST as $k =>  $v) {
  if(strpos($k, 'uid_') === 0) {
    $w = preg_split("/(_)+/", $v); 

      $uid=$w[0];
      $courseid=$w[1];

      $ucontext = context_course::instance($courseid);
      $ue = $DB->get_record_sql('SELECT * FROM bch_user_enrolments where userid ='.$uid.' and enrolid in (SELECT id FROM bch_enrol where courseid='.$courseid.')',null,0,0);
      $instance = $DB->get_record('enrol', array('id'=>$ue->enrolid), '*', MUST_EXIST);
      $plugin = enrol_get_plugin($instance->enrol);

      if (!$plugin->allow_unenrol_user($instance, $ue) or !has_capability("enrol/$instance->enrol:unenrol", $ucontext)) {
        print_error('erroreditenrolment', 'enrol');
      }
      $plugin->unenrol_user($instance, $ue->userid);

      $user = $DB->get_record('user', array('id'=>$uid), 'firstname,lastname');
      $user->fullname = $user->lastname.', '.$user->firstname;
      $course = $DB->get_record_sql('select summary from bch_course where id='.$courseid,null,0,0);

      $table->data[] = array ($user->lastname.', '.$user->firstname.'  '.profile_user_record($uid)->cedula,$course->summary);

  }
}

echo html_writer::table($table);
echo " <br>  ";

echo $OUTPUT->continue_button('desmatricular.php'); 
echo $OUTPUT->footer();