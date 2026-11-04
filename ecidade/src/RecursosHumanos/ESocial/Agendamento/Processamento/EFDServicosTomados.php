<?php
/**
 * Created by PhpStorm.
 * User: dbseller
 * Date: 11/12/18
 * Time: 16:21
 */

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use App\Domain\Integracoes\EFDReinf\Services\ConfiguracaoService;
use CgmRepository;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\DadosESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use Exception;
use stdClass;

class EFDServicosTomados extends ProcessamentoAbstract implements ProcessamentoInterface
{
    private $cgm;

    private $instituicao;

    private $ano;

    private $mes;

    private $dados;

    private $config;

    public function __construct($cgm, $instituicao = null, $ano = null, $mes = null)
    {
        $this->cgm = CgmRepository::getByCodigo($cgm);
        $this->instituicao = $instituicao;
        $this->ano = $ano;
        $this->mes = $mes;
        $this->config = ConfiguracaoService::getInstance($instituicao);
    }

    public function processar()
    {
        $cgm = $this->cgm->getCodigo();
        $instituicao = $this->instituicao;
        $ano = $this->ano;
        $mes = $this->mes;
        $alteracaoDados = false;
        $validaMd5 = true;

        if ($this->envioForcado) {
            $validaMd5 = false;
        }

        $daoRetencaoReceitasAdicionais = new \cl_retencaoreceitasadicionais();
        $where = array(
            "retencaoreceitas.e23_ativo is true",
            "e60_instit = {$instituicao}",
            "LENGTH(TRIM(cgmprestador.z01_cgccpf)) = 14",
            "e69_numero is not null",
            "emptiposervicoobra.e154_tipo is not null",
            "retencaotiporec.e21_retencaotipocalc = 4"
        );
        if (!empty($ano)) {
            $where[] = "EXTRACT(YEAR FROM e69_dtnota) = {$ano}";
        }
        if (!empty($mes)) {
            $where[] = "EXTRACT(MONTH FROM e69_dtnota) = {$mes}";
        }

        // Filtro orgao unidade ativo
        if ($this->config->filtraOrgaoUnidade()) {
            $unidadeCnpj = $this->cgm->getCnpj();
            $unidadeCnpjBase = substr($unidadeCnpj, 0, 8);
            $where[] = "substr(o41_cnpj, 1, 8) = '{$unidadeCnpjBase}'";
        }

        $sqlNotas = $daoRetencaoReceitasAdicionais->sql_query_notascgmretencaoadicionais($where);
        $rsNotas = \db_query($sqlNotas);

        $this->tratamentoPreProcessamento(\db_utils::getCollectionByRecord($rsNotas));

        foreach ($this->dados as &$dados) {
            $dadosEsocial = new DadosESocial();
            $dadosEsocial->setIntegracao(Tipo::EFD_REINF);
            $dadosEsocial->setReponsavelPeloPreenchimento($dados->ideEstabObra->idePrestServ->cgmPrestador);
            unset($dados->ideEstabObra->idePrestServ->cgmPrestador);
            $dadosEsocial->setAno($ano);
            $dadosEsocial->setMes($mes);
            $dadosPreenchimento = $dadosEsocial->getPorTipo(Tipo::EFD_RETENCOES_SERVICOS_TOMADOS);
            if (count($dadosPreenchimento) > 0) {
                $formatterContribuinte = FormatterFactory::get(Tipo::R2010);
                $dadosFormatadosContribuinte = $formatterContribuinte->formatar($dadosPreenchimento);

                foreach ($dadosFormatadosContribuinte as $index => $formatado) {
                    $dados->ideEstabObra->idePrestServ->{$index} = $formatado;
                }
            }
            unset($dados);
        }

        foreach ($this->dados as $dados) {
            $eventoFila = new Evento(Tipo::R2010, $cgm, $dados->referencia, $dados);

            if ($eventoFila->adicionarFila(false, $validaMd5)) {
                $alteracaoDados = true;
            }
        }

        return $alteracaoDados;
    }

