<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Fix;

use ECidade\File\Csv\Dumper\Dumper;
use Exception;
use Illuminate\Support\Facades\DB;

class LancamentosSemRecursoService
{
    private $lancamentos = [];

    public function arrumar($exercicio)
    {
        $documentos = $this->getTipoDocumentos($exercicio);

        foreach ($documentos as $documento) {
            $this->processaDocumento($exercicio, $documento->c53_tipo);
        }
    }

    /**
     * @return array
     */
    public function relatorio()
    {
        $filename = sprintf('tmp/lancamentos-afetados-%s.csv', time());
        $csv = new Dumper();
        $cabecalho = ['Tipo', 'Doc.', 'Lançamento', 'Siconfi', 'Subrecurso', 'Complemento'];
        $dados = array_merge([$cabecalho], $this->lancamentos);
        $csv->dumpToFile($dados, $filename);

        return [
            'csv' => $filename,
            'csvLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    private function getTipoDocumentos($exercicio)
    {
        $campos = "distinct c53_tipo";
        $where = ["c69_anousu = {$exercicio}"];
        $sql = $this->getSql($where, $campos);

        return DB::select($sql);
    }

    /**
     * @param $campos
     * @param array $filtros
     * @return string
     */
    public function getSql(array $filtros, $campos = "distinct *")
    {
        $where = implode(' and ', $filtros);

        $sql = "
        select {$campos}
          from (
         select c71_codlan as lancamento, c71_coddoc, c53_tipo, c69_anousu as anousu
           from conlancamval
           join conlancamdoc on c71_codlan = c69_codlan
           join conhistdoc on c53_coddoc = c71_coddoc
           left join conlancamcomplementorecurso on o201_codlan = c69_codlan
          where {$where}
            and c53_tipo in (
                10, 11, 20, 21, 30, 31, 40, 41, 50, 51, 60, 61, 70, 71, 90, 91, 92, 100, 101, 110, 111, 112, 113,
                200, 201, 414, 415, 900, 901, 1000, 1500, 2000, 2001
            )
            and o201_sequencial is null
         union
         select c71_codlan, c71_coddoc, c53_tipo, c69_anousu
           from conlancamval
           join conlancamdoc on c71_codlan = c69_codlan
           join conhistdoc on c53_coddoc = c71_coddoc
           left join conlancamrecurso on c130_conlancam = c69_codlan
          where {$where}
            and c130_sequencial is null
        ) as x
        ";
        return $sql;
    }

    private function processaDocumento($exercicio, $tipoDocumento)
    {
        switch ($tipoDocumento) {
            // origem dotação
            case 40:
            case 41:
            case 50:
            case 51:
            case 60:
            case 61:
            case 70:
            case 71:
                $this->processaOrigemDotacao($exercicio, $tipoDocumento);
                break;
            // origem empenho
            case 10:
            case 11:
            case 20:
            case 21:
            case 30:
            case 31:
            case 90:
            case 91:
            case 92:
            case 200:
            case 201:
            case 414:
            case 415:
            case 900:
            case 901:
                $this->processaOrigemEmpenho($exercicio, $tipoDocumento);
                break;
            // origem receita
            case 100:
            case 101:
            case 110:
            case 111:
            case 112:
            case 113:
                $this->processaOrigemReceita($exercicio, $tipoDocumento);
                break;

            case 120:
            case 131:
            case 151:
            case 152:
            case 161:
            case 162:
                $this->processaCreditoPrimeiroLancamento($exercicio, $tipoDocumento);
                break;

            case 121:
            case 130:
            case 150:
            case 153:
            case 160:
            case 163:
                $this->processaDebitoPrimeiroLancamento($exercicio, $tipoDocumento);
                break;

            // recurso do reduzido no plano de contas verificando débito e crédito
            case 80:
            case 81:
            case 140:
            case 141:
            case 300:
            case 301:
            case 400:
            case 401:
            case 402:
            case 403:
            case 600:
            case 601:
            case 602:
            case 603:
            case 604:
            case 605:
            case 700:
            case 701:
            case 702:
            case 3000:
            case 3001:
            case 3002:
            case 4000:
            case 4001:
            case 5000:
            case 5001:
            case 5002:
            case 5003:
            case 5004:
            case 5005:
                $this->processaDebitoCreditoLancamento($exercicio, $tipoDocumento);
                break;
            case 1000:
            case 1500:
            case 2000:
            case 2001:
                $this->processaAberturaEncerramento($exercicio, $tipoDocumento);
                break;
            default:
                throw new Exception("Tipo de documento {$tipoDocumento} não mapeado.");
        }
    }


    /**
     * Origem do recurso: Recurso da dotação vinculada ao lançamento
     * Inclui conlancamcomplementorecurso e conlancamrecurso
     * @param integer $exercicio
     * @param integer $tipoDocumento
     * @return void
     */
    private function processaOrigemDotacao($exercicio, $tipoDocumento)
    {
        $where = ["c69_anousu = {$exercicio}", "c53_tipo = $tipoDocumento"];
        $sqlLancamentos = $this->getSql($where);

        DB::statement('drop table if exists lancamento_dotacao_arrumar;');

        $sql = "
            create table lancamento_dotacao_arrumar as
            with lancamentos as ($sqlLancamentos),
            recurso as (
                select lancamentos.*, o15_codigo, o15_complemento, codigo_siconfi, o15_recurso
                  from lancamentos
                join conlancamdot on c73_codlan = lancamento
                join orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
                join orctiporec on o15_codigo = o58_codigo
                join fonterecurso on orctiporec_id = o15_codigo and exercicio = c73_anousu
            ), valores_conta as (
                select recurso.*, c69_sequen, 'D' as natureza, c69_debito as conta
                 from recurso
                 join conlancamval on conlancamval.c69_codlan = lancamento
                union all
                select recurso.*, c69_sequen, 'C' as natureza, c69_credito as conta
                 from recurso
                 join conlancamval on conlancamval.c69_codlan = lancamento
            ) select * from valores_conta
        ";

        DB::statement($sql);

        DB::statement('
            insert into conlancamcomplementorecurso (o201_codlan, o201_complemento, o201_orctiporec)
            select distinct lancamento, o15_complemento, o15_codigo
              from lancamento_dotacao_arrumar
              left join conlancamcomplementorecurso on o201_codlan = lancamento
              where o201_sequencial is null;
        ');

        DB::statement('
        insert into conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
        select distinct lancamento, o15_codigo, conta, anousu, natureza
          from lancamento_dotacao_arrumar
          left join conlancamrecurso on c130_conlancam = lancamento
                    and c130_conta = conta
                    and c130_natureza = natureza
         where c130_sequencial is null;
        ');
    }


    /**
     * Origem do recurso: Recurso da dotação vinculada ao lançamento
     * @param integer $exercicio
     * @param integer $tipoDocumento
     * @return void
     */
    private function processaOrigemEmpenho($exercicio, $tipoDocumento)
    {
        $where = ["c69_anousu = {$exercicio}", "c53_tipo = $tipoDocumento"];
        $sqlLancamentos = $this->getSql($where);

        DB::statement('drop table if exists lancamento_empenho_arrumar;');

        $sql = "
            create table lancamento_empenho_arrumar as
            with lancamentos as ($sqlLancamentos),
            empenho_exercicio as (
                select lancamentos.*,
                        case when o206_recurso is null then o58_codigo else o206_recurso end id_recurso,
                        c75_numemp,
                        false as rp
                from lancamentos
                join conlancamemp on c75_codlan = lancamento
                join empempenho  on e60_numemp = c75_numemp
                join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
                left join origemcomplementorecurso on o206_numero = e60_numemp
                          and o206_origem = 1
                where not exists(select 1 from empresto where e91_numemp = e60_numemp)
            ), empenho_rp as (
                select lancamentos.*,
                        case when o206_recurso is null then o58_codigo else o206_recurso end id_recurso,
                        c75_numemp,
                        true as rp
                from lancamentos
                join conlancamemp on c75_codlan = lancamento
                join empempenho  on e60_numemp = c75_numemp
                join empresto on e91_numemp = e60_numemp and e91_anousu = {$exercicio}
                join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
                left join origemcomplementorecurso on o206_numero = e60_numemp
                          and o206_origem = 10
            ), recurso as (
                select x.*, o15_codigo, o15_complemento, codigo_siconfi, o15_recurso
                from (
                    select * from empenho_exercicio
                    union
                    select * from empenho_rp
                ) as x
                join orctiporec on o15_codigo = id_recurso
                join fonterecurso on orctiporec_id = o15_codigo
                     and exercicio = x.anousu
            ), valores_conta as (
                select recurso.*, c69_sequen, 'D' as natureza, c69_debito as conta
                 from recurso
                 join conlancamval on conlancamval.c69_codlan = lancamento
                union all
                select recurso.*, c69_sequen, 'C' as natureza, c69_credito as conta
                 from recurso
                 join conlancamval on conlancamval.c69_codlan = lancamento
            ) select * from valores_conta;

        ";

        DB::statement($sql);

        DB::statement('
            insert into conlancamcomplementorecurso (o201_codlan, o201_complemento, o201_orctiporec)
            select distinct lancamento, o15_complemento, o15_codigo
              from lancamento_empenho_arrumar
              left join conlancamcomplementorecurso on o201_codlan = lancamento
              where o201_sequencial is null;
        ');

        DB::statement('
        insert into conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
        select distinct lancamento, o15_codigo, conta, anousu, natureza
          from lancamento_empenho_arrumar
          left join conlancamrecurso on c130_conlancam = lancamento
                    and c130_conta = conta
                    and c130_natureza = natureza
         where c130_sequencial is null;
        ');
    }

    /**
     * Origem do recurso: Recurso da receita vinculada ao lançamento
     * Inclui conlancamcomplementorecurso e conlancamrecurso
     * @param integer $exercicio
     * @param integer $tipoDocumento
     * @return void
     */
    private function processaOrigemReceita($exercicio, $tipoDocumento)
    {
        $where = ["c69_anousu = {$exercicio}", "c53_tipo = $tipoDocumento"];
        $sqlLancamentos = $this->getSql($where);

        DB::statement('drop table if exists lancamento_receita_arrumar;');

        $sql = "
            create table lancamento_receita_arrumar as
            with lancamentos as ($sqlLancamentos),
            recurso as (
                select lancamentos.*, o15_codigo, o15_complemento, codigo_siconfi, o15_recurso
                  from lancamentos
                join conlancamrec on c74_codlan = lancamento
                join orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)
                join orctiporec on o15_codigo = o70_codigo
                join fonterecurso on orctiporec_id = o15_codigo and exercicio = c74_anousu
            ), valores_conta as (
                select recurso.*, c69_sequen, 'D' as natureza, c69_debito as conta
                 from recurso
                 join conlancamval on conlancamval.c69_codlan = lancamento
                union all
                select recurso.*, c69_sequen, 'C' as natureza, c69_credito as conta
                 from recurso
                 join conlancamval on conlancamval.c69_codlan = lancamento
            ) select * from valores_conta
        ";

        DB::statement($sql);

        DB::statement('
            insert into conlancamcomplementorecurso (o201_codlan, o201_complemento, o201_orctiporec)
            select distinct lancamento, o15_complemento, o15_codigo
              from lancamento_receita_arrumar
              left join conlancamcomplementorecurso on o201_codlan = lancamento
             where o201_sequencial is null;
        ');

        DB::statement('
        insert into conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
        select distinct lancamento, o15_codigo, conta, anousu, natureza
          from lancamento_receita_arrumar
          left join conlancamrecurso on c130_conlancam = lancamento
                    and c130_conta = conta
                    and c130_natureza = natureza
         where c130_sequencial is null;
        ');
    }


    /**
     * Recurso do reduzido no plano de contas verificando débito e crédito
     * Inclui apenas conlancamrecurso.
     * @param integer $exercicio
     * @param integer $tipoDocumento
     * @return void
     */
    private function processaDebitoCreditoLancamento($exercicio, $tipoDocumento)
    {
        $where = ["c69_anousu = {$exercicio}", "c53_tipo = $tipoDocumento"];
        $sqlLancamentos = $this->getSql($where);

        DB::statement('drop table if exists primeiro_lancamento_arrumar;');

        $sql = "
            create table primeiro_lancamento_arrumar as
            with lancamentos as ($sqlLancamentos),
            recurso_debito as (
               select lancamentos.*,
                      o15_codigo,
                      o15_complemento,
                      codigo_siconfi,
                      o15_recurso,
                      c69_sequen,
                      c69_debito as conta,
                      'D' as natureza
               from lancamentos
               join conlancamval on conlancamval.c69_codlan = lancamento
               join conplanoreduz on (c61_reduz, c61_anousu) = (c69_debito, c69_anousu)
               join orctiporec on o15_codigo = c61_codigo
               join fonterecurso on orctiporec_id = o15_codigo
                    and exercicio = c69_anousu
            ), recurso_credito as (
               select lancamentos.*,
                      o15_codigo,
                      o15_complemento,
                      codigo_siconfi,
                      o15_recurso,
                      c69_sequen,
                      c69_credito as conta,
                      'C' as natureza
               from lancamentos
               join conlancamval on conlancamval.c69_codlan = lancamento
               join conplanoreduz on (c61_reduz, c61_anousu) = (c69_credito, c69_anousu)
               join orctiporec on o15_codigo = c61_codigo
               join fonterecurso on orctiporec_id = o15_codigo
                    and exercicio = c69_anousu
            ) , valores_conta as (
                select recurso_debito.*
                 from recurso_debito
                union
                select recurso_credito.*
                 from recurso_credito
            ) select * from valores_conta
            order by lancamento, c69_sequen
        ";

        DB::statement($sql);

        DB::statement('
        insert into conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
        select distinct lancamento, o15_codigo, conta, anousu, natureza
          from primeiro_lancamento_arrumar
          left join conlancamrecurso on c130_conlancam = lancamento
                    and c130_conta = conta
                    and c130_natureza = natureza
         where c130_sequencial is null;
        ');
    }


    /**
     * Recurso do reduzido no plano de contas verificando a conta a CRÉDITO no primeiro lançamento
     * @param $exercicio
     * @param $tipoDocumento
     * @return void
     */
    private function processaCreditoPrimeiroLancamento($exercicio, $tipoDocumento)
    {
        $where = ["c69_anousu = {$exercicio}", "c53_tipo = $tipoDocumento"];
        $sqlLancamentos = $this->getSql($where);

        DB::statement('drop table if exists credito_primeiro_lancamento_arrumar;');

        $sql = "
            create table credito_primeiro_lancamento_arrumar as
            with lancamentos as ($sqlLancamentos),
            recurso as (
               select lancamentos.*,
                      o15_codigo,
                      o15_complemento,
                      codigo_siconfi,
                      o15_recurso
               from lancamentos
               join conlancamval on conlancamval.c69_codlan = lancamento and c69_ordem = 1
               join conplanoreduz on (c61_reduz, c61_anousu) = (c69_credito, c69_anousu)
               join orctiporec on o15_codigo = c61_codigo
               join fonterecurso on orctiporec_id = o15_codigo
                    and exercicio = c69_anousu
            ) , valores_conta as (
                select recurso.*, c69_sequen, c69_debito as conta, 'D' as natureza
                 from recurso
                 join conlancamval on conlancamval.c69_codlan = lancamento
                union all
                select recurso.*, c69_sequen, c69_credito as conta, 'C' as natureza
                 from recurso
                 join conlancamval on conlancamval.c69_codlan = lancamento
            ) select * from valores_conta
            order by lancamento, c69_sequen
        ";

        DB::statement($sql);

        DB::statement('
        insert into conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
        select distinct lancamento, o15_codigo, conta, anousu, natureza
          from credito_primeiro_lancamento_arrumar
          left join conlancamrecurso on c130_conlancam = lancamento
                    and c130_conta = conta
                    and c130_natureza = natureza
         where c130_sequencial is null;
        ');
    }

    /**
     * Origem: Recurso do reduzido no plano de contas verificando a conta a DÉBITO no primeiro lançamento
     * @param $exercicio
     * @param $tipoDocumento
     * @return void
     */
    private function processaDebitoPrimeiroLancamento($exercicio, $tipoDocumento)
    {
        $where = ["c69_anousu = {$exercicio}", "c53_tipo = $tipoDocumento"];
        $sqlLancamentos = $this->getSql($where);

        DB::statement('drop table if exists debito_primeiro_lancamento_arrumar;');

        $sql = "
            create table debito_primeiro_lancamento_arrumar as
            with lancamentos as ($sqlLancamentos),
            recurso as (
               select lancamentos.*,
                      o15_codigo,
                      o15_complemento,
                      codigo_siconfi,
                      o15_recurso
               from lancamentos
               join conlancamval on conlancamval.c69_codlan = lancamento and c69_ordem = 1
               join conplanoreduz on (c61_reduz, c61_anousu) = (c69_debito, c69_anousu)
               join orctiporec on o15_codigo = c61_codigo
               join fonterecurso on orctiporec_id = o15_codigo
                    and exercicio = c69_anousu
            ) , valores_conta as (
                select recurso.*, c69_sequen, c69_debito as conta, 'D' as natureza
                 from recurso
                 join conlancamval on conlancamval.c69_codlan = lancamento
                union all
                select recurso.*, c69_sequen, c69_credito as conta, 'C' as natureza
                 from recurso
                 join conlancamval on conlancamval.c69_codlan = lancamento
            ) select * from valores_conta
            order by lancamento, c69_sequen
        ";

        DB::statement($sql);

        DB::statement('
        insert into conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
        select distinct lancamento, o15_codigo, conta, anousu, natureza
          from debito_primeiro_lancamento_arrumar
          left join conlancamrecurso on c130_conlancam = lancamento
                    and c130_conta = conta
                    and c130_natureza = natureza
         where c130_sequencial is null;
        ');
    }

    private function processaAberturaEncerramento($exercicio, $tipoDocumento)
    {
        $where = ["c69_anousu = {$exercicio}", "c53_tipo = $tipoDocumento"];
        $sqlLancamentos = $this->getSql($where);

        DB::statement('drop table if exists lancamento_arrumar_abertura_encerramento;');
        $sql = "
            create table lancamento_arrumar_abertura_encerramento as
            with lancamentos as ($sqlLancamentos),
            origem as (
               select lancamentos.*,
                      case when c74_codlan is not null then true else false end as receita,
                      case when c73_codlan is not null then true else false end as dotacao,
                      case when c75_codlan is not null then true else false end as empenho,
                      case
                          when c74_codlan is null
                               and c73_codlan is null
                               and c75_codlan is null then true
                          else false
                      end as outros
               from lancamentos
               left join conlancamrec on c74_codlan = lancamento
               left join conlancamdot on c73_codlan = lancamento
               left join conlancamemp on c75_codlan = lancamento
            ), origem_receita as (
               select origem.*, o15_codigo, o15_complemento, codigo_siconfi, o15_recurso
                 from origem
                 join conlancamrec on c74_codlan = lancamento
                 join orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)
                 join orctiporec on o15_codigo = o70_codigo
                 join fonterecurso on orctiporec_id = o15_codigo and exercicio = anousu
                where receita is true
            ), origem_dotacao as (
               select origem.*, o15_codigo, o15_complemento, codigo_siconfi, o15_recurso
                 from origem
                 join conlancamdot on c73_codlan = lancamento
                 join orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
                 join orctiporec on o15_codigo = o58_codigo
                 join fonterecurso on orctiporec_id = o15_codigo and exercicio = anousu
                where dotacao is true
                  and empenho is false
            ), origem_empenho_exercicio as (
               select origem.lancamento,
                      case when o206_recurso is null then o58_codigo else o206_recurso end id_recurso
                 from origem
                 join conlancamemp on c75_codlan = lancamento
                 join empempenho  on e60_numemp = c75_numemp
                 join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
                 left join origemcomplementorecurso on o206_numero = e60_numemp
                           and o206_origem = 1
                where empenho is true
                  and not exists(select 1 from empresto where e91_numemp = e60_numemp)
            ), origem_empenho_rp as (
                select origem.lancamento,
                      case when o206_recurso is null then o58_codigo else o206_recurso end id_recurso
                 from origem
                 join conlancamemp on c75_codlan = lancamento
                 join empempenho  on e60_numemp = c75_numemp
                 join empresto on e91_numemp = e60_numemp and e91_anousu = {$exercicio}
                 join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
                 left join origemcomplementorecurso on o206_numero = e60_numemp
                          and o206_origem = 10
                where empenho is true
            ), origem_empenho as (
                select origem.*, o15_codigo, o15_complemento, codigo_siconfi, o15_recurso
                  from origem
                  join origem_empenho_exercicio e on e.lancamento = origem.lancamento
                  join orctiporec on o15_codigo = id_recurso
                  join fonterecurso on orctiporec_id = o15_codigo and exercicio = origem.anousu
                union
                select origem.*, o15_codigo, o15_complemento, codigo_siconfi, o15_recurso
                  from origem
                  join origem_empenho_rp e on e.lancamento = origem.lancamento
                  join orctiporec on o15_codigo = id_recurso
                  join fonterecurso on orctiporec_id = o15_codigo and exercicio = origem.anousu
            ), recursos as (
                 select x.*
                  from (
                    select * from origem_receita
                    union
                    select * from origem_dotacao
                    union
                    select * from origem_empenho
                  ) as x
            ), valores_conta as (
                  select recursos.*, c69_sequen, c69_debito as conta, 'D' as natureza
                  from recursos
                  join conlancamval on c69_codlan = recursos.lancamento
                  union
                  select recursos.*, c69_sequen, c69_credito as conta, 'C' as natureza
                  from recursos
                  join conlancamval on c69_codlan = recursos.lancamento
                  union
                  select origem.*,
                         o15_codigo,
                         o15_complemento,
                         codigo_siconfi,
                         o15_recurso,
                         c69_sequen,
                         c69_debito as conta,
                         'D' as natureza
                  from origem
                  join conlancamval on conlancamval.c69_codlan = origem.lancamento
                  join conplanoreduz on (c61_reduz, c61_anousu) = (c69_debito, c69_anousu)
                  join orctiporec on o15_codigo = c61_codigo
                  join fonterecurso on orctiporec_id = o15_codigo
                       and exercicio = c69_anousu
                  where origem.outros is true

                  union

                  select origem.*,
                         o15_codigo,
                         o15_complemento,
                         codigo_siconfi,
                         o15_recurso,
                         c69_sequen,
                         c69_credito as conta,
                         'C' as natureza
                  from origem
                  join conlancamval on conlancamval.c69_codlan = origem.lancamento
                  join conplanoreduz on (c61_reduz, c61_anousu) = (c69_credito, c69_anousu)
                  join orctiporec on o15_codigo = c61_codigo
                  join fonterecurso on orctiporec_id = o15_codigo
                       and exercicio = c69_anousu
                  where origem.outros is true
            ) select * from valores_conta
            order by lancamento, c69_sequen
        ";

        DB::statement($sql);

        DB::statement('
            insert into conlancamcomplementorecurso (o201_codlan, o201_complemento, o201_orctiporec)
            select distinct lancamento, o15_complemento, o15_codigo
              from lancamento_arrumar_abertura_encerramento
              left join conlancamcomplementorecurso on o201_codlan = lancamento
              where o201_sequencial is null and lancamento_arrumar_abertura_encerramento.outros is false ;
        ');

        DB::statement('
        insert into conlancamrecurso (c130_conlancam, c130_orctiporec, c130_conta, c130_anousu, c130_natureza)
        select distinct lancamento, o15_codigo, conta, anousu, natureza
          from lancamento_arrumar_abertura_encerramento
          left join conlancamrecurso on c130_conlancam = lancamento
                    and c130_conta = conta
                    and c130_natureza = natureza
         where c130_sequencial is null;
        ');
    }
}
