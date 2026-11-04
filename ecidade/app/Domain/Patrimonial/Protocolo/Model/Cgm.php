<?php

namespace App\Domain\Patrimonial\Protocolo\Model;

use App\Domain\Configuracao\Usuario\Models\UsuarioCgm;
use App\Domain\Financeiro\Planejamento\Models\Comissao;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cgm
 *
 * @package App\Domain\Patrimonial\Patrimonio
 * @property int $z01_numcgm
 * @property string|null $z01_nome
 * @property string|null $z01_ender
 * @property int|null $z01_numero
 * @property string|null $z01_compl
 * @property string|null $z01_bairro
 * @property string|null $z01_munic
 * @property string|null $z01_uf
 * @property string|null $z01_cep
 * @property string|null $z01_cxpostal
 * @property string|null $z01_cadast
 * @property string|null $z01_telef
 * @property string|null $z01_ident
 * @property int|null $z01_login
 * @property string|null $z01_incest
 * @property string|null $z01_telcel
 * @property string|null $z01_email
 * @property string|null $z01_endcon
 * @property int|null $z01_numcon
 * @property string|null $z01_comcon
 * @property string|null $z01_baicon
 * @property string|null $z01_muncon
 * @property string|null $z01_ufcon
 * @property string|null $z01_cepcon
 * @property string|null $z01_cxposcon
 * @property string|null $z01_telcon
 * @property string|null $z01_celcon
 * @property string|null $z01_emailc
 * @property int|null $z01_nacion
 * @property int|null $z01_estciv
 * @property string|null $z01_profis
 * @property int|null $z01_tipcre
 * @property string|null $z01_cgccpf
 * @property string|null $z01_fax
 * @property string|null $z01_nasc
 * @property string|null $z01_pai
 * @property string|null $z01_mae
 * @property string|null $z01_sexo
 * @property string|null $z01_ultalt
 * @property string|null $z01_contato
 * @property string|null $z01_hora
 * @property string|null $z01_nomefanta
 * @property string|null $z01_cnh
 * @property string|null $z01_categoria
 * @property string|null $z01_dtemissao
 * @property string|null $z01_dthabilitacao
 * @property string|null $z01_nomecomple
 * @property string|null $z01_dtvencimento
 * @property string|null $z01_dtfalecimento
 * @property string|null $z01_escolaridade
 * @property string|null $z01_naturalidade
 * @property string|null $z01_identdtexp
 * @property string|null $z01_identorgao
 * @property bool|null $z01_trabalha
 * @property float|null $z01_renda
 * @property string|null $z01_localtrabalho
 * @property string|null $z01_pis
 * @property string|null $z01_obs
 * @property string|null $z01_genero
 * @property-read CgmEstrangeiro $cgmEstrangeiro
 * @property-read CgmEndereco $enderecoPrimario
 * @property-read Collection|CgmEndereco[] $enderecos
 * @property-read mixed $cpf_cgc_mask
 * @property-read mixed $nascimento_mask
 * @mixin \Eloquent
 * @method likeNome(string $nome)
 * @method inNumeroCgm(array $array)
 * @method likeCpfCnpj(string $cpfcnpj)
 * @method static cpfCnpj($cpf_cnpj)
 */
class Cgm extends Model
{
    public $timestamps = false;
    protected $table = 'protocolo.cgm';
    protected $primaryKey = 'z01_numcgm';
    protected $appends = ["nascimentoMask", "cpfCgcMask", 'tipo_pessoa'];
    protected $fillable = [
        "z01_numcgm",
        "z01_nome",
        "z01_ender",
        "z01_numero",
        "z01_compl",
        "z01_bairro",
        "z01_munic",
        "z01_uf",
        "z01_cep",
        "z01_cxpostal",
        "z01_cadast",
        "z01_telef",
        "z01_ident",
        "z01_login",
        "z01_incest",
        "z01_telcel",
        "z01_email",
        "z01_endcon",
        "z01_numcon",
        "z01_comcon",
        "z01_baicon",
        "z01_muncon",
        "z01_ufcon",
        "z01_cepcon",
        "z01_cxposcon",
        "z01_telcon",
        "z01_celcon",
        "z01_emailc",
        "z01_nacion",
        "z01_estciv",
        "z01_profis",
        "z01_tipcre",
        "z01_cgccpf",
        "z01_fax",
        "z01_nasc",
        "z01_pai",
        "z01_mae",
        "z01_sexo",
        "z01_ultalt",
        "z01_contato",
        "z01_hora",
        "z01_nomefanta",
        "z01_cnh",
        "z01_categoria",
        "z01_dtemissao",
        "z01_dthabilitacao",
        "z01_nomecomple",
        "z01_dtvencimento",
        "z01_dtfalecimento",
        "z01_escolaridade",
        "z01_naturalidade",
        "z01_identdtexp",
        "z01_identorgao",
        "z01_trabalha",
        "z01_renda",
        "z01_localtrabalho",
        "z01_pis",
        "z01_obs",
    ];

    public function comissoes()
    {
        $this->hasMany(Comissao::class, 'pl3_cgm', 'z01_numcgm');
    }

