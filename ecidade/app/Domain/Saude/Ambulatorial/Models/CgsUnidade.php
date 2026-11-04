<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Domain\Saude\Ambulatorial\Models
 * @property integer $z01_i_cgsund
 * @property string $z01_v_cgccpf
 * @property string $z01_v_nome
 * @property string $z01_v_ender
 * @property integer $z01_i_numero
 * @property string $z01_v_compl
 * @property string $z01_v_bairro
 * @property string $z01_v_munic
 * @property string $z01_v_uf
 * @property string $z01_v_cep
 * @property string $z01_d_cadast
 * @property string $z01_v_telef
 * @property string $z01_v_ident
 * @property integer $z01_i_login
 * @property string $z01_v_telcel
 * @property string $z01_v_email
 * @property string $z01_d_nasc
 * @property string $z01_v_sexo
 * @property string $z01_v_mae
 * @property string $z01_v_hora
 * @property string $z01_v_fax
 * @property string $z01_v_endcon
 * @property string $z01_v_emailc
 * @property string $z01_v_cxposcon
 * @property string $z01_v_contato
 * @property string $z01_v_comcon
 * @property string $z01_v_cnh
 * @property string $z01_v_cepcon
 * @property string $z01_v_celcon
 * @property string $z01_v_categoria
 * @property integer $z01_i_nacion
 * @property integer $z01_i_estciv
 * @property string $z01_d_ultalt
 * @property string $z01_d_dtvencimento
 * @property string $z01_d_dthabilitacao
 * @property string $z01_d_dtemissao
 * @property string $z01_c_passivo
 * @property string $z01_c_bolsafamilia
 * @property string $z01_c_nis
 * @property string $z01_c_certidaodata
 * @property string $z01_c_certidaocart
 * @property string $z01_c_certidaofolha
 * @property string $z01_c_certidaolivro
 * @property string $z01_c_certidaonum
 * @property string $z01_c_certidaotipo
 * @property string $z01_c_zona
 * @property string $z01_c_transporte
 * @property string $z01_t_obs
 * @property string $z01_c_atendesp
 * @property string $z01_c_emailresp
 * @property string $z01_c_nomeresp
 * @property string $z01_c_naturalidade
 * @property string $z01_v_cxpostal
 * @property string $z01_c_raca
 * @property string $z01_v_baicon
 * @property string $z01_i_familiamicroarea
 * @property string $z01_c_certidaotermo
 * @property string $z01_c_pis
 * @property string $z01_c_escolaridade
 * @property string $z01_c_banco
 * @property string $z01_c_agencia
 * @property string $z01_c_conta
 * @property string $z01_c_ufident
 * @property string $z01_c_numctps
 * @property string $z01_c_seriectps
 * @property string $z01_c_ufctps
 * @property string $z01_d_datapais
 * @property string $z01_d_dtemissaoctps
 * @property string $z01_v_familia
 * @property string $z01_v_microarea
 * @property string $z01_o_oid
 * @property string $z01_c_foto
 * @property string $z01_v_ufcon
 * @property string $z01_v_telcon
 * @property string $z01_v_profis
 * @property string $z01_v_pai
 * @property string $z01_v_muncon
 * @property integer $z01_i_numcon
 * @property string $z01_d_dtemissaocnh
 * @property string $z01_codigoibge
 * @property string $z01_orgaoemissoridentidade
 * @property boolean $z01_registromunicipio
 * @property string $z01_nome_social
 * @property integer $z01_distritosanitario
 *
 * @property Cgs $cgs
 * @property CgsUnidadeExtensao $cgsExtensao
 * @property FamiliaMicroarea $familiaMicroarea
 *
 * @method static CgsUnidade cpf(integer $cpf)
 * @method static CgsUnidade cns(integer $cns)
 * @method static CgsUnidade nome(string $nome)
 * @method static CgsUnidade nomeMae(string $nome)
 * @method static CgsUnidade dataNascimento(string $data)
 */
class CgsUnidade extends Model
{
    protected $table = 'ambulatorial.cgs_und';
    protected $primaryKey = 'z01_i_cgsund';
    public $timestamps = false;

    public function cgs()
    {
        return $this->belongsTo(Cgs::class, 'z01_i_cgsund', 'z01_i_numcgs');
    }

    public function cgsExtensao()
    {
        return $this->hasOne(CgsUnidadeExtensao::class, 'z01_i_cgsund', 'z01_i_cgsund');
    }

    public function familiaMicroarea()
    {
        return $this->hasOne(FamiliaMicroarea::class, 'sd35_i_codigo', 'z01_i_familiamicroarea');
    }

    public function scopeCpf(Builder $query, $cpf)
    {
        return $query->where('z01_v_cgccpf', $cpf);
    }

    public function scopeCns(Builder $query, $cns)
    {
        return $query->whereExists(function ($query) use ($cns) {
            $query->select(\DB::raw(1))
                ->from('ambulatorial.cgs_cartaosus')
                ->whereColumn('cgs_cartaosus.s115_i_cgs', 'cgs_und.z01_i_cgsund')
                ->where('cgs_cartaosus.s115_c_cartaosus', $cns);
        });
    }

    public function scopeNome(Builder $query, $nome)
    {
        return $query->where('z01_v_nome', 'ILIKE', trim($nome)."%");
    }

    public function scopeNomeMae(Builder $query, $nome)
    {
        return $query->where('z01_v_mae', 'ILIKE', trim($nome)."%");
    }

    public function scopeDataNascimento(Builder $query, $data)
    {
        return $query->where('z01_d_nasc', $data);
    }

    public function scopeCgs(Builder $query, $cgs)
    {
        return $query->where('z01_i_cgsund', $cgs);
    }
}
