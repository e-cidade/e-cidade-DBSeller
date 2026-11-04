<?php


namespace App\Domain\RecursosHumanos\Pessoal\Model\Previdencia;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TabelaPrevidencia
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Previdencia
 * @property int r33_codigo
 * @property int r33_anousu
 * @property int r33_mesusu
 * @property int r33_codtab
 * @property float r33_inic
 * @property float r33_fim
 * @property float r33_perc
 * @property float r33_deduzi
 * @property string r33_nome
 * @property string r33_tipo
 * @property string r33_rubmat
 * @property float r33_ppatro
 * @property string r33_rubsau
 * @property string r33_basfer
 * @property string r33_basfet
 * @property string r33_rubaci
 * @property float r33_tinati
 * @property int r33_instit
 * @property int r33_codele
 * @property string r33_rubprorrogacaomaternidade
 * @property string r33_rubfamiliar
 * @property string r33_rublicencapremio
 * @property int r33_tiposegregacao
*/
class TabelaPrevidencia extends Model
{
    protected $table = 'pessoal.inssirf';

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->r33_codigo;
    }

    /**
     * @param int $r33_codigo
     */
    public function setCodigo($r33_codigo)
    {
        $this->r33_codigo = $r33_codigo;
    }

    /**
     * @return int
     */
    public function getAno()
    {
        return $this->r33_anousu;
    }

    /**
     * @param int $r33_anousu
     */
    public function setAno($r33_anousu)
    {
        $this->r33_anousu = $r33_anousu;
    }

    /**
     * @return int
     */
    public function getMes()
    {
        return $this->r33_mesusu;
    }

    /**
     * @param int $r33_mesusu
     */
    public function setMes($r33_mesusu)
    {
        $this->r33_mesusu = $r33_mesusu;
    }

    /**
     * @return int
     */
    public function getCodigoTabela()
    {
        return $this->r33_codtab;
    }

    /**
     * @param int $r33_codtab
     */
    public function setCodigoTabela($r33_codtab)
    {
        $this->r33_codtab = $r33_codtab;
    }

    /**
     * @return float
     */
    public function getValorInicial()
    {
        return $this->r33_inic;
    }

    /**
     * @param float $r33_inic
     */
    public function setValorInicial($r33_inic)
    {
        $this->r33_inic = $r33_inic;
    }

    /**
     * @return float
     */
    public function getValorFinal()
    {
        return $this->r33_fim;
    }

    /**
     * @param float $r33_fim
     */
    public function setValorFinal($r33_fim)
    {
        $this->r33_fim = $r33_fim;
    }

    /**
     * @return float
     */
    public function getPercentual()
    {
        return $this->r33_perc;
    }

    /**
     * @param float $r33_perc
     */
    public function setPercentual($r33_perc)
    {
        $this->r33_perc = $r33_perc;
    }

    /**
     * @return float
     */
    public function getValorDeduzido()
    {
        return $this->r33_deduzi;
    }

    /**
     * @param float $r33_deduzi
     */
    public function setValorDeduzido($r33_deduzi)
    {
        $this->r33_deduzi = $r33_deduzi;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->r33_nome;
    }

    /**
     * @param string $r33_nome
     */
    public function setDescricao($r33_nome)
    {
        $this->r33_nome = $r33_nome;
    }

    /**
     * @return string
     */
    public function getTipo()
    {
        return $this->r33_tipo;
    }

    /**
     * @param string $r33_tipo
     */
    public function setTipo($r33_tipo)
    {
        $this->r33_tipo = $r33_tipo;
    }

    /**
     * @return string
     */
    public function getRubricaSalarioMaternidade()
    {
        return $this->r33_rubmat;
    }

    /**
     * @param string $r33_rubmat
     */
    public function setRubricaSalarioMaternidade($r33_rubmat)
    {
        $this->r33_rubmat = $r33_rubmat;
    }

    /**
     * @return float
     */
    public function getPercentualPatronal()
    {
        return $this->r33_ppatro;
    }

    /**
     * @param float $r33_ppatro
     */
    public function setPercentualPatronal($r33_ppatro)
    {
        $this->r33_ppatro = $r33_ppatro;
    }

    /**
     * @return string
     */
    public function getRubricaLicencaSaude()
    {
        return $this->r33_rubsau;
    }

    /**
     * @param string $r33_rubsau
     */
    public function setRubricaLicencaSaude($r33_rubsau)
    {
        $this->r33_rubsau = $r33_rubsau;
    }

    /**
     * @return string
     */
    public function getRubricaBaseFerias()
    {
        return $this->r33_basfer;
    }

    /**
     * @param string $r33_basfer
     */
    public function setRubricaBaseFerias($r33_basfer)
    {
        $this->r33_basfer = $r33_basfer;
    }

    /**
     * @return string
     */
    public function getRubricaBaseFeriasTotal()
    {
        return $this->r33_basfet;
    }

    /**
     * @param string $r33_basfet
     */
    public function setRubricaBaseFeriasTotal($r33_basfet)
    {
        $this->r33_basfet = $r33_basfet;
    }

    /**
     * @return string
     */
    public function getRubricaAcidenteTrabalho()
    {
        return $this->r33_rubaci;
    }

    /**
     * @param string $r33_rubaci
     */
    public function setRubricaAcidenteTrabalhp($r33_rubaci)
    {
        $this->r33_rubaci = $r33_rubaci;
    }

    /**
     * @return float
     */
    public function getTetoInativos()
    {
        return $this->r33_tinati;
    }

    /**
     * @param float $r33_tinati
     */
    public function setTetoInativos($r33_tinati)
    {
        $this->r33_tinati = $r33_tinati;
    }

    /**
     * @return int
     */
    public function getCodigoInstituicao()
    {
        return $this->r33_instit;
    }

    /**
     * @param int $r33_instit
     */
    public function setCodigoInstituicao($r33_instit)
    {
        $this->r33_instit = $r33_instit;
    }

    /**
     * @return int
     */
    public function getCodigoElemento()
    {
        return $this->r33_codele;
    }

    /**
     * @param int $r33_codele
     */
    public function setCodigoElemento($r33_codele)
    {
        $this->r33_codele = $r33_codele;
    }

    /**
     * @return string
     */
    public function getRubricaProrrogacaoMaternidade()
    {
        return $this->r33_rubprorrogacaomaternidade;
    }

    /**
     * @param string $r33_rubprorrogacaomaternidade
     */
    public function setRubricaProrrogacaoMaternidade($r33_rubprorrogacaomaternidade)
    {
        $this->r33_rubprorrogacaomaternidade = $r33_rubprorrogacaomaternidade;
    }

    /**
     * @return string
     */
    public function getRubricaCuidarFamiliar()
    {
        return $this->r33_rubfamiliar;
    }

    /**
     * @param string $r33_rubfamiliar
     */
    public function setRubricaCuidarFamiliar($r33_rubfamiliar)
    {
        $this->r33_rubfamiliar = $r33_rubfamiliar;
    }
    /**
     * @return string
     */
    public function getRubricaLicencaPremio()
    {
        return $this->r33_rublicencapremio;
    }

    /**
     * @param string $r33_rublicencapremio
     */
    public function setRubricaLicencaPremio($r33_rublicencapremio)
    {
        $this->r33_rublicencapremio = $r33_rublicencapremio;
    }

    /**
     * @return int
     */
    public function getTipoSegregacao()
    {
        return $this->r33_tiposegregacao;
    }

    /**
     * @param int $r33_tiposegregacao
     */
    public function setTipoSegregacao($r33_tiposegregacao)
    {
        $this->r33_tiposegregacao = $r33_tiposegregacao;
    }
}
