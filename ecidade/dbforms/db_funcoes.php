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

//#00#//documentacao
//#10#//Como documentar ou funcao ou classe
//#99#////#10#//
//#99#//Descrição da função ou método gerada no *//#00#//*
//#99#////#15#//
//#99#//Sintaxe da função ou método
//#99#////#20#//
//#99#//Parâmetros fornecidos para a função ou método, como mostra a sintaxe *//#10#//*
//#99#////#30#//
//#99#//Propriedades ou variaveis de função
//#99#////#40#//
//#99#//Retorno da função ou método
//#99#////#99#//
//#99#//Observação sobre a função ou método

//#00#//db_opcao
//#10#//Opção do sistema para inclusão, alteração ou exclusão em formulários
//#99#// 1 - Inclusão
//#99#//22 - Inicio do formulário antes de selecionar um ítem para alterar
//#99#// 3 - Exclusão
//#99#//33 - Inicio do formulário antes de selecionar um ítem para excluir
//#99#// 5 - Objeto desabilitado no formulário ( disabled )
if (!function_exists("db_inicio_transacao")) {
    function db_inicio_transacao()
    {
        //#00#//db_inicio_transacao
        //#10#//função para abrir uma transação
        //#15#//db_inicio_transacao();
        //#99#//Uma transação é um conjunto de execuções no banco de dados que deverão ser gravadas somente
        //#99#//se todas as execuções tiverem sucesso, caso contrário, nenhuma das execuções deverá ser
        //#99#//confirmada
        db_query('BEGIN');
        return;
    }
}

if (!function_exists("db_fim_transacao")) {
    function db_fim_transacao($erro = false)
    {
        //#00#//db_fim_transacao
        //#10#//função para finalizar uma transação
        //#20#//false : Finaliza transação com sucesso (commit)
        //#20#//true  : Transação com erro, desfaz os procedimentos executados (rollback)
        if ($erro == true) {
            db_query('ROLLBACK');
        } else {
            db_query('COMMIT');
        }
        return;
    }
}

