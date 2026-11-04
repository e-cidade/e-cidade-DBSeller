<?php
namespace ECidade\Tributario\Issqn\Inscricao\Service\Procedure;

use ECidade\Tributario\Library\Procedure;
use \Empresa;
use \DateTime;

class CalculoIssqn extends Procedure
{
    /**
     * @param Empresa $empresa
     * @param DateTime $dataCalculo
     * @param $codigoInstituiacao
     * @param $ano
     * @return string
     * @throws \BusinessException
     * @throws \DBException
     */
    public function execute(Empresa $empresa, DateTime $dataCalculo, $codigoInstituiacao, $ano)
    {
        $atividades = array_map(function ($atividade) {
            return $atividade->getSequencial();
        }, $empresa->getAtividades());

        /**
         * @todo implementar todos os tipos (TODOS, ISSQN e ALVARÁ)
         */
        $idUsuario = db_getsession("DB_id_usuario");
        $sql = "SELECT fc_issqn({$idUsuario},
                                {$empresa->getInscricao()},
                                '{$dataCalculo->format('Y-m-d')}',
                                {$ano},
                                null,
                                'true',
                                'false',
                                {$codigoInstituiacao},
                                '" . implode(",", $atividades)."',
                                2,
                                1,
                                0) AS resultado_calculo";

        $rs = $this->dataBase->execute($sql);

        if (!$rs) {
            throw new \DBException("Erro ao Processar calculo: ".pg_last_error()." - ".$sql);
        }

        $resultado  = \db_utils::fieldsMemory($rs, 0)->resultado_calculo;

        if (substr($resultado, 0, 2) != "01") {
            throw new \BusinessException("Erro ao Processar Cálculo : \n\n{$resultado}");
        }

        return "Cálculo processado com sucesso";
    }
}
