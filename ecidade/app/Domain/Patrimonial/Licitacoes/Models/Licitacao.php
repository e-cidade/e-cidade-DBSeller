<?php

namespace App\Domain\Patrimonial\Licitacoes\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $l20_codigo
 * @property int $l20_codtipocom
 * @property int $l20_numero
 * @property int $l20_id_usucria
 * @property $l20_datacria
 * @property string $l20_horacria
 * @property $l20_dataaber
 * @property $l20_dtpublic
 * @property string $l20_horaaber
 * @property string $l20_local
 * @property string $l20_objeto
 * @property int $l20_tipojulg
 * @property int $l20_liccomissao
 * @property int $l20_liclocal
 * @property string $l20_procadmin
 * @property bool $l20_correto
 * @property int $l20_instit
 * @property int $l20_licsituacao
 * @property int $l20_edital
 * @property int $l20_anousu
 * @property bool $l20_usaregistropreco
 * @property string $l20_localentrega
 * @property string $l20_prazoentrega
 * @property string $l20_condicoespag
 * @property string $l20_validadeproposta
 * @property int $l20_formacontroleregistropreco
 * @property int $l20_tipo
 * @property Collection $editais
 * @property Item[] $itens
 * @property Modalidade $modalidade
 * @property LiclicitaCadAttDinamicoValorGrupo $orcamentoSigiloso
 */
class Licitacao extends Model
{
    protected $table = 'licitacao.liclicita';
    protected $primaryKey = 'l20_codigo';
    public $timestamps = false;

    public function modalidade()
    {
        return $this->hasOne(Modalidade::class, 'l03_codigo', 'l20_codtipocom');
    }

    public function itens()
    {
        return $this->hasMany(Item::class, 'l21_codliclicita', 'l20_codigo');
    }

    public function orcamentoSigiloso()
    {
        return $this->hasOne(LiclicitaCadAttDinamicoValorGrupo::class, 'l16_liclicita', 'l20_codigo');
    }

    public function getOrcamentoSigiloso()
    {
        return $this->orcamentoSigiloso->l16_cadattdinamicovalorgrupo;
    }

    public function editais()
    {
        return $this->hasMany(LicitacaoEdital::class, 'l27_liclicita', 'l20_codigo');
    }
}
