<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\ParIssqn
 *
 * @property int $q60_receit
 * @property int $q60_tipo
 * @property int|null $q60_aliq
 * @property int $q60_codvencvar
 * @property int|null $q60_histsemmov
 * @property bool|null $q60_impcodativ
 * @property bool|null $q60_impobsativ
 * @property bool|null $q60_impdatas
 * @property bool|null $q60_impobsissqn
 * @property int|null $q60_modalvara
 * @property int|null $q60_integrasani
 * @property int|null $q60_campoutilcalc
 * @property int|null $q60_alvbaixadiv
 * @property bool|null $q60_notaavulsapesjur
 * @property int|null $q60_notaavulsavias
 * @property int|null $q60_notaavulsavlrmin
 * @property int|null $q60_notaavulsamax
 * @property int|null $q60_notaavulsaultimanota
 * @property int|null $q60_notaavulsadiasprazo
 * @property int|null $q60_tipopermalvara
 * @property int|null $q60_tiponumcertbaixa
 * @property int|null $q60_templatealvara
 * @property string|null $q60_dataimpmei
 * @property int|null $q60_bloqemiscertbaixa
 * @property int|null $q60_isstipoalvaraper
 * @property int|null $q60_isstipoalvaraprov
 * @property string|null $q60_parcelasalvara
 * @property int|null $q60_templatebaixaalvaranormal
 * @property int|null $q60_templatebaixaalvaraoficial
 * @property int|null $q60_parcelasissqn
 * @property int|null $q60_templatebicveiculo
 * @property int $q60_formaaliquotarbt
 * @property int|null $q60_portepadraomei
 * @property bool $q60_calcbaixorisco
 * @property int|null $q60_frequenciaatualizacaosimplesnacional
 * @property int|null $q60_diasemanalatualizacaosimplesnacional
 * @property int|null $q60_diamensalatualizacaosimplesnacional
 * @property string|null $q60_horaatualizacaosimplesnacional
 * @mixin \Eloquent
 */
class ParIssqn extends Model
{
    protected $table = "parissqn";
}
