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

defined('MOODLE_INTERNAL') || die();

require_login(0, false);
require_once($CFG->dirroot.'/local/clean.php');
$c=new clean($_GET,$_POST);

$PAGE->set_url("$CFG->wwwroot/local/infoprofe/inform_build.php");
$id=null;
$course=null;
$m=null; 

if(isset($_GET['id']) && isset($_GET['course']) && isset($_GET['m'])){
    $id=$c->g('/([0-9])+/','id');//$_GET['id'];
    $course=$c->g('/([0-9])+/','course');//$_GET['course'];
    $m=$c->g('/([1-4]){1}/','m');//$_GET['m']; 
}
elseif(isset($_POST['id']) && isset($_POST['course']) && isset($_POST['m'])){
    $id=$c->p('/([0-9])+/','id');//$_POST['id'];
    $course=$c->p('/([0-9])+/','course');//$_POST['course'];
    $m=$c->p('/([1-4]){1}/','m');//$_POST['m']; 
}

$context = context_course::instance($course);

if(!has_capability('mod/infoprofe:seeinform', $context) && !has_capability('mod/infoprofe:seecage', $context)){
    http_response_code(404);
    die(); 
}



$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Generacion de Informe alumnos');
$PAGE->set_heading("Generacion de Informe alumno");
$PAGE->requires->jquery();
echo $OUTPUT->header();

