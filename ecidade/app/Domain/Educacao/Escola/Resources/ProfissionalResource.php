<?php

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Models\Profissional;

class ProfissionalResource
{
    public static function toResponse(Profissional $profissional)
    {
        return (object) [
            "codigo" => $profissional->ed20_i_codigo,
            "codigoinep" => $profissional->ed20_i_codigoinep,
            "nis" => $profissional->ed20_c_nis,
            "raca" => $profissional->ed20_i_raca,
            "nacionalidade" => $profissional->ed20_i_nacionalidade,
            "censoEstadoNaturalidade" => $profissional->ed20_i_censoufnat,
            "censoMunicipioNaturalidade" => $profissional->ed20_i_censomunicnat,
            "identidadeCompleta" => $profissional->ed20_c_identcompl,
            "censoOrgaoEmissao" => $profissional->ed20_i_censoorgemiss,
            "censoEstadoIdentidade" => $profissional->ed20_i_censoufident,
            "dataIdentidade" => $profissional->ed20_d_dataident,
            "certidaoTipo" => $profissional->ed20_i_certidaotipo,
            "certidaoNumero" => $profissional->ed20_c_certidaonum,
            "certidaoFolha" => $profissional->ed20_c_certidaofolha,
            "certidaoLivro" => $profissional->ed20_c_certidaolivro,
            "certidaoData" => $profissional->ed20_c_certidaodata,
            "certidaoCartorio" => $profissional->ed20_c_certidaocart,
            "censoEstadocertidao" => $profissional->ed20_i_censoufcert,
            "passaporte" => $profissional->ed20_c_passaporte,
            "censoEstadoEndereco" => $profissional->ed20_i_censoufender,
            "censoMunicipioEndereco" => $profissional->ed20_i_censomunicender,
            "escolaridade" => $profissional->ed20_i_escolaridade,
            "posGraduacao" => $profissional->ed20_c_posgraduacao,
            "outrosCursos" => $profissional->ed20_c_outroscursos,
            "pais" => $profissional->ed20_i_pais,
            "tipoServidor" => $profissional->ed20_i_tiposervidor,
            "rhRegime" => $profissional->ed20_i_rhregime,
            "efetividade" => $profissional->ed20_c_efetividade,
            "censoCartorio" => $profissional->ed20_i_censocartorio,
            "zonaResidencia" => $profissional->ed20_i_zonaresidencia,
            "paisResidencia" => $profissional->ed20_paisresidencia,
            "localizacaoDiferenciada" => $profissional->ed20_localizacaodiferenciada,
            "tipoEnsinomedio" => $profissional->ed20_tipoensinomedio
        ];
    }
}
