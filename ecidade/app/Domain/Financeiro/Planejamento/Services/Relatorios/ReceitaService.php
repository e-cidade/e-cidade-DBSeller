<?php


namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use App\Domain\Financeiro\Orcamento\Models\FonteReceita;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaFormatter;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaPadrao;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ReceitaService
 * Essa classe é para facilitar a emissão dos relatórios referente ao planejamento da receita no PPA/LDO ou LOA
 *
 * @package App\Domain\Financeiro\Planejamento\Services\Relatorios
 */
abstract class ReceitaService
{
    /**
     * @var Planejamento
     */
    protected $planejamento;

    /**
     * Array com os exercicios anteriores ao planejamento
     * @var array
     */
    protected $exerciciosAnteriores = [];
    /**
     * Array com os dados para impressão
     * @var array
     */
    protected $dados = [];

    /**
     * Nível das contas retornadas
     * @var int
     */
    protected $nivel = 1;

    protected $filtros = [];

    protected $campos = [];

    /**
     * Totalizador os valores projetados por ano
     * @var array
     */
    protected $totalizador = [];

    protected $ementario = 'ecidade';
    /**
     * @var array
     */
    protected $fontesReceitas;

    protected $forcarDesdobramento = false;
    protected $mapaForcarDesdobramento = [
        //receita de impostos
        '4111' => [
            60 => 'Livre',
            25 => 'MDE',
            15 => 'ASPS',
        ],
        // receita de cotas
        '417' => [
            60 => 'Livre',
            5 => 'MDE',
            15 => 'ASPS',
            20 => 'Fundeb',
        ],
    ];

    abstract public function emitirPdf();

    public function processarFiltros()
    {
        if (empty($this->filtros)) {
            throw new Exception('Nenhum filtro foi informado.', 403);
        }

        if (empty($this->filtros['planejamento_id'])) {
            throw new Exception('Você deve selecionar o planejamento.', 403);
        }

        if (empty($this->filtros['DB_instit'])) {
            throw new Exception('A instituição deve ser informada.', 403);
        }
        if (!empty($this->filtros['natureza'])) {
            $fonte = str_pad($this->filtros['natureza'], 15, '0', STR_PAD_RIGHT);
            $estrutural = $this->estruturalFormatter($fonte);
            $this->nivel = $estrutural->getNivel();
        }

        if (!empty($this->filtros['ementario'])) {
            $this->ementario = $this->filtros['ementario'];
        }

        if (!empty($this->filtros['forcarDesdobramento'])) {
            $this->forcarDesdobramento = $this->filtros['forcarDesdobramento'] == 1;
        }

        $this->planejamento = Planejamento::find($this->filtros['planejamento_id']);

        foreach ($this->planejamento->execiciosPlanejamento() as $exercicio) {
            $this->totalizador[$exercicio] = 0;
        }
    }

