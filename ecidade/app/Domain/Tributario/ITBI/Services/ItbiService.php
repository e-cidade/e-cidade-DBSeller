<?php

namespace App\Domain\Tributario\ITBI\Services;

use App\Domain\Patrimonial\Protocolo\Services\CgmService;
use App\Domain\Tributario\Cadastro\Repositories\CaracterRepository;
use App\Domain\Tributario\Cadastro\Repositories\CarconstrRepository;
use App\Domain\Tributario\Cadastro\Repositories\CargrupRepository;
use App\Domain\Tributario\Cadastro\Repositories\IptubaseRepository;
use App\Domain\Tributario\Cadastro\Repositories\IptucalcRepository;
use App\Domain\Tributario\Cadastro\Repositories\IptucaleRepository;
use App\Domain\Tributario\Cadastro\Repositories\IptuconstrRepository;
use App\Domain\Tributario\ITBI\Repositories\AnexoArquivoItbiRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbisituacaoRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbitransacaoRepository;
use App\Domain\Tributario\ITBI\Repositories\ParitbiRepository;
use ECidade\Lib\Session\DefaultSession;
use ECidade\Tributario\Arrecadacao\Repository\TaxasLancadasRepository;
use ECidade\Tributario\ITBI\Model\Taxasitbitaxa;
use ECidade\Tributario\ITBI\Repository\TaxasitbiRepository;
use ECidade\Tributario\ITBI\Repository\TaxasitbitaxaRepository;
use ECidade\Tributario\Juridico\CartorioExtrajudicial\Repository\CartorioExtraTipoRepository;

final class ItbiService
{
    private $defaultSession;

    public function __construct()
    {
        $this->defaultSession = DefaultSession::getInstance();
    }

    private function getParitbi()
    {
        $paritbiRepository = new ParitbiRepository();

        return $paritbiRepository->getByAnousu($this->defaultSession->get(DefaultSession::DB_ANOUSU));
    }

    public function getTipos($tipoItbi)
    {
        $taxasitbiRepository = TaxasitbiRepository::getInstance();

        if ($tipoItbi == 1) {
            $sWhere = "(it36_imovelurbano = 't' OR it36_imovelurbanopleno = 't')";
        } else {
            $sWhere = "it36_imovelrural = 't'";
        }

        $aTipos = $taxasitbiRepository->getAllTipos($sWhere);

        return array_map(function ($oTipo) use ($aTipos) {
            return (object) [
                "codigo" => $oTipo->it36_sequencial,
                "descricao" => $oTipo->it36_descricao,
                "selecionado" => (count($aTipos) == 1)
            ];
        }, $aTipos);
    }

    public function getSituacao()
    {
        $itbisituacaoRepository = new ItbisituacaoRepository();
        $aSituacao = $itbisituacaoRepository->get()->toArray();

        return array_map(function ($aSituacao) {
            return (object) [
                "codigo" => $aSituacao["it07_codigo"],
                "descricao" => $aSituacao["it07_descr"]
            ];
        }, $aSituacao);
    }

    public function getTipoTransacao()
    {
        $itbitransacaoRepository = new ItbitransacaoRepository();
        $aTipoTransacao = $itbitransacaoRepository->get()->toArray();

        return array_map(function ($aTipoTransacao) {
            return (object) [
                "codigo" => $aTipoTransacao["it04_codigo"],
                "descricao" => $aTipoTransacao["it04_descr"]
            ];
        }, $aTipoTransacao);
    }

    public function getFormaPagamentoTipoTransacao($tipoTransacao)
    {
        $itbitransacaoRepository = new ItbitransacaoRepository();
        $aFormasPgamento = $itbitransacaoRepository->getFormaPagamentoByTipoTransacao($tipoTransacao, [
            "it25_sequencial",
            "it27_descricao",
            "it27_aliquota",
            "it28_avista"
        ], true)->toArray();

        return array_map(function ($aFormaPgamento) {
            return (object) [
                "codigo" => $aFormaPgamento["it25_sequencial"],
                "descricao" => $aFormaPgamento["it27_descricao"],
                "aliquota" => $aFormaPgamento["it27_aliquota"],
                "valor" => 0,
                "bloquear" => ($aFormaPgamento["it28_avista"] == "t")
            ];
        }, $aFormasPgamento);
    }

