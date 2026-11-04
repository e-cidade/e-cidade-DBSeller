<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Enum\TipoFolhaEnum;
use App\Domain\RecursosHumanos\Pessoal\Model\Instituicao\Instituicao;
use App\Domain\RecursosHumanos\Pessoal\Model\RegimeRecursosHumanos;
use App\Domain\RecursosHumanos\Pessoal\Relatorios\TabelaPrevidenciaCsv;
use App\Domain\RecursosHumanos\Pessoal\Relatorios\TabelaPrevidenciaPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PrevidenciaEncargosTributariosController extends Controller
{

    public function emitir(Request $request)
    {
        
        $filtro = [];
        $filtro['tipo'] = TipoFolhaEnum::getTipoFolha($request->tipo);
        $filtro['instituicao'] = Instituicao::where('codigo', db_getsession('DB_instit'))->get();
        $filtro['mes'] = $request->mes;
        $filtro['ano'] = $request->ano;
        $filtro['relatorio'] = (new RegimeRecursosHumanos())
            ->relatorioPrevidencia(
                $request->mes,
                $request->ano,
                $request->lotacoes,
                $request->tipo,
                $request->previdencia,
                $request->regime
            );
        if (!$filtro['relatorio']) {
            return new DBJsonResponse(0, 'Nao foram encontrado dados da Previdencia.');
        }
       
        $arquivo = (new TabelaPrevidenciaPdf($filtro))->emitir();
       
        return new DBJsonResponse($arquivo, 'Emissao de tipo de Guia de Previdencia.');
    }
}
