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

namespace App\Domain\Educacao\Escola\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Educacao\Escola\Models\ProfissionalEscola;
use App\Domain\Educacao\Escola\Models\Views\ProfissionaisEscolasView;
use App\Domain\Educacao\Escola\Models\Views\TurmasRegenteView;
use App\Domain\Educacao\Escola\Resources\TurmasRegentesViewResource;
use ECidade\Educacao\Escola\Model\ProfissionalEscola as ProfissionalEscolaLegacy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use DBDate;
use Exception;
use cl_rechumano;
use Escola;

class ProfissionalEscolaRepository extends BaseRepository
{
    public $scopes = [];
    protected $modelClass = ProfissionaisEscolasView::class;

    public function resetScopes()
    {
        $this->scopes = [];
    }

    public function getProfissionaisEscola($filtros)
    {
        $filtros = is_object($filtros) ? (array) $filtros : $filtros;
        $query = $this->newQuery()->apenasAtivos()->apenasUsuarioInterno();
        foreach ($filtros as $chave => $filtro) {
            switch ($chave) {
                case 'escola':
                    $query->where('cod_escola', $filtro);
                    break;
                case 'regente':
                    $query->where('is_regente', $filtro);
                    break;
                case 'cgm':
                    $query->where('cod_cgm', $filtro);
                    break;
                case 'cgms':
                    $query->whereIn('cod_cgm', $filtro);
                    break;
                case 'nome':
                    $query->whereHas('cgm', function (Builder $qeury) use ($filtro) {
                        $qeury->where('z01_nome', 'ILIKE', "%{$filtro}%");
                    });
                    break;
                case 'matricula':
                    $query->where('matricula', $filtro);
                    break;
                case 'cpf':
                    $query->whereHas('cgm', function (Builder $qeury) use ($filtro) {
                        $qeury->where('z01_cgccpf', $filtro);
                    });
                    break;
                case 'disciplina':
                    $query->whereHas('profissional', function (Builder $qeury) use ($filtro) {
                        $qeury->whereHas('regenciasHorario', function (Builder $qeury) use ($filtro) {
                            $qeury->whereHas('regencia', function (Builder $qeury) use ($filtro) {
                                $qeury->whereHas('disciplinaEnsino', function (Builder $qeury) use ($filtro) {
                                     $qeury->whereHas('disciplina', function (Builder $qeury) use ($filtro) {
                                         $qeury->where('ed232_i_codigo', $filtro);
                                     });
                                });
                            });
                        });
                    });
                    break;
            }
        }
        if (array_key_exists('items', $filtros)) {
            $total = $query->count();
            $regentes = $query->paginate($filtros['items'])->map(function ($retorno) use ($total) {
                $retorno->total = $total;
                return $retorno;
            });
        } else {
            $regentes = $query->get()->map(function ($retorno) {
                return $retorno;
            });
        }
        return $regentes;
    }

    public function getTurmasRegente($cgm, $escola, Carbon $dataSistema = null, $periodos = false)
    {
        $turmasQuery = TurmasRegenteView
            ::where('cgm', $cgm)
            ->where('escola_professor', $escola);
        if ($dataSistema !== null) {
            $turmasQuery->whereRaw(
                "'{$dataSistema->format('Y-m-d')}'
                    between inicio_calendario and fim_calendario"
            );
        }

        $turmas = $turmasQuery->get();

        return TurmasRegentesViewResource::toResponse($turmas, $periodos);
    }

    public function getCalendariosTurmasRegente(ProfissionaisEscolasView $profissionalEscola, $dataSistema = null)
    {
        $turmas = $this->getTurmasRegente($profissionalEscola->cod_cgm, $profissionalEscola->cod_escola, $dataSistema);
        $calendarios = [];
        foreach ($turmas as $trm) {
            $calendario = $trm->calendario;
            $calendario->aTurmas = [];
            $calendarios[$calendario->codigo] = $calendario;
        }
        foreach ($turmas as $trm) {
            $turma = (object) [
                'codigo' => $trm->codigo,
                'nome' => trim($trm->nome),
                'turno' => $trm->turno,
                'ensino' => $trm->ensino,
                'medidaFrequencia' => $trm->medidaFrequencia,
                'etapas' => $trm->etapas
            ];
            $calendarios[$trm->calendario->codigo]->aTurmas[$trm->codigo] = $turma;
        }
        foreach ($calendarios as $calendario) {
            $calendario->turmas = array_values($calendario->aTurmas);
        }
        return array_values($calendarios);
    }

    /**
     * @param Escola $escola
     * @param DBDate|null $dataLimiteCenso
     * @return ProfissionalEscola[]
     * @throws Exception
     */
    public function getProfissionaisAtivos(Escola $escola, DBDate $dataLimiteCenso = null)
    {
        $where = array();
        if ($dataLimiteCenso) {
            $where[] = "(ed75_d_ingresso <= '{$dataLimiteCenso->getDate()}')";
            $where[] = "(ed75_i_saidaescola is null or ed75_i_saidaescola >= '{$dataLimiteCenso->getDate()}')";
        }

        $dao = new cl_rechumano();
        $sql = $dao->sqlProfissionaisEscola(null, $escola->getCodigo(), $where, $this->scopes);
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar os profissionais da escola.");
        }

        if (pg_num_rows($rs) === 0) {
            return array();
        }

        $profissionais = array();
        while ($state = pg_fetch_array($rs)) {
            $profissionais[] = ProfissionalEscola::fromState($state);
        }

