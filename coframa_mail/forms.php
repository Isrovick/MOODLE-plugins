<?php
require_once("$CFG->libdir/formslib.php");
 
class admin_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
       
        $mform->addElement( $this->anoSelect() );
        //$mform->addElement( $this->catSelect() );
        $mform->addElement( $this->OptionSelect());
        $mform->addElement('static', 'description', 'Informacion Importante:','Seleccionar "Representante" indicara que esta notificacion sera enviada al representante de dicho(s) alumno(s).');
        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'tipo', '','Representantes', 'R', array());
        $radioarray[] = $mform->createElement('radio', 'tipo', '','Alumnos', 'A', array());
        $radioarray[] = $mform->createElement('radio', 'tipo', '','Profesores', 'P', array());
        $mform->setDefault('tipo', 'R');
        $mform->addGroup($radioarray, 'tipo', '', array(' '), false);
        
        $mform->addElement('text', 'gsearch', 'Usuarios Individuales', array()); $mform->setType('gsearch', PARAM_RAW); 
        $mform->addElement('html', ' <fieldset name="list"></fieldset> ');
        $mform->addElement('html', ' <fieldset name="users">
                                     <div class="col"></div>
                                    <div class="col-6">  
                                    <h6>Los usuarios Listados debajo seran notificados:</h6>
                                    </div>
                                    </fieldset> ');
        //$mform->addElement('html', '<hr>');
        $mform->addElement('text', 'subject', 'Asunto', array()); $mform->setType('subject', PARAM_RAW);

        $mform->addElement('editor', 'contenido', 'Contenido del correo');
        $mform->setType('contenido', PARAM_RAW);

        $this->add_action_buttons(); 
     
    }

    function anoSelect() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
        
        $attributes=array('class'=>'col');

        $select = $mform->createElement( 'select','ano_id', 'Indique el año',null,$attributes);
        $select->addOption( 'Seleccione el Año', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
        $select->addOption( 'Todos los años', 'T',array());
        $select->addOption( 'Solo Inicial', 'I',array());
        $select->addOption( 'Solo Primaria', 'P',array());
        $select->addOption( 'Solo Media General', 'S',array());
    
        $rec= $DB->get_records_sql('SELECT id, name FROM `bch_course_categories` where parent in (SELECT id FROM `bch_course_categories` WHERE idnumber = 1 or idnumber=2 or idnumber=3) ', null, 0, 0);
       
        foreach ( $rec as &$record ){
           
            $select->addOption( $record->name, $record->id );
             
         }
         $select->setSelected('-1');
        
        return $select;
    }

    function catSelect() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
        
        $attributes=array('class'=>'col');

        $select = $mform->createElement( 'select','materias_id', 'Indique la Materia:',null,$attributes);
        $select->addOption( 'Seleccione la materia', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
        $select->addOption( 'Todas las materias', 'T',array());
        
        return $select;
    }

    function OptionSelect() {

        global $CFG;
        global $DB;

        $attributes=array('class'=>'col');

        $mform = $this->_form; // Don't forget the underscore! 

        $select = $mform->createElement( 'select','seccion', 'Indique la seccion:',array(),$attributes);
        $select->addOption( 'Seleccione una opcion', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( 'Todos las Secciones', 'T', array() );
        $select->addOption( 'Seccion A', 'A', array() );
        $select->addOption( 'Seccion B', 'B', array() );
        return $select;

    }

}

class manager_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 


        $this->add_action_buttons(); 
     
    }
}

class teacher_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;

        $attributes=array();
    
        $mform = $this->_form; // Don't forget the underscore! 

        $mform->addElement('checkbox', 'PEDE', 'Notificar a PEDE');
        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'tipo', '','Representantes', 'R', array());
        $radioarray[] = $mform->createElement('radio', 'tipo', '','Alumnos', 'A', array());
        $mform->setDefault('tipo', 'R');
        $mform->addGroup($radioarray, 'tipo', '', array(' '), false);
        $mform->addElement('static', 'description', 'Informacion Importante:','Seleccionar "Representante" indicara que esta notificacion sera enviada al representante de dicho(s) alumno(s).');
        //$mform->addElement( $this->UserSelect($this->_customdata['id']) );
        $mform->addElement('hidden','cid',$this->_customdata['id']); $mform->setType('cid', PARAM_RAW); 
        $mform->addElement( $this->OptionSelect() );
        $mform->addElement('text', 'gsearch', 'Usuarios Individuales', array()); $mform->setType('gsearch', PARAM_RAW); 
        $mform->addElement('html', ' <fieldset name="list"></fieldset> ');
        $mform->addElement('html', ' <fieldset name="users">
                                     <div class="col"></div>
                                    <div class="col-6">  
                                    <h6>Los usuarios Listados debajo seran notificados:</h6>
                                    </div>
                                    </fieldset> ');
        
       
        $mform->addElement('text', 'subject', 'Asunto', array()); $mform->setType('subject', PARAM_RAW); 
        $mform->addElement('editor', 'contenido', 'Contenido del correo'); $mform->setType('contenido', PARAM_RAW);

       

       

        $this->add_action_buttons(); 
     
    }

    function OptionSelect() {

        global $CFG;
        global $DB;

        $attributes=array();

        $mform = $this->_form; // Don't forget the underscore! 

        $select = $mform->createElement( 'select','useroption', 'Indique la seccion:',array(),$attributes);
        $select->addOption( 'Seleccione una opcion', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( 'Todas las Secciones', 'T', array() );
        $select->addOption( 'Seccion A', 'A', array() );
        $select->addOption( 'Seccion B', 'B', array() );
        $select->addOption( 'Usuario Individual', '+', array() );
        return $select;

    }

    function UserSelect($courseid) {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $attributes= array();//array('disabled'=>'disabled');//

        
        $select = $mform->createElement( 'select','userids', 'Alumnos inscritos:',array(),$attributes);
        $select->addOption( 'Seleccione los Alumnos a enviar', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
       
        $users=$DB->get_records_sql("SELECT id, firstname, lastname from bch_user where id in 
                                        (SELECT distinct(userid) as id FROM bch_role_assignments where roleid=5 and contextid in
                                            (SELECT distinct(id) FROM bch_context where contextlevel=50 and instanceid =".$courseid."))
                                            order by lastname"
                                    ,null, 0, 0);

       foreach ($users as &$student ){
            $student_ced=profile_user_record($student->id)->cedula;
            $student_sec=profile_user_record($student->id)->seccion;
            $student_fln= "Seccion ".$student_sec." - ".$student->lastname.", ".$student->firstname." ".$student_ced;
            $select->addOption( $student_fln , $student->id);  
        }

         $select->setSelected('-1');
         $select->setMultiple(true);
        
         return $select;
    }
    

}