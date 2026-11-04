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

namespace ECidade\Saude\Laboratorio\Service;

use DateTime;
use ECidade\Saude\Laboratorio\Repository\LaboratorioRepository;
use ECidade\Saude\Laboratorio\Model\Laboratorio;
use Exception;
use Instituicao;
use DBDepartamento;
use JSON;
use InstituicaoRepository;
use Cgs;

/**
 * Class LaboratorioService
 * @package ECidade\Saude\Laboratorio\Service
 */
class LaboratorioService
{
    private $repositorio;

    /**
     * LaboratorioService constructor.
     * @param LaboratorioRepository $repositorio
     */
    public function __construct(LaboratorioRepository $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    /**
     * Salva os dados do laboratório
     */
    public function salvar($parametros)
    {

        $laboratorio = new laboratorio();
        $laboratorio->setCodigo(isset($parametros->la22_i_codigo) ? $parametros->la22_i_codigo : '');
        $laboratorio->setTipo(isset($parametros->la02_i_tipo) ? $parametros->la02_i_tipo : '');
        $laboratorio->setDescricao(isset($parametros->la02_c_descr) ? $parametros->la02_c_descr : '');
        $laboratorio->setAlvara(isset($parametros->la02_i_alvara) ? $parametros->la02_i_alvara : '');
        $laboratorio->setCnes(isset($parametros->la02_i_cnes) ? $parametros->la02_i_cnes : '');
        $laboratorio->setEndereco(isset($parametros->la02_c_endereco) ? $parametros->la02_c_endereco : '');
        $laboratorio->setTelefone(isset($parametros->la02_i_telefone) ? $parametros->la02_i_telefone : '');
        $laboratorio->setNumero(isset($parametros->la02_c_numero) ? $parametros->la02_c_numero : '');
        $laboratorio->setTurnoAtendimento(isset($parametros->la02_i_turnoatend) ? $parametros->la02_i_turnoatend : '');
        $laboratorio->setInterfaceado(isset($parametros->la02_interfaceado) ? $parametros->la02_interfaceado : '');

        $laboratorio = $this->repositorio->salvar($laboratorio);

        return $laboratorio;
    }

    public function verificaLaboratorioInterfaceadoRequisicao($codigo)
    {
        
        return $this->repositorio->verificaLaboratorioInterfaceadoRequisicao($codigo);
    }

    /**
     * @param $campos
     * @param $where
     * @return array
     */
    public function verificaLaboratorioInterfaceadoPorDepartamento($campos = '*', $where = '')
    {
        return $this->repositorio->verificaLaboratorioInterfaceadoPorDepartamento($campos, $where);
    }
}
