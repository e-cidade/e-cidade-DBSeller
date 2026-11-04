<?php
// phpcs:ignoreFile
namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\Deliberacao\Modelos;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Relatorios\Deliberacao\ModeloCincoCsv;
use App\Domain\Financeiro\Contabilidade\Relatorios\Deliberacao\ModeloCincoPdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Esse relatório foi feito a troco de caixa para atender uma demanda de VALENÇA
 * As contas estão tudo fixas
 */
class ModeloCincoService
{
    protected $contasMDE = [38822, 1385, 1374, 1361, 1336, 1343];
    protected $contasFundeb = [22323, 22322, 22321, 1358, 1342, 1333];

    protected $filtros = [];
    /**
     * @var Carbon
     */
    private $dataInicialAnterior;
    /**
     * @var Carbon
     */
    private $dataFinalAnterior;
    /**
     * @var array
     */
    private $dadosFundeb = [];
    private $dadosMde = [];

    public function setFiltros(array $filtros)
    {
        $this->filtros = $filtros;

        $this->dataInicialAnterior = Carbon::createFromFormat('Y-m-d', $filtros['dataInicial']);
        $this->dataInicialAnterior->modify('-1 year');

        $this->dataFinalAnterior = Carbon::createFromFormat('Y-m-d', $filtros['dataFinal']);
        $this->dataFinalAnterior->modify('-1 year');
    }

    public function emitir()
    {
        $this->processar();


        return array_merge($this->emitirPdf(), $this->emitirCsv());
//        return array_merge( $this->emitirCsv());
    }

    public function emitirPdf()
    {
        $pdf = new ModeloCincoPdf();
        $pdf->setFiltros($this->filtros);
        $pdf->setInstituicao(DBConfig::find($this->filtros['instituicao']));
        $pdf->setDadosFundeb($this->dadosFundeb);
        $pdf->setDadosMde($this->dadosMde);
        return $pdf->emitir();
    }

    public function emitirCsv()
    {
        $csv = new ModeloCincoCsv();
        $csv->setFiltros($this->filtros);
        $csv->setInstituicao(DBConfig::find($this->filtros['instituicao']));
        $csv->setDadosFundeb($this->dadosFundeb);
        $csv->setDadosMde($this->dadosMde);
        return $csv->emitir();
    }

    private function processar()
    {
        if (in_array('fundeb', $this->filtros)) {
            $this->processaFundeb();
        }

        if (in_array('mde', $this->filtros)) {
            $this->processaMde();
        }
    }

