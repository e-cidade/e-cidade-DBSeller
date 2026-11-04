<?php

namespace App\Domain\Configuracao\Usuario\Models;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Database\Eloquent\Model;

class UsuarioCgm extends Model
{
    protected $table = 'configuracoes.db_usuacgm';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;
    public $incrementing = false;

    public function cgm()
    {
        return $this->belongsTo(Cgm::class, 'cgmlogin', 'z01_numcgm');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public static function findByCgm($cgm)
    {
           return UsuarioCgm::where('cgmlogin', $cgm)->first();
    }
}
