<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Orcamento\Models\Recurso;
use App\Domain\Financeiro\Tesouraria\Models\Saltes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * @class ConplanoReduzido
 * @package App\Domain\Financeiro\Contabilidade\Models
 *
 * @property $c61_codcon
 * @property $c61_anousu
 * @property $c61_reduz
 * @property $c61_instit
 * @property $c61_codigo
 * @property $c61_contrapartida
 *
 * @method ConplanoReduzido reduzidoPossuiLancamento(integer $codigo, integer $exercicio)
 * @method ConplanoReduzido possuiSaldoInicial(integer $codigo, integer $exercicio)
 * @method ConplanoReduzido balanceteVerificacao(string $dataInicial, string $dataFinal, boolean $encerramento)
 */
class ConplanoReduzido extends Model
{
    protected $table = 'contabilidade.conplanoreduz';
    protected $primaryKey = 'c61_reduz';
    public $timestamps = false;

    private $storage = [];

    /**
     * @return DBConfig
     */
    public function instituicao()
    {
        return $this->belongsTo(DBConfig::class, 'c61_instit', 'codigo');
    }

    /**
     * @return Recurso|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recurso()
    {
        return $this->belongsTo(Recurso::class, 'c61_codigo', 'o15_codigo');
    }

    /**
     * @return integer
     */
    public static function nextReduzido()
    {
        return DB::select("select nextval('conplanoreduz_c61_reduz_seq')")[0]->nextval;
    }

