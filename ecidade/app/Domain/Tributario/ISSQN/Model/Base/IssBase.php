<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\IssBase
 *
 * @property int $q02_inscr
 * @property int $q02_numcgm
 * @property string|null $q02_memo
 * @property string|null $q02_tiplic
 * @property string|null $q02_regjuc
 * @property string|null $q02_inscmu
 * @property string|null $q02_obs
 * @property string|null $q02_dtcada
 * @property string|null $q02_dtinic
 * @property string|null $q02_dtbaix
 * @property float|null $q02_capit
 * @property string|null $q02_cep
 * @property string|null $q02_dtjunta
 * @property string|null $q02_ultalt
 * @property string|null $q02_dtalt
 * @property int|null $q02_formalocalvara
 * @property string|null $q02_protocolojuntacomercial
 * @property-read Cgm $cgm
 * @mixin \Eloquent
 */
class IssBase extends Model
{
    protected $table = 'issbase';
    protected $primaryKey = 'q02_inscr';
    public $timestamps = false;
    public static $snakeAttributes = false;

    protected $fillable = [
        'q02_inscr',
        'q02_numcgm',
        'q02_memo',
        'q02_tiplic',
        'q02_regjuc',
        'q02_inscmu',
        'q02_obs',
        'q02_dtcada',
        'q02_dtinic',
        'q02_dtbaix',
        'q02_capit',
        'q02_cep',
        'q02_dtjunta',
        'q02_ultalt',
        'q02_dtalt',
        'q02_formalocalvara',
        'q02_protocolojuntacomercial'
    ];

    public function cgm()
    {
        return $this->hasOne(Cgm::class, "z01_numcgm", "q02_numcgm");
    }

    /**
     * @return int
     */
    public function getInscr()
    {
        return $this->q02_inscr;
    }

    /**
     * @param int $q02_inscr
     */
    public function setInscr($q02_inscr)
    {
        $this->q02_inscr = $q02_inscr;
    }

    /**
     * @return int
     */
    public function getNumcgm()
    {
        return $this->q02_numcgm;
    }

    /**
     * @param int $q02_numcgm
     */
    public function setNumcgm($q02_numcgm)
    {
        $this->q02_numcgm = $q02_numcgm;
    }

    /**
     * @return string
     */
    public function getMemo()
    {
        return $this->q02_memo;
    }

    /**
     * @param string $q02_memo
     */
    public function setMemo($q02_memo)
    {
        $this->q02_memo = $q02_memo;
    }

    /**
     * @return string
     */
    public function getTiplic()
    {
        return $this->q02_tiplic;
    }

    /**
     * @param string $q02_tiplic
     */
    public function setTiplic($q02_tiplic)
    {
        $this->q02_tiplic = $q02_tiplic;
    }

    /**
     * @return string
     */
    public function getRegjuc()
    {
        return $this->q02_regjuc;
    }

    /**
     * @param string $q02_regjuc
     */
    public function setRegjuc($q02_regjuc)
    {
        $this->q02_regjuc = $q02_regjuc;
    }

    /**
     * @return string
     */
    public function getInscmu()
    {
        return $this->q02_inscmu;
    }

    /**
     * @param string $q02_inscmu
     */
    public function setInscmu($q02_inscmu)
    {
        $this->q02_inscmu = $q02_inscmu;
    }

    /**
     * @return string
     */
    public function getObs()
    {
        return $this->q02_obs;
    }

    /**
     * @param string $q02_obs
     */
    public function setObs($q02_obs)
    {
        $this->q02_obs = $q02_obs;
    }

    /**
     * @return string
     */
    public function getDtcada()
    {
        return $this->q02_dtcada;
    }

    /**
     * @param string $q02_dtcada
     */
    public function setDtcada($q02_dtcada)
    {
        $this->q02_dtcada = $q02_dtcada;
    }

    /**
     * @return string
     */
    public function getDtinic()
    {
        return $this->q02_dtinic;
    }

    /**
     * @param string $q02_dtinic
     */
    public function setDtinic($q02_dtinic)
    {
        $this->q02_dtinic = $q02_dtinic;
    }

    /**
     * @return string
     */
    public function getDtbaix()
    {
        return $this->q02_dtbaix;
    }

    /**
     * @param string $q02_dtbaix
     */
    public function setDtbaix($q02_dtbaix)
    {
        $this->q02_dtbaix = $q02_dtbaix;
    }

    /**
     * @return float
     */
    public function getCapit()
    {
        return $this->q02_capit;
    }

    /**
     * @param float $q02_capit
     */
    public function setCapit($q02_capit)
    {
        $this->q02_capit = $q02_capit;
    }

    /**
     * @return string
     */
    public function getCep()
    {
        return $this->q02_cep;
    }

    /**
     * @param string $q02_cep
     */
    public function setCep($q02_cep)
    {
        $this->q02_cep = $q02_cep;
    }

    /**
     * @return string
     */
    public function getDtjunta()
    {
        return $this->q02_dtjunta;
    }

    /**
     * @param string $q02_dtjunta
     */
    public function setDtjunta($q02_dtjunta)
    {
        $this->q02_dtjunta = $q02_dtjunta;
    }

    /**
     * @return string
     */
    public function getUltalt()
    {
        return $this->q02_ultalt;
    }

    /**
     * @param string $q02_ultalt
     */
    public function setUltalt($q02_ultalt)
    {
        $this->q02_ultalt = $q02_ultalt;
    }

    /**
     * @return string
     */
    public function getDtalt()
    {
        return $this->q02_dtalt;
    }

    /**
     * @param string $q02_dtalt
     */
    public function setDtalt($q02_dtalt)
    {
        $this->q02_dtalt = $q02_dtalt;
    }

    /**
     * @return int
     */
    public function getFormalocalvara()
    {
        return $this->q02_formalocalvara;
    }

    /**
     * @param int $q02_formalocalvara
     */
    public function setFormalocalvara($q02_formalocalvara)
    {
        $this->q02_formalocalvara = $q02_formalocalvara;
    }

    /**
     * @return string
     */
    public function getProtocolojuntacomercial()
    {
        return $this->q02_protocolojuntacomercial;
    }

    /**
     * @param string $q02_protocolojuntacomercial
     */
    public function setProtocolojuntacomercial($q02_protocolojuntacomercial)
    {
        $this->q02_protocolojuntacomercial = $q02_protocolojuntacomercial;
    }
}
