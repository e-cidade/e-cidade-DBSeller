<?php

namespace App\Domain\Tributario\Caixa\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Cadastro\Models\Iptubase as ModelsIptubase;

use Exception;

class Iptubase extends Controller
{
    public function listMatricula(Request $request)
    {
        try {
            $data = ModelsIptubase::with('cgm');

            if ($request->input('search')) {
                $data->where('j01_matric', $request->input('search'));
            }

            $data = $data->paginate(10);

            return new DBJsonResponse($data);
        } catch (Exception $e) {
            return new DBJsonResponse([], $e->getMessage(), 400);
        }
    }
}
