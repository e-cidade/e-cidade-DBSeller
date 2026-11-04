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

namespace App\Domain\Configuracao\Usuario\Models;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Carbon\Carbon;
use Encriptacao;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

/**
 * Class Usuario
 * @package App\Domain\Configuracao\Usuario\Models
 * @property integer $id_usuario
 * @property string $nome
 * @property string $login
 * @property string $senha
 * @property integer $usuarioativo
 * @property string $email
 * @property integer $usuext
 * @property integer $administrador
 * @property Carbon $datatoken
 * @property Carbon $dataexpira
 * @method isAtivo($ativo = true)
 * @method likeNome(string $nome)
 * @method likeEmail(string $email)
 * @method likeLogin(string $login)
 */
class Usuario extends User
{
    use Notifiable;
    use HasApiTokens;

    protected $table = 'configuracoes.db_usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;
    public $incrementing = false;


    protected $appends = [
        'id'
    ];

    protected $hidden = [
        "senha"
    ];

    public $casts = [
        "id_usuario" => "integer",
        "nome" => "string",
        "login" => "string",
        "senha" => "string",
        "usuarioativo" => "integer",
        "email" => "string",
        "usuext" => "integer",
        "administrador" => "integer",
        "datatoken" => "datetime",
        "dataexpira" => "datetime"
    ];

    /**
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->getAttribute('senha');
    }

    /**
     * @param string $login
     * @return Usuario|null
     */
    public function findForPassport($login)
    {
        return $this->where('login', $login)->first();
    }

    /**
     * @param string $password
     * @return boolean
     */
    public function validateForPassportPasswordGrant($password)
    {
        return app()->environment('local') ||
            Encriptacao::encriptaSenha($password) === $this->getAuthPassword();
    }

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->id_usuario;
    }

    /**
     * @param int $id_usuario
     * @return Usuario
     */
    public function setCodigo($id_usuario)
    {
        $this->id_usuario = $id_usuario;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return Usuario
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return Usuario
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * @param string $senha
     * @return Usuario
     */
    public function setSenha($senha)
    {
        $this->senha = $senha;
        return $this;
    }

    /**
     * @return int
     */
    public function isUsuarioAtivo()
    {
        return $this->usuarioativo == 1;
    }

    /**
     * @param int $usuarioativo
     * @return Usuario
     */
    public function setUsuarioAtivo($usuarioativo)
    {
        $this->usuarioativo = $usuarioativo;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Usuario
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return integer
     */
    public function getUsuarioExterno()
    {
        return $this->usuext;
    }

    /**
     * @param integer $usuext
     * @return Usuario
     */
    public function setUsuarioExterno($usuext)
    {
        $this->usuext = $usuext;
        return $this;
    }

    /**
     * @return integer
     */
    public function isAdministrador()
    {
        return !empty($this->administrador) && $this->administrador == 1;
    }

    /**
     * @param integer $administrador
     * @return Usuario
     */
    public function setAdministrador($administrador)
    {
        $this->administrador = $administrador;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getDataToken()
    {
        return $this->datatoken;
    }

    /**
     * @param Carbon $datatoken
     * @return Usuario
     */
    public function setDataToken($datatoken)
    {
        $this->datatoken = $datatoken;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getDataExpiracao()
    {
        return $this->dataexpira;
    }

    /**
     * @param Carbon $dataexpira
     * @return Usuario
     */
    public function setDataExpiracao($dataexpira)
    {
        $this->dataexpira = $dataexpira;
        return $this;
    }

    /**
     * Busca os orgaos que o usuário possui permissão no ano informado
     * @param $id
     * @param $ano
     * @return array
     */
    public static function getOrgaosLiberadoUsuario($id, $ano)
    {
        $permissoes = DB::select(
            "select distinct db20_orgao
              from db_usupermemp
              join db_permemp on db_permemp.db20_codperm = db_usupermemp.db21_codperm
             where db21_id_usuario = ?
               and db20_anousu = ?
               and db20_tipoperm = 'M'",
            [$id, $ano]
        );

        if (count($permissoes) === 0) {
            throw new Exception("Usuário sem permissão de despesa no ano de {$ano}.");
        }

        $orgaos = [];
        foreach ($permissoes as $permissaoOrgao) {
            $orgaos[] = $permissaoOrgao->db20_orgao;
        }

        return $orgaos;
    }

    /**
     * Retorna o usuário do e-cidade pelo CPF ou CNPJ
     * @param $sCpfCnpj
     * @return Usuario[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getUserEcidadeByCpfCnpj($sCpfCnpj)
    {
        $aUser = $this->join(
            "configuracoes.db_usuacgm",
            "db_usuacgm.id_usuario",
            "=",
            "db_usuarios.id_usuario"
        )->join(
            "cgm",
            "z01_numcgm",
            "=",
            "cgmlogin"
        )->where(
            "z01_cgccpf",
            "=",
            $sCpfCnpj
        )->where(
            "usuarioativo",
            "=",
            "1"
        )->where(
            "usuext",
            "!=",
            "2"
        )->get([
            "db_usuarios.id_usuario",
            "login",
            "usuext",
            "z01_nome",
            "z01_nomecomple",
            "z01_ender",
            "z01_numero",
            "z01_compl",
            "z01_bairro",
            "z01_munic",
            "z01_uf",
            "z01_cep",
            "z01_nasc"
        ]);

        return $aUser;
    }

    public function getIdAttribute()
    {
        return $this->getCodigo();
    }

    public function getNameAttribute()
    {
        return $this->getNome();
    }

    public function scopeIsAtivo($query, $ativo = true)
    {
        return $query->where('usuarioativo', $ativo);
    }

    public function scopeLikeNome($query, $nome = '')
    {
        return $query->where("nome", "ilike", "%{$nome}%");
    }

    public function scopeLikeEmail($query, $email = '')
    {
        return $query->where("email", "ilike", "%{$email}%");
    }


    public function scopeLikeLogin($query, $login = '')
    {
        return $query->where("login", "ilike", "%{$login}%");
    }


    public function usuarioCgm()
    {
        return $this->belongsTo(UsuarioCgm::class, 'id_usuario', 'id_usuario');
    }

     /**
     * Retorna as instituições do usuário
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function instituicoes()
    {
        return $this->belongsToMany(
            DBConfig::class,
            'configuracoes.db_userinst',
            'id_usuario',
            'id_instit'
        )->orderBy('id_instit');
    }
}
