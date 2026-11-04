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

namespace App\Domain\Tributario\DividaAtiva\Services\LancamentoHistorico;

use App\Domain\Tributario\DividaAtiva\DTO\LancamentoHistorico\GetDividasFiltradasDTO;
use App\Domain\Tributario\DividaAtiva\DTO\LancamentoHistorico\SaveHistoricoDTO;
use DB;

class DividasService
{

    public function getDividasAtivas(GetDividasFiltradasDTO $dados)
    {
        $dividas = DB::table('divida.divida')->select(
            'divida.v01_coddiv as codDivida',
            'divida.v01_exerc as exercicio',
            'divida.v01_numpre as numpre',
            'divida.v01_numpar as numpar',
            'divida.v01_proced as procedencia',
            'divida.v01_obs as historico',
            'proced.v03_descr as descricao',
            'certdiv.v14_certid as cda',
            'arrenumcgm.k00_numcgm as numcgm',
            'arreinscr.k00_inscr as inscricao',
            'arrematric.k00_matric as matricula'
        )
        ->selectRaw("string_agg(COALESCE(CAST(inicialcert.v51_inicial AS TEXT), ''), ',') as inicial")
        ->selectRaw("string_agg(COALESCE(CAST(processoforo.v70_codforo AS TEXT), ''), ',') as procforo")
        ->join('divida.proced', 'divida.v01_proced', '=', 'proced.v03_codigo')
        ->leftJoin('divida.certdiv', 'divida.v01_coddiv', '=', 'certdiv.v14_coddiv')
        ->leftJoin('juridico.inicialcert', 'certdiv.v14_certid', '=', 'inicialcert.v51_certidao')
        ->leftJoin('juridico.processoforoinicial', 'inicialcert.v51_inicial', '=', 'processoforoinicial.v71_inicial')
        ->leftJoin('juridico.processoforo', 'processoforo.v70_sequencial', '=', 'processoforoinicial.v71_processoforo')
        ->leftJoin('caixa.arrenumcgm', 'arrenumcgm.k00_numpre', '=', 'divida.v01_numpre')
        ->leftJoin('caixa.arreinscr', 'arreinscr.k00_numpre', '=', 'divida.v01_numpre')
        ->leftJoin('caixa.arrematric', 'arrematric.k00_numpre', '=', 'divida.v01_numpre')
        ->whereExists(function ($query) {
            $query->select('arrecad.k00_numpre')
                    ->from('caixa.arrecad')
                    ->whereRaw('divida.v01_numpre = arrecad.k00_numpre');
        })
        ->when($dados->numcgm, function ($query) use ($dados) {
            $query->where('arrenumcgm.k00_numcgm', '=', $dados->numcgm);
        })
        ->when($dados->matricula, function ($query) use ($dados) {
            $query->where('arrematric.k00_matric', '=', $dados->matricula);
        })
        ->when($dados->inscricao, function ($query) use ($dados) {
            $query->where('arreinscr.k00_inscr', '=', $dados->inscricao);
        })
        ->when($dados->codDivida, function ($query) use ($dados) {
            $query->where('divida.v01_coddiv', '=', $dados->codDivida);
        })
        ->when(!empty($dados->exercicio), function ($query) use ($dados) {
            if ($dados->exercicio[0] && empty($dados->exercicio[1])) {
                $query->where('divida.v01_exerc', '>=', $dados->exercicio[0]);
                return;
            }

            if ($dados->exercicio[0] && $dados->exercicio[1]) {
                $query->whereBetween('divida.v01_exerc', [$dados->exercicio[0], $dados->exercicio[1]]);
                return;
            }

            if (empty($dados->exercicio[0]) && $dados->exercicio[1]) {
                $query->where('divida.v01_exerc', '<=', $dados->exercicio[1]);
                return;
            }

            if ($dados->exercicio[0] == $dados->exercicio[1]) {
                $query->where('divida.v01_exerc', $dados->exercicio[0]);
                return;
            }
        })
        ->orderBy('divida.v01_exerc', 'desc')
        ->orderBy('divida.v01_coddiv', 'desc')
        ->orderBy('arrenumcgm.k00_numcgm', 'desc')
        ->orderBy('arreinscr.k00_inscr', 'desc')
        ->orderBy('arrematric.k00_matric', 'desc')
        ->groupBy(
            'divida.v01_coddiv',
            'divida.v01_exerc',
            'divida.v01_numpre',
            'divida.v01_numpar',
            'divida.v01_proced',
            'proced.v03_descr',
            'certdiv.v14_certid',
            'arrenumcgm.k00_numcgm',
            'arreinscr.k00_inscr',
            'arrematric.k00_matric'
        )
        ->paginate(15);

        return $dividas;
    }

