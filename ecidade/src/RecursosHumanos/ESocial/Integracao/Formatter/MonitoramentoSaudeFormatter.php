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

use ECidade\RecursosHumanos\ESocial\Repository\TrabalhadorSemVinculoInicio;
use ECidade\RecursosHumanos\RH\Assentamento\Model\ControleMedico;
use stdClass;
use DBCompetencia;
use DBDate;

class MonitoramentoSaudeFormatter extends Formatter
{
    /**
     * @var \Assentamento
     */
    private $dadoAtual;
    /**
     * @var string
     */
    private $inscricaoEmpregador;
    /**
     * @var int
     */
    private $ano;

    /**
     * @var int
     */
    private $mes;

    /**
     * @param array $dados
     * @return array|\Assentamento[]
     * @throws \DBException
     */
    public function formatar($dados)
    {
        $this->ano = $dados->ano;
        $this->mes = $dados->mes;
        $this->inscricaoEmpregador = $dados->inscricao_empregador;
        $dadosFormatados = [];
        foreach ($dados->assentamentos as $dado) {
            $this->dadoAtual = $dado;
            $dadosFormatados[] = $this->montarDados();
        }
        return $dadosFormatados;
    }

    private function montarDados()
    {
        $registro = new stdClass();
        $registro->inscricao_empregador = $this->inscricaoEmpregador;
        $registro->referencia = $this->dadoAtual->getCodigo();
        $registro->ideVinculo = $this->montarVinculo();
        $registro->exMedOcup = $this->montarInformacoesExame();
        return $registro;
    }

    private function montarVinculo()
    {
        $servidor = \ServidorRepository::getInstanciaByCodigo($this->dadoAtual->getMatricula(), $this->ano, $this->mes);
        $vinculo = new stdClass();
        $vinculo->cpfTrab = $servidor->getCgm()->getCpf();
        if ($servidor->temVinculoEmpregaticio()) {
            $vinculo->matricula = $servidor->getMatricula();
        } else {
            $vinculo->codCateg = $servidor->getVinculo()->getCodigoCategoria();
        }
        if ($servidor->getMatriculaAnterior() != "") {
            $vinculo->matricula = $servidor->getMatriculaAnterior();
        }


        return $vinculo;
    }

    private function montarInformacoesExame()
    {
        $exMedOcup = new stdClass();
        $controleMedico = new ControleMedico($this->dadoAtual->getCodigo());
        if (!empty($controleMedico->getTipoExameOcupacional()) or $controleMedico->getTipoExameOcupacional() == 0) {
            $exMedOcup->tpExameOcup = (int)$controleMedico->getTipoExameOcupacional();
        }
        $exMedOcup->aso = $this->montarAso($controleMedico);
        $responsavel = $this->montarResponsavel($controleMedico);
        if (!empty($responsavel)) {
            $exMedOcup->respMonit = $responsavel;
        }
        return $exMedOcup;
    }

    private function montarAso(ControleMedico &$controleMedico)
    {
        $aso = new stdClass();
        if (!empty($controleMedico->getDataAtestado())) {
            $aso->dtAso = DBDate::format($controleMedico->getDataAtestado(), DBDate::DATA_EN);
        }
        if (!empty($controleMedico->getResultadoAtestado())) {
            $aso->resAso = (int) $controleMedico->getResultadoAtestado();
        }
        $aso->exame = $this->montarExames($controleMedico);
        $aso->medico = $this->montarMedico($controleMedico);
        return $aso;
    }

    private function montarExames(ControleMedico &$controleMedico)
    {
        $exames = [];
        foreach ($controleMedico->getExames() as $exame) {
            $ex = new stdClass();
            $ex->dtExm = DBDate::format($exame->getData(), DBDate::DATA_EN);
            $ex->procRealizado = $exame->getProcedimento();
            if (!empty($exame->getObservacao())) {
                $ex->obsProc = $exame->getObservacao();
            }
            if (!empty($exame->getOrdem())) {
                $ex->ordExame = (int)$exame->getOrdem();
            }
            if (!empty($exame->getResultado())) {
                $ex->indResult = (int)$exame->getResultado();
            }

            $exames[] = $ex;
        }
        return $exames;
    }

    private function montarMedico(ControleMedico &$controleMedico)
    {
        $medico = new stdClass();
        if (!empty($controleMedico->getNomeMedico())) {
            $medico->nmMed = $controleMedico->getNomeMedico();
        }
        if (!empty($controleMedico->getCrmMedico())) {
            $medico->nrCRM = $controleMedico->getCrmMedico();
        }
        if (!empty($controleMedico->getUfCrm())) {
            $medico->ufCRM = $controleMedico->getUfCrm();
        }
        return $medico;
    }

    private function montarResponsavel(ControleMedico &$controleMedico)
    {
        $responsavel = new stdClass();
        if (!empty($controleMedico->getCpfResponsavel())) {
            $responsavel->cpfResp = $controleMedico->getCpfResponsavel();
        }
        if (!empty($controleMedico->getNomeResponsavel())) {
            $responsavel->nmResp = $controleMedico->getNomeResponsavel();
        }
        if (!empty($controleMedico->getCrmResponsavel())) {
            $responsavel->nrCRM = $controleMedico->getCrmResponsavel();
        }
        if (!empty($controleMedico->getUfCrmResponsavel())) {
            $responsavel->ufCRM = $controleMedico->getUfCrmResponsavel();
        }
        return $responsavel;
    }
}
