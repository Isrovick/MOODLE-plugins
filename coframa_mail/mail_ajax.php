<?php
require('../../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once $CFG->libdir.'/adminlib.php';


defined('MOODLE_INTERNAL') || die();


require_login(0, false);

$context = context_system::instance();

if(!has_capability('mod/coframa_mail:sendadminemail', $context)){
    http_response_code(404);
    die(); 
}

if(isset($_POST['payload'])){ 
    global $DB;

    $payload=$_POST['payload'];

    $np = "/^[0-9]+$/i";
    $sp = "/^[A-z]+$/i";

    if(preg_match($np,$payload)){
         
        $items=$DB->get_records_sql("
            SELECT uid.userid, uid.data, us.firstname, us.lastname FROM  bch_user_info_data as uid
            JOIN  bch_user us ON  uid.userid=us.id
            where uid.data like 'v-".$payload."%'
            and uid.fieldid=(
                select id from  bch_user_info_field where shortname='cedula')"
            , null, 0,0);

        echo json_encode($items);        
        
    }
    elseif(preg_match($sp,$payload)){
        $items=$DB->get_records_sql("   
            SELECT uid.userid, uid.data, us.firstname, us.lastname FROM  bch_user_info_data as uid
            JOIN  bch_user us ON  uid.userid=us.id
            where (us.firstname like '%".$payload."%' or us.lastname like '%".$payload."%')
            and uid.fieldid=(
                select id from  bch_user_info_field where shortname='cedula') "
            , null, 0,0);

        echo json_encode($items);     
    }
    else{
        echo json_encode(false);
    }

}
elseif(isset($_POST['matpayload'])){ 
    global $DB;
    
    $matpayload=$_POST['matpayload'];

    $items=$rec= $DB->get_records_sql('SELECT id, fullname as name FROM `bch_course` where category ='.$matpayload, null, 0, 0);

    echo json_encode($items);        
        
}
elseif(isset($_POST['teacher'])){ 
    global $DB;

    $payload=$_POST['teacher'];
    $courseid=$_POST['cid'];
    $np = "/^[0-9]+$/i";
    $sp = "/^[A-z]+$/i";

    if(preg_match($np,$payload)){
         
        $items=$DB->get_records_sql("
            SELECT uid.userid, uid.data, us.firstname, us.lastname FROM  bch_user_info_data as uid
            JOIN  bch_user us ON  uid.userid=us.id
            where uid.data like 'v-".$payload."%'
            and uid.userid in
                            (SELECT distinct(userid) as id FROM bch_role_assignments where roleid=5 and contextid in
                                                        (SELECT distinct(id) FROM bch_context where contextlevel=50 and instanceid =".$courseid.")
                            )
            and uid.fieldid=(
                            select id from  bch_user_info_field where shortname='cedula')"
            , null, 0,0);

        echo json_encode($items);        
        
    }
    elseif(preg_match($sp,$payload)){
        $items=$DB->get_records_sql("   
            SELECT uid.userid, uid.data, us.firstname, us.lastname FROM  bch_user_info_data as uid
            JOIN  bch_user us ON  uid.userid=us.id
            where (us.firstname like '%".$payload."%' or us.lastname like '%".$payload."%')
            and uid.userid in
                            (SELECT distinct(userid) as id FROM bch_role_assignments where roleid=5 and contextid in
                                                        (SELECT distinct(id) FROM bch_context where contextlevel=50 and instanceid =".$courseid.")
                            )
            and uid.fieldid=(
                            select id from  bch_user_info_field where shortname='cedula') "
            , null, 0,0);

        echo json_encode($items);     
    }
    else{
        echo json_encode(false);
    }

}