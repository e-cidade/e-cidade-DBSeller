<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Models\ConplanoexeConciliaRp;
use App\Domain\Financeiro\Orcamento\Models\Elemento;
use Exception;

class MapeamentoEmpenhoRPManualService
{

    public function get($exercicio)
    {
        return ConplanoexeConciliaRp::query()
            ->where('c162_exercicio', $exercicio)
            ->get()
            ->map(function (ConplanoexeConciliaRp $concilia) {
                $elemento = $concilia->getElemento();

                return (object)[
                    'exercicio' => $concilia->c162_exercicio,
                    'reduzido' => $concilia->c162_reduzido,
                    'ai' => $concilia->c162_ai,
                    'funcao' => $concilia->c162_funcao,
                    'subfuncao' => $concilia->c162_subfuncao,
                    'elemento' => $elemento->o56_elemento,
                    'recurso' => $concilia->c162_recurso,
                    'valor' => $concilia->c162_valor,
                    'natureza' => $concilia->c162_natureza,
                    'codigo' => $concilia->c162_codigo,
                ];
            });
    }

    /**
     * @param array $dados
     * @return int
     * @throws Exception
     */
    public function salvar(array $dados)
    {
        $elemento = Elemento::select('o56_codele')
            ->where('o56_elemento', $dados['elemento'])
            ->where('o56_anousu', $dados['ai'])
            ->first();

        if (is_null($elemento)) {
            throw new Exception("Não foi encontrado o elemento {$dados['elemento']} no exercício {$dados['ai']}", 403);
        }

        $concilia = new ConplanoexeConciliaRp();
        if (!empty($dados['codigo'])) {
            $concilia = ConplanoexeConciliaRp::find($dados['codigo']);
        }
        $concilia->c162_exercicio = $dados['exercicio'];
        $concilia->c162_reduzido = $dados['reduzido'];
        $concilia->c162_ai = $dados['ai'];
        $concilia->c162_funcao = $dados['funcao'];
        $concilia->c162_subfuncao = $dados['subfuncao'];
        $concilia->c162_elemento = $elemento->o56_codele;
        $concilia->c162_recurso = $dados['recurso'];
        $concilia->c162_valor = $dados['valor'];
        $concilia->c162_natureza = $dados['natureza'];

        $concilia->save();
        return $concilia->c162_codigo;
    }

    public function deletar($codigo)
    {
        ConplanoexeConciliaRp::query()->where('c162_codigo', $codigo)->delete();
    }
}
