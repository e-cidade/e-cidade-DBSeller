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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("classes/db_orcfontes_classe.php"));
require_once(modification("classes/db_orcreceita_classe.php"));
$clorcfontes = new cl_orcfontes;
$clorcreceita = new cl_orcreceita;
$clorcfontes->rotulo->label();
$clorcreceita->rotulo->label();
db_postmemory($HTTP_POST_VARS);
?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

    <script>
        function js_abre(codrec) {
            js_OpenJanelaIframe('', 'db_iframe_orgao', 'func_saldoorcreceita.php?o70_codrec=' + codrec, 'pesquisa', true);
        }
    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body >
<div class="container">
    <?php
    $filtro = " o70_anousu = " . db_getsession("DB_anousu") . " and o70_instit = " . db_getsession("DB_instit");
    $filtro .= " and o15_datalimite is null or o15_datalimite > '" . date('Y-m-d', db_getsession('DB_datausu')) . "'";
    $sql = "
    select fc_estruturalreceita(o70_anousu,o70_codrec) as dl_estrutural, o57_descr, o70_codrec, gestao, o15_recurso,
           descricao,(o15_complemento ||' - '|| o200_descricao)::varchar as complemento
     from orcreceita d
      join orcfontes e on e.o57_codfon = d.o70_codfon and o57_anousu = o70_anousu
      join orctiporec r on r.o15_codigo = d.o70_codigo
      join fonterecurso on fonterecurso.orctiporec_id = r.o15_codigo
        and fonterecurso.exercicio = o70_anousu
      join complementofonterecurso on o200_sequencial = o15_complemento";
    if (!empty($filtro)) {
        $sql .= " where ";
        $sql .= $filtro . " order by dl_estrutural";
    }

    db_lovrot($sql, 20, "()", "", "js_abre|o70_codrec");
    ?>
</div>
<?php
db_menu();
?>
</body>
</html>
