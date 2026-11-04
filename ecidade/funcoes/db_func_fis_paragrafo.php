<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2015  DBselller Servicos de Informatica             
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

$campos = "fis_paragrafo.pl09_codigo,
           case when fis_paragrafo.pl09_tipo = 1 then fis_paragrafo.pl09_tipo||' - AUTO'
                when fis_paragrafo.pl09_tipo = 2 then fis_paragrafo.pl09_tipo||' - INTIMAÇÃO'
                when fis_paragrafo.pl09_tipo = 3 then fis_paragrafo.pl09_tipo||' - NOTIFICAÇÃO'
           end as pl09_tipo,
           fis_paragrafo.pl09_descr,fis_paragrafo.pl09_resumo,fis_paragrafo.pl09_texto,
           fis_paragrafo.pl09_status, fis_paragrafo.pl09_coddepto";
?>
