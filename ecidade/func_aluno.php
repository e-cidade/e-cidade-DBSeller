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

//MODULO: educação
require_once modification("libs/db_stdlibwebseller.php");
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("libs/db_jsplibwebseller.php");

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"], $queryString);

$claluno = new cl_aluno;
$clserie = new cl_serie;
$clrotulo = new rotulocampo;
$clsecparametros  = new cl_sec_parametros;
$clrotulo->label("ed47_i_codigo");
$clrotulo->label("ed47_v_nome");
$clrotulo->label("ed47_v_pai");
$clrotulo->label("ed47_v_mae");
$clrotulo->label("ov02_cnpjcpf");
$clrotulo->label("ed223_i_serie");
$clrotulo->label("ed47_v_cpf");
$clrotulo->label("ed47_c_codigoinep");
$clrotulo->label("ed47_c_nis");
$clrotulo->label("ed47_certidaomatricula");

$repassa = array();

// Busca campo em Secretaria > Procedimentos > Parâmetros > Parâmetros Globais > Habilita Consulta Aluno Por Escola.
$sqlSecParametros = $clsecparametros->sql_query("", "ed290_habilitaconsultaalunoporescola");
$resultSecParametros = $clsecparametros->sql_record($sqlSecParametros);
$sHabilitaConsultaAlunoPorEscola = pg_fetch_result($resultSecParametros, 0, "ed290_habilitaconsultaalunoporescola");

// Se Habilita Consulta Aluno Por Escola for verdadeiro então o campo Escola: em Escola > Consultas > Alunos trará
// apenas a Escola selecionada no departamento, caso contrário trará todas as Escolas do mesmo jeito se fosse acessado
// pelo módulo Secretaria.
$escolaFixa = '';
if ($sHabilitaConsultaAlunoPorEscola == 't') {
    if (db_getsession("DB_modulo") == 1100747) {
        $codescola = db_getsession("DB_coddepto");
        $escolaFixa = " ed18_i_codigo = " . db_getsession("DB_coddepto");
        if ((db_getsession("DB_coddepto") == 10)) {
            $escolaFixa = "";
        }
    }
}


?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html" charset="iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
</head>
<script>
    nextfield = "campo1"; // nome do primeiro campo
    netscape = "";
    ver = navigator.appVersion;
    len = ver.length;

    for (var iln = 0; iln < len; iln++) {

        if (ver.charAt(iln) == "(") {
            break;
        }
    }

    netscape = (ver.charAt(iln + 1).toUpperCase() != "C");

    function keyDown(DnEvents) {

        k = (netscape) ? DnEvents.which : window.event.keyCode;

        if (k == 13) { // pressiona tecla enter

            if (nextfield == 'done') {
                return true; // envia quando termina os campos
            } else {
                document.getElementById(nextfield).focus();
                return false;
            }
        }
    }

    document.onkeydown = keyDown;
    if (netscape) {
        document.captureEvents(Event.KEYDOWN | Event.KEYUP);
    }
</script>

