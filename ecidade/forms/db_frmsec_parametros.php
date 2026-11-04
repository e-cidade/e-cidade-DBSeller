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
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_utils.php");
//MODULO: secretariadeeducacao
$clsec_parametros->rotulo->label();
$lMensagem = '';
$oGet      = db_utils::postMemory($_GET);
?>

<div class="container">
    <form name="form1" method="post" action="sec1_sec_parametros001.php?lMensagem=true" class="subcontainer">
        <fieldset>
            <legend>Parâmetros</legend>
            <table class="form-container">
                <tr>
                    <td>
                        <?php
                            db_input('ed290_sequencial', 10, $Ied290_sequencial, true, 'hidden', 3, "")
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="<?=$Ted290_importcenso?>">
                        <?=$Led290_importcenso?>
                    </td>
                    <td>
                        <?php
                        $aOpcoesCenso = array(
                            '1' => 'Importar todos registros do arquivo',
                            '2' => 'Importar apenas registros ativos na escola'
                        );
                        db_select('ed290_importcenso', $aOpcoesCenso, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="<?=$Ted290_controleprogressaoparcial?>">
                        <?=$Led290_controleprogressaoparcial?>
                    </td>
                    <td>
                        <?php
                        db_select('ed290_controleprogressaoparcial', getValoresPadroesCampo('ed290_controleprogressaoparcial'), true, $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="<?=$Led290_diasmanutencaohistorico;?>" class="bold">
                        <?=$Led290_diasmanutencaohistorico;?>
                    </td>
                    <td>
                        <?php
                            db_input('ed290_diasmanutencaohistorico', 3, $Ied290_diasmanutencaohistorico, true, 'text', $db_opcao, "", "", "", "", 3);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="<?=$Ted290_bncc?>"><?=$Led290_bncc;?></td>
                    <td>
                        <?php
                        $aOpcoesCenso = array(
                            '1' => 'BNCC Padrão',
                            '2' => 'BNCC Editada',
                            '3' => 'Referencial Curricular Estadual'
                        );
                        db_select('ed290_bncc', $aOpcoesCenso, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="<?=$Ted290_habilitaconsultaalunoporescola?>"><?=$Led290_habilitaconsultaalunoporescola?></td>
                    <td>
                        <?php
                        $aOpcoesConsultaAlunoPorEscola = ['f' => 'NO', 't' => 'SIM'];
                        db_select('ed290_habilitaconsultaalunoporescola',$aOpcoesConsultaAlunoPorEscola,true,$db_opcao,"");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="<?=$Ted290_habilconsmovalunescola?>"><?=$Led290_habilconsmovalunescola?></td>
                    <td>
                        <?php
                        $aOpcoesMovimentacaoAlunoEscola = ['f' => 'NO', 't' => 'SIM'];
                        db_select('ed290_habilconsmovalunescola',$aOpcoesMovimentacaoAlunoEscola,true,$db_opcao,"");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="">Permite Alteração Procedimento Avaliação</td>
                    <td>
                        <?php
                        $opcoes = ['f' => 'NÃO', 't' => 'SIM'];
                        db_select('ed290_permite_alteracao_proc_avaliacao',$opcoes,true,$db_opcao,"");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="<?=$Ted290_cordisciplinaquadro?>">
                        <?=$Led290_cordisciplinaquadro?>
                    </td>
                    <td>
                        <?php
                        $aOpcoesCor = array(
                            '0' => 'Descrição Disciplina/Nome Professor',
                            '1' => 'Quadro de Horários na Consulta'
                        );
                        db_select('ed290_cordisciplinaquadro', $aOpcoesCor, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="">Habilitar Campos TransEscolar:</td>
                    <td>
                        <?php
                        $opcoesTransEscolar = ['f' => 'NÃO', 't' => 'SIM'];
                        db_select('ed290_habilitarcampostransescolar',$opcoesTransEscolar,true,$db_opcao);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="">Habilitar Campos Matrícula Inicial:</td>
                    <td>
                        <?php
                        $opcoesMatInicial = ['f' => 'NÃO', 't' => 'SIM'];
                        db_select('ed290_habilitarcamposmatriculainicial',$opcoesMatInicial,true,$db_opcao);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td title="">Correção de Fluxo:</td>
                    <td>
                        <?php
                        $aOpcoesCenso = array(
                            '1' => 'Manual',
                            '2' => 'Automático',
                        );
                        db_select('ed290_correcao_de_fluxo_manual', $aOpcoesCenso, true, $db_opcao, "");
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="<?=($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir"))?>"
               type="submit"
               id="db_opcao"
               value="<?=($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir"))?>"
            <?=(!$db_botao?"disabled":"")?>
        />
    </form>
</div>
<script>
var sUrlRPC = 'edu4_parametrodependencia.RPC.php';
var oUrl    = js_urlToObject();

/**
 * Verificamos se alguma escola ja possui progressao parcial configurada, com aluno 'matriculado' nessa progressao
 */
function js_verificaAlunoEmProgressao() {
    var oParametro  = new Object();
    oParametro.exec = 'verificaAlunoEmProgressao';

    var oAjax = new Ajax.Request(
        sUrlRPC,
        {
            method:     'post',
            parameters: 'json='+Object.toJSON(oParametro),
            onComplete: js_retornoVerificaAlunoEmProgressao
        }
    );
}

/**
 * Retorno da validacao da progressao parcial
 * js_verificaAlunoEmProgressao()
 */
function js_retornoVerificaAlunoEmProgressao(oResponse) {
    var oRetorno = JSON.parse(oResponse.responseText);
    if (oRetorno.lTemEscolaProgressaoParcial) {
        $('ed290_controleprogressaoparcial').disabled = true;
        /**
         * * Validamos se existe a propriedade lMensagem, para que a mensagem seja apresentada apenas 1 vez
         * */
        if (!oUrl.hasOwnProperty('lMensagem')) {
            alert(oRetorno.message.urlDecode());
        }
        return false;
    }
}

js_verificaAlunoEmProgressao();
</script>
