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
//require_once($CFG->libdir.'/filelib.php');

require_once($CFG->libdir.'/gdlib.php');
require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';



require_login(0, false);

defined('MOODLE_INTERNAL') || die();
$context = context_system::instance();
if(!has_capability('mod/infoprofe:sendemails', $context)){
  http_response_code(404);
  die(); 
}

require_once($CFG->dirroot.'/local/clean.php');
$c=new clean($_GET,$_POST);
$PAGE->requires->jquery();
$context = context_system::instance();
$PAGE->set_url("$CFG->wwwroot/local/infoprofe/boletin_sec.php");
$PAGE->set_context($context);


require('boletin_sec_individual.php');
$data=array();
$lapso=$_POST['lapso'];
$out='S';
$force=0;
if(isset($_POST['force'])) $force=true;

if (isset($_POST['see'])) { 
  $out='I'; 
  $PAGE->set_heading("<h4> Visualizacion de boletines</h4>"); 
}
else{
  $PAGE->set_heading("<h4> Resultado de envio de Boletines</h4>"); 
}
echo $OUTPUT->header();

if(isset($_POST['materias_id'])){
    $val=$_POST['materias_id'];     
    
    if($val=="T"){  
      for ($i=1; $i <=5  ; $i++) { 
        $level=$i.'A';
        promo_get_users($level);
      }
      
    }
    elseif($val=="+"){

      foreach($_POST as $k => $v) {
          if(strpos($k, 'userid_') === 0) {
              promo_get_users(null,$v);
          }
      }                          
     
    }
    elseif(!empty($val) && $val!=""){
      $val=$_POST['materias_id'];
      //$val=$c->p('/([1-5]A)/','materias_id'); 
      promo_get_users($val);
    }
}

print_results($data);

echo $OUTPUT->footer(); 

//-------------------------------------------------------------------------------------------

  function promo_get_users($nivel,$userid = null){
    
    global $CFG;
    global $DB;
    global $lapso, $out, $data, $force;

    if($nivel){
      $users=$DB->get_records_sql('SELECT distinct(userid) as id FROM bch_role_assignments where roleid=5 and contextid in
                                  (SELECT distinct(id) FROM bch_context where contextlevel=50 )
                                      and userid in 
                                  (SELECT userid FROM bch_user_info_data where data=\''.$nivel.'\' and fieldid = 
                                        (select id from bch_user_info_field where shortname=\'nivel\')
                                  )',null, 0, 0);
            foreach($users as &$user){
             
              if($out=='I'){
                    $data[]=boletin_link($user->id);
              }
              else{
                $boletin=  new boletin($user->id,$lapso,$out,$force);
                $data[]=$boletin->generate_boletin();
              }  
            }                           
    }
    elseif ($userid) {
       if($out=='I'){
            $data[]=boletin_link($userid);
       }
       else {
        $boletin=  new boletin($userid,$lapso,$out,$force);
        $data[]=$boletin->generate_boletin();
       }
    }                            
   
  }
 
  function print_results($data){
    global $lapso, $out;
    $table = new html_table();
    $table->width = "95%";
    $columns = array('Nombre Completo', 'Cedula', 'Año','Seccion', 'Resultado');

    if($out=='I') $columns[4]= 'Link Boletin';
  
          foreach ($columns as $column) {
            $strtitle = $column;
            $table->head[] = $strtitle;
            $table->align[] = 'left';
          }
  
        $table->data = $data;      
  
        echo "<h5>".sizeof($table->data)." Boletines </h5>";
        echo html_writer::table($table);
  
  }

  function boletin_link($userid){
            global $DB;
            global $CFG;
            global $lapso, $out, $force;

            $student=$DB->get_record("user", array ("id"=>$userid), 'firstname,lastname');
            //var_dump($student);
            $pur=profile_user_record($userid);
            $student_ced=$pur->cedula;
            $student_sec=$pur->seccion;
            $student_btd=$pur->bday.'-'.$pur->bmonth.'-'.$pur->byear;
            $cat_n=$pur->nivel;
            $student_fln= $student->firstname.", ".$student->lastname;
            
            return array($student_fln ,$student_ced,$cat_n,$student_sec,' <a href="'.$CFG->wwwroot.'/local/infoprofe/boletin_sec_see.php?l='.$lapso.'&uid='.$userid.'&f='.$force.'">Ver Boletin</a>');

  }

