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

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Orcamento\Models\CaracteristicaPeculiar;
use App\Domain\Financeiro\Orcamento\Models\Funcao;
use App\Domain\Financeiro\Orcamento\Models\NaturezaDespesa;
use App\Domain\Financeiro\Orcamento\Models\Orgao;
use App\Domain\Financeiro\Orcamento\Models\PpaSubtituloLocalizadorGasto;
use App\Domain\Financeiro\Orcamento\Models\Recurso;
use App\Domain\Financeiro\Orcamento\Models\Subfuncao;
use App\Domain\Financeiro\Orcamento\Models\Unidade;
use ECidade\Enum\Financeiro\Orcamento\EsferaOrcamentariaEnum;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class DetalhamentoDespesa
 * @package App\Domain\Financeiro\Planejamento\Models
 * @property $pl20_codigo
 * @property $pl20_anoorcamento
 * @property $pl20_iniciativaprojativ
 * @property $pl20_instituicao
 * @property $pl20_orcorgao
 * @property $pl20_orcunidade
 * @property $pl20_orcfuncao
 * @property $pl20_orcsubfuncao
 * @property $pl20_orcelemento
 * @property $pl20_recurso
 * @property $pl20_concarpeculiar
 * @property $pl20_subtitulo
 * @property $pl20_esferaorcamentaria
 * @property $pl20_valorbase
 * @property $created_at
 * @property $updated_at
 */
class DetalhamentoDespesa extends Model
{
    protected $table = 'planejamento.detalhamentoiniciativa';
    protected $primaryKey = 'pl20_codigo';

    protected $casts = [
        'pl20_concarpeculiar' => 'string'
    ];

    /**
     * @var Collection|Valor[]
     */
    protected $valores = [];

    /**
     * @var mixed
     */
    private $storage = [];
    /**
     * @var EsferaOrcamentariaEnum
     */
    private $esferaOrcamentaria;

    /**
     * Retorna os valores das iniciativas
     * @return Collection|Valor[]
     */
    public function getValores()
    {
        if (!array_key_exists('valores', $this->storage)) {
            $this->storage['valores'] = $this->valores = Valor::where('pl10_chave', '=', $this->pl20_codigo)
                ->where('pl10_origem', '=', Valor::ORIGEM_DETALHAMENTO_DESPESA)
                ->orderBy('pl10_ano')
                ->get();
        }

        return $this->storage['valores'];
    }

    /**
     * @param Collection $valores
     */
    public function setValores(Collection $valores)
    {
        $this->storage['valores'] = $valores;
    }

    /**
     * @return Orgao
     */
    public function getOrgao()
    {
        if (!array_key_exists('orgao', $this->storage)) {
            $this->storage['orgao'] = Orgao::where('o40_orgao', '=', $this->pl20_orcorgao)
                ->where('o40_anousu', '=', $this->pl20_anoorcamento)
                ->first();
        }

        return $this->storage['orgao'];
    }

    /**
     * @return Unidade
     */
    public function getUnidade()
    {
        if (!array_key_exists('unidade', $this->storage)) {
            $this->storage['unidade'] = Unidade::where('o41_anousu', '=', $this->pl20_anoorcamento)
                ->where('o41_orgao', '=', $this->pl20_orcorgao)
                ->where('o41_unidade', '=', $this->pl20_orcunidade)
                ->first();
        }

        return $this->storage['unidade'];
    }

    /**
     * @return EsferaOrcamentariaEnum
     * @throws Exception
     */
    public function getEsferaOrcamentaria()
    {
        $this->esferaOrcamentaria = new EsferaOrcamentariaEnum((int)$this->pl20_esferaorcamentaria);
        return $this->esferaOrcamentaria;
    }

    /**
     * @return NaturezaDespesa
     */
    public function getNaturezaDespesa()
    {
        if (!array_key_exists('naturezaDespesa', $this->storage)) {
            $this->storage['naturezaDespesa'] = NaturezaDespesa::where('o56_codele', '=', $this->pl20_orcelemento)
                ->where('o56_anousu', '=', $this->pl20_anoorcamento)
                ->first();
        }

        return $this->storage['naturezaDespesa'];
    }