    public function getTaxasItbi($tipoTaxa, $matricula = null)
    {
        $valorVenalTerreno = 0;
        $valorVenalConstrucao = 0;

        if (!empty($matricula)) {
            $defaultSession = DefaultSession::getInstance();
            $anousu = $defaultSession->get(DefaultSession::DB_ANOUSU);

            $iptucalcRepository = new IptucalcRepository();
            $oIptucalc = $iptucalcRepository->getByAnoMatricula($anousu, $matricula);

            $valorVenalTerreno = $oIptucalc->j23_vlrter;

            $iptucaleRepository = new IptucaleRepository();
            $aIptucale = $iptucaleRepository->getByAnoMatricula($anousu, $matricula);

            if (count($aIptucale)) {
                $aValorConstrucao = array_map(function ($oIptucale) {
                    return floatval($oIptucale["j22_valor"]);
                }, $aIptucale->toArray());

                $valorVenalConstrucao = array_sum($aValorConstrucao);
            }
        }

        $taxasLancadasRepository = TaxasLancadasRepository::getInstance();
        $taxasitbitaxaRepository = TaxasitbitaxaRepository::getInstance();
        $taxasitbitaxa = new Taxasitbitaxa();

        $taxasitbitaxa->setTaxasitbi($tipoTaxa);
        $aTaxas = $taxasitbitaxaRepository->getTaxas($taxasitbitaxa);

        return array_map(function ($oTaxa) use ($taxasLancadasRepository, $valorVenalConstrucao, $valorVenalTerreno) {
            $oTaxaLancada = $taxasLancadasRepository->getTaxa($oTaxa->ar44_sequencial);

            $sCalculaSobre = "";

            switch ($oTaxa->it37_calculasobre) {
                case 1:
                    $sCalculaSobre = "Valor Venal do Terreno";
                    break;
                case 2:
                    $sCalculaSobre = "Valor Venal da Construção";
                    break;
                case 3:
                    $sCalculaSobre = "Valor Total";
                    break;
            }

            $fAliquota = null;
            $fValor = $oTaxaLancada->i02_valor;
            $sTipoValor = "Fixo";
            $sFaixa = "";

            if ($oTaxa->ar44_tipo == 2) {
                $valorCalculado = 0;

                switch ($oTaxa->it37_calculasobre) {
                    case 1:
                        $valorCalculado = ($oTaxaLancada->i02_valor / 100) * $valorVenalTerreno;
                        break;
                    case 2:
                        $valorCalculado = ($oTaxaLancada->i02_valor / 100) * $valorVenalConstrucao;
                        break;
                    case 3:
                         $valorCalculado =
                        ($oTaxaLancada->i02_valor / 100) * ($valorVenalTerreno + $valorVenalConstrucao);
                        break;
                }

                $fAliquota = "{$oTaxaLancada->i02_valor}%";
                $fValor = $valorCalculado;
            } else {
                if ($oTaxa->ar44_tipo == 3) {
                    $sTipoValor = "Fixo Sobre Faixa";

                    $fInicioFaixa = formataValorMonetario($oTaxa->it37_iniciofaixa);
                    $fFimFaixa = formataValorMonetario($oTaxa->it37_fimfaixa);

                    $sFaixa = "{$fInicioFaixa} à {$fFimFaixa}";
                } else {
                    $sTipoValor = "Percentual";
                }
            }

            return [
                "codigo" => $oTaxa->ar44_sequencial,
                "descricao" => $oTaxa->ar44_descricao,
                "tipoValor" => $sTipoValor,
                "codigoTipoValor" => $oTaxa->ar44_tipo,
                "calculaSobre" => $sCalculaSobre,
                "codigoCalculaSobre" => $oTaxa->it37_calculasobre,
                "aliquota" => $fAliquota,
                "valor" => formataValorMonetario($fValor),
                "faixa" => $sFaixa,
                "inicioFaixa" => $oTaxa->it37_iniciofaixa,
                "fimFaixa" => $oTaxa->it37_fimfaixa,
                "mostra" => true
            ];
        }, $aTaxas);
    }

