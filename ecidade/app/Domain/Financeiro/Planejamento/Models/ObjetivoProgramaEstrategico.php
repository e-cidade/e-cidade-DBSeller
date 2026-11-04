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

namespace App\Domain\Financeiro\Planejamento\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class ObjetivoProgramaEstrategico
 * @package App\Domain\Financeiro\Planejamento\Models
 * @property $pl11_codigo
 * @property $pl11_programaestrategico
 * @property $pl11_ods
 * @property $pl11_numero
 * @property $pl11_descricao
 * @property $created_at
 * @property $updated_at
 */
class ObjetivoProgramaEstrategico extends Model
{
    protected $table = 'planejamento.objetivosprogramaestrategico';
    protected $primaryKey = 'pl11_codigo';

    private $storage = [];

    /**
     * Seta a collection de valores
     * @param Collection $valores
     */
    public function setValores(Collection $valores)
    {
        $this->storage['valores'] = $valores;
    }

    /**
     * Retorna os valores do programa estratégico
     * @return Collection
     */
    public function getValores()
    {
        if (!array_key_exists('valores', $this->storage)) {
            $this->storage['valores'] = Valor::where('pl10_chave', '=', $this->pl11_codigo)
                ->where('pl10_origem', '=', Valor::ORIGEM_OBJETIVOS)
                ->orderBy('pl10_ano')
                ->get();
        }

        return $this->storage['valores'];
    }

    /**
     * @return ProgramaEstrategico
     */
    public function getProgramaEstrategico()
    {
        if (!array_key_exists('programaEstrategico', $this->storage)) {
            $this->storage['programaEstrategico'] = $this->programaEstrategico;
        }
        return $this->storage['programaEstrategico'];
    }

    /**
     * @return Ods
     */
    public function getOds()
    {
        if (!array_key_exists('ods', $this->storage)) {
            $this->storage['ods'] = $this->ods;
        }
        return $this->storage['ods'];
    }

    public function toArray()
    {
        $this->ods;
        $objetivo = parent::toArray();
        $objetivo['valores'] = $this->getValores()->toArray();
        return $objetivo;
    }

    /**
     * Valida se o número informado já esta cadastrado
     * @param Builder $query
     * @param string $numero
     * @param integer $codigoPrograma
     * @param integer $codigo
     * @return mixed
     */
    public function scopeNumeroJaCadastrado(Builder $query, $numero, $codigoPrograma, $codigo)
    {
        return $query
            ->where('pl11_programaestrategico', '=', $codigoPrograma)
            ->where('pl11_numero', '=', $numero)
            ->when(!empty($codigo), function ($query) use ($codigo) {
                $query->where('pl11_codigo', '!=', $codigo);
            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programaEstrategico()
    {
        return $this->belongsTo(ProgramaEstrategico::class, 'pl11_programaestrategico', 'pl9_codigo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ods()
    {
        return $this->belongsTo(Ods::class, 'pl11_ods', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function metas()
    {
        return $this->hasMany(MetasObjetivo::class, 'pl21_objetivosprogramaestrategico', 'pl11_codigo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function iniciativas()
    {
        return $this->belongsToMany(
            Iniciativa::class,
            "planejamento.iniciativaobjetivosprogramaestrategico",
            "pl16_objetivosprogramaestrategico",
            "pl16_iniciativaprojativ"
        )->orderBy('pl12_orcprojativ');
    }
}
