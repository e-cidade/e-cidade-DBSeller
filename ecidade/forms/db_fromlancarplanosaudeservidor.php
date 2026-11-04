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
<form name="form-servidor" class="container" id="formServidorOperadoraSaude">
    <input type="hidden" name="sequencial" id="sequencial">
    <fieldset>
        <legend>Plano de Saúde do Servidor</legend>
        <table class="form-container">
            <tr>
                <td>
                    <label for="mes">Competência:</label>
                </td>
                <td>
                    <input type="text" name="mes" id="mes" class="readonly field-size1" disabled>
                    <input type="text" name="ano" id="ano" title="Ano" class="readonly field-size1" disabled>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="codigoServidor"><a id="ancoraServidor" href="#">Servidor:</a></label>
                </td>
                <td>
                    <input type="text" id="codigoServidor" name="servidor" lang="rh01_regist"
                           class="field-size2" data-order="1" data-identificador="servidor"/>
                    <input type="text" id="nomeServidor" name="nomeServidor" lang="z01_nome" title="Nome"
                           class="field-size8" disabled data-order="1"/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="codigoOperadora"><a id="ancoraOperadora" href="#">Operadora:</a></label>
                </td>
                <td>
                    <input type="text" id="codigoOperadora" name="operadora" lang="rh221_sequencial"
                           class="field-size2"/>
                    <input type="text" id="nomeOperadora" name="nomeOperadora" lang="z01_nome" class="field-size8"
                           title="Operadora" disabled/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="codigoRubrica"><a id="ancoraRubrica" href="#">Rubrica:</a></label>
                </td>
                <td>
                    <input type="text" id="codigoRubrica" name="rubrica" lang="rh27_rubric" class="field-size2"/>
                    <input type="text" id="descricaoRubrica" name="descricaoRubrica" lang="rh27_descr"
                           class="field-size8" title="Rubrica" disabled/>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="valorServidor">Valor:</label>
                </td>
                <td>
                    <input type="text" id="valorServidor" name="valor" class="field-size2"/>
                </td>
            </tr>
        </table>
    </fieldset>
    <input type="button" id="salvarServidorOperadoraSaude" value="Salvar">
    <input type="reset" id="limparServidorOperadoraSaude" value="Novo">
</form>
<fieldset class="container" style="width: 1000px;">
    <legend>Planos de Saúde do Servidor</legend>
    <div id="divDataGridCollectionServidorOperadoraSaude"></div>
</fieldset>
