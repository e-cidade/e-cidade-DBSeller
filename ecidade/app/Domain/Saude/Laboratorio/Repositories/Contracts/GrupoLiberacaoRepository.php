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
use App\Domain\Saude\Laboratorio\Models\GrupoLiberacao;
use App\Domain\Saude\Laboratorio\Models\Laboratorio;
use App\Domain\Saude\Laboratorio\Requests\AtualizarGrupoLiberacaoRequest;
use App\Domain\Saude\Laboratorio\Requests\GrupoLiberacaoExamesRequest;
use App\Domain\Saude\Laboratorio\Requests\GrupoLiberacaoRequest;

interface GrupoLiberacaoRepository
{
    /**
     * Registra um novo grupo de liberaзгo
     * @param  GrupoLiberacaoRequest  $grupoLiberacaoRequest
     * @throws \PDOException
     * @return bool
     */
    public function criarGrupoLiberacao(GrupoLiberacaoRequest $grupoLiberacaoRequest);

    /**
     * Retorna todos os grupos do usuбrio autenticado.
     * @param int $usuarioId
     * @return GrupoLiberacao[]|bool
     */
    public function getAllByUsuario($usuarioId);

    /**
     * Retorna os setores vнnculados ao laboratуrio.
     * @param  Laboratorio  $laboratorio
     * @return Setor[]|bool
     */
    public function getLaboratoriosSetores(Laboratorio $laboratorio);

    /**
     * Atualiza um grupo de liberaзгo.
     * @param  AtualizarGrupoLiberacaoRequest  $atualizarGrupoLiberacaoRequest
     * @return bool
     */
    public function atualizarGrupoLiberacao(AtualizarGrupoLiberacaoRequest $atualizarGrupoLiberacaoRequest);

    /**
     * Registra um novo vнnculo entre um grupo de liberaзгo e um exame.
     * @param  GrupoLiberacaoExamesRequest  $grupoLiberacaoExameRequest
     * @return bool
     */
    public function adicionarGrupoLiberacaoExame(GrupoLiberacaoExamesRequest $grupoLiberacaoExameRequest);

    /**
     * Retorna o grupo de liberaзгo caso o mesmo possua um setor.
     * @param int $grupoLiberacaoId
     * @return GrupoLiberacao|bool
     */
    public function getGrupoLiberacaoComSetor($grupoLiberacaoId);

    /**
     * Recupera os exames vindulados ao grupo informado.
     * @param int $grupoLiberacaoId
     * @return Exame[]|bool
     */
    public function getExamesByGrupo($grupoLiberacaoId);


    /**
     * Desvнncula um exame de um grupo de liberaзгo.
     * @param int $grupoId
     * @param int $exameId
     * @return bool
     */
    public function excluirExameFromGrupoLiberacao($grupoId, $exameId);

    /**
     * Exclui um grupo de liberaзгo.
     * @param  GrupoLiberacao  $grupo
     * @return bool
     */
    public function excluirGrupoLiberacao(GrupoLiberacao $grupo);

    /**
     * Retorna os exames vinculados ao grupo de liberaзгo ou falso caso nгo haja exames.
     * @param  GrupoLiberacao  $grupo
     * @return Exame[]|bool
     */
    public function getExamesByGrupoLiberacao(GrupoLiberacao $grupo);

    /**
     * Busca um grupo por seu ID.
     * @param int $grupoId
     * @return GrupoLiberacao|bool
     */
    public function getGrupoById($grupoId);
}