if(!(is_null($id)||is_null($course)||is_null($m))){

  global $CFG;
  global $DB;   


  $profn="-";                        
  $profid=$DB->get_record_sql("SELECT distinct(userid) as id FROM bch_role_assignments where roleid=3 and contextid in
                                (SELECT distinct(id) FROM bch_context where contextlevel=50 and instanceid in
                                (SELECT id FROM `bch_course` where id=".$course."))"
                              ,null, 0, 0);
  if($profid){
    $prof=$DB->get_record("user", array ("id"=>$profid->id), 'firstname,lastname');
    $profn= $prof->firstname.', '.$prof->lastname; 
  }  


  $mgv="---";
  $user = $DB->get_record('user', array('id'=>$id), 'id, username, firstname, lastname, email, lastaccess, city');
  $user_info=profile_user_record($id);
  $nombreyapellido=$user->firstname.", ".$user->lastname;
  $cedula=$user_info->cedula;
  $año=$user_info->nivel;
  $seccion=$user_info->seccion;
  $año_esc=$DB->get_record_sql("SELECT id, meta_value FROM `bch_institution_meta` where meta_key='periodo' and meta_date = (SELECT MAX(meta_date) as md FROM `bch_institution_meta` WHERE meta_key='periodo')", null, 0, 0);
  $momento="---";
  $docente=$profn;
  $titulo_materia="---";
  $mform = null;
  
      $parent= $DB->get_record_sql('select name from bch_course_categories where id in(select parent as id from bch_course_categories where id in (SELECT category as id from bch_course where id = '.$course.' ))');
      $course = $DB->get_record('course', array('id'=>$course), 'id,shortname,fullname,summary,category');

      $grupo = $DB->get_record_sql("SELECT name FROM bch_course_categories where id in(select parent from bch_course_categories where id=".$course->category.")",null,0,0);
      
      $primaria=false;

      if($grupo->name=='Primaria'||$grupo->name=='Inicial'){
          $primaria=true;
      }

      $titulo_materia=$course->summary;

      if(!$primaria){

          $mg = $DB->get_record_sql("select finalgrade from bch_grade_grades where userid='".$id."' and itemid in(select id from bch_grade_items where courseid='".$course->id."' and itemname='Momento ".$m."')");
          
          if( $mg ){
              $mgv = (($mg->finalgrade!='')?$mg->finalgrade:"Nota no plasmada");
          }   
        
          switch($m){
              
              case '1': $momento="Primer Momento";  
                              break;
              case '2': $momento="Segundo Momento";   
                              break;
              case '3': $momento="Tercer Momento";
                              break;
              case '4': $momento="Ajuste";
                              break;
              default: 
                              break;
          }
      }
      

      $html= '<div class="profile_tree">
          <section class="node_category">
              <h3>Detalles de Informe</h3>
              <ul>
              
              <table class="admintable generaltable">
              <!--<thead>
                  <tr> 
                      <th class="header">Datos Materia</th>
                      <th class="header">Datos Profesor</th> 
                      <th class="header">Datos estudiante</th> 
                  </tr>
              </thead>-->
              <tbody> 
                  <tr>
                      <td> <dl><dt>Año Escolar</dt>
                      <dd>'.$año_esc->meta_value.'</dd></dl></td>
                      <td > <dl><dt>Profesor</dt>
                      <dd>'.$docente.'</dd></dl></li></td>
                      <td><dt>Nombre y apellido de estudiante</dt>
                      <dd></dd>'.$nombreyapellido.'</dl></td>
                  </tr>
                  <tr>
                      <td><dl><dt>Materia</dt>
                      <dd>'.$titulo_materia.'</dd></dl></td>
                      <td><dt>Seccion</dt>
                      <dd>'.$seccion.'</dd></dl></td>
                      <td><dl><dt>Cedula Estudiante</dt>
                      <dd>'.$cedula.'</dd></dl></td>
                  </tr>';

         if(!$primaria){
              $html.= '<tr>
                            <td><dl><dt>Nivel</dt>
                            <dd>'.$año.'</dd></dl></td>
                            <td><dl><dt>Momento</dt>
                            <dd></dd>
                            <dd>'.$momento.'</dd>
                            <dd></dd>
                            </dl></td>
                            <td><dl><dt>Nota momento</dt>
                                    <dd></dd>
                                    <dd>'.$mgv.'</dd>
                                    <dd></dd>
                            </dl></td>
                        </tr>
                    </tbody>
                    </table>
                  </section>
                  </div> ';
              $mform = new momento_form(null,array('id'=>$id,'course'=>$course->id,'m'=>$m));
        }
        else{
            $html.= '
                    </tbody>
                    </table>
                  </section>
                  </div> ';
            $mform = new momento_form_primaria(null,array('id'=>$id,'course'=>$course->id,'m'=>$m));
        }        
                  
      echo $html; 

      if ($mform->is_cancelled()) {
         header("Location: {$_SERVER['HTTP_REFERER']}"); 
      } 
      else if ($fromform = $mform->get_data()) {
          
          $data=json_decode(hex2bin($fromform->reference));

          $meta_name=null;
          if(isset($fromform->project)){
            $meta_name=$fromform->project;
          }
          
          $data_full=  array_merge((array)$data, array(
                                    'id_issuing' => '2',
                                    'meta_name' => $meta_name,
                                    'description' => $fromform->description['text'],
                                    'meta_date' => date("Y-m-d H:i:s"),
                                    'state' => 'PENDIENTE'
          ));

          $record_obj = $DB->get_record('institution_inform',(array)$data);
          
          if (isset($record_obj->id) && !empty($record_obj->id)){
              
              $record_id = $record_obj->id;
              
              $data_full['state']= $fromform->state;

             if($DB->update_record_raw('institution_inform',  array('id' => $record_id ) +  $data_full)){
                echo '<div class="alert alert-success" role="alert"> <h4>Informe actualizado exitosamente!</h4></div>';
                echo $OUTPUT->continue_button("index.php?c=u&id=".$data->id_course);
             }
             else{
                echo '<div class="alert alert-danger" role="alert"> <h4>Error Actualizando informe ...</h4></div>';
                echo $OUTPUT->continue_button('index.php?c=e&id='.$data->id_course);
             }
             
          }
          else{
              if($DB->insert_record_raw('institution_inform', $data_full)){
                echo '<div class="alert alert-success" role="alert"> <h4>Informe almacenado exitosamente!</h4></div>';
                echo $OUTPUT->continue_button("index.php?c=s&id=".$data->id_course);
              }
              else{
                echo '<div class="alert alert-danger" role="alert"> <h4>Error almacenando informe ...</h4></div>';
                echo $OUTPUT->continue_button("index.php?c=e&id=".$data->id_course);
              }
          }
      } else {

          $data = array(   
            'id_period' => $año_esc->id,       
            'id_propietary' => $id,          
            'id_course' => $course->id,      
            'id_type'=> (($primaria)?'proyecto':'momento'),             
            'meta_key' => $m,  
        );
                  
          $toform_json = bin2hex(json_encode($data)); 
          
          $toform = (object) array();               
          
          $toform->reference = $toform_json;

          $record= $DB->get_record('institution_inform',$data);

          if (!empty($record->id)){ 

              $toform->description['text']= $record->description;
              $toform->state = $record->state; 

              if($primaria){
                $toform->project = $record->meta_name;  
              }  
          }
          
          $mform->set_data($toform);
  
          $mform->display();
          echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/cancel.js"></script>';

          
      } 
                 
}


echo $OUTPUT->footer();