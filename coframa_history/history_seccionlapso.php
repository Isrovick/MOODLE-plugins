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

require_once('informes/seccionlapso.php');
require_once('../coframa_user/user_individual.php');

defined('MOODLE_INTERNAL') || die();
require_login(0, false);

$context = context_system::instance();

if(!has_capability('mod/coframa_history:generatehistory', $context)){
  http_response_code(404);
  die(); 
}

$PAGE->requires->jquery();
$PAGE->set_url("$CFG->wwwroot/local/coframa_history/history_seccionlapso.php");
$PAGE->set_context($context);
$PAGE->set_title('Historico Seccion');


if(!(isset($_POST['nivel'])&& isset($_POST['periodo']) && isset($_POST['seccion']) && isset($_POST['lapso']))){
  $PAGE->set_heading("Historia por seccion y momento.");
  echo $OUTPUT->header();

  echo $OUTPUT->continue_button("history_vis.php");
  echo $OUTPUT->footer();
  die();
}

$nivel=$_POST['nivel'];
$periodo=$_POST['periodo'];
$seccion=$_POST['seccion'];
$lapso=$_POST['lapso'];

if($lapso=='F') $lapsoaux='NOTAS FINALES';
else $lapsoaux=  'Momento '.$lapso;


$head_title=gimf('nombre_plantel').", Año escolar:".gimf('periodo').  ' Fecha:'.date('Y-m-d').'    '.$lapsoaux;

