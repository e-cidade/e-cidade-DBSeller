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
?>
<form name="form-servidor" class="container" id="formDependente">
    <input type="hidden" name="sequencial" id="sequencial">
    <fieldset>
        <legend>Dependente</legend>
        <table class="form-container">
            <tr>
                <td>
                    <label for="plano">Plano de Saúde:</label>
                </td>
                <td>
                    <input type="text" name="plano" id="plano" disabled class="readonly field-size-max">
                </td>
            </tr>
            <tr title="Selecione o dependente">
                <td>
                    <label for="codigoDependente"><a id="aDependente" href="#">Dependente:</a></label>
                </td>
                <td>
                    <input type="text" id="codigoDependente" name="codigoDependente" class="field-size2"
                           lang="rh31_codigo" data-order="2"/>
                    <input type="text" id="nomeDependente" name="nomeDependente" class="field-size8" lang="rh31_nome"
                           data-order="2"/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="tipoDependente">Tipo:</label>
                </td>
                <td>
                    <select id="tipoDependente" name="tipoDependente" style="max-width: 425px;">
                        <option value="">Selecione</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="codigoRubricaDependente"><a id="ancoraRubricaDependente" href="#">Rubrica:</a></label>
                </td>
                <td>
                    <input type="text" id="codigoRubricaDependente" name="rubricaDependente" lang="rh27_rubric" class="field-size2"/>
                    <input type="text" id="descricaoRubricaDependente" name="descricaoRubricaDependente" lang="rh27_descr"
                           class="field-size8" title="Rubrica" disabled/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="valorDependente">Valor:</label>
                </td>
                <td>
                    <input type="text" id="valorDependente" name="valorDependente"/>
                </td>
            </tr>
        </table>
    </fieldset>
    <input type="button" id="salvarDependente" value="Salvar">
    <input type="reset" id="limparDependente" value="Novo">
</form>
<fieldset class="container" style="width: 1000px;">
    <legend>Dependentes do Servidor</legend>
    <div id="divDataGridCollectionServidorOperadoraSaudeDependente"></div>
</fieldset>

