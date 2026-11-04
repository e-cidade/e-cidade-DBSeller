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

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Financeiro\Planejamento\Models\BaseCalculoProjecao;
use App\Domain\Financeiro\Planejamento\Models\CronogramaDesembolsoDespesa;
use App\Domain\Financeiro\Planejamento\Models\DetalhamentoDespesa;
use App\Domain\Financeiro\Planejamento\Models\DetalhamnentoPlanejamento;
use App\Domain\Financeiro\Planejamento\Models\FatorCorrecaoDespesa;
use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\OrgaoPrograma;
use App\Domain\Financeiro\Planejamento\Models\ProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Projecao\Despesa\CalculaProjecaoRequest;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use JSON;
use stdClass;

// phpcs:disable
include(modification("libs/db_liborcamento.php"));
// phpcs:enable

/**
 * Class ProjecaoDespesaService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class ProjecaoDespesaService extends ProjecaoService
{
    /**
     * 1 - Previsão Atualizada
     * 2 - Empenhado
     * 3 - Liquidado
     * 4 - Pago mo execício até o presente momento
     * @var integer
     */
    protected $formaRecalculo;

    /**
     * @throws Exception
     * @todo esse metodo pode ser usado para criar o plano, como um start
     */
    public function calcular()
    {
//        ProgramaEstrategico::where('pl9_planejamento', '=', $this->planejamento->pl2_codigo)->delete();
//
//        $valoresDotacao = $this->buscarValores();
//        $valoresDotacao = $this->calculaProjecao($valoresDotacao);
//
//        $valoresAgrupados = $this->agrupaValores($valoresDotacao);
//        $this->persisteCalculo($valoresAgrupados);
    }


    /**
     * @throws Exception
     */
    public function getProjecao()
    {
        $projecoesPrograma = ProgramaEstrategico::where('pl9_planejamento', '=', $this->planejamento->pl2_codigo)
            ->when(!$this->instituicao->prefeitura, function ($query) {
                $query->where('o58_instit', '=', $this->instituicao->codigo);
            })
            ->get();

        if ($projecoesPrograma->count() === 0) {
            $this->calcular();
        }
        /**
         * @todo esta fixo para trabalhar apenas PPA e LDO
         */
        $filtros = ["pl9_planejamento = {$this->planejamento->pl2_codigo}"];
        if (!$this->instituicao->prefeitura) {
            $filtros[] = " pl9_instituicao = {$this->instituicao->codigo}";
        }

        $where = implode(' and ', $filtros);

        $sql = "
            select
                   pl9_codigo as id_programa,
                   pl9_planejamento as planejamento,
                   pl9_orcprograma as orcprograma,
                   pl9_anoorcamento as anoorcamento,
                   pl12_codigo as id_iniciativa,
                   pl12_orcprojativ as orcprojativ,
                   pl12_valorbase as valorbase,
                   lpad(o54_programa, 4, 0) ||'.'|| lpad(o55_projativ, 4, 0) as estrutural,
                   o55_descr as descricao_iniciativa,
                   o54_descr as descricao_programa,

                   ( select json_agg(
                              json_build_object(
                                'ano', x.pl10_ano,
                                'valor', x.pl10_valor
                              )
                            ) as valores
                       from (select valores.pl10_ano,
                                    valores.pl10_valor
                               from planejamento.valores
                              where pl10_origem = 'INICIATIVA'
                                and pl10_chave = pl12_codigo
                              order by pl10_ano
                             ) as x
                   ) as valores
              from planejamento.programaestrategico
              join planejamento.iniciativaprojativ
                  on iniciativaprojativ.pl12_programaestrategico = programaestrategico.pl9_codigo
              join orcamento.orcprograma on (o54_anousu, o54_programa) = (pl9_anoorcamento, pl9_orcprograma)
              join orcamento.orcprojativ on (o55_anousu, o55_projativ) = (pl12_anoorcamento, pl12_orcprojativ)
            where {$where}
            order by  id_programa, id_iniciativa
        ";

        return \db_utils::makeCollectionFromRecord(db_query($sql), function ($dado) {
            $dado->valores = JSON::create()->parse($dado->valores);
            return $dado;
        });
    }

    /**
     * @param stdClass $programa
     * @return ProgramaEstrategico|Builder|Model|null
     */
    public function criarProgramaSeNaoExiste(stdClass $programa)
    {
        $programaEstrategico = ProgramaEstrategico::query()
            ->where('pl9_planejamento', $this->planejamento->pl2_codigo)
            ->where('pl9_orcprograma', $programa->programa)
            ->first();

        if (is_null($programaEstrategico)) {
            $programaEstrategico = new ProgramaEstrategico();
            $programaEstrategico->planejamento()->associate($this->planejamento);
            $programaEstrategico->pl9_orcprograma = $programa->programa;
            $programaEstrategico->pl9_anoorcamento = $this->planejamento->pl2_ano_inicial;
            $programaEstrategico->pl9_valorbase = 0;
            $programaEstrategico->save();
        }

        // cria uma coleção com os códigos dos órgão vinculado ao programa
        $orgaosPrograma = $programaEstrategico->orgaos->map(function ($data) {
            return $data->pl27_orcorgao;
        });

        // transforma a lista de orgãos em collection e remove usando o diff todos que já estão vínculados ao programa
        collect($programa->orgaos)
            ->diff($orgaosPrograma)->each(function ($codigo) use ($programaEstrategico) {
                $orgao = new OrgaoPrograma();
                $orgao->programaEstrategico()->associate($programaEstrategico);
                $orgao->pl27_orcorgao = $codigo;
                $orgao->pl27_anoorcamento = $this->planejamento->pl2_ano_inicial;
                $orgao->save();
            });

        return $programaEstrategico;
    }


    /**
     * Busca os orgaos que o usuário possui permissão no ano informado
     * @return array
     * @throws Exception
     */
    private function getOrgaosLiberadoUsuario()
    {
        return Usuario::getOrgaosLiberadoUsuario($this->idUsuario, $this->anoSessao);
    }

    /**
     * Busca os valores das dotações agrupando por programa e ação(iniciativa)
     * @return array
     */
    private function buscarValoresDetalhamento()
    {
        return $this->calulaValoresDetalhado();
    }

    private function agrupaValores(array $dadosDotacao)
    {
        $valoresAgrupados = [];
        foreach ($dadosDotacao as $dadoDotacao) {
            $idPrograma = "{$dadoDotacao->instituicao}#{$dadoDotacao->programa}";
            if (!array_key_exists($idPrograma, $valoresAgrupados)) {
                $valoresAgrupados[$idPrograma] = $this->createObjectPrograma($dadoDotacao);
            }

            $idAcao = $dadoDotacao->acao;
            if (!array_key_exists($idAcao, $valoresAgrupados[$idPrograma]->iniciativas)) {
                $valoresAgrupados[$idPrograma]->iniciativas[$idAcao] = $this->createObjectAcao($dadoDotacao);
            }

            $valoresAgrupados[$idPrograma]->valorBase += $dadoDotacao->valor;
            $valoresAgrupados[$idPrograma]->iniciativas[$idAcao]->valorBase += $dadoDotacao->valor;

            $acao = $valoresAgrupados[$idPrograma]->iniciativas[$idAcao];

            foreach ($dadoDotacao->valoresPrevisto as $ano => $valorPrevisto) {
                if (!array_key_exists($ano, $acao->valoresPrevisto)) {
                    $acao->valoresPrevisto[$ano] = 0;
                }
                $acao->valoresPrevisto[$ano] += $valorPrevisto;
            }
        }

        foreach ($valoresAgrupados as $programa) {
            foreach ($programa->iniciativas as $iniciativas) {
                foreach ($iniciativas->valoresPrevisto as $ano => $valorPrevisto) {
                    if (!array_key_exists($ano, $programa->valoresPrevisto)) {
                        $programa->valoresPrevisto[$ano] = 0;
                    }
                    $programa->valoresPrevisto[$ano] += $valorPrevisto;
                }
            }
        }

        return $valoresAgrupados;
    }


    private function createObjectPrograma(StdClass $valor)
    {
        return (object)[
            'codigo' => $valor->programa,
            'anoExercicio' => $this->anoSessao,
            'instituicao' => $valor->instituicao,
            'valorBase' => 0,
            'iniciativas' => [],
            'valoresPrevisto' => []
        ];
    }

    private function createObjectAcao($valor)
    {
        return (object)[
            'codigo' => $valor->acao,
            'anoExercicio' => $this->anoSessao,
            'valorBase' => 0,
            'valoresPrevisto' => [],
        ];
    }

    /**
     * Retorna um sql com dotações do exercício e o valor da base de cálculo escolhida
     * @return string
     */
    private function executaDotacaoSaldo()
    {
        $dataIni = "$this->anoSessao-01-01";
        $dataFim = "$this->anoSessao-12-31";
        $campo = $this->getFormulaCalculaValor();

        $sql = "
        with valores_dotacao as (
          select ($campo) as valor,
                 o58_programa,
                 o58_projativ,
                 o58_instit,
                 o58_orgao,
                 o58_unidade,
                 o58_funcao,
                 o58_subfuncao,
                 o58_codele,
                 o58_codigo,
                 o58_concarpeculiar,
                 o58_localizadorgastos,
                 o58_esferaorcamentaria
            from orcdotacao
            join fc_saldo_dotacao(o58_anousu, o58_coddot, '$dataIni', '$dataFim') on o58_coddot = reduzido
                 and o58_anousu = ano
           where o58_anousu = $this->anoSessao
             and o58_esferaorcamentaria != 0
        ), totaliza_na_dotacao as (
           select sum(valor) as valor,
                  o58_programa,
                  o58_projativ,
                  o58_instit,
                  o58_orgao,
                  o58_unidade,
                  o58_funcao,
                  o58_subfuncao,
                  o58_codele,
                  o58_codigo,
                  o58_concarpeculiar,
                  o58_localizadorgastos,
                  o58_esferaorcamentaria
             from valores_dotacao
             group by o58_programa, o58_projativ, o58_instit, o58_orgao, o58_unidade, o58_funcao, o58_subfuncao,
                      o58_codele, o58_codigo, o58_concarpeculiar, o58_localizadorgastos, o58_esferaorcamentaria
        )
        select * from totaliza_na_dotacao
        ";

        return $sql;
    }

    private function getFormulaCalculaValor()
    {
        switch ($this->formaRecalculo) {
            case 2: // Empenhado
                return 'empenhado_acumulado - anulado_acumulado';
            case 3:  // Liquidado
                return 'liquidado_acumulado';
            case 4: // Pago mo execício até o presente momento
                return 'pago_acumulado';
            case 1: // Previsão Atualizada
            default:
                return 'total_creditos';
        }
    }

    /**
     * @param array $valoresDotacao
     * @return array
     */
    private function calculaProjecao(array $valoresDotacao)
    {
        $exerciciosPlanejamento = $this->planejamento->execiciosPlanejamento();
        $fatores = FatorCorrecaoDespesa::where('pl7_planejamento', '=', $this->planejamento->pl2_codigo)
            ->get();

        // aplica correção por elemento
        foreach ($valoresDotacao as $valorDotacao) {
            $elemento = $valorDotacao->elemento;
            $valor = $valorDotacao->valor;

            foreach ($exerciciosPlanejamento as $exercicio) {
                $fator = $fatores->filter(function ($fator) use ($exercicio, $elemento) {
                    if ($fator->pl7_orcelemento == $elemento && $fator->pl7_exercicio == $exercicio) {
                        return $fator;
                    }
                })->shift();

                if (isset($fator->pl7_percentual)) {
                    $valor = round($valor * (1 + ($fator->pl7_percentual / 100)), $this->precisaoRound);
                }

                $valorDotacao->valoresPrevisto[$exercicio] = $valor;
            }
        }

        return $valoresDotacao;
    }


    private function persisteCalculo($valoresAgrupados)
    {
        foreach ($valoresAgrupados as $programa) {
            $programaestrategico = new ProgramaEstrategico();
            $programaestrategico->pl9_anoorcamento = $programa->anoExercicio;
            $programaestrategico->pl9_orcprograma = $programa->codigo;
            $programaestrategico->pl9_instituicao = $programa->instituicao;
            $programaestrategico->pl9_valorbase = $programa->valorBase;
            $programaestrategico->planejamento()->associate($this->planejamento);
            $programaestrategico->save();

            $this->persisteValores(
                Valor::ORIGEM_PROGRAMA,
                $programaestrategico->pl9_codigo,
                $programa->valoresPrevisto
            );

            // persiste as iniciativas
            foreach ($programa->iniciativas as $dadosIniciativa) {
                $iniciativa = new Iniciativa();
                $iniciativa->pl12_orcprojativ = $dadosIniciativa->codigo;
                $iniciativa->pl12_anoorcamento = $dadosIniciativa->anoExercicio;
                $iniciativa->programaEstrategico()->associate($programaestrategico);
                $iniciativa->pl12_valorbase = $dadosIniciativa->valorBase;
                $iniciativa->save();

                $this->persisteValores(
                    Valor::ORIGEM_INICIATIVA,
                    $iniciativa->pl12_codigo,
                    $dadosIniciativa->valoresPrevisto
                );

                /**
                 *
                 * @todo implementar para persistir o detalhamento
                 * $iniciativa->detalhamento
                 */
            }
        }
    }

    /**
     * Salva o valor do plano
     * @param string $origem
     * @param integer $chave
     * @param array $valores
     */
    private function persisteValores($origem, $chave, array $valores)
    {
        foreach ($valores as $ano => $valorPrevisto) {
            $this->atualizaValor($origem, $chave, $ano, $valorPrevisto);
        }
    }

    public function atualizaValor($origem, $chave, $ano, $valorPrevisto)
    {
        $valor = new Valor();
        $valor->pl10_origem = $origem;
        $valor->pl10_chave = $chave;
        $valor->pl10_ano = $ano;
        $valor->pl10_valor = $valorPrevisto;
        $valor->save();
    }

    /**
     * Atualiza a previsão da despesa e atualiza o cronograma.
     * @param integer $id
     * @param integer $exercicio
     * @param float $valor
     * @param bool $atualizarCronograma
     * @return true
     * @throws Exception
     */
    public function atualizarPrevisao($id, $exercicio, $valor, $atualizarCronograma = true)
    {
        $detalhamento = DetalhamentoDespesa::find($id);
        if (is_null($detalhamento)) {
            throw new Exception("Estimativa não encontrada.");
        }

        $valoreService = new ValoresService();
        $modelValor = $valoreService->salvar(Valor::ORIGEM_DETALHAMENTO_DESPESA, $id, $valor, $exercicio, true);

        if (!$atualizarCronograma) {
            return true;
        }
        $cronograma = $detalhamento->cronogramaDesembolso->filter(
            function (CronogramaDesembolsoDespesa $cronograma) use ($exercicio) {
                return $cronograma->exercicio == $exercicio;
            }
        )->shift();

        if (is_null($cronograma)) {
            $service = new CronogramaDesembolsoDespesaService();
            $service->criarCronogramaExercicio($detalhamento, $valor, $exercicio);
            return true;
        }

        $service = new CronogramaDesembolsoDespesaService();
        $service->updateRateioAutomatico($cronograma, $modelValor);
        return true;
    }

    public function setFormaRecalculo($formaRecalculo)
    {
        $this->formaRecalculo = $formaRecalculo;
        return $this;
    }

    private function log($tipo, $msg)
    {
        $date = new \DateTime();

        echo sprintf("%s - [%s] %s <br>", $date->format('Y-m-d H:i:s u'), $tipo, $msg);
    }

    /**
     * Esse recalculo é aplicado apenas a planos com detalhamento
     * @return void
     */
    public function recalcular()
    {
        $this->salvarBaseCalculo();
        $sqlDotacao = $this->executaDotacaoSaldo();
        $dotacoes = DB::select($sqlDotacao);
        $detalhamentos = $this->buscarValoresDetalhamento();

        foreach ($detalhamentos as $detalhamento) {
            $valor = $this->matchDotacao($detalhamento, $dotacoes);
            if (!is_null($valor)) {
                $this->atualizaValorBaseDetalhamento($detalhamento, $valor);
            }
        }
    }

    public function criarDetalhamentoNovasDotacoes()
    {
        $sqlDotacao = $this->executaDotacaoSaldo();
        $dotacoes = DB::select($sqlDotacao);
        $detalhamentos = $this->buscarValoresDetalhamento();

        $dotacoesNovas = [];
        foreach ($dotacoes as $dotacao) {
            if (!$this->existeDetalhamento($dotacao, $detalhamentos)) {
                $dotacoesNovas[] = $dotacao;
            }
        }

        if (empty($dotacoesNovas)) {
            throw new Exception('Não existem detalhamentos novos a serem incluídos.');
        }

        $dotacoesOrganizadas = [];
        foreach ($dotacoesNovas as $dotacao) {
            $programa = $dotacao->o58_programa;
            $acao = $dotacao->o58_projativ;
            if (!array_key_exists($programa, $dotacoesOrganizadas)) {
                $dotacoesOrganizadas[$programa] = (object)[
                    'programa' => $programa,
                    'orgaos' => [],
                    'iniciativas' => []
                ];
            }

            if (!array_key_exists($acao, $dotacoesOrganizadas[$programa]->iniciativas)) {
                $dotacoesOrganizadas[$programa]->iniciativas[$acao] = (object)[
                    'codigo' => $acao,
                    'detalhamentos' => []
                ];
            }

            $dotacoesOrganizadas[$programa]->iniciativas[$acao]->detalhamentos[] = $dotacao;
            if (!in_array($dotacao->o58_orgao, $dotacoesOrganizadas[$programa]->orgaos)) {
                $dotacoesOrganizadas[$programa]->orgaos[] = $dotacao->o58_orgao;
            }
        }

        foreach ($dotacoesOrganizadas as $programa) {
            $programaEstrategico = $this->criarProgramaSeNaoExiste($programa);

            foreach ($programa->iniciativas as $stdIniciativa) {
                $iniciativa = $this->criarIniciativaSeNaoExiste($stdIniciativa, $programaEstrategico);

                foreach ($stdIniciativa->detalhamentos as $detalhamento) {
                    $this->criarDetalhamento($detalhamento, $iniciativa);
                }
            }
        }
    }


    public function get()
    {
        $baseCalculo = $this->getBaseCalculo();

        return [
            'projecao' => $this->buscarValoresDetalhamento(),
            'baseCalculo' => (object) [
                'descricao' => $baseCalculo->pl31_basecalculo,
                'codigo' => $this->getCodigoBaseCalculo($baseCalculo->pl31_basecalculo),
            ]
        ];
    }

    /**
     * Retorna uma collection com todos detalhamentos do plano
     * @return Collection
     */
    private function calulaValoresDetalhado()
    {
        $campos = [
            'detalhamnento_planejamento.*',
            'o40_descr as descricao_orgao',
            'o41_descr as descricao_unidade',
            'o52_descr as descricao_funcao',
            'o53_descr as descricao_subfuncao',
            'o54_descr as descricao_programa',
            'o55_descr as descricao_iniciativa',
            'o56_elemento as elemento',
            'o56_descr as descricao_elemento',
            'o15_codigo',
            'o15_tipo',
            'orctiporec.o15_recurso as subrecurso',
            'fonterecurso.codigo_siconfi as siconfi',
            'o15_complemento as complemento',
            DB::raw("
            (SELECT json_agg(
                      json_build_object('ano', x.exercicio, 'percentual', x.percentual, 'deflator', x.deflator)
                    ) AS valores
              FROM (SELECT pl7_exercicio as exercicio,
                           pl7_percentual as percentual,
                           deflator
                     FROM planejamento.fatorcorrecaodespesa
                    WHERE fatorcorrecaodespesa.pl7_planejamento = detalhamnento_planejamento.planejamento_id
                      AND pl7_orcelemento = pl20_orcelemento
                    ORDER BY pl7_exercicio
              ) AS x
            ) AS inflatores
            ")
        ];


        return DetalhamnentoPlanejamento::where('planejamento_id', $this->planejamento->pl2_codigo)
            ->selectRaw(implode(', ', $campos))
            ->vinculoOrcamento()
            ->with('instituicao')
            ->orderBy('pl20_instituicao')
            ->orderBy('pl20_orcorgao')
            ->orderBy('pl20_orcunidade')
            ->orderBy('pl20_orcfuncao')
            ->orderBy('pl20_orcsubfuncao')
            ->orderBy('pl9_orcprograma')
            ->orderBy('pl12_orcprojativ')
            ->orderBy('o56_elemento')
            ->orderBy('siconfi')
            ->orderBy('complemento')
            ->get()
            ->map(function ($detalhamento) {
                $detalhamento->codigo = $detalhamento->detalhamentoiniciativa_id;
                $detalhamento->estrutural = $this->montaProgramatica($detalhamento);
                $detalhamento->valorBase = $detalhamento->pl20_valorbase;
                $detalhamento->valores = $this->parseValores($detalhamento->valores);
                $detalhamento->inflatores = JSON::create()->parse($detalhamento->inflatores);
                return $detalhamento;
            });
    }

    /**
     * Quando não existe valores no detalhamento, a view retorna um array com dados nulos [{ano:null, valor:null}]
     * Nesse caso crio os valores zerados com os exercícios que o plano abrange
     * @param string $valores string json com os valores
     * @return array
     */
    private function parseValores($valores)
    {
        $valores = JSON::create()->parse($valores);
        $exercicios = $this->planejamento->execiciosPlanejamento();
        $nulos = collect($valores)->filter(function ($valor) {
            return is_null($valor->ano);
        });

        if (count($valores) != count($exercicios) && $nulos->count()) {
            $valores = [];
            foreach ($exercicios as $exercicio) {
                $valores[] = (object)["ano" => $exercicio, "valor" => 0];
            }
        }

        return $valores;
    }

    /**
     * Monta a programática do detalhamento
     * @param $detalhamento
     * @return string
     */
    private function montaProgramatica($detalhamento)
    {
        return implode('.', [
            str_pad($detalhamento->pl20_orcorgao, 2, '0', STR_PAD_LEFT),
            str_pad($detalhamento->pl20_orcunidade, 2, '0', STR_PAD_LEFT),
            str_pad($detalhamento->pl20_orcfuncao, 2, '0', STR_PAD_LEFT),
            str_pad($detalhamento->pl20_orcsubfuncao, 3, '0', STR_PAD_LEFT),
            str_pad($detalhamento->pl9_orcprograma, 4, '0', STR_PAD_LEFT),
            str_pad($detalhamento->pl12_orcprojativ, 4, '0', STR_PAD_LEFT),
            $detalhamento->elemento,
            $detalhamento->siconfi,
            str_pad($detalhamento->o15_complemento, 4, '0', STR_PAD_LEFT)
        ]);
    }

    /**
     * Verifica se o detalhamento ex
     * @param DetalhamnentoPlanejamento $detalhamento
     * @param array $dotacoes
     * @return null
     */
    private function matchDotacao(DetalhamnentoPlanejamento $detalhamento, array $dotacoes)
    {
        foreach ($dotacoes as $dotacao) {
            if ($detalhamento->pl20_instituicao != $dotacao->o58_instit) {
                continue;
            }
            if ($detalhamento->pl20_orcorgao != $dotacao->o58_orgao) {
                continue;
            }
            if ($detalhamento->pl20_orcunidade != $dotacao->o58_unidade) {
                continue;
            }
            if ($detalhamento->pl20_orcfuncao != $dotacao->o58_funcao) {
                continue;
            }
            if ($detalhamento->pl20_orcsubfuncao != $dotacao->o58_subfuncao) {
                continue;
            }
            if ($detalhamento->pl9_orcprograma != $dotacao->o58_programa) {
                continue;
            }
            if ($detalhamento->pl12_orcprojativ != $dotacao->o58_projativ) {
                continue;
            }
            if ($detalhamento->pl20_orcelemento != $dotacao->o58_codele) {
                continue;
            }
            if ($detalhamento->pl20_recurso != $dotacao->o58_codigo) {
                continue;
            }
            if ($detalhamento->pl20_concarpeculiar != $dotacao->o58_concarpeculiar) {
                continue;
            }
            if ($detalhamento->pl20_subtitulo != $dotacao->o58_localizadorgastos) {
                continue;
            }
            if ($detalhamento->pl20_esferaorcamentaria != $dotacao->o58_esferaorcamentaria) {
                continue;
            }

            return $dotacao->valor;
        }

        return null;
    }

    private function atualizaValorBaseDetalhamento($stdDetalhamento, $valor)
    {
        $id = $stdDetalhamento->detalhamentoiniciativa_id;
        $detalhamento = DetalhamentoDespesa::find($id);
        $detalhamento->pl20_valorbase = $valor;
        $detalhamento->save();

        $inflatores = $stdDetalhamento->inflatores;
        $exerciciosPlanejamento = $this->planejamento->execiciosPlanejamento();

        $projecao = [];

        foreach ($exerciciosPlanejamento as $exercicio) {
            $projecao[$exercicio] = $this->buidValor($exercicio, $valor);
        }

        if (!is_null($inflatores)) {
            foreach ($inflatores as $inflator) {
                if ($inflator->deflator) {
                    $inflator->percentual *= -1;
                }

                $valor = round($valor * (1 + ($inflator->percentual / 100)), $this->precisaoRound);
                $projecao[$inflator->ano]->valor = $valor;
            }
        }

        (new ValoresService)->salvarColecao($projecao, Valor::ORIGEM_DETALHAMENTO_DESPESA, $id);
    }

    private function salvarBaseCalculo()
    {
        $base = $this->getBaseCalculo();

        $base->pl31_planejamento_id = $this->planejamento->pl2_codigo;
        $base->pl31_origem = 1;
        $base->pl31_basecalculo = $this->getDescricaoBaseCalculo();
        $base->save();
    }

    private function getDescricaoBaseCalculo()
    {
        switch ($this->formaRecalculo) {
            case 2:
                return 'Empenhado';
            case 3:
                return 'Liquidado';
            case 4: // Pago
                return 'Pago';
            case 1: //
            default:
                return 'Previsão Atualizada';
        }
    }

    private function getCodigoBaseCalculo($base)
    {
        switch ($base) {
            case 'Empenhado':
                return 2;
            case 'Liquidado':
                return 3;
            case 'Pago': // Pago
                return '4';
            case 'Previsão Atualizada': //
            default:
                return 1;
        }
    }
    /**
     * @return BaseCalculoProjecao
     */
    private function getBaseCalculo()
    {
        $base = BaseCalculoProjecao::query()
            ->where('pl31_planejamento_id', $this->planejamento->pl2_codigo)
            ->where('pl31_origem', 1)
            ->first();

        if (is_null($base)) {
            $base = new BaseCalculoProjecao();
        }
        return $base;
    }

    private function existeDetalhamento($dotacao, Collection $detalhamentos)
    {
        foreach ($detalhamentos as $detalhamento) {
            if ($detalhamento->pl20_instituicao != $dotacao->o58_instit) {
                continue;
            }
            if ($detalhamento->pl20_orcorgao != $dotacao->o58_orgao) {
                continue;
            }
            if ($detalhamento->pl20_orcunidade != $dotacao->o58_unidade) {
                continue;
            }
            if ($detalhamento->pl20_orcfuncao != $dotacao->o58_funcao) {
                continue;
            }
            if ($detalhamento->pl20_orcsubfuncao != $dotacao->o58_subfuncao) {
                continue;
            }
            if ($detalhamento->pl9_orcprograma != $dotacao->o58_programa) {
                continue;
            }
            if ($detalhamento->pl12_orcprojativ != $dotacao->o58_projativ) {
                continue;
            }
            if ($detalhamento->pl20_orcelemento != $dotacao->o58_codele) {
                continue;
            }
            if ($detalhamento->pl20_recurso != $dotacao->o58_codigo) {
                continue;
            }
            if ($detalhamento->pl20_concarpeculiar != $dotacao->o58_concarpeculiar) {
                continue;
            }
            if ($detalhamento->pl20_subtitulo != $dotacao->o58_localizadorgastos) {
                continue;
            }
            if ($detalhamento->pl20_esferaorcamentaria != $dotacao->o58_esferaorcamentaria) {
                continue;
            }

            return true;
        }

        return false;
    }

    private function criarIniciativaSeNaoExiste(stdClass $stdIniciativa, ProgramaEstrategico $programaEstrategico)
    {
        $iniciativa = $programaEstrategico->iniciativas->filter(function (Iniciativa $iniciativa) use ($stdIniciativa) {
            return $iniciativa->pl12_orcprojativ === $stdIniciativa->codigo;
        })->shift();

        if (is_null($iniciativa)) {
            $iniciativa = new Iniciativa();
            $iniciativa->programaEstrategico()->associate($programaEstrategico);
            $iniciativa->pl12_orcprojativ = $stdIniciativa->codigo;
            $iniciativa->pl12_anoorcamento = $this->planejamento->pl2_ano_inicial;
            $iniciativa->save();
        }

        return $iniciativa;
    }

    private function criarDetalhamento($detalhamento, Iniciativa $iniciativa)
    {
        $valores = [];
        foreach ($this->planejamento->execiciosPlanejamento() as $exercicio) {
            $valores[] = (object)['ano' => $exercicio, 'valor' => $detalhamento->valor];
        }

        $std = (object)[
            "pl20_anoorcamento" => $this->planejamento->pl2_ano_inicial,
            "pl20_valorbase" => $detalhamento->valor,
            "pl20_iniciativaprojativ" => $iniciativa->pl12_codigo,
            "pl20_instituicao" => $detalhamento->o58_instit,
            "pl20_orcorgao" => $detalhamento->o58_orgao,
            "pl20_orcunidade" => $detalhamento->o58_unidade,
            "pl20_orcfuncao" => $detalhamento->o58_funcao,
            "pl20_orcsubfuncao" => $detalhamento->o58_subfuncao,
            "pl9_orcprograma" => $detalhamento->o58_programa,
            "pl12_orcprojativ" => $detalhamento->o58_projativ,
            "pl20_orcelemento" => $detalhamento->o58_codele,
            "pl20_recurso" => $detalhamento->o58_codigo,
            "pl20_concarpeculiar" => $detalhamento->o58_concarpeculiar,
            "pl20_subtitulo" => $detalhamento->o58_localizadorgastos,
            "pl20_esferaorcamentaria" => $detalhamento->o58_esferaorcamentaria,
            "valores" => json_encode($valores)
        ];

        /**
         * todo tem quer criar
         */
        $service = new DetalhamentoDespesaService(new ValoresService);
        $service->salvarFromObject($std);
    }
}
