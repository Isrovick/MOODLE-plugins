<?php
require_once("$CFG->libdir/formslib.php");
 
class simplehtml_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('header', 'Inscripcion','Matriculaciones');
        $mform->addElement( $this->anoSelect() );
        $mform->addRule( 'materias_id', 'Debe seleccionar una opción.', 'required' );
        $mform->addElement('date_selector', 'startime', 'Fecha de Inicio');
        $mform->addElement( $this->promoSelect() );
        $mform->addRule( 'promo_id', 'Debe seleccionar una opción.', 'required' );
        
        
        $mform->addElement('text', 'gsearch', 'Usuarios Individuales', array()); $mform->setType('gsearch', PARAM_RAW); 
        $mform->addElement('html', ' <fieldset name="list"></fieldset> ');
        $mform->addElement('html', ' <fieldset name="users">
                                     <div class="col"></div>
                                    <div class="col-6">  
                                    <h6>Los usuarios Listados debajo seran matriculados:</h6>
                                    </div>
                                    </fieldset> ');
        //$mform->addElement('html', '<hr>');
     

        
        
        $this->add_action_buttons(); 
     
    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
    function promoSelect() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $select = $mform->createElement( 'select','promo_id', 'Promociones:',array());
        $select->addOption( 'Seleccione una promocion', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
        //$select->addOption( 'usuario Individual', '+');
    
        $rec= $DB->get_records_sql('SELECT id, name FROM bch_cohort', null, 0, 0);
       
        foreach ( $rec as &$record ){
           
            $select->addOption( $record->name, $record->id );
             
         }
         $select->setSelected('-1');
        
        return $select;
    }
    
    function anoSelect() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $select = $mform->createElement( 'select','materias_id', 'Matricular en:',array());
        $select->addOption( 'Seleccione el Año', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '0', array( 'disabled' => 'disabled' ) );
    
        $rec= $DB->get_records_sql('SELECT id, name FROM `bch_course_categories` where parent in (SELECT id FROM `bch_course_categories` WHERE idnumber=1 or idnumber=2) ', null, 0, 0);
       
        foreach ( $rec as &$record ){
           
            $select->addOption( $record->name, $record->id );
             
         }
         $select->setSelected('-1');
         

        return $select;
    }
}    

class second_form extends moodleform {
    
        //Add elements to form
     
  
        public function definition() {
            global $CFG;
            global $DB;
        
            $mform = $this->_form; // Don't forget the underscore! 
     
            $mform->addElement('header', 'visualizacion','Visualizacion de Matriculacion');
            $mform->addElement('hidden', 'promo_id', $this->_customdata['promo_id']);
            $mform->setType('promo_id', PARAM_RAW);
            $mform->addElement('hidden', 'materias_id', $this->_customdata['materias_id']);
            $mform->setType('materias_id', PARAM_RAW);
            $mform->addElement('hidden', 'startime', $this->_customdata['startime']);
            $mform->setType('startime', PARAM_RAW);
           
            
            $this->add_action_buttons(); 

        }
        //Custom validation should be added here
        function validation($data, $files) {
            return array();
        }
}

class remocion_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('header', 'Remocion','Remocion de curso');
        $mform->addElement( $this->anoSelect_RF() );
        $mform->addRule( 'materias_id', 'Debe seleccionar un año.', 'required' );

        $this->add_action_buttons(); 
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

    function anoSelect_RF() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $select = $mform->createElement( 'select','materias_id', 'Materias a reestablecer:',array());
        $select->addOption( 'Seleccione el Año', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
        $select->addOption( 'TODAS', '0');
    
        $rec= $DB->get_records_sql('SELECT id, name FROM `bch_course_categories` where parent in (SELECT id FROM `bch_course_categories` WHERE idnumber=1 or idnumber=2) ', null, 0, 0);
       
        foreach ( $rec as &$record ){
           
            $select->addOption( $record->name, $record->id );
             
         }
         $select->setSelected('-1');
        
        return $select;
    }
    
}

class revision_category_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('header', 'Revision','Revision de Informes');
        $mform->addElement( $this->anoSelect_rcf() );
        $mform->addRule( 'materias_id', 'Debe seleccionar un año.', 'required' );

        $this->add_action_buttons(); 
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

    function anoSelect_rcf() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $select = $mform->createElement( 'select','materias_id', 'Año a Revisar:',array());
        $select->addOption( 'Seleccione el Año', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
    
        $rec= $DB->get_records_sql('SELECT id, name FROM `bch_course_categories` where parent in (SELECT id FROM `bch_course_categories` WHERE idnumber=1 or idnumber=2 or idnumber=3) ', null, 0, 0);
       
        foreach ( $rec as &$record ){
           
            $select->addOption( $record->name, $record->id );
             
         }
         $select->setSelected('-1');
        
        return $select;
    }
    
}

