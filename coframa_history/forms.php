<?php
require_once("$CFG->libdir/formslib.php");
 
class history_main_form extends moodleform {
    
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;

        $attributes=array();
    
        $mform = $this->_form; // Don't forget the underscore! 

        $mform->addElement( $this->anoSelect() );
        $mform->addElement( $this->OptionSelect() );
        $mform->addElement( $this->PeriodSelect() );

        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'lapso', '','1er Momento', '1', array());
        $radioarray[] = $mform->createElement('radio', 'lapso', '','2do Momento', '2', array());
        $radioarray[] = $mform->createElement('radio', 'lapso', '','3er Momento', '3', array());
        $radioarray[] = $mform->createElement('radio', 'lapso', '','Revision', '4', array());
        $radioarray[] = $mform->createElement('radio', 'lapso', '','Final', 'F', array());
        $mform->setDefault('lapso', '1');
        $mform->addGroup($radioarray, 'lapso', '', array(' '), false);

        $this->add_action_buttons(); 
     
    }

    function OptionSelect() {

        global $CFG;
        global $DB;

        $attributes=array('class'=>'col');

        $mform = $this->_form; // Don't forget the underscore! 

        $select = $mform->createElement( 'select','seccion', 'Indique la seccion:',array(),$attributes);
        $select->addOption( 'Seleccione una opcion', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( 'Seccion A', 'A', array() );
        $select->addOption( 'Seccion B', 'B', array() );
        $select->setSelected('-1');
        return $select;

    }

    function anoSelect() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
        
        $attributes=array('class'=>'col');

        $select = $mform->createElement( 'select','nivel', 'Indique el año',null,$attributes);
        $select->addOption( 'Seleccione el Año', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
        
       /* $select->addOption( '1er Nivel', '1N',array());
        $select->addOption( '2do Nivel', '2N',array());
        $select->addOption( '3er Nivel', '3N',array());

        $select->addOption( '1er Grado', '1G',array());
        $select->addOption( '2do Grado', '2G',array());
        $select->addOption( '3er Grado', '3G',array());
        $select->addOption( '4to Grado', '4G',array());
        $select->addOption( '5to Grado', '5G',array());
        $select->addOption( '6to Grado', '6G',array());*/

        $select->addOption( '1er Año', '1A',array());
        $select->addOption( '2do Año', '2A',array());
        $select->addOption( '3er Año', '3A',array());
        $select->addOption( '4to Año', '4A',array());
        $select->addOption( '5to Año', '5A',array());


        $select->setSelected('-1');
        
        return $select;
    }

    function periodSelect() {
        global $CFG;
        global $DB;
    
        $mform = $this->_form; // Don't forget the underscore! 
        
        $attributes=array('class'=>'col');

        $select = $mform->createElement( 'select','periodo', 'Indique el Periodo',null,$attributes);
        $select->addOption( 'Seleccione el Periodo', '-1', array( 'disabled' => 'disabled' ) );
        $select->addOption( '-', '-2', array( 'disabled' => 'disabled' ) );
       
        $rec= $DB->get_records_sql("SELECT id, meta_value FROM `bch_institution_meta` 
        where meta_key='periodo' order by meta_value desc", 
        null, 0, 0);
       
        foreach ( $rec as &$record ){         
            $select->addOption( $record->meta_value, $record->id);
        }

        $select->setSelected('-1');
        
        return $select;
    }

}