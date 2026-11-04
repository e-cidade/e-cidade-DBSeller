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
use App\Domain\Financeiro\Planejamento\Builder\EstimativaReceitaCronogramaBuilder;
use App\Domain\Financeiro\Planejamento\Models\CronogramaDesembolsoReceita;
use App\Domain\Financeiro\Planejamento\Models\EstimativaReceita;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Manutencao\Cronograma\CronogramaRequest;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class CronogramaDesembolsoDespesaService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class CronogramaDesembolsoReceitaService extends CronogramaDesembolsoService
{
    /**
     * Deve receber um array de objeto com as seguintes propriedades
     * [{id: ,id_cronograma: ,exercicio_meta: ,janeiro: ,fevereiro: ,marco: ,abril: ,maio: ,junho: ,julho: ,
     * agosto: ,setembro: ,outubro: ,novembro: ,dezembro: ,}]
     * @param array $metas
     */
    public function salvarMetasArrecadacao(array $metas)
    {
        foreach ($metas as $meta) {
            $this->salvarFromObject($meta);
        }
    }

    public function recalcular($dados)
    {
        $exercicio = (int)$dados['exercicio'];

        foreach ($dados['estimativas'] as $idEstimativa) {
            $estimativa = EstimativaReceita::find($idEstimativa);
            $valorBase = $estimativa->getValores()->filter(function (Valor $valor) use ($exercicio) {
                if ($valor->pl10_ano === $exercicio) {
                    return $valor;
                }
            })->shift();

            $cronograma = $this->getCronograma($idEstimativa, $exercicio);
            switch ($dados['formula']) {
                case 1:
                    $this->salvar($cronograma, $valorBase->pl10_valor, $valorBase->pl10_ano);
                    break;
                case 2:
                    $cronograma = $this->zeraValores($cronograma);
                    $cronograma->{$dados['mes']} = $valorBase->pl10_valor;
                    $cronograma->save();
                    break;
                default:
                    throw new Exception("Fórmula desconhecida.");
            }
        }
    }

    /**
     * @param $dados
     * @return CronogramaDesembolsoReceita
     */
    public function salvarFromObject($dados)
    {
        $cronograma = CronogramaDesembolsoReceita::find($dados->id);

        $cronograma->estimativa()->associate(EstimativaReceita::find($dados->estimativareceita_id));
        $this->update($cronograma, $dados);

        $cronograma->save();
        return $cronograma;
    }

    /**
     * @param EstimativaReceita $estimativa
     * @return EstimativaReceita
     */
    public function criarCronogramaDesembolso(EstimativaReceita $estimativa)
    {
        $estimativa->getValores()->map(function (Valor $valor) use ($estimativa) {
            $this->criarCronogramaExercicio($estimativa, $valor->pl10_valor, $valor->pl10_ano);
        });

        return $estimativa;
    }

    /**
     * Cria a projeção de cada exercício
     *
     * @param EstimativaReceita $estimativa
     * @param $valor
     * @param $exercicio
     * @return void
     */
    public function criarCronogramaExercicio(EstimativaReceita $estimativa, $valor, $exercicio)
    {
        $cronograma = new CronogramaDesembolsoReceita();
        $cronograma->estimativa()->associate($estimativa);
        $this->salvar($cronograma, $valor, $exercicio);
    }

    public function getPorRequest(CronogramaRequest $request)
    {
        $planejamento = Planejamento::find($request->get('planejamento_id'));
        $estimativas = $this->getEstimativas($planejamento, $request->get('exercicio'));

        return $this->montaArvore($planejamento, $estimativas, $request->get('DB_anousu'));
    }

    /**
     * @param Planejamento $planejamento
     * @param $exercicio
     * @return array
     */
    private function getEstimativas(Planejamento $planejamento, $exercicio)
    {
        $planejamento_id = $planejamento->pl2_codigo;

        $sql = "
        SELECT valores.pl10_valor as novo_valor_base,
               coalesce(fatorcorrecaoreceita.percentual, 0) as inflator,
               estimativareceita.*,
               cronogramadesembolsoreceita.id as id_cronograma,
               cronogramadesembolsoreceita.exercicio as exercicio,
               coalesce(janeiro, 0) as janeiro,
               coalesce(fevereiro, 0) as fevereiro,
               coalesce(marco, 0) as marco,
               coalesce(abril, 0) as abril,
               coalesce(maio, 0) as maio,
               coalesce(junho, 0) as junho,
               coalesce(julho, 0) as julho,
               coalesce(agosto, 0) as agosto,
               coalesce(setembro, 0) as setembro,
               coalesce(outubro, 0) as outubro,
               coalesce(novembro, 0) as novembro,
               coalesce(dezembro, 0) as dezembro,
               orcfontes.o57_fonte AS fonte,
               orcfontes.o57_descr AS descricao_fonte,
               orcorgao.o40_descr AS descricao_orgao,
               orcunidade.o41_descr AS descricao_unidade,
               concarpeculiar.c58_descr AS caracteristica_peculiar,
               db_config.nomeinst AS nome_instituicao,
               fonterecurso.gestao AS fonte_recurso,
               orctiporec.o15_descr AS recurso,
               orctiporec.o15_complemento AS codigo_complemento,
               o200_descricao AS complemento,
               CASE c60_identificadoresultadoprimario
                   WHEN 1 THEN 'Financeiro'
                   WHEN 2 THEN 'Primário'
                   WHEN 3 THEN 'Primária Obrigatória'
                   WHEN 4 THEN 'Primária Discricionária'
                   ELSE 'Não se Aplica'
               END AS identificador_resultado,
               CASE esferaorcamentaria
                   WHEN 10 THEN 'F - Orçamento Fiscal'
                   WHEN 20 THEN 'S - Orçamento da Seguridade Social'
                   WHEN 30 THEN 'I - Orçamento de Investimento'
                   ELSE 'Não se Aplica'
               END AS esfera_orcamentaria
        FROM planejamento.estimativareceita
        JOIN orcamento.orcunidade ON (o41_anousu, o41_orgao, o41_unidade) = (anoorcamento, orcorgao_id,orcunidade_id)
        JOIN orcamento.orcorgao ON (o40_anousu, o40_orgao) = (anoorcamento, orcorgao_id)
        JOIN contabilidade.concarpeculiar ON c58_sequencial = estimativareceita.concarpeculiar_id
        JOIN configuracoes.db_config ON db_config.codigo = estimativareceita.instituicao_id
        JOIN orcamento.orcfontes ON (o57_codfon, o57_anousu) = (orcfontes_id, anoorcamento)
        JOIN orcamento.orctiporec ON orctiporec.o15_codigo = estimativareceita.recurso_id
        JOIN orcamento.fonterecurso ON fonterecurso.orctiporec_id = orctiporec.o15_codigo
             and fonterecurso.exercicio = anoorcamento
        JOIN complementofonterecurso ON complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
        JOIN contabilidade.conplanoorcamento ON (c60_codcon, c60_anousu) = (orcfontes_id, anoorcamento)
        join planejamento.valores
              on pl10_origem = 'RECEITA'
             AND pl10_chave = estimativareceita.id
             and pl10_ano = {$exercicio}
        left join planejamento.fatorcorrecaoreceita
              on fatorcorrecaoreceita.planejamento_id = estimativareceita.planejamento_id
             AND fatorcorrecaoreceita.orcfontes_id = o57_codfon
             and fatorcorrecaoreceita.exercicio = {$exercicio}
        left join planejamento.cronogramadesembolsoreceita
                   on cronogramadesembolsoreceita.estimativareceita_id = estimativareceita.id
                  and cronogramadesembolsoreceita.exercicio = {$exercicio}
        WHERE estimativareceita.planejamento_id = {$planejamento_id}
        ORDER BY fonte
        ";

        return DB::select($sql);
    }

    private function montaArvore(Planejamento $planejamento, array $estimativas, $anoSessao)
    {
        $anoPrevisao = $planejamento->pl2_ano_inicial;
        $fontesReceitas = FonteReceita::where('o57_anousu', '=', $anoPrevisao)->get();

        foreach ($estimativas as $estimativa) {
            $estrutural = new EstruturalReceita($estimativa->fonte);
            $nivel = $estrutural->getNivel();
            $fonte = $estrutural->getEstrutural();

            $estruturalPaiDesdobramento = null;
            $temDesdobramento = false;

            if (FonteReceita::hasDesdobramento($estimativa->orcfontes_id, $estimativa->anoorcamento)) {
                $estruturalPaiDesdobramento = $estrutural->getCodigoEstruturalPai();
                $temDesdobramento = true;
            }

            $builder = new EstimativaReceitaCronogramaBuilder();
            $receita = $builder->buildAnalitico($estimativa, $estrutural, $temDesdobramento);
            $cp = $estimativa->concarpeculiar_id;
            $hash = "$fonte#$cp";
            $receitas[$hash] = $receita;
            while ($nivel != 1) {
                $estrutural = new EstruturalReceita($estrutural->getCodigoEstruturalPai());

                $fonte = $estrutural->getEstrutural();
                $nivel = $estrutural->getNivel();

                if (!array_key_exists($fonte, $receitas)) {
                    // localiza a fonte de receita para setar a descrição
                    $fonteReceita = $fontesReceitas->filter(function (FonteReceita $fonteReceita) use ($fonte) {
                        return $fonteReceita->o57_fonte === $fonte;
                    })->shift();

                    if (is_null($fonteReceita)) {
                        $msg = sprintf(
                            "Não foi encontrado a Natureza de Receita: %s. Acesse: %s",
                            $fonte,
                            "DB:FINANCEIRO > Contabilidade > Cadastros > Plano de Contas Orçamentário > Inclusão"
                        );
                        throw new Exception($msg);
                    }

                    $builder = new EstimativaReceitaCronogramaBuilder();
                    $receitas[$fonte] = $builder->buildSintetico($estrutural, []);
                    $receitas[$fonte]->descricao_fonte = $fonteReceita->o57_descr;
                }

                $receitas[$fonte]->valor_base += $receita->valor_base;

                $receitas[$fonte]->janeiro += $receita->janeiro;
                $receitas[$fonte]->fevereiro += $receita->fevereiro;
                $receitas[$fonte]->marco += $receita->marco;
                $receitas[$fonte]->abril += $receita->abril;
                $receitas[$fonte]->maio += $receita->maio;
                $receitas[$fonte]->junho += $receita->junho;
                $receitas[$fonte]->julho += $receita->julho;
                $receitas[$fonte]->agosto += $receita->agosto;
                $receitas[$fonte]->setembro += $receita->setembro;
                $receitas[$fonte]->outubro += $receita->outubro;
                $receitas[$fonte]->novembro += $receita->novembro;
                $receitas[$fonte]->dezembro += $receita->dezembro;

                if (!is_null($estruturalPaiDesdobramento)
                    && $estruturalPaiDesdobramento === $estrutural->getEstruturalComMascara()
                    && count($receitas[$fonte]->contasDesdobramento) === 0) {
                    $estruturalAteNivel = $estrutural->getEstruturalAteNivel();
                    $receitas[$fonte]->contasDesdobramento = getDesdobramentosReceita($estruturalAteNivel, $anoSessao);
                }

                // Na receita analática indexa as contas que são pai da mesma
                $receita->fontesPai[] = $fonte;
            }
        }

        ksort($receitas);
        return $receitas;
    }

    /**
     * @param $idDetalhamento
     * @param $exercicio
     * @return mixed
     */
    private function getCronograma($idDetalhamento, $exercicio)
    {
        return CronogramaDesembolsoReceita::query()
            ->where('estimativareceita_id', '=', $idDetalhamento)
            ->where('exercicio', '=', $exercicio)
            ->get()
            ->shift();
    }

    /**
     * Recalcula o cronograma da estimativa informada conforme o valor da meta anual
     * @param EstimativaReceita $estimativa
     * @return void
     */
    public function recalcularEstimativa(EstimativaReceita $estimativa)
    {
        $estimativa->getValores()->each(function (Valor $valor) use ($estimativa) {
            $cronograma = $this->getCronograma($estimativa->id, $valor->pl10_ano);

            if (is_null($cronograma)) {
                $cronograma = new CronogramaDesembolsoReceita();
                $cronograma->estimativa()->associate($estimativa);
                $this->salvar($cronograma, $valor->pl10_valor, $valor->pl10_ano);
            } else {
                $this->updateRateioAutomatico($cronograma, $valor);
            }
        });
    }
}
