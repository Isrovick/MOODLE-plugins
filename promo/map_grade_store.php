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
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();

$context = context_system::instance();
if(!has_capability('mod/infoprofe:seeinform', $context)){
  http_response_code(404);
  die(); 
}

require_login(0, false);

$PAGE->set_url("$CFG->wwwroot/local/promo/map_grade_store.php");
$PAGE->set_context($context);
$PAGE->requires->jquery();
require_once($CFG->dirroot.'/local/clean.php');
$c=new clean($_GET,$_POST);


$post= $_POST;
$post['courseid']=$c->p('/([0-9])+/','courseid');

if(isset($_POST['cancel'])){

   header("Location:".$CFG->wwwroot."/course/view.php?id=".$post['courseid']);

}
elseif(isset($_POST['submit'])){

  global $DB;
    
    for($i=1;$i<=4;$i++){
      $j=1;
      
    /***************************** */   
     $k=0; 
      while(isset($post['lp-'.$i.'-'.($k+1)])){
        $k++;
      };
      
      $percentage=100;

      if($k>0){
        $percentage=(100/$k);
      }
    /*********************************** */     
      
      while(isset($post['lp-'.$i.'-'.$j])){
      
        $lp_item_id=null;
        $cd_item_id=null;

        if($post['lp-'.$i.'-'.$j]!=0){
          
          $lp_item_id=$post['lp-'.$i.'-'.$j];
          
          if($post['cd-'.$i.'-'.$j]!=0){
            //echo " :  cd";
            $cd_item_id=$post['cd-'.$i.'-'.$j];

          }

          $data_full = array(
                          'courseid' =>$post['courseid'],
                          'id_lp_item' =>$lp_item_id,
                          'id_cd_item' =>$cd_item_id,
                          'lp' =>$i,
                          'ev' =>$j,
                          //'percentage'=>$post['pc-'.$i.'-'.$j],
                          'percentage'=>$percentage,
                          'enabled'=>1,
                          'lastupdate'=>date('Y-m-d H:i:s'),
          );
                          
          $exists = array(
                          'courseid' =>$post['courseid'],
                          'lp' => $i,
                          'ev' => $j,
                          'enabled' => '1',
          );
          
          $record_obj = $DB->get_record('institution_grade_items', $exists);

          if (isset($record_obj->id) && !empty($record_obj->id)){
            $record_id = $record_obj->id;
            
            $response= $DB->update_record_raw('institution_grade_items',  array('id' => $record_id ) +  $data_full);
                
                
          }
          else{
           
              $response=$DB->insert_record_raw('institution_grade_items', $data_full);
          
          }
          
        }
        else{

          $exists = array(
            'courseid' =>$post['courseid'],
            'lp' => $i,
            'ev' => $j,
            'enabled' => '1',
          );

          $record_obj = $DB->get_record('institution_grade_items', $exists);
          
          if (isset($record_obj->id) && !empty($record_obj->id)){
          
            $record_id = $record_obj->id;  
            $response= $DB->delete_records('institution_grade_items',  array('id' => $record_id ));

          }

        }

        $j++;
        
      }
      
    }
    header("Location:".$CFG->wwwroot."/course/view.php?id=".$post['courseid']);
}
elseif(isset($_POST['reset'])){

  global $DB;
    
        $exists = array(
          'courseid' =>$post['courseid'],
        );

        $record_obj = $DB->get_records('institution_grade_items', $exists);
       
        if(!empty($record_obj)){
          $response= $DB->delete_records('institution_grade_items', $exists);
        }
       
      
  header("Location:".$CFG->wwwroot."/course/view.php?id=".$post['courseid']);

}



