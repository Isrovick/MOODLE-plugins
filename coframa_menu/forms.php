<?php
require_once("$CFG->libdir/formslib.php");
 
class meta_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 

        $data= $DB->get_record_sql("SELECT * FROM `bch_institution_meta` 
                                    where id= ".$this->_customdata['id'],null, 0, 0);

        $attributes=array('size'=>'40');
        $mk=strtoupper(str_replace("_", " ",$data->meta_key ));
        $dmv=$data->meta_value;
        $display='display:none';
        $display_text='';
        switch ($data->meta_key) {
            case "id_director":
                    $mk="DIRECTOR ACTUAL";    
                    $dir=$DB->get_record("user", array ("id"=>$dmv), 'firstname,lastname');
                    $dir_ced=profile_user_record($dmv)->cedula;
                    $dmv= $dir->firstname.", ".$dir->lastname.". ".$dir_ced;
                    $mform->addElement('hidden', 'userid',null);  $mform->setType('userid', PARAM_RAW); 
                    $display_text='Buscar Usuario';
                    $display='display:block';
                    $attributes['disabled']='disabled';
                    break;
            case "id_pede":
                    $mk="PEDE";    
                    $dir=$DB->get_record("user", array ("id"=>$dmv), 'firstname,lastname');
                    $dir_ced=profile_user_record($dmv)->cedula;
                    $dmv= $dir->firstname.", ".$dir->lastname.". ".$dir_ced;
                    $mform->addElement('hidden', 'userid',null);  $mform->setType('userid', PARAM_RAW); 
                    $display_text='Buscar Usuario';
                    $display='display:block';
                    $attributes['disabled']='disabled';
                    break; 
            case "periodo":  
                        $attributes['readonly']=true;
                        $attributes['value']=(date('Y')).'-'.(date('Y')+1);            
                    break;           
            default:
                
                break;
        }
        
        $mform->addElement('header', 'Informe','Actualizacion de '.$mk);
        
        $mform->addElement('text', 'meta_value', 'Nuevo Valor',$attributes);
        $mform->setType('meta_value', PARAM_RAW);
        $mform->addElement('hidden', 'meta_key', $data->meta_key);  $mform->setType('meta_key', PARAM_RAW);

        $mform->addElement('text', 'gsearch',$display_text,array('style'=>$display,'size'=>'30')); $mform->setType('gsearch', PARAM_RAW); 
        $this->add_action_buttons(); 
     
    }
}