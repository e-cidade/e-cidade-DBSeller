<?php

namespace App\Domain\Tributario\ISSQN\Parsers\Redesim;

use App\Domain\Tributario\ISSQN\Model\Base\QualificacaoSocio;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class RedesimDadosSocioBaseParser
{
    /**
     * @return \stdClass
     */
    protected static function buildPartner($partner)
    {
        $partnerQualificationCode = Arr::get($partner, "codQualificacaoSocio");

        if ($partnerQualificationCode) {
            $qualificacaoSocio = QualificacaoSocio::where("q180_codigo", $partnerQualificationCode)->first();

            if ($qualificacaoSocio) {
                $partnerQualificationCode = $qualificacaoSocio->q180_sequencial;
            } else {
                $partnerQualificationCode = null;
            }
        }

        $birthDate = Arr::get($partner, "dataNascimento");
        if ($birthDate) {
            $birthDate = Carbon::createFromFormat("Ymd", $birthDate)->format("Y-m-d");
        }

        $brazilianNationalityCode = 1;

        $capitalStockValue = Arr::get($partner, "capitalSocialSocio", 0);

        if ($capitalStockValue) {
            $capitalStockValue = (floatval($capitalStockValue) / 100);
        }

        $data = [];
        $data["cpfCnpj"] = $partner["cnpjCpfSocio"];
        $data["data"] = (object) [
            "q95_perc" => $capitalStockValue,
            "q95_tipo" => 1,
            "q95_qualificacaosocio" => $partnerQualificationCode
        ];

        $addressNumber = onlyNumbers(Arr::get($partner, "enderecoSocio.numLogradouro"));

        $data["personData"] = [
            "z01_cgccpf" => $partner["cnpjCpfSocio"],
            "z01_nome" => Arr::get($partner, "nome"),
            "z01_cep" => Arr::get($partner, "enderecoSocio.cep"),
            "z01_bairro" => Arr::get($partner, "enderecoSocio.bairro"),
            "z01_numero" => $addressNumber ?: null,
            "z01_ender" => Arr::get($partner, "enderecoSocio.logradouro"),
            "z01_compl" => Arr::get($partner, "enderecoSocio.complemento"),
            "z01_email" => Arr::get($partner, "contatoSocio.correioEletronico"),
            "z01_sexo" => Arr::get($partner, "sexo"),
            "z01_mae" => Arr::get($partner, "nomeMae"),
            "z01_estciv" => RedesimDadosSocioBaseParser::buildMaritalStatus(Arr::get($partner, "estadoCivil")),
            "z01_nasc" => $birthDate,
            "z01_nacion" => $brazilianNationalityCode
        ];

        return (object) $data;
    }

    private static function buildMaritalStatus($maritalStatusCode)
    {
        $single = 1;
        $married = 2;
        $widower = 3;
        $legalSeparation = 6;
        $divorced = 4;
        $stableUnion = 7;

        switch ($maritalStatusCode) {
            case 1:
                return $single;
            case 2:
                return $married;
            case 3:
                return $widower;
            case 4:
                return $legalSeparation;
            case 5:
                return $divorced;
            case 6:
                return $stableUnion;
            default:
                return null;
        }
    }
}
