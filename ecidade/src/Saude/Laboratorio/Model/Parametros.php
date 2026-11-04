<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
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

namespace ECidade\Saude\Laboratorio\Model;

/**
 * Class Parametros
 * @package ECidade\Saude\Laboratorio\Model
 */
class Parametros
{
    const INTEGRACAO_LUCKMANN = 1;
    const INTEGRACAO_INFINITY = true;

    /**
     * @var int
     */
    private $codigo;

    /**
     * @var string
     */
    private $estrutural;

    /**
     * @var boolean
     */
    private $liberaExamesDuplos;

    /**
     * @var int
     */
    private $modeloColetaAmostra;

    /**
     * @var int
     */
    private $integracao;

    /**
     * @var bool
     */
    private $numeroControleInterno = false;

    /**
     * @var int
     */
    private $modeloLaudo;

    /**
     * @var bool
     */
    private $exibeLiberacaoPorExame = false;

    /**
     * @var double
     */
    private $espacoLinhas;

    /**
     * @var double
     */
    private $tamanhoFonte;

    /**
     * @var bool
     */
    private $integracaoInfinity = false;

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param int $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * @return string
     */
    public function getEstrutural()
    {
        return $this->estrutural;
    }

    /**
     * @param string $estrutural
     */
    public function setEstrutural($estrutural)
    {
        $this->estrutural = $estrutural;
    }

    /**
     * @return bool
     */
    public function isLiberaExamesDuplos()
    {
        return $this->liberaExamesDuplos;
    }

    /**
     * @param bool $liberaExamesDuplos
     */
    public function setLiberaExamesDuplos($liberaExamesDuplos)
    {
        $this->liberaExamesDuplos = $liberaExamesDuplos;
    }

    /**
     * @return int
     */
    public function getModeloColetaAmostra()
    {
        return $this->modeloColetaAmostra;
    }

    /**
     * @param int $modeloColetaAmostra
     */
    public function setModeloColetaAmostra($modeloColetaAmostra)
    {
        $this->modeloColetaAmostra = $modeloColetaAmostra;
    }

    /**
     * @return int
     */
    public function getIntegracao()
    {
        return $this->integracao;
    }

    /**
     * @param int $integracao
     */
    public function setIntegracao($integracao)
    {
        $this->integracao = $integracao;
    }

    /**
     * @return bool
     */
    public function isNumeroControleInterno()
    {
        return $this->numeroControleInterno;
    }

    /**
     * @param bool $numeroControleInterno
     */
    public function setNumeroControleInterno($numeroControleInterno)
    {
        $this->numeroControleInterno = $numeroControleInterno;
    }

    /**
     * @return int
     */
    public function getModeloLaudo()
    {
        return $this->modeloLaudo;
    }

    /**
     * @param int $modeloLaudo
     */
    public function setModeloLaudo($modeloLaudo)
    {
        $this->modeloLaudo = $modeloLaudo;
    }

    /**
     * @return bool
     */
    public function isExibeLiberacaoPorExame()
    {
        return $this->exibeLiberacaoPorExame;
    }

    /**
     * @param bool $exibeLiberacaoPorExame
     */
    public function setExibeLiberacaoPorExame($exibeLiberacaoPorExame)
    {
        $this->exibeLiberacaoPorExame = $exibeLiberacaoPorExame;
    }

    public function getEspacoLinhas()
    {
        return $this->espacoLinhas;
    }

    public function setEspacoLinhas($espacoLinhas)
    {
        $this->espacoLinhas = $espacoLinhas;
    }

    public function getTamanhoFonte()
    {
        return $this->tamanhoFonte;
    }

    public function setTamanhoFonte($tamanhoFonte)
    {
        $this->tamanhoFonte = $tamanhoFonte;
    }

    /**
     * @return bool
     */
    public function getIntegracaoInfinity()
    {
        return $this->integracaoInfinity;
    }

    /**
     * @param bool $integracaoInfinity
     */
    public function setIntegracaoInfinity($integracaoInfinity)
    {
        $this->integracaoInfinity = $integracaoInfinity;
    }
}
