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

namespace ECidade\Tributario\Arrecadacao\Custas\Service;

use ECidade\Tributario\Arrecadacao\Custas\Interfaces\Service;
use ECidade\Tributario\Arrecadacao\Custas\Service\Emissao;

/**
 * Classe com finalidade de receber iniciais ou numpres (com ou sem parcerlamento) e gerar recibos com custa.
 * Essa geração é via requisição externa do e-cidade
 */
final class EmissaoExterna implements Service
{
    /**
     * @var Emissao
     */
    private $emissao;

    public function __construct()
    {
        $this->emissao = new Emissao();

        $this->emissao->setExterno(true);
    }

    /**
     * @return \Recibo[]
     */
    public function processar()
    {
        return $this->emissao->processar();
    }

    public function setIniciais($iniciais)
    {
        $this->emissao->setIniciais($iniciais);
    }

    public function setNumpres($numpres)
    {
        $this->emissao->setNumpres($numpres);
    }

    public function setCarne($carne)
    {
        $this->emissao->setCarne($carne);
    }

    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->emissao->setCodigoInstituicao($codigoInstituicao);
    }

    public function setCgm($codigoCgm)
    {
        $this->emissao->setCgm($codigoCgm);
    }

    /**
     * @param integer $matricula
     * @return void
     */
    public function setMatricula($matricula)
    {
        $this->emissao->setMatricula($matricula);
    }

    /**
     * @param integer $inscricao
     * @return void
     */
    public function setInscricao($inscricao)
    {
        $this->emissao->setInscricao($inscricao);
    }

    /**
     * @param bool $descontoQuitarTudo
     */
    public function setDescontoQuitarTudo($descontoQuitarTudo)
    {
        $this->emissao->setDescontoQuitarTudo($descontoQuitarTudo);
    }
}