    public function getTipoBenfeitoria($tipoItbi)
    {
        $oParitbi = $this->getParitbi();

        if ($tipoItbi == 1) {
            $iGrupoTipo = $oParitbi->it24_grupotipobenfurbana;
        } else {
            $iGrupoTipo = $oParitbi->it24_grupotipobenfrural;
        }

        $caracterRepository = new CaracterRepository();
        $aTipos = $caracterRepository->getByGrupo($iGrupoTipo)->toArray();

        return array_map(function ($aTipo) {
            return [
                "codigo" => $aTipo["j31_codigo"],
                "descricao" => $aTipo["j31_descr"]
            ];
        }, $aTipos);
    }

    public function getEspecieBenfeitoria($tipoItbi)
    {
        $oParitbi = $this->getParitbi();

        if ($tipoItbi == 1) {
            $iGrupoEspecie = $oParitbi->it24_grupoespbenfurbana;
        } else {
            $iGrupoEspecie = $oParitbi->it24_grupoespbenfrural;
        }

        $caracterRepository = new CaracterRepository();
        $aTipos = $caracterRepository->getByGrupo($iGrupoEspecie)->toArray();

        return array_map(function ($aTipo) {
            return [
                "codigo" => $aTipo["j31_codigo"],
                "descricao" => $aTipo["j31_descr"]
            ];
        }, $aTipos);
    }

    public function getPadraoConstrutivoBenfeitoria()
    {
        $oParitbi = $this->getParitbi();

        $caracterRepository = new CaracterRepository();
        $aTipos = $caracterRepository->getByGrupo($oParitbi->it24_grupopadraoconstrutivobenurbana)->toArray();

        return array_map(function ($aTipo) {
            return [
                "codigo" => $aTipo["j31_codigo"],
                "descricao" => $aTipo["j31_descr"]
            ];
        }, $aTipos);
    }

    public function getCaractImovelOrUtilImovel($tipo)
    {
        $oParitbi = $this->getParitbi();

        if ($tipo == 1) {
            $iGrupo = $oParitbi->it24_grupodistrterrarural;
        } else {
            $iGrupo = $oParitbi->it24_grupoutilterrarural;
        }

        $caracterRepository = new CaracterRepository();
        $aCaracter = $caracterRepository->getByGrupo($iGrupo)->toArray();

        return array_map(function ($aCaracter) use ($tipo) {
            return [
                "codigo" => $aCaracter["j31_codigo"],
                "descricao" => $aCaracter["j31_descr"],
                "tipo" => $tipo
            ];
        }, $aCaracter);
    }

    public function getTransmitentesMatricula($matricula)
    {
        $aEnvolvidos = fc_busca_envolvidos("f", 1, "M", $matricula);
        $cgmService = new CgmService();

        return array_map(function ($aEnvolvido) use ($cgmService) {
            $aTransmitente = $cgmService->getByNumcgm($aEnvolvido->rinumcgm);
            $aTransmitente["principal"] = $aEnvolvido->ritipoenvol;

            return $aTransmitente;
        }, $aEnvolvidos);
    }

