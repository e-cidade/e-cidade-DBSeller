<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use stdClass;

class ArquivoContaBancaria extends ArquivoBase
{
    protected $sNomeArquivo  = 'ContaBancaria';

    /**
    * Busca os dados para gerar o Arquivo de Unidade Orçamentária
    */

    public function gerarDados()
    {
        $contasExibidas = [];

        $contasBancarias = $this->getContasBancarias();
        $contasNaoMapeadas = $this->getContasNaoMapeadasPcasp();

        if (empty($contasBancarias)) {
            throw new \Exception('Não foi encontrado nenhuma conta bancária para o arquivo de Remessa ');
        }

        $obj = new \stdClass();
        $obj->ContasBancarias = [];

        foreach ($contasNaoMapeadas as $contaNaoMapeada) {
            $this->addLog("Conta $contaNaoMapeada->estrutural sem mapeamento dentro do Pcasp!");
        }

        foreach ($contasBancarias as $contaBancaria) {
            if (in_array($contaBancaria->codigo_conta, $contasExibidas)) {
                continue;
            }
            $contasExibidas[] = $contaBancaria->codigo_conta;

            $maiorDataConciliada = $this->getMaiorDataConciliadaContaBancaria($contaBancaria->codigo_conta);
            $contaBancaria->maiorDataConciliada = $maiorDataConciliada;

            $financeiro = $this->processaFinanceiroConta($contaBancaria);
            
            $saldoInicial = $this->processaSaldoInicialConta($contaBancaria);
            $saldoFinal = $this->processaSaldoFinalConta($contaBancaria);

            $saldoInicial = round(floatval($saldoInicial), 2);
            $saldoFinal = round(floatval($saldoFinal), 2);

            if ($this->mesCompetencia == 0) {
                $financeiro = (object)[
                    "entradaFinanceira" => 0,
                    "saidaFinanceira" => 0
                ];

                $saldoFinal = $saldoInicial;
            }
            
            $dadosLiquidacaoEmpenho = (object)[
                'Identificador' =>$contaBancaria->codigo_conta,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'Competencia' => $this->competencia,
                'NomeBanco' => Helper::convertAndLimit("<![CDATA[".$contaBancaria->descricao_banco, 29),
                'AberturaContaBancaria' =>  $contaBancaria->data_implantacao,
                'EncerramentoContaBancaria' =>  $contaBancaria->data_limite,
                'SaldoInicialContaBancaria' => $saldoInicial,
                'EntradasFinanceiras' => $financeiro->entradaFinanceira,
                'SaidasFinanceiras' => $financeiro->saidaFinanceira,
                'SaldoFinalContaBancaria' => $saldoFinal,
                'ContaContabil' => $contaBancaria->conta_pcasp,
                'CodigoBanco' => $contaBancaria->codigo_banco,
                'CodigoAgencia' => $contaBancaria->codigo_agencia,
                'NumeroContaBancaria' => $contaBancaria->codigo_conta,
                'DescricaoContaBancaria' =>
                Helper::convertAndLimit("<![CDATA[".$contaBancaria->descricao_conta, 264),
                'FonteRecursos' => $contaBancaria->siconfi
            ];

            $obj->ContasBancarias[] = (object)['ContaBancaria' => $dadosLiquidacaoEmpenho];
        }
        $this->aDados =  $obj;
    }

    public function getContasBancarias()
    {

        $camposContasBancarias = [
            DB::raw(" distinct fonterecurso.codigo_siconfi as siconfi"),
            'db83_sequencial as codigo_conta',
            'db90_codban as codigo_banco',
            'db90_descr as descricao_banco',
            'db89_codagencia as codigo_agencia',
            'db89_digito as digito_agencia',
            'db83_dvconta as digito_conta',
            'db83_descricao as descricao_conta',
            DB::raw("max(pcasp.conta) as conta_pcasp"),
            DB::raw("max(k13_dtimplantacao) as data_implantacao"),
            DB::raw("max(k13_limite) as data_limite")
        ];

        $groupBy = [
            'fonterecurso.codigo_siconfi',
            'db83_sequencial',
            'db90_codban',
            'db90_descr',
            'db89_codagencia',
            'db89_digito',
            'db83_dvconta',
            'db83_descricao'
        ];
        
        $contasBancarias = db::table('contabancaria')
        ->select($camposContasBancarias)
        ->join('bancoagencia', 'db89_sequencial', 'db83_bancoagencia')
        ->join('db_bancos', 'db90_codban', 'db89_db_bancos')
        ->join('conplanocontabancaria', 'c56_contabancaria', 'db83_sequencial')
        ->join('conplanoreduz', function ($join) {
            $join->on('c61_reduz', 'c56_reduz')
            ->on('c61_anousu', 'c56_anousu');
        })
        ->join('saltes', 'k13_reduz', 'c61_reduz')
        ->join('conplano', function ($join) {
            $join->on('c61_codcon', 'c60_codcon')
            ->on('c61_anousu', 'c60_anousu');
        })
        ->join('pcaspconplano', 'conplano_codigo', 'c60_codigo')
        ->join('pcasp', 'pcasp.id', 'pcasp_id')
        ->join('orctiporec', 'o15_codigo', 'c61_codigo')
        ->join('fonterecurso', function ($join) {
            $join->on('fonterecurso.orctiporec_id', 'o15_codigo')
            ->on('fonterecurso.exercicio', 'c61_anousu');
        })
        ->whereIn("c61_instit", $this->getListaInstituicoes())
        ->where("c61_anousu", "$this->iAnoUsu")
        ->groupBy($groupBy)
        ->get();
        
        return $contasBancarias;
    }

