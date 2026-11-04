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
var AndamentoProcesso = /** @class */ (function () {
    function AndamentoProcesso() {
        var _this = this;
        this.utilizaTelaAndamento = true;
        this.timerAtualizacao = 60;
        this.rpc = 'pro4_andamento_processo.RPC.php';
        this.apiUrl = '';
        this.codigoInstituicao = null;
        this.codigoDepartamento = null;
        this.anoProcesso = null;
        this.ultimoSequencialOuvidoria = null;
        this.collectionProcessos = [];
        this.windowAux = Object;
        this.windowTransferencia = Object;
        this.gridProcessos = Object;
        this.toogleFieldsetDadosProcesso = Object;
        this.toogleFieldsetCamposDinamicos = Object;
        this.data = {};
        this.documentos = [];
        this.despachosAnteriores = [];
        this.filtroStatus = [];
        this.filtroProcesso = [];
        this.filtroTipoProcesso = [];
        this.filtroResponsavel = []
        this.visualizarEmOutraJanela = false;
        this.departamentos = [];
        this.usuariosDepartamento = [];
        this.instituicoes = [];
        this.html = `<form action="" method="get" id="andamentoProcesso" enctype="multipart/form-data">
                    <div id="fieldset_dados_processo" style="display: flex;justify-content: center">

                                        <input type="text" name="dados_processo" class="" id="dados_processo" readonly style="width: 90px; background-color:#DEB887; margin: 7px 5px 0 5px" value="">
                                        <button type="button" for ="dados_processo"
                                        style="cursor:pointer; margin: 5px 5px 0 5px"
                                         id="consultaProcesso"
                                        >
                                           Ver Processo <i class="fa fa-gavel"></i>
                                        </button>
                                        <button type="button" style="cursor:pointer; margin: 5px 5px 0 5px;display: none;" id="apenasRecebe"  disabled>
                                        Apenas Receber <i class="fa fa-hand-holding"></i>
                                        </button>
                                        <button type="button" style="cursor:pointer; margin: 5px 5px 0 5px;" id="salvar">
                                        Despachar <i class="fa fa-file-import"></i>
                                        </button>
                                        <button type="button"  style="cursor:pointer; margin: 5px 5px 0 5px;display: none;" id="visualizarDocumentos">
                                        Visualizar Documentos <i class="fas fa-images"></i>
                                        </button>
                                        <button type="button"  style="cursor:pointer; margin: 5px 5px 0 5px;display: none;" id="executarAcao">
                                            Executar Ação <i class="fas fa-list-ul"></i>
                                        </button>
                                        <button type="button"  style="cursor:pointer; margin: 5px 5px 0 5px;display: none;" id="visualizarDados">
                                            Visualizar Dados <i class="fas fa-list-ul"></i>
                                        </button>
                                        <button type="button"  style="cursor:pointer; margin: 5px 5px 0 5px;display: none;" id="enviarRespostaRedesim">
                                            Enviar Resposta REDESIM <i class="fas fa-envelope"></i>
                                        </button>
                                        <button type="button" style="cursor:pointer; margin: 5px 5px 0 5px;display: none;" id="mensagem_processo_eletronico">
                                        Mensagens <i class="fas fa-envelope"></i>
                                        </button>
                                        <button type="button" style="cursor:pointer; margin: 5px 5px 0 5px;display: none;" id="btnTransferencia">
                                        Transferir <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        <button type="button" style="cursor:pointer; margin: 5px 5px 0 5px" id="cancelar">
                                           Voltar <i class="fas fa-backspace"></i>
                                        </button>

                                        <input type="hidden" name="dados_tipo" class="" id="dados_tipo" readonly style="width: 350px; background-color:#DEB887;" value="">
                                        <input type="hidden" name="dados_data" class="" id="dados_data" readonly style="width: 350px; background-color:#DEB887;" value="">
                                        <input type="hidden" name="dados_titular" class="" id="dados_titular" readonly style="width: 350px; background-color:#DEB887;" value="">
                                        <input type="hidden" name="dados_requerente" class="" id="dados_requerente" readonly style="width: 350px; background-color:#DEB887;" value="">
                                        <input type="hidden" name="dados_instituicao" class="" id="dados_instituicao" readonly style="width: 350px; background-color:#DEB887;" value="">
                                        <input type="hidden" name="dados_departamento_inicial" class="" id="dados_departamento_inicial" readonly style="width: 350px; background-color:#DEB887;" value="">
                                         </div>

                                     <fieldset>
                                    <legend>Despacho</legend>
                                    <table>
                                        <tr>
                                            <td>
                                                <label for ="despacho_publico">Despacho Público:</label>
                                            </td>
                                            <td>
                                               <select name="despacho_publico" id="despacho_publico">
                                                    <option value="true">Sim</option>
                                                    <option value="false">Não</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                    <table>
                                        <tr>
                                            <td>
                                                <label for ="despacho_interno">Despacho Interno:</label><br>
                                           </td>
                                        </tr>
                                    </table>
                                    <textarea style="width: 100%;" name="despacho_interno" id="despacho_interno" rows="10"></textarea>

                                    <fieldset>
                                        <legend>Documentos Anexados</legend>
                                        <table class="form-container">
                                            <tr>
                                                <td>
                                                    <label for ="despachoAnexos">Documento:</label>
                                                </td>
                                                <td>
                                                    <input type="file" name="arquivos[]" multiple="multiple" id="despachoAnexos"><br><br>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" >
                                                    <div id="gridAnexos"></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                </fieldset>
                                  <fieldset id="fieldset_campos_dinamicos">
                                    <legend>Campos</legend>
                                    <div id="camposDinamicos" class="container"></div>
                                </fieldset>
                                <fieldset>
                                    <legend>Despachos anteriores</legend>
                                    <div id="gridDespachosAnteriores" style="overflow: auto"></div>
                                </fieldset>
                </form>`;

        this.windowTransferenciaHTML = `
            <fieldset>
                <legend>Transferência</legend>
                <table>
                    <tr>
                        <td>
                            <label for ="instituicao_destino">Instituição:</label>
                        </td>
                        <td>
                            <select name="instituicao_destino" id="instituicao_destino">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for ="departamento_destino">Departamento:</label>
                        </td>
                        <td>
                            <select name="departamento_destino" id="departamento_destino">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for ="recebimento_destino">Recebimento:</label>
                        </td>
                        <td>
                            <select name="recebimento_destino" id="recebimento_destino">
                            </select><br>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <div style="
                    display: flex;
                    width: 100%;
                    justify-content: center;
             ">
                <button type="button" id="btnCancelarTransferencia" style="margin:10px">
                       Cancelar <i class="fas fa-backspace"></i>
                </button>
                <button type="button" id="btnConfirmarTransferencia" style="margin:10px">
                       Confirmar Transferência <i class="fas fa-exchange-alt"></i>
                </button>
             </div>
        `;

        this.criaGridAnexos = function () {
            _this.collectionAnexos = new Collection().setId('codigo');
            _this.gridAnexos = DatagridCollection.create(_this.collectionAnexos).configure({
                'order': false,
                'height': 80
            });
            _this.gridAnexos.addColumn('descricao', {
                'label': 'Descrição',
                'align': 'center',
                'width': '50%'
            });
            _this.gridAnexos.addAction('Excluir', 'Excluir', function (event, registro) {
                _this.gridAnexos.getCollection().remove(registro.codigo);
                _this.gridAnexos.reload();
            }, true, 'fa-trash-alt');
            _this.gridAnexos.addAction('Download', 'Download', function (event, registro) {
                window.open('db_download.php?arquivo=' + registro.caminho);
            }, true, 'fa-download');
        };
        this.criaGridDespachosAnteriores = function () {
            _this.collectionDespachosAnteriores = new Collection().setId('codigo');
            _this.gridDespachosAnteriores = DatagridCollection.create(_this.collectionDespachosAnteriores).configure({
                'order': false,
                'height': 154
            });
            _this.gridDespachosAnteriores.addColumn('data', {
                'label': 'Data',
                'align': 'center',
                'width': '80px'
            });
            _this.gridDespachosAnteriores.addColumn('tipo', {
                'label': 'Tipo',
                'align': 'center',
                'width': '70px'
            });
            _this.gridDespachosAnteriores.addColumn('usuario', {
                'label': 'Usuário ',
                'width': '40%'
            });
            _this.gridDespachosAnteriores.addColumn('despacho', {
                'label': 'Despacho',
                'align': 'center',
                'width': `30%`
            });

            if (!!_this.processo && _this.processo.tipoprocesso == 2) {
                _this.gridDespachosAnteriores.addAction('Visualizar', 'Visualizar', function (event, registro) {
                    _this.visualizarDocumentos(event, _this.processo, registro.codigo);
                }, true, 'fa-eye', '', {
                    width: '90px'
                });
            }

            _this.gridDespachosAnteriores.addAction('Assinar', 'Assinar', function (event, registro) {
                _this.assinarDocumentos(event, _this.processo, registro.codigo);
            }, true, 'fas fa-file-signature', '', {
                width: '90px'
            });

            _this.gridDespachosAnteriores.addAction('solicitarAssinatura', 'Solicitar Assinatura', function (event, registro) {
                _this.solicitarAssinaturaDocumentos(event, _this.processo, registro.codigo);
            }, true, 'fas fa-user-edit', '', {
                width: '90px'
            });
        };
        this.setRpc = function (rpc) {
            _this.rpc = rpc;
        };
        this.setApiUrl = function (apiUrl) {
            _this.apiUrl = apiUrl;
        }
        this.setCodigoInstituicao = function (codigoInstituicao) {
            _this.codigoInstituicao = codigoInstituicao;
        }
        this.setCodigoDepartamento = function (codigoDepartamento) {
            _this.codigoDepartamento = codigoDepartamento;
        }
        this.setWindowAux = function (windowAux) {
            _this.windowAux = windowAux;
        };

        this.setWindowTransferencia = function (windowTransferencia) {
            _this.windowTransferencia = windowTransferencia;
        };
        this.setStatus = function (tableRow, itemCollection, tableRowClass, itemCollectionText, itemCollectionStatus) {
            tableRow.className = 'normal ' + tableRowClass;
            itemCollection.status = itemCollectionText;
            itemCollection.codigostatus = itemCollectionStatus;
            var span = tableRow.querySelector('span.status');
            span.textContent = itemCollectionText;
        };
        this.setStatusAReceber = function (tableRow, itemCollection) {
            var tableRowClass = 'background-receber';
            var itemCollectionText = 'A receber';
            _this.setStatus(tableRow, itemCollection, tableRowClass, itemCollectionText, 1);
        };
        this.setStatusRecebido = function (tableRow, itemCollection) {
            var tableRowClass = 'background-recebido';
            var itemCollectionText = 'Recebido';
            tableRow.querySelector('[action_id=action_apenas_receber]').hide();
            _this.setStatus(tableRow, itemCollection, tableRowClass, itemCollectionText, 2);
        };
        this.setStatusDespachado = function (tableRow, itemCollection) {
            var tableRowClass = 'background-despachado';
            var itemCollectionText = 'Despachado';
            tableRow.querySelector('[action_id=action_apenas_receber]').hide();
            _this.setStatus(tableRow, itemCollection, tableRowClass, itemCollectionText, 3);
        };
        this.setStatusExterno = function (tableRow, itemCollection) {
            var tableRowClass = 'background-externo';
            var itemCollectionText = 'Externo';
            tableRow.querySelector('[action_id=action_apenas_receber]').hide();
            _this.setStatus(tableRow, itemCollection, tableRowClass, itemCollectionText, 4);
        };
        this.marcaProcessosComoRecebido = function () {
            _this.gridProcessos.getGrid().getRows().forEach(function (linha) {
                var itemCollection = linha.itemCollection;
                if (itemCollection.transferencia === _this.processo.transferencia) {
                    var tableRow = document.getElementById(linha.sId);
                    _this.setStatusRecebido(tableRow.closest('tr'), itemCollection);
                }
            });
        };

        this.setVisualizarEmOutraJanela = function (show = false) {
            _this.visualizarEmOutraJanela = show;
        }

        this.visualizarDocumentos = function (evento, processo, procandamint) {

            const data = new FormData();

            let apiDocumentos;
            if (procandamint) {
                data.append('procandamint', procandamint);
                data.append('codigoProcesso', processo.codigo);
                apiDocumentos = 'patrimonial/protocolo/processo/processodocumento/documentosPorProcAndamInt';
            } else {
                data.append('procandamint', procandamint);
                data.append('codigoProcesso', processo.codigo);
                apiDocumentos = 'patrimonial/protocolo/processo/processodocumento/documentosPorProcesso';
            }

            HttpClient.post(`${_this.apiUrl}${apiDocumentos}`, {body: data}).then(response => {
                if (response.error == true) {
                    alert(response.message);
                    return;
                }

                var codigosEStorage = [];
                var index = 0;

                if (!!procandamint) {
                    var ordem = response.data.filter(
                        (dados) => dados.codigo_andamento_interno == procandamint
                    ).map((dados) => dados.ordem).reduce(
                        (accumulator, currentValue) => (currentValue < accumulator) ? currentValue : accumulator
                    );

                    index = (ordem || 1) - 1;
                }

                response.data.forEach((documento) => {
                    codigosEStorage.push(documento.id_estorage);
                });


                if (codigosEStorage.length == 0) {
                    alert("Nenhum documento encontrado para o processo.");
                    return false;
                }

                if (_this.visualizarEmOutraJanela) {
                    window.open(`db_visualizador_documentos.php?ids=${codigosEStorage}&viewIndex=${index}`);
                } else {
                    js_OpenJanelaIframe(
                        'CurrentWindow.corpo',
                        'db_visualizador_imagens',
                        `db_visualizador_documentos.php?ids=${codigosEStorage}&viewIndex=${index}`,
                        'Visualizador de documentos',
                        true
                    );
                }

                $('Jandb_visualizador_imagens').style.zIndex = 501;

            });
        };

        this.executarAcao = function (evento, processo) {
            new ExecutarAcaoRedesim().init(processo, _this.apiUrl)
        }

        this.assinarDocumentos = function (evento, processo, procandamint) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_assinador_documentos',
                `db_assinar_documentos.php?codigoProcesso=${processo.codigo}&procandamint=${procandamint}`,
                'Visualizador de documentos',
                true
            );
        };

        this.solicitarAssinaturaDocumentos = function (evento, processo, procandamint) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_solicitacao_assinatura',
                `web/patrimonial/protocolo/solicitacao-assinatura/processo/${processo.codigo}/despacho/${procandamint}`,
                'Solicitação de assinatura',
                true
            );
        };

        this.consultaProcesso = function (event, processo) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_consulta_processo',
                `pro3_consultaprocesso002.php?codproc=${processo.codigo}`,
                'Consulta Processso',
                true
            );
        }

        this.recebeProcesso = function (evento, processo) {
            _this.setProcesso(processo);
            _this.verificaStatusAtualProcesso()
                .then(function (response) {
                    if (response.concluiRecebimento) {
                        var data = new FormData();
                        data.append('acao', 'apenasReceber');
                        data.append('codigoTransferencia', processo.transferencia);
                        data.append('filtraDepartamentosPorDataLimite', 1);
                        data.append('filtros', JSON.stringify(['p58_codproc = ' + _this.processo.codigo]));
                        data.append('hash', _this.processo.hash);
                        HttpClient.post(_this.rpc, {
                            body: data,
                            reportMessage: 'Recebendo...',
                            reportProgress: true,
                        }).then(function (response) {
                            if (response.erro) {
                                alert(response.mensagem);
                                return;
                            }
                            if (response.processo && _this.utilizaTelaAndamento) {
                                var processoAtualizado = response.processo.first();
                                _this.setProcessoAtualizado(processoAtualizado);
                            }

                            if (_this.utilizaTelaAndamento) {
                                _this.marcaProcessosComoRecebido();
                            } else {
                                window.location.reload();
                            }
                        });
                    }
                    if (response.exibeAlerta) {
                        alert(response.mensagem);
                    }
                })["catch"](function (mensagem) {
                return alert(mensagem);
            });
        };
        this.abreAndamentoProcesso = function (processo) {
            js_divCarregando("Carregando...", 'loading_message');
            _this.setProcesso(processo);

            if (processo.codigostatus == AndamentoProcesso.STATUS_EXTERNO) {
                var numeroAnoProcesso = processo.processo.split('/');
                var url = `pro4_processo_externo.php?processo=${numeroAnoProcesso[0]}&ano=${numeroAnoProcesso[1]}&tipoProcesso=${processo.tipo_processo}&escondeBotoes=false`;
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_processo_externo', url, 'Processo Externo', true, 0, 0);
                js_removeObj('loading_message');
                return;
            }


            _this.verificaStatusAtualProcesso()
                .then(function (response) {

                    if (response.abreJanelaAndamento) {
                        _this.setInstituicoes(_this.processo.instituicoes);
                        _this.setUsuariosDepartamento(_this.processo.usuariosDepartamento);
                        _this.setData({
                            'dados_processo': _this.processo.processo,
                            //  'dados_observacao': _this.processo.observacao,
                            'dados_requerente': _this.processo.requerente,
                            'dados_data': _this.processo.data,
                            'dados_tipo': _this.processo.descricao,
                            'dados_titular': _this.processo.titular,
                            'dados_instituicao': _this.processo.instituicao,
                            'dados_departamento_inicial': _this.processo.departamento
                        });
                        var cloneDespachosAnteriores_1 = [];
                        _this.processo.despachosAnteriores.map(function (despacho) {
                            return cloneDespachosAnteriores_1.push(Object.assign({}, despacho));
                        });
                        _this.setDespachosAnteriores(cloneDespachosAnteriores_1);
                        _this.exibe();
                    } else if (_this.utilizaTelaAndamento === false) {
                        window.location.reload();
                        if (response.exibeAlerta) {
                            alert(response.mensagem);
                        }
                    }
                    if (response.exibeAlerta) {
                        alert(response.mensagem);
                    }
                    js_removeObj('loading_message');
                })["catch"](function (mensagem) {
                js_removeObj('loading_message');
                return alert(mensagem);
            });
        };
        this.abreMensagens = function (processo) {
            //var numeroAnoProcesso = processo.processo.split('/');
            //var url = `pro4_processo_mensagem.php?processo=${processo.codigo}`;
            //js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_processo_mensagem', url, 'Processo Mensagens', true, 0, 0);
            js_OpenJanelaIframe('CurrentWindow.corpo', 'visualizar_mensagens', `web/patrimonial/protocolo/mensageria-protocolo/${processo.codigo}`, 'Visualizador de Mensagens', true);
        };
        this.setProcesso = function (processo) {
            _this.processo = processo;
        };
        this.setDepartamentos = function (departamentos) {
            _this.departamentos = departamentos;
        };
        this.setInstituicoes = function (instituicoes) {
            _this.instituicoes = instituicoes;
        };
        this.setUsuariosDepartamento = function (usuariosDepartamento) {
            this.usuariosDepartamento = usuariosDepartamento;
        };
        this.setBotaoAtualizar = function (btnAtualizar) {
            _this.btnAtualizar = btnAtualizar;
            return _this;
        };
        this.setBotaoAtualizarAno = function (btnAtualizarAno) {
            _this.btnAtualizarAno = btnAtualizarAno;
            return _this;
        };
        this.utilizaTelaAndamentoProcesso = function (utilizaTelaAndamento) {
            _this.utilizaTelaAndamento = utilizaTelaAndamento;
        };
        this.setData = function (data) {
            _this.data = data;
        };
        this.setDespachosAnteriores = function (despachosAnteriores) {
            _this.despachosAnteriores = despachosAnteriores;
        };
        this.setDocumentos = function (documentos) {
            _this.documentos = documentos;
        };
        this.setDataAno = (value) => {
            _this.anoProcesso = value;
            return _this;
        }
        this.setGridLinhaSelecionada = function (linhaGridSelecionada) {
            _this.linhaGridSelecionada = linhaGridSelecionada;
        };
        this.setFiltroProcesso = function (value) {
            _this.filtroProcesso = value;
            return _this;
        };
        this.setFiltroRequerente = function (value) {
            _this.filtroRequerente = value;
            return _this;
        };
        this.setFiltroDescricao = function (value) {
            _this.filtroDescricao = value;
            return _this;
        };
        this.setFiltroMensagem = function (value) {
            _this.filtroMensagem = value;
            return _this;
        };
        this.setFiltroData = function (value) {
            _this.filtroData = value;
            return _this;
        };

        this.addFiltroStatus = function (value) {
            _this.filtroStatus.push(value);
            return _this;
        };
        this.addFiltroTipoProcesso = function (value) {
            _this.filtroTipoProcesso.push(value);
            return _this;
        };
        this.addFiltroResponsavel = function (value) {
            _this.filtroResponsavel.push(value);
            return _this;
        };
        this.organizaDados = function () {
            Object.keys(_this.data).forEach(function (id) {
                var value = _this.data[id];
                var element = document.getElementById(id);
                var nodeName = element.nodeName;
                switch (nodeName.toLowerCase()) {
                    case 'textarea':
                    case 'input':
                        element.value = value;
                        break;
                    case 'select':
                        var option = element.querySelector('option[value=' + value + ']');
                        option.selected = true;
                        break;
                }
            });
        };
        this.organizaDocumentos = function () {
            _this.gridAnexos.show(document.getElementById('gridAnexos'));
            _this.gridAnexos.getCollection().add(_this.processo.documentos);
            _this.gridAnexos.reload();
        };
        this.organizaDespachosAnteriores = function () {
            _this.gridDespachosAnteriores.show(document.getElementById('gridDespachosAnteriores'));
            _this.gridDespachosAnteriores.getCollection().add(_this.despachosAnteriores);
            _this.gridDespachosAnteriores.reload();
        };
        this.organizaDepartamentos = function () {
            var selectDepartamentos = document.getElementById('departamento_destino');
            var option = document.createElement('option');
            option.value = '';
            option.textContent = "Selecione o Departamento";
            selectDepartamentos.appendChild(option);
            _this.departamentos.map(function (departamento) {
                option = document.createElement('option');
                option.value = departamento.sequencial;
                option.textContent = `${departamento.sequencial}  - ${departamento.descricao}`;
                selectDepartamentos.appendChild(option);
            });
        };
        this.organizaInstituicoes = function () {
            var selectInstituicoes = document.getElementById('instituicao_destino');
            var option = document.createElement('option');
            option.value = '';
            option.textContent = "Selecione o Instituição";
            selectInstituicoes.appendChild(option);
            _this.instituicoes.map(function (instituicao) {
                option = document.createElement('option');
                option.value = instituicao.codigo;
                option.textContent = instituicao.codigo + " - " + instituicao.descricao;
                selectInstituicoes.appendChild(option);
            });
        };
        this.organizaUsuariosDepartamento = function () {
            var selectUsuariosDepartamento = document.getElementById('recebimento_destino');
            var option = document.createElement('option');
            if (_this.usuariosDepartamento[0].id_usuario != 0) {
                option.value = '';
                option.textContent = "Selecione o Usuário";
                selectUsuariosDepartamento.appendChild(option);
            }
            _this.usuariosDepartamento.map(function (usuario) {
                option = document.createElement('option');
                option.value = usuario.id_usuario;
                option.textContent = usuario.nome.urlDecode();
                selectUsuariosDepartamento.appendChild(option);
            });
        };
        this.exibeGridProcessos = function (divProcessos) {
            _this.collectionProcessos = new Collection().setId('codigo');
            _this.gridProcessos = DatagridCollection.create(_this.collectionProcessos).configure({
                'order': false,
                'height': 400
            });
            _this.gridProcessos.addColumn('codigoStatus', {
                'label': 'codigoStatus'
            });
            _this.gridProcessos.addColumn('transferencia', {
                'label': 'Transferência',
                'align': 'center',
                'width': '80px'
            });
            _this.gridProcessos.addColumn('processo', {
                'label': 'Processo',
                'align': 'center',
                'width': '135px'
            });
            _this.gridProcessos.addColumn('requerente', {
                'label': 'Requerente',
                'align': 'center',
                'width': '280px'
            });
            _this.gridProcessos.addColumn('descricao', {
                'label': 'Descrição',
                'width': '180px',
                'align': 'center'
            });
            _this.gridProcessos.addColumn('data', {
                'label': 'Data',
                'align': 'center',
                'width': '65px'
            });
            _this.gridProcessos.addColumn('observacao', {
                'label': 'Observação',
                'align': 'center',
                'width': '220px'
            }).transform(function (valor, collection) {
                var span = document.createElement('span');
                const tempElement = document.createElement('div');
                tempElement.innerHTML = valor;
                const plainText = tempElement.textContent || tempElement.innerText;
                span.textContent = plainText;
                span.title = plainText;
                return span.outerHTML;
            });
            _this.gridProcessos.addColumn('status', {
                'label': 'Status',
                'align': 'center',
                'width': '90px'
            }).transform(function (valor, collection) {
                var span = document.createElement('span');
                span.className = 'status';
                span.textContent = valor;
                return span.outerHTML;
            });
            _this.gridProcessos.addAction('Apenas Receber', 'Apenas Receber', function (event, item) {
                _this.setGridLinhaSelecionada(event.target.closest('tr'));
                _this.recebeProcesso(event, item);
            }, true, 'fa-check');

            _this.gridProcessos.addAction('Mensagens', 'Mensagens', function (event, item) {
                _this.setGridLinhaSelecionada(event.target.closest('tr'));
                _this.abreMensagens(item);
            }, true, 'fa-envelope', '');

            _this.gridProcessos.addAction('Acoes', 'Ações', function (event, item) {
                _this.setGridLinhaSelecionada(event.target.closest('tr'));
                _this.abreAndamentoProcesso(item);
            }, true, 'fa-list-ul');

            _this.gridProcessos.setEvent('onaftercreatebutton', function (button, itemCollection) {
                if (button.getAttribute('action_id') == 'action_apenas_receber' && itemCollection.codigostatus != AndamentoProcesso.STATUS_A_RECEBER) {
                    button.hide();
                }

                if (button.getAttribute('action_id') == 'action_mensagens') {
                    let qtdMensagens = 0;
                    if (itemCollection.mensagens_novas) {
                        qtdMensagens = itemCollection.mensagens_novas;
                    }
                    // if (itemCollection.mensagens_nao_lidas) {
                    //     qtdMensagens = itemCollection.mensagens_nao_lidas
                    // }
                    button.firstElementChild.innerText = ` ${qtdMensagens}`;
                    if (itemCollection.codigostatus == 4) {
                        button.hide();
                    }
                }
            });

            _this.gridProcessos.hideColumns([0]);
            _this.gridProcessos.show(divProcessos);
        };
        this.verificaStatus = function () {
            _this.gridProcessos.getGrid().getRows().forEach(function (linha) {
                var tableRow = document.getElementById(linha.sId);
                switch (parseInt(linha.itemCollection.codigostatus)) {
                    case AndamentoProcesso.STATUS_A_RECEBER:
                        _this.setStatusAReceber(tableRow, linha.itemCollection);
                        break;
                    case AndamentoProcesso.STATUS_RECEBIDO:
                        _this.setStatusRecebido(tableRow, linha.itemCollection);
                        break;
                    case AndamentoProcesso.STATUS_DESPACHADO:
                        _this.setStatusDespachado(tableRow, linha.itemCollection);
                        break;
                    case AndamentoProcesso.STATUS_EXTERNO:
                        _this.setStatusExterno(tableRow, linha.itemCollection);
                        break;
                }
                if (linha.itemCollection.parausuariologado == 1) {
                    tableRow.classList.add('processoParaUsuarioLogado');
                }
            });
        };
        this.setProcessoAtualizado = function (processoAtualizado) {
            _this.gridProcessos.getCollection().add(processoAtualizado);
            _this.gridProcessos.getCollection().sort('desc', ['transferencia']);
            _this.gridProcessos.reload();
            _this.setProcesso(processoAtualizado);
            _this.verificaStatus();
            _this.aplicarFiltros();
        };
        this.verificaStatusAtualProcesso = function () {
            var data = new FormData();

            data.append('acao', 'buscarProcessos');
            data.append('filtros', JSON.stringify(['p58_codproc = ' + _this.processo.codigo]));
            data.append('filtraDepartamentosPorDataLimite', 1);

            return HttpClient.post(_this.rpc, {body: data}).then(function (response) {
                if (response.erro) {
                    throw response.mensagem;
                }
                var resposta = {
                    'abreJanelaAndamento': true,
                    'abreJanelaProcessoExterno': false,
                    'exibeAlerta': true,
                    'mensagem': 'O processo acessado foi alterado por outro usuário, verifique o estado atual do processo com as informações atualizadas.',
                    'concluiRecebimento': true
                };
                if (response.processos.length > 0) {
                    var processoAtualizado = response.processos.last();
                    if (processoAtualizado.hash !== _this.processo.hash) {
                        resposta.concluiRecebimento = false;
                        if (_this.utilizaTelaAndamento) {
                            _this.setProcessoAtualizado(processoAtualizado);
                        } else {
                            _this.setProcesso(processoAtualizado);
                        }
                    } else {
                        resposta.exibeAlerta = false;
                    }

                    if (processoAtualizado.codigostatus == AndamentoProcesso.STATUS_EXTERNO) {
                        resposta.abreJanelaAndamento = false;
                        resposta.abreJanelaProcessoExterno = true;
                    }
                } else {
                    resposta.concluiRecebimento = false;
                    resposta.abreJanelaAndamento = false;
                    resposta.mensagem = 'O processo acessado foi transferido por outro usuário, ele não está mais presente neste departamento.';
                    if (_this.utilizaTelaAndamento) {
                        _this.marcaProcessoComoTransferido();
                    }
                }
                return resposta;
            });
        };

        this.buscaProcessos = function (autoAtualizar) {
            if (autoAtualizar === void 0) {
                autoAtualizar = false;
            }

            var data = new FormData();
            data.append('acao', 'buscarProcessos');
            data.append('filtraDepartamentosPorDataLimite', 1);
            data.append('filtros', JSON.stringify(['p51_prottipodocumentoprocesso NOT IN (6, 7)']));

            HttpClient.post(_this.rpc, {body: data}).then(function (response) {

                _this.gridProcessos.getCollection().add(response.processos);
                _this.gridProcessos.getCollection().sort('desc', ['transferencia']);
                _this.gridProcessos.reload();
                _this.verificaStatus();
                _this.aplicarFiltros();
                if (autoAtualizar) {
                    _this.autoAtualizar();
                }
            });
        };

        this.buscaProcessosAno = (anoProcesso) => {
            var data = new FormData();
            data.append('acao', 'buscarProcessos');
            data.append('filtraDepartamentosPorDataLimite', 1);
            data.append('anoProcesso', anoProcesso);
            data.append('filtros', JSON.stringify(['p51_prottipodocumentoprocesso NOT IN (6, 7)']));

            HttpClient.post(_this.rpc, {body: data}).then(function (response) {
                _this.gridProcessos.getCollection().add(response.processos);
                _this.gridProcessos.getCollection().sort('desc', ['transferencia']);
                _this.gridProcessos.reload();
                _this.verificaStatus();
                _this.aplicarFiltros();
            })
        }

        this.buscaOuvidoria = function (ultimoSequencial) {

            const data = new FormData();
            data.append('DB_instit', _this.codigoInstituicao);
            data.append('DB_coddepto', _this.codigoDepartamento);

            if (ultimoSequencial) {
                data.append('ultimoSequencial', ultimoSequencial);
            }


            return HttpClient.post(`${_this.apiUrl}patrimonial/ouvidoria/atendimento/atendimento/buscarProcessosOuvidoria`, {
                body: data,
                reportProgress: false
            }).then(apiResponse => {

                if (apiResponse.error == true) {
                    alert(apiResponse.message);
                    return;
                }

                apiResponse.data.forEach((processo, index) => {

                    _this.ultimoSequencialOuvidoria = processo.sequencial;

                    var requerente = "ANÔNIMO";

                    if (processo.solicitante) {
                        requerente = processo.solicitante.toUpperCase()
                    }

                    var processoGrid = {
                        codigo: processo.sequencial + '#' + index,
                        codigostatus: 4,
                        transferencia: '-',
                        processo: processo.processo,
                        requerente: requerente,
                        descricao: processo.tipo_processo_descricao,
                        data: new Date(processo.data).toLocaleDateString('pt-Br', {timeZone: 'UTC'}),
                        observacao: '-',
                        status: 'Externo',
                        tipo_processo: processo.tipo_processo,
                        flag_processo_eletronico: 1,
                        parausuariologado: 0
                    };

                    _this.gridProcessos.getCollection().add(processoGrid);
                });
                _this.gridProcessos.getCollection().sort('desc', ['transferencia']);
                _this.gridProcessos.reload();
                _this.verificaStatus();
                _this.aplicarFiltros();
            });
        }

        this.adicionarDespacho = function (despacho) {
            _this.processo.despachosAnteriores.push(despacho);
        };
        this.marcaProcessoComoTransferido = function () {
            var linha = _this.gridProcessos.getGrid().getRowById(_this.linhaGridSelecionada.id);
            var linhas = _this.gridProcessos.getGrid().getRows();
            var tableRow = document.getElementById(linha.sId);
            tableRow.parentNode.removeChild(tableRow);
            _this.gridProcessos.getCollection().remove(linha.itemCollection.codigo);
            delete linhas[linha.getRowNumber()];
            _this.atualizarContadorGridProcessos();
        };
        this.processar = function () {
            var despachoInterno = document.getElementById('despacho_interno');
            var despachoPublico = document.getElementById('despacho_publico');
            var data = new FormData();
            data.append('acao', 'processar');

            if (_this.processo.codigostatus != AndamentoProcesso.STATUS_A_RECEBER) {
                var campos = new Map();
                var camposPreenchidos = [];
                var camposDinamicosErro = false;
                var divCamposDinamicos = document.querySelector('#camposDinamicos');

                divCamposDinamicos.childElements().forEach(div => {

                    let field = div.querySelector('#' + div.getAttribute('data-field-id'));
                    let typeField = div.getAttribute('data-field-type');
                    let required = field.getAttribute('data-validation-required');

                    field.removeClassName('error-field');

                    if (required === true || required === 'true') {
                        if (field.value.trim() == '') {
                            field.addClassName('error-field');
                            camposDinamicosErro = true;
                        }
                    }

                    campos.set(
                        field.getAttribute('data-p110_sequencial'),
                        {
                            codigo: field.getAttribute('data-p111_sequencial') ? field.getAttribute('data-p111_sequencial') : null,
                            resposta: field.value
                        }
                    );
                });

                if (camposDinamicosErro === true) {
                    return alert("Verifique o preenchimento dos campos");
                }

                campos.forEach((resposta, codcam) => {

                    camposPreenchidos.push('campo_' + codcam)

                    data.append(
                        'campo_' + codcam,
                        resposta.resposta
                    )

                    if (resposta.codigo) {
                        data.append(
                            'sequencial_campo_' + codcam,
                            resposta.codigo
                        )
                    }
                })

                data.append('campos', camposPreenchidos);
            }

            data.append('despachoInterno', despachoInterno.value);
            data.append('despachoPublico', despachoPublico.value);
            data.append('codigoProcesso', _this.processo.codigo);
            data.append('codigoTransferencia', _this.processo.transferencia);
            data.append('isMensagemProcessoEletronico', $('mensagem_processo_eletronico').checked);
            data.append('filtraDepartamentosPorDataLimite', 1);
            data.append('hash', _this.processo.hash);
            data.append('filtros', JSON.stringify(['p58_codproc = ' + _this.processo.codigo]));

            var documentos = [];
            _this.gridAnexos.getCollection().itens.map(function (anexo) {
                documentos.push({
                    'codigo': anexo.codigo,
                    'descricao': anexo.descricao,
                    'caminho': anexo.caminho
                });
            });
            data.append('despachoAnexos', JSON.stringify(documentos));
            HttpClient.post(_this.rpc, {
                body: data,
                reportMessage: "Salvando...",
                reportProgress: true
            }).then(function (response) {
                if (response.erro) {
                    throw response.mensagem;
                }
                if (response.processamento.houveAlteracao) {
                    if (response.processamento.processo == null) {
                        _this.marcaProcessoComoTransferido();
                    } else {
                        _this.setProcessoAtualizado(response.processamento.processo);
                    }
                    _this.windowAux.hide();
                    throw response.processamento.mensagem;
                }
                if (_this.utilizaTelaAndamento) {
                    var linha = _this.gridProcessos.getGrid().getRowById(_this.linhaGridSelecionada.id);
                    var tableRow = document.getElementById(linha.sId);
                    if (response.processamento.recebido) {
                        _this.marcaProcessosComoRecebido();
                    }
                    if (response.processamento.transferido) {
                        _this.marcaProcessoComoTransferido();
                    } else if (response.processamento.despachado) {
                        _this.adicionarDespacho(response.processamento.despachado);
                        _this.setStatusDespachado(tableRow, linha.itemCollection);
                    }
                    if (response.processo && response.processo.length > 0) {
                        _this.setProcessoAtualizado(response.processo.first());
                    }
                } else {
                    window.location.reload();
                }
                _this.windowAux.hide();
                _this.abreAndamentoProcesso(_this.processo);
            }).catch(mensagem => mensagem ? mensagem.message ? alert(mensagem.message) : alert(mensagem) : null);
        };

        this.transferir = function () {

            const departamentoDestino = document.getElementById('departamento_destino');
            const recebimentoDestino = document.getElementById('recebimento_destino');
            const data = new FormData();

            data.append('acao', 'transferir');
            data.append('departamentoDestino', departamentoDestino.value);
            data.append('recebimentoDestino', recebimentoDestino.value);
            data.append('codigoProcesso', _this.processo.codigo);
            data.append('codigoTransferencia', _this.processo.transferencia);
            data.append('filtraDepartamentosPorDataLimite', 1);
            data.append('hash', _this.processo.hash);
            data.append('filtros', JSON.stringify(['p58_codproc = ' + _this.processo.codigo]));

            HttpClient.post(_this.rpc, {
                body: data,
                reportMessage: "Salvando...",
                reportProgress: true
            }).then(function (response) {
                if (response.erro) {
                    throw response.mensagem;
                }
                if (response.processamento.houveAlteracao) {
                    if (response.processamento.processo == null) {
                        _this.marcaProcessoComoTransferido();
                    } else {
                        _this.setProcessoAtualizado(response.processamento.processo);
                    }
                    _this.windowAux.hide();
                    throw response.processamento.mensagem;
                }
                if (_this.utilizaTelaAndamento) {
                    var linha = _this.gridProcessos.getGrid().getRowById(_this.linhaGridSelecionada.id);
                    var tableRow = document.getElementById(linha.sId);
                    if (response.processamento.recebido) {
                        _this.marcaProcessosComoRecebido();
                    }

                    if (response.processamento.transferido) {
                        _this.marcaProcessoComoTransferido();
                    } else if (response.processamento.despachado) {
                        _this.adicionarDespacho(response.processamento.despachado);
                        _this.setStatusDespachado(tableRow, linha.itemCollection);
                    }
                    if (response.processo && response.processo.length > 0) {
                        _this.setProcessoAtualizado(response.processo.first());
                    }
                } else {
                    window.location.reload();
                }
                _this.windowAux.hide();
                _this.windowTransferencia.hide();
            }).catch(mensagem => mensagem ? mensagem.message ? alert(mensagem.message) : alert(mensagem) : null);
        };

        this.buscarUltimaTransferencia = function () {
            var ultimoProcesso = _this.gridProcessos.getCollection().itens.first();
            return ultimoProcesso.transferencia;
        };
        this.normalizarObjetoProcesso = function (processo) {
            return {
                'codigo': processo.codigo,
                'codigoandamento': processo.codigoandamento,
                'codigoprocesso': processo.codigoprocesso,
                'processo': processo.processo,
                'tipoprocesso': processo.tipoprocesso,
                'codigostatus': processo.codigostatus,
                'data': processo.data,
                'departamento': processo.departamento,
                'descricao': processo.descricao,
                'despachosAnteriores': processo.despachosAnteriores,
                'documentos': processo.documentos,
                'departamentos': [],
                'instituicoes': processo.instituicoes,
                'instituicao': processo.instituicao,
                'usuariosDepartamento': processo.usuariosDepartamento,
                'observacao': processo.observacao,
                'parausuariologado': processo.parausuariologado,
                'processointerno': processo.processointerno,
                'requerente': processo.requerente,
                'status': processo.status,
                'titular': processo.titular,
                'transferencia': processo.transferencia,
                'mensagens_nao_lidas': processo.mensagens_nao_lidas,
                'mensagens_novas': processo.mensagens_novas,
                'flag_processo_eletronico': processo.flag_processo_eletronico,
                'isProcessoRedesim': processo.isProcessoRedesim,
                'isProcessoRedesimInclusaoInscricao': processo.isProcessoRedesimInclusaoInscricao,
                'numcgm': processo.numcgm,
                'hash': processo.hash
            };
        };
        this.atualizar = function (ultimaTransferencia) {
            var data = new FormData();
            data.append('acao', 'buscaNovosProcessos');
            data.append('ultimaTransferencia', ultimaTransferencia);
            data.append('filtraDepartamentosPorDataLimite', 1);
            HttpClient.post(_this.rpc, {body: data}).then(function (response) {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                var processos = response.processos;
                _this.gridProcessos.getGrid().getRows().forEach(function (linha) {
                    processos.push(_this.normalizarObjetoProcesso(linha.itemCollection));
                });
                _this.gridProcessos.getCollection().add(processos);
                _this.gridProcessos.reload();
                _this.verificaStatus();
                _this.aplicarFiltros();
            });
        };
        this.isMensagemProcessoEletronico = function () {
            if (_this.processo.flag_processo_eletronico == 1) {
                $('mensagem_processo_eletronico').style.display = '';
            }
        };
        this.adicionaAcoes = function () {
            const btnApenasRecebe = document.getElementById('apenasRecebe');
            const btnCancelar = document.getElementById('cancelar');
            const btnSalvar = document.getElementById('salvar');
            const btnVisualizarDocumentos = document.getElementById('visualizarDocumentos');
            const btnExecutarAcao = document.getElementById('executarAcao');
            const btnVisualizarDados = document.getElementById('visualizarDados');
            const btnEnviarRespostaRedesim = document.getElementById('enviarRespostaRedesim');
            const btnMensagem = document.getElementById('mensagem_processo_eletronico');
            const btnTransferencia = document.getElementById('btnTransferencia');
            const fieldsetCamposDinamicos = document.getElementById('fieldset_campos_dinamicos');
            const btnConsultaProcesso = document.getElementById('consultaProcesso');
            const despachoAnexos = document.getElementById('despachoAnexos')

            if (_this.processo.tipoprocesso == 2) {
                btnVisualizarDocumentos.show();
            }

            if (_this.processo.codigostatus == AndamentoProcesso.STATUS_A_RECEBER) {
                btnApenasRecebe.show();
                btnApenasRecebe.enable();
            }

            if (_this.processo.codigostatus == AndamentoProcesso.STATUS_DESPACHADO
                ||
                _this.processo.codigostatus == AndamentoProcesso.STATUS_RECEBIDO
            ) {
                btnTransferencia.show();
                btnTransferencia.enable();
            }

            if (_this.processo.isProcessoRedesim) {
                btnExecutarAcao.show();

                btnExecutarAcao.addEventListener('click', function (event) {
                    _this.executarAcao(event, _this.processo);
                });

                btnEnviarRespostaRedesim.show();

                btnEnviarRespostaRedesim.addEventListener('click', function (event) {
                    new EnviaRespostaRedesim().init(_this.processo, _this.apiUrl);
                });

                btnVisualizarDados.show();

                btnVisualizarDados.addEventListener('click', function (event) {
                    new VisualizarDadosRedesim().init(_this.processo, _this.apiUrl);
                });
            }

            btnVisualizarDocumentos.addEventListener('click', function (event) {
                _this.visualizarDocumentos(event, _this.processo);
            });

            btnConsultaProcesso.addEventListener('click', function (event) {
                _this.consultaProcesso(event, _this.processo);
            });
            btnCancelar.addEventListener('click', function () {
                _this.windowAux.hide();
            });
            btnApenasRecebe.addEventListener('click', function (event) {
                _this.recebeProcesso(event, _this.processo);
                _this.windowAux.hide();
            });
            btnSalvar.addEventListener('click', function () {
                _this.processar();
            });
            btnMensagem.addEventListener("click", function () {
                _this.abreMensagens(_this.processo);
            });

            despachoAnexos.addEventListener('change', function (event) {
                var files = despachoAnexos.files;
                var data = new FormData();
                data.append('acao', 'prepararDocumentos');
                for (var i = 0; i < files.length; i++) {
                    data.append('anexos[]', files[i]);
                }
                HttpClient.post(_this.rpc, {body: data}).then(function (response) {
                    if (response.erro) {
                        alert(response.mensagem);
                        return;
                    }
                    var documentos = [];
                    response.documentos.map(function (documento) {
                        return documentos.push(Object.assign({}, documento));
                    });
                    _this.gridAnexos.getCollection().add(documentos);
                    _this.gridAnexos.reload();
                });
            });

            btnTransferencia.addEventListener('click', function () {
                _this.exibeWindowTransferencia();
            });

            this.toogleFieldsetCamposDinamicos = new DBToogle(fieldsetCamposDinamicos, false);
        };
        this.carregarRespostaCamposdinamicos = function (field) {

            const
                apiUrl = _this.apiUrl,
                camposDinamicosRoutePrefix = "patrimonial/protocolo/processo/",
                camposandpadrao = field.getAttribute('data-p110_sequencial')
            ;
            let urlRequest = `${apiUrl}${camposDinamicosRoutePrefix}andamento-padrao/campos-dinamicos/resposta`;
            urlRequest += `?codigo_camposandpadrao=${camposandpadrao}`;
            urlRequest += `&codigo_codandam=${_this.processo.codigoandamento}`;

            return HttpClient.get(
                urlRequest,
                {
                    reportProgress: false,
                }
            ).then(response => {

                if (response.error == true) {
                    throw response;
                }

                if (response.data.length == 0) {
                    return null;
                }

                const res = {
                    field: field,
                    resposta: []
                }

                if (response.data && response.data.length > 0) {
                    res.resposta = response.data.first();
                }

                return res;

            }).catch(error => error.message ? alert(error.message) : alert(error));
        };
        this.camposDinamicos = function () {

            const
                apiUrl = _this.apiUrl,
                camposDinamicosRoutePrefix = "patrimonial/protocolo/processo/",
                codigo_processo = _this.processo.codigo,
                coddepto = _this.codigoDepartamento,
                camposDinamicos = document.querySelector('#camposDinamicos')
            ;
            js_divCarregando("Carregando campos", 'loading_message');

            HttpClient.get(
                `${apiUrl}${camposDinamicosRoutePrefix}andamento-padrao/campos-dinamicos/${codigo_processo}?codigo_depto=${coddepto}`,
                {
                    reportProgress: false,
                }
            ).then(response => {
                if (response.error == true) {
                    throw response;
                }

                if (response.data.length == 0) {
                    js_removeObj('loading_message');
                    return response;
                }

                const
                    campos = response.data.reverse(),
                    respostasCamposDinamicos = []
                ;
                campos.forEach(c => {

                    const
                        divCampo = document.createElement('div')
                    labelCampo = document.createElement('label')
                    ;
                    let fieldCampo;

                    if (c.campo.tipo == 'textarea') {
                        divCampo.setAttribute('data-field-type', 'textarea');
                        fieldCampo = document.createElement('textarea');
                    } else if (c.campo.tipo == 'boolean' || c.campo.opcoes.length > 0) {
                        divCampo.setAttribute('data-field-type', 'select');
                        fieldCampo = document.createElement('select');

                        const optionSelecione = document.createElement('option');
                        optionSelecione.setAttribute('value', '');
                        optionSelecione.innerHTML = 'Selecione';

                        fieldCampo.append(optionSelecione);

                        c.campo.opcoes.forEach(opcao => {
                            const optionCampo = document.createElement('option');
                            optionCampo.setAttribute('value', opcao.id)
                            optionCampo.innerHTML = opcao.descricao;

                            fieldCampo.append(optionCampo);
                        });
                    } else {
                        divCampo.setAttribute('data-field-type', 'input');
                        fieldCampo = document.createElement('input');
                        fieldCampo.setAttribute('type', 'text');
                    }

                    labelCampo.setAttribute('for', 'campo_' + c.codigo);
                    labelCampo.setAttribute('title', c.campo.descricao);
                    labelCampo.innerHTML = c.campo.label + ':';

                    fieldCampo.setAttribute('name', c.campo.nomecam);
                    fieldCampo.setAttribute('id', c.campo.nomecam);
                    fieldCampo.setAttribute('data-p110_sequencial', c.codigo);
                    fieldCampo.setAttribute('data-validation-required', c.obrigatorio);
                    fieldCampo.addClassName('field-size4');

                    if (_this.processo.codigostatus == AndamentoProcesso.STATUS_A_RECEBER) {
                        fieldCampo.setAttribute('readonly', true);
                        fieldCampo.addClassName('readonly');
                    }

                    divCampo.setAttribute('data-field-id', c.campo.nomecam);
                    divCampo.append(labelCampo);
                    divCampo.append(fieldCampo);

                    camposDinamicos.append(divCampo);

                    respostasCamposDinamicos.push(this.carregarRespostaCamposdinamicos(fieldCampo));
                });

                this.toogleFieldsetDadosProcesso.show(false);
                this.toogleFieldsetCamposDinamicos.show(true);

                js_removeObj('loading_message');
                js_divCarregando("Carregando respostas campos", 'loading_message');

                Promise.all(respostasCamposDinamicos).then(response => {

                    response.forEach(r => {

                        if (r == null) {
                            return;
                        }

                        r.field.setAttribute('data-p111_sequencial', r.resposta.codigo);
                        r.field.value = r.resposta.resposta;
                    });

                    js_removeObj('loading_message');

                }).catch(error => {

                    js_removeObj('loading_message');
                    return error.message ? alert(error.message) : alert(error)
                });

                return response;

            }).then(response => {

                if (!response.data || response.data.length == 0) {
                    return;
                }

                const campos = response.data.reverse();
                campos.forEach(c => c.campo.tipo == 'date' ? new DBInputDate(document.getElementById(c.campo.nomecam)) : null);

            }).catch(error => {

                js_removeObj('loading_message');
                return error.message ? alert(error.message) : alert(error)
            });
        };
        this.atualizarContadorGridProcessos = function () {
            var linhasVisiveis = document.querySelectorAll('#gridProcessos .body-container tr:not([hidden])');
            var contador = document.querySelector("span[id*='numrows']");
            contador.innerHTML = linhasVisiveis.length;
        };
        this.filtrar = function () {
            for (var _i = 0, _a = document.getElementsByClassName('filtro'); _i < _a.length; _i++) {
                var element = _a[_i];
                element.onkeyup = element.onchange = element.onpaste = element.oncut = _this.delay(function () {
                    _this.aplicarFiltros();
                }, 500);
            }
        };
        this.aplicarFiltros = function () {
            var filtros = [
                {
                    field: _this.filtroData,
                    value: _this.filtroData.value.trim().replace('/  /', '') ? _this.filtroData.value : '',
                    id: 'data'
                },
                {
                    field: _this.filtroDescricao,
                    value: _this.filtroDescricao.value,
                    id: 'descricao'
                },
                {
                    field: _this.filtroRequerente,
                    value: _this.filtroRequerente.value,
                    id: 'requerente'
                },
                {
                    field: _this.filtroProcesso,
                    value: _this.filtroProcesso.value,
                    id: 'processo'
                },
                {
                    field: _this.filtroMensagem,
                    value: _this.filtroMensagem.checked,
                    id: 'mensagem'
                }
            ];
            _this.filtroStatus.map(function (filtro) {
                filtros.push({
                    field: filtro,
                    value: filtro.checked,
                    id: 'status'
                });
            });

            _this.filtroTipoProcesso.map(function (filtro) {
                filtros.push({
                    field: filtro,
                    value: filtro.checked,
                    id: 'tipoprocesso'
                });
            });

            _this.filtroResponsavel.map(function (filtro) {
                filtros.push({
                    field: filtro,
                    value: filtro.checked,
                    id: 'responsavel'
                });
            });

            var possuiFiltro = false;
            for (var _i = 0, filtros_1 = filtros; _i < filtros_1.length; _i++) {
                var filtro = filtros_1[_i];
                if (filtro.value) {
                    possuiFiltro = true;
                    break;
                }
            }
            if (!possuiFiltro) {
                _this.gridProcessos.getCollection().get().map(function (row) {
                    row.datagridRow.getElement().show();
                    row.datagridRow.getElement().removeAttribute('hidden');
                });
                _this.atualizarContadorGridProcessos();
                return;
            }
            var totalLinhas = 0;
            _this.gridProcessos.getCollection().get().map(function (row) {

                var exibirLinha = true;
                var _loop_1 = function (filtro) {
                    if (!filtro.value) {
                        return "continue";
                    }
                    if (filtro.id === 'responsavel') {
                        var codigosResponsavel_1 = [];
                        _this.filtroResponsavel.map(function (filtro) {
                            if (filtro.checked) {
                                codigosResponsavel_1.push(parseInt(filtro.getAttribute('data-responsavel')));
                            }
                        });
                        if (codigosResponsavel_1.indexOf(parseInt(row.datagridRow.itemCollection.parausuariologado)) == -1) {
                            exibirLinha = false;
                        }
                    } else if (filtro.id === 'tipoprocesso') {
                        var codigosTipoProcesso_1 = [];
                        _this.filtroTipoProcesso.map(function (filtro) {
                            if (filtro.checked) {
                                codigosTipoProcesso_1.push(parseInt(filtro.getAttribute('data-tipoprocesso')));
                            }
                        });
                        if (codigosTipoProcesso_1.indexOf(parseInt(row.datagridRow.itemCollection.flag_processo_eletronico)) == -1) {
                            exibirLinha = false;
                        }
                    } else if (filtro.id === 'status') {
                        var codigosStatus_1 = [];
                        _this.filtroStatus.map(function (filtro) {
                            if (filtro.checked) {
                                codigosStatus_1.push(parseInt(filtro.getAttribute('data-codigo')));
                            }
                        });
                        if (codigosStatus_1.indexOf(parseInt(row.datagridRow.itemCollection.codigostatus)) == -1) {
                            exibirLinha = false;
                        }
                    } else if (filtro.id === 'mensagem') {
                        if (row.datagridRow.itemCollection.mensagens_nao_lidas < 1 || !row.datagridRow.itemCollection.mensagens_nao_lidas) {
                            exibirLinha = false;
                        }
                    } else if (_this.removeAccents(row.datagridRow.itemCollection[filtro.id]).search(new RegExp(_this.removeAccents(filtro.value), 'i')) == -1) {
                        exibirLinha = false;
                    }
                };

                for (var _i = 0, filtros_2 = filtros; _i < filtros_2.length; _i++) {
                    var filtro = filtros_2[_i];
                    _loop_1(filtro);
                }
                if (exibirLinha) {
                    totalLinhas = totalLinhas + 1;
                    row.datagridRow.getElement().show();
                    row.datagridRow.getElement().removeAttribute('hidden');
                } else {
                    row.datagridRow.getElement().hide();
                    row.datagridRow.getElement().setAttribute('hidden', 'hidden');
                }
            });
            _this.atualizarContadorGridProcessos();
        };
        this.removeAccents = function (string) {
            return string.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
        };
        this.delay = function (callback, ms) {
            var timer = 0;
            return function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply();
                }, ms || 0);
            };
        };
        this.exibe = function () {
            _this.windowAux.setContent(_this.html);
            _this.windowAux.zIndex = 1;
            _this.windowAux.show();
            _this.criaGridAnexos();
            _this.criaGridDespachosAnteriores();
            _this.adicionaAcoes();
            _this.camposDinamicos();
            _this.organizaDados();
            _this.organizaDocumentos();
            _this.organizaDespachosAnteriores();

            _this.despachosAnteriores.map(function (despacho) {
                _this.processo.documentosSolicitadosAssinatura.map(function (documento) {
                    if (despacho.codigo === documento[0]['codigo']) {
                        if (documento[0]['id_usuario'] == localStorage.getItem("idUsuario") && documento[0]['data_assinatura'] === null) {
                            var idBtn = "action_assinar_" + despacho.codigo;
                            var botaoAssinatura = document.getElementById(idBtn);
                            botaoAssinatura.classList.add("pulsacao");
                            botaoAssinatura.style.color = 'rgb(44, 122, 254)';
                        }
                    }
                });
            });
            _this.isMensagemProcessoEletronico();
        };

        this.exibeWindowTransferencia = function () {
            _this.windowTransferencia.setContent(_this.windowTransferenciaHTML);
            _this.windowTransferencia.zIndex = 1;
            _this.windowTransferencia.show();
            _this.organizaDepartamentos();
            _this.organizaInstituicoes();
            _this.organizaUsuariosDepartamento();
            var selectInstituicoes = document.getElementById('instituicao_destino');
            var selectDepartamentos = document.getElementById('departamento_destino');
            var selectUsuariosDepartamento = document.getElementById('recebimento_destino');

            selectInstituicoes.addEventListener('change', function (e) {
                selectDepartamentos.innerHTML = '';
                selectUsuariosDepartamento.innerHTML = '';
                instituicao = _this.instituicoes.find(instituicao => instituicao.codigo === e.target.value);

                if (instituicao) {
                    _this.setDepartamentos(instituicao.departamentos);
                    _this.organizaDepartamentos();
                }
            });
            selectDepartamentos.addEventListener('change', function () {
                selectUsuariosDepartamento.innerHTML = '';
                var data = new FormData();
                js_divCarregando("Buscando usuários para o departamento selecionado", 'loading_message_depart');
                data.append('json', "{ 'icoddepto' : " + selectDepartamentos.value + "}");
                HttpClient.post('pro4_consusuariodeptoRPC.php', {body: data}).then(function (response) {
                    if (response.erro) {
                        alert(response.mensagem);
                        return;
                    }

                    _this.setUsuariosDepartamento(response);
                    _this.organizaUsuariosDepartamento();
                    js_removeObj('loading_message_depart');
                }).catch(() => {
                    js_removeObj('loading_message_depart');
                });
            });

            const btnConfirmarTransferencia = document.getElementById("btnConfirmarTransferencia");
            btnConfirmarTransferencia.addEventListener('click', function () {
                _this.transferir();
            });
            const btnCancelarTransferencia = document.getElementById("btnCancelarTransferencia");
            btnCancelarTransferencia.addEventListener('click', function () {
                _this.windowTransferencia.hide();
            });

        };

        this.isJanelaProcessosExternosFechada = function () {
            var janelaProcessosExternos = document.getElementById('Jandb_iframe_processo_externo');

            if (janelaProcessosExternos == null) {
                return true;
            }

            if (janelaProcessosExternos.style.display == 'none') {
                return true;
            }

            return false;
        }

        this.autoAtualizar = function () {
            setInterval(function () {
                if (!_this.windowAux.getElement().visible()
                    && _this.isJanelaProcessosExternosFechada()
                ) {
                    var ultimaTransferencia = _this.buscarUltimaTransferencia();
                    _this.buscaOuvidoria(_this.ultimoSequencialOuvidoria);
                    _this.atualizar(ultimaTransferencia);
                }
            }, _this.timerAtualizacao * 1000);
        };
        this.criaGridAnexos();
        this.criaGridDespachosAnteriores();

        function attTelaDespacho() {
            _this.windowAux.hide();
            _this.abreAndamentoProcesso(_this.processo);
        }

        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    var elemento = document.getElementById('fechardb_assinador_documentos');
                    if (elemento) {
                        elemento.addEventListener('click', attTelaDespacho);
                        observer.disconnect();
                    }
                }
            });
        });

        var observerConfig = {
            childList: true,
            subtree: true
        };

        var targetNode = document.body;

        observer.observe(targetNode, observerConfig);
    }

    AndamentoProcesso.prototype.adicionarAcaoAtualizar = function () {
        var _this = this;
        this.btnAtualizar.addEventListener('click', function () {
            _this.buscaOuvidoria(null);
            _this.buscaProcessos();
        });
        this.btnAtualizarAno.addEventListener('click', function (event) {
            event.preventDefault();
            if (_this.anoProcesso.value === '') {
                alert('Insira o ano do processo!');
                return;
            }
            _this.buscaOuvidoria(null);
            _this.gridProcessos.clear();
            _this.buscaProcessosAno(_this.anoProcesso.value);
        })
    };
    AndamentoProcesso.STATUS_A_RECEBER = 1;
    AndamentoProcesso.STATUS_RECEBIDO = 2;
    AndamentoProcesso.STATUS_DESPACHADO = 3;
    AndamentoProcesso.STATUS_EXTERNO = 4;
    return AndamentoProcesso;
}());
