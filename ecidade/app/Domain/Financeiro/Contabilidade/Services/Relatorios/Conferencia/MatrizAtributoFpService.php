<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\Conferencia;

use App\Domain\Financeiro\Contabilidade\Relatorios\Conferencia\MatrizAtributoFpPdf;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Essa classe é uma consistência do Atributo FP da MSC
 * Seu objetivo é identificar todas as contas mapeamentos
 */
class MatrizAtributoFpService
{
    public function emitir($exercicio)
    {
        $dados = $this->processar($exercicio);
        $pdf = new MatrizAtributoFpPdf();
        $pdf->headers($exercicio);
        $pdf->setDados($dados);
        return $pdf->imprimir();
    }

    private function processar($exercicio)
    {
        $dtInicial = "$exercicio-01-01";
        $dtFinal = "$exercicio-12-31";
        $sql = "
        select
               pcasp.conta,
               pcasp.informacoescomplementares,
               c60_estrut,
               c60_descr,
               c61_reduz,
               c61_anousu,
               c61_instit,
               nomeinst
        from contabilidade.balancete_verificacao_por_recurso (
               $exercicio, '$dtInicial', '$dtFinal', false, '{}'
             ) as bl
        join contabilidade.conplanoreduz on c61_reduz = bl.reduzido
             and c61_anousu = bl.exercicio
        join contabilidade.conplano on (c61_codcon,c61_anousu) = (c60_codcon, c60_anousu)
        join contabilidade.pcaspconplano on conplano_codigo = c60_codigo
        join contabilidade.pcasp on pcasp.id = pcasp_id
        join configuracoes.db_config on codigo = c61_instit
        where c61_anousu = {$exercicio}
          and uniao = 't'
          and informacoescomplementares ilike '%FP%'
          and c60_identificadorfinanceiro = 'N'
          and (saldo_anterior != 0 or
               saldo_debito != 0 or
               saldo_credito != 0 or
               saldo_final != 0)
        order by c61_instit, conta, c60_estrut
        ";

        $dados = DB::select($sql);

        if (count($dados) === 0) {
            throw new Exception('Não existem contas com movimento sem o atributo FP', 403);
        }

        return $this->organizaDados($dados);
    }

    private function organizaDados($dados)
    {
        $retorno = [];
        foreach ($dados as $dado) {
            if (!array_key_exists($dado->c61_instit, $retorno)) {
                $retorno[$dado->c61_instit] = (object)[
                    "codigo" => $dado->c61_instit,
                    "nome" => $dado->nomeinst,
                    "contas" => []
                ];
            }

            $retorno[$dado->c61_instit]->contas[] = (object)[
                "conta" => $dado->conta,
                "estrutural" => $dado->c60_estrut,
                "nome" => $dado->c60_descr,
                "reduzido" => $dado->c61_reduz
            ];
        }

        return $retorno;
    }
}
