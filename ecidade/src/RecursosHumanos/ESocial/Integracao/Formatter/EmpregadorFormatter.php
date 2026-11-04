<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use stdClass;

/**
 * Formata os dados do Empregador
 *
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 * @author Andrio Costa <andrio.costa@dbseller.com.br>
 */
class EmpregadorFormatter extends Formatter
{
    /**
     * @var Instituicao
     */
    private $instituicao;

    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
    }

    public function getInstituicao()
    {
        return $this->instituicao;
    }

    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array|stdClass[]
     */
    public function formatar($dados)
    {
        // Pega os dados preenchidos em formulários
        $dadosFormulario = parent::formatar($dados);
        return $this->posProcessamento($dadosFormulario);
    }

    /**
     * Realiza uma consistencia nos dados enviados
     *
     * @param array $dadosFormatado
     * @return array
     */
    private function posProcessamento(array $dadosFormatado)
    {
        foreach ($dadosFormatado as $dadoEmpregador) {
            if (empty($dadoEmpregador->infoCadastro->indConstr)) {
                $dadoEmpregador->infoCadastro->indConstr = 0;
            }

            if (!isset($dadoEmpregador->infoCadastro->indCoop)
                || (isset($dadoEmpregador->infoCadastro->natJurid)
                    && $dadoEmpregador->infoCadastro->natJurid != "2143")
            ) {
                $dadoEmpregador->infoCadastro->indCoop = 0;
            }

            if (empty($dadoEmpregador->infoCadastro->cnpjEFR)) {
                unset($dadoEmpregador->infoCadastro->cnpjEFR);
            } else {
                $cnpj = str_pad($dadoEmpregador->infoCadastro->cnpjEFR, 14, '0', STR_PAD_LEFT);
                $dadoEmpregador->infoCadastro->cnpjEFR = $cnpj;
            }

            if (empty($dadoEmpregador->infoCadastro->dtTrans11096)) {
                unset($dadoEmpregador->infoCadastro->dtTrans11096);
            }

            if (empty($dadoEmpregador->infoCadastro->indTribFolhaPisPasep)) {
                unset($dadoEmpregador->infoCadastro->indTribFolhaPisPasep);
            }

            if (isset($dadoEmpregador->infoCadastro->indOpcCP) && empty($dadoEmpregador->infoCadastro->indOpcCP)) {
                unset($dadoEmpregador->infoCadastro->indOpcCP);
            }
            if (isset($dadoEmpregador->infoCadastro->indPorte) && empty($dadoEmpregador->infoCadastro->indPorte)) {
                unset($dadoEmpregador->infoCadastro->indPorte);
            }


            // grupo dadosIsencao é opcional
            if (isset($dadoEmpregador->dadosIsencao)) {
                if (!$this->validaSeGrupoFoiPreenchido(get_object_vars($dadoEmpregador->dadosIsencao))) {
                    unset($dadoEmpregador->dadosIsencao);
                }
            }

            if (!empty($dadoEmpregador->dadosIsencao)
                && isset($dadoEmpregador->infoCadastro->classTrib)
                && $dadoEmpregador->infoCadastro->classTrib != 80) {
                unset($dadoEmpregador->dadosIsencao);
            }

            unset($dadoEmpregador->infoOrgInternacional);

            $dadoEmpregador->idePeriodo = new \stdClass();
            $dadoEmpregador->idePeriodo->iniValid = !empty($dadoEmpregador->infoCadastro->iniValid1000)
                ? $dadoEmpregador->infoCadastro->iniValid1000 : null;
            $dadoEmpregador->idePeriodo->fimValid = !empty($dadoEmpregador->infoCadastro->fimValid1000)
                ? $dadoEmpregador->infoCadastro->fimValid1000 : null;

            unset($dadoEmpregador->infoCadastro->iniValid1000);
            unset($dadoEmpregador->infoCadastro->fimValid1000);

            unset($dadoEmpregador->ideEstab);
        }

        return $dadosFormatado;
    }
}
