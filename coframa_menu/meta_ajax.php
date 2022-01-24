<?php
require('../../config.php');
//require_once($CFG->libdir.'/gdlib.php');
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();
require_login(0, false);
$context = context_system::instance();

if(!has_capability('mod/coframa_menu:modifymetadata', $context)){
    http_response_code(404);
    die(); 
}
$PAGE->requires->jquery();

require_once($CFG->dirroot.'/local/clean.php');
$c=new clean($_GET,$_POST);

if(isset($_POST['payload'])){ 
    $payload=$_POST['payload'];
    global $DB;

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
            where (us.firstname like '%".$payload."%' or us.lastname like '%".$payload."%') "
            , null, 0,0);

        echo json_encode($items);     
    }
    else{
        echo json_encode(false);
    }
    
}
else{

    if(isset($_POST['meta_key'])){
        $meta_key=$_POST['meta_key'];
        if(isset($_POST['meta_value'])){
            if($_POST['meta_value']!=""){
                $data=array(
                    'meta_key'=>$meta_key,
                    'meta_value'=>$_POST['meta_value'],
                    'parent_meta'=>null,
                    'meta_date'=>date('Y-m-d H:i:s'),
                    );
                   
                    if($DB->insert_record('institution_meta', $data)){
                        header('Location: '.$CFG->wwwroot.'/local/coframa_menu/menumeta.php?s=1');
                        die();
                      }
                      else{
                        header('Location: '.$CFG->wwwroot.'/local/coframa_menu/menumeta.php?s=0');
                        die();
                    }
            }
            else{
                        header('Location: '.$CFG->wwwroot.'/local/coframa_menu/menumeta.php?s=0');
                        die();
            }
        }
        elseif(isset($_POST['userid'])){
                $userid=$c->p('/([0-9])+/','userid');
                $data=array(
                    'meta_key'=>$meta_key,
                    'meta_value'=>$userid,
                    'parent_meta'=>null,
                    'meta_date'=>date('Y-m-d H:i:s'),
                    );
                    
                if($DB->insert_record('institution_meta', $data)){
                    header('Location: '.$CFG->wwwroot.'/local/coframa_menu/menumeta.php?s=1');
                    die();
                }
                else{
                    header('Location: '.$CFG->wwwroot.'/local/coframa_menu/menumeta.php?s=0');
                    die();
                }
             
        }
        

    }
  
}
