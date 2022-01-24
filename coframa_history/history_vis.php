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

if(!has_capability('mod/coframa_history:generatehistory', $context)){
  http_response_code(404);
  die(); 
}

$PAGE->requires->jquery();
$PAGE->set_url("$CFG->wwwroot/local/coframa_history/history_vis.php");
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title('Historico Secciones');
$PAGE->set_heading("Visualizacion Historico por Secciones");
echo $OUTPUT->header();


$mform = new history_main_form(new moodle_url("/local/coframa_history/history_seccionlapso.php"),array()); 

if ($mform->is_cancelled()) {
  //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
//In this case you process validated data. $mform->get_data() returns data posted in form.

  
} else {
// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
// or on the first display of the form.

//Set default data (if any)
//$mform->set_data($toform);
//displays the form
  $mform->display();
  echo '<script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_menu/js/cancel.js"></script>';

  $data= $DB->get_record_sql("SELECT lastupdate FROM `bch_institution_hgrades` order by lastupdate desc limit 1", 
                              null, 0, 0);
  
  echo '
  <div class="d-grid gap-2 d-md-block container col-4"></div>
 
  <div class="d-grid gap-2 d-md-block container col-4"><hr></div>
  <div class="d-grid gap-2 d-md-block container col-4"></div>
            
  <div class="d-grid gap-2 d-md-block container col-4">
            <a href="'.$CFG->wwwroot.'/local/coframa_history/history_gen.php'.'">
                <button class="  align-self-center  btn btn-warning" type="button">Generar/Actualizar Historicos Finales</button>
            </a>
        </div>
        <div class="d-grid gap-2 d-md-block container col-4">
           Ultima Actualizacion: '.$data->lastupdate.'
        </div>
        <script type="text/javascript" charset="utf-8" data-requirecontext="_" src="'.$CFG->wwwroot.'/local/coframa_history/js/functions.js"></script>
        ';
}


echo $OUTPUT->footer();