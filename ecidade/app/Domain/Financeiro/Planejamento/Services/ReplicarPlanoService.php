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
use App\Domain\Financeiro\Orcamento\Models\Subtitulo;
use App\Domain\Financeiro\Planejamento\Models\Abrangencia;
use App\Domain\Financeiro\Planejamento\Models\AreaResultado;
use App\Domain\Financeiro\Planejamento\Models\Comissao;
use App\Domain\Financeiro\Planejamento\Models\CronogramaDesembolsoDespesa;
use App\Domain\Financeiro\Planejamento\Models\CronogramaDesembolsoReceita;
use App\Domain\Financeiro\Planejamento\Models\DetalhamentoDespesa;
use App\Domain\Financeiro\Planejamento\Models\EstimativaReceita;
use App\Domain\Financeiro\Planejamento\Models\FatorCorrecaoReceita;
use App\Domain\Financeiro\Planejamento\Models\IndicadorProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\MetasIniciativa;
use App\Domain\Financeiro\Planejamento\Models\MetasObjetivo;
use App\Domain\Financeiro\Planejamento\Models\ObjetivoEstrategico;
use App\Domain\Financeiro\Planejamento\Models\ObjetivoProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Models\ProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use Exception;
use Illuminate\Support\Collection;

class ReplicarPlanoService
{
    /**
     * @var Planejamento
     */
    private $novoPlano;
    /**
     * @var Planejamento
     */
    private $planoOriginal;
    /**
     * @var array
     */
    private $deParaObjetivoPrograma = [];

    /**
     * @var array
     */
    private $deParaAreaResultado = [];

    /**
     * @var array
     */
    private $deParaObjetivoEstrategico = [];

    /**
     * @var integer[]
     */
    private $exercicios = [];

    /**
     * ReplicarPlanoService constructor.
     * @param Planejamento $novoPlano
     * @param Planejamento $planoOriginal
     */
    public function __construct(Planejamento $novoPlano, Planejamento $planoOriginal)
    {
        $this->novoPlano = $novoPlano;
        $this->exercicios = $this->novoPlano->execiciosPlanejamento();
        $this->planoOriginal = $planoOriginal;
    }

    public function replicar()
    {
        $this->replicarComissoes();
        $this->replicarAreasResultados();
        $this->replicarProgramas();
        $this->replicarReceitas();
    }

    /**
     * Replica as comissões do programa estratégico
     */
    private function replicarComissoes()
    {
        /**
         * @var Comissao $comissao
         */
        foreach ($this->planoOriginal->comissoes as $comissao) {
            $novaComissao = $comissao->replicate();
            $novaComissao->planejamento()->associate($this->novoPlano);
            $novaComissao->save();
        }
    }


    private function replicarProgramas()
    {
        /**
         * @var ProgramaEstrategico $programa
         */
        foreach ($this->planoOriginal->programas as $programa) {
            $this->replicarPrograma($programa);
        }
    }

    /**
     * @param ProgramaEstrategico $programa
     */
    private function replicarPrograma(ProgramaEstrategico $programa)
    {
        /**
         * @var ProgramaEstrategico $novoPrograma
         */
        $novoPrograma = $programa->replicate();
        $novoPrograma->planejamento()->associate($this->novoPlano);
        $novoPrograma->pl9_anoorcamento = $this->novoPlano->pl2_ano_inicial;
        $novoPrograma->save();


        $this->replicarValores($programa->getValores(), $novoPrograma->pl9_codigo, Valor::ORIGEM_PROGRAMA);

        $this->replicarOrgaosPrograma($programa->orgaos, $novoPrograma);
        $this->replicarObjetivosPrograma($programa->objetivos, $novoPrograma);
        $this->replicarIndicadoresPrograma($programa->indicadores, $novoPrograma);
        $this->replicarIniciativasPrograma($programa->iniciativas, $novoPrograma);

        if ($this->planoOriginal->pl2_composicao === 2) {
            $idsNovasAreaResultado = $programa->areasResultado->map(function (AreaResultado $areaResultado) {
                return $this->deParaAreaResultado[$areaResultado->pl4_codigo];
            });
            $novoPrograma->areasResultado()->sync($idsNovasAreaResultado);
        }

        if ($this->planoOriginal->pl2_composicao === 3) {
            $idsNovosObjetivos = $programa->objetivosEstrategicos->map(function (ObjetivoEstrategico $objetivo) {
                return $this->deParaObjetivoEstrategico[$objetivo->pl5_codigo];
            });

            $novoPrograma->objetivosEstrategicos()->sync($idsNovosObjetivos);
        }
    }

