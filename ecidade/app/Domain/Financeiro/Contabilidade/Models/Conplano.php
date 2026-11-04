<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use ECidade\Financeiro\Contabilidade\PlanoDeContas\Estrutural;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Conplano
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property $c60_codcon
 * @property $c60_anousu
 * @property $c60_estrut
 * @property $c60_descr
 * @property $c60_finali
 * @property $c60_codsis
 * @property $c60_codcla
 * @property $c60_consistemaconta
 * @property $c60_identificadorfinanceiro
 * @property $c60_naturezasaldo
 * @property $c60_funcao
 * @property $c60_saldocontinuo
 * @property $c60_codigo
 * @property $nome
 * @property $conta
 *
 * @method Conplano apenasSintetica()
 * @method Conplano apenasAnaliticas()
 * @method Conplano contaVinculada(integer $id)
 * @method Conplano outrasContas($idMicroarea = null)
 * @method Conplano contasCaixa()
 * @method Conplano excetoContasCaixa()
 * @method Conplano contasBancarias()
 * @method Conplano excetoContasBancarias()
 * @method Conplano contasExtrasOrcamentarias()
 * @method Conplano excetoContasExtrasOrcamentarias()
 * @method Conplano reduzidos()
 */
class Conplano extends Model
{
    protected $table = 'contabilidade.conplano';
    protected $primaryKey = 'c60_codigo';
    public $timestamps = false;

    private $storage = [];

    protected $fillable = [
        'c60_codcon',
        'c60_anousu',
        'c60_estrut',
        'c60_descr',
        'c60_finali',
        'c60_codsis',
        'c60_codcla',
        'c60_consistemaconta',
        'c60_identificadorfinanceiro',
        'c60_naturezasaldo',
        'c60_funcao',
        'c60_saldocontinuo',
    ];

    /**
     * @return string
     */
    public function getNomeAttribute()
    {
        return $this->c60_descr;
    }

    /**
     * @return string
     */
    public function getContaAttribute()
    {
        return $this->c60_estrut;
    }

    /**
     * Retorna os reduzidos da conta
     * @return ConplanoReduzido[]
     */
    public function getReduzidos()
    {
        if (!array_key_exists('reduzido', $this->storage)) {
            $this->storage['reduzido'] = ConplanoReduzido::where('c61_codcon', '=', $this->c60_codcon)
                ->where('c61_anousu', '=', $this->c60_anousu)
                ->with('instituicao')
                ->orderBy('c61_instit')
                ->get();
        }

        return $this->storage['reduzido'];
    }

    /**
     * Retorna a coleção dos vínculos da conta "conplano" com as contas do orçamento "conplanoorcamento"
     * @return ConplanoConplanoOrcamento[]
     */
    public function vinculoOrcamento()
    {
        if (!array_key_exists('vinculoOrcamento', $this->storage)) {
            $this->storage['vinculoOrcamento'] = ConplanoConplanoOrcamento::where('c72_conplano', $this->c60_codcon)
                ->where('c72_anousu', '=', $this->c60_anousu)
                ->get();
        }
        return $this->storage['vinculoOrcamento'];
    }

    /**
     * Retorna o próximo código da conta para inclusão
     * @return integer
     */
    public static function nextCodigoConta()
    {
        return DB::select("select nextval('conplano_c60_codcon_seq')")[0]->nextval;
    }

    public function toArray()
    {
        $data = parent::toArray();
        $estrutural = new Estrutural($this->c60_estrut);
        $data['mascara'] = $estrutural->getEstruturalComMascara();
        return $data;
    }

    /**
     * @param $validarReduzidos
     * @return bool
     * @throws Exception
     */
    public function validaExclusaoConta($validarReduzidos = true)
    {
        if ($this->vinculoOrcamento()->count()) {
            $msg = "Existem vínculo dessa conta com contas do plano orçamentário.\n";
            $msg .= "Para a exclusão dessa conta, altere o vínculo do plano orçamentário com outra conta do PCASP.";
            throw new Exception($msg);
        }

        if ($validarReduzidos) {
            $this->getReduzidos()->each(function (ConplanoReduzido $conplanoReduzido) {
                $conplanoReduzido->podeExcluirReduzido($conplanoReduzido->c61_reduz, $this->c60_anousu);
            });
        }

        return true;
    }

