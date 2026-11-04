<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Mappers;

use App\Domain\Configuracao\GeradorRelatorio\Requests\RelatorioRequest;
use Illuminate\Support\Facades\DB;

class RelatorioJsonMapper
{
    private $json;

    public function __construct(RelatorioRequest $json)
    {
        $this->json = $json;
    }

    public function getCodigo()
    {
        return $this->json['codigo'];
    }

    public function getTipo()
    {
        return $this->json['tipo'];
    }

    public function getTipoSaida()
    {
        return $this->json['layout']['tipoSaida'];
    }

    public function getCampos()
    {
        return $this->json['campos'];
    }

    public function getLayout()
    {
        return $this->json['layout'];
    }

    public function getQueryFrom()
    {
        if ($this->json['origem'] == 2) {
            return $this->json['sql'];
        }

        $sql = $this->json['sql'];
        foreach ($this->json['variaveis'] as $variavel) {
            $valor = is_integer($variavel['valor']) ? $variavel['valor'] : "'{$variavel['valor']}'";
            $sql = str_replace($variavel['nome'], $valor, $sql);
        }

        return DB::raw("($sql) as gerador");
    }

    public function getQueryColumns($alias = false)
    {
        return array_map(function ($campo) use ($alias) {
            $campoRetorno = $campo['nome'];
            if ($alias) {
                $campoRetorno .= " as {$campo['alias']}";
            }

            return $campoRetorno;
        }, $this->json['campos']);
    }

    public function getQueryWhere()
    {
        return array_map(function ($filtro) {
            $valor = $filtro['valor'];
            $index = array_search($valor, array_column($this->json['variaveis'], 'nome'));
            if ($index !== false) {
                $valor = $this->json['variaveis'][$index]['valor'];
            }

            return [$filtro['campo'], $filtro['condicao'], $valor];
        }, $this->json['filtros']);
    }

    public function getQueryOrderBy()
    {
        return array_map(function ($ordem) {
            return "{$ordem['nome']} {$ordem['tipo']}";
        }, $this->json['ordem']);
    }
}
