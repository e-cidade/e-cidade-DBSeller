<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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


require_once(Modification("SigfisArquivoBase.model.php"));
require_once(Modification("model/contabilidade/arquivos/sigfis/SigfisArquivoBase.model.php"));
require_once(Modification("model/contabilidade/arquivos/sigfis/SigfisVinculoRecurso.model.php"));

/**
 * Classe que processa as informações para serem inseridas no
 * arquivo Diaria.txt
 * @package contabilidade
 * @subpackage sigfis
 */

class SigfisArquivoDiaria extends SigfisArquivoBase implements iPadArquivoTXTBase {

  protected $iCodigoLayout = 306;
  protected $sNomeArquivo  = 'Diaria';

  /**
   * @return array
   * @throws Exception
   */
  public function gerarDados() {

    $oDaoPagOrdem       = new cl_pagordem;
    $iAnoSessao         = db_getsession('DB_anousu');
    $iInstituicaoSessao = db_getsession('DB_instit');
    $iCodigoLinha       = self::getLinhaLayout();
    $aCampos            = array();

    $aCampos['nu_Empenho']             = "e60_codemp";
    $aCampos['dt_PagamentoEmpenho']    = "c70_data";
    $aCampos['cd_UnidadeOrcamentaria'] = "o58_unidade";
    $aCampos['dt_Ano']                 = "e60_anousu";
    $aCampos['cd_Orgao']               = "o58_orgao";
    $aCampos['Nu_Diaria']              = "e81_codmov";
    $aCampos['e69_codnota']            = "e69_codnota";

    $sJoinDiarias  = " inner join conlancamord ON conlancamord.c80_codord = pagordem.e50_codord ";
    $sJoinDiarias .= " inner join conlancam on c80_codlan = c70_codlan ";
    $sJoinDiarias .= " inner join conlancamdoc on c71_codlan = c70_codlan ";
    $sJoinDiarias .= " inner join conhistdoc on c71_coddoc = c53_coddoc ";

    $sWhereDiarias   = " c70_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}' ";
    $sWhereDiarias  .= " and exists (select 1 from emppresta inner join empprestatip on e44_tipo = e45_tipo  where e45_numemp = e60_numemp and e44_diaria is true )";
    $sWhereDiarias  .= " and e69_codnota is not null";
    $sWhereDiarias  .= " and e60_instit = {$iInstituicaoSessao}";
    $sWhereDiarias  .= " and c53_tipo = 30";

    $sCampos            = implode(",", $aCampos);
    $sSqlBuscaEmpenhos  = $oDaoPagOrdem->sql_query_empagemovforma(null, "distinct {$sCampos}", null, $sWhereDiarias, $sJoinDiarias);
    $rsSqlBuscaEmpenhos = $oDaoPagOrdem->sql_record($sSqlBuscaEmpenhos);
    $oEmpenhosDiaria    = db_utils::getCollectionByRecord($rsSqlBuscaEmpenhos);

    $this->addLog($sSqlBuscaEmpenhos);

    foreach ($oEmpenhosDiaria as $oLinhaEmpenho) {
      /**
       * Busca informações da liquidaçao
       */
      $oNotasItemLiquidacao = self::getValoresItemNota($oLinhaEmpenho->e69_codnota);
      $fValorDiaria         = str_replace('.', '', db_formatar($oNotasItemLiquidacao->valor, 'p'));
      $fQuantidadeDiaria    = $oNotasItemLiquidacao->quantidade;

      /**
       * Busca informações das diárias
       */
      $oDadosDiarias = self::getDadosDiariasPorMovimento($oLinhaEmpenho->e81_codmov);
      if (!$oDadosDiarias) {
        $this->addLog("\nMovimento {$oLinhaEmpenho->e81_codmov} não efetuado.");
        continue;
      }

      $sMotivoDiaria = str_replace(array("\n", "\r", "<br>"), " ", trim($oDadosDiarias->motivo));
      $oDadosLinha = new stdClass();

      $oDadosLinha->cd_Unidade              = str_pad($this->sCodigoTribunal,4, ' ', STR_PAD_LEFT);
      $oDadosLinha->cd_UnidadeOrcamentaria  = str_pad($oLinhaEmpenho->o58_unidade,4   ,' ', STR_PAD_LEFT);
      $oDadosLinha->nu_Empenho              = str_pad($oLinhaEmpenho->e60_codemp,10  ,' ', STR_PAD_RIGHT);
      $oDadosLinha->dt_PagamentoEmpenho     = str_replace('/', '', db_formatar($oLinhaEmpenho->c70_data,"d"));
      $oDadosLinha->nu_MatriculaFuncionario = str_pad($oDadosDiarias->matricula,10  ,' ', STR_PAD_RIGHT);
      $oDadosLinha->dt_Ano                  = str_pad($oLinhaEmpenho->e60_anousu,4   ,' ', STR_PAD_LEFT);
      $oDadosLinha->nm_Funcionario          = str_pad(substr($oDadosDiarias->nome,0,50),50  ,' ', STR_PAD_RIGHT);
      $oDadosLinha->Reservado_tce           = str_repeat(' ', 100);
      $oDadosLinha->de_MotivoViagem         = str_pad($sMotivoDiaria,200 ,' ', STR_PAD_RIGHT);
      $oDadosLinha->dt_Saida                = str_replace('/', '', db_formatar($oDadosDiarias->dtentrada,"d"));
      $oDadosLinha->Reservado_tce_1         = str_repeat(' ', 5);
      $oDadosLinha->dt_Retorno              = str_replace('/', '', db_formatar($oDadosDiarias->dtretorno,"d"));
      $oDadosLinha->Reservado_tce_2         = str_repeat(' ', 5);
      $oDadosLinha->qt_Diarias              = str_pad($fQuantidadeDiaria,3   ,' ', STR_PAD_LEFT);
      $oDadosLinha->vl_TotalDiarias         = str_pad($fValorDiaria,16  ,' ', STR_PAD_LEFT);
      $oDadosLinha->dt_AnoMes               = str_pad(substr(str_replace('-', '', $oLinhaEmpenho->c70_data),0,6),6   ,' ', STR_PAD_LEFT);
      $oDadosLinha->cd_Orgao                = str_pad($oLinhaEmpenho->o58_orgao,4   ,' ', STR_PAD_LEFT);
      $oDadosLinha->nu_EmpenhoSup           = str_pad($oLinhaEmpenho->e60_codemp,10  ,' ', STR_PAD_RIGHT);
      $oDadosLinha->Nu_Diaria               = str_pad($oLinhaEmpenho->e81_codmov,9   ,' ', STR_PAD_LEFT);

      $oDadosLinha->codigolinha             = $iCodigoLinha;

      $this->aDados[] = $oDadosLinha;

    }

    return $this->aDados;

  }

