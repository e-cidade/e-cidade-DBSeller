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

include(modification("libs/db_stdlibwebseller.php"));
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_matricula_classe.php"));
include(modification("dbforms/db_funcoes.php"));
$iEscola = db_getsession("DB_coddepto");
$iModulo = db_getsession('DB_modulo');
$clmatricula = new cl_matricula;
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
        select {
            width: 350px !important;
        }
    </style>
</head>
<body class="body-default">
<input type="hidden" name="session" id="session">
<?php MsgAviso(db_getsession("DB_coddepto"), "escola"); ?>
<div class="container">
    <fieldset>
        <legend><b>Relatório de Horário das Turmas</b></legend>
        <form name="form1" method="post" action="">
            <table class="form-container">
                <tr id="ctnEscola" style="display: none">
                    <td>
                        <b>Escola: </b>
                    </td>
                    <td>
                        <select id="selectEscola">
                            <option value="">Selecione a escola</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Calendário:</b>
                    </td>
                    <td>
                        <select id='selectCalendario' name="grupo">
                            <option value="">Selecione o calendário</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Turma:</b>
                    </td>
                    <td>
                        <select id='selectTurma' name="subgrupo">
                            <option value="">Selecione a turma</option>
                        </select>
                    </td>
                </tr>
                <tr  id='ctnProfessor' style="display: none;">
                    <td>
                        <label for="professor"> Professor (Opcional):</label>
                    </td>
                    <td>
                        <select name="professor" id='professor'>
                            <option value="">Selecione o professor</option>
                        </select>
                    </td>
                </tr>
            </table>
            <input id='processar' type="button" value="Processar" style="margin-top: 15px" disabled>
        </form>
    </fieldset>
