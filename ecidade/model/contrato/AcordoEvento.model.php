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

/**
 * Class AcordoEvento
 */
class AcordoEvento {

  const TIPO_EVENTO_ANULACAO_DETERMINACAO_JUDICIAL = 1;
  const TIPO_EVENTO_ANULACAO_OFICIO                = 2;
  const TIPO_EVENTO_APOSTILA                       = 3;
  const TIPO_EVENTO_ENCERRAMENTO_CONTRATO          = 4;
  const TIPO_EVENTO_ORDEM_INICIO                   = 5;
  const TIPO_EVENTO_PUBLICACAO                     = 6;
  const TIPO_EVENTO_RETORNO_EFEITOS_CONTRATO       = 7;
  const TIPO_EVENTO_RESCISAO                       = 8;
  const TIPO_EVENTO_SUSPENSAO_CAUTELAR             = 9;
  const TIPO_EVENTO_SUSPENSAO_DECISAO_JUDICIAL     = 10;
  const TIPO_EVENTO_SUSPENSAO_OFICIO               = 11;
  const TIPO_EVENTO_TERMO_ADITIVIVO                = 12;
  const TIPO_EVENTO_TERMO_RECEBIMENTO_DEFINITIVO   = 13;
  const TIPO_EVENTO_TERMO_RECEBIMENTO_PROVISORIO   = 14;
  const TIPO_EVENTO_TERMO_INDENIZACAO              = 16;

  const PUBLICACAO_DIARIO_ESTADO          = 1;
  const PUBLICACAO_INTERNET               = 2;
  const PUBLICACAO_JORNAL                 = 3;
  const PUBLICACAO_MURAL_ENTIDADE         = 4;
  const PUBLICACAO_DIARIO_MUNICIPIO       = 5;
  const PUBLICACAO_DIARIO_MUNICIPIO_RS    = 6;
  const PUBLICACAO_DIARIO_UNIAO           = 7;
	const PUBLICACAO_NAO_PUBLICADO          = 8;
  const PUBLICACAO_SITE_OFICIAL           = 9;
  const PUBLICACAO_CONTRATACOES_PUBLICAS  = 10;

  /**
   * Condigo do evento
   * @var integer
   */
  private $iCodigo = null;


  /**
   * Acordo que gerou o evento
   * @var Acordo
   */
  private $oAcordo = null;

  /**
   * Data do evento
   * @var DBDate
   */
  private $oData;

  /**
   * Veículo de comunicação
   * @var integer
   */
  private $iVeiculoComunicacao = null;

  /**
   * Descrição do Veiculo de COmunicação
   * @var string
   */
  private $sDescricaoVeiculo = null;

  /**
   * Número do processo
   * @var string
   */
  private $iProcesso;

  /**
   * Ano do processo
   * @var integer
   */
  private $iAnoProcesso;

  /**
   * Tipo do evento
   * @var integer
   */
  private $iTipoEvento = null;

  /**
   * Documentos vinculados ao evento
   * @var DocumentoEventoAcordo[]
   */
  private $aDocumentos = array();

  /**
   * Código do acordo
   * @var integer
   */
  private $iCodigoAcordo;

  /**
   * Propriedade para controlar se os documentos já foram carregados
   * @var bool
   */
  private $lDocumentosCarregados = false;

  /**
   * AcordoEvento constructor.
   * @param integer|null $iCodigoEvento
   * @throws BusinessException
   */
  public function __construct($iCodigoEvento = null) {

    if (empty($iCodigoEvento)) {
      return;
    }
    $oDaoEvento       = new cl_acordoevento();
    if (!$oDadosEvento = db_utils::getRowFromDao($oDaoEvento, array($iCodigoEvento))) {
      throw new BusinessException("Evento com o código {$iCodigoEvento} não cadastrado");
    }

    $this->setCodigo($oDadosEvento->ac55_sequencial);
    $this->setAnoProcesso($oDadosEvento->ac55_anoprocesso);
    $this->setProcesso($oDadosEvento->ac55_numeroprocesso);
    $this->setData(new DBDate($oDadosEvento->ac55_data));
    $this->setDescricaoVeiculo($oDadosEvento->ac55_descricaopublicacao);
    $this->setTipoEvento($oDadosEvento->ac55_tipoevento);
    $this->setVeiculoComunicacao($oDadosEvento->ac55_veiculocomunicacao);
    $this->iCodigoAcordo = $oDadosEvento->ac55_acordo;

  }

