<?php


namespace ECidade\RecursosHumanos\Pessoal\Repository;

use cl_rhvinculoaprendiz;
use Exception;
use ECidade\RecursosHumanos\Pessoal\Model\MenorAprendiz;

class MenorAprendizRepository
{
    /**
     * @var array
     */
    private $scopes = array();

    public static function menorAprendiz()
    {
        return new MenorAprendiz();
    }

    /**
     * @param array|int $matricula
     * @return int
     * @throws Exception
     */
    public static function destroy($ids)
    {
        $count = 0;
        $ids = is_array($ids) ? $ids : func_get_args();

        $self = new self();

        foreach ($ids as $id) {
            $self->delete(self::find($id));
            $count++;
        }

        return $count;
    }

    /**
     * @param MenorAprendiz|null $menorAprendiz
     * @throws Exception
     */
    public function delete(MenorAprendiz $menorAprendiz = null)
    {
        $id = $menorAprendiz instanceof MenorAprendiz ? $menorAprendiz->getCodigo() : null;
        $dao = new cl_rhvinculoaprendiz();
        $dao->excluir($id, implode(' AND ', $this->scopes));

        if ($dao->erro_status === '0') {
            throw new Exception("Não foi possível excluir.\nContate o suporte.");
        }
    }

    /**
     * @param $id
     * @param array $columns
     * @return bool|MenorAprendiz
     * @throws Exception
     */
    public static function find($id, $columns = array('*'))
    {
        $dao = new cl_rhvinculoaprendiz();
        $sql = $dao->sql_query($id, implode(', ', $columns));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar o vinculo de Menor Aprendiz do servidor.\nContate o suporte.");
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        $resultado = pg_fetch_array($rs);

        return MenorAprendiz::fromState($resultado);
    }

    /**
     * @param $id
     * @param array $columns
     * @return bool|MenorAprendiz
     * @throws Exception
     */
    public static function findByMatricula($matricula, $columns = array('*'))
    {
        $dao = new cl_rhvinculoaprendiz();
        $sql = $dao->sql_query(null, implode(', ', $columns), null, "rh312_matricula = {$matricula}");
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar o vinculo de Menor Aprendiz do servidor.\nContate o suporte.");
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        $resultado = pg_fetch_array($rs);

        return MenorAprendiz::fromState($resultado);
    }

    /**
     * @param array $columns
     * @return ServidorProcessosJudiciaisFolha[]
     * @throws Exception
     */
    public function all($columns = array('*'))
    {
        $dao = new cl_rhvinculoaprendiz();
        $sql = $dao->sql_query(null, implode(', ', $columns));
        $rs = db_query($sql);

        $menorAprendiz = array();

        if (pg_num_rows($rs) === 0) {
            return $menorAprendiz;
        }

        while ($dado = pg_fetch_array($rs)) {
            $menorAprendiz[] = MenorAprendiz::fromState($dado);
        }

        return $menorAprendiz;
    }

    /**
     * @return MenorAprendiz[]
     * @throws Exception
     */
    public function get()
    {
        $dao = new cl_rhvinculoaprendiz();
        $sql = $dao->sql_query(null, '*', null, implode(' AND ', $this->scopes));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar o vinculo de Menor Aprendiz do servidor.\nContate o suporte.");
        }

        $menorAprendiz = array();

        if (pg_num_rows($rs) === 0) {
            return $menorAprendiz;
        }

        while ($processo = pg_fetch_array($rs)) {
            $menorAprendiz[] = MenorAprendiz::fromState($processo);
        }

        return $menorAprendiz;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function count()
    {
        $dao = new cl_rhvinculoaprendiz();
        $sql = $dao->sql_query(null, 'count(*)', null, implode(' AND ', $this->scopes));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar o vinculo de Menor Aprendiz do servidor.\nContate o suporte.");
        }

