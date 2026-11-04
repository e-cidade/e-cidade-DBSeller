<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use Illuminate\Support\Facades\DB;

class ConsultaLancamentoPcaspService
{

    /**
     * @var string
     */
    private $dataInicial;

    /**
     * @var string
     */
    private $dataFinal;

    /**
     * @var array
     */
    private $contas = [];

    /**
     * @var array
     */
    private $documentosContas = [];

    /**
     * @param string $dataInicial
     * @param string $dataFinal
     * @return this
     */
    public function setPeriodo($dataInicial, $dataFinal)
    {
        $this->dataInicial = $dataInicial;
        $this->dataFinal = $dataFinal;
        return $this;
    }

   /**
    * @param array $contas
    * @return this
    */
    public function setContas($contas)
    {
        $this->contas = $contas;
        return $this;
    }

   /**
    * @param array $contas
    * @return this
    */
    public function setFiltroDocumentos($filtroDocumentos)
    {
        $this->filtroDocumentos = $filtroDocumentos;
        return $this;
    }

    /**
     * @param $reduzido
     */
    private function montaWithQuery($reduzido)
    {
        $withQuery = "
            with lancamentos_conta as (
                select
                    c53_coddoc,
                    c53_descr,
                    c53_tipo,
                    c57_sequencial,
                    c57_descricao,
                    c69_codlan,
                    c69_credito,
                    c69_debito,
                    c69_valor,
                    c69_data
                from contabilidade.conlancamval
                join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
                join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
                join contabilidade.conhistdoctipo on conhistdoctipo.c57_sequencial = c53_tipo
                where c69_data between '{$this->dataInicial}' and '{$this->dataFinal}'
                and (c69_debito = {$reduzido} or c69_credito = {$reduzido})
            ),  valores_por_conta as (
                select
                    c53_coddoc,
                    c53_tipo,
                    c53_descr,
                    c57_sequencial,
                    c57_descricao,
                    c69_codlan,
                    c69_credito,
                    c69_debito,
                    CASE
                        WHEN c69_debito = {$reduzido} THEN c69_debito
                        ELSE c69_credito
                    END AS conta_partida,
                    CASE
                        WHEN c69_credito = {$reduzido} THEN c69_valor
                        ELSE 0
                    END AS soma_credito,
                    CASE
                        WHEN c69_debito = {$reduzido} THEN c69_valor
                        ELSE 0
                    END AS soma_debito
                from lancamentos_conta
                where  (c69_debito = {$reduzido} or c69_credito = {$reduzido})
            )";
        return $withQuery;
    }

    /**
     * reindexa o array para não bugar no javascript
     * @return array $documentosContas
     */
    public function getValoresPorDocumento()
    {
        $this->processaDocumentosContas();
        foreach ($this->documentosContas as $documento) {
            $documento['contas'] = array_values($documento['contas']);
        }
        $this->documentosContas = array_values($this->documentosContas);
        return $this->documentosContas;
    }

    /**
     * executa uma query somando os valores de lançamentos por recurso, de acordo com os documentos filtrados
     * @return array $lancamentos
     */
    public function getValoresPorRecurso($tiposDocumento)
    {
        if ($this->filtroDocumentos == 'documento') {
            $whereFiltroDocumentos = 'c53_coddoc';
        } elseif ($this->filtroDocumentos == 'tipo_documento') {
            $whereFiltroDocumentos = 'c53_tipo';
        }
        $tiposDocumento = implode(',', $tiposDocumento);
        $valores = [];
        foreach ($this->contas as $reduzido) {
            // foi alterado a consulta devido às transferências bancárias de cobertura financeira onde a mesma conta
            // esta a débito e crédito na conlancamrecurso me obrigando a filtra na busca os valores por natureza
            $str = "
                {$this->montaWithQuery($reduzido)},
                valores_por_recurso as (
                    select
                    c130_orctiporec,
                    fonterecurso,
                    descricaorecurso,
                    complementorecurso,
                    descricaocomplemento,
                    reduzido,
                    sum(x.valorcredito) as valorcredito,
                    sum(x.valordebito) as valordebito
                    from (
                    select
                        c130_orctiporec,
                        o15_recurso as fonterecurso,
                        o15_descr as descricaorecurso,
                        o15_complemento as complementorecurso,
                        o200_descricao as descricaocomplemento,
                        conta_partida as reduzido,
                        0 as valorcredito,
                        soma_debito as valordebito
                    from valores_por_conta
                    join contabilidade.conlancamrecurso as a on a.c130_conlancam = c69_codlan
                         and a.c130_conta = conta_partida
                         and a.c130_natureza = 'D'
                    join orcamento.orctiporec on orctiporec.o15_codigo = c130_orctiporec
                    join orcamento.complementofonterecurso on complementofonterecurso.o200_sequencial = o15_complemento
                    where $whereFiltroDocumentos in ({$tiposDocumento})
                  union all
                  select
                        c130_orctiporec,
                        o15_recurso as fonterecurso,
                        o15_descr as descricaorecurso,
                        o15_complemento as complementorecurso,
                        o200_descricao as descricaocomplemento,
                        conta_partida as reduzido,
                        soma_credito as valorcredito,
                        0 as valordebito
                    from valores_por_conta
                    join contabilidade.conlancamrecurso as a on a.c130_conlancam = c69_codlan
                         and a.c130_conta = conta_partida
                         and a.c130_natureza = 'C'
                    join orcamento.orctiporec on orctiporec.o15_codigo = c130_orctiporec
                    join orcamento.complementofonterecurso on complementofonterecurso.o200_sequencial = o15_complemento
                    where $whereFiltroDocumentos in ({$tiposDocumento})
                    ) as x
                    group by c130_orctiporec, fonterecurso, descricaorecurso, complementorecurso,
                    descricaocomplemento, reduzido
                )
                select * from valores_por_recurso
            ";
            $valoresPorRecurso = DB::select($str);
            $valores = array_merge($valores, $valoresPorRecurso);
        }
        return $valores;
    }

