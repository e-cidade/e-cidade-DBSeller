<?php

namespace App\Domain\Tributario\Caixa\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int k00_codbco
 * @property string k00_codage
 * @property int|null k00_tipo
 * @property string k00_descr
 * @property bool k00_emrec
 * @property bool k00_agnum
 * @property bool k00_agpar
 * @property string k00_msguni
 * @property string k00_msguni2
 * @property string k00_msgparc
 * @property string k00_msgparc2
 * @property string k00_msgparcvenc
 * @property string k00_msgparcvenc2
 * @property string k00_msgrecibo
 * @property int k00_tercdigcarneunica
 * @property int k00_tercdigcarnenormal
 * @property int k00_tercdigrecunica
 * @property int k00_tercdigrecnormal
 * @property double k00_txban
 * @property int k00_rectx
 * @property int codmodelo
 * @property bool k00_impval
 * @property double k00_vlrmin
 * @property int|null k03_tipo
 * @property string k00_marcado
 * @property string k00_hist1
 * @property string k00_hist2
 * @property string k00_hist3
 * @property string k00_hist4
 * @property string k00_hist5
 * @property string k00_hist6
 * @property string k00_hist7
 * @property string k00_hist8
 * @property int k00_tipoagrup
 * @property int k00_recibodbpref
 * @property int|null k00_instit
 * @property int|null k00_formemissao
 * @property int k00_receitacredito
 * @property int|null k00_exercicioscarne
 * @property string k00_dtvencimento
 * @property string k00_horainicial
 * @property string k00_horafinal
 * @property bool k00_bloqnutil
 * @property bool|null k00_parcelainternet
 * @property bool k00_liberacarnesis
 * @property bool k00_liberacarnepref
 */
class Arretipo extends Model
{
    public $timestamps = false;

    protected $table = 'caixa.arretipo';
    protected $primaryKey = 'k00_tipo';

    public $fillable = [
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
        'k00_parcelainternet',
        'k00_liberacarnesis',
        'k00_liberacarnepref'
    ];
}
