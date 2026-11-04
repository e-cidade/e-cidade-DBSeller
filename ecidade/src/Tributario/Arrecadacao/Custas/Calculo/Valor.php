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

namespace ECidade\Tributario\Arrecadacao\Custas\Calculo;

final class Valor
{
    private $valorCorrigido;

    private $valorJuros;

    private $valorMulta;

    private $valorDesconto;

    public function __construct($valorCorrigido, $valorJuros, $valorMulta, $valorDesconto)
    {
        $this->valorCorrigido = $valorCorrigido;
        $this->valorJuros = $valorJuros;
        $this->valorMulta = $valorMulta;
        $this->valorDesconto = $valorDesconto;
    }

    public function getValorCorrigido()
    {
        return $this->valorCorrigido;
    }

    public function getValorJuros()
    {
        return $this->valorJuros;
    }

    public function getValorMulta()
    {
        return $this->valorMulta;
    }

    public function getValorDesconto()
    {
        return $this->valorDesconto;
    }

    public function setValorCorrigido($valorCorrigido)
    {
        $this->valorCorrigido = $valorCorrigido;
    }

    public function setValorJuros($valorJuros)
    {
        $this->valorJuros = $valorJuros;
    }

    public function setValorMulta($valorMulta)
    {
        $this->valorMulta = $valorMulta;
    }

    public function setValorDesconto($valorDesconto)
    {
        $this->valorDesconto = $valorDesconto;
    }
}
