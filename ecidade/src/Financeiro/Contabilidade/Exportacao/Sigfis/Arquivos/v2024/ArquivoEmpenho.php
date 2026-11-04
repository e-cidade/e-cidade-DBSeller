<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use BusinessException;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

class ArquivoEmpenho extends ArquivoBase
{
    protected $sNomeArquivo  = 'Empenho';

    public function gerarDados()
    {
        $empenhos = $this->getEmpenhos();

        if ($empenhos->isEmpty()) {
            throw new BusinessException('Não há empenhos para a competência informada.');
        }

        $RemessaEmpenho = new stdClass();
        $RemessaEmpenho->Empenhos = [];

        foreach ($empenhos as $item) {
            // validacoes
            if (empty($item->fonte)) {
                $this->addLog("[Empenho {$item->e60_numemp}]: Recurso sem vinculo com Fonte Recurso padrão STN");
                continue;
            }

            if (empty($item->natureza)) {
                $this->addLog("[Empenho {$item->e60_numemp}]: ND sem vinculo com Plano de Contas Governo");
                continue;
            }

            $Empenho = new stdClass();
            $Empenho->Identificador = $item->e60_numemp;
            $Empenho->CodigoUnidadeGestora = $this->sCodigoTribunal;
            $Empenho->CpfOrdenador = $this->unidadeGestora->ordenadorDespesa->z01_cgccpf;
            $Empenho->Competencia = $this->competencia;
            $Empenho->NumeroEmpenho = $item->e60_codemp;
            $Empenho->AnoEmpenho = $item->e60_anousu;
            $Empenho->Data = $item->e60_emiss;
            $Empenho->Tipo = $item->e60_codtipo;
            $Empenho->CodigoFuncao = $item->o52_siconfi;
            $Empenho->CodigoSubFuncao = $item->o53_siconfi;
            $Empenho->CodigoAcao = $item->o55_projativ;
            $Empenho->TipoAcao = $item->o55_tipo;
            $Empenho->CodigoPrograma = $item->o54_programa;
            $Empenho->NaturezaDespesa = substr($item->natureza, 0, 6);
            $Empenho->FonteRecurso = $item->fonte;
            $Empenho->ValorEmpenho = $item->e60_vlremp;

            $Empenho->Historico = (!empty($item->e60_re40_descr)
                ? Helper::convertAndLimit("<![CDATA[".$item->e60_re40_descr, 264)
                : 'PADRAO');

            $Empenho->NomeCredor = Helper::convertAndLimit("<![CDATA[".$item->z01_nome, 264);
            $Empenho->CnpjCpfNifCredor = $item->z01_cgccpf;
            $Empenho->TipoPessoa = ($item->tipo_pessoa == 14) ? 1 : 2;
            $Empenho->CodigoOrgao = $item->o58_orgao;
            $Empenho->CodigoUnidadeOrcamentaria = $item->o58_unidade;

            // CodigoAcompanhamentoExecucaoOrcamentaria
            $Empenho->CodigoAcompanhamentoExecucaoOrcamentaria = $item->o15_complemento;
            if ($item->o15_complemento == 0 || empty($item->o15_complemento)) {
                $Empenho->CodigoAcompanhamentoExecucaoOrcamentaria = '0000';
            }

            // CodigoJustificativaAusenciaAtoJuridico
            $Empenho->CodigoJustificativaAusenciaAtoJuridico = $item->ausenciaatojuridico;
            if (empty($item->ausenciaatojuridico)) {
                $Empenho->CodigoJustificativaAusenciaAtoJuridico = null;
            }

            // CodigoJustificativaAusenciaInstrumentoPrevio
            $Empenho->CodigoJustificativaAusenciaInstrumentoPrevio = $item->ausenciainstrumentoprevio;
            if (empty($item->ausenciainstrumentoprevio)) {
                $Empenho->CodigoJustificativaAusenciaInstrumentoPrevio = null;
            }

            $Empenho->SubElementos = [];
            $EmpenhoSubElemento = new stdClass();
            $EmpenhoSubElemento->Identificador = $item->planodespesa;
            $EmpenhoSubElemento->Valor = $item->e60_vlremp;
            $EmpenhoSubElemento->NaturezaDespesa = $item->natureza;

            $Empenho->SubElementos[] = (object) ['EmpenhoSubElemento' => $EmpenhoSubElemento];
            $RemessaEmpenho->Empenhos[] = (object) ['Empenho' => $Empenho];
        }

        $this->aDados = $RemessaEmpenho;
    }

