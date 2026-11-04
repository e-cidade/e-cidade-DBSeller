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

// $campos = "fis_fiscal.y30_codnoti,fis_fiscal.y30_data,fis_fiscal.y30_hora,fis_fiscal.y30_obs,fis_fiscal.y30_setor,fis_fiscal.y30_nome,fis_fiscal.y30_dtvenc,fis_fiscal.y30_numbloco";

// Ticket 108335
if (isset($intimacao) && $intimacao == 1) {
	$campos = "distinct y30_codnoti,
              y30_data     as \"dl_Data da Intimação\", 
              y30_hora     as \"dl_Hora da Intimação\", 
              y30_obs      as \"dl_Observação da Intimação\", 
              y30_setor,
              y30_nome,
              y30_dtvenc,
              (select y110_procfiscal from fiscalizacao.fis_procfiscalnotificacao where y110_notificacaofiscal = y30_codnoti) as dl_Processo_Fiscal";
}else{
	$campos = "distinct fis_fiscal.y30_codnoti,fis_fiscal.y30_data,fis_fiscal.y30_hora,fis_fiscal.y30_obs,fis_fiscal.y30_setor,fis_fiscal.y30_nome,fis_fiscal.y30_dtvenc,(select y110_procfiscal from fiscalizacao.fis_procfiscalnotificacao where y110_notificacaofiscal = y30_codnoti) as \"dl_Processo Fiscal\"";
}
// -------------

?>
