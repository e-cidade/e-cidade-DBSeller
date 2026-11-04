<?php

namespace App\Domain\RecursosHumanos\ESocial\Controller\InformacoesComplementares;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\RecursosHumanos\ESocial\Requests\InfoComplementarCreateRequest;
use App\Http\Controllers\Controller;
use App\Domain\RecursosHumanos\ESocial\Models\InformacaoComplementar;
use App\Domain\RecursosHumanos\Pessoal\Model\Instituicao\Instituicao;

class InformacoesComplementaresController extends Controller
{
    public function index()
    {
        $instit = db_getsession('DB_instit');
        $instituicao = Instituicao::find($instit);
        $informacaoComplementar = InformacaoComplementar::with('instituicao:codigo,nomeinst,cgc')
            ->where('eso40_cod_inst', $instit)
            ->get();
        return new DBJsonResponse(['instituicao' => $instituicao, 'informacaoComplementar' => $informacaoComplementar]);
    }

    public function store(InfoComplementarCreateRequest $request)
    {

        if ($request->sequencial !== null && $request->sequencial !== '') {
            $informacaoComplementar = InformacaoComplementar::find($request->sequencial);
        } else {
            $informacaoComplementar = new InformacaoComplementar();
        }
        $informacaoComplementar->eso40_tp_insc = $request->tpInsc;
        $informacaoComplementar->eso40_nr_insc = $request->cgm;
        $informacaoComplementar->eso40_num_insc = $request->tpInscNumber;
        $informacaoComplementar->eso40_ind_subst_patr = $request->indSubstPatr;
        $informacaoComplementar->eso40_perc_red_contrib = $request->percRedContrib;
        $informacaoComplementar->eso40_cod_lotacao = $request->lotacaoTributaria;
        $informacaoComplementar->eso40_fator_mes = $request->fatorMes;
        $informacaoComplementar->eso40_fator_13 = $request->fator13;
        $informacaoComplementar->eso40_perc_transf = $request->percTransf;
        $informacaoComplementar->eso40_cod_inst = $request->instituicao;
        $informacaoComplementar->eso40_periodo = $request->periodo;
        $informacaoComplementar->save();

        return new DBJsonResponse(['informacao' => $informacaoComplementar]);
    }

    public function indexPeriodo($periodo, $empregador)
    {
        $instituicao = Instituicao::find(db_getsession('DB_instit'));
        $informacaoComplementar = InformacaoComplementar::with('instituicao:codigo,nomeinst,cgc')
            ->where('eso40_periodo', $periodo)
            ->where('eso40_nr_insc', $empregador)
            ->first();
        return new DBJsonResponse(['instituicao' => $instituicao,'informacao' => $informacaoComplementar]);
    }
}
