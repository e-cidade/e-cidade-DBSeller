<?php


namespace ECidade\Financeiro\Orcamento\Service;

use App\Domain\Financeiro\Orcamento\Models\FonteRecurso;
use DotacaoRepository;
use RecursoRepository;

class RecursoService
{

    /**
     * @param $codigoDotacao
     * @param $ano
     * @param $complemento
     * @return \ECidade\Financeiro\Orcamento\Recurso\Recurso
     * @throws \Exception
     */
    public static function identificaRecursoComplemento($codigoDotacao, $ano, $complemento)
    {
        $dotacao = DotacaoRepository::getDotacaoPorCodigoAno($codigoDotacao, $ano);
        $fonte = FonteRecurso::where('orctiporec_id', $dotacao->getRecurso())
            ->where('exercicio', $dotacao->getAno())
            ->first();
        $recursoDotacao = RecursoRepository::getRecursoPorCodigo($dotacao->getRecurso());
        $recurso = RecursoRepository::getRecursoPorCodigoRecursoAndComplemento(
            $fonte->gestao,
            $recursoDotacao->getRecurso(),
            $complemento,
            $fonte->exercicio
        );

        return $recurso;
    }
}
