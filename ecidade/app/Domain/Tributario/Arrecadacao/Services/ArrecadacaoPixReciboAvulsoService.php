<?php

namespace App\Domain\Tributario\Arrecadacao\Services;

use App\Domain\Tributario\Arrecadacao\Repositories\RecibobarpixRepository;
use App\Domain\Tributario\Arrecadacao\Models\Arretipopixbancogeracao;
use App\Domain\Patrimonial\Protocolo\Repository\CgmRepository;
use App\Domain\Tributario\Arrecadacao\Pix\Bancos\BancoDoBrasil;
use App\Domain\Tributario\Arrecadacao\Pix\Bancos\Banrisul;
use App\Domain\Tributario\Arrecadacao\Models\Arretipopix;
use Exception;
use DateTime;
use convenio;

final class ArrecadacaoPixReciboAvulsoService
{
    /** @var integer */
    private $parcela_arrecadacao = 0;

    /** @var convenio */
    private $convenio;

    /** @var integer */
    private $tipo_debito;

    /** @var string */
    private $vencimento;

    /** @var integer */
    private $cgm;

    /** @var float */
    private $valorRecibo;

    /** @var integer */
    private $numpreRecibo;

    /** @var integer */
    private $codigoBarras;

    /**
     * @return void
     * @throws \BusinessException
     * @throws Exception
     */
    public function gerarPix()
    {
        $repositoryCgm = new CgmRepository();
        $cgm = $repositoryCgm->getByNumcgm($this->getCgm());
        $convenio = $this->getConvenio();
        $valor = $this->getValorRecibo();

        if ($this->validaEmissaoReciboBarPix($convenio->getCodigoBarra())) {
            return;
        }

        $arretipopix = Arretipopix::query()->where("k00_tipo", $this->getTipoDebito())->first();

        if (!$this->validaPossuiRegistroPixBanco($arretipopix)) {
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

        $this->setCodigoBarras($convenio->getCodigoBarra());

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
        if (isset($arretipopix)) {
            $registrosArreTipoPixBancoGeracao = Arretipopixbancogeracao::query()
                ->where("k213_arretipopix", $arretipopix->codtipopix)
                ->get([
                    "k213_sequencial"
                ])->toArray();

            return count($registrosArreTipoPixBancoGeracao) > 0;
        }
        return false;
    }

    /**
     * @param string $codigobarras
     * @return Recibobarpix|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    protected function validaEmissaoReciboBarPix($codigoBarras)
    {
        $repository = new RecibobarpixRepository();
        return $repository->getByCodBar($codigoBarras);
    }

    /**
     * @param integer $codigoBarras
     * @return void
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
     * @return void
     */
    public function setValorRecibo($valor)
    {
        $this->valorRecibo = $valor;
    }

    /**
     * @return float
     * @return void
     */
    public function getValorRecibo()
    {
        return $this->valorRecibo;
    }

    /**
     * @param integer $cgm
     * @return void
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
     * @return void
     */
    public function setNumpreRecibo($numpre)
    {
        $this->numpreRecibo = $numpre;
    }

    /**
     * @return integer
     */
    public function getNumpreRecibo()
    {
        return $this->numpreRecibo;
    }

    /**
     * @param convenio $convenio
     * @return void
     */
    public function setConvenio($convenio)
    {
        $this->convenio = $convenio;
    }

    /**
     * @return convenio
     */
    public function getConvenio()
    {
        return $this->convenio;
    }

    /**
     * @param string $vencimento
     * @return void
     */
    public function setVencimento($vencimento)
    {
        $this->vencimento = $vencimento;
    }

    /**
     * @return string
     */
    public function getVencimento()
    {
        return $this->vencimento;
    }

    /**
     * @return integer
     */
    public function getParcelaArrecadacao()
    {
        return $this->parcela_arrecadacao;
    }

    /**
     * @param integer $parcela_arrecadacao
     * @return void
     */
    public function setParcelaArrecadacao($parcela_arrecadacao)
    {
        $this->parcela_arrecadacao = $parcela_arrecadacao;
    }

    /**
     * @param integer $tipo_debito
     * @return void
     */
    public function setTipoDebito($tipo_debito)
    {
        $this->tipo_debito = $tipo_debito;
    }

    /**
     * @return integer
     */
    public function getTipoDebito()
    {
        return $this->tipo_debito;
    }
}
