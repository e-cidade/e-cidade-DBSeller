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

namespace ECidade\RecursosHumanos\Pessoal\Service;

use ECidade\RecursosHumanos\Pessoal\Model\MenorAprendiz;

class MenorAprendizService
{
    /**
     * @var int
     */
    private $matricula;
    /**
     * @var int
     */
    private $codigoModalidade;
    /**
     * @var string
     */
    private $codigoInstituicao;
    /**
     * @var string
     */
    private $cnpjDireta;
    /**
     * @var string
     */
    private $cnpjIndireta;
    /**
     * @var string
     */
    private $cnpjPratica;

    /**
     * @var int $matricula
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
    }

    /**
     * @var int $codigoModalidade
     */
    public function setModalidade($codigoModalidade)
    {
        dump($codigoModalidade);
        $this->codigoInstituicao = $codigoModalidade;
    }

    /**
     * @var int $codigoInstituicaoo
     */
    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    /**
     * @var string $cnpjDireta
     */
    public function setCnpjDireta($cnpjDireta)
    {
        $this->cnpjDireta = $cnpjDireta;
    }

    /**
     * @var string $cnpjIndireta
     */
    public function setCnpjIndireta($cnpjIndireta)
    {
        $this->cnpjIndireta = $cnpjIndireta;
    }

    /**
     * @var string $cnpjPratica
     */
    public function setCnpjPratica($cnpjPratica)
    {
        $this->cnpjPratica = $cnpjPratica;
    }
}
