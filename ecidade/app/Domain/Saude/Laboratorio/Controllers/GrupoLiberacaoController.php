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
use App\Domain\Saude\Laboratorio\Models\Laboratorio;
use App\Domain\Saude\Laboratorio\Repositories\GrupoLiberacaoRepositoryEloquent;
use App\Domain\Saude\Laboratorio\Requests\AtualizarGrupoLiberacaoRequest;
use App\Domain\Saude\Laboratorio\Requests\GrupoLiberacaoExamesRequest;
use App\Domain\Saude\Laboratorio\Requests\GrupoLiberacaoRequest;
use App\Domain\Saude\Laboratorio\Services\AdicionarGrupoLiberacaoExameService;
use App\Domain\Saude\Laboratorio\Services\CriarGrupoLiberacaoService;
use App\Domain\Saude\Laboratorio\Services\ExcluirExameFromGrupoLiberacaoService;
use App\Domain\Saude\Laboratorio\Services\ExcluirGrupoLiberacaoService;
use App\Domain\Saude\Laboratorio\Services\GetAllGruposLiberacaoByUsuarioService;
use App\Domain\Saude\Laboratorio\Services\GetExamesByGrupoService;
use App\Domain\Saude\Laboratorio\Services\GetLaboratorioSetoresService;
use App\Http\Controllers\Controller;

class GrupoLiberacaoController extends Controller
{
    public function criarGrupoLiberacao(GrupoLiberacaoRequest $grupoLiberacaoRequest)
    {
        $response = (new CriarGrupoLiberacaoService(new GrupoLiberacaoRepositoryEloquent()))
            ->criarGrupoLiberacao($grupoLiberacaoRequest);

        return new DBJsonResponse(
            $response->data,
            $response->message,
            $response->statusCode
        );
    }

    public function getAllByUsuario($usuarioId)
    {
        if (!filter_var($usuarioId, FILTER_VALIDATE_INT)) {
            return new DBJsonResponse(
                null,
                'Usuário inválido.',
                500
            );
        }
        $response = (new GetAllGruposLiberacaoByUsuarioService(new GrupoLiberacaoRepositoryEloquent()))
            ->getAllByUsuario($usuarioId);

        return new DBJsonResponse(
            $response->data,
            $response->message,
            $response->statusCode
        );
    }

    public function getLaboratorioSetores(Laboratorio $laboratorio)
    {
        $response = (new GetLaboratorioSetoresService(new GrupoLiberacaoRepositoryEloquent()))->getLaboratorioSetores(
            $laboratorio
        );

        return new DBJsonResponse(
            $response->data,
            $response->message,
            $response->statusCode
        );
    }

    public function atualizarGrupoLiberacao(AtualizarGrupoLiberacaoRequest $atualizarGrupoLiberacaoRequest)
    {
        $response = (new AtualizarGrupoLiberacaoService(new GrupoLiberacaoRepositoryEloquent()))
            ->atualizarGrupoLiberacao($atualizarGrupoLiberacaoRequest);

        return new DBJsonResponse(
            $response->data,
            $response->message,
            $response->statusCode
        );
    }

    public function adicionarGrupoLiberacaoExame(GrupoLiberacaoExamesRequest $grupoLiberacaoExamesRequest)
    {
        $response = (new AdicionarGrupoLiberacaoExameService(new GrupoLiberacaoRepositoryEloquent()))
            ->adicionarGrupoLiberacaoExame($grupoLiberacaoExamesRequest);

        return new DBJsonResponse(
            $response->data,
            $response->message,
            $response->statusCode
        );
    }

    public function getExamesByGrupo($grupoLiberacaoId)
    {
        if (!filter_var($grupoLiberacaoId, FILTER_VALIDATE_INT)) {
            return new DBJsonResponse(
                null,
                'Grupo de validação inválido.',
                500
            );
        }

        $response = (new GetExamesByGrupoService(new GrupoLiberacaoRepositoryEloquent()))
            ->getExamesByGrupo($grupoLiberacaoId);

        return new DBJsonResponse(
            $response->data,
            $response->message,
            $response->statusCode
        );
    }

    public function excluirExameFromGrupoLiberacao($grupoId, $exameId)
    {
        if (!filter_var($grupoId, FILTER_VALIDATE_INT)) {
            return new DBJsonResponse(
                null,
                'Grupo de validação inválido.',
                500
            );
        }

        if (!filter_var($exameId, FILTER_VALIDATE_INT)) {
            return new DBJsonResponse(
                null,
                'Exame inválido.',
                500
            );
        }

        $response = (new ExcluirExameFromGrupoLiberacaoService(new GrupoLiberacaoRepositoryEloquent()))
            ->excluirExameFromGrupoLiberacao($grupoId, $exameId);

        return new DBJsonResponse(
            $response->data,
            $response->message,
            $response->statusCode
        );
    }

    public function excluirGrupoLiberacao($grupoId)
    {
        if (!filter_var($grupoId, FILTER_VALIDATE_INT)) {
            return new DBJsonResponse(
                null,
                'Grupo inválido.',
                500
            );
        }

        $response = (new ExcluirGrupoLiberacaoService(new GrupoLiberacaoRepositoryEloquent()))
            ->excluirGrupoLiberacao($grupoId);

        return new DBJsonResponse(
            $response->data,
            $response->message,
            $response->statusCode
        );
    }
}
