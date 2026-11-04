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

namespace App\Domain\Educacao\Escola\Services;

use App\Domain\Educacao\Escola\Models\CriterioOrdenacaoTransposicao;
use App\Domain\Educacao\Escola\Models\DesignadoTransposicao;
use App\Domain\Educacao\Escola\Models\Transposicao;
use App\Domain\Educacao\Escola\Models\VagasTransposicao;
use App\Domain\Educacao\Escola\Resources\TransposicaoResource;
use DBDate;
use DBException;
use Exception;
use Illuminate\Support\Facades\DB;

class ProcessarTransposicaoService
{
    /**
     * @var TransposicaoService
     */
    private $service;
    /**
     * @var []
     */
    private $criterios;
    /**
     * @var Transposicao
     */
    private $transposicao;

    public function __construct()
    {
        $this->service = new TransposicaoService();
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function processarDesignacao(Transposicao $transposicao)
    {
        $this->transposicao = $transposicao;

        $inscritos = $this->service->buscarInscritos($transposicao->ed353_ano);
        $this->carregarCriterios($inscritos['etapas']);

        $opcoes = [];
        foreach ($inscritos as $inscrito) {
            if (is_object($inscrito)) {
                $inscrito = $this->getPontuacaoInscricao($inscrito);
                if (!array_key_exists($inscrito->opcao, $opcoes)) {
                    $opcoes[$inscrito->opcao] = (object)[
                        "escolas" => []
                    ];
                }

                if (!array_key_exists($inscrito->escola_codigo, $opcoes[$inscrito->opcao]->escolas)) {
                    $opcoes[$inscrito->opcao]->escolas[$inscrito->escola_codigo] = (object)[
                        "etapas" => []
                    ];
                }
                $opcoes[$inscrito->opcao]
                ->escolas[$inscrito->escola_codigo]
                ->etapas[$inscrito->etapa_codigo][] = $inscrito;
            }
        }

        //ordenar listas antes de designar
        ksort($opcoes);
        foreach ($opcoes as $opcaoLista) {
            ksort($opcaoLista->escolas);

            foreach ($opcaoLista->escolas as $escola) {
                ksort($escola->etapas);

                foreach ($escola->etapas as $codigoEtapa => $etapa) {
                    usort($etapa, function ($inscritoA, $inscritoB) {
                        // Comparação de pontuação (ordem descendente)
                        if ($inscritoA->pontuacao > $inscritoB->pontuacao) {
                            return -1;
                        } elseif ($inscritoA->pontuacao < $inscritoB->pontuacao) {
                            return 1;
                        }

                        // Comparação de data de nascimento (mais velho primeiro)
                        $dataNascimentoA = $inscritoA->aluno_datanascimento->getDate(DBDate::DATA_EN);
                        $dataNascimentoB = $inscritoB->aluno_datanascimento->getDate(DBDate::DATA_EN);

                        if ($dataNascimentoA < $dataNascimentoB) {
                            return -1;
                        } elseif ($dataNascimentoA > $dataNascimentoB) {
                            return 1;
                        }

                        // Mantém a ordem original se pontuação e data de nascimento forem iguais
                        return 0;
                    });

                    $escola->etapas[$codigoEtapa] = $etapa;
                }
            }
        }

        $this->designar($opcoes);

        $transposicao->ed353_processada = true;
        $transposicao->save();
    }

    /**
     * @param $inscrito
     * @return mixed
     * @throws Exception
     */
    private function getPontuacaoInscricao($inscrito)
    {
        $array = [];
        if (!array_key_exists($inscrito->aluno->getCodigo(), $array)) {
            $array[$inscrito->aluno->getCodigo()] = $inscrito->aluno->getNome();
        }

        $etapaInscricao = $inscrito->candidatoOpcoes['ed349_etapa'];

        if (!array_key_exists($etapaInscricao, $this->criterios)) {
            throw new Exception("Não foi configurado critérios de ordenação para a etapa {$inscrito->etapa}");
        }

        $criteriosEtapa = $this->criterios[$etapaInscricao];

        $criterioTransposicaoService = new CriterioTransposicaoService();
        $inscrito->pontuacao = $criterioTransposicaoService->calcularPontuacao($inscrito, $criteriosEtapa);

        return $inscrito;
    }

    private function carregarCriterios($etapas)
    {
        $ordemCriterioPorEtapa = [];

        foreach ($etapas as $etapa) {
            $ordemCriterioPorEtapa[] =
                DB::table('escola.criteriostransposicaoordenacao')
                    ->join('escola.criteriostransposicao', 'ed352_criterio', '=', 'ed351_codigo')
                    ->where('ed352_ano', '=', $this->transposicao->ed353_ano)
                    ->where('ed352_etapa', '=', $etapa->getCodigo())
                    ->select(['ed352_etapa', 'ed352_criterio', 'ed351_descricao', 'ed352_ordem'])
                    ->orderBy('ed352_ordem')
                    ->get();
        }

        $this->criterios = TransposicaoResource::criterios($ordemCriterioPorEtapa);
    }

    /**
     * @param array $opcoes
     * @return void
     */
    private function designar($opcoes)
    {
        $designados = DesignadoTransposicao::query()
            ->where('ed354_transposicao', $this->transposicao->ed353_codigo)
            ->pluck('ed354_aluno')
            ->toArray();

        foreach ($opcoes as $opcaoLista) {
            foreach ($opcaoLista->escolas as $escola) {
                foreach ($escola->etapas as $etapa) {
                    foreach ($etapa as $inscrito) {
                        if (in_array($inscrito->aluno_codigo, $designados)) {
                            continue;
                        }
                        $ano = $inscrito->candidatoOpcoes->ed349_ano;
                        $vagas = VagasTransposicao::query()
                            ->ano($ano)
                            ->escola($inscrito->escola_codigo)
                            ->etapa($inscrito->etapa_codigo)
                            ->first();
                        if (is_null($vagas)) {
                            continue;
                        }

                        if ($inscrito->aluno_necessidadeespecial && $vagas->ed350_saldopcd > 0) {
                            $this->salvarDesignado($inscrito);
                            $designados[] = $inscrito->aluno_codigo;
                            $vagas->ed350_saldopcd--;
                            $vagas->save();
                        }

                        if (!$inscrito->aluno_necessidadeespecial && $vagas->ed350_saldo > 0) {
                            $this->salvarDesignado($inscrito);
                            $designados[] = $inscrito->aluno_codigo;
                            $vagas->ed350_saldo--;
                            $vagas->save();
                        }
                    }
                }
            }
        }
    }

    private function salvarDesignado($inscrito)
    {
        $designadoTransposicao = new DesignadoTransposicao();
        $designadoTransposicao->transposicao()->associate($this->transposicao);
        $designadoTransposicao->ed354_aluno = $inscrito->aluno_codigo;
        $designadoTransposicao->ed354_escoladestino = $inscrito->escola_codigo;
        $designadoTransposicao->ed354_etapadestino = $inscrito->etapa_codigo;
        $designadoTransposicao->ed354_opcao = $inscrito->opcao;
        $designadoTransposicao->ed354_pontuacao = $inscrito->pontuacao;
        $designadoTransposicao->ed354_deficiente = $inscrito->aluno_necessidadeespecial;

        $designadoTransposicao->save();
    }
}
