<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Financeiro\Planejamento\Models\IndicadorProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\MetasObjetivo;
use App\Domain\Financeiro\Planejamento\Models\ObjetivoProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\OrgaoPrograma;
use App\Domain\Financeiro\Planejamento\Models\ProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Services\ProgramaEstrategicoService;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class AcompanhamentoPpaService extends AcompanhamentoService
{

    /**
     * @var mixed
     */
    private $programas;

    protected function processarDados()
    {
        $service = new ProgramaEstrategicoService();
        $filtros = ['planejamento' => $this->planejamento->pl2_codigo];

        if (isset($this->filtros['orcprograma_id'])) {
            $filtros['orcprograma_id'] = $this->filtros['orcprograma_id'];
        }
        if (isset($this->filtros['orcorgao_id'])) {
            $filtros['orcorgao_id'] = $this->filtros['orcorgao_id'];
        }

        $this->programas = $service->buscar($filtros);
        if ($this->programas->count() === 0) {
            throw new Exception("Nenhum resultado encontrado para os filtros informados.", 403);
        }
    }

    protected function organizaDados()
    {
        $this->programas->each(function (ProgramaEstrategico $programaEstrategico) {
            $programaOrcamento = $programaEstrategico->getProgramaOrcamento();
            $programa = $programaOrcamento->toArray();
            $programa['formatado'] = $programaOrcamento->formataCodigo();
            $programa['valores'] = $programaEstrategico->getValores()->toArray();


            $programa['indicadores'] = $this->organizaIndicadores($programaEstrategico->indicadores);
            $programa['orgaos'] = $this->organizaOrgaos($programaEstrategico->orgaos);
            $programa['objetivos'] = $this->organizaObjetivos($programaEstrategico->objetivos);
            // iniciativas sem objetivos
            $programa['iniciativas'] = $this->buscaIniciativasSemObjetivos($programaEstrategico);

            $this->dados['programas'][] = $programa;
        });

        if (!$this->apresentarExecucao) {
            return $this->dados;
        }

        foreach ($this->dados['programas'] as &$programa) {
            $orgaos = collect($programa['orgaos'])->map(function ($orgao) {
                return $orgao['codigo'];
            })->toArray();

            foreach ($programa['objetivos'] as &$objetivo) {
                $this->calculaExecucaoIniciativas($programa, $orgaos, $objetivo['iniciativas']);
            }

            if (!empty($programa['iniciativas'])) {
                $this->calculaExecucaoIniciativas($programa, $orgaos, $programa['iniciativas']);
            }
        }
    }

    private function calculaExecucaoIniciativas($programa, $orgaos, &$iniciativas)
    {
        foreach ($iniciativas as &$iniciativa) {
            foreach ($iniciativa['metas'] as &$meta) {
                $meta['execucao'] = $this->calculaExecucao(
                    $programa,
                    $orgaos,
                    $iniciativa,
                    $meta['exercicio']
                );
            }
        }
    }

    /**
     * @param Collection $indicadores
     * @return array
     */
    private function organizaIndicadores(Collection $indicadores)
    {
        return $indicadores->map(function (IndicadorProgramaEstrategico $indicador) {
            return [
                'descricao' => $indicador->indicador->o10_descr,
                'unidade' => $indicador->indicador->o10_descrunidade,
                'ano' => $indicador->pl22_ano,
                'indice' => $indicador->pl22_indice,
            ];
        })->toArray();
    }

    /**
     * @param Collection $orgaos
     * @return array
     */
    private function organizaOrgaos(Collection $orgaos)
    {
        return $orgaos->map(function (OrgaoPrograma $orgaoPrograma) {
            $orgao = $orgaoPrograma->getOrgaoOrcamento();
            return [
                'codigo' => $orgao->o40_orgao,
                'formatado' => $orgao->formataCodigo(),
                'descricao' => $orgao->o40_descr
            ];
        })->toArray();
    }

    /**
     * @param Collection $objetivos
     * @return array
     */
    private function organizaObjetivos(Collection $objetivos)
    {
        return $objetivos->map(function (ObjetivoProgramaEstrategico $objetivoProgramaEstrategico) {
            $objetivoProgramaEstrategico->iniciativas->each(function (Iniciativa $iniciativa) {
                $iniciativa->metas;
                $iniciativa->origem;
                $iniciativa->periodo;
                $iniciativa->regionalizacoes;
                $iniciativa->abrangencias;
            });

            $objetivoProgramaEstrategico->iniciativas->sortBy('pl12_orcprojativ');

            $metas = $objetivoProgramaEstrategico->metas->map(function (MetasObjetivo $meta) {
                $array = $meta->toArray();
                $array['valores'] = $meta->getValores()->toArray();
                return $array;
            })->toArray();

            $objetivo = $objetivoProgramaEstrategico->toArray();
            $objetivo['metas'] = $metas;
            return $objetivo;
        })->toArray();
    }

    private function calculaExecucao($programa, $orgaos, $iniciativa, $exercicio)
    {
        $valor = 0;

        foreach ($this->execucaoDespesa[$exercicio] as $execucaoDespesa) {
            if ($execucaoDespesa->programa != $programa['o54_programa']) {
                continue;
            }

            if (!in_array($execucaoDespesa->orgao, $orgaos)) {
                continue;
            }

            if ($execucaoDespesa->projeto != $iniciativa['pl12_orcprojativ']) {
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

    /**
     * @param ProgramaEstrategico $programaEstrategico
     * @return \Illuminate\Database\Eloquent\Builder[]|Collection|\Illuminate\Support\Collection
     */
    private function buscaIniciativasSemObjetivos(ProgramaEstrategico $programaEstrategico)
    {
        $str = 'select 1 from iniciativaobjetivosprogramaestrategico where pl16_iniciativaprojativ = pl12_codigo';
        return Iniciativa::query()
            ->where('pl12_programaestrategico', $programaEstrategico->pl9_codigo)
            ->whereRaw("not exists($str)")
            ->orderBy('pl12_orcprojativ')
            ->get()
            ->map(function (Iniciativa $iniciativa) {
                $iniciativa->metas;
                $iniciativa->origem;
                $iniciativa->periodo;
                $iniciativa->regionalizacoes;
                $iniciativa->abrangencias;
                return $iniciativa;
            })->toArray();
    }
}
