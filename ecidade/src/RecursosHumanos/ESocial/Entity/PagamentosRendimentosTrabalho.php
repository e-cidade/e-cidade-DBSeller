<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                    www.dbseller.com.br
 *                 e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */
namespace ECidade\RecursosHumanos\ESocial\Entity;

/**
 * Class PagamentosRendimentosTrabalho
 * @package ECidade\RecursosHumanos\ESocial\Entity
 */

class PagamentosRendimentosTrabalho
{
    /**
     * @var int
     */
    const AVALIACAO = 3000041;
    /**
     * @var string
     */
    private $cpfBeneficiente;
    /**
     * @var array
     */
    private $pagamentos = array();

    /**
     * @var float
     */
    private $valorIRRFDependentes;

    /**
     * @var array
     */
    private $codigoReceita;

    /**
     * @var DateTime
     */
    private $dataLaudoMolestia;

    /**
     * @var bool
     */
    private $portadorMolestica;

    /**
     * @var Servidor
     */
    private $servidor;

    /**
     * @var array
     */
    private $dependentesNaoCadastrado;
 
    /**
     * @var array
     */
    private $rendimentoTributavelDependente;
    
    /**
     * @var array
     */
    private $pensaoAlimenticia;

    /**
     * @var array
     */
    private $previdenciaComplementar;

    /**
     * @var array
     */
    private $processoRetencaoJudicial;

    /**
     * @var array
     */
    private $planoSaudeColetivo;

    /**
     * @return string
     */
    public function getCPFBeneficiente()
    {
        return $this->cpfBeneficiente;
    }

    /**
     * @param string $cpfBeneficiente
     */
    public function setCPFBeneficiente($cpfBeneficiente)
    {
        $this->cpfBeneficiente = $cpfBeneficiente;
    }

    /**
     * @return array
     */
    public function getPagamentos()
    {
        return $this->pagamentos;
    }

    /**
     * @param array $pagamentos
     */
    public function setPagamentos($pagamentos)
    {
        $this->pagamentos = $pagamentos;
    }

    /**
     * @return DateTime
     */
    public function getDataLaudoMolestia()
    {
        return $this->dataLaudoMolestia;
    }

    /**
     * @param string $dataLaudoMolestia
     */
    public function setDataLaudoMolestia($dataLaudoMolestia)
    {
        $this->dataLaudoMolestia = $dataLaudoMolestia;
    }

    /**
     * @return string
     */
    public function getPortadorMolestica()
    {
        return $this->portadorMolestica;
    }

    /**
     * @param string $portadorMolestica
     */
    public function setPortadorMolestica($portadorMolestica)
    {
        $this->portadorMolestica = $portadorMolestica;
    }

    /**
     * @return Servidor
     */
    public function getServidor()
    {
        return $this->servidor;
    }

    /**
     * @param Servidor $servidor
     */
    public function setServidor($servidor)
    {
        $this->servidor = $servidor;
    }

    /**
     * @return array
     */
    public function getDependentesNaoCadastrado()
    {
        return $this->dependentesNaoCadastrado;
    }

    /**
     * @param $dependentesNaoCadastrado
     */
    public function setDependentesNaoCadastrado($dependentesNaoCadastrado)
    {
        $this->dependentesNaoCadastrado = $dependentesNaoCadastrado;
    }

    /**
     * Get the value of rendimentoTributavelDependente
     *
     * @return  array
     */
    public function getRendimentoTributavelDependente()
    {
        return $this->rendimentoTributavelDependente;
    }

    /**
     * Set the value of rendimentoTributavelDependente
     *
     * @param  array  $rendimentoTributavelDependente
     *
     * @return  self
     */
    public function setRendimentoTributavelDependente($rendimentoTributavelDependente)
    {
        $this->rendimentoTributavelDependente = $rendimentoTributavelDependente;
    }

    /**
     * Get the value of pensaoAlimenticia
     *
     * @return  array
     */
    public function getPensaoAlimenticia()
    {
        return $this->pensaoAlimenticia;
    }

    /**
     * Set the value of pensaoAlimenticia
     *
     * @param  array  $pensaoAlimenticia
     */
    public function setPensaoAlimenticia($pensaoAlimenticia)
    {
        $this->pensaoAlimenticia = $pensaoAlimenticia;
    }

    /**
     * Get the value of previdenciaComplementar
     *
     * @return  array
     */
    public function getPrevidenciaComplementar()
    {
        return $this->previdenciaComplementar;
    }

    /**
     * Set the value of previdenciaComplementar
     *
     * @param  array  $previdenciaComplementar
     */
    public function setPrevidenciaComplementar($previdenciaComplementar)
    {
        $this->previdenciaComplementar = $previdenciaComplementar;
    }

    /**
     * Get the value of processoRetencaoJudicial
     *
     * @return  array
     */
    public function getProcessoRetencaoJudicial()
    {
        return $this->processoRetencaoJudicial;
    }

    /**
     * Set the value of processoRetencaoJudicial
     *
     * @param  array  $processoRetencaoJudicial
     */
    public function setProcessoRetencaoJudicial($processoRetencaoJudicial)
    {
        $this->processoRetencaoJudicial = $processoRetencaoJudicial;
    }

    /**
     * Get the value of planoSaudeColetivo
     *
     * @return  array
     */
    public function getPlanoSaudeColetivo()
    {
        return $this->planoSaudeColetivo;
    }

    /**
     * Set the value of planoSaudeColetivo
     *
     * @param  array  $planoSaudeColetivo
     */
    public function setPlanoSaudeColetivo($planoSaudeColetivo)
    {
        $this->planoSaudeColetivo = $planoSaudeColetivo;
    }

    /**
     * Get the value of codigoReceita
     *
     * @return  array
     */
    public function getCodigoReceita()
    {
        return $this->codigoReceita;
    }

    /**
     * Set the value of codigoReceita
     *
     * @param  array  $codigoReceita
     */
    public function setCodigoReceita($codigoReceita)
    {
        $this->codigoReceita = $codigoReceita;
    }
}
