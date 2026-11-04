<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Models\Periodo;
use App\Domain\Financeiro\Contabilidade\Relatorios\AnexoXVIIPDF;
use db_utils;
use DBDate;
use ParameterException;

class AnexoXVIIService
{

    /**
     * @var array
     */
    private $linhasrp;

    /**
     * @var array
     */
    private $linhasrnp;

    /**
     * @var DBDate
     */
    private $dataInicial;

    /**
     * @var DBDate
     */
    private $dataFinal;

    /**
     * @var integer
     */
    private $ano;

    /**
     * @var object
     */
    private $dtFinalBal;

    /**
     * @var integer
     */
    private $instituicao;

    /**
     * @var array
     */
    private $aDados;

    /**
     * @var array
     */
    private $linhasparametrosDeposito;

    /**
     * @var array
     */
    private $linhasparametros;

    /**
     * RelatorioAnexoXVIIService constructor.
     * @param $parametros
     * @throws ParameterException
     */
    public function __construct($parametros)
    {
        $mes = Periodo::where('o114_sequencial', '=', $parametros['o116_periodo'])->first();

        $this->dataInicial   = "{$parametros['DB_anousu']}-01-01";
        $this->dataFinal     = "{$parametros['DB_anousu']}-12-31";
        $this->dtFinalBal     = "{$parametros['DB_anousu']}-{$mes['o114_mesfinal']}-{$mes['o114_diafinal']}";
        $this->ano           = $parametros['DB_anousu'];
        $this->instituicao   = $parametros['DB_instit'];

        $this->processamento();
    }


