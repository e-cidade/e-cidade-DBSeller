<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use BusinessException;
use CgmFisico;
use CgmJuridico;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorMovimentacao;
use Exception;
use ServidorRepository;

class CadastroBeneficioFormatter extends Formatter
{
    /**
     * @var \Servidor
     */
    private $servidorAtual;

    /**
     * @var \Servidor
     */
    private $servidorOrigem;

    /**
     * @var \ServidorMovimentacao
     */
    private $movimentacaoAtual;
    /**
     * @var CgmJuridico
     */
    private $empregador;

    /**
     * @var \DBDate
     */
    private $dataObrigatoriedade;

    /**
     * @param array $dados
     * @return array|\Assentamento[]
     * @throws \DBException
     */
    public function formatar($dados)
    {
        $this->dataObrigatoriedade = \DBPessoal::getDataFaseEsocial(2);
        $dadosServidor = [];
        foreach ($dados->beneficios as $servidor) {
            $this->servidorAtual = $servidor;
            $servidorMovimentacaoRepository = new ServidorMovimentacaoRepository();
            $this->movimentacaoAtual = $servidorMovimentacaoRepository
            ->scopeAno($this->servidorAtual->getAnoCompetencia())
            ->scopeMes($this->servidorAtual->getMesCompetencia())
            ->scopeMatricula($this->servidorAtual->getMatricula())
                ->first();
            $dado = $this->processar();
            if ($dado) {
                $dadosServidor[] = $dado;
            }
        }
        return $dadosServidor;
    }

    private function processar()
    {
        $dadosServidor = new \stdClass();
        $cgmServidor = $this->servidorAtual->getCgm();

        if (!($cgmServidor instanceof CgmFisico)) {
            return false;
        }

        $dadosServidor->inscricao_empregador = $this->empregador->getCnpj();
        $dadosServidor->ideEmpregador = new \stdClass();
        $dadosServidor->ideEmpregador->tpInsc = 1;
        $dadosServidor->ideEmpregador->nrInsc = $this->empregador->getCnpj();
        $dadosServidor->referencia = $this->servidorAtual->getMatricula();

        $this->formatarDados($dadosServidor);

        return $dadosServidor;
    }

    private function formatarDados(&$dadosServidor)
    {
        $dadosServidor->beneficiario = new \stdClass();
        $dadosServidor->beneficiario->cpfBenef = $this->servidorAtual->getCgm()->getCpf();
        $dadosServidor->beneficiario->matricula = $this->servidorAtual->getMatricula();
        if ($this->servidorAtual->getMatriculaAnterior() != "") {
            $dadosServidor->beneficiario->matricula = $this->servidorAtual->getMatriculaAnterior();
        }
        $dadosServidor->beneficiario->cnpjOrigem = $this->empregador->getCnpj();
        $matriculaOrigem = $this->servidorAtual->getOrigem();
        if ($matriculaOrigem) {
            $this->servidorOrigem = \ServidorRepository::getInstanciaByCodigo(
                $matriculaOrigem,
                null,
                null,
                null,
                false
            );
            $dadosServidor->beneficiario->cnpjOrigem = $this->servidorOrigem->getInstituicao()->getCgm()->getCnpj();
        }
        $this->dadosInicioBeneficio($dadosServidor);
    }

    private function dadosInicioBeneficio(&$dadosServidor)
    {
        $infoBenInicio = new \stdClass();
        if ($this->dataObrigatoriedade->getDate() > $this->servidorAtual->getDataAdmissao()->getDate()) {
            $infoBenInicio->cadIni = 'S';
        } else {
            $infoBenInicio->cadIni = 'N';
            /**
             * 1 - Benefício concedido pelo próprio órgão declarante
             * 2 - Benefício transferido de outro órgão
             * 3 - Mudança de CPF do beneficiário
            */
            $infoBenInicio->indSitBenef = 1;
        }

        if ($infoBenInicio->cadIni !== 'N') {
            unset($dadosServidor->beneficiario->cnpjOrigem);
        }
        // Numero do Beneficio é a própria matricula
        $infoBenInicio->nrBeneficio = $this->servidorAtual->getMatricula();
        if ($this->servidorAtual->getMatriculaAnterior() != "") {
            $infoBenInicio->nrBeneficio = $this->servidorAtual->getMatriculaAnterior();
        }
        $infoBenInicio->dtIniBeneficio = $this->servidorAtual->getDataAdmissao()->getDate();
        $infoBenInicio->dtPublic = $this->servidorAtual->getDataAdmissao()->getProximoDia()->getDate();
        $dadosServidor->infoBenInicio = $infoBenInicio;

        $this->dadosBeneficio($dadosServidor);
    }