// Parametros do $tipo
// 1  Bota as contas do plano que não existem no saltes
// 2  Bota as contas do saltes
// 3  Bota as contas do plano
if (!function_exists("db_contas")) {
    function db_contas($nome, $valor = "", $tipo = 1)
    {
        if ($tipo == 1) {
            //echo    $sql_redu = "select c62_reduz,
            //                  c60_descr
            //       from conplano
            //             inner join conplanoreduz on c61_codcon = c60_codcon
            //             inner join conplanoexe   on c62_reduz  = c61_reduz
            //                                     and c62_anousu = ".db_getsession("DB_anousu")."
            //       where substr(c60_estrut,1,3) in ('111') order by c60_estrut";
            $sql_desc = "select c62_reduz,
					                    c60_descr
						 from conplano
						       inner join conplanoreduz on c61_codcon = c60_codcon and
                                                                            c61_anousu=c60_anousu and
						       			                                    c61_instit = " . db_getsession("DB_instit") . "
						       inner join conplanoexe   on c62_reduz  = c61_reduz
						                               and c62_anousu = " . db_getsession("DB_anousu") . "
						 where substr(c60_estrut,1,3) in ('111')
                                  and c60_anousu=" . db_getsession("DB_anousu") . "
                         order by c60_estrut";

            //  $sql_desc = "select c01_reduz,c01_descr
            //            from plano
            //       where c01_reduz <> 0 and substr(c01_estrut,1,3) in ('111','112') and c01_anousu = ".db_getsession("DB_anousu")."
            //       order by c01_descr";
        } elseif ($tipo == 2) {
            $sql_redu = "select p.k13_conta,l.c01_descr
							             from saltes p
										      inner join saltesplan s on s.k13_conta = p.k13_conta and s.c01_anousu = " . db_getsession("DB_anousu") . "
										      inner join conplanoexe on c62_reduz = k13_reduz and c62_anousu = " . db_getsession("DB_anousu") . "
										      inner join conplanoreduz on c62_reduz = c61_reduz and c61_instit = " . db_getsession("DB_instit") . "
                         															and c61_anousu=c62_anousu
											  inner join plano l on l.c01_anousu = " . db_getsession("DB_anousu") . " and l.c01_reduz = s.c01_reduz
									     order by p.k13_conta";
            $sql_desc = "select p.k13_conta,l.c01_descr
							             from saltes p
										      inner join saltesplan s on s.k13_conta = p.k13_conta  and s.c01_anousu = " . db_getsession("DB_anousu") . "
											  inner join plano l on l.c01_anousu = " . db_getsession("DB_anousu") . " and l.c01_reduz = s.c01_reduz
										 order by c01_descr";
        } elseif ($tipo == 3) {
            $sql_redu = "select c01_reduz,c01_descr
									             from plano
												 where c01_anousu = " . db_getsession("DB_anousu") . " and c01_reduz <> 0 order by c01_reduz";
            $sql_desc = "select c01_reduz,c01_descr
									             from plano
												 where c01_anousu = " . db_getsession("DB_anousu") . " and c01_reduz <> 0 order by c01_descr";
        }
        ?>
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td nowrap>
                    <select name="<?php echo  $nome ?>" onChange="js_ProcCod('<?php echo  $nome ?>','<?php echo  $nome . "descr" ?>')">
                        <?php


                        $result_redu = db_query($sql_redu);
                        $numrows = pg_num_rows($result_redu);
                        for ($i = 0; $i < $numrows; $i++) {
                            echo "<option value=\"" . pg_fetch_result($result_redu, $i, 0) . "\" >" . pg_fetch_result($result_redu, $i, 0) . "</option>\n";
                        }
                        ?>
                    </select>&nbsp;&nbsp;
                    <select name="<?php echo  $nome . "descr" ?>"
                            onChange="js_ProcCod('<?php echo  $nome . "descr" ?>','<?php echo  $nome ?>')">
                        <?php

                        $result_desc = db_query($sql_desc);
                        for ($i = 0; $i < $numrows; $i++) {
                            echo "<option value=\"" . pg_fetch_result($result_desc, $i, 0) . "\">" . pg_fetch_result($result_desc, $i, 1) . "</option>\n";
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <script>
            function js_ProcCod(proc, res) {
                var sel1 = document.form1.elements[proc];
                var sel2 = document.form1.elements[res];
                for (var i = 0; i < sel1.options.length; i++) {
                    if (sel1.options[sel1.selectedIndex].value == sel2.options[i].value)
                        sel2.options[i].selected = true;
                }
            }

            document.form1.elements['<?php echo $nome?>'].options[0].selected = true;
            js_ProcCod('<?php echo $nome?>', '<?php echo $nome . "descr"?>');
        </script>
        <?php
    }
}

/**
 * Monta um input na tela, utilizando a documentação do sistema
 * @param string $nome Nome            : Nome do campo da documentacao do sistema ou do arquivo
 * @param integer $dbsize Tamanho         : Tamanho do objeto na tela (default tamanho na documentação)
 * @param integer $dbvalidatipo Validáção       : Tipo de validação JAVASCRIPT para o campo, retirado da documentação
 * @param boolean $dbcadastro Cadastro        : True se cadastro ou false se nao cadastro Padrão: true )
 * @param string $dbhidden Type            : Tipo do objeto INPUT a ser mostrado na tela (text,hidden,file,submit,button,...) Padrão: text
 * @param number $db_opcao Opcao           : *db_opcao* do programa a ser executado neste objeto input, inclusão(1) alteração(2) exclusão(3)
 * @param string $js_script Script          : JAVASCRIPT  a ser executado juntamento com o objeto, indicando os métodos
 * @param string $nomevar Nome Secundário : Nome do input que será gerado, assumindo somente as características do campo Nome
 * @param string $bgcolor Cor Background  : Cor de fundo da tela, no caso de *db_opcao*=3 será "#DEB887"
 * @param string $css Estilo CSS      : Css Personalidado do Componente
 * @param string $iMaxLen MaxLenght       : Maximo de Caractereres
 * @return void
 * @example db_input($nome,$dbsize,$dbvalidatipo,$dbcadastro,$dbhidden='text',$db_opcao=3,$js_script="",$nomevar="",$bgcolor="");
 */
if (!function_exists("db_input")) {
    function db_input($nome, $dbsize, $dbvalidatipo, $dbcadastro, $dbhidden = 'text', $db_opcao = 3, $js_script = "", $nomevar = "", $bgcolor = "", $css = "", $iMaxLen = null, $bOcultaJavascript = false)
    {

        if ($iMaxLen != null && !empty($iMaxLen)) {
            $iMax = $iMaxLen;
        } else {
            $iMax = @$GLOBALS['M' . $nome];
        }
        ?>

        <input title="<?php echo  @$GLOBALS['T' . $nome] ?>" name="<?php echo  ($nomevar == "" ? $nome : $nomevar) ?>"
               type="<?php echo  $dbhidden ?>" <?php echo  ($dbhidden == "checkbox" ? (@$GLOBALS[($nomevar == "" ? $nome : $nomevar)] == "t" ? "checked" : "") : "") ?>
               id="<?php echo  ($nomevar == "" ? $nome : $nomevar) ?>"
               value="<?php echo  @$GLOBALS[($nomevar == "" ? $nome : $nomevar)] ?>" size="<?php echo  $dbsize ?>"
               maxlength="<?php echo  @$iMax ?>"
            <?php

            echo $js_script;
            if ($dbcadastro == true) {
                if ($db_opcao == 3 || $db_opcao == 22 || $db_opcao == 33 || $db_opcao == 11) {
                    echo " readonly ";
                    if ($bgcolor == "") {
                        $bgcolor = "#DEB887";
                    }
                }
                if ($db_opcao == 5) {
                    echo " disabled ";
                }
            }
            $db_style = '';
            if ($bgcolor == "") {
                echo " " . @ $GLOBALS['N' . $nome] . " ";
            } else {
                $db_style .= "background-color:$bgcolor;";
            }

            if (isset($GLOBALS['G' . $nome]) && $GLOBALS['G' . $nome] == 't') {
                $db_style .= "text-transform:uppercase;";
            }

            if ($db_style != '') {
                if ($css != "") {
                    echo " style=\"$db_style;$css\" ";
                } else {
                    echo " style=\"$db_style\" ";
                }
            } else {
                if ($css != "") {
                    echo " style=\"$css\" ";
                }
            }

            if (($db_opcao != 3) && ($db_opcao != 5 && !$bOcultaJavascript)) {
                ?>
                onkeyup="js_ValidaMaiusculo(this,'<?php echo  @$GLOBALS['G' . $nome] ?>',event);"
                onInput="js_ValidaCampos(this,<?php echo  ($dbvalidatipo == '' ? 0 : $dbvalidatipo) ?>,'<?php echo  @$GLOBALS['S' . $nome] ?>','<?php echo  ($db_opcao == 4 ? "t" : @$GLOBALS['U' . $nome]) ?>','<?php echo  @$GLOBALS['G' . $nome] ?>',event);"
                onKeyDown="return js_controla_tecla_enter(this,event);"
                <?php
            }
            ?>
               autocomplete='<?php echo  @$GLOBALS['A' . $nome] ?>'
               labelValidacao="<?php echo  @$GLOBALS['LS' . $nome] ?>" <?php echo  (@$GLOBALS[($nomevar == "" ? 'U' . $nome : 'U' . $nomevar)] == "f" ? "isMandatory='true'" : "") ?>>
        <?php
    }
}
/*************************************/
/**
 * Função para montar um textarea na tela do programa
 *
 * @param $nome - Nome            : Nome do campo da documentacao do sistema ou do arquivo
 * @param $dbsizelinha - Numero Linhas   : Número de linhas do objeto textarea
 * @param $dbsizecoluna - Numero Colunas  : Número de Coluna do objeto textarea
 * @param $dbvalidatipo - Validáção       : Tipo de validação JAVASCRIPT para o campo, retirado da documentação
 * @param $dbcadastro - Cadastro        : True se cadastro ou false se nao cadastro Padrão: true )
 * @param $dbhidden - Type            : Tipo do objeto INPUT a ser mostrado na tela (text,hidden,type,submit,...) Padrão: text
 * @param $db_opcao - Opcao           : *db_opcao* do programa a ser executado neste objeto input, inclusão(1) alteração(2) exclusão(3)
 * @param $js_script - Script          : JAVASCRIPT  a ser executado juntamento com o objeto, indicando os métodos
 * @param $nomevar - Nome Secundário : Nome do input que será gerado, assumindo somente as características do campo Nome
 * @param $bgcolor - Cor Background  : Cor de fundo da tela, no caso de *db_opcao*=3 será "#DEB887"
 * @param $maxlength - Maxlenght       : Tamanho máximo permitido para escrita no textarea
 * @example db_textarea($nome,$dbsizelinha=1,$dbsizecoluna=1,$dbvalidatipo,$dbcadastro=true,$dbhidden='text',$db_opcao=3,$js_script="",$nomevar="",$bgcolor="");
 *
 */
if (!function_exists("db_textarea")) {
    function db_textarea($nome, $dbsizelinha = 1, $dbsizecoluna = 1, $dbvalidatipo, $dbcadastro = true, $dbhidden = 'text', $db_opcao = 3, $js_script = "", $nomevar = "", $bgcolor = "", $maxlength = "")
    {

        $sOnInput = "";
        ?>
        <textarea labelValidacao="<?php echo  @$GLOBALS['LS' . $nome] ?>" title="<?php echo  @$GLOBALS['T' . $nome] ?>"
                  name="<?php echo  ($nomevar == "" ? $nome : $nomevar) ?>" type="<?php echo  $dbhidden ?>"
                  id="<?php echo  ($nomevar == "" ? $nome : $nomevar) ?>" rows="<?php echo  $dbsizelinha ?>" cols="<?php echo  $dbsizecoluna ?>"
        <?php
        echo $js_script;
        if ($dbcadastro == true) {
            /*
           if ($db_opcao==3 || $db_opcao==22){só coloquei a opcao 11...  dia 28-10-2004
      */
            if ($db_opcao == 3 || $db_opcao == 22 || $db_opcao == 11 || $db_opcao == 33) {
                echo " readonly ";
                if ($bgcolor == "") {
                    $bgcolor = "#DEB887";
                }
            }
            if ($db_opcao == 5) {
                echo " disabled ";
            }
        }
        //começa a colocas CSS
        $db_style = '';
        if ($bgcolor != "") {
            $db_style = 'background-color:' . $bgcolor . ';';
        }

        if (isset($GLOBALS['G' . $nome]) && $GLOBALS['G' . $nome] == 't') {
            $db_style .= "text-transform:uppercase;";
        }

        if ($db_style != '') {
            echo " style=\"$db_style\" ";
        }
        $OnBlur = " js_ValidaMaiusculo(this,'" . @$GLOBALS['G' . $nome] . "',event); ";
        $OnKeyUp = " js_ValidaCampos(this," . ($dbvalidatipo == '' ? 0 : $dbvalidatipo) . ",'" . @$GLOBALS['S' . $nome] . "','" . @$GLOBALS['U' . $nome] . "','" . @$GLOBALS['G' . $nome] . "',event); ";

        if ($maxlength != "") {
            $sOnInput = " js_maxlenghttextarea(this,event," . $maxlength . "); ";
            $OnKeyUp .= $sOnInput;
        }

        $sValue = (!isset($GLOBALS[$nome]) ? "" : stripslashes($GLOBALS[($nomevar == "" ? $nome : $nomevar)]));
        ?>
                        onblur="<?php echo  $OnBlur ?>"
                  onKeyUp="<?php echo  $OnKeyUp ?>"
                  onInput="<?php echo $sOnInput; ?>"

        <?php echo  @$GLOBALS['N' . $nome] ?>

                        autocomplete='<?php echo  @$GLOBALS['A' . $nome] ?>'><?php echo $sValue; ?></textarea>
        <?php
        if ($maxlength != "") {
            echo "<br>";
            echo "<div align='right'>";
            echo "<span style='float:left;color:red;font-weight:bold' id='{$nome}errobar'></span>";
            echo " <b> Caracteres Digitados : </b> ";
            echo "  <input type='text' name='{$nome}obsdig' id='{$nome}obsdig' size='3' value='" . strlen($sValue) . "' style='color: #000;' disabled> ";
            echo " <b> - Limite " . $maxlength . " </b> ";
            echo "</div> ";
        }
    }
}
if (!function_exists("db_ancora")) {
    function db_ancora($nome, $js_script, $db_opcao = "", $style = "", $varnome = "")
    {
        //#00#//db_ancora
        //#10#//Coloca uma âncora no Label do campo e executa uma função JAVASCRIPT para pesquisa do arquivo em referencia
        //#15#//db_ancora($nome,$js_script,$db_opcao,$style="");
        //#20#//Nome : Nome do campo da documentação do sistema ou do arquivo
        //#20#//Script : Função JAVASCRIPT que será executado no onclik do objeto label
        //#20#//Opcao : *db_opcao* do programa a ser executado neste objeto input, inclusão(1) alteração(2) exclusão(3)
        //#20#//Style : Opção para programador mudar o estilo da âncora
        if (($db_opcao < 3) || ($db_opcao == 4)) {
            if ($varnome != "") {
                ?>
                <a id="<?php echo  $varnome ?>" class='DBAncora'
                   style='text-decoration:underline;<?php echo  trim($style) != "" ? ";$style" : "" ?>'
                   onclick="<?php echo  $js_script ?>"><?php echo  $nome ?></a>
                <?php
            } else {
                ?>
                <a class='DBAncora'
                   style='text-decoration:underline;<?php echo  trim($style) != "" ? ";$style" : "" ?>'
                   onclick="<?php echo  $js_script ?>"><?php echo  $nome ?></a>
                <?php
            }
        } else {
            echo $nome;
        }
    }
}
/*************************************/

if (!function_exists("db_multiploselect")) {
    function db_multiploselect($valueobj, $descrobj, $objnsel = "", $objsel = "", $recordnsel, $recordsel, $nlinhas = 10, $width = 250, $descrnsel = "", $descrsel = "", $ordenarselect = true, $jsincluir = "")
    {
        // Função para montar dois objetos select multiple na tela, recebendo dados de recordset distintos. Selects para seleção em que ficam passando as informações de um para o outro.
        // valueobj   : Campo que será o value dos objetos.
        // descrobj   : Campo que será a descrição nos objetos.
        // objnsel    : Nome do objeto dos valores a selecionar.
        // objsel     : Nome do objeto dos valores já selecionados.
        // recordnsel : Recordset ou array com os valores a selecionar.
        // recordsel  : Recordset ou array com os valores já selecionados.
        // nlinhas    : Número de linhas que os objetos terão. Valor default é 10.
        // width      : Largura dos objetos. Valor default é 250.
        // descrsel   : Descrição que aparecerá no FIELDSET dos itens a selecionar
        // descrnsel  : Descrição que aparecerá no FIELDSET dos itens já selecionados
        // ordenarselect : True se programador desejar ordenar os values dentro dos selects ao mudar os itens de lugar.
        // jsincluir  : Função JavaScript chamada ao passar campos de um select para o outro
        if (trim($descrnsel) == "") {
            $descrnsel = "A selecionar";
        }
        if (trim($descrsel) == "") {
            $descrsel = "Selecionados";
        }
        if (trim($objnsel) == "") {
            $objnsel = "objeto1";
        }
        if (trim($objsel) == "") {
            $objsel = "objeto2";
        }
        ?>
        <table>
            <tr>
                <td>
                    <fieldset>
                        <Legend align="left">
                            <b><?php echo  $descrnsel ?></b>
                        </Legend>
                        <select name="<?php echo  $objnsel ?>[]" id="<?php echo  $objnsel ?>" size="<?php echo  $nlinhas ?>"
                                style="width:<?php echo  $width ?>px" multiple
                                onDblClick="js_db_multiploselect_incluir_item(this,document.form1.<?php echo  $objsel ?>);">
                            <?php
                            if (gettype($recordnsel) == "resource") {
                                $numrows_recnsel = pg_num_rows($recordnsel);
                                for ($i = 0; $i < $numrows_recnsel; $i++) {
                                    db_fieldsmemory($recordnsel, $i);
                                    global $$valueobj;
                                    global $$descrobj;
                                    echo "<option value='" . $$valueobj . "'>" . $$descrobj . "</option>\n";
                                }
                            } elseif (gettype($recordnsel) == "array") {
                                $numrows_recnsel = count($recordnsel);
                                reset($recordnsel);
                                for ($i = 0; $i < $numrows_recnsel; $i++) {
                                    $$valueobj = key($recordnsel);
                                    $$descrobj = $recordnsel[$$valueobj];
                                    echo "<option value='" . $$valueobj . "'>" . $$descrobj . "</option>\n";
                                    next($recordnsel);
                                }
                            }
                            ?>
                        </select>
                    </fieldset>
                </td>
                <td width='10%' align='center'>
                    <table>
                        <tr>
                            <td align='center'><input type='button' name='selecionD'
                                                      title='Enviar selecionados para direita' value='&nbsp;>&nbsp;'
                                                      onclick='js_db_multiploselect_incluir_item(document.form1.<?php echo  $objnsel ?>,document.form1.<?php echo  $objsel ?>);'>
                            </td>
                        </tr>
                        <tr>
                            <td align='center'><input type='button' name='seltodosD' title='Enviar todos para direita'
                                                      value='>>'
                                                      onclick='js_db_multiposelect_incluir_todos(document.form1.<?php echo  $objnsel ?>,document.form1.<?php echo  $objsel ?>);'>
                            </td>
                        </tr>
                        <tr>
                            <td align='center'><input type='button' name='selecionE'
                                                      title='Enviar selecionados para esquerda' value='&nbsp;<&nbsp;'
                                                      onclick='js_db_multiploselect_incluir_item(document.form1.<?php echo  $objsel ?>,document.form1.<?php echo  $objnsel ?>);'>
                            </td>
                        </tr>
                        <tr>
                            <td align='center'><input type='button' name='seltodosE' title='Enviar todos para esquerda'
                                                      value='<<'
                                                      onclick='js_db_multiposelect_incluir_todos(document.form1.<?php echo  $objsel ?>,document.form1.<?php echo  $objnsel ?>);'>
                            </td>
                        </tr>
                    </table>
                </td>
                <td nowrap>
                    <fieldset>
                        <Legend align="left">
                            <b><?php echo  $descrsel ?></b>
                        </Legend>
                        <select name="<?php echo  $objsel ?>[]" id="<?php echo  $objsel ?>" size="<?php echo  $nlinhas ?>"
                                style="width:<?php echo  $width ?>px" multiple
                                onDblClick="js_db_multiploselect_incluir_item(this,document.form1.<?php echo  $objnsel ?>);">
                            <?php
                            if (gettype($recordsel) == "resource") {
                                $numrows_recsel = pg_num_rows($recordsel);
                                for ($i = 0; $i < $numrows_recsel; $i++) {
                                    db_fieldsmemory($recordsel, $i);
                                    global $$valueobj;
                                    global $$descrobj;
                                    echo "<option value='" . $$valueobj . "'>" . $$descrobj . "</option>\n";
                                }
                            } elseif (gettype($recordsel) == "array") {
                                $numrows_recsel = count($recordsel);
                                reset($recordsel);
                                for ($i = 0; $i < $numrows_recsel; $i++) {
                                    $$valueobj = key($recordsel);
                                    $$descrobj = $recordsel[$$valueobj];
                                    echo "<option value='" . $$valueobj . "'>" . $$descrobj . "</option>\n";
                                    next($recordsel);
                                }
                            }
                            ?>
                        </select>
                    </fieldset>
                </td>
                <td nowrap valign="center">
                    <?php
                    if ($ordenarselect == false) {
                        echo "
                      <img onClick='js_sobe();return false;' src='skins/img.php?file=Controles/seta_up.png' />
                  <br/><br/>
                 <img onClick='js_desce()' src='skins/img.php?file=Controles/seta_down.png' />
                   ";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="3" align='center'>
                    <b>Dois Clicks para Movimentar os Itens</b>
                </td>
            </tr>
        </table>
        <script>
            function js_sobe() {
                var F = document.form1.<?php echo $objsel?>;
                if (F.selectedIndex != -1 && F.selectedIndex > 0) {
                    var SI = F.selectedIndex - 1;
                    var auxText = F.options[SI].text;
                    var auxValue = F.options[SI].value;
                    F.options[SI] = new Option(F.options[SI + 1].text, F.options[SI + 1].value);
                    F.options[SI + 1] = new Option(auxText, auxValue);
                    js_trocacordeselect();
                    F.options[SI].selected = true;
                }
            }

            function js_desce() {
                var F = document.form1.<?php echo $objsel?>;
                if (F.selectedIndex != -1 && F.selectedIndex < (F.length - 1)) {
                    var SI = F.selectedIndex + 1;
                    var auxText = F.options[SI].text;
                    var auxValue = F.options[SI].value;
                    F.options[SI] = new Option(F.options[SI - 1].text, F.options[SI - 1].value);
                    F.options[SI - 1] = new Option(auxText, auxValue);
                    js_trocacordeselect();
                    F.options[SI].selected = true;
                }
            }

            // Retorna apenas campos selecionados
            // variavel = return js_db_multiploselect_retornaselecionados();
            function js_db_multiploselect_retornaselecionados() {
                txt11 = "";
                vir11 = "";

                for (i = 0; i < document.form1.<?php echo $objsel?>.length; i++) {
                    txt11 += vir11 + document.form1.<?php echo $objsel?>.options[i].value;
                    vir11 = ",";
                }
                stringretorno = txt11;
                return stringretorno;
            }

            // Retorna apenas campos não selecionados
            // variavel = return js_db_multiploselect_retornanaoselecionados();
            function js_db_multiploselect_retornanaoselecionados() {
                txt11 = "";
                vir11 = "";

                for (i = 0; i < document.form1.<?php echo $objnsel?>.length; i++) {
                    txt11 += vir11 + document.form1.<?php echo $objnsel?>.options[i].value;
                    vir11 = ",";
                }
                stringretorno = txt11;
                return stringretorno;
            }

            // Retorna apenas campos selecionadosn e não selecionados
            // variavel = return js_db_multiploselect_retornacampos();
            function js_db_multiploselect_retornacampos() {
                txt22 = "";
                vir22 = "";

                txt11 = "";
                vir11 = "";

                for (i = 0; i < document.form1.<?php echo $objnsel?>.length; i++) {
                    txt22 += vir22 + document.form1.<?php echo $objnsel?>.options[i].value;
                    vir22 = ",";
                }

                for (i = 0; i < document.form1.<?php echo $objsel?>.length; i++) {
                    txt11 += vir11 + document.form1.<?php echo $objsel?>.options[i].value;
                    vir11 = ",";
                }
                stringretorno = txt22 + "#" + txt11;
                return stringretorno;
            }

            // Função para incluir todos os elementos do SELECT MULTIPLE escolhido no outro
            // Esta função selecionará todos os elementos do SELECT e chamará a função js_db_multiploselect_incluir_item para enviar os itens
            // para o SELECT desejado. Quando retornar da função js_db_multiploselect_incluir_item, ela limpará o select remetente
            function js_db_multiposelect_incluir_todos(obj1, obj2) {
                for (i = 0; i < obj1.length; i++) {
                    obj1.options[i].selected = true;
                }
                linhasoption = obj2.length;
                js_db_multiploselect_incluir_item(obj1, obj2);
                obj1.length = 0;
                if (linhasoption == 0) {
                    for (i = 0; i < obj2.length; i++) {
                        obj2.options[i].selected = false;
                    }
                }
            }

            // Esta função serve para passar os itens de um SELECT para o outro.
            function js_db_multiploselect_incluir_item(obj1, obj2) {
                var erro = 0;

                // Tirar o foco de todos os itens do select RECEPTOR
                for (i = 0; i < obj2.length; i++) {
                    obj2.options[i].selected = false;
                }

                // Verifica a quantidade de itens no SELECT EMISSOR
                for (i = 0; i < obj1.length; i++) {

                    // Testa se o item corrente esta selecionado
                    if (obj1.options[i].selected) {

                        // Seta o valor defaul do novo item do SELECT RECEPTOR
                        x = obj2.length;

                        // Se for para ordenar os itens dentro dos selects ao serem mudados de local
                        ordenaritens = true;
                        <?php
                        if ($ordenarselect == false) {
                            echo "ordenaritens = false;\n";
                        }
                        ?>

                        // Se a quantidade de itens do SELECT RECEPTOR for maior que zero, testa se encontra algum item que o valor
                        // seja maior que o item corrente do SELECT EMISSOR
                        if (obj2.length > 0 && ordenaritens == true) {
                            for (x = 0; x < obj2.length; x++) {
                                if (obj1.options[i].value < obj2.options[x].value) {
                                    break;
                                }
                            }

                            // Repete no SELECT RECEPTOR o seu último item
                            obj2.options[obj2.length] = new Option(obj2.options[obj2.length - 1].text, obj2.options[obj2.length - 1].value);

                            // Busca todos os itens que o valor é menor que o último item e reorganiza os dados dentro do SELECT
                            for (y = obj2.length - 1; x < y; y--) {
                                obj2.options[y] = new Option(obj2.options[y - 1].text, obj2.options[y - 1].value)
                            }
                        }

                        // Inclui o item que esta vindo do select EMISSOR
                        obj2.options[x] = new Option(obj1.options[i].text, obj1.options[i].value);
                        obj2.options[x].selected = true;
                        erro++;
                    }
                }
                if (erro > 0) {
                    // Tira a seleção dos itens do SELECT EMISSOR
                    for (i = 0; i < obj1.length; i++) {
                        if (obj1.options[i].selected) {
                            obj1.options[i] = null;
                            i = -1;
                        }
                    }
                } else {
                    alert("Selecione um item");
                }
                <?php echo $jsincluir?>
                js_trocacordeselect();
            }

            js_trocacordeselect();
        </script>
        <?php
    }
}
if (!function_exists("db_selectrecord")) {
    function db_selectrecord($nome, $record, $dbcadastro, $db_opcao = 3, $js_script = "", $nomevar = "", $bgcolor = "", $todos = "", $onchange = "", $numcol = 2)
    {
        //#00#//db_selectrecord
        //#10#//Função para montar um ou dois objetos select na tela, recebendo dados de um recordset
        //#15#//db_selectrecord($nome,$record,$dbcadastro,$db_opcao=3,$js_script="",$nomevar="",$bgcolor="",$todos="",$onchange="",$numcol=2);
        //#20#//Nome            : Nome do ca po da documentacao do sistema ou do arquivo
        //#20#//Record Set      : Recordset que gerará os objetos select, sendo o primeiro campo do recordset o campo chave
        //#20#//                  e o segundo campo a descricao.
        //#20#//Cadastro        : True se cadastro ou false se nao cadastro Padrão: true )
        //#20#//Opcao           : *db_opcao* do programa a ser executado neste objeto input, inclusão(1) alteração(2) exclusão(3)
        //#20#//Script          : JAVASCRIPT  a ser executado juntamento com o objeto, indicando os métodos
        //#20#//Nome Secundário : Nome do input que será gerado, assumindo somente as características do campo Nome
        //#20#//Cor Background  : Cor de fundo da tela, no caso de *db_opcao*=3 será "#DEB887"
        //#20#//Todos           : Indica de será colocado um ítem inicial com opção de todos "Todos ..." com valor zero (0)
        //#20#//OnChange        : Função que será incluída no método onchange dos objetos select, além das funçõe ja incluídas
        //#20#//                  que servem para movimentar os select. Sempre que alterar um deles, o sistema altera o outro
        //#20#//Numero Select   : Número de select que serão mostrados na tela. O padrão é dois, caso seja indicado este
        //#20#//                  parâmetro, o sistema mostrará somente o select do segundo campo (descrição) e retornará o
        //#20#//                  código do ítem, o valor do primeiro campo
        //#99#//Quando o parâmetro *db_opcao* for de alteração (Opcao = 22) ou exclusão (Opção = 33) o sistema
        //#99#//não mostrará os objetos desta função e sim executará o objeto INPUT com as opções deste
        //#99#//objeto. Isto faz com que o usuário não movimente um select enquanto não selecionar um
        //#99#//código de registro para alterar ou excluir
        //#99#//
        //#99#//O tamanho do objeto na tela dependerá do tamanho do campo inserido no select
        //#99#//
        //#99#//Após montar o select, sistema executa uma função javascript para selecionar o elemento
        //#99#//do select que possui o mesmo valor do campo indicado na variável Nome
        if ($db_opcao != 3 && $db_opcao != 5 && $db_opcao != 22 && $db_opcao != 33 && $db_opcao != 11) {
            if ($nomevar != "") {
                $nome = $nomevar;
                $nomedescr = $nomevar . "descr";
            } else {
                $nomedescr = $nome . "descr";
            }
            if ($numcol == 2) {
                ?>
                <select name="<?php echo  $nome ?>" id="<?php echo  $nome ?>"
                    <?php


                    if ($numcol == 2) {
                        echo "onchange=\"js_ProcCod_$nome('$nome','$nomedescr');$onchange\"";
                    } else {
                        echo "onchange=\"$onchange\"";
                    }
                    if ($dbcadastro == true) {
                        if ($db_opcao == 3 || $db_opcao == 22 || $db_opcao == 11) {
                            echo " readonly ";
                            if ($bgcolor == "") {
                                $bgcolor = "#DEB887";
                            }
                        }
                        if ($db_opcao == 5) {
                            echo " disabled ";
                        }
                    }
                    echo $js_script;
                    ?>
                >
                    <?php


                    if ($todos != "") {
                        if (strpos($todos, "-") > 0) {
                            $todos = explode("-", $todos);
                        } else {
                            $todos = array("0" => $todos, "1" => "Todos ...");
                        }
                        ?>
                        <option value="<?php echo  $todos[0] ?>"><?php echo  $todos[0] ?></option>
                        <?php
                    }
                    $iTotalRegistros = 0;
                    if ($record) {
                        $iTotalRegistros = pg_num_rows($record);
                    }
                    for ($sqli = 0; $sqli < $iTotalRegistros; $sqli++) {
                        $sqlv = pg_fetch_result($record, $sqli, 0);
                        ?>
                        <option
                            value="<?php echo  $sqlv ?>" <?php echo  (@$GLOBALS[$nome] == $sqlv ? "selected" : "") ?>><?php echo  $sqlv ?></option>
                        <?php
                    }
                    ?>
                </select>
                <?php
            } else {
                $nomedescr = $nome;
            }
            if ($record != false && pg_num_rows($record) > 0 && pg_num_fields($record) > 0) {
                ?>
                <select name="<?php echo  $nomedescr ?>" id="<?php echo  $nomedescr ?>"
                        onchange="js_ProcCod_<?php echo  $nome ?>('<?php echo  $nomedescr ?>','<?php echo  $nome ?>');<?php echo  $onchange ?>"
                    <?php


                    if ($dbcadastro == true) {
                        if ($db_opcao == 3 || $db_opcao == 22) {
                            echo " readonly ";
                            if ($bgcolor == "") {
                                $bgcolor = "#DEB887";
                            }
                        }
                        if ($db_opcao == 5) {
                            echo " disabled ";
                        }
                    }
                    echo $js_script;
                    ?>
                >
                    <?php


                    if (is_array($todos) || $todos != "") {
                        ?>
                        <option value="<?php echo  $todos[0] ?>"><?php echo  $todos[1] ?></option>
                        <?php
                    }
                    for ($sqli = 0; $sqli < pg_num_rows($record); $sqli++) {
                        $sqlv = pg_fetch_result($record, $sqli, 0);
                        $sqlv1 = pg_fetch_result($record, $sqli, 1);
                        ?>
                        <option value="<?php echo  $sqlv ?>"><?php echo  $sqlv1 ?></option>
                        <?php
                    }
                    ?>
                </select>
                <script>
                    function js_ProcCod_<?php echo $nome?>(proc, res) {
                        var sel1 = document.forms[0].elements[proc];
                        var sel2 = document.forms[0].elements[res];
                        for (var i = 0; i < sel1.options.length; i++) {
                            if (sel1.options[sel1.selectedIndex].value == sel2.options[i].value)
                                sel2.options[i].selected = true;
                        }
                    }
                    <?php



                    if (isset($GLOBALS[$nome])) {
                        if ($GLOBALS[$nome] != "") {
                            echo "var sel1 = document.form1.$nome;\n";
                            echo "for(var i = 0;i < sel1.options.length;i++) {\n";
                            echo "  if(sel1.options[i].value == '" . $GLOBALS[$nome] . "')\n";
                            echo "  sel1.options[i].selected = true;\n";
                            echo "}\n";
                        } else {
                            echo "document.forms[0]." . $nome . ".options[0].selected = true;";
                        }
                    } else {
                        echo "document.forms[0]." . $nome . ".options[0].selected = true;";
                    }
                    ?>
                    js_ProcCod_<?php echo $nome?>('<?php echo $nome?>', '<?php echo $nomedescr?>');
                </script>
                <?php
            } else {
                ?>
                <script>
                    function js_ProcCod_<?php echo $nome?>() {
                    }
                </script>
                <?php
            }
        } else {
            $clrot = new rotulocampo;
            $clrot->label("$nome");
            $tamm = "M$nome";
            db_input($nome, $GLOBALS[$tamm], '', true, 'text', 3, "", $nomevar, "");
            $nomec = "";
            if ($nomevar != "") {
                $nome = $nomevar;
                $nomedescr = $nomevar . "descr";
            } else {
                $nomedescr = $nome . "descr";
            }

            if (is_resource($record)) {
                for ($sqli = 0; $sqli < pg_num_rows($record); $sqli++) {
                    if (pg_fetch_result($record, $sqli, 0) == @ $GLOBALS[$nome]) {
                        $nomec = pg_field_name($record, 1);
                        global $$nomec;
                        $$nomec = pg_fetch_result($record, $sqli, 1);
                        $clrot->label($nomec);
                        $tamm = "M" . trim($nomec);
                        break;
                    }
                }
            }
            if (!empty($nomec)) {
                if ($GLOBALS[$tamm] > 40) {
                    $GLOBALS[$tamm] = 60;
                }
                db_input($nomec, $GLOBALS[$tamm], '', true, 'text', 3, "");
            }
        }
    }
}
//////////////////////////////////////
if (!function_exists("db_selectmultiple")) {
    function db_selectmultiple($nome, $record, $size, $db_opcao = 3, $js_script = "", $nomevar = "", $bgcolor = "", $record_select = "", $onchange = "", $compltags = "")
    {
        //#00#//db_selectmultiple
        //#10#//Função para montar um objeto select do tipo multiple (multiplas linhas) na tela, recebendo dados de um recordset
        //#15#//db_selectmultiple($nome,$record,$size,$db_opcao=3,$js_script="",$nomevar="",$bgcolor="",$record_select="",$onchange="");
        //#20#//Nome            : Nome do ca po da documentacao do sistema ou do arquivo
        //#20#//Record Set      : Recordset ou Array que gera o objeto select, sendo o primeiro campo do recordset o campo chave
        //#20#//                  e o segundo campo a descricao que aparecerá na tela
        //#20#//Tamanho         : Número de linhas que o objeto ocupará na tela
        //#20#//Opcao           : *db_opcao* do programa a ser executado neste objeto input, inclusão(1) alteração(2) exclusão(3)
        //#20#//Script          : JAVASCRIPT  a ser executado juntamento com o objeto, indicando os métodos
        //#20#//Nome Secundário : Nome do input que será gerado, assumindo somente as características do campo Nome
        //#20#//Cor Background  : Cor de fundo da tela, no caso de *db_opcao*=3 será "#DEB887"
        //#20#//Record Set      : Este recordset enviado para a função terá os valores que serão habilitados no objeto select
        //#20#//                  multiple, colocandos-os com a propriedade selected habilidata
        //#20#//OnChange        : Função ou funções que serão incluídas no método onchange dos objetos select.
        //#20#//compltags       : Complementos da tag do Select como por exemplo: onclick, onblur, etc...
        //#99#//Quando o parâmetro Opção for de alteração (Opcao = 22) ou exclusão (Opção = 33) o sistema
        //#99#//não mostrará os objetos desta função e sim executará o objeto SELECT com as opções do
        //#99#//segundo recordset, mostrando somente os dados cadastrados no código de registro para alterar
        //#99#//ou excluir
        if ($nomevar != "") {
            $nome = $nomevar;
        }
        if ($db_opcao != 3 && $db_opcao != 5 && $db_opcao != 33 && $db_opcao != 22) {
            /*change="js_ProcCod_<?php echo $nome?>('<?php echo $nome?>','<?php echo $nome?>');<?php echo $onchange?>"tava assim dae eu mudei pra : (ze)*/
            ?>
            <select class="DBSelectMultiplo" multiple name="<?php echo  $nome ?>[]" size="<?php echo  $size ?>" id="<?php echo  $nome ?>"
                    onchange="<?php echo  $js_script ?>" <?php echo  $compltags ?>
                <?php


                if ($db_opcao == 3 || $db_opcao == 22) {
                    echo " readonly ";
                    if ($bgcolor == "") {
                        $bgcolor = "#DEB887";
                    }
                }
                if ($db_opcao == 5) {
                    echo " disabled ";
                }
                echo $js_script;
                ?>
            >
                <?php
                if (gettype($record) == "resource") {
                    for ($sqli = 0; $sqli < pg_num_rows($record); $sqli++) {
                        if ($sqli % 2 == 0) {
                            $color = "#D7CC06";
                        } else {
                            $color = "#F8EC07";
                        }
                        $sqlv = pg_fetch_result($record, $sqli, 0);
                        $sqlv1 = pg_fetch_result($record, $sqli, 1);
                        $esta_selecionado = "";
                        if ($db_opcao != 1 && $db_opcao != 22 && is_resource($record_select)) {
                            for ($sqls = 0; $sqls < pg_num_rows($record_select); $sqls++) {
                                $sqlsv = pg_fetch_result($record_select, $sqls, 0);
                                if ($sqlsv == $sqlv) {
                                    $esta_selecionado = " selected ";
                                }
                            }
                        }
                        /** <option value="<?php echo $sqlv?>" style="background-color:<?php echo $color?>" <?php echo $esta_selecionado?>><?php echo $sqlv1?></option> */
                        ?>
                        <option value="<?php echo  $sqlv ?>" <?php echo  $esta_selecionado ?>><?php echo  $sqlv1 ?></option>
                        <?php
                    }
                } elseif (gettype($record) == "array") {
                    $numrows_recsel = count($record);
                    reset($record);
                    for ($sqli = 0; $sqli < $numrows_recsel; $sqli++) {
                        if ($sqli % 2 == 0) {
                            $color = "#D7CC06";
                        } else {
                            $color = "#F8EC07";
                        }
                        $valueobj = key($record);
                        $descrobj = $record[$valueobj];
                        $esta_selecionado = "";
                        if ($db_opcao != 1 && $db_opcao != 22) {
                            reset($record_select);
                            for ($sqls = 0; $sqls < count($record_select); $sqls++) {
                                $sqlsv = key($record_select);
                                if ($sqlsv == $valueobj) {
                                    $esta_selecionado = " selected ";
                                }
                                next($record_select);
                            }
                        }
                        /**option value="<?php echo $valueobj?>" style="background-color:<?php echo $color?>" <?php echo $esta_selecionado?>><?php echo $descrobj?></option>*/
                        ?>
                        <option value="<?php echo  $valueobj ?>" <?php echo  $esta_selecionado ?>><?php echo  $descrobj ?></option>
                        <?php
                        next($record);
                    }
                }
                ?>
            </select>
            <?php
        } else {
            if (!is_int($record_select) && $record_select != false) {
                if (gettype($record) == "resource") {
                    if (pg_num_rows($record_select) > 0) {
                        db_selectrecord($nome, $record_select, true, ($db_opcao == 3 ? 2 : $db_opcao), "", $nomevar = "", $bgcolor = "", $todos = "", $onchange = "");
                    }
                } elseif (gettype($record) == "array") {
                    if (count($record_select) > 0) {
                        db_select($nome, $record_select, true, ($db_opcao == 3 ? 2 : $db_opcao), "", "", "");
                    }
                }
            } else {
                db_input($nome, 5, '', true, 'text', 3, "");
            }
        }
    }
}
/**
 * Função para montar um objeto select na tela, recebendo dados de uma matriz
 *
 * Quando o parâmetro Opção for de alteração (Opcao = 22) ou exclusão (Opção = 33) o sistema
 * não mostrará o objeto desta função e sim executará o objeto INPUT e colocará o valor do
 * conteúdo para este bjeto
 *
 * O sistema verifica o valor do campo Nome (conteúdo do campo) e verifica se algum dos
 * campos key da matriz é igual a ele, então coloca a propriedade SELECTED habilitada
 * para este elemento, deixando-o selecionado na tela
 *
 * @param mixed $nome
 * @param mixed $db_matriz - Matriz com os dados a serem colocados no select, sendo a chave (key) da matriz o valor
 *                              a ser retornado e o conteúdo da matriz o valor a ser mostrado na tela
 *                              ex: $x = array("1"=>"um") 1=key e um=conteúdo;
 * @param mixed $dbcadastro - True se cadastro ou false se nao cadastro Padrão: true )
 * @param integer $db_opcao - *db_opcao* do programa a ser executado neste objeto input, inclusão(1) alteração(2) exclusão(3)
 * @param string $js_script - JAVASCRIPT  a ser executado juntamento com o objeto, indicando os métodos
 * @param string $nomevar - Nome do input que será gerado, assumindo somente as características do campo Nome
 * @param string $bgcolor - Cor de fundo da tela, no caso de *db_opcao*=3 será "#DEB887"
 * @access public
 * @return void
 */
if (!function_exists("db_select")) {
    function db_select($nome, $db_matriz, $dbcadastro, $db_opcao = 3, $js_script = "", $nomevar = "", $bgcolor = "")
    {

        if ($db_opcao != 3 && $db_opcao != 5 && $db_opcao != 22 && $db_opcao != 33) {
            ?>
            <select name="<?php echo  $nome ?>" id="<?php echo  $nome ?>"
                <?php


                if ($dbcadastro == true) {
                    if ($db_opcao == 3 || $db_opcao == 22) {
                        echo " readonly ";
                        if ($bgcolor == "") {
                            $bgcolor = "#DEB887";
                        }
                    }
                    if ($db_opcao == 5) {
                        echo " disabled ";
                    }
                }
                echo $js_script;
                ?>
            >
                <?php

                $nomevar = $nomevar == "" ? $nome : $nomevar;

                //x = array("a"=>"1","2")
                reset($db_matriz);
                for ($i = 0; $i < sizeof($db_matriz); $i++) {
                    ?>
                    <option
                        value="<?php echo  key($db_matriz) ?>" <?php echo  (@$GLOBALS[$nomevar] == key($db_matriz) ? "selected" : "") ?>><?php echo  $db_matriz[key($db_matriz)] ?></option>
                    <?php


                    next($db_matriz);
                }
                ?>
            </select>
            <?php
        } else {
            $nome_select_descr = $nome . "_select_descr";
            global $$nome_select_descr, $$nome;
            $$nome = $GLOBALS[$nome];

            reset($db_matriz);
            for ($matsel = 0; $matsel < sizeof($db_matriz); $matsel++) {
                if (key($db_matriz) == $$nome) {
                    $$nome_select_descr = $db_matriz[key($db_matriz)];
                    $$nome = key($db_matriz);
                }
                next($db_matriz);
            }
            if (strlen($$nome_select_descr) > 8) {
                if (strlen($$nome_select_descr) > 40) {
                    $tamanho = 60;
                } else {
                    $tamanho = strlen($$nome_select_descr);
                }
            } else {
                $tamanho = strlen($$nome_select_descr);
            }
            $Mtam = "M$nome";
            global $$Mtam;
            $$Mtam = $tamanho;
            db_input($nome_select_descr, $tamanho + 4, '', $dbcadastro, 'text', 3, "", "", "");
            db_input($nome, $tamanho + 4, '', $dbcadastro, 'hidden', 3, "", "", "");
        }
    }
}
//////////////////////////////////////
if (!function_exists("db_inputdata")) {
    function db_inputdata($nome, $dia = "", $mes = "", $ano = "", $dbcadastro = true, $dbtype = 'text', $db_opcao = 3, $js_script = "", $nomevar = "", $bgcolor = "", $shutdown_function = "none", $onclickBT = "", $onfocus = "", $jsRetornoCal = "", $validaCor = "")
    {
        //#00#//db_inputdata
        //#10#//Função para montar um objeto tipo data. Serão três objetos input na tela mais um objeto input tipo button para
        //#10#//acessar o calendário do sistema
        //#15#//db_inputdata($nome,$dia="",$mes="",$ano="",$dbcadastro=true,$dbtype='text',$db_opcao=3,$js_script="",$nomevar="",$bgcolor="",$shutdown_funcion="none",$onclickBT="",$onfocus"");
        //#20#//Nome            : Nome do campo da documentacao do sistema ou do arquivo
        //#20#//Dia             : Valor para o objeto |db_input| do dia
        //#20#//Mês             : Valor para o objeto |db_input| do mês
        //#20#//Ano             : Valor para o objeto |db_input| do ano
        //#20#//Cadastro        : True se cadastro ou false se nao cadastro Padrão: true
        //#20#//Type            : Tipo a ser incluido para a data Padrão: text
        //#20#//Opcao           : *db_opcao* do programa a ser executado neste objeto input, inclusão(1) alteração(2) exclusão(3)
        //#20#//Script          : JAVASCRIPT  a ser executado juntamento com o objeto, indicando os métodos
        //#20#//Nome Secundário : Nome do input que será gerado, assumindo somente as características do campo Nome
        //#20#//Cor Background  : Cor de fundo da tela, no caso de *db_opcao*=3 será "#DEB887"
        //#20#//shutdown_funcion : função que será executada apos o retorno do calendário
        //#20#//onclickBT       : Função que será executada ao clicar no botão que abre o calendário
        //#20#//onfocus         : Função que será executada ao focar os campos
        //#99#//Quando o parâmetro Opção for de alteração (Opcao = 22) ou exclusão (Opção = 33) o sistema
        //#99#//colocará a sem acesso ao calendário
        //#99#//Para *db_opcao* 3 e 5 o sistema colocará sem o calendário e com readonly
        //#99#//
        //#99#//Os três input gerados para a data terão o nome do campo acrescido do [Nome]_dia, [Nome]_mes e
        //#99#//[Nome]_ano os quais serão acessados pela classe com estes nome.
        //#99#//
        //#99#//O sistema gerá para a primeira data incluída um formulário, um objeto de JanelaIframe do nosso
        //#99#//sistema para que sejá mostrado o calendário.

        global $DataJavaScript;

        if (($db_opcao == 3 || $db_opcao == 22 || $db_opcao == 33) && $validaCor == "") {
            $bgcolor = "style='background-color:#DEB887'";
        }

        if ($bgcolor == "") {
            $bgcolor = @$GLOBALS['N' . $nome];
        }


        if (isset($dia) && $dia != "" && isset($mes) && $mes != '' && isset($ano) && $ano != "") {
            $diamesano = $dia . "/" . $mes . "/" . $ano;
            $anomesdia = $ano . "/" . $mes . "/" . $dia;
        }

        $sButtonType = "button";
        ?>

        <input name="<?php echo  ($nomevar == "" ? $nome : $nomevar) . "" ?>" <?php echo  $bgcolor ?> type="<?php echo  $dbtype ?>"
               id="<?php echo  ($nomevar == "" ? $nome : $nomevar) . "" ?>" <?php echo  ($db_opcao == 3 || $db_opcao == 33 || $db_opcao == 22 ? 'readonly' : ($db_opcao == 5 ? 'disabled' : '')) ?>
               value="<?php echo  @$diamesano ?>" size="10" maxlength="10" autocomplete="off" onBlur='js_validaDbData(this);'
               onKeyUp="return js_mascaraData(this,event)" onFocus="js_validaEntrada(this);" onpaste="return false"
               ondrop="return false" <?php echo  $js_script ?>
               labelValidacao="<?php echo  @$GLOBALS['LS' . $nome] ?>" <?php echo  (@$GLOBALS['U' . $nome] == "f" ? "isMandatory='true'" : "") ?>>
        <input name="<?php echo  ($nomevar == "" ? $nome : $nomevar) . "_dia" ?>" type="hidden" title=""
               id="<?php echo  ($nomevar == "" ? $nome : $nomevar) . "_dia" ?>" value="<?php echo  @$dia ?>" size="2" maxlength="2">
        <input name="<?php echo  ($nomevar == "" ? $nome : $nomevar) . "_mes" ?>" type="hidden" title=""
               id="<?php echo  ($nomevar == "" ? $nome : $nomevar) . "_mes" ?>" value="<?php echo  @$mes ?>" size="2" maxlength="2">
        <input name="<?php echo  ($nomevar == "" ? $nome : $nomevar) . "_ano" ?>" type="hidden" title=""
               id="<?php echo  ($nomevar == "" ? $nome : $nomevar) . "_ano" ?>" value="<?php echo  @$ano ?>" size="4" maxlength="4">
        <?php
        if (($db_opcao < 3) || ($db_opcao == 4)) {
            ?>
            <script>
                var PosMouseY, PosMoudeX;

                function js_comparaDatas<?php echo ($nomevar == "" ? $nome : $nomevar) . ""?>(dia, mes, ano) {
                    var objData = document.getElementById('<?php echo ($nomevar == "" ? $nome : $nomevar) . ""?>');
                    objData.value = dia + "/" + mes + '/' + ano;
                    <?php echo $jsRetornoCal?>
                }

            </script>
            <?php
            if (isset($dbtype) && strtolower($dbtype) == strtolower('hidden')) {
                $sButtonType = "hidden";
            }

            ?>

            <input value="D" type="<?php echo  $sButtonType ?>" id="dtjs_<?php echo  ($nomevar == "" ? $nome : $nomevar) ?>"
                   name="dtjs_<?php echo  ($nomevar == "" ? $nome : $nomevar) ?>"
                   onclick="<?php echo  $onclickBT ?>pegaPosMouse(event);show_calendar('<?php echo  ($nomevar == "" ? $nome : $nomevar) ?>','<?php echo  $shutdown_function ?>', event)">
            <?php
        }
    }
}
/*************************************/

//////////////////////////////////////
if (!function_exists("db_data")) {
    function db_data($nome, $dia = "", $mes = "", $ano = "")
    {
        global $DataJavaScript;
        if (!isset($DataJavaScript)) {
            $DataJavaScript = new janela("DataJavaScript", "");
            $DataJavaScript->posX = 1;
            $DataJavaScript->posY = 1;
            $DataJavaScript->largura = 140;
            $DataJavaScript->altura = 210;
            $DataJavaScript->titulo = "Calendário";
            $DataJavaScript->iniciarVisivel = false;
            $DataJavaScript->scrollbar = "no";
            $DataJavaScript->janBotoes = "001";
            $DataJavaScript->mostrar();
        }
        ?>
        <input name="<?php echo  $nome . "_dia" ?>" onFocus="ContrlDigitos=0"
               onKeyUp="js_Passa(this.name,<?php echo  date("j") ?>,<?php echo  (date("n") - 1) ?>,<?php echo  date("Y") ?>)" type="text"
               id="<?php echo  $nome . "_dia" ?>" value="<?php echo  $dia ?>" size="2" maxlength="2" autocomplete="off">
        <strong>/</strong>
        <input name="<?php echo  $nome . "_mes" ?>" onFocus="ContrlDigitos=0"
               onKeyUp="js_Passa(this.name,<?php echo  date("j") ?>,<?php echo  (date("n") - 1) ?>,<?php echo  date("Y") ?>)" type="text"
               id="<?php echo  $nome . "_mes" ?>" value="<?php echo  $mes ?>" size="2" maxlength="2" autocomplete="off">
        <strong>/</strong>
        <input name="<?php echo  $nome . "_ano" ?>" onFocus="ContrlDigitos=0"
               onKeyUp="js_Passa(this.name,<?php echo  date("j") ?>,<?php echo  (date("n") - 1) ?>,<?php echo  date("Y") ?>)" type="text"
               id="<?php echo  $nome . "_ano" ?>" value="<?php echo  $ano ?>" size="4" maxlength="4" autocomplete="off">
        <input value="D" type="button" name="acessadatajavascript"
               onclick="pegaPosMouse(event);show_calendar('form1.<?php echo  $nome ?>')">
        <?php
    }
}
/*************************************/
if (!function_exists("db_label_blur")) {
    function db_label_blur($tab, $label, $campo = "", $campoaux = "")
    {

        $campo = ($campo == "") ? $label : $campo;
        ?>
        <strong>
            <label for="db_<?php echo  $campo ?>">
                <a href="" class="rotulos"
                   onClick="js_lista_blur('dbforms/db_<?php echo  $tab ?>.php',document.form1.db_<?php echo  $campo ?>.value,'<?php echo  $campo ?>',100,50,600,420,document.form1.db_<?php echo  $campoaux ?>.value,'<?php echo  $campoaux ?>');return false">
                    <?php echo  ucwords($label) ?>:
                </a>
            </label>
        </strong>
        <?php
    }
}
if (!function_exists("db_text_blur")) {
    function db_text_blur($tab, $campo, $campoaux, $tamanho, $max, $db_nome = "", $dbh_nome = "")
    {
        ?>
        <input name="db_<?php echo  $campo ?>" id="db_<?php echo  $campo ?>" <?php echo  @$read_only ?> value="<?php echo  $db_nome ?>" type="text"
               size="<?php echo  $tamanho ?>" maxlength="<?php echo  $max ?>"
               onChange="if(this.value!='') js_lista_blur('dbforms/db_<?php echo  $tab ?>.php','db_<?php echo  $campo ?>' + '==' + document.form1.db_<?php echo  $campo ?>.value,'<?php echo  $campo ?>',100,50,600,420,'db_<?php echo  $campoaux ?>' + '==' + document.form1.db_<?php echo  $campoaux ?>.value,'<?php echo  $campoaux ?>','')"
               autocomplete="off">
        <input name="dbh_<?php echo  $campo ?>" type="hidden" value="<?php echo  $dbh_nome ?>">
        <?php
    }
}
if (!function_exists("db_label")) {
    function db_label($tab, $label, $campo = "")
    {
        $campo = ($campo == "") ? $label : $campo;
        ?>
        <strong>
            <label for="db_<?php echo  $campo ?>">
                <a href="" class="rotulos"
                   onClick="js_lista('dbforms/db_<?php echo  $tab ?>.php','db_<?php echo  $campo ?>' + '==' + document.form1.db_<?php echo  $campo ?>.value,'<?php echo  $campo ?>',05,50,780);return false">
                    <?php echo  ucwords($label) ?>:
                </a>
            </label>
        </strong>
        <?php
    }
}
/************************************/
// Parametro $validacao
// 0 Aceita qualquer coisa
// 1 Aceita apenas numeros
// 2 Aceita apenas letras
if (!function_exists("db_text")) {
    function db_text($campo, $tamanho, $max, $db_nome = "", $dbh_nome = "", $validacao = 0)
    {
        ?>
        <input name="db_<?php echo  $campo ?>" onBlur="js_ValidaCamposText(this,<?php echo  $validacao ?>)"
               id="db_<?php echo  $campo ?>" <?php echo  @$readonly ?> value="<?php echo  $db_nome ?>" type="text" size="<?php echo  $tamanho ?>"
               maxlength="<?php echo  $max ?>" autocomplete="off">
        <input name="dbh_<?php echo  $campo ?>" type="hidden" value="<?php echo  $dbh_nome ?>">
        <?php
    }
}

/************************************/
if (!function_exists("db_file")) {
    function db_file($campo, $tamanho, $max, $dbh_nome = "", $db_nome = "")
    {
        ?>
        <input onChange="js_preencheCampo(this.value,this.form.dbh_<?php echo  $campo ?>.name)" name="db_<?php echo  $campo ?>"
               id="db_<?php echo  $campo ?>" value="<?php echo  $db_nome ?>" type="file" size="<?php echo  $tamanho ?>" maxlength="<?php echo  $max ?>"
               autocomplete="off"><br>
        <input name="dbh_<?php echo  $campo ?>" type="text" value="<?php echo  $dbh_nome ?>" size="<?php echo  $tamanho ?>"
               maxlength="<?php echo  $max ?>" autocomplete="off">
        <?php
    }
}
/************************************/
if (!function_exists("db_getfile")) {
    function db_getfile($arq, $text, $funcao = "0")
    {
        db_postmemory($GLOBALS["_FILES"][$arq]);
        $DB_FILES = $GLOBALS["DB_FILES"];
        $tmp_name = $GLOBALS["tmp_name"];
        $name = $GLOBALS["name"];
        $size = $GLOBALS["size"];
        if ($funcao != "0") {
            if ($name != "") {
                system("rm -f $DB_FILES/$funcao");
                copy($tmp_name, "$DB_FILES/$text");
                return $text;
            } elseif ($text != "") {
                if ($text != $funcao) {
                    system("mv $DB_FILES/$funcao $DB_FILES/$text");
                    return $text;
                } else {
                    return $text;
                }
            } elseif ($text == "") {
                system("rm -f $DB_FILES/$funcao");
                return "";
            }
        } elseif ($name != "" && $size == 0) {
            db_erro("O arquivo $name não foi encontrado ou ele está vazio. Verifique o seu caminho e o seu tamanho e tente novamente.");
        } else {
            copy($tmp_name, "$DB_FILES/$text");
            return $text;
        }
    }
}

/////////////////  datas dos relatorios da RLF //////////////
if (!function_exists("bimestre_meses")) {
    function bimestre_meses($bimestre = 1)
    {
        $mes[0] = $bimestre + ($bimestre - 1); // primeiro mes do bimestre
        $mes[1] = $mes[0] + 1; // segundo mes do bimestre
        return $mes;
    }
}
if (!function_exists("ultimo_dia_mes")) {
    function ultimo_dia_mes($mes = 1, $ano = "")
    {
        $res = db_query("select fc_ultimodiames($ano,$mes)"); //fc_ultimodiames(anousu,mes); // retorna ultimo da do mes
        $ultimo_dia = pg_fetch_result($res, 0, 0);
        return $ultimo_dia;
    }
}

if (!function_exists("data_periodo")) {
    function data_periodo($anousu, $tipo = '1B')
    {
        //#10#// retorna datas bimestrais, quadrimestras, semestrais ou anuais
        //#20#// tipo: [1B|2B|3B|4B|5B|6B|1Q|2Q|3Q|1S|2S|A]
        //#20#//
        $mes_ini = '';
        $mes_fin = '';
        $texto = '';
        $abrev = '';

        if ($tipo == '1B') {
            $mes_ini = 1;
            $mes_fin = 2;
            $texto = 'PRIMEIRO BIMESTRE';
            $abrev = 'Bimestre';
        } elseif ($tipo == '2B') {
            $mes_ini = 3;
            $mes_fin = 4;
            $texto = 'SEGUNDO BIMESTRE';
            $abrev = 'Bimestre';
        } elseif ($tipo == '3B') {
            $mes_ini = 5;
            $mes_fin = 6;
            $texto = 'TERCEIRO BIMESTRE';
            $abrev = 'Bimestre';
        } elseif ($tipo == '4B') {
            $mes_ini = 7;
            $mes_fin = 8;
            $texto = 'QUARTO BIMESTRE';
            $abrev = 'Bimestre';
        } elseif ($tipo == '5B') {
            $mes_ini = 9;
            $mes_fin = 10;
            $texto = 'QUINTO BIMESTRE';
            $abrev = 'Bimestre';
        } elseif ($tipo == '6B') {
            $mes_ini = 11;
            $mes_fin = 12;
            $texto = 'SEXTO BIMESTRE';
            $abrev = 'Bimestre';
        } elseif ($tipo == '1Q') {
            $mes_ini = 1;
            $mes_fin = 4;
            $texto = 'PRIMEIRO QUADRIMESTRE';
            $abrev = 'Quadrimestre';
        } elseif ($tipo == '2Q') {
            $mes_ini = 5;
            $mes_fin = 8;
            $texto = 'SEGUNDO QUADRIMESTRE';
            $abrev = 'Quadrimestre';
        } elseif ($tipo == '3Q') {
            $mes_ini = 9;
            $mes_fin = 12;
            $texto = 'TERCEIRO QUADRIMESTRE';
            $abrev = 'Quadrimestre';
        } elseif ($tipo == '1S') {
            $mes_ini = 1;
            $mes_fin = 6;
            $texto = 'PRIMEIRO SEMESTRE';
            $abrev = 'Semestre';
        } elseif ($tipo == '2S') {
            $mes_ini = 7;
            $mes_fin = 12;
            $texto = 'SEGUNDO SEMESTRE';
            $abrev = 'Semestre';
        } elseif ($tipo == 'JAN') {
            $mes_ini = 1;
            $mes_fin = 1;
            $texto = 'JANEIRO';
            $abrev = 'Mês';
        } elseif ($tipo == 'FEV') {
            $mes_ini = 2;
            $mes_fin = 2;
            $texto = 'FEVEREIRO';
            $abrev = 'Mês';
        } elseif ($tipo == 'MAR') {
            $mes_ini = 3;
            $mes_fin = 3;
            $texto = 'MARÇO';
            $abrev = 'Mês';
        } elseif ($tipo == 'ABR') {
            $mes_ini = 4;
            $mes_fin = 4;
            $texto = 'ABRIL';
            $abrev = 'Mês';
        } elseif ($tipo == 'MAI') {
            $mes_ini = 5;
            $mes_fin = 5;
            $texto = 'MAIO';
            $abrev = 'Mês';
        } elseif ($tipo == 'JUN') {
            $mes_ini = 6;
            $mes_fin = 6;
            $texto = 'JUNHO';
            $abrev = 'Mês';
        } elseif ($tipo == 'JUL') {
            $mes_ini = 7;
            $mes_fin = 7;
            $texto = 'JULHO';
            $abrev = 'Mês';
        } elseif ($tipo == 'AGO') {
            $mes_ini = 8;
            $mes_fin = 8;
            $texto = 'AGOSTO';
            $abrev = 'Mês';
        } elseif ($tipo == 'SET') {
            $mes_ini = 9;
            $mes_fin = 9;
            $texto = 'SETEMBRO';
            $abrev = 'Mês';
        } elseif ($tipo == 'OUT') {
            $mes_ini = 10;
            $mes_fin = 10;
            $texto = 'OUTUBRO';
            $abrev = 'Mês';
        } elseif ($tipo == 'NOV') {
            $mes_ini = 11;
            $mes_fin = 11;
            $texto = 'NOVEMBRO';
            $abrev = 'Mês';
        } elseif ($tipo == 'DEZ') {
            $mes_ini = 12;
            $mes_fin = 12;
            $texto = 'DEZEMBRO';
            $abrev = 'Mês';
        } elseif ($tipo == '1T') {
            $mes_ini = 1;
            $mes_fin = 3;
            $texto = 'PRIMEIRO TRIMESTRE';
            $abrev = 'TRIMESTRE';
        } elseif ($tipo == '2T') {
            $mes_ini = 4;
            $mes_fin = 6;
            $texto = 'SEGUNDO TRIMESTRE';
            $abrev = 'TRIMESTRE';
        } elseif ($tipo == '3T') {
            $mes_ini = 7;
            $mes_fin = 9;
            $texto = 'TERCEIRO TRIMESTRE';
            $abrev = 'TRIMESTRE';
        } elseif ($tipo == '4T') {
            $mes_ini = 10;
            $mes_fin = 12;
            $texto = 'QUARTO TRIMESTRE';
            $abrev = 'TRIMESTRE';
        } elseif ($tipo == 'A') {
            $mes_ini = 1;
            $mes_fin = 12;
            $texto = 'ANUAL';
            $abrev = 'ANO';
        } else {
            echo "Datas inválidas";
            exit;
        }

        $data_ini = $anousu . "-" . $mes_ini . "-01";
        $data_fin = $anousu . "-" . $mes_fin . "-" . ultimo_dia_mes($mes_fin, $anousu);
        $matriz[0] = $data_ini;
        $matriz[1] = $data_fin;
        $matriz['texto'] = $texto;
        $matriz['periodo'] = $abrev;
        return $matriz;
    }
}
if (!function_exists("datas_bimestre")) {
    function datas_bimestre($bimestre, $ano)
    {
        if ($ano == "") {
            $ano = db_getsession("DB_anousu");
        }
        $meses = bimestre_meses($bimestre);
        $data_ini = $ano . "-" . $meses[0] . "-01";
        $data_fin = $ano . "-" . $meses[1] . "-" . ultimo_dia_mes($meses[1], $ano);
        $matriz[0] = $data_ini;
        $matriz[1] = $data_fin;
        return $matriz;
    }
}

// essa retorna o quadrimestre, por sorte fevereiro so entra no bimestral
if (!function_exists("datas_quadrimestre")) {
    function datas_quadrimestre($quadrimestre, $ano)
    {
        if ($ano == "") {
            $ano = db_getsession("DB_anousu");
        }
        if ($quadrimestre == 1) {
            $matriz[0] = $ano . "-01-01";
            $matriz[1] = $ano . "-04-" . ultimo_dia_mes(4, $ano);
        } elseif ($quadrimestre == 2) {
            $matriz[0] = $ano . "-05-01";
            $matriz[1] = $ano . "-08-" . ultimo_dia_mes(8, $ano);
        } else { // terceiro quadrimestre
            $matriz[0] = $ano . "-09-01";
            $matriz[1] = $ano . "-12-31";
        }
        return $matriz;
    }
}
/////////////////////////////////////////////////////////////////////////////////

if (!function_exists("assinaturas")) {
    function assinaturas(&$pdf, &$classinatura, $tipo = 'LRF', $lVerificaQuebra = true, $lOutPut = true)
    {
        //#10#// assinaturas dos relatorios da LRF
        //#10#// e de gestão fiscal
        //#20#//
        //#20#// tipo = [LRF,GF,]
        //#20#// pdf = instancia da classe pdf
        //#20#// classinatura = instancia da classe classinatura
        //#20#// LRF = relatorios da LRF execuão orçamentaria
        //#20#// GF  = relatorios da LRF getão fiscal
        //#20#// BG  = relatorios da 4320 Balanço geral
        $controle = "______________________________" . "\n" . "Controle Interno";
        $sec = "______________________________" . "\n" . "Secretaria da Fazenda";
        $cont = "______________________________" . "\n" . "Contadoria";
        $pref = "______________________________" . "\n" . "Prefeito";

        $ass_pref = $classinatura->assinatura(1000, $pref);
        $ass_sec = $classinatura->assinatura(1002, $sec);
        $ass_cont = $classinatura->assinatura(1005, $cont);
        $ass_controle = $classinatura->assinatura(1009, $controle);


        if ($tipo == 'LRF' || $tipo == 'BG') {
            if ($lVerificaQuebra && ($pdf->gety() > ($pdf->getH() - 30))) {
                $pdf->addPage();
                $pdf->Ln(14);
            }
            $largura = ($pdf->getW()) / 3;

            $pos = $pdf->gety();
            $pdf->multicell($largura, 3, $ass_pref, 0, "C", 0, 0);

            // o rpps não tem a assinatura abaixo
            global $db21_idtribunal;
            db_sel_instit(db_getsession("DB_instit"));

            $pdf->setxy($largura * 1, $pos);

            $pdf->multicell($largura, 3, $ass_cont, 0, "C", 0, 0);


            if ($db21_idtribunal == 6 || $db21_idtribunal == 7) {
                //  não tem esse campo 6-RPPS(Autarquia) , 7-RPPS (Exceto Autarquia )
            } else {
                $pdf->setxy($largura * 2, $pos);
                $pdf->multicell($largura, 3, $ass_sec, 0, "C", 0, 0);
            }
        } elseif ($tipo == 'GF') {
            if ($lVerificaQuebra && ($pdf->gety() > ($pdf->getH() - 30))) {
                $pdf->addPage();
                $pdf->Ln(14);
            }
            $largura = (($pdf->getW()) - 15) / 4;

            $pos = $pdf->gety();
            $pdf->multicell($largura, 3, $ass_pref, 0, "C", 0, 0);

            $pdf->setxy($largura * 1, $pos);
            $pdf->multicell($largura, 3, $ass_sec, 0, "C", 0, 0);

            $pdf->setxy($largura * 2, $pos);
            $pdf->multicell($largura, 3, $ass_cont, 0, "C", 0, 0);

            $pdf->setxy($largura * 3, $pos);
            $pdf->multicell($largura, 3, $ass_controle, 0, "C", 0, 0);
        }

        if ($lOutPut) {
            $pdf->Output();
        }
    }
}

// Retorna periodo conforme mes
if (!function_exists("db_retorna_periodo")) {
    function db_retorna_periodo($mes = 1, $tipo = "B")
    {
        $periodo = "0";

        if ($tipo == "B") {  // retorna bimestre do mes
            switch ($mes) {
                case 1:
                case 2:
                    $periodo = "1B";
                    break;
                case 3:
                case 4:
                    $periodo = "2B";
                    break;
                case 5:
                case 6:
                    $periodo = "3B";
                    break;
                case 7:
                case 8:
                    $periodo = "4B";
                    break;
                case 9:
                case 10:
                    $periodo = "5B";
                    break;
                case 11:
                case 12:
                    $periodo = "6B";
                    break;
            }
        }

        if ($tipo == "Q") {  // retorna quadrimestre do mes
            switch ($mes) {
                case 1:
                case 2:
                case 3:
                case 4:
                    $periodo = "1Q";
                    break;
                case 5:
                case 6:
                case 7:
                case 8:
                    $periodo = "2Q";
                    break;
                case 9:
                case 10:
                case 11:
                case 12:
                    $periodo = "3Q";
                    break;
            }
        }

        if ($tipo == "S") {  // retorna semestre do mes
            switch ($mes) {
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                    $periodo = "1S";
                    break;
                case 7:
                case 8:
                case 9:
                case 10:
                case 11:
                case 12:
                    $periodo = "2S";
                    break;
            }
        }

        return $periodo;
    }
}
