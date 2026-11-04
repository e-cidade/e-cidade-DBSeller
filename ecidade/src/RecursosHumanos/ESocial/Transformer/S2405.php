<?php

namespace ECidade\RecursosHumanos\ESocial\Transformer;

use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Repository\ServidorAlteracao;
use InvalidArgumentException;
use DBDate;
use DBException;
use db_utils;

/**
 * Class S2405
 * @package ECidade\RecursosHumanos\ESocial\Transformer
 */
class S2405
{
    /**
     * @var string $matricula
     */
    private $matricula;

    /**
     * S2405 constructor.
     * @param $matricula
     */
    public function __construct($matricula = null)
    {
        if (!empty($matricula)) {
            $this->matricula = $matricula;
        }
    }

    /**
     * Verifica a diferença entre os campos da classe atual x classe após modificação.
     */
    public function setDataS2405($clAtual, $clModificada)
    {
        
        $campos = ['rh01_sexo', 'rh01_raca', 'rh01_estciv'];

        foreach ($campos as $campo) {
            if (isset($clAtual->$campo) && isset($clModificada)
                    && $clAtual->$campo != $clModificada->$campo) {
                $this->salvarDataAlteracao();
            }
        }
    }

    /*
     * Efetua o preenchimento da dtAlteração para o evento S2405.
     * O método compara se existe diferença de atributos entre os campos da classe.
     */
    public function validarS2405($tipo, $clconsulta, $where, $sql = null)
    {
        $result = null;
        $dadosAtuais = null;
        $campos = [];

        switch ($tipo) {
            case $tipo == 'rhdepend':
                $sql = $clconsulta->sql_query(null, '*', null, $where);
                $result = $clconsulta->sql_record($sql);
                $dadosAtuais = pg_fetch_object($result, 0);

                $campos = [
                    'rh31_nome',
                    'rh31_dtnasc',
                    'rh31_irf',
                    'rh31_gparen',
                    'rh31_depend',
                    'rh31_especi',
                    'rh31_fins_previdenciarios',
                    'rh31_descricaoparentesco'
                ];
                return; // remover depois
                break;
            case $tipo == 'rhdependplug':
                $result = db_query($sql);
                if (!$result) {
                    throw new DBException("Erro ao buscar dados do dependente.");
                }

                if (pg_num_rows($result) > 0) {
                    $dadosAtuais = db_utils::fieldsMemory($result, 0);
                    $campos = ['dp01_cpf', 'dp01_sexo'];
                }
                break;
            default:
                throw new InvalidArgumentException("Parâmetro passado {$tipo} não existe.");
        }

        /**
         * Verifica se os campos são diferentes e seta a data de atualização do evento.
         */
        if (!empty($clconsulta) && !empty($dadosAtuais)) {
            foreach ($campos as $campo) {
                if (isset($dadosAtuais->$campo) && $dadosAtuais->$campo != $clconsulta->$campo) {
                    $this->salvarDataAlteracao();
                }
            }
        }
    }

    public function salvarDataAlteracao()
    {
        $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout($this->matricula, Tipo::S2405);
        $servidorAlteracao->setDataS2405(new DBDate(date('Y-m-d')));
        $servidorAlteracao->save();
    }
}
