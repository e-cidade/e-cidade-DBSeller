<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\v2023;

use ECidade\Core\Mappers\ParseArray;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;

class Receita extends ParseArray implements LayoutPad
{
    protected $dePara = [
        "Codigo da Receita Orcamentaria - SG" => "fonte",
        "Codigo do orgao + Unid. Orcamentaria" => "orgaoUnidade",
        "Receita Realizada - Janeiro" => "realizadaJaneiro",
        "Receita Realizada - Fevereiro" => "realizadaFevereiro",
        "Receita Realizada - Marco" => "realizadaMarco",
        "Receita Realizada - Abril" => "realizadaAbril",
        "Receita Realizada - Maio" => "realizadaMaio",
        "Receita Realizada - Junho" => "realizadaJunho",
        "Receita Realizada - Julho" => "realizadaJulho",
        "Receita Realizada - Agosto" => "realizadaAgosto",
        "Receita Realizada - Setembro" => "realizadaSetembro",
        "Receita Realizada - Outubro" => "realizadaOutubro",
        "Receita Realizada - Novembro" => "realizadaNovembro",
        "Receita Realizada - Dezembro" => "realizadaDezembro",
        "Meta de Arrecadacao - 1° bimestre" => "meta1Bimestre",
        "Meta de Arrecadacao - 2° bimestre" => "meta2Bimestre",
        "Meta de Arrecadacao - 3° bimestre" => "meta3Bimestre",
        "Meta de Arrecadacao - 4° bimestre" => "meta4Bimestre",
        "Meta de Arrecadacao - 5° bimestre" => "meta5Bimestre",
        "Meta de Arrecadacao - 6° bimestre" => "meta6Bimestre",
        "Caracteristica Peculiar" => "cp",
        "Campo Obsoleto1" => "campoObsoleto1",
        "Campo Obsoleto2" => "campoObsoleto2",
        "Codigo da Fonte de Recurso" => "fonteRecurso",
        "Codigo de Acompanhamento da Execucao Orcamentaria CO" => "complemento",
    ];

    protected $fonte;
    protected $orgaoUnidade;
    protected $realizadaJaneiro;
    protected $realizadaFevereiro;
    protected $realizadaMarco;
    protected $realizadaAbril;
    protected $realizadaMaio;
    protected $realizadaJunho;
    protected $realizadaJulho;
    protected $realizadaAgosto;
    protected $realizadaSetembro;
    protected $realizadaOutubro;
    protected $realizadaNovembro;
    protected $realizadaDezembro;
    protected $meta1Bimestre;
    protected $meta2Bimestre;
    protected $meta3Bimestre;
    protected $meta4Bimestre;
    protected $meta5Bimestre;
    protected $meta6Bimestre;
    protected $cp;
    protected $campoObsoleto1 = '0000';
    protected $campoObsoleto2 = '0000';
    protected $fonteRecurso;
    protected $complemento;

    /**
     * @return mixed
     */
    public function getFonte()
    {
        return $this->fonte;
    }

    /**
     * @param mixed $fonte
     */
    public function setFonte($fonte)
    {
        $this->fonte = $fonte;
    }

    /**
     * @return mixed
     */
    public function getOrgaoUnidade()
    {
        return $this->orgaoUnidade;
    }

    /**
     * @param mixed $orgaoUnidade
     */
    public function setOrgaoUnidade($orgaoUnidade)
    {
        $this->orgaoUnidade = $orgaoUnidade;
    }

    /**
     * @return mixed
     */
    public function getRealizadaJaneiro()
    {
        return $this->realizadaJaneiro;
    }

    /**
     * @param mixed $realizadaJaneiro
     */
    public function setRealizadaJaneiro($realizadaJaneiro)
    {
        $this->realizadaJaneiro = $realizadaJaneiro;
    }

    /**
     * @return mixed
     */
    public function getRealizadaFevereiro()
    {
        return $this->realizadaFevereiro;
    }

    /**
     * @param mixed $realizadaFevereiro
     */
    public function setRealizadaFevereiro($realizadaFevereiro)
    {
        $this->realizadaFevereiro = $realizadaFevereiro;
    }

