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

namespace ECidade\Patrimonial\Protocolo\TipoProcesso\Collection;

use ECidade\Patrimonial\Protocolo\TipoProcesso\Model\TipoProcesso as TipoProcessoModel;

/**
 * Class TipoProcesso
 * @package ECidade\Patrimonial\Protocolo\TipoProcesso\Collection
 */
class TipoProcesso
{
    /**
     * @var TipoProcessoModel[]
     */
    private $colecao = array();

    /**
     * @param TipoProcessoModel $tipoProcesso
     */
    public function add(TipoProcessoModel $tipoProcesso)
    {
        if (!array_key_exists($tipoProcesso->getCodigo(), $this->colecao)) {
            $this->colecao[$tipoProcesso->getCodigo()] = $tipoProcesso;
        }
    }

    /**
     * @return TipoProcessoModel[]
     */
    public function getAll()
    {
        return $this->colecao;
    }

    /**
     * @param int $codigoTipoProcess
     * @return TipoProcessoModel|null
     */
    public function getByCodigo($codigoTipoProcess)
    {
        if (array_key_exists($codigoTipoProcess, $this->colecao)) {
            return $this->colecao[$codigoTipoProcess];
        }

        return null;
    }
}
