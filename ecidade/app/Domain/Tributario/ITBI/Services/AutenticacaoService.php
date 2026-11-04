<?php

namespace App\Domain\Tributario\ITBI\Services;

use App\Domain\Configuracao\Instituicao\Repository\InstituicaoRepository;
use App\Domain\Tributario\Arrecadacao\Repositories\ArrebancoRepository;
use App\Domain\Tributario\Arrecadacao\Repositories\DisbancoRepository;
use App\Domain\Tributario\Cadastro\Repositories\IptuantRepository;
use App\Domain\Tributario\Cadastro\Repositories\IptuenderRepository;
use App\Domain\Tributario\ITBI\Reports\Autenticacao;
use App\Domain\Tributario\ITBI\Repositories\ItbiavaliaRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbidadosimovelRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbimatricRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbinomeRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbinumpreRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiRepository;
use App\Domain\Tributario\ITBI\Repositories\ItbiruralcaractRepository;
use Illuminate\Support\Facades\DB;

final class AutenticacaoService extends Autenticacao
{
    /**
     * Carrega os métodos necessários para geração do PDF
     */
    public function autenticar()
    {
        $this->buscarGuiaReciboMigrado();

        $retorno = $this->buscarDadosItbi();

        if ($retorno === false) {
            return false;
        }

        $this->gerarArquivoAutenticacao();

        return true;
    }

    /**
     * Busca a guia de ITBI de recibos gerados em sistemas anteriores ao e-cidade
     * @throws \Exception
     */
    private function buscarGuiaReciboMigrado()
    {
        $arrebancoRepository = new ArrebancoRepository();
        $oArrebanco = $arrebancoRepository->getItbiMigradoByNbant($this->numeroGuia, false);

        if (empty($oArrebanco->it15_guia)) {
            return false;
        }

        $this->setMigrado(true);
        $this->setNumeroDam("{$oArrebanco->k00_nbant}/1");

        $this->setNumeroGuia($oArrebanco->it15_guia);
    }

    /**
     * Busca os dados necessários para fazer a autenticação
     * @throws \Exception
     */
    private function buscarDadosItbi()
    {
        $itbiRepository = new ItbiRepository();
        $oItbi = $itbiRepository->getAllByGuia($this->numeroGuia, [
            "itbi.*",
            "it04_descr",
            "it05_guia",
            "it18_guia",
            "it22_matricri",
            "j90_descr",
            "j05_descr",
            "j06_quadraloc",
            "j06_lote",
            "j13_descr"
        ]);

        $this->setDadosItbi("oGuia", $oItbi);

        $aPagamentos = $this->buscarPagamentoGuia();

        if ($aPagamentos === false) {
            return false;
        }

        $this->setDadosItbi("aPagamentos", $aPagamentos);

        $instituicaoRepository = new InstituicaoRepository();
        $oInstituicao = $instituicaoRepository->find(1);

        $this->setDadosItbi("oInstituicao", $oInstituicao);

        $itbimatricRepository = new ItbimatricRepository();
        $oItbiMatric = $itbimatricRepository->getByGuia($this->numeroGuia);

        $this->setDadosItbi("oItbiMatric", $oItbiMatric);

        $iptuantRepository = new IptuantRepository();
        $oIptuant = new \stdClass();
        if (isset($oItbiMatric->it06_matric)) {
            $oIptuant = $iptuantRepository->getByMatric($oItbiMatric->it06_matric);
        }

        $this->setDadosItbi("oIptuant", $oIptuant);

        $this->buscarTransmitenteAdquirente();

        $itbiavaliaRepository = new ItbiavaliaRepository();
        $oItbiavalia = $itbiavaliaRepository->getByGuia($this->numeroGuia);

        $this->setDadosItbi("oItbiavalia", $oItbiavalia);

        $oDisbanco = $this->buscarBaixaDebito();

        $this->setDadosItbi("oDisbanco", $oDisbanco);

        $itbidadosimovelRepository = new ItbidadosimovelRepository();
        $oDadosImovel = $itbidadosimovelRepository->getByGuia($this->numeroGuia);

        $this->setDadosItbi("oDadosImovel", $oDadosImovel);

        $iptuenderRepository = new IptuenderRepository();
        $oIptuender = new \stdClass();
        if (isset($oItbiMatric->it06_matric)) {
            $oIptuender = $iptuenderRepository->getByMatricu($oItbiMatric->it06_matric);
        }

        $this->setDadosItbi("oIptuender", $oIptuender);

        $this->buscarCaracteristicasRural();

        return true;
    }

