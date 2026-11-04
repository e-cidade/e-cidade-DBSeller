<?php

namespace App\Domain\Tributario\Cadastro\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Cadastro\Requests\ImoveisRequest;
use App\Domain\Tributario\Cadastro\Requests\ImovelRequest;
use App\Domain\Tributario\Cadastro\Requests\LogradouroRequest;
use App\Domain\Tributario\Cadastro\Services\CadastroService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CadastroController extends Controller
{
    private $cadastroService;

    public function __construct(CadastroService $cadastroService)
    {
        $this->cadastroService = $cadastroService;
    }

    public function dadosImovel(ImovelRequest $request)
    {
        $oIptubase = $this->cadastroService->getDadosRegImovByMatric($request->matricula);

        return new DBJsonResponse($oIptubase);
    }

    public function getSetorRegImoveis()
    {
        $aSetor = $this->cadastroService->getSetorRegImoveis();

        return new DBJsonResponse($aSetor);
    }

    public function getLocalidadeRural()
    {
        $aLocalidade = $this->cadastroService->getLocalidadeRural();

        return new DBJsonResponse($aLocalidade);
    }

    public function getBairros()
    {
        $aBairros = $this->cadastroService->getBairros();

        return new DBJsonResponse($aBairros);
    }

    public function getLogradouros(LogradouroRequest $request)
    {
        $iCep = $request->query("cep", null);
        $iBairro = $request->query("bairro", null);

        $aLogradouros = $this->cadastroService->getLogradouros($iCep, $iBairro);

        return new DBJsonResponse($aLogradouros);
    }

    /**
     * Metodo para buscar uma lista de imoveis de acordo com
     * os parametros fornecidos
     */
    public function getListaImoveis(ImoveisRequest $request)
    {
        $resultados = $this->cadastroService->getListaImoveis($request);

        return new DBJsonResponse($resultados);
    }

    /**
     * Metodo para buscar as labels para a tabela do frontend
     */
    public function getLabelsListaImoveis()
    {
        $resultados = $this->cadastroService->getLabelsListaImoveis();

        return new DBJsonResponse($resultados);
    }

    public function getEnderecoBaseCliente(Request $request)
    {
        $this->validate($request, [
            "cep" => "required"
        ]);
        $enderecos = $this->cadastroService->getEnderecoBaseCliente(
            $request->get("cep"),
            $request->get("logradouro")
        );
        return new DBJsonResponse($enderecos);
    }

    public function getBairroBaseCliente(Request $request)
    {
        $this->validate($request, [
            "id_rua" => "required"
        ]);
        $enderecos = $this->cadastroService->getBairroBaseCliente($request->get("id_rua"));
        return new DBJsonResponse($enderecos);
    }

    public function getEscritoriosContabeis()
    {
        return new DBJsonResponse($this->cadastroService->getEscritoriosContabeis());
    }

    public function getAtividadesPorTipo(Request $request)
    {
        $this->validate($request, [
            "tipo" => "required"
        ]);

        $atividades = null;

        try {
            $atividades = $this->cadastroService->getAtividadesPorTipo($request->get('tipo'));
            return new DBJsonResponse($atividades);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage(), $ex->getTrace());
            return new DBJsonResponse([], "Erro ao buscar atividades!", 404);
        }
    }

    public function getIptuMatricula(Request $request)
    {
        $this->validate($request, [
            "matricula" => "required"
        ]);

        $matricula = $request->get('matricula');

        try {
            $atividades = $this->cadastroService->getIptuMatricula($matricula);
            return new DBJsonResponse($atividades);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage(), $ex->getTrace());
            return new DBJsonResponse([], "Erro ao buscar IPTU matricula!", 404);
        }
    }

    public function getZonas(Request $request)
    {
        $zonas = null;

        try {
            $zonas = $this->cadastroService->getZonas();
            if (!empty($request->get("termo"))) {
                $zonas = $this->cadastroService->getZonas($request->get("termo"));
            } else {
                $zonas = $this->cadastroService->getZonas();
            }
            return new DBJsonResponse($zonas);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage(), $ex->getTrace());
            return new DBJsonResponse([], "Erro ao buscar zonas!", 404);
        }
    }


    public function getValidIdPrinc(Request $request)
    {
        $this->validate($request, [
            "j39_matric" => "required",
            "j39_idcons" => "required"
        ]);

        $matricula = $request->get('j39_matric');
        $idCons = $request->get('j39_idcons');
        try {
            $principal = $this->cadastroService->getByMatricIdCons($matricula, $idCons);
            if ($principal["j39_idprinc"]) {
                $listar = $this->cadastroService->getAllDtDemoNull(
                    $matricula,
                    ['j39_idprinc', 'j39_idcons', 'j39_matric', 'j39_dtdemo']
                );
                return new DBJsonResponse($listar);
            } else {
                return new DBJsonResponse([]);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage(), $ex->getTrace());
            return new DBJsonResponse([], "Erro ao buscar Construçao principal!", 404);
        }
    }

    public function getMatriculaCondominios(Request $request)
    {
        $this->validate($request, [
            "nome" => "string",
            "sequencial" => "integer"
        ]);

        $dados = (object) $request->all();

        try {
            $condominiosFiltrados = $this->cadastroService->getMatriculaCondominios($dados);
            return new DBJsonResponse($condominiosFiltrados);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro Interno ao buscar condominios.', 500);
        }
    }

    public function getMatriculaLogradouros(Request $request)
    {
        $this->validate($request, [
            "nome" => "string",
            "sequencial" => "integer",
            "cep" => "integer"
        ]);

        $dados = (object) $request->all();

        try {
            $logradourosFiltrados = $this->cadastroService->getMatriculaLogradouros($dados);
            return new DBJsonResponse($logradourosFiltrados);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro Interno ao buscar logradouros.', 500);
        }
    }

    public function getMatriculaLoteamento(Request $request)
    {
        $this->validate($request, [
            "descricao" => "string",
            "sequencial" => "integer"
        ]);

        $dados = (object) $request->all();

        try {
            $loteamentosFiltrados = $this->cadastroService->getMatriculaLoteamento($dados);
            return new DBJsonResponse($loteamentosFiltrados);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return new DBJsonResponse(null, 'Erro Interno ao buscar loteamentos.', 500);
        }
    }
}
