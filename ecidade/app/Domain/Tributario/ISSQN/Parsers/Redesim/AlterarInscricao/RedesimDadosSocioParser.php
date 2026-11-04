<?php

namespace App\Domain\Tributario\ISSQN\Parsers\Redesim\AlterarInscricao;

use App\Domain\Tributario\ISSQN\Parsers\Redesim\RedesimDadosSocioBaseParser;
use Illuminate\Support\Arr;

class RedesimDadosSocioParser extends RedesimDadosSocioBaseParser
{
    public static function buildChangeCapitalStockInfo($establishmentData)
    {
        return RedesimDadosSocioParser::buildPartners($establishmentData, function ($partner) {
            $capitalStockValue = Arr::get($partner, "capitalSocialSocio");

            if ($capitalStockValue) {
                $capitalStockValue = (floatval($capitalStockValue) / 100);
            }

            $data = [];
            $data["cpfCnpj"] = $partner["cnpjCpfSocio"];
            $data["data"] = [
                "q95_perc" => $capitalStockValue
            ];

            return (object) $data;
        });
    }

    public static function buildPartnerInfoList($establishmentData)
    {
        return RedesimDadosSocioParser::buildPartners($establishmentData, function ($partner) {
            return RedesimDadosSocioParser::buildPartner($partner);
        });
    }

    /**
     * @param array $establishmentData
     * @return array
     */
    private static function buildPartners($establishmentData, $callback)
    {
        $partnerDataList = [];

        $partnerList = Arr::get($establishmentData, "dadosRedesim.socios.socio");

        if (!$partnerList) {
            return $partnerDataList;
        }

        foreach ($partnerList as $partner) {
            $partnerDataList[] = $callback($partner);
        }

        return $partnerDataList;
    }
}
