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


require_once(modification('model/tceEstruturaBasica.php'));

class tceFolhaTabelaTotalizadores extends tceEstruturaBasica {

  const  NOME_ARQUIVO   = 'TCE_4960.TXT';
  const  CODIGO_ARQUIVO = 37;

  public  $iInstit       = "";
  public  $sInstituicoes = "";
  public  $sDataIni      = "";
  public  $sDataFim      = "";
  public  $sCodRemessa   = "";

  protected $oDadosArquivo = null;

  private $oLeiaute = null;
  /**
   *
   */
  function __construct($iInstit,$sCodRemessa,$sDataIni,$sDataFim,$oData, $oLeiaute = null, $sInstituicoes) {

    try {
      parent::__construct(self::CODIGO_ARQUIVO,self::NOME_ARQUIVO);
    } catch (Exception $e) {
    	throw new Exception($e->getMessage());
    }
    $this->oDadosArquivo = $oData;
    $this->iInstit       = $iInstit;
    $this->sInstituicoes = $sInstituicoes;
    $this->sDataIni      = $sDataIni;
    $this->sDataFim      = $sDataFim;
    $this->sCodRemessa   = $sCodRemessa;
    if ($oLeiaute != null) {
      $this->oLeiaute = $oLeiaute;
    }


  }

  function getNomeArquivo(){
    return self::NOME_ARQUIVO;
  }

  function geraArquivo() {

    // db_criatermometro('terTCE4960', 'Arquivo TCE4960...', 'blue', 1);
    $this->oTxtLayout->setByLineOfDBUtils($this->cabecalhoPadrao($this->iInstit,
                                                                 $this->sDataIni,
                                                                 $this->sDataFim,
                                                                 $this->sCodRemessa), 1);
    $rsFolhaRubricas = db_query($this->sqlTabelaTotalizadores());
    $iNumRows        = pg_num_rows($rsFolhaRubricas);
    $iTotalRegistros = 0;


    for($i = 0; $i < $iNumRows; $i ++) {

      // db_atutermometro($i, $iNumRows, "terTCE4960");
      $oFolhaRubricas = db_utils::fieldsMemory($rsFolhaRubricas, $i);
      $this->oTxtLayout->setByLineOfDBUtils($oFolhaRubricas, 3);
      $iTotalRegistros ++;

    }

    $this->oTxtLayout->setByLineOfDBUtils($this->rodapePadrao($iTotalRegistros), 5);
    unset($rsFolhaRubricas);

  }

  /**
   * Funcao para montar uma string sql para busca do cadastro de rubricas
   *
   * @return string                  string sql com o cadastro de rubricas
   */
  function sqlTabelaTotalizadores() {

    $sql  = <<<SQL
        select
            rh27_instit::char||rh27_rubric  as rubrica,
            0 as codigovantagemdescontototalizador,
            cast('{$this->sDataFim}' as date) as dataatualizacao,
            rh27_descr as nomevantagemdescontototalizador,
            rh137_descricao as baselegal,
            case
                when (eso26_subgrupotce is not null and eso26_natureza = '4011' and eso26_subgrupotce != '') then '1000' || eso26_subgrupotce
                when (eso26_subgrupotce is not null and eso26_subgrupotce != '') then eso26_natureza || eso26_subgrupotce
                when (eso26_subgrupotce is not null and eso26_natureza = '1016') then '102001'
                when (eso26_subgrupotce is not null and eso26_natureza = '1017') then '102001'
                when (eso26_subgrupotce is not null and eso26_natureza = '1018') then '102101'
                when (eso26_subgrupotce is not null and eso26_natureza = '1019') then '102101'
            else '000000' end as codigo_conta,
            'X' as piso_magisterio,
            case when rh23_rubric is not null then 'S' else 'X' end as compoe_empenho
        from
            rhrubricas
            left join rhfundamentacaolegal on rh27_rhfundamentacaolegal = rh137_sequencial
            left join rhrubelemento on rh23_instit = rh27_instit and rh23_rubric = rh27_rubric
            left join esocial.esocialrubricas on eso26_rubrica = rh27_rubric and eso26_instituicao = rh27_instit
        where
            rh27_instit in ({$this->sInstituicoes});
SQL;
    return $sql;

  }



}
