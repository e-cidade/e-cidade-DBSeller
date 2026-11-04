<?php

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Mappers\LancamentoManualAtributosMinimos;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoReduzido;
use App\Domain\Financeiro\Contabilidade\Resources\ConsultaReduzidosResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReduzidoPcaspController extends Controller
{
    public function index(Request $request)
    {
        $campos = [
            'c60_codcon',
            'c60_anousu',
            'c60_estrut',
            'c60_descr',
            'c60_codigo',
            'c61_codigo',
            'c61_reduz',
            'c61_instit',
        ];

        if ($request->has('uniao')) {
            $campos[] = 'conta';
            $campos[] = 'uniao';
            $campos[] = 'nome';
            $campos[] = 'funcao';
            $campos[] = 'natureza';
            $campos[] = 'sintetica';
            $campos[] = 'indicador';
            $campos[] = 'informacoescomplementares';
        }

        $paginate = ConplanoReduzido::query()
            ->select($campos)
            ->join('contabilidade.conplano', function ($join) {
                $join->on('c61_codcon', '=', 'c60_codcon')
                    ->on('c61_anousu', '=', 'c60_anousu');
            })
            ->when($request->has('uniao'), function ($query) use ($request) {
                $query->join('contabilidade.pcaspconplano', 'conplano_codigo', '=', 'c60_codigo')
                    ->join('contabilidade.pcasp', function ($join) use ($request) {
                        $join->on('pcasp.id', '=', 'pcasp_id')
                            ->on('pcasp.uniap', '=', $request->get('uniao'));
                    });
            })
            ->when($request->has('instituicao'), function ($query) use ($request) {
                $query->where('c61_instit', $request->get('instituicao'));
            })
            ->when($request->has('exercicio'), function ($query) use ($request) {
                $query->where('c61_anousu', $request->get('exercicio'));
            })
            ->when($request->has('reduzido'), function ($query) use ($request) {
                $query->where('c61_reduz', $request->get('reduzido'));
            })
            ->when($request->has('estrutural'), function ($query) use ($request) {
                $estrutural = $request->get('estrutural');
                $query->where('c60_estrut', 'like', "$estrutural%");
            })
            ->when($request->has('excluirContasBancarias'), function ($query) use ($request) {
                $query->where('c60_estrut', 'not like', "111%");
            })
            ->when($request->has('sortField'), function ($query) use ($request) {
                $mapa = [
                    'estrutural' => 'c60_estrut',
                    'reduzido' => 'c61_reduz',
                    'descricao' => 'c60_descr',
                ];

                $query->orderby($mapa[$request->get('sortField')], $request->get('sortOrder'));
            })
            ->when(!$request->has('sortField'), function ($query) {
                $query->orderby('c60_estrut');
            })
            ->paginate($request->get("rows"));


        return new DBJsonResponse(ConsultaReduzidosResource::toArray($paginate), 'Reduzidos encontrados');
    }
}
