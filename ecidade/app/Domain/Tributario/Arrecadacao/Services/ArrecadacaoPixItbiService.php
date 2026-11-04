<?php

namespace App\Domain\Tributario\Arrecadacao\Services;

use App\Domain\Tributario\Arrecadacao\Repositories\RecibobarpixRepository;
use App\Domain\Tributario\Arrecadacao\Models\Arretipopixbancogeracao;
use App\Domain\Tributario\Arrecadacao\Pix\Bancos\BancoDoBrasil;
use App\Domain\Patrimonial\Protocolo\Repository\CgmRepository;
use App\Domain\Tributario\Arrecadacao\Pix\Bancos\Banrisul;
use App\Domain\Tributario\Arrecadacao\Models\Arretipopix;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use regraEmissao;
use Exception;
use DateTime;
use recibo;

final class ArrecadacaoPixItbiService
{
    private $codigo_arrecadacao;
    private $parcela_arrecadacao = 0;
    private $recibo;
    private $convenio;
    private $tipo_debito;
    private $vencimento;
    private $regraEmissao;
    private $cgm;
    private $valorRecibo;
    private $numpreRecibo;
    private $codigoBarras;

    public function gerarPix()
    {
        $repositoryCgm = new CgmRepository();
        $cgm = $repositoryCgm->getByNumcgm($this->getCgm());
        $valorRecibo = $this->getValorRecibo();

        $this->geraPixBanco($this->getConvenio(), $cgm, $valorRecibo);
    }

    /**
     * @throws \BusinessException
     * @throws Exception
     */
    private function geraPixBanco($convenio, Cgm $cgm, $valor, $codigoBarras = null)
    {
        if ($this->validaEmissaoReciboBarPix($codigoBarras ? $codigoBarras : $convenio->getCodigoBarra())) {
            return;
        }

        $arretipopix = Arretipopix::query()->where("k00_tipo", $this->getTipoDebito())->first();

        if (!isset($arretipopix)) {
            return;
        }

        if (!$this->validaPossuiRegistroPixBanco((object)$arretipopix->getOriginal())) {
            return;
        }

        $arretipopixbancogeracaoService = new ArretipopixbancogeracaoService();
        $bankCode = $arretipopixbancogeracaoService->chooseBankToGeneratePix($arretipopix, true, false);

        switch ($bankCode) {
            case BancoDoBrasil::BANK_CODE:
                $banco = new BancoDoBrasil();
                break;
            case Banrisul::BANK_CODE:
                $banco = new Banrisul();
                break;
            default:
                throw new \BusinessException("Verifique as configurações do PIX, banco {$bankCode} não configurado.");
        }

        $dataVencimento = new DateTime($this->getVencimento());
        $dataAtual = new DateTime();
        $dataVencimento = $dataVencimento < $dataAtual ? $dataAtual : $dataVencimento;

        $this->setCodigoBarras($codigoBarras ? $codigoBarras : $convenio->getCodigoBarra());

        $banco->setConvenio($convenio);
        $banco->setCodigoBarras($this->getCodigoBarras());
        $banco->setCgm($cgm);
        $banco->setValor($valor);
        $banco->setCodigoArrecadacao($this->getNumpreRecibo());
        $banco->setParcela($this->getParcelaArrecadacao());
        $banco->setVencimento($dataVencimento->format('Y-m-d'));
        $banco->gerarPix();
    }

    /**
     * @param Arretipopix|null $arretipopix
     * @return bool
     */
    private function validaPossuiRegistroPixBanco($arretipopix)
    {
        if (!isset($arretipopix) || !property_exists($arretipopix, 'codtipopix')) {
            return false;
        }

        $registrosArreTipoPixBancoGeracao = Arretipopixbancogeracao::query()
            ->where("k213_arretipopix", $arretipopix->codtipopix)
            ->get([
                "k213_sequencial"
            ])->toArray();

        return count($registrosArreTipoPixBancoGeracao) > 0;
    }

    protected function validaEmissaoReciboBarPix($codigoBarras)
    {
        $repository = new RecibobarpixRepository();
        return $repository->getByCodBar($codigoBarras);
    }

    /**
     * @param integer $codigoBarras
     */
    public function setCodigoBarras($codigoBarras)
    {
        $this->codigoBarras = $codigoBarras;
    }


    /**
     * @return integer
     */
    public function getCodigoBarras()
    {
        return $this->codigoBarras;
    }

    /**
     * @param float
     */
    public function setValorRecibo($valor)
    {
        $this->valorRecibo = $valor;
    }

    /**
     * @return float
     */
    public function getValorRecibo()
    {
        return $this->valorRecibo;
    }

    /**
     * @param Recibo $recibo
     */
    public function setRecibo($recibo)
    {
        $this->recibo = $recibo;
    }

    /**
     * @return Recibo
     */
    public function getRecibo()
    {
        return $this->recibo;
    }

    /**
     * @param integer $cgm
     */
    public function setCgm($cgm)
    {
        $this->cgm = $cgm;
    }

    /**
     * @return integer
     */
    public function getCgm()
    {
        return $this->cgm;
    }

    /**
     * @param integer $numpre
     */
    public function setNumpreRecibo($numpre)
    {
        $this->numpreRecibo = $numpre;
    }

    /**
     * @return integer $numpre
     */
    public function getNumpreRecibo()
    {
        return $this->numpreRecibo;
    }

    public function setCodigoArrecadacao($codigo_arrecadacao)
    {
        $this->codigo_arrecadacao = $codigo_arrecadacao;
    }

    public function getCodigoArrecadacao()
    {
        return $this->codigo_arrecadacao;
    }

    public function setConvenio($convenio)
    {
        $this->convenio = $convenio;
    }

    public function getConvenio()
    {
        return $this->convenio;
    }

    public function setVencimento($vencimento)
    {
        $this->vencimento = $vencimento;
    }

    public function getVencimento()
    {
        return $this->vencimento;
    }

    public function getParcelaArrecadacao()
    {
        return $this->parcela_arrecadacao;
    }

    public function setParcelaArrecadacao($parcela_arrecadacao)
    {
        $this->parcela_arrecadacao = $parcela_arrecadacao;
    }

    public function setTipoDebito($tipo_debito)
    {
        $this->tipo_debito = $tipo_debito;
    }

    public function getTipoDebito()
    {
        return $this->tipo_debito;
    }

    /**
     * @param regraEmissao $regraEmissao
     * @return void
     */
    public function setRegraEmissao($regraEmissao)
    {
        $this->regraEmissao = $regraEmissao;
    }

    public function getRegraEmissao()
    {
        return $this->regraEmissao;
    }
}
