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

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use ECidade\Enum\Educacao\Escola\TipoAusenciaEnum;
use ECidade\Enum\Educacao\Escola\TipoAusenciaCategoriaEnum as CategoriaEnum;
use ECidade\Enum\Educacao\Escola\TipoAusenciaSubcategoriaEnum as SubcategoriaEnum;

/**
 * Class TipoAusencia
 * @package App\Domain\Educacao\Escola\Models
 * @property int
 */
class TipoAusencia extends Model
{

    protected $table = 'escola.tipoausencia';
    protected $primaryKey = 'ed320_sequencial';

    public $timestamps = false;
    public $incrementing = false;

    public $fillable = [
        'ed320_descricao',
        'ed320_categoria',
        'ed320_subcategoria',
    ];

    public $appends = [
        'categoria_nome',
        'subcategoria_nome',
    ];

// TODO: verificar se essas funcoes estao sendo usadas e substituir a logica
    public function getAbonoAttribute()
    {
        return in_array($this->tipo, [2,3]) ? 1 : 0;
    }

    public function getDescritivoAttribute()
    {
        $desc = $this->ed320_descricao . $this->label;

        return $desc;
    }

    public function getSubcategoriaNomeAttribute()
    {
        return (new SubcategoriaEnum($this->ed320_subcategoria))->name();
    }

    public function getCategoriaNomeAttribute()
    {
        return (new CategoriaEnum($this->ed320_categoria))->name();
    }

    public function getLabelAttribute()
    {
        return $this->ed320_subcategoria ? " ($this->subcategoria_nome)" : '';
    }

    public static function validate(Request $request)
    {
        $rules = [
            'ed320_descricao' => 'required|string|max:250',
            'ed320_categoria' => 'required|integer|in:' . implode(',', array_values(CategoriaEnum::values())),
            'ed320_subcategoria' => 'required|integer|in:' . implode(',', array_values(SubcategoriaEnum::values())),
        ];

        validaRequest(
            $request->all(),
            !isset($this)
                ? array_map(function ($rule) {
                    return str_replace('required', 'nullable', $rule);
                }, $rules)
                : $rules
        );
    }
}
