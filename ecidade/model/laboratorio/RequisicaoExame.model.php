<?php
/**
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

define("MENSAGEM_REQUISICAO_EXAME", "saude.laboratorio.RequisicaoExame.");

use ECidade\Saude\Laboratorio\Exame\ColetaAmostra\Repository\ColetaItem as ColetaItemRepository;
use ECidade\Saude\Laboratorio\Exame\Repository\ConferenciaResultado as ConferenciaResultadoRepository;

/**
 * Exame de uma Requisicao
 * Class RequisicaoExame
 * Representa a tabela lab_requiitem
 */
class RequisicaoExame
{

    private $iCodigo = null;

    /**
     * Paciente da Solicitacao;
     * @var Cgs
     */
    private $oSolicitante = null;

    /**
     * Cpdigo do Exame que deverá ser executado
     * @var integer
     */
    protected $iCodigoExame;

    /**
     * Exame que deverá ser realizado
     * @var Exame
     */
    protected $oExame;

    /**
     * Situacao do Exame
     * @var string
     */
    protected $sSituacao = '';

    /**
     * Exame nao digitado
     * @var string
     */
    const NAO_DIGITADO = '10 - Nao Digitado';

    /**
     * Valores do exame foi lançando
     * @var string
     */
    const LANCADO = '50 - Lancado';

    /**
     * Exame FOi entrege ao solicitante
     * @var string
     */
    const ENTREGUE = '70 - Entregue';

    /**
     * Amostrados do exame foram coletadas
     */
    const COLETADO = '30 - Coletado';

    /**
     * Exame foi conferido e está pronto para a entrega
     */
    const CONFERIDO = '60 - Conferido';

    /**
     * Exame autorizado
     */
    const AUTORIZADO = '20 - Autorizado';

    /**
     * Quando necessario nova coleta
     */
    const NOVA_COLETA = "40 - Nova Coleta";

    /**
     * Quando a coleta é rejeitada
     */
    const REJEITADO = "80 - Amostra Rejeitada - Nova Coleta";

    /**
     * Quando a coleta é rejeitada
     */
    const EM_PROCESSAMENTO = "45 - Em processamento"; 

    const FALTA_MATERIAL = 'f - falta material';

    private static $aSituacoes = array(
        self::NAO_DIGITADO => "Não Digitado",
        self::AUTORIZADO => "Autorizado",
        self::COLETADO => "Coletado",
        self::NOVA_COLETA => "Nova coleta",
        self::LANCADO => "Lançado",
        self::CONFERIDO => "Conferido",
        self::ENTREGUE => "Entregue",
        self::FALTA_MATERIAL => "Falta de Material",
        self::REJEITADO => "Amostra Rejeitada - Nova Coleta",
        self::EM_PROCESSAMENTO => "Em processamento"
    );

    /**
     * Observação lançada para o exâme
     * @var string
     */
    private $sObservacao;


    /**
     * Observação lançada para o exâme
     * @var string
     */
    private $sMotivoNovaColeta;

    /**
     * Medicamentos utilizados para realizar o resultado
     * @var MedicamentoLaboratorio[]
     */
    private $aMedicamentos = array();

    private $oCID = null;

    /**
     * Data do agendamento
     * @var DBDate
     */
    private $oData;

    /**
     * @var DBDate
     */
    private $dataEntrega;

    /**
     * @var string
     */
    private $horaColeta;

    /**
     * Código da requisição
     * @var integer
     */
    private $iCodigoRequisicao;

    /**
     * @var Laboratorio
     */
    private $laboratorio;

    /**
     * @var integer
     */
    private $quantidade;

    /**
     * @var ResultadoExame
     */
    private $resultadoExame;

    /**
     * @var array
     */
    private static $situacoesValidas = array(
        self::NAO_DIGITADO,
        self::AUTORIZADO,
        self::COLETADO,
        self::NOVA_COLETA,
        self::LANCADO,
        self::CONFERIDO,
        self::ENTREGUE,
        self::FALTA_MATERIAL
    );

    /**
     * @var float
     */
    private $peso;

    /**
     * @var integer
     */
    private $altura;

    /**
     * @var float
     */
    private $volumeAmostra;

