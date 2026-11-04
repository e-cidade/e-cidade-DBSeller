<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use BusinessException;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

class ArquivoPagamentoRestosPagarAnulacao extends ArquivoBase
{
    protected $sNomeArquivo = 'PagamentoRestosPagarAnulacao';

    public function gerarDados()
    {
        $estornos = $this->getEstornos();

        if ($estornos->isEmpty()) {
            throw new BusinessException('Não há dados para a competência informada.');
        }

        $data = new stdClass();
        $data->PagamentosRestosPagarAnulacoes = [];

        foreach ($estornos as $estorno) {
            $rpAnulacao = new stdClass();
            $rpAnulacao->Identificador = $estorno->NumeroNotaPagamento ;
            $rpAnulacao->CodigoUnidadeGestora = $this->sCodigoTribunal;
            $rpAnulacao->NumeroEmpenho = $estorno->NumeroEmpenho;
            $rpAnulacao->AnoEmpenho = $estorno->AnoEmpenho;
            $rpAnulacao->Competencia = $this->competencia;
            $rpAnulacao->NumeroAnulacao = $estorno->NumeroAnulacao;
            $rpAnulacao->DataAnulacao = $estorno->dataanulacao;
            $rpAnulacao->NumeroNotaPagamento = $estorno->origemlancamento;
            $rpAnulacao->AnoPagamentoRestosPagar = $this->iAnoUsu;
            $rpAnulacao->CPFResponsavel = $estorno->cpf;
            $rpAnulacao->Justificativa = Helper::convertAndLimit("<![CDATA[".$estorno->justificativa, 264);
            $rpAnulacao->Valor = $estorno->valor;
            $rpAnulacao->CodigoOrgao = $estorno->CodigoOrgao;
            $rpAnulacao->CodigoUnidadeOrcamentaria = $estorno->CodigoUnidadeOrcamentaria;

            $data->PagamentosRestosPagarAnulacoes[] = (object) ['PagamentoRestosPagarAnulacao' => $rpAnulacao];
        }

        $this->aDados = $data;
    }

    private function getEstornos()
    {
        $query = DB::table('empempenho')
            ->select(
                'empempenho.e60_codemp as NumeroEmpenho',
                'empempenho.e60_anousu as AnoEmpenho',
                'pagordem.e50_codord as NumeroAnulacao',
                'conlancam.c70_codlan as NumeroNotaPagamento',
                'orcdotacao.o58_orgao as CodigoOrgao',
                'orcdotacao.o58_unidade as CodigoUnidadeOrcamentaria',
                'pagordem.e50_codord'
            )
            ->distinct()
            ->selectRaw("sum(c70_valor) as Valor")
            ->selectRaw("min(c70_data) as DataAnulacao")
            ->selectRaw("string_agg(c72_complem, ' ') as Justificativa")
            ->selectRaw("min(z01_cgccpf) as CPF")
            ->selectRaw("
             (select origem_lancamento.c70_codlan
             from conlancam origem_lancamento
                  inner join conlancamord origem_conlancam_ordem
                    on origem_conlancam_ordem.c80_codlan = origem_lancamento.c70_codlan
                  inner join conlancamdoc origem_conlancam_doc on
                  origem_conlancam_doc.c71_codlan = origem_conlancam_ordem.c80_codlan
            where origem_conlancam_ordem.c80_codord = pagordem.e50_codord
              and origem_conlancam_doc.c71_coddoc in (35, 37)
              and origem_lancamento.c70_valor >= conlancam.c70_valor
              and origem_lancamento.c70_codlan < conlancam.c70_codlan
            order by origem_lancamento.c70_codlan desc limit 1) as OrigemLancamento
            ")  // Logica retirada da rotina de estorno do pagamento
            ->join('orcdotacao', function ($join) {
                $join->on(
                    'orcdotacao.o58_coddot',
                    'empempenho.e60_coddot'
                )
                    ->on(
                        'orcdotacao.o58_anousu',
                        'empempenho.e60_anousu'
                    );
            })
            ->join('empnota', 'empnota.e69_numemp', 'empempenho.e60_numemp')
            ->join('pagordemnota', 'pagordemnota.e71_codnota', 'empnota.e69_codnota')
            ->join('pagordem', 'pagordem.e50_codord', 'pagordemnota.e71_codord')
            ->join('db_usuacgm', 'db_usuacgm.id_usuario', 'pagordem.e50_id_usuario')
            ->join('cgm as cgmusu', 'cgmusu.z01_numcgm', 'db_usuacgm.cgmlogin')
            ->join('conlancamemp', 'conlancamemp.c75_numemp', 'empempenho.e60_numemp')
            ->join('conlancam', 'conlancam.c70_codlan', 'conlancamemp.c75_codlan')
            ->join('conlancamord', function ($join) {
                $join->on('conlancamord.c80_codlan', 'conlancam.c70_codlan')
                    ->on('conlancamord.c80_codord', 'pagordemnota.e71_codord');
            })
            ->join('conlancamcompl', 'c72_codlan', 'conlancam.c70_codlan')
            ->join('conlancamdoc', 'conlancamdoc.c71_codlan', 'conlancam.c70_codlan')
            ->whereIn('conlancamdoc.c71_coddoc', [36, 38])
            ->whereBetween('conlancam.c70_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->whereIn('empempenho.e60_instit', $this->getListaInstituicoes())
            ->whereRaw("exists (select 1 from empresto where empresto.e91_numemp = empempenho.e60_numemp)")
            ->groupBy(
                [
                'empempenho.e60_codemp',
                'empempenho.e60_anousu',
                'conlancam.c70_codlan',
                'empnota.e69_codnota',
                'empnota.e69_anousu',
                'orcdotacao.o58_orgao',
                'orcdotacao.o58_unidade',
                'pagordem.e50_codord'
                ]
            );

        return $query->get();
    }
}
