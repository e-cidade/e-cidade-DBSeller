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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

$iModulo = db_getsession('DB_modulo');
$iEscola = db_getsession('DB_coddepto');

// 1100747 - escola
// 7159 - secretaria

$oGet = db_utils::postMemory($_GET);
$iCodigo = isset($oGet->codigo) ? $oGet->codigo : null;

?>
<!doctype html>
<html lang="pt-BR">
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link rel="stylesheet" type="text/css" 
        href="./extension/package/Desktop/assets/vendors/select2/css/select2.min.css" />
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>

    <!-- page css -->
    <link type="text/css" href="estilos/new-bootstrap4.css?v=0.1" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap-utilities.min.css"
        integrity="sha512-DEGBrwaCF4lkKzMKNwt8Qe/V54bmJctk7I1HyfINGAIugDvsdBeuWzAWZmXAmm49P6EBfl/OeUM01U3r7cW4jg=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/attla/bs-addons/addons.bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap-grid.rtl.min.css"
        integrity="sha512-C9DqRFOOTJ5aY4nPjM2zWBP1leudW4G1Sdb5fjRqdP/Vvfyx0bgittDhurMhu/LlXw3oC/QRlPz47qQPTYl+FA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <style>
        /* page css */
        input[name="datainicio"],input[name="datafim"]{
            min-width:23%!important
        }
    </style>
</head>
<body>
<div class="container">
    <ol class="breadcrumb mt-n3">
        <li class="breadcrumb-item">Profissões</li>
        <li class="breadcrumb-item active" aria-current="page"><?=($iCodigo ? 'Alteração' : 'Inclusão')?></li>
    </ol>

    <div id="alert-msg" class="alert d-none my-4" role="alert"></div>

    <fieldset>
        <legend>Parâmetros</legend>

        <table class="form-container form-search bold mt-4">
            <?php if ($iCodigo): ?>
            <tr>
                <td class="py-2" nowrap="nowrap" style="width: 15px">
                    <label class="float-right ms-4 me-3 fs-135" for="titulo">Código:</label>
                </td>
                <td class="py-2 fs-135" nowrap="nowrap" colspan="3"><b id="codigo">Carregando..</b></td>
            </tr>
            <?php endif; ?>

            <tr>
                <td class="py-2" nowrap="nowrap" style="width: 15px">
                    <label class="float-right ms-4 me-3 fs-135" for="titulo">Título: </label>
                </td>
                <td class="py-2" nowrap="nowrap" colspan="3">
                    <input
                        class="form-control fs-135"
                        rel="ignore-css"
                        type="text"
                        placeholder="Ex: Autônomo"
                        autocomplete="off"
                        style="width:70%"
                        maxlength=250
                        onpaste="return false"
                        ondrop="return false"
                        id="titulo"
                        value="<?=($iCodigo ? 'Carregando..' : '')?>"
                    />
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="field-size5">
                    <button id="btn-buscar" class="btn btn-light float-left mt-2 fs-5" <?=($iCodigo ? 'disable' : '')?>>
                        <i class="fa fa-check-circle me-1"></i>
                        <?=($iCodigo ? 'Editar' : 'Cadastrar')?>
                    </button>
                </td>
            </tr>
        </table>
    </fieldset>
</div>

<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/select2.min.js"></script>
<script src="./extension/package/Desktop/assets/vendors/select2/js/i18n/pt-BR.js"></script>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">

const alertMsg = document.getElementById('alert-msg'),
    showMessage = (msg, type = 'primary') => {
        alertMsg.className = `my-4 text-start alert alert-${type}`;
        alertMsg.innerHTML = msg + `<button onclick="hideMessage()"type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>`;
    },
    hideMessage = () => {
        alertMsg.className = `alert d-none`;
        alertMsg.innerText = '';
    },
    showElem = (el) => {
        if (typeof el === 'string') {
            el = document.getElementById(el)
        }

        el && el.removeAttribute('hidden')
    },
    hideElem = (el) => {
        if (typeof el === 'string') {
            el = document.getElementById(el)
        }

        el && el.setAttribute('hidden', !0)
    },
    toggleElem = (el) => {
        if (typeof el === 'string') {
            el = document.getElementById(el)
        }

        el && el.toggleAttribute('hidden')
    },
    objectToFormData = function (obj) {
        const formData = new FormData();

        Object.entries(obj)
            .forEach(([key, value]) => {
                formData.append(key, value);
            });

        return formData;
    },
    clear = function(...args) {
        args.forEach(elem => {
            if (typeof elem === 'string') {
                elem = document.getElementById(elem)
            }

            if (elem) {
                elem.value = ''
            }
        })
    };

    Object.filter = (obj, predicate) =>
    Object.keys(obj)
          .filter( key => predicate(obj[key]) )
          .reduce( (res, key) => (res[key] = obj[key], res), {} );

    $.noConflict();
    jQuery(document).ready(function ($) {
        let urlApi = '';
        PHPSession.loadData().then(() => {
            urlApi = PHPSession.requestApi;
            searchItem();
        });

        const codigo = document.getElementById('codigo'),
            titulo = document.getElementById('titulo'),
            btnBuscar = document.getElementById('btn-buscar'),
            ocodigo = new URLSearchParams(window.location.search).get('codigo'),
            validate = () => {
                if (!titulo.value) {
                    alert('Você deve preencher o título.');
                    titulo.focus();
                    return false;
                }

                return true;
            },
            buscar = (event) => {
                if (!validate()) {
                    event?.preventDefault();
                    return;
                }

                HttpClient.post(
                    `${urlApi}/educacao/escola/ocupacoes/ocupacao${ocodigo ? '/'+ ocodigo: ''}`,
                    {body: objectToFormData({titulo: titulo.value})}
                ).then((response) => {
                    if (response.error) {
                        hideMessage();
                        alert(response.message);
                        return;
                    }

                    !ocodigo && clear('titulo');
                    showMessage(`Profissão ${ocodigo ? 'editada': 'criada'} com sucesso.`);
                });
            },
            searchItem = () => {
                if (ocodigo) {
                    HttpClient.get(`${urlApi}/educacao/escola/ocupacoes/${ocodigo}`)
                        .then((response) => {
                            if (response.error) {
                                alert(response.message);
                                return;
                            }

                            response.data?.mo304_codigo && $('#codigo').html(response.data?.mo304_codigo);
                            response.data?.mo304_titulo && $('#titulo').val(response.data?.mo304_titulo);

                            btnBuscar.removeAttribute('disable');
                        });
                }
            };

        btnBuscar.addEventListener('click', buscar);
    });
</script>
</body>
</html>
