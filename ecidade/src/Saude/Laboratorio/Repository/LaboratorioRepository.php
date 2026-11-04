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

namespace ECidade\Saude\Laboratorio\Repository;

use cl_lab_laboratorio;
use db_utils;
use DomainException;
use ECidade\Saude\Laboratorio\Model\Laboratorio;
use Exception;
use UsuarioSistema;

/**
 * Class LaboratorioRepository
 * @package ECidade\Saude\Laboratorio\Repository
 */
class LaboratorioRepository
{
    /**
     * @var Object
     */
    private $dao;

    /**
     * LaboratorioRepository constructor.
     * @param $dao cl_lab_laboratorio
     */
    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param Laboratorio $laboratorio
     * @return Laboratorio
     * @throws Exception
     */
    public function salvar(
        Laboratorio $laboratorio
    ) {
        $this->dao->la02_i_codigo = $laboratorio->getCodigo();
        $this->dao->la02_i_tipo = $laboratorio->getTipo();
        $this->dao->la02_c_descr = $laboratorio->getDescricao();
        $this->dao->la02_i_alvara = $laboratorio->getAlvara();
        $this->dao->la02_i_cnes = $laboratorio->getCnes();
        $this->dao->la02_c_endereco = $laboratorio->getEndereco();
        $this->dao->la02_i_telefone = $laboratorio->getTelefone();
        $this->dao->la02_c_numero = $laboratorio->getNumero();
        $this->dao->la02_i_turnoatend = $laboratorio->getTurnoAtendimento();
        $this->dao->la02_interfaceado = $laboratorio->getInterfaceado();

        if (!$laboratorio->getCodigo()) {
            $this->dao->incluir(null);
        } else {
            $this->dao->alterar($laboratorio->getCodigo());
        }
        if ($this->dao->erro_status === '0') {
            throw new Exception($this->dao->erro_msg);
        }

        return $laboratorio;
    }

    public function verificaLaboratorioInterfaceadoRequisicao($codigo)
    {
        $rs = $this->dao->sql_record(
            $this->dao->verifica_laboratorio_interfaceado_por_requisicao("la22_i_codigo = $codigo")
        );

        if (!$rs) {
            return false;
        }

        return pg_fetch_row($rs)[0];
    }

    /**
     * @param $campos
     * @param $where
     * @return array
     */
    public function verificaLaboratorioInterfaceadoPorDepartamento($campos = '*', $where = '')
    {
        $retorno = [];
        try {
            $sql = $this->dao->sql_query_laboratorio(null, $campos, null, $where);
            $rsSql = db_query($sql);

            if (!$rsSql || pg_num_rows($rsSql) == 0) {
                throw new DomainException('
                Erro ao verificar se o departamento está vinculado a um laboratório que realiza interfaceamento!
                ');
            }

            $retorno['interfaceado'] = (db_utils::fieldsMemory($rsSql, 0)->la02_interfaceado === 't');
            $retorno['erro'] = false;
            $retorno['mensagem'] = '';

            return $retorno;
        } catch (Exception $exception) {
            $retorno['mensagem'] = urlencode($exception->getMessage());
            $retorno['erro'] = true;

            return $retorno;
        }
    }
}