    /**
     * Instancia o Exame
     * @param $iCodigo
     */
    public function __construct($iCodigo = null)
    {
        if (!empty($iCodigo)) {
            $oDaoRequisicao = new cl_lab_requiitem();
            $sSqlExameRequisicao = $oDaoRequisicao->sql_query($iCodigo);
            $rsExameRequisicao = $oDaoRequisicao->sql_record($sSqlExameRequisicao);
            if ($rsExameRequisicao && $oDaoRequisicao->numrows > 0) {
                $oDadosRequisicao = db_utils::fieldsMemory($rsExameRequisicao, 0);
                $this->oSolicitante = CgsRepository::getByCodigo($oDadosRequisicao->la22_i_cgs);
                $this->iCodigo = $oDadosRequisicao->la21_i_codigo;
                $this->iCodigoExame = $oDadosRequisicao->la08_i_codigo;
                $this->sSituacao = trim($oDadosRequisicao->la21_c_situacao);
                $this->sObservacao = trim($oDadosRequisicao->la21_observacao);
                $this->sMotivoNovaColeta = trim($oDadosRequisicao->la21_motivonovacoleta);
                $this->oData = DBDate::create($oDadosRequisicao->la21_d_data);
                $this->dataEntrega = DBDate::create($oDadosRequisicao->la21_d_entrega);
                $this->horaColeta = $oDadosRequisicao->la21_c_hora;
                $this->quantidade = $oDadosRequisicao->la21_i_quantidade;
                $this->iCodigoRequisicao = $oDadosRequisicao->la21_i_requisicao;
                $this->laboratorio = $oDadosRequisicao->la24_i_laboratorio;
                $this->peso = $oDadosRequisicao->la22_peso;
                $this->altura = $oDadosRequisicao->la22_altura;
                $this->volumeAmostra = $oDadosRequisicao->la22_volumeamostra;
            }
        }
    }

    /**
     * Retorna o codigo do item da requisicao
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
     * @param int $iCodigoRequisicao
     */
    public function setCodigoRequisicao($iCodigoRequisicao)
    {
        $this->iCodigoRequisicao = $iCodigoRequisicao;
    }

    /**
     * Define o solicitante da requisicao
     * @param Cgs $oSolicitante Solictante da requisicao
     */
    public function setSolicitante(Cgs $oSolicitante)
    {
        $this->oSolicitante = $oSolicitante;
    }

    /**
     * Retorna o solicitante da Requisicao
     * @return Cgs
     */
    public function getSolicitante()
    {
        return $this->oSolicitante;
    }

    /**
     * Exame que deverá ser Realizado
     * @return Exame
     */
    public function getExame()
    {
        if (empty($this->oExame) && !empty($this->iCodigoExame)) {
            $this->oExame = ExameRepository::getByCodigo($this->iCodigoExame);
        }
        return $this->oExame;
    }

    public function getExameRequisicao()
    {
        return $this->oExame;
    }

    /**
     * @param Exame $exame
     */
    public function setExame(Exame $exame)
    {
        $this->oExame = $exame;
    }

    /**
     * Retorna o Resultado do exame
     * @return ResultadoExame
     */
    public function getResultado()
    {
        if ($this->resultadoExame === null) {
            $this->resultadoExame = new ResultadoExame($this);
        }
        return $this->resultadoExame;
    }

    /**
     * Define a situacao do exame
     *
     * @param string $sSituacao Sitaucao do Exame
     */
    public function setSituacao($sSituacao)
    {
        $this->sSituacao = $sSituacao;
    }

    /**
     * Retorna a situacao do exame
     * @return string
     */
    public function getSituacao()
    {
        return $this->sSituacao;
    }

    /**
     * Persiste os dados do exame
     * @throws BusinessException
     */
    public function salvar()
    {
        $oDaoItemExame = new cl_lab_requiitem();
        if (!empty($this->iCodigo)) {
            $oDaoItemExame->la21_i_codigo = $this->iCodigo;
            $oDaoItemExame->la21_c_situacao = $this->getSituacao();
            $oDaoItemExame->la21_observacao = pg_escape_string($this->sObservacao);
            $oDaoItemExame->la21_motivonovacoleta = pg_escape_string($this->sMotivoNovaColeta);
            $oDaoItemExame->alterar($this->iCodigo);
            if ($oDaoItemExame->erro_status == 0) {
                throw new BusinessException(_M(MENSAGEM_REQUISICAO_EXAME . "erro_salvar"));
            }
        }
    }