    private function dadosBeneficio(&$dadosServidor)
    {
        $dadosBeneficio = new \stdClass();
        /**
         * Regra de dePara informada pela Analista Lorenna, juntamente com o Sandro
         */
        if ($this->dataObrigatoriedade->getDate() > $this->servidorAtual->getDataAdmissao()->getDate()) {
            $dadosBeneficio->tpBeneficio = $this->deParaCodigos($this->movimentacaoAtual->getTipoAposentadoriaPensao());
        } else {
            $dadosBeneficio->tpBeneficio = $this->movimentacaoAtual->getTipoAposentadoriaPensao();
        }

        $tpPlanRP = $this->movimentacaoAtual->getTipoSegregacao();

        if (empty($tpPlanRP)) {
            $dadosBeneficio->tpPlanRP = 0;
        } else {
            $dadosBeneficio->tpPlanRP = intval($tpPlanRP);
        }

        if (in_array($dadosBeneficio->tpBeneficio, ['0909', '1001', '1009'])) {
            $dsc = $this->movimentacaoAtual->getDescricaoInstrumento();
            if (!empty($dsc)) {
                $dadosBeneficio->dsc = $dsc;
            }
        }

        if ($dadosServidor->infoBenInicio->cadIni == 'N') {
            if ($this->movimentacaoAtual->isPensaoJudicial()) {
                $dadosBeneficio->indDecJud = 'S';
            } else {
                $dadosBeneficio->indDecJud = 'N';
            }
        }
        if (!($this->deParaGrupoBeneficios($dadosBeneficio->tpBeneficio)
            && $dadosServidor->infoBenInicio->cadIni == 'N')
        ) {
            unset($dadosServidor->beneficiario->matricula);
        }
        $infoPenMorte = $this->infoPenMorte();

        if ($infoPenMorte) {
            $dadosBeneficio->infoPenMorte = $infoPenMorte;
            $dadosServidor->beneficiario->matricula = $this->servidorOrigem->getMatricula();
            if ($this->servidorOrigem->getMatriculaAnterior() != "") {
                $dadosServidor->beneficiario->matricula = $this->servidorOrigem->getMatriculaAnterior();
            }
            $dadosServidor->beneficiario->cnpjOrigem = $this->servidorOrigem->getInstituicao()->getCgm()->getCnpj();
        }
        $dadosServidor->infoBenInicio->dadosBeneficio = $dadosBeneficio;
    }

    private function infoPenMorte()
    {
        $infoPenMorte = false;
        if ($this->servidorAtual->possuiPensaoPorMorte()) {
            /**
             *   pada enviar os dados de pensao por morte é levada em consideracao a data inicial dos envios da fase 2
             *   do eSocial
             */
            $dataObrigatoriedade = \DBPessoal::getDataFaseEsocial(2);
            // Se nao tiver configurada a data de obrigatoriedade, desconsideramos os dados
            if (empty($dataObrigatoriedade)) {
                return false;
            }
            // Verificamos de a data de admissao é inferior a data de obrigatoriedade
            if ($this->servidorAtual->getDataAdmissao() < $dataObrigatoriedade) {
                return false;
            }
            $infoPenMorte = new \stdClass();
            //Pensao com validade - temporaria
            if (!empty($this->movimentacaoAtual->getValidadePensao())) {
                $infoPenMorte->tpPenMorte = 2;
            } else {
                // Pensao Vitalicia
                $infoPenMorte->tpPenMorte = 1;
            }
            $infoPenMorte->instPenMorte = $this->instPenMorte($this->servidorAtual->getMatriculaPensaoPorMorte());
        }
        return $infoPenMorte;
    }

    private function instPenMorte($matricula)
    {
        $instPenMorte = new \stdClass();
        $sql = "SELECT rh01_regist FROM rhpessoal  WHERE  rh01_regist = {$matricula}";
        $rs = db_query($sql);
        if (!$rs) {
            $msg1 = "Erro ao buscar matricula ,<b>{$matricula}</b>";
            $msg1 .= ", necessário realizar correção no sistema";
            throw new \DBException($msg1);
        }

        $instPenMorte->dtInst = '';
        $instPenMorte->cpfInst = '';

        if (pg_numrows($rs) > 0) {
            $servidorPensao = \ServidorRepository::getInstanciaByCodigo($matricula, null, null, null, false);
            $instPenMorte->cpfInst = $servidorPensao->getCgm()->getCpf();
            /**
             * alterada regra a pedido do analista Sandro no dia 16/08/2022
             * a data de falecimento da matricula de origem sera a data de admissao do pensionista
             */
            $instPenMorte->dtInst = $this->servidorAtual->getDataAdmissao()->getDate();
        }

        return $instPenMorte;
    }

    /**
     * @param  CgmJuridico  $empregador
     */
    public function setEmpregador(CgmJuridico $empregador)
    {
        $this->empregador = $empregador;
    }

    public function deParaCodigos($codigo)
    {
        switch ($codigo) {
            case "0102":
            case "0106":
                return "0801";
                break;
            case "0105":
            case "0103":
            case "0101":
                return "0802";
                break;
            case "0302":
                return "0804";
                break;
            case "0301":
                return "0803";
                break;
            case "0601":
                return "0808";
                break;
            case "0603":
                return "0807";
                break;
            default:
                return $codigo;
            break;
        }
    }

    private function deParaGrupoBeneficios($codigo)
    {
        $grupo = substr($codigo, 0, 2);
        $grupos = ['01', '02', '03', '04', '06', '11'];
        if (in_array($grupo, $grupos)) {
            return true;
        }
        return false;
    }
}
