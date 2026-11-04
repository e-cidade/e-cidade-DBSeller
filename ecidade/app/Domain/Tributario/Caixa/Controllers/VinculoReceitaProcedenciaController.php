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

namespace App\Domain\Tributario\Caixa\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Caixa\Services\VinculoReceitaProcedenciaService;
use App\Http\Controllers\Controller;
use BusinessException;
use Exception;
use Illuminate\Http\Request;
use Log;

class VinculoReceitaProcedenciaController extends Controller
{
    private $service;

    public function __construct(VinculoReceitaProcedenciaService $service)
    {
        $this->service = $service;
    }

    public function getDados(Request $request)
    {
        $this->validate($request, [
            'receita' => 'required|numeric'
        ]);

        try {
            $data = $this->service->getDados($request->receita);
            return new DBJsonResponse($data);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 400);
        } catch (Exception $e) {
            $erroMsg = 'Vinculo receita procedencia: ' . $e->getMessage() . ' trace: ' . $e->getTraceAsString();
            Log::error($erroMsg);
            return new DBJsonResponse(null, 'Erro Interno. Contate o Suporte', 500);
        }
    }

    public function salvar(Request $request)
    {
        $this->validate($request, [
            'receita' => 'required|numeric',
            'procedencia' => 'required|numeric',
            'tipoinscricao' => 'required|numeric|in:1,2'
        ]);

        try {
            $this->service->setReceita($request->receita);
            $this->service->setProcedencia($request->procedencia);
            $this->service->setTipoInscricao($request->tipoinscricao);
            $this->service->salvar();
            return new DBJsonResponse(null, "Procedencia {$request->procedencia} vinculada com sucesso", 200);
        } catch (BusinessException $e) {
            return new DBJsonResponse(null, $e->getMessage(), 400);
        } catch (Exception $e) {
            $erroMsg = 'Vinculo receita procedencia: ' . $e->getMessage() . ' trace: ' . $e->getTraceAsString();
            Log::error($erroMsg);
            return new DBJsonResponse(null, 'Erro Interno. Contate o Suporte', 500);
        }
    }
}
