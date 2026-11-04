<?php


namespace App\Domain\Financeiro\Planejamento\Services;

use App\Domain\Financeiro\Orcamento\Models\Orgao;
use App\Domain\Financeiro\Orcamento\Models\Programa;
use App\Domain\Financeiro\Planejamento\Models\AreaResultado;
use App\Domain\Financeiro\Planejamento\Models\Comissao;
use App\Domain\Financeiro\Planejamento\Models\ObjetivoEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Models\Status;
use App\Domain\Financeiro\Planejamento\Requests\SalvarAreaResultadoRequest;
use App\Domain\Financeiro\Planejamento\Requests\SalvarComissaoRequest;
use App\Domain\Financeiro\Planejamento\Requests\SalvarObjetivoRequest;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use ECidade\Enum\Financeiro\Planejamento\StatusEnum;
use ECidade\Enum\Financeiro\Planejamento\TipoEnum;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class PlanejamentoService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class PlanejamentoService
{
    protected $tipo;
    /**
     * @var Planejamento
     */
    protected $model;

    /**
     * @param $request
     * @return Planejamento|mixed
     */
    public function salvarIdentidadeOrganizacional($request)
    {
        $this->model = Planejamento::find($request->get('pl2_codigo'));
        $this->model->pl2_missao = $request->get('pl2_missao');
        $this->model->pl2_visao = $request->get('pl2_visao');
        $this->model->pl2_valores = $request->get('pl2_valores');
        $this->model->save();
        return $this->model;
    }

    /**
     * @param $id
     * @return Planejamento|mixed
     */
    public function find($id)
    {
        $planejamento = Planejamento::find($id);
        $planejamento = $this->carregaDependencias($planejamento);

        return $planejamento;
    }


    /**
     * @param Planejamento $planejamento
     * @return Planejamento
     */
    private function carregaDependencias(Planejamento $planejamento)
    {
        $planejamento->status;
        $planejamento->comissoes->map(function ($comissao) {
            $comissao->cgm;
        });
        $planejamento->areasResultado->map(function ($area) {
            $area->programas;
            $area->objetivosEstrategicos->map(function ($objetivo) {
                $objetivo->programas;
            });
        });

        return $planejamento;
    }

    /**
     * @param $id
     * @return int
     * @throws Exception
     */
    public function remover($id)
    {
        if (empty($id)) {
            throw new Exception("Você deve enviar o código do plano que deseja excluir", 406);
        }

        return Planejamento::find($id)->delete();
    }

    public function salvarComissao(SalvarComissaoRequest $request)
    {
        $planejamento = Planejamento::find($request->get('pl2_codigo'));
        $planejamento->comissoes()->delete();

        collect($request->get('cgms'))->map(function ($codigoCgm) use ($planejamento) {
            $comissao = new Comissao();
            $comissao->cgm()->associate(Cgm::find($codigoCgm));
            $comissao->planejamento()->associate($planejamento);
            $comissao->save();
        });
        return $planejamento;
    }

    /**
     * @param SalvarAreaResultadoRequest $request
     * @return Collection
     */
    public function salvarAreaResultado(SalvarAreaResultadoRequest $request)
    {
        $planejamento = Planejamento::find($request->get('pl2_codigo'));
        if ($request->has('pl4_codigo') && $request->has('pl4_codigo')) {
            $area = AreaResultado::find($request->get('pl4_codigo'));
        } else {
            $area = new AreaResultado();
        }

        $area->pl4_titulo = $request->get('pl4_titulo');
        $area->pl4_contextualizacao = $request->get('pl4_contextualizacao');

        $area->planejamento()->associate($planejamento);

        $area->save();
        $planejamento->areasResultado->map(function ($area) {
            $area->objetivosEstrategicos;
        });
        return $planejamento->areasResultado;
    }

    /**
     * Exclui a área de conhecimento
     * @param integer $id código da Área de Resultado
     * @return int
     */
    public function removerAreaResultado($id)
    {
        return AreaResultado::destroy($id);
    }

    /**
     * @param SalvarObjetivoRequest $request
     * @return Collection
     */
    public function salvarObjetivoEstrategico(SalvarObjetivoRequest $request)
    {
        $area = AreaResultado::find($request->get('pl5_arearesultado'));
        if ($request->has('pl5_codigo') && $request->has('pl5_codigo')) {
            $objetivo = ObjetivoEstrategico::find($request->get('pl5_codigo'));
        } else {
            $objetivo = new ObjetivoEstrategico();
        }

        $objetivo->pl5_titulo = $request->get('pl5_titulo');
        $objetivo->pl5_contextualizacao = $request->get('pl5_contextualizacao');
        $objetivo->pl5_fonte = $request->get('pl5_fonte');

        $objetivo->areaResultado()->associate($area);
        $objetivo->save();

        return $area->objetivosEstrategicos;
    }

    /**
     * Exclui a área de conhecimento
     * @param integer $id código da Área de Resultado
     * @return int
     */
    public function removerObjetivoEstrategico($id)
    {
        return DB::table('objetivoestrategico')->where('pl5_codigo', '=', $id)->delete();
    }

    /**
     * @param $tipoCriar
     * @param $tipoVincular
     * @return \Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function criarVinculo($tipoCriar, $tipoVincular)
    {
        $plano = new Planejamento();
        /**
         * @var Planejamento $planoVincular
         */
//        $planoVincular = $plano->planoAprovado($tipoVincular)->first();
        /**
         * @todo solução paliativa para atender Marica... depois veremos como contornar
         */
        $planoVincular = $plano
            ->where('pl2_tipo', '=', $tipoVincular)
            ->where('pl2_ativo', '=', 't')
            ->orderBy('pl2_codigo', 'desc')
            ->first();

        if (is_null($planoVincular)) {
            throw new Exception("Antes de criar uma {$tipoCriar} você deve ter um(a) {$tipoVincular} APROVADO", 406);
        }

        $numeroMaximoFilhos = 4;
        $incrementar = 2;
        if ($tipoCriar === 'LOA') {
            $numeroMaximoFilhos = 1;
            $incrementar = 0;
        }

        $filhos = $planoVincular->filhosAtivos();

        if (count($filhos) === $numeroMaximoFilhos) {
            throw new Exception("Número de {$tipoCriar} excedido.", 406);
        }

        $anoInicial = $planoVincular->pl2_ano_inicial + count($filhos);
        $anoFinal = $anoInicial + $incrementar;
        $planoReplicar = $this->getPlanoReplicar($planoVincular, $tipoCriar, $anoInicial);

        if (Orgao::query()->where('o40_anousu', $anoInicial)->get()->count() === 0) {
            throw new Exception(sprintf(
                "Não foi aberto o exercício contábil para o exercício: %s.\nAcesse: %s para incluir.",
                $anoInicial,
                'DB:FINANCEIRO > Contabilidade > Procedimentos > Exercício Contábil > Inclusão'
            ));
        }

        /**
         * @var Planejamento $novoPlano
         */
        $novoPlano = $planoReplicar->replicate();
        $novoPlano->pl2_tipo = $tipoCriar;
        $novoPlano->pl2_codigo_pai = $planoVincular->pl2_codigo;
        $novoPlano->pl2_ano_inicial = $anoInicial;
        $novoPlano->pl2_ano_final = $anoFinal;
        $novoPlano->pl2_titulo = "$tipoCriar {$anoInicial} - {$anoFinal}";
        $novoPlano->status()->associate(Status::find(StatusEnum::EM_DESENVOLVIMENTO));

        $novoPlano->push();

        $replicar = new ReplicarPlanoService($novoPlano, $planoReplicar);
        $replicar->replicar();

        return $novoPlano;
    }

    /**
     * Retorna as possíveis situações para a qual é possível atualizar o Plano de Governo
     * @param Planejamento $plano
     * @return array
     * @throws Exception
     */
    public function possiveisSituacoesAtualizar(Planejamento $plano)
    {
        $situacoesPossiveis = [];
        switch ($plano->getStatus()->pl1_codigo) {
            case StatusEnum::EM_DESENVOLVIMENTO:
                $situacoesPossiveis = [StatusEnum::ENCAMINHADO_PODER_LEGISLATIVO];
                break;
            case StatusEnum::ENCAMINHADO_PODER_LEGISLATIVO:
                $situacoesPossiveis = [
                    StatusEnum::EM_DESENVOLVIMENTO,
                    StatusEnum::APROVADO_EMENDAS,
                    StatusEnum::APROVADO,
                ];
                break;
            case StatusEnum::APROVADO_EMENDAS:
                throw new Exception("Não é possivel alterar a situação de um plano APROVADO COM EMENDAS.", 403);
            case StatusEnum::APROVADO:
                throw new Exception("Não é possivel alterar a situação de um plano APROVADO.", 403);
        }

        return array_values(Status::all()->filter(function ($situacao) use ($situacoesPossiveis) {
            return in_array($situacao->pl1_codigo, $situacoesPossiveis);
        })->toArray());
    }

    public function criarPpaComBaseNoPlanoInformado($codigoPlanoBase, $anoInicial)
    {
        $valida = Planejamento::query()->where('pl2_tipo', TipoEnum::PPA)
            ->whereRaw(" $anoInicial between pl2_ano_inicial and pl2_ano_final")
            ->first();

        if (!is_null($valida)) {
            $msg = sprintf('O exercício informado %s não pode ser usado pois conflica com outro PPA.', $anoInicial);
            throw new Exception($msg, 403);
        }

        if (!$this->existeOrcamentoExercicio($anoInicial)) {
            $msg = "Não foi encontrado orçamento para o exercício de $anoInicial. \nAcesse a rotina: ";
            $msg .= "DB:FINANCEIRO > Contabilidade > Procedimentos > Exercício Contábil > Inclusão ";

            throw new Exception($msg);
        }

        $planoBase = Planejamento::find($codigoPlanoBase);

        /**
         * @var Planejamento $novoPlano
         */
        $anoFinal = $anoInicial + 3;
        $novoPlano = $planoBase->replicate();
        $novoPlano->pl2_tipo = TipoEnum::PPA;
        $novoPlano->pl2_codigo_pai = null;
        $novoPlano->pl2_ano_inicial = $anoInicial;
        $novoPlano->pl2_ano_final = $anoFinal;
        $novoPlano->pl2_titulo = "PPA {$anoInicial}";
        $novoPlano->status()->associate(Status::find(StatusEnum::EM_DESENVOLVIMENTO));

        $novoPlano->push();

        $replicar = new ReplicarPlanoService($novoPlano, $planoBase);
        $replicar->replicar();

        return $novoPlano->pl2_codigo;
    }

    /**
     * Valida se o período do Plano de Governo (PPA, LDO, LOA), ano de inicio e fim, conflita com outro Plano
     * @param integer $id
     * @param integer $anoInicial
     * @param integer $anoFinal
     * @return bool
     */
    protected function hasConflito($id, $anoInicial, $anoFinal)
    {
        $situacoes = [StatusEnum::EM_DESENVOLVIMENTO, StatusEnum::ENCAMINHADO_PODER_LEGISLATIVO, StatusEnum::APROVADO];
        return Planejamento::where('pl2_tipo', '=', $this->tipo)
                ->when(!empty($id), function ($query) use ($id) {
                    $query->where("pl2_codigo", '!=', $id);
                })
                ->where('pl2_ativo', 't')
                ->whereIn('pl2_status', $situacoes)
                ->where('pl2_ano_inicial', $anoInicial)
                ->where('pl2_ano_final', $anoFinal)
                ->get()
                ->count() > 0;
    }

    /**
     * @param integer $id
     * @param integer $anoInicial
     * @param integer $anoFinal
     * @throws Exception
     */
    protected function validaConflito($id, $anoInicial, $anoFinal)
    {
        if ($this->hasConflito($id, $anoInicial, $anoFinal)) {
            throw new Exception("Você não pode salvar um Plano de Governo que conflita com outro", 406);
        }
    }

    /**
     * Retorna os planejamentos de acordo com a situacao informada
     * @param array $status
     * @param string $tipo
     * @return Planejamento[]|Collection
     */
    public function planejamentoPorStatus(array $status, $tipo = null)
    {
        return Planejamento::with('status')
            ->where('pl2_status', '=', $status)
            ->when(!empty($tipo), function ($query) use ($tipo) {
                $query->where("pl2_tipo", '=', "$tipo");
            })
            ->orderBy('pl2_created_at')->get();
    }

    /**
     * @return Planejamento[]|Collection
     */
    public function planejamentoEmDesenvolvimento($tipo = null)
    {
        if (!is_null($tipo)) {
            $tipo = mb_strtoupper($tipo);
        }

        return $this->planejamentoPorStatus([StatusEnum::EM_DESENVOLVIMENTO], $tipo);
    }

    /**
     * Identifica o plano a ser replicado
     * @param Planejamento $planoVincular
     * @param string $tipoCriar
     * @param $anoInicialNovoPlano
     * @return Planejamento
     */
    private function getPlanoReplicar(Planejamento $planoVincular, $tipoCriar, $anoInicialNovoPlano)
    {
        if ($anoInicialNovoPlano === $planoVincular->pl2_ano_inicial) {
            return $planoVincular;
        }

        return Planejamento::where('pl2_codigo_pai', '=', $planoVincular->pl2_codigo)
            ->where('pl2_tipo', '=', $tipoCriar)
            ->orderBy('pl2_ano_inicial', 'desc')
            ->limit(1)
            ->first();
    }

    /**
     * Valida a existência do cadastro do orçamento para o exercício
     * @param $anoInicial
     * @return bool
     */
    private function existeOrcamentoExercicio($anoInicial)
    {
        $programa = Programa::query()->where('o54_anousu', '=', $anoInicial)->first();
        return !is_null($programa);
    }
}
