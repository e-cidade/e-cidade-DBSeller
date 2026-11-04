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

use App\Domain\Financeiro\Planejamento\Models\DetalhamentoDespesa;
use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\MetasIniciativa;
use App\Domain\Financeiro\Planejamento\Models\ObjetivoProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Models\ProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use Exception;
use Illuminate\Support\Collection;

class CalcularValoresSinteticosService
{
    /**
     * @var Planejamento
     */
    protected $plano;

    /**
     * @var ValoresService
     */
    protected $valoresService;

    /**
     * @param $planejamentoId
     * @throws Exception
     */
    public function recalcular($planejamentoId)
    {
        $this->valoresService = new ValoresService();
        $this->plano = Planejamento::find($planejamentoId);
        $this->plano->programas->each(function (ProgramaEstrategico $programaEstrategico) {
            $this->recalcularValoresIniciativas($programaEstrategico);
            $this->recalcularValoresObjetivos($programaEstrategico);
            $this->recalcularPrograma($programaEstrategico);
        });
    }

    /**
     * @param ProgramaEstrategico $programaEstrategico
     */
    private function recalcularValoresIniciativas(ProgramaEstrategico $programaEstrategico)
    {
        $programaEstrategico->iniciativas->map(function (Iniciativa $iniciativa) {
            $valores = $iniciativa->detalhamentoDespesa->map(function (DetalhamentoDespesa $detalhamentoDespesa) {
                return $this->valoresPorAno($detalhamentoDespesa->getValores());
            });

            $valoresPorAno = $this->totalizaValoresPorAno($valores);
            $metas = $iniciativa->metas;
            if ($metas->count() === 0) {
                foreach ($valoresPorAno as $ano => $valor) {
                    $meta = new MetasIniciativa();
                    $meta->exercicio = $ano;
                    $meta->meta_financeira = $valor;
                    $meta->iniciativa()->associate($iniciativa);
                    $meta->save();
                }
            } else {
                $metas->each(function (MetasIniciativa $meta) use ($valoresPorAno) {
                    if (count($valoresPorAno) === 0) {
                        $meta->meta_financeira = 0;
                    } else {
                        $meta->meta_financeira = $valoresPorAno[$meta->exercicio];
                    }
                    $meta->save();
                });
            }
            $iniciativa->refresh();
        });
    }

    /**
     * @param ProgramaEstrategico $programaEstrategico
     * @throws Exception
     */
    private function recalcularValoresObjetivos(ProgramaEstrategico $programaEstrategico)
    {
        $programaEstrategico->objetivos->each(function (ObjetivoProgramaEstrategico $objetivo) {
            $valores = $this->totalizaValoresPorAno(
                $objetivo->iniciativas->map(function (Iniciativa $iniciativa) {
                    return $this->getValoresIniciativa($iniciativa);
                })
            );

            $this->salvarValores($objetivo->pl11_codigo, Valor::ORIGEM_OBJETIVOS, $valores);
            $objetivo->refresh();
        });
    }

    /**
     * @param ProgramaEstrategico $programaEstrategico
     * @throws Exception
     */
    private function recalcularPrograma(ProgramaEstrategico $programaEstrategico)
    {
        $valores = $this->totalizaValoresPorAno(
            $programaEstrategico->iniciativas->map(function (Iniciativa $iniciativa) {
                return $this->getValoresIniciativa($iniciativa);
            })
        );

        $this->salvarValores($programaEstrategico->pl9_codigo, Valor::ORIGEM_PROGRAMA, $valores);
        $programaEstrategico->refresh();
    }

    /**
     * @param Collection $valores
     * @return array|mixed
     */
    private function totalizaValoresPorAno(Collection $valores)
    {
        $porAno = [];
        $valores->each(function ($valor) use (&$porAno) {
            foreach ($valor as $ano => $value) {
                $porAno = $this->agrupa($ano, $value, $porAno);
            }
        });

        return $porAno;
    }

    /**
     * @param Collection $valores
     * @return array|mixed
     */
    private function valoresPorAno(Collection $valores)
    {
        $porAno = [];
        $valores->each(function (Valor $valor) use (&$porAno) {
            $porAno = $this->agrupa($valor->pl10_ano, $valor->pl10_valor, $porAno);
        });

        return $porAno;
    }

    /**
     * @param $ano
     * @param $valor
     * @param $array
     * @return mixed
     */
    private function agrupa($ano, $valor, $array)
    {
        if (!array_key_exists($ano, $array)) {
            $array[$ano] = 0;
        }
        $array[$ano] += $valor;
        return $array;
    }


    /**
     * @param integer $chave
     * @param string $origem
     * @param array $valores
     * @throws Exception
     */
    private function salvarValores($chave, $origem, $valores)
    {
        $this->valoresService->delete($chave, $origem);
        foreach ($valores as $ano => $valor) {
            $this->valoresService->salvar($origem, $chave, $valor, $ano);
        }
    }

    /**
     * @param Iniciativa $iniciativa
     * @return array|mixed
     */
    private function getValoresIniciativa(Iniciativa $iniciativa)
    {
        $valores = $iniciativa->metas->map(function (MetasIniciativa $meta) {
            return $this->agrupa($meta->exercicio, $meta->meta_financeira, []);
        });

        return $this->totalizaValoresPorAno($valores);
    }
}
