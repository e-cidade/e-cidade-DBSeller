<?php

namespace App\Domain\Configuracao\Job\Model;

use Illuminate\Database\Eloquent\Model;

class JobFailed extends Model
{
    protected $table= "failed_jobs";

    protected $fillable = [
         "connection"
    ];

    public $timestamps = false;
}
