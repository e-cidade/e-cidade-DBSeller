describe('Atualizador de Melhorias', () => {
    const pluginAtualizacao = 'tests/cypress/fixtures/plugins/pluginatualizacaoecidadejenkins.tar.gz';
    const apiEntregaContinua = Cypress.env('api_entrega_continua');

    beforeEach(() => {
        cy.login();
        cy.visit(Cypress.env('url_ecidade'));
    });

    it('Deve ser possível instalar e configurar o plugin', () => {
        cy.abrirCardapio();
        cy.abrirMenu('.area_11');
        cy.abrirMenu('.modulo_1');
        cy.abrirMenu('#menu_id_32');
        cy.abrirMenu('#menu_id_9881');

        cy.getBody().find('#file', { timeout: 5000 }).selectFile(pluginAtualizacao);
        cy.getBody().find('#incluir').click();
        cy.getIframeContent().find('#alertify-ok').should('be.visible').click();

        cy.getBody().find('.selectRow').first().click();
        cy.getIframeContent().find('#alertify-ok').should('be.visible').click();
        cy.getBody().find('.configRow').first().click();
        cy.getBody().find('#apiUrl').invoke('val', apiEntregaContinua.url);
        cy.getBody().find('#clientId').invoke('val', apiEntregaContinua.client_id);
        cy.getBody().find('#clientSecret').invoke('val', apiEntregaContinua.client_secret);
        cy.getBody().find("[name='salvar']").click();
        cy.getIframeContent().find('#alertify-ok').should('be.visible').click();
    });

    it('Deve ser possível atualizar a melhoria', () => {
        cy.abrirCardapio();
        cy.abrirMenu('.area_11');
        cy.abrirMenu('.modulo_1');
        cy.abrirMenu('#menu_id_32');
        // O último item de menu deve ser o de atualização de melhorias
        cy.get('span', { timeout: 10000 }).last().should('have.text', 'Atualização de Melhorias').click({ force: true });

        cy.intercept('con4_entregacontinua.RPC.php').as('postMelhorias');

        cy.getBody().find('#btnVerificarAtualiacao').click();
        cy.wait('@postMelhorias');

        cy.get('iframe').then(iframe => {
            const window = iframe[0].contentWindow[0];

            cy.stub(window, 'confirm').callsFake(() => true).as('windowConfirm')
        });

        cy.getBody().find('input[value="Atualizar"]').first().click();
        cy.wait('@postMelhorias');
        cy.getIframeContent().find('.alertify-message').should(alert => {
            expect(alert).to.contain('Melhorias atualizadas com sucesso.');
        });
    });
});
