var DBViewAssinaturaPortaria = {
    requestRPC: 'rh_processaassinaturadigital.RPC.php',
    oCollectionPortarias: null,
    oGridPortarias: null,
    centificados: [],
    status_extension: false,
    codigoRelatorio: 1000058,
    pin: null,
    windowAlterarSituacaoPortaria: null,
    dataitens: [],
    dataitensSigned: [],
    dataitensErrorOnSign: [],

    init: function () {
        SignerSDK = window['SignerSDK'] || {};

        DBViewAssinaturaPortaria.makeGrid();
        DBViewAssinaturaPortaria.addEvents();

        window.DBViewAssinaturaPortaria = DBViewAssinaturaPortaria;
    },
    makeGrid: function () {

        DBViewAssinaturaPortaria.oCollectionPortarias = new Collection().setId("sId");
        DBViewAssinaturaPortaria.oGridPortarias       = new DatagridCollection(
            DBViewAssinaturaPortaria.oCollectionPortarias
        );

        var idColumn = null;
        var columns  = {
            "sId":                          { label: "Id",
                                              align: "center",
                                              width: "1%"
                                            },
            "sAno":                         { label: "sAno",
                                              align: "center",
                                              width: "1%"
                                            },
            "sPortaria":                    { label: "Portaria",
                                              align: "center",
                                              width: "7%"
                                            },
            "sMatricula":                   { label: "Matrícula",
                                              align: "center",
                                              width: "5%"
                                            },
            "sNome":                        { label: "Nome",
                                              align: "left",
                                              width: "25%"
                                            },
            "sCargo":                       { label: "Cargo",
                                              align: "left",
                                              width: "10%"
                                            },
            "sTipo":                        { label: "Tipo Portaria",
                                              align: "left",
                                              width: "15%"
                                            },
            "sInformacoes":                 { label: "Informações",
                                              align: "left",
                                              width: "10%"
                                            },
            "sData":                        { label: "Data",
                                              align: "center",
                                              width: "7%"
                                            },
            "sSituacaoPortariaCompleto":    { label: "Situação",
                                              align: "center",
                                              width: "7%"
                                            },
        }

        for (idColumn in columns) {
            DBViewAssinaturaPortaria.oGridPortarias.addColumn(idColumn, columns[idColumn]);
        }

        DBViewAssinaturaPortaria.oGridPortarias.addAction(
            'V',                        //label
            'Visualizar',               //title
            callbackBotaoVisualizar,
            true,                       //asButton
            'fa-file',                  //withIcon
        );
        DBViewAssinaturaPortaria.oGridPortarias.addAction(
            'S',                        //label
            'Alterar Situação',         //title
            callbackBotaoAlterarSituacao.bind(DBViewAssinaturaPortaria),
            true,                       //asButton
            'fa-ellipsis-v',            //withIcon
        );

        DBViewAssinaturaPortaria.oGridPortarias.grid.setCheckbox(1);
        DBViewAssinaturaPortaria.oGridPortarias.configure({
            order: false,
            height: 300
        });
        DBViewAssinaturaPortaria.oGridPortarias.hideColumns([1, 2]);
        DBViewAssinaturaPortaria.oGridPortarias.show($('ctnGrid'));
        DBViewAssinaturaPortaria.oGridPortarias.grid.setCallbackSingle(function(checkBox, id, item) {

            var itensSelecionados = DBViewAssinaturaPortaria.oGridPortarias.grid.getSelection('object')
            document.querySelectorAll('.collection_button').forEach(node => node.removeAttribute('disabled'));

            if(itensSelecionados.length > 0) {

                document.querySelectorAll('.collection_button').forEach(node => node.setAttribute('disabled', true));

                itensSelecionados.forEach(item => {

                    document.querySelector('#action_v_' + item.itemCollection.sId).removeAttribute('disabled')

                    if(itensSelecionados.length == 1) {
                        document.querySelector('#action_s_' + item.itemCollection.sId).removeAttribute('disabled')
                    }
                });
            }
        });

        DBViewAssinaturaPortaria.getPortarias();
    },
    getPortarias: function () {

        var filter = {
            sTipoPortaria    : document.querySelector('#h31_portariatipo').value,
            sAno             : document.querySelector('#anousu').value,
            sPortariainicial : document.querySelector('#porti').value,
            sPortariafinal   : document.querySelector('#portf').value,
            cSituacao        : document.querySelector('#situacaoPortarias').value,
        }

        const parametros = new FormData();
              parametros.append('exec',     'buscaPortarias');
              parametros.append('filter',   JSON.stringify(filter));

        js_divCarregando('Buscando portarias', 'loading_message');

        return fetch(DBViewAssinaturaPortaria.requestRPC, {
            method: 'POST',
            body: parametros,
            credentials: 'include',
        }).then(function (response) {
            return response.json();
        }).then(function (response) {

            js_removeObj('loading_message');

            if (response.erro) {
                return alert(response.mensagem);
            }

            var selectSituacao = document.querySelector('#situacaoPortarias');
            var situacao       = JSON.parse(parametros.get('filter'));

            DBViewAssinaturaPortaria.buildGridToResponse(response);

        }).catch(function (erro) {

            js_removeObj('loading_message');
            console.error(erro);
        });
    },
    buildGridToResponse: function (response) {

        var  limiteTamanhoInformacoes = 10
            ,limiteTamanhoTipo        = 18
            ,limiteTamanhoCargo       = 14
            ,limiteTamanhoNome        = 35
            ,limiteTamanhoSituacao    = 10
        ;

        DBViewAssinaturaPortaria.oCollectionPortarias.clear();
        DBViewAssinaturaPortaria.oGridPortarias.reload();

        response.portarias.forEach(function (oPortaria) {

            var portaria = {
                sId                         : oPortaria.codigo,
                sPortaria                   : oPortaria.portaria,
                iTotalAssinaturas           : oPortaria.total_assinaturas,
                sMatricula                  : oPortaria.matricula,
                sNome                       : oPortaria.nomeservidor,
                sNomeCompleto               : oPortaria.nomeservidor,
                sCargo                      : oPortaria.cargoservidor,
                sCargoCompleto              : oPortaria.cargoservidor ? oPortaria.cargoservidor : '',
                sData                       : oPortaria.dataportaria,
                sSituacaoPortaria           : oPortaria.situacao,
                sSituacaoPortariaCompleto   : retornarLabelSitucao(oPortaria.situacao),
                lStatusAssinatura           : oPortaria.status_assinatura ? oPortaria.status_assinatura : false,
                aAssinaturas                : oPortaria.status_assinatura ? oPortaria.assinaturas : null,
                sAno                        : oPortaria.anoportaria,
                sInformacoes                : oPortaria.informacoes,
                sInformacoesCompleto        : oPortaria.informacoes,
                sTipo                       : oPortaria.tipoportaria,
                sTipoCompleto               : oPortaria.tipoportaria,
                iIdestorage                 : null,
                sUrl                        : null,
                assinante : oPortaria.assinante
            };

            if((portaria.sSituacaoPortariaCompleto.length > limiteTamanhoSituacao)) {
                portaria.sSituacaoPortariaCompleto = portaria.sSituacaoPortariaCompleto.substr(0, limiteTamanhoSituacao) + '...';
            }

            if((portaria.sInformacoesCompleto.length > limiteTamanhoInformacoes)) {
                portaria.sInformacoes = portaria.sInformacoesCompleto.substr(0, limiteTamanhoInformacoes) + '...';
                portaria.sInformacoes = portaria.sInformacoes.replace(/\n/g, "<br/>");
            }

            if((portaria.sTipoCompleto.length > limiteTamanhoTipo)) {
                portaria.sTipo = portaria.sTipoCompleto.substr(0, limiteTamanhoTipo) + '...';
            }

            if((portaria.sCargoCompleto.length > limiteTamanhoCargo)) {
                portaria.sCargo = portaria.sCargoCompleto.substr(0, limiteTamanhoCargo) + '...';
            }

            if((portaria.sNomeCompleto.length > limiteTamanhoNome)) {
                portaria.sNome = portaria.sNomeCompleto.substr(0, limiteTamanhoNome) + '...';
            }

            if(oPortaria.idestorage instanceof Array && oPortaria.idestorage.length > 0) {
                portaria.iIdestorage = oPortaria.idestorage.first();
            }

            if(oPortaria.url instanceof Array && oPortaria.url.length > 0) {
                portaria.sUrl = oPortaria.url.first();
            }

            DBViewAssinaturaPortaria.oCollectionPortarias.add(portaria);
        });

        DBViewAssinaturaPortaria.oGridPortarias.reload();
        DBViewAssinaturaPortaria.oGridPortarias.collection.get().forEach(function (item, i) {

            if((item.sSituacaoPortariaCompleto.length > limiteTamanhoSituacao)) {
                DBViewAssinaturaPortaria.oGridPortarias.grid.setHint(i, 10,  retornarLabelSitucao(item.sSituacaoPortaria));
            }

            if((item.sInformacoesCompleto.length > limiteTamanhoInformacoes)) {
                DBViewAssinaturaPortaria.oGridPortarias.grid.setHint(i, 8,  item.sInformacoesCompleto);
            }

            if((item.sTipoCompleto.length > limiteTamanhoTipo)) {
                DBViewAssinaturaPortaria.oGridPortarias.grid.setHint(i, 7,  item.sTipoCompleto);
            }

            if((item.sCargoCompleto.length > limiteTamanhoCargo)) {
                DBViewAssinaturaPortaria.oGridPortarias.grid.setHint(i, 6,  item.sCargoCompleto);
            }

            if((item.sNomeCompleto.length > limiteTamanhoNome)) {
                DBViewAssinaturaPortaria.oGridPortarias.grid.setHint(i, 5,  item.sNomeCompleto);
            }

            if(item.aAssinaturas == null) {
                return;
            }

            var assinantes = 'Assinado por: <br/>';

            item.aAssinaturas.forEach(assinatura => assinantes += assinatura.nome + "<br/>");

            DBViewAssinaturaPortaria.oGridPortarias.grid.setHint(i, 10, assinantes);
        })
    },
    getFilePortaria: function (url, idestorage) {
        const parametros = new FormData();
                parametros.append('exec',       'getArquivoEstorage');
                parametros.append('idestorage', idestorage);

        return fetch('rh_processaassinaturadigital.RPC.php', {

            method     : 'POST',
            body       : parametros,
            credentials: 'include',

        }).then(function (response) {
            return response.json();
        }).then(function (response) {

            if (response.erro) {
                return alert(response.mensagem.urlDecode());
            }

            var sCaminhoDownloadArquivo = response.path;

            window.open("db_download.php?arquivo=" + sCaminhoDownloadArquivo);
        });
    },
    getPackageToSend: function () {

        var package = [];
        var itens = DBViewAssinaturaPortaria.oGridPortarias.grid.getSelection('object');

        itens.forEach(function (selectedObject) {

            if (!selectedObject.isSelected) {
                return;
            }

            package.push(selectedObject.itemCollection);
        });

        return package;
    },
    getPin: function () {

        return new Promise(function(resolve, reject) {

            top.alertify.prompt("Informe a senha do certificado.", function(ok, pin) {
                if (!ok) {
                    return reject(false);
                }
                if (!pin) {
                    CurrentWindow.focus();
                    alert("Senha não poder estar em branco.");
                    return reject(false);
                }
                this.pin = pin;
                resolve(pin);
            }.bind(this));

            var input = top.document.querySelector('.alertify-prompt input');
            if (input) {
                input.type = 'password';
                input.focus();
            }
        }.bind(this));
    },
    sign: function (filesToSign) {

        this.dataitens            = filesToSign || this.getPackageToSend();
        this.dataitensSigned      = new Map();
        this.dataitensErrorOnSign = new Map();

        if (!this.status_extension) {
            alert('Extensão não instalada ou não foi possível obter a versão do assinador.');
            return;
        }

        if (this.dataitens.length == 0) {
            alert('Selecione ao menos uma portaria.');
            return;
        }

        js_divCarregando('Confirmando dados do usuário', 'loading_message');

        const parametrosConsultaUsuario = new FormData();
              parametrosConsultaUsuario.append('exec', 'getInformacoesUsuario');

        fetch(DBViewAssinaturaPortaria.requestRPC, {
            method      : 'POST',
            body        : parametrosConsultaUsuario,
            credentials : 'include'
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (response) {

            if(response.erro) {
                throw new Error(response.mensagem);
            }

            var usuario = response.usuario;
        }.bind(this))
        .catch(function(err) {
            js_removeObj('loading_message');
            console.log('metodo sign() - fetch dados usuario', parametrosConsultaUsuario);
            console.error(err);
            alert(err);
        });
    },
    signFile: async function(certificate, oPortaria) {

        var erroPathFile = null, pathFile = null;

        pathFile = await getFilePathToSign(oPortaria)
                            .then(path => pathFile = path)
                            .catch(err => {
                                erroPathFile  = "Nao foi possível obter o arquivo do e-Storage para assinatura.\n";
                                erroPathFile += err +"\n";
                            });

        return new Promise((res, rej) => {

            if (erroPathFile !== null) {
                return rej({portaria: oPortaria.sPortaria, error: erroPathFile});
            }


            var cpfCertificado = certificate.subject.replace(/^.*?:(.*)$/g, "$1");

            if(oPortaria.lStatusAssinatura) {

                var documentoJaAssinado;

                oPortaria.aAssinaturas.forEach((assinatura) => (assinatura.cpf == cpfCertificado) ? documentoJaAssinado = true : documentoJaAssinado = false)

                if(documentoJaAssinado) {
                    return rej({portaria: oPortaria.sPortaria, error: 'Documento já assinado'});
                }
            }

            var config = {
                method     : 'GET',
            };

            if(oPortaria.lStatusAssinatura && oPortaria.aAssinaturas.length == oPortaria.iTotalAssinaturas) {

                return this.alterarSituacao('S', oPortaria)
                           .then(response => rej({portaria: oPortaria.sPortaria, error: "Documento assinado por todas as pessoas."}))
                           .catch(error => rej({portaria: oPortaria.sPortaria, error: error}));
            }

            var signature = null;

            if(oPortaria.lStatusAssinatura && oPortaria.aAssinaturas.length == 1) {
                signature.position.x = 370;
            }

            var documento = null;
        })
    },
    fechar: function() {
        this.windowAlterarSituacaoPortaria.destroy();
    },
    alterarSituacao: function (situacao, portaria) {
        return new Promise((resolve, reject) => {
            var parametros = new FormData();
                parametros.append('exec',           'salvarSituacao');
                parametros.append('cSituacao',       situacao);
                parametros.append('sCodigoPortaria', portaria.sId);

            fetch(DBViewAssinaturaPortaria.requestRPC, {
                method: 'POST',
                body: parametros,
                credentials: 'include',
            }).then(function (response) {
                return response.json();
            }).then(response => {
                DBViewAssinaturaPortaria.oCollectionPortarias.remove(portaria.sId);
                DBViewAssinaturaPortaria.oGridPortarias.reload();
                resolve(response)
            })
            .catch(function (error) {
                console.error(error);
                reject(error);
            })
        })
    },
    refreshSignatureStatusFiles: function () {
        const parametros = new FormData();
              parametros.append('exec', 'atualizarStatusArquivos');

        const arquivos = DBViewAssinaturaPortaria.oCollectionPortarias.get()
        arquivos.forEach(arquivo => {
            if (!arquivo.iIdestorage) {
                return;
            }

            parametros.append('arquivo[]',  arquivo.iIdestorage);
        });

        if (!parametros.has('arquivo[]')) {
            return;
        }

        js_divCarregando('Atualizando portarias assinadas', 'loading_message');

        return fetch(DBViewAssinaturaPortaria.requestRPC, {
            method: 'POST',
            body: parametros,
            credentials: 'include',
        }).then(response => response.json())
          .then(function (response) {
            console.log('response on refreshSignatureStatusFiles', response)
            js_removeObj('loading_message');

            if (response.mensagem) {
                alert(response.mensagem);
            }

            if (response.erro && response.erro == true) {
                return;
            }
        }).catch(function (erro) {
            js_removeObj('loading_message');
            console.error(erro);
        });
    }
};

callbackBotaoVisualizar = function(target, portaria) {

    if(portaria.iIdestorage != null) {

        idestorage = portaria.iIdestorage;
        url        = portaria.sUrl;

        DBViewAssinaturaPortaria.getFilePortaria(url, idestorage)

    } else {

        js_divCarregando('Gerando documento', 'loading_message');

        gerarNovoDocumento(portaria)
        .then(path => {
            js_removeObj('loading_message');
            window.open(path)
        })
        .catch(err => {
            js_removeObj('loading_message');
            alert(err)
        });
    }
}

callbackBotaoAlterarSituacao = function (target, portaria) {

    var
         proximaSituacao  = null
        ,situacaoAnterior = null
    ;

    switch(portaria.sSituacaoPortaria) {

        case 'C': //Criada
        case 'D': //Devolvido para abertura
            proximaSituacao  = 'O';
            break;

        case 'O': //Conferido
        case 'F': //Devolvido para conferência
            proximaSituacao  = 'A';
            situacaoAnterior = 'D';
            break;

        case 'A': //Aguarda assinatura
            situacaoAnterior = 'F';
            proximaSituacao  = 'S';
            break;

        case 'S': //Assinado
            proximaSituacao  = 'I';
            break;

        case 'I': //Impresso
            situacaoAnterior = 'S';
            break;
    }

    this.windowAlterarSituacaoPortaria = new windowAux('AlterarSituacaoPortaria', 'Alterar Situação da Portaria', 450, 250);
    this.windowAlterarSituacaoPortaria.zIndex = 2;
    this.windowAlterarSituacaoPortaria.setContent('');
    this.windowAlterarSituacaoPortaria.setShutDownFunction(function () {
        DBViewAssinaturaPortaria.fechar();
    });

    this.windowAlterarSituacaoPortaria.show(null, null, true);
    this.windowAlterarSituacaoPortaria.getContentContainer().load('rh_processaassinaturadigitalalterasituacao.php', function () {

        var btnProximaSituacao    = document.querySelector('#proximaSituacaoIr');
        var btnSituacaoAnterior   = document.querySelector('#situacaoAnteriorIr');
        var inputNumeroPortaria   = document.querySelector('#numeroPortaria');
        var inputServidor         = document.querySelector('#servidor');
        var inputProximaSituacao  = document.querySelector('#proximaSituacao');
        var inputSituacaoAnterior = document.querySelector('#situacaoAnterior');

            inputProximaSituacao.value  = proximaSituacao != null  ? retornarLabelSitucao(proximaSituacao)  : '';
            inputSituacaoAnterior.value = situacaoAnterior != null ? retornarLabelSitucao(situacaoAnterior) : '';
            inputNumeroPortaria.value   = portaria.sId;
            inputServidor.value         = portaria.sMatricula +' - '+ portaria.sNome;

        if(proximaSituacao === null) {
            btnProximaSituacao.setAttribute('disabled', true);
        } else {
            btnProximaSituacao.addEventListener('click', function (event) {

                js_divCarregando('Alterando situação da portaria', 'loading_message');

                DBViewAssinaturaPortaria.alterarSituacao(proximaSituacao, portaria)
                .then(response => callbackAlteracaoSituacao(proximaSituacao, portaria, response));
            });
        }

        if(situacaoAnterior === null) {
            btnSituacaoAnterior.setAttribute('disabled', true);
        } else {
            btnSituacaoAnterior.addEventListener('click', function (event) {

                js_divCarregando('Alterando situação da portaria', 'loading_message');

                DBViewAssinaturaPortaria.alterarSituacao(situacaoAnterior, portaria)
                .then(response => callbackAlteracaoSituacao(situacaoAnterior, portaria, response));
            });
        }
    });
}

callbackAlteracaoSituacao = function (situacao, portaria, response) {

    DBViewAssinaturaPortaria.fechar();

    if(response.mensagem) {
        alert(response.mensagem);
    }

    if(response.erro) {
        return;
    }

    js_removeObj('loading_message');
}

retornarLabelSitucao = function (situacao) {

    var labelSituacao;

    switch(situacao){

        case 'C': //Criada
            labelSituacao = 'Criada';
            break;

        case 'D': //Devolvido para abertura
            labelSituacao = 'Devolvido para abertura';
            break;

        case 'O': //Conferido
            labelSituacao = 'Conferido';
            break;

        case 'F': //Devolvido para conferência
            labelSituacao = 'Devolvido para conferência';
            break;

        case 'A': //Aguarda assinatura
            labelSituacao = 'Aguarda assinatura';
            break;

        case 'S': //Assinado
            labelSituacao = 'Assinado';
            break;

        case 'I': //Impresso
            labelSituacao = 'Impresso';
            break;
    }

    return labelSituacao;
}

responseSignFiles = function (totalItens) {

    var errorsOnSignItens = this.dataitensErrorOnSign.size;
    var signedItens       = this.dataitensSigned.size;

    if( (signedItens + errorsOnSignItens) != totalItens) {
        return;
    }

    if(errorsOnSignItens > 0) {

        var msgErro = "Ocorreram erros ao assinar as portarias:\n\n";

        if (errorsOnSignItens > 20) {

            var portariasComErro = [], erros = [], errosOcorridos = new Map();

            this.dataitensErrorOnSign.forEach((value, index) => {
                portariasComErro.push(index);
                errosOcorridos.set(value);
            });

            errosOcorridos.forEach((v, e) => erros.push(e));

            msgErro += portariasComErro.join(', ');
            msgErro += "\n\n";
            msgErro += erros.join("\n");

        } else {
            this.dataitensErrorOnSign.forEach((value, index) => msgErro += imprimirMensagemRetorno(index, value));
        }


        alert(msgErro);
    }

    if(signedItens > 0) {

        var msgSucesso = "Portaria(s) assinadas com sucesso!\n\n";

        if (signedItens > 20) {

            var assinados = [];
            this.dataitensSigned.forEach((value, index) => assinados.push(index));
            msgSucesso += assinados.join(', ');

        } else {
            this.dataitensSigned.forEach((value, index) => msgSucesso += imprimirMensagemRetorno(index, value));
        }

        alert(msgSucesso);
    }

    this.getPortarias();
}

imprimirMensagemRetorno = function (index, value) {
    return "\n* "+ index +' - '+ value;
}

gerarNovoDocumento = async function(item) {

    var pathFile;
    var param = [
        {sNome: "$portaria", sValor: item.sPortaria},
        {sNome: "$ano",      sValor: item.sAno}
    ];

    const parametros = new FormData();
          parametros.append('exec',           'gerarArquivo');
          parametros.append('aParametros',     JSON.stringify(param));
          parametros.append('iCodRelatorio',   DBViewAssinaturaPortaria.codigoRelatorio);
          parametros.append('iCodigoPortaria', item.sId);

    return new Promise((resolve, reject) => {

        fetch(DBViewAssinaturaPortaria.requestRPC, {
            method       : 'POST',
            body         : parametros,
            credentials  : 'include'
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (response) {

            if(response.erro) {
                return reject(response.mensagem.urlDecode());
            }

            /**
             * @todo ajustar isto, não está atualizando a grid corretamente
             */
            var oItem = DBViewAssinaturaPortaria.oCollectionPortarias.get(item.sId);
                oItem.iIdestorage = response.idestorage;
                oItem.sUrl        = response.url;
            delete oItem.ID;

            DBViewAssinaturaPortaria.oCollectionPortarias.remove(item.sId);
            DBViewAssinaturaPortaria.oCollectionPortarias.add(oItem);
            DBViewAssinaturaPortaria.oGridPortarias.reload();

            /**
             * @todo rever o path que está sendo passado, não deve ser caminho absoluto do arquivo
             */
            var path  = location.protocol;
                path += '//';
                path += location.hostname;

                if(location.port) {

                    path += ':';
                    path += location.port;
                }

                if(location.pathname.replace(/(.*?)w\/\d+.*$/g, "$1").replace(/\/*/g, '')) {

                    path += '/';
                    path += location.pathname.replace(/(.*?)w\/\d+.*$/g, "$1").replace(/\/*/g, '');
                }

                path += '/';
                path += response.path;

            resolve(path);
        })
        .catch(function (erro) {
            console.error(erro);
            reject(erro);
        })
    });
}

getFilePathToSign = async function(item) {

    var pathFile;

    if(item.iIdestorage == null || item.iIdestorage == 'null') {
        await gerarNovoDocumento(item).then(path => pathFile = path);
        return pathFile;
    }

    if(item.sUrl != null && item.sUrl != 'null') {
        return item.sUrl;
    }

    const parametros = new FormData();
          parametros.append('exec',       'getArquivoEstorage');
          parametros.append('idestorage', item.iIdestorage);

    return new Promise((resolve, reject) => {

        fetch(DBViewAssinaturaPortaria.requestRPC, {
            method      : 'POST',
            body        : parametros,
            credentials : 'include'
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (response) {

            if(response.erro) {
                throw(response.mensagem.urlDecode());
            }

            /**
             * @todo rever o path que está sendo passado, não deve ser caminho absoluto do arquivo
             */
            var path  = location.protocol;
                path += '//';
                path += location.hostname;

            if(location.port) {

                path += ':';
                path += location.port;
            }

            if(location.pathname.replace(/(.*?)w\/\d+.*$/g, "$1").replace(/\/*/g, '')) {

                path += '/';
                path += location.pathname.replace(/(.*?)w\/\d+.*$/g, "$1").replace(/\/*/g, '');
            }

            path += '/';
            path += response.path;

            resolve(path);
        })
        .catch(function (erro) {
            console.error(erro);
            reject(erro);
        })
    });
}

DBViewAssinaturaPortaria.init();
