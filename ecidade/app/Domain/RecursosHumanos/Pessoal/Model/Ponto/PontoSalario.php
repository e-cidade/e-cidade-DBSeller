<?php


namespace App\Domain\RecursosHumanos\Pessoal\Model\Ponto;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PontoBase
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Ponto
 * @property int r10_anousu
 * @property int r10_mesusu
 * @property string r10_regist
 * @property int r10_rubric
 * @property float r10_valor
 * @property float r10_quant
 * @property string r10_lotac
 * @property string r10_datlim
 * @property int r10_instit
 */
class PontoSalario extends Model
{
    protected $table = 'pessoal.pontofs';
    /**
     * @return int
     */
    public function getAno()
    {
        return $this->r10_anousu;
    }

    /**
     * @param int $r10_anousu
     */
    public function setAno($r10_anousu)
    {
        $this->r10_anousu = $r10_anousu;
    }

    /**
     * @return int
     */
    public function getMes()
    {
        return $this->r10_mesusu;
    }

    /**
     * @param int $r10_mesusu
     */
    public function setMes($r10_mesusu)
    {
        $this->r10_mesusu = $r10_mesusu;
    }

    /**
     * @return string
     */
    public function getMatricula()
    {
        return $this->r10_regist;
    }

    /**
     * @param string $r10_regist
     */
    public function setMatricula($r10_regist)
    {
        $this->r10_regist = $r10_regist;
    }

    /**
     * @return int
     */
    public function getRubrica()
    {
        return $this->r10_rubric;
    }

    /**
     * @param int $r10_rubric
     */
    public function setRubrica($r10_rubric)
    {
        $this->r10_rubric = $r10_rubric;
    }

    /**
     * @return float
     */
    public function getValor()
    {
        return $this->r10_valor;
    }

    /**
     * @param float $r10_valor
     */
    public function setValor($r10_valor)
    {
        $this->r10_valor = $r10_valor;
    }

    /**
     * @return float
     */
    public function getQuantidade()
    {
        return $this->r10_quant;
    }

    /**
     * @param float $r10_quant
     */
    public function setQuantidade($r10_quant)
    {
        $this->r10_quant = $r10_quant;
    }

    /**
     * @return string
     */
    public function getLotacao()
    {
        return $this->r10_lotac;
    }

    /**
     * @param string $r10_lotac
     */
    public function setLotacao($r10_lotac)
    {
        $this->r10_lotac = $r10_lotac;
    }

    /**
     * @return string
     */
    public function getDataLimite()
    {
        return $this->r10_datlim;
    }

    /**
     * @param string $r10_datlim
     */
    public function setDataLimite($r10_datlim)
    {
        $this->r10_datlim = $r10_datlim;
    }

    /**
     * @return int
     */
    public function getInstitituicao()
    {
        return $this->r10_instit;
    }

    /**
     * @param int $r10_instit
     */
    public function setInstituicao($r10_instit)
    {
        $this->r10_instit = $r10_instit;
    }

    /**
     * @param array $options
     * @return bool|void
     */
    public function save(array $options = [])
    {
        $daoPontoSalario = new \cl_pontofs();
        $daoPontoSalario->r10_anousu = $this->r10_anousu;
        $daoPontoSalario->r10_mesusu = $this->r10_mesusu;
        $daoPontoSalario->r10_regist = $this->r10_regist;
        $daoPontoSalario->r10_rubric = $this->r10_rubric;
        $daoPontoSalario->r10_valor = $this->r10_valor;
        $daoPontoSalario->r10_quant = $this->r10_quant;
        $daoPontoSalario->r10_lotac = $this->r10_lotac;
        $daoPontoSalario->r10_datlim = $this->r10_datlim;
        $daoPontoSalario->r10_instit = $this->r10_instit;

        $pontoSalario = PontoSalario::where([
            'r10_regist' => $this->r10_regist,
            'r10_anousu' => $this->r10_anousu,
            'r10_mesusu' => $this->r10_mesusu,
            'r10_rubric' => $this->r10_rubric
        ])->first();

        if (empty($pontoSalario) || isset($options['forceInsert'])) {
            return $daoPontoSalario->incluir(
                $this->r10_anousu,
                $this->r10_mesusu,
                $this->r10_regist,
                $this->r10_rubric
            );
        } else {
            return $daoPontoSalario->alterar(
                $this->r10_anousu,
                $this->r10_mesusu,
                $this->r10_regist,
                $this->r10_rubric
            );
        }
    }

    public function delete()
    {
        if (empty($this->r10_anousu)) {
            throw new \Exception('Não é possível excluir o registro do ponto, pois o ano não está preenchido');
        }
        if (empty($this->r10_mesusu)) {
            throw new \Exception('Não é possível excluir o registro do ponto, pois o mês não está preenchido');
        }
        if (empty($this->r10_regist)) {
            throw new \Exception('Não é possível excluir o registro do ponto, pois a matrícula não está preenchida');
        }
        if (empty($this->r10_rubric)) {
            throw new \Exception('Não é possível excluir o registro do ponto, pois a rubrica não está preenchida');
        }

        $daoPontoSalario = new \cl_pontofs();
        return $daoPontoSalario->excluir($this->r10_anousu, $this->r10_mesusu, $this->r10_regist, $this->r10_rubric);
    }
}
