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
namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Service;

use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Builder\Registro00Builder;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro00;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Repository\Registro00Repository;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Validators\Registro00Validator;
use Escola;
use Exception;
use ECidade\Educacao\Escola\Censo\Censo;

class Registro00Service
{
    /**
     * @var Escola
     */
    private $escola;

    /**
     * @var Registro00
     */
    private $registro;

    /**
     * @var Censo
     */
    private $censo;

    /**
     * @var Registro00
     */
    private static $registroProcessado;

    public function __construct()
    {
        $this->registro = new Registro00();
    }

    /**
     * @param Escola $escola
     */
    public function setEscola(Escola $escola)
    {
        $this->escola = $escola;
    }

    public function setCenso($censo)
    {
        $this->censo = $censo;
    }

    /**
     * @throws Exception
     */
    public function buscarDados()
    {
        $repository = new Registro00Repository();

        $builder = new Registro00Builder();
        $builder->addDadosCenso($repository->getDadosCenso($this->escola));
        $builder->addDadosTelefones($this->escola->getTelefones());
        $builder
            ->addDadosCalendarioEscolar($repository
                ->buscaDatasCalendarioEscolar($this->escola, $this->censo->getAno()));
        $this->registro = self::$registroProcessado = $builder->build();
    }

    public function getRegistro()
    {
        return $this->registro;
    }

    public function validar()
    {
        $validator = new Registro00Validator();
        $validator->setRegistro($this->registro);
        $validator->setCenso($this->censo);
        $validator->validar();
    }

    /**
     * @return Registro00
     */
    public static function getRegistroProcessado()
    {
        return self::$registroProcessado;
    }
}
