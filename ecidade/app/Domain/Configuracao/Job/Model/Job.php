<?php

namespace App\Domain\Configuracao\Job\Model;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table= "jobs";

    protected $fillable = [
        "connection"
    ];

    public $timestamps = false;

    public function setPayloadAttribute($value)
    {
        // Remover caracteres não suportados
        if (is_string($value)) {
            $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value); // Remove caracteres de controle
        }

        // Definir o payload
        $this->attributes['payload'] = $value;
    }
}