    /**
     * Retorna o setor no qual o exame está vinculado
     * @return Setor
     */
    public function getLaboratorioSetor()
    {
        $oDaoRequiItem = new cl_lab_requiitem();
        $sWhere = "la21_i_codigo = {$this->iCodigo}";
        $sSqlRequiItem = $oDaoRequiItem->sql_query('', 'la24_i_setor, la24_i_laboratorio', '', $sWhere);
        $rsRequiItem = db_query($sSqlRequiItem);

        if (!$rsRequiItem) {
            $oMensagem = new stdClass();
            $oMensagem->sErro = pg_result_error($rsRequiItem);
            throw new BusinessException(_M(MENSAGEM_REQUISICAO_EXAME . "erro_buscar_setor", $oMensagem));
        }

        if (pg_num_rows($rsRequiItem) == 0) {
            throw new BusinessException(_M(MENSAGEM_REQUISICAO_EXAME . "setor_nao_encontrado"));
        }

        $iCodigoExame = db_utils::fieldsMemory($rsRequiItem, 0)->la24_i_setor;
        $codigoLaboratorio = db_utils::fieldsMemory($rsRequiItem, 0)->la24_i_laboratorio;
        $this->laboratorio = LaboratorioRepository::getLaboratorioByCodigo($codigoLaboratorio);
        return new Setor($iCodigoExame);
    }

    /**
     * Retorna a observação lançada para o exâme
     * @return string
     */
    public function getObservacao()
    {
        return $this->sObservacao;
    }

    /**
     * Define uma observação ao exame
     * @param string $sObservacao
     */
    public function setObservacao($sObservacao)
    {
        $this->sObservacao = $sObservacao;
    }

    /**
     * Retorna a observação lançada para o exâme
     * @return string
     */
    public function getMotivoNovaColeta()
    {
        return $this->sMotivoNovaColeta;
    }

    /**
     * Define uma observação ao exame
     * @param string $sMotivoNovaColeta
     */
    public function setMotivoNovaColeta($sMotivoNovaColeta)
    {
        $this->sMotivoNovaColeta = $sMotivoNovaColeta;
    }

    /**
     * Adiciona o medicamento utilizado no resultado
     * @param MedicamentoLaboratorio $oMedicamento
     */
    public function adicionarMedicamento(MedicamentoLaboratorio $oMedicamento)
    {
        $this->aMedicamentos[] = $oMedicamento;
    }

    /**
     * Salva os medicamentos utilizados para realizar resultado
     * @return boolean
     * @throws DBException
     */
    public function salvarMedicamento()
    {
        $oDaoMedicamentoExame = new cl_medicamentoslaboratoriorequiitem();
        $oDaoMedicamentoExame->excluir(null, " la44_requiitem = {$this->iCodigo} ");

        foreach ($this->aMedicamentos as $oMedicamento) {
            $oDaoMedicamentoExame->la44_sequencial = null;
            $oDaoMedicamentoExame->la44_medicamentoslaboratorio = $oMedicamento->getCodigo();
            $oDaoMedicamentoExame->la44_requiitem = $this->iCodigo;
            $oDaoMedicamentoExame->incluir(null);

            if ($oDaoMedicamentoExame->erro_status == 0) {
                $oMsgErro = new stdClass();
                $oMsgErro->sErro = pg_last_error();
                throw new DBException(_M(MENSAGEM_REQUISICAO_EXAME . "erro_salvar_medicamento", $oMsgErro));
            }
        }
        return true;
    }

    /**
     * Remove o medicamento do exame da requisição
     * @param MedicamentoLaboratorio $oMedicamento
     * @return boolean
     */
    public function removerMedicamento(MedicamentoLaboratorio $oMedicamento)
    {
        $sWhere = "     la44_requiitem = {$this->iCodigo} ";
        $sWhere .= " and la44_medicamentoslaboratorio = {$oMedicamento->getCodigo()} ";

        $oDaoMedicamentoExame = new cl_medicamentoslaboratoriorequiitem();
        $oDaoMedicamentoExame->excluir(null, $sWhere);

        $oMsgErro = new stdClass();
        $oMsgErro->sErro = pg_last_error();
        if ($oDaoMedicamentoExame->erro_status == 0) {
            throw new DBException(_M(MENSAGEM_REQUISICAO_EXAME . "erro_excluir_medicamento_exame", $oMsgErro));
        }

        $iChave = array_search($oMedicamento, $this->aMedicamentos);
        unset($this->aMedicamentos[$iChave]);
        return true;
    }

