<?php

namespace App\Domain\Tributario\ITBI\Models;

use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @property int|null $it01_protprocesso
 * @property int|null $it01_cartorioextra
 * @property string $it01_dtprocesso
 * @property string $it01_tituprocesso
 * @property int $it01_processo
 * @property string $it01_notificado
 * @property float $it01_percentualareatransmitida
 * @property string $it01_envia
 * @property float $it01_valorconstr
 * @property float $it01_valorterreno
 * @property int $it01_coddepto
 * @property int $it01_id_usuario
 * @property int $it01_origem
 * @property string $it01_finalizado
 * @property string $it01_mail
 * @property float $it01_areatrans
 * @property float $it01_valortransacao
 * @property string $it01_obs
 * @property float $it01_areaedificada
 * @property float $it01_areaterreno
 * @property int $it01_tipotransacao
 * @property string $it01_hora
 * @property string $it01_data
 * @property int $it01_guia
 */
class Itbi extends Model
{
    protected $table = "itbi";

    protected $primaryKey = "it01_guia";
    protected $fillable = [
        "it01_guia",
        "it01_data",
        "it01_hora",
        "it01_tipotransacao",
        "it01_areaterreno",
        "it01_areaedificada",
        "it01_obs",
        "it01_valortransacao",
        "it01_areatrans",
        "it01_mail",
        "it01_finalizado",
        "it01_origem",
        "it01_id_usuario",
        "it01_coddepto",
        "it01_valorterreno",
        "it01_valorconstr",
        "it01_envia",
        "it01_percentualareatransmitida",
        "it01_notificado",
        "it01_processo",
        "it01_tituprocesso",
        "it01_dtprocesso",
        "it01_cartorioextra",
        "it01_protprocesso"
    ];


    /**
     * @return int
     */
    public function getGuia()
    {
        return $this->it01_guia;
    }

    /**
     * @param int $guia
     * @return Itbi
     */
    public function setGuia($guia)
    {
        $this->it01_guia = $guia;
        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->it01_data;
    }