    /**
     * Busca os pagamentos que foram feitos para a guia selecionada
     * @return \App\Domain\Tributario\ITBI\Models\Itbinumpre[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    private function buscarPagamentoGuia()
    {
        $itbinumpreRepository = ItbinumpreRepository::getInstance();

        $aPagamentos = $itbinumpreRepository->getPagamentosGuia(
            $this->numeroGuia,
            null,
            ($this->migrado? "recibopaga" : "recibo")
        );

        if (count($aPagamentos) == 0) {
            return false;
        }

        if (!$this->migrado) {
            $oPagamento = $aPagamentos[0];


            $dataPagamento = isset($oPagamento->dtpago) ? $oPagamento->dtpago : $oPagamento->k00_dtpaga;
            $this->setDataPagamento($dataPagamento);
            $this->setNumeroDam("{$oPagamento->it15_numpre}/{$oPagamento->k00_numpar}");
        }

        return $aPagamentos;
    }

    /**
     * Busca os transmitentes e adquirentes principais da guia selecionada
     * @throws \Exception
     */
    private function buscarTransmitenteAdquirente()
    {
        $itbinomeRepository = new ItbinomeRepository();
        $aItbinome = $itbinomeRepository->getByGuia($this->numeroGuia);

        $aTransSecond = [];
        $aAdquiSecond = [];

        foreach ($aItbinome as $oItbinome) {
            if ($oItbinome->it03_tipo == "T") {
                if ($oItbinome->it03_princ == true || $oItbinome->it03_princ == "t") {
                    $this->setDadosItbi("oTransmitente", $oItbinome);
                } else {
                    $aTransSecond[] = $oItbinome->it03_nome;
                }
            } else {
                if ($oItbinome->it03_princ == true || $oItbinome->it03_princ == "t") {
                    $this->setDadosItbi("oAdquirente", $oItbinome);
                } else {
                    $aAdquiSecond[] = $oItbinome->it03_nome;
                }
            }
        }

        $this->setDadosItbi("aTransSecond", implode("; ", $aTransSecond));
        $this->setDadosItbi("aAdquiSecond", implode("; ", $aAdquiSecond));
    }

    /**
     * Busca o debito baixado com base no numpre e numpar
     * @return \stdClass
     * @throws \Exception
     */
    private function buscarBaixaDebito()
    {
        $oPagamento = $this->dadosItbi->aPagamentos[0];

        $disbancoRepository = new DisbancoRepository();

        $oDisbanco = $disbancoRepository->getBaixaByNumpreNumpar(
            $oPagamento->it15_numpre,
            $oPagamento->k00_numpar
        );

        return $oDisbanco;
    }

    /**
     * Busca as caracteristicas do imóvel rural
     */
    private function buscarCaracteristicasRural()
    {
        $itbiruralcaractRepository = new ItbiruralcaractRepository();

        $aCaracterDistrib = $itbiruralcaractRepository->getByGuia($this->numeroGuia, 1);
        $this->setDadosItbi("aCaracterDistrib", $aCaracterDistrib);

        $aCaracterUtil = $itbiruralcaractRepository->getByGuia($this->numeroGuia, 2);
        $this->setDadosItbi("aCaracterUtil", $aCaracterUtil);
    }
}
