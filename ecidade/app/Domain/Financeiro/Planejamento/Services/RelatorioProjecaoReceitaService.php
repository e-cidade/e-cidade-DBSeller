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

use App\Domain\Financeiro\Planejamento\Relatorios\DemonstrativoProjecaoReceita;
use App\Domain\Financeiro\Planejamento\Relatorios\DemonstrativoProjecaoReceitaCsv;
use App\Domain\Financeiro\Planejamento\Relatorios\DemonstrativoProjecaoReceitaPorRecurso;
use App\Domain\Financeiro\Planejamento\Services\Relatorios\ReceitaService;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RelatorioProjecaoReceitaService extends ReceitaService
{
    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
        $this->processar();
    }

    public function emitir()
    {
        return array_merge($this->emitirPdf(), $this->emitirCSV());
    }

    /**
     * @return array
     */
    public function emitirPdf()
    {
        $relatorio = new DemonstrativoProjecaoReceita();
        if ($this->filtros['agruparPorRecurso'] == 1) {
            $relatorio = new DemonstrativoProjecaoReceitaPorRecurso();
        }
        $relatorio->setDados($this->dados);
        return $relatorio->emitir();
    }

    public function emitirCSV()
    {
        $relatorio = new DemonstrativoProjecaoReceitaCsv();
        $relatorio->porRecurso($this->filtros['agruparPorRecurso'] == 1);
        $relatorio->setDados($this->dados);
        return $relatorio->emitir();
    }

    /**
     * @throws Exception
     */
    private function processar()
    {
        $this->processarFiltros();

        if (!empty($this->filtros['natureza'])) {
            $fonte = str_pad($this->filtros['natureza'], 15, '0', STR_PAD_RIGHT);
            $estrutural = new EstruturalReceita($fonte);
            $this->nivel = $estrutural->getNivel();
        }

        // constrói o array dos exercícios anteriores
        $exercicio = $this->planejamento->pl2_ano_inicial - 1;
        for ($i = 1; $i <= 4; $i++) {
            $this->exerciciosAnteriores[$exercicio] = $exercicio;
            $exercicio--;
        }

        $dados = $this->projecao();
        $this->organizaDados($dados);
    }

    private function projecao()
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
        $campos = array_merge([
            'orcfontes_id',
            'o70_codrec',
            'fonterecurso.codigo_siconfi as recurso',
            'fonterecurso.descricao as descricao_recurso',
            'o15_complemento as complemento',
            'valorbase as valor_base',
        ], $camposFonte);

        $outrosCampos = [
            DB::raw("
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
            ) as valores ")
        ];

        foreach ($this->exerciciosAnteriores as $exercicio) {
            $dataInicio = "{$exercicio}-01-01";
            $dataFim = "{$exercicio}-12-31";

            $sql = "
                (select coalesce(fc_receitasaldo_codfon[8], 0)
                  from (select
                    fc_receitasaldo_codfon($exercicio, o57_codfon, concarpeculiar_id, 3,'{$dataInicio}','$dataFim')
                  ) as x
                ) as arrecadado_{$exercicio}
            ";
            $outrosCampos[] = DB::raw($sql);
        }

        $estimativas = $this->buscarProjecao(array_merge($campos, $outrosCampos));

        if ($estimativas->count() === 0) {
            throw new Exception("Nenhuma receita encontrada para o filtro encontrado.", 403);
        }
        if ($this->filtros['agruparPorRecurso'] == 0) {
            return $this->montaArvoreEstrutural($estimativas);
        }
        return $this->agrupaPorRecurso($estimativas);
    }

    /**
     * @param array $dados
     */
    protected function organizaDados(array $dados)
    {
        parent::organizaDados($dados);
        $this->dados['exerciciosAnteriores'] = array_reverse($this->exerciciosAnteriores);
    }

    /**
     * @param Collection $dadosEstimativas
     * @return array
     */
    protected function agrupaPorRecurso(Collection $dadosEstimativas)
    {
        $recursos = [];

        foreach ($dadosEstimativas as $dadosEstimativa) {
            $recurso = "$dadosEstimativa->recurso#$dadosEstimativa->complemento";
            if (!array_key_exists($recurso, $recursos)) {
                $recursos[$recurso] = $this->builderRecurso(
                    $dadosEstimativa->recurso,
                    $dadosEstimativa->descricao_recurso,
                    $dadosEstimativa->complemento
                );
            }
            $recursos[$recurso]->valor_base += $dadosEstimativa->valor_base;

            $valores = \JSON::create()->parse($dadosEstimativa->valores);
            foreach ($valores as $valor) {
                $recursos[$recurso]->{"valor_{$valor->ano}"} += (float)$valor->valor;
                $this->totalizador[$valor->ano] += (float)$valor->valor;
            }

            foreach ($this->exerciciosAnteriores as $exercicio) {
                $propriedade = "arrecadado_{$exercicio}";
                $recursos[$recurso]->{$propriedade} += (float)$dadosEstimativa->{$propriedade};
            }
        }
        ksort($recursos);
        return $recursos;
    }

    protected function builderRecurso($recurso, $descricao, $complemento)
    {
        $std = (object)[
            'recurso' => "$recurso",
            'complemento' => "$complemento",
            'descricao' => $descricao,
            'sintetico' => false,
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
}
