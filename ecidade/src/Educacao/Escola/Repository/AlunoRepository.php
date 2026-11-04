<?php
/**
 * Created by PhpStorm.
 * User: andri
 * Date: 29/04/2019
 * Time: 08:21
 */

namespace ECidade\Educacao\Escola\Repository;

use cl_aluno;
use DBDate;
use ECidade\Educacao\Escola\Model\Aluno;
use Escola;
use Exception;

class AlunoRepository extends Repository
{

    /**
     * @param $id
     * @return Aluno|null
     * @throws Exception
     */
    public static function find($id)
    {
        $dao = new cl_aluno();
        $rs = db_query($dao->sql_query_file($id));
        if (!$rs) {
            throw new Exception("Erro ao buscar o aluno.");
        }

        if (pg_num_rows($rs) === 0) {
            return null;
        }

        $aluno = Aluno::fromState(pg_fetch_array($rs));

        $alunoNecessidadeRepository = new AlunoNecessidadeEspecialRepository();
        $necessidades = $alunoNecessidadeRepository->scopeAluno($aluno)->get();
        $aluno->setNecessidades($necessidades);

        return $aluno;
    }

    /**
     * Deve retornar todos alunos matriculados em turmas regulares assim como todos alunos de turmas de NEE
     * Os Alunos de turmas de atividades complementares já foram retornados junto com os alunos matriculados visto
     * que esses alunos devem possuir matrícula na escola para irem no censo.
     *
     * @param Escola $escola
     * @param DBDate $dataCenso
     * @return Aluno[]
     * @throws Exception
     */
    public function getAlunosCenso(Escola $escola, DBDate $dataCenso = null)
    {
        $where = $this->scopes;

        $this->scopeMatriculadosTurmaRegular($escola, $dataCenso)
             ->scopeMatriculadosTurmaAC($escola, $dataCenso);

        $dao = new cl_aluno();
        $sql = $dao->sqlAlunosMatriculadosCenso(
            $this->scopes['matriculados_turma_regular'],
            $this->scopes['matriculados_turma_ac'],
            $where
        );

        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception("Erro ao buscar os alunos matriculados.");
        }

        if (pg_num_rows($rs) === 0) {
            return array();
        }

        $alunosCenso = array();
        while ($state = pg_fetch_array($rs)) {
            $alunosCenso[] = Aluno::fromState($state);
        }

        return $alunosCenso;
    }

    public function scopeMatriculadosTurmaRegular(Escola $escola, DBDate $data = null)
    {
        if (is_null($data)) {
            $this->scopes['matriculados_turma_regular'] = "
                turma.ed57_i_escola = {$escola->getCodigo()}
            ";
            return $this;
        }
        
        $this->scopes['matriculados_turma_regular'] = "
            turma.ed57_i_escola = {$escola->getCodigo()}
            AND calendario.ed52_i_ano = {$data->getAno()}
            AND ed60_d_datamatricula <= '{$data->getDate()}'
            AND ((ed60_c_situacao = 'MATRICULADO' AND ed60_d_datasaida IS NULL)
                OR (ed60_c_situacao != 'MATRICULADO' AND ed60_d_datasaida > '{$data->getDate()}'))
        ";
        return $this;
    }


    /**
     * @param Escola $escola
     * @param DBDate $data
     * @return $this
     */
    public function scopeMatriculadosTurmaAC(Escola $escola, DBDate $data = null)
    {
        if (is_null($data)) {
            $this->scopes['matriculados_turma_ac'] = "
                    ed268_i_tipoatend = 5
            ";
            return $this;
        }
        $this->scopes['matriculados_turma_ac'] = "
        ed268_i_tipoatend = 5
        and ed269_d_data <= '{$data->getDate()}'
        and ed52_i_ano = {$data->getAno()}
        and ed268_i_escola = {$escola->getCodigo()}
        ";
        return $this;
    }

    public function scopeSemCodigoInep()
    {
        $this->scopes['inep'] = " ed47_c_codigoinep = ''";
    }
}
