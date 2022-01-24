<?php
//require('../../../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once("$CFG->libdir/formslib.php");
require_once $CFG->libdir.'/adminlib.php';

defined('MOODLE_INTERNAL') || die();
require_login(0, false);

require_once($CFG->libdir . '/pdflib.php');

class informpdf extends TCPDF {

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


    
    public function individual_inform($obj){
        $this->SetPrintHeader(false);
        $this->SetPrintFooter(false);
        $this->setHeaderMargin( 10);
       
        $this->SetMargins( 15,15, 15, false );
        $this->setFooterMargin( 2 );
        $this->SetFont('helvetica');
        $this->SetFontSize(9);
        $this->AddPage();
            
        $html = '
            <style>
            table {border: none;}
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

                    <table class="head1">
                        <tr>
                            <th colspan="1" rowspan="7">
                                <img src="informes/images/MINISTERIOEDUCACION.png" height="60" width="150" >
                            </th>
                            <th colspan="2"></th>
                            <th colspan="1" rowspan="7">
                                <img src="'.$this->gimf('uri_logo').'" height="60" width="60" >
                            </th>
                        </tr>
                        <tr>          
                            <th colspan="2">'.$this->gimf('nombre_plantel').'</th>
                        </tr>
                        <tr> 
                        <th colspan="2">CODIGO PLANTEL '.$this->gimf('codigo_plantel').'</th>
                        </tr>
                        <tr> 
                        <th colspan="2">CONVENIO M.P.P.P.E – AVEC</th>
                        </tr>
                        <tr>
                        <th colspan="2">'.$this->gimf('direccion').'</th>
                        </tr>
                        <tr> 
                            <th colspan="2">RIF. J-31118560-7</th> 
                        </tr>
                        <tr> 
                            <th colspan="2"></th> 
                        </tr>
                    </table>

                    <div></div>
                    <table >
                        <tr>
                            <td align="" colspan="7"><b>Nombre del escolar: '.$obj['nombreyapellido'].'</b></td>
                            <td align="" colspan="1"><b>C.I/C.E:</b></td>
                            <td align="" colspan="2"><b>'.$obj['cedula'].'</b></td>
                        </tr>
                        <tr>
                            <td align="" colspan="10"><b>Representante: '.$obj['n_representante'].'</b></td>
                        </tr>';
                       

                        if($obj['inicial']){
                            $html .= '
                                <tr>
                                    <td align="" colspan="2"><b>Nivel: '.$obj['año'].'</b></td>
                                    <td align="center" colspan="2"><b>grupo: '.$obj['seccion'].'</b></td>
                                    <td align="center" colspan="2"><b>Seccion:'.$obj['seccion'].'</b></td>
                                    <td align="center" colspan="2"></td>
                                </tr> 
                                <tr>
                                    <td align="" colspan="10"><b>Planes y Proyectos de Aprendizaje: '.$obj['proyecto'].'</b></td>
                                </tr>';                               
                                
                        }
                        else{
                            $html .= '
                                <tr>
                                    <td align="" colspan="2"><b>Grado:'.$obj['año'].'</b></td>
                                    <td align="center" colspan="2"><b>Seccion:'.$obj['seccion'].'</b></td>
                                    <td align="center" colspan="2"></td>
                                </tr>
                                <tr>
                                    <td align="" colspan="10"><b>Nombre del proyecto: '.$obj['proyecto'].'</b></td>
                                </tr>';  

                        }
                            
                       $html .= '
                            <tr>
                                <td align="" colspan="10"><b>Docente(s): '.$obj['docente'].' / '.$obj['asistente'].'</b></td>
                            </tr> 
                            <tr>
                                <td align="" colspan="10"><b>Fecha:'.$obj['fecha'].'</b></td>
                            </tr>

                            </table>
                            
                            <div></div>
                            <table >
                                <tr>';
                        if($obj['inicial']){
                            $html .= '
                                        <td align="center"><b>INFORME DESCRIPTIVO '.$obj['tipo_eval'].'</b></td>
                                    </tr>
                                </table>';
                        }
                        else{
                            $html .= '
                                    <td align="center"><b>INFORME'.$obj['tipo_eval'].' '.$obj['momento'].' MOMENTO</b></td>
                                </tr>
                            </table>';

                        }
              
             $this->writeHTML($html, true, false, true, false, '');
             $this->SetFontSize(10);
             $this->writeHTMLCell(170, 175,22 ,75, $obj['informe_descriptivo'], 1);
             $this->SetFontSize(10);

             $html='';
             if($obj['inicial']){
                $html = '
                    <table >
                        <div></div>
                        <div></div>
                        <tr>
                            <td align="center" colspan="4"><b>Docente</b></td> 
                            <td align="center" colspan="3"><b>Sello</b></td> 
                            <td align="center" colspan="4"><b>Docente</b></td> 
                        </tr>
                        <tr>
                            <td align="center" colspan="4"><b>Pedagoga:('.$obj['docente'].')</b></td> 
                            <td align="center" colspan="3"><b></b></td> 
                            <td align="center" colspan="4"><b>Prof.('.$obj['asistente'].')</b></td> 
                        </tr>

                    </table>';
            }
            else{
                $html = '
                    <table >
                        
                        <div></div>
                        <div></div>
                        <tr>
                            <td align="center" colspan="4"><b>'.$obj['nombre_director'].'</b></td> 
                            <td align="center" colspan="3"><b>Sello del Plantel</b></td> 
                            <td align="center" colspan="4"><b>'.$obj['docente'].'</b></td> 
                        </tr>   
                        <tr>
                            <td align="center" colspan="4"><b>Director</b></td> 
                            <td align="center" colspan="3"><b></b></td> 
                            <td align="center" colspan="4"><b>Docente</b></td> 
                        </tr>
                    </table>';

            }
            $this->writeHTMLCell(170, 21,22 ,250,$html, 0);

    }

