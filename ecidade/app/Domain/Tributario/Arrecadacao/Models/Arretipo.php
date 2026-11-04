<?php

namespace App\Domain\Tributario\Arrecadacao\Models;

use App\Domain\Tributario\Caixa\Models\Arrecad;
use App\Domain\Tributario\Caixa\Models\Cadtipo;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Model Arretipo
 *
 * @property int|null k00_codbco
 * @property string|null k00_codage
 * @property int k00_tipo
 * @property string|null k00_descr
 * @property bool|null k00_emrec
 * @property bool|null k00_agnum
 * @property bool|null k00_agpar
 * @property string|null k00_msguni
 * @property string|null k00_msguni2
 * @property string|null k00_msgparc
 * @property string|null k00_msgparc2
 * @property string|null k00_msgparcvenc
 * @property string|null k00_msgparcvenc2
 * @property string|null k00_msgrecibo
 * @property int|null k00_tercdigcarneunica
 * @property int|null k00_tercdigcarnenormal
 * @property int|null k00_tercdigrecunica
 * @property int|null k00_tercdigrecnormal
 * @property double|null k00_txban
 * @property int|null k00_rectx
 * @property int|null codmodelo
 * @property bool|null k00_impval
 * @property double|null k00_vlrmin
 * @property int k03_tipo
 * @property string|null k00_marcado
 * @property string|null k00_hist1
 * @property string|null k00_hist2
 * @property string|null k00_hist3
 * @property string|null k00_hist4
 * @property string|null k00_hist5
 * @property string|null k00_hist6
 * @property string|null k00_hist7
 * @property string|null k00_hist8
 * @property int|null k00_tipoagrup
 * @property int|null k00_recibodbpref
 * @property int k00_instit
 * @property int k00_formemissao
 * @property int|null k00_receitacredito
 * @property int k00_exercicioscarne
 * @property string|null k00_dtvencimento
 * @property string|null k00_horainicial
 * @property string|null k00_horafinal
 * @property bool|null k00_bloqnutil
 * @property int|null k00_taxaespecifica
 * @property bool|null k00_liberacarnesis
 * @property bool|null k00_liberacarnepref
 * @property bool|null k00_taxaadm
 */
class Arretipo extends Model
{
    protected $table = "caixa.arretipo";

    protected $fillable = [
        'k00_codbco',
        'k00_codage',
        'k00_tipo',
        'k00_descr',
        'k00_emrec',
        'k00_agnum',
        'k00_agpar',
        'k00_msguni',
        'k00_msguni2',
        'k00_msgparc',
        'k00_msgparc2',
        'k00_msgparcvenc',
        'k00_msgparcvenc2',
        'k00_msgrecibo',
        'k00_tercdigcarneunica',
        'k00_tercdigcarnenormal',
        'k00_tercdigrecunica',
        'k00_tercdigrecnormal',
        'k00_txban',
        'k00_rectx',
        'codmodelo',
        'k00_impval',
        'k00_vlrmin',
        'k03_tipo',
        'k00_marcado',
        'k00_hist1',
        'k00_hist2',
        'k00_hist3',
        'k00_hist4',
        'k00_hist5',
        'k00_hist6',
        'k00_hist7',
        'k00_hist8',
        'k00_tipoagrup',
        'k00_recibodbpref',
        'k00_instit',
        'k00_formemissao',
        'k00_receitacredito',
        'k00_exercicioscarne',
        'k00_dtvencimento',
        'k00_horainicial',
        'k00_horafinal',
        'k00_bloqnutil',
        'k00_taxaespecifica',
        'k00_liberacarnesis',
        'k00_liberacarnepref',
        'k00_taxaadm'
    ];

    public function arrecad()
    {
        return $this->hasMany(Arrecad::class, 'k00_tipo', 'k00_tipo');
    }

    public function cadtipo()
    {
        return $this->hasOne(Cadtipo::class, 'k03_tipo', 'k03_tipo');
    }
}
