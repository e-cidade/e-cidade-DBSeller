<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Financeiro\Planejamento\Models\DetalhamnentoPlanejamento;
use Illuminate\Database\Eloquent\Collection;
use JSON;

class AcompanhamentoLdoService extends AcompanhamentoService
{

    private $detalhamento;

    protected function processarDados()
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
        ];

        $this->detalhamento = DetalhamnentoPlanejamento::where('planejamento_id', $this->planejamento->pl2_codigo)
            ->select($campos)
            ->vinculoOrcamento($this->filtros)
            ->orderBy('pl20_orcorgao')
            ->orderBy('pl20_orcunidade')
            ->orderBy('pl20_orcfuncao')
            ->orderBy('pl20_orcsubfuncao')
            ->orderBy('pl9_orcprograma')
            ->orderBy('pl12_orcprojativ')
            ->get();
    }

    protected function organizaDados()
    {
        $dados = [];
        foreach ($this->detalhamento as $detalhamento) {
            $hashOrgao = $detalhamento->pl20_orcorgao;
            $unidade = $detalhamento->pl20_orcunidade;
            $hashFuncao = $detalhamento->pl20_orcfuncao;
            $hashSubfuncao = $detalhamento->pl20_orcsubfuncao;
            $hashPrograma = $detalhamento->pl9_orcprograma;

            if (!array_key_exists($hashOrgao, $dados)) {
                $dados[$hashOrgao] = $this->montarOrgao($detalhamento);
            }

            if (!array_key_exists($unidade, $dados[$hashOrgao]->unidades)) {
                $dados[$hashOrgao]->unidades[$unidade] = $this->montarUnidades($detalhamento);
            }
            if (!array_key_exists($hashFuncao, $dados[$hashOrgao]->unidades[$unidade]->funcoes)) {
                $dados[$hashOrgao]->unidades[$unidade]->funcoes[$hashFuncao] = $this->montarFuncao($detalhamento);
            }

            $funcao = &$dados[$hashOrgao]->unidades[$unidade]->funcoes[$hashFuncao];
            if (!array_key_exists($hashSubfuncao, $funcao->subfuncoes)) {
                $funcao->subfuncoes[$hashSubfuncao] = $this->montarSubfuncao($detalhamento);
            }

            $subfuncao = &$funcao->subfuncoes[$hashSubfuncao];
            if (!array_key_exists($hashPrograma, $subfuncao->programas)) {
                $subfuncao->programas[$hashPrograma] = $this->montarProgramas($detalhamento);
            }

            $programa = &$subfuncao->programas[$hashPrograma];
            $objetivo = $this->buscaIdentificadorObjetivo($detalhamento);
            if (!array_key_exists($objetivo->codigo, $programa->objetivos)) {
                $programa->objetivos[$objetivo->codigo] = $objetivo;
            }

            $iniciativas = &$programa->objetivos[$objetivo->codigo]->iniciativas;
            $iniciativa = $this->montarIniciativa($detalhamento);

            // se é a primeira vez que a iniciativa está sendo processada adiciona
            if (!array_key_exists($iniciativa->codigo, $iniciativas)) {
                $iniciativas[$iniciativa->codigo] = $iniciativa;
            } else {
                // se não só soma os valores
                foreach ($iniciativa->valores as $valor) {
                    $iniciativas[$iniciativa->codigo]->valores[$valor->ano]->valor += $valor->valor;
                }
            }

            // totaliza o órgão
            foreach ($iniciativa->valores as $valor) {
                $valorLivre = 0;
                $valorVinculado = $valor->valor;
                if ($iniciativa->recurso_livre) {
                    $valorLivre = $valor->valor;
                    $valorVinculado = 0;
                }
                $dados[$hashOrgao]->valores[$valor->ano]->valorLivre += $valorLivre;
                $dados[$hashOrgao]->valores[$valor->ano]->valorVinculado += $valorVinculado;
            }
        }

        $this->dados['detalhamentos'] = $dados;
        if (empty($dados)) {
            throw new \Exception("Nenhum resultado encontrado para os filtros informados.", 403);
        }

        if (!$this->apresentarExecucao) {
            return $this->dados;
        }

        foreach ($this->dados['detalhamentos'] as &$hashOrgao) {
            foreach ($hashOrgao->unidades as &$unidade) {
                foreach ($unidade->funcoes as &$funcao) {
                    foreach ($funcao->subfuncoes as &$subfuncao) {
                        foreach ($subfuncao->programas as &$programa) {
                            foreach ($programa->objetivos as &$objetivo) {
                                foreach ($objetivo->iniciativas as &$iniciativa) {
                                    foreach ($iniciativa->valores as &$valor) {
                                        $execucao = $this->calculaExecucao(
                                            $hashOrgao,
                                            $unidade,
                                            $funcao,
                                            $subfuncao,
                                            $programa,
                                            $iniciativa,
                                            $valor->ano
                                        );

                                        // totaliza execução da iniciativa
                                        $valor->execucao += $execucao;
                                        // totaliza execução por órgão
                                        $hashOrgao->valores[$valor->ano]->execucao += $execucao;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->dados['detalhamentos'];
    }

    private function montarIniciativa($detalhamento)
    {
        $regionalizacoes = $detalhamento->iniciativa->regionalizacoes->map(function ($regionalizacao) {
            return $regionalizacao->o11_descricao;
        })->implode(', ');

        $metas = [];
        foreach ($detalhamento->iniciativa->metas as $meta) {
            $metas[$meta->exercicio] = (object)[
                "exercicio" => $meta->exercicio,
                "meta_financeira" => $meta->meta_financeira,
                "unidade" => $meta->unidade,
                "meta_fisica" => $meta->meta_fisica,
            ];
        }

        $valores = [];
        foreach ($this->planejamento->execiciosPlanejamento() as $exercicio) {
            $valores[$exercicio] = (object) [
                "ano" => $exercicio,
                "valor" => 0,
                "execucao" => 0,
            ];
        }

        $parciarValores = JSON::create()->parse($detalhamento->valores);

        foreach ($parciarValores as $valor) {
            if (array_key_exists($valor->ano, $valores)) {
                $valores[$valor->ano]->valor = $valor->valor;
            }
        }

        $acao = $detalhamento->iniciativa->getIniciativaOrcamento();

        return (object)[
            'codigo' => $detalhamento->pl12_orcprojativ,
            'formatado' => str_pad($detalhamento->pl12_orcprojativ, 4, '0', STR_PAD_LEFT),
            'descricao' => $detalhamento->descricao_iniciativa,
            'produto' => $acao->produto->o22_descrprod,
            'recurso_livre' => $detalhamento->o15_tipo == 1,
            'regionalizacoes' => $regionalizacoes,
            'metas' => $metas,
            'valores' => $valores
        ];
    }

    private function buscaIdentificadorObjetivo($detalhamento)
    {
        /**
         * @var Collection $objetivos
         */
        $objetivos = $detalhamento->iniciativa->objetivos;

        if ($objetivos->isEmpty()) {
            return (object) [
                'codigo' => 'xxx',
                'descricao' => '',
                'iniciativas' => []
            ];
        }

        return (object) [
            'codigo' => $objetivos[0]->pl11_codigo,
            'descricao' => $objetivos[0]->pl11_descricao,
            'iniciativas' => []
        ];
    }

    private function calculaExecucao($orgao, $unidade, $funcao, $subfuncao, $programa, $iniciativa, $ano)
    {
        $valor = 0;

        foreach ($this->execucaoDespesa[$ano] as $execucaoDespesa) {
            if ($execucaoDespesa->orgao != $orgao->codigo) {
                continue;
            }
            if ($execucaoDespesa->unidade != $unidade->codigo) {
                continue;
            }
            if ($execucaoDespesa->funcao != $funcao->codigo) {
                continue;
            }
            if ($execucaoDespesa->subfuncao != $subfuncao->codigo) {
                continue;
            }
            if ($execucaoDespesa->programa != $programa->codigo) {
                continue;
            }
            if ($execucaoDespesa->projeto != $iniciativa->codigo) {
                continue;
            }

            $propriedade = 'empenhado_liquido_acumulado';
            if ($this->filtros['execucaoPor'] === 'liquidado') {
                $propriedade = 'liquidado_acumulado';
            }

            $valor += $execucaoDespesa->$propriedade;
        }

        return $valor;
    }

    private function montarOrgao($detalhamento)
    {
        return (object)[
            'codigo' => $detalhamento->pl20_orcorgao,
            'formatado' => str_pad($detalhamento->pl20_orcorgao, 2, '0', STR_PAD_LEFT),
            'descricao' => $detalhamento->descricao_orgao,
            'valores' => $this->montarValores(),
            'unidades' => []
        ];
    }

    private function montarValores()
    {
        $exercicios = [];
        foreach ($this->planejamento->execiciosPlanejamento() as $exercicio) {
            $exercicios[$exercicio] = (object)[
                'ano' => $exercicio,
                'valorLivre' => 0,
                'valorVinculado' => 0,
                'execucao' => 0,
            ];
        }
        return $exercicios;
    }

    private function montarUnidades($detalhamento = null)
    {
        return (object)[
            'codigo' => $detalhamento->pl20_orcunidade,
            'formatado' => str_pad($detalhamento->pl20_orcunidade, 2, '0', STR_PAD_LEFT),
            'descricao' => $detalhamento->descricao_unidade,
            'funcoes' => []
        ];
    }

    private function montarFuncao($detalhamento)
    {
        return (object)[
            'codigo' => $detalhamento->pl20_orcfuncao,
            'formatado' => str_pad($detalhamento->pl20_orcfuncao, 2, '0', STR_PAD_LEFT),
            'descricao' => $detalhamento->descricao_funcao,
            'subfuncoes' => []
        ];
    }

    private function montarSubfuncao($detalhamento)
    {
        return (object)[
            'codigo' => $detalhamento->pl20_orcsubfuncao,
            'formatado' => str_pad($detalhamento->pl20_orcsubfuncao, 3, '0', STR_PAD_LEFT),
            'descricao' => $detalhamento->descricao_subfuncao,
            'programas' => []
        ];
    }

    private function montarProgramas($detalhamento)
    {
        return (object)[
            'codigo' => $detalhamento->pl9_orcprograma,
            'formatado' => str_pad($detalhamento->pl9_orcprograma, 4, '0', STR_PAD_LEFT),
            'descricao' => $detalhamento->descricao_programa,
            'objetivos' => []
        ];
    }
}