  /**
   * @return int
   */
  public function getCodigo() {
    return $this->iCodigo;
  }

  /**
   * @param int $iCodigo
   * @return AcordoEvento
   */
  public function setCodigo($iCodigo) {
    $this->iCodigo = $iCodigo;
    return $this;
  }

  /**
   * @return Acordo
   */
  public function getAcordo() {

    if (empty($this->oAcordo) && !empty($this->iCodigoAcordo)) {
      $this->oAcordo = AcordoRepository::getByCodigo($this->iCodigoAcordo);
    }
    return $this->oAcordo;
  }

  /**
   * @param Acordo $oAcordo
   * @return AcordoEvento
   */
  public function setAcordo(Acordo $oAcordo) {
    $this->oAcordo = $oAcordo;
    return $this;
  }

  /**
   * @return DBDate
   */
  public function getData() {
    return $this->oData;
  }

  /**
   * @param DBDate $oData
   * @return AcordoEvento
   */
  public function setData(DBDate $oData) {
    $this->oData = $oData;
    return $this;
  }

  /**
   * @return int
   */
  public function getVeiculoComunicacao() {
    return $this->iVeiculoComunicacao;
  }

  /**
   * @param int $iVeiculoComunicacao
   * @return AcordoEvento
   */
  public function setVeiculoComunicacao($iVeiculoComunicacao) {
    $this->iVeiculoComunicacao = $iVeiculoComunicacao;
    return $this;
  }

  /**
   * @return string
   */
  public function getDescricaoVeiculo() {
    return $this->sDescricaoVeiculo;
  }

  /**
   * @param string $sDescricaoVeiculo
   * @return AcordoEvento
   */
  public function setDescricaoVeiculo($sDescricaoVeiculo) {
    $this->sDescricaoVeiculo = $sDescricaoVeiculo;
    return $this;
  }

  /**
   * @return string
   */
  public function getProcesso() {
    return $this->iProcesso;
  }

  /**
   * @param string $iProcesso
   * @return AcordoEvento
   */
  public function setProcesso($iProcesso) {

    $this->iProcesso = $iProcesso;
    return $this;
  }

  /**
   * @return int
   */
  public function getAnoProcesso() {
    return $this->iAnoProcesso;
  }

  /**
   * @param int $iAnoProcesso
   * @return AcordoEvento
   */
  public function setAnoProcesso($iAnoProcesso) {

    $this->iAnoProcesso = $iAnoProcesso;
    return $this;
  }

  /**
   * @return int
   */
  public function getTipoEvento() {
    return $this->iTipoEvento;
  }

  /**
   * @param int $iTipoEvento
   */
  public function setTipoEvento($iTipoEvento) {
    $this->iTipoEvento = $iTipoEvento;
  }

  /**
   * Persiste os dados do evento
   * @throws BusinessException
   * @throws DBException
   */
  public function salvar() {

    if (!db_utils::inTransaction()) {
      throw new DBException("Transação com o banca de dados não encontrada");
    }

    if ($this->getAcordo() == "") {
      throw  new BusinessException('Acordo não informado');
    }

    $oDaoAcordoEvento                           = new cl_acordoevento();
    $oDaoAcordoEvento->ac55_acordo              = $this->getAcordo()->getCodigo();
    $oDaoAcordoEvento->ac55_anoprocesso         = $this->getAnoProcesso();
    $oDaoAcordoEvento->ac55_numeroprocesso      = $this->getProcesso();
    $oDaoAcordoEvento->ac55_tipoevento          = $this->getTipoEvento();
    $oDaoAcordoEvento->ac55_descricaopublicacao = $this->getDescricaoVeiculo();
    $oDaoAcordoEvento->ac55_veiculocomunicacao  = $this->getVeiculoComunicacao();
    $oDaoAcordoEvento->ac55_data                = $this->getData()->getDate();

    if (empty($this->iCodigo)) {
      $oDaoAcordoEvento->incluir(null);
      $this->setCodigo($oDaoAcordoEvento->ac55_sequencial);
    } else {
      $oDaoAcordoEvento->ac55_sequencial = $this->getCodigo();
      $oDaoAcordoEvento->alterar($this->getCodigo());
    }

    if ($oDaoAcordoEvento->erro_status == 0) {
      throw  new BusinessException("Erro ao salvar os dados do evento do contrato");
    }

    $this->realizarMovimentacaoNoAcordo();
  }

