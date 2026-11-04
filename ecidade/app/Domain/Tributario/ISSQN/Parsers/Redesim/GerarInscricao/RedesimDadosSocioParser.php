<?php

namespace App\Domain\Tributario\ISSQN\Parsers\Redesim\GerarInscricao;

use App\Domain\Tributario\ISSQN\Model\Base\QualificacaoSocio;
use App\Domain\Tributario\ISSQN\Parsers\Redesim\RedesimDadosSocioBaseParser;
use Illuminate\Support\Arr;

class RedesimDadosSocioParser extends RedesimDadosSocioBaseParser
{

    /**
     * @param array $establishmentData
     * @return array
     */
    public static function buildPartners($establishmentData)
    {
        $partnerDataList = [];

        $partnerList = Arr::get($establishmentData, "dadosRedesim.socios.socio");

        if (!$partnerList) {
            return $partnerDataList;
        }

        foreach ($partnerList as $partner) {
            $partnerDataList[] = RedesimDadosSocioParser::buildPartner($partner);
        }

        return $partnerDataList;
    }
}
