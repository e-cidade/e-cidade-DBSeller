<?php


namespace ECidade\Educacao\Escola\Repository;

use \cl_feriado;
use \cl_periodocalendario;

use Exception;

/**
 * Class FeriadoCalendarioRepository
 * @package ECidade\Educacao\Escola\Repository
 */
class FeriadoCalendarioRepository extends Repository
{

    public function adicioneFeriadoEscolas($codigoCalendario, $clferiado)
    {
        $oDaoFeriadoCalendario =  new cl_feriado;
        $sWhere = " ed54_i_calendario = {$codigoCalendario}";
        $sCampos = " *";
        $existe = false;
        $sSqlFeriados = $oDaoFeriadoCalendario->sql_query(
            null,
            $sCampos,
            "",
            $sWhere
        );
                                                    
        $rsFeriados = $oDaoFeriadoCalendario->sql_record($sSqlFeriados);

        if ($oDaoFeriadoCalendario->numrows > 0) {
            $Feriados = \db_utils::getCollectionByRecord($rsFeriados, false, false, true);
        
            foreach ($Feriados as $Feriado) {
                if ($Feriado->ed54_d_data == $clferiado->ed54_d_data) {
                    $existe = true;
                }
            }
        }
      
        if ($existe == false) {
            $oDaoFeriadoNovo  =  new cl_feriado;
            $oDaoFeriadoNovo->ed54_i_calendario = $codigoCalendario;
            $oDaoFeriadoNovo->ed54_c_descr      = $clferiado->ed54_c_descr;
            $oDaoFeriadoNovo->ed54_c_diasemana  = $clferiado->ed54_c_diasemana;
            $oDaoFeriadoNovo->ed54_d_data       = $clferiado->ed54_d_data;
            $oDaoFeriadoNovo->ed54_c_dialetivo  = $clferiado->ed54_c_dialetivo;
            $oDaoFeriadoNovo->ed54_i_evento     = $clferiado->ed54_i_evento;
            $oDaoFeriadoNovo->incluir(null);
        }
    }
      
    public function saveFeriadoEscolas($codigoCalendario, $clferiado, $dataAnterior, $eventoAnterior)
    {
      
        $oDaoFeriadoCalendario  =  new cl_feriado;
        $sWhere                 = " ed54_i_calendario = {$codigoCalendario}";
        $sCampos                = " *";
        $sSqlFeriados           = $oDaoFeriadoCalendario->sql_query(
            null,
            $sCampos,
            "",
            $sWhere
        );

        $rsFeriados = $oDaoFeriadoCalendario->sql_record($sSqlFeriados);

        if ($oDaoFeriadoCalendario->numrows > 0) {
            $Feriados = \db_utils::getCollectionByRecord($rsFeriados, false, false, true);
        
            foreach ($Feriados as $Feriado) {
                if ($Feriado->ed54_d_data == $dataAnterior && $Feriado->ed54_i_evento == $eventoAnterior) {
                    $oDaoFeriadoNovo  =  new cl_feriado;
                    $oDaoFeriadoNovo->ed54_i_codigo     = $Feriado->ed54_i_codigo;
                    $oDaoFeriadoNovo->ed54_i_calendario = $Feriado->ed54_i_calendario;
                    $oDaoFeriadoNovo->ed54_c_descr      = $clferiado->ed54_c_descr;
                    $oDaoFeriadoNovo->ed54_c_diasemana  = $clferiado->ed54_c_diasemana;
                    $oDaoFeriadoNovo->ed54_d_data       = $clferiado->ed54_d_data;
                    $oDaoFeriadoNovo->ed54_c_dialetivo  = $clferiado->ed54_c_dialetivo;
                    $oDaoFeriadoNovo->ed54_i_evento     = $clferiado->ed54_i_evento;
                    $oDaoFeriadoNovo->alterar($Feriado->ed54_i_codigo);
                }
            }
        }
    }
    
    public function excluiFeriadoEscolas($codigoCalendario, $clferiado)
    {
      
        $oDaoFeriadoCalendario  =  new cl_feriado;
        $sWhere                 = " ed54_i_calendario = {$codigoCalendario}";
        $sCampos                = " *";
        $sSqlFeriados           = $oDaoFeriadoCalendario->sql_query(
            null,
            $sCampos,
            "",
            $sWhere
        );
    
        $rsFeriados = $oDaoFeriadoCalendario->sql_record($sSqlFeriados);
                                                                
        if ($oDaoFeriadoCalendario->numrows > 0) {
            $Feriados = \db_utils::getCollectionByRecord($rsFeriados, false, false, true);
            $oDaoFeriadoNovo  =  new cl_feriado;
    
            foreach ($Feriados as $Feriado) {
                if ($Feriado->ed54_d_data == $clferiado->ed54_d_data &&
                        $Feriado->ed54_i_evento == $clferiado->ed54_i_evento) {
                    $oDaoFeriadoNovo->excluir($Feriado->ed54_i_codigo);
                }
            }
        }
    }
}
