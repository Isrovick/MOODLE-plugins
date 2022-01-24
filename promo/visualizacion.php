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

$context = context_system::instance();
$PAGE->set_url("$CFG->wwwroot/local/promo/visualizacion.php");
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('visualizacion de Matriculacion');
$PAGE->set_heading('visualizacion de Matriculacion');
$PAGE->requires->jquery();
echo $OUTPUT->header();

//var_dump($_POST);
if(isset($_POST['materias_id']) && isset($_POST['startime'])){
    
    
    $m=$_POST['materias_id'];
    $t=$_POST['startime'];
        echo '<form action="'."$CFG->wwwroot/local/promo/vismatricular.php".'" method="post">'; 
        echo '<h4>Las siguientes materias seran vistas:</h4><br/>';

        $courses=$DB->get_records_sql("SELECT id FROM bch_course where category=".$m, null, 0, 0);

        foreach ($courses as &$key) {
          $course = $DB->get_record('course', array('id'=>$key->id), 'id, shortname, fullname, summary');
          $courses[$key->id] = $course;
        }

        $table = new html_table();
        $table->width = "95%";
        $columns = array('Materia', 'Nombre Completo', 'Seleccionar');

        foreach ($columns as $column) {
          $strtitle = $column;
          $table->head[] = $strtitle;
          $table->align[] = 'left';
        }

        foreach($courses as $course) {

          $table->data[] = array (
              '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'>' .$course->shortname.'</a>',
              $course->fullname,
              $course->summary,
              '<input type="checkbox" name="cid_'.$course->id.'" value="'.$course->id.'" checked >'
          );
        }

        echo html_writer::table($table);
        echo '<br><h4>Por los siguientes usuarios:</h4><br/>';
        
        $users=array();
        $lusers=array();

      if( isset($_POST['promo_id'])){
        $p=$_POST['promo_id'];
        $lusers = $DB->get_records_sql("SELECT userid as id FROM bch_cohort_members where cohortid=".$p, null, 0, 0);

      } 
        foreach($_POST as $k => $v) {
          if(strpos($k, 'userid_') === 0) {
            $users[$v] = user_inf($v);
          }
        }
        foreach ($lusers as &$key) {
            $users[$key->id] = user_inf($key->id);
        }

        $table = new html_table();
        $table->width = "95%";
        $columns = array('Nombre Completo','cedula',"seccion", "Matricular");
        foreach ($columns as $column) {
          $strtitle = $column;
          $columnicon = ' ';
          $table->head[] = '<a href="">'.$strtitle.'</a>'.$columnicon;
          $table->align[] = 'left';
        }

       
        foreach($users as $user) {
          $table->data[] = array (
            '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.SITEID.'">'.$user->fullname.'</a>',
            $user->cedula,
            $user->seccion,
            '<input type="checkbox" name="userid_'.$user->id.'" value="'.$user->id.'" checked >'

            

          );
        }
        //var_dump($t);

        echo html_writer::table($table);
        echo '<input type="hidden" name="day" value="'.$t['day'].'">';
        echo '<input type="hidden" name="month" value="'.$t['month'].'">';
        echo '<input type="hidden" name="year" value="'.$t['year'].'">';
        echo '<input type="submit" value="matricular">';
        echo " </form> ";


}

echo $OUTPUT->footer();

function user_inf($id){
  global $DB;
  
  $user = $DB->get_record('user', array('id'=>$id), 'id,firstname,lastname');
  $user->fullname = $user->lastname.', '.$user->firstname;
  $user->cedula=profile_user_record($id)->cedula;
  $user->seccion=profile_user_record($id)->seccion; 

  return $user;

}

