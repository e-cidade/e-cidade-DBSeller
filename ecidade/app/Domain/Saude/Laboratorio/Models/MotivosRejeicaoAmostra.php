<?php
namespace App\Domain\Saude\Laboratorio\Models;

use Illuminate\Database\Eloquent\Model;

class MotivosRejeicaoAmostra extends Model
{
    public $timestamps = false;
    protected $table = 'laboratorio.motivosrejeicaoamostra';
    protected $primaryKey = 'la73_sequencial';
}
