<style>
.modal-lovrot {
    position: fixed;
    top: 0px;
    bottom: 0px;
    left: 0px;
    right: 0px;
    width: 100%;
    height: 100%;
    display: none;
    justify-content: center;
    align-items: center;
    background: rgba(0, 0, 0, 0.1);
    overflow: auto;
}

.modal-lovrot.open {
    display: flex;
}

.modal-lovrot-container {
    width: 600px;
    max-width: 600px;
    margin: auto;
    background: #ffffff;
    z-index: 9999;
}

.modal-lovrot-header {
    background: #eee;
    font-weight: bold;
    padding: 1em;
    font-size: 16px;
}

.modal-lovrot-body {
    padding: 1em;
}

.modal-lovrot-body label {
    margin-bottom: 0.8em;
    font-size: 14px;
    display: block;
    width: 100%;
}

.form-group-lovrot {
    margin-bottom: 1.5em;
}

.form-group-lovrot:last-child {
    margin-bottom: 0px;
}

.input-campos-lovrot {
    display: flex;
    flex-direction: row;
    width: 50%;
}

.input-campos-lovrot > label {
    margin-left: 10px;
}

.btn-active {
    background: aquamarine;
    color: #fff;
}

#campos-table {
    display: flex;
    flex-wrap: wrap;
}

.modal-lovrot-footer {
    border-top: 1px solid #eee;
    text-align: right;
    padding: 1em;
}

.grab * {
    cursor: -webkit-grab;
    cursor: grab;
}

</style>

<div class="modal-lovrot" id="modal-lovrot">
    <div class="modal-lovrot-container">
        <div class="modal-lovrot-header">
            Configuração PDF
        </div>
        <div class="modal-lovrot-body">
            <div class="form-group-lovrot">
                <label><b>Tamanho da folha:</b></label>
                <select value="A4" id="tamanho_folha" name="tamanho_folha">
                    <option value="A2">A2</option>
                    <option value="A3">A3</option>
                    <option value="A4" selected>A4</option>
                    <option value="A5">A5</option>
                </select>
            </div>
            <div class="form-group-lovrot">
                <label><b>Formato da folha:</b></label>
                <input id="formato_folha" name="formato_folha" type="hidden" value="P" />
                <button class="btn-select-formato-folha btn-active" value="p" type="button" style="width: 30px; height: 30px;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6,19L9,15.14L11.14,17.72L14.14,13.86L18,19H6M6,4H11V12L8.5,10.5L6,12M18,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V4A2,2 0 0,0 18,2Z" /></svg>
                </button>
                <button class="btn-select-formato-folha" value="L" type="button" style="width: 30px; height: 30px;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20,5A2,2 0 0,1 22,7V17A2,2 0 0,1 20,19H4C2.89,19 2,18.1 2,17V7C2,5.89 2.89,5 4,5H20M5,16H19L14.5,10L11,14.5L8.5,11.5L5,16Z" /></svg>
                </button>
            </div>
            <div class="form-group-lovrot">
                <label><b>Campos para o Relatório:</b></label>
                <div id="campos-table"></div>
            </div>
        </div>
        <div class="modal-lovrot-footer">
            <button type="button" id="close-btn-modal-lovrot">
                Fechar
            </button>
            <button type="button" id="imprimir-pdf-btn-modal-lovrot" style="margin-left: 0.5em">
                Imprimir
            </button>
        </div>
    </div>
</div>

