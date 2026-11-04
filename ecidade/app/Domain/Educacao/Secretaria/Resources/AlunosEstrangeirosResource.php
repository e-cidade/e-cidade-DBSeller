<?php


namespace App\Domain\Educacao\Secretaria\Resources;

use App\Domain\Educacao\Escola\Models\DiarioFinal;
use App\Domain\Educacao\Escola\Models\Historico;
use Illuminate\Support\Facades\DB;

class AlunosEstrangeirosResource
{
    public static function toObjectToRelatorio($aluno)
    {
        $percentuaal = DB::table('historicomps')->whereRaw("
            ed62_i_historico in (
                select ed61_i_codigo from historico
                    where
                ed61_i_aluno = {$aluno->ed47_i_codigo}
            ) and
            ed62_i_serie = {$aluno->ed11_i_codigo}
        ")->get();

        if (count($percentuaal) > 0) {
            $percentuaal = $percentuaal[0]->ed62_percentualfrequencia;
        } else {
            $percentuaal = DiarioFinal
                ::whereRaw("ed74_i_diario in(select ed95_i_codigo from diario
                where ed95_i_aluno = {$aluno->ed47_i_codigo} and
                ed95_i_escola = {$aluno->ed57_i_escola} and
                ed95_i_calendario = {$aluno->ed52_i_codigo})")
                ->limit(1)->get();

             $percentuaal = isset($percentuaal[0]) ? $percentuaal[0]->ed74_i_percfreq : 0;
        }
        
        $retorno = [
            'codigo' => $aluno->ed47_i_codigo,
            'nome' => trim($aluno->ed47_v_nome),
            'sexo' => $aluno->ed47_v_sexo == 'F' ? 'FEMININO' : 'MASCULINO',
            'dataNascimento' => $aluno->ed47_d_nasc->format('d/m/Y'),
            'dataMatricula' => \DBDate::converter($aluno->ed60_d_datamatricula),
            'dataTransferencia' =>  trim($aluno->ed60_c_situacao) == 'TRANSFERIDO FORA' ||
                trim($aluno->ed60_c_situacao) == 'TRANSFERIDO REDE' ?
                    \DBDate::converter($aluno->ed60_d_datasaida) : '',
            'etapa' => trim($aluno->ed11_c_descr),
            'percentualFrequencia' => $percentuaal
        ];

        return (object) $retorno;
    }
}
