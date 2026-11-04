<?php

namespace App\Domain\Tributario\ISSQN\Parsers\Redesim\AlterarInscricao;

use App\Domain\Tributario\Cadastro\Models\Bairro;
use App\Domain\Tributario\Cadastro\Models\Ruas;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\Isscadsimples;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class RedesimDadosEmpresaParser
{
    public static function buildChangeEmailInfo($establishmentData)
    {
        $data = [];
        $data["z01_email"] = Arr::get($establishmentData, "dadosRedesim.contato.correioEletronico");

        return $data;
    }

    public static function buildChangeFantasyNameInfo($establishmentData)
    {
        $data = [];
        $data["z01_nomefanta"] = Arr::get($establishmentData, "dadosRedesim.nomeFantasia");

        return $data;
    }

    public static function buildChangeEstablishmentNameInfo($establishmentData)
    {
        $data = [];
        $data["z01_nome"] = substr(Arr::get($establishmentData, "dadosRedesim.nomeEmpresarial"), 0, 40);
        $data["z01_nomecomple"] = substr(Arr::get($establishmentData, "dadosRedesim.nomeEmpresarial"), 0, 100);

        return $data;
    }

    /**
     * @param array $establishmentData
     * @return array
     * @throws \Exception
     */
    public static function buildIssRuas($establishmentData)
    {
        $cep = Arr::get($establishmentData, "dadosRedesim.endereco.cep");

        if (!$cep) {
            throw new \Exception("CEP não informado.");
        }

        $rua = Ruas::joinCep()->cep($cep)->first();

        if (!$rua) {
            throw new \Exception("Rua não encontrada para o CEP: {$cep}.");
        }

        $data = [];
        $data["j14_codigo"] = $rua->j14_codigo;
        $data["q02_numero"] = Arr::get($establishmentData, "dadosRedesim.endereco.numLogradouro");
        $data["q02_compl"] = Arr::get($establishmentData, "dadosRedesim.endereco.complemento");
        $data["z01_cep"] = $cep;

        $data = array_filter($data, function ($value) {
            return !is_null($value) && $value !== "";
        });

        return $data;
    }

    /**
     * @param array $establishmentData
     * @return array
     * @throws \Exception
     */
    public static function buildIssBairro($establishmentData)
    {
        $districtName = Arr::get($establishmentData, "dadosRedesim.endereco.bairro");

        if (!$districtName) {
            throw new \Exception("Nome do bairro não informado.");
        }

        $bairro = Bairro::nome($districtName)->first();

        if (!$bairro) {
            throw new \Exception("Bairro {$districtName} não encontrado.");
        }

        $data = [];
        $data["q13_bairro"] = $bairro->j13_codi;

        return $data;
    }

    public static function buildChangeCgmAddress($establishmentData)
    {
        $address = Arr::get($establishmentData, "dadosRedesim.endereco");

        $data = [];
        $data["z01_cep"] = Arr::get($address, "cep");
        $data["z01_bairro"] = Arr::get($address, "bairro");
        $data["z01_numero"] = onlyNumbers(Arr::get($address, "numLogradouro"));
        $data["z01_ender"] = Arr::get($address, "logradouro");
        $data["z01_compl"] = Arr::get($address, "complemento");

        return $data;
    }

    /**
     * @throws \Exception
     */
    public static function buildChangePhoneInfo($establishmentData)
    {
        $dddMainPhone = Arr::get($establishmentData, "dadosRedesim.contato.dddTelefone1");
        $mainNumber = Arr::get($establishmentData, "dadosRedesim.contato.telefone1");

        if ($dddMainPhone && $mainNumber) {
            $dddPhone = $dddMainPhone;
            $number = $mainNumber;
        } else {
            $dddPhone = Arr::get($establishmentData, "dadosRedesim.contato.dddTelefone2");
            $number = Arr::get($establishmentData, "dadosRedesim.contato.telefone2");
        }

        if (!$dddPhone || !$number) {
            throw new \Exception("Informação do número do telefone está incompleta");
        }

        $data = [];
        $data["z01_telef"] = "({$dddPhone}) {$number}";

        return $data;
    }

    public static function buildChangeSimplesNacional(IssBase $issBase, $establishmentData)
    {
        $simplesInfo = Arr::get($establishmentData, "dadosRedesim.periodosSimplesNacional.periodo");

        if (!$simplesInfo) {
            return [];
        }

        $startDateCategoryCode = 1;

        $cadSimples = Isscadsimples::query()
                                   ->leftJoin("isscadsimplesbaixa", "q39_isscadsimples", "q38_sequencial")
                                   ->where("q38_inscr", $issBase->q02_inscr)
                                   ->orderBy("q38_dtinicial", "desc")
                                   ->first(["q38_categoria", "q39_sequencial"]);

        if ($cadSimples && !$cadSimples->q39_sequencial) {
            $startDateCategoryCode = $cadSimples->q38_categoria;
        } else {
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
