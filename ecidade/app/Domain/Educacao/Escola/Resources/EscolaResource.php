<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\Escola;

class EscolaResource
{
    public static function toResponse(Escola $escola)
    {
        return (object) [
            'codigo' => $escola->ed18_i_codigo,
            'rua' => trim($escola->ed18_i_rua),
            'numero' => $escola->ed18_i_numero,
            'complemento' => trim($escola->ed18_c_compl),
            'bairro' => $escola->ed18_i_bairro,
            'nome' => trim($escola->ed18_c_nome),
            'abreviatura' => trim($escola->ed18_c_abrev),
            'mantenedora' => $escola->ed18_c_mantenedora,
            'anoIncicio' => $escola->ed18_i_anoinicio,
            'email' => $escola->ed18_c_email,
            'homePage' => trim($escola->ed18_c_homepage),
            'tipo' => $escola->ed18_c_tipo,
            'codigoInep' => $escola->ed18_c_codigoinep,
            'local' => trim($escola->ed18_c_local),
            'logo' => trim($escola->ed18_c_logo),
            'cep' => trim($escola->ed18_c_cep),
            'funcionamento' => $escola->ed18_i_funcionamento,
            'censoEstado' => $escola->ed18_i_censouf,
            'censoDistrito' => $escola->ed18_i_censodistrito,
            'censoOrgaoRegistro' => $escola->ed18_i_censoorgreg,
            'cnpj' => $escola->ed18_i_cnpj,
            'credenciamento' => $escola->ed18_i_credenciamento,
            'localizacaoDiferenciada' => $escola->ed18_i_locdiferenciada,
            'educacaoIndigena' => $escola->ed18_i_educindigena,
            'tipoLinguaIn' => $escola->ed18_i_tipolinguain,
            'tipoLinguaPt' => $escola->ed18_i_tipolinguapt,
            'linguaIndigena' => $escola->ed18_i_linguaindigena,
            'categoriaPrivada' => $escola->ed18_i_categprivada,
            'cnpjPrivada' => trim($escola->ed18_i_cnpjprivada),
            'cnpjMantenedoraPrivada' => trim($escola->ed18_i_cnpjmantprivada),
            'latitude' => trim($escola->ed18_latitude),
            'longitude' => trim($escola->ed18_longitude),
            'codigoReferencia' => $escola->ed18_codigoreferencia,
            'esferaAdministrativa' => $escola->ed18_i_esferaadministrativa,
            'tipoEscola' => $escola->ed18_i_tipoescola
        ];
    }
}
