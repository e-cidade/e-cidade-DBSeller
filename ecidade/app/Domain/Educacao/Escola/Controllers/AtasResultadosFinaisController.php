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
use App\Domain\Educacao\Escola\Relatorios\AtaResultadosFinaisSigaPDF;
use App\Domain\Educacao\Escola\Services\AtaResultadosFinaisSigaService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class AtasResultadosFinaisController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function ataResultadosFinaisSiga(Request $request)
    {
        if (empty($request->get('turmasEtapas'))) {
            throw new Exception('Selecione uma turma');
        }
        $turmasSerieRegimeMat = explode(',', $request->get('turmasEtapas'));

        $dadosRelatorios = [];
        foreach ($turmasSerieRegimeMat as $turmaSerieRegimeMat) {
            $turma = \TurmaRepository::getTurmaByCodigoTurmaSerieRegimeMat($turmaSerieRegimeMat);
            $etapa = \EtapaRepository::getEtapaByCodigoTurmaSerieRegimeMat($turmaSerieRegimeMat);
            $ataResultadosFinaisService = new AtaResultadosFinaisSigaService($turma, $etapa);
//        $ataResultadosFinaisService->setSomenteAlunosAtivos($request->get('somenteAlunosAtivos') == 'true');
            $dadosRelatorios[] = $ataResultadosFinaisService->getDados();
        }

        $espelhoPeriodo = new AtaResultadosFinaisSigaPDF($dadosRelatorios);
        return new DBJsonResponse($espelhoPeriodo->emitirPdf());
    }
}
