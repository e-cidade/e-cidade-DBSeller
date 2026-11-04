<?php


namespace ECidade\Educacao\Escola\Repository;

use \cl_matricula;

use Exception;

/**
 * Class MatriculaMovRepository
 * @package ECidade\Educacao\Escola\Repository
 */
class MatriculaMovRepository extends Repository
{
    public function saveMatricula($codigo, $oParam, $dtInicioAnterior)
    {

        $oDaoMatriculas = new cl_matricula();
        $aMatriculas    = array();
      
        $sWhere         = " turma.ed57_i_calendario = {$codigo}";
        $sCampos        = " ed60_i_codigo, ed60_d_datamatricula";
        $sSqlMatricula  = $oDaoMatriculas->sql_query(
            null,
            $sCampos,
            "",
            $sWhere
        );
        $rsMatricula = $oDaoMatriculas->sql_record($sSqlMatricula);
      
        if ($oDaoMatriculas->numrows > 0) {
            $aMatriculas  = \db_utils::getCollectionByRecord($rsMatricula, false, false, true);
      
            foreach ($aMatriculas as $matricula) {
                if ($matricula->ed60_d_datamatricula == $dtInicioAnterior) {
                    $oDaoMatricula  = new cl_matricula();
      
                    $oDaoMatricula->ed60_d_datamatricula = $oParam->dDataInicio;
                    $oDaoMatricula->ed60_i_codigo = $matricula->ed60_i_codigo;
      
                    $oDaoMatricula->alterar($matricula->ed60_i_codigo);
      
                    $dtInicio = str_replace("/", "-", $oParam->dDataInicio);
                    $dtInicio = date('Y-m-d', strtotime($dtInicio));
      
                    $sqlMatriculaMov  = " UPDATE matriculamov ";
                    $sqlMatriculaMov .= "   SET ed229_d_dataevento = '{$dtInicio}' ";
                    $sqlMatriculaMov .= "   WHERE ed229_i_matricula = {$matricula->ed60_i_codigo} ";
      
                    db_query($sqlMatriculaMov);
                }
            }
        }
    }
}
