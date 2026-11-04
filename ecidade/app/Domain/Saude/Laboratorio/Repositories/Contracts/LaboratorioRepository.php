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

namespace App\Domain\Saude\Laboratorio\Repositories\Contracts;

use App\Domain\Saude\Laboratorio\Models\Exame;
use App\Domain\Saude\Laboratorio\Models\Laboratorio;

interface LaboratorioRepository
{
    /**
     * Recupera o laboratório através do departamento.
     * @param int $departamentoId
     * @return Laboratorio|false
     */
    public function getByDepartamento($departamentoId);

    /**
     * Recupera os exames levando em conta o laboratório e setor em que se está vinculado.
     * @param int $laboratorioId
     * @param int $setorId
     * @return Exame[]
     */
    public function getLaboratorioSetorExames($laboratorioId, $setorId);
}
