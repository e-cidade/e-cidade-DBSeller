<?php

namespace App\Domain\Tributario\ISSQN\Parsers\Redesim\AlterarInscricao;

use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishment;
use App\Domain\Tributario\ISSQN\Parsers\Redesim\RedesimDadosAtividadeBaseParser;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class RedesimDadosAtividadeParser extends RedesimDadosAtividadeBaseParser
{
    public static function buildBaixa(ProcessedEstablishment $processedEstablishment, $certificateNumber)
    {
        $currentDate = date('Y-m-d', db_getsession("DB_datausu"));

        $data = [];
        $data["tabAtiv"] = [];
        $data["tabAtiv"]["q07_databx"] = $currentDate;
        $data["tabAtiv"]["q07_datafi"] = $currentDate;

        $data["tabAtivBaixa"] = [];
        $data["tabAtivBaixa"]["q11_processo"] = $processedEstablishment->q190_process_id;
        $data["tabAtivBaixa"]["q11_oficio"] = "false";
        $data["tabAtivBaixa"]["q11_obs"] = "Baixa a partir de processo da REDESIM.";
        $data["tabAtivBaixa"]["q11_login"] = db_getsession("DB_id_usuario");
        $data["tabAtivBaixa"]["q11_data"] = $currentDate;
        $data["tabAtivBaixa"]["q11_hora"] = db_hora();
        $data["tabAtivBaixa"]["q11_numero"] = $certificateNumber;

        return $data;
    }

    /**
     * @throws \Exception
     * @return array
     */
    public static function buildActivities($establishmentData)
    {
        $activitiesInfo =  RedesimDadosAtividadeBaseParser::buildActivities($establishmentData);

        $activityStartDate = Arr::get($establishmentData, "dadosRedesim.dataAprovacaoProcesso");

        if ($activityStartDate) {
            $activityStartDate = Carbon::createFromFormat('Ymd', $activityStartDate)->format("Y-m-d");

            $activitiesInfo = array_map(function ($activityInfo) use ($activityStartDate) {
                $activityInfo->data->q07_datain = $activityStartDate;

                return $activityInfo;
            }, $activitiesInfo);
        }

        return $activitiesInfo;
    }
}
