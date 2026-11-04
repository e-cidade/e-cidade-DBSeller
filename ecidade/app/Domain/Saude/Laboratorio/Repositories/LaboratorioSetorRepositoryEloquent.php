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
use App\Domain\Saude\Laboratorio\Models\LaboratorioSetor;
use App\Domain\Saude\Laboratorio\Repositories\Contracts\LaboratorioSetorRepository;

class LaboratorioSetorRepositoryEloquent extends BaseRepository implements LaboratorioSetorRepository
{
    /**
     * Verifica se o exame pertence ao setor passado.
     * @param int $exameId
     * @param int $grupoSetorId
     * @return bool
     */
    public function exameValidoParaGrupoService($exameId, $grupoSetorId)
    {
        $exameValido = false;
        $laboratorioSetor = LaboratorioSetor::with(['exames'])->where('la24_i_setor', $grupoSetorId)->get();

        foreach ($laboratorioSetor as $setor) {
            if ($exameValido) {
                break;
            }

            foreach ($setor->exames as $exame) {
                if ($exame->la08_i_codigo == $exameId) {
                    $exameValido = true;
                    break;
                }
            }
        }

        if (!$exameValido) {
            return false;
        }
        return true;
    }
}