<script>
function js_pdf_lovrot(event) {
    const modal = document.getElementById('modal-lovrot');
    const camposTable = modal.querySelector('#campos-table');
    const url = document.getElementById('ecidade_request_path').value;

    let table = event.target;

    do {
        table = table.parentNode
    } while (table.nodeName != 'TABLE');

    let tr = Array.from(table.querySelectorAll('tr'));

    if (tr.length <= 3) {
        return;
    }

    tr.pop();
    tr.shift();

    let header = tr.shift();

    header = header.querySelectorAll('td')

    modal.classList.add('open');

    camposTable.innerHTML = "";

    for (let index = 0; index < header.length; index++) {
        let origin = header[index].querySelector('input')

        if (!origin) {
            continue;
        }

        let input = document.createElement('input');
        let label = document.createElement('label');
        let div = document.createElement('div');

        div.classList.add('grab')

        input.checked   = true;
        input.type      = 'checkbox';
        input.name      = 'input-' + index;
        input.value     = index;
        label.for       = 'input-' + index;
        label.id        = 'input-' + index;
        label.innerHTML = origin.value;
        
        input.classList.add('input-checkbox-lovrot');
        div.append(input);
        div.append(label);
        div.classList.add('input-campos-lovrot', 'item');
        div.draggable = true;

        camposTable.append(div);
    }

    const items = camposTable.querySelectorAll(".item");

    items.forEach(item => {
        item.addEventListener("dragstart", () => {
            setTimeout(() => item.classList.add("dragging"), 0);
        });
        item.addEventListener("dragend", () => item.classList.remove("dragging"));
    });

    const initSortableList = (e) => {
        e.preventDefault();
        const draggingItem = document.querySelector(".dragging");
       
        let siblings = [...camposTable.querySelectorAll(".item:not(.dragging)")];

        let nextSibling = siblings.find(sibling => {
            return e.clientY <= sibling.offsetTop + sibling.offsetHeight / 2;
        });

        camposTable.insertBefore(draggingItem, nextSibling);
    }

    camposTable.addEventListener("dragover", initSortableList);
    camposTable.addEventListener("dragenter", e => e.preventDefault());

    document.querySelector('#close-btn-modal-lovrot').addEventListener('click', () => {
        modal.classList.remove('open');
    })

    document.querySelectorAll('.btn-select-formato-folha').forEach((btn) => {
        btn.addEventListener('click', (event) => {
            const input = document.getElementById('formato_folha')
            input.value = event.currentTarget.value

            document.querySelectorAll('.btn-select-formato-folha').forEach((element) => {
                if (element.value == input.value) {
                    element.classList.add('btn-active')
                } else {
                    element.classList.remove('btn-active')
                }
            })
        })
    })

    document.querySelector('#imprimir-pdf-btn-modal-lovrot').addEventListener('click', () => {
        const 
            form  = document.createElement('form'),
            inputB = document.createElement('input'),
            inputO = document.createElement('input'),
            inputT = document.createElement('input'),
            dados = btoa(js_lovrot_montar_table(tr));

        form.target = 'PDF';
        form.method = 'POST';
        form.action = url + 'w/1/gerar_pdf_lovrot.php';
        form.style.display = 'none';
        
        inputB.type  = 'text';
        inputB.name  = 'table';
        inputB.value = dados;

        inputO.value = document.getElementById('formato_folha').value;
        inputO.name  = 'formato';

        inputT.value = document.getElementById('tamanho_folha').value;
        inputT.name  = 'tamanho';

        let dadosHeaderPDF = document.getElementById('DBLovrotInputHeaderPDF')

        if (dadosHeaderPDF) {
            form.appendChild(dadosHeaderPDF);
        }

        form.appendChild(inputB);
        form.appendChild(inputO);
        form.appendChild(inputT);
        window.document.body.appendChild(form);

        const janela = window.open('', 'PDF', 'status=0,title=0,height=600,width=800,scrollbars=1');

        if (janela) {
            form.submit();
        }
        else {
            alert('Ocorreu um erro na tentativa de carregar os dados na nova tela');
        }
    })
}

function js_lovrot_montar_table(trs) {
    const 
        cows  = document.querySelectorAll('.input-checkbox-lovrot'),
        table = document.createElement('table'),
        thead = document.createElement('thead'),
        trH   = document.createElement('tr'),
        tbody = document.createElement('tbody');
    
    colunaIndex = [];

    cows.forEach((element) => {
        let th = document.createElement('th');

        if (element.checked) {
            colunaIndex.push(element.value);

            th.innerHTML = document.getElementById('input-' + element.value).innerHTML;

            trH.append(th);
        }
    })

    trs.forEach((td, indexTd) => {
        const tr = document.createElement('tr');

        let ordemColun = [];

        td.querySelectorAll('td').forEach((element, index) => {
            if (colunaIndex.indexOf(index + '') >= 0) {
                let tdV = document.createElement('td');

                if (element.hasAttribute('onmouseover')) {
                    let dataValue = `div_text_${indexTd}_${index}`;
                    dataValue = document.getElementById(dataValue);

                    if (dataValue) {
                        dataValue = dataValue.querySelector('.DBLovrotLabelDivTextDescricao')

                        if (dataValue) {
                            tdV.innerHTML = dataValue.innerHTML; 
                        } 
                    } else {
                        tdV.innerHTML = ''
                    }
                } else {
                    let aValue = element.querySelectorAll('a');
                    let valores = [];

                    aValue.forEach((element) => {
                        valores.push(element.innerHTML)
                    })
                
                    tdV.innerHTML = valores.join(' ');
                }

                ordemColun[colunaIndex.indexOf(index + '')] = tdV;
            }
        })

        for (let trd of ordemColun) {
            tr.append(trd);
        }

        tbody.append(tr);
    })

    thead.append(trH);
    table.append(thead);
    table.append(tbody);

    table.classList.add('table')

    return table.outerHTML;
}

</script>