    /**
     * Retorna as contas não
     * @param Builder $query
     * @return Builder
     */
    public function scopeApenasSintetica(Builder $query)
    {
        return $query->whereRaw('
            not exists(
                select 1 from contabilidade.conplanoreduz where c61_codcon = c60_codcon and c61_anousu = c60_anousu
            )
        ');
    }

    /**
     * Valida se o número informado já esta cadastrado
     * @param Builder $query
     * @return Builder
     */
    public function scopeApenasAnaliticas(Builder $query)
    {
        return $query->whereRaw('
            exists(select 1 from contabilidade.conplanoreduz where c61_codcon = c60_codcon and c61_anousu = c60_anousu)
        ');
    }

    /**
     * Valida se a conta do e-cidade esta vinculada a conta do governo pelo id da conta no sistema
     * @param Builder $query
     * @param integer $id
     * @return Builder
     */
    public function scopeContaVinculada(Builder $query, $id)
    {
        return $query->whereRaw("exists(
            select 1 from contabilidade.pcaspconplano
             where conplano_codigo = c60_codigo
               and pcasp_id = {$id}
        )");
    }

    /**
     * Objetivo é filtrar as contas do plano PCASP que sejam:
     * - contas do Ativo (grupo 1) não enquadradas como caixa, bancos ou operações extra orçamentárias;
     * - contas do Passivo (grupo 2) não enquadradas como operações extra orçamentárias;
     * - contas dos grupos 3,4,5,6,7 e 8.
     * @param Builder $query
     */
    public function scopeOutrasContas(Builder $query)
    {
        $query->excetoContasCaixa();
        $query->excetoContasBancarias();
        $query->excetoContasExtrasOrcamentarias();
    }

    /**
     * Filtra a(s) contas caixa
     * @param Builder $query
     */
    public function scopeContasCaixa(Builder $query)
    {
        $query->where(function (Builder $query) {
            $query->orWhere('c60_estrut', 'like', "111110100%")
                ->orWhere('c60_estrut', 'like', "1112101%");
        });
    }

    /**
     * Filtra no plano PCASP as contas EXCLUINDO as contas caixa
     * @param Builder $query
     */
    public function scopeExcetoContasCaixa(Builder $query)
    {
        $query->where(function (Builder $query) {
            $query->where('c60_estrut', 'not like', "111110100%")
                ->where('c60_estrut', 'not like', "1112101%");
        });
    }

    /**
     * Filtra as contas bancárias do PCASP
     * @param Builder $query
     */
    public function scopeContasBancarias(Builder $query)
    {
        $query->where(function (Builder $query) {
            $query->orWhere('c60_estrut', 'like', "1111106%")
                ->orWhere('c60_estrut', 'like', "1111119%")
                ->orWhere('c60_estrut', 'like', "1111150%")
                ->orWhere('c60_estrut', 'like', "1111151%")
                ->orWhere('c60_estrut', 'like', "1111152%")
                ->orWhere('c60_estrut', 'like', "1111153%")
                ->orWhere('c60_estrut', 'like', "1111200%")
                ->orWhere('c60_estrut', 'like', "1112102%")
                ->orWhere('c60_estrut', 'like', "1112103%");
        });
    }

    public function scopeExcetoContasBancarias(Builder $query)
    {
        $query->where(function (Builder $query) {
            $query->where('c60_estrut', 'not like', "1111106%")
                ->where('c60_estrut', 'not like', "1111119%")
                ->where('c60_estrut', 'not like', "1111150%")
                ->where('c60_estrut', 'not like', "1111151%")
                ->where('c60_estrut', 'not like', "1111152%")
                ->where('c60_estrut', 'not like', "1111153%")
                ->where('c60_estrut', 'not like', "1111200%")
                ->where('c60_estrut', 'not like', "1112102%")
                ->where('c60_estrut', 'not like', "1112103%");
        });
    }

    /**
     * Filtra as contas extra-orçamentárias do PCASP do e-cidade
     * @param Builder $query
     */
    public function scopeContasExtrasOrcamentarias(Builder $query)
    {
        $query->where(function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->where('c60_identificadorfinanceiro', 'F');
            });
            $query->where(function (Builder $query) {
                $query->orWhere('c60_estrut', 'like', '1113%')
                    ->orWhere('c60_estrut', 'like', '1131%')
                    ->orWhere('c60_estrut', 'like', '1132%')
                    ->orWhere('c60_estrut', 'like', '1135%')
                    ->orWhere('c60_estrut', 'like', '113620101%')
                    ->orWhere('c60_estrut', 'like', '1136203%')
                    ->orWhere('c60_estrut', 'like', '1136301%')
                    ->orWhere('c60_estrut', 'like', '1136401%')
                    ->orWhere('c60_estrut', 'like', '1136503%')
                    ->orWhere('c60_estrut', 'like', '1138106%')
                    ->orWhere('c60_estrut', 'like', '1138108%')
                    ->orWhere('c60_estrut', 'like', '1138109%')
                    ->orWhere('c60_estrut', 'like', '1138110%')
                    ->orWhere('c60_estrut', 'like', '1138111%')
                    ->orWhere('c60_estrut', 'like', '1138117%')
                    ->orWhere('c60_estrut', 'like', '1138199%')
                    ->orWhere('c60_estrut', 'like', '11382%')
                    ->orWhere('c60_estrut', 'like', '11383%')
                    ->orWhere('c60_estrut', 'like', '11384%')
                    ->orWhere('c60_estrut', 'like', '11385%')
                    ->orWhere('c60_estrut', 'like', '12121%')
                    ->orWhere('c60_estrut', 'like', '2188%')
                    ->orWhere('c60_estrut', 'like', '2288%');
            });
        });
    }

    /**
     * Filtra no plano PCASP do e-cidade as contas EXCLUINDO as contas extra-orçamentárias
     * @param Builder $query
     */
    public function scopeExcetoContasExtrasOrcamentarias(Builder $query)
    {
        $query->whereRaw("
            c60_codigo not in (
              select extra.c60_codigo
                from contabilidade.conplano as extra
               where extra.c60_codigo = conplano.c60_codigo
                 and (    (extra.c60_identificadorfinanceiro = 'F')
                      and (    extra.c60_estrut like '1113%'
                            or extra.c60_estrut like '1131%'
                            or extra.c60_estrut like '1132%'
                            or extra.c60_estrut like '1135%'
                            or extra.c60_estrut like '113620101%'
                            or extra.c60_estrut like '1136203%'
                            or extra.c60_estrut like '1136301%'
                            or extra.c60_estrut like '1136401%'
                            or extra.c60_estrut like '1136503%'
                            or extra.c60_estrut like '1138106%'
                            or extra.c60_estrut like '1138108%'
                            or extra.c60_estrut like '1138109%'
                            or extra.c60_estrut like '1138110%'
                            or extra.c60_estrut like '1138111%'
                            or extra.c60_estrut like '1138117%'
                            or extra.c60_estrut like '1138199%'
                            or extra.c60_estrut like '11382%'
                            or extra.c60_estrut like '11383%'
                            or extra.c60_estrut like '11384%'
                            or extra.c60_estrut like '11385%'
                            or extra.c60_estrut like '12121%'
                            or extra.c60_estrut like '2188%'
                            or extra.c60_estrut like '2288%'
                          )
                     )
        )");
    }

    /**
     * Adiciona join na conplanoreduz
     * @param Builder $query
     * @return void
     */
    public function scopeReduzidos(Builder $query)
    {
        $query->join('contabilidade.conplanoreduz', function ($join) {
            $join->on('c61_codcon', 'c60_codcon')
                ->on('c61_anousu', 'c60_anousu');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pcaspUniao()
    {
        return $this->belongsToMany(
            Pcasp::class,
            'contabilidade.pcaspconplano',
            'conplano_codigo',
            'pcasp_id'
        )->where('uniao', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pcaspEstadual()
    {
        return $this->belongsToMany(
            Pcasp::class,
            'contabilidade.pcaspconplano',
            'conplano_codigo',
            'pcasp_id'
        )->where('uniao', false);
    }
}