    public function getMaiorDataConciliadaContaBancaria($codContaBancaria)
    {
        
        $data = DB::table('concilia')
        ->select(DB::raw("max(k68_data) as maior_data_conciliada"))
        ->where('k68_contabancaria', $codContaBancaria)
        ->where('k68_data', '<', $this->dtDataInicial)
        ->get();

        $maiorData = date("Y-m-d", strtotime($this->dtDataInicial . " -1 day"));
        foreach ($data as $dataConciliacao) {
            if (!empty($dataConciliacao->maior_data_conciliada)) {
                $maiorData = $dataConciliacao->maior_data_conciliada;
            }
        }

        return $maiorData;
    }

    public function getContasNaoMapeadasPcasp()
    {
                
        $camposContasBancarias = [
            DB::raw("conplano.c60_estrut as estrutural"),
            "db83_sequencial"
        ];

        $contasBancarias = db::table('contabancaria')
        ->select($camposContasBancarias)
        ->join('bancoagencia', 'db89_sequencial', 'db83_bancoagencia')
        ->join('db_bancos', 'db90_codban', 'db89_db_bancos')
        ->join('conplanocontabancaria', 'c56_contabancaria', 'db83_sequencial')
        ->join('conplanoreduz', function ($join) {
            $join->on('c61_reduz', 'c56_reduz')
            ->on('c61_anousu', 'c56_anousu');
        })
        ->join('saltes', 'k13_reduz', 'c61_reduz')
        ->join('conplano', function ($join) {
            $join->on('c61_codcon', 'c60_codcon')
            ->on('c61_anousu', 'c60_anousu');
        })
        ->leftJoin('pcaspconplano', 'conplano_codigo', 'c60_codcon')
        ->leftJoin('pcasp', 'pcasp.id', 'pcasp_id')
        ->join('orctiporec', 'o15_codigo', 'c61_codigo')
        ->join('fonterecurso', function ($join) {
            $join->on('fonterecurso.orctiporec_id', 'o15_codigo')
            ->on('fonterecurso.exercicio', 'c61_anousu');
        })
        ->whereIn("c61_instit", $this->getListaInstituicoes())
        ->where("c61_anousu", "$this->iAnoUsu")
        ->whereNull("pcasp.id")
        ->orderBy('db90_codban')
        ->get();

        return $contasBancarias;
    }


