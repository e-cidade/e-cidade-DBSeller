<?php

namespace App\Domain\Tributario\Cadastro\Services;

use Illuminate\Support\Facades\DB;

class EnderecoService
{
    public static function getPaises($termo = null)
    {
        if (empty($termo)) {
            $termo = '';
        }

        $termo = '%'.$termo.'%';

        $sql = "
                SELECT
                    db70_sequencial AS codigo,
                    db70_descricao AS descricao,
                    db70_sigla AS sigla
                FROM
                    cadenderpais
                WHERE db70_descricao ILIKE ?
                ORDER BY
                    codigo
            ";

        return DB::select($sql, [$termo]);
    }

    public static function getEstados($termo = null)
    {
        if (empty($termo)) {
            $termo = '';
        }

        $termo = '%'.$termo.'%';

        $sql = "
                SELECT
                    cp03_sigla AS codigo,
                    cp03_estado AS descricao
                FROM
                    cepestados
                WHERE cp03_estado ILIKE ?
                ORDER BY
                    descricao
            ";

        return DB::select($sql, [$termo]);
    }

    public static function getCidades($uf = null)
    {
        if (!empty($uf)) {
            $sql = "
                SELECT
                  cp05_codlocalidades AS codigo,
                  cp05_localidades AS descricao,
                  cp03_sigla AS estado,
                  cp03_estado AS estado_descricao
                FROM
                  ceplocalidades
                INNER JOIN
                  cepestados ON cp03_sigla = cp05_sigla
                WHERE
                  cp05_sigla = UPPER(?)
                ORDER BY
                  descricao
            ";

            return DB::select($sql, [$uf]);
        }

        $sql = "
                SELECT
                  cp05_codlocalidades AS codigo,
                  cp05_localidades AS descricao,
                  cp03_sigla AS estado,
                  cp03_estado AS estado_descricao
                FROM
                  ceplocalidades
                INNER JOIN
                  cepestados ON cp03_sigla = cp05_sigla
                ORDER BY
                  descricao
            ";

        return DB::select($sql);
    }

    public static function getEnderecoLocalidadeCep($cep)
    {
        $sql = "
                    SELECT
            cp06_logradouro AS descricao_rua,
            cp06_codlocalidade AS codigo_rua,
            cp05_localidades AS descricao_cidade,
            cp05_codlocalidades AS codigo_cidade,
            cp03_sigla AS codigo_estado,
            cp03_estado AS descricao_estado,
            cp01_codbairro AS codigo_bairro,
            cp01_bairro AS descricao_bairro,
            1 AS codigo_pais,
            'BRASIL' AS descricao_pais
        FROM
            ceplocalidades
        LEFT JOIN
            cepestados ON cp05_sigla = cp03_sigla
        LEFT JOIN
            ceplogradouros ON cp06_codlocalidade = cp05_codlocalidades
        LEFT JOIN
            cepbairros ON cp01_sigla = cp03_sigla AND cp01_codbairro = cp06_codbairroinicial
        WHERE
            (CASE WHEN cp05_situacao = 'C' THEN cp06_cep ELSE cp05_cepinicial END) = ?
        ORDER BY
            cp05_sigla, cp05_localidades, cp06_logradouro;
        ";

        return DB::select($sql, [$cep]);
    }
}
