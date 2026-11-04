<?php

namespace App\Domain\Tributario\ISSQN\Parsers\Redesim;

use App\Domain\Tributario\ISSQN\Model\Base\QualificacaoSocio;
use App\Domain\Tributario\ISSQN\Parsers\Redesim\GerarInscricao\RedesimDadosAtividadeParser;
use Carbon\Carbon;
use ECidade\Tributario\Issqn\Inscricao\Atividades\Filter\ListagemAtividades as FiltroListagemAtividades;
use ECidade\V3\Extension\Registry;
use Illuminate\Support\Arr;

class RedesimDadosAtividadeBaseParser
{
    /**
     * @throws \Exception
     * @return array
     */
    protected static function buildActivities($establishmentData)
    {
        $activityStartDate = Arr::get($establishmentData, "dadosRedesim.dataInicioAtividade");

        if ($activityStartDate) {
            $activityStartDate = Carbon::createFromFormat('Ymd', $activityStartDate)->format("Y-m-d");
        } else {
            $activityStartDate = Arr::get($establishmentData, "dadosRedesim.dataAberturaEmpresa");

            if ($activityStartDate) {
                $activityStartDate = Carbon::createFromFormat('Ymd', $activityStartDate)->format("Y-m-d");
            } else {
                $activityStartDate = Carbon::now()->format("Y-m-d");
            }
        }

        $activityList = Arr::get($establishmentData, "dadosRedesim.atividadesEconomica");

        if (!$activityList) {
            throw new \Exception("Atividades não informadas.");
        }

        $activityDataList = [];

        $mainActivityCnaeCode = Arr::get($activityList, "cnaeFiscal.codigo");
        $activityData = RedesimDadosAtividadeBaseParser::buildActivity(
            $mainActivityCnaeCode,
            true,
            $activityStartDate
        );

        $activityDataList[$activityData->data->q07_ativ] = $activityData;

        $secondaryActivityList = Arr::get($activityList, "cnaesSecundarias.cnaeSecundaria");

        if (!$secondaryActivityList) {
            return $activityDataList;
        }

        foreach ($secondaryActivityList as $secondaryActivity) {
            $secondaryActivityCnaeCode = Arr::get($secondaryActivity, "codigo");

            $activityData = RedesimDadosAtividadeBaseParser::buildActivity(
                $secondaryActivityCnaeCode,
                false,
                $activityStartDate
            );

            $activityDataList[$activityData->data->q07_ativ] = $activityData;
        }

        return $activityDataList;
    }

    /**
     * @param \stdClass $activityData
     * @param boolean $isMainActivity
     * @param string $activityStartDate
     * @return object
     * @throws \Exception
     */
    private static function buildActivity($cnaeCode, $isMainActivity, $activityStartDate)
    {
        if (!$cnaeCode) {
            throw new \Exception("CNAE não informado.");
        }

        $activityData = RedesimDadosAtividadeBaseParser::findActivityByCnaeCode($cnaeCode);

        $data = [];
        $data["isMainActivity"] = $isMainActivity;
        $data["data"] = (object) [
            "q07_ativ" => $activityData->sequencial,
            "q07_datain" => $activityStartDate,
            "q07_quant" => 1,
            "q07_perman" => "true",
            "q07_tipbx" => "0",
            "q07_imprimealvara" => "Sim",
            "q07_datafi" => null,
            "risk" => $activityData->risco
        ];

        return (object) $data;
    }

    /**
     * @throws \Exception
     * @return \stdClass
     */
    private static function findActivityByCnaeCode($cnaeCode)
    {
        if (!$cnaeCode) {
            throw new \Exception("CNAE não informado.");
        }

        $container = Registry::get('app.container')->get('tributario.container');
        $activityRepository = $container->get('Inscricao\Atividades\Repository\Atividades');

        $filter = new FiltroListagemAtividades();
        $filter->setEstruturalCnae($cnaeCode);

        $activityList = $activityRepository->listarAtividades($filter);

        if (count($activityList) == 0) {
            throw new \Exception("Atividade não encontrada para o CNAE: {$cnaeCode}");
        }

        return $activityList[0];
    }
}
