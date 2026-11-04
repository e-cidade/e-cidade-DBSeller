<?php

namespace App\Domain\Educacao\CentralMatriculas\Models;

use \App\Domain\Tributario\Cadastro\Models\Bairro;
use App\Domain\Educacao\Escola\Models\Escola as EscolaSede;
use App\Domain\Tributario\Cadastro\Models\Ruas;
use ECidade\Enum\Educacao\MatriculaOnline\TipoEscolaEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Escolas
 * @package App\Domain\Educacao\CentralMatriculas\Models
 * @property integer $mo53_codigo
 * @property string $mo53_nome
 * @property EscolaSede $mo53_escola
 * @property integer $mo53_bairro
 * @property integer $mo53_ruas
 * @property integer $mo53_numero
 * @property string $mo53_cep
 * @property string $mo53_complemento
 * @property string $mo53_telefone
 * @property TipoEscolaEnum $mo53_tipo
 * @property boolean $mo53_ativa
 * @property string $mo53_cnpj
 * @property string $mo53_email
 * @property string $mo53_diretor
 * @property string $mo53_acessibilidade
 * @property array $bairrosAtendidos
 */
class Escola extends Model
{
    protected $table = "plugins.escolas";
    protected $primaryKey = 'mo53_codigo';
    public $timestamps = false;
    public $incrementing = false;
    private $storage;
    protected $casts = [
        'mo53_escola' => \App\Domain\Educacao\Escola\Models\Escola::class,
    ];

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->mo53_codigo;
    }

    /**
     * @param int $mo53_codigo
     */
    public function setCodigo($mo53_codigo)
    {
        $this->mo53_codigo = $mo53_codigo;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->mo53_nome;
    }

    /**
     * @param string $mo53_nome
     */
    public function setNome($mo53_nome)
    {
        $this->mo53_nome = $mo53_nome;
    }

    /**
     * @return EscolaSede
     */
    public function getEscolaSede()
    {
        if (empty($this->storage['escola_sede'])) {
            $this->storage['escola_sede'] = $this->escolaSede;
        }
        return $this->storage['escola_sede'];
    }

    /**
     * @param EscolaSede $escola
     */
    public function setEscolaSede($escola)
    {
        $this->storage['escola_sede'] = $escola;
    }

    /**
     * @return int
     */
    public function getBairro()
    {
        if (empty($this->storage['bairro'])) {
            $this->storage['bairro'] = $this->bairro;
        }
        return $this->storage['bairro'];
    }

    /**
     * @param int $mo53_bairro
     */
    public function setBairro($mo53_bairro)
    {
        $this->mo53_bairro = $mo53_bairro;
    }

    /**
     * @return int
     */
    public function getRuas()
    {
        return $this->mo53_ruas;
    }

    /**
     * @param int $mo53_ruas
     */
    public function setRuas($mo53_ruas)
    {
        $this->mo53_ruas = $mo53_ruas;
    }

    /**
     * @return int
     */
    public function getNumero()
    {
        return $this->mo53_numero;
    }

    /**
     * @param int $mo53_numero
     */
    public function setNumero($mo53_numero)
    {
        $this->mo53_numero = $mo53_numero;
    }

    /**
     * @return string
     */
    public function getCep()
    {
        return $this->mo53_cep;
    }

    /**
     * @param string $mo53_cep
     */
    public function setCep($mo53_cep)
    {
        $this->mo53_cep = $mo53_cep;
    }

    /**
     * @return string
     */
    public function getComplemento()
    {
        return $this->mo53_complemento;
    }

    /**
     * @param string $mo53_complemento
     */
    public function setComplemento($mo53_complemento)
    {
        $this->mo53_complemento = $mo53_complemento;
    }

    /**
     * @return string
     */
    public function getTelefone()
    {
        return $this->mo53_telefone;
    }

    /**
     * @param string $mo53_telefone
     */
    public function setTelefone($mo53_telefone)
    {
        $this->mo53_telefone = $mo53_telefone;
    }

    /**
     * @return TipoEscolaEnum
     */
    public function getTipo()
    {
        return $this->mo53_tipo;
    }

    /**
     * @param TipoEscolaEnum $mo53_tipo
     */
    public function setTipo($mo53_tipo)
    {
        $this->mo53_tipo = $mo53_tipo;
    }

    /**
     * @return bool
     */
    public function isAtiva()
    {
        return $this->mo53_ativa;
    }

    /**
     * @param bool $mo53_ativa
     */
    public function setAtiva($mo53_ativa)
    {
        $this->mo53_ativa = $mo53_ativa;
    }

    /**
     * @return string
     */
    public function getCnpj()
    {
        return $this->mo53_cnpj;
    }

    /**
     * @param string $mo53_cnpj
     */
    public function setCnpj($mo53_cnpj)
    {
        $this->mo53_cnpj = $mo53_cnpj;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->mo53_email;
    }

    /**
     * @param string $mo53_email
     */
    public function setEmail($mo53_email)
    {
        $this->mo53_email = $mo53_email;
    }

    /**
     * @return string
     */
    public function getDiretor()
    {
        return $this->mo53_diretor;
    }

    /**
     * @param string $mo53_diretor
     */
    public function setDiretor($mo53_diretor)
    {
        $this->mo53_diretor = $mo53_diretor;
    }

    /** Relacionamentos */

    /**
     * @return BelongsTo
     */
    public function escolaSede()
    {
        return $this->belongsTo(EscolaSede::class, "mo53_escola", "ed18_i_codigo");
    }

    /**
     * @return BelongsTo
     */
    public function bairro()
    {
        return $this->belongsTo(Bairro::class, 'mo53_bairro', 'j13_codi');
    }

    public function rua()
    {
        return $this->belongsTo(Ruas::class, 'mo53_ruas', 'j14_codigo');
    }

    public function bairrosAtendidos()
    {
        return $this->hasMany(EscolaBairro::class, 'mo08_escola', 'mo53_codigo');
    }
}
