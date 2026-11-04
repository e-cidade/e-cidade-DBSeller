<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Services\BalanceteDespesaService;
use Carbon\Carbon;
use ECidade\Library\SpreadSheet\Template\Parser;
use Exception;
use Illuminate\Support\Facades\DB;

class SuperavitFinanceiroPorRecursoService
{
    /**
     * @var array
     */
    protected $filtros = [];

    /**
     * Recursos a ser impressos
     * @var Illuminate\Support\Collection
     */
    protected $recursos;

    protected $template;
    /**
     * @var Parser
     */
    protected $parser;

    public function __construct()
    {
        $template = sprintf('%s/financeiro/superavit_financeiro_por_recurso.xlsx', storage_path());

        if (!file_exists($template)) {
            $str = 'Não foi encontrado o template para impressão do relatório. Contate o Suporte';
            throw new Exception($str, 403);
        }
        $parser = new Parser();
        $parser->findSectionsautomatically(false);
        $parser->loadXLS($template);
        $parser->setOrientation('landscape');

        $this->parser = $parser;
    }

    public function setFiltros(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function emitir()
    {
        $this->processar();
        $periodo = sprintf(
            'de %s a %s',
            Carbon::createFromFormat('Y-m-d', $this->filtros['dataInicial'])->format('d/m/Y'),
            Carbon::createFromFormat('Y-m-d', $this->filtros['dataFinal'])->format('d/m/Y')
        );

        $this->parser->addVariable('data_hora_emissao', (new Carbon())->format('d/m/Y H:i:s'));
        $this->parser->addVariable('periodo_selecionado', $periodo);
        $this->parser->addCollection('dados', $this->recursos->toArraY());

        $filename = sprintf('tmp/superavit_financeiro_por_recurso_%s.xlsx', time());
        $this->parser->parse();
        $this->parser->save($filename);

        return [
            'xls' => $filename,
            'xlsLinkExterno' => url($filename)
        ];
    }

    protected function processar()
    {
        $this->recursos = collect([]);
        $this->buscarRecursos();
        $this->calcularAtivoFinanceiro();
        $this->calcularPassivo();
        $this->calcular6221301();
        $this->calcular6311();
        $this->calcular63171();
        $this->calcular8211();

        $balanceteDespesa = $this->executarBalanceteDespesa();
        $this->calcularPassivoLiquidacao($balanceteDespesa);
        $this->calcularValorLiquidar($balanceteDespesa);

        $rps = $this->executarRPs();
        $this->calcularValorLiquidacaoRP($rps);
        $this->calcularValorLiquidarRP($rps);
        $this->totalizaLinhas();
        $this->filtraApenasLinhasComValores();
    }

    private function buscarRecursos()
    {
        $campos[] = "codigo_siconfi as siconfi";
        $groupBy = "1";

        if ($this->filtros['apresentarRecurso'] === 2) {
            $campos[] = "o15_complemento as complemento";
            $groupBy .= ",2";
        }

        if ($this->filtros['apresentarRecurso'] === 3) {
            $campos[] = "o15_recurso as subrecurso, o15_complemento as complemento";
            $groupBy .= ",2,3";
        }
        $campos[] = "array_to_string(array_agg(o15_codigo), ',') as codigos";
        $campos = implode(', ', $campos);

        $where = ["exercicio = {$this->filtros['DB_anousu']}"];
        if (!empty($this->filtros['recursos'])) {
            $where[] = sprintf('orctiporec_id in (%s)', implode(',', $this->filtros['recursos']));
        }

        $where = implode(' and ', $where);
        $sql = "
            select {$campos}
              from orcamento.fonterecurso
              join orcamento.orctiporec on o15_codigo = orctiporec_id
            where {$where}
            group by {$groupBy}
            order by {$groupBy}
        ";

        $this->buildRecursos(DB::select($sql));
    }

    private function buildRecursos(array $dados)
    {
        foreach ($dados as $dado) {
            $fonte = $dado->siconfi;
            if ($this->filtros['apresentarRecurso'] === 2) {
                $fonte = sprintf('%s - %s', $dado->siconfi, $dado->complemento);
            }
            if ($this->filtros['apresentarRecurso'] === 3) {
                $fonte = sprintf('%s - %s - %s', $dado->siconfi, $dado->subrecurso, $dado->complemento);
            }
            $dado->idsRecursos = explode(',', $dado->codigos);
            $dado->fonte_recurso = $fonte;
            $dado->ativo_financeiro = 0;
            $dado->passivo_extra = 0;
            $dado->passivo_liquidacao = 0;
            $dado->vlr_paga_liquidado = 0;
            $dado->vlr_liquidacao_rp = 0;
            $dado->soma_liquidacao = 0;
            $dado->passivo_6221301 = 0;
            $dado->vlr_liquidar = 0;
            $dado->passivo_6311 = 0;
            $dado->passivo_63171 = 0;
            $dado->soma_i_j = 0;
            $dado->vlr_liquidar_rp = 0;
            $dado->superavit_contabil = 0;
            $dado->superavit_execucao_orcamentaria = 0;
            $dado->vlr_8211101 = 0;
            $dado->vlr_8211102 = 0;
            $dado->soma_ddr = 0;
            $dado->diferenca_contabil = 0;
            $this->recursos->push($dado);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function calcularAtivoFinanceiro()
    {
        $filtrar = [
            "c60_identificadorfinanceiro = 'F'",
            "substring(c60_estrut, 1, 1)::int = 1"
        ];

        $dados = $this->executaBalanceteVerificacaoPorRecurso($filtrar);
        $this->somaDadosBalanceteVerificacao($dados, 'ativo_financeiro');
    }

    /**
     * @param array $dados dados da execução do balancete
     * @param string $propriedadeSomar qual o nome da propriedade que irá receber os valores
     * @return void
     */
    protected function somaDadosBalanceteVerificacao($dados, $propriedadeSomar)
    {
        foreach ($dados as $dado) {
            $this->recursos->each(function ($recurso) use ($dado, $propriedadeSomar) {
                if (in_array($dado->id_recurso, $recurso->idsRecursos)) {
                    $valor = $dado->classe % 2 === 0 ? ($dado->saldo_final * -1) : $dado->saldo_final;

                    $recurso->{$propriedadeSomar} += $valor;
                }
            });
        }
    }

    /**
     * @param $outrosFliltros
     * @return string
     * @throws Exception
     */
    private function executaBalanceteVerificacaoPorRecurso($outrosFliltros)
    {
        $instituicoes = implode(',', $this->filtros['instituicoes']);
        $exercicio = $this->filtros['DB_anousu'];
        $where = [
            "c61_instit in ($instituicoes)",
            "c61_anousu = {$exercicio}"
        ];

        $where = implode(' and ', array_merge($where, $outrosFliltros));
        $sqlFiltraReduzidos = "
        select c61_reduz
          from contabilidade.conplano
          join contabilidade.conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
         where {$where}
        ";
        $reduzidos = DB::select($sqlFiltraReduzidos);
        if (empty($reduzidos)) {
            $str = "Não foi possível buscar dados do balancete de verificação para os filtros informados.";
            throw new Exception($str, 400);
        }

        $datIni = $this->filtros['dataInicial'];
        $datFim = $this->filtros['dataFinal'];
        $sqlReduz = "(select array_agg(c61_reduz) from ({$sqlFiltraReduzidos}) as x )::int[]";
        $pl = "contabilidade.balancete_verificacao_por_recurso";

        $where = "";
        if (!empty($this->filtros['recursos'])) {
            $where = sprintf('where id_recurso in (%s)', implode(',', $this->filtros['recursos']));
        }

        $sqlPl = "select * from {$pl}($exercicio, '{$datIni}', '$datFim', false, $sqlReduz) {$where}";

        return DB::select($sqlPl);
    }

    protected function calcularPassivo()
    {
        $filtrar = [
            "c60_identificadorfinanceiro = 'F'",
            "c60_estrut like '21%'"
        ];

        $dados = $this->executaBalanceteVerificacaoPorRecurso($filtrar);

        $estruturaisPassivoExtra = ['2188', '2189'];
        foreach ($dados as $dado) {
            $this->recursos->each(function ($recurso) use ($dado, $estruturaisPassivoExtra) {
                if (in_array($dado->id_recurso, $recurso->idsRecursos)) {
                    $part = substr($dado->estrutural, 0, 4);

                    $propriedade = 'passivo_liquidacao';
                    if (in_array($part, $estruturaisPassivoExtra)) {
                        $propriedade = 'passivo_extra';
                    }
                    $recurso->{$propriedade} += ($dado->saldo_final * -1);
                }
            });
        }
    }

    /**
     * Executa o calculo da conta 6221301
     * @return void
     * @throws Exception
     */
    protected function calcular6221301()
    {
        $filtrar = [
            "c60_estrut like '6221301%'"
        ];

        $dados = $this->executaBalanceteVerificacaoPorRecurso($filtrar);
        $this->somaDadosBalanceteVerificacao($dados, 'passivo_6221301');
    }

    /**
     * Executa o calculo da conta 6311
     * @return void
     * @throws Exception
     */
    protected function calcular6311()
    {
        $filtrar = [
            "c60_estrut like '6311%'"
        ];

        $dados = $this->executaBalanceteVerificacaoPorRecurso($filtrar);
        $this->somaDadosBalanceteVerificacao($dados, 'passivo_6311');
    }

    /**
     * Executa o calculo da conta 63171
     * @return void
     * @throws Exception
     */
    protected function calcular63171()
    {
        $filtrar = ["c60_estrut like '63171%'"];
        $dados = $this->executaBalanceteVerificacaoPorRecurso($filtrar);
        $this->somaDadosBalanceteVerificacao($dados, 'passivo_63171');
    }

    /**
     * @return array
     */
    protected function executarBalanceteDespesa()
    {
        $filtroDespesa = (object)[];
        if (!empty($this->filtros['recursos'])) {
            $filtroDespesa->recurso = (object)[
                "aRecursos" => $this->filtros['recursos'],
                "operador" => 'in'
            ];
        }

        $servico = new BalanceteDespesaService();
        $servico->setFiltro($filtroDespesa);
        $servico->setFiltrarInstituicoes(collect($this->filtros['instituicoes']));
        $servico->setAno($this->filtros['DB_anousu']);
        $servico->setFiltroDataInicio(Carbon::createFromFormat('Y-m-d', $this->filtros['dataInicial']));
        $servico->setFiltroDataFinal(Carbon::createFromFormat('Y-m-d', $this->filtros['dataFinal']));

        return DB::select($servico->sqlPrincipal());
    }

    /**
     * @param array $balanceteDespesa
     * @return void
     */
    private function calcularPassivoLiquidacao($balanceteDespesa)
    {
        foreach ($balanceteDespesa as $dado) {
            $this->recursos->each(function ($recurso) use ($dado) {
                if (in_array($dado->recurso, $recurso->idsRecursos)) {
                    $recurso->vlr_paga_liquidado += $dado->a_pagar_liquidado;
                }
            });
        }
    }

    /**
     * @param array $balanceteDespesa
     * @return void
     */
    protected function calcularValorLiquidar($balanceteDespesa)
    {
        foreach ($balanceteDespesa as $dado) {
            $this->recursos->each(function ($recurso) use ($dado) {
                if (in_array($dado->recurso, $recurso->idsRecursos)) {
                    $recurso->vlr_liquidar += $dado->a_liquidar;
                }
            });
        }
    }

    protected function executarRPs()
    {
        $exercicio = $this->filtros['DB_anousu'];
        $instituicoes = sprintf("%s", implode(', ', $this->filtros['instituicoes']));
        $where = [
            "e91_anousu = $exercicio",
            "e60_instit in ($instituicoes)"
        ];
        if (!empty($this->filtros['recursos'])) {
            $where[] = sprintf('e91_recurso in (%s)', implode(', ', $this->filtros['recursos']));
        }
        $where = implode(' and ', $where);

        $dataInicial = $this->filtros['dataInicial'];
        $dataFinal = $this->filtros['dataFinal'];
        $sql = "
        with empenhos_rp as (
          select e91_numemp,
                 e91_recurso,
                 e91_vlremp::numeric(17,2),
                 e91_vlranu::numeric(17,2),
                 e91_vlrliq::numeric(17,2),
                 e91_vlrpag::numeric(17,2)
          from empresto
          join empempenho on e60_numemp = e91_numemp
          where {$where}
        ), lancamentos as (
          select c75_numemp,
                 sum(case when c71_coddoc = 31 then c70_valor else 0 end::numeric(17,2)) as estorno_rpp,
                 sum(case when c71_coddoc = 32 then c70_valor else 0 end::numeric(17,2)) as estorno_rpnp,
                 sum(
                    case when c53_tipo = 20
                         then c70_valor when c53_tipo = 21 then c70_valor*-1 else 0
                    end::numeric(17,2)
                 ) as restos_liquidados,
                 sum(
                    case when c71_coddoc in (35, 6008)
                           then c70_valor
                         when c71_coddoc in (36, 6009)
                           then c70_valor*-1 else 0
                    end::numeric(17,2)
                 ) as restos_pagos_processados,
                 sum(
                     case when c71_coddoc in (37, 6010)
                            then c70_valor
                          when c71_coddoc in (38, 6011)
                            then c70_valor*-1 else 0
                     end::numeric(17,2)
                 ) as restos_pagos_nao_processados
            from empenhos_rp
            join conlancamemp on c75_numemp = e91_numemp
            join conlancamdoc on c71_codlan = c75_codlan
            join conhistdoc on c53_coddoc = c71_coddoc
            join conlancam on c70_codlan = c75_codlan
           where c75_data between '$dataInicial' and '$dataFinal'
             and c70_anousu = $exercicio
           group by c75_numemp
        ), totaliza_por_recurso as (
          select e91_recurso,
                 sum(e91_vlremp - e91_vlranu - e91_vlrliq) as restos_a_liquidar,
                 sum(e91_vlrliq - e91_vlrpag) as restos_a_pagar,
                 coalesce(sum(estorno_rpp), 0) as estorno_rpp,
                 coalesce(sum(estorno_rpnp), 0) as estorno_rpnp,
                 coalesce(sum(restos_liquidados), 0) as restos_liquidados,
                 coalesce(sum(restos_pagos_processados), 0) as restos_pagos_processados,
                 coalesce(sum(restos_pagos_nao_processados), 0) as restos_pagos_nao_processados,
                 coalesce(
                    sum(e91_vlremp - e91_vlranu - e91_vlrliq - estorno_rpnp - restos_liquidados), 0
                 ) as vlr_liquidacao_rp,
                 coalesce(sum(e91_vlrliq - e91_vlrpag - restos_liquidados - restos_pagos_processados -
                    restos_pagos_nao_processados - estorno_rpp
                 ), 0) as vlr_a_liquidar
            from empenhos_rp
            left join lancamentos on e91_numemp = c75_numemp
           group by 1
        ), calculos as (
          select e91_recurso,
                 (restos_a_liquidar - estorno_rpnp - restos_liquidados) as vlr_liquidacao_rp,
                 (restos_a_pagar + restos_liquidados - restos_pagos_processados -
                  restos_pagos_nao_processados - estorno_rpp) as vlr_a_liquidar
            from totaliza_por_recurso
        )
        select e91_recurso,
               round(coalesce(vlr_liquidacao_rp, 0), 2) as vlr_liquidacao_rp ,
               round(coalesce(vlr_a_liquidar, 0), 2) as vlr_a_liquidar
          from calculos
        where (vlr_liquidacao_rp != 0 or vlr_a_liquidar != 0)
        ";

        return DB::select($sql);
    }

    protected function calcularValorLiquidacaoRP($rps)
    {
        foreach ($rps as $dado) {
            $this->recursos->each(function ($recurso) use ($dado) {
                if (in_array($dado->e91_recurso, $recurso->idsRecursos)) {
                    $recurso->vlr_liquidar_rp += $dado->vlr_liquidacao_rp;
                }
            });
        }
    }

    protected function calcularValorLiquidarRP($rps)
    {
        foreach ($rps as $dado) {
            $this->recursos->each(function ($recurso) use ($dado) {
                if (in_array($dado->e91_recurso, $recurso->idsRecursos)) {
                    $recurso->vlr_liquidacao_rp += $dado->vlr_a_liquidar;
                }
            });
        }
    }

    private function totalizaLinhas()
    {
        $this->recursos->each(function ($recurso) {
            // (D+E)
            $recurso->soma_liquidacao = $recurso->vlr_paga_liquidado + $recurso->vlr_liquidacao_rp;
            // (I+J)
            $recurso->soma_i_j = $recurso->passivo_6311 + $recurso->passivo_63171;
            // M = (A-B-C-G-I-J)
            $recurso->superavit_contabil = ($recurso->ativo_financeiro - $recurso->passivo_extra -
                $recurso->passivo_liquidacao - $recurso->passivo_6221301 - $recurso->passivo_6311 -
                $recurso->passivo_63171
            );
            // N = (A-B-D-E-H-L)
            $recurso->superavit_execucao_orcamentaria = ($recurso->ativo_financeiro - $recurso->passivo_extra -
                $recurso->vlr_paga_liquidado - $recurso->vlr_liquidacao_rp - $recurso->vlr_liquidar -
                $recurso->vlr_liquidar_rp
            );
            // Q = (O+P)
            $recurso->soma_ddr = $recurso->vlr_8211101 + $recurso->vlr_8211102;
            // M - Q
            $recurso->diferenca_contabil = $recurso->superavit_contabil - $recurso->soma_ddr;

            $recurso->soma_liquidacao = round($recurso->soma_liquidacao, 2);
            $recurso->soma_i_j = round($recurso->soma_i_j, 2);
            $recurso->superavit_contabil = round($recurso->superavit_contabil, 2);
            $recurso->superavit_execucao_orcamentaria = round($recurso->superavit_execucao_orcamentaria, 2);
            $recurso->soma_ddr = round($recurso->soma_ddr, 2);
            $recurso->diferenca_contabil = round($recurso->diferenca_contabil, 2);
        });
    }

    protected function calcular8211()
    {
        $filtrar = ["c60_estrut like '82111%'"];
        $dados = $this->executaBalanceteVerificacaoPorRecurso($filtrar);

        foreach ($dados as $dado) {
            $this->recursos->each(function ($recurso) use ($dado) {
                if (in_array($dado->id_recurso, $recurso->idsRecursos)) {
                    $part = substr($dado->estrutural, 0, 7);
                    if ($part == '8211101') {
                        $recurso->vlr_8211101 += ($dado->saldo_final * -1);
                    }

                    if ($part == '8211102') {
                        $recurso->vlr_8211102 += ($dado->saldo_final * -1);
                    }
                }
            });
        }
    }

    protected function filtraApenasLinhasComValores()
    {
        $this->recursos = $this->recursos->reject(function ($recurso) {
            $valores = collect([
                $recurso->ativo_financeiro,
                $recurso->passivo_extra,
                $recurso->passivo_liquidacao,
                $recurso->vlr_paga_liquidado,
                $recurso->vlr_liquidacao_rp,
                $recurso->soma_liquidacao,
                $recurso->passivo_6221301,
                $recurso->vlr_liquidar,
                $recurso->passivo_6311,
                $recurso->passivo_63171,
                $recurso->soma_i_j,
                $recurso->vlr_liquidar_rp,
                $recurso->superavit_contabil,
                $recurso->superavit_execucao_orcamentaria,
                $recurso->vlr_8211101,
                $recurso->vlr_8211102,
                $recurso->soma_ddr,
                $recurso->diferenca_contabil
            ]);
            return $valores->filter()->count() === 0;
        });
    }
}
