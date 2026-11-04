<?php

namespace App\Domain\Financeiro\Contabilidade\Resources\Lrf;

use App\Domain\Financeiro\Contabilidade\Models\EmissoesLrf;

class EmissaoResource
{
    public static function toArray($dados)
    {
        $retorno = [];
        foreach ($dados as $dado) {
            /**
             * @var $dado EmissoesLrf
             */
            $retorno[] = (object)[
                "codigo" => $dado->c181_codigo,
                "relatorio_id" => $dado->c181_relatorio,
                "relatorio" => self::toRelatorio($dado),
                "periodo_id" => $dado->c181_periodo,
                "periodo" => self::toPeriodo($dado),
                "usuario_id" => $dado->c181_usuario,
                "usuario" => self::toUsuario($dado),
                "instituicao_id" => $dado->c181_instituicao,
                "instituicao" => self::toInsituicao($dado),
                "status" => $dado->c181_status,
                "publicado" => $dado->c181_publicado,
                "storage" => self::toDadosStorage($dado->c181_storage),
                "filtrosEmissao" => self::toFiltrosEmissao($dado->c181_filtrosemissao),
                "create_at" => $dado->created_at->format('Y-m-d'),
            ];
        }
        return $retorno;
    }

    private static function toRelatorio($dado)
    {
        $retorno = null;
        if ($dado->relationLoaded('relatorio')) {
            $retorno = (object)[
                'codigo' => $dado->relatorio->o42_codparrel,
                'descricao' => $dado->relatorio->o42_descrrel
            ];
        }
        return $retorno;
    }

    private static function toPeriodo(EmissoesLrf $dado)
    {
        $retorno = null;
        if ($dado->relationLoaded('periodo')) {
            $retorno = (object)[
                'codigo' => $dado->periodo->o114_sequencial,
                'descricao' => $dado->periodo->o114_descricao
            ];
        }
        return $retorno;
    }

    private static function toUsuario(EmissoesLrf $dado)
    {
        $retorno = null;
        if ($dado->relationLoaded('usuario')) {
            $retorno = (object)[
                'codigo' => $dado->usuario->id_usuario,
                'nome' => $dado->usuario->nome,
                'login' => $dado->usuario->login,
            ];
        }
        return $retorno;
    }

    private static function toInsituicao(EmissoesLrf $dado)
    {
         $retorno = null;
        if ($dado->relationLoaded('instituicao')) {
            $retorno = (object)[
                'codigo' => $dado->instituicao->codigo,
                'descricao' => $dado->instituicao->nomeinst
            ];
        }
        return $retorno;
    }

    private static function toDadosStorage($string)
    {
        if (!empty($string)) {
            return \JSON::create()->parse($string);
        }
        return null;
    }

    private static function toFiltrosEmissao($string)
    {
        if (!empty($string)) {
            return \JSON::create()->parse($string);
        }
        return null;
    }
}
