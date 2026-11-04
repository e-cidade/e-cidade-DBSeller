<?php

namespace App\Domain\Tributario\ISSQN\Parsers\Redesim\GerarInscricao;

use App\Domain\Tributario\Cadastro\Models\Bairro;
use App\Domain\Tributario\Cadastro\Models\Ruas;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class RedesimDadosEmpresaParser
{

    /**
     * @param array $establishmentData
     * @return object
     */
    public static function buildIssBase($establishmentData)
    {
        $startDate = Arr::get($establishmentData, "dadosRedesim.dataAberturaEmpresa");

        if ($startDate) {
            $startDate = Carbon::createFromFormat('Ymd', $startDate)->format("Y-m-d");
        } else {
            $startDate = Carbon::now()->format("Y-m-d");
        }

        $data = [];
        $data["q02_memo"] = "";
        $data["q02_tiplic"] = "0";
        $data["q02_regjuc"] = Arr::get($establishmentData, "atoAprovado.numeroProcessoOrgaoRegistro");
        $data["q02_inscmu"] = 0;
        $data["q02_obs"] = "";
        $data["q02_dtinic"] = $startDate;
        $data["q02_capit"] = "0";
        $data["q02_dtjunta"] = Arr::get($establishmentData, "atoAprovado.dataRegistro");
        $data["q02_formalocalvara"] = 1;
        $data["q02_protocolojuntacomercial"] = Arr::get($establishmentData, "dadosRedesim.numeroOrgaoRegistro");

        return (object) $data;
    }

    /**
     * @param array $establishmentData
     * @return object
     */
    public static function buildIssQuant($establishmentData)
    {
        $data = [];
        $data["q30_anousu"] = Carbon::now()->format("Y");
        $data["q30_quant"] = 0;
        $data["q30_mult"] = 1;
        $data["q30_area"] = Arr::get($establishmentData, "dadosRedesim.areaTotalEdificacao", 1);

        return (object) $data;
    }

    /**
     * @param array $establishmentData
     * @return object
     */
    public static function buildIssRuas($establishmentData)
    {
        $cep = Arr::get($establishmentData, "dadosRedesim.endereco.cep");

        if (!$cep) {
            return null;
        }

        $rua = Ruas::joinCep()->cep($cep)->first();

        if (!$rua) {
            return null;
        }

        $data = [];
        $data["j14_codigo"] = $rua->j14_codigo;
        $data["q02_numero"] = Arr::get($establishmentData, "dadosRedesim.endereco.numLogradouro");
        $data["q02_compl"] = Arr::get($establishmentData, "dadosRedesim.endereco.complemento");
        $data["z01_cep"] = $cep;

        return (object) $data;
    }

    /**
     * @param array $establishmentData
     * @return object
     */
    public static function buildIssBairro($establishmentData)
    {
        $districtName = Arr::get($establishmentData, "dadosRedesim.endereco.bairro");

        if (!$districtName) {
            return null;
        }

        $bairro = Bairro::nome($districtName)->first();

        if (!$bairro) {
            return null;
        }

        $data = [];
        $data["q13_bairro"] = $bairro->j13_codi;

        return (object) $data;
    }

    public static function buildSimplesNacionalInfo($establishmentData)
    {
        $simplesInfo = Arr::get($establishmentData, "dadosRedesim.periodosSimplesNacional.periodo");

        if (!$simplesInfo) {
            return [];
        }

        $startDateCategoryCode = 1;

        if (Arr::get($establishmentData, "dadosRedesim.opcaoSimei") == "S") {
            $meiCategoryCode = 3;
            $startDateCategoryCode = $meiCategoryCode;
        } else {
            $porte = Arr::get($establishmentData, "dadosRedesim.opcaoSimei");
            if ($porte && $porte != "ME") {
                $eppCategoryCode = 2;
                $startDateCategoryCode = $eppCategoryCode;
            }
        }

        $endDateReasonCode = 3;

        return array_map(function ($data) use ($endDateReasonCode, $startDateCategoryCode) {
            $startDate = Carbon::createFromFormat('Ymd', $data["dataInclusao"])->format("Y-m-d");

            $endDate = Arr::get($data, "dataExclusao");
            if ($endDate) {
                $endDate = Carbon::createFromFormat('Ymd', $endDate)->format("Y-m-d");
            }

            $returnData = [
                "startDateInfo" => (object) [
                    "q38_categoria" => $startDateCategoryCode,
                    "q38_dtinicial" => $startDate
                ],
                "endDateInfo" => null
            ];

            if ($endDate) {
                $returnData["endDateInfo"] = (object) [
                    "q39_issmotivobaixa" => $endDateReasonCode,
                    "q39_dtbaixa" => $endDate,
                    "q39_obs" => "Baixado a partir de evento da REDESIM."
                ];
            }

            return (object) $returnData;
        }, $simplesInfo);
    }
}
