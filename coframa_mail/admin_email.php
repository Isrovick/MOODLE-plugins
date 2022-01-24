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

defined('MOODLE_INTERNAL') || die();
require_login(0, false);


$context = context_system::instance();

if(!has_capability('mod/coframa_mail:sendadminemail', $context)){
  http_response_code(404);
  die(); 
}

$PAGE->requires->jquery();
$PAGE->set_url("$CFG->wwwroot/local/coframa_mail/admin_email.php");
$PAGE->set_context($context);
$PAGE->set_title('Envio Notificacion');

$PAGE->set_heading("<h4> Envio de Notificacion por Correo</h4>");
echo $OUTPUT->header();



$mform = new admin_form(null,array()); 

if ($mform->is_cancelled()) {
  //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
//In this case you process validated data. $mform->get_data() returns data posted in form.
  //var_dump($fromform);
  //var_dump($_POST);
  $data=array();

  $uids=array();
  $utip=array();
  $notuids=array();

  require_once($CFG->dirroot.'/message/output/email/message_output_email.php');
  
  require_once($CFG->dirroot.'/local/clean.php');
  $c=new clean($_GET,$_POST);

  $message = new message_output_email();
  $message->userfrom = $USER;
  $message->notification = '0';
  $message->conversationtype = null;
  $message->subject = $_POST['subject'];
  $message->fullmessageformat = FORMAT_MARKDOWN;
  $message->fullmessagehtml = $_POST['contenido']['text'].'<br><br>Este correo fue enviado desde la seccion de Administracion del Aula Virtual COFRAMA CORDERO.';
  $message->fullmessage=$message->fullmessagehtml;

  foreach($_POST as $k => $v) {
    if(strpos($k, 'userid_') === 0) {
       $uids[$v]=$v;
       $utip[$v]='U';
    }
  }

  if(isset($_POST['ano_id'])){
    $np = "/^[0-9]+$/i";
    $sp = "/^[A-z]+$/i";
    $role=null;
    $join=null;
    $tipo=$c->p('/([APR]{1})/','tipo');//$_POST['tipo'];
    $seccion=$c->p('/([ABT]{1})/','seccion');//$_POST['seccion'];
    $ano_id=$c->p('/([IPST]{1})/','ano_id');//$_POST['ano_id'];
    
    switch($tipo){
      case 'R': $role='5'; break;
      case 'A': $role='5'; break;
      case 'P': $role='3 or roleid=4';break;
    }
  
    

    switch($seccion){
      case 'A': $join="and userid in (SELECT userid FROM  bch_user_info_data where data='A' and fieldid =
                                      (SELECT id FROM  bch_user_info_field where name='seccion'))"; 
                break;
      case 'B': $join="and userid in (SELECT userid FROM  bch_user_info_data where data='B' and fieldid =
                                      (SELECT id FROM  bch_user_info_field where name='seccion'))";  
                break;
      case 'T': $join="and userid in (SELECT userid FROM  bch_user_info_data where data in ('A','B') and fieldid =
                                      (SELECT id FROM  bch_user_info_field where name='seccion'))"; 
                break;
      default: 
                break;
    }

        $rest='';
    if(preg_match($np,$ano_id)){      
        
      $rest= "=".$ano_id;                           
    
    }
    elseif(preg_match($sp,$ano_id)){
    
      $nivel = 'null';
    
      switch($ano_id){
        case 'I':  $nivel="3"; break;
        case 'P':  $nivel="1"; break;
        case 'S':  $nivel="2"; break;
        case 'T':  $nivel="1,2,3"; break;
        default: break;
      }

        $rest= " in (Select id from bch_course_categories where  parent in 
                      (SELECT id FROM bch_course_categories where idnumber in (".$nivel.") )
                    )"; 
    }

    $users=$DB->get_records_sql("SELECT distinct(userid) as id FROM bch_role_assignments where (roleid =".$role.") 
                                    ".$join."
                                    and contextid in
                                    (SELECT distinct(id) FROM bch_context where contextlevel=50 
                                      and instanceid in (SELECT id FROM bch_course where category ".$rest." )
                                    )"
                                     ,null, 0, 0);
    


    foreach($users as &$user){
     
       if($tipo=='R'){
          $usercontext = context_user::instance($user->id);
          $aux = $DB->get_record('role_assignments', array('contextid' => $usercontext->id, 'roleid'=>'9'));
          
          if($aux){          
              $userto = $DB->get_record('user', array('id' => $aux->userid));
              if($userto){ 
                 $uids[$userto->id]=$userto->id;
                 $utip[$userto->id]=$tipo;
              }
              else{
                  $notuids[$user->id]=$user->id;
                  $utip[$user->id]='R';
              }
          }
          else{
            $notuids[$user->id]=$user->id;
            $utip[$user->id]='R';
          }
       }
       else{
          $uids[$user->id]=$user->id;
          $utip[$user->id]=$tipo;
       }
    }
  }


    $message->userto= $USER;
    if($message->send_message($message)){
      echo "<h5>Copia enviada por email a remitente.</hr>";
    }
    else echo "<h5>Error enviando copia por Email.</hr>";   

    foreach($uids as $k => $userid){
      $student=$DB->get_record("user", array ("id"=>$userid), 'firstname,lastname');
      $pur=profile_user_record($userid);
      $student_ced=$pur->cedula;
      $student_sec=$pur->seccion;
      $student_btd=$pur->bday.'-'.$pur->bmonth.'-'.$pur->byear;
      $student_fln= $student->firstname.", ".$student->lastname;

      $message->userto= core_user::get_user($userid); 

      if($message->send_message($message)){ 
        $data[]=ud($userid,"ENVIADO.",$utip[$k]);
      }
      else{
        $data[]=ud($userid,"OCURRIO UN ERROR, INTENE NUEVAMENTE. NO ENVIADO.",$utip[$k]);
      }
    }

    foreach($notuids as $k => $notuserid){
      $data[]=ud($notuserid,"USUARIO SIN REPRESENTANTE, NO ENVIADO.",$utip[$k]);
    }
  
    print_results($data);
    echo '<a href="'.'" ><button type="button" class="btn btn-primary btn-sm">Continuar</button></a>';
  
} else {
// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
// or on the first display of the form.

//Set default data (if any)
//$mform->set_data($toform);
//displays the form
  $mform->display();
  echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_mail/js/function.js"></script>';
  echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/cancel.js"></script>';

}


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


function ud($userid,$result,$t=false,$optional=false){
            
  if(!$optional){
    $user=core_user::get_user($userid);
    $user_ced=profile_user_record($userid)->cedula;
    $user_sec=profile_user_record($userid)->seccion;
    $user_fln=$user->firstname.", ".$user->lastname;
    $tipo="-";
    
    if($t){
      switch($t){
        case'A':  $tipo="Alumno";  break;
        case'R':  $tipo="Representante";  break;
        case'P':  $tipo="Profesor";  break;
        case'U':  $tipo="Usuario";  break;
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
