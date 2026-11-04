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

namespace App\Domain\Patrimonial\Contratos\Models;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Patrimonial\PNCP\Models\ContratoPNCP;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $ac16_sequencial
 * @property $ac16_coddepto
 * @property $ac16_numero
 * @property $ac16_anousu
 * @property $ac16_dataassinatura
 * @property $ac16_contratado
 * @property $ac16_datainicio
 * @property $ac16_datafim
 * @property $ac16_resumoobjeto
 * @property $ac16_objeto
 * @property $ac16_instit
 * @property $ac16_acordocomissao
 * @property $ac16_lei
 * @property $ac16_acordogrupo
 * @property $ac16_origem
 * @property $ac16_qtdrenovacao
 * @property $ac16_tipounidtempo
 * @property $ac16_deptoresponsavel
 * @property $ac16_numeroprocesso
 * @property $ac16_periodocomercial
 * @property $ac16_qtdperiodo
 * @property $ac16_tipounidtempoperiod
 * @property $ac16_acordocategoria
 * @property $ac16_acordoclassificacao
 * @property $ac16_numeroacordo
 * @property $ac16_valor
 * @property $ac16_tipoinstrumento
 * @property $ac16_acordosituacao
 * @property $ac16_dependeordeminicio
 */
class Acordo extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'ac16_sequencial';
    protected $table = 'acordos.acordo';
    protected $fillable = [];

    public function contratado()
    {
        return $this->hasOne(Cgm::class, 'z01_numcgm', 'ac16_contratado');
    }

    public function posicoes()
    {
        return $this->hasMany(AcordoPosicao::class, 'ac26_acordo', 'ac16_sequencial');
    }

    public function contratoPncp()
    {
        return $this->hasOne(ContratoPNCP::class, 'pn04_acordo', 'ac16_sequencial');
    }

    public function instituicao()
    {
        return $this->belongsTo(DBConfig::class, 'ac16_instit', 'codigo');
    }
}
