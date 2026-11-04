<?php

namespace App\Domain\Patrimonial\Protocolo\Controller\Processo\AndamentoPadrao;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Patrimonial\Protocolo\Model\Processo\AndamentoPadrao\CamposDinamicos;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\AndamentoPadrao\CamposDinamicosRepository;
use App\Domain\Patrimonial\Protocolo\Transformers\Processo\AndamentoPadrao\CamposDinamicos as CamposDinamicosTransform;

class CamposDinamicosController extends Controller
{
    /**
     * Construtor da classe
     *
     * @return void
     */
    public function __construct(CamposDinamicosRepository $camposDinamicosRepository)
    {
        $this->repository = $camposDinamicosRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tipo_processo)
    {
        $ordem = $request->input('ordem', null);

        if (!empty($tipo_processo) && !empty($ordem)) {
            return new DBJsonResponse((new CamposDinamicosTransform)->transform(
                $this->repository->findAllByTipoprocessoAndOrdem(
                    $tipo_processo,
                    $ordem
                )
            ));
        }

        return new DBJsonResponse((new CamposDinamicosTransform)->transform($this->repository->getAll()));
    }

    public function delete(Request $request, $tipo_processo)
    {
        $ordem      = $request->input('ordem', null);
        $codigo     = $request->input('codigo', null);
        $msgSucesso = "Excluí­do com sucesso";

        if (!empty($codigo)) {
            $this->repository->deleteByCodigo($codigo);
            return new DBJsonResponse(null, $msgSucesso, 200, false);
        }

        if (empty($ordem)) {
            throw new \Exception("Deve ser informado o parametro ordem para a exclusão dos andamentos");
        }

        $this->repository->deleteByTipoprocessoAndOrdem($tipo_processo, $ordem);
        return new DBJsonResponse(null, $msgSucesso, 200, false);
    }

    public function salvar(Request $request, $tipo_processo, $ordem)
    {
        $idCampoDinamico = $request->input('idCampoDinamico', null);
        $codcam          = $request->input('codcam', null);
        $obrigatorio     = $request->input('obrigatorio', null);
        
        if (empty($codcam)) {
            throw new Exception('Código do campo não informado.');
        }

        if ($obrigatorio === null) {
            throw new Exception('Campo que define se é obrigatório não foi informado.');
        }
        
        $obrigatorio = (bool)$obrigatorio;

        $camposDinamicosModel = new CamposDinamicos();
        $camposDinamicosModel->setAndpadraoCodigo($tipo_processo);
        $camposDinamicosModel->setAndpadraoOrdem($ordem);

        if (!empty($idCampoDinamico)) {
            $camposDinamicosModel->setCodigo($idCampoDinamico);
        }

        $camposDinamicosModel->setCodcam($codcam);
        $camposDinamicosModel->setObrigatorio($obrigatorio);
        
        $this->repository->persist($camposDinamicosModel);

        $campoSalvo = $this->repository->findByCodigo($camposDinamicosModel->getCodigo());
        
        return new DBJsonResponse((new CamposDinamicosTransform)->transform($camposDinamicosModel));
    }

    public function getByProcessoDepto(Request $request, $codigo_processo)
    {
        $codigoDepto = $request->input('codigo_depto', null);
        $codigoDepto = $codigoDepto == 'null' ? null : $codigoDepto;
        if (!empty($codigoDepto)) {
            $campos = $this->repository->getByProcessoDepto($codigo_processo, $codigoDepto);
        } else {
            $campos = $this->repository->getByProcesso($codigo_processo);
        }
        
        return new DBJsonResponse((new CamposDinamicosTransform)->transform($campos));
    }
}