    private function getEmpenhos()
    {
        $query = DB::table('empempenho')
            ->select([
                'empempenho.e60_emiss',
                'empempenho.e60_codemp',
                'empempenho.e60_numemp',
                'empempenho.e60_anousu',
                'orcfuncao.o52_siconfi',
                'orcsubfuncao.o53_siconfi',
                'orcprojativ.o55_projativ',
                'orcprojativ.o55_tipo',
                'orcprograma.o54_programa',
                'orctiporec.o15_complemento',
                'empempenho.e60_vlremp',
                'emphist.e40_descr',
                'cgm.z01_nome',
                'planodespesa.id AS planodespesa',
                'cgm.z01_cgccpf',
                'orcdotacao.o58_orgao',
                'orcdotacao.o58_unidade',
                'e173_sequencial as ausenciaatojuridico',
                'e174_sequencial as ausenciainstrumentoprevio',
                'e171_dados'
            ])
            ->selectRaw("
                CASE
                    WHEN empempenho.e60_codtipo = 1 THEN 3
                    WHEN empempenho.e60_codtipo = 2 THEN 2
                    WHEN empempenho.e60_codtipo = 3 THEN 3
                    WHEN empempenho.e60_codtipo = 4 THEN 1
                END AS e60_codtipo
            ")
            ->selectRaw("char_length(cgm.z01_cgccpf) as tipo_pessoa")
            ->selectRaw("substring(planodespesa.conta, 1, 8) as natureza")
            ->selectRaw("substring(fonterecurso.codigo_siconfi, 1, 4) as fonte")

            // empenho
            ->leftJoin('empemphist', 'empemphist.e63_numemp', 'empempenho.e60_numemp')
            ->leftJoin('emphist', 'emphist.e40_codhist', 'empemphist.e63_codhist')
            ->join('cgm', 'cgm.z01_numcgm', 'empempenho.e60_numcgm')

            // orcamento
            ->join('orcdotacao', function ($join) {
                $join->on('orcdotacao.o58_coddot', 'empempenho.e60_coddot');
                $join->on('orcdotacao.o58_anousu', 'empempenho.e60_anousu');
            })
            ->join('orcprojativ', function ($join) {
                $join->on('orcprojativ.o55_projativ', 'orcdotacao.o58_projativ');
                $join->on('orcprojativ.o55_anousu', 'orcdotacao.o58_anousu');
            })
            ->join('orcprograma', function ($join) {
                $join->on('orcprograma.o54_programa', 'orcdotacao.o58_programa');
                $join->on('orcprograma.o54_anousu', 'orcdotacao.o58_anousu');
            })
            ->join('orctiporec', 'orctiporec.o15_codigo', 'orcdotacao.o58_codigo')
            ->join('orcfuncao', 'o52_funcao', 'o58_funcao')
            ->join('orcsubfuncao', 'o53_subfuncao', 'o58_subfuncao')
            ->leftJoin('fonterecurso', function ($join) {
                $join->on('fonterecurso.orctiporec_id', 'orctiporec.o15_codigo');
                $join->on('fonterecurso.exercicio', 'orcdotacao.o58_anousu');
            })

            // elemento
            ->join('empelemento', 'empelemento.e64_numemp', 'empempenho.e60_numemp')
            ->leftJoin('conplanoorcamento', function ($join) {
                $join->on('conplanoorcamento.c60_codcon', 'empelemento.e64_codele');
                $join->on('conplanoorcamento.c60_anousu', 'empempenho.e60_anousu');
            })
            ->leftJoin(
                'planodespesaconplanoorcamento AS plandesp',
                'plandesp.conplanoorcamento_codigo',
                'conplanoorcamento.c60_codigo'
            )
            ->leftJoin('planodespesa', function ($join) {
                $join->on('planodespesa.id', 'plandesp.planodespesa_id')->where('uniao', $this->getUtilizaPlanoUniao());
            })

            // Ato juridico / Instrumento Previo
            ->leftJoin(
                'empjustificativaatojuridico',
                'empjustificativaatojuridico.e175_empempenho',
                'empempenho.e60_numemp'
            )
            ->leftJoin(
                'empjustificativainstrumentoprevio',
                'empjustificativainstrumentoprevio.e176_empempenho',
                'empempenho.e60_numemp'
            )
            ->leftJoin(
                'justificativaatojuridico',
                'justificativaatojuridico.e173_sequencial',
                'empjustificativaatojuridico.e175_justificativaatojuridico'
            )
            ->leftJoin(
                'justificativainstrumentoprevio',
                'justificativainstrumentoprevio.e174_sequencial',
                'empjustificativainstrumentoprevio.e176_justificativainstrumentoprevio'
            )
            ->leftJoin(
                'empempenhooutrosdados',
                'empempenhooutrosdados.e171_numemp',
                'empempenho.e60_numemp'
            )
            ->whereBetween('empempenho.e60_emiss', [$this->dtDataInicial, $this->dtDataFinal])
            ->whereIn('empempenho.e60_instit', $this->getListaInstituicoes())
            ->get();

        return $query;
    }
}
