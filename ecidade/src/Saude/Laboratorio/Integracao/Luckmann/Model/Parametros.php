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

namespace ECidade\Saude\Laboratorio\Integracao\Luckmann\Model;

/**
 * Class Parametros
 * @package ECidade\Saude\Laboratorio\Integracao\Luckmann\Model
 */
class Parametros
{
    /**
     * @var string
     */
    private $pastaPedidos;

    /**
     * @var string
     */
    private $pastaResultados;

    /**
     * @var string
     */
    private $pastaPedidosInfinity;

    /**
     * @var string
     */
    private $pastaResultadosInfinity;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getPastaPedidos()
    {
        return $this->pastaPedidos;
    }

    /**
     * @param string $pastaPedidos
     */
    public function setPastaPedidos($pastaPedidos)
    {
        $this->pastaPedidos = $pastaPedidos;
    }

    /**
     * @return string
     */
    public function getPastaResultados()
    {
        return $this->pastaResultados;
    }

    /**
     * @param string $pastaResultados
     */
    public function setPastaResultados($pastaResultados)
    {
        $this->pastaResultados = $pastaResultados;
    }

    /**
     * @return string
     */
    public function getPastaPedidosInfinity()
    {
        return trim($this->pastaPedidosInfinity);
    }

    /**
     * @param string $pastaPedidosInfinity
     */
    public function setPastaPedidosInfinity($pastaPedidosInfinity)
    {
        $this->pastaPedidosInfinity = $pastaPedidosInfinity;
    }

    /**
     * @return string
     */
    public function getPastaResultadosInfinity()
    {
        return trim($this->pastaResultadosInfinity);
    }

    /**
     * @param string $pastaResultadosInfinity
     */
    public function setPastaResultadosInfinity($pastaResultadosInfinity)
    {
        $this->pastaResultadosInfinity = $pastaResultadosInfinity;
    }
}
