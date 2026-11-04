<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Resources\Lrf\EmissaoResource;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF\EmissaoLrfService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class LrfEmissaoController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     */
    public function get(Request $request)
    {
        $dados = (new EmissaoLrfService)->getByFilters($request->all());
        return new DBJsonResponse(EmissaoResource::toArray($dados), 'Emissões');
    }

    /**
     * @param integer $codigo esse código é o id do storage
     * @return false|int
     * @throws Exception
     */
    public function download($codigo)
    {
        $servico = new EmissaoLrfService();
        $data = $servico->download($codigo);

        $name = basename($data);

        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($name) . "\"");

        return readfile($data);
    }

    /**
     * @param $codigo
     * @return DBJsonResponse
     */
    public function delete($codigo)
    {
        $servico = new EmissaoLrfService();
        $servico->deletar($codigo);
        return new DBJsonResponse([], 'Emissão deletada.');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function publicar(Request $request)
    {
        $servico = new EmissaoLrfService();
        $servico->publicar($request->get('codigo'), $request->get('publicado'));

        return new DBJsonResponse([], 'Emissão identificada como publicada.');
    }
}
