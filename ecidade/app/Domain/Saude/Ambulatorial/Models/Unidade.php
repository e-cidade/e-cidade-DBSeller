<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use App\Domain\Configuracao\Departamento\Models\Departamento;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $sd02_i_codigo
 * @property integer $sd02_i_distrito
 * @property integer $sd02_i_regiao
 * @property integer $sd02_i_cidade
 * @property string $sd02_c_siasus
 * @property integer $sd02_i_numcgm
 * @property integer $sd02_i_situacao
 * @property string $sd02_v_cnes
 * @property string $sd02_v_microreg
 * @property string $sd02_v_distsant
 * @property string $sd02_v_distadmin
 * @property integer $sd02_i_cod_esfadm
 * @property integer $sd02_i_cod_ativ
 * @property integer $sd02_i_reten_trib
 * @property integer $sd02_i_cod_natorg
 * @property integer $sd02_i_cod_client
 * @property string $sd02_v_num_alvara
 * @property \DateTime $sd02_d_data_exped
 * @property string $sd02_v_ind_orgexp
 * @property integer $sd02_i_tp_unid_id
 * @property integer $sd02_i_cod_turnat
 * @property integer $sd02_i_codnivhier
 * @property integer $sd02_i_diretor
 * @property string $sd02_c_centralagenda
 * @property string $sd02_v_diretorreg
 * @property string $sd02_cnpjcpf
 *
 * @property Departamento $departamento
 */
class Unidade extends Model
{
    protected $table = 'ambulatorial.unidades';
    protected $primaryKey = 'sd02_i_codigo';
    public $timestamps = false;

    public $casts = [
        'sd02_d_data_exped' => 'datetime'
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'sd02_i_codigo', 'coddepto');
    }
}
