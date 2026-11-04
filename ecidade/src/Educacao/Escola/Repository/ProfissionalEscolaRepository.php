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

namespace ECidade\Educacao\Escola\Repository;

use cl_rechumano;
use DBDate;
use ECidade\Educacao\Escola\Model\ProfissionalEscola;
use Escola;
use Exception;

/**
 * Class ProfissionalEscolaRepository
 * @package ECidade\Educacao\Escola\Repository
 */
class ProfissionalEscolaRepository extends Repository
{
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
     * @return ProfissionalEscola
     * @throws Exception
     */
    public function findByCpf($cpf)
    {
        $this->resetScopes();
        $this->scopeCpf($cpf);
        return $this->get();
    }

    /**
     * Filtra os profissionais que não tem ausencia até a data informada
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
    public function scopeDiretorProfessorMonitor(DBDate $data)
    {
        $this->scopes['diretor_professor_monitor'] = "
            exists(
              select 1
                from rechumanoativ
                join atividaderh on atividaderh.ed01_i_codigo = rechumanoativ.ed22_i_atividade
                join funcaoatividade on atividaderh.ed01_funcaoatividade = funcaoatividade.ed119_sequencial
               where rechumanoativ.ed22_i_rechumanoescola = origem_profissinal.ed75_i_codigo
                and (   ed01_funcaoatividade in (2, 3, 4)
                     or ed01_i_funcaoadmin = 2
                     or (ed01_funcaoatividade in (1, 5, 6) and ed01_c_regencia = 'S')
                    )
                and ed22_datainicio <= '{$data->getDate()}'
                and (ed22_datafim is null or ed22_datafim >= '{$data->getDate()}'
               )  and (funcaoatividade.ed119_docente is true or funcaoatividade.ed119_sequencial = 2)
            )
        ";
    }

    /**
     * Filtra profissionais sem o código INEP
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
     * @return null|ProfissionalEscola
     * @throws Exception
     */
    public function first()
    {
        $registros = $this->get();

        return empty($registros) ? null : array_shift($registros);
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
     * @return ProfissionalEscola[]|null
     * @throws Exception
     */
    public function get()
    {
        $dao = new cl_rechumano();
        $sql = $dao->sqlProfissionais($this->scopes);
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar os profissionais da escola.");
        }

        if (pg_num_rows($rs) === 0) {
            return null;
        }

        $profissionais = [];
        while ($state = pg_fetch_array($rs)) {
            $profissionais[] = ProfissionalEscola::fromState($state);
        }

        return $profissionais;
    }

    /**
     * @param $codigoCgm
     * @return $this
     */
    public function scopeCgm($codigoCgm)
    {
        $this->scopes['cgm'] = "rhpessoal.rh01_numcgm = {$codigoCgm} or rechumanocgm.ed285_i_cgm = {$codigoCgm}";
        return $this;
    }

    /**
     * @param int $cpf
     * @return ProfissionalEscolaRepository
     */
    public function scopeCpf($cpf)
    {
        $this->scopes['cpf'] = "rh_cgm.z01_cgccpf = '{$cpf}' or rec_cgm.z01_cgccpf = '{$cpf}'";
        return $this;
    }

    /**
     * @return $this
     */
    public function scopeAtivo()
    {
        $this->scopes['isAtivo'] = "ed75_i_saidaescola is null or ed75_i_saidaescola > CURRENT_DATE";
        return $this;
    }
}
