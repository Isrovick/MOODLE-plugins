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
require_once($CFG->libdir.'/gdlib.php');
require_once $CFG->libdir.'/adminlib.php';

require_login(0, false);

defined('MOODLE_INTERNAL') || die();
$context = context_system::instance();
/*if(!has_capability('mod/coframa_history:generatehistory', $context)){
  http_response_code(404);
  die(); 
}*/

$context = context_system::instance();
$PAGE->set_url("$CFG->wwwroot/local/coframa_user/user_individual.php");
$PAGE->set_context($context);

class user_individual{

    protected int $userid;
    public object $pur;
    public object $student;
    protected array $ncol;
    public string $student_fln;
    public string $student_btd;
    public int $student_idn;
    /**********************************************************/
    public function __construct($userid){
        global $DB;

        $this->userid=$userid;
        $this->student=$DB->get_record("user", array ("id"=>$this->userid), 'firstname,lastname,idnumber');
        $this->pur=profile_user_record($this->userid);
        $this->ncol=array(4,4,4,1);
        $this->student_fln= $this->student->firstname.", ".$this->student->lastname;
        $this->student_idn=$this->student->idnumber;
        $this->student_btd=$this->pur->bday.'-'.$this->pur->bmonth.'-'.$this->pur->byear;
    }
    /**********************************************************/
    public function generate_full(){
        
        $cou=$this->get_user_courses();
        $boletin_grades=array();

        foreach ( $cou as &$course ){
            $boletin_grades[$course->id]=$this->course_item($course->id);
           
        }

        $boletin_grades['promedio_info']=$this->get_promedio_final();
        return $boletin_grades;
    }
    /**********************************************************/
    public function generate_full_lite(){
        
        $cou=$this->get_user_courses();
        $boletin_grades=array();

        foreach ( $cou as &$course ){
            $boletin_grades[$course->id]=course_item_lite($course->id);
           
        }

        $boletin_grades['promedio_info']=$this->get_promedio_final();
        return $boletin_grades;
    }
    /**********************************************************/    
    public function generate_final_grades(){
        
        $cou=$this->get_user_courses();
        $finalgrades=array();

        foreach ( $cou as &$course ){
            $ci=$this->course_item_lite($course->id);
            $finalgrades[$course->id]=$ci['boletin_items']['final'];
        }

        return $finalgrades;
    }
    /**********************************************************/      
    public function course_item($courseid){
        global $DB;

        $course=$this->get_course_info($courseid);
    
        $ci=    array(      'course'=>$course->id,
                            'profn'=>$this->get_profn($course->id),
                            'asisn'=>$this->get_asisn($course->id),
                            'fullname'=>$course->fullname,
                            'category'=>$this->get_user_cat()->name,
                            'boletin_items'=>$this->course_boletin_items($course->id),
                            'qual'=>$this->set_qualitative($course->id),
                            );   
        return $ci; 
    }
    /**********************************************************/          
    public function course_item_lite($courseid){
        global $DB;

        $course=$this->get_course_info($courseid);
    
        $ci=    array(      'course'=>$course->id,
                            'boletin_items'=>$this->course_boletin_items($course->id),
                            );   
        return $ci; 
    }
    /**********************************************************/
    public function course_boletin_items($courseid){


        $bi=array(
          '1'=>$this->lapso($courseid,1),
          '2'=>$this->lapso($courseid,2),
          '3'=>$this->lapso($courseid,3),
          'revision'=>$this->lapso($courseid,4),
        );
          
        $bi['total']=number_format(($bi['1']['finallapsocurso']+$bi['2']['finallapsocurso']+$bi['3']['finallapsocurso'])/3,2);
        $bi['final']=$this->nota_definitiva($bi['total'] , $bi['revision']['finallapsocurso']);
    
        return $bi;
    }
    /**********************************************************/
    public function lapso($courseid,$lp){
        
        global $CFG;
        global $DB;
    
        $evaluaciones=array();
    
        $items=$DB->get_records_sql("Select * from bch_institution_grade_items where courseid=".$courseid." and lp=".$lp." 
                                        and ( id_lp_item in(Select id from bch_grade_items where itemname IS NOT NULL and courseid=".$courseid.")
                                        or id_cd_item in(Select id from bch_grade_items where itemname IS NOT NULL and courseid=".$courseid."))
                                    order by lp asc, ev asc", null, 0,0);
        $cont=0;
        $acum=0;

        foreach($items as $obj){

            $lp_g=$this->calculo_nota($obj->id_lp_item);
            $cd_g=$this->calculo_nota($obj->id_cd_item);
            $def=$this->nota_definitiva($lp_g,$cd_g);

            $evaluaciones[]=array(
                                            'lp_id'=>$obj->id_lp_item,
                                            'cd_id'=>$obj->id_cd_item,
                                            'lp_g'=>$lp_g,
                                            'cd_g'=>$cd_g,
                                            'def'=>$def,
                                         );
            $acum+=$def;
            $cont++;
        }

        if($cont==0) $cont=1;

        $evaluaciones['finallapsocurso']=number_format(($acum/$cont),2);

        return $evaluaciones;

    }
    /**********************************************************/
    public function calculo_nota($itemid,$percentage=false){
        $grade=$this->get_grade_item($itemid);
        
        if($percentage) return ($grade*$percentage/100);
        else return $grade;
    }
    /**********************************************************/
    public function nota_definitiva($lp_g,$cd_g){
        if($lp_g){
            if($lp_g<10){
                if($cd_g){
                    if($cd_g>$lp_g){
                        return $cd_g;
                    }
                    else return $lp_g;
                }
                else return $lp_g;
            }
            else return $lp_g;
        }
        else return '0.0';
    }
    /**********************************************************/   
    public function get_grade_item($itemid){
        global $DB; 
        $userid=$this->userid;

        if(is_null($itemid)) return null;
      
        $item=$DB->get_record("grade_grades", array ("itemid"=>$itemid,"userid"=>$userid), 'finalgrade');
       
        return ($item ? number_format ( $item->finalgrade,1) : null);
    }
    /**********************************************************/         
    public function get_promedio_lapso($lp){
        global $CFG;
        global $DB;
        global $data;
      
        $cou=$this->get_user_courses();
        $lsum=0;
        $cont=0;
        $aprobadas=0;
        $aplazadas=0;
        $lapso_items=array();

        foreach ( $cou as &$course ){
          $clp=$this->lapso($course->id,$lp); 
          $lapso_items[$course->id]=$clp;
          $lsum+=$clp['finallapsocurso'];
          $cont++; 
          if($clp['finallapsocurso']>=10){ $aprobadas++;}
          else { $aplazadas++;}              
        }

            if($cont==0) $cont=1;
            $lt=number_format($lsum/$cont,3);
            $lp_info=array(
                    'lapso_items'=>$lapso_items,
                    'promedio'=>($lt),
                    'posicion'=>0,
                    'aprobadas'=>$aprobadas,
                    'aplazadas'=>$aplazadas,
            );

        return $lp_info; 
    
    }
    /**********************************************************/      
    public function get_promedio_final(){
        global $CFG;
        global $DB;
        global $data;
      
        $gpl1=$this->get_promedio_lapso(1);
        $gpl2=$this->get_promedio_lapso(2);
        $gpl3=$this->get_promedio_lapso(3);

       
        $aprobadas=$gpl1['aprobadas']+$gpl2['aprobadas']+$gpl3['aprobadas'];
        $aplazadas=$gpl1['aplazadas']+$gpl2['aplazadas']+$gpl3['aplazadas'];
        
        $fsum= $gpl1['promedio']+ $gpl2['promedio'] + $gpl2['promedio'];
        $ft=number_format($fsum/3,2);
        
        $final_info=array(
            'promedio'=>($ft),
            'aprobadas'=>$aprobadas,
            'aplazadas'=>$aplazadas,
        );

        return $final_info; 
    
    }
    /**********************************************************/
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
    /**********************************************************/
    public function n($qual,$note){
        if(!is_numeric($note))  return $note;
        if($qual){
            $n='A';
            if($note<19) $n='B';
            if($note<16) $n='C';
            if($note<13) $n='D';
            if($note<10) $n='E';
            
            return $n;

        }else 
        return $note;
    }
    /**********************************************************/
    public function get_user_courses(){
        global $CFG;
        global $DB;
        return  $DB->get_records_sql('SELECT id FROM bch_course where id in 
                                        (SELECT distinct(courseid) as id  FROM bch_enrol a where roleid=5 and a.id in 
                                        (SELECT enrolid as id FROM bch_user_enrolments where userid='.$this->userid.')) order by category desc',null, 0, 0);

    }
    /**********************************************************/
    public function get_user_cat(){
        global $CFG;
        global $DB;
        return $DB->get_record_sql('SELECT id , name FROM bch_course_categories  where id=
                                    (select category from  
                                        (SELECT count(category) count, category FROM bch_course where id in 
                                            (SELECT distinct(courseid) as id  FROM bch_enrol a where roleid=5 and a.id in 
                                             (SELECT enrolid as id FROM bch_user_enrolments where userid='.$this->userid.')) 
                                        group by category order by count desc) 
                                    s limit 1)',null,0,0);

    }
    public function get_course_info($courseid){
        global $CFG;
        global $DB;

        return $DB->get_record_sql('select co.id, co.summary as fullname, cc.name as catname FROM bch_course co
                                    join bch_course_categories cc on co.category=cc.id
                                    where co.id='.$courseid,null,0,0);

    }
    /**********************************************************/    
    public function get_profn($courseid){
        global $CFG;
        global $DB;
        $profn="-";                        
        $profid=$DB->get_record_sql("SELECT distinct(userid) as id FROM bch_role_assignments where roleid=3 and contextid in
                                      (SELECT distinct(id) FROM bch_context where contextlevel=50 and instanceid in
                                      (SELECT id FROM `bch_course` where id=".$courseid."))"
                                    ,null, 0, 0);
        if($profid){
          $prof=$DB->get_record("user", array ("id"=>$profid->id), 'firstname,lastname');
          $profn= $prof->firstname.', '.$prof->lastname; 
        }
        return $profn;  
    }
    /**********************************************************/    
    public function get_asisn($courseid){
        global $CFG;
        global $DB;

        $asisn="-";  
        $asisid=$DB->get_record_sql("SELECT distinct(userid) as id FROM bch_role_assignments where roleid=4 and contextid in
                                    (SELECT distinct(id) FROM bch_context where contextlevel=50 and instanceid in
                                    (SELECT id FROM `bch_course` where id=".$courseid."))"
                                    ,null, 0, 0);
        if($asisid){
        $asis=$DB->get_record("user", array ("id"=>$asisid->id), 'firstname,lastname');
        $asisn=$asisn=$asis->firstname.', '.$asis->lastname; ; 
        } 
        
        return $asisn;
    }
    /**********************************************************/    
    public function cat(){
        switch($this->pur->nivel){
            case '1A': return '1er Año'; break;
            case '2A': return '2do Año'; break;
            case '3A': return '3er Año'; break;
            case '4A': return '4to Año'; break;
            case '5A': return '5to Año'; break;
            case ' ': break;
            
            default: break;
        }
    }
}