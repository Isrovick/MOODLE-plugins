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

require_login(0, false);


defined('MOODLE_INTERNAL') || die();
$context = context_system::instance();
if(!has_capability('mod/coframa_menu:modifymetadata', $context)){
  http_response_code(404);
  die(); 
}

$PAGE->requires->jquery();
$context = context_system::instance();
$PAGE->set_url("$CFG->wwwroot/local/coframa_menu/menumeta.php");
$PAGE->set_context($context);
$PAGE->set_title('Actualizacion Metadata');
$PAGE->set_heading( "<h2> Informacion Instituto</h2>"); 

$PAGE->requires->jquery();
echo $OUTPUT->header();

echo "
  <style>
      .collapsible {
        background-color: #ced4da;
        color: white;
        cursor: pointer;
        padding: 18px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 15px;
        margin: 10px;
      }

      .active, .collapsible:hover {
        background-color: #1177d1;
      }

      .content {
        color: black;
        padding: 0 18px;
        display: none;
        overflow: hidden;
        background-color: #fff;
      }

  </style>
";
if(isset($_GET['s'])){
  if($_GET['s']){
    echo '<h5 >Datos Actualizados con exito!</h5><hr>';
  }
  else{
    echo '<h5 >Ocurrio un error, Intente luego nuevamente...</h5><hr>';
  }
}
echo "<h5> Metadata Actual: </h5>"; 

$meta_keys=$DB->get_records_sql("SELECT distinct(meta_key) FROM `bch_institution_meta`", null, 0, 0);

  foreach ($meta_keys as $meta_key) {

    $data= $DB->get_record_sql("SELECT id,meta_value FROM `bch_institution_meta` 
                            where meta_key='$meta_key->meta_key' 
                            and meta_date = (SELECT MAX(meta_date) as md 
                                            FROM `bch_institution_meta` 
                                            WHERE meta_key='$meta_key->meta_key')", 
                            null, 0, 0);

    $mk=strtoupper(str_replace("_", " ",$meta_key->meta_key ));
    $url=$CFG->wwwroot.'/local/coframa_menu/modifymeta.php?mk='. $data->id;
    $dmv=$data->meta_value;
    
        switch ($meta_key->meta_key) {
          case "id_director":
            $mk="DIRECTOR ACTUAL";    
                $dir=$DB->get_record("user", array ("id"=>$dmv), 'firstname,lastname');
                $dir_ced=profile_user_record($dmv)->cedula;
                $dmv= $dir->firstname.", ".$dir->lastname.". ".$dir_ced;
                break;
          case "id_pede":
            $mk="PEDE";    
                $dir=$DB->get_record("user", array ("id"=>$dmv), 'firstname,lastname');
                $dir_ced=profile_user_record($dmv)->cedula;
                $dmv= $dir->firstname.", ".$dir->lastname.". ".$dir_ced;
                break;
          
          default:
              break;
        }

    echo '<fieldset class="clearfix collapsible containsadvancedelements">
          <h4 class="ftoggler">
            '.$mk.'
          </h4>
          <div class="content" >
            <div class="row"> 
              <div class="mat col-8">'.$dmv.'</div>
              <a href="'.$url.'"><div class="col">Renovar Informacion</div></a>
            </div>
          </div>
        </fieldset>';
  }

  echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/infoprofe/js/cage_functions.js"></script>';
echo $OUTPUT->footer();