    public function getAllDividasToSelect(GetDividasFiltradasDTO $dados)
    {
        $dividas = DB::table('divida.divida')->select(
            'divida.v01_coddiv as codDivida',
            'divida.v01_numpre as numpre'
        )
        ->leftJoin('caixa.arrenumcgm', 'arrenumcgm.k00_numpre', '=', 'divida.v01_numpre')
        ->leftJoin('caixa.arrematric', 'arrematric.k00_numpre', '=', 'divida.v01_numpre')
        ->whereExists(function ($query) {
            $query->select('arrecad.k00_numpre')
                    ->from('caixa.arrecad')
                    ->whereRaw('divida.v01_numpre = arrecad.k00_numpre');
        })
        ->when($dados->numcgm, function ($query) use ($dados) {
            $query->where('arrenumcgm.k00_numcgm', '=', $dados->numcgm);
        })
        ->when($dados->matricula, function ($query) use ($dados) {
            $query->where('arrematric.k00_matric', '=', $dados->matricula);
        })
        ->when($dados->inscricao, function ($query) use ($dados) {
            $query->where('arreinscr.k00_inscr', '=', $dados->inscricao);
        })
        ->when($dados->codDivida, function ($query) use ($dados) {
            $query->where('divida.v01_coddiv', '=', $dados->codDivida);
        })
        ->when(!empty($dados->exercicio), function ($query) use ($dados) {
            if ($dados->exercicio[0] && empty($dados->exercicio[1])) {
                $query->where('divida.v01_exerc', '>=', $dados->exercicio[0]);
                return;
            }

            if ($dados->exercicio[0] && $dados->exercicio[1]) {
                $query->whereBetween('divida.v01_exerc', [$dados->exercicio[0], $dados->exercicio[1]]);
                return;
            }

            if (empty($dados->exercicio[0]) && $dados->exercicio[1]) {
                $query->where('divida.v01_exerc', '<=', $dados->exercicio[1]);
                return;
            }

            if ($dados->exercicio[0] == $dados->exercicio[1]) {
                $query->where('divida.v01_exerc', $dados->exercicio[0]);
                return;
            }
        })
        ->get();

        return $dividas;
    }

    public function getDividasExercicios(GetDividasFiltradasDTO $dados)
    {
        $exerciciosDividas = DB::table('divida.divida')->distinct()
        ->select(
            'divida.v01_exerc as exercicios'
        )
        ->join('caixa.arrecad', 'divida.v01_numpre', '=', 'arrecad.k00_numpre')
        ->leftJoin('caixa.arrenumcgm', 'arrenumcgm.k00_numpre', '=', 'divida.v01_numpre')
        ->leftJoin('caixa.arreinscr', 'arreinscr.k00_numpre', '=', 'divida.v01_numpre')
        ->leftJoin('caixa.arrematric', 'arrematric.k00_numpre', '=', 'divida.v01_numpre')
        ->when($dados->numcgm, function ($query) use ($dados) {
            $query->where('arrenumcgm.k00_numcgm', '=', $dados->numcgm);
        })
        ->when($dados->matricula, function ($query) use ($dados) {
            $query->where('arrematric.k00_matric', '=', $dados->matricula);
        })
        ->when($dados->inscricao, function ($query) use ($dados) {
            $query->where('arreinscr.k00_inscr', '=', $dados->inscricao);
        })
        ->when($dados->codDivida, function ($query) use ($dados) {
            $query->where('divida.v01_coddiv', '=', $dados->codDivida);
        })
        ->orderBy('divida.v01_exerc', 'desc')
        ->get()
        ->map(function ($item) {
            return $item->exercicios;
        });

        return $exerciciosDividas;
    }

    public function saveHistorico(SaveHistoricoDTO $dados)
    {
        DB::table('divida.divida')
        ->whereIn('v01_coddiv', $dados->dividas)
        ->update([
            'v01_obs' => DB::raw("concat_ws('\n', nullif(v01_obs, ''), '$dados->historico - $dados->data')")
        ]);
    }
}
