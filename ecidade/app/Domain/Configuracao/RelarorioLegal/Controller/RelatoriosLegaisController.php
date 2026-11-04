<?php

namespace App\Domain\Configuracao\RelarorioLegal\Controller;

use App\Domain\Configuracao\RelarorioLegal\Model\Relatorio;
use App\Domain\Configuracao\RelarorioLegal\Services\RelatoriosLegaisService;
use App\Domain\Configuracao\RelarorioLegal\Services\TemplatesXlsRelatoriosLegaisService;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;

class RelatoriosLegaisController extends Controller
{
    public function relatoriosLRF()
    {
        $service = new RelatoriosLegaisService();

        $relatorios = $service->relatoriosLRF();
        return new DBJsonResponse($relatorios->toArray(), "Relatórios");
    }

    public function periodos($relatorio)
    {
        $relatorio = Relatorio::find($relatorio);
        $periodos = $relatorio->periodos->map(function ($c) {
            return [
                "codigo" => $c->o114_sequencial,
                "periodo" => $c->toArray()
            ];
        })->toArray();

        return new DBJsonResponse($periodos, "Períodos");
    }

    public function upload(Request $request)
    {
        $service = new TemplatesXlsRelatoriosLegaisService(new Filesystem());
        $service->salvarTemplate($request->all());
        return new DBJsonResponse([], "Template salvo com sucesso.");
    }
}