    public function getConciliadosTesouraria($codContaBancaria)
    {

        $camposConciliadosTesouraria = [
            DB::raw('k84_sequencial as identificador'),
            DB::raw("extract(year from k68_data) as ano_conciliacao"),
            DB::raw('k68_data as data'),
            DB::raw('k12_valor as valor'),
            DB::raw('k12_codord as ordem'),
            DB::raw('k12_codigo as slip'),
            DB::raw("
                case when coremp.k12_codord is not null then 'ordem'
                when corlanc.k12_codigo <> 0 then 'slip' 
                else '' END
                as tipo
            ")
        ];

        $conciliadosTesouraria = db::table('concilia')
        ->select($camposConciliadosTesouraria)
        ->join('conciliaitem', 'k83_concilia', 'k68_sequencial')
        ->join('conciliacor', 'k84_conciliaitem', 'k83_sequencial')
        ->join('corrente', function ($join) {
            $join->on('corrente.k12_id', 'conciliacor.k84_id')
            ->on('corrente.k12_data', 'conciliacor.k84_data')
            ->on('corrente.k12_autent', 'conciliacor.k84_autent');
        })
        ->leftJoin('corlanc', function ($join) {
            $join->on('corlanc.k12_id', 'conciliacor.k84_id')
            ->on('corlanc.k12_data', 'conciliacor.k84_data')
            ->on('corlanc.k12_autent', 'conciliacor.k84_autent');
        })
        ->leftJoin('coremp', function ($join) {
            $join->on('coremp.k12_id', 'conciliacor.k84_id')
            ->on('coremp.k12_data', 'conciliacor.k84_data')
            ->on('coremp.k12_autent', 'conciliacor.k84_autent');
        })

        ->where('k68_contabancaria', $codContaBancaria)
        ->whereBetween('k68_data', [$this->dtDataInicial,$this->dtDataFinal])
        ->get();
        
        return $conciliadosTesouraria;
    }

    public function getTipoConciliacaoTesouraria(stdClass $conciliacao)
    {

        $tipoConciliacao = 'C';

        if ($conciliacao->tipo == 'slip') {
            $camposSlip = [
               'k17_codigo'
            ];
            $slip = db::table('slip')
            ->select($camposSlip)
            ->where('k17_codigo', $conciliacao->slip)
            ->whereRaw('k17_dtestorno is not null')
            ->get();
            
            if (count($slip) > 0) {
                $tipoConciliacao = 'D';
            }
        } elseif ($conciliacao->tipo == 'ordem') {
            $camposOP = [
                'e53_vlranu'
            ];

            $elementosOP = db::table('pagordem')
            ->select($camposOP)
            ->join('pagordemele', 'e53_codord', 'e50_codord')
            ->where('e50_codord', $conciliacao->ordem)
            ->where('e53_vlranu', '>', 0)
            ->get();

            if (count($elementosOP) > 0) {
                $tipoConciliacao = 'D';
            }
        } else {
            if ($conciliacao->valor > 0) {
                $tipoConciliacao = 'D';
            }
        }

        return $tipoConciliacao;
    }

    public function getConciliacaoExtrato($codContaBancaria)
    {

        $camposPendenciasExtrato = [
            'distinct k86_sequencial',
            'k86_tipo as tipo',
            'k86_valor as valor'
        ];

        $camposPendenciasExtrato = implode(',', $camposPendenciasExtrato);
        $pendenciasExtrato = db::table('extratolinha')
        ->selectRaw($camposPendenciasExtrato)
        ->leftJoin('conciliapendextrato', 'k86_sequencial', 'k88_extratolinha')
        ->leftJoin('conciliaextrato', 'k86_sequencial', 'k87_extratolinha')
        ->where('k86_contabancaria', $codContaBancaria)
        ->whereRaw('(k87_sequencial is not null or k88_sequencial is not null)')
        ->whereBetween('k86_data', [$this->dtDataInicial,$this->dtDataFinal])
        ->get();

        return $pendenciasExtrato;
    }

    public function getSaldoExtrato(stdClass $contaBancaria)
    {

        $camposSaldoExtrato = [
            "CASE WHEN k97_situacao = 'C' THEN k97_saldofinal*-1
                  ELSE k97_saldofinal END AS valor"
        ];

        $camposSaldoExtrato = implode(',', $camposSaldoExtrato);

        $saldoExtrato = db::table('extratosaldo')
        ->selectRaw($camposSaldoExtrato)
        ->where('k97_contabancaria', $contaBancaria->codigo_conta)
        ->whereRaw("
           k97_dtsaldofinal = (
                SELECT k97_dtsaldofinal
                  FROM extratosaldo a
                 WHERE a.k97_contabancaria = ($contaBancaria->codigo_conta)
                   AND '$contaBancaria->maiorDataConciliada' >= a.k97_dtsaldofinal
                ORDER BY a.k97_dtsaldofinal desc,a.k97_extrato desc LIMIT 1
            )
        ")
        ->orderBy("k97_extrato", 'DESC')
        ->get();

        return $saldoExtrato;
    }

    public function processaFinanceiroConta(stdClass $contaBancaria)
    {
        $entradaFinanceira = 0;
        $saidaFinanceira = 0;

        $reduzidos = $this->getReduzidosContaBancaria($contaBancaria->codigo_conta);
        foreach ($reduzidos as $reduzido) {
            $saldo = DB::select("select saldos[2] as entradas_financeiras, 
                                        saldos[3] as saidas_financeiras
                                   from fc_planosaldonovo_array(
                                            {$this->iAnoUsu},
                                            {$reduzido},
                                            '{$this->dtDataInicial}',
                                            '{$this->dtDataFinal}',
                                            false
                                        ) as saldos");
            $entradaFinanceira += $saldo[0]->entradas_financeiras;
            $saidaFinanceira += $saldo[0]->saidas_financeiras;
        }

        return (object)[
            'entradaFinanceira' => $entradaFinanceira,
            'saidaFinanceira' => $saidaFinanceira
        ];
    }

    public function processaSaldoInicialConta(stdClass $contaBancaria)
    {
        $saldoExtrato = $this->getSaldoExtrato($contaBancaria);

        $valorSaldo = 0;
        foreach ($saldoExtrato as $saldo) {
            $valorSaldo = floatval($saldo->valor);
        }

        return $valorSaldo;
    }

    public function processaSaldoFinalConta(stdClass $contaBancaria)
    {
        $contaBancaria->maiorDataConciliada = $this->dtDataFinal;
        $saldoExtrato = $this->getSaldoExtrato($contaBancaria);

        $valorSaldo = 0;
        foreach ($saldoExtrato as $saldo) {
            $valorSaldo = floatval($saldo->valor);
        }

        return $valorSaldo;
    }

    private function getReduzidosContaBancaria($codigoConta)
    {
        return DB::TABlE("conplanocontabancaria")
        ->select('c56_reduz')
        ->join("conplanoreduz", function ($join) {
            $join->on('c61_reduz', 'c56_reduz')
            ->on('c61_anousu', 'c56_anousu')
            ->whereIn('c61_instit', $this->getListaInstituicoes());
        })
        ->where('c56_anousu', $this->iAnoUsu)
        ->where("c56_contabancaria", $codigoConta)
            ->get()
            ->map(function ($dados) {
                return $dados->c56_reduz;
            })->toArray();
    }
}
