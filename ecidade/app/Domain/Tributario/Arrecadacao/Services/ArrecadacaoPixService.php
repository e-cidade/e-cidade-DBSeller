<?php

namespace App\Domain\Tributario\Arrecadacao\Services;

use App\Domain\Patrimonial\Protocolo\Repository\CgmRepository;
use App\Domain\Tributario\Arrecadacao\Models\Arretipo;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\Arrecadacao\Models\Arretipopix;
use App\Domain\Tributario\Arrecadacao\Models\Arretipopixasso;
use App\Domain\Tributario\Arrecadacao\Models\Modcarnepadraopix;
use App\Domain\Tributario\Arrecadacao\Models\Recibobarpix;
use App\Domain\Tributario\Arrecadacao\Models\Recibopaga;
use App\Domain\Tributario\Arrecadacao\Pix\Bancos\BancoFactory;
use App\Domain\Tributario\Arrecadacao\Pix\Bancos\BancoDoBrasil;
use App\Domain\Tributario\Arrecadacao\Pix\Bancos\Banrisul;
use App\Domain\Tributario\Arrecadacao\Repositories\RecibobarpixRepository;
use Exception;
use recibo;
use regraEmissao;
use convenio;
use Illuminate\Support\Facades\DB;

final class ArrecadacaoPixService
{
    protected $codigo_arrecadacao;
    protected $parcela_arrecadacao = 0;
    protected $convenio;
    protected $modelo;
    protected $parcelainicio;
    protected $parcelafim;
    protected $tipo_debito;
    protected $vencimento;
    protected $config;

    private $instit  = null;
    private $ip      = null;
    private $datausu = null;

    /**
     * @var convenio
     */
    protected $modelConvenio;

    /**
     * @var recibo
     */
    protected $modelRecibo;

    /**
     * @var Cgm
     */
    protected $modelCgm;

    /**
     * @var Arretipo
     */
    protected $tipoDebito;

    public function seInstit($value)
    {
        $this->instit = $value;
    }

    public function setIp($value)
    {
        $this->ip = $value;
    }

    public function setDatausu($value)
    {
        $this->datausu = $value;
    }

    public function gerarPix()
    {
        if (empty($this->getCodigoArrecadacao())) {
            throw new Exception("Recibo não informado!", 500);
        }

        $regraemissao = new regraEmissao(
            $this->getTipoDebito(),
            $this->getModelo(),
            \db_getsession('DB_instit'),
            date("Y-m-d", \db_getsession("DB_datausu")),
            \db_getsession('DB_ip'),
            true,
            false,
            $this->getParcelaInicio(),
            $this->getParcelaFim()
        );

        if (!self::validaEmissaoPix(
            $this->getTipoDebito(),
            $regraemissao,
            $this->getCodigoArrecadacao()
        )) {
            return false;
        }

        $recibo = new recibo(
            null,
            null,
            1,
            $this->getCodigoArrecadacao()
        );

        $recibo->setTipoEmissao(2);


        $repositoryCgm = new CgmRepository();
        $cgm = $repositoryCgm->getByNumcgm($recibo->getCgm());

        $this->tipoDebito = Arretipo::where(
            'k00_tipo',
            $this->getTipoDebito()
        )->first();

        $valorRecibo = $recibo->getTotalRecibo();
        $valorCodigoBarras = str_pad(
            number_format((float)$valorRecibo, 2, '', ''),
            11,
            0,
            STR_PAD_LEFT
        );

        $convenio = new convenio(
            $regraemissao->getConvenio(),
            $recibo->getNumpreRecibo(),
            0,
            $valorRecibo,
            $valorCodigoBarras,
            $this->getVencimento(),
            $this->tipoDebito->k00_tercdigrecnormal
        );
        $this->modelConvenio = $convenio;
        $this->modelRecibo = $recibo;
        $this->modelCgm = $cgm;

        return $this->geraPixBanco($convenio, $cgm, $valorRecibo);
    }

    public function geraPixCarne(convenio $convenio, $numpre, $numpar, $numcgm, $valor, $codigoBarras = null)
    {
        $repositoryCgm = new CgmRepository();
        $cgm = $repositoryCgm->getByNumcgm($numcgm);

        $this->setCodigoArrecadacao($numpre);
        $this->setParcelaArrecadacao($numpar);

        $this->geraPixBanco($convenio, $cgm, $valor, $codigoBarras);
    }

    /**
     * @throws \BusinessException
     * @throws Exception
     */
    protected function geraPixBanco($convenio, Cgm $cgm, $valor, $codigoBarras = null)
    {
        if ($this->validaEmissaoReciboBarPix($codigoBarras ? $codigoBarras : $convenio->getCodigoBarra())) {
            return;
        }

        $arretipopix = Arretipopix::query()->where("k00_tipo", $this->getTipoDebito())->first();
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

        //$tipoPixBanco = $this->getBancoEmissao();
        //$banco = BancoFactory::getByBanco($tipoPixBanco->db90_codban);

        $banco->setConvenio($convenio);
        $banco->setCodigoBarras($codigoBarras ? $codigoBarras : $convenio->getCodigoBarra());
        $banco->setCgm($cgm);
        $banco->setValor($valor);
        $banco->setCodigoArrecadacao($this->getCodigoArrecadacao());
        $banco->setParcela($this->getParcelaArrecadacao());
        $banco->setVencimento($this->getVencimento());

        $banco->gerarPix();

        return true;
    }