  /**
   * @param AcordoPosicao $oAcordoPosicao
   * @throws BusinessException
   */
  public function adicionarAcordoPosicaoEvento(AcordoPosicao $oAcordoPosicao) {

    $oDaoAcordoPosicaoEvento = new cl_acordoposicaoevento();
    $oDaoAcordoPosicaoEvento->ac56_acordoevento  = $this->getCodigo();
    $oDaoAcordoPosicaoEvento->ac56_acordoposicao = $oAcordoPosicao->getCodigo();
    $oDaoAcordoPosicaoEvento->incluir(null);

    if ($oDaoAcordoPosicaoEvento->erro_status == 0) {
      throw new BusinessException("Houve um erro ao salvar o evento do contrato.");
    }
  }

  /**
   * Retorna todos os documentos
   * @return DocumentoEventoAcordo[]
   * @throws DBException
   */
  public function getDocumentos() {

    if (count($this->aDocumentos) > 0 && $this->lDocumentosCarregados) {
      return $this->aDocumentos;
    }

    $oDaoAcordoEventoDocumento = new cl_acordodocumentoevento();
    $sWhere                    = "ac57_acordoevento = {$this->getCodigo()}";
    $sSqlDocumentos            = $oDaoAcordoEventoDocumento->sql_query_file(null, 'ac57_sequencial', 'ac57_sequencial', $sWhere);
    $rsDocumentos              = db_query($sSqlDocumentos);
    if (!$rsDocumentos) {
      throw new DBException("Erro ao pesquisar os documentos do evento de codigo {$this->getCodigo()}");
    }
    $iTotalLinhas = pg_num_rows($rsDocumentos);

    for ($iDocumento = 0; $iDocumento < $iTotalLinhas; $iDocumento++) {
      $this->aDocumentos[] = new DocumentoEventoAcordo(db_utils::fieldsMemory($rsDocumentos, $iDocumento)->ac57_sequencial);
    }
    return $this->aDocumentos;
  }

