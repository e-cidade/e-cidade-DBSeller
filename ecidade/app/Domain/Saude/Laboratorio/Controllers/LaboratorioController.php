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

namespace App\Domain\Saude\Laboratorio\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Saude\Laboratorio\Repositories\LaboratorioRepositoryEloquent;
use App\Domain\Saude\Laboratorio\Services\GetLaboratorioByDepartamentoService;
use App\Domain\Saude\Laboratorio\Services\GetLaboratorioSetorExamesService;
use App\Http\Controllers\Controller;

class LaboratorioController extends Controller
{
    public function getByDepartamento($departamentoId)
    {
        sleep(5);
        if (!filter_var($departamentoId, FILTER_VALIDATE_INT)) {
            return new DBJsonResponse(
                null,
                'Departamento inválido.',
                500
            );
        }

        $response = (new GetLaboratorioByDepartamentoService(new LaboratorioRepositoryEloquent()))
            ->getByDepartamento($departamentoId);

        return new DBJsonResponse(
            $response->data,
            $response->message,
            $response->statusCode
        );
    }

    public function getLaboratorioSetorExames($laboratorioId, $setorId)
    {
        if (!filter_var($laboratorioId, FILTER_VALIDATE_INT)) {
            return new DBJsonResponse(
                null,
                'Laboratório inválido.',
                500
            );
        }

        if (!filter_var($setorId, FILTER_VALIDATE_INT)) {
            return new DBJsonResponse(
                null,
                'Setor inválido.',
                500
            );
        }

        $response = (new GetLaboratorioSetorExamesService(new LaboratorioRepositoryEloquent()))
            ->getLaboratorioSetorExames($laboratorioId, $setorId);

        return new DBJsonResponse(
            $response->data,
            $response->message,
            $response->statusCode
        );
    }
}
