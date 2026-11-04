<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use BusinessException;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

class ArquivoPagamentoDeEmpenhoConsignacaoRetencao extends ArquivoBase
{
    protected $sNomeArquivo = 'PagamentoDeEmpenhoConsignacaoRetencao';

    public function gerarDados()
    {
        $retencoes = $this->getRetencoes();

        if ($retencoes->isEmpty()) {
            throw new BusinessException('Não há retenções para a competência informada.');
        }

        $EmpenhoConsignacaoRetencao = new stdClass();
        $EmpenhoConsignacaoRetencao->PagamentosDeEmpenhoConsignacoesRetencoes = [];


        foreach ($retencoes as $retencao) {
            $idNotaPegamentoRetencao = $retencao->ordem . $retencao->CodigoRetencao;

            $contaBancaria = $this->getContaBancaria($retencao->IdentificadorRetencao);

            $ConsigRet = new stdClass();
            $ConsigRet->Identificador = $retencao->IdentificadorRetencao;
            $ConsigRet->CodigoUnidadeGestora = $this->sCodigoTribunal;
            $ConsigRet->Competencia = $this->competencia;
            $ConsigRet->NumeroEmpenho = $retencao->NumeroEmpenho;
            $ConsigRet->AnoEmpenho = $retencao->AnoEmpenho;
            $ConsigRet->CodigoOrgao = $retencao->CodigoOrgao;
            $ConsigRet->CodigoUnidadeOrcamentaria = $retencao->CodigoUnidadeOrcamentaria;
            $ConsigRet->NumeroNotaPagamentoDeEmpenhoConsignacaoRetencao = $idNotaPegamentoRetencao;
            $ConsigRet->DataPagamento = $retencao->DataPagamento;
            $ConsigRet->TipoConsignacaoRetencaoPaga = $this->convertTipoRetencao($retencao->Tipo);
            $ConsigRet->ValorPago = $retencao->valorpago;
            $ConsigRet->NomeCredor = Helper::convertAndLimit("<![CDATA[".$retencao->NomeCredor, 264);
            $ConsigRet->CpfCnpjCredor = $retencao->CpfCnpjCredor;
            $ConsigRet->NaturezaCredor = strlen($retencao->CpfCnpjCredor) == 14 ? '1' : '2' ;
            $ConsigRet->CPF = $retencao->CPFResponsavel;

            $ConsigRet->LiquidacoesDePagamento = [];
            $Liquidacao = new stdClass();
            $Liquidacao->Identificador = $retencao->IdentificadorLiquidacao;
            $Liquidacao->NumeroLiquidacaoEmpenho = $retencao->ordem;
            $Liquidacao->AnoLiquidacaoEmpenho = $retencao->e69_anousu;
            $Liquidacao->ValorConsignadoRetidoLiquidacao = $retencao->valorpago;


            $Liquidacao->ContasPagadoras = [];
            $Conta = new stdClass();
            $Conta->Identificador = $retencao->IdentificadorRetencao;
            $Conta->ValorContaPagadora = $retencao->valorpago;
            $Conta->Banco = $contaBancaria->Banco;
            $Conta->Agencia = $contaBancaria->Agencia;
            $Conta->ContaBancaria = $contaBancaria->ContaBancaria;


            $ConsigRet->LiquidacoesDePagamento[] = (object) [
            'PagamentoDeEmpenhoConsignacaoRetencaoLiquidacaoPagamento' => $Liquidacao
            ];

            $Liquidacao->ContasPagadoras[] = (object) [
            'PagamentoDeEmpenhoConsignacaoRetencaoLiquidacaoPagamentoContaPagadora' => $Conta
            ];

            $EmpenhoConsignacaoRetencao->PagamentosDeEmpenhoConsignacoesRetencoes[] = (object) [
            'PagamentoDeEmpenhoConsignacaoRetencao' => $ConsigRet
            ];
        }

        $this->aDados = $EmpenhoConsignacaoRetencao;
    }