    /**
     * @param Builder $query
     * @param $codigo
     * @param $exercicio
     * @return Builder
     */
    public function scopeReduzidoPossuiLancamento(Builder $query, $codigo, $exercicio)
    {
        return $query->whereRaw("
            c61_anousu >= {$exercicio}
            and c61_reduz = {$codigo}
            and c61_reduz in (
                select c69_credito from conlancamval where c69_anousu = c61_anousu and c69_credito = c61_reduz
                union all
                select c69_debito from conlancamval where c69_anousu = c61_anousu and c69_debito = c61_reduz
            )
          ");
    }

    public function scopePossuiSaldoInicial(Builder $query, $codigo, $exercicio)
    {
        return $query->join('conplanoexe', function ($join) {
            $join->on('conplanoexe.c62_anousu', '=', 'conplanoreduz.c61_anousu')
                ->on('conplanoexe.c62_reduz', '=', 'conplanoreduz.c61_reduz');
        })
            ->where('c61_anousu', '>=', $exercicio)
            ->where('c61_reduz', $codigo)
            ->whereRaw("(c62_vlrcre > 0 or c62_vlrdeb > 0)");
    }

    public function scopePossuiConciliacaoBancaria(Builder $query, $codigo, $exercicio)
    {
        return $query->join('conplanocontabancaria', function ($join) {
            $join->on('conplanocontabancaria.c56_anousu', '=', 'conplanoreduz.c61_anousu')
                ->on('conplanocontabancaria.c56_reduz', '=', 'conplanoreduz.c61_reduz');
        })->join('contabancaria', 'c56_contabancaria', '=', 'db83_sequencial')
            ->join('concilia', 'k68_contabancaria', '=', 'db83_sequencial')
            ->where('c61_anousu', '>=', $exercicio)
            ->where('c61_reduz', $codigo);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeBalanceteVerificacao(Builder $query, $dataInicial, $dataFinal, $encerramento)
    {
        $pl = sprintf(
            "fc_planosaldonovo_record(c61_anousu, c61_reduz, '%s'::date, '%s'::date, %s) AS saldos",
            $dataInicial,
            $dataFinal,
            $encerramento ? 'true' : 'false'
        );

        return $query->join(DB::raw($pl), 'saldos.reduzido', 'c61_reduz');
    }

    public function reduzidoEstaEmUso()
    {
        if (self::reduzidoPossuiLancamento($this->c61_reduz, $this->c61_anousu)->get()->count()) {
            return true;
        }

        if (self::possuiSaldoInicial($this->c61_reduz, $this->c61_anousu)->get()->count()) {
            return true;
        }

        if (self::possuiConciliacaoBancaria($this->c61_reduz, $this->c61_anousu)->get()->count()) {
            return true;
        }

        return false;
    }

    /**
     * Não permite a exclusão do reduzido se
     * @param $codigo
     * @param $exercicio
     * @return bool
     * @throws Exception
     */
    public function podeExcluirReduzido($codigo, $exercicio)
    {
        if (self::reduzidoPossuiLancamento($codigo, $exercicio)->get()->count()) {
            throw new Exception(sprintf(
                'O reduzido %s em %s não pode ser excluído pois já possui lançamento.',
                $codigo,
                $exercicio
            ));
        }

        if (self::possuiSaldoInicial($codigo, $exercicio)->get()->count()) {
            throw new Exception('Esta conta não pode ser excluída pois possui saldo inicial lançado.');
        }

        if (self::possuiConciliacaoBancaria($codigo, $exercicio)->get()->count()) {
            throw new Exception('Esta conta não pode ser excluída pois possui conciliação bancária.');
        }

        return true;
    }

    /**
     * Não permite a exclusão do reduzido se
     * @param $codigo
     * @param $exercicio
     * @return bool
     * @throws Exception
     */
    public function possueMovimentacao($codigo, $exercicio)
    {
        if (self::reduzidoPossuiLancamento($codigo, $exercicio)->get()->count()) {
            return true;
        }

        if (self::possuiSaldoInicial($codigo, $exercicio)->get()->count()) {
            return true;
        }

        if (self::possuiConciliacaoBancaria($codigo, $exercicio)->get()->count()) {
            return true;
        }

        return false;
    }

    public function getVinculosContaBancaria()
    {
        if (!array_key_exists('vinculos_conta_bancaria', $this->storage)) {
            $this->storage['vinculos_conta_bancaria'] = ConplanoContaBancaria::query()
                ->where('c56_codcon', $this->c61_codcon)
                ->where('c56_anousu', $this->c61_anousu)
                ->where('c56_reduz', $this->c61_reduz)
                ->with('contaBancaria')
                ->first();
        }
        return $this->storage['vinculos_conta_bancaria'];
    }

    public function getVinculoTesouraria()
    {
        if (!array_key_exists('saltes', $this->storage)) {
            $this->storage['saltes'] = Saltes::query()
                ->where('k13_reduz', $this->c61_reduz)
                ->with('empagetipo')
                ->first();
        }
        return $this->storage['saltes'];
    }

    /**
     * @return null|\stdClass
     */
    public function dadosBancario()
    {
        $campo = "e83_codtipo as codigo_pagadora, e83_convenio as convenio, e83_sequencia as cheque, ";
        $campo .= "'Bco: '|| db90_codban || ' Ag: ' || db89_codagencia || ' - ' || db89_digito || ' Cta: ' || ";
        $campo .= "db83_conta || ' - ' || db83_dvconta AS domicilio_bancario, ";
        $campo .= "db83_sequencial as id_contabancaria";

        $dado = DB::select("
            select $campo
              from conplano
              join conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
              join conplanocontabancaria on c56_codcon = c60_codcon
                   and c56_anousu = c60_anousu
                   and c56_reduz = c61_reduz
              join contabancaria on db83_sequencial = c56_contabancaria
              join bancoagencia on bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia
              join db_bancos on db_bancos.db90_codban = bancoagencia.db89_db_bancos
              join caixa.saltes on k13_reduz = c61_reduz
              join empenho.empagetipo on empagetipo.e83_conta = saltes.k13_conta
             where c60_codcon = {$this->c61_codcon}
               and c61_reduz = {$this->c61_reduz}
               and c60_anousu = {$this->c61_anousu};
        ");

        if (empty($dado)) {
            return null;
        }
        return $dado[0];
    }
}
