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

use App\Domain\Financeiro\Orcamento\Models\Subtitulo;
use App\Domain\Financeiro\Orcamento\Models\ProjetoAtividadeUnidadeMedida;
use App\Domain\Financeiro\Planejamento\Models\Abrangencia;
use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\MetasIniciativa;
use App\Domain\Financeiro\Planejamento\Models\Origem;
use App\Domain\Financeiro\Planejamento\Models\Periodo;
use App\Domain\Financeiro\Planejamento\Models\ProgramaEstrategico;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use phpDocumentor\Reflection\Types\Integer;
use stdClass;

/**
 * Class IniciativaService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class IniciativaService
{
    /**
     * Soma o valor de todas as iniciativas informadas totalizando por ano
     * @param Iniciativa[] $iniciativas
     * @param Integer|null $idIniciativaIgnorar
     * @return array
     */
    public static function totalizarValoresPorAno($iniciativas, $idIniciativaIgnorar = null)
    {
        $valoresAno = [];
        $iniciativas->each(function (Iniciativa $iniciativa) use (&$valoresAno, $idIniciativaIgnorar) {
            if (!empty($idIniciativaIgnorar) && $iniciativa->pl12_codigo == $idIniciativaIgnorar) {
                return;
            }

            $iniciativa->metas->each(function (MetasIniciativa $meta) use (&$valoresAno) {
                if (!array_key_exists($meta->exercicio, $valoresAno)) {
                    $valoresAno[$meta->exercicio] = 0;
                }
                $valoresAno[$meta->exercicio] += $meta->meta_financeira;
            });
        });
        return $valoresAno;
    }

    /**
     * @param $id
     * @return Iniciativa
     */
    public function find($id)
    {
        $iniciativa = Iniciativa::with('periodo')
            ->with(['programaEstrategico' => function ($programaEstrategico) {
                $programaEstrategico->with('objetivos');
            }])
            ->with('origem')
            ->with('metas')
            ->with('regionalizacoes')
            ->with('abrangencias')
            ->with('objetivos')
            ->where('pl12_codigo', '=', $id)
            ->first();

                
        $unidadeMedidaSigfis =  ProjetoAtividadeUnidadeMedida::where(
            'o221_orcprojativ',
            $iniciativa->pl12_orcprojativ
        )->get();
        
        $iniciativa->programaEstrategico->planejamento;
        $iniciativa->vinculoSigfisUnidadeMedida = $unidadeMedidaSigfis;
            
        return $iniciativa;
    }

    /**
     * @param stdClass $dados
     * @return Iniciativa|mixed
     * @throws Exception
     */
    public function salvarFromStdClass(stdClass $dados)
    {
        $iniciativa = new Iniciativa();
        if (!empty($dados->pl12_codigo)) {
            $iniciativa = Iniciativa::find($dados->pl12_codigo);
        }

        $jaCadastrado = $iniciativa->validaAcaoJaCadastrada(
            $dados->pl12_programaestrategico,
            $dados->pl12_orcprojativ,
            $dados->pl12_codigo
        )->first();
        if (!is_null($jaCadastrado)) {
            throw new Exception("Essa ação já esta cadastrada para o programa estratégico selecionado.", 403);
        }

        $iniciativa->programaEstrategico()->associate(ProgramaEstrategico::find($dados->pl12_programaestrategico));

        $iniciativa->origem()->dissociate();
        if (!empty($dados->pl12_origeminiciativa)) {
            $iniciativa->origem()->associate(Origem::find($dados->pl12_origeminiciativa));
        }

        $iniciativa->periodo()->dissociate();
        if (!empty($dados->pl12_periodoacao)) {
            $iniciativa->periodo()->associate(Periodo::find($dados->pl12_periodoacao));
        }

        $iniciativa->pl12_orcprojativ = $dados->pl12_orcprojativ;
        $iniciativa->pl12_anoorcamento = $dados->pl12_anoorcamento;

        $iniciativa->save();

        $sync = $dados->pl12_objetivo ? [$dados->pl12_objetivo] : [];
        $iniciativa->objetivos()->sync($sync);

        return $iniciativa;
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function delete($id)
    {
        Iniciativa::find($id)->delete();
    }

    /**
     * @param stdClass $dados
     * @return Collection|Subtitulo[]
     */
    public function saveRegionalizacaoToObject(stdClass $dados)
    {
        $iniciativa = Iniciativa::find($dados->pl12_codigo);
        $iniciativa->regionalizacoes()->sync($dados->regionalizacoes);

        return $iniciativa->regionalizacoes;
    }

    /**
     * @param stdClass $dados
     * @return Collection|Abrangencia[]
     */
    public function saveAbrangenciaToObject(stdClass $dados)
    {
        $iniciativa = Iniciativa::find($dados->pl12_codigo);
        $iniciativa->abrangencias()->sync($dados->abrangencias);

        return $iniciativa->abrangencias;
    }

    /**
     * Desvincula todas as regionalizações da iniciativa
     * @param integer $id
     */
    public function excluirRegionalizacoes($id)
    {
        $iniciativa = Iniciativa::find($id);
        $iniciativa->regionalizacoes()->sync([]);
    }

    /**
     * Desvincula todas as abrangências da iniciativa
     * @param integer $id
     */
    public function excluirAbrangencias($id)
    {
        $iniciativa = Iniciativa::find($id);
        $iniciativa->abrangencias()->sync([]);
    }
}
