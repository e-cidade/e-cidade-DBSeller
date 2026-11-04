<?php

namespace App\Domain\Patrimonial\Material\Services;

use App\Domain\Patrimonial\Material\Models\LancamentoMovimentacao;
use App\Domain\Patrimonial\Material\Models\MaterialEstoque;
use App\Domain\Patrimonial\Material\Models\MaterialEstoqueItem;
use App\Domain\Patrimonial\Material\Models\MovimentacaoEstoqueItem;
use Carbon\Carbon;
use Exception;

class ImportarPlanilhaService
{
    public function incluir($dados, $date)
    {
        try {
            foreach ($dados as $dado) {
                $m70_query = "nextval('matestoque_m70_codigo_seq') as nxt";
                $m70_codigo = MaterialEstoque::selectRaw($m70_query)->value('nxt');
                MaterialEstoque::create([
                    'm70_codigo' => $m70_codigo,
                    'm70_coddepto' => $dado->departamento_1,
                    'm70_codmatmater' => $dado->codigo_material_2,
                    'm70_valor' => $dado->valor_total_5,
                    'm70_quant' => $dado->qtd_entrada_3
                ]);

                $m80_query = "nextval('matestoqueini_m80_codigo_seq') as nxt";
                $m80_codigo = LancamentoMovimentacao::selectRaw($m80_query)->value('nxt');
                LancamentoMovimentacao::create([
                    'm80_codigo' => $m80_codigo,
                    'm80_login' => db_getsession("DB_id_usuario"),
                    'm80_data' => $date,
                    'm80_hora' => Carbon::now()->format('H:i:s'),
                    'm80_obs' => $dado->observacao_6,
                    'm80_codtipo' => $dado->cod_tipo_7,
                    'm80_coddepto' => $dado->departamento_1
                ]);

                if (isset($m70_codigo) && trim($m70_codigo) != "") {
                    $m71_query = "nextval('matestoqueitem_m71_codlanc_seq') as nxt";
                    $m71_codlanc = MaterialEstoqueItem::selectRaw($m71_query)->value('nxt');
                    MaterialEstoqueItem::create([
                        'm71_codlanc' => $m71_codlanc,
                        'm71_codmatestoque' => $m70_codigo,
                        'm71_data' => $date,
                        'm71_valor' => $dado->valor_total_5,
                        'm71_quant' => $dado->qtd_entrada_3,
                        'm71_quantatend' => '0'
                    ]);
                }

                $m82_query = "nextval('matestoqueinimei_m82_codigo_seq') as nxt";
                $m82_codigo = MovimentacaoEstoqueItem::selectRaw($m82_query)->value('nxt');
                MovimentacaoEstoqueItem::create([
                    'm82_codigo' => $m82_codigo,
                    'm82_matestoqueitem' => $m71_codlanc,
                    'm82_matestoqueini' => $m80_codigo,
                    'm82_quant' => $dado->qtd_entrada_3
                ]);
            }
        } catch (Exception $e) {
            throw new Exception("Ocorreu um Erro ao importar os dados Planilha: ", $e->getMessage());
        }

        $mensagem = "Dados da Planilha importado com Sucesso!";
        return $mensagem;
    }
}
