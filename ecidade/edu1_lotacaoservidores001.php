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

// -----
require 'libs/db_stdlibwebseller.php';
require 'libs/db_stdlib.php';
require 'libs/db_conecta.php';
require 'libs/db_sessoes.php';
require 'libs/db_usuariosonline.php';
require 'dbforms/db_funcoes.php';
require 'libs/db_jsplibwebseller.php';

db_postmemory($HTTP_POST_VARS);

$db_opcao           = 1;
$db_opcao1          = 1;
$db_botao           = true;
$naotem             = false;


$tipo_servidor = isset($_GET['tipo_servidor']) ? $_GET['tipo_servidor'] : '0';
$lotacao       = isset($_GET['lotacao']) ? $_GET['lotacao'] : '0';

if (!isset($_GET['codigo'])) {
    $_GET['codigo'] = '';
}

$codigo = $_GET['codigo'] == '' ? '' : $_GET['codigo'];

if (!isset($_GET['nome'])) {
    $_GET['nome'] = '';
}

$nome = $_GET['nome'] == '' ? '' : $_GET['nome'];

$escolas = isset($_GET['escolas']) ? $_GET['escolas'] : 'todas';
$ano     = isset($_GET['ano']) ? $_GET['ano'] : '';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<div class="container">
    <form onLoad="a=1">
        <fieldset title="Recursos Humanos">
            <legend>LOTAÇÃO DE SERVIDORES:</legend>
            <form name="form1">
                <table style="margin: auto">
                    <tr>
                        <td style="text-align: right">
                            <b>Escolas:</b>
                        </td>
                        <td>
                            <?php
                            $sql  = 'select  ed18_i_codigo,ed18_c_nome';
                            $sql .= '  from escola order by  ed18_i_codigo';

                            $result_escola = db_query($sql);

                            $rel = ['todas' => 'Todas as escolas'];
                            for ($x_escola = 0; $x_escola < pg_num_rows($result_escola); $x_escola++) {
                                db_fieldsmemory($result_escola, $x_escola);
                                $rel[$ed18_i_codigo] = $ed18_i_codigo.'   -   '.$ed18_c_nome;
                            }

                            db_select('escolas', $rel, true, 2);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right">
                            <b>Ano:</b>
                        </td>
                        <td>
                            <?php
                            $sql  = 'select DISTINCT ed52_i_ano';
                            $sql .= '  from calendario ORDER BY 1 DESC';

                            $result_calendario = db_query($sql);
                            $rel               = [];
                            for ($x_calendario = 0; $x_calendario < pg_num_rows($result_calendario); $x_calendario++) {
                                db_fieldsmemory($result_calendario, $x_calendario);
                                $rel[$ed52_i_ano] = $ed52_i_ano;
                            }

                            db_select('ano', $rel, true, 2);
                            ?>

                            <div class="alert alert-danger text-left" role="alert"
                                 style="margin-left:170px; margin-top: -18px; position: absolute">
                                <b>NÃO LOTADOS:</b> As Unidades Escolares<br>apresentadas são as últimas lotações
                                <br>do servidor informadas no Sistema.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right">
                            <b>Servidor Publico:</b>
                        </td>
                        <td>
                            <?php
                            $x = [
                                '0' => '',
                                '1' => 'SIM',
                                '2' => 'NÃO',
                            ];
                            db_select('tipo_servidor', $x, true, $db_opcao1, 'onChange="js_tiposervidor(this.value)"');
                            ?>
                        </td>
                    </tr>
                    <?php if ($tipo_servidor == '1') { ?>
                        <tr id="div_pessoal" style="position: relative;">
                        <tr>
                            <td style="text-align: right">
                                <b>Trazer Servidores:</b>
                            </td>
                            <td>
                                <?php
                                $x = [
                                    '0' => 'LOTADOS',
                                    '1' => 'NÃO LOTADOS',
                                ];
                                db_select('lotacao', $x, true, $db_opcao1, '');
                                ?>
                            </td>
                        </tr>
                        <td style="text-align: right">
                            <b>Matrícula:</b>
                        </td>
                        <td>
                            <input name="codigo_funcionario" type="text" id="codigo_funcionario"
                                   onkeyup="validaNumero(this)">
                        </td>
                        <tr>
                            <td style="text-align: right">
                                <b>Nome:</b>
                            </td>
                            <td>
                                <input style="width: 80%;" name="nome_funcionario" type="text"
                                       id="nome_funcionario"
                                       onkeyup="alteraMaiusculo(this)">
                            </td>
                        </tr>
                        <?php
                    }//end if
                    ?>
                    <?php if ($tipo_servidor == '2') { ?>
                        <tr id="div_cgm" style="position: relative;">
                        <tr>
                            <td style="text-align: right">
                                <b>Trazer Servidores:</b>
                            </td>
                            <td>
                                <?php
                                $x = [
                                    '0' => 'LOTADOS',
                                    '1' => 'NÃO LOTADOS',
                                ];

                                db_select('lotacao', $x, true, $db_opcao1, '');
                                ?>
                            </td>
                        </tr>
                        <td style="text-align: right">
                            <b>CGM:</b>
                        </td>
                        <td>
                            <input name="codigo_funcionario" type="text" id="codigo_funcionario"
                                   onkeyup="validaNumero(this)">
                        </td>
                        <tr>
                            <td style="text-align: right">
                                <b>Nome:</b>
                            </td>
                            <td>
                                <input style="width: 80%;" name="nome_funcionario" type="text"
                                       id="nome_funcionario"
                                       onkeyup="alteraMaiusculo(this)">
                            </td>
                        </tr>
                        <?php
                    }//end if
                    ?>
                    <tr>
                        <td height="4"></td>
                        <td height="4"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td>

                            <button type="button" value="Pesquisar" id="pesquisar2" name="pesquisar"
                                    onclick="js_pesquisar()">
                                <i class="fas fa-search"></i>
                                Pesquisar
                            </button>
                        </td>
                    </tr>
                </table>
        </fieldset>
    </form>
    <table>
        <tr>
            <td style="width: 25%"></td>
            <td style="width: 50%">
                <?php
                if (isset($_GET['pesquisar']) && $_GET['pesquisar'] == 1) {
                    $where = '';
                    if ($tipo_servidor == 1) {
                        if ($codigo != '') {
                            $where .= " AND rh01_regist = $codigo";
                        }

                        if ($nome != '') {
                            $where .= " AND  cgmrh.z01_nome LIKE '$nome%'";
                        }
                    }

                    if ($tipo_servidor == 2) {
                        if ($codigo != '') {
                            $where .= " AND cgmcgm.z01_numcgm = $codigo";
                        }

                        if ($nome != '') {
                            $where .= " AND cgmcgm.z01_nome LIKE '$nome%'";
                        }
                    }

                    if ($lotacao == 0) {
                        $where .= ' AND ed75_i_saidaescola is null';
                    }

                    if ($lotacao == 1) {
                        $where .= ' AND ed75_i_saidaescola is not null';
                        $where .= ' AND ( NOT EXISTS(SELECT * FROM rechumano
                        LEFT JOIN rechumanopessoal ON rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
                        LEFT JOIN rhpessoal ON rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
                        LEFT JOIN cgm AS cgmrhexists ON cgmrhexists.z01_numcgm = rhpessoal.rh01_numcgm
                        INNER JOIN rechumanoescola ON rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo
                        INNER JOIN escola ON escola.ed18_i_codigo = rechumanoescola.ed75_i_escola
                        WHERE ed20_i_tiposervidor = 1 AND  ed75_i_saidaescola is null AND cgmrhexists.z01_nome = cgmrh.z01_nome)
                        )';
                    }

                    if ($ano == '') {
                        $where .= " AND date_part('Y',ed75_d_ingresso) <= date_part('year', CURRENT_DATE)";
                    } else {
                        $where .= " AND date_part('Y',ed75_d_ingresso) <= '$ano'";
                    }

                    if ($escolas != 'todas') {
                        $where .= " AND ed18_i_codigo = $escolas ";
                    }

                    $alias = 'dl_Matricula';
                    if ($tipo_servidor == 2) {
                        $alias = 'dl_CGM';
                    }

                    $sql = "SELECT DISTINCT
                            case when $tipo_servidor = 1
                            then rh01_regist
                            else cgmcgm.z01_numcgm
                            end as $alias,

                            CASE WHEN $tipo_servidor = 1
                            THEN cgmrh.z01_cgccpf
                            ELSE cgmcgm.z01_cgccpf END   AS dl_cpf,

                            case when $tipo_servidor = 1
                            then cgmrh.z01_nome
                            else cgmcgm.z01_nome
                            end as z01_nome,
                            escola.ed18_c_nome,
                            atividaderh.ed01_c_descr as dl_Atividade

                            from rechumano
                            left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
                            left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
                            left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
                            left join db_config  on  db_config.codigo = rhpessoal.rh01_instit
                            left join rhpessoalmov on rhpessoalmov.rh02_anousu  = ".db_anofolha().'
                                                                      and rhpessoalmov.rh02_mesusu  = '.db_mesfolha()."
                                                                      and rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist
                            left join rhregime as regimerh on  regimerh.rh30_codreg = rhpessoalmov.rh02_codreg
                            left join rhlota  on  rhlota.r70_codigo = rhpessoal.rh01_lotac
                            left join rhpesdoc  on  rhpesdoc.rh16_regist = rhpessoal.rh01_regist
                            left join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv
                            left join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca
                            left join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao
                            left join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru
                            left join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion
                            left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
                            left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm
                            left join cgmdoc on  cgmdoc.z02_i_cgm = cgmcgm.z01_numcgm
                            left join rhregime as regimecgm on  regimecgm.rh30_codreg = rechumano.ed20_i_rhregime
                            inner join rhregime on rhregime.rh30_codreg = rechumano.ed20_i_rhregime
                            inner join rechumanoescola  on  rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo
                            inner join escola  on  escola.ed18_i_codigo = rechumanoescola.ed75_i_escola
                            left join relacaotrabalho  on  relacaotrabalho.ed23_i_rechumanoescola = rechumanoescola.ed75_i_codigo
                            left join rechumanoativ  on  rechumanoativ.ed22_i_rechumanoescola = rechumanoescola.ed75_i_codigo
                            left join atividaderh  on  atividaderh.ed01_i_codigo = rechumanoativ.ed22_i_atividade
                            left join disciplina  on  disciplina.ed12_i_codigo = relacaotrabalho.ed23_i_disciplina
                            left join caddisciplina on ed232_i_codigo= ed12_i_caddisciplina
                            where ed20_i_tiposervidor = $tipo_servidor  ".$where.' order by z01_nome';
                    db_lovrot($sql, 15, '', '', '');
                }//end if
                ?>
            </td>
            <td style="width:25%"></td>
        </tr>
    </table>
    <?php
    db_menu(
        db_getsession('DB_id_usuario'),
        db_getsession('DB_modulo'),
        db_getsession('DB_anousu'),
        db_getsession('DB_instit')
    );
    ?>