    public function getCartorios()
    {
        $cartorioExtraTipoRepository = CartorioExtraTipoRepository::getInstance();
        $aCartorios = $cartorioExtraTipoRepository->setOuterCondition("j168_tiposcartorioextra IN (2,3)")
                                                  ->setGroupBy("j167_sequencial, j167_descricao")
                                                  ->setOrderBy("j167_sequencial")
                                                  ->setCampos("cartorioextra.*")
                                                  ->get();

        return array_map(function ($oCartorio) {
            return [
                "codigo" => $oCartorio->j167_sequencial,
                "descricao" => $oCartorio->j167_descricao
            ];
        }, $aCartorios);
    }

    public function getBenfeitoriasByMatric($matricula)
    {
        $oParitbi = $this->getParitbi();
        $aBenfeitorias = [];

        if ($oParitbi->it24_carregaconstrucoesbenfeitoriasitbi) {
            $carconstrRepository = new CarconstrRepository();

            $iptuconstrRepository = new IptuconstrRepository();
            $aIptuconstr = $iptuconstrRepository->getByMatric($matricula, [
                "j39_idcons",
                "j39_area",
                "j39_ano"
            ])->toArray();

            $aBenfeitorias = array_map(function ($aIptuconstr) use ($carconstrRepository, $matricula, $oParitbi) {
                $oCarconstrTipo = $carconstrRepository->getCaracterSelecionadaByMatricConstrucao(
                    $matricula,
                    $aIptuconstr["j39_idcons"],
                    $oParitbi->it24_grupotipobenfurbana
                );

                $oCarconstrEspecie = $carconstrRepository->getCaracterSelecionadaByMatricConstrucao(
                    $matricula,
                    $aIptuconstr["j39_idcons"],
                    $oParitbi->it24_grupoespbenfurbana
                );

                $oCarconstrPadraoConstr = $carconstrRepository->getCaracterSelecionadaByMatricConstrucao(
                    $matricula,
                    $aIptuconstr["j39_idcons"],
                    $oParitbi->it24_grupopadraoconstrutivobenurbana
                );

                return [
                    "tipo" => $oCarconstrTipo->j31_codigo,
                    "especie" => $oCarconstrEspecie->j31_codigo,
                    "padraoConstrutivo" => $oCarconstrPadraoConstr->j31_codigo,
                    "area" => $aIptuconstr["j39_area"],
                    "areaTrans" => $aIptuconstr["j39_area"],
                    "ano" => $aIptuconstr["j39_ano"]
                ];
            }, $aIptuconstr);
        }

        return $aBenfeitorias;
    }


    public function processarArquivo($itbi, $desc, $obs, $user, $file)
    {
        $anexoRepository = new AnexoArquivoItbiRepository();

        $arqPath = $file->getRealPath();
        $arqName = $file->getClientOriginalName();
        $upload = null;
        $saveInfo = null;

        if ($arqPath != null && $arqName != null) {
            $upload = $anexoRepository->uploadArquivo($arqPath, $arqName, $itbi);
            if (isset($upload["sucesso"]) && $upload["sucesso"] != null) {
                $saveInfo = $anexoRepository->saveDadosUpload($itbi, $desc, $obs, $user, $arqName, $upload["sucesso"]);
            }
        }

        return ['upload' => $upload, 'saveInfo' => $saveInfo];
    }


    public function getArquivosItbi($itbi)
    {
        $anexoRepository = new AnexoArquivoItbiRepository();
        return $anexoRepository->getArquivosItbi($itbi);
    }

    public function deleteArquivo($sequencial, $idArq, $idUser, $itbi)
    {
        $anexoRepository = new AnexoArquivoItbiRepository();
        return $anexoRepository->deleteArquivo($sequencial, $idArq, $idUser, $itbi);
    }

    public function updateDadosArquivo($sequencial, $itbi, $desc, $obs)
    {
        $anexoRepository = new AnexoArquivoItbiRepository();
        return $anexoRepository->updateDadosArquivo($sequencial, $itbi, $desc, $obs);
    }

    public function downloadArquivo($idArq)
    {
        $anexoRepository = new AnexoArquivoItbiRepository();
        return $anexoRepository->downloadArquivo($idArq);
    }
}
