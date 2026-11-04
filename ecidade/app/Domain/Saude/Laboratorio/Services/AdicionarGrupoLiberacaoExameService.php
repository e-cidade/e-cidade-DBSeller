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

namespace App\Domain\Saude\Laboratorio\Services;

use App\Domain\Saude\Laboratorio\Repositories\Contracts\GrupoLiberacaoRepository;
use App\Domain\Saude\Laboratorio\Repositories\GrupoLiberacaoRepositoryEloquent;
use App\Domain\Saude\Laboratorio\Repositories\LaboratorioSetorRepositoryEloquent;
use App\Domain\Saude\Laboratorio\Requests\GrupoLiberacaoExamesRequest;

class AdicionarGrupoLiberacaoExameService
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
     * Registra um vínculo entre um grupo de liberação e um exame.
     * @param  GrupoLiberacaoExamesRequest  $grupoLiberacaoExamesRequest
     * @return object
     */
    public function adicionarGrupoLiberacaoExame(GrupoLiberacaoExamesRequest $grupoLiberacaoExamesRequest)
    {
        $grupoLiberacao = (new ValidarSetorGrupoLiberacaoService(new GrupoLiberacaoRepositoryEloquent()))
            ->validarSetorGrupoLiberacao($grupoLiberacaoExamesRequest->grupo);

        if (!$grupoLiberacao) {
            return (object)[
                'data' => null,
                'message' => 'Grupo sem setor cadastrado!',
                'statusCode' => 406,
            ];
        }

        $exameValido = (new ExameValidoParaGrupoService(new LaboratorioSetorRepositoryEloquent()))
            ->exameValidoParaGrupoService($grupoLiberacaoExamesRequest->exame, $grupoLiberacao->la71_setor);

        if (!$exameValido) {
            return (object)[
                'data' => null,
                'message' => 'Exame não cadastrado no mesmo setor do grupo informado!',
                'statusCode' => 406,
            ];
        }

        $grupoExameSalvo = $this->grupoLiberacaoRepository->adicionarGrupoLiberacaoExame($grupoLiberacaoExamesRequest);

        if (!$grupoExameSalvo) {
            return (object)[
                'data' => null,
                'message' => 'Falha ao adicionar exame!',
                'statusCode' => 500,
            ];
        }

        return (object)[
            'data' => null,
            'message' => 'Exame adicionado com sucesso!',
            'statusCode' => 201,
        ];
    }
}
