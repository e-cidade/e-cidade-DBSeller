<?php
/**
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
<form name="form1" method="post" action="">
    <fieldset style="width: 900px">
        <legend>Situação de Eventos</legend>
        <table class="form-container">
            <tr style="display: none" id="trEmpregador">
                <td>
                    <label for="empregador">Responsável:</label>
                </td>
                <td>
                    <select id="empregador" name="empregador" class="field-size-max"></select>
                </td>
            </tr>
            <tr style="display: none" id="trContribuinte">
                <td>
                    <label for="descricao">Contribuinte:</label>
                </td>
                <td>
                    <select name="contribuinte" id="contribuinte"></select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="evento">Evento:</label>
                </td>
                <td>
                    <select id="evento">
                        <option value="">Todos</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="statusErro">Filtrar Envios com Erros de Layout</label></td>
                <td>
                    <select name="statusErro" id="statusErro">
                        <option value="false">Não</option>
                        <option value="true">Sim</option>
                    </select>
                </td>
            </tr>
            <tr>
            </tr>
            <tr>
                <td><label for="statusRecibo">Filtrar Envios com Sucesso/Recibo</label></td>
                <td>
                    <select name="statusRecibo" id="statusRecibo">
                        <option value="false">Não</option>
                        <option value="true">Sim</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="statusOcorrencia">Filtrar Envios com Erros/Ocorrências Sem Recibo</label></td>
                <td>
                    <select name="statusOcorrencia" id="statusOcorrencia">
                        <option value="false">Não</option>
                        <option value="true">Sim</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="statusAdvertencia">Filtrar Envios com Advertências/Recibo</label></td>
                <td>
                    <select name="statusAdvertencia" id="statusAdvertencia">
                        <option value="false">Não</option>
                        <option value="true">Sim</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="dataInicio">Data Envio:</label>
                </td>
                <td>
                    <?php
                    $anoAtual = date("Y", db_getsession("DB_datausu"));
                    $diaAtual = date("d", db_getsession("DB_datausu"));
                    $mesAtual = date("m", db_getsession("DB_datausu"));

                    db_inputdata('dataInicio', $diaAtual, $mesAtual, $anoAtual, true, 'text', 1, "");
                    echo "à";
                    db_inputdata('dataFim', $diaAtual, $mesAtual, $anoAtual, true, 'text', 1, "");
                    ?>
                </td>
            </tr>
        </table>
        <br>
        <div id="msgDataCertificado" >
        </div>
    </fieldset>
    <button type="button" id="consultar"><i class="fas fa-search"></i> Consultar</button>
    <button type="button" onclick="imprimeRelatorio('pdf')" id="imprimir"><i class="fas fa-print"></i> Imprimir</button>
    <button type="button" onclick="imprimeRelatorio('csv')" id="exportarcsv"><i class="fas fa-file-csv"></i> Exportar CSV</button>
    <div id="containerEventos">
        <fieldset>
            <legend>Eventos</legend>
            <div id="gridEventos"></div>
                <div id="container">
                    <table id="data-table"/>
                </div>
            </div>
        </fieldset>
    </div>
</form>

<script>
    const selectErro = document.querySelector("select[name=statusErro]");
    const selectRecibo = document.querySelector("select[name=statusRecibo]");
    const selectOcorrencia = document.querySelector("select[name=statusOcorrencia]");
    const selectAdvertencia = document.querySelector("select[name=statusAdvertencia]");
    const selectEmpregador = document.getElementById('empregador');
    const selectContribuinte = document.getElementById('contribuinte');

    const EFD_REINF = '1';
    const ESOCIAL = '2';
    const urlParams = new URLSearchParams(window.location.search);
    const integracao = urlParams.has('integracao') ? urlParams.get('integracao') : '2';
    const formData = new FormData();

    formData.append('acao', 'inicializar');
    formData.append('integracao', integracao);

    HttpClient.post('sped02_preenchimento.RPC.php', {
        body: formData
    }).then(response => {
        if (response.erro) {
            throw response.mensagem;
        }

        if (integracao === EFD_REINF) {
            const contribuintes = response.contribuinte;
            contribuintes.forEach(i => {
                let option = new Option(i.descricao, i.cgm)
                selectContribuinte.appendChild(option);
            })
            document.getElementById('trContribuinte').style.display = '';
        }

        if (integracao === ESOCIAL) {
            response.empregadores.map(empregadorOption => {
                selectEmpregador.add(new Option(empregadorOption.nome, empregadorOption.cgm));
            });
            document.getElementById('trEmpregador').style.display = '';
        }
        buscarCertificado();

    }).catch(mensagem => alert(mensagem));

    selectRecibo.addEventListener("change", function () {
        selectOcorrencia.value = false;
        selectAdvertencia.value = false;
        selectErro.value = false;
    });
    selectOcorrencia.addEventListener("change", function () {
        selectRecibo.value = false;
        selectAdvertencia.value = false;
        selectErro.value = false;
    });
    selectAdvertencia.addEventListener("change", function () {
        selectOcorrencia.value = false;
        selectRecibo.value = false;
        selectErro.value = false;
    });
    selectErro.addEventListener("change", function () {
        selectOcorrencia.value = false;
        selectRecibo.value = false;
        selectAdvertencia.value = false;
    });
    selectEmpregador.addEventListener("change", function () {
        buscarCertificado();
    })


    function buscarArquivos() {

        const parametros = {
            'exec': 'getTipos',
            'integracao': integracao
        };

        new AjaxRequest('eso4_esocialapi.RPC.php', parametros, function (retorno) {
            let boxAlerta = document.getElementById("msgDataCertificado");
            if (retorno.erro) {
                boxAlerta.innerHTML = retorno.msgCertificado;
                boxAlerta.className = "alert alert-danger";
                return false;
            }

            retorno.tipos.each(function (evento) {
                $('evento').add(new Option(evento.titulo, evento.layout));
            });
        }).setMessage('Buscando Arquivos.').execute();
    }

    function buscarCertificado() {
        var cgmEmpregador = selectEmpregador.value;
        if (integracao === EFD_REINF){
            cgmEmpregador = selectContribuinte.value;
        }
        const parametros = {
            'exec': 'getCertificado',
            'cgmEmpregador': cgmEmpregador,
            'integracao' : integracao
        };

        new AjaxRequest('eso4_esocialapi.RPC.php', parametros, function (retorno) {
            let boxAlerta = document.getElementById("msgDataCertificado");
            if (retorno.erro) {
                boxAlerta.innerHTML = retorno.msgCertificado;
                boxAlerta.className = "alert alert-danger";
                if (retorno.msgCertificado == '404 Not Found') {
                    boxAlerta.className = "alert alert-danger";
                    boxAlerta.innerHTML = '<strong>Atenção!</strong> A API não esta configurada corretamente. Favor entrar em contato com o suporte.';
                }
                return false;
            }
            if (retorno.tipoAlerta === "Verde") {
                boxAlerta.className = "alert alert-success";
                boxAlerta.innerHTML = retorno.msgCertificado;
            }
            if (retorno.tipoAlerta === "Laranja") {
                boxAlerta.className = "alert alert-warning";
                boxAlerta.innerHTML = retorno.msgCertificado;
            }
            if (retorno.tipoAlerta === "Vermelho") {
                boxAlerta.className = "alert alert-danger";
                boxAlerta.innerHTML = retorno.msgCertificado;
            }
        }).setMessage('Pesquisando certificado.').execute();
    }
</script>
