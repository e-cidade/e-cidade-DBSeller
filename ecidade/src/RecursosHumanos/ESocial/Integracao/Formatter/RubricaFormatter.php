<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use ECidade\RecursosHumanos\ESocial\Integracao\Formatter\Formatter;
use Exception;
use Rubrica;
use stdClass;
use RubricaRepository;
use RubricaEsocial;

/**
 * Formata os dados da Rubrica
 *
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 * @author Andrio Costa <andrio.costa@dbseller.com.br>
 * @author Lucas Cavalcanti <lucas.cavalcanti@dbseller.com.br>
 */
class RubricaFormatter extends Formatter
{

    /**
     * @var rubrica
     */
    private $rubricasNaoProcessadas = [];

    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array
     */
    public function formatar($dados)
    {
        $rubricasValidas = [];
        foreach ($dados as $dadosRubrica) {
            try {
                $rubrica = RubricaRepository::getInstanciaByCodigo($dadosRubrica);
                if ($rubrica->isAtivo() != true || $rubrica->isAtivo() != 't') {
                    continue;
                }
                $rubricasValidas[] = $this->processamento($rubrica);
            } catch (Exception $e) {
                $this->rubricasNaoProcessadas[$dadosRubrica] = "Rubrica provavelmente excluida do sistema.";
            }
        }
        return $rubricasValidas;
    }

    /**
     * Preenche os Grupos e Campos do layout.
     */
    private function processamento($rubrica)
    {
        $rubricaValida = new stdClass();
        $rubricaValida->inscricao_empregador = $this->getEmpregador()->getCnpj();
        $rubricaValida->referencia = $rubrica->getCodigo();

        $rubricaValida->ideRubrica = new stdClass();
        $rubricaValida->ideRubrica->codRubr = $rubrica->getCodigo();
        $rubricaValida->ideRubrica->ideTabRubr = $rubrica->getCodigo();

        $esocialRubricas = new RubricaEsocial($rubrica->getCodigo());
        $rubricaValida->ideRubrica->iniValid = substr($esocialRubricas->getInicioValidade(), 0, 7);
        $rubricaValida->ideRubrica->fimValid = substr($esocialRubricas->getFimValidade(), 0, 7);

        $rubricaValida->dadosRubrica = new stdClass();
        $rubricaValida->dadosRubrica->dscRubr    = $rubrica->getDescricao();
        $rubricaValida->dadosRubrica->natRubr    = (int) $esocialRubricas->getNaturezaRubrica();
        $rubricaValida->dadosRubrica->tpRubr     = (int) $rubrica->getTipo();
        $rubricaValida->dadosRubrica->codIncCP   = $esocialRubricas->getCodIncCP();
        $rubricaValida->dadosRubrica->codIncIRRF = $esocialRubricas->getCodIncIRRF();
        $rubricaValida->dadosRubrica->codIncFGTS = $esocialRubricas->getCodIncFGTS();
        $rubricaValida->dadosRubrica->codIncCPRP = $esocialRubricas->getCodIncCPRP();
        $rubricaValida->dadosRubrica->codIncPisPasep = $esocialRubricas->getCodIncPisPasep();
        $rubricaValida->dadosRubrica->tetoRemun  = $esocialRubricas->getTetoRemuneracao();
        $rubricaValida->dadosRubrica->observacao = $rubrica->getObservacao();

        $rubricaValida->dadosRubrica->ideProcessoCP = [];
        $rubricaValida->dadosRubrica->ideProcessoCP[] = new stdClass();

        $rubricaValida->dadosRubrica->ideProcessoCP[0]->tpProc =  (int) $esocialRubricas->getTipoProcesso();

        $rubricaValida->dadosRubrica->ideProcessoCP[0]->nrProc = $esocialRubricas->getNumeroProcessoCP();
        $rubricaValida->dadosRubrica->ideProcessoCP[0]->extDecisao = (int) $esocialRubricas->getExtensaoDecisao();
        $rubricaValida->dadosRubrica->ideProcessoCP[0]->codSusp
            = $esocialRubricas->getCodigoSuspensaoProcessoCP();

        $rubricaValida->dadosRubrica->ideProcessoIRRF = [];
        $rubricaValida->dadosRubrica->ideProcessoIRRF[] = new stdClass();
        $rubricaValida->dadosRubrica->ideProcessoIRRF[0]->nrProc = $esocialRubricas->getNumeroProcessoIRRF();
        $rubricaValida->dadosRubrica->ideProcessoIRRF[0]->codSusp = $esocialRubricas->getCodigoSuspensaoIRRF();

        $rubricaValida->dadosRubrica->ideProcessoFGTS = [];
        $rubricaValida->dadosRubrica->ideProcessoFGTS[] = new stdClass();
        $rubricaValida->dadosRubrica->ideProcessoFGTS[0]->nrProc = $esocialRubricas->getNumeroProcessoFGTS();

        $rubricaValida->dadosRubrica->ideProcessoPisPasep = [];
        $rubricaValida->dadosRubrica->ideProcessoPisPasep[] = new stdClass();
        $rubricaValida->dadosRubrica->ideProcessoPisPasep[0]->nrProc
            = $esocialRubricas->getNumeroProcessoPisPasep();
        $rubricaValida->dadosRubrica->ideProcessoPisPasep[0]->codSusp
            = $esocialRubricas->getCodigoSuspensaoPisPasep();

        $this->regraRubricaFormatter($rubricaValida);

        return $rubricaValida;
    }