    private function processamento()
    {
        require_once(modification("libs/db_libcontabilidade.php"));

        $totalSaldoAnterior = 0;
        $totalDebito = 0;
        $totalCredito = 0;
        $saldoFinal = 0;
        
        $this->aDados['deposito'] = [];
        $this->aDados['totalizadorasaldo'] = $totalSaldoAnterior;
        $this->aDados['totalizadoradebito'] = $totalDebito;
        $this->aDados['totalizadoracredito'] = $totalCredito;

        $where  = " p.c60_estrut like '2188%' AND  r.c61_instit = {$this->instituicao} ";

        $consultaBalancete = db_planocontassaldo_matriz(
            $this->ano,
            $this->dataInicial,
            $this->dtFinalBal,
            true,
            $where,
            '',
            'true',
            'false'
        );

        $consultaBalancete = " WITH deposito as (select * from ({$consultaBalancete})  as x)
         SELECT * from deposito
where (saldo_anterior != 0
    OR saldo_anterior_debito != 0
    OR saldo_anterior_credito != 0
    OR saldo_final != 0)
AND c61_reduz != 0";

        $result = db_query($consultaBalancete);

        for ($i = 0; $i <pg_num_rows($result); $i++) {
            $dado = db_utils::fieldsMemory($result, $i);

            $totalSaldoAnterior +=  $dado->saldo_anterior;
            $totalDebito        +=  $dado->saldo_anterior_debito;
            $totalCredito       +=  $dado->saldo_anterior_credito;
            $saldoFinal         +=  $dado->saldo_final;

            $this->linhasparametrosDeposito[$i] = (object) array(
                'estrutural'             => $dado->estrutural,
                'saldo_anterior'         => $dado->saldo_anterior,
                'saldo_anterior_debito'  => $dado->saldo_anterior_debito,
                'saldo_anterior_credito' => $dado->saldo_anterior_credito,
                'saldo_final'            => $dado->saldo_final,
                'c60_descr'              => $dado->c60_descr,
            );
        }

        $this->aDados['deposito'] =  $this->linhasparametrosDeposito;
        $this->aDados['totalizadorasaldo'] = $totalSaldoAnterior;
        $this->aDados['totalizadoradebito'] = $totalDebito;
        $this->aDados['totalizadoracredito'] = $totalCredito;

        $totSaldoAnterior = 0;
        $totlDebito = 0;
        $totlCredito = 0;
        $saldoFinalOutras = 0;

        $totalDividaPagarSaldoAnterior = 0;
        $totalDividaPagarSaldoAnteriorDebito = 0;
        $totalDividaPagarSaldoAnteriorCredito = 0;
        $totalDividaPagarSaldoAnteriorSaldoFinal = 0;

        $this->aDados['outraslinhas'] = [];
        $this->aDados['linhasInternaContratos'] = [];
        $this->aDados['linhasParcelamentosPagar'] = [];

        $elementosDepositos = [
            '212310202010000',
            '212310202020000',
            '212320200010000',
            '212320200020000',
            '212900000000000',
        ];

        $elementosInternaContratos = [
            '212000000000000'
        ];

        $elementosParcelamentosPagar = [
            '211420100000000'
        ];

        $whereElementos = [];
        for ($x = 0; $x < count($elementosDepositos); $x++) {
            $whereElementos[] = " p.c60_estrut ilike '".$elementosDepositos[$x]."%' ";
        }

        for ($x = 0; $x < count($elementosInternaContratos); $x++) {
            $whereElementos[] = " p.c60_estrut ilike '212%' ";
        }
        
        for ($x = 0; $x < count($elementosParcelamentosPagar); $x++) {
            $whereElementos[] = " p.c60_estrut ilike '2114201%' ";
        }
        
        $where  = " ( ".implode(" or ", $whereElementos).") AND r.c61_instit = {$this->instituicao} ";
        $consultaLinhas = db_planocontassaldo_matriz(
            $this->ano,
            $this->dataInicial,
            $this->dtFinalBal,
            true,
            $where,
            '',
            'false',
            'false'
        );

        $resultOutrasLinhas = db_query($consultaLinhas);

        $totalSaldoAnteriorOutrasLinhas = 0;
        $totalDebitoOutrasLinhas = 0;
        $totalCreditoOutrasLinhas = 0;

        for ($i = 0; $i <pg_num_rows($resultOutrasLinhas); $i++) {
            $dado = db_utils::fieldsMemory($resultOutrasLinhas, $i);

            if (in_array($dado->estrutural, $elementosDepositos)) {
                $totSaldoAnterior   +=  $dado->saldo_anterior;
                $totlDebito         +=  $dado->saldo_anterior_debito;
                $totlCredito        +=  $dado->saldo_anterior_credito;
                $saldoFinalOutras   +=  $dado->saldo_final;

                $totalSaldoAnteriorOutrasLinhas +=  $dado->saldo_anterior;
                $totalDebitoOutrasLinhas        +=  $dado->saldo_anterior_debito;
                $totalCreditoOutrasLinhas       +=  $dado->saldo_anterior_credito;

                $this->linhasparametros[$i] = (object) array(
                'estrutural'             => $dado->estrutural,
                'saldo_anterior'         => $dado->saldo_anterior,
                'saldo_anterior_debito'  => $dado->saldo_anterior_debito,
                'saldo_anterior_credito' => $dado->saldo_anterior_credito,
                'saldo_final'            => $dado->saldo_final,
                'c60_descr'              => $dado->c60_descr,
                );
            }

            if (in_array($dado->estrutural, $elementosInternaContratos)) {
                $this->aDados['linhasInternaContratos'][] = (object) array(
                    'estrutural'             => $dado->estrutural,
                    'saldo_anterior'         => $dado->saldo_anterior,
                    'saldo_anterior_debito'  => $dado->saldo_anterior_debito,
                    'saldo_anterior_credito' => $dado->saldo_anterior_credito,
                    'saldo_final'            => $dado->saldo_final,
                    'c60_descr'              => $dado->c60_descr,
                    );

                $totalDividaPagarSaldoAnterior += $dado->saldo_anterior;
                $totalDividaPagarSaldoAnteriorDebito += $dado->saldo_anterior_debito;
                $totalDividaPagarSaldoAnteriorCredito += $dado->saldo_anterior_credito;
                $totalDividaPagarSaldoAnteriorSaldoFinal += $dado->saldo_final;
            }

            if (in_array($dado->estrutural, $elementosParcelamentosPagar)) {
                $this->aDados['linhasParcelamentosPagar'][] = (object) array(
                    'estrutural'             => $dado->estrutural,
                    'saldo_anterior'         => $dado->saldo_anterior,
                    'saldo_anterior_debito'  => $dado->saldo_anterior_debito,
                    'saldo_anterior_credito' => $dado->saldo_anterior_credito,
                    'saldo_final'            => $dado->saldo_final,
                    'c60_descr'              => $dado->c60_descr,
                    );

                $totalDividaPagarSaldoAnterior += $dado->saldo_anterior;
                $totalDividaPagarSaldoAnteriorDebito += $dado->saldo_anterior_debito;
                $totalDividaPagarSaldoAnteriorCredito += $dado->saldo_anterior_credito;
                $totalDividaPagarSaldoAnteriorSaldoFinal += $dado->saldo_final;
            }
        }

        $this->aDados['outraslinhas'] =  $this->linhasparametros;
        $this->aDados['totlizadorasaldo'] = $totalSaldoAnterior;
        $this->aDados['totlizadoradebito'] = $totalDebito;
        $this->aDados['totlizadoracredito'] = $totalCredito;
        $this->aDados['totsaldofinaloutras'] = $saldoFinal;
        
        $this->aDados['totalDividaPagarSaldoAnterior'] = $totalDividaPagarSaldoAnterior;
        $this->aDados['totalDividaPagarSaldoAnteriorDebito'] = $totalDividaPagarSaldoAnteriorDebito;
        $this->aDados['totalDividaPagarSaldoAnteriorCredito'] = $totalDividaPagarSaldoAnteriorCredito;
        $this->aDados['totalDividaPagarSaldoAnteriorSaldoFinal'] = $totalDividaPagarSaldoAnteriorSaldoFinal;

        $daoempresto = new \cl_empresto();

        $sqlempresto = $daoempresto->sql_rp_novo(
            $this->ano,
            "e60_instit in ({$this->instituicao})",
            $this->dataInicial,
            $this->dataFinal,
            "",
            "",
            "order by e60_anousu, e60_codemp::integer"
        );

        $result = \db_query($sqlempresto);

        $totalSaldAnterior      = 0;
        $totalbaixa             = 0;
        $totalinscricao         = 0;
        $totalSaldAnteriorrp    = 0;
        $totalbaixarp           = 0;
        $totalinscricaornp      = 0;

        while ($dados = pg_fetch_object($result)) {
            $saldoanteriorrp = $this->getSaldoAnterior($dados, 'rp');
            $baixarp         = $this->getValorBaixa($dados, 'rp');

            if (isset($this->linhasrp[$dados->e60_anousu])) {
                $this->linhasrp[$dados->e60_anousu]->saldoanterior += $saldoanteriorrp;
                $this->linhasrp[$dados->e60_anousu]->baixa += $baixarp;
            } else {
                $this->linhasrp[$dados->e60_anousu] = (object) array(
                    'saldoanterior' => $saldoanteriorrp,
                    'baixa'         => $baixarp,
                    'inscricao'     => 0,
                );
            }

            $saldoanterior = $this->getSaldoAnterior($dados, 'rnp');
            $baixa         = $this->getValorBaixa($dados, 'rnp');

            if (isset($this->linhasrnp[$dados->e60_anousu])) {
                $this->linhasrnp[$dados->e60_anousu]->saldoanterior += $saldoanterior;
                $this->linhasrnp[$dados->e60_anousu]->baixa += $baixa;
            } else {
                $this->linhasrnp[$dados->e60_anousu] = (object) array(
                    'saldoanterior' => $saldoanterior,
                    'baixa'         => $baixa,
                    'inscricao'     => 0,
                );
            }

            $totalSaldAnteriorrp += $saldoanteriorrp;
            $totalbaixarp        += $baixarp;
            $totalSaldAnterior   += $saldoanterior;
            $totalbaixa          += $baixa;
        }

        $this->linhasrp[ $this->ano] = (object) array(
            "inscricao"     => (float) $this->getValorinscricaoByAno('rp'),
            "baixa"         => 0,
            "saldoanterior" => 0,
        );

        $totalinscricao += (float) $this->getValorinscricaoByAno('rp');

        $this->aDados['linhasrp'] = $this->linhasrp;
        $this->aDados['totalizadorasaldoanterior'] = $totalSaldAnteriorrp;
        $this->aDados['totalizadorabaixa'] = $totalbaixarp;
        $this->aDados['totalizadorainscricao'] = $totalinscricao;
        $this->aDados['saldofinalrp'] = $totalSaldAnteriorrp + $totalinscricao - $totalbaixarp;

        $this->linhasrnp[$this->ano] = (object) array(
            "inscricao"     => (float) $this->getValorinscricaoByAno('nrp'),
            "baixa"         => 0,
            "saldoanterior" => 0,
        );

        $totalinscricaornp += (float) $this->getValorinscricaoByAno('nrp');

        $saldofinalrnp = (($totalSaldAnterior + $totalinscricaornp) - $totalbaixa);

        $this->aDados['linhasrnp'] = $this->linhasrnp;
        $this->aDados['totalizadorasaldoanteriorrnp'] = $totalSaldAnterior;
        $this->aDados['totalizadorabaixarnp'] = $totalbaixa;
        $this->aDados['totalizadorainscricaornp'] = $totalinscricaornp;
        $this->aDados['saldofinalrnp'] = $saldofinalrnp;
    }


