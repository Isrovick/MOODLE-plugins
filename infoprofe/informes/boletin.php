<?php
//require('../../../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();
require_login(0, false);

require_once($CFG->libdir . '/pdflib.php');

class boletinpdf extends TCPDF {

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
    
    public function boletin($obj, $out){

        $this->setHeaderMargin( 5 );
        $this->setFooterMargin( 5 );
        $this->SetMargins( 5,5, 5, false );
        $this->SetFont('helvetica');
        $this->SetFontSize(9);
        $this->AddPage();
        $s='';
        $s.=$this->headerb($obj);
        $s.=$this->boletin_items( $obj);
        $s.=$this->boletin_grades( $obj);
        $s.=$this->footerb($obj);
        
        $this->writeHTML($s, true, false, true, false, '');
        $this->boletin_informs($obj);

        
        return parent::Output('boletin_'.$obj['cedula'].'_'.$obj['año_esc'].date('d/m/Y').'.pdf', $out);
    }

    public function headerb($obj) {
        

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

        <table border="1" class="head1">
            <tr>
                <th colspan="2" rowspan="2"><bold>BOLETÍN DE EVALUACION</bold></th> 
                <th colspan="7">ALUMNO</th>
                <th colspan="2">CODIGO</th>
                <th colspan="1">LISTA</th>
                <th colspan="1">T.ALUMN</th>
                <th colspan="1">CURSO</th>
                <th colspan="1">SEC</th>
                <th colspan="2">FOTO</th> 
            </tr>
            <tr>
                <th colspan="7">'.$obj['nombreyapellido'].'</th> 
                <th colspan="2">'.$this->gimf('codigo_plantel').'</th>
                <th colspan="1">'.$obj['n_lista'].'</th>
                <th colspan="1"></th>
                <th colspan="1">'.$obj['año'].'</th>
                <th colspan="1">'.$obj['seccion'].'</th>
                <th colspan="2" rowspan="7">
                    <img src="'.$obj['user_picture'].'" height="40" width="40" >
                </th>           
            </tr>
            <tr>
                <th colspan="2">ESCUDO</th>
                <th colspan="3">CEDULA IDENTIDAD</th>
                <th colspan="3">FECHA NACIMIENTO</th>
                <th colspan="4">LOC. NACIMIENTO</th>
                <th colspan="3">ESTADO NACIMIENTO</th>
            </tr>
            <tr>
                <th colspan="2" rowspan="5">
                    <img src="'.$this->gimf('uri_logo').'" height="40" width="40" >
                </th>
                <th colspan="3">'.$obj['cedula'].'</th>
                <th colspan="3">'.$obj['fecha_nacimiento'].'</th>
                <th colspan="4">'.$obj['loc_nac'].'</th>
                <th colspan="3">'.$obj['edo_nac'].'</th>
            </tr>
            <tr>   
                <th colspan="6">NOMBRE Y DIRECCION DEL PLANTEL</th>
                <th colspan="5">NOMBRE Y DIRECCION DEL PADRE O REPRESENTANTE</th>
                <th colspan="2">AÑO ESCOLAR</th>
            </tr>
            <tr>   
                <th colspan="6" rowspan="3">
                '.$this->gimf('nombre_plantel').'<br>
                '.$this->gimf('direccion').'<br>
                </th>
                <th colspan="5" rowspan="3">'.$obj['n_representante'].'</th>
                <th colspan="2">'.$obj['año_esc'].'</th>
            </tr>
            <tr>
                <th colspan="2">INASISTENCIAS</th>
            </tr>
            <tr>
                <th colspan="2"></th>
            </tr>   

        </table>

        ';
       
        return $html;

    }

