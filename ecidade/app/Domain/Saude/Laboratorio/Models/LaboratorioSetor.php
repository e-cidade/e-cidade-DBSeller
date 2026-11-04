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

namespace App\Domain\Saude\Laboratorio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $la24_i_codigo
 * @property integer $la24_i_resp
 * @property integer $la24_i_setor
 * @property integer $la24_i_laboratorio
 * @property integer $la24_o_assinatura
 * @property string $la24_c_nomearq
 * @property Exame $exames
 */
class LaboratorioSetor extends Model
{
    protected $table = 'lab_labsetor';

    protected $primaryKey = 'la24_i_codigo';

    public $timestamps = false;

    /**
     * @return BelongsToMany
     */
    public function exames()
    {
        return $this->belongsToMany(
            Exame::class,
            'lab_setorexame',
            'la09_i_labsetor',
            'la09_i_exame'
        );
    }
}
