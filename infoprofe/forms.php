<?php
require_once("$CFG->libdir/formslib.php");
 
class momento_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('header', 'Informe','Informe de curso');
        
        /***** */
        $mform->addElement( $this->estadoSelect() );
        $mform->addRule( 'state', 'Debe seleccionar un Estado.', 'required' );

        /***** */
        $mform->addElement('editor', 'description', 'Relate el desempeño del alumno en este cuadro');
        $mform->setType('description', PARAM_RAW);
        $mform->addElement('hidden', 'reference', '');  $mform->setType('reference', PARAM_RAW); 
        
        $mform->addElement('hidden', 'id', $this->_customdata['id']); $mform->setType('id', PARAM_RAW);
        $mform->addElement('hidden', 'course', $this->_customdata['course']); $mform->setType('course', PARAM_RAW);
        $mform->addElement('hidden', 'm', $this->_customdata['m']); $mform->setType('m', PARAM_RAW);

        $this->add_action_buttons(); 
     
    }

    function estadoSelect() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $attributes= array();//

        $context = context_system::instance();
        
        if(!has_capability('mod/infoprofe:changeinformstatus', $context)){
            $attributes= array('disabled' => 'true');
        }
        
        $select = $mform->createElement( 'select','state', 'Indique el estado del informe:',array(),$attributes);
        $select->addOption( 'Seleccione el estado', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
    
        $rec= $DB->get_record_sql('SELECT distinct(COLUMN_TYPE) FROM information_schema.`COLUMNS` WHERE TABLE_NAME = \'bch_institution_inform\' AND COLUMN_NAME = \'state\' ',null,0,0);
        $out= array("enum(","'",")","ENVIADO");
        $out= str_replace($out,"",$rec->column_type);
        $rec=preg_split("/[,]+/",$out);
       

       foreach ( $rec as &$record ){
           
            $select->addOption( $record, $record);
             
        }
         $select->setSelected('-1');
        
        return $select;
    }
    
}

class momento_form_primaria extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('header', 'Informe','Informe de curso');
        
        /***** */
        $mform->addElement( $this->estadoSelect() );
        $mform->addRule( 'state', 'Debe seleccionar un Estado.', 'required' );
        $mform->addElement('text', 'project', 'Nombre Proyecto', 'required'); $mform->setType('project', PARAM_RAW); 
        /***** */
        $mform->addElement('editor', 'description', 'Relate el desempeño del alumno en este cuadro');
        $mform->setType('description', PARAM_RAW);
        $mform->addElement('hidden', 'reference', '');  $mform->setType('reference', PARAM_RAW); 
        
        $mform->addElement('hidden', 'id', $this->_customdata['id']); $mform->setType('id', PARAM_RAW);
        $mform->addElement('hidden', 'course', $this->_customdata['course']); $mform->setType('course', PARAM_RAW);
        $mform->addElement('hidden', 'm', $this->_customdata['m']); $mform->setType('m', PARAM_RAW);

        $this->add_action_buttons(); 
     
    }

    function estadoSelect() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $attributes= array();//

        $context = context_system::instance();
        
      
        
        $select = $mform->createElement( 'select','state', 'Indique el estado del informe:',array(),$attributes);
        $select->addOption( 'Seleccione el estado', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
    
        $rec= $DB->get_record_sql('SELECT distinct(COLUMN_TYPE) FROM information_schema.`COLUMNS` WHERE TABLE_NAME = \'bch_institution_inform\' AND COLUMN_NAME = \'state\' ',null,0,0);
        $out= array("enum(","'",")","ENVIADO");
        $out= str_replace($out,"",$rec->column_type);
        $rec=preg_split("/[,]+/",$out);
       
       foreach ( $rec as &$record ){
           
            $select->addOption( $record, $record);
             
        }
         $select->setSelected('-1');

         if(!has_capability('mod/infoprofe:changeinformstatus', $context)){
            $attributes= array('disabled' => 'true');
            $select->setSelected('PENDIENTE');
        }
        
        return $select;
    }
    
}