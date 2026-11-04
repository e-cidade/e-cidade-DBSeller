<?php

namespace App\Domain\Integracoes\EFDReinf\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Integracoes\EFDReinf\Models\EFDReabFechDadosResp;
use App\Domain\Integracoes\EFDReinf\Repository\UnidadeResponsavelRepository;
use App\Domain\Integracoes\EFDReinf\Services\ConfiguracaoService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use InstituicaoRepository;
use stdClass;

class ReabFechDadosRespController extends Controller
{
    public function getContribuinte()
    {
        $efdConfig = ConfiguracaoService::getInstance(session('DB_instit'));
        $instituicaoSessao = InstituicaoRepository::getInstituicaoSessao();
        $response = new stdClass;

        if ($efdConfig->filtraOrgaoUnidade()) {
            $unidaderesp = new UnidadeResponsavelRepository;
            $response->contribuinte = $unidaderesp->getAll($instituicaoSessao->getCodigo());
        } else {
            $contribuinte = [$instituicaoSessao->toArray()];
            $contribuinte[0]['cgm'] = $instituicaoSessao->getCgm()->getCodigo();
            $response->contribuinte = $contribuinte;
        }

        return new DBJsonResponse($response);
    }

    public function get(Request $request)
    {
        $this->validate($request, [
            'cgm' => 'required|numeric'
        ]);

        $response = new stdClass;
        $response->resp = EFDReabFechDadosResp::where('efd10_numcgm', $request->cgm)->first();
        return new DBJsonResponse($response);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'cgm'            => 'required|numeric',
            'efd10_nome'     => 'required|string',
            'efd10_cpf'      => 'required|string',
            'efd10_email'    => 'required|string',
            'efd10_telefone' => 'required|string'
        ]);

        $resp = EFDReabFechDadosResp::where('efd10_numcgm', $request->cgm)->first();
        if (!$resp) {
            $resp = new EFDReabFechDadosResp;
            $resp->efd10_numcgm = $request->cgm;
        }

        $resp->efd10_nome     = $request->efd10_nome;
        $resp->efd10_cpf      = preg_replace('/\D/', '', $request->efd10_cpf);
        $resp->efd10_email    = $request->efd10_email;
        $resp->efd10_telefone = preg_replace('/\D/', '', $request->efd10_telefone);

        try {
            $resp->save();
            return new DBJsonResponse(null, 'Dados salvos com sucesso.');
        } catch (\Exception $th) {
            return new DBJsonResponse(null, 'Erro ao salvar dados.', 500);
        }
    }
}
