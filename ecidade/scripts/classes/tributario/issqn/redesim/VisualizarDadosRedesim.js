const VisualizarDadosRedesim = function () {
    let modal
    let _this = this;

    this.init = (process, apiUrl) => {
        _this.apiUrl = apiUrl

        modal = new DBModal();
        modal.setTitle(`DADOS ${process.descricao}`);
        modal.show();

        buildDataView(process.codigo)

        loadContainerStyle();
    }

    const loadContainerStyle = () => {
        modal.oDivCabecalho.setAttribute("style", "font-size: 14px;");
        modal.oDivContainer.setAttribute("style", "background: #e1dede; width: 100%; margin: auto; margin-top: 15%; border-radius: 5px; padding:10px; font-size: 18px;");
        modal.oDivConteudo.setAttribute("style", "margin-bottom: 15px");
        modal.oDivRodape.setAttribute("style", "align-items: center; display: flex; flex-direction: row; flex-wrap: wrap; justify-content: center;");
    }

    const buildDataView = (processId) => {
        js_divCarregando("Carregando Dados", 'buildDataViewLoadingMessage');

        HttpClient.get(`${_this.apiUrl}tributario/issqn/redesim/dados-estabelecimento/?processo=${processId}`).then(response => {
            if (response.error) {
                js_removeObj('buildDataViewLoadingMessage');
                alert(response.message);
                return;
            }

            const data = JSON.parse(response.data.establishmentData)
            buildFields(modal.oDivConteudo, data.dadosRedesim)

            js_removeObj('buildDataViewLoadingMessage');
        });
    }

    const buildFields = (parentElement, data) => {
        Object.keys(data).forEach(field => {
            const dataValue = data[field]

            switch (typeof dataValue) {
                case "string":
                case "number":
                case "boolean":
                    addInput(parentElement, field, dataValue)
                    break;
                case "object":
                default:
                    const fieldset = createParentElement(field)
                    buildFields(fieldset, dataValue)
                    parentElement.appendChild(fieldset)
                    break;
            }
        });
    }

    const createParentElement = (fieldName) => {
        const fieldset = document.createElement("fieldset")
        fieldset.classList.add("bloco")
        fieldset.style = "width: auto;margin-top: 10px;margin-bottom: 10px;border: solid 1px black;"

        const legend = document.createElement("legend")
        legend.innerText = fieldName

        fieldset.appendChild(legend)

        return fieldset
    }

    const addInput = (parentElement, fieldName, value, level) => {
        const label = document.createElement("label")
        const input = document.createElement("input")

        label.innerText = ` ${fieldName}: `
        input.value = value
        input.setAttribute("readonly", "true")
        input.style = "background-color: grey"

        parentElement.appendChild(label);
        parentElement.appendChild(input);
    }
}