    protected function validaEmissaoReciboBarPix($codigoBarras)
    {
        $repository = new RecibobarpixRepository();
        return $repository->getByCodBar($codigoBarras);
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

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function setParcelaInicio($parcelainicio)
    {
        $this->parcelainicio = $parcelainicio;
    }

    public function getParcelaInicio()
    {
        return $this->parcelainicio;
    }

    public function setParcelaFim($parcelafim)
    {
        $this->parcelafim = $parcelafim;
    }

    public function getParcelaFim()
    {
        return $this->parcelafim;
    }

    public function setTipoDebito($tipo_debito)
    {
        $this->tipo_debito = $tipo_debito;
    }

    public function getTipoDebito()
    {
        return $this->tipo_debito;
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

    public static function validaEmissaoPix($tipoDebito, regraEmissao $regraemissao, $numpre)
    {
        $codigoModelo = $regraemissao->getModCarnePadrao();

        $regraEmissaoPix = Modcarnepadraopix::where([
            'k48_sequencial' => $codigoModelo,
            'k48_ammpix' =>  true
        ])->first();

        $tipoDebito = Arretipopix::where([
            'k00_tipo' => $tipoDebito,
            'modsistema' => true
        ])->first();

        if ($tipoDebito && $regraEmissaoPix) {
            if (!empty($tipoDebito->dtini) && !empty($tipoDebito->dtfim)) {
                $dadosReciboPaga = Recibopaga::where(["k00_numnov" => $numpre])
                    ->where("k00_dtvenc", "<", "'{$tipoDebito->dtini}'")
                    ->where("k00_dtvenc", ">", "'{$tipoDebito->dtfim}'")
                    ->get();

                if (!$dadosReciboPaga->isEmpty()) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     *
     * @return array
     */
    public function build()
    {
        $repository = new RecibobarpixRepository();
        $pix = $repository->getByCodBar($this->modelConvenio->getCodigoBarra());

        if (empty($pix)) {
            $sMsg  = "Não foi possivel se comunicar com api do pix, ";
            $sMsg .= "pois o cadastro encontra-se desatualizado. ";
            $sMsg .= "Regularize o cadastro junto a Secretaria de Fazenda do Município.";

            throw new \Exception($sMsg);
        }

        $imagemQRCode = "tmp/pix_arrecadacao_{$this->codigo_arrecadacao}" . time() . ".png";
        $url = $pix->k00_qrcode;
        \PHPQRCode\QRcode::png($url, $imagemQRCode, 'L', 4, 2);

        return [
            'url_qrcode'       => ECIDADE_REQUEST_PATH . $imagemQRCode,
            'codigo_barras'    => $this->modelConvenio->getCodigoBarra(),
            'linha_digitavel'  => $this->modelConvenio->getLinhaDigitavel(),
            'link_qrcode'      => $pix->k00_linkqrcode,
            'qrcode'           => $pix->k00_qrcode,
            'data_criacao'     => $pix->k00_criacaosolicitacao,
            'tipo_debito'      => $this->tipoDebito->k00_tipo,
            'descr_debito'     => $this->tipoDebito->k00_descr,
            'valor_debito'     => $this->modelRecibo->getTotalRecibo(),
            'data_vencimento'  => $this->modelRecibo->getDataVencimento(),
            'numero_documento' => $this->modelRecibo->getNumpreRecibo(),
            'nome_devedor'     => $this->modelCgm->z01_nome,
            'debitos'          => $this->buildDebitos()
        ];
    }

    private function buildDebitos()
    {
        $sql = <<<SQL
        select
            k00_numpre as debito,
            k00_numpar as parcela,
            k00_hist,
            sum(k00_valor) as valor_debito,
            (
                select
                    j20_anousu
                from
                    iptunump
                where
                    j20_numpre = k00_numpre
                union
                all
                select
                    v01_exerc
                from
                    divida
                where
                    v01_numpre = k00_numpre
                union
                all
                select
                    dv05_exerc
                from
                    diversos
                where
                    dv05_numpre = k00_numpre
            ) as exercicio,
            fc_origem_numpre(k00_numpre, 1, '') as origem
        from
            recibopaga
        where
            k00_numnov = {$this->modelRecibo->getNumpreRecibo()}
        group by
            1,
            2,
            3
        order by k00_hist, k00_numpar;
SQL;
        return DB::select($sql);
    }

    /**
     * @return Arretipopixasso
     */
    protected function getBancoEmissao()
    {
        if (empty($this->getTipoDebito())) {
            throw new \Exception('Tipo De Débito Para Geração do Pix Não Informado');
        }

        $tipodebitoBanco = Arretipopixasso::where([
            "k00_tipo" => $this->getTipoDebito()
        ])->first();

        return $tipodebitoBanco;
    }
}
