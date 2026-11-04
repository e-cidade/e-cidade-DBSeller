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

namespace App\Domain\Saude\Laboratorio\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Saude\Laboratorio\Models\Exame;
use App\Domain\Saude\Laboratorio\Models\GrupoLiberacao;
use App\Domain\Saude\Laboratorio\Models\Laboratorio;
use App\Domain\Saude\Laboratorio\Repositories\Contracts\GrupoLiberacaoRepository;
use App\Domain\Saude\Laboratorio\Requests\AtualizarGrupoLiberacaoRequest;
use App\Domain\Saude\Laboratorio\Requests\GrupoLiberacaoExamesRequest;
use App\Domain\Saude\Laboratorio\Requests\GrupoLiberacaoRequest;
use App\Domain\Saude\Laboratorio\Models\GrupoLiberacaoExame;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HigherOrderCollectionProxy;
use PDOException;

class GrupoLiberacaoRepositoryEloquent extends BaseRepository implements GrupoLiberacaoRepository
{
    /**
     * Registra um novo grupo de liberação
     * @param  GrupoLiberacaoRequest  $grupoLiberacaoRequest
     * @throws PDOException
     * @return bool
     */
    public function criarGrupoLiberacao(GrupoLiberacaoRequest $grupoLiberacaoRequest)
    {
        try {
            $grupoLiberacao = new GrupoLiberacao();
            $grupoLiberacao->la71_descricao = $grupoLiberacaoRequest->nomeGrupo;
            $grupoLiberacao->la71_dbusuarios = $grupoLiberacaoRequest->usuario;
            $salvo = $grupoLiberacao->save();

            if (!$salvo) {
                return false;
            }
            return true;
        } catch (PDOException $PDOException) {
            return false;
        }
    }

    /**
     * Retorna todos os grupos do usuário autenticado.
     * @param int $usuarioId
     * @return GrupoLiberacao[] | bool
     */
    public function getAllByUsuario($usuarioId)
    {
        $grupos = GrupoLiberacao::where('la71_dbusuarios', $usuarioId)
            ->orderBy('la71_descricao', 'asc')
            ->get();

        if ($grupos->count() == 0) {
            return false;
        }
        return $grupos;
    }

    /**
     * Retorna os setores vínculados ao laboratório.
     * @param  Laboratorio  $laboratorio
     * @return Setor[]|bool
     */
    public function getLaboratoriosSetores(Laboratorio $laboratorio)
    {
        $setores = $laboratorio
            ->laboratorioSetores()
            ->distinct()
            ->orderBy('la23_c_descr')
            ->get(['la23_i_codigo', 'la23_c_descr']);

        if ($setores->count() == 0) {
            return false;
        }
        return $setores->all();
    }

    /**
     * Atualiza um grupo de liberação.
     * @param  AtualizarGrupoLiberacaoRequest  $atualizarGrupoLiberacaoRequest
     * @return bool
     */
    public function atualizarGrupoLiberacao(AtualizarGrupoLiberacaoRequest $atualizarGrupoLiberacaoRequest)
    {
        try {
            $grupo = GrupoLiberacao::find($atualizarGrupoLiberacaoRequest->grupoId);
            $grupo->la71_descricao = $atualizarGrupoLiberacaoRequest->nomeGrupo;

            if (!empty($atualizarGrupoLiberacaoRequest->setor)) {
                $grupo->la71_setor = $atualizarGrupoLiberacaoRequest->setor;
            }
            $grupoSalvo = $grupo->save();

            if (!$grupoSalvo) {
                return false;
            }
            return true;
        } catch (PDOException $PDOException) {
            return false;
        }
    }

    /**
     * Registra um novo vínculo entre um grupo de liberação e um exame.
     * @param  GrupoLiberacaoExamesRequest  $grupoLiberacaoExameRequest
     * @return bool
     */
    public function adicionarGrupoLiberacaoExame(GrupoLiberacaoExamesRequest $grupoLiberacaoExameRequest)
    {
        try {
            $grupoLiberacaoExame = new GrupoLiberacaoExame();
            $grupoLiberacaoExame->la72_grupoliberacao = $grupoLiberacaoExameRequest->grupo;
            $grupoLiberacaoExame->la72_labexame = $grupoLiberacaoExameRequest->exame;
            $salvo = $grupoLiberacaoExame->save();

            if (!$salvo) {
                return false;
            }
            return true;
        } catch (PDOException $PDOException) {
            return false;
        }
    }

    /**
     * Retorna o grupo de liberação caso o mesmo possua um setor.
     * @param int $grupoLiberacaoId
     * @return GrupoLiberacao|bool
     */
    public function getGrupoLiberacaoComSetor($grupoLiberacaoId)
    {
        $grupoLiberacao = GrupoLiberacao::find($grupoLiberacaoId);

        if (!empty($grupoLiberacao) && !empty($grupoLiberacao->la71_setor)) {
            return $grupoLiberacao;
        }
        return false;
    }

    /**
     * Recupera os exames vindulados ao grupo informado.
     * @param int $grupoLiberacaoId
     * @return Exame[]|bool|HigherOrderCollectionProxy|mixed
     */
    public function getExamesByGrupo($grupoLiberacaoId)
    {
        $grupoLiberacao = GrupoLiberacao::find($grupoLiberacaoId);

        if (is_null($grupoLiberacao) || $grupoLiberacao->exames->count() == 0) {
            return false;
        }
        return $grupoLiberacao->exames;
    }

    /**
     * Desvíncula um exame de um grupo de liberação.
     * @param int $grupoId
     * @param int $exameId
     * @return bool
     */
    public function excluirExameFromGrupoLiberacao($grupoId, $exameId)
    {
        $grupoLiberacaoExame = GrupoLiberacaoExame::where('la72_grupoliberacao', $grupoId)
            ->where('la72_labexame', $exameId)->first();

        if (is_null($grupoLiberacaoExame)) {
            return false;
        }

        if (!$grupoLiberacaoExame->delete()) {
            return false;
        }

        return true;
    }

    /**
     * Exclui um grupo de liberação.
     * @param  GrupoLiberacao  $grupo
     * @return bool
     * @throws \Exception
     */
    public function excluirGrupoLiberacao(GrupoLiberacao $grupo)
    {
        if (!$grupo->delete()) {
            return false;
        }
        return true;
    }

    /**
     * Retorna os exames vinculados ao grupo de liberação ou falso caso não haja exames.
     * @param  GrupoLiberacao  $grupo
     * @return Exame[]|bool
     */
    public function getExamesByGrupoLiberacao(GrupoLiberacao $grupo)
    {
        if ($grupo->exames->count() == 0) {
            return false;
        }
        return $grupo->exames;
    }

    /**
     * Busca um grupo por seu ID.
     * @param int $grupoId
     * @return GrupoLiberacao|GrupoLiberacao[]|bool|Collection|Model|mixed
     */
    public function getGrupoById($grupoId)
    {
        $grupo = GrupoLiberacao::find($grupoId);

        if (empty($grupo)) {
            return false;
        }
        return $grupo;
    }
}
