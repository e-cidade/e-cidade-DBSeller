<?php
/**
 * E-cidade Software Publico para Gestão Municipal
 *   Copyright (C) 2009 DBSeller Serviços de Informática Ltda
 *                          www.dbseller.com.br
 *                          e-cidade@dbseller.com.br
 *   Este programa é software livre; você pode redistribuí-lo e/ou
 *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *   publicada pela Free Software Foundation; tanto a versão 2 da
 *   Licença como (a seu critério) qualquer versão mais nova.
 *   Este programa e distribuído na expectativa de ser útil, mas SEM
 *   QUALQUER GARANTIA; sem mesmo a garantia implícita de
 *   COMERCIALIZAÇÃO ou de ADEQUAÇÃO A QUALQUER PROPÓSITO EM
 *   PARTICULAR. Consulte a Licença Pública Geral GNU para obter mais
 *   detalhes.
 *   Você deve ter recebido uma cópia da Licença Pública Geral GNU
 *   junto com este programa; se não, escreva para a Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307, USA.
 *   Cópia da licença no diretório licenca/licenca_en.txt
 *                                 licenca/licenca_pt.txt
 */

use \ECidade\Saude\Laboratorio\Repository\RequisicaoExameRepository;

const CAMINHO_MENSAGEM_REQUISICAO_LABORATORIAL = "laboratorio.RequisicaoLaboratorial";

/**
 * Class RequisicaoLaboratorial
 * Representa a tabela lab_requisicao
 * @package Laboratorio
 * @author André Mello andre.mello@dbseller.com.br
 * @version $
 */
class RequisicaoLaboratorial
{
    private $iCodigo = null;

    /**
     * Nome do médico responsável pela requisição
     * @var string
     */
    private $sMedico;

    /**
     * Array de objetos de RequisicaoExame
     * @var array RequisicaoExame
     */
    private $aRequisicaoExame;

    /**
     * Data da requisição
     * @var DBDate
     */
    private $oData;

    /**
     * @var cgs
     */
    private $cgs;

    /**
     * @var DBDepartamento
     */
    private $departamento;

    /**
     * @var UsuarioSistema
     */
    private $usuario;

    /**
     * @var string
     */
    private $responsavel;

    /**
     * @var string
     */
    private $hora;

    /**
     * @var string
     */
    private $medicamento;

    /**
     * @var string
     */
    private $diagnostico;

    /**
     * @var string
     */
    private $observacao;

    /**
     * @var string
     */
    private $autorizacao;

    /**
     * @var string
     */
    private $contato;

    /**
     * @var Laboratorio
     */
    private $laboratorio;

    /**
     * @var DBDate
     */
    private $dataUltimaMenstruacao;

    /**
     * @var array $prioridadeTipo
     */
    private $prioridadeTipo;

    /**
     * Construtor da RequisiçãoExame (lab_requisicao)
     * @param integer $iCodigo Código da tabela lab_requisicao
     * @throws BusinessException If Código informado não for encontrado
     * @throws Exception
     */
    public function __construct($iCodigo = null)
    {
        if (!empty($iCodigo)) {
            $oDaoRequisicaoLaboratorial = new cl_lab_requisicao();
            $sSqlRequisicaoLaboratorial = $oDaoRequisicaoLaboratorial->sql_query_file($iCodigo);
            $rsRequisicaoLaboratorial = $oDaoRequisicaoLaboratorial->sql_record($sSqlRequisicaoLaboratorial);

            if (!$rsRequisicaoLaboratorial || $oDaoRequisicaoLaboratorial->numrows == 0) {
                $mensagem = _M(CAMINHO_MENSAGEM_REQUISICAO_LABORATORIAL . ".codigo_nao_encontrado") . PHP_EOL;
                $mensagem .= "Requisição laboratorial: " . $iCodigo;
                throw new BusinessException($mensagem);
            }

            $oDadosRequisicao = db_utils::fieldsMemory($rsRequisicaoLaboratorial, 0);
            $this->iCodigo = $oDadosRequisicao->la22_i_codigo;
            $this->sMedico = $oDadosRequisicao->la22_c_medico;
            $this->oData = new DBDate($oDadosRequisicao->la22_d_data);
            $this->departamento = DBDepartamentoRepository::getPorCodigo($oDadosRequisicao->la22_i_departamento);
            $this->usuario = UsuarioSistemaRepository::getPorCodigo($oDadosRequisicao->la22_i_usuario);
            $this->cgs = CgsRepository::getByCodigo($oDadosRequisicao->la22_i_cgs);
            $this->responsavel = $oDadosRequisicao->la22_c_responsavel;
            $this->hora = $oDadosRequisicao->la22_c_hora;
            $this->medicamento = $oDadosRequisicao->la22_t_medicamento;
            $this->diagnostico = $oDadosRequisicao->la22_t_diagnostico;
            $this->observacao = $oDadosRequisicao->la22_t_observacao;
            $this->autorizacao = $oDadosRequisicao->la22_i_autoriza;
            $this->contato = $oDadosRequisicao->la22_c_contato;
            $this->prioridadeTipo = $this->setPrioridadeTipo();
        }
    }

