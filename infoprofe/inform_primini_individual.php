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


require_once($CFG->libdir.'/filelib.php');
require('informes/informe.php');
require_once($CFG->libdir.'/gdlib.php');
require_once $CFG->libdir.'/adminlib.php';

require_login(0, false);

defined('MOODLE_INTERNAL') || die();
$context = context_system::instance();
if(!has_capability('mod/infoprofe:sendemails', $context)){
  http_response_code(404);
  die(); 
}

$context = context_system::instance();
$PAGE->set_url("$CFG->wwwroot/local/infoprofe/inform_primini_individual.php");
$PAGE->set_context($context);
$PAGE->set_title('Visualizacion de informe ');


class inform {

    protected int $userid;
    protected int $lapso;
    protected string $out;
    protected string $force;
    protected object $period;
    protected object $pur;
    protected object $student;

    public function __construct($userid, $lapso, $out, $force=false){
        global $DB;

        $this->userid=$userid;
        $this->lapso=$lapso;
        $this->out=$out;
        $this->force=$force;

        $this->period= $DB->get_record_sql("SELECT id,meta_value FROM `bch_institution_meta` 
        where meta_key='periodo' 
        and meta_date = (SELECT MAX(meta_date) as md 
                        FROM `bch_institution_meta` 
                        WHERE meta_key='periodo')", 
        null, 0, 0);
        $this->student=$DB->get_record("user", array ("id"=>$this->userid), 'firstname,lastname,idnumber,address');
        $this->pur=profile_user_record($this->userid);



    }