    /**
     * @param string $data
     * @return Itbi
     */
    public function setData($data)
    {
        $this->it01_data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getHora()
    {
        return $this->it01_hora;
    }

    /**
     * @param string $hora
     * @return Itbi
     */
    public function setHora($hora)
    {
        $this->it01_hora = $hora;
        return $this;
    }

    /**
     * @return int
     */
    public function getTipotransacao()
    {
        return $this->it01_tipotransacao;
    }

    /**
     * @param int $tipotransacao
     * @return Itbi
     */
    public function setTipotransacao($tipotransacao)
    {
        $this->it01_tipotransacao = $tipotransacao;
        return $this;
    }

    /**
     * @return float
     */
    public function getAreaterreno()
    {
        return $this->it01_areaterreno;
    }

    /**
     * @param float $areaterreno
     * @return Itbi
     */
    public function setAreaterreno($areaterreno)
    {
        $this->it01_areaterreno = $areaterreno;
        return $this;
    }

    /**
     * @return float
     */
    public function getAreaedificada()
    {
        return $this->it01_areaedificada;
    }

    /**
     * @param float $areaedificada
     * @return Itbi
     */
    public function setAreaedificada($areaedificada)
    {
        $this->it01_areaedificada = $areaedificada;
        return $this;
    }

    /**
     * @return string
     */
    public function getObs()
    {
        return $this->it01_obs;
    }

    /**
     * @param string $obs
     * @return Itbi
     */
    public function setObs($obs)
    {
        $this->it01_obs = $obs;
        return $this;
    }

    /**
     * @return float
     */
    public function getValortransacao()
    {
        return $this->it01_valortransacao;
    }

    /**
     * @param float $valortransacao
     * @return Itbi
     */
    public function setValortransacao($valortransacao)
    {
        $this->it01_valortransacao = $valortransacao;
        return $this;
    }

    /**
     * @return float
     */
    public function getAreatrans()
    {
        return $this->it01_areatrans;
    }

    /**
     * @param float $areatrans
     * @return Itbi
     */
    public function setAreatrans($areatrans)
    {
        $this->it01_areatrans = $areatrans;
        return $this;
    }

    /**
     * @return string
     */
    public function getMail()
    {
        return $this->it01_mail;
    }

    /**
     * @param string $mail
     * @return Itbi
     */
    public function setMail($mail)
    {
        $this->it01_mail = $mail;
        return $this;
    }

    /**
     * @return string
     */
    public function getFinalizado()
    {
        return $this->it01_finalizado;
    }

    /**
     * @param string $finalizado
     * @return Itbi
     */
    public function setFinalizado($finalizado)
    {
        $this->it01_finalizado = $finalizado;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrigem()
    {
        return $this->it01_origem;
    }

    /**
     * @param int $origem
     * @return Itbi
     */
    public function setOrigem($origem)
    {
        $this->it01_origem = $origem;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdUsuario()
    {
        return $this->it01_id_usuario;
    }

    /**
     * @param int $id_usuario
     * @return Itbi
     */
    public function setIdUsuario($id_usuario)
    {
        $this->it01_id_usuario = $id_usuario;
        return $this;
    }

    /**
     * @return int
     */
    public function getCoddepto()
    {
        return $this->it01_coddepto;
    }

    /**
     * @param int $coddepto
     * @return Itbi
     */
    public function setCoddepto($coddepto)
    {
        $this->it01_coddepto = $coddepto;
        return $this;
    }

    /**
     * @return float
     */
    public function getValorterreno()
    {
        return $this->it01_valorterreno;
    }

    /**
     * @param float $valorterreno
     * @return Itbi
     */
    public function setValorterreno($valorterreno)
    {
        $this->it01_valorterreno = $valorterreno;
        return $this;
    }

    /**
     * @return float
     */
    public function getValorconstr()
    {
        return $this->it01_valorconstr;
    }

    /**
     * @param float $valorconstr
     * @return Itbi
     */
    public function setValorconstr($valorconstr)
    {
        $this->it01_valorconstr = $valorconstr;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnvia()
    {
        return $this->it01_envia;
    }

    /**
     * @param string $envia
     * @return Itbi
     */
    public function setEnvia($envia)
    {
        $this->it01_envia = $envia;
        return $this;
    }

    /**
     * @return float
     */
    public function getPercentualareatransmitida()
    {
        return $this->it01_percentualareatransmitida;
    }

    /**
     * @param float $percentualareatransmitida
     * @return Itbi
     */
    public function setPercentualareatransmitida($percentualareatransmitida)
    {
        $this->it01_percentualareatransmitida = $percentualareatransmitida;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotificado()
    {
        return $this->it01_notificado;
    }

    /**
     * @param string $notificado
     * @return Itbi
     */
    public function setNotificado($notificado)
    {
        $this->it01_notificado = $notificado;
        return $this;
    }

    /**
     * @return int
     */
    public function getProcesso()
    {
        return $this->it01_processo;
    }

    /**
     * @param int $processo
     * @return Itbi
     */
    public function setProcesso($processo)
    {
        $this->it01_processo = $processo;
        return $this;
    }

    /**
     * @return string
     */
    public function getTituprocesso()
    {
        return $this->it01_tituprocesso;
    }

    /**
     * @param string $tituprocesso
     * @return Itbi
     */
    public function setTituprocesso($tituprocesso)
    {
        $this->it01_tituprocesso = $tituprocesso;
        return $this;
    }

    /**
     * @return string
     */
    public function getDtprocesso()
    {
        return $this->it01_dtprocesso;
    }

    /**
     * @param string $dtprocesso
     * @return Itbi
     */
    public function setDtprocesso($dtprocesso)
    {
        $this->it01_dtprocesso = $dtprocesso;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCartorioextra()
    {
        return $this->it01_cartorioextra;
    }

    /**
     * @param integer $cartorioextra
     * @return Itbi
     */
    public function setCartorioextra($cartorioextra)
    {
        $this->it01_cartorioextra = $cartorioextra;
        return $this;
    }

    /**
     * @return integer $it01_protprocesso
     */
    public function getProcessoSequencial()
    {
        return $this->it01_protprocesso;
    }

    /**
     * @param int $processoSequencial
     *
     * @return $this
     */
    public function setProcessoSequencial($processoSequencial)
    {
        $this->it01_protprocesso =  $processoSequencial;
        return $this;
    }

    public function processo()
    {
        return $this->hasOne(Processo::class, "p58_codproc", "it01_protprocesso");
    }
}
