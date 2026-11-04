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
use App\Domain\Saude\Laboratorio\Resources\ExameResource;

class GetExamesByGrupoService
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
     * Recupera os exames ligados ao grupo informado.
     * @param int $grupoLiberacaoId
     * @return object
     */
    public function getExamesBygrupo($grupoLiberacaoId)
    {
        $exames = $this->grupoLiberacaoRepository->getExamesByGrupo($grupoLiberacaoId);

        return (object)[
            'data' => (!empty($exames) ? ExameResource::descricaoExames($exames) : null),
            'message' => 'OK!',
            'statusCode' => 200,
        ];
    }
}