    public function generate_inform(){
        global $DB;

        $period= $this->period;
        $userid=$this->userid;
        $student=$this->student;
        $pur=$this->pur;
        $student_nli=$student->idnumber;
        $student_add=$student->address;
        $student_ced=$pur->cedula;
        $student_sec=$pur->seccion;
        $student_niv=$pur->nivel;
        $student_btd=$pur->bday.'-'.$pur->bmonth.'-'.$pur->byear;
        $student_fln= $student->firstname." ".$student->lastname;
        $usercontext = context_user::instance($userid);
        $aux = $DB->get_record('role_assignments', array('contextid' => $usercontext->id, 'roleid'=>'9'));
        
        $suspended=$DB->get_field('user', 'suspended', array('id'=>$userid));

        if($suspended && $this->out!='I'){
            return array($student_fln,$student_ced,$cat_n,$student_sec,"NO ENVIADO, USUARIO SUSPENDIDO");
        }
        
        $userto = false;
        $usertocontext=NULL;
        $parent_fln = 'REPRESENTANTE NO ASIGNADO EN SISTEMA';
        
          if(!$aux){ 
              if($this->out!='I')return array($student_fln ,$student_ced,$cat_n,$student_sec,"NO ENVIADO, SIN REPRESENTANTE.");
          }
          else{
            $userto = $DB->get_record('user', array('id' => $aux->userid));
              if(!$userto){ 
                if($this->out!='I') return array($student_fln ,$student_ced,$cat_n,$student_sec,"NO ENVIADO, SIN REPRESENTANTE.");
              }else{
                $usertocontext=context_user::instance($userto->id);
                $parent_fln = $userto->firstname.", ".$userto->lastname;
              }
          }


        $category=$DB->get_record_sql('SELECT id , name FROM bch_course_categories  where id=
                                        (select category from  
                                            (SELECT count(category) count, category FROM coframa_1.bch_course where id in 
                                                (SELECT distinct(courseid) as id  FROM coframa_1.bch_enrol a where roleid=5 and a.id in 
                                                (SELECT enrolid as id FROM coframa_1.bch_user_enrolments where userid='.$this->userid.')) 
                                            group by category order by count desc) 
                                        s limit 1)',null,0,0);

        $cou=$DB->get_records_sql('SELECT id, summary as fullname FROM coframa_1.bch_course where id in 
                                    (SELECT distinct(courseid) as id  FROM coframa_1.bch_enrol a where roleid=5 and a.id in 
                                        (SELECT enrolid as id FROM coframa_1.bch_user_enrolments where userid='.$this->userid.'))
                                        order by category desc',null, 0, 0);
        
        $id_director= $DB->get_record_sql("SELECT id,meta_value FROM `bch_institution_meta` 
                                            where meta_key='id_director' 
                                            and meta_date = (SELECT MAX(meta_date) as md 
                                                            FROM `bch_institution_meta` 
                                                            WHERE meta_key='id_director')", null,0,0);

        $record_director = $DB->get_record('user', array('id' => $id_director->meta_value));
        $nombre_director = $record_director->firstname.' , '.$record_director->lastname;


        $size = array('large' => 'f1', 'small' => 'f2');
        $src = null;
        //$src = get_file_url($userid.'/'.$size['small'].'.jpg', null, 'user');
        
        $tipo_eval=' ';
        $lp=$this->lapso;
            if($lp==3){
              $tipo_eval=' FINAL ';
            }

            $force=$this->force;
            if($force){
              $force=time();    
            }
                $inicial_flag=false;
            if(preg_match('/([1-6][N])/', $student_niv)){
                $inicial_flag=true;
            }

            $literal_momento='';
            switch($this->lapso){
                case 1: $literal_momento='I';break;
                case 2: $literal_momento='II';break;
                case 3: $literal_momento='III';break;
                default: break;
            }

            $obj= array(   
                'nombreyapellido'=>$student_fln,
                'cedula'=>$student_ced,
                'user_picture'=>$src,
                'año'=>'',
                'fecha_nacimiento'=>$student_btd,
                'seccion'=>$student_sec,
                'año_esc'=>$period->meta_value,
                'fecha'=>date('d/m/Y'),
                'n_representante'=>$parent_fln,
                'tipo_eval'=>$tipo_eval,
                'momento'=>$literal_momento,
                'inicial'=>$inicial_flag,
                'nombre_director'=>$nombre_director,
             );

        $pdf= new informpdf(); 

        foreach ( $cou as &$course ){
            $record=$this->get_inform_item($course->id);
            //var_dump($record);
            //die();
            if($record){
                $this->load_single_inform($record,$obj,$pdf);
            }
            else{
                $this->load_fail_inform($obj,$pdf,$course->id);
                if($this->out!='I') return array($student_fln,$student_ced,$cat_n,$student_sec,"NO ENVIADO, INFORMES SIN REVISAR.");
            }
        }
        
        $doc=$pdf->inform_full($obj,$this->out);
        if($this->out=='I') return null;

        require_once($CFG->dirroot.'/message/output/email/message_output_email.php');

            $message = new message_output_email();
            $message->courseid = SITEID; 
            $message->userfrom = \core_user::get_noreply_user();
            $message->userto = $userto;
            $message->notification = '0';
            $message->conversationtype = null;
            $message->subject = 'Informe Momento '.$obj['momento'].', '.$obj['año_esc'];
            $message->fullmessageformat = FORMAT_MARKDOWN;
            $message->fullmessagehtml = '
            
                <h2>Bienvenido al sistema de envio de notificaciones en linea COFRAMA.</h2> 
                <p>El siguiente correo es para entregarle el informe de desempeño del alumno
                '.$obj['nombreyapellido'].', '.$obj['cedula'].', en la materia '.$obj['titulo_materia'].' de '.$obj['año'].'.</p>
                <h3> Coframa, Educacion y excelencia!</h3>. ';
            $message->fullmessage=$message->fullmessagehtml;
                
            $file = new stdClass;
            $file->contextid = $usertocontext->id;
            $file->component = 'user';
            $file->filearea  = 'private';
            $file->itemid    = 0;
            $file->filepath  = '/';
            $file->filename  = $obj['cedula'].'M'.$obj['momento'].'C'.$record->id_course.time().'.pdf';
            $file->source    = 'coframa';
            
            $message->attachname =  $file->filename;

            $fs = get_file_storage();
                
            $fileExist = $fs->get_file($file->contextid, $file->component, $file->filearea, $file->itemid, $file->filepath, $file->filename);
            if ($fileExist) {
                if(!$this->force){
                  return array($student_fln,$student_ced,$cat_n,$student_sec,"NO ENVIADO, INFORME ENVIADO PREVIAMENTE.");
                }
                else{
                  $file->delete();
                }
            }

            $file = $fs->create_file_from_string($file, $doc);///*
            $message->attachment = $file;

            if($message->send_message($message)){ 
                
                return array($student_fln,$student_ced,$obj['año'],"ENVIADO");
            }
            else{ 
                $file->delete();
                return array($student_fln,$student_ced,$obj['año'],"NO ENVIADO, INTENTE DENUEVO LUEGO.");
            }

    }
    
    public  function get_inform_item($courseid){
        global $CFG;
        global $DB;
        
          
        $record= $DB->get_record_sql("SELECT c.fullname, ii.meta_key, ii.meta_name, ii.description, ii.id_course FROM `bch_institution_inform` ii 
                                      join `bch_course` as c on  c.id=ii.id_course
                                      where (ii.id_type='momento' or  ii.id_type='proyecto')
                                      and meta_key=".$this->lapso." 
                                      and ii.id_period=".$this->period->id."
                                      and ii.state='POR ENVIAR' 
                                      and ii.id_propietary= ".$this->userid."
                                      and ii.id_course=".$courseid." order by c.fullname ASC", null, 0, 0);                        
        return $record;                              
    }

    public function load_single_inform($record,$obj,$pdf){
        global $DB;
        
        $mat=$DB->get_record_sql("SELECT b.fullname as mat, a.name as ano 
                                    FROM `bch_course` b
                                    join (select id, name from `bch_course_categories` ) 
                                    a on category=a.id  where b.id=".$record->id_course );

        $profn="";                        
        $profid=$DB->get_record_sql("SELECT distinct(userid) as id FROM bch_role_assignments where roleid=3 and contextid in
                                    (SELECT distinct(id) FROM bch_context where contextlevel=50 and instanceid in
                                    (SELECT id FROM `bch_course` where id=".$record->id_course."))"
                                    ,null, 0, 0);
        if($profid){
            $prof=$DB->get_record("user", array ("id"=>$profid->id), 'firstname,lastname');
            $profn= $prof->firstname.', '.$prof->lastname; 
        }  

        $asisn="";  
        $asisid=$DB->get_record_sql("SELECT distinct(userid) as id FROM bch_role_assignments where roleid=4 and contextid in
                                    (SELECT distinct(id) FROM bch_context where contextlevel=50 and instanceid in
                                    (SELECT id FROM `bch_course` where id=".$record->id_course."))"
                                    ,null, 0, 0);
        if($asisid){
            $asis=$DB->get_record("user", array ("id"=>$asisid->id), 'firstname,lastname');
            $asisn=$asisn=$asis->firstname.', '.$asis->lastname;
        }  

        $aux_obj=$obj;
        $aux_obj['año']=$mat->ano;
        $aux_obj['proyecto']=$record->meta_name;
        $aux_obj['titulo_materia']=$mat->mat;
        $aux_obj['docente']=$profn;
        $aux_obj['asistente']=$asisn;
        $aux_obj['informe_descriptivo']=$record->description;

        $pdf->individual_inform($aux_obj);

    }

    public function load_fail_inform($obj,$pdf,$courseid){
        global $DB;
        
        $mat=$DB->get_record_sql("SELECT b.fullname as mat, a.name as ano 
                                    FROM `bch_course` b
                                    join (select id, name from `bch_course_categories` ) 
                                    a on category=a.id  where b.id=".$courseid );

       

        $aux_obj=$obj;
        $aux_obj['año']=$mat->ano;
        $aux_obj['titulo_materia']=$mat->mat;

        $pdf->inform_fail($aux_obj);

    }
       
      

}