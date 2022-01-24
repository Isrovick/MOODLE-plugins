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
$PAGE->set_url("$CFG->wwwroot/local/promo/restablecerm.php");
$PAGE->set_context($context);
$PAGE->requires->jquery();
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Restablecido de cursos');
$PAGE->set_heading('Restablecido de cursos');

echo $OUTPUT->header();

require_once('forms.php');

$mform = new remocion_form();
 
//Form processing and displaying is done here
if ($mform->is_cancelled()) {
  //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
  
  //In this case you process validated data. $mform->get_data() returns data posted in form.
  $data = new stdClass();
     $data->reset_start_date= 0;
     $data->reset_end_date= 0;
     $data->reset_events= 1;
     $data->reset_notes= 1;
     $data->reset_comments= 1;
     $data->reset_completion= 1;
     $data->delete_blog_associations= 1;
     $data->reset_competency_ratings= 1;
     $data->unenrol_users= array(0,5);
     //$data->reset_roles_overrides= 1;
     //$data->reset_gradebook_items= 1;
     //$data->reset_groups_remove= 1;
     //$data->reset_groupings_remove= 1;
     $data->reset_forum_all= 1;
     $data->reset_forum_digests= 1;
     $data->reset_forum_subscriptions= 1;
     
     $info = array();
     
    if($fromform->materias_id >0){
        $courses=$DB->get_records_sql("SELECT id FROM bch_course where category=".$fromform->materias_id, null, 0, 0);
        foreach ($courses as &$key) {
            $data->id=$key->id;
            $status = reset_course_userdata($data);
        }
        
        $info[]=array("id","curso",$key->id);
        
        foreach ($status as $item) {
          $line = array();
          $line[] = $item['component'];
          $line[] = $item['item'];
          $line[] = ($item['error']===false) ? get_string('ok') : '<div class="notifyproblem">'.$item['error'].'</div>';
          $info[] = $line;
        }
    }
     else if($fromform->materias_id ==0){
        
      $courses=$DB->get_records_sql("SELECT id FROM bch_course where category!=0", null, 0, 0);
        foreach ($courses as &$key) {
            $data->id=$key->id;
            $status = reset_course_userdata($data);
        }
        
        $info[]=array("id","curso",$key->id);
        
        foreach ($status as $item) {
          $line = array();
          $line[] = $item['component'];
          $line[] = $item['item'];
          $line[] = ($item['error']===false) ? get_string('ok') : '<div class="notifyproblem">'.$item['error'].'</div>';
          $info[] = $line;
        }
     }

   
        $table = new html_table();
        $table->head  = array(get_string('resetcomponent'), get_string('resettask'), get_string('resetstatus'));
        $table->size  = array('20%', '40%', '40%');
        $table->align = array('left', 'left', 'left');
        $table->width = '80%';
        $table->data  = $info;
        echo html_writer::table($table);

        echo $OUTPUT->continue_button('restablecerm.php'); 
    

} else {
  
  $mform->display();
  echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/cancel.js"></script>';

}

echo $OUTPUT->footer();