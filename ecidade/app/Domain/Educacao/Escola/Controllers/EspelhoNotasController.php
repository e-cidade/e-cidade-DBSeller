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

use classes\cl_edu_parametros;
use libs\db_utils;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\Escola\Models\PeriodoAvaliacao;
use App\Domain\Educacao\Escola\Relatorios\EspelhoAnualPDF;
use App\Domain\Educacao\Escola\Relatorios\EspelhoPeriodoPDF;
use App\Domain\Educacao\Escola\Services\EspelhoAnualService;
use App\Domain\Educacao\Escola\Services\EspelhoPeriodoService;
use App\Domain\Educacao\Escola\Models\TurmaEtapaRegimeMatricula;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class EspelhoNotasController extends Controller
{
    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function espelhoPeriodo(Request $request)
    {
        $codigoTurma = $request->get('codigoTurma');
        $turma = \TurmaRepository::getTurmaByCodigo($codigoTurma);
        $turmaRegime = TurmaEtapaRegimeMatricula::join('serieregimemat', 'ed220_i_serieregimemat', 'ed223_i_codigo')
            ->where('ed220_i_turma', $codigoTurma)
            ->where('ed223_i_serie', $request->get('etapa'))
            ->first();

        $etapa = \EtapaRepository::getEtapaByCodigoTurmaSerieRegimeMat($turmaRegime->ed220_i_codigo);
        $periodo = PeriodoAvaliacao::find($request->get('periodo'));

        $disciplinasFiltrar = [];
        if (!empty($request->get('disciplinas'))) {
            $disciplinasFiltrar = explode(',', $request->get('disciplinas'));
        }

        $espelhoPeriodoService = new EspelhoPeriodoService($turma, $etapa, $periodo);
        $espelhoPeriodoService->setSomenteAlunosAtivos($request->get('somenteAlunosAtivos') == 'true');
        $dados = $espelhoPeriodoService->getDados($disciplinasFiltrar);

        $umaPorPagina = $request->get('umaPorPagina') == 'true';
        $exibirNotas = $request->get('exibirNotas') == 'true';

        $parametroRecNotasParciais = $this->verificarParamentro();

        $espelhoPeriodo = new EspelhoPeriodoPDF($dados, $exibirNotas, $umaPorPagina, $parametroRecNotasParciais);
        return new DBJsonResponse($espelhoPeriodo->emitirPdf());
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function espelhoAnual(Request $request)
    {
        $codigoTurma = $request->get('codigoTurma');
        $turma = \TurmaRepository::getTurmaByCodigo($codigoTurma);
        $turmaRegime = TurmaEtapaRegimeMatricula::join('serieregimemat', 'ed220_i_serieregimemat', 'ed223_i_codigo')
            ->where('ed220_i_turma', $codigoTurma)
            ->where('ed223_i_serie', $request->get('etapa'))
            ->first();

        $etapa = \EtapaRepository::getEtapaByCodigoTurmaSerieRegimeMat($turmaRegime->ed220_i_codigo);

        $disciplinasFiltrar = [];
        if (!empty($request->get('disciplinas'))) {
            $disciplinasFiltrar = explode(',', $request->get('disciplinas'));
        }

        $espelhoPeriodoService = new EspelhoAnualService($turma, $etapa, $request->get('modelo'));
        $dados = $espelhoPeriodoService->getDados($disciplinasFiltrar);

        $espelhoAnual = new EspelhoAnualPDF($dados);
        return new DBJsonResponse($espelhoAnual->emitirPdf());
    }

    public function verificarParamentro()
    {
        $cledu_parametros  = new \cl_edu_parametros;
        $escola            = db_getsession("DB_coddepto");
        $sCamposParametros = "ed233_habilitarrecuperacaonotaparcial";
        $sSqlParametros    = $cledu_parametros->sql_query(null, $sCamposParametros, null, " ed233_i_escola = $escola");
        $sResultParametros = $cledu_parametros->sql_record($sSqlParametros);
        $bHabilitarRecuperacaoNotaParcial = \db_utils::fieldsMemory($sResultParametros, 0)
                                                                    ->ed233_habilitarrecuperacaonotaparcial;
        if ($bHabilitarRecuperacaoNotaParcial == "t") {
            return true;
        } else {
            return false;
        }
    }
}
