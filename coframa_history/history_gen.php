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

require_once('../coframa_user/user_individual.php');

defined('MOODLE_INTERNAL') || die();
require_login(0, false);


$context = context_system::instance();

if(!has_capability('mod/coframa_history:generatehistory', $context)){
  http_response_code(404);
  die(); 
}

$PAGE->requires->jquery();
$PAGE->set_url("$CFG->wwwroot/local/coframa_history/history_gen.php");
$PAGE->set_context($context);
$PAGE->set_title('Historico Secciones');
$PAGE->set_heading("Generacion / Actualizacion de Historico final de año");
$PAGE->requires->jquery();
echo $OUTPUT->header();

$period= $DB->get_record_sql("SELECT id,meta_value FROM `bch_institution_meta` 
                                            where meta_key='periodo' 
                                            and meta_date = (SELECT MAX(meta_date) as md 
                                                            FROM `bch_institution_meta` 
                                                            WHERE meta_key='periodo')", null,0,0); 

$niveles= array('1A',
                '2A',
                '3A',
                '4A',
                '5A',
                );
        
foreach($niveles as $nivel){

    $users=$DB->get_records_sql('SELECT distinct(userid) as id FROM bch_role_assignments where roleid=5 and contextid in
                                (SELECT distinct(id) FROM bch_context where contextlevel=50 )
                                    and userid in 
                                (SELECT userid FROM bch_user_info_data where data=\''.$nivel.'\' and fieldid = 
                                    (select id from bch_user_info_field where shortname=\'nivel\')
                                )',null, 0, 0);
    
    foreach($users as &$user){
            
            $pur=profile_user_record($user->id);
            $student_niv=$pur->nivel;
            $student_idn=$DB->get_field('user', 'idnumber', array('id'=>$user->id));
            $student_sec=$pur->seccion;


            $huser=array(
                'userid'=>$user->id,
                'periodid'=>$period->id,
            );
            $huserobj=array(
                'userid'=>$user->id,
                'periodid'=>$period->id,
                'idnumber'=>$student_idn,
                'seccion'=>$student_sec,
                'nivel'=>$student_niv,
            );
            
            
           
            if(!$DB->record_exists('institution_huser', $huser)){
                $huserid=$DB->insert_record('institution_huser', $huserobj, true);
            }
            else{
                $huserid=$DB->get_field('institution_huser', 'id', $huser);
                $huserobj['id']= $huserid;     
                $DB->update_record_raw('institution_huser', $huserobj);
            }
            
            $ulf=new user_individual($user->id);
            $fgs=$ulf->generate_final_grades();
            
            foreach($fgs as $courseid=>$finalgrade){
                
                $hgrade=array(
                    'courseid'=>$courseid,
                    'huserid'=>$huserid,
                );
                $hgradeobj=array(
                    'courseid'=>$courseid,
                    'huserid'=>$huserid,
                    'finalgrade'=>$finalgrade,
                    'lastupdate'=>date('Y-m-d H:i:s'),
                );
                
                if(!$DB->record_exists('institution_hgrades', $hgrade)){
                    $hgradeid=$DB->insert_record('institution_hgrades', $hgradeobj, false);
                }
                else{
                    $hgradeid=$DB->get_field('institution_hgrades', 'id', $hgrade);
                    $hgradeobj['id']= $hgradeid;     
                    $DB->update_record_raw('institution_hgrades', $hgradeobj);
                }

            }
    }                           
}
echo '<div class="alert alert-primary" role="alert">
            <h4>Generado/Actualizado registro de notas por seccion.</h4>
      </div>';
echo $OUTPUT->continue_button("history_vis.php");
echo $OUTPUT->footer();