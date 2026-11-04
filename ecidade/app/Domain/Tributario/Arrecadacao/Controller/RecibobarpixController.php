<?php

namespace App\Domain\Tributario\Arrecadacao\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Tributario\Arrecadacao\Services\ArrecadacaoPixService;
use App\Domain\Tributario\Arrecadacao\Requests\ValidacaoPixRequest;
use ECidade\Tributario\Caixa\Repository\RecibopagaRepository;
use regraEmissao;

class RecibobarpixController extends Controller
{

    public function gerarPix(Request $request)
    {

        $servicePixArrecadao = new ArrecadacaoPixService();
        $servicePixArrecadao->setCodigoArrecadacao($request->codigo_arrecadacao);
        $servicePixArrecadao->setConvenio($request->convenio);
        $servicePixArrecadao->setModelo($request->modelo);
        $servicePixArrecadao->setParcelaInicio($request->parcelainicio);
        $servicePixArrecadao->setParcelaFim($request->parcelafim);
        $servicePixArrecadao->setTipoDebito($request->tipo_debito);
        $servicePixArrecadao->setVencimento($request->vencimento);
        $servicePixArrecadao->gerarPix();

        return new DBJsonResponse([], 'Sucesso');
    }

    public function gerarPixDBPref(ValidacaoPixRequest $request)
    {
        try {
            $servicePixArrecadao = new ArrecadacaoPixService();
            $servicePixArrecadao->setCodigoArrecadacao($request->codigo_arrecadacao);
            $servicePixArrecadao->setConvenio($request->convenio);
            $servicePixArrecadao->setModelo($request->modelo);
            $servicePixArrecadao->setParcelaInicio($request->parcela_inicio);
            $servicePixArrecadao->setParcelaFim($request->parcela_fim);
            $servicePixArrecadao->setTipoDebito($request->tipo_debito);
            $servicePixArrecadao->setVencimento($request->vencimento);

            if (!$servicePixArrecadao->gerarPix()) {
                return new DBJsonResponse(
                    [],
                    'Não foi possível gerar o pix, regra não configurada.',
                    202
                );
            }

            return new DBJsonResponse(
                $servicePixArrecadao->build(),
                'Sucesso'
            );
        } catch (\Exception $erro) {
            return new DBJsonResponse([], $erro->getMessage(), 202);
        }
    }
}