  /**
   * Remove os dados do evento
   * @throws BusinessException
   * @throws DBException
   */
  public function remover() {



    switch ($this->getTipoEvento()) {

        case self::TIPO_EVENTO_ENCERRAMENTO_CONTRATO:

            $oDaoEncerramento = new cl_acordoencerramentolicitacon();
            $oDaoEncerramento->excluir(null, "ac58_acordo = {$this->getAcordo()->getCodigo()}");
            if ($oDaoEncerramento->erro_status == "0") {
                throw new DBException("Não foi possível excluir o encerramento existente para o acordo.");
            }
            break;

        case self::TIPO_EVENTO_SUSPENSAO_CAUTELAR:
        case self::TIPO_EVENTO_SUSPENSAO_DECISAO_JUDICIAL:
        case self::TIPO_EVENTO_SUSPENSAO_OFICIO:
        case self::TIPO_EVENTO_RETORNO_EFEITOS_CONTRATO:

            $dataEvento = $this->getData() == null ? null : $this->getData()->getDate("Y-m-d");
            $suspensao = array();
            $oSuspensao = new cl_acordoparalisacao();
            $codAcordo = $this->getAcordo()->getCodigo();
            $acordo = new Acordo($codAcordo);

            $isRetornoEfeitoContrato = $this->getTipoEvento() ==  self::TIPO_EVENTO_RETORNO_EFEITOS_CONTRATO ? true : false;

            $sql = $oSuspensao->sql_query_file(null, "ac47_sequencial, ac47_datainicio, ac47_datafim", null, "ac47_acordoevento = {$this->getCodigo()}");
            $resource = db_query($sql);
            $suspensao = pg_fetch_assoc($resource);

            if (empty($suspensao || !$resource)) {
                break;
            }
            if ($isRetornoEfeitoContrato) {
              $oSuspensao->ac47_datafim = null;
              $oSuspensao->ac47_sequencial = $suspensao['ac47_sequencial'];
              $oSuspensao->alterar($suspensao['ac47_sequencial']);
              $acordo->setSituacao(5);
              $acordo->save();
              break;
            }

            $oAcordoParalisacaoMovimentacao = new cl_acordoparalisacaoacordomovimentacao();
            $where = "ac48_acordoparalisacao = {$suspensao['ac47_sequencial']}";
            $sql =  $oAcordoParalisacaoMovimentacao->sql_query(null, "ac48_acordomovimentacao", null, $where);
            $resource = db_query($sql);
            if (pg_num_rows($resource) == 0) {
              break;
            }
            $idAcordoMovimentacao =  pg_fetch_assoc($resource);
            $acordoMovimentacao = new cl_acordomovimentacao();
            $oAcordoParalisacaoMovimentacao->excluir(null, "ac48_acordoparalisacao = {$suspensao['ac47_sequencial']}");
            $acordoMovimentacao->excluir(null, "ac10_sequencial = {$idAcordoMovimentacao['ac48_acordomovimentacao']}");
            $oSuspensao->excluir(null, "ac47_sequencial = {$suspensao['ac47_sequencial']}");
            $acordo->setSituacao(4);
            $acordo->save();
            break;

        case self::TIPO_EVENTO_ANULACAO_DETERMINACAO_JUDICIAL:
        case self::TIPO_EVENTO_ANULACAO_OFICIO:

            $acordoAnulacao = new AcordoAnulacao();
            $acordoAnulacao->setAcordo($this->getAcordo()->getCodigo());
            $acordoAnulacao->removeAnulacao();
            $acordo = new Acordo($this->getAcordo()->getCodigo());
            $acordo->setSituacao(4);
            $acordo->save();
            break;

        case self::TIPO_EVENTO_RESCISAO:

            $acordoRescisao = new AcordoRescisao();
            $acordoRescisao->setAcordo($this->getAcordo()->getCodigo());
            $acordoRescisao->removeRescisao();
            $acordo = new Acordo($this->getAcordo()->getCodigo());
            $acordo->setSituacao(4);
            $acordo->save();
            break;
    }

    if (!db_utils::inTransaction()) {
      throw new DBException("Transação com o banca de dados não encontrada");
    }

    foreach ($this->getDocumentos() as $oDocumento) {
      $oDocumento->remover();
    }

    if ($this->temPosicaoEvento()) {

      $sErro  = "Evento originado por alteração contratual. Para excluir, será necessário excluir o Aditamento ou Apostilamento antes.";
      $sErro .= " Contate o suporte.";
      throw new BusinessException($sErro);
    }
    $oDaoAcordoEvento = new cl_acordoevento();
    if ($this->getTipoEvento() == 7) {
        $where = "ac55_acordo = {$this->getAcordo()->getCodigo()} and ac55_tipoevento = {$this->getTipoEvento()}";
        $oDaoAcordoEvento->excluir(null, $where);
        return;
    }
    $oDaoAcordoEvento->excluir($this->getCodigo());
    if ($oDaoAcordoEvento->erro_status == 0) {
      throw new BusinessException("Houve um erro ao remover o evento do contrato.");
    }
  }

