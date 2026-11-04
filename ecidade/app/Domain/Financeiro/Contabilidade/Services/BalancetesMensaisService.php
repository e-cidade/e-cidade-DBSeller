<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Relatorios\BalancetesMensais\Anexo1PDF;

use db_utils;
use Illuminate\Support\Facades\DB;
use stdClass;
use Carbon\Carbon;
use App\Domain\Financeiro\Contabilidade\Services\BalanceteDespesaService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\BalanceteReceitaComplementoService;
use cl_conlancam;

/**
 * Class BalancetesMensaisService
 * @package App\Domain\Financeiro\Contabilidade\Services
 */
class BalancetesMensaisService
{

    private $filtros ;
    private $instituicoes;
    private $listaInstituicoes;

    public function balanceteMensalAnexo1($filtros)
    {
        $this->filtros = $filtros;

        $oDadosRelatorio = new stdClass;
        $oDadosRelatorio->dadosMovimentoOrcamentario = $this->getMovimentoOrcamentario();
        $oDadosRelatorio->dadosExtraOrcamentario = $this->getMovimentacaoExtraOrcamentaria();
        $oDadosRelatorio->saldoDisponivel = $this->getSaldoDisponivel();

        $pdf = new Anexo1PDF();
        $pdf->setFiltros($this->filtros);
        $pdf->setDados($oDadosRelatorio);

        return $pdf->emitir();
    }


    public function getValorAcumulado($estrutural, $codDoc, $natureza)
    {


        $valorAcumulado = 0;
        $sql = "
                          select
                                 sum(c70_valor) as c70_valor
                     from w_extra_orcamentario
                    where estrutural = '$estrutural'
                      and natureza = $natureza
                      and c71_coddoc = $codDoc
        ";

        $rs = db_query($sql);
        if (pg_numrows($rs) > 0) {
            $valorAcumulado = db_utils::fieldsMemory($rs, 0)->c70_valor;
        }

        return  $valorAcumulado;
    }


    /**
     * cria uma 'visao' com os dados com datas do inicio do ano até o final selecionado (acumulado)
     * para depois nesse balaio buscar os dados com data inicial do usuario
     *
     */
    public function gerarDadosExtra()
    {

        $sInstituicoes = implode($this->getListaInstituicoes(), ',');
        $anousu = $this->filtros->ano;
        $datainicial = "$anousu-01-01";
        $datafinal = $this->filtros->dataFinal;
        $oDao = new cl_conlancam;

        $sqlReceitas = $oDao->sql_receitaExtraOrcamentaria(
            $sInstituicoes,
            $datainicial,
            $datafinal,
            $anousu
        );

        $sqlDespesas = $oDao->sql_despesaExtraOrcamentaria(
            $sInstituicoes,
            $datainicial,
            $datafinal,
            $anousu
        );

        $sqlReceitas = "
                          select estrutural,
                                 c71_coddoc,
                                 descricao_conta,
                                 c53_descr,
                                 1 as natureza,
                                 c71_data,
                                 sum(c70_valor) as c70_valor
                            from ( $sqlReceitas ) as receitas
                            group by estrutural,
                                     c71_coddoc,
                                     descricao_conta,
                                     c53_descr,
                                     natureza,
                                     c71_data
        ";

        $sqlDespesas = "
                           select estrutural,
                                  c71_coddoc,
                                  descricao_conta,
                                  c53_descr,
                                  2 as natureza,
                                  c71_data,
                                  sum(c70_valor) as c70_valor
                      from ( $sqlDespesas ) as despesas
                      group by estrutural,
                               c71_coddoc,
                               descricao_conta,
                               c53_descr,
                               natureza,
                               c71_data
                      order by c71_coddoc
        ";

        $sqlGeral = "create temp table w_extra_orcamentario as $sqlReceitas union  $sqlDespesas";
        db_query($sqlGeral);
    }




