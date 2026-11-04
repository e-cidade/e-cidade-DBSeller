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

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Orcamento\Models\CaracteristicaPeculiar;
use App\Domain\Financeiro\Planejamento\Mappers\ProjecaoReceitaMapper;
use App\Domain\Financeiro\Planejamento\Models\CronogramaDesembolsoReceita;
use App\Domain\Financeiro\Planejamento\Models\EstimativaReceita;
use App\Domain\Financeiro\Planejamento\Models\FatorCorrecaoReceita;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use Exception;
use Illuminate\Support\Collection;
use StdClass;

/**
 * Class EstimativaReceitaService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class EstimativaReceitaService
{
    /**
     * @param ProjecaoReceitaMapper $projecao
     * @return EstimativaReceita
     * @throws Exception
     */
    public function salvarProjecao(ProjecaoReceitaMapper $projecao)
    {
        $model = EstimativaReceita::hasEstimativaProjecao($projecao)->first();

        if (!is_null($model) && $model->inclusaomanual) {
            return $model;
        }

        if (is_null($model)) {
            $model = new EstimativaReceita();
        }

        $model->planejamento()->associate($projecao->planejamento);
        $model->anoorcamento = $projecao->planejamento->pl2_ano_inicial;
        $model->orcfontes_id = $projecao->codigoFonte;
        $model->recurso_id = $projecao->recurso;
        $model->instituicao()->associate(DBConfig::find($projecao->instituicao));
        $model->caracteristicaPeculiar()->associate(CaracteristicaPeculiar::find($projecao->caracteristicaPeculiar));
        $model->orcorgao_id = $projecao->orgao;
        $model->orcunidade_id = $projecao->unidade;
        $model->esferaorcamentaria = $projecao->esferaOrcamentaria;
        $model->valorbase = $projecao->valorBase;
        $model->inclusaomanual = false;
        $model->save();

        $model->setValores($this->salvarValores($projecao->valoresProjetados, $model->id));
        $this->atualizaCronogramaDesembolso($model);

        return $model;
    }

    /**
     *
     * @param integer $id
     * @param integer $exercicio
     * @param float $valor
     * @throws Exception
     */
    public function atualizarPrevisao($id, $exercicio, $valor)
    {
        $estimativa = EstimativaReceita::find($id);
        if (is_null($estimativa)) {
            throw new Exception("Estimativa não encontrada.");
        }

        $valoreService = new ValoresService();
        $modelValor = $valoreService->salvar(Valor::ORIGEM_RECEITA, $id, $valor, $exercicio, true);

        $cronograma = $estimativa->cronogramaDesembolso->filter(
            function (CronogramaDesembolsoReceita $cronograma) use ($exercicio) {
                return $cronograma->exercicio == $exercicio;
            }
        )->shift();

        if (is_null($cronograma)) {
            $service = new CronogramaDesembolsoReceitaService();
            $service->criarCronogramaExercicio($estimativa, $valor, $exercicio);
            return true;
        }

        $service = new CronogramaDesembolsoReceitaService();
        $service->updateRateioAutomatico($cronograma, $modelValor);
    }

    /**
     * @param StdClass $dados
     * @return EstimativaReceita|mixed
     * @throws Exception
     */
    public function salvarToObject(StdClass $dados)
    {
        $estimativa = EstimativaReceita::existeEstimativa($dados)->first();
        if (!is_null($estimativa)) {
            throw new Exception('Já existe uma estimativa de receita com os dados informados.', 403);
        }
        $model = new EstimativaReceita();
        if (!empty($dados->id)) {
            $model = EstimativaReceita::find($dados->id);
        }

        $model->planejamento()->associate(Planejamento::find($dados->planejamento_id));
        $model->anoorcamento = $dados->anoorcamento;
        $model->orcfontes_id = $dados->orcfontes_id;
        $model->recurso_id = $dados->recurso_id;
        $model->instituicao()->associate(DBConfig::find($dados->instituicao_id));
        $model->caracteristicaPeculiar()->associate(CaracteristicaPeculiar::find($dados->concarpeculiar_id));
        $model->orcorgao_id = $dados->orcorgao_id;
        $model->orcunidade_id = $dados->orcunidade_id;
        $model->esferaorcamentaria = $dados->esferaorcamentaria;
        $model->valorbase = !empty($dados->valorBase) ?: null;
        $model->inclusaomanual = true;
        $model->save();

        $valores = str_replace('\\', '', $dados->valores);
        $valores = json_decode($valores);
        if (substr($dados->natureza, 0, 1) == 9) {
            foreach ($valores as $valor) {
                if ($valor->valor > 0) {
                    $valor->valor *= -1;
                }
            }
        }

        $model->setValores($this->salvarValores($valores, $model->id));

        $this->atualizaCronogramaDesembolso($model);

        return $model;
    }

    /**
     * @param array $valores
     * @param integer $id
     * @return Collection
     * @throws Exception
     */
    public function salvarValores(array $valores, $id)
    {
        $service = new ValoresService();
        return $service->salvarColecao($valores, Valor::ORIGEM_RECEITA, $id);
    }

    /**
     * Cria ou atualiza cronograma de desembolso
     * @param EstimativaReceita $model
     * @throws Exception
     */
    private function atualizaCronogramaDesembolso(EstimativaReceita $model)
    {
        $service = new CronogramaDesembolsoReceitaService();
        if ($model->cronogramaDesembolso()->count() === 0) {
            $service->criarCronogramaDesembolso($model);
        } else {
            $service->recalcularEstimativa($model);
        }
    }

    /**
     * Retorna as estimativas das resceitas
     * @param array $filtros
     * @return mixed
     */
    public function filtrar(array $filtros)
    {
        $filtrarInstituicao = null;
        $codigoInstituicao = null;
        if (!empty($filtros['DB_instit']) && empty($filtros['instituicao_id'])) {
            $codigoInstituicao = $filtros['DB_instit'];
        } elseif (!empty($filtros['instituicao_id'])) {
            $codigoInstituicao = $filtros['instituicao_id'];
        }
        if (!empty($codigoInstituicao)) {
            $instituicao = DBConfig::find($codigoInstituicao);
            if (!$instituicao->prefeitura) {
                $filtrarInstituicao = $instituicao->codigo;
            }
        }

        return EstimativaReceita::when(!empty($filtros['planejamento']), function ($query) use ($filtros) {
            $query->where('planejamento_id', '=', $filtros['planejamento']);
        })->when(!empty($filtrarInstituicao), function ($query) use ($filtrarInstituicao) {
            $query->where('instituicao_id', '=', $filtrarInstituicao);
        })->when(!empty($filtros['instituicao_id']), function ($query) use ($filtros) {
            $query->where('instituicao_id', '=', $filtros['instituicao_id']);
        })->when(!empty($filtros['inclusaomanual']), function ($query) use ($filtros) {
            $tipo = ($filtros['inclusaomanual'] === 'true') ? 't' : 'f';

            $query->where('inclusaomanual', '=', $tipo);
        })
            ->get();
    }

    /**
     * @param $id
     * @return int
     * @throws Exception
     */
    public function remover($id)
    {
        $estimativa = EstimativaReceita::find($id);
        $fonte = $estimativa->getNaturezaOrcamento();

        FatorCorrecaoReceita::where('orcfontes_id', $fonte->o57_codfon)
            ->where('anoorcamento', $fonte->o57_anousu)
            ->where('planejamento_id', $estimativa->planejamento_id)
            ->delete();

        return EstimativaReceita::destroy($id);
    }

    /**
     * @param $id
     * @return EstimativaReceita
     */
    public function find($id)
    {
        return EstimativaReceita::find($id);
    }

    /**
     * @param $codigoPlanejamento
     * @param $naturezas
     */
    public function removerNaturezas($codigoPlanejamento, $naturezas)
    {
        $estimativas = EstimativaReceita::where('planejamento_id', '=', $codigoPlanejamento)
            ->whereIn('orcfontes_id', $naturezas)
            ->get();

        $estimativas->each(function (EstimativaReceita $estimativaReceita) {
            $this->remover($estimativaReceita->id);
        });
    }

    /**
     * @param integer $id
     * @param float $valor
     */
    public function atualizarValorBase($id, $valor)
    {
        $estimativa = EstimativaReceita::find($id);
        $estimativa->valorbase = $valor;
        $estimativa->inclusaomanual = true;
        $estimativa->save();
    }
}