    /**
     * Retorna os medicamentos vinculados ao exame
     * @return MedicamentoLaboratorio[]
     * @throws DBException
     */
    public function getMedicamentos()
    {
        if (count($this->aMedicamentos) == 0) {
            $sWhere = " la44_requiitem = {$this->iCodigo} ";
            $oDaoMedicamentoExame = new cl_medicamentoslaboratoriorequiitem();
            $sSqlMedicamentos = $oDaoMedicamentoExame->sql_query_file(
                null,
                "la44_medicamentoslaboratorio",
                null,
                $sWhere
            );
            $rsMedicamentos = db_query($sSqlMedicamentos);

            if (!$rsMedicamentos) {
                $oMsgErro = new stdClass();
                $oMsgErro->sErro = pg_last_error();
                throw new DBException(_M(MENSAGEM_REQUISICAO_EXAME . "erro_buscar_medicamentos", $oMsgErro));
            }

            $iLinhas = pg_num_rows($rsMedicamentos);
            for ($iContador = 0; $iContador < $iLinhas; $iContador++) {
                $iMedicamento = db_utils::fieldsMemory($rsMedicamentos, $iContador)->la44_medicamentoslaboratorio;
                $this->adicionarMedicamento(new MedicamentoLaboratorio($iMedicamento));
            }
        }

        return $this->aMedicamentos;
    }

    /**
     * Retorna o CID vínculado a conferência do Exame
     * @return CID
     * @throws DBException
     */
    public function getCID()
    {
        if (empty($this->iCodigo)) {
            return null;
        }

        if (!empty($this->oCID)) {
            return $this->oCID;
        }

        $oDaoConferencia = new cl_lab_conferencia();
        $sWhereConferencia = " la47_i_requiitem = {$this->iCodigo} and la47_i_cid is not null";
        $sOrderConferencia = " la47_i_codigo desc";
        $sSqlConferencia = $oDaoConferencia->sql_query_file(null, 'la47_i_cid', $sOrderConferencia, $sWhereConferencia);
        $rsConferencia = db_query($sSqlConferencia);

        if (!$rsConferencia) {
            $oMsgErro = new stdClass();
            $oMsgErro->sErro = pg_last_error();
            throw new DBException(_M(MENSAGEM_REQUISICAO_EXAME . "erro_bucar_cid", $oMsgErro));
        }

        if (pg_num_rows($rsConferencia) > 0) {
            $iCID = db_utils::fieldsMemory($rsConferencia, 0)->la47_i_cid;
            $this->oCID = new CID($iCID);
        }

        return $this->oCID;
    }

    /**
     * Retorna a descrição sem erro de português e sem o código
     * @return string
     */
    public function getDescricaoSituacao()
    {
        return self::$aSituacoes[$this->getSituacao()];
    }

    /**
     * Define data de agendamento
     * @param DBDate $oData
     */
    public function setData($oData)
    {
        $this->oData = $oData;
    }

    /**
     * Retorna data de agendamento
     * @return DBDate
     */
    public function getData()
    {
        return $this->oData;
    }

    public function getCodigoRequisicao()
    {
        return $this->iCodigoRequisicao;
    }

    /**
     * @return DBDate
     */
    public function getDataEntrega()
    {
        return $this->dataEntrega;
    }

    /**
     * @param DBDate $dataEntrega
     * @return RequisicaoExame
     */
    public function setDataEntrega($dataEntrega)
    {
        $this->dataEntrega = $dataEntrega;
        return $this;
    }

    /**
     * @return string
     */
    public function getHoraColeta()
    {
        return $this->horaColeta;
    }

    /**
     * @param string $horaColeta
     * @return RequisicaoExame
     */
    public function setHoraColeta($horaColeta)
    {
        $this->horaColeta = $horaColeta;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * @param int $quantidade
     * @return RequisicaoExame
     */
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;
        return $this;
    }

    /**
     * @param $codigoMaterialColeta
     * @param $setor
     * @return string
     */
    public function getNumeroCodigoBarras($codigoMaterialColeta, $setor)
    {
        $codigoBarrasMaterialColeta = str_pad($codigoMaterialColeta, 3, '0', STR_PAD_LEFT);
        $codigoBarrasRequisicao = str_pad($this->iCodigoRequisicao, 9, '0', STR_PAD_LEFT);
        $codigoBarrasSetor = str_pad($setor, 2, '0', STR_PAD_LEFT);

        return $codigoBarrasMaterialColeta . $codigoBarrasRequisicao . $codigoBarrasSetor;
    }

    /**
     * @param $codigoBarras
     * @return array
     */
    public static function getDadosPorCodigoBarras($codigoBarras)
    {
        $codigoMaterial = (int)substr($codigoBarras, 0, 3);
        $codigoRequisicao = (int)substr($codigoBarras, 3, 9);
        $codigoSetor = (int)substr($codigoBarras, 12, 2);

        return array(
            'codigoMaterial' => $codigoMaterial,
            'codigoRequisicao' => $codigoRequisicao,
            'codigoSetor' => $codigoSetor
        );
    }