    /**
     * @param Collection $valores
     * @param int $chave
     * @param string $origem
     * @return void|null
     * @throws Exception
     */
    private function replicarValores(Collection $valores, $chave, $origem)
    {
        if ($this->novoPlano->pl2_tipo === 'PPA') {
            $exercicios = $this->novoPlano->execiciosPlanejamento();
            $novosValores = [];
            foreach ($exercicios as $exercicio) {
                $novosValores[] = (object) ["ano" => $exercicio, "valor" => 0];
            }

            $service = new ValoresService();
            $service->salvarColecao($novosValores, $origem, $chave);
            return;
        }

        if ($valores->isEmpty()) {
            return null;
        }
        foreach ($valores as $valor) {
            if (!in_array($valor->pl10_ano, $this->exercicios)) {
                continue;
            }

            $novoValor = $valor->replicate();
            $novoValor->pl10_chave = $chave;
            $novoValor->pl10_editadomanual = false;
            $novoValor->save();
        }

        $valor = $valores->last();
        $exercicioFinalPlano = $this->novoPlano->pl2_ano_final;
        if ($this->novoPlano->pl2_tipo === 'LDO' && $valor->pl10_ano < $exercicioFinalPlano) {
            $novoValor = $valor->replicate();
            $novoValor->pl10_valor = 0;
            $novoValor->pl10_chave = $chave;
            $novoValor->pl10_ano = $exercicioFinalPlano;
            $novoValor->pl10_editadomanual = false;
            $novoValor->save();
        }
    }

    /**
     * @param Collection $orgaos
     * @param ProgramaEstrategico $novoPrograma
     */
    private function replicarOrgaosPrograma(Collection $orgaos, ProgramaEstrategico $novoPrograma)
    {
        foreach ($orgaos as $orgao) {
            $novoOrgao = $orgao->replicate();
            $novoOrgao->pl27_anoorcamento = $this->novoPlano->pl2_ano_inicial;
            $novoOrgao->programaEstrategico()->associate($novoPrograma);
            $novoOrgao->save();
        }
    }

    /**
     * @param Collection $objetivos
     * @param ProgramaEstrategico $novoPrograma
     */
    private function replicarObjetivosPrograma(Collection $objetivos, ProgramaEstrategico $novoPrograma)
    {
        /**
         * @var ObjetivoProgramaEstrategico $objetivo
         */
        foreach ($objetivos as $objetivo) {
            $this->replicaObjetivo($objetivo, $novoPrograma);
        }
    }

    /**
     * @param ObjetivoProgramaEstrategico $objetivo
     * @param ProgramaEstrategico $novoPrograma
     */
    private function replicaObjetivo(ObjetivoProgramaEstrategico $objetivo, ProgramaEstrategico $novoPrograma)
    {
        /**
         * @var ObjetivoProgramaEstrategico $novoObjetivo
         */
        $novoObjetivo = $objetivo->replicate();
        $novoObjetivo->programaEstrategico()->associate($novoPrograma);
        $novoObjetivo->save();

        $this->replicarValores($objetivo->getValores(), $novoObjetivo->pl11_codigo, Valor::ORIGEM_OBJETIVOS);

        $this->replicarMetas($objetivo->metas, $novoObjetivo);

        $this->deParaObjetivoPrograma[$objetivo->pl11_codigo] = $novoObjetivo->pl11_codigo;
    }

