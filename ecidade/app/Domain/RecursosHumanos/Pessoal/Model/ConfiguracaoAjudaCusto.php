<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConfiguracaoAjudaCusto extends Model
{
    protected $table = 'pessoal.configuracaoajudacusto';
    protected $primaryKey = 'rh311_sequencial';
    public $timestamps = false;

    public function configuracao($instituicao)
    {
        $query = DB::table($this->table)
            ->join(
                'rhrubricas as rubricaservidor',
                'rubricaservidor.rh27_rubric',
                '=',
                DB::Raw('rh311_rubric and rubricaservidor.rh27_instit = rh311_instit')
            )
            ->leftjoin(
                'rhrubricas as rubricadependente',
                'rubricadependente.rh27_rubric',
                '=',
                DB::Raw('rh311_rubricdepend and rubricadependente.rh27_instit = rh311_instit')
            )->select(
                "{$this->table}.*",
                "rubricaservidor.rh27_rubric as rubrica",
                "rubricaservidor.rh27_descr as descricao_rubrica",
                "rubricadependente.rh27_rubric as rubrica_dependente",
                "rubricadependente.rh27_descr as rubrica_dependente_descricao"
            )->where("rh311_instit", "=", $instituicao);

        $data = $query->first();
        
        if (!empty($data->rh311_grauparentesco)) {
            $data->rh311_grauparentesco = json_decode($data->rh311_grauparentesco, true);
        }

        return $data;
    }
}
