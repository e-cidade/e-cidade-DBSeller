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

namespace App\Domain\Educacao\Escola\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\AtestadoFrequenciaResumo;
use App\Domain\Educacao\Escola\Services\AtestadoFrequenciaService as AFService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AtestadoFrequenciaController extends Controller
{
    public function globalObs(Request $request)
    {
        validaRequest($request->all(), [
            'rechumanoescola' => 'required|integer|exists:rechumanoescola,ed75_i_codigo',
            'obs' => 'nullable|string|max:300',
        ]);

        AFService::updateOrCreateObs($request->rechumanoescola, $request->obs);

        return new DBJsonResponse([]);
    }

    public function getGlobalObs($rechumanoescola = null)
    {
        $obs = '';
        $tReturn = "";
        if ($rechumanoescola) {
            $obs = AtestadoFrequenciaResumo::where('ed364_rechumanoescola', $rechumanoescola)
                ->whereNull('ed364_atestado')
                ->get();
            foreach ($obs as $value) {
                $tReturn .= $value->ed364_observacao;
            }
        }

        return new DBJsonResponse($tReturn ? $tReturn : "");
    }
}