    public function inform_full($obj,$out){

        return parent::Output('informe_'.$obj['cedula'].'_'.$obj['año_esc'].date('d/m/Y').'.pdf', $out); 

    }
    public function inform_fail($obj){
        $this->SetPrintHeader(false);
        $this->SetPrintFooter(false);
        $this->setHeaderMargin( 10);
       
        $this->SetMargins( 15,15, 15, false );
        $this->setFooterMargin( 2 );
        $this->SetFont('helvetica');
        $this->SetFontSize(9);
        $this->AddPage();
        
        $html = '
            <table >
                <tr>
                    <td align="" colspan="7"><b>Nombre del escolar: '.$obj['nombreyapellido'].'</b></td>
                    <td align="" colspan="1"><b>C.I/C.E:</b></td>
                    <td align="" colspan="2"><b>'.$obj['cedula'].'</b></td>
                </tr>
                <tr>
                    <td align="" colspan="10"><b>Materia/Asignatura/curso: '.$obj['titulo_materia'].'</b></td>
                </tr>
                <tr>
                    <td align="" colspan="10"><b>Momento/Lapso: '.$obj['momento'].'</b></td>
                </tr>
                <tr>
                    <td align="center" colspan="10"><b>...</b></td>
                </tr>
                <tr>
                    <td align="center" colspan="10"><b>...</b></td>
                </tr>
                <tr>
                    <td align="center" colspan="10"><b>...</b></td>
                </tr>
                <tr>
                    <td align="center" colspan="10"><b></b></td>
                </tr>
                <tr>
                    <td align="center " colspan="10"><b> Este informe no esta listo para enviar. Porfavor, cambie el estatus a "POR ENVIAR" para proceder con la visualizacion/envio.</b></td>
                </tr>
            </table >
        ';

        $this->writeHTML($html, true, false, true, false, '');
       

    }
}