    /**
     * @param $situacao
     * @return bool
     */
    public static function getSituacoes($situacao)
    {
        return in_array($situacao, self::$situacoesValidas);
    }

    /**
     * @return \ECidade\Saude\Laboratorio\Exame\ColetaAmostra\Model\ColetaItem|null
     * @throws Exception
     */
    public function getColetaItem()
    {
        $coletaItemRepository = ColetaItemRepository::getInstance();
        return $coletaItemRepository->getByRequisicaoExame($this);
    }

    /**
     * @return \ECidade\Saude\Laboratorio\Exame\Model\ConferenciaResultado|null
     * @throws DBException
     */
    public function getConferenciaResultado()
    {
        $conferenciaResultadoRepository = ConferenciaResultadoRepository::getInstance();
        return $conferenciaResultadoRepository->getByRequisicaoExame($this);
    }

    /**
     * @return integer
     */
    public function getLaboratorio()
    {
        return $this->laboratorio;
    }

    /**
     * @param integer $laboratorio
     */
    public function setLaboratorio($laboratorio)
    {
        $this->laboratorio = $laboratorio;
    }

    public function getAllSituacoes()
    {
        $situacoes = [];
        foreach (self::$aSituacoes as $key => $value) {
            $situacao = new stdClass();
            $codigo = $key;
            if ($codigo != 'f - falta material') {
                $situacao->codigo = $codigo;
                $situacao->descricao = $value;
                $situacoes[] = $situacao;
            }
        }
        return $situacoes;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function toArray()
    {
        return array(
            'la21_i_codigo' => $this->getCodigoRequisicao(),
            'lab_exame' => !is_null($this->getExameRequisicao()) ? $this->getExameRequisicao()->toArray() : null,
            'la21_c_situacao' => $this->getSituacao(),
            'la21_observacao' => $this->getObservacao(),
            'la21_motivonovacoleta' => $this->getMotivoNovaColeta(),
            'la21_d_data' => $this->getData(),
            'la21_d_entrega' => $this->getDataEntrega(),
            'la21_i_quantidade' => $this->getQuantidade()
        );
    }

    /**
     * @param array $state
     * @return RequisicaoExame
     * @throws DBException
     */
    public static function fromState(array $state)
    {
        $requisicaoExame = new self;

        if (array_key_exists('la21_i_codigo', $state)) {
            $requisicaoExame->setCodigo((int)$state['la21_i_codigo']);
        }

        if (array_key_exists('la21_i_requisicao', $state)) {
            $requisicaoExame->setCodigoRequisicao((int)$state['la21_i_requisicao']);
        }

        if (array_key_exists('la21_c_situacao', $state)) {
            $requisicaoExame->setSituacao($state['la21_c_situacao']);
        }

        if (array_key_exists('la21_observacao', $state)) {
            $requisicaoExame->setObservacao($state['la21_observacao']);
        }

        if (array_key_exists('la21_motivonovacoleta', $state)) {
            $requisicaoExame->setMotivoNovaColeta($state['la21_motivonovacoleta']);
        }

        if (array_key_exists('la21_d_data', $state)) {
            $requisicaoExame->setData(DBDate::create($state['la21_d_data']));
        }

        if (array_key_exists('la21_d_entrega', $state)) {
            $requisicaoExame->setDataEntrega(DBDate::create($state['la21_d_entrega']));
        }

        if (array_key_exists('la21_i_quantidade', $state)) {
            $requisicaoExame->setQuantidade((int)$state['la21_i_quantidade']);
        }

        if (array_key_exists('la21_c_hora', $state)) {
            $requisicaoExame->setHoraColeta($state['la21_c_hora']);
        }

        if (array_key_exists('la08_i_codigo', $state)) {
            $exameRepository = new ExameRepository(new \cl_lab_exame());
            $requisicaoExame->setExame($exameRepository->find($state['la08_i_codigo']));
        }

        if (array_key_exists('la22_i_cgs', $state) && !empty($state['la22_i_cgs'])) {
            $exameRepository = new Cgs($state['la22_i_cgs']);
            $requisicaoExame->setSolicitante($exameRepository);
        }

        return $requisicaoExame;
    }

    /**
     * @return float
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * @return int
     */
    public function getAltura()
    {
        return $this->altura;
    }

    /**
     * @return float
     */
    public function getVolumeAmostra()
    {
        return $this->volumeAmostra;
    }
}
