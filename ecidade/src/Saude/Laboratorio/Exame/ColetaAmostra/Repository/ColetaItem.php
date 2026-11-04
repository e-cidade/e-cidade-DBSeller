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

namespace ECidade\Saude\Laboratorio\Exame\ColetaAmostra\Repository;

use cl_lab_coletaitem;
use DBDate;
use db_utils;
use ECidade\Saude\Laboratorio\Exame\ColetaAmostra\Model\ColetaItem as ColetaItemModel;
use RequisicaoExame as RequisicaoExameModel;
use ECidade\Saude\Laboratorio\Exame\Repository\RequisicaoExame as RequisicaoExameRepository;
use Exception;
use stdClass;
use UsuarioSistemaRepository;

/**
 * Class ColetaItem
 * @package ECidade\Saude\Laboratorio\Exame\ColetaAmostra\Repository
 */
class ColetaItem extends \BaseClassRepository
{
    /**
     * @param RequisicaoExameModel $requisicaoExame
     * @return ColetaItemModel|null
     * @throws Exception
     */
    public function getByRequisicaoExame(RequisicaoExameModel $requisicaoExame)
    {
        $coletaItem = $this->getInstanceByRequisicaoExame($requisicaoExame);

        if ($coletaItem instanceof ColetaItemModel) {
            return $coletaItem;
        }

        $dao = new cl_lab_coletaitem();
        $where = "la32_i_requiitem = {$requisicaoExame->getCodigo()}";
        $sql = $dao->sql_query_file(null, '*', null, $where);
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception('Erro ao buscar a coleta do item.');
        }

        if (pg_num_rows($rs) === 0) {
            return null;
        }

        return $this->makeByStdClass(db_utils::fieldsMemory($rs, 0));
    }

    /**
     * @param RequisicaoExameModel $requisicaoExame
     * @return ColetaItemModel|null
     */
    private function getInstanceByRequisicaoExame(RequisicaoExameModel $requisicaoExame)
    {
        if (empty($this->aColecao)) {
            return null;
        }

        foreach ($this->aColecao as $coletaItem) {
            if ($coletaItem->getRequisicaoExame()->getCodigo() === $requisicaoExame->getCodigo()) {
                return $coletaItem;
            }
        }

        return null;
    }

    /**
     * @param stdClass $stdClass
     * @return ColetaItemModel
     */
    protected function makeByStdClass(stdClass $stdClass)
    {
        $requisicaoExameRepository = RequisicaoExameRepository::getInstance();
        $requisicaoExameModel = $requisicaoExameRepository->getRequisicaoExameByCodigo($stdClass->la32_i_requiitem);

        $coletaItem = new ColetaItemModel();
        $coletaItem->setCodigo($stdClass->la32_i_codigo);
        $coletaItem->setUsuarioSistema(UsuarioSistemaRepository::getPorCodigo($stdClass->la32_i_usuario));
        $coletaItem->setRequisicaoExame($requisicaoExameModel);
        $coletaItem->setData(DBDate::create($stdClass->la32_d_data));
        $coletaItem->setHora($stdClass->la32_c_hora);
        $coletaItem->setAvisaPaciente($stdClass->la32_i_avisapaciente === 1);
        $coletaItem->setHoraEntrega($stdClass->la32_c_horaentrega);
        $coletaItem->setDataEntrega($stdClass->la32_d_entrega ? DBDate::create($stdClass->la32_d_entrega) : '');

        self::getInstance()->add($coletaItem);

        return $coletaItem;
    }
}