    /**
     * Retorna uma string sql onde
     *  ultima_conciliacao - possui a última conciliação da conta bancaria
     *  saldo_extrato_bancario - possui o valor do saldo da conta bancária
     *  movimentacao_tesoraria - possui os valores (crédito e débito) da tesoraria
     *  movimentacao_extrato_bancario - possui os valores (crédito e débito) do extrato bancário
     *  movimentacao_credito - toda movimentação a crédito
     *  movimentacao_debito - toda movimentação a débito
     * @return string
     */
    protected function getConsultaPadrao($contas)
    {
        $reduzidos = implode(',', $contas);

        $dataInicio = $this->filtros['dataInicial'];
        $dataFim = $this->filtros['dataFinal'];
        $instituicao = $this->filtros['instituicao'];

        $anoUsu = $this->filtros['exercicio'];

        $sql = "
        WITH ultima_conciliacao as (
            select concilia.k68_sequencial as sequencial, data, conta,
              array_to_string(array_accum(distinct c61_codigo), ', ') as fonte_recurso,
              array_to_string(array_accum(distinct c60_estrut), ', ') as codigo_contabil,
              db90_descr as banco,
              db89_codagencia||'-'||db89_digito||' '||db83_conta||'-'||db83_dvconta as agencia_conta
            from (
                   select max(concilia.k68_data) as data, concilia.k68_contabancaria as conta
                   from concilia
                   join conciliastatus on conciliastatus.k95_sequencial = concilia.k68_conciliastatus
                   join contabancaria on db83_sequencial = k68_contabancaria
                   join conplanocontabancaria on c56_contabancaria = db83_sequencial
                   join conplano on (c60_codcon, c60_anousu) = (c56_codcon, c56_anousu)
                   join conplanoreduz on (c61_reduz, c61_anousu) = (c56_reduz, c56_anousu)
                   where concilia.k68_data between '{$dataInicio}' and '{$dataFim}'
                     and conciliastatus.k95_fechada is true
                     and c61_reduz in ($reduzidos)
                   group by 2
                 ) as ultima_data
              JOIN concilia ON concilia.k68_contabancaria = ultima_data.conta
                               AND concilia.k68_data = ultima_data.data
              join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = concilia.k68_contabancaria
              join conplanoreduz on conplanoreduz.c61_codcon = conplanocontabancaria.c56_codcon
                                    and conplanoreduz.c61_anousu = conplanocontabancaria.c56_anousu
              join conplano on conplano.c60_codcon = conplanoreduz.c61_codcon
                               and conplano.c60_anousu = conplanoreduz.c61_anousu
              join contabancaria on contabancaria.db83_sequencial = concilia.k68_contabancaria
              join bancoagencia on bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia
              join db_bancos on db_bancos.db90_codban = bancoagencia.db89_db_bancos
            where conplanoreduz.c61_instit = {$instituicao}
            and conplanoreduz.c61_anousu = {$anoUsu}
            and c61_reduz in ($reduzidos)
            group by sequencial, data, conta, banco, agencia_conta
        ),
        saldo_extrato_bancario as (
            select coalesce(k97_saldofinal, 0) as saldo_extrato, ultima_conciliacao.conta
              from extratosaldo
              join ultima_conciliacao on ultima_conciliacao.conta = extratosaldo.k97_contabancaria
             WHERE k97_sequencial = (
               SELECT max(k97_sequencial)
                 FROM extratosaldo a
                WHERE a.k97_contabancaria = ultima_conciliacao.conta
                  AND a.k97_dtsaldofinal = (
                    SELECT max(k97_dtsaldofinal)
                      FROM extratosaldo b
                     WHERE b.k97_contabancaria = ultima_conciliacao.conta
                       AND b.k97_dtsaldofinal <= ultima_conciliacao.data
                    )
               )
        ),
        movimentacao_tesoraria as (
            select
              max(case
                  when richeque is not null and richeque <> 0 and rivalorcredito <> 0
                    then 'cheque'
                  when (rnvalordebito is not null and rnvalordebito <> 0)
                        or (richeque is not null and richeque <> 0 and rnvalordebito <> 0)
                    then 'debito'
                  when rivalorcredito is not null and rivalorcredito <> 0
                    then 'credito'
                  end) as tipo,
              max(case
                  when rnvalordebito is not null
                       and rnvalordebito <> 0
                    then 'D'
                  else 'C'
                  end) as tipomov,
              coalesce(sum(case
                  when rnvalordebito is not null
                       and rnvalordebito <> 0
                    then rnvalordebito
                  else rivalorcredito
                  end), 0) as valor,
              ultima_conciliacao.sequencial,
              ultima_conciliacao.conta,
              ultima_conciliacao.agencia_conta,
              ridata AS data_evento,
              ricaixa as id_autenticacao,
              riautent as autenticacao,
              ultima_conciliacao.banco
            from conciliapendcorrente
            join ultima_conciliacao on ultima_conciliacao.sequencial = conciliapendcorrente.k89_concilia
            join fc_extratocaixa({$instituicao}, ultima_conciliacao.conta, null, null, false)
                    on ricaixa = k89_id
                   and riautent = k89_autent
                   and ridata = k89_data
            where not exists(
               select 1
                 from corgrupocorrente
                where k105_autent = k89_autent
                  and k105_id = k89_id
                  and k105_data = k89_data
                  and k105_corgrupotipo in (2, 3, 5, 6)
                  and extract(year from k105_data) <= 2012)
            group by ricaixa, riautent, ridata, riempenho, riordem, riplanilha, rislip, richeque,
            ultima_conciliacao.sequencial, ultima_conciliacao.conta, ultima_conciliacao.agencia_conta,
            ultima_conciliacao.banco
            order by tipo, ridata, ricaixa, riautent
        ),
        movimentacao_extrato_bancario AS  (
            select
                k86_data AS data_evento,
                k86_documento as numero_documento,
                k86_observacao as natureza,
                coalesce(k86_valor, 0) AS valor,
                k86_tipo,
                case
                  when k85_tipoinclusao = 2
                    then 'outros'
                  when k86_tipo = 'D'
                    then 'debito'
                  when k86_tipo = 'C'
                    then 'credito'
                end as tipo,
                ultima_conciliacao.sequencial,
                ultima_conciliacao.conta,
                ultima_conciliacao.agencia_conta,
                ultima_conciliacao.banco
              from conciliapendextrato
              join ultima_conciliacao on ultima_conciliacao.sequencial = conciliapendextrato.k88_concilia
              join extratolinha on k86_sequencial = k88_extratolinha
              join extrato on k85_sequencial = k86_extrato
             order by tipo, k86_data
        ),
        movimentacao_credito as (
          select
            conta,
            data_evento,
            case
            when corlanc.k12_codigo <> 0
              then corlanc.k12_codigo
            when coremp.k12_empen <> 0
              then coremp.k12_empen
            when corplacaixa.k82_seqpla <> 0
              then corplacaixa.k82_seqpla
            else '0'
            end::text as numero_documento,
            k12_histcor as natureza,
            valor,
            null as nota_explicativa,
            null as data_regularizacao,
            coalesce(agencia_conta, '') as agencia_conta,
            coalesce(agencia_conta, '') as conta_bancaria,
            coalesce(banco, '') as banco
          from movimentacao_tesoraria
            left join corhist on corhist.k12_id = movimentacao_tesoraria.id_autenticacao
                                 and corhist.k12_data = movimentacao_tesoraria.data_evento
                                 and corhist.k12_autent = movimentacao_tesoraria.autenticacao
            left join corlanc on corlanc.k12_id = movimentacao_tesoraria.id_autenticacao
                                 and corlanc.k12_data = movimentacao_tesoraria.data_evento
                                 and corlanc.k12_autent = movimentacao_tesoraria.autenticacao
            left join coremp on coremp.k12_id = movimentacao_tesoraria.id_autenticacao
                                and coremp.k12_data = movimentacao_tesoraria.data_evento
                                and coremp.k12_autent = movimentacao_tesoraria.autenticacao
            left join corplacaixa on corplacaixa.k82_id = movimentacao_tesoraria.id_autenticacao
                                     and corplacaixa.k82_data = movimentacao_tesoraria.data_evento
                                     and corplacaixa.k82_autent = movimentacao_tesoraria.autenticacao
          WHERE movimentacao_tesoraria.tipomov = 'C'

          UNION ALL

          select
            conta,
            movimentacao_extrato_bancario.data_evento,
            movimentacao_extrato_bancario.numero_documento::text,
            movimentacao_extrato_bancario.natureza,
            movimentacao_extrato_bancario.valor,
            null as nota_explicativa,
            null as data_regularizacao,
            agencia_conta,
            agencia_conta as conta_bancaria,
            banco
          from movimentacao_extrato_bancario
          where movimentacao_extrato_bancario.k86_tipo = 'C'
        ),
        movimentacao_debito as (
          select
            conta,
            data_evento,
            case
            when corlanc.k12_codigo <> 0
              then corlanc.k12_codigo
            when coremp.k12_empen <> 0
              then coremp.k12_empen
            when corplacaixa.k82_seqpla <> 0
              then corplacaixa.k82_seqpla
            else '0'
            end::text as numero_documento,
            k12_histcor as natureza,
            valor,
            null as nota_explicativa,
            null as data_regularizacao,
            coalesce(agencia_conta, '') as agencia_conta,
            coalesce(agencia_conta, '') as conta_bancaria,
            coalesce(banco, '') as banco
          from movimentacao_tesoraria
            left join corhist on corhist.k12_id = movimentacao_tesoraria.id_autenticacao
                                 and corhist.k12_data = movimentacao_tesoraria.data_evento
                                 and corhist.k12_autent = movimentacao_tesoraria.autenticacao
            left join corlanc on corlanc.k12_id = movimentacao_tesoraria.id_autenticacao
                                 and corlanc.k12_data = movimentacao_tesoraria.data_evento
                                 and corlanc.k12_autent = movimentacao_tesoraria.autenticacao
            left join coremp on coremp.k12_id = movimentacao_tesoraria.id_autenticacao
                                and coremp.k12_data = movimentacao_tesoraria.data_evento
                                and coremp.k12_autent = movimentacao_tesoraria.autenticacao
            left join corplacaixa on corplacaixa.k82_id = movimentacao_tesoraria.id_autenticacao
                                     and corplacaixa.k82_data = movimentacao_tesoraria.data_evento
                                     and corplacaixa.k82_autent = movimentacao_tesoraria.autenticacao
          WHERE movimentacao_tesoraria.tipomov = 'D'

          UNION ALL

          select
            conta,
            movimentacao_extrato_bancario.data_evento,
            movimentacao_extrato_bancario.numero_documento::text,
            movimentacao_extrato_bancario.natureza,
            movimentacao_extrato_bancario.valor,
            null as nota_explicativa,
            null as data_regularizacao,
            agencia_conta,
            agencia_conta as conta_bancaria,
            banco
          from movimentacao_extrato_bancario
          where movimentacao_extrato_bancario.k86_tipo = 'D'
        ),
        dados_cabecalho as (
          select
              conta,
              agencia_conta,
              banco,
              fonte_recurso,
              codigo_contabil,
              (case when db83_tipoconta = 1 then false else true end) as aplicacao_financeira,
              data,
              coalesce((
                select saldo_extrato
                  from saldo_extrato_bancario
                  where saldo_extrato_bancario.conta = ultima_conciliacao.conta), 0) as saldo_extrato,
              coalesce((
                 select sum(valor)
                   from movimentacao_debito
                  where movimentacao_debito.conta = ultima_conciliacao.conta), 0) as debitos,
              coalesce((
                 select sum(valor)
                   from movimentacao_credito
                  where movimentacao_credito.conta = ultima_conciliacao.conta), 0) as creditos
           from ultima_conciliacao
           join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = ultima_conciliacao.conta
           join contabancaria on contabancaria.db83_sequencial = ultima_conciliacao.conta
           join bancoagencia on bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia
           join db_bancos on db_bancos.db90_codban = bancoagencia.db89_db_bancos

           group by conta, agencia_conta, banco, db83_tipoconta, data, fonte_recurso, codigo_contabil
        )
        ";
        return $sql;
    }