</div>
<?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit")); ?>
</body>
</html>
<script>
    const cboEscola = document.getElementById('selectEscola'),
        ctnEscola = document.getElementById('ctnEscola'),
        cboCalendario = document.getElementById('selectCalendario'),
        cboTurma = document.getElementById('selectTurma'),
        cboProfessor = document.getElementById('professor'),
        ctnProfessor = document.getElementById('ctnProfessor'),
        btnProcessar = document.getElementById('processar'),
        inputSession = document.getElementById('session');
        codigoDepartamento = <?php echo $iEscola; ?>;

    var turmas = []
    var turmasSemRegente = []

    /* Carrega lista de escolas após carregar a página */
    window.onload = () => {
        /* Implementei essa verificação, pois, o método js_buscaEscolas, mudou no arquivo edu_educacaobase.RPC.php */
        VerificaSeModuloSecretaria();
        if (!VerificaSeModuloSecretaria()) {
            js_buscaCalendarios();
        } else {
            limpaEscola();
            js_buscaEscolas();
        }
    }

    cboEscola.addEventListener('change', () => {
        if (cboEscola.value == '') {
            limpaEscola();
            return;
        }
        js_buscaCalendarios();
    });

    cboCalendario.addEventListener('change', () => {
        if (cboCalendario.value == '') {
            limpaCalendario();
            return;
        }

        js_buscaTurmas();
    });

    cboTurma.addEventListener('change', () => {
        if (cboTurma.value == '') {
            cboProfessor.options.length = 0;
            cboProfessor.add(new Option('Selecione o professor', ''));
            ctnProfessor.style.display = 'none';
            return;
        }

        if (cboTurma.value == 'TODAS') {
            cboProfessor.options.length = 0;
            cboProfessor.add(new Option('Selecione o professor', ''));
            ctnProfessor.style.display = '';
            cboProfessor.add(new Option('TODOS', ''));
            btnProcessar.removeAttribute('disabled');
            js_removeObj('msgBox');
            return;
        }

        if (cboTurma.value == 'semRegente') {
            cboProfessor.options.length = 0;
            cboProfessor.add(new Option('Selecione o professor', ''));
            ctnProfessor.style.display = 'none';
            btnProcessar.removeAttribute('disabled');
            js_removeObj('msgBox');
            return;
        }

        js_buscaDocentes();
    });

    btnProcessar.addEventListener('click', js_imprimir);

    /**
     * Busca as escolas da rede ou a escola logada se modulo = escola
     */
    function js_buscaEscolas() {
        let oParametro = {};
        oParametro.exec = 'pesquisaEscola';

        js_divCarregando("Aguarde, Buscando Escolas.", "msgBox");

        new Ajax.Request(
            'edu_educacaobase.RPC.php',
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                onComplete: js_retornoEscolas
            }
        );
    }

    /**
     * Popula o select de Escolas com a resposta do js_listaEscoals()
     * @param oResponse
     */
    function js_retornoEscolas(oResponse) {
        let oRetorno = JSON.parse(oResponse.responseText);
        let iEscola = null;

        oRetorno.dados.each((oEscola) => {
            cboEscola.add(new Option(`${oEscola.codigo_escola} - ${oEscola.nome_escola.urlDecode()}`, oEscola.codigo_escola));
            iEscola = oEscola.codigo_escola;
        });

        if (oRetorno.dados.length == 1) {
            cboEscola.value = iEscola;
            cboEscola.dispatchEvent(new Event('change'))
        }

        js_removeObj('msgBox');
    }

    /* Busca Calendários */
    function js_buscaCalendarios() {
        limpaCalendario();

        let oParametro = {};
        oParametro.exec = 'pesquisaCalendario';
        oParametro.escola = VerificaSeModuloSecretaria() ? cboEscola.value : codigoDepartamento;

        js_divCarregando("Aguarde, Buscando Calendários.", "msgBox");

        new Ajax.Request(
            'edu_educacaobase.RPC.php',
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                onComplete: js_retornoCalendarios
            }
        );
    }

    function js_retornoCalendarios(oResponse) {
        let oRetorno = JSON.parse(oResponse.responseText);
        let iCalendario = null;

        cboCalendario.options.length = 0;
        cboCalendario.add(new Option('Selecione o calendário', ''));

        oRetorno.dados.each((oCalendario) => {
            cboCalendario.add(new Option(
                `${oCalendario.ed52_i_codigo} - ${oCalendario.ed52_c_descr.urlDecode()}`,
                oCalendario.ed52_i_codigo
            ));

            iCalendario = oCalendario.ed52_i_codigo;
        });

        if (oRetorno.dados.length == 1) {
            cboCalendario.value = iCalendario;
        }

        js_removeObj('msgBox');
    }

    function js_buscaTurmas() {
        limpaTurma();

        let oParametro = {};
        oParametro.exec = "pesquisaTurmaTipoGradeHorario";
        oParametro.escola =  VerificaSeModuloSecretaria() ? cboEscola.value : codigoDepartamento;
        oParametro.iCalendario = cboCalendario.value;
        oParametro.tipoVinculo = 2;

        js_divCarregando("Aguarde, Buscando Turmas.", "msgBox");

        new Ajax.Request(
            'edu_educacaobase.RPC.php',
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                onComplete: js_retornoTurmas
            }
        );
    }

    function js_retornoTurmas(oResponse) {
        let oRetorno = JSON.parse(oResponse.responseText);

        cboTurma.options.length = 0;
        cboTurma.add(new Option('Selecione a turma', ''));
        if (oRetorno.dados.length > 0) {
            cboTurma.add(new Option('TODAS', 'TODAS'));
            cboTurma.add(new Option('TODAS TURMAS SEM REGENTE', 'semRegente'));
            turmasSemRegente = oRetorno.turmasSemRegente;
            oRetorno.dados.each(function (oTurma) {
                turmas.push(oTurma.ed220_i_codigo)
                cboTurma.add(new Option(`${oTurma.ed57_c_descr.urlDecode()} - ${oTurma.ed11_c_descr.urlDecode()}`, oTurma.ed220_i_codigo));
            });
        } else {
            cboTurma.add(new Option('Sem Turmas', ''));
        }
        js_removeObj('msgBox');
    }

    function js_buscaDocentes() {
        ctnProfessor.style.display = '';
        cboProfessor.options.length = 0;
        let oParametro = {};
        oParametro.exec = 'buscaProfessoresTurma';
        oParametro.escola = VerificaSeModuloSecretaria() ? cboEscola.value : codigoDepartamento;
        oParametro.iTurmaSerieRegimeMat = cboTurma.value == 'TODAS' ? turmas : cboTurma.value;
        js_divCarregando("Aguarde, Buscando Professores!.", "msgBox");

        new Ajax.Request(
            'edu_educacaobase.RPC.php',
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                asynchronous: true,
                onComplete: js_retornoProfessor
            }
        );
    }

    function js_retornoProfessor(oResponse) {
        let oRetorno = JSON.parse(oResponse.responseText);

        if (oRetorno.dados.length > 0) {
            cboProfessor.add(new Option('TODOS', ''));

            oRetorno.dados.each(function (oProfessor) {
                cboProfessor.add(new Option(
                    `${oProfessor.z01_numcgm} - ${oProfessor.z01_nome.urlDecode()}`,
                    oProfessor.ed20_i_codigo
                ));
            });

            btnProcessar.removeAttribute('disabled');
        } else {
            cboProfessor.add(new Option('NENHUM PROFESSOR CADASTRADO PARA ESTA TURMA.', ''));
            btnProcessar.removeAttribute('disabled');
        }
        js_removeObj('msgBox');
    }

    function js_imprimir() {
        if (cboTurma.value == 'TODAS') {
            turma = turmas;
        } else if (cboTurma.value == 'semRegente') {
            turma = turmasSemRegente;
        } else {
            turma = cboTurma.value;
        }
        const codEscola = VerificaSeModuloSecretaria() ? cboEscola.value : codigoDepartamento;

        let sUrl = `edu2_horarioturma002.php?escola=${codEscola}&professor=${cboProfessor.value}&turma=${turma}`

        let jan = window.open(
            sUrl,
            '',
            `width = ${screen.availWidth - 5}, height = ${screen.availHeight - 40}, scrollbars = 1,location = 0`);

        jan.moveTo(0, 0);
    }

    function limpaEscola() {
        cboCalendario.options.length = 0;
        cboCalendario.add(new Option('', ''));

        cboTurma.options.length = 0;
        cboTurma.add(new Option('', ''));

        cboProfessor.options.length = 0;
        ctnProfessor.style.display = 'none';

        btnProcessar.setAttribute('disabled', 'disabled');
    }

    function limpaCalendario() {
        cboTurma.options.length = 0;
        cboTurma.add(new Option('', ''));

        cboProfessor.options.length = 0;
        ctnProfessor.style.display = 'none';

        btnProcessar.setAttribute('disabled', 'disabled');
    }

    function limpaTurma() {
        cboTurma.options.length = 0;
        cboTurma.add(new Option('Selecione a turma', ''));

        cboProfessor.options.length = 0;
        ctnProfessor.style.display = 'none';

        btnProcessar.setAttribute('disabled', 'disabled');
    }

    function VerificaSeModuloSecretaria() {
        inputSession.innerHTML = <?= $iModulo ?>;
        iModulo = inputSession.innerHTML;

        if (iModulo === '7159') {
            ctnEscola.style.display = '';
            return true;
        }
        return false;
    }
</script>
