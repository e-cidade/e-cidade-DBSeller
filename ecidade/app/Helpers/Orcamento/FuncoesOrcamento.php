<?php

use ECidade\Financeiro\Contabilidade\PlanoDeContas\Estrutural;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use ECidade\Financeiro\Orcamento\Repository\ElementoRepository;
use ECidade\Financeiro\Orcamento\Repository\FonteReceitaRepository;
use ECidade\V3\Extension\Registry;
use Illuminate\Support\Facades\DB;

/**
 * Retorna um split do estrutural até o nível dele
 * A função identifica e o estrutural é de despesa, receita ou do balancete de verificação
 *
 * @exemple
 * Estrutural: 411130310000000 retorno 41113031
 * Estrutural: 411130311050300 retorno 4111303110503
 *
 */
if (!function_exists('estruturalAteNivel')) {
    function estruturalAteNivel($natureza)
    {
        $estrutural = new EstruturalReceita($natureza);
        if ((strpos($natureza, '3') === 0) || (strpos($natureza, '1') === 0)) {
            $estrutural = new Estrutural($natureza);
        }

        return $estrutural->getEstruturalAteNivel();
    }
}

/**
 * retorna todos os estruturais analíticos da receita.
 */
if (!function_exists('receitasAnaliticas')) {
    function receitasAnaliticas($exercicio, $operador = '=')
    {
        if (Registry::has('estruturaisReceita')) {
            return Registry::get('estruturaisReceita');
        }

        $estruturaisReceita = [];
        $repository = new FonteReceitaRepository();
        $fontes = $repository->scopeAno($exercicio, $operador)
            ->scopeApenasFonteAnalitica()
            ->get();

        foreach ($fontes as $fonte) {
            $estruturaisReceita[] = $fonte->getFonte();
        }

        Registry::set('estruturaisReceita', array_unique($estruturaisReceita));

        return Registry::get('estruturaisReceita');
    }
}

if (!function_exists('elementosDespesa')) {
    function elementosDespesa($exercicio)
    {
        if (Registry::has('elementosDespesa')) {
            return Registry::get('elementosDespesa');
        }

        $elementos = [];
        $repository = new ElementoRepository();
        $fontes = $repository->scopeAno($exercicio)->get();

        foreach ($fontes as $fonte) {
            $elementos[] = $fonte->getElemento();
        }

        Registry::set('elementosDespesa', array_unique($elementos));
        return Registry::get('elementosDespesa');
    }
}


if (!function_exists('todosElementosDespesa')) {
    function todosElementosDespesa()
    {
        if (Registry::has('todosElementosDespesa')) {
            return Registry::get('todosElementosDespesa');
        }

        $elementos = [];
        $repository = new ElementoRepository();
        $fontes = $repository->scopeHasVinculoContabilidade()->get(['distinct o56_elemento']);

        foreach ($fontes as $fonte) {
            $elementos[] = $fonte->getElemento();
        }


        Registry::set('todosElementosDespesa', array_unique($elementos));
        return Registry::get('todosElementosDespesa');
    }
}


if (!function_exists('contasBalanceteVerificacao')) {
    function contasBalanceteVerificacao($exercicio, $operador = ' = ', $indicadorSuperavit = null)
    {
        $hash = 'contasBalanceteVerificacao';
        if (!empty($indicadorSuperavit)) {
            $hash = "contasBalanceteVerificacao#{$indicadorSuperavit}";
        }
        if (Registry::has($hash)) {
            return Registry::get($hash);
        }

        $where = " c62_anousu {$operador} {$exercicio} ";
        if (!empty($indicadorSuperavit)) {
            $where .= " and c60_identificadorfinanceiro = '$indicadorSuperavit' ";
        }

        $sql = "
        SELECT distinct p.c60_estrut AS estrutural
          FROM conplanoexe e
          INNER JOIN conplanoreduz r ON r.c61_anousu = c62_anousu
                AND r.c61_reduz = c62_reduz
          INNER JOIN conplano p ON r.c61_codcon = c60_codcon
                AND r.c61_anousu = c60_anousu
          LEFT OUTER JOIN consistema ON c60_codsis = c52_codsis
        WHERE $where
        order by 1
        ";

        $rs = db_query($sql);
        $contas = db_utils::makeCollectionFromRecord($rs, function ($dado) {
            return $dado->estrutural;
        });

        Registry::set($hash, $contas);

        return Registry::get($hash);
    }
}