    private function processaFundeb()
    {
        $this->dadosFundeb = [
            'disponibilidade' => $this->processaCabecalho($this->contasFundeb),
            'debitos' => $this->processaDebitos($this->contasFundeb),
            'creditos' => $this->processaCredito($this->contasFundeb),
        ];
    }

    private function processaMde()
    {
        $this->dadosMde = [
            'disponibilidade' => $this->processaCabecalho($this->contasMDE),
            'debitos' => $this->processaDebitos($this->contasMDE),
            'creditos' => $this->processaCredito($this->contasMDE),
        ];
    }

    private function processaCabecalho($contas)
    {
        $dataInicial = $this->filtros['dataInicial'];
        $dataFinalAnterior = $this->dataFinalAnterior->format("Y-m-d");
        $consulta = $this->getConsultaPadrao($contas);
        $consulta .= "
            SELECT
              cabecalho.*,
              (saldo_31_12 + debito_ex_anterior + debito_ex_atual - credito_ex_anterior - credito_ex_atual
               ) AS saldo_contabil
            FROM (
              SELECT conta                                                                                                                                                        AS contabancaria,
                     banco,
                     agencia_conta,
                     fonte_recurso,
                     codigo_contabil,
                     (CASE WHEN db83_tipoconta = 1
                       THEN FALSE
                      ELSE TRUE END)                                                                                                                                              AS aplicacao_financeira,
                     data,
                     (SELECT coalesce(sum(saldo_extrato), 0)
                        FROM saldo_extrato_bancario
                        WHERE saldo_extrato_bancario.conta = ultima_conciliacao.conta)                                                                                              AS saldo_31_12,
                     (SELECT coalesce(sum(valor), 0)
                        FROM movimentacao_debito
                      WHERE movimentacao_debito.conta = ultima_conciliacao.conta
                        AND movimentacao_debito.data_evento <= '{$dataFinalAnterior}'
                      ) AS debito_ex_anterior,
                     (SELECT coalesce(sum(valor), 0)
                      FROM movimentacao_debito
                      WHERE movimentacao_debito.conta = ultima_conciliacao.conta
                        AND movimentacao_debito.data_evento >= '{$dataInicial}'
                     ) AS debito_ex_atual,
                     (SELECT coalesce(sum(valor), 0)
                      FROM movimentacao_credito
                      WHERE movimentacao_credito.conta = ultima_conciliacao.conta
                        AND movimentacao_credito.data_evento <= '{$dataFinalAnterior}'
                     ) AS credito_ex_anterior,
                     (SELECT coalesce(sum(valor), 0)
                      FROM movimentacao_credito
                      WHERE movimentacao_credito.conta = ultima_conciliacao.conta
                        AND movimentacao_credito.data_evento >= '{$dataInicial}') AS credito_ex_atual
               FROM ultima_conciliacao
               JOIN conplanocontabancaria ON conplanocontabancaria.c56_contabancaria = ultima_conciliacao.conta
               JOIN contabancaria ON contabancaria.db83_sequencial = ultima_conciliacao.conta
               JOIN bancoagencia ON bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia
               JOIN db_bancos ON db_bancos.db90_codban = bancoagencia.db89_db_bancos
              GROUP BY conta, banco, agencia_conta, db83_tipoconta, data, fonte_recurso, codigo_contabil
              ) AS cabecalho
            order by banco, agencia_conta
        ";

        return DB::select($consulta);
    }

