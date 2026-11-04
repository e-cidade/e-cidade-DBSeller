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

namespace App\Domain\Financeiro\Contabilidade\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Financeiro\Contabilidade\Services\ImplantacaoSaldoInicialService;
use App\Domain\Financeiro\Contabilidade\Services\ViradaRestosAPagarService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExercicioContabilController extends Controller
{
    public function implantarSaldoInicial(Request $request)
    {

        $service = new ImplantacaoSaldoInicialService();
        $service->setExercicio($request->get('DB_anousu'))
            ->setInstituicao($request->get('DB_instit'))
            ->processar();
        return new DBJsonResponse([], 'Contas da instituição implantadas.');
    }

    public function virarRestosPagar(Request $request)
    {

        $service = new ViradaRestosAPagarService();
        $service->setExercicio($request->get('DB_anousu'))
            ->setInstituicao($request->get('DB_instit'))
            ->setUsuario($request->get('DB_id_usuario'))
            ->processar();
        return new DBJsonResponse([], 'Virada de Restos a Pagar realizada com sucesso.');
    }

    public function validaVirarRestorPagar(Request $request)
    {

        $service = new ViradaRestosAPagarService();
        $processamento = $service->setExercicio($request->get('DB_anousu'))
            ->setInstituicao($request->get('DB_instit'))
            ->verificaProcessamento();
        $msg = "Verificação da virada de Restos a Pagar realizada com sucesso.";
        return new DBJsonResponse(["processado" => $processamento], $msg);
    }
}
