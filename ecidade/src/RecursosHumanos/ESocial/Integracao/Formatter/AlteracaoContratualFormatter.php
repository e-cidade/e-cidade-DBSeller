<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Repository\ServidorAlteracao;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;
use ServidorRepository;

/**
 * Formata os dados da Exclusão de Eventos
 *
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 * @author John Reis <john.reis@dbseller.com.br>
 */
class AlteracaoContratualFormatter extends ServidorFormatter
{
    private $dataAlteracao = null;

    private $regTrab = null;

    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array
     */
    public function formatar($dados, $alteracao = true)
    {
        $retorno = [];

        $dadosServidor = parent::formatar($dados, $alteracao);
        foreach ($dadosServidor as $servidor) {
            $this->dataAlteracao =
                ServidorAlteracao::findMatriculaByLayout(
                    $servidor->referencia,
                    Tipo::S2206,
                    false,
                    true
                );
            if ($this->dataAlteracao) {
                $retorno[] = $this->processamento($servidor);
                $this->dataAlteracao->setProcessamentoS2206(true);
                $this->dataAlteracao->save();
            }
        }
        return $retorno;
    }

    /**
     * Realiza uma consistência nos dados enviados
     *
     * @param $dado
     */
    private function processamento($servidor)
    {

        $novoDado = new \stdClass();
        $novoDado->inscricao_empregador = $this->getEmpregador()->getCnpj();
        $novoDado->referencia = $servidor->referencia;
        if (!empty($novoDado->inscricao_empregador)) {
            $novoDado->ideEmpregador['tpInsc'] = 1;
            $novoDado->ideEmpregador['nrInsc'] = $novoDado->inscricao_empregador;
            $servidorDado = ServidorRepository::getInstanciaByCodigo($servidor->referencia);
            if ($servidorDado) {
                if (!empty($servidorDado->getDadosCargo()->rh37_acumcargo)) {
                    $novoDado->ideVinculo['acumCargo'] =
                    $servidorDado->getDadosCargo()->rh37_acumcargo === 'true' ? 'S' : 'N';
                    $servidor->vinculo['infoContrato']['acumCargo'] = $novoDado->ideVinculo['acumCargo'];
                }
            }
        }

        $novoDado->ideVinculo = [];
        $novoDado->ideVinculo['cpfTrab'] = $servidor->trabalhador['cpfTrab'];
        $novoDado->ideVinculo['matricula'] = $servidor->vinculo['matricula'];

        $this->atualizarDadosServidor($novoDado, $servidor);
        return $novoDado;
    }

    private function atualizarDadosServidor($servidorAtual, &$servidor)
    {
        $this->preencheGrupos($servidorAtual, $servidor);
        $this->removeOpcionais($servidorAtual);
    }

