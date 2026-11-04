<?php

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use stdClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use ECidade\Educacao\MatriculaOnline\Repository\ConfiguracaoRepository;
use App\Domain\Educacao\MatriculaOnline\Services\ParametrosEnvioNotificacoesService;
use Symfony\Component\Validator\Constraints\Length;

class ParametrosEnvioNotificacoesController extends Controller
{
    protected $configuracaoRepository;
    protected $service;

    public function __construct()
    {
        $this->configuracaoRepository = new ConfiguracaoRepository();
        $this->service = new ParametrosEnvioNotificacoesService();
    }

    public function getParametros()
    {
        $configuracaoModel = $this->configuracaoRepository->get();
        $parametros = new stdClass();
        $parametros->checks = $this->service->getParametrosTipoNotificacao($configuracaoModel);
        $parametros->telefoneCentralMatriculas = $configuracaoModel->getTelefoneCentralMatriculas();
        return  new DBJsonResponse($parametros);
    }

    public function salvar(Request $request)
    {
        $configuracaoModel = $this->configuracaoRepository->get();
        $model = $this->service->ajustarParametrosTipoNotificacao($configuracaoModel, $request->campos['checks']);
        $configuracaoModel->setTelefoneCentralMatriculas($request->campos['telefoneCentralMatriculas']);
        return  new DBJsonResponse($this->configuracaoRepository->salvar($model));
    }
}