if (!function_exists('getDesdobramentosReceita')) {
    /**
     * retorna os desdobramentos das fontes de recurso
     * @param $fonte
     * @param $exercicio
     * @return array
     */
    function getDesdobramentosReceita($fonte, $exercicio)
    {
        return DB::select("
            select o60_codfon as codigo_fonte,
                   o57_fonte as fonte,
                   o60_perc as percentual,
                   exists(
                   select 1
                    from contabilidade.conplanoorcamentoanalitica
                    join orcamento.orctiporec on o15_codigo = c61_codigo
                   where c61_codcon = o60_codfon
                     and c61_anousu = o60_anousu
                     and o15_tipo = 1
                  ) as livre,
                   o70_concarpeculiar as cp
              from orcfontes
              join orcfontesdes on o60_anousu = o57_anousu and o60_codfon = o57_codfon
              join orcamento.orcreceita on (o70_codfon, o70_anousu) = (o57_codfon, o57_anousu)
            where o57_anousu = {$exercicio} and o57_fonte like '{$fonte}%'
            order by o57_fonte;
        ");
    }
}

/**
 * Em vários relatórios realizava o mesmo procedimento, ai criei essa função que retorna a string com o nome
 * completo do recurso....
 *
 */
if (!function_exists('descricaoCompletaRecurso')) {
    /**
     * O Retorno vai depender do tipo informavado:
     * tipo = 1: gestão - descr recurso - cod complemento - descr complemento
     * tipo = 2: gestão - cod complemento - descr recurso
     * tipo = 3: gestão - cod complemento
     * tipo = 4: gestão - subrecurso - cod complemento
     * tipo = 5: gestão - subrecurso - cod complemento - descr recurso
     * tipo = 6: codigo_siconfi - cod complemento - descr recurso
     * tipo = 7: codigo_siconfi - cod complemento
     * tipo = 8: codigo_siconfi - subrecurso - cod complemento
     * tipo = 9: subrecurso - cod complemento
     * tipo = 10: codigo_siconfi - subrecurso - cod complemento - descr recurso
     *
     * @param int $codigoRecurso
     * @param int $exercicio
     * @param int $tipo
     * @return string
     */
    function descricaoCompletaRecurso($codigoRecurso, $exercicio, $tipo = 1)
    {
        $fonteRecurso = \App\Domain\Financeiro\Orcamento\Models\FonteRecurso::with('recurso')
            ->where('exercicio', $exercicio)
            ->where('orctiporec_id', $codigoRecurso)
            ->first();

        switch ($tipo) {
            case 1:
            default:
                return sprintf(
                    '%s - %s | %s - %s',
                    $fonteRecurso->gestao,
                    $fonteRecurso->descricao,
                    str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT),
                    $fonteRecurso->recurso->complemento->o200_descricao
                );
            case 2:
                return sprintf(
                    '%s - %s - %s',
                    $fonteRecurso->gestao,
                    str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT),
                    $fonteRecurso->descricao
                );
            case 3:
                return sprintf(
                    '%s - %s',
                    $fonteRecurso->gestao,
                    str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT)
                );
            case 4:
                return sprintf(
                    '%s - %s - %s',
                    $fonteRecurso->gestao,
                    $fonteRecurso->recurso->o15_recurso,
                    str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT)
                );
            case 5:
                return sprintf(
                    '%s - %s - %s - %s',
                    $fonteRecurso->gestao,
                    $fonteRecurso->recurso->o15_recurso,
                    str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT),
                    $fonteRecurso->descricao
                );
            case 6:
                return sprintf(
                    '%s - %s - %s',
                    $fonteRecurso->codigo_siconfi,
                    str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT),
                    $fonteRecurso->descricao
                );
            case 7:
                return sprintf(
                    '%s - %s',
                    $fonteRecurso->codigo_siconfi,
                    str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT)
                );
            case 8:
                return sprintf(
                    '%s - %s - %s',
                    $fonteRecurso->codigo_siconfi,
                    $fonteRecurso->recurso->o15_recurso,
                    str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT)
                );
            case 9:
                return sprintf(
                    '%s - %s',
                    $fonteRecurso->recurso->o15_recurso,
                    str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT)
                );
            case 10:
                return sprintf(
                    '%s - %s - %s - %s',
                    $fonteRecurso->codigo_siconfi,
                    $fonteRecurso->recurso->o15_recurso,
                    str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT),
                    $fonteRecurso->descricao
                );
        }
    }
}

if (!function_exists('apresentacaoRecurso')) {

    /**
     * @param $apresentacaoRecurso
     * @param $codigoRecurso id do recurso
     * @param $exercicio
     * @return string
     */
    function apresentacaoRecurso($apresentacaoRecurso, $codigoRecurso, $exercicio)
    {
        $tipo = 2;
        switch ($apresentacaoRecurso) {
            case 'fonteRecurso':
                $tipo = 2;
                break;
            case 'depara':
                $tipo = 5;
                break;
            case 'siconfi':
                $tipo = 6;
                break;
        }

        return descricaoCompletaRecurso($codigoRecurso, $exercicio, $tipo);
    }
}
