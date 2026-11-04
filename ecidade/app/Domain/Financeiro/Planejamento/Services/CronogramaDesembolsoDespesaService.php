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

use App\Domain\Financeiro\Planejamento\Models\CronogramaDesembolsoDespesa;
use App\Domain\Financeiro\Planejamento\Models\DetalhamentoDespesa;
use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\ProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Manutencao\Cronograma\CronogramaRequest;
use Exception;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CronogramaDesembolsoDespesaService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class CronogramaDesembolsoDespesaService extends CronogramaDesembolsoService
{
    /**
     * @param $dados
     * @return CronogramaDesembolsoDespesa|mixed
     */
    public function salvarFromObject($dados)
    {
        $cronograma = CronogramaDesembolsoDespesa::find($dados->id);
        $cronograma->detalhamento()->associate(DetalhamentoDespesa::find($dados->detalhamentoiniciativa_id));
        $this->update($cronograma, $dados);
        return $cronograma;
    }

    /**
     * Cria o cronograma de desembolso apenas se ja nao tem
     * @param DetalhamentoDespesa $detalhamento
     * @return DetalhamentoDespesa
     */
    public function criarCronogramaDesembolso(DetalhamentoDespesa $detalhamento)
    {
        $detalhamento->getValores()->map(function (Valor $valor) use ($detalhamento) {
            $cronograma = $this->getCronograma($detalhamento->pl20_codigo, $valor->pl10_ano);

            if (is_null($cronograma)) {
                $this->criarCronogramaExercicio($detalhamento, $valor->pl10_valor, $valor->pl10_ano);
            }
        });

        return $detalhamento;
    }

    public function criarCronogramaExercicio(DetalhamentoDespesa $detalhamento, $valor, $exercicio)
    {
        $cronograma = new CronogramaDesembolsoDespesa();
        $cronograma->detalhamento()->associate($detalhamento);
        $this->salvar($cronograma, $valor, $exercicio);
    }

    /**
     * @param $dados
     * @return bool
     * @throws Exception
     */
    public function recalcular($dados)
    {
        $detalhamento = DetalhamentoDespesa::find($dados['detalhamentoiniciativa_id']);
        $valores = $detalhamento->getValores()->filter(function (Valor $valor) use ($dados) {
            return in_array($valor->pl10_ano, $dados['anos']);
        });

        $cronogramas = $valores->map(function (Valor $valor) use ($dados, $detalhamento) {
            $cronograma = $this->getCronograma($dados['detalhamentoiniciativa_id'], $valor->pl10_ano);
            if (is_null($cronograma)) {
                $cronograma = new CronogramaDesembolsoDespesa();
                $cronograma->detalhamento()->associate($detalhamento);
            }

            switch ($dados['formula']) {
                case 1:
                    $this->salvar($cronograma, $valor->pl10_valor, $valor->pl10_ano);
                    break;
                case 2:
                    $cronograma = $this->zeraValores($cronograma);
                    $cronograma->{$dados['mes']} = $valor->pl10_valor;
                    $cronograma->save();
                    break;
                default:
                    throw new Exception("Fórmula desconhecida.");
            }
            return $cronograma;
        });

        return $cronogramas;
    }

    /**
     * @param array $dados
     * @throws Exception
     */
    public function recalcularGeral(array $dados)
    {
        foreach ($dados['detalhamentoiniciativas'] as $idDetalhamento) {
            $recalcular['detalhamentoiniciativa_id'] = $idDetalhamento;
            $recalcular['formula'] = $dados['formula'];
            $recalcular['anos'] = $dados['anos'];
            $this->recalcular($recalcular);
        }
    }

    /**
     * @param $idDetalhamento
     * @param $exercicio
     * @return mixed
     */
    private function getCronograma($idDetalhamento, $exercicio)
    {
        return CronogramaDesembolsoDespesa::query()
            ->where('detalhamentoiniciativa_id', '=', $idDetalhamento)
            ->where('exercicio', '=', $exercicio)
            ->get()
            ->shift();
    }

    /**
     * Retorna os dados do cronograma de desembolso com as informações do da previsão
     * (orgao, unidade, funcao, subfuncao, programa, iniciativa, elemento...)
     * @param CronogramaRequest $request
     * @return array
     * @throws Exception
     */
    public function buscarPorRequest(CronogramaRequest $request)
    {
        $estimativas = $this->buscar($request->get('exercicio'), $request->get('planejamento_id'));
        return $estimativas;
    }

    private function createObjeto($descricao, $codigo)
    {
        return (object)['descricao' => $descricao, 'codigo' => $codigo];
    }

    /**
     * @param integer $exercicio
     * @param integer $planejamento_id
     * @return array
     * @throws Exception
     */
    public function buscar($exercicio, $planejamento_id)
    {
        $exercicio = (int)$exercicio;
        $service = new ProgramaEstrategicoService();
        $programas = $service->buscar(['planejamento' => $planejamento_id]);
        return $this->buscarEstimativas($programas, $exercicio);
    }

    /**
     * @param Collection $programas colleção de ProgramaEstrategico
     * @param $exercicio
     * @param null|stdClass $filtros conforme retorno da função filtrosDespesaToPlanejamento
     * @return array
     */
    public function buscarEstimativas(Collection $programas, $exercicio, $filtros = null)
    {
        $estimativas = [];
        foreach ($programas as $programa) {
            /**
             * @var ProgramaEstrategico $programa
             */
            $iniciativas = $programa->iniciativas;
            foreach ($iniciativas as $iniciativa) {
                /**
                 * @var Iniciativa $iniciativa
                 */
                foreach ($iniciativa->detalhamentoDespesa as $detalhamentoDespesa) {
                    if (!is_null($filtros) &&
                        !matchFiltros($filtros, $programa, $iniciativa, $detalhamentoDespesa)) {
                        continue;
                    }

                    /**
                     * @var DetalhamentoDespesa $detalhamentoDespesa
                     */
                    $valorBase = $detalhamentoDespesa->getValores()->filter(function (Valor $valor) use ($exercicio) {
                        return $valor->pl10_ano === $exercicio;
                    })->shift();
                    /**
                     * @var CronogramaDesembolsoDespesa $cronograma
                     */
                    $cronograma = $detalhamentoDespesa->cronogramaDesembolso->filter(
                        function (CronogramaDesembolsoDespesa $cronograma) use ($exercicio) {
                            return $cronograma->exercicio === $exercicio;
                        }
                    )->shift();

                    $data = (object)$cronograma->toArray();
                    $data->valor_base = 0;
                    if (!is_null($valorBase)) {
                        $data->valor_base = $valorBase->pl10_valor;
                    }
                    $data->estrutural = $detalhamentoDespesa->getEstrutural();
                    $iniciativaOrc = $iniciativa->getIniciativaOrcamento();
                    $orgao = $detalhamentoDespesa->getOrgao();
                    $unidade = $detalhamentoDespesa->getUnidade();
                    $funcao = $detalhamentoDespesa->funcao;
                    $subfuncao = $detalhamentoDespesa->subfuncao;
                    $elemento = $detalhamentoDespesa->getNaturezaDespesa();
                    $recurso = $detalhamentoDespesa->recurso;
                    $fonteRecurso = $detalhamentoDespesa->recurso->fonteRecurso($exercicio);
                    $instituicao = $detalhamentoDespesa->instituicao;
                    $programaOrc = $programa->getProgramaOrcamento();

                    $data->orgao = $this->createObjeto($orgao->o40_descr, $orgao->formataCodigo());
                    $data->unidade = $this->createObjeto($unidade->o41_descr, $unidade->formataCodigo());
                    $data->funcao = $this->createObjeto($funcao->o52_descr, $funcao->formataCodigo());
                    $data->subfuncao = $this->createObjeto($subfuncao->o53_descr, $subfuncao->formataCodigo());
                    $data->programa = $this->createObjeto($programaOrc->o54_descr, $programaOrc->formataCodigo());
                    $data->iniciativa = $this->createObjeto($iniciativaOrc->o55_descr, $iniciativaOrc->formataCodigo());
                    $data->elemento = $this->createObjeto($elemento->o56_descr, $elemento->o56_elemento);
                    $data->recurso = $this->createObjeto($fonteRecurso->descricao, $fonteRecurso->gestao);
                    $data->recurso->complemento = (object)$recurso->complemento->toArray();
                    $data->cp = (object)$detalhamentoDespesa->caracteristicaPeculiar->toArray();
                    $data->instituicao = $this->createObjeto($instituicao->nomeinst, $instituicao->codigo);
                    $data->subrecurso = $fonteRecurso->recurso->o15_recurso;

                    $hash = sprintf(
                        '%s#%s#%s#%s',
                        $data->estrutural,
                        $fonteRecurso->gestao,
                        $fonteRecurso->recurso->o15_recurso,
                        $fonteRecurso->recurso->o15_complemento
                    );


                    $estimativas[$hash] = $data;
                }
            }
        }

        ksort($estimativas);
        return $estimativas;
    }
}