    private function getRetencoes()
    {
        $query = DB::table('retencaoreceitas')
        ->select(
            [
            'e23_sequencial as IdentificadorRetencao',
            'e21_sequencial as CodigoRetencao',
            'empempenho.e60_codemp as NumeroEmpenho',
            'empempenho.e60_anousu as AnoEmpenho',
            'orcdotacao.o58_orgao as CodigoOrgao',
            'orcdotacao.o58_unidade as CodigoUnidadeOrcamentaria',
            'empnota.e69_codnota',
            'empnota.e69_anousu',
            'pagordem.e50_codord as ordem',
            'k12_data as DataPagamento',
            'e32_sequencial as Tipo',
            'cgm.z01_nome as NomeCredor',
            'cgm.z01_cgccpf as CpfCnpjCredor',
            'cgmusu.z01_cgccpf as CPFResponsavel',
            'empnota.e69_codnota as IdentificadorLiquidacao',
            'empnota.e69_anousu as AnoLiquidacaoEmpenho'
            ]
        )
        ->selectRaw("
                sum(e23_valorretencao)
                over(partition by e23_retencaotiporec, e20_pagordem)
                as valorpago
            ")
        ->selectRaw("
                row_number() over(partition by e23_retencaotiporec, e20_pagordem) as row_number
            ")
        ->join(
            'retencaotiporec',
            'e21_sequencial',
            'e23_retencaotiporec'
        )
        ->join(
            'retencaotipocalc',
            'e32_sequencial',
            'e21_retencaotipocalc'
        )
        ->join(
            'retencaotiporeccgm',
            'e48_retencaotiporec',
            'e21_sequencial'
        )
        ->join(
            'retencaopagordem',
            'e23_retencaopagordem',
            'e20_sequencial'
        )
        ->join(
            'retencaoempagemov',
            'e27_retencaoreceitas',
            'e23_sequencial'
        )
        ->join(
            'empagemov',
            'e81_codmov',
            'e27_empagemov'
        )
        ->join(
            'pagordem',
            'e50_codord',
            'e20_pagordem'
        )
        ->join(
            'pagordemnota',
            'e71_codord',
            'e50_codord'
        )
        ->join(
            'empempenho',
            'e60_numemp',
            'e50_numemp'
        )
        ->join(
            'orcdotacao',
            function ($join) {
                $join->on(
                    'o58_anousu',
                    'e60_anousu'
                )
                 ->on('o58_coddot', 'e60_coddot');
            }
        )
        ->join(
            'empnota',
            'e69_codnota',
            'e71_codnota'
        )
        ->join(
            'db_usuacgm',
            'db_usuacgm.id_usuario',
            'e50_id_usuario'
        )
        ->join(
            'cgm as cgmusu',
            'cgmlogin',
            'cgmusu.z01_numcgm'
        )
        ->join(
            'cgm',
            'cgm.z01_numcgm',
            'retencaotiporeccgm.e48_cgm'
        )
        ->join(
            'retencaocorgrupocorrente',
            'e47_retencaoreceita',
            'e23_sequencial'
        )
        ->join(
            'corgrupocorrente',
            'k105_sequencial',
            'e47_corgrupocorrente'
        )
        ->join(
            'corrente',
            function ($join) {
                $join->on(
                    'k105_sequencial',
                    'e47_corgrupocorrente'
                )
                ->on(
                    'k105_id',
                    'k12_id'
                )
                ->on(
                    'k105_autent',
                    'k12_autent'
                )
                ->on(
                    'k105_data',
                    'k12_data'
                );
            }
        )
        ->whereIn(
            'e60_instit',
            $this->getListaInstituicoes()
        )
        ->where(
            'e60_anousu',
            $this->iAnoUsu
        )
        ->where(
            'k12_estorn',
            false
        )
        ->whereBetween(
            'k12_data',
            [$this->dtDataInicial,$this->dtDataFinal]
        );

        return $query->get()->filter(function ($retencao) {
            return $retencao->row_number == 1;
        });
    }

    private function getContaSlip($sequencialRetencao)
    {
        return DB::table("empagemovslips")
        ->select("slip.k17_credito as reduzido")
        ->join(
            "slipempagemovslips",
            "k108_empagemovslips",
            "k107_sequencial"
        )
        ->join(
            "slip",
            "slip.k17_codigo",
            "k108_slip"
        )
        ->where(
            "k107_retencao",
            $sequencialRetencao
        )
        ->first()->reduzido;
    }

    private function getContaOP($sequencialRetencao)
    {
        return DB::table("retencaoreceitas")
        ->select("c82_reduz as reduzido")
        ->join(
            "retencaopagordem",
            "e20_sequencial",
            "e23_retencaopagordem"
        )
        ->join(
            "conlancamord",
            "c80_codord",
            "e20_pagordem"
        )
        ->join(
            "conlancampag",
            "c82_codlan",
            "c80_codlan"
        )
        ->join(
            "conlancamdoc",
            "c71_codlan",
            "c82_codlan"
        )
        ->join(
            "conhistdoc",
            "c53_coddoc",
            "c71_coddoc"
        )
        ->where(
            "c53_tipo",
            30
        )
        ->where(
            "e23_sequencial",
            $sequencialRetencao
        )
        ->orderBy(
            "c80_data",
            "desc"
        )
        ->first()->reduzido;
    }

    private function getContaEmpenho($sequencialRetencao)
    {
        return DB::table("retencaoreceitas")
        ->select("c82_reduz as reduzido")
        ->join(
            "retencaopagordem",
            "e20_sequencial",
            "e23_retencaopagordem"
        )
        ->join(
            "pagordem",
            "e50_codord",
            "e20_pagordem"
        )
        ->join(
            "conlancamemp",
            "c75_numemp",
            "e50_numemp"
        )
        ->join(
            "conlancampag",
            "c82_codlan",
            "c75_codlan"
        )
         ->join(
             "conlancamdoc",
             "c71_codlan",
             "c82_codlan"
         )
        ->join(
            "conhistdoc",
            "c53_coddoc",
            "c71_coddoc"
        )
        ->where(
            "c53_tipo",
            30
        )
        ->where(
            "e23_sequencial",
            $sequencialRetencao
        )
        ->orderBy(
            "c75_data",
            "desc"
        )
        ->first()->reduzido;
    }

    private function getContaCredor($sequencialRetencao)
    {
        return DB::table("retencaoreceitas")
        ->select("e83_conta as reduzido")
        ->join(
            "retencaopagordem",
            "e20_sequencial",
            "e23_retencaopagordem"
        )
        ->join(
            "pagordem",
            "e50_codord",
            "e20_pagordem"
        )
        ->join(
            "empempenho",
            "e60_numemp",
            "e50_numemp"
        )
        ->join(
            'empageformacgm',
            'e28_numcgm',
            'empempenho.e60_numcgm'
        )
        ->join(
            'empagetipo',
            'e83_codtipo',
            'empageformacgm.e28_empagetipo'
        )
        ->where(
            "e23_sequencial",
            $sequencialRetencao
        )
        ->first()->reduzido;
    }

    private function getDadosContaBancaria($reduzido)
    {
        $contaBancaria = DB::table("conplanocontabancaria")
        ->select(
            'c56_reduz as reduzido',
            'db83_sequencial as contabancaria_sequencial',
            'db89_db_bancos as Banco',
            'db89_codagencia as Agencia',
            'db83_sequencial as ContaBancaria'
        )
        ->join(
            'contabancaria',
            'contabancaria.db83_sequencial',
            'conplanocontabancaria.c56_contabancaria'
        )
        ->join(
            'bancoagencia',
            'bancoagencia.db89_sequencial',
            'contabancaria.db83_bancoagencia'
        )
        ->where(
            "c56_reduz",
            $reduzido
        )
        ->where("c56_anousu", $this->iAnoUsu)
        ->first();

        return $contaBancaria;
    }

    private function getContaBancaria($sequencialRetencao)
    {
        //criamos objeto para retorno das informações
        $contaBancaria = new stdClass();
        $contaBancaria->reduzido = null;
        $contaBancaria->contabancaria_sequencial = null;
        $contaBancaria->Banco = null;
        $contaBancaria->Agencia = null;
        $contaBancaria->ContaBancaria = null;
        
        //Busca utilizando o slip
        $reduzido = $this->getContaSlip($sequencialRetencao);
        
        //Se não existe SLIP utilizamos a ultima conta de pagamento para a OP
        if (empty($reduzido)) {
            $reduzido = $this->getContaOP($sequencialRetencao);
        }
            
        //Se não existe pagamento para a OP utilizamos o ultimo pagamento para o empenho
        if (empty($reduzido)) {
            $reduzido = $this->getContaEmpenho($sequencialRetencao);
        }
            //Se não existe pagamento para o empenho utilizamos a ultima conta utilizada para o credor
        if (empty($reduzido)) {
            $reduzido = $this->getContaCredor($sequencialRetencao);
        }
        if (!empty($reduzido)) {
            $dadosContaBancaria = $this->getDadosContaBancaria($reduzido);

            //Se encontrar dados da conta bancaria retornamos as informacoes
            //Se não, é gerado log
            if (!empty($dadosContaBancaria)) {
                return $dadosContaBancaria;
            }
            $this->addLog("Não encontrado vinculo com conta bancária para o reduzido {$reduzido}");
        } else {
            $this->addLog(
                "Sequencial da Retenção: {$sequencialRetencao} - ".
                "Não encontrado nenhum vinculo com contas pagadoras para a OP, Empenho ou Credor"
            );
        }

        return $contaBancaria;
    }


    private function convertTipoRetencao($tipo)
    {
        switch ($tipo) {
            // Imposto de Renda Retido na Fonte (IRRF)
            case '1':
            case '2':
                return 6;
            // Contribuicoes Previdenciárias
            case '3':
            case '4':
            case '7':
                return 3;
            // Imposto Sobre Servicos
            case '5':
                return 7;
            // Outras Retencoes
            default:
                return 8;
        }
    }
}
