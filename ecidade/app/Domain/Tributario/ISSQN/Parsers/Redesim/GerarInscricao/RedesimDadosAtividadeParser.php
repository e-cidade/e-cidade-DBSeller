<?php

namespace App\Domain\Tributario\ISSQN\Parsers\Redesim\GerarInscricao;

use App\Domain\Tributario\ISSQN\Parsers\Redesim\RedesimDadosAtividadeBaseParser;

class RedesimDadosAtividadeParser extends RedesimDadosAtividadeBaseParser
{

    /**
     * @throws \Exception
     * @return array
     */
    public static function buildActivities($establishmentData)
    {
        return RedesimDadosAtividadeBaseParser::buildActivities($establishmentData);
    }
}
