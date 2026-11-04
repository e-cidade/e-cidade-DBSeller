const EnviaRespostaRedesim = function () {
    let modal
    let _this = this;
    let municipalRegistrationList = [];

    this.init = (process, apiUrl) => {
        _this.process = process;
        _this.apiUrl = apiUrl

        modal = new DBModal();
        modal.setTitle(`Enviar Resposta REDESIM`);
        modal.show();

        buildMunicipalRegistration();

        modal.aButtons.push(buildSendResponseButton())
        modal.setButtons(modal.aButtons);

        loadContainerStyle();
    }

    const buildContainer = () => {
        const containerElement = document.createElement("div");
        const hrElement = document.createElement("hr");
        const ulElement = document.createElement("ul");
        ulElement.setAttribute("style", "list-style-type: none; margin: 0; padding: 0;")

        const options = [{id: "true", description: "Deferido"}, {id: "false", description: "Indeferido"}];

        options.forEach((option) => {
            const liElement = document.createElement("li");

            let inputId = "input"+option.id;

            const input = document.createElement("input");
            input.setAttribute("type", "radio");
            input.setAttribute("class", "js-response-option-radio");
            input.setAttribute("style", "margin-bottom: 6px;");
            input.setAttribute("value", option.id);
            input.setAttribute("id", inputId);
            input.onclick = () => { adjustCheckedResponseRadioButtons(input, "js-response-option-radio") }

            const labelElement = document.createElement("label");
            labelElement.setAttribute("for", inputId);
            labelElement.innerText = option.description;

            liElement.appendChild(input);
            liElement.appendChild(labelElement);
            ulElement.appendChild(liElement);
        });

        if (municipalRegistrationList.length > 0) {
            containerElement.appendChild(hrElement);
        }

        containerElement.appendChild(ulElement);
        modal.oDivConteudo.appendChild(containerElement)
    }

    const loadContainerStyle = () => {
        modal.oDivCabecalho.setAttribute("style", "font-size: 14px;");
        modal.oDivContainer.setAttribute("style", "background: #e1dede; width: 450px; margin: auto; margin-top: 15%; border-radius: 5px; padding:10px; font-size: 18px;");
        modal.oDivConteudo.setAttribute("style", "margin-bottom: 15px");
        modal.oDivRodape.setAttribute("style", "align-items: center; display: flex; flex-direction: row; flex-wrap: wrap; justify-content: center;");
    }

    const buildSendResponseButton = () => {
        return {
            label: "Enviar Resposta",
            onclick: sendResponse,
            disabled: false,
            type: "button",
            styles: `border:1px solid #8e8e8e;margin-right:5px;`
        };
    }

    const adjustCheckedResponseRadioButtons = (inputElement, jsClass) => {
        const inputList = document.getElementsByClassName(jsClass)

        for (let input of inputList) {
            input.checked = false;
        }

        inputElement.checked = true;
    }

    const buildMunicipalRegistration = () => {
        js_divCarregando("Carregando Inscrições", 'buildUpdateMunicipalRegistrationLoadingMessage');

        HttpClient.get(`${_this.apiUrl}tributario/issqn/redesim/inscricoes-cgm/?cgm=${_this.process.numcgm}`).then(response => {
            if (response.error) {
                js_removeObj('buildUpdateMunicipalRegistrationLoadingMessage');
                alert(response.message);
                return;
            }

            if (response.data.length) {
                municipalRegistrationList = response.data;
                buildContainerMunicipalRegistrations(response.data)
            }

            buildContainer();

            js_removeObj('buildUpdateMunicipalRegistrationLoadingMessage');
        });
    }

    const buildContainerMunicipalRegistrations = (municipalRegistrations) => {
        const containerElement = document.createElement("div");
        const ulElement = document.createElement("ul");
        ulElement.setAttribute("style", "list-style-type: none; margin: 0; padding: 0;")

        municipalRegistrations.forEach((municipalRegistration) => {
            const liElement = document.createElement("li");

            let inputId = "input"+municipalRegistration.id;

            const input = document.createElement("input");
            input.setAttribute("type", "radio");
            input.setAttribute("class", "js-registration-radio");
            input.setAttribute("style", "margin-bottom: 6px;");
            input.setAttribute("value", municipalRegistration.id);
            input.setAttribute("id", inputId);
            input.onclick = () => { adjustCheckedResponseRadioButtons(input, "js-registration-radio") }
            if (municipalRegistrations.length == 1) {
                input.setAttribute("checked", "checked");
            }

            const labelElement = document.createElement("label");
            labelElement.setAttribute("for", inputId);
            labelElement.innerText = municipalRegistration.id + " - " + municipalRegistration.name;

            liElement.appendChild(input);
            liElement.appendChild(labelElement);
            ulElement.appendChild(liElement);
        });

        containerElement.appendChild(ulElement);
        modal.oDivConteudo.appendChild(containerElement)
    }

    const getSelectValue = (elementClass) => {
        const inputList = document.getElementsByClassName(elementClass)

        let selectedValue = null;

        for (let input of inputList) {
            if (input.checked) {
                selectedValue = input.value;
            }
        }

        return selectedValue;
    }

    const sendResponse = () => {
        const selectedOptionValue = getSelectValue("js-response-option-radio")

        if (!selectedOptionValue) {
            alert("Selecione uma resposta.");
            return;
        }

        const selectedMunicipalRegistrationValue = getSelectValue("js-registration-radio")

        if (municipalRegistrationList.length > 0 && !selectedMunicipalRegistrationValue) {
            alert("Selecione uma inscrição.");
            return;
        }

        if (selectedOptionValue == "true" && !selectedMunicipalRegistrationValue) {
            alert("Ao responder como deferida deve ser enviado uma inscrição.");
            return;
        }

        if (confirm("Ao confirmar, a resposta será enviada para a REDESIM. Deseja continuar?")) {
            const data = new FormData();
            data.append('municipalRegistrationId', selectedMunicipalRegistrationValue);
            data.append('isDeferred', selectedOptionValue);
            data.append('processId', _this.process.codigo);

            PHPSession.appendFormData(data);

            HttpClient.post(`${_this.apiUrl}tributario/issqn/redesim/enviar-resposta`, { body: data, reportMessage: 'Processando...', reportProgress: true }).then(response => {
                alert(response.message);
            });
        }
    }
}