    private function processaDebitos($contasFundeb)
    {
        $consulta = $this->getConsultaPadrao($contasFundeb);
        $consulta .= $this->getSqlMovimentacao('movimentacao_debito');

        $dados = DB::select($consulta);

        $retorno = [];
        foreach ($dados as $dado) {
            $hash = sprintf('%s#%s', $dado->banco, $dado->agencia_conta);
            if (!array_key_exists($hash, $retorno)) {
                $retorno[$hash] = (object)[
                    'banco' => $dado->banco,
                    'conta' => $dado->agencia_conta,
                    'tipo' => 'D',
                    'anteriorExercicio' => [],
                    'doExercicio' => [],
                ];
            }

            if ($dado->tipo_quadro === 1) {
                $retorno[$hash]->anteriorExercicio[] = $dado;
            }
            if ($dado->tipo_quadro === 2) {
                $retorno[$hash]->doExercicio[] = $dado;
            }
        }
        return $retorno;
    }

    private function processaCredito($contasFundeb)
    {
        $consulta = $this->getConsultaPadrao($contasFundeb);
        $consulta .= $this->getSqlMovimentacao('movimentacao_credito');

        $dados = DB::select($consulta);

        $retorno = [];
        foreach ($dados as $dado) {
            $hash = sprintf('%s#%s', $dado->banco, $dado->agencia_conta);

            if (!array_key_exists($hash, $retorno)) {
                $retorno[$hash] = (object)[
                    'banco' => $dado->banco,
                    'conta' => $dado->agencia_conta,
                    'tipo' => 'C',
                    'anteriorExercicio' => [],
                    'doExercicio' => [],
                ];
            }

            if ($dado->tipo_quadro === 1) {
                $retorno[$hash]->anteriorExercicio[] = $dado;
            }
            if ($dado->tipo_quadro === 2) {
                $retorno[$hash]->doExercicio[] = $dado;
            }
        }

        return $retorno;
    }

    private function getSqlMovimentacao($tabela)
    {
        $dataFinalAnterior = $this->dataFinalAnterior->format("Y-m-d");
        $dataInicial = $this->filtros['dataInicial'];

        return "
            select
                1 as tipo_quadro,
                agencia_conta,
                data_evento,
                numero_documento,
                natureza,
                valor,
                nota_explicativa,
                data_regularizacao,
                banco
             from {$tabela}
            where data_evento <='{$dataFinalAnterior}'

            union all

            select
                2 as tipo_quadro,
                agencia_conta,
                data_evento,
                numero_documento,
                natureza,
                valor,
                nota_explicativa,
                data_regularizacao,
                banco
             from {$tabela}
            where data_evento >= '{$dataInicial}'
            order by agencia_conta, data_evento, data_evento
        ";
    }

}
