Cypress.Commands.add('login', () => {
    /**
     * @todo achar melhor forma de corrigir
     * ignora o erro "Unexpected token '<'"
     */
    Cypress.on('uncaught:exception', (err, runnable) => {
        console.log(err);
        return false;
    });

    const usuario = Cypress.env('usuario_ecidade');
    const senha = Cypress.env('senha_ecidade');

    const setup = () => {
        cy.visit(Cypress.env('url_ecidade'));
        cy.get('#usu_login').type(usuario);
        cy.get('#usu_senha').type(senha + '{enter}');

        cy.url().should('include', 'extension/desktop');
    };
    const validate = () => {
        cy.getCookie('ECIDADEWINDOWMAIN').should('exist');
    };

    cy.session(usuario, setup, { validate });
});

Cypress.Commands.add('abrirCardapio', () => {
    cy.get('.taskbar-menu-button', { timeout: 15000 }).should('be.visible').click();
});

Cypress.Commands.add('abrirMenu', idItem => {
    cy.get(idItem, { timeout: 15000 }).should('exist').click({force: true});
});

Cypress.Commands.add('getIframeContent', () => {
    const getIframeDocument = () => {
        return cy.get('iframe', { timeout: 15000 }).first().its('0.contentDocument').should('exist');
    }

    // Aguarda a página carregar
    cy.get('iframe', { timeout: 15000 }).first().next({ timeout: 120000 }).should('not.be.visible');

    return getIframeDocument().its('body').should('not.be.undefined').then(cy.wrap);
});

Cypress.Commands.add('getBody', () => {
    return cy.getIframeContent()
        .find('#corpo', { timeout: 15000 })
        .its('0.contentDocument').should('exist')
        .its('body').should('not.be.undefined')
        .then(cy.wrap);
});
