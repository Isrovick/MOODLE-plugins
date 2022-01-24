<?php
require('../../config.php');
//require_once($CFG->libdir.'/gdlib.php');
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();
require_login(0, false);


if(isset($_POST['payload'])){ 
    global $DB;

    $np = "/^[0-9]+$/i";
    $sp = "/^[A-z]+$/i";

    if(preg_match($np,$_POST['payload'])){
        //echo json_encode("number");
        /*$itemid=$_POST['id'];*/
         
        $items=$DB->get_records_sql("
            SELECT distinct(uid.userid) , uid.data, us.firstname, us.lastname FROM  bch_user_info_data as uid
            JOIN  bch_user us ON  uid.userid=us.id
            where uid.data like '%".$_POST['payload']."%'
            and uid.fieldid=(
                select id from  bch_user_info_field where shortname='cedula')"
            , null, 0,0);

        echo json_encode($items);        
        
    }
    elseif(preg_match($sp,$_POST['payload'])){
        $items=$DB->get_records_sql("
        
        SELECT distinct(uid.userid), uid.data, us.firstname, us.lastname FROM  bch_user_info_data as uid
        JOIN  bch_user us ON  uid.userid=us.id
        where (us.firstname like '%".$_POST['payload']."%' or us.lastname like '%".$_POST['payload']."%')
        and uid.fieldid=(
            select id from  bch_user_info_field where shortname='cedula')
        "
        , null, 0,0);

        echo json_encode($items);     
    }
    else{
        echo json_encode(false);
    }
    
}
