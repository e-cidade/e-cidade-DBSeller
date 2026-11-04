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

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Factories\DocumentoAndamentoFactory;
use App\Domain\Patrimonial\Protocolo\Model\AtividadeExecucao;
use App\Domain\Patrimonial\Protocolo\Model\DocumentoAndamento;
use App\Domain\Patrimonial\Protocolo\Repository\DocumentosMovimentacaoRepository;
use ECidade\Financeiro\Orcamento\Recurso\Origem;

require_once(modification("libs/db_stdlib.php"));
require_once(modification('libs/db_utils.php'));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("model/Dotacao.model.php"));
require_once(modification("model/CgmFactory.model.php"));
require_once(modification("classes/db_empempenho_classe.php"));
require_once(modification("classes/db_empelemento_classe.php"));
require_once(modification("classes/db_orcdotacao_classe.php"));
require_once(modification("classes/db_empempaut_classe.php"));
require_once(modification("classes/db_empemphist_classe.php"));
require_once(modification("classes/db_emphist_classe.php"));
require_once(modification("classes/db_orctiporec_classe.php"));
require_once(modification("classes/db_empagemov_classe.php"));
require_once(modification("classes/db_empautitem_classe.php"));
require_once(modification("classes/db_empempitem_classe.php"));
require_once(modification("classes/db_empempenhonl_classe.php"));
require_once(modification('classes/db_empresto_classe.php'));
require_once(modification("dbforms/verticalTab.widget.php"));

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <link href="estilos/tab.style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/infoLancamentoContabil.classe.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <style>
        .valores {
            background-color: #FFFFFF
        }
        /**
         * @TODO Criar arquivo separado
         */
        .stepper li {
            list-style: none;
            float: left;
            padding: 0px 30px;
            position: relative;
            text-align: center;
            color: #b7b7b7;
        }
        .stepper li i {
            width: 42px;
            height: 42px;
            line-height: 42px;
            border: 2px solid #b7b7b7;
            border-radius: 30px;
            display: block;
            text-align: center;
            background-color: #e1dede;
            margin-bottom: 7px;
        }
        .stepper li:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: #b7b7b7;
            top: 21px;
            left: -50%;
            z-index: -1;
        }
        .stepper li:first-child:after {
            content: none;
        }
        .stepper li.active {
            color: #0f3d64;
        }
        .stepper li.active i {
            border: 2px solid #0f3d64;
        }
        .stepper li.active:after {
            background-color: #0f3d64;
        }
    </style>
</head>
<body bgcolor="#CCCCCC">
<form name='form1'>
    <fieldset style='padding-left:0px'>
        <legend><b>Detalhamento</b></legend>
        <?php
        $oTabDetalhes = new verticalTab("detalhesemp2", 300);
        $oTabDetalhes->add("lancamentos", "Lançamentos", "emp2_consultasubcontratacao002.php?e23_sequencial={$e21_sequencial}");
        $oTabDetalhes->add(
            "transferencias_bancarias",
            "Transferências Bancárias",
            "emp2_consultasubcontratacao003.php?e23_sequencial={$e21_sequencial}"
        );
        $oTabDetalhes->add("recolhimento", "Recolhimento", "emp2_consultasubcontratacao004.php?e23_sequencial={$e21_sequencial}");
        $oTabDetalhes->add("subcontratacoes", "Subcontratações", "emp2_consultasubcontratacao001.php?e23_sequencial={$e21_sequencial}");
        $oTabDetalhes->show();
        ?>
    </fieldset>
</form>
</body>
</html>
<script>

</script>
