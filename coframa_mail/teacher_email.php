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
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/gdlib.php');
require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';
require_once('forms.php');
require_login(0, false);;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/clean.php');
$c=new clean($_GET,$_POST);

if(!isset($_GET['id'])){ http_response_code(404); exit(); }

$courseid=$c->g('/([0-9])+/','id');

$context = context_course::instance($_GET['id']);

if(!has_capability('mod/coframa_mail:sendteacheremail', $context)){
  http_response_code(404);
  die(); 
}

$PAGE->set_url("$CFG->wwwroot/local/coframa_mail/teacher_email.php");
$PAGE->set_context($context);
$PAGE->set_title('Envio Notificacion');
$PAGE->requires->jquery();
$PAGE->set_heading("<h4> Envio de Notificacion por Correo de Profesor.</h4>");
echo $OUTPUT->header();

$mform = new teacher_form($PAGE->url.'?id='.$courseid,array('id'=>$courseid)); 

    if ($mform->is_cancelled()) {
        //Handle form cancel operation, if cancel button is present on form
    } else if ($fromform = $mform->get_data()) {
        //In this case you process validated data. $mform->get_data() returns data posted in form.
        //var_dump($fromform);
        $users=array();
        $data=array();

        require_once($CFG->dirroot.'/message/output/email/message_output_email.php');
  
        $message = new message_output_email();
        $message->courseid = $courseid; 
        $message->userfrom = $USER;
        $message->notification = '0';
        $message->conversationtype = null;
        $message->subject = $fromform->subject;
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = $fromform->contenido['text'].'<br><br>Este correo fue enviado desde la seccion de profesores del Aula Virtual COFRAMA CORDERO.';
        $message->fullmessage=$message->fullmessagehtml;


        switch($fromform->useroption){
            case 'T':        
                      $users=$DB->get_records_sql("SELECT distinct(ra.userid) as id, u.lastname as lastname, u.firstname as firstname FROM bch_role_assignments ra
                                                  join bch_user u on u.id=ra.userid
                                                  where ra.roleid=5 
                                                  and ra.contextid in (SELECT distinct(id) FROM bch_context where contextlevel=50 
                                                                  and instanceid in (SELECT id FROM `bch_course` where id=".$courseid."))"
                                                  ,null, 0, 0);

                      break;
            case 'A': 
            case 'B': 
                      $users=$DB->get_records_sql("SELECT distinct(ra.userid) as id, u.lastname as lastname, u.firstname as firstname FROM bch_role_assignments ra
                                                  join bch_user u on u.id=ra.userid
                                                  where ra.roleid=5 
                                                  and ra.userid in (SELECT userid FROM coframa_1.bch_user_info_data where data='".$fromform->useroption."'
                                                                  and fieldid =(SELECT id FROM coframa_1.bch_user_info_field where name='seccion')) 
                                                  and ra.contextid in (SELECT distinct(id) FROM bch_context where contextlevel=50 
                                                                  and instanceid in (SELECT id FROM `bch_course` where id=".$courseid."))"
                                                  ,null, 0, 0);     
                      break;
            case '+': 

                  foreach($_POST as $k => $v) {
                    if(strpos($k, 'userid_') === 0) {
                        $record=$DB->get_record_sql("SELECT distinct(ra.userid) as id, u.lastname as lastname, u.firstname as firstname FROM bch_role_assignments ra
                                                join bch_user u on u.id=ra.userid
                                                where ra.roleid=5 
                                                and ra.userid =".$v."
                                                and ra.contextid in (SELECT distinct(id) FROM bch_context where contextlevel=50 
                                                                and instanceid in (SELECT id FROM `bch_course` where id=".$courseid."))"
                                                ,null, 0, 0);
                      if($record){
                      $users[]=$record;
                      }
                    }  
                  }    
                  
            break;
            
            default: break;
        }


        $message->userto= core_user::get_noreply_user();

        if(!$message->send_message($message)){
           echo "<br><h6>Ocurrio un error, intente luego...</h6><br>";
        }
        else{
                
                $pedeid= $DB->get_record_sql("SELECT meta_value as id FROM `bch_institution_meta` 
                  where meta_key='id_pede' 
                  and meta_date = (SELECT MAX(meta_date) as md 
                                  FROM `bch_institution_meta` 
                                  WHERE meta_key='id_pede')", 
                  null, 0, 0);
          
                  if(($pedeid) && isset($fromform->PEDE)){
                      $message->userto= core_user::get_user($pedeid->id); 
                      if($message->send_message($message)){
                        echo "<h5>Notificacion Enviada a PEDE.</hr>";
                      }
                      else echo "<h5>Error enviando notificacion a PEDE.</hr>";
                  }

                $message->userto= $USER;
                if($message->send_message($message)){
                  echo "<h5>Copia enviada por email.</hr>";
                }
                else echo "<h5>Error enviando copia por Email.</hr>";
                  
                foreach($users as $user){
                    
                    $tipo='-';
                    if($fromform->tipo=='R'){
                      $r=get_parent_context($user->id);
                      if(!$r){
                        $data[]=ud($user->id,"USUARIO SIN REPRESENTANTE, NO ENVIADO.",$fromform->tipo);
                          continue; 
                      }
                      $message->userto= core_user::get_user($r); 
                    } 
                    elseif($fromform->tipo=='A'){
                      $message->userto= core_user::get_user($user->id);
                    }
                       
                    if($message->send_message($message)){ 
                      $data[]=ud($user->id,"ENVIADO.",$fromform->tipo);
                    }
                    else{
                      $data[]=ud($user->id,"OCURRIO UN ERROR, INTENE NUEVAMENTE. NO ENVIADO.",$fromform->tipo);
                    }

                }

        }

      print_results($data);
      echo $OUTPUT->continue_button('../../course/view.php?id='.$_GET['id']);

    } else {

        $mform->display();
      echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_mail/js/form_teacher.js"></script>';
      echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/cancel.js"></script>';

    }

echo $OUTPUT->footer();

function print_results($data){
   
    $table = new html_table();
    $table->width = "95%";
    $columns = array('Nombre Completo', 'Cedula', 'Enviado A', 'Resultado');
  
          foreach ($columns as $column) {
            $strtitle = $column;
            $table->head[] = $strtitle;
            $table->align[] = 'left';
          }
  
        $table->data = $data;      
  
        echo "<h5>".sizeof($table->data)." Remitentes </h5>";
        echo html_writer::table($table);
  
  }

 function get_parent_context($userid){
  global $DB;
  global $CFG;

    $usercontext = context_user::instance($userid);
    $aux = $DB->get_record('role_assignments', array('contextid' => $usercontext->id, 'roleid'=>'9'));
    
    if(!$aux){ 
      return false;
    }

    $userto = $DB->get_record('user', array('id' => $aux->userid));
    
    if(!$userto){ 
      return false;
    }

    return $userto->id;

 }

 function ud($userid,$result,$t=false,$optional=false){
            
      if(!$optional){
        $user=core_user::get_user($userid);
        $user_ced=profile_user_record($userid)->cedula;
        $user_sec=profile_user_record($userid)->seccion;
        $user_fln=$user->firstname.", ".$user->lastname;
        $tipo="-";
        
        if($t){
          $user_fln= "Seccion ".$user_sec." - ".$user->firstname.", ".$user->lastname;
          switch($t){
            case'A':  $tipo="Alumno";  break;
            case'R':  $tipo="Representante";  break;
            default: 
                    break;
          }
        }

          return array($user_fln,$user_ced,$tipo,$result);
      }
      else{
        array($optional,'','-',$result);
      }     
 }
