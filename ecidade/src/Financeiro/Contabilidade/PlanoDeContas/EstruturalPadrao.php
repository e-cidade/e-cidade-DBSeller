<?php

namespace ECidade\Financeiro\Contabilidade\PlanoDeContas;

use DBEstrutura;

abstract class EstruturalPadrao
{
    protected $estrutural;
    protected $mascaraPadrao;

    public function __construct($estrutural)
    {
        $this->estrutural = str_replace('.', '', $estrutural);
        $this->estrutural = str_pad($this->estrutural, 15, '0', STR_PAD_RIGHT);
    }

    /**
     * Retorna o estrutural sem formatação
     * @return string
     */
    public function getEstrutural()
    {
        return $this->estrutural;
    }

    /**
     * Retorna o estrutural com a máscara
     * @return String
     */
    public function getEstruturalComMascara()
    {
        return DBEstrutura::mascararString($this->mascaraPadrao, $this->estrutural);
    }

    /**
     * @return int
     */
    public function getNivel()
    {
        $estrutural = $this->getEstruturalComMascara();
        $parts = array_reverse(explode('.', $estrutural));
        $nivelRetorno = 0;
        foreach ($parts as $nivelEstrutural) {
            $tamanhoNivel = strlen($nivelEstrutural);
            if ($nivelEstrutural == str_repeat('0', $tamanhoNivel) && $nivelRetorno == 0) {
                continue;
            }

            $nivelRetorno++;
        }


        return $nivelRetorno;
    }

    /**
     * @return string
     */
    public function getEstruturalAteNivel()
    {
        $partesEstrutural = explode(".", $this->getEstruturalComMascara());
        $nivel = $this->getNivel();

        return implode('', array_slice($partesEstrutural, 0, $nivel));
    }

    /**
     * Retorna o estrutural pai do estrutural atual.
     * Se o estrutural for o 1º nível, retorna null
     * @return string|null
     */
    public function getCodigoEstruturalPai()
    {
        $parts = explode(".", $this->getEstruturalComMascara());
        $nivel = $this->getNivel() - 1;

        if ($nivel === 0) {
            return null;
        }

        $iTamanho = strlen($parts[$nivel]);
        $parts[$nivel] = str_repeat('0', $iTamanho);
        return implode(".", $parts);
    }

    /**
     * Retorna o estrutural pai.
     * Se estrutural não possuir estrutural pai, retorna false
     * @return $this|false
     */
    public function getEstruturalPai()
    {
        $estrutural = $this->getCodigoEstruturalPai();
        if (is_null($estrutural)) {
            return false;
        }
        return new static($estrutural);
    }
}