class revision_course_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('header', 'Revision','Revision de notas');
        $mform->addElement( $this->courseSelect($this->_customdata['m']) );
        $mform->addRule( 'course_id', 'Debe seleccionar un año.', 'required' );

        $this->add_action_buttons(); 
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

    function courseSelect($m) {
        global $CFG;
        global $DB;
       
        $mform = $this->_form; // Don't forget the underscore! 
      
        $select = $mform->createElement( 'select','course_id', 'Materia a Revisar:',array());
        $select->addOption( 'Seleccione la materia', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
        
        if($m!=null && $m!=""){
                   
            $rec= $DB->get_record_sql('SELECT name FROM `bch_course_categories` where id='.$m, null, 0, 0);
            $cat=$rec->name;
            $rec= $DB->get_records_sql('SELECT id, fullname FROM `bch_course` where category='.$m, null, 0, 0);
        
            foreach ( $rec as &$record ){
            
                $select->addOption( $record->fullname.', '.$cat, $record->id );
                
            }
         
        }
        
         $select->setSelected('-1');
        
        return $select;
    }
    
}

class send_category_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('header', 'Enviado','Enviado de Informes Primaria e Inicial');
        $mform->addElement('static', 'description', '' ,'Seleccione a que momento corresponde este informe: ');
        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'lapso', '','1er Momento', 1, array());
        $radioarray[] = $mform->createElement('radio', 'lapso', '','2do Momento', 2, array());
        $radioarray[] = $mform->createElement('radio', 'lapso', '','3er Momento', 3, array());

        $mform->addGroup($radioarray, 'lapsos', '', array(' '), false);

        $mform->addElement('checkbox', 'force', 'Forzar envio de informes');
        
        $mform->addElement( $this->anoSelect_scf() );
        $mform->addRule( 'materias_id', 'Debe seleccionar un año.', 'required' );
        $mform->addElement('text', 'gsearch', 'Usuarios Individuales', array()); $mform->setType('gsearch', PARAM_RAW); 
        
        $mform->addElement('html', ' <fieldset name="list"></fieldset> ');
        $mform->addElement('html', ' <fieldset name="users">
                                     <div class="col"></div>
                                    <div class="col-6">  
                                    <h6>El boletin sera enviado individualmente a los siguientes usuarios:</h6>
                                    </div>
                                    </fieldset> ');




         $buttonarray=array();
            $buttonarray[] = $mform->createElement('submit', 'see', 'Visualizar');
            $buttonarray[] = $mform->createElement('submit', 'send', 'Enviar por correo');
            $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

    function anoSelect_scf() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $select = $mform->createElement( 'select','materias_id', 'Año a Notificar:',array());
        $select->addOption( 'Seleccione el Año', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
        $select->addOption( 'Usuarios Individuales', '+', array() );
        $select->addOption( 'Toda Primaria e Inicial', 'T', array() );


        $select->addOption( '1er Nivel', '1N', array() );
        $select->addOption( '2do Nivel', '2N', array() );
        $select->addOption( '3er Nivel', '3N', array() );

        $select->addOption( '1er Grado', '1G', array() );
        $select->addOption( '2do Grado', '2G', array() );
        $select->addOption( '3er Grado', '3G', array() );
        $select->addOption( '4to Grado', '4G', array() );
        $select->addOption( '5to Grado', '5G', array() );
        $select->addOption( '6to Grado', '6G', array() );

        /*$rec= $DB->get_records_sql('SELECT id, name FROM `bch_course_categories` where parent in (SELECT id FROM `bch_course_categories` WHERE idnumber=1 or idnumber=3) ', null, 0, 0);
       
        foreach ( $rec as &$record ){
           
            $select->addOption( $record->name, $record->id );
             
         }*/
         $select->setSelected('-1');


         
        return $select;
    }
    
}

class send_course_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('header', 'Enviado','Enviado de informes Primaria e Inicial');
        $mform->addElement( $this->courseSelect($this->_customdata['m']) );
        $mform->addRule( 'course_id', 'Debe seleccionar un año.', 'required' );

        $this->add_action_buttons(); 
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

    function courseSelect($m) {
        global $CFG;
        global $DB;
       
        $mform = $this->_form; // Don't forget the underscore! 
      
        $select = $mform->createElement( 'select','course_id', 'Materias por enviar informe:',array());
        $select->addOption( 'Seleccione la materia', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
        $select->addOption( 'TODAS LAS MATERIAS', '-'.$m.'-', array() );
        
        if($m!=null && $m!=""){
                   
            $rec= $DB->get_record_sql('SELECT name FROM `bch_course_categories` where id='.$m, null, 0, 0);
            $cat=$rec->name;
            $rec= $DB->get_records_sql('SELECT id, fullname FROM `bch_course` where category='.$m, null, 0, 0);
        
            foreach ( $rec as &$record ){
            
                $select->addOption( $record->fullname.', '.$cat, $record->id );
                
            }
         
        }
        
         $select->setSelected('-1');
        
        return $select;
    }
    
}

