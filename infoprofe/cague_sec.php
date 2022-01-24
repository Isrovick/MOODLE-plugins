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

require_login(0, false);

defined('MOODLE_INTERNAL') || die();
$context = context_system::instance();

if(!has_capability('mod/infoprofe:seeadmcage', $context)){
  http_response_code(404);
  die(); 
}

$PAGE->set_url("$CFG->wwwroot/local/infoprofe/cague_sec.php");
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('C.A.G.E.');
$PAGE->set_heading("<h4> Generacion de Control de Actuacion general del estudiante</h4>"); 
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

echo "<h6> Esta pagina podra escoger cual Año de Media General desea ver informe respectivo.</h6><br>"; 

$data=array();

  $cat=$DB->get_records_sql("SELECT id, name FROM bch_course_categories where parent in
                            (SELECT id FROM bch_course_categories where idnumber=2)"
                            ,null,0,0);
  foreach ( $cat as &$category ){

    echo '<fieldset class="clearfix collapsible containsadvancedelements">
          <h4 class="ftoggler">
             Materias de '.$category->name.'
          </h4>
    ';       
      courses_links($category);
    echo '</fieldset>';
  }


function courses_links ($category){
    global $DB, $CFG;
  $cou=$DB->get_records_sql("SELECT id, fullname FROM `bch_course` where category=".$category->id
  ,null, 0, 0);

  echo '<div class="content" >';

  foreach ( $cou as &$course ){
    echo ' <div class="row"> 
              <div class="mat col-8">'.$course->fullname.'</div>
              <a href="'.$CFG->wwwroot.'/local/infoprofe/cage.php?t=A&id='.$course->id.'"><div class="col">Ver seccion A</div></a>
              <a href="'.$CFG->wwwroot.'/local/infoprofe/cage.php?t=B&id='.$course->id.'"><div class="col">Ver seccion B</div></a> 

          </div>';
  }

  echo '</div>';
}

echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/infoprofe/js/cage_functions.js"></script>';
echo $OUTPUT->footer(); 
