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

namespace App\Domain\Tributario\ISSQN\Controller\SalaoParceiro;

use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\ISSQN\Requests\SalaoParceiro\BuscarInscricoesDialogRequest;
use App\Domain\Tributario\ISSQN\Requests\SalaoParceiro\SaveParceirosRequest;
use App\Domain\Tributario\ISSQN\Requests\SalaoParceiro\SaveAtividadesSalaoRequest;
use App\Domain\Tributario\ISSQN\Requests\SalaoParceiro\GetSaloesParceirosRequest;
use App\Domain\Tributario\ISSQN\Requests\SalaoParceiro\UpdateParceiroRequest;
use App\Domain\Tributario\ISSQN\Requests\SalaoParceiro\UpdateSalaoRequest;
use App\Domain\Tributario\ISSQN\Services\SalaoParceiro\SalaoParceiroService;

class SalaoParceiroController extends Controller
{
     
    
    protected $salaoParceiroService;

    public function __construct(SalaoParceiroService $salaoParceiroService)
    {
        $this->salaoParceiroService = $salaoParceiroService;
    }

    /**
     * Salva o salão parceiro.
     *
     * @param SaveParceirosRequest $request O objeto de requisição contendo os dados.
     * @param GetSaloesParceirosRequest $request O objeto de requisição contendo os dados.
     * @return DBJsonResponse O objeto de resposta.
    */
    
    public function saveSalaoParceiro(SaveParceirosRequest $request)
    {
        $data = $request->all();
        $result = $this->salaoParceiroService->saveParceiros($data);

        return new DBJsonResponse($result);
    }

    public function getParceiros(GetSaloesParceirosRequest $request)
    {
        $q202_inscr = $request['q202_inscr'];
        return $this->salaoParceiroService->getParceiros($q202_inscr);
    }

    public function getSalao(GetSaloesParceirosRequest $request)
    {
        $q202_inscr = $request['q202_inscr'];
        return $this->salaoParceiroService->getSalao($q202_inscr);
    }

    public function getAll(GetSaloesParceirosRequest $request)
    {
        $q202_inscr = $request['q202_inscr'];
        return new DBJsonResponse($this->salaoParceiroService->getAll($q202_inscr));
    }


    public function updateParceiro(UpdateParceiroRequest $request)
    {
        $data = $request->all();
        if (!isset($data['q203_dtfinal'])) {
            $data['q203_dtfinal'] = null;
        }
        return $this->salaoParceiroService->updateParceiro($data);
    }

    public function updateSalao(UpdateSalaoRequest $request)
    {

        $data = $request->all();
        if (!isset($data['q202_dtfinal']) || $data['q202_dtfinal'] == '') {
            $data['q202_dtfinal'] = null;
        }
        return $this->salaoParceiroService->updateSalao($data);
    }

    public function getAtividadesSalao(GetSaloesParceirosRequest $request)
    {

        $q202_inscr = $request['q202_inscr'];
        $result = $this->salaoParceiroService->getAtividadesSalao($q202_inscr);
        return new DBJsonResponse($result);
    }

    public function saveAtividadesSalao(SaveAtividadesSalaoRequest $request)
    {

        return $this->salaoParceiroService->saveAtividadesSalao($request->input('deducoes'));
    }

    public function getInscricoesDialog(BuscarInscricoesDialogRequest $request)
    {
        $resultados = $this->salaoParceiroService->getInscricoesDialog($request);

        return new DBJsonResponse($resultados);
    }
}
