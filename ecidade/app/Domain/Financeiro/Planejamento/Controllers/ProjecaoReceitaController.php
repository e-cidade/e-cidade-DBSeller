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

namespace App\Domain\Financeiro\Planejamento\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Projecao\AtualizaPrevisaoExercicioRequest;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Projecao\CalculaProjecaoRequest;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Projecao\ValorBaseReceitaRequest;
use App\Domain\Financeiro\Planejamento\Services\EstimativaReceitaService;
use App\Domain\Financeiro\Planejamento\Services\ProjecaoReceitaService;
use App\Http\Controllers\Controller;
use Exception;

/**
 * Class ProjecaoReceitaController
 * @package App\Domain\Financeiro\Planejamento\Controllers
 */
class ProjecaoReceitaController extends Controller
{
    /**
     * @var ProjecaoReceitaService
     */
    private $service;

    public function __construct(ProjecaoReceitaService $service)
    {
        $this->service = $service;
    }

    /**
     * @param CalculaProjecaoRequest $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function recalcular(CalculaProjecaoRequest $request)
    {
        $receitas = array_values($this->service->porRequest($request)->recalcular());
        return new DBJsonResponse($receitas, "Dados da projeção");
    }

    /**
     * @param CalculaProjecaoRequest $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function buscar(CalculaProjecaoRequest $request)
    {
        $receitas = array_values($this->service->porRequest($request)->get());
        return new DBJsonResponse($receitas, "Dados da projeção");
    }

    /**
     * @param AtualizaPrevisaoExercicioRequest $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function previsaoExercicio(AtualizaPrevisaoExercicioRequest $request)
    {
        $service = new EstimativaReceitaService();
        $exercicio = (int)$request->get('exercicio');
        $service->atualizarPrevisao($request->get('id'), $exercicio, $request->get('valor'));
        return new DBJsonResponse([], "Valor atualizado");
    }

    /**
     * Atualiza o valor base
     * @param ValorBaseReceitaRequest $request
     * @return DBJsonResponse
     */
    public function valorBase(ValorBaseReceitaRequest $request)
    {
        $service = new EstimativaReceitaService();
        $service->atualizarValorBase($request->get('id'), $request->get('valor'));
        return new DBJsonResponse([], "Valor atualizado");
    }
}
