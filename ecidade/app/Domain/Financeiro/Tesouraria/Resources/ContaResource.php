<?php

namespace App\Domain\Financeiro\Tesouraria\Resources;

use App\Domain\Financeiro\Tesouraria\Models\Saltes;
use Illuminate\Database\Eloquent\Collection;

class ContaResource
{

    public static function toArray(Collection $contas)
    {
        $dados = [];

        $contas->each(function ($conta) use (&$dados) {
            /**
             * @var Saltes $conta
             */
            $obj = self::montaConta($conta);
            $obj->contaContabil = null;
            $obj->contaExtra = null;
            $obj->contrapartida = null;

            if (!empty($conta->c61_reduz)) {
                $obj->contaContabil = self::montaContaContabil($conta);
            }

            if (array_key_exists('contaExtra', $conta->getRelations())) {
                $contaExtra = $conta->getRelations()['contaExtra'];
                if (!is_null($contaExtra)) {
                    $obj->contaExtra = self::montaContaExtra($contaExtra);
                }
            }
            if (array_key_exists('contaContrapartida', $conta->getRelations())) {
                $contrapartida = $conta->getRelations()['contaContrapartida'];
                if (!is_null($contrapartida)) {
                    $obj->contrapartida = self::montaContrapartida($contrapartida);
                }
            }

            $dados[] = $obj;
        });

        return $dados;
    }

    protected static function montaConta(Saltes $conta)
    {
        return (object)[
            "conta" => $conta->k13_conta,
            "nome" => $conta->k13_descr,
            "saldoImplantacao" => $conta->k13_saldo,
            "identificacao" => $conta->k13_ident,
            "valorAtual" => $conta->k13_vlratu,
            "dataAtualizacao" => $conta->k13_datvlr,
            "dataLimite" => $conta->k13_limite, // se informado, desativa a conta da data para frente
            "dataImplantacao" => $conta->k13_dtimplantacao,
            "outrosDados" => json_decode($conta->k13_outrosdados),
        ];
    }

    protected static function montaContaContabil($conta)
    {
        return (object)[
            'estrural' => $conta->c60_estrut,
            'codcon' => $conta->c60_codcon,
            'nome' => $conta->c60_descr,
            'reduzido' => $conta->c61_reduz,
            'instituicao' => $conta->c61_instit,
            'recurso' => self::montaRecurso($conta),
        ];
    }

    protected static function montaRecurso($conta)
    {
        return (object)[
            'codigo' => $conta->orctiporec_id,
            'gestao' => $conta->gestao,
            'siconfi' => $conta->codigo_siconfi,
            'subrecurso' => $conta->o15_recurso,
            'complemento' => $conta->o15_complemento,
            'nome' => $conta->descricao,
        ];
    }

    protected static function montaContaExtra($contaExtra)
    {
        return self::montaConta($contaExtra->conta);
    }

    private static function montaContrapartida($contrapartida)
    {
        return self::montaConta($contrapartida->conta);
    }
}