        return (int)pg_fetch_result($rs, 0, 'count');
    }

    /**
     * @param MenorAprendiz $menorAprendiz
     * @return MenorAprendiz
     * @throws Exception
     */
    public function save(MenorAprendiz $menorAprendiz)
    {
        $dao = new cl_rhvinculoaprendiz();
        $dao->rh312_sequencial = $menorAprendiz->getCodigo();
        $dao->rh312_matricula = $menorAprendiz->getMatricula();
        $dao->rh312_instit = $menorAprendiz->getCodigoInstituicao();
        $dao->rh312_modalidade = $menorAprendiz->getModalidade();
        $dao->rh312_cnpjqualificadora = $menorAprendiz->getCnpjDireta();
        $dao->rh312_cnpjefetivada = $menorAprendiz->getCnpjIndireta();
        $dao->rh312_cnpjpratica = $menorAprendiz->getCnpjPratica();
        $dao->rh312_sequencial ? $dao->alterar($menorAprendiz->getCodigo()) : $dao->incluir(null);

        if ($dao->erro_status === '0') {
            dd($dao);
            throw new Exception("Não foi possível salvar os dados de Menor Aprendiz do servidor.\nContate o suporte.");
        }

        $menorAprendiz->setCodigo($dao->rh312_sequencial);

        return $menorAprendiz;
    }

    /**
     * @param $sequencial
     * @param string $operator
     * @return $this
     */
    public function scopeSequencial($sequencial, $operator = '=')
    {
        $this->scopes['sequencial'] = "rh226_sequencial {$operator} {$sequencial}";
        return $this;
    }

    /**
     * @param $instituicao
     * @param string $operator
     * @return $this
     */
    public function scopeInstituicao(Instituicao $instituicao, $operator = '=')
    {
        $this->scopes['instituicao'] = "rh226_instituicao {$operator} {$instituicao->getCodigo()}";
        return $this;
    }

    /**
     * @param $ano
     * @param string $operator
     * @return $this
     */
    public function scopeAno($ano, $operator = '=')
    {
        $this->scopes['ano'] = "rh226_ano {$operator} {$ano}";
        return $this;
    }

    /**
     * @param $mes
     * @param string $operator
     * @return $this
     */
    public function scopeMes($mes, $operator = '=')
    {
        $this->scopes['mes'] = "rh226_mes {$operator} {$mes}";
        return $this;
    }

    /**
     * @param $servidor
     * @param string $operator
     * @return $this
     */
    public function scopeServidor(Servidor $servidor, $operator = '=')
    {
        $this->scopes['servidor'] = "rh226_matricula {$operator} {$servidor->getMatricula()}";
        return $this;
    }

    /**
     * @param $tipoProcesso
     * @param string $operator
     * @return $this
     */
    public function scopeTipoProcesso($tipoProcesso, $operator = '=')
    {
        $this->scopes['tipoProcesso'] = "rh226_tipoprocesso {$operator} {$tipoProcesso}";
        return $this;
    }

    /**
     * @param $numeroProcesso
     * @param string $operator
     * @return $this
     */
    public function scopeNumeroProcesso($numeroProcesso, $operator = '=')
    {
        $this->scopes['numeroProcesso'] = "rh226_numero {$operator} {$numeroProcesso}";
        return $this;
    }

    /**
     * @param $indicativoSuspensao
     * @param string $operator
     * @return $this
     */
    public function scopeIndicativoSuspensao($indicativoSuspensao, $operator = '=')
    {
        $this->scopes['indicativoSuspensao'] = "rh226_indicativosuspensao {$operator} {$indicativoSuspensao}";
        return $this;
    }

    /**
     * @return $this
     */
    public function resetScopes()
    {
        $this->scopes = array();
        return $this;
    }

    /**
     * @param $key
     * @return MenorAprendizRepository
     */
    public function removeScope($key)
    {
        if (array_key_exists($key, $this->scopes)) {
            unset($this->scopes[$key]);
        }

        return $this;
    }
}