    private function replicarMetas($metas, ObjetivoProgramaEstrategico $novoObjetivo)
    {
        /**
         * @var  MetasObjetivo $meta
         */
        foreach ($metas as $meta) {
            $novaMeta = $meta->replicate();
            $novaMeta->objetivo()->associate($novoObjetivo);
            $novaMeta->save();

            $this->replicarValores($meta->getValores(), $novaMeta->pl21_codigo, Valor::ORIGEM_META_OBJETIVO);
        }
    }

    private function replicarIndicadoresPrograma(Collection $indicadores, ProgramaEstrategico $novoPrograma)
    {
        /**
         * @var IndicadorProgramaEstrategico $indicador
         */
        foreach ($indicadores as $indicador) {
            $novoIndicador = $indicador->replicate();
            $novoIndicador->programaEstrategico()->associate($novoPrograma);
            $novoIndicador->save();
        }
    }

    /**
     * @param Collection $iniciativas
     * @param ProgramaEstrategico $novoPrograma
     */
    private function replicarIniciativasPrograma(Collection $iniciativas, ProgramaEstrategico $novoPrograma)
    {
        foreach ($iniciativas as $iniciativa) {
            $this->replicarIniciativaPrograma($iniciativa, $novoPrograma);
        }
    }

    /**
     *
     * @param Iniciativa $iniciativa
     * @param ProgramaEstrategico $novoPrograma
     */
    private function replicarIniciativaPrograma(Iniciativa $iniciativa, ProgramaEstrategico $novoPrograma)
    {
        /**
         * @var Iniciativa $novaIniciativa
         */
        $novaIniciativa = $iniciativa->replicate();
        $novaIniciativa->programaEstrategico()->associate($novoPrograma);
        $novaIniciativa->pl12_anoorcamento = $this->novoPlano->pl2_ano_inicial;
        $novaIniciativa->save();

        $this->replicarMetasIniciativa($iniciativa->metas, $novaIniciativa);

        $novaIniciativa->regionalizacoes()->sync(
            $iniciativa->regionalizacoes->map(function (Subtitulo $regionalizacao) {
                return $regionalizacao->o11_sequencial;
            })
        );

        $novaIniciativa->abrangencias()->sync(
            $iniciativa->abrangencias->map(function (Abrangencia $abrangencia) {
                return $abrangencia->pl18_codigo;
            })
        );

        $this->replicarDetalhamento($iniciativa->detalhamentoDespesa, $novaIniciativa);

        $idsNovosObjetivos = $iniciativa->objetivos->map(function (ObjetivoProgramaEstrategico $objetivoPrograma) {
            return $this->deParaObjetivoPrograma[$objetivoPrograma->pl11_codigo];
        });

        $novaIniciativa->objetivos()->sync($idsNovosObjetivos);
    }

    /**
     * @param Collection $metas
     * @param Iniciativa $novaIniciativa
     */
    private function replicarMetasIniciativa(Collection $metas, Iniciativa $novaIniciativa)
    {
        if ($metas->isEmpty()) {
            return null;
        }
        foreach ($metas as $meta) {
            $novaMetasIniciativa = $meta->replicate();
            /**
             * @var MetasIniciativa $novaMetasIniciativa
             */
            if (!in_array($novaMetasIniciativa->exercicio, $this->exercicios)) {
                continue;
            }
            $novaMetasIniciativa->iniciativa()->associate($novaIniciativa);
            $novaMetasIniciativa->save();
        }

        $meta = $metas->last();
        $exercicioFinalPlano = $this->novoPlano->pl2_ano_final;
        if ($this->novoPlano->pl2_tipo === 'LDO' && $meta->exercicio < $exercicioFinalPlano) {
            $novaMeta = $meta->replicate();
            $novaMeta->iniciativa()->associate($novaIniciativa);
            $novaMeta->exercicio = $exercicioFinalPlano;
            $novaMeta->save();
        }
    }

