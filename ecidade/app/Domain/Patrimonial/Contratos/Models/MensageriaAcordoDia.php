<?php

namespace App\Domain\Patrimonial\Contratos\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $mensageriaacordo_id
 * @property int $dias
 */

class MensageriaAcordoDia extends Model
{
    protected $table = "mensageria_acordo_dias";
    protected $fillable = [
        "mensageriaacordo_id",
        "dia"
    ];

    /**
     * Get the value of mensageriaacordo_id
     */
    public function getMensageriaAcordoId()
    {
        return $this->mensageriaacordo_id;
    }

    /**
     * Set the value of mensageriaacordo_id
     *
     * @return  self
     */
    public function setMensageriaAcordoId($mensageriaacordo_id)
    {
        $this->mensageriaacordo_id = $mensageriaacordo_id;

        return $this;
    }

    /**
     * Get the value of dia
     */
    public function getDia()
    {
        return $this->dia;
    }

    /**
     * Set the value of dia
     *
     * @return  self
     */
    public function setDia($dia)
    {
        $this->dia = $dia;

        return $this;
    }

    public function mensageriaAcordo()
    {
        return $this->belongsTo(MensageriaAcordo::class, "mensageriaacordo_id");
    }
}