    private function preencheGrupos($novoDado, &$servidor)
    {
        $novoDado->vinculo = $servidor->vinculo;

        $novoDado->altContratual = [];

        $novoDado->altContratual = ServidorAlteracao::getGrupoAlteracaoContratual(
            $this->dataAlteracao->getMatricula(),
            $this->dataAlteracao->getDataS2206()->getDate()
        );
        $this->regTrab = $servidor->vinculo['tpRegTrab'];

        $novoDado->altContratual['vinculo']['tpRegPrev'] = $servidor->vinculo['tpRegPrev'];
        if (isset($novoDado->vinculo['infoRegimeTrab']['infoCeletista'])) {
            unset($novoDado->vinculo['infoRegimeTrab']['infoCeletista']['dtAdm']);
            unset($novoDado->vinculo['infoRegimeTrab']['infoCeletista']['tpAdmissao']);
            unset($novoDado->vinculo['infoRegimeTrab']['infoCeletista']['indAdmissao']);
            unset($novoDado->vinculo['infoRegimeTrab']['infoCeletista']['nrProcTrab']);
            unset($novoDado->vinculo['infoRegimeTrab']['infoCeletista']['FGTS']);

            $novoDado->altContratual['vinculo']['infoRegimeTrab']['infoCeletista']['tpRegJor']
                = $servidor->vinculo['infoRegimeTrab']['infoCeletista']['tpRegJor'];

            $novoDado->altContratual['vinculo']['infoRegimeTrab']['infoCeletista']['natAtividade']
                = $servidor->vinculo['infoRegimeTrab']['infoCeletista']['natAtividade'];

            $novoDado->altContratual['vinculo']['infoRegimeTrab']['infoCeletista']['cnpjSindCategProf']
                = $servidor->vinculo['infoRegimeTrab']['infoCeletista']['cnpjSindCategProf'];

            if (isset($novoDado->vinculo['infoRegimeTrab']['infoCeletista']['trabTemporario'])) {
                unset($novoDado->vinculo['infoRegimeTrab']['infoCeletista']['trabTemporario']['hipLeg']);
                unset($novoDado->vinculo['infoRegimeTrab']['infoCeletista']['trabTemporario']['justContr']);
                unset($novoDado->vinculo['infoRegimeTrab']['infoCeletista']['trabTemporario']['ideEstabVinc']);
                unset(
                    $novoDado->vinculo['infoRegimeTrab']['infoCeletista']['trabTemporario']['ideTrabSubstituido']
                );
                $novoDado->vinculo['infoRegimeTrab']['infoCeletista']['trabTemporario']['justProrr'] =
                $novoDado->vinculo['infoRegimeTrab']['infoCeletista']['trabTemporario']['justContr'];
            }
        }

        if (isset($novoDado->vinculo['infoRegimeTrab']['infoEstatutario'])) {
            if (isset($servidor->vinculo['infoRegimeTrab']['infoEstatutario']['tpPlanRP'])) {
                $novoDado->altContratual['vinculo']['infoRegimeTrab']['infoEstatutario']['tpPlanRP']
                = $servidor->vinculo['infoRegimeTrab']['infoEstatutario']['tpPlanRP'];
            } else {
                $servidorMovimentacaoRepository = new ServidorMovimentacaoRepository();

                $movimentacao = $servidorMovimentacaoRepository
                    ->scopeAno($this->getServidorAtual()->getAnoCompetencia())
                    ->scopeMes($this->getServidorAtual()->getMesCompetencia())
                    ->scopeMatricula($this->getServidorAtual()->getMatricula())
                    ->first();
                $tpPlanRP = empty($movimentacao->getTipoSegregacao()) ? 0 : (int) $movimentacao->getTipoSegregacao();
                $novoDado->altContratual['vinculo']['infoRegimeTrab']['infoEstatutario']['tpPlanRP'] = $tpPlanRP;
            }
            if (isset($servidor->vinculo['infoRegimeTrab']['infoEstatutario']['indTetoRGPS'])) {
                $novoDado->altContratual['vinculo']['infoRegimeTrab']['infoEstatutario']['indTetoRGPS']
                = $servidor->vinculo['infoRegimeTrab']['infoEstatutario']['indTetoRGPS'];
            }
            if (!isset($novoDado->altContratual['vinculo']['infoRegimeTrab']['infoEstatutario']['indTetoRGPS'])) {
                $novoDado->altContratual['vinculo']['infoRegimeTrab']['infoEstatutario']['indTetoRGPS'] = 'N';
            }

            if (isset($servidor->vinculo['infoRegimeTrab']['infoEstatutario']['indAbonoPerm'])) {
                $novoDado->altContratual['vinculo']['infoRegimeTrab']['infoEstatutario']['indAbonoPerm']
                = $servidor->vinculo['infoRegimeTrab']['infoEstatutario']['indAbonoPerm'];
            }
            if (isset($servidor->vinculo['infoRegimeTrab']['infoEstatutario']['dtIniAbono'])) {
                $novoDado->altContratual['vinculo']['infoRegimeTrab']['infoEstatutario']['dtIniAbono']
                = $servidor->vinculo['infoRegimeTrab']['infoEstatutario']['dtIniAbono'];
            }
            if (!isset($novoDado->altContratual['vinculo']['infoRegimeTrab']['infoEstatutario']['indAbonoPerm'])) {
                $novoDado->altContratual['vinculo']['infoRegimeTrab']['infoEstatutario']['indAbonoPerm'] = 'N';
            }
        }
        
        //(validação infoRegimeTrab) Removendo Alteração contratual
        if ($servidor->vinculo['tpRegPrev'] == 1 && $servidor->vinculo['tpRegTrab'] == 2) {
            unset($novoDado->altContratual['vinculo']['infoRegimeTrab']);
        }

        // Adicionando regra conforme o layout que diz, quando for S-2006 deve ser informado o cnpj do empregador
        if (isset($novoDado->vinculo['infoContrato']['localTrabalho']) &&
            isset($novoDado->vinculo['infoContrato']['localTrabalho']['localTrabGeral'])) {
            $cnpj = $this->getEmpregador()->getCnpj();
            $novoDado->vinculo['infoContrato']['localTrabalho']['localTrabGeral']['nrInsc'] = $cnpj;
            $novoDado->vinculo['infoContrato']['localTrabalho']['localTrabGeral']['tpInsc'] = 1; //CNPJ
        }
        $novoDado->altContratual['vinculo']['infoContrato'] = $novoDado->vinculo['infoContrato'];
        
        if (sizeof($novoDado->altContratual['vinculo']['infoContrato']['treiCap']) == 0) {
            unset($novoDado->altContratual['vinculo']['infoContrato']['treiCap']);
        }
    }

    private function removeOpcionais($novoDado)
    {

        if (isset($novoDado->vinculo['infoContrato']['duracao'])) {
            unset($novoDado->vinculo['infoContrato']['duracao']['clauAssec']);
            unset($novoDado->vinculo['sucessaoVinc']);
            unset($novoDado->vinculo['transfDom']);
            unset($novoDado->vinculo['mudancaCPF']);
            unset($novoDado->vinculo['afastamento']);
            unset($novoDado->vinculo['desligamento']);
            unset($novoDado->vinculo['cessao']);
        }

        if (empty($novoDado->vinculo['infoContrato']['duracao']['tpContr'])
            && empty($novoDado->vinculo['infoContrato']['duracao']['dtTerm'])
            && empty($novoDado->vinculo['infoContrato']['duracao']['objDet'])) {
            unset($novoDado->vinculo['infoContrato']['duracao']);
        }

        if (empty($novoDado->vinculo['infoContrato']['duracao']['objDet'])) {
            unset($novoDado->vinculo['infoContrato']['duracao']['objDet']);
        }

        if (empty($novoDado->vinculo['infoContrato']['duracao']['dtTerm'])) {
            unset($novoDado->vinculo['infoContrato']['duracao']['dtTerm']);
        }

        if ($this->regTrab != 1
            && !empty($novoDado->altContratual['vinculo']['infoContrato']['duracao'])) {
            unset($novoDado->altContratual['vinculo']['infoContrato']['duracao']);
        }

        if ($this->regTrab != 1
            && !empty($novoDado->altContratual['vinculo']['infoContrato']['remuneracao'])) {
            unset($novoDado->altContratual['vinculo']['infoContrato']['remuneracao']);
        }
        /**
         * Remove grupos pertencentes ao evt2200.
         */

        unset($novoDado->vinculo);
    }
}
