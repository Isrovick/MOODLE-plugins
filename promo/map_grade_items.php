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
 
require_once($CFG->dirroot.'/local/clean.php');
$c=new clean($_GET,$_POST);


$PAGE->requires->jquery();

$PAGE->set_url("$CFG->wwwroot/local/promo/map_grade_items.php");
$PAGE->set_context($context);

$PAGE->set_pagelayout('standard');
$PAGE->set_title('Asignacion de Pruebas');
$PAGE->set_heading('Asignacion de Pruebas');

echo $OUTPUT->header();

if(!isset($_GET['id'])) die();

$course_id=$c->g('/([0-9])+/','id'); //$_GET['id'];
$course = $DB->get_record('course', array('id'=>$course_id), 'id, shortname, fullname, summary, category');
$grupo = $DB->get_record_sql("SELECT name FROM bch_course_categories where id in(select parent from bch_course_categories where id=".$course->category.")",null,0,0);
        
    $primaria=false;
if($grupo->name=='Primaria'|| $grupo->name=='Inicial'){
  $html="Opcion solo disponible para Media General"; 
  echo $html;
  echo $OUTPUT->footer();
  die();
}



    $items=$DB->get_records_sql("Select id, itemname from bch_grade_items where itemname IS NOT NULL and courseid=".$course_id."", null, 0,0);
    //var_dump($items);
    //echo "<hr>";
    $items_se=$DB->get_records_sql("Select id_lp_item, id_cd_item from bch_institution_grade_items where courseid=".$course_id." 
                                    and ( id_lp_item in(Select id from bch_grade_items where itemname IS NOT NULL and courseid=".$course_id.")
                                    or id_cd_item in(Select id from bch_grade_items where itemname IS NOT NULL and courseid=".$course_id."))
                                    order by lp asc, ev asc", null, 0,0);
    //var_dump($items_se);
    //echo "<hr>";

    $options=array();
    $options_kv=array();

  if($items){
    
    foreach($items as &$obj){
      $what =  " <option value='$obj->id' >$obj->itemname</option> \n";
      $options[$obj->id] = $what;
      $options_kv[$obj->id] = $obj->itemname;
    }
      $options_complete=$options;

    if($items_se){
      foreach($items_se as &$remove){
            unset($options[$remove->id_lp_item]);
          if($remove->id_cd_item){
            unset($options[$remove->id_cd_item]);
          }
      }
    }


    $html='
    <form class="mform" id="course_'.$course_id.'" method="post" action="'.$CFG->wwwroot.'/local/promo/map_grade_store.php">

    <fieldset class="clearfix collapsible containsadvancedelements"  id="lp-1">
      <legend class="ftoggler">
        <a href="#" class="fheader" role="button" aria-controls="id_newfilter" aria-expanded="true" >1er Momento</a>
      </legend>
        '.options(1).'
      </fieldset> 
    <div class="col-md-9 text-center">
        <input type="button" class="btn btn-primary" name="addfilter" id="add-1" value="+ Agregar Evaluacion">
    </div>
    

    <fieldset class="clearfix collapsible containsadvancedelements"  id="lp-2">
      <legend class="ftoggler">
        <a href="#" class="fheader" role="button" aria-controls="id_newfilter" aria-expanded="true" >2do Momento</a>
      </legend>
      '.options(2).'
    </fieldset>
    <div class="col-md-9 text-center">
        <input type="button" class="btn btn-primary" name="addfilter" id="add-2" value="+ Agregar Evaluacion">
    </div> 


    <fieldset class="clearfix collapsible containsadvancedelements"  id="lp-3">
      <legend class="ftoggler">
        <a href="#" class="fheader" role="button" aria-controls="id_newfilter" aria-expanded="true" >3er Momento</a>
      </legend>
      '.options(3).'
    </fieldset>
    <div class="col-md-9 text-center">
        <input type="button" class="btn btn-primary" name="addfilter" id="add-3" value="+ Agregar Evaluacion">
    </div> 

    <fieldset class="clearfix collapsible containsadvancedelements"  id="lp-4">
    <legend class="ftoggler">
      <a href="#" class="fheader" role="button" aria-controls="id_newfilter" aria-expanded="true" >Revision</a>
      </legend>
      '.options(4).'
    </fieldset>
    <div class="col-md-9 text-center">
      <input type="button" class="btn btn-primary" name="addfilter" id="add-4" value="+ Agregar Evaluacion">
    </div> 
    <br>

    <br>   
    <div class="col-md-9 text-center">
        <input type="hidden" class=""            name="courseid" id="id_courseid" value="'.$course_id.'">
        <input type="submit" class="btn btn-primary" name="submit" id="id_submit" value="Guardar cambios">
        <input type="submit" class="btn btn-primary" name="reset" id="id_reset" value="Restablecer">
        <input type="submit" class="btn btn-primary" name="cancel" id="id_cancel" value="Cancelar">
    </div> 

    </form>

    ';
}

echo $html;

echo $OUTPUT->footer();

echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/promo/js/form_func.js"></script>';

function options($lp){
  
  global $DB;
  global $options;
  global $options_kv;
  global $options_complete;
  global $course_id;

  $items=$DB->get_records_sql("Select * from bch_institution_grade_items where courseid=".$course_id." and lp=".$lp."
  and ( id_lp_item in(Select id from bch_grade_items where itemname IS NOT NULL and courseid=".$course_id.")
  or id_cd_item in(Select id from bch_grade_items where itemname IS NOT NULL and courseid=".$course_id."))
                                order by lp asc, ev asc", null, 0,0);
  $html="";
  //var_dump($items);
  if($items){
    $opts=""; 
    foreach($options as $opt){
      $opts.=$opt;
    }

    foreach ($items as $obj) {

    $o="-".$obj->lp."-".$obj->ev;

    $html.= '<div class="col-md-9 form-inline felement" data-fieldtype="group" id="ev'.$o.'">   '.  
    '<div class="form-group  fitem  "> '.  
    '   <label for="lp'.$o.'">Eval '.$obj->ev.'</label> '.  
    '<select name ="lp'.$o.'" class="custom-select"> '. 
    '    <option value = "0" >Seleccione el item</option>'. 
    '    <option value = "'.$obj->id_lp_item.'" selected>'.$options_kv[$obj->id_lp_item].'</option>'.  
    $opts. 
    '</select> '.
    '</div> '.
    '<div class="form-group  fitem  "> '.
    '    <label for="cd'.$o.'">112 </label> '.
    '<select name ="cd'.$o.'" class="custom-select">';
    
    if($obj->id_cd_item!=null){
      $html.='
      <option value = "0" >Seleccione el item</option>
      <option value = "'.$obj->id_cd_item.'" selected>'.$options_kv[$obj->id_cd_item].'</option> 
      '.$opts;
    }
    else{
      $html.='<option value = "0" selected>Seleccione el item</option>'.
      $opts;
    }

    $html.='</select>'.
    '</div>'.
    '<div class="col-md-1">  </label>'.
    '</div>'.
    '<div class="col-md-2">'.///*
    //'    <input type="number" class="form-control " name="pc'.$o.'" id="pc'.$o.'" value="'.$obj->percentage.'"  min="0" max="100" >'.
    '</div>'.
    '</div>';
         
    }

  }
  return $html;
}