if($lapso=='F'){

            $husers=$DB->get_records_sql("select * from bch_institution_huser ih 
                                            join bch_user u on ih.userid=u.id
                                          where 
                                          ih.nivel='".$nivel."' and 
                                          ih.seccion='".$seccion."' and 
                                          ih.periodid='".$periodo."'
                                          order by cast(u.idnumber as unsigned) asc",null,0,0);

            $hcourses=$DB->get_records_sql("SELECT distinct(hg.courseid) as courseid, mc.shortname, mc.summary FROM bch_institution_hgrades hg
                                            join bch_course mc on hg.courseid=mc.id
                                            where hg.huserid in(
                                                            select id from bch_institution_huser 
                                                              where 
                                                              nivel='$nivel' and 
                                                              seccion='$seccion' and 
                                                              periodid='$periodo'
                                                            )
                                            order by mc.shortname asc
                                            ",null,0,0);

            $usersinfo=array();
            $courses_order=array();
            $st=0;
              
              foreach ($hcourses as &$hcourse) {
                $courses_order[$hcourse->courseid]='<th align="center" colspan="1"><bold>'.$hcourse->shortname.'</bold></th> ';
              }

              foreach ($husers as &$huser) {
                
                  $promedio_suma=0;
                  $posicion=0;
                  $aprobadas=0;
                  $aplazadas=0;
                  $notasfinales=array();
                  $cont=0;
                  
                  foreach ($hcourses as &$hcourse) {

                      $rec=$DB->get_record_sql("SELECT finalgrade FROM bch_institution_hgrades
                                                  where courseid='$hcourse->courseid' 
                                                  and   huserid in(
                                                                  select id from bch_institution_huser 
                                                                    where 
                                                                    userid='$huser->userid' and 
                                                                    nivel='$nivel' and 
                                                                    seccion='$seccion' and 
                                                                    periodid='$periodo'
                                                                  )
                                                  
                                                  ",null,0,0);
                      if($rec){
                        
                        $notasfinales[$hcourse->courseid]=$rec->finalgrade;
                        $promedio_suma+=$notasfinales[$hcourse->courseid];
                        $cont++;

                        if($notasfinales[$hcourse->courseid]>=10){ $aprobadas++;}
                        else { $aplazadas++;}

                      }                            
                }
                 
                  $usersinfo[$huser->userid]=array(
                      'idnumber'=>$huser->idnumber,
                      'fullname'=>$huser->firstname.', '.$huser->lastname,
                      'notasfinales'=>$notasfinales,
                      'promedio'=>number_format($promedio_suma/$cont,3),
                      'aprobadas'=>$aprobadas,
                      'aplazadas'=>$aplazadas,
                      'posicion'=>0,
                  );
                  $up[$huser->userid]= $usersinfo[$huser->userid]['promedio'];
                                 $st+= $usersinfo[$huser->userid]['promedio'];
                 
            }

              $html='';
              if($st>0){
                arsort($up);
                $j=0;
                foreach($up as $k => $v) {
                  $j++;
                  $usersinfo[$k]['posicion']=$j;
                }
              }  

                foreach($usersinfo as $userinfo){
                    $html.='<tr>
                              <td align="center" colspan="1">'.$userinfo['idnumber'].' </td> 
                              <td align="left" colspan="6">'.$userinfo['fullname'].'</td>'; 
                      
                    foreach ($courses_order as $hcourseid=>$html_head) {
                        
                        if(isset($userinfo['notasfinales'][$hcourseid])){
                          $html.='<td align="center" colspan="1">'.$userinfo['notasfinales'][$hcourseid].'</td> ';
                        }
                        else{
                          $html.='<td align="center" colspan="1">-</td> ';
                        }
                  }

                  $html .='
                            <td align="center" colspan="1">'.$userinfo['promedio'].'</td> 
                            <td align="center" colspan="1">'.$userinfo['posicion'].'</td>
                            <td align="center" colspan="1">'.$userinfo['aprobadas'].'</td>
                            <td align="center" colspan="1">'.$userinfo['aplazadas'].'</td>
                        </tr> ';
                }

                $title=$nivel.'-'.$periodo.'-'.$seccion;

                $header_html='';
                foreach ($courses_order as $hcourseid=>$html_head) {
                    $header_html.=$html_head;
                }


                $sl=new seccionlapsopdf();
                $sl->seccion_lapso($title,$head_title, $header_html, $html);
}
elseif($lapso>0 && $lapso<5){

    $users=$DB->get_records_sql("SELECT distinct(userid) as id FROM bch_role_assignments where roleid=5 and contextid in
                                (SELECT distinct(id) FROM bch_context where contextlevel=50 )
                                    and userid in 
                                    (SELECT userid FROM bch_user_info_data where data='$nivel' and fieldid = 
                                    (select id from bch_user_info_field where shortname='nivel') )",null, 0, 0);
    $ulist=array(); 
    $up=array();
    $st=0;
    $courses_order=array();
    foreach($users as &$user){

      $uaux=new user_individual($user->id);
      $guc=$uaux->get_user_courses();
      
      foreach($guc as &$course){
        
        $shortname=$DB->get_field('course','shortname',array('id'=>$course->id));
        $courses_order[$course->id]='<th align="center" colspan="1"><bold>'.$shortname.'</bold></th> ';
      }
      $ulist[$user->id]=array(
        'fullname'=>$uaux->student_fln,
        'idnumber'=>$uaux->student_idn,
        'lapsoinfo'=>$uaux->get_promedio_lapso($lapso),
      );
      $up[$user->id]= $ulist[$user->id]['lapsoinfo']['promedio'];
                $st+= $ulist[$user->id]['lapsoinfo']['promedio'];

    }

    $html='';
    if($st>0){
      arsort($up);
      $j=0;
      foreach($up as $k => $v) {
        $j++;
        $ulist[$k]['lapsoinfo']['posicion']=$j;
      }
    }

    foreach($ulist as $userid=>$userinfo){
      $html.='<tr>
                <td align="center" colspan="1">'.$userinfo['idnumber'].' </td> 
                <td align="left" colspan="6">'.$userinfo['fullname'].'</td>'; 
      foreach ($courses_order as $courseid=>$html_head) {
        if(isset($userinfo['lapsoinfo']['lapso_items'][$courseid])){
          $html.='<td align="center" colspan="1">'.$userinfo['lapsoinfo']['lapso_items'][$courseid]['finallapsocurso'].'</td> ';
        }
        else{
          $html.='<td align="center" colspan="1">-</td> ';
      
        }
      }

      $html .='
                  <td align="center" colspan="1">'.$userinfo['lapsoinfo']['promedio'].'</td> 
                  <td align="center" colspan="1">'.$userinfo['lapsoinfo']['posicion'].'</td>
                  <td align="center" colspan="1">'.$userinfo['lapsoinfo']['aprobadas'].'</td>
                  <td align="center" colspan="1">'.$userinfo['lapsoinfo']['aplazadas'].'</td>
              </tr> ';
    }

    $header_html='';

    foreach ($courses_order as $hcourseid=>$html_head) {
      $header_html.=$html_head;
    }
    $title=$nivel.'-'.$periodo.'-'.$seccion;
    $sl=new seccionlapsopdf();
    $sl->seccion_lapso($title,$head_title, $header_html, $html);


}

function gimf($field){
  global $DB;
  $info= $DB->get_record_sql("SELECT meta_value FROM `bch_institution_meta` 
          where meta_key='".$field."' 
          and meta_date = (SELECT MAX(meta_date) as md 
                          FROM `bch_institution_meta` 
                          WHERE meta_key='".$field."')",
  null, 0, 0);
  
  if($info){
      return $info->meta_value;
  }
  return null;

}    