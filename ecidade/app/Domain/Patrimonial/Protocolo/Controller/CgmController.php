<?php

namespace App\Domain\Patrimonial\Protocolo\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Patrimonial\Protocolo\Requests\CgmCpfCnpjRequest;
use App\Domain\Patrimonial\Protocolo\Requests\CgmParamsRequest;
use App\Domain\Patrimonial\Protocolo\Requests\CgmRequest;
use App\Domain\Patrimonial\Protocolo\Services\CgmService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CgmController extends Controller
{
    private $cgmService;

    public function __construct(CgmService $cgmService)
    {
        $this->cgmService = $cgmService;
    }

    public function getByNumcgm(CgmRequest $request)
    {
        $aCgm = $this->cgmService->getByNumcgm($request->numcgm);

        return new DBJsonResponse($aCgm);
    }

    /**
     * @throws \Exception
     */
    public function getCgmByCpfCnpj(CgmCpfCnpjRequest $request)
    {
        $aCgm = collect($this->cgmService->getCgmByCpfCnpj($request->cpfCnpj))->first();

        return new DBJsonResponse($aCgm);
    }

    /**
     * Metodo para buscar registros a partir de um ou mais parametros:
     * cgm, nome, cgcpf ou email
     */
    public function getByParams(CgmParamsRequest $request)
    {
        $resultados = $this->cgmService->getByParams($request);

        return new DBJsonResponse($resultados);
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     */
    public function getRotulosPesquisaCgm()
    {
        $resultados = $this->cgmService->getRotulosPesquisaCgm();

        return new DBJsonResponse($resultados);
    }

    public function search(Request $request)
    {
        $perpage = 10;
        $cgm = new Cgm();
        if (!empty($request->get("perpage")) and is_numeric($request->get("perpage"))) {
            $perpage = $request->get("perpage");
        }

        if (!empty($request->get("nome"))) {
            $cgm =   $cgm->likeNome($request->get("nome"));
        }

        if (!empty($request->get("cgm"))) {
            $cgm =  $cgm->inNumeroCgm(explode(",", $request->get("cgm")));
        }

        if (!empty($request->get("cpf_cnpj"))) {
            $cgm =  $cgm->likeCpfCnpj($request->get("cpf_cnpj"));
        }

        return new DBJsonResponse($cgm->orderBy('z01_nome')->paginate($perpage));
    }

    public function getLocalidadeCep(Request $request)
    {
        if (!empty($request->get("cep"))) {
            $cep = str_replace('-', '', $request->get("cep"));
            return new DBJsonResponse($this->cgmService->getEnderecoLocalidadeCep($cep));
        }

        return new DBJsonResponse(['mensagem' => 'CEP não encontrado', 'erro' => true]);
    }

    public function saveCgm(Request $request)
    {
        return $this->cgmService->saveCgm($request->all());
    }

    public function verificaPermissaoCgm(Request $request)
    {
        return $this->cgmService->verificaPermissaoCgm($request->all());
    }

    public function verificaCgm(Request $request)
    {
        if (!empty($request->get("cpfCnpj"))) {
            return $this->cgmService->verificaCgm($request->get("cpfCnpj"));
        }
        return new DBJsonResponse(['mensagem' => 'Parâmetro cpfCnpj vazio', 'erro' => true]);
    }
}
