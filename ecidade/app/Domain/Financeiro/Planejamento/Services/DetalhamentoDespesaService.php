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

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Orcamento\Models\CaracteristicaPeculiar;
use App\Domain\Financeiro\Orcamento\Models\Funcao;
use App\Domain\Financeiro\Orcamento\Models\PpaSubtituloLocalizadorGasto;
use App\Domain\Financeiro\Orcamento\Models\Recurso;
use App\Domain\Financeiro\Orcamento\Models\Subfuncao;
use App\Domain\Financeiro\Planejamento\Models\DetalhamentoDespesa;
use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use Exception;
use stdClass;

/**
 * Class DetalhamentoDespesaService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class DetalhamentoDespesaService
{
    /**
     * @var ValoresService
     */
    private $serviceValores;

    public function __construct(ValoresService $service)
    {
        $this->serviceValores = $service;
    }

    /**
     * @param $id
     * @return DetalhamentoDespesa|mixed
     */
    public function find($id)
    {
        return DetalhamentoDespesa::with('funcao')
            ->with('subfuncao')
            ->with('recurso')
            ->with('caracteristicaPeculiar')
            ->with('subtitulo')
            ->with('iniciativa')
            ->with('instituicao')
            ->with('cronogramaDesembolso')
            ->find($id);
    }

    /**
     * @param stdClass $dados
     * @return DetalhamentoDespesa|mixed
     * @throws Exception
     */
    public function salvarFromObject(stdClass $dados)
    {
        $detalhamento = new DetalhamentoDespesa();
        if (!empty($dados->pl20_codigo)) {
            $detalhamento = DetalhamentoDespesa::find($dados->pl20_codigo);
        }

        if (!is_null($detalhamento->existsDetalhamento($dados)->first())) {
            throw new Exception("Já existe um detalhamento com o conjunto de informações.", 403);
        }

        $detalhamento->pl20_anoorcamento = $dados->pl20_anoorcamento;
        $detalhamento->pl20_orcorgao = $dados->pl20_orcorgao;
        $detalhamento->pl20_orcunidade = $dados->pl20_orcunidade;
        $detalhamento->pl20_orcelemento = $dados->pl20_orcelemento;
        $detalhamento->pl20_esferaorcamentaria = $dados->pl20_esferaorcamentaria;
        $detalhamento->pl20_valorbase = $dados->pl20_valorbase;
        $detalhamento->funcao()->associate(Funcao::find($dados->pl20_orcfuncao));
        $detalhamento->subfuncao()->associate(Subfuncao::find($dados->pl20_orcsubfuncao));
        $detalhamento->recurso()->associate(Recurso::find($dados->pl20_recurso));
        $detalhamento->caracteristicaPeculiar()->associate(CaracteristicaPeculiar::find($dados->pl20_concarpeculiar));
        $detalhamento->subtitulo()->associate(PpaSubtituloLocalizadorGasto::find($dados->pl20_subtitulo));
        $detalhamento->iniciativa()->associate(Iniciativa::find($dados->pl20_iniciativaprojativ));
        $detalhamento->instituicao()->associate(DBConfig::find($dados->pl20_instituicao));

        $detalhamento->save();

        $valores = str_replace('\\', '', $dados->valores);

        $detalhamento->setValores(
            $this->serviceValores->salvarColecao(
                json_decode($valores),
                Valor::ORIGEM_DETALHAMENTO_DESPESA,
                $detalhamento->pl20_codigo
            )
        );

        $this->criaCronogramaDesembolso($detalhamento);

        $detalhamento->cronogramaDesembolso;
        return $detalhamento;
    }

    /**
     * @param array $filtros
     * @return mixed
     */
    public function buscar(array $filtros)
    {
        return DetalhamentoDespesa::when(!empty($filtros['pl20_iniciativaprojativ']), function ($query) use ($filtros) {
            $query->where('pl20_iniciativaprojativ', '=', $filtros['pl20_iniciativaprojativ']);
        })
            ->orderBy('pl20_orcelemento')
            ->orderBy('pl20_codigo')
            ->with('funcao')
            ->with('subfuncao')
            ->with('recurso')
            ->with('caracteristicaPeculiar')
            ->with('subtitulo')
            ->with('iniciativa')
            ->with('instituicao')
            ->get();
    }

    /**
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    public function remover($id)
    {
        return DetalhamentoDespesa::find($id)->delete();
    }

    /**
     * @param $detalhamento
     * @return void
     */
    private function criaCronogramaDesembolso($detalhamento)
    {
        $service = new CronogramaDesembolsoDespesaService();
        $service->criarCronogramaDesembolso($detalhamento);
    }
}
