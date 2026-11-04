<?php
/*
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

namespace App\Domain\Financeiro\Planejamento\Services;

use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;
use App\Domain\Financeiro\Orcamento\Models\Funcao;
use App\Domain\Financeiro\Orcamento\Models\NaturezaDespesa;
use App\Domain\Financeiro\Orcamento\Models\Orgao;
use App\Domain\Financeiro\Orcamento\Models\Programa;
use App\Domain\Financeiro\Orcamento\Models\ProjetoAtividade;
use App\Domain\Financeiro\Orcamento\Models\Subfuncao;
use App\Domain\Financeiro\Orcamento\Models\Unidade;
use App\Domain\Financeiro\Planejamento\Relatorios\ProjecaoDespesaAgrupadaPdf;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class RelatorioProjecaoDespesaAgrupadaService
 * Essa classe tem por objetivo processar os dados para impressão do: Demonstrativo das Projeções da Despesa
 * Toda a informação é processada, indexada e organizada no array $dados.
 * Assim o template, seja pdf ou outro só precisa receber esse array.
 *
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class RelatorioProjecaoDespesaAgrupadaService extends RelatorioProjecaoDespesaService
{
    public function __construct(array $filtros)
    {
        $this->processar($filtros);
    }

    public function emitirPdf()
    {
        $pdf = new ProjecaoDespesaAgrupadaPdf();
        $pdf->setDados($this->dados);
        return $pdf->emitir();
    }

    /**
     * @param array $filtros
     * @throws Exception
     */
    protected function processar(array $filtros)
    {
        parent::processar($filtros);
        $this->agrupar = $filtros['agrupar'];

        $this->fitroAgruparPlanejamento();
        $this->processaPlanejamento($filtros['instituicoes']);
        $this->processarDotacoes($filtros['instituicoes']);
        $this->organizaPlanejamento();
        $this->removeZerados();
    }

    /**
     * Busca as dotações e totaliza de acordo com o agrupador
     * @param array $idsInstituicoes
     * @throws Exception
     */
    private function processarDotacoes(array $idsInstituicoes)
    {
        $dotacoes = [];
        foreach ($this->exerciciosAnteriores as $exercicio) {
            $dotacoes[$exercicio] = $this->dotacoes($exercicio, $idsInstituicoes);
        }

        $this->totalizaDotacoesPorAgrupador($dotacoes);
    }

    /**
     * Busca as dotações conforme o exercício e as instituições
     * @param $exercicio
     * @param array $idsInstituicoes
     * @return array
     */
    public function dotacoes($exercicio, array $idsInstituicoes)
    {
        $dataInicio = "{$exercicio}-01-01";
        $dataFim = "{$exercicio}-12-31";
        $instituicoes = implode(', ', $idsInstituicoes);
        $sql = "
            with dotacoes as (
                select
                        o58_anousu,
                        o58_coddot,
                        o58_orgao as orgao,
                        o58_orgao||'-'||o58_unidade as unidade,
                        o58_subfuncao as subfuncao,
                        o58_projativ as iniciativa,
                        codigo_siconfi||'#'||o15_complemento as recurso,
                        o15_codigo||'#'||o15_complemento as recurso_original,
                        o58_funcao as funcao,
                        o58_programa as programa,
                        o56_elemento as elemento,
                        fc_dotacaosaldo(o58_anousu, o58_coddot, 2, '{$dataInicio}', '{$dataFim}') as dotacaosaldo
                from orcdotacao
                join orcelemento on (o56_codele, o56_anousu) = (o58_codele, o58_anousu)
                join orctiporec on o15_codigo = o58_codigo
                join fonterecurso on fonterecurso.orctiporec_id = o15_codigo
                     and fonterecurso.exercicio = {$this->planejamento->pl2_ano_inicial}
            where o58_anousu = {$exercicio}
              and o58_instit in ({$instituicoes})
            ), valores as (
                select
                    o58_anousu,
                    o58_coddot,
                    orgao,
                    unidade,
                    subfuncao,
                    iniciativa,
                    recurso,
                    funcao,
                    programa,
                    elemento,
                    substr(dotacaosaldo, 198, 12)::float8 as liquidado
                 from dotacoes
            ) select * from valores
        ";

        return DB::select($sql);
    }


    /**
     * Totaliza as dotações conforme o agrupador informado
     * @param array $dotacoesAnteriores
     * @throws Exception
     */
    private function totalizaDotacoesPorAgrupador(array $dotacoesAnteriores)
    {
        foreach ($dotacoesAnteriores as $exercicio => $dotacoes) {
            foreach ($dotacoes as $dotacao) {
                $dotacao = (array)$dotacao;
                if (!isset($dotacao[$this->agrupar])) {
                    continue;
                }
                $codigo = $dotacao[$this->agrupar];

                // se o dado dotação de exercício anteriores não existir no hoje só ignora ao
                if (!isset($this->dados['dados'][$codigo])) {
                    continue;
                }
                $this->dados['dados'][$codigo]->exercicioAnteriores[$exercicio] += $dotacao['liquidado'];
            }
        }
    }

    protected function buscarOrgaos()
    {
        $orgaos = [];
        Orgao::query()
            ->where('o40_anousu', '=', $this->planejamento->pl2_ano_inicial)
            ->get()
            ->map(function (Orgao $orgao) use (&$orgaos) {
                $objeto = $this->objetoBaseAgrupador($orgao->o40_orgao, $orgao->formataCodigo(), $orgao->o40_descr);
                $orgaos[$orgao->o40_orgao] = $objeto;
            });
        $this->dados['dados'] = $orgaos;
    }

    protected function buscarUnidades()
    {
        $unidades = [];
        Unidade::query()
            ->where('o41_anousu', '=', $this->planejamento->pl2_ano_inicial)
            ->orderBy('o41_orgao')
            ->orderBy('o41_unidade')
            ->get()
            ->each(function (Unidade $unidade) use (&$unidades) {
                $codigo = "{$unidade->o41_orgao}-$unidade->o41_unidade";
                $objeto = $this->objetoBaseAgrupador($codigo, $unidade->formataCodigoComOrgao(), $unidade->o41_descr);
                $unidades[$codigo] = $objeto;
            });
        $this->dados['dados'] = $unidades;
    }

    protected function buscarFuncoes()
    {
        $funcoes = [];
        Funcao::all()->each(function (Funcao $funcao) use (&$funcoes) {
            $objeto = $this->objetoBaseAgrupador($funcao->o52_funcao, $funcao->formataCodigo(), $funcao->o52_descr);
            $funcoes[$funcao->o52_funcao] = $objeto;
        });
        ksort($funcoes);
        $this->dados['dados'] = $funcoes;
    }

    protected function buscarSubfuncoes()
    {
        $subfuncoes = [];
        Subfuncao::all()->each(function (Subfuncao $subfuncao) use (&$subfuncoes) {
            $subfuncoes[$subfuncao->o53_subfuncao] = $this->objetoBaseAgrupador(
                $subfuncao->o53_subfuncao,
                $subfuncao->formataCodigo(),
                $subfuncao->o53_descr
            );
        });
        ksort($subfuncoes);
        $this->dados['dados'] = $subfuncoes;
    }

    protected function buscarProgramas()
    {
        $programas = [];
        Programa::query()
            ->where('o54_anousu', '=', $this->planejamento->pl2_ano_inicial)
            ->orderBy('o54_programa')
            ->each(function (Programa $programa) use (&$programas) {
                $programas[$programa->o54_programa] = $this->objetoBaseAgrupador(
                    $programa->o54_programa,
                    $programa->formataCodigo(),
                    $programa->o54_descr
                );
            });
        $this->dados['dados'] = $programas;
    }

    protected function buscarIniciativas()
    {
        $iniciativas = [];
        ProjetoAtividade::query()
            ->where('o55_anousu', '=', $this->planejamento->pl2_ano_inicial)
            ->orderBy('o55_projativ')
            ->each(function (ProjetoAtividade $projetoAtividade) use (&$iniciativas) {
                $iniciativas[$projetoAtividade->o55_projativ] = $this->objetoBaseAgrupador(
                    $projetoAtividade->o55_projativ,
                    $projetoAtividade->formataCodigo(),
                    $projetoAtividade->o55_descr
                );
            });
        $this->dados['dados'] = $iniciativas;
    }

    protected function buscarElementos()
    {
        $elementos = [];
        NaturezaDespesa::query()
            ->where('o56_anousu', '=', $this->planejamento->pl2_ano_inicial)
            ->orderBy('o56_elemento')
            ->each(function (NaturezaDespesa $naturezaDespesa) use (&$elementos) {
                $elementos[$naturezaDespesa->o56_elemento] = $this->objetoBaseAgrupador(
                    $naturezaDespesa->o56_elemento,
                    $naturezaDespesa->o56_elemento,
                    $naturezaDespesa->o56_descr
                );
            });

        $this->dados['dados'] = $elementos;
    }

    protected function buscarRecursos()
    {
        $recursos = [];
        $fontesRecursos = DB::select(sprintf("
        select distinct codigo_siconfi, gestao, o15_complemento
          from fonterecurso
          join orctiporec on o15_codigo = fonterecurso.orctiporec_id
         where exercicio = %s
           and codigo_siconfi != ''
         order by codigo_siconfi, gestao, o15_complemento;
        ", $this->planejamento->pl2_ano_inicial));

        foreach ($fontesRecursos as $dadoFR) {
            $fonteRecurso = FonteRecurso::where('codigo_siconfi', $dadoFR->codigo_siconfi)
                ->where('exercicio', $this->planejamento->pl2_ano_inicial)
                ->first();

            $fonte = "{$dadoFR->codigo_siconfi}#{$dadoFR->o15_complemento}";
            $recursos[$fonte] = $this->objetoBaseAgrupador(
                $fonte,
                $dadoFR->gestao,
                sprintf(
                    '%s - Complemento: %s',
                    $fonteRecurso->descricao,
                    str_pad($dadoFR->o15_complemento, 4, 0, STR_PAD_LEFT)
                )
            );
        }

        $this->dados['dados'] = $recursos;
    }


    /**
     * Cria um objeto base para o agrupador
     * @param $codigo
     * @param $formatado
     * @param $descricao
     * @return object
     */
    private function objetoBaseAgrupador($codigo, $formatado, $descricao)
    {
        return (object)[
            'codigo' => $codigo,
            'formatado' => $formatado,
            'descricao' => $descricao,
            'valorBase' => 0,
            'exercicioAnteriores' => $this->criaArrayValores($this->exerciciosAnteriores),
            'exerciciosPlanejamento' => $this->criaArrayValores($this->planejamento->execiciosPlanejamento()),
        ];
    }


    /**
     * Busca o detalhamento do Panejamento e totaliza de acordo com o agrupador selecionado;
     * @param array $idsInstituicoes
     */
    protected function processaPlanejamento(array $idsInstituicoes)
    {
        $this->dados['totalizador'] = (object)[
            'valorBase' => 0,
            'exercicios' => $this->criaArrayValores($this->execiciosPlanejamento),
        ];

        $dadosDetalhamentos = $this->buscaDetalhamentoPlanejamento($idsInstituicoes);
        foreach ($dadosDetalhamentos as $detalhamento) {
            $detalhamento = (array)$detalhamento;
            if (!isset($detalhamento[$this->agrupar])) {
                continue;
            }
            $codigo = $detalhamento[$this->agrupar];
            $this->dados['dados'][$codigo]->valorBase += $detalhamento['valor_base'];
            $this->dados['totalizador']->valorBase += $detalhamento['valor_base'];
            if (!empty($detalhamento['valores'])) {
                $valores = json_decode($detalhamento['valores']);
                foreach ($valores as $valor) {
                    $this->dados['dados'][$codigo]->exerciciosPlanejamento[$valor->ano] += $valor->valor;
                    $this->dados['totalizador']->exercicios[$valor->ano] += $valor->valor;
                }
            }
        }
    }

    /**
     * Busca o detalhamento do planejamento
     * @param array $idsInstituicoes
     * @return array
     */
    protected function buscaDetalhamentoPlanejamento(array $idsInstituicoes)
    {
        $instituicoes = implode(', ', $idsInstituicoes);
        $sql = "
            select  pl20_orcorgao as orgao
                 ,pl20_orcorgao||'-'||pl20_orcunidade as unidade
                 ,pl20_orcfuncao as funcao
                 ,pl20_orcsubfuncao as subfuncao
                 ,pl9_orcprograma as programa
                 ,pl12_orcprojativ as iniciativa
                 ,o56_elemento as elemento
                 ,codigo_siconfi||'#'||o15_complemento as recurso
                 ,codigo_siconfi||'#'||o15_recurso||'#'||o15_complemento as recurso_original
                 ,pl20_valorbase as valor_base
                 ,(select json_agg(
                                  json_build_object(
                                          'ano', x.pl10_ano,
                                          'valor', x.pl10_valor
                                      )
                              ) as valores
                   from (select valores.pl10_ano, valores.pl10_valor
                         from planejamento.valores
                         where pl10_origem = 'DETALHAMENTO INICIATIVA'
                           and pl10_chave = detalhamentoiniciativa.pl20_codigo
                         order by pl10_ano
                        ) as x
            ) as valores
            from planejamento.planejamento
            join planejamento.programaestrategico on pl9_planejamento = planejamento.pl2_codigo
            join planejamento.iniciativaprojativ
                 on iniciativaprojativ.pl12_programaestrategico = programaestrategico.pl9_codigo
            join planejamento.detalhamentoiniciativa
                 on detalhamentoiniciativa.pl20_iniciativaprojativ = iniciativaprojativ.pl12_codigo
            join orcelemento on (o56_codele, o56_anousu) = (pl20_orcelemento, pl20_anoorcamento)
            join orctiporec on o15_codigo = pl20_recurso
            join fonterecurso on fonterecurso.orctiporec_id = o15_codigo
                 and fonterecurso.exercicio = planejamento.pl2_ano_inicial
           where pl2_codigo = {$this->planejamento->pl2_codigo}
             and pl20_instituicao in ({$instituicoes})
        ";

        return DB::select($sql);
    }

    /**
     * Remove os registros onde não haver valor no planejamento
     */
    protected function removeZerados()
    {
        foreach ($this->dados['dados'] as $index => $dado) {
            $x = array_filter($dado->exerciciosPlanejamento, function ($v) {
                return !empty($v);
            });
            if (empty($dado->valorBase) && empty($x)) {
                unset($this->dados['dados'][$index]);
            }
        }
    }
}
