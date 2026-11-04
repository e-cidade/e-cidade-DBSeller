<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Configuracao\Departamento\Models;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Material\Models\Deposito;
use App\Domain\Patrimonial\Patrimonio\Models\DivisaoDepartamento;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Departamento
 * @package App\Domain\Configuracao\Departamento\Models
 * @property integer $coddepto
 * @property string $descrdepto
 * @property string $nomeresponsavel
 * @property string $emailresponsavel
 * @property Carbon $limite
 * @property string $fonedepto
 * @property string $emaildepto
 * @property string $faxdepto
 * @property string $ramaldepto
 * @property integer $instit
 * @property integer $id_usuarioresp
 *
 * @property DBConfig $instituicao
 * @property Usuario $usuario
 * @property \Illuminate\Database\Eloquent\Collection $divisoes
 * @property Deposito $deposito
 */
class Departamento extends Model
{
    protected $table = 'configuracoes.db_depart';
    protected $primaryKey = 'coddepto';
    public $timestamps = false;
    public $incrementing = false;

    protected $dates = ['limite'];

    protected $casts = [
        "coddepto" => "integer",
        "descrdepto" => "string",
        "nomeresponsavel" => "string",
        "emailresponsavel" => "string",
        "fonedepto" => "string",
        "emaildepto" => "string",
        "faxdepto" => "string",
        "ramaldepto" => "string",
        "instit" => "integer",
        "id_usuarioresp" => "integer",
    ];

    /**
     * @var array
     */
    private $storage = [];

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->coddepto;
    }

    /**
     * @param int $coddepto
     * @return Departamento
     */
    public function setCodigo($coddepto)
    {
        $this->coddepto = $coddepto;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descrdepto;
    }

    /**
     * @param string $descrdepto
     * @return Departamento
     */
    public function setDescricao($descrdepto)
    {
        $this->descrdepto = $descrdepto;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomeResponsavel()
    {
        return $this->nomeresponsavel;
    }

    /**
     * @param string $nomeresponsavel
     * @return Departamento
     */
    public function setNomeResponsavel($nomeresponsavel)
    {
        $this->nomeresponsavel = $nomeresponsavel;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailResponsavel()
    {
        return $this->emailresponsavel;
    }

    /**
     * @param string $emailresponsavel
     * @return Departamento
     */
    public function setEmailResponsavel($emailresponsavel)
    {
        $this->emailresponsavel = $emailresponsavel;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getDataLimite()
    {
        return $this->limite;
    }

    /**
     * @param Carbon $limite
     * @return Departamento
     */
    public function setDataLimite($limite)
    {
        $this->limite = $limite;
        return $this;
    }

    /**
     * @return string
     */
    public function getTelefone()
    {
        return $this->fonedepto;
    }

    /**
     * @param string $fonedepto
     * @return Departamento
     */
    public function setTelefone($fonedepto)
    {
        $this->fonedepto = $fonedepto;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->emaildepto;
    }

    /**
     * @param string $emaildepto
     * @return Departamento
     */
    public function setEmail($emaildepto)
    {
        $this->emaildepto = $emaildepto;
        return $this;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->faxdepto;
    }

    /**
     * @param string $faxdepto
     * @return Departamento
     */
    public function setFax($faxdepto)
    {
        $this->faxdepto = $faxdepto;
        return $this;
    }

    /**
     * @return string
     */
    public function getRamal()
    {
        return $this->ramaldepto;
    }

    /**
     * @param string $ramaldepto
     * @return Departamento
     */
    public function setRamal($ramaldepto)
    {
        $this->ramaldepto = $ramaldepto;
        return $this;
    }

    /**
     * @return int
     */
    public function getInstituicao()
    {
        if (empty($this->storage['instituicao'])) {
            $this->storage['instituicao'] = $this->instituicao;
        }
        return $this->storage['instituicao'];
    }

    /**
     * @param int $instit
     * @return Departamento
     */
    public function setCodigoInstituicao($instit)
    {
        $this->instit = $instit;
        return $this;
    }

    /**
     * @return int
     */
    public function getUsuario()
    {
        if (empty($this->storage['usuario'])) {
            $this->storage['usuario'] = $this->usuario;
        }
        return $this->storage['usuario'];
    }

    /**
     * @param int $id_usuarioresp
     * @return Departamento
     */
    public function setIdUsuario($id_usuarioresp)
    {
        $this->id_usuarioresp = $id_usuarioresp;
        return $this;
    }

    /**
     * @return BelongsTo
     */
    public function instituicao()
    {
        return $this->belongsTo(DBConfig::class, 'instit', 'codigo');
    }

    /**
     * @return BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuarioresp', 'id_usuario');
    }

    /**
     * @return BelongsToMany
     */
    public function usuarios()
    {
        return  $this->belongsToMany(Usuario::class, "db_depusu", "coddepto", "id_usuario");
    }

    public function divisoes()
    {
        return $this->hasMany(DivisaoDepartamento::class, 't30_depto', 'coddepto');
    }

    public function deposito()
    {
        return $this->hasOne(Deposito::class, 'm91_depto', 'coddepto');
    }

    public function scopeLikeDescricao($query, $descricao = '')
    {
        return $query->where("descrdepto", "ilike", "%{$descricao}%");
    }

    public function scopeInCodigo($query, array $codigo = [])
    {
        return $query->whereIn("coddepto", $codigo);
    }

    public function scopeDataLimite($query, $data)
    {
        return $query->whereRaw("(limite  >'{$data}' or limite is null)");
    }
}
