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
 * Class ConciliacaoBancaria
 */
class ConciliacaoBancaria {

  /**
   * @type integer
   */
  const SITUACAO_EM_ABERTO = 1;

  /**
   * @type integer
   */
  const SITUACAO_FECHADA   = 2;



  public static function getMenorDataConciliacao()
  {
    $menordatacaixa = '2006-01-01';
    $clconcilia = new cl_concilia;
    $sqlMenorDataCaixa  = " select min(menordatacaixa) as menordatacaixa ";
    $sqlMenorDataCaixa .= "   from (  select min(k68_data) as menordatacaixa from concilia ";
    $sqlMenorDataCaixa .= "	       union ";
    $sqlMenorDataCaixa .= "	  	 	    select min(k89_data) as menordatacaixa from conciliapendcorrente ) as x ";
    $rsMenorDataCaixa   = $clconcilia->sql_record($sqlMenorDataCaixa);
    if($clconcilia->numrows > 0){
        $menordatacaixa = db_utils::fieldsMemory($rsMenorDataCaixa, 0)->menordatacaixa;
    }
    return $menordatacaixa;
  }

}