    /**
     * @param Collection $detalhamentosDespesa
     * @param Iniciativa $novaIniciativa
     */
    private function replicarDetalhamento(Collection $detalhamentosDespesa, Iniciativa $novaIniciativa)
    {
        foreach ($detalhamentosDespesa as $detalhamento) {
            /**
             * @var DetalhamentoDespesa $novoDetalhamento
             */
            $novoDetalhamento = $detalhamento->replicate();
            $novoDetalhamento->pl20_anoorcamento = $this->novoPlano->pl2_ano_inicial;
            $novoDetalhamento->iniciativa()->associate($novaIniciativa);
            $novoDetalhamento->save();

            $origem = Valor::ORIGEM_DETALHAMENTO_DESPESA;
            $this->replicarValores($detalhamento->getValores(), $novoDetalhamento->pl20_codigo, $origem);
            $this->replicarCronogramaDesembolsoDespesa($detalhamento->cronogramaDesembolso, $novoDetalhamento);
        }
    }

    /**
     * @param Collection $cronogramas
     * @param DetalhamentoDespesa $novoDetalhamento
     */
    private function replicarCronogramaDesembolsoDespesa(Collection $cronogramas, DetalhamentoDespesa $novoDetalhamento)
    {
        foreach ($cronogramas as $cronograma) {
            if (!in_array($cronograma->exercicio, $this->exercicios)) {
                continue;
            }
            /**
             * @var CronogramaDesembolsoDespesa $novoCronograma
             */
            $novoCronograma =  $cronograma->replicate();
            $novoCronograma->detalhamento()->associate($novoDetalhamento);
            $novoCronograma->save();
        }

        $ultimo = $cronogramas->last();
        $exercicioFinalPlano = $this->novoPlano->pl2_ano_final;
        if ($this->novoPlano->pl2_tipo === 'LDO' && $ultimo->exercicio < $exercicioFinalPlano) {
            $novoCronograma = $ultimo->replicate();
            $novoCronograma->exercicio = $exercicioFinalPlano;
            $novoCronograma->detalhamento()->associate($novoDetalhamento);
            $novoCronograma->save();
        }
    }

    private function replicarReceitas()
    {
        $this->replicarFatorCorrecaoReceita($this->planoOriginal->fatorCorrecaoReceita);
        $this->replicarEstimativaReceita($this->planoOriginal->estimativaReceita);
    }

    private function replicarFatorCorrecaoReceita(Collection $fatorCorrecaoReceita)
    {
        /**
         * @var FatorCorrecaoReceita $fatorCorrecao
         */
        if ($fatorCorrecaoReceita->isEmpty()) {
            return;
        }

        foreach ($fatorCorrecaoReceita as $fatorCorrecao) {
            $fonte = FonteReceita::where('o57_codfon', $fatorCorrecao->orcfontes_id)
                ->where('o57_anousu', $this->novoPlano->pl2_ano_inicial)
                ->first();

            if (is_null($fonte)) {
                continue;
            }

            if (!in_array($fatorCorrecao->exercicio, $this->exercicios)) {
                continue;
            }
            /**
             * @var FatorCorrecaoReceita $novoFator
             */
            $novoFator = $fatorCorrecao->replicate();
            $novoFator->planejamento()->associate($this->novoPlano);
            $novoFator->anoorcamento = $this->novoPlano->pl2_ano_inicial;
            $novoFator->save();
        }

        $fatorCorrecaoReceita = $fatorCorrecaoReceita->last();
        $exercicioFinalPlano = $this->novoPlano->pl2_ano_final;
        if ($this->novoPlano->pl2_tipo === 'LDO' && $fatorCorrecaoReceita->exercicio < $exercicioFinalPlano) {
            $novoValor = $fatorCorrecaoReceita->replicate();
            $novoValor->anoorcamento = $this->novoPlano->pl2_ano_inicial;
            $novoValor->exercicio = $exercicioFinalPlano;
            $novoValor->save();
        }
    }

