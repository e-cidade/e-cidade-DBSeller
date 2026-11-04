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
 *  junto com este programa; se nao, escreva para a Free Softwareb
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use CgmRepository;
use DBCompetencia;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Repository\RemuneracaoRGPS as RemuneracaoRGPSRepository;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use stdClass;
use ParameterException;
use ParametrosPessoalRepository;
use InstituicaoRepository;
use ServidorRepository;
use Exception;

/**
 * Class RemuneracaoRGPS
 * @package ECidade\RecursosHumanos\ESocial\Agendamento\Processamento
 */
class RemuneracaoRGPS extends ProcessamentoAbstract implements ProcessamentoInterface
{
    private $cgm;

    private $mes;
    private $ano;

    /**
     * @param integer $mes
     * Seta o mes da competencia informada
     */
    public function setMes($mes)
    {
        if (!empty($mes)) {
            $this->mes = $mes;
        }
    }

    /**
     * @param integer $ano
     */
    public function setAno($ano)
    {
        if (!empty($ano)) {
            $this->ano = $ano;
        }
    }

    public function __construct($cgm)
    {
        $this->cgm = $cgm;
    }

    /**
     * @return bool|mixed
     * @throws \BusinessException
     * @throws \DBException
     * @throws \ParameterException
     * @throws \Exception
     */
    public function processar()
    {
        $alteracao = false;

        if ($this->getIndicativoPeriodoApuracao() === null) {
            throw new ParameterException("Indicativo de Período de Apuração não informado.");
        }

        if ($this->getIndicativoPeriodoApuracao() === '1' && empty($this->ano) && empty($this->mes)) {
            throw new ParameterException("Ano e mês do período de apuração não informados.");
        }

        if ($this->getIndicativoPeriodoApuracao() === '2' && empty($this->ano) && empty($this->mes)) {
            throw new ParameterException("Ano e mês do período de apuração não informados.");
        }

        if ($this->isForcarMatricula() && sizeof($this->servidores) == 0) {
            throw new ParameterException("Nenhuma matrícula informada. Por Favor selecione as matrículas.");
        }

        if (!empty($this->selecao)) {
            try {
                $instituicao = InstituicaoRepository::getInstituicaoByCodigo(db_getsession("DB_instit"));

                $this->servidores = ServidorRepository::getServidoresBySelecao(
                    $this->ano,
                    $this->mes,
                    $this->selecao,
                    $instituicao->getCodigo()
                );
                $this->servidores = array_values($this->servidores);
            } catch (Exception $e) {
                throw new \DBException("Ocorrêu um erro ao buscar as informações da seleção informada.");
            }
        }

        $dados = new stdClass();
        $dados->indApuracao = (int) $this->getIndicativoPeriodoApuracao();
        $dados->perApur = $this->ano;

        if ($dados->indApuracao === 1 && !empty($this->mes)) {
            $dados->perApur .= "-{$this->mes}";
        }

        $competencia =  new DBCompetencia($this->ano, $this->mes);
        $dados->cgms = RemuneracaoRGPSRepository::buscarTodosCGMCompetencia($competencia, $this->servidores);
        $dados->rubricaDiferenca = RemuneracaoRGPSRepository::buscarRubricaDiferencaReajusteCompetencia($competencia);
        $dados->inscricao_empregador = RemuneracaoRGPSRepository::buscarCNPJEmpregador($this->cgm);
        $dados->anoCompetencia = $this->ano;
        $dados->mesCompetencia = $this->mes;

        $parametros = ParametrosPessoalRepository::getParametros($competencia);


        $formatter = FormatterFactory::get(Tipo::S1200);
        $formatter->setEmpregador(CgmRepository::getByCodigo($this->cgm));
        // Verifica se a competencia é a de pagamento do decimo terceiro
        if (!empty($parametros->getMes13()) && $parametros->getMes13() == $this->mes) {
            $formatter->setUltimaParcelaDecimoTerceiro();
        }
        if ($this->getIndicativoPeriodoApuracao() === '2') {
            $formatter->setDecimoTerceiro();
        }

        if ($this->isForcarMatricula()) {
            $formatter->setServidores($this->servidores);
        }

        $dadosPreenchimentoEmpregador = $formatter->formatar((array) $dados);
        $naoEnviados = $formatter->getCgmsNaoEnviados();
        if (!empty($naoEnviados)) {
            $this->setInconsitencias($naoEnviados);
        }
        $validaMd5 = true;
        if ($this->envioForcado) {
            $validaMd5 = false;
        }
        foreach ($dadosPreenchimentoEmpregador as $indice => $dados) {
            $evento = new Evento(TIPO::S1200, $this->cgm, $dados->referencia, $dados);
            $evento->iContador = $indice;

            if ($evento->adicionarFila(false, $validaMd5)) {
                $alteracao = true;
            }
        }
        return $alteracao;
    }
}
