const ExecutarAcaoRedesim = function () {
    let modal
    let _this = this;

    this.init = (process, apiUrl) => {
        _this.process = process;
        _this.apiUrl = apiUrl

        modal = new DBModal();
        modal.setTitle(_this.process.descricao);
        modal.show();

        if (_this.process.isProcessoRedesimInclusaoInscricao) {
            buildCreateMunicipalRegistration()
        } else {
            buildUpdateMunicipalRegistration();
        }

        loadContainerStyle();
    }

    const loadContainerStyle = () => {
        modal.oDivCabecalho.setAttribute("style", "font-size: 14px;");
        modal.oDivContainer.setAttribute("style", "background: #e1dede; width: 777px; margin: auto; margin-top: 15%; border-radius: 5px; padding:10px; font-size: 18px;");
        modal.oDivConteudo.setAttribute("style", "margin-bottom: 15px");
        modal.oDivRodape.setAttribute("style", "align-items: center; display: flex; flex-direction: row; flex-wrap: wrap; justify-content: center;");
    }

    const buildUpdateMunicipalRegistration = () => {
        js_divCarregando("Carregando Inscrições", 'buildUpdateMunicipalRegistrationLoadingMessage');

        HttpClient.get(`${_this.apiUrl}tributario/issqn/redesim/inscricoes-cgm/?cgm=${_this.process.numcgm}&onlyActivated=true`).then(async response => {
            if (response.error) {
                js_removeObj('buildUpdateMunicipalRegistrationLoadingMessage');
                alert(response.message);
                return;
            }

            if (!response.data.length) {
                buildContainerWithoutData();
            } else {
                await buildContainer(response.data)

                loadButtons([
                    buildCloseButtonConfig(),
                    buildExecuteActionUpdateRegistrationButtonConfig()
                ]);
            }

            js_removeObj('buildUpdateMunicipalRegistrationLoadingMessage');
        });
    }

    const buildContainer = async (municipalRegistrations) => {
        const containerElement = document.createElement("div");

        const hrElement = document.createElement("hr");

        containerElement.appendChild(await buildEventsTable())
        containerElement.appendChild(hrElement)

        const ulElement = document.createElement("ul");
        ulElement.setAttribute("style", "list-style-type: none; margin: 0; padding: 0;")

        municipalRegistrations.forEach((municipalRegistration) => {
            const liElement = document.createElement("li");

            let inputId = "input" + municipalRegistration.id;

            const input = document.createElement("input");
            input.setAttribute("type", "checkbox");
            input.setAttribute("class", "js-registration-checkbox");
            input.setAttribute("style", "margin-bottom: 6px;");
            input.setAttribute("value", municipalRegistration.id);
            input.setAttribute("id", inputId);
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
        modal.setContent(containerElement)
    }

    const buildCloseButtonConfig = () => {
        return {
            label: "Fechar",
            onclick: () => modal.destroy(),
            disabled: false,
            type: "button",
            styles: `border:1px solid #8e8e8e;margin-right:5px;`
        };
    }

    const buildExecuteActionNewRegistrationButtonConfig = () => {
        return {
            label: "Incluir inscrição",
            onclick: createRegistration,
            disabled: false,
            type: "button",
            styles: `border:1px solid #8e8e8e;margin-right:5px;`
        };
    }

    const buildExecuteActionUpdateRegistrationButtonConfig = () => {
        return {
            label: "Atualizar Inscrição",
            onclick: updateRegistration,
            disabled: false,
            type: "button",
            styles: `border:1px solid #8e8e8e;margin-right:5px;`
        };
    }

    const createRegistration = () => {
        if (confirm("Ao confirmar, será gerada uma nova inscrição. Deseja continuar?")) {
            loadButtons([
                buildCloseButtonConfig()
            ]);

            const data = new FormData();
            data.append('cgm', _this.process.numcgm);
            data.append('processo', _this.process.codigo);

            PHPSession.appendFormData(data);

            HttpClient.post(`${_this.apiUrl}tributario/issqn/redesim/processar-evento-inclusao-inscricao`, { body: data, reportMessage: 'Processando...', reportProgress: true }).then(response => {
                alert(response.message);

                if (response.error) {
                    loadButtons([
                        buildCloseButtonConfig(),
                        buildExecuteActionNewRegistrationButtonConfig()
                    ]);
                }
            });
        }
    }

    const updateRegistration = () => {
        let municipalRegistrationIdList = getSelectCheckboxes();

        if (municipalRegistrationIdList.length == 0) {
            alert("Selecione uma inscrição.");
            return;
        }

        if (confirm("Ao confirmar, a inscrição selecionada será atualizada. Deseja continuar?")) {
            loadButtons([
                buildCloseButtonConfig()
            ]);

            const data = new FormData();
            data.append('inscr', municipalRegistrationIdList.join(","));
            data.append('processo', _this.process.codigo);

            PHPSession.appendFormData(data);

            HttpClient.post(`${_this.apiUrl}tributario/issqn/redesim/processar-evento-alteracao-inscricao`, { body: data, reportMessage: 'Processando...', reportProgress: true }).then(response => {
                alert(response.message);

                if (response.error) {
                    loadButtons([
                        buildCloseButtonConfig(),
                        buildExecuteActionUpdateRegistrationButtonConfig()
                    ]);
                }
            });
        }
    }

    const getSelectCheckboxes = () => {
        const inputList = document.getElementsByClassName("js-registration-checkbox")

        const selecteds = [];

        for (let input of inputList) {
            if (input.checked) {
                selecteds.push(input.value)
            }
        }

        return selecteds;
    }

    const buildCreateMunicipalRegistration = () => {
        loadButtons([
            buildCloseButtonConfig(),
            buildExecuteActionNewRegistrationButtonConfig()
        ]);
    }

    const buildContainerWithoutData = () => {
        modal.setContent("Não foram encontradas inscrições ativas para o CGM do titular do processo.");
    }

    const loadButtons = (buttons) => {
        modal.setButtons(buttons);
        loadContainerStyle();
    }

    const buildEventsTable = async() => {
        js_divCarregando("Carregando Eventos", 'buildEventsTableLoadingMessage');

        return HttpClient.get(`${_this.apiUrl}tributario/issqn/redesim/eventos/?processo=${_this.process.codigo}`).then(response => {
            js_removeObj('buildEventsTableLoadingMessage');

            if (response.error) {
                alert(response.message);
                return;
            }

            const style = "border: 1px solid black; border-collapse: collapse;";
            const tableElement = document.createElement("table");
            tableElement.setAttribute("style", "width: 100%")
            const trHeaderElement = document.createElement("tr");
            const thElement = document.createElement("th");
            thElement.innerText = "EVENTOS"
            thElement.setAttribute("colspan", 2);

            trHeaderElement.appendChild(thElement)
            tableElement.appendChild(trHeaderElement)

            response.data.forEach((data) => {
                const trElement = document.createElement("tr");

                const tdExternalIdElement = document.createElement("td");
                tdExternalIdElement.innerText = data.external_id
                tdExternalIdElement.setAttribute("style", style + "text-align: center;")

                const tdDescriptionElement = document.createElement("td");
                tdDescriptionElement.innerText = data.description
                tdDescriptionElement.setAttribute("style", style)

                trElement.appendChild(tdExternalIdElement)
                trElement.appendChild(tdDescriptionElement)
                tableElement.appendChild(trElement)
            });

            return tableElement;
        });
    }
}
