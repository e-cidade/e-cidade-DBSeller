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
use App\Domain\Educacao\Escola\Models\Calendario;
use App\Domain\Educacao\Escola\Models\Turma;
use App\Domain\Educacao\Escola\Repositories\CalendarioRepository;
use App\Domain\Educacao\Escola\Repositories\ProfissionalEscolaRepository;
use App\Domain\Educacao\Escola\Resources\CalendarioResource;
use App\Domain\Educacao\Escola\Resources\TurmaResource;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class CalendarioController
 * @package App\Domain\Educacao\Escola\Controllers
 */
class CalendarioController extends Controller
{
    protected $repository;
    public function __construct()
    {
        $this->repository = new CalendarioRepository();
    }

    public function buscarCalendariosAtivosEscola(Request $request, $escola)
    {
        $validaUsuario = $request->has('validaUsuario');
        $isLoginDB = $request->user()->id_usuario == 1;
        $hasFullPermission = false;

        if ($validaUsuario && !$isLoginDB) {
            $calendarios = [];
            $profissionalRepository = new ProfissionalEscolaRepository();
            $filtros = (object) [
                'escola' => $escola,
                'cgm' => $request->user()->usuarioCgm->cgm->z01_numcgm
            ];

            $profissionalEscola = $profissionalRepository->getProfissionaisEscola($filtros);

            if ($profissionalEscola->count() === 0) {
                $hasFullPermission = true;
            }

            if ($profissionalEscola->count() > 0) {
                $permissoes = [];
                foreach ($profissionalEscola as $profissional) {
                    $permissoes[] = $profissional->permissaoDiario;
                }

                $hasFullPermission = in_array('TOTAL', $permissoes);
                $isProfessor = in_array('PROFESSOR', $permissoes);

                if (!$hasFullPermission && $isProfessor) {
                    $dataSistema = Carbon::createFromTimestamp($request->get('DB_datausu'));
                    $calendarios = $profissionalRepository
                        ->getCalendariosTurmasRegente($profissionalEscola->first(), $dataSistema);
                    return new DBJsonResponse($calendarios);
                }
            }
        }

        return new DBJsonResponse($this->repository->buscarCalendariosAtivosEscola($escola));
    }

    public function buscarCalendariosAtivosEscolaAEE($escola)
    {
        $calendarios = Calendario::selectRaw('ed52_i_codigo, ed52_i_ano, ed52_c_descr')
                ->distinct()
                ->join('duracaocal', 'duracaocal.ed55_i_codigo', '=', 'calendario.ed52_i_duracaocal')
                ->join('calendarioescola', 'calendarioescola.ed38_i_calendario', '=', 'calendario.ed52_i_codigo')
                ->join('turmaac', 'turmaac.ed268_i_calendario', '=', 'calendario.ed52_i_codigo')
                ->where('ed38_i_escola', $escola)
                ->apenasAtivos()
                ->orderBy('ed52_i_ano', 'desc')->get()->all();
        return new DBJsonResponse(CalendarioResource::toArray($calendarios), '');
    }

    public function buscarPeriodosCalendario($calendario)
    {
        $periodos = Calendario::selectRaw('ed09_i_codigo, ed09_c_abrev, ed09_c_descr')
                ->join('periodocalendario', 'periodocalendario.ed53_i_calendario', '=', 'calendario.ed52_i_codigo')
                ->join(
                    'periodoavaliacao',
                    'periodoavaliacao.ed09_i_codigo',
                    '=',
                    'periodocalendario.ed53_i_periodoavaliacao'
                )
                ->where('calendario.ed52_i_codigo', $calendario)
                ->orderBy('ed09_i_sequencia')->get()->all();

        return new DBJsonResponse($periodos);
    }

    public function periodosCalendario($calendario)
    {
        $periodos = Calendario::find($calendario)->periodos()->get();
        return new DBJsonResponse($periodos, '');
    }
}