        return $profissionais;
    }

    /**
     * @param int $codigoCgm
     * @return ProfissionalEscola[]
     * @throws Exception
     */
    public function findByCgm($codigoCgm)
    {
        $this->resetScopes();
        $this->scopeCgm($codigoCgm);
        return $this->get();
    }

    /**
     * @param int $cpf
     * @return ProfissionalEscola[]
     * @throws Exception
     */
    public function findByCpf($cpf)
    {
        $this->resetScopes();
        $this->scopeCpf($cpf);
        return $this->get();
    }

    /**
     * @return null|ProfissionalEscolaLegacy
     * @throws Exception
     */
    public function first()
    {
        $registros = $this->get();

        return empty($registros) ? null : array_shift($registros);
    }

    /**
     * Filtra os profissionais que n?o tem ausencia at? a data informada
     * @param DBDate $date
     */
    public function scopeDocentePresente(DBDate $date = null)
    {
        if (is_null($date)) {
            $this->scopes['docente_presente'] = "
            not exists(
             select 1
              from docenteausencia
               where docenteausencia.ed321_rechumano = origem_profissinal.ed20_i_codigo
                 and docenteausencia.ed321_escola = origem_profissinal.ed75_i_escola
                )
            ";
        } else {
            $this->scopes['docente_presente'] = "
                not exists(
                 select 1
                  from docenteausencia
                   where docenteausencia.ed321_rechumano = origem_profissinal.ed20_i_codigo
                     and docenteausencia.ed321_escola = origem_profissinal.ed75_i_escola
                     and (    (ed321_inicio <= '{$date->getDate()}' and ed321_final is null)
                           or ('{$date->getDate()}' between ed321_inicio and ed321_final)
                         )
                 )
            ";
        }
    }

    /**
     * Filtra os:
     * - Professores: (ed01_funcaoatividade = 1, 5 e 6 and ed01_c_regencia = 'S')
     * - Diretores: ed01_i_funcaoadmin = 2
     * - Outros (Monitores, Assistente educacional e Monitores): ed01_funcaoatividade in (2, 3, 4)
     */
    public function scopeDiretorProfessorMonitor()
    {
        $this->scopes['diretor_professor_monitor'] = "
            exists(
              select 1
                from rechumanoativ
                join atividaderh on atividaderh.ed01_i_codigo = rechumanoativ.ed22_i_atividade
               where rechumanoativ.ed22_i_rechumanoescola = origem_profissinal.ed75_i_codigo
                and (   ed01_funcaoatividade in (2, 3, 4)
                     or ed01_i_funcaoadmin = 2
                     or (ed01_funcaoatividade in (1, 5, 6) and ed01_c_regencia = 'S')
                    )
            )
        ";
    }

    /**
     * Filtra profissionais sem o c?digo INEP
     */
    public function scopeSemCodigoInep()
    {
        $this->scopes['inep'] = "ed20_i_codigoinep is null";
    }

    public function atualizarINEP(ProfissionalEscola $profissionalEscola)
    {
        $inep = $profissionalEscola->getCodigoInep();
        $inep = !empty($inep) ? $inep : 'null';

        $sql = "
            UPDATE rechumano
               SET ed20_i_codigoinep = {$inep}
             WHERE ed20_i_codigo = {$profissionalEscola->getCodigoRecursoHumano()}
        ";

        $rs = db_query($sql);

        if (!$rs || pg_affected_rows($rs) === 0) {
            throw new Exception("Erro ao atualizar INEP do profissional.");
        }

        return true;
    }

    /**
     * @param $codigo
     * @return ProfissionalEscolaRepository
     */
    public function scopeVinculoEscola($codigo)
    {
        $this->scopes['vinculo_escola'] = "ed75_i_codigo = {$codigo}";
        return $this;
    }

    /**
     * @param $codigoEscola
     * @return ProfissionalEscolaRepository
     */
    public function scopeEscola($codigoEscola)
    {
        $this->scopes['escola'] = "ed75_i_escola = {$codigoEscola}";

        return $this;
    }

    /**
     * @return ProfissionalEscolaLegacy[]|null
     * @throws Exception
     */
    public function get()
    {
        $dao = new cl_rechumano();
        $sql = $dao->sqlProfissionais($this->scopes);
        $rs  = db_query($sql);

        if (!$rs) {
            throw new Exception("Usuário não possui permissão para utilizar esta funcionalidade.");
        }

        if (pg_num_rows($rs) === 0) {
            throw new Exception("Usuário não possui permissão para utilizar esta funcionalidade.");
        }

        $profissionais = [];

        while ($state = pg_fetch_array($rs)) {
            $profissionais[] = ProfissionalEscolaLegacy::fromState($state);
        }

        return $profissionais;
    }

    /**
     * @param $codigoCgm
     * @return ProfissionalEscolaRepository
     */
    public function scopeCgm($codigoCgm)
    {
        $this->scopes['cgm'] = "(rhpessoal.rh01_numcgm = {$codigoCgm} or rechumanocgm.ed285_i_cgm = {$codigoCgm})";
        return $this;
    }

    /**
     * @param int $cpf
     * @return ProfissionalEscolaRepository
     */
    private function scopeCpf($cpf)
    {
        $this->scopes['cpf'] = "rh_cgm.z01_cgccpf = '{$cpf}' or rec_cgm.z01_cgccpf = '{$cpf}'";
        return $this;
    }

    /**
     * @return ProfissionalEscolaRepository
     */
    public function scopeAtivo()
    {
        $this->scopes['isAtivo'] = "ed75_i_saidaescola is null or ed75_i_saidaescola > CURRENT_DATE";
        return $this;
    }
}