    /**
     * @return string
     */
    public function getEstrutural()
    {
        $unidade = $this->getUnidade();
        $naturezaDespesa = $this->getNaturezaDespesa();
        $programa = $this->iniciativa->programaEstrategico->pl9_orcprograma;

        return implode('.', [
            str_pad($unidade->o41_orgao, 2, '0', STR_PAD_LEFT),
            str_pad($unidade->o41_unidade, 2, '0', STR_PAD_LEFT),
            str_pad($this->funcao->o52_funcao, 2, '0', STR_PAD_LEFT),
            str_pad($this->subfuncao->o53_subfuncao, 3, '0', STR_PAD_LEFT),
            str_pad($programa, 4, '0', STR_PAD_LEFT),
            str_pad($this->iniciativa->pl12_orcprojativ, 4, '0', STR_PAD_LEFT),
            $naturezaDespesa->o56_elemento,
            $this->recurso->fonteRecurso($this->pl20_anoorcamento)->codigo_siconfi,
            str_pad($this->recurso->o15_complemento, 4, '0', STR_PAD_LEFT)
        ]);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray()
    {
        $this->recurso->fonteRecurso = $this->recurso->fonteRecurso($this->pl20_anoorcamento);
        $dados = parent::toArray();
        $dados['orgao'] = $this->getOrgao();
        $dados['unidade'] = $this->getUnidade();
        $dados['natureza_despesa'] = $this->getNaturezaDespesa();
        $dados['estrutural'] = $this->getEstrutural();
        $dados['esferaOrcamentaria'] = $this->getEsferaOrcamentaria()->name();
        $dados['valores'] = $this->getValores();

        return $dados;
    }

    /**
     * @param $query
     * @param $dados
     * @return mixed
     */
    public function scopeExistsDetalhamento(Builder $query, $dados)
    {
        return $query->when(!empty($dados->pl20_codigo), function ($query) use ($dados) {
            $query->where('pl20_codigo', '!=', $dados->pl20_codigo);
        })
            ->where('pl20_anoorcamento', '=', $dados->pl20_anoorcamento)
            ->where('pl20_orcorgao', '=', $dados->pl20_orcorgao)
            ->where('pl20_orcunidade', '=', $dados->pl20_orcunidade)
            ->where('pl20_orcelemento', '=', $dados->pl20_orcelemento)
            ->where('pl20_esferaorcamentaria', '=', $dados->pl20_esferaorcamentaria)
            ->where('pl20_orcfuncao', '=', $dados->pl20_orcfuncao)
            ->where('pl20_orcsubfuncao', '=', $dados->pl20_orcsubfuncao)
            ->where('pl20_recurso', '=', $dados->pl20_recurso)
            ->where('pl20_concarpeculiar', '=', $dados->pl20_concarpeculiar)
            ->where('pl20_subtitulo', '=', $dados->pl20_subtitulo)
            ->where('pl20_iniciativaprojativ', '=', $dados->pl20_iniciativaprojativ)
            ->where('pl20_instituicao', '=', $dados->pl20_instituicao);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function funcao()
    {
        return $this->belongsTo(Funcao::class, 'pl20_orcfuncao', 'o52_funcao');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subfuncao()
    {
        return $this->belongsTo(Subfuncao::class, 'pl20_orcsubfuncao', 'o53_subfuncao');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recurso()
    {
        return $this->belongsTo(Recurso::class, 'pl20_recurso', 'o15_codigo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function caracteristicaPeculiar()
    {
        return $this->belongsTo(CaracteristicaPeculiar::class, 'pl20_concarpeculiar', 'c58_sequencial');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subtitulo()
    {
        return $this->belongsTo(PpaSubtituloLocalizadorGasto::class, 'pl20_subtitulo', 'o11_sequencial');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function iniciativa()
    {
        return $this->belongsTo(Iniciativa::class, 'pl20_iniciativaprojativ', 'pl12_codigo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function instituicao()
    {
        return $this->belongsTo(DBConfig::class, 'pl20_instituicao', 'codigo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cronogramaDesembolso()
    {
        return $this->hasMany(CronogramaDesembolsoDespesa::class, 'detalhamentoiniciativa_id', 'pl20_codigo');
    }
}