    /**
     * Responsável por buscar todas as Requisições de Exames vinculadas a Requisição Laboratorial
     * @return RequisicaoExame[]
     */
    public function getRequisicoesDeExames()
    {
        if (empty($this->aRequisicaoExame)) {
            $oDaoRequisicaoExame = new cl_lab_requiitem();
            $sWhere = "la21_i_requisicao = {$this->iCodigo}";
            $sSqlRequisicaoExame = $oDaoRequisicaoExame->sql_query_file('', 'la21_i_codigo', 'la21_i_codigo', $sWhere);
            $rsRequisicaoExame = $oDaoRequisicaoExame->sql_record($sSqlRequisicaoExame);
            $iLinhaRequisicaoExame = $oDaoRequisicaoExame->numrows;

            if ($rsRequisicaoExame && $iLinhaRequisicaoExame > 0) {
                for ($iContador = 0; $iContador < $iLinhaRequisicaoExame; $iContador++) {
                    $iRequisicaoExame = db_utils::fieldsMemory($rsRequisicaoExame, $iContador)->la21_i_codigo;
                    $oRequisicaoExame = new RequisicaoExame($iRequisicaoExame);
                    $this->aRequisicaoExame[] = $oRequisicaoExame;
                }
            }
        }

        return $this->aRequisicaoExame;
    }

    /**
     * Retorna o nome do médico. Verifica se existe registro no campo la22_c_medico referente a lab_requisicao.
     * Caso não exista o nome de um médico, busca do vínculo de lab_medico com lab_requisicao, o nome no CGM
     * @return string
     * @throws DBException
     */
    public function getMedico()
    {
        if (empty($this->sMedico)) {
            $oDaoLabMedicos = new cl_lab_medico();
            $sSqlLabMedico = $oDaoLabMedicos->sql_query(null, 'z01_nome', null, "la38_i_requisicao = {$this->iCodigo}");
            $rsLabMedico = db_query($sSqlLabMedico);

            if (!$rsLabMedico) {
                $oErro = new stdClass();
                $oErro->sErro = pg_last_error();
                throw new DBException(_M(CAMINHO_MENSAGEM_REQUISICAO_LABORATORIAL . 'erro_buscar_medico', $oErro));
            }

            if (pg_num_rows($rsLabMedico) > 0) {
                $this->sMedico = db_utils::fieldsMemory($rsLabMedico, 0)->z01_nome;
            }
        }

        return $this->sMedico;
    }

    /**
     * Retorna o código da requisição
     * @return integer
     */
    public function getCodigo()
    {
        return $this->iCodigo;
    }

    /**
     * @param $codigo
     */
    public function setCodigo($codigo)
    {
        $this->iCodigo = $codigo;
    }

    /**
     * retorna a data de criação da requisição
     * @return DBDate
     */
    public function getData()
    {
        return $this->oData;
    }

    /**
     * @return CGS
     */
    public function getCgs()
    {
        return $this->cgs;
    }

    /**
     * @param CGS $cgs
     * @return RequisicaoLaboratorial
     */
    public function setCgs($cgs)
    {
        $this->cgs = $cgs;

        return $this;
    }

    /**
     * @return DBDepartamento
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * @param DBDepartamento $departamento
     * @return RequisicaoLaboratorial
     */
    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;

