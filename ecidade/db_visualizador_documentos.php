<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("std/DBDate.php"));

$fileIds = $_GET['ids'];
$descricoes = "";
if (!empty($_GET['descricoes'])) {
    $descricoes = $_GET['descricoes'];
}
$viewIndex = (!empty($_GET['viewIndex'])) ? $_GET['viewIndex'] : 0;
$oPreferenciaUsuario = db_getsession("DB_preferencias_usuario", false, true);
$visualizarEmOutraJanela = $oPreferenciaUsuario->isVisulizarEmOutraJanela();
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DBSeller Informática Ltda</title>
    <link rel="stylesheet" type="text/css" href="assets/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="estilos/viewerjs.css">
</head>
<body>
<input type="hidden" id="fileIds" value=<?= $fileIds ?>>
<input type="hidden" id="descricoes" value=<?= $descricoes ?>>
<input type="hidden" id="viewIndex" value=<?= $viewIndex ?>>
<div class="container">
    <div id="galley">
        <div id="control-venrtical">
            <button type="button" class="btn" id="zoomOut"><i class="fas fa-search-plus"></i></button>
            <button type="button" class="btn" id="zoomIn"><i class="fas fa-search-minus"></i></button>
            <button type="button" class="btn" id="rotateH"><i class="fas fa-share"></i></button>
            <button type="button" class="btn inverse" id="rotateA"><i class="fas fa-share"></i></button>
            <button type="button" class="btn" id="download" title="Download aquivo selecionado"><i
                        class="fas fa-file-download"></i></button>
            <button type="button" class="btn" id="download_todos_arquivos" title="Download todos os arquivos"><i
                        class="fas fa-file-download"></i></button>
        </div>
        <nav id="buttons">
            <button type="button" class="btn" id="prev"><i class="fas fa-arrow-circle-left"></i></button>
            <button type="button" class="btn" id="next"><i class="fas fa-arrow-circle-right"></i></button>
        </nav>
        <ul id="pictures" style="opacity: 0"></ul>
    </div>
</div>
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/viewerjs.js"></script>
<script type="text/javascript">

    var visualizarEmOutraJanela = "<?php echo $visualizarEmOutraJanela; ?>";
    var galley = document.getElementById('galley');
    var viewIndex = 0;
    const fileIds = document.getElementById('fileIds').value;
    var descricoes = document.getElementById('descricoes').value;
    var imagensIds = [];
    var imagensIdsLidos = [];
    var totalImagens = 0;
    var contarPaginasArquivo = 0;
    var imagensFaltamPesquisar = 0;
    const IMAGEMS_POR_CARREGAMENTO = 5;
    const pictures = document.querySelector('#pictures');
    var viewer = new Viewer(galley, {
        url: 'data-original',
        title: function (image) {
            const me = this;
            var key = imagensIds.findIndex(element => {
                return element == viewer.images[me.index].id;
            });
            var nameImage = image.alt;
            return `nameImage  (${key + 1}) / (${totalImagens})`;
        },
        transition: false,
        movable: true,
        scalable: true,
        zoomable: true,
        loop: false,
        toolbar: false,
        inline: true,
        initialViewIndex: viewIndex,
        viewed() {
            viewer.full();
        }
    });

    document.getElementById('prev').addEventListener('click', () => {
        viewer.prev();
    });

    document.getElementById('next').addEventListener('click', () => {
        viewer.next();
    });

    document.getElementById('zoomIn').addEventListener('click', () => {
        viewer.zoom(-0.1);
    });
    document.getElementById('zoomOut').addEventListener('click', () => {
        viewer.zoom(0.1);
    });
    document.getElementById('rotateH').addEventListener('click', () => {
        viewer.rotate(90);
    });
    document.getElementById('rotateA').addEventListener('click', () => {
        viewer.rotate(-90);
    });
    document.getElementById('download').addEventListener('click', () => {
        var arquivo = viewer.images[viewer.index].getAttribute('data-file');
        if (visualizarEmOutraJanela) {
            window.open('db_download.php?arquivo=' + arquivo);
        } else {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_download',
                'db_download.php?arquivo=' + arquivo,
                'Download de arquivos',
                false
            );
        }


    });

    document.getElementById('download_todos_arquivos').addEventListener('click', () => {
        const documentos = document.querySelectorAll("#pictures img");
        var imagens = [];

        documentos.forEach(el => {
            imagens.push(encodeURI('arquivos[]=' + el.getAttribute('data-original')));
        });

        if (imagens.length > 1) {
            const params = imagens.join("&");
            window.open(`db_download_todos_arquivos_visualizador.php?${params}`, '_blank');
        } else {
            alert("Não foi possivel identificar arquivos.");
        }

    });


    const criaElementoImagem = (imagem) => {
        const imageItem = document.createElement('img');
        imageItem.setAttribute('src', imagem.thumbnail);
        imageItem.setAttribute('data-original', imagem.original);
        imageItem.setAttribute('data-file', imagem.download);
        imageItem.setAttribute('alt', imagem.descricao);
        imageItem.setAttribute("id", imagem.id);
        const item = document.createElement('li');
        item.appendChild(imageItem);
        pictures.prepend(item);
    }

    const criaListaImagem = (imagens) => {
        imagens.map(imagem => {
            criaElementoImagem(imagem);
        });
    }

    const js_download = (el) => {
        const arquivo = el.getAttribute('data-file');

        if (visualizarEmOutraJanela == "true") {
            window.open('db_download.php?arquivo=' + arquivo);
        } else {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_download',
                'db_download.php?arquivo=' + arquivo,
                'Download de arquivos',
                false
            );
        }
    };

    async function getImagens(fileIds, loading = true) {
        const formData = new FormData();
        formData.append('acao', 'buscar_imagens');
        formData.append('fileIds', fileIds);
        formData.append('descricoes', descricoes);
        const response = await HttpClient.post('db_visualizador.RPC.php', {body: formData, reportProgress: false});
        if (response.erro) {
            return;
        }

        criaListaImagem(response.imagens);
        viewer.index = document.querySelectorAll("#pictures li").length - 1;
        viewer.update();
        return true;

    }

    async function main() {

        imagensIds = fileIds.split(",");
        imagensFaltamPesquisar = totalImagens = imagensIds.length;
        let i = 0;
        let imagens = [];
        for (i = 0; i < IMAGEMS_POR_CARREGAMENTO; i++) {
            let id = imagensIds[--imagensFaltamPesquisar];
            if (id) {
                imagens.push(id);
            }
        }

        js_ajax_msg(`1 Carregando...`);
        await getImagens(imagens.join(","));
        js_remove_ajax_msg();
        while (imagensFaltamPesquisar > 0) {
            imagens = [];
            for (i = 0; i < IMAGEMS_POR_CARREGAMENTO; i++) {
                let id = imagensIds[--imagensFaltamPesquisar];
                if (id) {
                    imagens.push(id);
                }
            }

            if (imagens.length > 0) {
                await getImagens(imagens.join(","), false);
            }

        }
    }

    main();

</script>
</body>
</html>