    public function getMovimentacaoExtraOrcamentaria()
    {

        $this->gerarDadosExtra(); // cria a tabela com os dados com data para acumulado
        $oDao = new cl_conlancam;
        $datainicial = $this->filtros->dataInicial;
        $datafinal = $this->filtros->dataFinal;

        $oMovimentacaoExtra = new stdClass();
        $receitaExtra = array();
        $despesaExtra = array();

        $sqlReceitas = "
            select estrutural,
                   c71_coddoc,
                   descricao_conta,
                   c53_descr,
                   sum(c70_valor) as c70_valor
              from w_extra_orcamentario
             where natureza = 1
               and c71_data between '$datainicial' and '$datafinal'
              group by estrutural,
                       c71_coddoc,
                       descricao_conta,
                       c53_descr
              order by c71_coddoc
        ";

        $rsReceitas = $oDao->sql_record($sqlReceitas);

        $totalReceita = 0;
        $totalNoMes = 0;
        $totalAcumulado = 0;


        if ($oDao->numrows > 0) {
            for ($i = 0; $i < $oDao->numrows; $i++) {
                $oDados = db_utils::fieldsMemory($rsReceitas, $i);
                $oReceita = new stdClass();
                $oReceita->estrutural = $oDados->estrutural;
                $oReceita->descricao_conta = $oDados->descricao_conta;
                $oReceita->valor = db_formatar($oDados->c70_valor, "f");
                $oReceita->subTitulo = "$oDados->c71_coddoc -  $oDados->c53_descr";
                $oReceita->noMes = db_formatar($oDados->c70_valor, "f");

                $receitaAcumulada = $this->getValorAcumulado($oDados->estrutural, $oDados->c71_coddoc, 1);

                $oReceita->acumulado = db_formatar($receitaAcumulada, "f");

                $totalNoMes += $oDados->c70_valor;
                $totalAcumulado += $receitaAcumulada;

                $receitaExtra["$oDados->c71_coddoc -  $oDados->c53_descr"][] = $oReceita;
            }
        }

        $oDadosReceita = new stdClass();
        $oDadosReceita->dados = $receitaExtra;
        $oDadosReceita->total = db_formatar($totalReceita, "f");
        $oDadosReceita->totalNoMes = $totalNoMes;
        $oDadosReceita->totalAcumulado = $totalAcumulado;
        $oDadosReceita->quantidadeRegistros = $oDao->numrows;


        // DESPESAS
        $sqlDespesas = "
                          select estrutural,
                                 c71_coddoc,
                                 descricao_conta,
                                 c53_descr,
                                 sum(c70_valor) as c70_valor
                     from  w_extra_orcamentario
                      where natureza = 2
                       and c71_data between '$datainicial' and '$datafinal'
                     group by estrutural,
                              c71_coddoc,
                              descricao_conta,
                              c53_descr
                     order by c71_coddoc
        ";

        $rsDespesas = $oDao->sql_record($sqlDespesas);
        $nTotalDespesa = 0;
        $totalNoMes = 0;
        $totalAcumulado = 0;

        if ($oDao->numrows > 0) {
            for ($i = 0; $i < $oDao->numrows; $i++) {
                $oDados = db_utils::fieldsMemory($rsDespesas, $i);
                $oDespesa = new stdClass();
                $oDespesa->estrutural =$oDados->estrutural;
                $oDespesa->descricao_conta = $oDados->descricao_conta;
                $oDespesa->valor = db_formatar($oDados->c70_valor, "f");
                $oDespesa->noMes = db_formatar($oDados->c70_valor, "f");

                $despesaAcumulada = $this->getValorAcumulado($oDados->estrutural, $oDados->c71_coddoc, 2);

                $oDespesa->acumulado = db_formatar($despesaAcumulada, "f");
                $oDespesa->subTitulo = "$oDados->c71_coddoc - $oDados->c53_descr";

                $nTotalDespesa += $oDados->c70_valor;
                $totalNoMes += $oDados->c70_valor;
                $totalAcumulado += $despesaAcumulada;

                $despesaExtra["$oDados->c71_coddoc - $oDados->c53_descr"][] = $oDespesa;
            }
        }

        $oDadosDespesa = new stdClass();
        $oDadosDespesa->dados = $despesaExtra;
        $oDadosDespesa->total = db_formatar($nTotalDespesa, "f");
        $oDadosDespesa->totalNoMes = $totalNoMes;
        $oDadosDespesa->totalAcumulado = $totalAcumulado;
        $oDadosDespesa->quantidadeRegistros = $oDao->numrows;

        $oMovimentacaoExtra->receitas = $oDadosReceita;
        $oMovimentacaoExtra->despesas = $oDadosDespesa;

        return $oMovimentacaoExtra;
    }