    /**
     * @return mixed
     */
    public function getRealizadaMarco()
    {
        return $this->realizadaMarco;
    }

    /**
     * @param mixed $realizadaMarco
     */
    public function setRealizadaMarco($realizadaMarco)
    {
        $this->realizadaMarco = $realizadaMarco;
    }

    /**
     * @return mixed
     */
    public function getRealizadaAbril()
    {
        return $this->realizadaAbril;
    }

    /**
     * @param mixed $realizadaAbril
     */
    public function setRealizadaAbril($realizadaAbril)
    {
        $this->realizadaAbril = $realizadaAbril;
    }

    /**
     * @return mixed
     */
    public function getRealizadaMaio()
    {
        return $this->realizadaMaio;
    }

    /**
     * @param mixed $realizadaMaio
     */
    public function setRealizadaMaio($realizadaMaio)
    {
        $this->realizadaMaio = $realizadaMaio;
    }

    /**
     * @return mixed
     */
    public function getRealizadaJunho()
    {
        return $this->realizadaJunho;
    }

    /**
     * @param mixed $realizadaJunho
     */
    public function setRealizadaJunho($realizadaJunho)
    {
        $this->realizadaJunho = $realizadaJunho;
    }

    /**
     * @return mixed
     */
    public function getRealizadaJulho()
    {
        return $this->realizadaJulho;
    }

    /**
     * @param mixed $realizadaJulho
     */
    public function setRealizadaJulho($realizadaJulho)
    {
        $this->realizadaJulho = $realizadaJulho;
    }

    /**
     * @return mixed
     */
    public function getRealizadaAgosto()
    {
        return $this->realizadaAgosto;
    }

    /**
     * @param mixed $realizadaAgosto
     */
    public function setRealizadaAgosto($realizadaAgosto)
    {
        $this->realizadaAgosto = $realizadaAgosto;
    }

    /**
     * @return mixed
     */
    public function getRealizadaSetembro()
    {
        return $this->realizadaSetembro;
    }

    /**
     * @param mixed $realizadaSetembro
     */
    public function setRealizadaSetembro($realizadaSetembro)
    {
        $this->realizadaSetembro = $realizadaSetembro;
    }

    /**
     * @return mixed
     */
    public function getRealizadaOutubro()
    {
        return $this->realizadaOutubro;
    }

    /**
     * @param mixed $realizadaOutubro
     */
    public function setRealizadaOutubro($realizadaOutubro)
    {
        $this->realizadaOutubro = $realizadaOutubro;
    }

    /**
     * @return mixed
     */
    public function getRealizadaNovembro()
    {
        return $this->realizadaNovembro;
    }

    /**
     * @param mixed $realizadaNovembro
     */
    public function setRealizadaNovembro($realizadaNovembro)
    {
        $this->realizadaNovembro = $realizadaNovembro;
    }

    /**
     * @return mixed
     */
    public function getRealizadaDezembro()
    {
        return $this->realizadaDezembro;
    }

    /**
     * @param mixed $realizadaDezembro
     */
    public function setRealizadaDezembro($realizadaDezembro)
    {
        $this->realizadaDezembro = $realizadaDezembro;
    }

    /**
     * @return mixed
     */
    public function getMeta1Bimestre()
    {
        return $this->meta1Bimestre;
    }

    /**
     * @param mixed $meta1Bimestre
     */
    public function setMeta1Bimestre($meta1Bimestre)
    {
        $this->meta1Bimestre = $meta1Bimestre;
    }

    /**
     * @return mixed
     */
    public function getMeta2Bimestre()
    {
        return $this->meta2Bimestre;
    }

    /**
     * @param mixed $meta2Bimestre
     */
    public function setMeta2Bimestre($meta2Bimestre)
    {
        $this->meta2Bimestre = $meta2Bimestre;
    }

    /**
     * @return mixed
     */
    public function getMeta3Bimestre()
    {
        return $this->meta3Bimestre;
    }

