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
require('informes/cage_inform.php');
require('../coframa_user/user_individual.php');
require_once($CFG->libdir.'/gdlib.php');
require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();
require_login(0, false);

require_once($CFG->dirroot.'/local/clean.php');
$c=new clean($_GET,$_POST);


$context = context_course::instance($c->g('/([0-9])+/','id'));

if(!has_capability('mod/infoprofe:seecage', $context)){
  http_response_code(404);
  die(); 
}
$PAGE->requires->jquery();
$PAGE->set_url("$CFG->wwwroot/local/infoprofe/cage.php");
$PAGE->set_context($context);
$PAGE->set_title('C.A.G.E.');

if(isset($_GET['t']) && isset($_GET['id'])){
 
  $course_id=$c->g('/([0-9])+/','id'); //$_GET['id'];
  $id=$c->g('/([0-9])+/','id'); //$_GET['id'];
  $t=$c->g('/([AB]){1}/','t');//$_GET['t'];

  $course = $DB->get_record('course', array('id'=>$course_id), 'id, shortname, fullname, summary, category');
  $grupo = $DB->get_record_sql("SELECT name FROM bch_course_categories where id in(select parent from bch_course_categories where id=".$course->category.")",null,0,0);
        
    if($grupo->name=='Primaria'|| $grupo->name=='Inicial'){
        $PAGE->set_heading('C.A.G.E.');
        echo $OUTPUT->header();
        echo "Opcion solo disponible para Media General";;
        echo $OUTPUT->footer();
    }
    else{
     cague_main($id,$t);
    }
     
}

function cague_main($courseid,$seccion){
  global $DB,$CFG;
  $ncol=array(4,4,4,1);
  $course=get_course_info($courseid);
  $html='<table border="1" class="boletin">';
  $html.='  <tr><th colspan="44" align="center">CONTROL DE LA EVALUACION GENERAL DEL ESTUDIANTE</th></tr>
            <tr>
              <th align="center" colspan="8">Año</th>
              <td align="center" colspan="14" class="ced">'.$course->catname.'</td>
              <th align="center" colspan="8">Asignatura</th>
              <td align="center" colspan="14" class="ced">'.$course->fullname.'</td>
            </tr>
            <tr>
              <th align="center" colspan="8">Profesor</th>
              <td align="center" colspan="14" class="ced">'.get_profn($courseid).'</td>
              <th align="center" colspan="8">Profesor Guia</th>
              <td align="center" colspan="14" class="ced">'.get_asisn($courseid).'</td>
            </tr>
  
  
  ';
  $html.=

     '<tr>
          <th align="center" colspan="8">Seccion</th>
          <th align="center" colspan="'. (($ncol['0'])*2 +1) .'"> Momento 1</th>
          <th align="center" colspan="'. (($ncol['1'])*2 +1) .'"> Momento 2</th>
          <th align="center" colspan="'. (($ncol['2'])*2 +1) .'"> Momento 3</th>
          <th align="center" colspan="2" rowspan="2">Total</th>
          <th align="center" colspan="'. (($ncol['3'])*2) .'" rowspan="2"> Revision</th>
          
          <th align="center" colspan="5" rowspan="2">Calificacion Final</th>
      </tr>
      <tr>
      <th align="center" colspan="8">'.$seccion.'</th>
      
      ';
      
        for($k=0;$k<3;$k++){
            $col=$ncol[$k];
            $j=1;
            for($i=0;$i<$col;$i++){
              $html.='<th align="center" colspan="1" >Ev.'. ($i +1) .'</th>
                      <th align="center" colspan="1" >112</th>'; 
            }
            $html.='<th align="center" colspan="1">DF.'.$j.'</th>'; 
            $j++;             
        }

          $html.='  
                  </tr>
                  <tr>
                      <th align="center" colspan="5"> Nombre Alumno </th>
                      <th align="center" colspan="3"> Cedula </th> '; 

          for($i=1;$i<=3;$i++){
            $col=$ncol[$i-1];
            for($j=0;$j<$col;$j++){
                  $html.='<th align="center" colspan="2">100%</th>';
            }

            $html.='<th align="center" colspan="1">100%</th>';                 
          }          
                    
          $html.= '<th align="center" colspan="2">100%</th>
                   <th align="center" colspan="2">100%</th>
                   <th align="center" colspan="5">100%</th>
          </tr>';  
      
  $html.=get_cage_item($courseid,$ncol,$seccion);

  $html.='</table>';       
 
  $pdf= new cagepdf();
  $doc=$pdf->cage( $html);

  //return $html;

}




