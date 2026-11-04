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

use CgmRepository;
use ECidade\RecursosHumanos\Pessoal\Model\OperadoraSaude;
use ECidade\RecursosHumanos\Pessoal\Repository\OperadoraSaudeRepository;
use Exception;
use stdClass;

/**
 * Class OperadoraSaudeService
 * @package ECidade\RecursosHumanos\Pessoal\Service
 */
class OperadoraSaudeService
{
    /**
     * @var OperadoraSaudeRepository
     */
    private $repositorio;

    /**
     * OperadoraSaudeService constructor.
     */
    public function __construct()
    {
        $this->repositorio = new OperadoraSaudeRepository();
    }

    /**
     * @return OperadoraSaude[]
     * @throws Exception
     */
    public function todas()
    {
        return $this->repositorio->all();
    }

    /**
     * @param stdClass $parametros
     * @return OperadoraSaude
     * @throws Exception
     */
    public function salvar(stdClass $parametros)
    {
        if (isset($parametros->cgm) === false || $parametros->cgm === false || trim($parametros->cgm) === '') {
            throw new Exception('O campo "Operadora" é obrigatório.');
        }

        if (isset($parametros->ativo) === false || $parametros->ativo === '') {
            throw new Exception('O campo "Situação" é obrigatório.');
        }

        $operadoraSaude = new OperadoraSaude();
        $operadoraSaude->setCgm(CgmRepository::getByCodigo($parametros->cgm));
        $operadoraSaude->setAns($parametros->ans);
        $operadoraSaude->setAtivo($parametros->ativo);
        $operadoraSaude->setCodigoInstituicao(db_getsession('DB_instit'));

        if (isset($parametros->sequencial) && $parametros->sequencial) {
            $operadoraSaude->setSequencial($parametros->sequencial);
            $this->repositorio->scopeSequencial($parametros->sequencial, '!=');
        }

        $existe = $this->repositorio->scopeCgm($operadoraSaude->getCgm())->get();

        if (count($existe) > 0) {
            throw new Exception("A operadora {$operadoraSaude->getCgm()->getNome()} já está sendo utilizada.");
        }

        $existe = $this->repositorio->removeScope('cgm')->scopeAns($operadoraSaude->getAns())->get();

        if (count($existe) > 0) {
            throw new Exception("O ANS {$operadoraSaude->getAns()} já está sendo utilizado.");
        }

        return $this->repositorio->save($operadoraSaude);
    }
}
