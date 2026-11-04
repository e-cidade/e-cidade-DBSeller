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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_pcfornecon_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
$clpcfornecon = new cl_pcfornecon;
$clrotulo = new rotulocampo;
$clrotulo->label("");
?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script>
    </script>
    <style>
        .bordas {
            border: 2px solid #cccccc;
            border-top-color: #999999;
            border-right-color: #999999;
            border-left-color: #999999;
            border-bottom-color: #999999;
            background-color: #999999;
        }

        .bordas_corp {
            border: 1px solid #cccccc;
            border-top-color: #999999;
            border-right-color: #999999;
            border-left-color: #999999;
            border-bottom-color: #999999;
            background-color: #cccccc;
        }
    </style>
</head>

<body>
    <form name='form1'>
        <?php
        db_input('pc60_numcgm', 10, '', true, 'hidden', 3);

        if (!empty($pc60_numcgm)) {
            $sql = $clpcfornecon->sql_query_lefpadrao(
                null,
                "pc63_contabanco,pc63_cnpjcpf,pc63_numcgm,pc63_banco,pc63_agencia||' - '||
                pc63_agencia_dig as pc63_agencia,pc63_conta||' - '||
                pc63_conta_dig as pc63_conta,pc63_dataconf,
                case when pc64_contabanco is not null then 'Padrão' else '' end as pc64_contabanco",
                "pc63_contabanco",
                "pc63_numcgm = $pc60_numcgm"
            );
        }
        ?>
    </form>

    <div class="subcontainer">
        <?php
        $repassa = ['dblov' => '0'];
        db_lovrot(@$sql, 10, "()", "", "", "", "NoMe", $repassa);
        ?>
    </div>
</body>

</html>
