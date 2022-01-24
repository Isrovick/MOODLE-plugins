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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/clean.php');
$c=new clean($_GET,$_POST);
$context = context_system::instance();

if(isset($_GET['id'])){

    $course_id=$c->g('/([0-9])+/','id'); 

    $context = context_course::instance($course_id);
}

else if(isset($_POST['course_id'])){
    $course_id=$c->p('/([0-9])+/','course_id'); 
    $context = context_course::instance($course_id);
}


if(!has_capability('mod/infoprofe:seecage', $context) && !has_capability('mod/infoprofe:seeinform', $context)){
  http_response_code(404);
  die(); 
}
require_login(0, false);

$PAGE->set_url("$CFG->wwwroot/local/infoprofe/index.php");
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Generacion de informes');
$PAGE->set_heading("<h2> Generacion de informes </h2>");
$PAGE->requires->jquery();

echo $OUTPUT->header();

require_once('forms.php');


if(isset($_GET['id']) || isset($_POST['course_id'])){
        
        $course = $DB->get_record('course', array('id'=>$course_id), 'id, shortname, fullname, summary, category');
        $contextid = context_course::instance($course_id);
        $inf_sec=$DB->record_exists_sql("SELECT id FROM bch_tag_instance where contextid=".$contextid->id." and tagid=(SELECT id FROM bch_tag where name='cualitativa')",null);

        
        echo "<h3> ".$course->fullname." </h3>";

        $grupo = $DB->get_record_sql("SELECT name FROM bch_course_categories where id in(select parent from bch_course_categories where id=".$course->category.")",null,0,0);
            $primaria=false;
        if($grupo->name=='Primaria'|| $grupo->name=='Inicial'){
            $primaria=true;
        }
        elseif(!$primaria && !$inf_sec ){
            echo "<br><h5> Esta materia no requiere informe de momento. </h5><br>";
            echo $OUTPUT->footer();
            die();
        }
        
        //$users = $DB->get_records_sql("SELECT distinct userid as id FROM bch_user_enrolments where enrolid in (SELECT enrolid FROM bch_enrol where courseid=".$course_id.")", null, 0, 0);
        $users = $DB->get_records_sql("SELECT id, lastname, firstname FROM bch_user where id in 
                                      (SELECT distinct(userid) FROM bch_role_assignments where roleid=5 and contextid in
                                      (SELECT id FROM bch_context where contextlevel=50 and instanceid=".$course_id."))"
                                      , null, 0, 0);
       
        $usercount = count($users);
      
        $año_esc=$DB->get_record_sql("SELECT id, meta_value FROM `bch_institution_meta` where meta_key='periodo' and meta_date = (SELECT MAX(meta_date) as md FROM `bch_institution_meta` WHERE meta_key='periodo')", null, 0, 0);

        $table = new html_table();
        $table->width = "95%";
        $columns = array('Nombre Completo', 'Cedula','Seccion','Momento 1', 'Momento 2', 'Momento 3');
        
        if($primaria){
            $columns = array('Nombre Completo', 'Cedula','Seccion','Proyecto 1', 'Proyecto 2', 'Proyecto 3');
        }

        foreach ($columns as $column) {
            $strtitle = $column;
            $table->head[] = $strtitle;
            $table->align[] = 'left';
        }
        
        $columnicon = ' ' . $OUTPUT->pix_icon( 't/grades', "Generar Informe de Momento");
        
        foreach($users as $user) {

            $states = array("          ","          ","          "); 
            $data_states = array(   
                'id_period' => $año_esc->id,       
                'id_propietary' => $user->id,          
                'id_course' => $course_id,      
                'id_type'=> (($primaria)?'proyecto':'momento'),             
                'meta_key' => 0 );  
            
            for ($i = 0; $i <3; $i++) {
                $data_states['meta_key']=$i+1;
                $info= $DB->get_field('institution_inform','state',$data_states);
                if($info){
                    $states[$i]=$info; 
                }
            }        
             
            $table->data[] = array (
                '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.SITEID.'">'.$user->lastname.', '.$user->firstname.'</a>',
                profile_user_record($user->id)->cedula,
                profile_user_record($user->id)->seccion,
                '<a href="'.$CFG->wwwroot.'/local/infoprofe/inform_build.php?id='.$user->id.'&amp;course='.$course_id.'&amp;m=1">&emsp;&emsp;'.$columnicon.' '.$states[0].'</a>',
                '<a href="'.$CFG->wwwroot.'/local/infoprofe/inform_build.php?id='.$user->id.'&amp;course='.$course_id.'&amp;m=2">&emsp;&emsp;'.$columnicon.' '.$states[1].'</a>',
                '<a href="'.$CFG->wwwroot.'/local/infoprofe/inform_build.php?id='.$user->id.'&amp;course='.$course_id.'&amp;m=3">&emsp;&emsp;'.$columnicon.' '.$states[2].'</a>'
            );

        }

            echo $OUTPUT->heading("$usercount ".get_string('users'));
            echo html_writer::table($table);
}
    
echo $OUTPUT->footer();