<?php

namespace ECidade\Financeiro\Contabilidade\PlanoDeContas;

use DBEstrutura;

/**
 * Classe responsável pela formatação do estrutural da Receita no plano padrão.
 * Assim como as outras classes (EstruturalReceita) recebe o estrutural tanto formatado ou não e fornece os mesmos
 * métodos.
 *
 * A diferença mais gritante, é que as contas de dedução, o 9 não conta como um nível.
 */
class EstruturalReceitaPadrao extends EstruturalPadrao implements EstruturalReceitaFormatter
{
    protected $deducao = false;
    protected $mascaraPadrao = '0.0.0.0.00.0.0.00.00.00';
    protected $mascaraDeducao = '0.0.0.0.0.00.0.0.00.00.00';

    public function __construct($estrutural)
    {
        $this->estrutural = str_replace('.', '', $estrutural);

        if ((int)substr($this->estrutural, 0, 1) === 9 &&
            (int)substr($this->estrutural, 0, 3) !== 999) {
            $this->deducao = true;
        }

        // contas de dedução tem um dígito a mais no início
        $length = $this->deducao ? 15 : 14;
        $this->estrutural = str_pad($this->estrutural, $length, '0', STR_PAD_RIGHT);
    }

    /**
     * Retorna o estrutural com a máscara
     * @return String
     */
    public function getEstruturalComMascara()
    {
        $mascara = $this->deducao ? $this->mascaraDeducao : $this->mascaraPadrao;
        return DBEstrutura::mascararString($mascara, $this->estrutural);
    }

    /**
     * Retorna o nível da conta.
     * Adicionado uma exceção para a conta: "99900000 - Recursos Arrecadados em Exercícios Anteriores"
     * Essa conta existe apenas no plano da união e não tem conta pai. Ela é analítica e não tem sintética.
     * @return int
     */
    public function getNivel()
    {
        // validação devido à conta da união 99900000
        if ((int)substr($this->estrutural, 0, 3) === 999) {
            return 1;
        }
        $nivelRetorno = parent::getNivel();
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
     * Retorna se é um estrutural de dedução.
     * @return bool|mixed
     */
    public function isDeducao()
    {
        return $this->deducao;
    }
}
