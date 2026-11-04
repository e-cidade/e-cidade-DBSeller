
function dbViewFormacaoProfissional(iFormacao, cgm, nome, oNode) {
    var iWidth           = document.body.getWidth()/1.4;
    this.iFormacao       = iFormacao;
    this.view            = '';

    if (oNode != null) {
        this.view = oNode;
    }

    this.arquivoPosgraduacao = '';
    this.arquivo = '';
    this.windowAvaliacao = new windowAux('wndAvaliacao'+iFormacao,'Formação Profissional', iWidth);
    var sContent  = '<body class="body-default">';
    sContent     += ' <div class="container" id="">';
    sContent     += '<div id="ctnFormulario">'
    sContent     += '<fieldset>';
    sContent     += '<form id="formFormacao" name="formFormacao">';
    sContent     += ' <legend>Formação do Profissional</legend>';
    sContent     += ' <table class="form-container">';
    sContent     += '  <tr>';
    sContent     += '    <td>';
    sContent     += '  <label for="">CGM / Profissional:</label>';
    sContent     += ' </td>';
    sContent     += '   <td> ';
    sContent     += '     <input type="hidden" name="ed183_id" id="ed183_id" value="">';
    sContent     += '     <input';
    sContent     += ' class="readonly"';
    sContent     += ' type="text"';
    sContent     += ' value='+cgm;
    sContent     += ' name="ed183_cgm" ';
    sContent     += ' id="ed183_cgm"';
    sContent     += ' style="width: 100px;"';
    sContent     += ' readonly ';
    sContent     += '   >';
    sContent     += '     <input';
    sContent     += '     type="text" name="nomeProfissional"';
    sContent     += '   id="nomeProfissional"';
    sContent     += ' value="'+nome+'"';
    sContent     += '   style="width: 755px;"';
    sContent     += ' disabled';
    sContent     += ' >';
    sContent     += ' </td>';
    sContent     += '  </tr>';
    sContent     += '  <tr>';
    sContent     += ' <td>';
    sContent     += '<label for="ed183_nomecurso">Nome do Curso:</label>';
    sContent     += ' </td>';
    sContent     += ' <td>';
    sContent     += '<input type="text" name="ed183_nomecurso" id="ed183_nomecurso" style="width: 658px;">';
    sContent     += ' </td>';
    sContent     += '</tr>';
    sContent     += ' <tr>';
    sContent     += ' <td>';
    sContent     += '<label for="ed183_tipoformacao">Tipo da Formação:</label>';
    sContent     += ' </td>';
    sContent     += ' <td>';
    sContent     += ' <select name="ed183_tipoformacao" id="ed183_tipoformacao"style="width: 175px;">';
    sContent     += ' <option value="">Selecione </option>';
    sContent     += ' </select>';
    sContent     += ' <label for="ed183_areaformacao">Área da Formação:</label>';
    sContent     += ' <select name="ed183_areaformacao" id="ed183_areaformacao"style="width: 175px;">';
    sContent     += ' <option value="">Selecione </option>';
    sContent     += ' </select>';
    sContent     += ' <label for="ed183_anoconclusao">Ano de Conclusão:</label>';
    sContent     += ' <input type="text"';
    sContent     += ' name="ed183_anoconclusao" id="ed183_anoconclusao" style="width: 80px;">';
    sContent     += ' </td>';
    sContent     += ' </tr>';

    sContent     += '<tr>';
    sContent     += '<td>';
    sContent     += '<br>';
    sContent     += '</td>';
    sContent     += '</tr>';
    sContent     += '<tr id="anexopos">';
    sContent     += '<td>';
    sContent     += '<b>Anexo da Pós graduação:</b>';
    sContent     += '</td>';
    sContent     += '<td>';
    sContent     += '<div style="display: flex; align-items: center; width:100%;">';
    sContent     += '<iframe name="frame_imagemPosgraduacao" id="frame_imagemPosgraduacao" src="edu4_alunodocumentoposgraduacao.php" width="56" height="50" frameborder="1" scrolling="no"></iframe>';
    sContent     += '<script> ';
    sContent     += 'frame_imagemPosgraduacao.location.href="edu4_alunodocumentoposgraduacao.php?imagem_gerada="'+this.arquivoPosgraduacao;
    sContent     += '</script>';
    sContent     += '<div style="display: flex;margin-top: -5px;flex-direction: column;width:100%;">';
    sContent     += '<iframe name="frame_posgraduacao" id="frame_posgraduacao" src="edu1_frameposgraduacao.php" width="100%" height="31" frameborder="0" scrolling="no" style="margin-bottom: 0px;margin-top:2px;"></iframe>';
    sContent     += '<input type="button" value="Excluir Imagem" id="ExcluirImagem"';
    sContent     += 'style="font-size: 9px;padding: 0px;margin-left: 3px;width:82px;" ';
    sContent     += '</div> ';
    sContent     += '<input name="oid_arquivoPosgraduacao" type="hidden" id="oid_arquivoPosgraduacao" value='+this.arquivo+'size="30">'
    sContent     += '</div> '
    sContent     += '</td>';
    sContent     += '</tr>';

    sContent     += ' </table>';
    sContent     += ' </form>';
    sContent     += ' </fieldset>';
    sContent     += ' <button id="salvar">';
    sContent     += ' <i class="fas fa-save"></i>';
    sContent     += ' Salvar';
    sContent     += ' </button>';
    sContent     += ' </div>';
    sContent     += ' </div>';
    sContent     += ' <div class="container" style="display:;" id="ctnGridFormacoes">';
    sContent     += ' <fieldset>';
    sContent     += ' <legend>Formacões do Profissional: <span id="spanNomeProfissional"></span></legend>';
    sContent     += ' <table id="grid-formacoes">';
    sContent     += ' </table>';
    sContent     += ' </fieldset>';
    sContent     += ' </div>';
    sContent     += '</body>';

    if (this.view === "") {
        this.windowAvaliacao.setContent(sContent);
      } else {
        oNode.innerHTML = sContent;
    }

    if (this.view == "") {
        this.windowAvaliacao.show();
    }
    const btnClose = this.windowAvaliacao.linkWindow;

    this.tableProfisiionais = jQuery('#table-profissionais');
    this.gridFormacoes = jQuery('#grid-formacoes');
    this.spanNomeProfissional = document.querySelector('#spanNomeProfissional');

    this.ctnFormulario = document.querySelector('#ctnFormulario');
    this.ctnBuscaProfissional = document.querySelector('#ctnBuscaProfissional');
    this.ctnGridFormacoes = document.querySelector('#ctnGridFormacoes');

    this.inputCGM = document.querySelector('#ed183_cgm');
    this.inputNome = document.querySelector('#nomeProfissional');
    this.inputNomeCurso = document.querySelector('#ed183_nomecurso');
    this.inputAnoConclusao = document.querySelector('#ed183_anoconclusao');
    this.inputId = document.querySelector('#ed183_id');

    this.arquivoPos = document.getElementById("oid_arquivoPosgraduacao");

    this.btnSalvar = document.querySelector('#salvar');

    this.selecTipoFormacao = document.querySelector('#ed183_tipoformacao');
    this.selectAreaFormacao = document.querySelector('#ed183_areaformacao');

    this.formulario = document.querySelector('#formFormacao');
    this.anexoPos = document.getElementById("anexopos");
    this.excluirImagem = document.getElementById("ExcluirImagem");

    this.routs = {
        buscarAreasFormacao: `educacao/censo/tabelas-censo/areas-pos-graduacao/`,
        buscarTiposFormacao: `educacao/censo/tabelas-censo/tipos-pos-graduacao/`,
        salvar: `educacao/escola/recursos-humanos/salvar-formacao-superior-profissional/`,
        buscarFormacoesDoProfissional: `educacao/escola/recursos-humanos/buscar-formacoes-superior-profissional/`,
        excluirFormacao: `educacao/escola/recursos-humanos/excluir-formacao-superior-profissional/`,
        buscarDocumentoPosGraduacao: `educacao/escola/recursos-humanos/buscar-documento-pos-graducao`,
        salvarDocumentoPosGraduacao: `educacao/escola/recursos-humanos/salvar-documento-pos-graducao`,
        excluirDocumentoPosGraduacao: `educacao/escola/recursos-humanos/excluir-documento-pos-graducao`
    }

    btnClose.addEventListener('click', function () {
        window.location.reload();
    });

    this.windowAvaliacao.addEvent('keydown', e => {
       if (e.key === 'Escape') {
           window.location.reload();
       }
    });

    this.buscaFormacoesProfissional = async (cgm) => {
        const formData = new FormData();
        formData.append('cgm', cgm);
        return await HttpClient.post(`${PHPSession.requestApi}/${this.routs.buscarFormacoesDoProfissional}`,{body: formData})
            .then(response => {
                if(response.error) {
                    alert(response.message);
                    return;
                }
                return response.data;
        })
    }

    this.carregaGridFormacoes = async (cgm) => {
        this.spanNomeProfissional.innerHTML = this.inputNome.value;
        const acoesEventos = {
            'click .alterar': async (e, value, row, index) => {
                this.inputId.value = row.ed183_id;
                this.inputNomeCurso.value = row.ed183_nomecurso;
                this.inputAnoConclusao.value = row.ed183_anoconclusao;
                Object.values(this.selecTipoFormacao.options).each(opt =>{
                    if (opt.value == row.ed183_tipoformacao) {
                        opt.selected = true;
                    }
                })

                Object.values(this.selectAreaFormacao.options).each(opt =>{
                    if (opt.value == row.ed183_areaformacao) {
                        opt.selected = true;
                    }
                })

                this.anexoPos.style.display = '';
                this.buscarArquivoPosGraduacao(row.ed183_id);
            },
            'click .excluir': async (e, value, row, index) => {
                if (confirm('Tem certeza que deseja excluir?')) {
                    const formData = new FormData();
                    formData.append('ed183_id', row.ed183_id);
                    HttpClient.post(`${PHPSession.requestApi}/${this.routs.excluirFormacao}`, {body: formData})
                        .then(async response => {
                            alert(response.message);
                            if(response.error) {
                                return;
                            }
                            let formacoes = await this.buscaFormacoesProfissional(this.inputCGM.value);
                            this.gridFormacoes.bootstrapTable('load', formacoes);
                    })
                }
            }
        }

        const formataCampos = (value, row, index, field) => {
            let template = "";
            if (field == 'ed183_tipoformacao'){
                Object.values(this.selecTipoFormacao.options).each(opt =>{
                    if (opt.value == value) {
                        template = opt.text;
                    }
                })
            } else if (field == 'ed183_areaformacao'){
                Object.values(this.selectAreaFormacao.options).each(opt =>{
                    if (opt.value == value) {
                        template = opt.text;
                    }
                })
            } else if (field == 'ed183_docpos_estorage'){
                template = "Não";
                if (value !== null) {
                    template = "Sim";
                }

            } else if (field == 'acoes') {
                template =  `<a class="alterar" href="javascript:void(0)" title="Alterar">
                                <i class="fa fa-edit"></i>
                            </a>
                            &nbsp;&nbsp;
                            <a class="excluir" href="javascript:void(0)" title="Excluir">
                                <i class="fas fa-trash-alt"></i>
                            </a>`;
            }
            return template;
        };

        colunas = [
            {
                title: 'Nome do Curso',
                field: 'ed183_nomecurso',
                halign: 'center',
                align: 'right',
                width: '300',
            },
            {
                title: 'Tipo de Formação',
                field: 'ed183_tipoformacao',
                halign: 'center',
                align: 'right',
                width: '200',
                formatter: formataCampos,
            },
            {
                title: 'Área da Formação',
                field: 'ed183_areaformacao',
                halign: 'center',
                align: 'right',
                width: '400',
                formatter: formataCampos,
            },
            {
                title: 'Ano de Conclusão',
                field: 'ed183_anoconclusao',
                halign: 'center',
                align: 'right',
                width: '100',
            }
            ,
            {
                title: 'Anexo da Pós graduação',
                field: 'ed183_docpos_estorage',
                halign: 'center',
                align: 'right',
                width: '100',
                formatter: formataCampos,
            },
            {
                title: 'Ações',
                field: 'acoes',
                halign: 'center',
                align: 'right',
                width: '100',
                formatter: formataCampos,
                events: acoesEventos
            }
        ]

        this.gridFormacoes.bootstrapTable({
            columns: colunas,
            uniqueId: "ed183_id",
            locale: 'pt-BR',
            cache: false,
            search: true,
            class: "table table-sm",
            pageSize: 10,
            pagination: true,
        });

        let formacoes = await this.buscaFormacoesProfissional(cgm);
        this.gridFormacoes.bootstrapTable('load', formacoes);
        this.ctnGridFormacoes.style.display = '';
    }

    this.buscarArquivoPosGraduacao = (ed183_id) => {
        const formData = new FormData();
        formData.append('ed183_id', ed183_id);
        HttpClient.post(`${PHPSession.requestApi}/${this.routs.buscarDocumentoPosGraduacao}`, {body: formData})
            .then(async response => {
                if(response.error) {
                    alert(response.message);
                    return;
                }
                document.getElementById('oid_arquivoPosgraduacao').value = response.data.arquivo;
                frame_imagemPosgraduacao.location.href="edu4_alunodocumentoposgraduacao.php?imagem_gerada="+response.data.arquivoPosgraduacao;
        });
    }

    this.salvarArquivoPosGraduacao = (ed183_id, arquivo_id) => {
        const formData = new FormData();
        formData.append('ed183_id', ed183_id);
        formData.append('arquivo_id', arquivo_id);
        HttpClient.post(`${PHPSession.requestApi}/${this.routs.salvarDocumentoPosGraduacao}`, {body: formData})
            .then(async response => {
                if(response.error) {
                    alert(response.message);
                    return;
                }
                else{
                    frame_imagemPosgraduacao.location.href="edu4_alunodocumentoposgraduacao.php?imagem_gerada="+"";
                    document.getElementById('oid_arquivoPosgraduacao').value = "";
                }
        })
    }

    this.excluirArquivoPosGraduacao = () => {
        const formData = new FormData();
        formData.append('ed183_id', this.inputId.value);
        HttpClient.post(`${PHPSession.requestApi}/${this.routs.excluirDocumentoPosGraduacao}`, {body: formData})
            .then(async response => {
                if(response.error) {
                    alert(response.message);
                    return;
                }
        })
        frame_imagemPosgraduacao.location.href="edu4_alunodocumentoposgraduacao.php?imagem_gerada="+"";
    }

    this.carregaDadosSelects = async () => {
        await HttpClient.get(`${PHPSession.requestApi}/${this.routs.buscarTiposFormacao}`)
            .then(async response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                Object.keys(response.data).each((chave) => {
                    this.selecTipoFormacao.add(new Option(response.data[chave], chave));
                })
        });

        await HttpClient.get(`${PHPSession.requestApi}/${this.routs.buscarAreasFormacao}`)
            .then(async response => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                Object.keys(response.data).each((chave) => {
                    this.selectAreaFormacao.add(new Option(response.data[chave], chave));
                })
        });
    }

    this.carregaEventos = async () => {
        this.btnSalvar.addEventListener('click', event => {
            event.preventDefault();
            if (this.formulario.ed183_cgm.value == '') {
                alert('Campo CGM deve ser informado!');
                return;
            }
            if (this.formulario.ed183_tipoformacao.value == '') {
                alert('Tipo de Formação deve ser informado!');
                return;
            }
            if (this.formulario.ed183_areaformacao.value == '') {
                alert('Área ds Formação deve ser informada!');
                return;
            }
            if (this.formulario.ed183_anoconclusao.value == '') {
                alert('Áno de conclusão deve ser informado!');
                return;
            }

            const formData = new FormData(this.formulario);

            HttpClient.post(`${PHPSession.requestApi}/${this.routs.salvar}`, {body: formData}).then(async response => {
                alert(response.message);
                if(response.error) {
                    return;
                }

                if (this.arquivoPos.value != undefined && this.arquivoPos.value != 'size="30"') {
                    this.salvarArquivoPosGraduacao(response.data, this.arquivoPos.value);
                }

                this.inputId.value = "";
                this.inputNomeCurso.value = "";
                this.inputAnoConclusao.value = "";
                Object.values(this.selecTipoFormacao.options).each(opt =>{
                    if (opt.value == "") {
                        opt.selected = true;
                    }
                })

                Object.values(this.selectAreaFormacao.options).each(opt =>{
                    if (opt.value == "") {
                        opt.selected = true;
                    }
                })

                let formacoes = await this.buscaFormacoesProfissional(this.inputCGM.value);
                this.gridFormacoes.bootstrapTable('load', formacoes);
            })
        });
    }

    this.excluirImagem.addEventListener("click",
        event => {
            event.preventDefault();
            this.excluirArquivoPosGraduacao();
        }
    );

    this.listen = async () => {
    await PHPSession.loadData();
    await this.carregaDadosSelects();
    await this.carregaGridFormacoes(cgm);
    await this.carregaEventos();
    }

    this.listen();
}
