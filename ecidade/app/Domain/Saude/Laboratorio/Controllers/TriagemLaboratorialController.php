<?php

/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Saude\Laboratorio\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Saude\Laboratorio\Services\TriagemLaboratorialService;
use App\Domain\Saude\Laboratorio\Requests\TriagemLaboratorialRequest;
use App\Domain\Saude\Laboratorio\Resources\TriagemLaboratorialResource;
use App\Domain\Saude\Laboratorio\Relatorios\PendenciasTriagemPdf;
use JSON;

class TriagemLaboratorialController extends Controller
{

    protected $service;
    protected $resource;

    public function __construct()
    {
        $this->service = new TriagemLaboratorialService;
        $this->resource = new TriagemLaboratorialResource;
    }

    public function exportarPendenciasTriagem(Request $request)
    {
        TriagemLaboratorialRequest::validaRequestExportarPendencias($request->all());
        $pendenciasTriagem = $this->service->buscarPendenciasTriagem($request->all());
        $pdf = new PendenciasTriagemPdf($pendenciasTriagem);
        $pdf->setFiltros($request->all());
        return new DBJsonResponse($pdf->emitir(), 'Dados exportados com sucesso!');
    }

    public function buscarRequisicoes(Request $request)
    {
        TriagemLaboratorialRequest::validaRequestBuscarRequisicoes($request->all());
        return new DBJsonResponse(
            $this->resource->condicionarBuscaRequisicoes(
                $this->service->buscarRequisicoes($request->all())
            ),
            'Requisições com suas respectivas situações dos exames listadas com sucesso!'
        );
    }

    public function buscar(Request $request)
    {
        TriagemLaboratorialRequest::validaRequestBuscar($request->all());
        return new DBJsonResponse(
            $this->resource->toArray($this->service->buscar($request->all())),
            'Triagens do laboratório listadas com sucesso!'
        );
    }

    public function processar(Request $request)
    {
        $requestTriagens = json_decode(utf8_encode_all(stripslashes($request->all()['requestTriagens'])));
        TriagemLaboratorialRequest::validaRequestProcessar($requestTriagens);
        $this->service->processar($requestTriagens);
        return new DBJsonResponse(null, 'Triagens do laboratório processadas com sucesso!');
    }
}
