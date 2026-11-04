<?php

namespace App\Domain\Educacao\CentralMatriculas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ListaEspera
 * @package App\Domain\Educacao\CentralMatriculas\Models
 * @property integer $mo18_sequencial
 * @property Base $mo18_base
 */
class ListaEspera extends Model
{
    protected $table = 'plugins.listaespera';
    protected $primaryKey = 'mo18_sequencial';
    public $incrementing = false;
    public $timestamps = true;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'mo18_created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'mo18_updated_at';

    /** Relacionamentos */

    public function base()
    {
        return $this->belongsTo(MoBase::class, 'mo18_base', 'mo01_codigo');
    }
}