    /**
     * Replica as estimativas das receitas
     * @param Collection $estimativasReceitas
     */
    private function replicarEstimativaReceita(Collection $estimativasReceitas)
    {
        /**
         * @var EstimativaReceita $estimativaReceita
         */
        foreach ($estimativasReceitas as $estimativaReceita) {
            $fonte = FonteReceita::where('o57_codfon', $estimativaReceita->orcfontes_id)
                ->where('o57_anousu', $this->novoPlano->pl2_ano_inicial)
                ->first();

            if (is_null($fonte)) {
                continue;
            }
            /**
             * @var EstimativaReceita $novaEstimativa
             */
            $novaEstimativa = $estimativaReceita->replicate();
            $novaEstimativa->planejamento()->associate($this->novoPlano);
            $novaEstimativa->anoorcamento = $this->novoPlano->pl2_ano_inicial;
            $novaEstimativa->save();

            $this->replicarCronogramaDesembolsoReceita($estimativaReceita->cronogramaDesembolso, $novaEstimativa);

            $this->replicarValores($estimativaReceita->getValores(), $novaEstimativa->id, Valor::ORIGEM_RECEITA);
        }
    }

    private function replicarAreasResultados()
    {
        foreach ($this->planoOriginal->areasResultado as $area) {
            $this->replicarAreaResultado($area);
        }
    }

    /**
     * @param AreaResultado $area
     */
    private function replicarAreaResultado(AreaResultado $area)
    {
        /**
         * @var AreaResultado $novaArea
         */
        $novaArea = $area->replicate();
        $novaArea->planejamento()->associate($this->novoPlano);
        $novaArea->save();

        $this->deParaAreaResultado[$area->pl4_codigo] = $novaArea->pl4_codigo;

        $this->replicarObjetivosEstrategicos($area->objetivosEstrategicos, $novaArea);
    }

    /**
     * @param Collection $objetivosEstrategicos
     * @param AreaResultado $novaArea
     */
    private function replicarObjetivosEstrategicos(Collection $objetivosEstrategicos, AreaResultado $novaArea)
    {
        /**
         * @var ObjetivoEstrategico $objetivo
         */
        foreach ($objetivosEstrategicos as $objetivo) {
            /**
             * @var ObjetivoEstrategico $novoObjetivo
             */
            $novoObjetivo = $objetivo->replicate();
            $novoObjetivo->areaResultado()->associate($novaArea);
            $novoObjetivo->save();

            $this->deParaObjetivoEstrategico[$objetivo->pl5_codigo] = $novoObjetivo->pl5_codigo;
        }
    }

    /**
     * @param Collection $cronogramas
     * @param EstimativaReceita $novaEstimativa
     */
    private function replicarCronogramaDesembolsoReceita(Collection $cronogramas, EstimativaReceita $novaEstimativa)
    {
        foreach ($cronogramas as $cronograma) {
            if (!in_array($cronograma->exercicio, $this->exercicios)) {
                continue;
            }
            /**
             * @var CronogramaDesembolsoReceita $novoCronograma
             */
            $novoCronograma =  $cronograma->replicate();
            $novoCronograma->estimativa()->associate($novaEstimativa);
            $novoCronograma->save();
        }

        $ultimo = $cronogramas->last();
        $exercicioFinalPlano = $this->novoPlano->pl2_ano_final;
        if ($this->novoPlano->pl2_tipo === 'LDO' && $ultimo->exercicio < $exercicioFinalPlano) {
            $novoCronograma = $ultimo->replicate();
            $novoCronograma->exercicio = $exercicioFinalPlano;
            $novoCronograma->estimativa()->associate($novaEstimativa);
            $novoCronograma->save();
        }
    }
}
