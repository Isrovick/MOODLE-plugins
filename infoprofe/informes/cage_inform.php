<?php
//require('../../../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();
require_login(0, false);

require_once($CFG->libdir . '/pdflib.php');

class cagepdf extends TCPDF {

    public function gimf($field){
        global $DB;
        $info= $DB->get_record_sql("SELECT meta_value FROM `bch_institution_meta` 
                where meta_key='".$field."' 
                and meta_date = (SELECT MAX(meta_date) as md 
                                FROM `bch_institution_meta` 
                                WHERE meta_key='".$field."')",
        null, 0, 0);
        
        if($info){
            return $info->meta_value;
        }
        return null;
    
    }  
                   

    function cage($content){
        //$this->setPrintHeader(false);
        //$this->setPrintFooter(false);
        //$this->setHeaderMargin( 5 );
        //$this->setFooterMargin( 5 );
        $this->SetMargins( 5,20, 5, true );
        //$this->SetFont('helvetica');
        $this->SetFontSize(9);
        $this->AddPage('L','A4');
        //$html=$this->headerc();
        $html.=$content;
        //echo $html;
        //$html=utf8_encode($html);
        $this->writeHTML($html, true, false, true, false, '');

        return $this->Output('cage.pdf', 'I');
    }
    public function Header() {
        

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
                font-size: 75%; 
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
            td.desc{
                height:630px;
            }
            td.desc > div {
                padding:10%;
                text-align: justify;
                text-justify: inter-word;
                margin: 10%;
            }
            td.ced{
                font-size: 100%;
            }
            td.nota{
                font-size: 100%;
                vertical-align: bottom;
            }
            .fln{
                font-size: 2px;
            }
        </style>

        <table class="head1">
            <tr>
                <th>
                    <img src="informes/images/MINISTERIOEDUCACION.png" height="30" width="75"> 
                </th>
                <th>
                <ul>
                    <li><span class="s1">UNIDAD EDUCATIVA COLEGIO FRANCISCANO MARÍA AUXILIADORA</span></li>
                    <li><span class="s1">CÓDIGO PLANTEL S3437D2001</span></li>
                    <li><span class="s1">CONVENIO M.P.P.P.E – AVEC</span></li>
                    <li><span class="s1">CORDERO – ESTADO TÁCHIRA</span></li>
                </ul>
                </th>
                <th align="right">
                    <img src="informes/images/coframa_logo.jpg" height="35" width="35" >
                </th>
            </tr>
        </table>

        ';

        $this->setHeaderMargin( 5 );
        $this->setFooterMargin( 5 );
        $this->SetFont('helvetica');
        $this->SetFontSize(9);
        
        
        $this->writeHTML($html, true, false, true, false, '');

    }


}


