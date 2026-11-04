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
use App\Domain\Saude\Laboratorio\Models\Laboratorio;
use App\Domain\Saude\Laboratorio\Models\LaboratorioDepartamento;
use App\Domain\Saude\Laboratorio\Models\LaboratorioSetor;
use App\Domain\Saude\Laboratorio\Repositories\Contracts\LaboratorioRepository;

class LaboratorioRepositoryEloquent extends BaseRepository implements LaboratorioRepository
{
    /**
     * Recupera o laboratório através do departamento.
     * @param int $departamentoId
     * @return Laboratorio|false
     */
    public function getByDepartamento($departamentoId)
    {
        $laboratorioDepartamento = LaboratorioDepartamento::where('la03_i_departamento', $departamentoId)->first();

        if (is_null($laboratorioDepartamento)) {
            return false;
        }
        return $laboratorioDepartamento->laboratorio;
    }

    /**
     * Recupera os exames levando em conta o laboratório e setor em que se está vinculado.
     * @param int $laboratorioId
     * @param int $setorId
     * @return Exame[]
     */
    public function getLaboratorioSetorExames($laboratorioId, $setorId)
    {
        $exames = [];
        $labSetores = LaboratorioSetor::where('la24_i_laboratorio', $laboratorioId)
            ->where('la24_i_setor', $setorId)
            ->get();

        if ($labSetores->count() == 0) {
            return false;
        }

        foreach ($labSetores as $labSetor) {
            foreach ($labSetor->exames as $exame) {
                if (!array_key_exists($exame->la08_i_codigo, $exames) && $exame->la08_i_ativo === 1) {
                    $exames[$exame->la08_i_codigo] = $exame;
                }
            }
        }

        //Ordenando por ordem alfabética.
        usort($exames, function ($exameA, $exameB) {
            return strcmp($exameA->la08_c_descr, $exameB->la08_c_descr);
        });

        return $exames;
    }
}
