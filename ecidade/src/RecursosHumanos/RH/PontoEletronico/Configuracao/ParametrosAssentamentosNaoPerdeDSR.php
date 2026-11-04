<?php
/*
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

namespace ECidade\RecursosHumanos\RH\PontoEletronico\Configuracao;

class ParametrosAssentamentosNaoPerdeDSR
{

    /**
     * @var \Instituicao
     * @access private
     */
    private $instituicao;

    /**
     * @var \TipoAssentamento[]
     * @access private
     */
    private $tiposAssentamento = array();

    /**
     * @param \Instituicao $instituicao
     * @return self
     */
    public function setInstituicao(\Instituicao $instituicao)
    {
        $this->instituicao = $instituicao;
        return $this;
    }

    /**
     * @return \Instituicao
     */
    public function getInstituicao()
    {
        return $this->instituicao;
    }

    /**
     * @param \TipoAssentamento[] $tipoAssentamento
     * @return self
     */
    public function setTiposAssentamento($tipoAssentamento)
    {
        $this->tiposAssentamento = $tipoAssentamento;
        return $this;
    }

    /**
     * @return \TipoAssentamento[]
     */
    public function getTiposAssentamento()
    {
        return $this->tiposAssentamento;
    }

    /**
     * @return array
     */
    public function getCodigosTiposAssentamento()
    {
        $codigosTiposAssentamento = array();

        if (!empty($this->tiposAssentamento)) {
            foreach ($this->tiposAssentamento as $tipoAssentamento) {
                $codigosTiposAssentamento[] = (int)$tipoAssentamento->getSequencial();
            }
        }
        
        return $codigosTiposAssentamento;
    }

    /**
     * @param \TipoAssentamento $tipoAssentamento
     * @return self
     */
    public function addTipoAssentamento($tipoAssentamento)
    {
        $this->tiposAssentamento[] = $tipoAssentamento;
        return $this;
    }
}
