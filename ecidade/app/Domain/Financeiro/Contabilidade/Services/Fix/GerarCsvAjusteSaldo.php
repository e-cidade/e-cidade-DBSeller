<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Fix;

use ECidade\File\Csv\Dumper\Dumper;
use Illuminate\Support\Facades\DB;

class GerarCsvAjusteSaldo
{
    /**
     * @var array
     */
    protected $filtros;

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
    }

    public function gerar()
    {
        $dados = $this->executarSql();

        $file = 'tmp/ajuste_saldo_' . time() . '.csv';

        $dumper = new Dumper();
        $dumper->setCsvControl(",", '"');
        return $dumper->dumpToFile($this->formataDados($dados), $file);
    }

    private function executarSql()
    {
        $dataInicial = $this->filtros['dataInicial'];
        $dataFinal = $this->filtros['dataFinal'];
        $anousu = explode('-', $this->filtros['dataFinal'])[0];
        $estrutural = $this->filtros['estrutural'];
        $contrapartida = $this->filtros['contrapartida'];

        $recurso = "select c61_codigo from conplanoreduz where c61_reduz = {$contrapartida} and c61_anousu = {$anousu}";


        $sql = "
        with dados_balancete as (
          SELECT reduzido,
                 id_recurso,
                 saldo_final,
                 estrutural,
                 principal,
                 siconfi,
                 gestao,
                 subrecurso,
                 complemento
          FROM contabilidade.balancete_verificacao_por_recurso(
                  {$anousu},
                  '{$dataInicial}',
                  '{$dataFinal}',
                  false,
                  (SELECT array_agg(c61_reduz)
                     FROM (
                       SELECT c61_reduz
                         FROM contabilidade.conplano
                      JOIN contabilidade.conplanoreduz ON (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
                      WHERE c61_instit IN (1)
                        AND c61_anousu = $anousu
                        AND (c60_estrut LIKE '{$estrutural}%')) AS x)::int[]
               ) x
          where saldo_final != 0
          ORDER BY estrutural, principal DESC
        ), multiplos_recursos as (
          SELECT reduzido, count(*)
            from dados_balancete
           group by 1
           having count(*) > 1
        ), prepara_planilha as (
            select
                   case when bl.saldo_final > 0 then bl.reduzido else $contrapartida end as credito,
                   case when bl.saldo_final < 0 then bl.reduzido else $contrapartida end as debito,
                   1 as historico,
                   'Ajuste de encerramento de FR' as observacao,
                   case when bl.saldo_final > 0 then bl.id_recurso else ({$recurso}) end as recurso_credito,
                   case when bl.saldo_final < 0 then bl.id_recurso else ({$recurso}) end as recurso_debito,
                   case when bl.saldo_final < 0 then bl.saldo_final*-1 else bl.saldo_final end as valor,
                   estrutural,
                   principal,
                   siconfi,
                   gestao,
                   subrecurso,
                   complemento
            from dados_balancete bl
            join multiplos_recursos mt on mt.reduzido = bl.reduzido
        ) select * from prepara_planilha
        ";

        return DB::select($sql);
    }

    private function cabecalho()
    {
        return [
            'C Credito', 'C Debito', 'Historico', 'Observacao', 'Recurso Crédito', 'Recurso Débito', 'Valor',
            'Função', 'Subfunção', 'Elemento', 'Ai', 'NR'
        ];
    }

    private function formataDados(array $dados)
    {
        $retorno = [
            $this->cabecalho()
        ];

        foreach ($dados as $dado) {
            $retorno[] = [
                $dado->credito,
                $dado->debito,
                $this->filtros['historico'],
                $dado->observacao,
                $dado->recurso_credito,
                $dado->recurso_debito,
                number_format($dado->valor, 2, ',', '.')
            ];
        }

        return $retorno;
    }
}