  private function temPosicaoEvento() {

    $sCampos = "ac56_sequencial";
    $sWhere  = "ac56_acordoevento = " . $this->getCodigo();

    $oDaoAcordoPosicaoEvento = new cl_acordoposicaoevento();
    $sSqlAcordoPosicaoEvento = $oDaoAcordoPosicaoEvento->sql_query_file(null, $sCampos, null, $sWhere);
    $rsAcordoPosicaoEvento   = db_query($sSqlAcordoPosicaoEvento);

    if (!$rsAcordoPosicaoEvento) {
      throw new DBException("Houve um erro ao verificar as informações do evento.");
    }

    return pg_num_rows($rsAcordoPosicaoEvento) > 0;
  }

  /**
   * Realiza as movimentações necessárias no acordo
   * @throws Exception
   *
   */
  private function realizarMovimentacaoNoAcordo() {

    $aTiposEventos = TipoEventoAcordo::getTipos();

    switch ($this->getTipoEvento()) {

      case self::TIPO_EVENTO_RESCISAO:

        $oRescisao = new AcordoRescisao();
        $oRescisao->setAcordo($this->getAcordo()->getCodigo());

        if (!$oRescisao->possuiMovimentacaoCorrente()) {

          $oRescisao->setDataMovimento($this->getData()->getDate());
          $oRescisao->setObservacao('Rescisão');
          $oRescisao->save();
        }

        break;

      case self::TIPO_EVENTO_SUSPENSAO_CAUTELAR:
      case self::TIPO_EVENTO_SUSPENSAO_DECISAO_JUDICIAL:
      case self::TIPO_EVENTO_SUSPENSAO_OFICIO:

        $oUltimaParalisacao = $this->getAcordo()->getUltimaParalisacao();

        if (empty($oUltimaParalisacao) || $oUltimaParalisacao->getDataTermino() != null) {

            $oSuspensao = new AcordoParalisacao();
            $oSuspensao->setAcordo($this->getAcordo());
            $Acordo = $this->getAcordo();
            $vigenciaFinal = new DBDate($Acordo->getDataFinal());

            if ($vigenciaFinal->getTimeStamp() < $this->getData()->getTimeStamp()) {
              throw new ParameterException("Não é permitido paralisar um acordo após o fim da vigência!");
            }

            $oSuspensao->setDataInicio($this->getData());
            $oSuspensao->setEvento($this->getCodigo());
            $oSuspensao->setObservacao($aTiposEventos[$this->getTipoEvento()]);
            $oSuspensao->salvar();
        }

        break;

      case self::TIPO_EVENTO_ANULACAO_DETERMINACAO_JUDICIAL:
      case self::TIPO_EVENTO_ANULACAO_OFICIO:

        $oAcordoMovimentacao = new AcordoAnulacao();
        $oAcordoMovimentacao->setAcordo($this->getAcordo()->getCodigo());

        if (!$oAcordoMovimentacao->possuiMovimentacaoCorrente()) {

          $oAcordoMovimentacao->setDataMovimento($this->getData()->getDate());
          $oAcordoMovimentacao->setObservacao($aTiposEventos[$this->getTipoEvento()]);
          $oAcordoMovimentacao->save();
        }

        break;

      case self::TIPO_EVENTO_RETORNO_EFEITOS_CONTRATO:

          $oParalisacao = $this->getAcordo()->getUltimaParalisacao();
          if (empty($oParalisacao) || $oParalisacao->getDataTermino() != null) {
              throw new DBException("Acordo já reativado");
          }

          if ($oParalisacao->getDataInicio()->getTimeStamp() > $this->getData()->getTimeStamp()) {
            throw new ParameterException("Data final não pode ser menor que a data inicial");
          }
          $oParalisacao->setDataTermino($this->getData());

          $oParalisacao->salvar();

          $oAcordoMovimentacaoReativacao = new AcordoMovimentacaoReativacao();
          $oAcordoMovimentacaoReativacao->setAcordo($this->getAcordo()->getCodigoAcordo());
          $oAcordoMovimentacaoReativacao->setObservacao($this->getDescricaoVeiculo());
          $oAcordoMovimentacaoReativacao->save();

        break;
    }
  }
}

