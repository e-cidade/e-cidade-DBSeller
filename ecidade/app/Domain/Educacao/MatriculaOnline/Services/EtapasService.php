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

namespace App\Domain\Educacao\MatriculaOnline\Services;

use App\Domain\Educacao\Escola\Models\Ensino;
use App\Domain\Educacao\MatriculaOnline\Models\Ciclo;
use App\Domain\Educacao\MatriculaOnline\Models\CicloEnsino;
use App\Domain\Educacao\MatriculaOnline\Models\Fase;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class EtapasService
{
    public function getEtapasPorFase($codFase)
    {
        return DB::table('plugins.vagas')
            ->distinct()
            ->select(
                'serie.ed11_i_codigo',
                DB::raw('TRIM(serie.ed11_c_descr) as ed11_c_descr'),
                DB::raw('TRIM(ensino.ed10_c_descr) as ed10_c_descr'),
                DB::raw('ensino.ed10_i_codigo AS db_ed10_i_codigo')
            )
            ->join('plugins.fase', 'plugins.fase.mo04_codigo', '=', 'plugins.vagas.mo10_fase')
            ->join('serie', 'serie.ed11_i_codigo', '=', 'plugins.vagas.mo10_serie')
            ->join('plugins.escolas', 'plugins.escolas.mo53_codigo', '=', 'plugins.vagas.mo10_escola')
            ->join('ensino', 'ensino.ed10_i_codigo', '=', 'plugins.vagas.mo10_ensino')
            ->join('cursoedu', 'cursoedu.ed29_i_ensino', '=', 'ensino.ed10_i_codigo')
            ->leftJoin('cursoturno', function ($join) {
                $join->on('cursoturno.ed85_i_curso', '=', 'cursoedu.ed29_i_codigo')
                    ->on('cursoturno.ed85_i_escola', '=', 'plugins.escolas.mo53_escola')
                    ->on('cursoturno.ed85_i_turno', '=', 'plugins.vagas.mo10_turno');
            })
            ->where('plugins.vagas.mo10_fase', $codFase)
            ->where('plugins.fase.mo04_processada', true)
            ->orderBy('ensino.ed10_i_codigo')
            ->orderBy('serie.ed11_i_codigo')
            ->get();
    }

    public function getEscolas($codFase, $codEtapa)
    {
        return DB::table('plugins.vagas')
            ->distinct()
            ->select(
                'escolas.mo53_codigo as cod_escola',
                DB::raw('TRIM(escolas.mo53_nome) as nome_escola'),
                'escolas.mo53_escola as cod_escola_sede',
                'escola.ed18_c_nome as nome_escola_sede'
            )
            ->join('plugins.fase', 'plugins.fase.mo04_codigo', '=', 'plugins.vagas.mo10_fase')
            ->join('serie', 'serie.ed11_i_codigo', '=', 'plugins.vagas.mo10_serie')
            ->join('plugins.escolas', 'plugins.escolas.mo53_codigo', '=', 'plugins.vagas.mo10_escola')
            ->join('escola.escola', 'escola.ed18_i_codigo', '=', 'plugins.escolas.mo53_escola')
            ->join('ensino', 'ensino.ed10_i_codigo', '=', 'plugins.vagas.mo10_ensino')
            ->join('cursoedu', 'cursoedu.ed29_i_ensino', '=', 'ensino.ed10_i_codigo')
            ->leftJoin('cursoturno', function ($join) {
                $join->on('cursoturno.ed85_i_curso', '=', 'cursoedu.ed29_i_codigo')
                    ->on('cursoturno.ed85_i_escola', '=', 'plugins.escolas.mo53_escola')
                    ->on('cursoturno.ed85_i_turno', '=', 'plugins.vagas.mo10_turno');
            })
            ->where('plugins.vagas.mo10_fase', $codFase)
            ->where('serie.ed11_i_codigo', $codEtapa)
            ->orderBy('nome_escola')
            ->get();
    }
}
