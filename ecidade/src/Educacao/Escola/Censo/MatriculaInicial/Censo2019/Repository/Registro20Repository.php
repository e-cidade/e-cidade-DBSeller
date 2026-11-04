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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Repository;

use cl_dialetivo;
use cl_regencia;
use cl_turma;
use cl_turmaac;
use cl_turmaacativ;
use cl_turmaacmatricula;
use cl_turmaatividadecomplementar;
use cl_turmacenso;
use DBDate;
use ECidade\Educacao\Escola\Censo\Censo;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\TurmaCensoDisciplinaVO;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\TurmaCensoVo;
use ECidade\Educacao\Escola\Model\CensoDisciplina;
use ECidade\Educacao\Escola\Repository\Repository;
use Escola;
use Exception;

/**
 * Class Registro20Repository
 * @package ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Repository
 */
class Registro20Repository extends Repository
{
    /**
     * @param Escola $escola
     * @param DBDate $data
     * @return array
     * @throws Exception
     */
    public function getTurmasMultietapaEnsinoDiferente(Escola $escola, DBDate $data)
    {
        $campos = "ed342_sequencial, ed134_censoetapa, ed343_turma, ed343_principal, ed342_nome";
        $where = array(
            "ed57_i_escola = {$escola->getCodigo()}",
            "ed134_ano = {$data->getAno()}",
            "ed52_i_ano = {$data->getAno()}",
        );
        $dao = new cl_turmacenso();
        $sql = $dao->sql_censo(null, $campos, null, implode(' and ', $where));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar Turmas Multietapa de Ensinos Diferentes");
        }

        if (pg_num_rows($rs) === 0) {
            return array();
        }