    public function tratamentoPreProcessamento($registroNotas)
    {
        $this->dados = array();
        $dados = new stdClass();
        foreach ($registroNotas as $registroNota) {

            /**
             * Contribuinte
             */
            $cnpjContribuinte = ($this->config->filtraOrgaoUnidade())
                ? $registroNota->cnpj_unidade
                : $registroNota->cnpj_contribuinte;

            if (!$cnpjContribuinte) {
                throw new Exception("
                    Nota {$registroNota->numero_nota},
                    não possui CNPJ do contribuinte
                ");
            }

            if (empty($dados->{$cnpjContribuinte})) {
                $dados->{$cnpjContribuinte} = new stdClass();
            }

            $contribuinte = &$dados->{$cnpjContribuinte};

            /**
             * Prestador
             */
            $registroNota->identificador_prestador = preg_replace('/\D/', '', $registroNota->identificador_prestador);
            if (empty($contribuinte->{$registroNota->identificador_prestador})) {
                $contribuinte->{$registroNota->identificador_prestador} = new stdClass();

                $identificador = &$contribuinte->{$registroNota->identificador_prestador};

                $identificador->notas = new stdClass();
                $identificador->cnpjPrestador = $registroNota->cnpj_prestador;
                $identificador->indCPRB = ($registroNota->indicativo_cprb === 't' ) ? 1 : 0;
                $identificador->cnpjContribuinte = $cnpjContribuinte;
                $identificador->cgmPrestador = $registroNota->cgm_prestador;
                $identificador->indObra = $registroNota->indicativo_obra_tipo;
                $identificador->cno = $registroNota->indicativo_obra_cno;
            }

            $identificador = &$contribuinte->{$registroNota->identificador_prestador};

            /**
             * Notas
             */
            $registroNota->numero_nota = str_replace(['/', '.', '-'], '_', trim($registroNota->numero_nota));
            if (empty($identificador->notas->{$registroNota->numero_nota})) {
                $identificador->notas->{$registroNota->numero_nota} = new stdClass;

                $nota = &$identificador->notas->{$registroNota->numero_nota};

                $nota->serie = (empty($registroNota->serie_nota)) ? "0" : $registroNota->serie_nota;
                $nota->numDocto = $registroNota->numero_nota;
                $nota->dtEmissaoNF = $registroNota->data_emissao;
                $nota->vlrBruto = floatval($registroNota->valor_bruto) + floatval($registroNota->notas_nao_retidas);
                $nota->infoTpServ = array();
            }

            $nota = &$identificador->notas->{$registroNota->numero_nota};

            /**
             * Tipo de servico nota
             */
            if (!array_key_exists($registroNota->referencia_tipo_servico, $nota->infoTpServ)) {
                $tipoServico = new stdClass();
                $tipoServico->tpServico = $registroNota->referencia_tipo_servico;
                $tipoServico->vlrBaseRet    = 0;
                $tipoServico->vlrRetencao   = 0;
                $tipoServico->vlrNRetPrinc  = 0;
                $tipoServico->vlrServicos15 = 0;
                $tipoServico->vlrServicos20 = 0;
                $tipoServico->vlrServicos25 = 0;
                $tipoServico->vlrAdicional  = 0;
                $tipoServico->vlrNRetAdic   = 0;
            } else {
                $tipoServico = $nota->infoTpServ[$registroNota->referencia_tipo_servico];
            }

            $valorBaseRetencao = ($registroNota->indicativo_valor_base == "t")
                ? $registroNota->valor_base_retido
                : floatval($registroNota->valor_bruto) + floatval($registroNota->notas_nao_retidas);

            $tipoServico->vlrBaseRet += $valorBaseRetencao;

            // Truncar valor apenas quando for somatorio de notas
            if (intval($registroNota->qnt_nota) > 1) {
                $tipoServico->vlrRetencao += $this->getValorTruncadoRetencao(
                    $registroNota->valor_retencao,
                    $valorBaseRetencao,
                    $registroNota->aliquota
                );
            } else {
                $tipoServico->vlrRetencao += $registroNota->valor_retencao;
            }

            $tipoServico->vlrNRetPrinc += !empty($registroNota->valor_nao_retido_principal)
                ? ((float) $registroNota->valor_nao_retido_principal)
                : 0;

            $tipoServico->vlrServicos15 += (float) $registroNota->valor_servicos_15;
            $tipoServico->vlrServicos20 += (float) $registroNota->valor_servicos_20;
            $tipoServico->vlrServicos25 += (float) $registroNota->valor_servicos_25;

            $tipoServico->vlrAdicional  += $tipoServico->vlrServicos15 * 0.04;
            $tipoServico->vlrAdicional  += ($tipoServico->vlrServicos20 * 0.03) + ($tipoServico->vlrServicos25 * 0.02);

            $tipoServico->vlrNRetAdic += !empty($registroNota->valor_nao_retido_adicional)
                ? ((float) $registroNota->valor_nao_retido_adicional)
                : 0;

            $nota->infoTpServ[$registroNota->referencia_tipo_servico] = $tipoServico;

            unset($tipoServico);
            unset($nota);
            unset($identificador);
            unset($contribuinte);
        }

        /**
         * Totalizadores
         */
        foreach ($dados as &$contribuinte) {
            foreach ($contribuinte as &$dado) {
                $vlrTotalBruto = 0;
                $vlrTotalBaseRet = 0;
                $vlrTotalRetPrinc = 0;
                $vlrTotalRetAdic = 0;
                $vlrTotalNRetPrinc = 0;
                $vlrTotalNRetAdic = 0;
                $dado->nfs = array();
                foreach ($dado->notas as $nota) {
                    $vlrTotalBruto += $nota->vlrBruto;

                    // remove identificador no array do tipo servico
                    $nota->infoTpServ = array_values($nota->infoTpServ);

                    foreach ($nota->infoTpServ as $tipoServico) {
                        $vlrTotalBaseRet   += $tipoServico->vlrBaseRet;
                        $vlrTotalRetPrinc  += $tipoServico->vlrRetencao;
                        $vlrTotalRetAdic   += $tipoServico->vlrAdicional;
                        $vlrTotalNRetPrinc +=$tipoServico->vlrNRetPrinc;
                        $vlrTotalNRetAdic  += $tipoServico->vlrNRetAdic;
                    }

                    $dado->nfs[] = $nota;
                }
                unset($dado->notas);

                $dado->vlrTotalBruto    = $vlrTotalBruto;
                $dado->vlrTotalBaseRet  = $vlrTotalBaseRet;
                $dado->vlrTotalRetPrinc = $vlrTotalRetPrinc;
                $dado->vlrTotalRetAdic  = $vlrTotalRetAdic;
                if (!empty($vlrTotalNRetPrinc)) {
                    $dado->vlrTotalNRetPrinc = $vlrTotalNRetPrinc;
                }
                if (!empty($vlrTotalNRetAdic)) {
                    $dado->vlrTotalNRetAdic = $vlrTotalNRetAdic;
                }
            }
        }

        /**
         * Identificadores do evento
         */
        foreach ($dados as &$contribuinte) {
            foreach ($contribuinte as &$dado) {
                $dadosPrestador = clone $dado;
                unset($dadosPrestador->cnpjContribuinte);
                unset($dadosPrestador->indObra);
                unset($dadosPrestador->cno);

                $ideEstabObra = new stdClass();
                $ideEstabObra->indObra = (empty($dado->indObra)) ? 0 : (int) $dado->indObra;
                $ideEstabObra->tpInscEstab = ($ideEstabObra->indObra == 0) ? "1" : "4";
                $ideEstabObra->nrInscEstab = ($ideEstabObra->indObra == 0) ? $dado->cnpjContribuinte : $dado->cno;
                $ideEstabObra->cnpjContribuinte = $dado->cnpjContribuinte;
                $ideEstabObra->idePrestServ = $dadosPrestador;

                unset($dado->cno);

                $registro = new stdClass();
                $registro->ideEstabObra = $ideEstabObra;
                $this->dados[] = $registro;
            }
        }

        foreach ($this->dados as &$dados) {
            $dados->inscricao_contribuinte = $this->cgm->getCnpj();
            $dados->referencia  = "{$this->ano}-{$this->mes} ";
            $dados->referencia .= "{$dados->ideEstabObra->idePrestServ->cgmPrestador}-";
            $dados->referencia .= "{$dados->ideEstabObra->cnpjContribuinte}";
            if ($dados->ideEstabObra->indObra > 0) {
                $dados->referencia .= " {$dados->ideEstabObra->nrInscEstab}";
            }

            $dados->perApur = "{$this->ano}-{$this->mes}";
            unset($dados->ideEstabObra->cnpjContribuinte);
        }
    }

    private function getValorTruncadoRetencao($valorRetido, $valorBase, $aliquota)
    {
        $valorTruncado = bcdiv(($valorBase * ($aliquota / 100)), 1, 2);
        if (bcsub($valorRetido, $valorTruncado, 2) == 0.01) {
            return $valorTruncado;
        }
        return $valorRetido;
    }
}