    /**
     * @param mixed $meta3Bimestre
     */
    public function setMeta3Bimestre($meta3Bimestre)
    {
        $this->meta3Bimestre = $meta3Bimestre;
    }

    /**
     * @return mixed
     */
    public function getMeta4Bimestre()
    {
        return $this->meta4Bimestre;
    }

    /**
     * @param mixed $meta4Bimestre
     */
    public function setMeta4Bimestre($meta4Bimestre)
    {
        $this->meta4Bimestre = $meta4Bimestre;
    }

    /**
     * @return mixed
     */
    public function getMeta5Bimestre()
    {
        return $this->meta5Bimestre;
    }

    /**
     * @param mixed $meta5Bimestre
     */
    public function setMeta5Bimestre($meta5Bimestre)
    {
        $this->meta5Bimestre = $meta5Bimestre;
    }

    /**
     * @return mixed
     */
    public function getMeta6Bimestre()
    {
        return $this->meta6Bimestre;
    }

    /**
     * @param mixed $meta6Bimestre
     */
    public function setMeta6Bimestre($meta6Bimestre)
    {
        $this->meta6Bimestre = $meta6Bimestre;
    }

    /**
     * @return mixed
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * @param mixed $cp
     */
    public function setCp($cp)
    {
        $this->cp = $cp;
    }

    /**
     * @return mixed
     */
    public function getCampoObsoleto1()
    {
        return $this->campoObsoleto1;
    }

    /**
     * @param string $campoObsoleto1
     */
    public function setCampoObsoleto1($campoObsoleto1)
    {
        $this->campoObsoleto1 = $campoObsoleto1;
    }

    /**
     * @return mixed
     */
    public function getCampoObsoleto2()
    {
        return $this->campoObsoleto2;
    }

    /**
     * @param string $campoObsoleto2
     */
    public function setCampoObsoleto2($campoObsoleto2)
    {
        $this->campoObsoleto2 = $campoObsoleto2;
    }

    /**
     * @return mixed
     */
    public function getFonteRecurso()
    {
        return $this->fonteRecurso;
    }

    /**
     * @param mixed $fonteRecurso
     */
    public function setFonteRecurso($fonteRecurso)
    {
        $this->fonteRecurso = $fonteRecurso;
    }

    /**
     * @return mixed
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * @param mixed $complemento
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
    }

    public function toArray()
    {
        return [
            'fonte' => $this->getFonte(),
            'orgaoUnidade' => $this->getOrgaoUnidade(),
            'realizadaJaneiro' => $this->getRealizadaJaneiro(),
            'realizadaFevereiro' => $this->getRealizadaFevereiro(),
            'realizadaMarco' => $this->getRealizadaMarco(),
            'realizadaAbril' => $this->getRealizadaAbril(),
            'realizadaMaio' => $this->getRealizadaMaio(),
            'realizadaJunho' => $this->getRealizadaJunho(),
            'realizadaJulho' => $this->getRealizadaJulho(),
            'realizadaAgosto' => $this->getRealizadaAgosto(),
            'realizadaSetembro' => $this->getRealizadaSetembro(),
            'realizadaOutubro' => $this->getRealizadaOutubro(),
            'realizadaNovembro' => $this->getRealizadaNovembro(),
            'realizadaDezembro' => $this->getRealizadaDezembro(),
            'meta1Bimestre' => $this->getMeta1Bimestre(),
            'meta2Bimestre' => $this->getMeta2Bimestre(),
            'meta3Bimestre' => $this->getMeta3Bimestre(),
            'meta4Bimestre' => $this->getMeta4Bimestre(),
            'meta5Bimestre' => $this->getMeta5Bimestre(),
            'meta6Bimestre' => $this->getMeta6Bimestre(),
            'cp' => $this->getCp(),
            'campoObsoleto1' => $this->getCampoObsoleto1(),
            'campoObsoleto2' => $this->getCampoObsoleto2(),
            'fonteRecurso' => $this->getFonteRecurso(),
            'complemento' => $this->getComplemento(),
        ];
    }
}