        return pg_fetch_all($rs);
    }

    /**
     * @param array $campos
     * @return TurmaCensoVo[]
     * @throws Exception
     */
    public function getTurmasRegulares($campos = array('*'))
    {
        $dao = new cl_turma();
        $where = implode(' and ', $this->scopes);
        $sql = $dao->sql_query(null, implode(', ', $campos), null, $where);
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar turmas regulares.");
        }

        if (pg_num_rows($rs) === 0) {
            return array();
        }

        $turmas = array();
        while ($state = pg_fetch_array($rs)) {
            $turmas[] = TurmaCensoVo::fromState($state);
        }
        return $turmas;
    }

    /**
     * @param array $campos
     * @return TurmaCensoVo[]
     * @throws Exception
     */
    public function getTurmasEspeciais($campos = array('*'))
    {
        $dao = new cl_turmaac();
        $sql = $dao->sql_query(null, implode(', ', $campos), null, implode(' and ', $this->scopes));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar turmas regulares.");
        }

        if (pg_num_rows($rs) === 0) {
            return array();
        }

        $turmas = array();
        while ($state = pg_fetch_array($rs)) {
            $turmas[] = TurmaCensoVo::fromState($state);
        }
        return $turmas;
    }

    /**
     * @param DBDate $dada
     * @return $this
     */
    public function scopeExistsMatricula(DBDate $dada)
    {
        $this->scopes['exists_matricula'] = "
            exists( select 1
             from matricula
             where ed60_i_turma = ed57_i_codigo
               and extract(year from ed60_d_datamatricula) = {$dada->getAno()}
               and ed60_d_datamatricula <= '{$dada->getDate()}'
               and ((ed60_c_situacao = 'MATRICULADO' and ed60_d_datasaida is null) OR
                    (ed60_c_situacao != 'MATRICULADO' and ed60_d_datasaida > '{$dada->getDate()}'))
                    )
        ";
        return $this;
    }

    /**
     * @param $ano
     * @param string $operador
     * @return $this
     */
    public function scopeAnoCalendario($ano, $operador = '=')
    {
        $this->scopes['ano'] = "ed52_i_ano {$operador} {$ano}";
        return $this;
    }

    /**
     * @param Escola $escola
     * @param string $operador
     * @return $this
     */
    public function scopeEscola(Escola $escola, $operador = '=', $campo = 'ed57_i_escola')
    {
        $this->scopes['escola'] = "{$campo} {$operador} {$escola->getCodigo()}";
        return $this;
    }

    /**
     * @param Escola $escola
     * @return array
     * @throws Exception
     */
    public function getDiasLetivo(Escola $escola)
    {
        $where = "ed04_i_escola = {$escola->getCodigo()} and ed04_c_letivo = 'S'";
        $dao = new cl_dialetivo();
        $rs = db_query($dao->sql_query(null, "trim(ed32_c_descr) dia", null, $where));

        if (!$rs) {
            throw new Exception("Erro ao buscar dias letivos.");
        }

        return pg_fetch_all_columns($rs);
    }

    /**
     * @param $codigoTurma
     * @return CensoDisciplina[]
     * @throws Exception
     */
    public function getDisciplinasCensoTurma($codigoTurma, DBDate $data)
    {
        $campos = array(
            "distinct ed294_censodisciplina",
            "ed59_tipobase",
            'ed59_unidade_curricular',
            "exists(
              select 1 from regenciahorario
                where ed58_i_regencia = regencia.ed59_i_codigo
                    and '{$data->getDate()}' between ed58_datainicio and ed58_datafim
                    and not exists(select 1 from docenteausencia
                where docenteausencia.ed321_rechumano = regenciahorario.ed58_i_rechumano
                    and docenteausencia.ed321_escola = turma.ed57_i_escola
                    and (    (ed321_inicio <= '{$data->getDate()}' and ed321_final is null)
                       or ('{$data->getDate()}' between ed321_inicio and ed321_final)
                     )
                )) as oferece"
        );
        $where = "ed59_i_turma = {$codigoTurma}";
        $dao = new cl_regencia();
        $sql = $dao->sql_query_disciplina_censo(null, implode(', ', $campos), null, $where);

        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar disciplinas da turma");
        }

        $disciplinas = [];
        while ($state = pg_fetch_array($rs)) {
            $state['codigo_turma'] = $codigoTurma;
            $disciplinas[] = TurmaCensoDisciplinaVO::fromState($state);
        }

        return $disciplinas;
    }

    /**
     * @param $codigoTurma
     * @return array
     * @throws Exception
     */
    public function getAtividadesComplementaresTurmasRegulares($codigoTurma)
    {
        $campos = "distinct ed146_censoativcompl ";
        $where = "ed146_turma = {$codigoTurma} limit 6";
        $dao = new cl_turmaatividadecomplementar();
        $rs = db_query($dao->sql_query_file(null, $campos, null, $where));

        if (!$rs) {
            throw new Exception("Erro ao buscar atividades complementar da turma.");
        }

        return pg_fetch_all_columns($rs);
    }

    /**
     * @param TurmaCensoVo $turma
     * @return array
     * @throws Exception
     */
    public function getHorariosInicioFimTurnoTurma(TurmaCensoVo $turma)
    {
        $sql = "
            with horarios_turno as (
              select distinct ed17_h_inicio, ed17_h_fim
              from periodoescola
              where ed17_i_turno  = {$turma->getCodigoTurno()}
                and ed17_i_escola =  {$turma->getEscola()->getCodigo()}
            ), horarios_atividade as (
              select ed146_horainicial, ed146_horafinal
              from turmaatividadecomplementar
              where ed146_turma = {$turma->getCodigoTurma()}
            ), uniao_horarios as (
              select ed17_h_inicio as hora_inicio, ed17_h_fim as hora_fim
                from horarios_turno
              union all
              select to_char(ed146_horainicial, 'HH24:MI')::varchar,
                     to_char(ed146_horafinal, 'HH24:MI')::varchar
                from horarios_atividade
             ) select min(hora_inicio) as hora_inicio, max(hora_fim) as hora_fim
               from uniao_horarios
        ";
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar horários do turno da turma.");
        }

        return pg_fetch_assoc($rs);
    }

    /**
     * @param DBDate $data
     * @param string $operador
     * @param string $campo
     * @return $this
     */
    public function scopeAlunoMatriculadoTurmaEspecial(DBDate $data, $operador = '=')
    {
        $this->scopes['data'] = " exists (select 1 from turmaacmatricula where ed269_d_data <= '{$data->getDate()}')";
        return $this;
    }

    /**
     * @param TurmaCensoVo $turma
     * @param Censo $censo
     * @return boolean
     * @throws Exception
     */
    public function hasAlunosMatriculadosNaEscola(TurmaCensoVo $turma, Censo $censo)
    {
        $where = array(
            "ed269_i_turmaac = {$turma->getCodigoTurma()}",
            "ed52_i_ano = {$censo->getAno()}",
            "ed268_i_escola = {$turma->getEscola()->getCodigo()}",
        );
        $where[] = "
        exists (
            select 1
              from matricula
              join turma on turma.ed57_i_codigo = matricula.ed60_i_turma
              join calendario on calendario.ed52_i_codigo = turma.ed57_i_calendario
             where matricula.ed60_i_aluno = ed269_aluno
               and ed52_i_ano = {$censo->getAno()}
               and ed57_i_escola = {$turma->getEscola()->getCodigo()}
               and ed60_d_datamatricula <= '{$censo->getDataCenso()->getDate()}'
               and ((ed60_c_situacao = 'MATRICULADO' and ed60_d_datasaida is null) OR
                    (ed60_c_situacao != 'MATRICULADO' and ed60_d_datasaida > '{$censo->getDataCenso()->getDate()}'))
                   )
        ";
        $dao = new cl_turmaacmatricula();
        $sql = $dao->sql_query_turma(null, 'distinct 1', null, implode(' and ', $where));
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception(
                "Erro ao validar se turma de atividade complementar possui aluno matriculado na escola."
            );
        }

        return pg_num_rows($rs) > 0;
    }

    /**
     * @param TurmaCensoVo $turmaEspecial
     * @return array
     * @throws Exception
     */
    public function getAtividadesComplementarTurmaEspecial(TurmaCensoVo $turmaEspecial)
    {
        $dao = new cl_turmaacativ();
        $campos = "distinct ed267_i_censoativcompl ";
        $where = "ed267_i_turmaac = {$turmaEspecial->getCodigoTurma()} limit 6";
        $rs = db_query($dao->sql_query_file(null, $campos, null, $where));

        if (!$rs) {
            throw new Exception("Erro ao buscar atividades complementar da turma de atividade complementar.");
        }

        return pg_fetch_all_columns($rs);
    }

    /**
     * @param TurmaCensoVo $turmaEspecial
     * @return array
     * @throws Exception
     */
    public function getDiasLetivoTurmaEspecial(TurmaCensoVo $turmaEspecial)
    {
        $where = "ed346_turmaac = {$turmaEspecial->getCodigoTurma()}";
        $dao = new \cl_turmaachorarioprofissional();
        $rs = db_query($dao->sql_query(null, "distinct trim(ed32_c_descr) dia", null, $where));

        if (!$rs) {
            throw new Exception("Erro ao buscar dias da semana Turmas Especiais.");
        }

        return pg_fetch_all_columns($rs);
    }
}