    public function getNascimentoMaskAttribute()
    {
        if (empty($this->attributes["z01_nasc"])) {
            return null;
        }
        $nascimento = new \DateTime($this->attributes["z01_nasc"]);
        return $nascimento->format('d/m/Y');
    }

    public function getCpfCgcMaskAttribute()
    {
        return db_cgccpf($this->attributes["z01_cgccpf"]);
    }

    public function getTipoPessoaAttribute()
    {
        if ($this->z01_nacion === 2) {
            return 'E';
        }

        if (strlen(trim($this->z01_cgccpf)) === 14) {
            return 'J';
        } else {
            return 'F';
        }
    }

    public function enderecos()
    {
        return $this->hasMany(CgmEndereco::class, "z07_numcgm");
    }

    public function enderecoPrimario()
    {
        return $this->hasOne(CgmEndereco::class, "z07_numcgm")->where("z07_tipo", "=", "P");
    }

    public function cgmEstrangeiro()
    {
        return $this->hasOne(CgmEstrangeiro::class, 'z09_numcgm');
    }

    public function cgmFisico()
    {
        return $this->hasOne(CgmFisico::class, 'z04_numcgm');
    }

    public function scopeLikeNome($query, $nome = '')
    {
        return $query->where("z01_nome", "ilike", "%{$nome}%");
    }

    public function scopeInNumeroCgm($query, array $numero = [])
    {
        return $query->whereIn("z01_numcgm", $numero);
    }

    public function scopeLikeCpfCnpj($query, $cpfcnpj)
    {
        return $query->where("z01_cgccpf", "ilike", "%{$cpfcnpj}%");
    }

    public function scopeCpfCnpj($query, $cpfCnpj)
    {
        return $query->where('z01_cgccpf', $cpfCnpj);
    }

    public function usuarios()
    {
        return $this->hasMany(UsuarioCgm::class, 'cgmlogin', 'z01_numcgm');
    }

    /**
     * @param int $z01_numcgm
     */
    public function setId($z01_numcgm)
    {
        $this->z01_numcgm = $z01_numcgm;
    }

    /**
     * @param string $z01_nome
     */
    public function setNome($z01_nome)
    {
        $this->z01_nome = $z01_nome;
    }

    /**
     * @param string $z01_nomecomple
     */
    public function setNomeCompleto($z01_nomecomple)
    {
        $this->z01_nomecomple = $z01_nomecomple;
    }

    /**
     * @param string $z01_ender
     */
    public function setLogradouro($z01_ender)
    {
        $this->z01_ender = $z01_ender;
    }

    /**
     * @param int $z01_numero
     */
    public function setNumero($z01_numero)
    {
        $this->z01_numero = $z01_numero;
    }

    /**
     * @param int $z01_login
     */
    public function setLogin($z01_login)
    {
        $this->z01_login = $z01_login;
    }

    /**
     * @param string $z01_compl
     */
    public function setComplemento($z01_compl)
    {
        $this->z01_compl = $z01_compl;
    }

    /**
     * @param string $z01_bairro
     */
    public function setBairro($z01_bairro)
    {
        $this->z01_bairro = $z01_bairro;
    }

    /**
     * @param string $z01_munic
     */
    public function setMunicipio($z01_munic)
    {
        $this->z01_munic = $z01_munic;
    }

    /**
     * @param string $z01_uf
     */
    public function setUf($z01_uf)
    {
        $this->z01_uf = $z01_uf;
    }

    /**
     * @param string $z01_cep
     */
    public function setCep($z01_cep)
    {
        $this->z01_cep = \DBString::somenteNumeros($z01_cep);
    }

    /**
     * @param string $z01_telef
     */
    public function setTelefone($z01_telef)
    {
        $this->z01_telef = $z01_telef;
    }

    /**
     * @param string $z01_telcel
     */
    public function setCelular($z01_telcel)
    {
        $this->z01_telcel = $z01_telcel;
    }

    /**
     * @param string $z01_email
     */
    public function setEmail($z01_email)
    {
        $this->z01_email = $z01_email;
    }

    /**
     * @param string $z01_cgccpf
     */
    public function setCgcCpf($z01_cgccpf)
    {
        $this->z01_cgccpf = $z01_cgccpf;
    }

    /**
     * @param date $z01_nasc
     */
    public function setNascimento($z01_nasc)
    {
        $this->z01_nasc = $z01_nasc;
    }

    /**
     * @param string $z01_pai
     */
    public function setPai($z01_pai)
    {
        $this->z01_pai = $z01_pai;
    }

    /**
     * @param string $z01_mae
     */
    public function setMae($z01_mae)
    {
        $this->z01_mae = $z01_mae;
    }

    /**
     * @return string|null
     */
    public function getMae()
    {
        return $this->z01_mae;
    }

    /**
     * @param string $z01_sexo
     */
    public function setSexo($z01_sexo)
    {
        $this->z01_sexo = $z01_sexo;
    }

    /**
     * @param string $z01_hora
     */
    public function setHora($z01_hora)
    {
        $this->z01_hora = $z01_hora;
    }
}
