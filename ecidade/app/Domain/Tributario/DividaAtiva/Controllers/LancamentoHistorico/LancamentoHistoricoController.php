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

namespace App\Domain\Tributario\DividaAtiva\Controllers\LancamentoHistorico;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\DividaAtiva\DTO\LancamentoHistorico\GetDividasFiltradasDTO;
use App\Domain\Tributario\DividaAtiva\DTO\LancamentoHistorico\SaveHistoricoDTO;
use App\Domain\Tributario\DividaAtiva\Services\LancamentoHistorico\DividasService;
use App\Http\Controllers\Controller;
use BusinessException;
use Exception;
use Log;
use Illuminate\Http\Request;

class LancamentoHistoricoController extends Controller
{
    public function getDividas(Request $request, DividasService $service)
    {
        $this->validate($request, [
            'numcgm' => 'string',
            'matricula' => 'string',
            'inscricao' => 'numeric',
            'codDivida' => 'numeric',
            'exercicio' => 'array',
            'page' => 'numeric'
        ]);

        try {
            $dividasAtivas = GetDividasFiltradasDTO::fromArray($request->all());
            $dividasAtivasFiltradas = $service->getDividasAtivas($dividasAtivas);
            return new DBJsonResponse($dividasAtivasFiltradas);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 400);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro Interno ao buscar as dividas.', 500);
        }
    }

    public function getAllDividasToSelect(Request $request, DividasService $service)
    {
        $this->validate($request, [
            'numcgm' => 'string',
            'matricula' => 'string',
            'inscricao' => 'numeric',
            'codDivida' => 'numeric',
            'exercicio' => 'array',
            'page' => 'numeric'
        ]);

        try {
            $dividasAtivas = GetDividasFiltradasDTO::fromArray($request->all());
            $dividasAtivasFiltradas = $service->getAllDividasToSelect($dividasAtivas);
            return new DBJsonResponse($dividasAtivasFiltradas);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 400);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro Interno ao selecionar dividas.', 500);
        }
    }

    public function getDividasExercicios(Request $request, DividasService $service)
    {
        $this->validate($request, [
            'numcgm' => 'string',
            'matricula' => 'string',
            'inscricao' => 'numeric',
            'codDivida' => 'numeric',
            'exercicio' => 'array',
            'page' => 'numeric'
        ]);

        try {
            $exerciciosDividas = GetDividasFiltradasDTO::fromArray($request->all());
            $exerciciosDividasFiltrados = $service->getDividasExercicios($exerciciosDividas);
            return new DBJsonResponse($exerciciosDividasFiltrados);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 400);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro Interno ao buscar os exercícios.', 500);
        }
    }

    public function saveHistorico(Request $request, DividasService $service)
    {
        $this->validate($request, [
            'dividas' => 'array',
            'historico' => 'string',
            'data' => 'string'
        ]);

        try {
            $dadosHistorico = SaveHistoricoDTO::fromArray($request->all());
            $service->saveHistorico($dadosHistorico);

            $mensagem = 'Histórico salvo';
            return new DBJsonResponse(null, $mensagem);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 400);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro Interno ao salvar histórico.', 500);
        }
    }
}
