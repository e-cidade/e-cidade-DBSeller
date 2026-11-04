<?php

namespace App\Domain\RecursosHumanos\Pessoal\Controller;

use App\Domain\RecursosHumanos\Pessoal\Services\PrevidenciaComplementarService;
use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\Pessoal\Model\PrevidenciaComplementarModel;
use App\Domain\RecursosHumanos\Pessoal\Requests\PrevidenciaComplementar\SalvarPrevidenciaComplementarRequest;
use App\Domain\RecursosHumanos\Pessoal\Requests\PrevidenciaComplementar\BuscaPrevidenciaComplementarRequest;
use Illuminate\Http\Request;

class PrevidenciaComplementarController extends Controller
{
    /**
     * @var PrevidenciaComplementarService
     */
    private $previdenciaComplementarService;

    public function __construct(PrevidenciaComplementarService $previdenciaComplementar)
    {
        $this->previdenciaComplementarService = $previdenciaComplementar;
    }

    public function save(SalvarPrevidenciaComplementarRequest $request)
    {
        $previdenciaComplementar = $this->previdenciaComplementarService
            ->buscarPorMatricula($request->get('matricula'));
        if (empty($previdenciaComplementar)) {
            $previdenciaComplementar = new PrevidenciaComplementarModel();
        }
        $cnpj = (string) str_replace('-', '', str_replace('/', '', str_replace('.', '', $request->get('cnpj'))));
        $previdenciaComplementar->setMatricula($request->get('matricula'));
        $previdenciaComplementar->setInstituicao($request->get('DB_instit'));
        $previdenciaComplementar->setTipoPrevidencia($request->get('tipoPrevidencia'));
        $previdenciaComplementar->setCnpj($cnpj);
        $previdenciaComplementar->setDeducaoRelativa($request->get('deducaoRelativa'));
        $previdenciaComplementar->setContribuicaoPatrocinador($request->get('contribuicaoPatrocinador'));
        $previdenciaComplementar->save();

        return new DBJsonResponse([], 'Previdência Complementar salva com sucesso.');
    }

    public function get(BuscaPrevidenciaComplementarRequest $request)
    {
        return $this->previdenciaComplementarService->buscarPorMatricula($request->get('matricula'));
    }

    public function delete(BuscaPrevidenciaComplementarRequest $request)
    {
        $this->previdenciaComplementarService->deletarPorMatricula($request->get('matricula'));
        return new DBJsonResponse([], 'Previdência Complementar excluída com sucesso.');
    }
}
