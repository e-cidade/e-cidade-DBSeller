<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Financeiro\Planejamento\Services;

use App\Domain\Financeiro\Orcamento\Models\FonteReceita;
use App\Domain\Financeiro\Planejamento\Builder\EstimativaReceitaBuilder;
use App\Domain\Financeiro\Planejamento\Builder\ProjecaoReceitaBuilder;
use App\Domain\Financeiro\Planejamento\Mappers\ProjecaoReceitaMapper;
use App\Domain\Financeiro\Planejamento\Models\EstimativaReceita;
use App\Domain\Financeiro\Planejamento\Models\FatorCorrecaoReceita;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Projecao\CalculaProjecaoRequest;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ProjecaoReceitaService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class ProjecaoReceitaService extends ProjecaoService
{
    /**
     * @var Collection
     */
    protected $fatoresCorrecao = [];

    /**
     * @var array
     */
    protected $filtrarReduzidos = [];

    public function porRequest(CalculaProjecaoRequest $request)
    {
        parent::porRequest($request);

        if ($request->has('reduzidos')) {
            $this->filtrarReduzidos = $request->get('reduzidos');
        }

        $this->fatoresCorrecao = $this->planejamento->fatorCorrecaoReceita;
        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function recalcular()
    {
        $this->montaQuery()->delete();
        $this->calcular();

        return $this->get();
    }

    /**
     *
     * @return array
     * @throws Exception
     */
    public function get()
    {
        $this->seNaoExistirEstimativaCalcula();

        $estimativas = $this->buscaEstimativas();
        return $this->montaArvoreEstrutural($estimativas);
    }

    /**
     * @param bool $filtrarInclusaoProjetadaExercicioAnterior se deve filtrar as inclusões projetadas
     * @return \Illuminate\Database\Concerns\BuildsQueries
     */
    private function montaQuery($filtrarInclusaoProjetadaExercicioAnterior = true)
    {
        $instituicao = $this->instituicao;
        return EstimativaReceita::where('planejamento_id', '=', $this->planejamento->pl2_codigo)
            ->when($filtrarInclusaoProjetadaExercicioAnterior, function ($query) {
                $query->where('inclusaomanual', '=', 'f');
            })
            ->when(!$instituicao->prefeitura, function ($query) use ($instituicao) {
                $query->where('instituicao_id', '=', $instituicao->codigo);
            });
    }

    private function seNaoExistirEstimativaCalcula()
    {
        $estimativa = $this->montaQuery(false)->first();
        if (is_null($estimativa)) {
            $this->calcular();
        }
    }

    /**
     * Executa o calculo das receitas
     * @throws Exception
     */
    protected function calcular()
    {
        $saldoReceita = $this->executaReceitasaldo();
        $projecoes = $this->calculaProjecao($saldoReceita);

        $projecoes->each(function (ProjecaoReceitaMapper $projecao) {
            $service = new EstimativaReceitaService();
            $service->salvarProjecao($projecao);
        });
    }

    /**
     * Retorna os valores das receitas do exercício da sessão
     * @return array
     */
    private function executaReceitasaldo()
    {
        $exercicio = $this->anoSessao;
        $dataInicial = "{$this->anoSessao}-01-01";
        $dataFinal = $this->dataUsuario;

        $where = ["o70_anousu = {$exercicio}"];
        if (!$this->instituicao->prefeitura) {
            $where[] = "o70_instit in ({$this->instituicao->codigo})";
        }
        // garante que as fontes de receita existe no ano inicial do plano de contas
        $where[] = "exists(
            select 1
              from orcfontes fonte
             where fonte.o57_anousu = {$this->planejamento->pl2_ano_inicial}
               and fonte.o57_codfon = orcfontes.o57_codfon
        )";

        $filtroPrincipal = implode(' and ', $where);

        // Esse filtro é utilizado no balancete da receita, portanto mantive aqui
        $filtroBalancete = '(previsao_atualizada <> 0 or saldo_a_arrecadar <> 0)';
        // Já vou deixar preparado essa variável caso seja necessário informar outra condição
        $filtroAdicional = [$filtroBalancete];
        if (!empty($this->filtrarReduzidos)) {
            $reduzidos = implode(', ', $this->filtrarReduzidos);
            $filtroAdicional[] = "o70_codrec in ($reduzidos)";
        }

        $sql = "
        with matriz_saldo_receita as (
            select fc_receitasaldo_array(o70_anousu,o70_codrec,3,'{$dataInicial}','{$dataFinal}') as valores_receita,
                   o57_fonte,
                   o57_descr,
                   orcreceita.*
              from orcreceita
              join orcfontes on (o57_codfon, o57_anousu) = (o70_codfon, o70_anousu)
              where {$filtroPrincipal}
        ), saldo_receita as (
            select o57_fonte,
                   o57_descr,
                   valores_receita[2] as previsao_inicial,
                   valores_receita[3] as previsao_adicional,
                   valores_receita[4] as previsao_atualizada,
                   valores_receita[5] as arrecadado_anterior,
                   valores_receita[6] as arrecadado_periodo,
                   valores_receita[7] as saldo_a_arrecadar,
                   valores_receita[8] as arrecadado_acumulado,
                   valores_receita[9] as saldo_adicional_anterior_periodo,
                   o70_anousu,
                   o70_codrec,
                   o70_codfon,
                   o70_codigo,
                   o70_instit,
                   o70_concarpeculiar,
                   o70_orcorgao,
                   o70_orcunidade,
                   o70_esferaorcamentaria
              from matriz_saldo_receita
              order by o57_fonte
        )
            select *
              from saldo_receita
        ";
        if (!empty($filtroAdicional)) {
            $sql .= "where " . implode(' and ', $filtroAdicional);
        }

        return DB::select($sql);
    }

    /**
     * @param array $saldoReceita
     * @return Collection
     */
    private function calculaProjecao(array $saldoReceita)
    {
        $projecoes = collect([]);
        foreach ($saldoReceita as $dadosReceita) {
            $valorBase = $this->getValorBase($dadosReceita);
            $builder = new ProjecaoReceitaBuilder();
            $builder->addFromStdClass($dadosReceita)
                ->addValorBase($valorBase)
                ->addPlanejamento($this->planejamento)
                ->addIsManual(false);

            $mapper = $builder->build();
            $this->calculaInflator($mapper);

            $projecoes->push($mapper);
        }

        return $projecoes;
    }

    /**
     * Retorna o valor base o calculo das projeções
     * @param $dadosReceita
     * @return float
     * @todo é nesse metodo que devemos validar o parâmetro da "Base de Cálculo".
     * @todo nesse ano só iremos implementar a Previsão Atualizada
     */
    private function getValorBase($dadosReceita)
    {
        return $dadosReceita->previsao_atualizada;
    }

    /**
     * @param ProjecaoReceitaMapper $mapper
     * @return ProjecaoReceitaMapper
     */
    private function calculaInflator(ProjecaoReceitaMapper $mapper)
    {
        if ($this->fatoresCorrecao->isEmpty()) {
            return $this->replicaValorBase($mapper);
        }

        // deve retornar os fatores configurado para a fonte de recurso.
        $fatores = $this->fatoresCorrecao->filter(function (FatorCorrecaoReceita $fator) use ($mapper) {
            if ($fator->orcfontes_id === $mapper->codigoFonte) {
                return $fator;
            }
        });

        if ($fatores->isEmpty()) {
            return $this->replicaValorBase($mapper);
        }

        $valor = $mapper->valorBase;
        $valores = [];
        $exercicios = range($mapper->planejamento->pl2_ano_inicial, $mapper->planejamento->pl2_ano_final);

        foreach ($exercicios as $exercicio) {
            $fator = $fatores->filter(function (FatorCorrecaoReceita $fator) use ($exercicio) {
                return $fator->exercicio === $exercicio;
            })->shift();

            $valores[$exercicio] = $this->buidValor($exercicio, $valor);

            if ($fator instanceof FatorCorrecaoReceita) {
                if ($fator->deflator) {
                    $fator->percentual *= -1;
                }

                $valor = round($valor * (1 + ($fator->percentual / 100)), $this->precisaoRound);
                $valores[$fator->exercicio] = $this->buidValor($fator->exercicio, $valor);
            }
        }

        $mapper->valoresProjetados = $valores;
        return $mapper;
    }

    /**
     * se não foi configurado o fator de correção, aplica o valor base para todos exercícios do planejamento
     * @param ProjecaoReceitaMapper $mapper
     * @return ProjecaoReceitaMapper
     */
    private function replicaValorBase(ProjecaoReceitaMapper $mapper)
    {
        $valores = [];
        foreach ($this->planejamento->execiciosPlanejamento() as $exercicio) {
            $valores[$exercicio] = $this->buidValor($exercicio, $mapper->valorBase);
        }
        $mapper->valoresProjetados = $valores;
        return $mapper;
    }

    /**
     * Busca as estimativas processadas.
     * @return array
     */
    private function buscaEstimativas()
    {
        return DB::select("
        SELECT (select json_agg(
                          json_build_object(
                            'ano', x.pl10_ano,
                            'valor', x.pl10_valor
                          )
                       ) as valores
                  from (select valores.pl10_ano, valores.pl10_valor
                         from planejamento.valores
                        where pl10_origem = 'RECEITA'
                          and pl10_chave = estimativareceita.id
                        order by pl10_ano
                     ) as x
              ) as valores,
               (select json_agg(
                          json_build_object(
                            'ano', x.exercicio,
                            'percentual', x.percentual,
                            'deflator',  x.deflator
                          )
                       ) as valores
                  from (select exercicio, percentual, deflator
                         from planejamento.fatorcorrecaoreceita
                        where fatorcorrecaoreceita.planejamento_id = estimativareceita.planejamento_id
                          and orcfontes_id = o57_codfon
                        order by exercicio
                      ) as x
               ) as inflatores,
              estimativareceita.*,
              orcfontes.o57_fonte as fonte,
              orcfontes.o57_descr as descricao_fonte,
              orcorgao.o40_descr as descricao_orgao,
              orcunidade.o41_descr as descricao_unidade,
              concarpeculiar.c58_descr as caracteristica_peculiar,
              db_config.nomeinst as nome_instituicao,
              fonterecurso.gestao as fonte_recurso,
              fonterecurso.descricao as recurso,
              o200_sequencial as codigo_complemento,
              o200_descricao as complemento,
              case c60_identificadoresultadoprimario
                 when 1
                   then 'Financeiro'
                 when 2
                   then 'Primário'
                 when 3
                   then 'Primária Obrigatória'
                 when 4
                   then 'Primária Discricionária'
                 else  'Não se Aplica'
              end as identificador_resultado,
              case esferaorcamentaria
                when 10
                  then 'F - Orçamento Fiscal'
                when 20
                  then 'S - Orçamento da Seguridade Social'
                when 30
                  then 'I - Orçamento de Investimento'
                else 'Não se Aplica'
              end as esfera_orcamentaria
         FROM planejamento.estimativareceita
        join orcamento.orcunidade on (o41_anousu, o41_orgao, o41_unidade) = (anoorcamento, orcorgao_id, orcunidade_id)
        join orcamento.orcorgao on (o40_anousu, o40_orgao) = (anoorcamento, orcorgao_id)
        join contabilidade.concarpeculiar on c58_sequencial = estimativareceita.concarpeculiar_id
        join configuracoes.db_config on db_config.codigo = estimativareceita.instituicao_id
        join orcamento.orcfontes on (o57_codfon, o57_anousu) = (orcfontes_id, anoorcamento)
        join orcamento.orctiporec on orctiporec.o15_codigo = estimativareceita.recurso_id
        join orcamento.fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
             and fonterecurso.exercicio = estimativareceita.anoorcamento
        join complementofonterecurso on complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
        join contabilidade.conplanoorcamento on (c60_codcon, c60_anousu) = (orcfontes_id, anoorcamento)
        where estimativareceita.planejamento_id = {$this->planejamento->pl2_codigo}
        order by fonte
        ");
    }

    /**
     * Processa recursivamente a arvore de estruturais
     * @param $estimativas
     * @return array
     * @throws Exception
     */
    private function montaArvoreEstrutural($estimativas)
    {
        $anoPrevisao = $this->planejamento->pl2_ano_inicial;
        $fontesReceitas = FonteReceita::where('o57_anousu', '=', $anoPrevisao)->get();

        $receitas = [];
        $execicios = $this->planejamento->execiciosPlanejamento();

        $valoresSintetico = [];
        foreach ($execicios as $ano) {
            $valoresSintetico[] = $this->buidValor($ano, 0);
        }

        foreach ($estimativas as $estimativa) {
            $estrutural = new EstruturalReceita($estimativa->fonte);
            $nivel = $estrutural->getNivel();
            $fonte = $estrutural->getEstrutural();

            $estruturalPaiDesdobramento = null;
            $temDesdobramento = false;

            if (FonteReceita::hasDesdobramento($estimativa->orcfontes_id, $estimativa->anoorcamento)) {
                $estruturalPaiDesdobramento = $estrutural->getCodigoEstruturalPai();
                $temDesdobramento = true;
            }

            $builder = new EstimativaReceitaBuilder();
            $receita = $builder->buildAnalitico($estimativa, $estrutural, $temDesdobramento);
            $cp = $estimativa->concarpeculiar_id;
            $hash = "$fonte#$cp";
            $receitas[$hash] = $receita;

            while ($nivel != 1) {
                $estrutural = new EstruturalReceita($estrutural->getCodigoEstruturalPai());

                $fonte = $estrutural->getEstrutural();
                $nivel = $estrutural->getNivel();

                if (!array_key_exists($fonte, $receitas)) {
                    // localiza a fonte de receita para setar a descrição
                    $fonteReceita = $fontesReceitas->filter(function (FonteReceita $fonteReceita) use ($fonte) {
                        return $fonteReceita->o57_fonte === $fonte;
                    })->shift();

                    if (is_null($fonteReceita)) {
                        $msg = sprintf(
                            "Não foi encontrado a Natureza de Receita: %s. Acesse: %s",
                            $fonte,
                            "DB:FINANCEIRO > Contabilidade > Cadastros > Plano de Contas Orçamentário > Inclusão"
                        );
                        throw new Exception($msg);
                    }

                    $builder = new EstimativaReceitaBuilder();
                    $receitas[$fonte] = $builder->buildSintetico($estrutural, $valoresSintetico);
                    $receitas[$fonte]->descricao_fonte = $fonteReceita->o57_descr;
                }

                $receitas[$fonte]->valor_base += $receita->valor_base;
                foreach ($execicios as $execicio) {
                    $propriedade = "valor_{$execicio}";
                    $valor = $receitas[$fonte]->{$propriedade} + $receita->{$propriedade};
                    $receitas[$fonte]->{$propriedade} = $valor;
                }

                if (!is_null($estruturalPaiDesdobramento)
                    && $estruturalPaiDesdobramento === $estrutural->getEstruturalComMascara()
                    && count($receitas[$fonte]->contasDesdobramento) === 0) {
                    $estruturalAteNivel = $estrutural->getEstruturalAteNivel();
                    $receitas[$fonte]->contasDesdobramento = $this->getDesdobramento($estruturalAteNivel);
                }

                // Na receita analitica indexa as contas que são pai da mesma
                $receita->fontesPai[] = $fonte;
            }
        }

        ksort($receitas);
        return $receitas;
    }

    /**
     * valida se a fonte possui desdobramento
     * @param $codigo
     * @param $exercicio
     * @return bool
     */
    public function temDesdobramento($codigo, $exercicio)
    {
        return collect(DB::select(
            "select 1 from orcamento.orcfontesdes where o60_codfon = {$codigo} and o60_anousu = {$exercicio}"
        ))->count() > 0;
    }

    /**
     * retorna os desdobramentos das fontes de recurso
     * @param $fonte
     * @return array
     */
    private function getDesdobramento($fonte)
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
                   concarpeculiar_id as cp
              from orcfontes
              join orcfontesdes on o60_anousu = o57_anousu and o60_codfon = o57_codfon
              join estimativareceita on (orcfontes_id, anoorcamento) = (o57_codfon, o57_anousu)
            where o57_anousu = {$this->planejamento->pl2_ano_inicial} and o57_fonte like '{$fonte}%'
            order by o57_fonte;
        ");
    }
}
