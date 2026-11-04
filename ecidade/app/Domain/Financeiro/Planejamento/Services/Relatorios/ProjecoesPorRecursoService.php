<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Financeiro\Planejamento\Models\DetalhamentoDespesa;
use App\Domain\Financeiro\Planejamento\Models\EstimativaReceita;
use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Models\ProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Relatorios\ProjecoesPorRecursoPdf;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * O obetivo desse service é compilar as projeções da despesa e receita filtrando por recurso
 * com o intuíto de ajudar o usuário a corrigir o planejamento em recursos já depreciados.
 */
class ProjecoesPorRecursoService
{
    /**
     * @var Planejamento
     */
    protected $planejamento;
    protected $idsOrctiporec = [];

    public function __construct(array $filtros)
    {
        if (empty($filtros['planejamento_id'])) {
            throw new Exception('O planejamento é uma informação obrigatória.');
        }

        if (empty($filtros['orctiporec_id'])) {
            throw new Exception('O recurso é uma informação obrigatória');
        }

        $this->planejamento = Planejamento::find($filtros['planejamento_id']);
        $this->idsOrctiporec = $filtros['orctiporec_id'];
    }

    public function emitir()
    {
        $projecoesDespesa = $this->processarDespesa();
        $projecoesReceita = $this->processarReceita();

        if (empty($projecoesDespesa) && $projecoesReceita->count() === 0) {
            throw new Exception('Sem registros para o filtro selecionado.');
        }


        $relatorio = new ProjecoesPorRecursoPdf();
        $relatorio->setHeaders([
            sprintf('Planejemanto: %s', $this->planejamento->pl2_titulo),
            sprintf('Tipo: %s', $this->planejamento->pl2_tipo)
        ]);
        $relatorio->setDados($projecoesDespesa, $projecoesReceita);

        return $relatorio->emitir();
    }

    /**
     * @return ProgramaEstrategico[]
     */
    private function processarDespesa()
    {
        $where = implode(' and ', [
            "pl9_planejamento = {$this->planejamento->pl2_codigo}",
            "pl20_recurso in (" . implode(', ', $this->idsOrctiporec) . ")"
        ]);
        $sql = "
        SELECT pl9_orcprograma,
               o54_descr,
               pl12_orcprojativ,
               o55_descr,
               pl20_instituicao,
               pl20_orcorgao,
               pl20_orcunidade,
               pl20_orcfuncao,
               pl20_orcsubfuncao,
               o56_elemento,
               pl20_recurso,
               pl20_concarpeculiar,
               pl20_subtitulo,
               pl20_esferaorcamentaria,
               o15_complemento,
               o15_recurso,
               codigo_siconfi,
               gestao
        from programaestrategico
        join orcprograma on (o54_anousu, o54_programa) = (pl9_anoorcamento, pl9_orcprograma)
        join iniciativaprojativ ON pl12_programaestrategico = pl9_codigo
        join orcprojativ on (o55_anousu, o55_projativ) = (pl12_anoorcamento, pl12_orcprojativ)
        join detalhamentoiniciativa ON pl20_iniciativaprojativ = pl12_codigo
        join orcelemento on (o56_codele, o56_anousu) = (pl20_orcelemento, pl20_anoorcamento)
        join orctiporec on o15_codigo = pl20_recurso
        join fonterecurso on orctiporec_id = o15_codigo
             and exercicio = pl20_anoorcamento
        where {$where}
        order by pl20_orcorgao, pl20_orcunidade, pl20_orcfuncao, pl20_orcsubfuncao, pl9_orcprograma,
                 o56_elemento, pl12_orcprojativ
        ";

        $dados = DB::select($sql);
        $projecoesDespesa = [];
        foreach ($dados as $dado) {
            $programa = $dado->pl9_orcprograma;
            if (!array_key_exists($programa, $projecoesDespesa)) {
                $projecoesDespesa[$programa] = $this->createSimpleStd($programa, $dado->o54_descr, 4);
                $projecoesDespesa[$programa]->iniciativas = [];
            }

            $projeto = $dado->pl12_orcprojativ;
            if (!array_key_exists($projeto, $projecoesDespesa[$programa]->iniciativas)) {
                $projecoesDespesa[$programa]->iniciativas[$projeto] = $this->createSimpleStd(
                    $projeto,
                    $dado->o55_descr,
                    4
                );
                $projecoesDespesa[$programa]->iniciativas[$projeto]->detalhamento = [];
            }

            $projecoesDespesa[$programa]->iniciativas[$projeto]->detalhamentos[] = (object)[
                "estrutural" => $this->geraEstrutural($dado),
                "siconfi" => $dado->codigo_siconfi,
                "recurso" => $dado->o15_recurso,
                "gestao" => $dado->gestao,
                "complemento" => str_pad($dado->o15_complemento, 4, '0', STR_PAD_LEFT),
            ];
        }

        return $projecoesDespesa;
    }

    /**
     * @return EstimativaReceita[]
     */
    private function processarReceita()
    {
        return EstimativaReceita::where('planejamento_id', $this->planejamento->pl2_codigo)
            ->whereIn('recurso_id', $this->idsOrctiporec)
            ->get();
    }

    private function createSimpleStd($codigo, $nome, $int)
    {
        return (object)[
            'codigo' => $codigo,
            'nome' => $nome,
            'formatado' => str_pad($codigo, $int, '0', STR_PAD_LEFT),
        ];
    }

    private function geraEstrutural($dado)
    {
        return implode('.', [
            str_pad($dado->pl20_orcorgao, 2, '0', STR_PAD_LEFT),
            str_pad($dado->pl20_orcunidade, 2, '0', STR_PAD_LEFT),
            str_pad($dado->pl20_orcfuncao, 2, '0', STR_PAD_LEFT),
            str_pad($dado->pl20_orcsubfuncao, 3, '0', STR_PAD_LEFT),
            str_pad($dado->pl9_orcprograma, 4, '0', STR_PAD_LEFT),
            str_pad($dado->pl12_orcprojativ, 4, '0', STR_PAD_LEFT),
            $dado->o56_elemento,
            $dado->codigo_siconfi,
            str_pad($dado->o15_complemento, 4, '0', STR_PAD_LEFT)
        ]);
    }
}