</div>
</body>
</html>
<script type="text/javascript">
    function js_tiposervidor(tipo) {
        let escolas = document.getElementById('escolas').value;
        let ano = document.getElementById('ano').value;

        location.href = "edu1_lotacaoservidores001.php?pesquisar=1&tipo_servidor=" + tipo + "&escolas=" + escolas
            + "&ano=" + ano;
    }

    function js_pesquisar() {
        let nome = document.getElementById('nome_funcionario').value;
        let codigo = document.getElementById('codigo_funcionario').value;
        let tipo_servidor = document.getElementById('tipo_servidor').value;
        let lotacao = document.getElementById('lotacao').value;
        let escolas = document.getElementById('escolas').value;
        let ano = document.getElementById('ano').value;

        location.href = "edu1_lotacaoservidores001.php?pesquisar=1&tipo_servidor=" + tipo_servidor + "&codigo="
            + codigo + "&nome=" + nome + "&lotacao=" + lotacao + "&escolas=" + escolas + "&ano=" + ano;
    }

    function alteraMaiusculo(elemento) {
        let valor = elemento;
        let novoTexto = valor.value.toUpperCase();
        valor.value = novoTexto;
    }

    function validaNumero(elemento) {
        let valor = elemento;
        if (isNaN(valor.value)) {
            alert("NÃO É UM NÚMERO")
            valor.value = "";
        }
    }
</script>
