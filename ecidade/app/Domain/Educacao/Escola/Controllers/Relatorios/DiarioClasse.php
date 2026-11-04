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

namespace App\Domain\Educacao\Escola\Controllers\Relatorios;

use App\Domain\Educacao\Escola\Requests\EmissaoDiarioClasseEscolarizacaoRequest;
use App\Domain\Educacao\Escola\Requests\EmissaoDiarioClasseEspecialRequest;
use App\Http\Controllers\Controller;
use ECidade\Educacao\Escola\Relatorios\DiarioClasse\DiarioClasseTurmaEscolarizacaoPdf;
use ECidade\Educacao\Escola\Relatorios\DiarioClasse\DiarioClasseTurmaEspecialPdf;
use ECidade\Educacao\Escola\Relatorios\DiarioClasse\Factory\TurmaEspecialFactory;
use ECidade\Educacao\Escola\Relatorios\DiarioClasse\Service\TurmaEscolarizacaoService;
use Exception;
use Illuminate\Http\JsonResponse;

class DiarioClasse extends Controller
{
    /**
     * @param EmissaoDiarioClasseEspecialRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function turmasEspeciais(EmissaoDiarioClasseEspecialRequest $request)
    {
        $service = TurmaEspecialFactory::get($request->get('tipo_turma'), $request);
        $diarioClasse = new DiarioClasseTurmaEspecialPdf($request, [$service->processarDados()]);

        $path = $diarioClasse->emitir($request);

        return response()->json(
            [
                'data'=> $path,
                "message"=> utf8_encode("Diário de Classe emitido com sucesso.")
            ]
        );
    }

    /**
     * @param EmissaoDiarioClasseEscolarizacaoRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function turmasEscolarizacao(EmissaoDiarioClasseEscolarizacaoRequest $request)
    {
        $service = new TurmaEscolarizacaoService($request);
        $dadosDiarioClasse = $service->processarDados();
        $diarioClasse = new DiarioClasseTurmaEscolarizacaoPdf($request, $dadosDiarioClasse);
        $path = $diarioClasse->emitir($request);

        return response()->json(
            [
                'data'=> $path,
                "message"=> utf8_encode("Diário de Classe emitido com sucesso.")
            ]
        );
    }
}