  /**
   * Busca o código da linha do layout
   * @return integer
   */
  public function getLinhaLayout(){

    $oDaoLayoutCampos = new cl_db_layoutlinha;
    $sSqlLayoutLinha  = $oDaoLayoutCampos->sql_query(null,"db51_codigo",null,"db50_codigo = {$this->getCodigoLayout()}");
    $rsLayoutLinha    = $oDaoLayoutCampos->sql_record($sSqlLayoutLinha);

    if( $oDaoLayoutCampos->numrows == 0 ){
      throw new Exception("Linha não cadastrada para o layout {$this->getCodigoLayout()}");
    }

    return db_utils::fieldsMemory($rsLayoutLinha,0)->db51_codigo;

  }

  /**
   * Busca as informações das diárias pelo código do movimento
   * @param $codigomovimento integer
   * @return stdClass
   */
  static protected function getDadosDiariasPorMovimento( $codigomovimento ){

    $oDaoEmpprestaItemDiaria = new cl_empprestaitemdiaria;
    $SqlEmprestaItemDiaria   = $oDaoEmpprestaItemDiaria->sql_query_file(null,"*",null,"e446_movimento = {$codigomovimento}");
    $rsEmprestaItemDiaria    = $oDaoEmpprestaItemDiaria->sql_record($SqlEmprestaItemDiaria);

    if( $oDaoEmpprestaItemDiaria->numrows == 0 ){
        return false;
    }

    $oDadosDiarias = db_utils::fieldsMemory($rsEmprestaItemDiaria,0);

    $oRetornoDiarias = new stdClass();
    $oRetornoDiarias->matricula = $oDadosDiarias->e446_regist;

    $oRetornoDiarias->nome = self::getServidor($oDadosDiarias->e446_regist)->getCgm()->getNome();

    $oRetornoDiarias->motivo    = $oDadosDiarias->e446_motivo;
    $oRetornoDiarias->dtentrada = $oDadosDiarias->e446_datainicio;
    $oRetornoDiarias->dtretorno = $oDadosDiarias->e446_datafim;

    return $oRetornoDiarias;
  }

  /**
   * Busca valores da nota de liquidacao
   * @param $codigonota integer
   * @return stdClass
   */
  static protected function getValoresItemNota( $codigonota ){

    $oDaoEmpnotaItem = new cl_empnotaitem;
    $sSqlDadosNota   = $oDaoEmpnotaItem->sql_query_ordemCompra(null,"*",null,"e72_codnota = {$codigonota}");
    $rsDadosNota     = $oDaoEmpnotaItem->sql_record($sSqlDadosNota);

    if( $oDaoEmpnotaItem->numrows == 0 ){
      throw new Exception("Item não encontrado para nota : {$codigonota}");
    }

    $oDadoNotaItem = db_utils::fieldsMemory($rsDadosNota, 0);

    $oRetornoNota = new stdClass();
    $oRetornoNota->valor      = $oDadoNotaItem->e72_valor;
    $oRetornoNota->quantidade = $oDadoNotaItem->e72_qtd;

    return $oRetornoNota;

  }

  /**
   * Dados do servidor
   *
   * @property int $regist
   */
  static protected function getServidor($regist)
  {
    $oDaoRhPessoal = new \cl_rhpessoal;
    $sSql = $oDaoRhPessoal->sql_query_file(
        $regist,
        "rh01_instit as instituicao,(
            select
                r11_anousu || '-' || r11_mesusu
            from
                cfpess
            where
                rh01_instit = r11_instit
            order by
                (r11_anousu ||''||r11_mesusu) desc limit 1
        ) as competencia
    ");

    $rsSql = db_query($sSql);

    if (!$rsSql) {
        throw new Exception("Erro ao buscar instituição de origem do servidor");
    }

    if (pg_num_rows($rsSql) == 0) {
        throw new Exception("Não encontrado a instituição de origem do servidor");
    }

    $dadosInstituicaoServidor = pg_fetch_object($rsSql, 0);
    $competencia = explode("-", $dadosInstituicaoServidor->competencia);
    $instituicao = $dadosInstituicaoServidor->instituicao;
    $ano         = $competencia[0];
    $mes         = $competencia[1];

    $oServidor = ServidorRepository::getInstanciaByCodigo($regist, $ano, $mes, $instituicao, false);

    if (!$oServidor) {
        throw new Exception("Matricula: {$regist} não encontrada");
    }

    return $oServidor;
  }
}

?>
