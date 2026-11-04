<?php

namespace App\Domain\Integracoes\EFDReinf\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Integracoes\EFDReinf\Repository\UnidadeResponsavelRepository;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class UnidadeResponsavelController extends Controller
{
    private $unidadeResponsavel;

    public function __construct(UnidadeResponsavelRepository $unidadeRespRepository)
    {
        $this->unidadeResponsavel = $unidadeRespRepository;
    }

    public function get(Request $request)
    {
        $instit = $request->DB_instit;
        $unidades = $this->unidadeResponsavel->getAll($instit);

        return new DBJsonResponse($unidades);
    }

    public function save(Request $request)
    {
        $this->validate($request, ['cgm' => 'required', 'DB_instit' => 'required']);

        try {
            $dados = [];
            $dados['efd08_cgm']    = $request->cgm;
            $dados['efd08_instit'] = $request->DB_instit;

            $this->unidadeResponsavel->save($dados);
            return new DBJsonResponse('', 'Cadastrado com sucesso.');
        } catch (Exception $e) {
            return new DBJsonResponse('', $e->getMessage(), 500);
        }
    }

    public function delete(Request $request)
    {
        $this->validate($request, ['id' => 'required|numeric']);

        try {
            $this->unidadeResponsavel->delete($request->id);
            return new DBJsonResponse('', "Registro {$request->id} deletado.");
        } catch (Exception $e) {
            return new  DBJsonResponse('', $e->getMessage(), 500);
        }
    }
}
