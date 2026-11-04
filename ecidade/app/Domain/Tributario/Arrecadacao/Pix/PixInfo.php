<?php

namespace App\Domain\Tributario\Arrecadacao\Pix;

use App\Domain\Configuracao\Banco\Models\DBBancosPix;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\Arrecadacao\Pix\Bancos\Banco;
use \App\Domain\Tributario\Arrecadacao\Models\Recibobarpix;
use convenio;
use DBString;

abstract class PixInfo extends Banco
{
    /**
     * @var integer
     */
    private $environmentType;

    /**
     * @var string
     */
    private $appKey;

    /**
     * @var string
     */
    private $appUsername;

    /**
     * @var string
     */
    private $appPassword;

    /**
     * @var integer
     */
    private $covenantCode;

    /**
     * @var string
     */
    protected $cnpj;

    /**
     * @var boolean
     */
    private $countyCnpj = false;

    /**
     * @var string
     */
    private $pixKey;

    /**
     * @var array
     */
    private $baseAuthUrlList;

    /**
     * @var array
     */
    private $baseUrlList;

    /**
     * @return int
     */
    public function getEnvironmentType()
    {
        return $this->environmentType;
    }

    /**
     * @param int $environmentType
     * @return void
     */
    private function setEnvironmentType($environmentType)
    {
        $this->environmentType = $environmentType;
    }

    /**
     * @return string
     */
    public function getAppKey()
    {
        return $this->appKey;
    }

    /**
     * @param string $appKey
     * @return void
     */
    private function setAppKey($appKey)
    {
        $this->appKey = $appKey;
    }

    /**
     * @return string
     */
    public function getAppUsername()
    {
        return $this->appUsername;
    }

    /**
     * @param string $appUsername
     * @return void
     */
    private function setAppUsername($appUsername)
    {
        $this->appUsername = $appUsername;
    }

    /**
     * @return string
     */
    public function getAppPassword()
    {
        return $this->appPassword;
    }

    /**
     * @param string $appPassword
     * @return void
     */
    private function setAppPassword($appPassword)
    {
        $this->appPassword = $appPassword;
    }

    /**
     * @return int
     */
    public function getCovenantCode()
    {
        return $this->covenantCode;
    }

    /**
     * @param int $covenantCode
     * @return void
     */
    private function setCovenantCode($covenantCode)
    {
        $this->covenantCode = $covenantCode;
    }

    /**
     * @return string
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * @param string $cnpj
     * @return void
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
    }

    /**
     * @return bool
     */
    public function isCountyCnpj()
    {
        return (bool) $this->countyCnpj;
    }

    /**
     * @param bool $countyCnpj
     * @return void
     */
    private function setCountyCnpj($countyCnpj)
    {
        $this->countyCnpj = $countyCnpj;
    }

    /**
     * @return string
     */
    public function getPixKey()
    {
        return $this->pixKey;
    }

    /**
     * @param string $pixKey
     * @return void
     */
    private function setPixKey($pixKey)
    {
        $this->pixKey = $pixKey;
    }

    /**
     * @return array
     */
    public function getBaseAuthUrlList()
    {
        return $this->baseAuthUrlList;
    }

    /**
     * @param array $baseAuthUrlList
     * @return void
     */
    private function setBaseAuthUrlList($baseAuthUrlList)
    {
        $this->baseAuthUrlList = $baseAuthUrlList;
    }

    /**
     * @return array
     */
    public function getBaseUrlList()
    {
        return $this->baseUrlList;
    }

    /**
     * @param array $baseUrlList
     * @return void
     */
    private function setBaseUrlList($baseUrlList)
    {
        $this->baseUrlList = $baseUrlList;
    }

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        if ($this->getEnvironmentType() == 1) {
            return $this->getBaseUrlList()["production"];
        }

        return $this->getBaseUrlList()["sandbox"];
    }

    /**
     * @return string
     */
    protected function getBaseAuthUrl()
    {
        if ($this->getEnvironmentType() == 1) {
            return $this->getBaseAuthUrlList()["production"];
        }

        return $this->getBaseAuthUrlList()["sandbox"];
    }

    public function __construct($bankCode, $baseAuthUrlList, $baseUrlList)
    {
        $pixConfig = DBBancosPix::where("db90_codban", $bankCode)->first();

        if (!$pixConfig) {
            throw new \Exception("Pix não configurado para o Banco");
        }

        $this->setAppUsername($pixConfig->db90_login);
        $this->setAppPassword($pixConfig->db90_senha);
        $this->setAppKey($pixConfig->db90_chave_api);
        $this->setPixKey($pixConfig->db90_chave_pix);
        $this->setCovenantCode($pixConfig->db90_numconv);
        $this->setEnvironmentType($pixConfig->db90_tipo_ambiente);
        $this->setCountyCnpj($pixConfig->db90_cnpj_municipio);
        $this->setCnpj($pixConfig->db90_cnpj);

        $this->setBaseAuthUrlList($baseAuthUrlList);
        $this->setBaseUrlList($baseUrlList);
    }

    protected function savePixTransactionInfo(
        $dateCreated,
        $status,
        $solicitationConciliationCode,
        $version,
        $qrCodeLink,
        $qrCodeText,
        $bankCode
    ) {
        $recibobarpix = new Recibobarpix();
        $recibobarpix->k00_numpre = $this->getCodigoArrecadacao();
        $recibobarpix->k00_numpar = $this->getParcela();
        $recibobarpix->k00_codbar = $this->getCodigoBarras();
        $recibobarpix->k00_criacaosolicitacao = $dateCreated;
        $recibobarpix->k00_estadosolicitacao = $status;
        $recibobarpix->k00_conciliacaosolicitante = $solicitationConciliationCode;
        $recibobarpix->k00_numeroversaosolicitacaopagamento = $version;
        $recibobarpix->k00_linkqrcode = $qrCodeLink;
        $recibobarpix->k00_qrcode = $qrCodeText;
        $recibobarpix->k00_codban = $bankCode;
        $recibobarpix->save();
    }

    /**
     * @return bool
     */
    protected function canGeneratePix()
    {
        if (empty($this->cgm) || !$this->cgm instanceof Cgm) {
            return false;
        }

        if (empty($this->getCodigoBarras())) {
            return false;
        }

        if ($this->isCountyCnpj() === false && empty($this->cgm->z01_cgccpf)) {
            return false;
        }

        if (!DBString::isCNPJ($this->cgm->z01_cgccpf) && !DBString::isCPF($this->cgm->z01_cgccpf)) {
            return false;
        }

        return true;
    }
}