    public function boletin_items($obj){

            $html = '<table border="1" class="headb">
            <tr>
                <th colspan="5">RESULTADOS DE LA EVALUACION</th>
                <th colspan="36">HISTORIA DEL ALUMNO DURANTE EL AÑO ESCOLAR</th>
            </tr>
            <tr>
                <th align="center" colspan="5" rowspan="2">AREAS O MATERIAS</th>
                <th align="center" colspan="10"> Momento 1</th>
                <th align="center" colspan="10"> Momento 2</th>
                <th align="center" colspan="10"> Momento 3</th>
                <th align="center" colspan="2" rowspan="2">TOTAL</th> 
                <th align="center" colspan="2" rowspan="2">REVISION</th> 
                <th align="center" colspan="2" rowspan="2">FINAL</th>
            </tr>
            <tr>
                <th align="center" colspan="1">Ev.1</th>
                <th align="center" colspan="1">112</th>
                <th align="center" colspan="1">Ev.2</th>
                <th align="center" colspan="1">112</th>
                <th align="center" colspan="1">Ev.3</th>
                <th align="center" colspan="1">112</th>
                <th align="center" colspan="1">Ev.4</th>
                <th align="center" colspan="1">112</th>
        

                <th align="center" colspan="2">DEF.LP</th>


                <th align="center" colspan="1">Ev.1</th>
                <th align="center" colspan="1">112</th>
                <th align="center" colspan="1">Ev.2</th>
                <th align="center" colspan="1">112</th>
                <th align="center" colspan="1">Ev.3</th>
                <th align="center" colspan="1">112</th>
                <th align="center" colspan="1">Ev.4</th>
                <th align="center" colspan="1">112</th>
               

                <th align="center" colspan="2">DEF.LP</th>



                <th align="center" colspan="1">Ev.1</th>
                <th align="center" colspan="1">112</th>
                <th align="center" colspan="1">Ev.2</th>
                <th align="center" colspan="1">112</th>
                <th align="center" colspan="1">Ev.3</th>
                <th align="center" colspan="1">112</th>
                <th align="center" colspan="1">Ev.4</th>
                <th align="center" colspan="1">112</th>
               
                <th align="center" colspan="2">DEF.LP</th>

            </tr> 
        </table>
        ';
        
        return $html; 
    }
    public function boletin_grades($obj){
        //$this->writeHTML($obj['boletin_grades'], true, false, true, false, '');
        return $obj['boletin_grades'];
    }
    public function boletin_informs($obj){
    

        /*$this->setHeaderMargin( 15 );
        $this->setFooterMargin( 15 );
        $this->SetMargins( 25,45, 15, false );*/
        

        foreach($obj['boletin_informs'] as $info){
            $this->AddPage();
                $html=$this->headerb($obj);
                $html.= '
                <div></div>
                <table border="1" >
                    <tr>
                        <td align="center" colspan="8"><b>NOMBRE MATERIA</b></td>
                        <td align="center" colspan="2"><b>MOMENTO</b></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="8">'.$info->fullname.'</td>
                        <td align="center" colspan="2">'.$info->meta_key.'</td>
                    </tr>
                </table>
                
                <div></div>
                <table border="1" >
                <tr>
                    <td align="center"><b>INFORME DESCRIPTIVO</b></td>
                </tr>
            </table>';
               //echo $html."<br><br><br>";
             $this->writeHTML($html, true, false, true, false, '');
             $this->writeHTMLCell(192, 216, 10,60, $info->description, 1);
        }

    }
    
    public function footerb($obj) {
        $html = '
                <table><tr><td></td></tr></table>

                <table border="1" class="footerb">

                    <tr>
                        <td colspan="4" rowspan="12">SELLO DEL PLANTEL</td>
                        <td colspan="8" rowspan="12"></td>
                        <td colspan="4">TIPO EVALUACION</td>
                    </tr>
                    <tr>
                        <td colspan="4">'.$obj['tipo_eval'].'</td>
                    </tr>
                    <tr> <td colspan="4">MATRICULA</td> </tr>
                    <tr> <td colspan="4"></td> </tr>
                    <tr> <td colspan="4" rowspan="6">FIRMA DEL DIRECTOR O REPRESENTANTE DEL CONSEJO DOCENTE</td> </tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>
                    <tr><td></td></tr>

                </table>

                <table><tr><td></td></tr></table>

                <table border="1" class="footerb">
                    <tr>
                        <td colspan="6">PROFESOR</td>
                        <td colspan="14">OBSERVACIONES</td>
                        <td colspan="10">JUSTIFICANTE DE LA RECEPCION DE LA BOLETA(ALUMNO)</td>
                    </tr>
                    <tr>
                        <td colspan="6"  rowspan="4"></td>
                        <td colspan="14" rowspan="4"></td>
                        <td colspan="3">Alumno(a)</td>
                        <td colspan="7">'.$obj['nombreyapellido'].'</td>
                    </tr>
                    <tr>
                        <td colspan="2">Curso</td>
                        <td colspan="1">'.$obj['año'].'</td>
                        <td colspan="2">Numero de Lista:</td>
                        <td colspan="1">'.$obj['n_lista'].'</td>
                        <td colspan="1">C.I.:</td>
                        <td colspan="3">'.$obj['cedula'].'</td>
                    </tr>
                    <tr>
                        <td colspan="3">Representante</td>
                        <td colspan="7">'.$obj['n_representante'].'</td>
                    </tr>
                    <tr>
                        <td colspan="5">FECHA</td>
                        <td colspan="5">FIRMA DEL REPRESENTANTE</td>
                    </tr>
                    <tr>
                    <td colspan="20">NOTIFICACION AL REPRESENTANTE</td>
                    <td colspan="5">'.$obj['fecha'].'</td>
                    <td colspan="5" rowspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="20"></td>
                        <td colspan="5">TIPO DE EVALUACION</td>
                    </tr>
                    <tr>
                        <td colspan="20"></td>
                        <td colspan="5">'.$obj['tipo_eval'].'</td>
                        
                    </tr>
                </table>
        ';
        
        //$this->writeHTML($html, true, false, true, false, '');
        return $html;
    }

}


