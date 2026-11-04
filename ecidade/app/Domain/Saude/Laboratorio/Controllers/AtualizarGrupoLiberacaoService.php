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

use App\Domain\Saude\Laboratorio\Repositories\Contracts\GrupoLiberacaoRepository;
use App\Domain\Saude\Laboratorio\Repositories\GrupoLiberacaoRepositoryEloquent;
use App\Domain\Saude\Laboratorio\Requests\AtualizarGrupoLiberacaoRequest;
use App\Domain\Saude\Laboratorio\Services\GetGrupoByIdService;

class AtualizarGrupoLiberacaoService
{
    /**
     * @var GrupoLiberacaoRepository
     */
    private $grupoLiberacaoRepository;

    public function __construct(GrupoLiberacaoRepository $grupoLiberacaoRepository)
    {
        $this->grupoLiberacaoRepository = $grupoLiberacaoRepository;
    }

    /**
     * Atualiza um grupo de liberação.
     * @param  AtualizarGrupoLiberacaoRequest  $atualizarGrupoLiberacaoRequest
     * @return object
     */
    public function atualizarGrupoLiberacao(AtualizarGrupoLiberacaoRequest $atualizarGrupoLiberacaoRequest)
    {
        $resposta = (object)[
            'data' => null,
            'message' => 'Grupo de liberação atualizado com sucesso!',
            'statusCode' => 202,
        ];

        $grupo = (new GetGrupoByIdService(new GrupoLiberacaoRepositoryEloquent()))
            ->getGrupoByIdService($atualizarGrupoLiberacaoRequest->grupoId);

        /**
         * Valida se o grupo já possui exames e se está tentando trocar de setor.
         */
        if ($grupo->la71_setor != $atualizarGrupoLiberacaoRequest->setor && $grupo->exames->count() > 0) {
            return (object)[
                'data' => null,
                'message' => 'Grupo com exames já cadastrados. Não é possível alterar o setor!',
                'statusCode' => 500,
            ];
        }

        if (!$this->grupoLiberacaoRepository->atualizarGrupoLiberacao($atualizarGrupoLiberacaoRequest)) {
            return (object)[
                'data' => null,
                'message' => 'Falha ao atualizar grupo de liberação!',
                'statusCode' => 500,
            ];
        }
        return $resposta;
    }
}
