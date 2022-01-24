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

require_once($CFG->dirroot.'/enrol/locallib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/enrol/manual/locallib.php');
require_once($CFG->dirroot.'/cohort/lib.php');

defined('MOODLE_INTERNAL') || die();
$context = context_system::instance();
if(!has_capability('mod/infoprofe:enroll', $context)){
  http_response_code(404);
  die(); 
}
require_login(0, false);

$context = context_system::instance();
$PAGE->set_url("$CFG->wwwroot/local/promo/vismatricular.php");
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title('visualizacion de Matriculacion');
$PAGE->set_heading('visualizacion de Matriculacion masiva');
$PAGE->requires->jquery();

echo $OUTPUT->header();

$roleid = 5;
$recovergrades = optional_param('recovergrades', 0, PARAM_INT);
$timestart = make_timestamp($_POST['year'], $_POST['month'], $_POST['day'], 0, 0, 0);;
$timeend =  strtotime('+334 days', $timestart);



foreach($_POST as $k => $v) {
    if(strpos($k, 'cid_') === 0) {

            $courseid = $v; // Course id.
            $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
            $context = context_course::instance($courseid, MUST_EXIST);
            
            if ($courseid == SITEID) {
                throw new moodle_exception('invalidcourse');
            }
            
                $manager = new course_enrolment_manager($PAGE, $course);
                $enrolid = $DB->get_record_sql("select id from bch_enrol where enrol='manual' and courseid=".$courseid)->id;      
                $instances = $manager->get_enrolment_instances();
                $plugins = $manager->get_enrolment_plugins(true); // Do not allow actions on disabled plugins.
                if (!array_key_exists($enrolid, $instances)) {
                    throw new enrol_ajax_exception('invalidenrolinstance');
                }
                $instance = $instances[$enrolid];
                if (!isset($plugins[$instance->enrol])) {
                    throw new enrol_ajax_exception('enrolnotpermitted');
                }
                $plugin = $plugins[$instance->enrol];
                if ($plugin->allow_enrol($instance) && has_capability('enrol/'.$plugin->get_name().':enrol', $context)) {
                    foreach($_POST as $l =>  $userid) {
                        if(strpos($l, 'userid_') === 0) {
                            $plugin->enrol_user($instance, $userid, $roleid, $timestart, $timeend, null, $recovergrades);
                        }
                    }
                
                } else {
                    throw new enrol_ajax_exception('enrolnotpermitted'); 
                }
    }             
}  

echo $OUTPUT->continue_button('index.php');   
echo $OUTPUT->footer();
