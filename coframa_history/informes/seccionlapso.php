<?php
//require('../../../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();
require_login(0, false);

require_once($CFG->libdir . '/pdflib.php');

class seccionlapsopdf extends TCPDF {

           
    
    public function seccion_lapso($title,$head_title,$header,$body){
        $this->SetPrintHeader(false);
        $this->SetPrintFooter(false);
        $this->setHeaderMargin( 5 );
        $this->setFooterMargin( 5 );
        $this->SetMargins( 5,5, 5, false );
        $this->SetFont('helvetica');
        $this->SetFontSize(6);
        $this->AddPage();

        $html = '
        <style>
            .head1 {
                font-size: 75%; 
            }
            .headb {
                font-size: 55%; 
            }
            ul{
                list-style-type: none; 
            }
            .s1{
                text-align: center;
            }
            .footerb{
                font-size: 75%;  
            }
            .footerb > td{
                text-align:center;  
            }
            th{
                font-weitght:bold;
                text-align:center;
            }
            .boletin > td{
                font-size: 70%; 
            } 
            .cdc{
                font-size: 85%; 
            } 
            desc{
                padding:100px;
                text-align: center;
                text-justify: inner-word;
                margin: 10%;
                height:630px;
            }
        </style>
        <table>
            <tr>
                <th align="center" colspan="1" ><bold><h2>'.$head_title.'</h4></bold></th>
            </tr> 
            <tr>
                <th align="center" colspan="1" ></th>
            </tr> 
            <tr>
                <th align="center" colspan="1" ></th>
            </tr> 
        </table>

        <table border="1" >
            <tr>
                <th colspan="1"><bold>Nro.</bold></th> 
                <th colspan="6"><bold>ESTUDIANTE</bold></th>'; 
        $html .=$header;
        $html .='
                <th colspan="1"><bold>Pro.</bold></th> 
                <th colspan="1"><bold>Pos.</bold></th>
                <th colspan="1"><bold>Apr.</bold></th>
                <th colspan="1"><bold>Apl.</bold></th>
            </tr>
        ';       
        $html .=$body;
        
        $html .='       
        </table>';     

        
        $this->writeHTML($html, true, false, true, false, '');
      
        return parent::Output($title.date('d/m/Y').'.pdf', 'I');
    }

   

}