        return $this;
    }

    /**
     * @return Exame[]
     */
    public function getRequisicaoExame()
    {
        return $this->aRequisicaoExame;
    }

    /**
     * @param array $aRequisicaoExame
     */
    public function setRequisicaoExame($aRequisicaoExame)
    {
        $this->aRequisicaoExame = $aRequisicaoExame;
    }

    /**
     * @return UsuarioSistema
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param UsuarioSistema $usuario
     * @return RequisicaoLaboratorial
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return DBDate
     */
    public function getDataUltimaMenstruacao()
    {
        return $this->dataUltimaMenstruacao;
    }

    /**
     * @param DBDate $dataUltimaMenstruacao
     */
    public function setDataUltimaMenstruacao($dataUltimaMenstruacao)
    {
        $this->dataUltimaMenstruacao = $dataUltimaMenstruacao;
    }

    /**
     * @return string
     */
    public function getResponsavel()
    {
        return $this->responsavel;
    }

    /**
     * @param string $responsavel
     * @return RequisicaoLaboratorial
     */
    public function setResponsavel($responsavel)
    {
        $this->responsavel = $responsavel;

        return $this;
    }

    /**
     * @return string
     */
    public function getHora()
    {
        return $this->hora;
    }

    /**
     * @param string $hora
     * @return RequisicaoLaboratorial
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * @return string
     */
    public function getMedicamento()
    {
        return $this->medicamento;
    }

    /**
     * @param string $medicamento
     * @return RequisicaoLaboratorial
     */
    public function setMedicamento($medicamento)
    {
        $this->medicamento = $medicamento;

        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnostico()
    {
        return $this->diagnostico;
    }

    /**
     * @param string $diagnostico
     * @return RequisicaoLaboratorial
     */
    public function setDiagnostico($diagnostico)
    {
        $this->diagnostico = $diagnostico;

        return $this;
    }

    /**
     * @return string
     */
    public function getObservacao()
    {
        return $this->observacao;
    }

    /**
     * @param string $observacao
     * @return RequisicaoLaboratorial
     */
    public function setObservacao($observacao)
    {
        $this->observacao = $observacao;

        return $this;
    }

    /**
     * @return string
     */
    public function getAutorizacao()
    {
        return $this->autorizacao;
    }

    /**
     * @param string $autorizacao
     * @return RequisicaoLaboratorial
     */
    public function setAutorizacao($autorizacao)
    {
        $this->autorizacao = $autorizacao;

        return $this;
    }

    /**
     * @return string
     */
    public function getContato()
    {
        return $this->contato;
    }

    /**
     * @param string $contato
     * @return RequisicaoLaboratorial
     */
    public function setContato($contato)
    {
        $this->contato = $contato;

        return $this;
    }

    /**
     * @param DBDate $oData
     */
    public function setData($oData)
    {
        $this->oData = $oData;
    }

    /**
     * @param string $sMedico
     */
    public function setMedico($sMedico)
    {
        $this->sMedico = $sMedico;
    }

    /**
     * @return Laboratorio|null
     * @throws Exception
     */
    public function getLaboratorio()
    {
        if (is_null($this->laboratorio)) {
            $dao = new cl_lab_requiitem();
            $sql = $dao->sql_query2(null, "la02_i_codigo", "1 limit 1", "la22_i_codigo = {$this->iCodigo}");
            $rs = db_query($sql);

            if (!$rs) {
                throw new Exception("Erro ao buscar laboratório");
            }

            if (pg_num_rows($rs) === 0) {
                return null;
            }

            $this->laboratorio = LaboratorioRepository::getLaboratorioByCodigo(
                db_utils::fieldsMemory(
                    $rs,
                    0
                )->la02_i_codigo
            );
        }

        return $this->laboratorio;
    }

    /**
     * @return $this
     * @throws DBException
     */
    public function withRequisicaoExame()
    {
        if (empty($this->aRequisicaoExame)) {
            $repository = new RequisicaoExameRepository(new cl_lab_requiitem());
            $exames = $repository->getRequisicaoExamePorRequisicao($this);
            $this->setRequisicaoExame($exames);
        }

        return $this;
    }


    /**
     * @param array $state
     * @return RequisicaoLaboratorial
     * @throws Exception
     */
    public static function fromState(array $state)
    {
        $requisicaoLaboratorial = new self;

        if (array_key_exists('la22_i_codigo', $state)) {
            $requisicaoLaboratorial->setCodigo((int)$state['la22_i_codigo']);
        }

        if (array_key_exists('la22_i_departamento', $state)) {
            $requisicaoLaboratorial->setDepartamento(
                \DBDepartamentoRepository::getDBDepartamentoByCodigo((int)$state['la22_i_departamento'])
            );
        }

        if (array_key_exists('la22_i_usuario', $state)) {
            $requisicaoLaboratorial->setUsuario(new UsuarioSistema((int)$state['la22_i_usuario']));
        }

        if (array_key_exists('la22_i_usuario', $state)) {
            $requisicaoLaboratorial->setUsuario(new UsuarioSistema((int)$state['la22_i_usuario']));
        }

        if (array_key_exists('la22_i_cgs', $state)) {
            $requisicaoLaboratorial->setCgs(new Cgs((int)$state['la22_i_cgs']));
        }

        if (array_key_exists('la22_c_responsavel', $state)) {
            $requisicaoLaboratorial->setResponsavel($state['la22_c_responsavel']);
        }

        if (array_key_exists('la22_d_data', $state)) {
            $requisicaoLaboratorial->setData(
                !is_null($state['la22_d_data']) ? new DBDate($state['la22_d_data']) :
                    ''
            );
        }

        if (array_key_exists('la22_c_hora', $state)) {
            $requisicaoLaboratorial->setHora($state['la22_c_hora']);
        }

        if (array_key_exists('la22_c_medico', $state)) {
            $requisicaoLaboratorial->setMedico($state['la22_c_medico']);
        }

        if (array_key_exists('la22_d_dum', $state)) {
            $requisicaoLaboratorial->setDataUltimaMenstruacao(
                !is_null($state['la22_d_dum']) ? new DBDate($state['la22_d_dum']) :
                    ''
            );
        }

        if (array_key_exists('la22_t_medicamento', $state)) {
            $requisicaoLaboratorial->setMedicamento($state['la22_t_medicamento']);
        }

        if (array_key_exists('la22_t_diagnostico', $state)) {
            $requisicaoLaboratorial->setDiagnostico($state['la22_t_diagnostico']);
        }

        if (array_key_exists('la22_t_observacao', $state)) {
            $requisicaoLaboratorial->setDiagnostico($state['la22_t_observacao']);
        }

        if (array_key_exists('la22_i_autoriza', $state)) {
            $requisicaoLaboratorial->setAutorizacao($state['la22_i_autoriza']);
        }

        if (array_key_exists('la22_c_contato', $state)) {
            $requisicaoLaboratorial->setContato($state['la22_c_contato']);
        }

        return $requisicaoLaboratorial;
    }

    /**
     * @return array
     * @throws DBException
     * @throws ParameterException
     */
    public function toArray()
    {
        foreach ($this->getRequisicaoExame() as $exame) {
            $exames[] = $exame->toArray();
        }

        return array(
            'la22_i_codigo' => $this->getCodigo(),
            'la22_i_departamento' => $this->getDepartamento(),
            'la22_i_cgs' => $this->getCgs(),
            'la22_c_responsavel' => $this->getResponsavel(),
            'la22_d_data' => !is_null($this->getData()) ? $this->getData()->convertTo('Y-m-d') : null,
            'la22_c_hora' => $this->getHora(),
            'la22_c_medico' => $this->getMedico(),
            'la22_d_dum' => !empty($this->getDataUltimaMenstruacao()) ?
                $this->getDataUltimaMenstruacao()->convertTo('Y-m-d') :
                null,
            'la22_t_medicamento' => $this->getMedicamento(),
            'la22_t_diagnostico' => $this->getDiagnostico(),
            'la22_t_observacao' => $this->getObservacao(),
            'la22_c_contato' => $this->getContato(),
            'requisicaoExames' => $exames,
        );
    }

    /**
     * Seta as prioridades que a requisição possuir.
     * É possível informar essas prioridades através de um array de objetos.
     * @param array $prioridadesDaRequisicao
     * @return array|mixed
     */
    public function setPrioridadeTipo($prioridadesDaRequisicao = null)
    {
        if (!empty($this->prioridadeTipo)) {
            return $this->prioridadeTipo;
        }

        if (empty($this->getCodigo())) {
            throw new BadMethodCallException('Número de requisição não informado!');
        }

        $prioridades = [];

        if (empty($prioridadesDaRequisicao)) {
            $sqlPrioridade = "
                select prioridadetipo.* from lab_requisicao
                inner join labrequisicao_prioridadetipo on la70_labrequisicao = la22_i_codigo
                inner join prioridadetipo on la69_sequencial = la70_prioridadetipo
                where la22_i_codigo = {$this->getCodigo()}
            ";
            $rsSqlPrioridade = db_query($sqlPrioridade);

            $prioridadesDaRequisicao = db_utils::getCollectionByRecord($rsSqlPrioridade);
        }

        if (empty($prioridadesDaRequisicao)) {
            return [];
        }

        foreach ($prioridadesDaRequisicao as $prioridade) {
            $prioridades[] = [
                'id' => $prioridade->la69_sequencial,
                'descricao' => $prioridade->la69_descricao
            ];
        }

        return $prioridades;
    }

    public function getPrioridadeTipo()
    {
        return $this->prioridadeTipo;
    }
}
