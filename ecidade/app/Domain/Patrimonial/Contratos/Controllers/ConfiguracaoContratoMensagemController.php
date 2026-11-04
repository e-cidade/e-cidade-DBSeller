<?php

namespace App\Domain\Patrimonial\Contratos\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Contratos\Models\MensageriaAcordo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Patrimonial\Contratos\Models\MensageriaAcordoDia;

class ConfiguracaoContratoMensagemController extends Controller
{
    public function show()
    {
        $mensageria_acordo = MensageriaAcordo::firstOrCreate([
            'instituicao_id'=> (integer) db_getsession("DB_instit"),
        ], [
            "ac51_assunto" => "Contrato [numero]/[ano] irá vencer",
            "ac51_mensagem" => "O contrato [numero]/[ano] irá vencer em [dias] dias.
            Vigência: [data_inicial] - [data_final]",
        ]);

        return new DBJsonResponse($mensageria_acordo->load('dias'));
    }

    public function update($mensageria_acordo_id, Request $request)
    {
        $this->validate($request, [
            "ac51_assunto",
            "ac51_mensagem"
        ]);

        $mensageria_acordo = MensageriaAcordo::findOrFail($mensageria_acordo_id);
        $mensageria_acordo->setAssunto($request->get("ac51_assunto"));
        $mensageria_acordo->setMensagem($request->get("ac51_mensagem"));
        $mensageria_acordo->update();

        return new DBJsonResponse($mensageria_acordo);
    }

    public function addDia($mensageria_acordo_id, Request $request)
    {
        $this->validate($request, [
             "dia"=>'required'
        ]);

        $data["data"]  = MensageriaAcordoDia::firstOrCreate([
            'dia'=> $request->get("dia"),
            'mensageriaacordo_id' => $mensageria_acordo_id
        ]);

        return new DBJsonResponse($data);
    }

    public function removeDia($mensageria_acordo_dia_id)
    {

        $diaAcordo = MensageriaAcordoDia::findOrFail($mensageria_acordo_dia_id);

        if ($diaAcordo->delete()) {
            return new DBJsonResponse([], "Removido com sucesso!");
        }
        return new DBJsonResponse([], "Erro ao remover", 400);
    }
}
