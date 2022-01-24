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

$PAGE->set_url("$CFG->wwwroot/local/promo/desmatricular.php");
$PAGE->set_context($context);
$PAGE->requires->jquery();
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Restablecer matricula de alumno');

$PAGE->set_heading('Anular matricula: Visualizuacion de alumnos.');
echo $OUTPUT->header();

require_once('forms.php');
 

  $mform = new desmatricular_form();
//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
 
  echo '<form action="'."$CFG->wwwroot/local/promo/desmatricular_materias.php".'" method="post">'; 
 
  foreach($_POST as $l =>  $userid) {
    if(strpos($l, 'userid_') === 0) {
      $user=user_inf($userid);
      echo '<h5>El alumno <a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.SITEID.'">'.$user->fullname.', '.$user->cedula.'</a></h5>';
      echo '<h6>'.$user->nivel.', Seccion '.$user->seccion.' - Sera desmatriculado de las siguientes materias:</h5>';
      
      $table = new html_table();
      $table->width = "95%";
      $columns = array('Materia','Seleccionar');
      foreach ($columns as $column) {
        $strtitle = $column;
        $columnicon = ' ';
        $table->head[] = '<a href="">'.$strtitle.'</a>'.$columnicon;
        $table->align[] = 'left';
      }
      
      foreach($user->courses as &$course){
        $table->data[] = array (
          '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->summary.'</a>',
          '<input type="checkbox" name="uid_'.$user->id.'_cid'.$course->id.'" value="'.$user->id.'_'.$course->id.'" checked >');
      } 
      echo html_writer::table($table);
      echo " <br>  ";
    }
  
}
echo '<input type="submit" value="Desmatricular">';
echo " </form> "; 


} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.
 
  //Set default data (if any)
  //$mform->set_data($toform);
  //displays the form
  $mform->display();
  echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/promo/js/form_desmatr.js"></script>';
  echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/cancel.js"></script>';
}

echo $OUTPUT->footer();

function user_inf($id){
    global $DB;
    
    $user = $DB->get_record('user', array('id'=>$id), 'id,firstname,lastname');
    $user->fullname = $user->lastname.', '.$user->firstname;
    $pur=profile_user_record($id);
    $user->cedula=$pur->cedula;
    $user->seccion=$pur->seccion; 
    $user->nivel=$pur->nivel; 
    $user->courses=courses_inf($id);
    //echo ("<br><br>");
    //var_dump($user);

    return $user;
  
}

function courses_inf($id){
    global $DB;
    
    $courses = $DB->get_records_sql('select id, summary from bch_course where id in 
                                    (SELECT courseid FROM bch_enrol where id in
                                    (SELECT enrolid FROM bch_user_enrolments where userid='.$id.'))');

    //var_dump($courses);
    return $courses;
  
}