    /**
     * Remove os Grupos e Campos não obrigatórios do layout.
     */
    private function regraRubricaFormatter(&$rubricaValida)
    {
        if (!isset($rubricaValida->ideRubrica->fimValid) || empty($rubricaValida->ideRubrica->fimValid)) {
            unset($rubricaValida->ideRubrica->fimValid);
        }

        if (!isset($rubricaValida->dadosRubrica->codIncCPRP)) {
            unset($rubricaValida->dadosRubrica->codIncCPRP);
        }

        if (!isset($rubricaValida->dadosRubrica->codIncPisPasep)
            || empty($rubricaValida->dadosRubrica->codIncPisPasep)) {
                unset($rubricaValida->dadosRubrica->codIncPisPasep);
        }

        if (!isset($rubricaValida->dadosRubrica->tetoRemun)) {
            unset($rubricaValida->dadosRubrica->tetoRemun);
        }

        if (!isset($rubricaValida->dadosRubrica->observacao)) {
            unset($rubricaValida->dadosRubrica->observacao);
        }
        $this->validaIdeProcessoCP($rubricaValida);
        $this->validaIdeProcessoIRRF($rubricaValida);
        $this->validaIdeProcessoFGTS($rubricaValida);
        $this->validaIdeProcessoPisPasep($rubricaValida);
    }

    private function validaIdeProcessoCP(&$rubricaValida)
    {
        if (isset($rubricaValida->dadosRubrica->ideProcessoCP[0])) {
            if (empty($rubricaValida->dadosRubrica->ideProcessoCP[0]->tpProc)) {
                unset($rubricaValida->dadosRubrica->ideProcessoCP[0]->tpProc);
            }
            if (empty($rubricaValida->dadosRubrica->ideProcessoCP[0]->nrProc)) {
                unset($rubricaValida->dadosRubrica->ideProcessoCP[0]->nrProc);
            }
            if (empty($rubricaValida->dadosRubrica->ideProcessoCP[0]->extDecisao)) {
                unset($rubricaValida->dadosRubrica->ideProcessoCP[0]->extDecisao);
            }
            if (empty($rubricaValida->dadosRubrica->ideProcessoCP[0]->codSusp)) {
                unset($rubricaValida->dadosRubrica->ideProcessoCP[0]->codSusp);
            }

            if (!isset($rubricaValida->dadosRubrica->ideProcessoCP[0]->tpProc) &&
                !isset($rubricaValida->dadosRubrica->ideProcessoCP[0]->nrProc) &&
                !isset($rubricaValida->dadosRubrica->ideProcessoCP[0]->extDecisao) &&
                !isset($rubricaValida->dadosRubrica->ideProcessoCP[0]->codSusp)) {
                unset($rubricaValida->dadosRubrica->ideProcessoCP);
            }
        }
    }

    private function validaIdeProcessoIRRF(&$rubricaValida)
    {
        if (isset($rubricaValida->dadosRubrica->ideProcessoIRRF[0])) {
            if (empty($rubricaValida->dadosRubrica->ideProcessoIRRF[0]->nrProc)) {
                unset($rubricaValida->dadosRubrica->ideProcessoIRRF[0]->nrProc);
            }
            if (empty($rubricaValida->dadosRubrica->ideProcessoIRRF[0]->codSusp)) {
                unset($rubricaValida->dadosRubrica->ideProcessoIRRF[0]->codSusp);
            }

            if (!isset($rubricaValida->dadosRubrica->ideProcessoIRRF[0]->nrProc) &&
                !isset($rubricaValida->dadosRubrica->ideProcessoIRRF[0]->codSusp)) {
                unset($rubricaValida->dadosRubrica->ideProcessoIRRF);
            }
        }
    }

    private function validaIdeProcessoFGTS(&$rubricaValida)
    {
        if (isset($rubricaValida->dadosRubrica->ideProcessoFGTS[0])) {
            if (empty($rubricaValida->dadosRubrica->ideProcessoFGTS[0]->nrProc)) {
                unset($rubricaValida->dadosRubrica->ideProcessoFGTS[0]->nrProc);
            }

            if (!isset($rubricaValida->dadosRubrica->ideProcessoFGTS[0]->nrProc)) {
                unset($rubricaValida->dadosRubrica->ideProcessoFGTS);
            }
        }
    }

    private function validaIdeProcessoPisPasep(&$rubricaValida)
    {
        if (isset($rubricaValida->dadosRubrica->ideProcessoPisPasep[0])) {
            if (empty($rubricaValida->dadosRubrica->ideProcessoPisPasep[0]->nrProc)) {
                unset($rubricaValida->dadosRubrica->ideProcessoPisPasep[0]->nrProc);
            }
            if (empty($rubricaValida->dadosRubrica->ideProcessoPisPasep[0]->codSusp)) {
                unset($rubricaValida->dadosRubrica->ideProcessoPisPasep[0]->codSusp);
            }

            if (!isset($rubricaValida->dadosRubrica->ideProcessoPisPasep[0]->nrProc) &&
                !isset($rubricaValida->dadosRubrica->ideProcessoPisPasep[0]->codSusp)) {
                unset($rubricaValida->dadosRubrica->ideProcessoPisPasep);
            }
        }
    }
}
