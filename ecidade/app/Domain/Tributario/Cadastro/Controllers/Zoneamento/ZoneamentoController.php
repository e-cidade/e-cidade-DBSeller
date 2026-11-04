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

namespace App\Domain\Tributario\Cadastro\Controllers\Zoneamento;

use App\Domain\Tributario\Cadastro\Requests\Zoneamento\BuscaMacrozonasDialogRequest;
use App\Domain\Tributario\Cadastro\Requests\Zoneamento\BuscaZonasDialogRequest;
use App\Domain\Tributario\Cadastro\Requests\Zoneamento\UpdateMacrozonaRequest;
use App\Domain\Tributario\Cadastro\Requests\Zoneamento\SaveMacrozonaRequest;
use App\Domain\Tributario\Cadastro\Requests\Zoneamento\GetZonaByIdRequest;
use App\Domain\Tributario\Cadastro\Requests\Zoneamento\UpdateZonaRequest;
use App\Domain\Tributario\Cadastro\Services\Zoneamento\ZoneamentoService;
use App\Domain\Tributario\Cadastro\Requests\Zoneamento\SaveZonaRequest;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Http\Controllers\Controller;

class ZoneamentoController extends Controller
{
     
    
    protected $zoneamentoService;

    public function __construct(ZoneamentoService $zoneamentoService)
    {
        $this->zoneamentoService = $zoneamentoService;
    }


    public function getNextSeqMacrozona()
    {
        return $this->zoneamentoService->getNextSeqMacrozona();
    }

    public function getNextSeqZona()
    {
        return $this->zoneamentoService->getNextSeqZona();
    }

    public function saveMacroZona(SaveMacrozonaRequest $request)
    {
        return $this->zoneamentoService->saveMacroZona($request->sigla, $request->localizacao, $request->descricao);
    }


    public function getMacrozonasDialog(BuscaMacrozonasDialogRequest $request)
    {
        $resultados = $this->zoneamentoService->getMacrozonasDialog($request);
        
        return new DBJsonResponse($resultados);
    }

    public function getZonasDialog(BuscaZonasDialogRequest $request)
    {
        $resultados = $this->zoneamentoService->getZonasDialog($request);
        
        return new DBJsonResponse($resultados);
    }

    public function getMacrozonaById(GetZonaByIdRequest $request)
    {
        
        return $this->zoneamentoService->getMacrozonaById($request->sequencial);
    }

    public function saveZona(SaveZonaRequest $request)
    {
        return $this->zoneamentoService->saveZona(
            $request->sequencial,
            $request->sigla,
            $request->descricao
        );
    }

    public function updateMacrozona(UpdateMacrozonaRequest $request)
    {

        return $this->zoneamentoService->updateMacrozona(
            $request->sequencial,
            $request->sigla,
            $request->localizacao,
            $request->descricao
        );
    }

    public function updateZona(UpdateZonaRequest $request)
    {

        return $this->zoneamentoService->updateZona(
            $request->sequencial,
            $request->macrozona,
            $request->sigla,
            $request->descricao
        );
    }
}
