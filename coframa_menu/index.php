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
require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();
require_login(0, false);
$context = context_system::instance();


$PAGE->set_url("$CFG->wwwroot/local/coframa_menu/index.php");
$PAGE->set_context($context);
$PAGE->requires->jquery();
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Menu COFRAMA');
$PAGE->set_heading("<h3>Menu COFRAMA</h3>");


echo $OUTPUT->header();

 
  if(has_capability('mod/coframa_menu:inscribe', $context)){
    echo '
    <div class="block clearfix odd first">
      <div class="info">
        <h3 class="coursename">
          <a class="" href="'.new moodle_url("/local/promo/index.php").'">Matricular Alumnos</a>
        </h3>
      <div class="moreinfo"></div></div>
      <div class="content">
            <div class="summary fullsummarywidth">
                <div class="no-overflow">Esta opcion es para Matricular alumnos masivamente o individualmente en las materias correspondientes a su nivel.</div>
            </div>
      </div>
    </div> 
    '; 
 
    echo '
    <div class="block clearfix odd first">
      <div class="info">
        <h3 class="coursename">
          <a class="" href="'.new moodle_url("/local/promo/desmatricular.php").'">Des-Matricular Alumnos. </a>
        </h3>
      <div class="moreinfo"></div></div>
      <div class="content">
            <div class="summary fullsummarywidth">
                <div class="no-overflow">Esta opcion es para desmatricular el alumno de las materias requeridas.</div>
            </div>
      </div>
    </div> 
    '; 

    echo '
    <div class="block clearfix odd first">
      <div class="info">
        <h3 class="coursename">
          <a class="" href="'.new moodle_url("/local/promo/restablecerm.php").'">Reestablecer matriculaciones. </a>
        </h3>
      <div class="moreinfo"></div></div>
      <div class="content">
            <div class="summary fullsummarywidth">
                <div class="no-overflow">Esta opcion es para reestablecer masivamente el las Matriculaciones de alumnos.</div>
            </div>
      </div>
    </div> 
    '; 
}

if(has_capability('mod/infoprofe:seeinform', $context)){
    echo '
    <div class="block clearfix odd first">
      <div class="info">
        <h3 class="coursename">
          <a class="" href="'.new moodle_url("/local/promo/choose_revision_inform.php").'">Revision de informes</a>
        </h3>
      <div class="moreinfo"></div></div>
      <div class="content">
            <div class="summary fullsummarywidth">
                <div class="no-overflow">Esta opcion es para revisar informes de alumnos.</div>
            </div>
      </div>
    </div> 
    ';
} 

if(has_capability('mod/infoprofe:seeinform', $context)){
    echo '
    <div class="block clearfix odd first">
      <div class="info">
        <h3 class="coursename">
          <a class="" href="'.new moodle_url("/local/promo/choose_inform_send.php").'">Envio de informes de Primara e Inicial</a>
        </h3>
      <div class="moreinfo"></div></div>
      <div class="content">
            <div class="summary fullsummarywidth">
                <div class="no-overflow">Esta opcion es para realizar el envio de informes.</div>
            </div>
      </div>
    </div> 
    ';

    echo '
    <div class="block clearfix odd first">
      <div class="info">
        <h3 class="coursename">
          <a class="" href="'.new moodle_url("/local/promo/choose_grade_send.php").'">Envio de Boletines Media General</a>
        </h3>
      <div class="moreinfo"></div></div>
      <div class="content">
            <div class="summary fullsummarywidth">
                <div class="no-overflow">Esta opcion es para realizar el envio de Boletines.</div>
            </div>
      </div>
    </div> 
    ';
}

if(has_capability('mod/coframa_mail:sendadminemail', $context)){
    echo '
    <div class="block clearfix odd first">
      <div class="info">
        <h3 class="coursename">
          <a class="" href="'.new moodle_url("/local/coframa_mail/admin_email.php").'">Envio de Correo</a>
        </h3>
      <div class="moreinfo"></div></div>
      <div class="content">
            <div class="summary fullsummarywidth">
                <div class="no-overflow">Esta opcion es para realizar el envio de Correo por parte de la administracion.</div>
            </div>
      </div>
    </div> 
    ';
}
if(has_capability('mod/infoprofe:seecage', $context)){ 
echo '
<div class="block clearfix odd first">
  <div class="info">
    <h3 class="coursename">
       <a class="" href="'.new moodle_url("/local/infoprofe/cague_sec.php").'">Control de la actuacion General del estudiante</a>
    </h3>
  <div class="moreinfo"></div></div>
  <div class="content">
        <div class="summary fullsummarywidth">
            <div class="no-overflow"> Esta opcion es para realizar la emision del Control de la actuacion General del estudiante.</div>
        </div>
  </div>
</div> 
'; 
}

if(has_capability('mod/coframa_history:generatehistory', $context)){ 
  echo '
  <div class="block clearfix odd first">
    <div class="info">
      <h3 class="coursename">
         <a class="" href="'.new moodle_url("/local/coframa_history/history_vis.php").'">Visualizar Historial de Secciones</a>
      </h3>
    <div class="moreinfo"></div></div>
    <div class="content">
          <div class="summary fullsummarywidth">
              <div class="no-overflow">Esta opcion es para visualizar Historial de Secciones por periodo escolar, incluye todas las materias.</div>
          </div>
    </div>
  </div> 
  '; 
  }

if(has_capability('mod/coframa_menu:modifymetadata', $context)){ 
  echo '
  <div class="block clearfix odd first">
    <div class="info">
      <h3 class="coursename">
         <a class="" href="'.new moodle_url("/local/coframa_menu/menumeta.php").'">Informacion institucion</a>
      </h3>
    <div class="moreinfo"></div></div>
    <div class="content">
          <div class="summary fullsummarywidth">
              <div class="no-overflow">Esta opcion es para realizar la Modificacion de la data de la institucion.</div>
          </div>
    </div>
  </div> 
  '; 
  
  }


echo $OUTPUT->footer();