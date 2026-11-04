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

use App\Domain\Financeiro\Planejamento\Models\Comissao;
use App\Domain\Financeiro\Planejamento\Models\IndicadorProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\MetasObjetivo;
use App\Domain\Financeiro\Planejamento\Models\ObjetivoProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\OrgaoPrograma;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Models\ProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Relatorios\ProgramasTematicosPdf;
use ECidade\Financeiro\Teste;
use Exception;
use FpdfMultiCellBorder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Class RelatorioProgramaEstrategicoService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class RelatorioProgramaTematicoService
{
    /**
     * @var Planejamento
     */
    private $planejamento;

    /**
     * @var Request
     */
    private $request;
    /**
     * @var Collection|ProgramaEstrategico[]
     */
    private $programasTematicos;
    /**
     * @var FpdfMultiCellBorder
     */
    private $pdf;
    /**
     * @var array
     */
    private $execiciosPlanejamento;

    private $dados;
    /**
     * @var array
     */
    private $filtros = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->processar();
    }

    /**
     * @throws Exception
     */
    public function processar()
    {
        $idPlanejamento = $this->request->get('planejamento_id');

        $this->planejamento = Planejamento::find($idPlanejamento);

        $this->filtros = [
            'apresentaValoresMetaObjetivo' => $this->request->get('apresentaValoresMetaObjetivo'),
            'apresentaRegionalizacao' => $this->request->get('apresentaRegionalizacao'),
            'apresentaProduto' => $this->request->get('apresentaProduto'),
            'apresentaValoresMetaFisicas' => $this->request->get('apresentaValoresMetaFisicas'),
            'isPPA' => ($this->planejamento->pl2_tipo === 'PPA'),
            'apresentaIdentidadeOrganizacional' => $this->request->get('apresentaIdentidadeOrganizacional'),
        ];

        $this->execiciosPlanejamento = $this->planejamento->execiciosPlanejamento();
        $service = new ProgramaEstrategicoService();
        $filtros = ['planejamento' => $idPlanejamento, 'apenasProgramaTematico' => true];

        if ($this->request->has('orcprograma_id')) {
            $filtros['orcprograma_id'] = $this->request->get('orcprograma_id');
        }
        if ($this->request->has('orcorgao_id')) {
            $filtros['orcorgao_id'] = $this->request->get('orcorgao_id');
        }

        $this->programasTematicos = $service->buscar($filtros);
        if ($this->programasTematicos->count() === 0) {
            throw new Exception("Nenhum programa encontrado para o filtro encontrado.", 403);
        }
        $this->organizaDados();
    }

    /**
     * @return array
     */
    public function emitirPdf()
    {
        $relatorio = new ProgramasTematicosPdf();
        $relatorio->setFiltros($this->filtros);
        $relatorio->setDados($this->dados);
        if ($this->planejamento->pl2_tipo !== 'PPA') {
            $relatorio->setPeriodo($this->planejamento['pl2_ano_inicial']);
        }
        return $relatorio->emitir();
    }

    private function organizaDados()
    {
        $this->organizaPlanejamento();
        $this->organizaProgramas();
        $this->organizaComissao();
    }

    private function organizaPlanejamento()
    {
        $this->dados['planejamento'] = $this->planejamento->toArray();
        $this->dados['planejamento']['exercicios'] = $this->planejamento->execiciosPlanejamento();
        $this->dados['planejamento']['missao'] = $this->planejamento->pl2_missao;
        $this->dados['planejamento']['visao'] = $this->planejamento->pl2_visao;
        $this->dados['planejamento']['valores'] = $this->planejamento->pl2_valores;
    }

    private function organizaComissao()
    {
        $cgms = $this->planejamento->comissoes->map(function (Comissao $comissao) {
            return $comissao->cgm->z01_nome;
        })->toArray();
        $this->dados['planejamento']['comissao'] = $cgms;
    }

    /**
     * Organiza os programas
     */
    private function organizaProgramas()
    {
        $this->programasTematicos->each(function (ProgramaEstrategico $programaEstrategico) {
            $programaOrcamento = $programaEstrategico->getProgramaOrcamento();
            $programa = $programaOrcamento->toArray();
            $programa['formatado'] = $programaOrcamento->formataCodigo();
            $programa['valores'] = $programaEstrategico->getValores()->toArray();

            if ($this->planejamento->pl2_composicao === 3 && $programaEstrategico->objetivosEstrategicos->count()) {
                $objetivoEstrategico = $programaEstrategico->objetivosEstrategicos->first();
                $objetivoEstrategico->areaResultado;
                $programa['objetivoEstrategico'] = $objetivoEstrategico->toArray();
            }

            if ($this->planejamento->pl2_composicao === 2 && $programaEstrategico->areasResultado->count()) {
                $area = $programaEstrategico->areasResultado->first();
                $programa['areasResultado'] = $area->toArray();
            }

            $programa['indicadores'] = $this->organizaIndicadores($programaEstrategico->indicadores);

            $programa['orgaos'] = $this->organizaOrgaos($programaEstrategico->orgaos);
            $programa['objetivos'] = $this->organizaObjetivos($programaEstrategico->objetivos);

            $this->dados['programas'][] = $programa;
        });
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
}
