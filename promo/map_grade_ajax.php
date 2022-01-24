<?php
require('../../config.php');
//require_once($CFG->libdir.'/gdlib.php');
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();
require_login(0, false);

if(isset($_POST['id']) && isset($_POST['t'])){ 
    global $DB;

    if($_POST['t']==0){
        
        $itemid=$_POST['id'];
        $items=$DB->get_records_sql("Select id, itemname from bch_grade_items where itemname IS NOT NULL and courseid=".$itemid, null, 0,0);
        echo json_encode($items);
    }

    if($_POST['t']==1){
       
        $itemid=$_POST['id'];
        $items=$DB->get_record_sql("Select aggregationcoef from bch_grade_items where id=".$itemid, null, 0,0);
        echo json_encode($items);
    }
    if($_POST['t']==2){
       
        $itemid=$_POST['id'];
        $items=$DB->get_records_sql("Select * from bch_institution_grade_items where courseid=".$itemid." order by lp asc, ev asc", null, 0,0);
        echo json_encode($items);
    }
    
}