    /**
     * Calcula o saldo anterior
     * @param  object  $dados
     * @param  rp      $rp  rp - restos processados, rnp restos nao processados
     * @return numeric
     */
    private function getSaldoAnterior($dados, $rp = 'rp')
    {
        $saldo = array(
            'rp'  => abs($dados->e91_vlrliq - $dados->e91_vlrpag),
            'rnp' => $dados->e91_vlremp - $dados->e91_vlranu - $dados->e91_vlrliq,
        );

        return $saldo[$rp];
    }

    private function getValorBaixa($dados, $rp = 'rp')
    {
        $baixa = array(
            'rp'  => abs($dados->vlranuliq + $dados->vlrpag),
            'rnp' => abs($dados->vlranuliqnaoproc + $dados->vlrpagnproc),
        );

        return $baixa[$rp];
    }

    private function getSqlinscricao($rp = 'rp')
    {
        $dtinicio = "{$this->ano}-01-01";
        $dtfim    = "{$this->ano}-12-31";

        $where = "and (round(yyy.e60_vlrliq,2) - round(yyy.e60_vlrpag,2) > 0) and round(yyy.e60_vlrliq,2) > 0";

        if ($rp == 'nrp') {
            $where = "and (round(yyy.e60_vlremp,2) - round(yyy.e60_vlranu,2) - round(yyy.e60_vlrliq,2) > 0)";
        }

        $campos = $this->getCamposinscricao($rp);

        $sql = "select        empempenho.e60_numemp, ";
        $sql .= "                         e60_resumo, ";
        $sql .= "                         e60_codemp, ";
        $sql .= "                         e60_emiss, ";
        $sql .= "                         e60_numcgm, ";
        $sql .= "                         z01_nome, ";
        $sql .= "                         z01_cgccpf, ";
        $sql .= "                         z01_munic, ";
        $sql .= "                         yyy.e60_vlremp, ";
        $sql .= "                         yyy.e60_vlranu, ";
        $sql .= "                         yyy.e60_vlrliq, ";
        $sql .= "                         e63_codhist, ";
        $sql .= "                         e40_descr, ";
        $sql .= "                         yyy.e60_vlrpag, ";
        $sql .= "                         e60_anousu, ";
        $sql .= "                         e60_coddot, ";
        $sql .= "                         o58_coddot, ";
        $sql .= "                         o58_orgao, ";
        $sql .= "                         o40_orgao, ";
        $sql .= "                         o40_descr, ";
        $sql .= "                         o58_unidade, ";
        $sql .= "                         o41_descr, ";
        $sql .= "                         o15_codigo, ";
        $sql .= "                         o15_descr, ";
        $sql .= "                         fc_estruturaldotacao(e60_anousu,e60_coddot) as dl_estrutural, ";
        $sql .= "                         e60_codcom, ";
        $sql .= "                         pc50_descr, ";
        $sql .= "                        (select e150_numeroprocesso ";
        $sql .= "                            from empautoriza ";
        $sql .= "                            inner join empenho.empempaut on e54_autori = e61_autori ";
        $sql .= "                            inner join empautorizaprocesso on e150_empautoriza = e54_autori ";
        $sql .= "                            where e61_numemp = empempenho.e60_numemp limit 1) as numprocesso, ";
        $sql .= "                        e60_concarpeculiar ";
        $sql .= "        from ";
        $sql .= "           ( ";
        $sql .= "            select  e60_numemp, ";
        $sql .= "                    sum(case when c53_tipo = 10 then c70_valor else 0 end) as e60_vlremp, ";
        $sql .= "                    sum(case when c53_tipo = 11 then c70_valor else 0 end) as e60_vlranu, ";
        $sql .= "                    sum(case when c53_tipo = 20 then c70_valor else 0 end) - ";
        $sql .= "                    sum(case when c53_tipo = 21 then c70_valor else 0 end) as e60_vlrliq, ";
        $sql .= "                    sum(case when c53_tipo = 30 then c70_valor else 0 end) - ";
        $sql .= "                    sum(case when c53_tipo = 31 then c70_valor else 0 end) as e60_vlrpag ";
        $sql .= "                from ";
        $sql .= "                ( ";
        $sql .= "                    select e60_numemp, ";
        $sql .= "                        c53_tipo, ";
        $sql .= "                        sum(c70_valor) as c70_valor ";
        $sql .= "                    from ";
        $sql .= "                       ( ";
        $sql .= "                        select e60_numemp, ";
        $sql .= "                               e60_anousu, ";
        $sql .= "                               e60_coddot ";
        $sql .= "                            from empempenho ";
        $sql .= "                            where e60_instit in ({$this->instituicao}) ";
        $sql .= "                             and e60_emiss between '$dtinicio' and '$dtfim' ";
        $sql .= "                        ) as xxx ";
        $sql .= "                     inner join orcdotacao on orcdotacao.o58_anousu = xxx.e60_anousu ";
        $sql .= "                     and orcdotacao.o58_coddot = xxx.e60_coddot ";
        $sql .= "                     inner join orcelemento on orcelemento.o56_codele = orcdotacao.o58_codele ";
        $sql .= "                     and orcelemento.o56_anousu = orcdotacao.o58_anousu ";
        $sql .= "                     inner join conlancamemp on c75_numemp = xxx.e60_numemp ";
        $sql .= "                     inner join conlancam on c70_codlan = c75_codlan ";
        $sql .= "                     and c70_data <= '$dtfim' ";
        $sql .= "                     inner join conlancamdoc on c71_codlan = c70_codlan ";
        $sql .= "                     inner join conhistdoc on c53_coddoc = c71_coddoc ";
        $sql .= "                     and c53_tipo in (10, ";
        $sql .= "                                      11, ";
        $sql .= "                                      20, ";
        $sql .= "                                      21, ";
        $sql .= "                                      30, ";
        $sql .= "                                      31) ";
        $sql .= "                     inner join conlancamdot on c73_codlan = c75_codlan ";
        $sql .= "                     group by e60_numemp, ";
        $sql .= "                              c53_tipo ";
        $sql .= "                ) as xxx ";
        $sql .= "                    group by e60_numemp ";
        $sql .= "            ) as yyy ";
        $sql .= "        inner join empempenho on empempenho.e60_numemp = yyy.e60_numemp ";
        $sql .= "        inner join cgm on cgm.z01_numcgm = empempenho.e60_numcgm ";
        $sql .= "        inner join db_config on db_config.codigo = empempenho.e60_instit ";
        $sql .= "        inner join orcdotacao on orcdotacao.o58_anousu = empempenho.e60_anousu ";
        $sql .= "        and orcdotacao.o58_coddot = empempenho.e60_coddot ";
        $sql .= "        inner join emptipo on emptipo.e41_codtipo = empempenho.e60_codtipo ";
        $sql .= "        inner join db_config as a on a.codigo = orcdotacao.o58_instit ";
        $sql .= "        inner join orctiporec on orctiporec.o15_codigo = orcdotacao.o58_codigo ";
        $sql .= "        inner join orcfuncao on orcfuncao.o52_funcao = orcdotacao.o58_funcao ";
        $sql .= "        inner join orcsubfuncao on orcsubfuncao.o53_subfuncao = orcdotacao.o58_subfuncao ";
        $sql .= "        inner join orcprograma on orcprograma.o54_anousu = orcdotacao.o58_anousu ";
        $sql .= "        and orcprograma.o54_programa = orcdotacao.o58_programa ";
        $sql .= "        inner join orcelemento on orcelemento.o56_codele = orcdotacao.o58_codele ";
        $sql .= "        and orcelemento.o56_anousu = orcdotacao.o58_anousu ";
        $sql .= "        inner join orcprojativ on orcprojativ.o55_anousu = orcdotacao.o58_anousu ";
        $sql .= "        and orcprojativ.o55_projativ = orcdotacao.o58_projativ ";
        $sql .= "        inner join orcorgao on orcorgao.o40_anousu = orcdotacao.o58_anousu ";
        $sql .= "        and orcorgao.o40_orgao = orcdotacao.o58_orgao ";
        $sql .= "        inner join orcunidade on orcunidade.o41_anousu = orcdotacao.o58_anousu ";
        $sql .= "        and orcunidade.o41_orgao = orcdotacao.o58_orgao ";
        $sql .= "        and orcunidade.o41_unidade = orcdotacao.o58_unidade ";
        $sql .= "        left join empemphist on empemphist.e63_numemp = empempenho.e60_numemp ";
        $sql .= "        left join emphist on emphist.e40_codhist = empemphist.e63_codhist ";
        $sql .= "        inner join pctipocompra on pctipocompra.pc50_codcom = empempenho.e60_codcom ";
        $sql .= "        where e60_instit in ({$this->instituicao}) ";
        $sql .= "              and e60_emiss between '$dtinicio' and '$dtfim' ";
        $sql .= "                {$where} ";
        $sql .= "                order by ";
        $sql .= "                    e60_numemp, ";
        $sql .= "                    to_number(e60_codemp::text,'9999999999')";

        $sql = "select {$campos} from ({$sql}) as calculo";

        return $sql;
    }

    private function getCamposinscricao($rp = 'rp')
    {
        $campos = array(
            'rp'  => "sum(e60_vlrliq - e60_vlrpag) as valorinscricao",
            'nrp' => "sum(e60_vlremp-e60_vlranu -e60_vlrliq) as valorinscricao",
        );
        return $campos[$rp];
    }

    private function getValorinscricaoByAno($rp = 'rp')
    {
        $sqlempenho = $this->getSqlinscricao($rp);
        $result     = \db_query($sqlempenho) or die($sqlempenho);

        return \db_utils::fieldsMemory($result, 0)->valorinscricao;
    }

    public function emitirPdf()
    {
        $pdf = new AnexoXVIIPDF($this->ano);
        return $pdf->emitir($this->aDados);
    }
}
