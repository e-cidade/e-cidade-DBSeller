<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracaoGeral extends Model
{
    protected $table = 'plugins.configuracaomatriculaonline';
    public $timestamps = false;
    protected $primaryKey = 'mo25_sequencial';
    public $incrementing = true;
}
