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

//require('../../config.php');
require_once($CFG->libdir.'/filelib.php');
require('informes/boletin.php');
require('../coframa_user/user_individual.php');
require_once($CFG->libdir.'/gdlib.php');
//require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';

require_login(0, false);

defined('MOODLE_INTERNAL') || die();
$context = context_system::instance();
if(!has_capability('mod/infoprofe:sendemails', $context)){
  http_response_code(404);
  die(); 
}

$context = context_system::instance();
$PAGE->set_url("$CFG->wwwroot/local/infoprofe/boletin_sec_individual.php");
$PAGE->set_context($context);

class boletin {

    protected int $userid;
    protected int $lapso;
    protected string $out;
    protected string $force;
    protected object $period;
    protected user_individual $user;

    public function __construct($userid, $lapso, $out, $force=false){
        global $DB;

        $this->userid=$userid;
        $this->lapso=$lapso;
        $this->out=$out;
        $this->force=$force;

        $this->user=new user_individual($userid);
        $this->period= $DB->get_record_sql("SELECT id,meta_value FROM `bch_institution_meta` 
                                            where meta_key='periodo' 
                                            and meta_date = (SELECT MAX(meta_date) as md 
                                                            FROM `bch_institution_meta` 
                                                            WHERE meta_key='periodo')", null,0,0); 
    

    }

    public function generate_boletin(){

        global $CFG;
        global $DB;
        global $data;
      
        $ncol=array(4,4,4,1);
        
        $bg=$this->get_boletin_item($ncol);
        $ii=$this->get_inform_item();
        $res=$this->mail_student_grade($this->userid,$bg,$ii);
        return $res; 
        
      }

    public  function get_inform_item(){
        global $CFG;
        global $DB;
        
          $period= $DB->get_record_sql("SELECT id,meta_value FROM `bch_institution_meta` 
                                        where meta_key='periodo' 
                                        and meta_date = (SELECT MAX(meta_date) as md 
                                                        FROM `bch_institution_meta` 
                                                        WHERE meta_key='periodo')", 
                                null, 0, 0);
          $rec= $DB->get_records_sql("SELECT c.fullname, ii.meta_key, ii.description FROM `bch_institution_inform` ii 
                                      join `bch_course` as c on  c.id=ii.id_course
                                      where ii.id_type='momento' and ii.id_period=".$this->period->id."
                                      and ii.state='POR ENVIAR' and ii.id_propietary= ".$this->userid."
                                      and ii.meta_key=".$this->lapso."
                                      order by 1 ASC", 
                                    null, 0, 0);
      
        return $rec;                    
      }

    public function get_boletin_item($ncol){
      global $CFG;
      global $DB;
    

      $html='<table border="1" class="boletin">';
      $u=$this->user->generate_full();
      foreach($u as $k=>$bi){
        if(!is_numeric($k)) continue;
         
        $y=$bi['qual'];
        $html.= '<tr> <td align="center" colspan="5">'.$bi['fullname'].'</td>';
            $gk=$bi['boletin_items'];
          for($i=1;$i<=3;$i++){
              $gi=$gk[$i];
              for($j=0;$j<$ncol[$i-1];$j++){
    
                if(isset($gi[$j])){
                  $gg=$gi[$j];
    
                  $htmlaux ='<td align="center" colspan="1">'.$this->n($y,$gg['lp_g']).'</td>'; 
                  $htmlaux.='<td align="center" colspan="1">'.$this->n($y,$gg['cd_g']).'</td>';       
                  $html.=$htmlaux;
                }
                else{
                  $htmlaux='<td align="center" colspan="1"></td>'; 
                  $htmlaux.='<td align="center" colspan="1"></td>';       
                  $html.=$htmlaux;
                }
    
              }//$j
                $html.='<td align="center" colspan="2">'.$this->n($y,$gi['finallapsocurso']).'</td>';
          
          }//$i
          
            
        if($this->lapso==3){
        

          $html.='<td align="center" colspan="2">'.$this->n($y,number_format ($gk['total'],1)).'</td>'; 
          $nota_revision=' - ';
              
            if(isset($gk['revision'][0])){
                $gg = $gk['revision'][0];
                $html.='<td align="center" colspan="1">'.$this->n($y,$gg['lp_g']).'</td>
                        <td align="center" colspan="1">'.$this->n($y,$gg['cd_g']).'</td>';
            }
            else{
              $html.='<td align="center" colspan="1"></td>';
              $html.='<td align="center" colspan="1"></td>';
            } 
            $html.='<td align="center" colspan="2">'.$this->n($y,number_format ($gk['final'],1)).'</td>'; 
        }
        else{
              $html.='<td align="center" colspan="2">-</td>';
              $html.='<td align="center" colspan="2">-</td>';
              $html.='<td align="center" colspan="2">-</td>';
        }       
          
        
          $html.='</tr>';   
      }
       
      if($this->lapso==3){
      
        $html.='
                <tr>
                <td align="center" colspan="35"> PROMEDIO </td>
                <td align="center" colspan="4">'.($u['promedio_info']['promedio']).'  /  '.$this->n(true,($u['promedio_info']['promedio'])).'</td>
                </tr>';
      }
      else{
        $html.='
          <tr>
            <td align="center" colspan="35"> PROMEDIO </td>
            <td align="center" colspan="4">-</td>
          </tr>';
      }
                
      $html.=' </table>';
      
      return $html;
    }
    
    
    public function mail_student_grade($userid,$bol_grade,$informs){
    
        global $CFG;
        global $DB;
        global $category; 
            $cat_n=$this->user->cat();
            $period= $this->period ;
            $student=$this->user->student;
            $pur=$this->user->pur;
            $student_ced=$pur->cedula;
            $student_sec=$pur->seccion;
            $student_btd=$this->user->student_btd;
            $student_fln= $this->user->student_fln;
            $usercontext = context_user::instance($userid);
            $aux = $DB->get_record('role_assignments', array('contextid' => $usercontext->id, 'roleid'=>'9'));
            
            $suspended=$DB->get_field('user', 'suspended', array('id'=>$userid));
            $idnumber=$DB->get_field('user', 'idnumber', array('id'=>$userid));

            if($suspended && $this->out!='I'){
              return array($student_fln,$student_ced,$cat_n,$student_sec,"NO ENVIADO, USUARIO SUSPENDIDO");
            }

            $userto = false;
            $usertocontext=NULL;
            $parent_fln = '<BR>REPRESENTANTE NO ASIGNADO EN SISTEMA';
            
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
          
              $size = array('large' => 'f1', 'small' => 'f2');
              $src = null;
              //$src = get_file_url($userid.'/'.$size['small'].'.jpg', null, 'user');
    
              $tipo_eval='Parcial';
              $lp=$this->lapso;
                  if($lp==3){
                    $tipo_eval='Final';
                  }
              
              $force=$this->force;
              if($force){
                $force=time();    
              }
             
              $obj= array(    
                'nombreyapellido'=>$student_fln,
                'cedula'=>$student_ced,
                'n_lista'=>$idnumber,
                'user_picture'=>$src,
                'año'=>$cat_n,
                'fecha_nacimiento'=>$student_btd,
                'loc_nac'=>$pur->placeofbirth,
                'edo_nac'=>'-',
                'seccion'=>$student_sec,
                'año_esc'=>$period->meta_value,
                'fecha'=>date('d/m/Y'),
                'boletin_grades'=>$bol_grade,
                'boletin_informs'=>$informs,
                'n_representante'=>$parent_fln,
                'tipo_eval'=>$tipo_eval,
              );
              

            $pdf= new boletinpdf();
            //die();
            $doc=$pdf->boletin($obj, $this->out);
           
            if($this->out=='I') return null;

            require_once($CFG->dirroot.'/message/output/email/message_output_email.php');
      
            $message = new message_output_email();
            $message->courseid = SITEID; 
            $message->userfrom = \core_user::get_noreply_user();
            $message->userto = $userto;
            $message->notification = '0';
            $message->conversationtype = null;
            $message->subject = 'Boletin alumno '.$student_fln.', '.$student_ced.'. '.$cat_n;
            $message->fullmessageformat = FORMAT_MARKDOWN;
            $message->fullmessagehtml = '
            
                <h2>Bienvenido al sistema de envio de notificaciones en linea COFRAMA.</h2> 
                <p>El siguiente correo es para entregarle el boletin del alumno : 
                 '.$student_fln.', '.$student_ced.' de '.$cat_n.'.</p>
                <br>
                <h3> Coframa, Educacion y excelencia!</h3>. ';
            $message->fullmessage=$message->fullmessagehtml;
              
            $file = new stdClass;
            $file->contextid = $usertocontext->id;
            $file->component = 'user';
            $file->filearea  = 'private';
            $file->itemid    = 0;
            $file->filepath  = '/';
            $file->filename  = $student_ced.$period->meta_value.'BOL'.$lp.$force.'.pdf';
            $file->source    = 'coframa';
      
            $message->attachname =  $file->filename;
            
            $fs = get_file_storage();
            
    
            $fileExist = $fs->get_file($file->contextid, $file->component, $file->filearea, $file->itemid, $file->filepath, $file->filename);
            if ($fileExist) {
              if(!$this->force){

                return array($student_fln,$student_ced,$cat_n,$student_sec,"NO ENVIADO, BOLETIN ENVIADO PREVIAMENTE.");

              }
              else{
                $file->delete();
              }
            }
           
            $file = $fs->create_file_from_string($file, $doc);///*
            $message->attachment = $file;
    
            if($message->send_message($message)){ 
              
              return array($student_fln,$student_ced,$cat_n,$student_sec,"ENVIADO");
            }
            else{ //
              $file->delete();
              return array($student_fln,$student_ced,$cat_n,$student_sec,"NO ENVIADO, INTENTE DENUEVO LUEGO.");
            }
        
      }

      public function set_qualitative($courseid){
        global $DB;
        $contextid = context_course::instance($courseid);
        $inf_sec=$DB->record_exists_sql("SELECT id FROM bch_tag_instance 
                                         where contextid=".$contextid->id." 
                                         and tagid=(SELECT id FROM bch_tag where name='cualitativa')",null);
        if($inf_sec){
           return true;
        }
        else{
         return false;
        }
      }

      public function n($qual,$note){
        
        if (!is_numeric($note))return $note;
        
        if($qual){
          $n='A';
          if($note<19) $n='B';
          if($note<16) $n='C';
          if($note<13) $n='D';
          if($note<10) $n='E';
          if($note==0) $n=' ';
         
          return $n;

        }
        else return $note;
      }


}
      