    public function getSaldoDisponivel()
    {

        $oSaldoDisponivel = new stdClass;
        $aInstit = $this->getListaInstituicoes();
        $encerramento = 'false';
        $ano = $this->filtros->ano;
        $dataini = $this->filtros->dataInicial;
        $datafim = $this->filtros->dataFinal;
        $sInstituicoes = implode($aInstit, ',');

        $sql = "
        select estrut_mae,
               estrut,
               c61_reduz,
               c61_codcon,
               c61_codigo,
               c60_descr,
               c60_finali,
               c61_instit,
               round(fc_planosaldonovo_array[1]::float8, 2)::float8 as saldo_anterior,
               round(fc_planosaldonovo_array[2]::float8, 2)::float8 as saldo_anterior_debito,
               round(fc_planosaldonovo_array[3]::float8, 2)::float8 as saldo_anterior_credito,
               round(fc_planosaldonovo_array[4]::float8, 2)::float8 as saldo_final,
               round(fc_planosaldonovo_array_acumulado[2]::float8, 2)::float8 as acumulado_debito,
               round(fc_planosaldonovo_array_acumulado[3]::float8, 2)::float8 as acumulado_credito,
               fc_planosaldonovo_array[5]::varchar(1) as sinal_anterior,
               fc_planosaldonovo_array[6]::varchar(1) as sinal_final,
               c60_identificadorfinanceiro,
               c60_consistemaconta
          from (select p.c60_estrut as estrut_mae,
                       p.c60_estrut as estrut,
                       c61_reduz,
                       c61_codcon,
                       c61_codigo,
                       p.c60_descr,
                       p.c60_finali,
                       r.c61_instit,
 fc_planosaldonovo_array($ano, c61_reduz, '$dataini', '$datafim', $encerramento),
 fc_planosaldonovo_array($ano, c61_reduz, '$ano-01-01', '$datafim', $encerramento) as fc_planosaldonovo_array_acumulado,
                       p.c60_identificadorfinanceiro,
                       c60_consistemaconta
                  from conplanoexe e
                       inner join conplanoreduz r on r.c61_anousu = c62_anousu
                                                 and  r.c61_reduz = c62_reduz
                       inner join conplano p on r.c61_codcon = c60_codcon
                                            and r.c61_anousu = c60_anousu
                       left outer join consistema on c60_codsis = c52_codsis
                 where c62_anousu = $ano
                 AND c61_instit in ({$sInstituicoes})
                  ) as x
                 where estrut ilike '11111%'
            ";

            //echo $sql; die();



            db_query('drop table if exists work_pl;');
            db_query("CREATE  TABLE work_pl (
                           estrut_mae VARCHAR(15),
                           estrut VARCHAR(15),
                           c61_reduz INTEGER,
                           c61_codcon INTEGER,
                           c61_codigo INTEGER,
                           c60_descr VARCHAR(200),
                           c60_finali TEXT,
                           c61_instit INTEGER,
                           saldo_anterior FLOAT8,
                           saldo_anterior_debito FLOAT8,
                           saldo_anterior_credito FLOAT8,
                           saldo_final FLOAT8,
                           acumulado_debito FLOAT8,
                           acumulado_credito FLOAT8,
                           sinal_anterior VARCHAR(1),
                           sinal_final VARCHAR(1),
                           c60_identificadorfinanceiro CHARACTER(1),
                           c60_consistemaconta INTEGER)");


            db_query("CREATE INDEX work_pl_estrut ON work_pl(estrut)");
            db_query("CREATE INDEX work_pl_estrutmae ON work_pl(estrut_mae)");
            $result = db_query($sql);

            $acumula_reduzido = true;

            $tot_anterior = 0;
            $tot_anterior_debito = 0;
            $tot_anterior_credito = 0;
            $tot_saldo_final = 0;
            $seq = null;
            $estrut = null;
            $c61_reduz = null;
            $c61_codcon = null;
            $c61_codigo = null;
            $c60_codcon = null;
            $c60_descr = null;
            $c60_finali = null;
            $c61_instit = null;
            $saldo_anterior = null;
            $saldo_anterior_debito = null;
            $saldo_anterior_credito = null;
            $saldo_final = null;
            $acumulado_debito = null;
            $acumulado_credito = null;
            $sinal_anterior = null;
            $sinal_final = null;
            $c60_identificadorfinanceiro = null;
            $c60_consistemaconta = null;

            $work_planomae = array();
            $work_planoestrut = array();
            $work_plano = array();
            $seq = 0;

        for ($i = 0; $i < pg_numrows($result); $i++) {
            db_fieldsmemory($result, $i);
            $oDados = db_utils::fieldsMemory($result, $i);

            if ($oDados->sinal_anterior == "C") {
                $oDados->saldo_anterior *= -1;
            }
            if ($oDados->sinal_final == "C") {
                $oDados->saldo_final *= -1;
            }
            $tot_anterior = dbround_php_52($oDados->saldo_anterior, 2);
            $tot_anterior_debito = dbround_php_52($oDados->saldo_anterior_debito, 2);
            $tot_anterior_credito = dbround_php_52($oDados->saldo_anterior_credito, 2);
            $tot_saldo_final = dbround_php_52($oDados->saldo_final, 2);

            $tot_acumulado_debito  =  dbround_php_52($oDados->acumulado_credito, 2);
            $tot_acumulado_credito = dbround_php_52($oDados->acumulado_debito, 2);



            if ($acumula_reduzido == true) {
                $key = array_search("$oDados->estrut_mae", $work_planomae);
            } else {
                $key = false;
            }

            if ($key === false) { // não achou
                $work_planomae[$seq] = $oDados->estrut_mae;
                $work_planoestrut[$seq] = $oDados->estrut;
                $work_plano[$seq] = array(
                    0 => "$oDados->c61_reduz",
                    1 => "$oDados->c61_codcon",
                    2 => "$oDados->c61_codigo",
                    3 => "$oDados->c60_descr",
                    4 => "$oDados->c60_finali",
                    5 => "$oDados->c61_instit",
                    6 => "$oDados->saldo_anterior",
                    7 => "$oDados->saldo_anterior_debito",
                    8 => "$oDados->saldo_anterior_credito",
                    9 => "$oDados->saldo_final",

                    10 => "$oDados->acumulado_debito",
                    11 => "$oDados->acumulado_credito",

                    12 => "$oDados->sinal_anterior",
                    13 => "$oDados->sinal_final",
                    14 => "$oDados->c60_identificadorfinanceiro",
                    15 => "$oDados->c60_consistemaconta"
                );
                $seq = $seq + 1;
            } else {
                $work_plano[$key][6] =
                  dbround_php_52($work_plano[$key][6], 2) + dbround_php_52($tot_anterior, 2);
                $work_plano[$key][7] =
                  dbround_php_52($work_plano[$key][7], 2) + dbround_php_52($tot_anterior_debito, 2);
                $work_plano[$key][8] =
                  dbround_php_52($work_plano[$key][8], 2) + dbround_php_52($tot_anterior_credito, 2);
                $work_plano[$key][9] =
                  dbround_php_52($work_plano[$key][9], 2) + dbround_php_52($tot_saldo_final, 2);
            }
            $estrutural = $oDados->estrut;


            for ($ii = 1; $ii < 10; $ii++) {
                $estrutural = $this->ajustaContaMae($estrutural);
                $nivel = $this->ajustaContaMae($estrutural, true);
                $key = array_search("$estrutural", $work_planomae);
                if ($key === false) { // não achou
                // busca no banco e inclui
                    $res = db_query("select c60_descr,
                                            c60_finali,
                                            c60_codcon,
                                            c60_identificadorfinanceiro
                                       from conplano
                                      where c60_anousu = {$ano} and c60_estrut = '$estrutural' ");

                    if ($res == false || pg_numrows($res) == 0) {
                        $sMensagemErro = "Está faltando cadastrar esse estrutural na
                          contabilidade. Nível : {$nivel}  Estrutural : {$estrutural} - ano: {$ano}";
                        throw new \Exception($sMensagemErro);
                    }

                    $oBusca = db_utils::fieldsMemory($res, 0);
                    $c60_descr = $oBusca->c60_descr;
                    $c60_finali = $oBusca->c60_finali;
                    $c60_codcon = $oBusca->c60_codcon;
                    $c60_identificadorfinanceiro = $oBusca->c60_identificadorfinanceiro;

                    $work_planomae[$seq] = $estrutural;
                    $work_planoestrut[$seq] = '';
                /// Validar Parametros do Orcamento para Acumular as Sinteticas (Estrutura e Instituicao)
                    $work_plano[$seq] = (array(
                    0 => 0,
                    1 => $c60_codcon,
                    2 => 0,
                    3 => $c60_descr,
                    4 => $c60_finali,
                    5 => 0,
                    6 => $oDados->saldo_anterior,
                    7 => $oDados->saldo_anterior_debito,
                    8 => $oDados->saldo_anterior_credito,
                    9 => $oDados->saldo_final,

                    10 => $oDados->acumulado_debito,
                    11 => $oDados->acumulado_credito,

                    12 => $oDados->sinal_anterior,
                    13 => $oDados->sinal_final,
                    14 => $c60_identificadorfinanceiro,
                    15 => $oDados->c60_consistemaconta
                    ));

                    $seq++;
                } else {
                  /// Validar Parametros do Orcamento para Acumular as Sinteticas (Estrutura e Instituicao)
                      $work_plano[$key][6] = dbround_php_52($work_plano[$key][6], 2) +
                        dbround_php_52($tot_anterior, 2);
                      $work_plano[$key][7] = dbround_php_52(
                          $work_plano[$key][7],
                          2
                      ) + dbround_php_52($tot_anterior_debito, 2);
                      $work_plano[$key][8] = dbround_php_52(
                          $work_plano[$key][8],
                          2
                      ) + dbround_php_52($tot_anterior_credito, 2);
                      $work_plano[$key][9] = dbround_php_52($work_plano[$key][9], 2) +
                        dbround_php_52($tot_saldo_final, 2);
                }
                if ($nivel == 1) {
                    break;
                }
            }
        }

        for ($i = 0; $i < sizeof($work_planomae); $i++) {
            $mae = $work_planomae[$i];
            $estrut = $work_planoestrut[$i];
            $c61_reduz = $work_plano[$i][0];
            $c61_codcon = $work_plano[$i][1];
            $c61_codigo = $work_plano[$i][2];
            $c60_descr = $work_plano[$i][3];
            $c60_finali = $work_plano[$i][4];
            $c61_instit = $work_plano[$i][5];
            $saldo_anterior = $work_plano[$i][6];
            $saldo_anterior_debito = $work_plano[$i][7];
            $saldo_anterior_credito = $work_plano[$i][8];
            $saldo_final = $work_plano[$i][9];

            $acumulado_debito =$work_plano[$i][10];
            $acumulado_credito = $work_plano[$i][11];


            $sinal_anterior = $work_plano[$i][12];
            $sinal_final = $work_plano[$i][13];
            $c60_identificadorfinanceiro = $work_plano[$i][14];
            $c60_consistemaconta = $work_plano[$i][15];

            $sql = "insert into work_pl
                values ('$mae',
                '$estrut',
                $c61_reduz,
                $c61_codcon,
                $c61_codigo,
                '" . pg_escape_string($c60_descr) . "',
                '" . pg_escape_string($c60_finali) . "',
                $c61_instit,
                $saldo_anterior,
                $saldo_anterior_debito,
                $saldo_anterior_credito,
                $saldo_final,
                $acumulado_debito,
                $acumulado_credito,
                '$sinal_anterior',
                '$sinal_final',
                '$c60_identificadorfinanceiro',
                $c60_consistemaconta)";
            db_query($sql);
        }
            $sql = "SELECT db83_conta || ' - ' || db83_dvconta as descr_conta_bancaria,
                           CASE WHEN c61_reduz = 0 THEN
                           estrut_mae
                           ELSE
                           estrut
                           END AS estrutural,
                           c61_reduz,
                           c61_codcon,
                           c61_codigo,
                           c60_descr,
                           c60_finali,
                           c61_instit,
                           abs(saldo_anterior) AS saldo_anterior,
                           abs(saldo_anterior_debito) AS saldo_anterior_debito,
                           abs(saldo_anterior_credito) AS saldo_anterior_credito,
                           abs(saldo_final) AS saldo_final,

                           abs(acumulado_debito) AS acumulado_debito,
                           abs(acumulado_credito) AS acumulado_credito,

                           CASE WHEN saldo_anterior < 0 THEN  'C'
                           WHEN saldo_anterior > 0 THEN 'D'
                           ELSE ' '
                           END AS  sinal_anterior,
                           CASE WHEN saldo_final < 0 THEN 'C'
                           WHEN saldo_final > 0 THEN 'D'
                           ELSE ' '
                           END AS  sinal_final,
                           CASE WHEN c60_identificadorfinanceiro = 'N' THEN ''
                                ELSE c60_identificadorfinanceiro
                           END AS isf,
                           CASE WHEN c60_consistemaconta = 0 THEN ''
                                WHEN c60_consistemaconta = 1 THEN 'O'
                                WHEN c60_consistemaconta = 2 THEN 'P'
                                ELSE 'C'
                           END AS sis
                     FROM work_pl
                left join conplanocontabancaria on c56_reduz = c61_reduz
                                               and c56_codcon = c61_codcon
                                               and c56_anousu = {$ano}
                left join contabancaria on c56_contabancaria = db83_sequencial
                 ORDER BY estrut_mae,
                          estrut";

            $rsBalver = db_query($sql);

            $aBalanceteVerificacao = [];

            $saldo_anterior_debitoTotal = 0;
            $saldo_anterior_creditoTotal = 0;
            $acumulado_debitoTotal = 0;
            $acumulado_creditoTotal = 0;


        for ($i = 0; $i < pg_numrows($rsBalver); $i++) {
            $oDados = db_utils::fieldsMemory($rsBalver, $i);

            if ($oDados->c61_reduz <= 0) {
                continue;
            }

            if ($oDados->saldo_anterior_debito <= 0 &&
                 $oDados->saldo_anterior_credito <= 0 &&
                 $oDados->acumulado_debito <= 0 &&
                 $oDados->acumulado_credito <= 0) {
                continue;
            }

            $oBalver = new stdClass;
            $oBalver->descr_conta_bancaria = $oDados->descr_conta_bancaria;
            $oBalver->descricao = $oDados->c60_descr;

            $oBalver->saldo_anterior_debito = db_formatar($oDados->saldo_anterior_debito, "f");
            $oBalver->saldo_anterior_credito = db_formatar($oDados->saldo_anterior_credito, "f");

            $oBalver->acumulado_debito = db_formatar($oDados->acumulado_debito, "f");
            $oBalver->acumulado_credito = db_formatar($oDados->acumulado_credito, "f");

            $saldo_anterior_debitoTotal += $oDados->saldo_anterior_debito;
            $saldo_anterior_creditoTotal += $oDados->saldo_anterior_credito;
            $acumulado_debitoTotal += $oDados->acumulado_debito;
            $acumulado_creditoTotal += $oDados->acumulado_credito;

            $aBalanceteVerificacao[] = $oBalver;
        }

            $oSaldoDisponivel->saldoDisponivel = $aBalanceteVerificacao;
            $oSaldoDisponivel->saldo_anterior_debitoTotal = $saldo_anterior_debitoTotal;
            $oSaldoDisponivel->saldo_anterior_creditoTotal = $saldo_anterior_creditoTotal;
            $oSaldoDisponivel->acumulado_debitoTotal = $acumulado_debitoTotal;
            $oSaldoDisponivel->acumulado_creditoTotal = $acumulado_creditoTotal;

            return $oSaldoDisponivel;
    }


    public function ajustaContaMae($codigo, $nivel = false)
    {
        $retorno = "";

        if ($retorno == "" && substr($codigo, 13, 2) != '00') {
            if ($nivel == true) {
                $retorno = 10;
            } else {
                $retorno = substr($codigo, 0, 13) . '00';
            }
        }
        if ($retorno == "" && substr($codigo, 11, 2) != '00') {
            if ($nivel == true) {
                $retorno = 9;
            } else {
                $retorno = substr($codigo, 0, 11) . '0000';
            }
        }
        if ($retorno == "" && substr($codigo, 9, 6) != '000000') {
            if ($nivel == true) {
                $retorno = 8;
            } else {
                $retorno = substr($codigo, 0, 9) . '000000';
            }
        }
        if ($retorno == "" && substr($codigo, 7, 8) != '00000000') {
            if ($nivel == true) {
                $retorno = 7;
            } else {
                $retorno = substr($codigo, 0, 7) . '00000000';
            }
        }
        if ($retorno == "" && substr($codigo, 5, 10) != '0000000000') {
            if ($nivel == true) {
                $retorno = 6;
            } else {
                $retorno = substr($codigo, 0, 5) . '0000000000';
            }
        }
        if ($retorno == "" && substr($codigo, 4, 11) != '00000000000') {
            if ($nivel == true) {
                $retorno = 5;
            } else {
                $retorno = substr($codigo, 0, 4) . '00000000000';
            }
        }
        if ($retorno == "" && substr($codigo, 3, 12) != '000000000000') {
            if ($nivel == true) {
                $retorno = 4;
            } else {
                $retorno = substr($codigo, 0, 3) . '000000000000';
            }
        }
        if ($retorno == "" && substr($codigo, 2, 13) != '0000000000000') {
            if ($nivel == true) {
                $retorno = 3;
            } else {
                $retorno = substr($codigo, 0, 2) . '0000000000000';
            }
        }
        if ($retorno == "" && substr($codigo, 1, 14) != '00000000000000') {
            if ($nivel == true) {
                $retorno = 2;
            } else {
                $retorno = substr($codigo, 0, 1) . '00000000000000';
            }
        }
        if ($retorno == "") {
            if ($nivel == true) {
                $retorno = 1;
            } else {
                $retorno = $codigo;
            }
        }
        return $retorno;
    }

    /*
      @return array
    */
    public function getListaInstituicoes()
    {

        $aInstit = array();
        $instituicoes = str_replace('\"', '"', $this->filtros->instituicoes);
        $instituicoes = \JSON::create()->parse($instituicoes);

        foreach ($instituicoes as $oInstit) {
            $aInstit[] = $oInstit->codigo;
        }
        return $aInstit;
    }


    public function getMovimentoOrcamentario()
    {

        $oMovimentoOrcamentario = new stdClass();
        $oMovimentoOrcamentario->receitas = [];
        $oMovimentoOrcamentario->oTotalReceitas = null;
        $oMovimentoOrcamentario->despesas = [];
        $oMovimentoOrcamentario->oTotalDespesas = null;

        $dataInicial = $this->filtros->dataInicial;
        $dataFinal = $this->filtros->dataFinal;
        $ano = $this->filtros->ano;



        // RECEITAS

        $aFiltros = [];
        $aFiltros["natureza"] = "";
        $aFiltros["nivel_agrupar"] = "0";
        $aFiltros["apenasComMovimentacao"] = "1";
        $aFiltros["db_selinstit"] = "";
        $aFiltros["dataInicio"] = $dataInicial;
        $aFiltros["dataFinal"] = $dataFinal;
        $aFiltros["instituicoes"] = $this->filtros->instituicoes;

        $balanceteReceita = new BalanceteReceitaComplementoService;
        $balanceteReceita->setFiltrosRequest($aFiltros);
        $aDadosReceita = $balanceteReceita->getArvore();

        //dd( $aDadosReceita);
        $aReceitas = [];

        $nTotalPreiodo = 0;
        $nTotalAcumulado = 0;
        foreach ($aDadosReceita as $oDados) {
            $oDadosReceita = new stdClass;
            $oDadosReceita->elemento = $oDados->natureza;
            $oDadosReceita->descricao = $oDados->descricao;
            $oDadosReceita->periodo =  db_formatar($oDados->arrecadado_periodo, "f");
            $oDadosReceita->acumulado = db_formatar($oDados->arrecadado_acumulado, "f");

            if ($oDados->natureza == '400000000000000' || $oDados->natureza == '900000000000000') {
                $nTotalPreiodo += $oDados->arrecadado_periodo;
                $nTotalAcumulado += $oDados->arrecadado_acumulado;
            }

            $aReceitas[] = $oDadosReceita;
        }
       // dd($aReceitas);
        $oTotalReceitas = new stdClass;
        $oTotalReceitas->totalPeriodo = $nTotalPreiodo;
        $oTotalReceitas->totalAcumulado = $nTotalAcumulado;

        $oMovimentoOrcamentario->receitas = $aReceitas;
        $oMovimentoOrcamentario->oTotalReceitas = $oTotalReceitas;



        // DESPESAS

        $balanceteDespesa = new BalanceteDespesaService;
        $balanceteDespesa->setFiltroDataInicio(Carbon::createFromFormat('d/m/Y', $dataInicial));
        $balanceteDespesa->setFiltroDataFinal(Carbon::createFromFormat('d/m/Y', $dataFinal));
        $balanceteDespesa->setFiltrarInstituicoes(collect($this->getListaInstituicoes()));
        $balanceteDespesa->setAno($ano);
        $sqlDespesa = $balanceteDespesa->sqlPrincipal();

        $sqlDespesa = " select elemento,
                               descricao_elemento,
                               sum(pago) as pago,
                               sum(pago_acumulado) as pago_acumulado
                          from ({$sqlDespesa} ) as dd
                        group by elemento,
                                 descricao_elemento
        ";

        $rsDespesas = db_query($sqlDespesa);


        //echo $sqlDespesa;die();

        if (pg_numrows($rsDespesas) > 0) {
            $aDespesas = [];

            $totalPago = 0;
            $totalPagoAcumulado = 0;
            for ($i = 0; $i < pg_numrows($rsDespesas); $i++) {
                $oDados = db_utils::fieldsMemory($rsDespesas, $i);

                $oDadosDespesa = new stdClass;
                $oDadosDespesa->elemento = $oDados->elemento;
                $oDadosDespesa->descricao = $oDados->descricao_elemento;
                $oDadosDespesa->periodo = db_formatar($oDados->pago, "f");
                $oDadosDespesa->acumulado = db_formatar($oDados->pago_acumulado, "f");

                $totalPago += $oDados->pago;
                $totalPagoAcumulado += $oDados->pago_acumulado;

                $aDespesas[] = $oDadosDespesa;
            }

            $oTotalDespesas = new stdClass;
            $oTotalDespesas->totalPago = $totalPago;
            $oTotalDespesas->totalPagoAcumulado = $totalPagoAcumulado;

            $oMovimentoOrcamentario->despesas = $aDespesas;
            $oMovimentoOrcamentario->oTotalDespesas = $oTotalDespesas;
        }


        return $oMovimentoOrcamentario;
    }
}