<body class="body-default">
    <form name="form2" method="post" action="">
        <div class="container">
            <fieldset>
                <legend>Filtros</legend>

                <fieldset class="separator">
                    <legend>Dados do Aluno</legend>

                    <table border="0" align="center" cellspacing="3">
                        <?php
                        if (isset($iEscola) && $iEscola != '') {
                            db_input('iEscola', '10', '', true, 'hidden', 3, '', 'iEscola');
                            db_input('sAlunos', '300', '', true, 'hidden', 3, '', 'sAlunos');
                        }
                        ?>
                        <tr>
                            <td nowrap title="<?php echo $Ted47_i_codigo ?>">
                                <?php echo $Led47_i_codigo ?>
                            </td>
                            <td nowrap>
                                <?php db_input("ed47_i_codigo", 10, $Ied47_i_codigo, true, "text", 1, "onFocus=\"nextfield='pesquisar2'\"", "chave_ed47_i_codigo"); ?>
                            </td>
                            <td nowrap title="<?php echo $Ted47_v_nome ?>">
                                <?php echo $Led47_v_nome ?>
                            </td>
                            <td nowrap>
                                <input type="text" id="inputNovoNome" name="ed47_v_nome" onKeyPress="return lettersOnly(event)" style="width: 494px;" />
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?php echo $Ted47_v_mae ?>">
                                <?php echo $Led47_v_mae ?>
                            </td>
                            <td nowrap>
                                <input type="text" id="inputNovoNomeMae" name="ed47_v_mae" onKeyPress="return lettersOnly(event)" style="width: 494px;" />
                            </td>
                            <td nowrap title="<?php echo $Ted47_v_pai ?>">
                                <?php echo $Led47_v_pai ?>
                            </td>
                            <td nowrap>
                                <input type="text" id="inputNovoNomePai" name="ed47_v_pai" onKeyPress="return lettersOnly(event)" style="width: 494px;" />
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?php echo $Tov02_cnpjcpf ?>">
                                <b>CPF Filiação:</b>
                            </td>
                            <td nowrap>
                                <?php db_input("ov02_cnpjcpf", 50, 1, true, "text", 1, "onFocus=\"nextfield='pesquisar2'\"", "chave_ov02_cnpjcpf"); ?>
                            </td>
                            <td nowrap title="<?php echo $Tov02_cnpjcpf ?>">
                                <b>CPF Resp.:</b>
                            </td>
                            <td nowrap>
                                <?php db_input("ov02_cpfresp", 50, 1, true, "text", 1, "onFocus=\"nextfield='pesquisar2'\"", "chave_ov02_cpfresp"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?php echo $Ted223_i_serie ?>">
                                <?php echo $Led223_i_serie ?>
                            </td>
                            <td>
                                <?php
                                $sCamposSerie = "ed11_i_codigo, ed11_c_descr||' - '||ed10_c_descr as descr, ed11_i_ensino, ed11_i_sequencia";
                                $sSqlSerie = $clserie->sql_query_equiv("", $sCamposSerie, " ed11_i_ensino, ed11_i_sequencia", "");
                                $result_serie = $clserie->sql_record($sSqlSerie);

                                $x = array('' => 'NENHUM REGISTRO');
                                if ($clserie->numrows > 0) {
                                    $x = ['' => ' '];
                                    while ($state = pg_fetch_array($result_serie)) {
                                        $x[$state['ed11_i_codigo']] = $state['descr'];
                                    }
                                }

                                db_select('chave_ed223_i_serie', $x, true, 1, "onFocus=\"nextfield='pesquisar2'\"");

                                ?>
                            </td>
                            <td>
                                <b>Situação:</b>
                            </td>
                            <td>
                                <?php
                                $x = array(
                                    '' => '',
                                    'APROVADO' => 'APROVADO',
                                    'CANCELADO' => 'CANCELADO',
                                    'CANDIDATO' => 'CANDIDATO',
                                    'CONCLUÍDO' => 'CONCLUÍDO',
                                    'DESISTENTE' => 'DESISTENTE',
                                    'EVADIDO' => 'EVADIDO',
                                    'ABANDONO ESCOLAR' => 'ABANDONO ESCOLAR',
                                    'FALECIDO' => 'FALECIDO',
                                    'MATRICULADO' => 'MATRICULADO',
                                    'REPETENTE' => 'REPETENTE',
                                    'TRANSFERIDO FORA' => 'TRANSFERIDO FORA',
                                    'TRANSFERIDO REDE' => 'TRANSFERIDO REDE'
                                );
                                db_select('situacao', $x, true, 1, "onFocus=\"nextfield='pesquisar2'\"");
                                ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset class="separator">
                    <legend>Documentos</legend>
                    <table class="form-container">
                        <tr>
                            <td nowrap align="right" title="CPF">
                                <b>CPF:</b>
                            </td>
                            <td nowrap>
                                <?php db_input("ed47_v_cpf", 42, 1, true, "text", 1, "onFocus=\"nextfield='pesquisar2'\"", "ed47_v_cpf"); ?>
                            </td>
                            <td nowrap align="right" title="Cód. INEP">
                                <b>Código INEP:</b>
                            </td>
                            <td nowrap>
                                <?php db_input("ed47_c_codigoinep", 42, 1, true, "text", 1, "onFocus=\"nextfield='pesquisar2'\""); ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap align="right" title="Cód. NIS">
                                <b>NIS:</b>
                            </td>
                            <td nowrap>
                                <?php db_input("ed47_c_nis", 42, 1, true, "text", 1, "onFocus=\"nextfield='pesquisar2'\""); ?>
                            </td>
                            <td nowrap align="right" title="Certidão Matricula">
                                <b>Certidão de Nascimento (Nova):</b>
                            </td>
                            <td nowrap>
                                <?php db_input("ed47_certidaomatricula", 42, 1, true, "text", 1, "onFocus=\"nextfield='pesquisar2'\""); ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </fieldset>
            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar" onFocus="nextfield='done'">
            <input name="limpar" type="reset" id="limpar" value="Limpar">
            <input name="Fechar" type="button" id="fechar" value="Fechar" onClick="parent.db_iframe_aluno.hide();">
        </div>
    </form>
    <?php
    $escola = db_getsession("DB_coddepto");

    $where = [];
    if (isset($iEscola) && trim($iEscola) != '') {
        $situacao = 'MATRICULADO';

        $sSqlExists = " select matric.ed60_i_aluno                              ";
        $sSqlExists .= "   from matricula as matric                              ";
        $sSqlExists .= "        inner join turma on ed60_i_turma = ed57_i_codigo ";
        $sSqlExists .= "  where ed60_c_situacao     = '{$situacao}'              ";
        $sSqlExists .= "    and ed60_c_ativa        = 'S'                        ";
        $sSqlExists .= "    and ed57_i_escola       = {$iEscola}                 ";
        $sSqlExists .= "    and matric.ed60_i_aluno = aluno.ed47_i_codigo        ";
        $sSqlExists .= "    and matric.ed60_i_aluno not in({$sAlunos})           ";

        $where[] = "exists ({$sSqlExists})";
    }

    if (!empty($listar_apenas_progressao_parcial)) {
        $sSqlExistsProgressao = " exists (select 1 ";
        $sSqlExistsProgressao .= "           from progressaoparcialaluno";
        $sSqlExistsProgressao .= "                inner join situacaoeducacao on ed114_situacaoeducacao = ed109_sequencial";
        $sSqlExistsProgressao .= "          where ed114_aluno = aluno.ed47_i_codigo";

        if (!empty($listar_situacao_progressao_parcial)) {
            $sSqlExistsProgressao .= "   and ed114_situacaoeducacao in({$listar_situacao_progressao_parcial}) ";
        }

        if (!empty($progressao_parcial_ativa)) {
            $sSqlExistsProgressao .= "   and ed109_ativo is {$progressao_parcial_ativa}";
        }

        $sSqlExistsProgressao .= ")";
        $where[] = $sSqlExistsProgressao;
    }

    /**
     * Caso tenha sido passado como parâmetro lMatriculaEscola, busca somente alunos que possuam matrícula ativa
     * na escola logada
     */
    $sSqlMatriculaEscola = "";

    if (isset($lMatriculaEscola) && $sHabilitaConsultaAlunoPorEscola == 'f') {
        $sSqlMatriculaEscola .= " exists ( select 1 ";
        $sSqlMatriculaEscola .= "            from matricula ";
        $sSqlMatriculaEscola .= "                 INNER JOIN turma ON turma.ed57_i_codigo = matricula.ed60_i_turma ";
        $sSqlMatriculaEscola .= "           WHERE matricula.ed60_i_aluno = aluno.ed47_i_codigo ";
        $sSqlMatriculaEscola .= "             AND turma.ed57_i_escola        = {$escola} ";
        $sSqlMatriculaEscola .= "             AND matricula.ed60_c_situacao  = 'MATRICULADO' ";
        $sSqlMatriculaEscola .= "             AND matricula.ed60_c_concluida = 'N' ";
        $sSqlMatriculaEscola .= "             AND matricula.ed60_c_ativa     = 'S' ";
        $sSqlMatriculaEscola .= "             AND matricula.ed60_c_tipo      = 'N' )";
        $where[] = $sSqlMatriculaEscola;
    }

    if (!isset($iAlteracaoAluno) && $sHabilitaConsultaAlunoPorEscola == 't') {
        $sSqlMatriculaEscola .= " exists ( select 1 ";
        $sSqlMatriculaEscola .= "            from matricula ";
        $sSqlMatriculaEscola .= "                 INNER JOIN turma ON turma.ed57_i_codigo = matricula.ed60_i_turma ";
        $sSqlMatriculaEscola .= "           WHERE matricula.ed60_i_aluno = aluno.ed47_i_codigo AND turma.ed57_i_escola = {$escola})";
        $where[] = $sSqlMatriculaEscola;
    }

    if (!isset($pesquisa_chave) && (!isset($pesquisa_chave2))) {
        $sql = "SELECT * ";
        $sql .= " FROM ( ";
        $sql .= "         SELECT distinct on (aluno.ed47_i_codigo) aluno.ed47_i_codigo, ";
        $sql .= "                aluno.ed47_v_nome, ";
        $sql .= "                alunocurso.ed56_c_situacao, ";
        $sql .= "                serie.ed11_c_descr as dl_serie, ";
        $sql .= "                case ";
        $sql .= "                     when (alunocurso.ed56_c_situacao != '' or trim(alunocurso.ed56_c_situacao) != 'CANDIDATO') ";
        $sql .= "                     then ";
        $sql .= "                          (select ed57_c_descr ";
        $sql .= "                             from matricula  ";
        $sql .= "                                  inner join turma on ed57_i_codigo = ed60_i_turma ";
        $sql .= "                            where ed47_i_codigo = ed60_i_aluno  ";
        $sql .= "                            order by ed60_i_codigo desc limit 1) ";
        $sql .= "                     else null ";
        $sql .= "                 end as dl_turma, ";
        $sql .= "                case  ";
        $sql .= "                     when alunocurso.ed56_i_codigo is not null ";
        $sql .= "                     then ";
        $sql .= "                          case ";
        $sql .= "                               when alunocurso.ed56_c_situacao = 'TRANSFERIDO REDE' ";
        $sql .= "                               then ";
        $sql .= "                                    (select ed18_c_nome ";
        $sql .= "                                       from transfescolarede ";
        $sql .= "                                            inner join matricula on ed60_i_codigo = ed103_i_matricula ";
        $sql .= "                                            inner join turma     on ed57_i_codigo = ed60_i_turma ";
        $sql .= "                                            inner join escola    on ed18_i_codigo = ed57_i_escola ";
        $sql .= "                                      where ed60_i_aluno      = ed56_i_aluno ";
        $sql .= "                                        and ed57_i_base       = ed56_i_base ";
        $sql .= "                                        and ed57_i_calendario = ed56_i_calendario ";
        $sql .= "                                      order by ed103_d_data desc limit 1) ";
        $sql .= "                               else escola.ed18_c_nome ";
        $sql .= "                           end ";
        $sql .= "                      else null ";
        $sql .= "                  end as dl_escola, ";
        $sql .= "                cursoedu.ed29_c_descr as dl_curso, ";
        $sql .= "                calendario.ed52_c_descr as dl_calendario ";
        $sql .= "           FROM aluno ";
        $sql .= "                left join alunocurso  on alunocurso.ed56_i_aluno        = aluno.ed47_i_codigo ";
        $sql .= "                left join escola      on escola.ed18_i_codigo           = alunocurso.ed56_i_escola ";
        $sql .= "                left join calendario  on  calendario.ed52_i_codigo      = alunocurso.ed56_i_calendario ";
        $sql .= "                left join base        on  base.ed31_i_codigo            = alunocurso.ed56_i_base ";
        $sql .= "                left join cursoedu    on  cursoedu.ed29_i_codigo        = base.ed31_i_curso ";
        $sql .= "                left join alunopossib on  alunopossib.ed79_i_alunocurso = alunocurso.ed56_i_codigo ";
        $sql .= "                left join serie       on  serie.ed11_i_codigo           = alunopossib.ed79_i_serie ";

        if (isset($lPesquisaTransportePublico)) {
            $dtCalendario = date('Y', db_getsession('DB_datausu'));

            if (empty($situacao)) {
                $situacao = 'MATRICULADO';
            }

            $where[] = "trim(ed47_c_transporte) = '{$iTransporte}'";
            $where[] = " ed47_i_transpublico     = {$iUtilizaTransporte}";
            $where[] = " trim(ed56_c_situacao)   = '{$situacao}'";
            $where[] = " ed52_i_ano = {$dtCalendario}";
        }

        if (isset($chave_ed47_i_codigo) && !empty($chave_ed47_i_codigo)) {
            $repassa = array(
                "chave_ed47_i_codigo" => $chave_ed47_i_codigo,
                "chave_ed47_v_nome" => $ed47_v_nome,
                "chave_ed47_v_mae" => $ed47_v_mae,
                "chave_ed47_v_pai" => $ed47_v_pai,
                "chave_ov02_cnpjcpf" => $chave_ov02_cnpjcpf,
                "chave_ov02_cpfresp" => $chave_ov02_cpfresp,
                "chave_ed223_i_serie" => $chave_ed223_i_serie,
                "ed47_v_cpf" => $ed47_v_cpf,
                "ed47_c_codigoinep" => $ed47_c_codigoinep,
                "ed47_c_nis" => $ed47_c_nis,
                "ed47_certidaomatricula" => $ed47_certidaomatricula,
                "situacao" => $situacao
            );
        }

        if (isset($iEscola) && trim($iEscola) != '' && $sHabilitaConsultaAlunoPorEscola != 't') {
            $where[] = "ed18_i_codigo = {$iEscola}";
        }

        $lBuscaDados = false;

        $whereFiltros = [];
        if (isset($chave_ed47_i_codigo) && (trim($chave_ed47_i_codigo) != "")) {
            $whereFiltros[] = "ed47_i_codigo = {$chave_ed47_i_codigo}";
        }
        if (isset($ed47_v_nome) && (trim($ed47_v_nome) != "")) {
            $whereFiltros[] = "ed47_v_nome ilike '{$ed47_v_nome}%'";
        }
        if (isset($ed47_v_pai) && (trim($ed47_v_pai) != "")) {
            $whereFiltros[] = "ed47_v_pai ilike '{$ed47_v_pai}%'";
        }
        if (isset($ed47_v_mae) && (trim($ed47_v_mae) != "")) {
            $whereFiltros[] = "ed47_v_mae ilike '{$ed47_v_mae}%'";
        }
        if (isset($chave_ov02_cnpjcpf) && (trim($chave_ov02_cnpjcpf) != "")) {
            $lBuscaDados = true;
            $sql .= " join alunocidadao on aluno.ed47_i_codigo = alunocidadao.ed330_aluno";
            $sql .= " join cidadaofiliacao on alunocidadao.ed330_cidadao = cidadaofiliacao.ov29_cidadao ";
            $sql .= " join cidadao on cidadaofiliacao.ov29_cidadao = cidadao.ov02_sequencial ";
            $sql .= " join cidadao cidadao2 ON cidadao2.ov02_sequencial = cidadaofiliacao.ov29_cidadaovinculo ";
            $whereFiltros[] = "cidadao2.ov02_cnpjcpf ilike '$chave_ov02_cnpjcpf%'";
        }
        if (isset($chave_ov02_cpfresp) && (trim($chave_ov02_cpfresp) != "")) {
            $lBuscaDados = true;
            $sql .= " left join alunocidadaoresponsavel on aluno.ed47_i_codigo = alunocidadaoresponsavel.ed331_aluno ";
            $sql .= " left join cidadao on alunocidadaoresponsavel.ed331_cidadao = cidadao.ov02_sequencial ";
            $whereFiltros[] = "ov02_cnpjcpf ilike '$chave_ov02_cpfresp%'";
        }
        if (isset($chave_ed223_i_serie) && (trim($chave_ed223_i_serie) != "")) {
            $whereFiltros[] = "ed79_i_serie = {$chave_ed223_i_serie}";
        }
        if (isset($situacao) && (trim($situacao) != "")) {
            $whereFiltros[] = "trim(ed56_c_situacao) = '{$situacao}'";
        }
        if (isset($ed47_v_cpf) && (trim($ed47_v_cpf) != "")) {
            $whereFiltros[] = "ed47_v_cpf = '{$ed47_v_cpf}'";
        }
        if (isset($ed47_c_codigoinep) && (trim($ed47_c_codigoinep) != "")) {
            $whereFiltros[] = "ed47_c_codigoinep = '{$ed47_c_codigoinep}'";
        }
        if (isset($ed47_c_nis) && (trim($ed47_c_nis) != "")) {
            $whereFiltros[] = "ed47_c_nis = '{$ed47_c_nis}'";
        } else if (isset($ed47_certidaomatricula) && (trim($ed47_certidaomatricula) != "")) {
            $whereFiltros[] = "ed47_certidaomatricula ilike '{$ed47_certidaomatricula}%'";
        }
        if (count($whereFiltros)) {
            $where = array_merge($where, $whereFiltros);
        }
        $sql .= " where " . implode(' and ', $where);
        $sql .= ") as x ORDER BY to_ascii(ed47_v_nome)";
        // Carrega a tabela do db_lovrot caso haja dados no array $_POST
        // Ou seja, verifica se o array $_POST não está vazio, indicando que o formulário foi enviado.
        // Essa verificação evita que a página tente carregar a tabela do db_lovrot sem que haja dados enviados
        if ($_POST) {
            echo '<div class="container">';
            echo '  <fieldset>';
            echo '    <legend>Resultado da Pesquisa</legend>';
            db_lovrot(@$sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa);
            echo '  </fieldset>';
            echo '</div>';
        }
    } else {
        if (isset($pesquisa_chave) && $pesquisa_chave != null && $pesquisa_chave != "") {
            $where[] = "ed47_i_codigo = {$pesquisa_chave}";
            $where = implode(' and ', $where);

            $result = $claluno->sql_record($claluno->sql_query_file("", "*", "", $where));

            if ($claluno->numrows != 0) {
                db_fieldsmemory($result, 0);
                echo "<script>" . $funcao_js . "('$ed47_i_codigo', '$ed47_v_nome');</script>";
            } else {
                echo "<script>" . $funcao_js . "(null);</script>";
            }
        } else {
            echo "<script>" . $funcao_js . "('',false);</script>";
        }

        if (isset($pesquisa_chave2) && $pesquisa_chave2 != null && $pesquisa_chave2 != "") {
            $where[] = "ed47_i_codigo = {$pesquisa_chave2}";
            $where = implode(' and ', $where);

            $result = $claluno->sql_record($claluno->sql_query_file("", "*", "", $where));

            if ($claluno->numrows != 0) {
                db_fieldsmemory($result, 0);
                echo "<script>" . $funcao_js . "('$ed47_v_nome',false, '$ed47_i_codigo');</script>";
            } else {
                echo "<script>" . $funcao_js . "(null,true);</script>";
            }
        } else {
            echo "<script>" . $funcao_js . "('',false);</script>";
        }
    }
    ?>
</body>

</html>
<script>
    js_tabulacaoforms("form2", "chave_ed47_i_codigo", true, 1, "chave_ed47_i_codigo", true);
    const inputNovoNome = document.querySelector('#inputNovoNome');
    const inputNovoNomePai = document.querySelector('#inputNovoNomePai');
    const inputNovoNomeMae = document.querySelector('#inputNovoNomeMae');

    function lettersOnly(evt) {
        if (!/[a-záéíóúàèìòùãõâêîôûäëïöüç'% ]/i.test(evt.key)) return false;
    }
    inputNovoNome.addEventListener('keyup', () => {
        inputNovoNome.value = inputNovoNome.value.toUpperCase()
    })
    inputNovoNomePai.addEventListener('keyup', () => {
        inputNovoNomePai.value = inputNovoNomePai.value.toUpperCase()
    })
    inputNovoNomeMae.addEventListener('keyup', () => {
        inputNovoNomeMae.value = inputNovoNomeMae.value.toUpperCase()
    })
    <?php
    if (isset($iEscola) && trim($iEscola) != '') { ?>

        var oOption = document.getElementById('situacao');
        oOption.value = 'MATRICULADO';

        for (var iIndice = 0; iIndice < oOption.options.length; iIndice++) {

            if (oOption.options[iIndice].value != 'MATRICULADO') {
                oOption.options[iIndice].disabled = true;
            }
        }

    <?php } ?>
</script>
<script type="text/javascript">
    (function() {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
