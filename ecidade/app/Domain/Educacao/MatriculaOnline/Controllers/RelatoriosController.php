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

namespace App\Domain\Educacao\MatriculaOnline\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\MatriculaOnline\Relatorios\RelatorioDemandaReprimida;
use App\Domain\Educacao\MatriculaOnline\Relatorios\RelatorioGeralInscricoes;
use App\Domain\Educacao\MatriculaOnline\Resources\FaseResource;
use App\Domain\Educacao\MatriculaOnline\Relatorios\Vagas\RelatorioVagasParciaisCSV;
use App\Domain\Educacao\MatriculaOnline\Relatorios\Vagas\RelatorioVagasParciaisPdf;
use App\Domain\Educacao\MatriculaOnline\Services\InscricoesService;
use App\Domain\Educacao\MatriculaOnline\Services\VagasParciaisService;
use App\Http\Controllers\Controller;
use DBException;
use Exception;
use Illuminate\Http\Request;

class RelatoriosController extends Controller
{

    private $service;

    public function __construct(VagasParciaisService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws DBException
     */
    public function emitirRelatorioVagasParciais(Request $request)
    {
        $retorno = [];
        $parametros = $request->all();
        $dados = $this->service->getDados($parametros);

        if ($request->input('isPdf')) {
            $retorno[] = (new RelatorioVagasParciaisPdf($dados))->emitir();
        } else {
            $retorno[] = (new RelatorioVagasParciaisCSV($dados))->emitir();
        }

        return new DBJsonResponse($retorno);
    }

    /**
     * @throws Exception
     */
    public function geralInscricoes(Request $request)
    {
        $retorno = [];
        $parametros = $request->input('fases');
        $inscricoesService = new InscricoesService();
        $dados = $inscricoesService->getDados($parametros);

        $retorno[] = (new RelatorioGeralInscricoes($dados))->emitir();

        return new DBJsonResponse($retorno);
    }

    public function demandaReprimida(Request $request)
    {
        $retorno = [];
        $fase = $request->input('fase');
        $etapa = $request->input('etapa');
        $escola = $request->input('escola');
        $inscricoesService = new InscricoesService();
        $dados = $inscricoesService->getDadosDemandaReprimida($fase, $etapa, $escola);

        $retorno[] = (new RelatorioDemandaReprimida($dados))->emitir();

        return new DBJsonResponse($retorno);
    }
}