class send_category_form_grades extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('header', 'Enviado','Enviado de notas Media General');
        
        $mform->addElement('static', 'description', '' ,'Seleccione a que momento corresponde este boletin: ');
        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'lapso', '','1er Momento', 1, array());
        $radioarray[] = $mform->createElement('radio', 'lapso', '','2do Momento', 2, array());
        $radioarray[] = $mform->createElement('radio', 'lapso', '','3er Momento', 3, array());

        $mform->addGroup($radioarray, 'lapsos', '', array(' '), false);

        $mform->addElement('checkbox', 'force', 'Forzar envio de boletines');
       
        $mform->addElement( $this->anoSelect_scfg() );
        $mform->addElement('text', 'gsearch', 'Usuarios Individuales', array()); $mform->setType('gsearch', PARAM_RAW); 
        
        $mform->addElement('html', ' <fieldset name="list"></fieldset> ');
        $mform->addElement('html', ' <fieldset name="users">
                                     <div class="col"></div>
                                    <div class="col-6">  
                                    <h6>El boletin sera enviado individualmente a los siguientes usuarios:</h6>
                                    </div>
                                    </fieldset> ');

       

        //$mform->addRule( 'materias_id', 'Debe seleccionar un año.', 'required' );

        $buttonarray=array();
            $buttonarray[] = $mform->createElement('submit', 'see', 'Visualizar');
            $buttonarray[] = $mform->createElement('submit', 'send', 'Enviar por correo');
            $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

    function anoSelect_scfg() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $select = $mform->createElement( 'select','materias_id', 'Año a Notificar:',array());
        $select->addOption( 'Seleccione el Año', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( 'Usuarios individuales', '+', array() );
        $select->addOption( 'TODOS LOS AÑOS', 'T', array() );
        
        //$select->addOption( 'SOLO PRIMARIA', 'P', array() );
        //$select->addOption( 'SOLO Media General', 'S', array() );

        //$rec= $DB->get_records_sql('SELECT id, name FROM `bch_course_categories` where parent in (SELECT id FROM `bch_course_categories` WHERE /*name = "Primaria" or*/ idnumber=2) ', null, 0, 0);
       /*
        foreach ( $rec as &$record ){
           
            $select->addOption( $record->name, $record->id );
             
        }*/

        $select->addOption( '1er Año', '1A' );
        $select->addOption( '2do Año', '2A' );
        $select->addOption( '3er Año', '3A' );
        $select->addOption( '4to Año', '4A' );
        $select->addOption( '5to Año', '5A' );

        $select->setSelected('-1');

        
        return $select;
    }
    
}

class cague_course_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('header', 'Enviado','Generacion C.A.G.E.');
        $mform->addElement( $this->courseSelect($this->_customdata['m']) );
        $mform->addRule( 'course_id', 'Debe seleccionar un año.', 'required' );

        $this->add_action_buttons(); 
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

    function courseSelect($m) {
        global $CFG;
        global $DB;
       
        $mform = $this->_form; // Don't forget the underscore! 
      
        $select = $mform->createElement( 'select','course_id', 'Materias por a generar C.A.G.E:',array());
        $select->addOption( 'Seleccione la materia', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
        //$select->addOption( 'TODAS LAS MATERIAS', '-'.$m.'-', array() );
        
        if($m!=null && $m!=""){
                   
            $rec= $DB->get_record_sql('SELECT name FROM `bch_course_categories` where id='.$m, null, 0, 0);
            $cat=$rec->name;
            $rec= $DB->get_records_sql('SELECT id, fullname FROM `bch_course` where category='.$m, null, 0, 0);
        
            foreach ( $rec as &$record ){
            
                $select->addOption( $record->fullname.', '.$cat, $record->id );
                
            }
         
        }
        
         $select->setSelected('-1');
        
        return $select;
    }
    
}

class cague_category_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('header', 'Enviado','Generacion C.A.G.E.');
        $mform->addElement( $this->anoSelect_scfg() );

        $mform->addRule( 'materias_id', 'Debe seleccionar un año.', 'required' );

        $this->add_action_buttons(); 
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

    function anoSelect_scfg() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $select = $mform->createElement( 'select','materias_id', 'Año a generar:',array());
        $select->addOption( 'Seleccione el Año', '-1', array( 'disabled' => 'disabled' ) );
        //$select->addOption( 'TODOS LOS AÑOS', 'T', array() );

        $rec= $DB->get_records_sql('SELECT id, name FROM `bch_course_categories` where parent in (SELECT id FROM `bch_course_categories` WHERE idnumber=2) ', null, 0, 0);
       
        foreach ( $rec as &$record ){
           
            $select->addOption( $record->name, $record->id );
             
        }
        $select->setSelected('-1');

        
        return $select;
    }
    
}

class desmatricular_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
 
        $mform->addElement('header', 'Inscripcion',' Anular matriculaciones alumno');

        $mform->addElement('text', 'gsearch', 'Usuarios Individuales', array()); $mform->setType('gsearch', PARAM_RAW); 
        $mform->addElement('html', ' <fieldset name="list"></fieldset> ');
        $mform->addElement('html', ' <fieldset name="users">
                                     <div class="col"></div>
                                    <div class="col-6">  
                                    <h6>Los usuarios Listados debajo seran des-matriculados:</h6>
                                    </div>
                                    </fieldset> ');

        $this->add_action_buttons(); 
    
    }
}    
