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

namespace App\Domain\Educacao\Escola\Resources;

use App\Domain\Educacao\Escola\Enums\MedidaFrequenciaEnum;
use App\Domain\Educacao\Escola\Models\Turma;
use App\Domain\Educacao\Escola\Models\TurmaTurnoReferente;
use App\Domain\Educacao\Escola\Models\Turno;
use App\Domain\Educacao\Escola\Models\TurnoReferente;
use App\Domain\Educacao\Secretaria\Resources\EnsinoResource;
use TurmaRepository;
use EtapaRepository;

class TurmaResource
{
    public static function toResponse(Turma $turma, $relations = [])
    {
        $obj = [
            'codigo' => $turma->ed57_i_codigo,
            'nome' => trim($turma->ed57_c_descr),
            'turno' => TurnoResource::toResponse($turma->turno),
            'ensino' => EnsinoResource::toResponse($turma->base->curso->ensino),
            'medidaFrequencia' => (new MedidaFrequenciaEnum(trim($turma->ed57_c_medfreq)))->descricao(),
        ];

        $oTurma = TurmaRepository::getTurmaByCodigo($turma->getCodigo());

        foreach ($relations as $relation => $valor) {
            switch ($valor) {
                case 'turnoReferente':
                    if (!is_null($turma->turnosReferentes)) {
                        $obj['turnoReferente'] = $turma->turnosReferentes
                        ->map(function (TurmaTurnoReferente $turnoRef) {
                            return (array)$turnoRef->turnoReferente->referencia->toOject();
                        });
                    }
                    break;
                case 'etapa':
                    if (!is_null($turma->etapa)) {
                        $oEtapa = EtapaRepository::getEtapaByCodigo($turma->etapa->ed11_i_codigo);
                        $procedimento = $oTurma->getProcedimentoDeAvaliacaoDaEtapa($oEtapa)
                            ->getFormaAvaliacao()
                            ->getTipo();

                        $obj['etapas'] = EtapaResource::toResponse($turma->etapa);
                        $obj['procedimentos'] = (object)['procedimento' => $procedimento];
                    }
                    if (count($turma->getEtapas()) > 0) {
                        $obj['etapas'] = [];
                        $obj['procedimentos'] = [];
                        foreach ($turma->getEtapas() as $etapa) {
                            $oEtapa = EtapaRepository::getEtapaByCodigo($etapa->ed11_i_codigo);
                            $procedimento =  $oTurma->getProcedimentoDeAvaliacaoDaEtapa($oEtapa)
                                ->getFormaAvaliacao()
                                ->getTipo();

                            $obj['etapas'][] = EtapaResource::toResponse($etapa);
                            $obj['procedimentos'][] = (object)['procedimento' => $procedimento];
                        }
                    }
                    break;
            }
        }

        return (object) $obj;
    }
}
