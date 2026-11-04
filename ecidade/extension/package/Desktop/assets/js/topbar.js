;(function($){
    $(function ($) {
        const posicaoIconesPadrao = '-376px -364px';
        const posicoesIcones = {
            1: '-248px -331px',
            2: '-279px -331px',
            3: '-312px -331px',
            4: '-345px -331px',
            5: '-376px -331px',
            6: '-409px -331px',
            7: '-442px -331px',
            8: '-248px -364px',
            9: '-279px -364px',
            10: '-312px -364px',
            11: '-345px -364px',
        };

        const divCardsAllFavoritos = document.getElementById("cards-all-favoritos");
        const refreshFavoritos = document.getElementById("refresh-favoritos");
        const configFavoritos = document.getElementById("config-favoritos");

        refreshFavoritos.style.display='none';
        configFavoritos.style.display='none';
        divCardsAllFavoritos.style.display='none';

        refreshFavoritos.addEventListener('click', async () => {
            refreshFavoritos.classList.add('rotating');
            await getFavoritos();
            refreshFavoritos.classList.remove('rotating');
        });

        async function limpaDivFavoritos () {
            const div = document.getElementById('cards-all-favoritos');
            const filhosEspecificos = div.querySelectorAll('.card-favorito');
            filhosEspecificos.forEach((filho) => {
                div.removeChild(filho);
            });
        }

        function abreJanelaMenu (descricaoJanela, insituicao, area, modulo, funcao) {
            var parametros = {
                action: funcao, //programe ou rota
                iInstitId: insituicao, // Instituição Prefeitura
                iAreaId: area, // Area Patrimonial
                iModuloId: modulo, // Módulo Protocolo
                lAtalhoDesktop: false
            }
            Desktop.Window.create(descricaoJanela, parametros);
        }

        window.getFavoritos = async function () {
            const resp = await window.axios.get(
                `${window.urlApi}/configuracao/menu/buscar-favoritos`
            );

            let count = 0;
            await limpaDivFavoritos();

            if (resp.data.data.length >= 1) {
                resp.data.data.forEach((favorito) => {
                    if (count >= 6) return;

                    const favoritoDiv = document.createElement('div');
                    favoritoDiv.classList.add('card-favorito');

                    favoritoDiv.addEventListener('click', () => {
                        abreJanelaMenu(favorito.item.descricao, favorito.id_insituicao, favorito.area, favorito.modulo, favorito.item.funcao);
                    });

                    favoritoDiv.setAttribute('title', favorito.item.descricao);

                    const iconeInstituicaoDiv = document.createElement('div');
                    iconeInstituicaoDiv.classList.add('instituicao-icone-barra-favoritos');
                    iconeInstituicaoDiv.style.backgroundPosition = posicoesIcones[favorito.instituicao.codigo] || posicaoIconesPadrao;

                    const tituloB = document.createElement('b');
                    tituloB.classList.add('truncate');
                    tituloB.textContent = favorito.item.descricao;

                    favoritoDiv.appendChild(iconeInstituicaoDiv);
                    favoritoDiv.appendChild(tituloB);

                    divCardsAllFavoritos.appendChild(favoritoDiv);

                    count++;
                });

                refreshFavoritos.style.display='flex';
                configFavoritos.style.display='flex';
                divCardsAllFavoritos.style.display='flex';
            } else {
                refreshFavoritos.style.display='none';
                configFavoritos.style.display='none';
                divCardsAllFavoritos.style.display='none';
            }
        }

        getFavoritos();
    });
})(jQuery);