    private function defaultCampos()
    {
        $camposFonte = [
            'o57_fonte as fonte',
            'o57_descr as descricao',
        ];
        if ($this->ementario !== 'ecidade') {
            $camposFonte = [
                'conta as fonte',
                'nome as descricao',
            ];
        }

        $this->campos = array_merge([
            'orcfontes_id',
            'o70_codrec',
            'fonterecurso.codigo_siconfi as recurso',
            'fonterecurso.descricao as descricao_recurso',
            'o15_complemento as complemento',
            'valorbase as valor_base',
        ], $camposFonte);


        $this->campos[] = DB::raw("
            (select json_agg(
                       json_build_object(
                         'ano', x.pl10_ano,
                         'valor', x.pl10_valor
                       )
                    )
              from (select valores.pl10_ano, valores.pl10_valor
                     from planejamento.valores
                    where pl10_origem = 'RECEITA'
                      and pl10_chave = estimativareceita.id
                    order by pl10_ano
                 ) as x
            ) as valores
        ");

        return $this->campos;
    }

    /**
     * @param array $campos com a lista de campos
     * @return Collection
     */
    public function buscarProjecao(array $campos = [])
    {
        if (empty($campos)) {
            $campos = $this->defaultCampos();
        }

        $exercicioReceita = $this->planejamento->pl2_ano_inicial - 1;
        /**
         * @var Collection $estimativas
         */
        return DB::table('estimativareceita')
            ->select($campos)
            ->join('orcfontes', function ($join) {
                $join->on('orcfontes.o57_codfon', '=', 'estimativareceita.orcfontes_id')
                    ->on('orcfontes.o57_anousu', '=', 'estimativareceita.anoorcamento');
            })
            ->leftJoin('orcreceita', function ($join) use ($exercicioReceita) {
                $join->on('orcreceita.o70_codfon', '=', 'estimativareceita.orcfontes_id')
                    ->on('orcreceita.o70_concarpeculiar', '=', 'estimativareceita.concarpeculiar_id')
                    ->where('o70_anousu', '=', $exercicioReceita);
            })
            ->join('fonterecurso', function ($join) {
                $join->on('fonterecurso.orctiporec_id', '=', 'estimativareceita.recurso_id')
                    ->on('fonterecurso.exercicio', '=', 'estimativareceita.anoorcamento');
            })
            ->join('orctiporec', 'o15_codigo', '=', 'recurso_id')
            ->where('planejamento_id', '=', $this->planejamento->pl2_codigo)
            ->when(!empty($this->filtros['instituicoes']), function ($query) {
                $query->whereIn('instituicao_id', $this->filtros['instituicoes']);
            })
            ->when(!empty($this->filtros['natureza']), function ($query) {
                $query->where('o57_fonte', 'like', "{$this->filtros['natureza']}%");
            })
            ->when($this->ementario !== 'ecidade', function ($query) {
                $exercicio = $this->exercicioMapeamento();

                $query->join('contabilidade.conplanoorcamento', function ($join) use ($exercicio) {
                    $join->on('conplanoorcamento.c60_codcon', '=', 'estimativareceita.orcfontes_id')
                        ->where('conplanoorcamento.c60_anousu', $exercicio);
                })
                    ->join('planoreceitaconplanoorcamento', 'conplanoorcamento_codigo', '=', 'c60_codigo')
                    ->join('planoreceita', 'planoreceita.id', '=', 'planoreceita_id')
                    ->where('uniao', $this->ementario === 'uniao');
            })
            ->get();
    }

    /**
     * @param $estrutural
     * @return EstruturalReceita|EstruturalReceitaPadrao
     */
    public function estruturalFormatter($estrutural)
    {
        if ($this->ementario === 'ecidade') {
            return new EstruturalReceita($estrutural);
        }

        return new EstruturalReceitaPadrao($estrutural);
    }

    /**
     * @param $estrutural
     * @param $estimativa
     * @return string
     */
    public function getHash($estrutural, $estimativa)
    {
        return sprintf('%s#%s#%s', $estrutural->getEstrutural(), $estimativa->recurso, $estimativa->complemento);
    }

    /**
     * Retorna as fontes do ementário
     * @return array
     */
    public function getFontesReceitas()
    {
        if (is_null($this->fontesReceitas)) {
            $exercicio = $this->planejamento->pl2_ano_inicial;
            if ($this->ementario !== 'ecidade') {
                $exercicio = $this->exercicioMapeamento();
            }

            $this->fontesReceitas = getFontesEmentario($this->ementario, $exercicio);
        }

        return $this->fontesReceitas;
    }

    protected function montaArvoreEstrutural(Collection $dadosEstimativas)
    {
        $receitas = [];
        $estimativas = $this->processaValoresEstimativa($dadosEstimativas);

        if ($this->forcarDesdobramento) {
            $estimativas = $this->desdobrarReceita($estimativas);
        }

        foreach ($estimativas as $key => $estimativa) {
            $estrutural = $this->estruturalFormatter($estimativa->fonte);
            $nivel = $estrutural->getNivel();

            $hash = $key;
            $receitas[$hash] = $estimativa;
            list($estrutural, $receitas) = $this->montaContaPai($nivel, $estrutural, $receitas, $estimativa);
        }


        ksort($receitas);
        return $receitas;
    }

    protected function montaContaPai($nivel, $estrutural, array $arvore, $estimativa)
    {
        while ($nivel != 1 && $this->nivel < $nivel) {
            $estrutural = $this->estruturalFormatter($estrutural->getCodigoEstruturalPai());
            /**
             * As classes de formatter do estrutural, não validam a existência do estrutural no sistema.
             * A função buscaFonteReceita realiza uma busca recursiva pelo estrutural pai caso o estrutural informado
             * por parâmetro não exista no sistema.
             */
            $fonteReceita = $this->buscaFonteReceita($estrutural->getEstrutural());
            $estrutural = $this->estruturalFormatter($fonteReceita->natureza);
            $fonte = $estrutural->getEstrutural();
            $nivel = $estrutural->getNivel();

            if (!array_key_exists($fonte, $arvore)) {
                $arvore[$fonte] = $this->builder($estrutural, $fonteReceita->nome);
            }
            $arvore[$fonte]->valor_base += $estimativa->valor_base;

            foreach ($this->exerciciosAnteriores as $exercicio) {
                $propriedade = "arrecadado_{$exercicio}";
                $arvore[$fonte]->{$propriedade} += $estimativa->{$propriedade};
            }

            foreach ($this->planejamento->execiciosPlanejamento() as $exercicio) {
                $propriedade = "valor_{$exercicio}";
                $arvore[$fonte]->{$propriedade} += $estimativa->{$propriedade};
            }
        }
        return [$estrutural, $arvore];
    }

    /**
     * A função buscaFonteReceita realiza uma busca recursiva pelo estrutural pai caso o estrutural informado
     * por parâmetro não exista no sistema.
     * Retornando então a instância da fonte de receita do ementário
     * @param $fonte
     * @return FonteReceita|PlanoReceita
     */
    protected function buscaFonteReceita($fonte)
    {
        if (!array_key_exists($fonte, $this->getFontesReceitas())) {
            $estruturalPai = $this->estruturalFormatter($fonte)->getEstruturalPai();
            $fonte = $estruturalPai->getEstrutural();
            return $this->buscaFonteReceita($fonte);
        }
        return $this->fontesReceitas[$fonte];
    }

    protected function builder(EstruturalReceitaFormatter $estrutural, $descricao)
    {
        $std = (object)[
            'orcfontes_id' => null,
            'o70_codrec' => null,
            'sintetico' => true,
            'nivel' => $estrutural->getNivel(),
            'fonte' => $estrutural->getEstrutural(),
            'estrutural' => $estrutural->getEstruturalComMascara(),
            'descricao' => $descricao,
            'recurso' => null,
            'complemento' => null,
            'valor_base' => 0,
        ];

        foreach ($this->exerciciosAnteriores as $exercicio) {
            $std->{"arrecadado_{$exercicio}"} = 0;
        }
        foreach ($this->planejamento->execiciosPlanejamento() as $exercicio) {
            $std->{"valor_{$exercicio}"} = 0;
        }
        return $std;
    }

    /**
     * @param $dadosEstimativa
     * @param EstruturalReceitaFormatter $estrutural
     * @return object
     */
    protected function builderAnalitico($dadosEstimativa, EstruturalReceitaFormatter $estrutural)
    {
        $estimativa = $this->builder($estrutural, $dadosEstimativa->descricao);
        $estimativa->sintetico = false;
        $estimativa->orcfontes_id = $dadosEstimativa->orcfontes_id;
        $estimativa->o70_codrec = $dadosEstimativa->o70_codrec;
        $estimativa->recurso = $dadosEstimativa->recurso;
        $estimativa->complemento = $dadosEstimativa->complemento;
        $estimativa->valor_base = $dadosEstimativa->valor_base;

        $valores = \JSON::create()->parse($dadosEstimativa->valores);
        $estimativa->valores = $valores;

        foreach ($valores as $valor) {
            $estimativa->{"valor_{$valor->ano}"} = (float)$valor->valor;
            $this->totalizador[$valor->ano] += (float)$valor->valor;
        }

        foreach ($this->exerciciosAnteriores as $exercicio) {
            $estimativa->{"arrecadado_{$exercicio}"} = (float)$dadosEstimativa->{"arrecadado_{$exercicio}"};
        }

        return $estimativa;
    }

    /**
     * Quando realizamos a estimativa da receita os valores vem abertos por CP.
     * Nesse método somamos as estimativas como se não houvesse uma estimativa diferente para uma CP diferente
     * - Os valores arrecadados não enxergam a CP, sendo o total da natureza da receita.
     * @param Collection $dadosEstimativas
     * @return array
     */
    protected function processaValoresEstimativa(Collection $dadosEstimativas)
    {
        $receitas = [];
        foreach ($dadosEstimativas as $dadosEstimativa) {
            $estrutural = $this->estruturalFormatter($dadosEstimativa->fonte);
            $estimativa = $this->builderAnalitico($dadosEstimativa, $estrutural);

            $hash = $this->getHash($estrutural, $estimativa);

            if (array_key_exists($hash, $receitas)) {
                $receitas[$hash]->valor_base += $estimativa->valor_base;
                $valores = \JSON::create()->parse($dadosEstimativa->valores);
                foreach ($valores as $valor) {
                    $receitas[$hash]->{"valor_{$valor->ano}"} += (float)$valor->valor;
                }
            } else {
                $receitas[$hash] = $estimativa;
            }
        }

        ksort($receitas);
        return $receitas;
    }

    /**
     * @param array $dados
     */
    protected function organizaDados(array $dados)
    {
        $this->dados['planejamento'] = $this->planejamento->toArray();
        $this->dados['planejamento']['exercicios'] = $this->planejamento->execiciosPlanejamento();
        $this->dados['dados'] = $dados;
        $this->dados['ementario'] = $this->ementario;

        $this->dados['totalizador'] = $this->totalizador;
    }

    /**
     * Retorna o exercício do mapeamento a ser usado.
     * Nem sempre teremos o ementário para o exercício do planejamento, nesse caso ficou acordado de pegar o anterior.
     * @return int
     */
    protected function exercicioMapeamento()
    {
        $plano = PlanoReceita::query()
            ->where('uniao', $this->ementario === 'uniao')
            ->where('exercicio', $this->planejamento->pl2_ano_inicial)
            ->first();

        if (is_null($plano)) {
            return $this->planejamento->pl2_ano_inicial - 1;
        }

        return $this->planejamento->pl2_ano_inicial;
    }

    /**
     * Esse método força a criação de um desdobramento da receita.
     * Apenas as receitas onde o recurso siconfi for 1500 e atendam o padrão de estrutural prédefinido (4111 ou 417).
     * Onde: 4111 são receita de impostos e 417 são receitas de cotas
     *
     * @param array $estimativas
     * @return array
     */
    private function desdobrarReceita(array $estimativas)
    {
        $novasEstimativas = [];
        foreach ($estimativas as $key => $estimativa) {
            // se atende ao padrão desdobra
            if ((strpos($estimativa->fonte, '4111') === 0 || strpos($estimativa->fonte, '417') === 0)
                && $estimativa->recurso === '1500') {
                $valorBase = $estimativa->valor_base;

                $desdobrar = $this->mapaForcarDesdobramento['4111'];
                if (strpos($estimativa->fonte, '417') === 0) {
                    $desdobrar = $this->mapaForcarDesdobramento['417'];
                }
                $contador = 1;
                foreach ($desdobrar as $percentual => $label) {
                    $hash = sprintf('%s#%s', $key, $contador);

                    $novaEstimativa = $this->clonaObjeto($estimativa);
                    $novaEstimativa->descricao .= " - $label ($percentual%)";
                    $novaEstimativa->valor_base = (float) ($valorBase * ($percentual / 100));

                    foreach ($novaEstimativa->valores as $valor) {
                        $novaEstimativa->{"valor_{$valor->ano}"} = (float)$valor->valor * ($percentual / 100);
                    }

                    if ($contador != 1) {
                        $novaEstimativa->recurso = '';
                        $novaEstimativa->complemento = '';
                        foreach ($this->exerciciosAnteriores as $exercicio) {
                            $novaEstimativa->{"arrecadado_{$exercicio}"} = 0;
                        }
                    }

                    $novasEstimativas[$hash] = $novaEstimativa;

                    $contador ++;
                }

                continue;
            }

            // se não atende só mantêm como estava
            $novasEstimativas[$key] = $estimativa;
        }

        return $novasEstimativas;
    }

    private function clonaObjeto($estimativa)
    {
        return (object)(array)$estimativa;
    }
}