function get_cage_item($courseid,$ncol,$seccion){
      global $CFG;
      global $DB;
      

      $users=$DB->get_records_sql("SELECT distinct(userid) as id FROM bch_role_assignments where roleid=5 
                                  and userid in (SELECT userid FROM  bch_user_info_data where data='".$seccion."'
                                                  and fieldid =(SELECT id FROM  bch_user_info_field where shortname='seccion')) 
                                  and contextid in (SELECT distinct(id) FROM bch_context where contextlevel=50 
                                                    and instanceid in (SELECT id FROM `bch_course` where id=".$courseid."))"
                              ,null, 0, 0);

      $html='';
    
      foreach($users as $user){
      
       
        $u=new user_individual($user->id);
       
        $student_ced=$u->pur->cedula;
        $student_fln=$u->student_fln;
        
        $html.= '<tr> <td align="center" colspan="5" class="fln">'.$student_fln.'</td>';
        $html.= '     <td align="center" colspan="3" class="ced">'.$student_ced.'</td>';
        $bi=$u->course_item($courseid);
        $y=$bi['qual'];
          $gk=$bi['boletin_items'];
        for($i=1;$i<=3;$i++){
           $gi=$gk[$i];
            $col=$ncol[$i-1];
            for($j=0;$j<$col;$j++){  
              if(isset($gi[$j])){
                $gg=$gi[$j];
  
                $htmlaux ='<td align="center" colspan="1">'.$gg['lp_g'].'</td>'; 
                $htmlaux.='<td align="center" colspan="1">'.$gg['cd_g'].'</td>';       
                $html.=$htmlaux;
              }
              else{
                $htmlaux='<td align="center" colspan="1">-</td>'; 
                $htmlaux.='<td align="center" colspan="1">-</td>';       
                $html.=$htmlaux;
              } 
            }//$j
      
          
          $html.='<td align="center" colspan="1" class="nota">'.$gi['finallapsocurso'].'</td>';
        }//$i
          
        $html.='<td align="center" colspan="2">'.$u->n($y,$gk['total']).'</td>';

          if(isset($gk['revision'][0])){
            $gg = $gk['revision'][0];
            $html.='<td align="center" colspan="1">'.$u->n($y,$gg['lp_g']).'</td>
                    <td align="center" colspan="1">'.$u->n($y,$gg['cd_g']).'</td>';
          }
          else{
            $html.='<td align="center" colspan="1">-</td>';
            $html.='<td align="center" colspan="1">-</td>';
          } 

          $html.='<td align="center" colspan="5" class="nota">'.$gk['final'].'</td>';
  
        $html.='</tr>';   
      }
      
      $html.='';

      return $html;
  }
function get_course_info($courseid){
    global $CFG;
    global $DB;
    
    return $DB->get_record_sql('select co.id, co.summary as fullname, cc.name as catname FROM bch_course co
    join bch_course_categories cc on co.category=cc.id
    where co.id='.$courseid,null,0,0);

}
  
function get_profn($courseid){
    global $CFG;
    global $DB;
    $profn="-";                        
    $profid=$DB->get_record_sql("SELECT distinct(userid) as id FROM bch_role_assignments where roleid=3 and contextid in
                                  (SELECT distinct(id) FROM bch_context where contextlevel=50 and instanceid in
                                  (SELECT id FROM `bch_course` where id=".$courseid."))"
                                ,null, 0, 0);
    if($profid){
      $prof=$DB->get_record("user", array ("id"=>$profid->id), 'firstname,lastname');
      $profn= $prof->firstname.', '.$prof->lastname; 
    }
    return $profn;  
}
   
function get_asisn($courseid){
    global $CFG;
    global $DB;

    $asisn="-";  
    $asisid=$DB->get_record_sql("SELECT distinct(userid) as id FROM bch_role_assignments where roleid=4 and contextid in
                                (SELECT distinct(id) FROM bch_context where contextlevel=50 and instanceid in
                                (SELECT id FROM `bch_course` where id=".$courseid."))"
                                ,null, 0, 0);
    if($asisid){
    $asis=$DB->get_record("user", array ("id"=>$asisid->id), 'firstname,lastname');
    $asisn=$asisn=$asis->firstname.', '.$asis->lastname; ; 
    } 
    
    return $asisn;
}




