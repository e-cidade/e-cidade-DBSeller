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

/**
 * Monta uma view utilizando windowAux para configurar o organograma.
 * @param {integer} codigoInstituicao
 * @param {integer} codigoDepartamento
 *
 * @requires scripts/widgets/windowAux.widget.js
 * @requires scripts/widgets/DBTreeView.widget.js
 * @requires scripts/classes/http/http.js
 * @requires scripts/session.js
 */
const ConfigurarOrganograma = function(codigoInstituicao, codigoDepartamento) {

    let wndWidth = screen.availWidth / 2;
    let wndHeight = screen.availHeight / 2;
    const window = new windowAux('Organograma', 'Configurar Organograma', wndWidth, wndHeight);
    const treeView = new DBTreeView('TreeViewOrganograma');
    const rotas = {
        get: 'configuracao/organograma',
        salvar: 'configuracao/organograma/salvar'
    }

    const montaTreeView = (div) => {
        treeView.show(div);
        treeView.allowFind(true);
        treeView.setFindOptions('hint');
    }

    /**
     * Monta os Elementos HTML da View
     */
    const montaWindow = () => {
        const divWindowAux = document.createElement('div');
        const divAlert = document.createElement('div');
        const divSubContainer = document.createElement('div');
        const divTreeView = document.createElement('div');
        const divBtn = document.createElement('div');
        const fieldset = document.createElement('fieldset');
        const table = document.createElement('table');
        const fieldsetTreeView = document.createElement('fieldset');
        const labelDescricao = document.createTextNode('Descrição: ');
        const inputDescricao = document.createElement('input');
        const labelAssociado = document.createTextNode('Associado: ');
        const inputAssociado = document.createElement('input');
        const btnSalvar = document.createElement('button');

        let li = document.createElement('li');
        li.innerHTML = `Selecione o Departamento desejado e clique em
            <kbd><i class="fas fa-save"></i> Salvar</kbd> para vincular o departamento;`;

        divAlert.classList.add('alert', 'alert-primary');
        divAlert.setAttribute('role', 'alert');
        divAlert.setAttribute('style', 'height: 25px;');
        divAlert.appendChild(li);

        divSubContainer.setAttribute('rel', 'ignore-css');
        divSubContainer.classList.add('container');

        divTreeView.style.height = (wndHeight - 200);

        divBtn.classList.add('subcontainer');

        fieldsetTreeView.classList.add('separator');

        inputDescricao.setAttribute('type', 'text');
        inputDescricao.setAttribute('id', 'descricao');

        inputAssociado.setAttribute('type', 'checkbox');
        inputAssociado.setAttribute('id', 'associado');

        btnSalvar.setAttribute('id', 'btnSalvar');
        btnSalvar.innerHTML = '<i class="far fa-save"></i> Salvar';
        btnSalvar.addEventListener('click', salvar);


        let tr = document.createElement('tr');
        let td = document.createElement('td');

        td.appendChild(labelDescricao);
        td.appendChild(inputDescricao);
        tr.appendChild(td);

        td = document.createElement('td');

        td.appendChild(labelAssociado);
        td.appendChild(inputAssociado);
        tr.appendChild(td);

        table.appendChild(tr);

        fieldsetTreeView.appendChild(divTreeView);
        fieldset.append(table);
        fieldset.appendChild(fieldsetTreeView);

        montaTreeView(divTreeView);

        divSubContainer.appendChild(fieldset);

        divBtn.appendChild(btnSalvar);

        divWindowAux.appendChild(divAlert);
        divWindowAux.appendChild(divSubContainer);
        divWindowAux.appendChild(divBtn);

        window.setContent(divWindowAux);
    }

    const loadData = () => {
        PHPSession.loadData().then(() => {
            let rota = `${PHPSession.requestApi}/${rotas.get}/${codigoInstituicao}/${codigoDepartamento}`;
            HttpClient.get(rota).then(response => {
                if (response.error) {
                    return alert(response.message);
                }
                montaOrganograma(response.data, response.data.departamentopai);
            });
        });
    }

    /**
     * evento do Click no checkbox. Somente um checkbox pode estar selecionado.
     * @param {DBTreeView} node
     * @param {Event} event
     */
    const eventoCheckBox = (node, event) => {
        const checkeds = document.querySelectorAll('.marker-checked');
        checkeds.forEach(checked => {
            checked.setAttribute('class', 'marker');
        });

        if (node.checkbox.checked) {
            node.uncheckAll(event);
            treeView.aNodes.each(no => {
                no.uncheckAll(event);
            });
            treeView.setChecked(event, event.target);
        }
    }

    /**
     * Monta a TreeView de acordo com o Objeto passado por parametro
     * @param {Object} data
     * @param {integer} parentNode
     */
    const montaOrganograma = (data, parentNode) => {
        const checkbox = {
            checked: data.checked,
            disabled: data.associado,
            onClick: eventoCheckBox
        }
        let node = treeView.addNode(data.departamento, data.descricao, parentNode, null, null, checkbox);

        if (data.departamento == codigoDepartamento) {
            document.getElementById('descricao').value = data.descricao;
            document.getElementById('associado').checked = data.associado;
            node.expand();
        }

        data.filhos.forEach(filho => {
            montaOrganograma(filho, filho.departamentopai);
        });
    }


    const salvar = () => {
        const formData = new FormData();
        let txtDescricao = document.getElementById('descricao');
        let chkAssociado = document.getElementById('associado');
        let chkNode = treeView.getNodesChecked();

        if (txtDescricao.value == '') {
            return alert('Campo Descrição é de preenchimento obrigátorio.')
        }
        if (chkNode.length === 0) {
            return alert('Necessário informar o departamento pai.');
        }

        chkNode = chkNode.pop();

        formData.append('departamentofilho', codigoDepartamento);
        formData.append('descricao', txtDescricao.value);
        formData.append('associado', chkAssociado.checked);
        formData.append('departamento', chkNode.value)

        PHPSession.appendFormData(formData);

        HttpClient.post(`${PHPSession.requestApi}/${rotas.salvar}`, {body: formData}).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }
            recarregaTreeView();
        });
    }

    const recarregaTreeView = () => {
        let li = document.getElementById('TreeViewOrganograma').querySelectorAll('li');
        li.forEach(li => li.remove());
        loadData();
    }

    this.show = () => {
        recarregaTreeView();
        window.show();
    }

    montaWindow();
    loadData();
}