    /**
     * executa uma query para buscar todos os lançamentos da conta informada com o documento informado
     * @return array $lancamentos
     */
    public function getInfoLancamentos($reduzido, $tipoDocumento)
    {
        if ($this->filtroDocumentos == 'documento') {
            $whereFiltroDocumentos = 'c53_coddoc';
        } elseif ($this->filtroDocumentos == 'tipo_documento') {
            $whereFiltroDocumentos = 'c53_tipo';
        }
        $lancamentos = DB::select("
            {$this->montaWithQuery($reduzido)}
            select
                c53_coddoc,
                c69_codlan as codigo,
                c53_descr as descricao,
                c69_debito as debito,
                c69_credito as credito,
                c69_valor as valor,
                c69_data as data
            from lancamentos_conta
            where $whereFiltroDocumentos = {$tipoDocumento}
        ");
        return $lancamentos;
    }

    /**
     * Procura os documentos vinculados as contas da propriedade $contas e salva na propriedade $documentosContas
     * @return this
     */
    private function processaDocumentosContas()
    {
        if ($this->filtroDocumentos == 'documento') {
            $groupByTipo = 'c53_coddoc';
            $groupByDescricao = 'c53_descr';
        } elseif ($this->filtroDocumentos == 'tipo_documento') {
            $groupByTipo = 'c57_sequencial';
            $groupByDescricao = 'c57_descricao';
        }
        foreach ($this->contas as $reduzido) {
            $valoresDocumento = DB::select("
                {$this->montaWithQuery($reduzido)},
                soma_por_documento as (
                    select
                        $groupByTipo,
                        $groupByDescricao,
                        conta_partida,
                        sum(soma_credito) as soma_credito,
                        sum(soma_debito) as soma_debito
                    from valores_por_conta
                    group by
                        $groupByTipo,
                        $groupByDescricao,
                        conta_partida
                )
                select * from soma_por_documento
            ");
            foreach ($valoresDocumento as $valorDocumento) {
                $this->setValoresPorDocumento($valorDocumento, $reduzido, $groupByTipo, $groupByDescricao);
            }
        }
        return $this;
    }

    //cria indice para cada documento e para cada conta
    private function setValoresPorDocumento($valorDocumento, $reduzido, $groupByTipo, $groupByDescricao)
    {
        $tipo = $valorDocumento->{$groupByTipo};
        $descricao = $valorDocumento->{$groupByDescricao};

        if (!array_key_exists($tipo, $this->documentosContas)) {
            $this->documentosContas[$tipo]['tipo'] = $tipo;
            $this->documentosContas[$tipo]['descricao'] = $descricao;
            $this->documentosContas[$tipo]['contas'] = [];
        }

        if (!array_key_exists($reduzido, $this->documentosContas[$tipo]['contas'])) {
            $this->documentosContas[$tipo]['contas'][$reduzido] = (object)[
                "reduzido" => $reduzido,
                "tipoDocumento" => $tipo,
                "valorDebito" => $valorDocumento->soma_debito,
                "valorCredito" => $valorDocumento->soma_credito,
                "valorTotal" => $valorDocumento->soma_debito - $valorDocumento->soma_credito,
            ];
        }
    }
}
