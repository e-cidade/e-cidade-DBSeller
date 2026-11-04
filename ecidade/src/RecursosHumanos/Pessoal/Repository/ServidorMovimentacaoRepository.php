<?php

namespace ECidade\RecursosHumanos\Pessoal\Repository;

use cl_rhpessoalmov;
use DateTime;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorMovimentacao;
use Exception;
use Instituicao;

class ServidorMovimentacaoRepository
{
    /**
     * @var array
     */
    protected $scopes = array();

    /**
     * @param ServidorMovimentacao $servidorMovimentacao
     * @return ServidorMovimentacao
     * @throws Exception
     */
    public static function save(ServidorMovimentacao $servidorMovimentacao)
    {
        $dao = new cl_rhpessoalmov();
        $dao->rh02_instit = $servidorMovimentacao->getInstituicao() instanceof Instituicao
            ? $servidorMovimentacao->getInstituicao()->getSequencial()
            : null;
        $dao->rh02_seqpes = $servidorMovimentacao->getSequencial();
        $dao->rh02_anousu = $servidorMovimentacao->getAno();
        $dao->rh02_mesusu = $servidorMovimentacao->getMes();
        $dao->rh02_regist = $servidorMovimentacao->getMatricula();
        $dao->rh02_codreg = $servidorMovimentacao->getRegime();
        $dao->rh02_tipsal = $servidorMovimentacao->getTipoSalario();
        $dao->rh02_folha = $servidorMovimentacao->getFolha();
        $dao->rh02_fpagto = $servidorMovimentacao->getFormaPagamento();
        $dao->rh02_tbprev = $servidorMovimentacao->getTabelaCalculoPrevidencia();
        $dao->rh02_hrsmen = $servidorMovimentacao->getHorasMensais();
        $dao->rh02_hrssem = $servidorMovimentacao->getHorasSemanais();
        $dao->rh02_ocorre = $servidorMovimentacao->getAgentesNocivos();
        $dao->rh02_equip = $servidorMovimentacao->isRecebeComplementacaoSalarial() ? 't' : 'f';
        $dao->rh02_tpcont = $servidorMovimentacao->getTipoContrato();
        $dao->rh02_vincrais = $servidorMovimentacao->getVinculo();
        $dao->rh02_salari = $servidorMovimentacao->getSalario();
        $dao->rh02_lota = $servidorMovimentacao->getLotacao();
        $dao->rh02_funcao = $servidorMovimentacao->getFuncao();
        $dao->rh02_rhtipoapos = $servidorMovimentacao->getTipoAposentadoriaPensao();
        $dao->rh02_validadepensao = $servidorMovimentacao->getValidadePensao() instanceof DateTime
            ? $servidorMovimentacao->getValidadePensao()->format('Y-m-d')
            : null;
        $dao->rh02_deficientefisico = $servidorMovimentacao->isDeficienteFisico() ? 't' : 'f';
        $dao->rh02_portadormolestia = $servidorMovimentacao->isPortadorMolestia() ? 't' : 'f';
        $dao->rh02_datalaudomolestia = $servidorMovimentacao->getDataLaudoMolestia() instanceof DateTime
            ? $servidorMovimentacao->getDataLaudoMolestia()->format('Y-m-d')
            : null;
        $dao->rh02_tipodeficiencia = $servidorMovimentacao->getTipoDeficiencia();
        $dao->rh02_abonopermanencia = $servidorMovimentacao->isPermanenciaAbonada() ? 't' : 'f';
        $dao->rh02_diasgozoferias = $servidorMovimentacao->getDiasGozoFerias();
        $dao->rh02_horasdiarias = $servidorMovimentacao->getHorasDiarias();
        $dao->rh02_onus = $servidorMovimentacao->getOnus();
        $dao->rh02_ressarcimento = $servidorMovimentacao->getRessarcimento();
        $dao->rh02_datacedencia = $servidorMovimentacao->getDataCedencia() instanceof DateTime
            ? $servidorMovimentacao->getDataCedencia()->format('Y-m-d')
            : null;
        $dao->rh02_cnpjcedencia = $servidorMovimentacao->getCnpjCedencia();
        $dao->rh02_cedencia = $servidorMovimentacao->getCedencia();
        $dao->rh02_regimejornadatrabalho = $servidorMovimentacao->getRegimeJornadaTrabalho();
        $dao->rh02_dataabonopermanencia = $servidorMovimentacao->getDataPermanenciaAbonada() instanceof DateTime
            ? $servidorMovimentacao->getDataPermanenciaAbonada()->format('Y-m-d')
            : null;
        $dao->rh02_sitpagbeneficio = $servidorMovimentacao->isPensaoJudicial() ? 't' : 'f';
        $dao->rh02_descinstrumento = $servidorMovimentacao->getDescricaoInstrumento();

        if ($servidorMovimentacao->getSequencial() && $servidorMovimentacao->getInstituicao()) {
            $dao->alterar(
                $servidorMovimentacao->getSequencial(),
                $servidorMovimentacao->getInstituicao()->getSequencial()
            );
        } else {
            $dao->incluir(null, null);
        }

        if ($dao->erro_status === '0') {
            throw new Exception("Não foi possível salvar as informações.\nContate o suporte.");
        }

        $servidorMovimentacao->setSequencial($dao->rh02_seqpes);

        return $servidorMovimentacao;
    }

    /**
     * @param int $ano
     * @param string $operator
     * @return $this
     */
    public function scopeAno($ano, $operator = '=')
    {
        $this->scopes['rh02_anousu'] = "rh02_anousu {$operator} {$ano}";
        return $this;
    }

    /**
     * @param int $seqpes
     * @param string $operator
     * @return $this
     */
    public function scopeSeqPes($seqpes, $operator = '=')
    {
        $this->scopes['rh02_seqpes'] = "rh02_seqpes {$operator} {$seqpes}";
        return $this;
    }

    /**
     * @param int $mes
     * @param string $operator
     * @return $this
     */
    public function scopeMes($mes, $operator = '=')
    {
        $this->scopes['rh02_mesusu'] = "rh02_mesusu {$operator} {$mes}";
        return $this;
    }

    /**
     * @param int $matricula
     * @param string $operator
     * @return $this
     */
    public function scopeMatricula($matricula, $operator = '=')
    {
        $this->scopes['rh02_regist'] = "rh02_regist {$operator} {$matricula}";
        return $this;
    }

    /**
     * @param array $columns
     * @return ServidorMovimentacao|null
     * @throws Exception
     */
    public function first($columns = array('*'))
    {
        $registros = $this->get($columns);
        return count($registros) > 0
            ? array_shift($registros)
            : null;
    }

    /**
     * @param array $columns
     * @return ServidorMovimentacao[]
     * @throws Exception
     */
    public function get($columns = array('*'))
    {
        $dao = new cl_rhpessoalmov();
        $sql = $dao->sql_query_file_regime(
            null,
            null,
            implode(', ', $columns),
            null,
            implode(' AND ', $this->scopes)
        );
        $resultado = db_query($sql);

        if (!$resultado) {
            throw new Exception(
                "Não foi possível buscar as movimentações do servidor.\nContate o suporte."
            );
        }

        $registros = array();

        if (pg_num_rows($resultado) === 0) {
            return $registros;
        }

        while ($registro = pg_fetch_array($resultado)) {
            $registros[] = ServidorMovimentacao::fromState($registro);
        }

        return $registros;
    }

    /**
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getScope($key)
    {
        return array_key_exists($key, $this->scopes) ? $this->scopes[$key] : null;
    }
}
