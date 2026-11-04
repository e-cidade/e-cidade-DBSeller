<?php

namespace App\Domain\RecursosHumanos\ESocial\Models;

use App\Domain\RecursosHumanos\Pessoal\Model\RhPessoal;
use Illuminate\Database\Eloquent\Model;

class ExameToxicologico extends Model
{
    // Nome da tabela
    protected $table = 'esocial.exametoxicologico';

    // Chave primária
    protected $primaryKey = 'eso41_sequencial';

    // Desabilita os campos timestamps (created_at e updated_at)
    public $timestamps = false;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'eso41_cpftrab',
        'eso41_matricula',
        'eso41_dtexame',
        'eso41_cnpjlab',
        'eso41_codseqexame',
        'eso41_nmmed',
        'eso41_nrcrm',
        'eso41_ufcrm',
        'eso41_instit',
    ];

    public function matricula()
    {
        return $this->belongsTo(RhPessoal::class, 'eso41_matricula', 'rh01_regist');
    }
}
