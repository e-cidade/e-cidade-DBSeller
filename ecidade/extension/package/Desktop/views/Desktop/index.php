<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php echo $this->document->getCharset(); ?>"/>
    <title><?php echo $this->document->getTitle(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="ecidade:version" content="<?php echo $this->version; ?>">
    <meta name="ECIDADE_REQUEST_PATH" content="<?= ECIDADE_REQUEST_PATH ?>">
    <meta name="USER_ID" content="<?= $this->usuarioSistema->getCodigo(); ?>">
    <meta name="PUSHER_CONFIG" content='<?= json_encode(getPusherConfig()); ?>'>
    <?php echo $this->document->renderLinks(); ?>
    <base href="<?php echo ECIDADE_CURRENT_EXTENSION_REQUEST_PATH; ?>"/>
    <link rel="stylesheet" href="public/css/app.css">
</head>
<body>
<div class="topbar">
    <div id="favoritos-bar" class="favoritos-bar">
        <div id="cards-all-favoritos" class="cards-all-favoritos"></div>
        <div id="refresh-favoritos" class="fas fa-redo-alt icone-refresh-favoritos"></div>
        <div id="config-favoritos" class="fas fa-angle-double-right icone-manutencao-favoritos"></div>
    </div>
    <div class="user-nav">
        <div class="dropdown user-nav-item settings" id="documentos-assinaturas" title="Solicitações de assinaturas">
            <div class="dropdown-toggle fas fa-file-signature" id="" style="
                    display: flex;
                    align-items: center;
                    color: #007ba5;
                    justify-content: center;"
            >
            </div>
        </div>

        <div class="dropdown user-nav-item settings" id="documentos-atividades" title="Empenhos e portarias">
            <div class="dropdown-toggle icon-document" id="totalUserDocuments">
            </div>
        </div>

        <div data-type="dropdown" class="dropdown user-nav-item settings">
            <div class="dropdown-toggle icon-notification" title="Notificações">
                <div class='notify' id="count-notifications" style="display: none;"></div>
            </div>

            <div class="dropdown-menu">
                <div class="loader" id="notification-loader">
                    <div class="loader-wheel"></div>
                </div>
                <ul class="notifications" id="notifications"></ul>
            </div>
        </div>

        <div data-type="dropdown" class="dropdown user-nav-item settings">
            <div class="dropdown-toggle icon-config"></div>

          <ul class="dropdown-menu">
            <li><a data-in-full-screen="false" id="fullscreen">Usar em tela cheia</a></li>
          </ul>
        </div>

        <div data-type="dropdown" class="dropdown user-nav-item user">
            <div class="dropdown-toggle">
                <span class="user-name"><?php echo $this->usuarioSistema->getLogin(); ?></span>
                <img class="user-picture" src="assets/img/topbar/user-picture-default.png">
            </div>

            <div class="dropdown-menu">
                <ul>
                    <li class="dropdown-item-no-fx">
                        <div class="profile">
                            <div class="user-info">
                                <div class="picture">
                                    <img src="<?php echo ECIDADE_REQUEST_PATH . $this->caminhoFoto; ?>">
                                </div>
                                <div class="name">
                                    <span>
                                        <?php echo \DBString::utf8_encode_all($this->usuarioSistema->getNome()); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="system-info">
                                <p>
                                    <b>Base:</b>
                                    <em class="base-name selectable">
                                        <?php echo $this->request->session()->get('DB_NBASE'); ?>
                                    </em>
                                </p>
                                <p>
                                    <b>Servidor:</b>
                                    <em class="selectable">
                                        <?php echo $this->request->session()->get('DB_servidor') . ':' . $this->request->session()->get('DB_porta'); ?>
                                    </em>
                                </p>
                            </div>
                        </div>
                    </li>
                    <li><a id="alterarSenha">Alterar Senha</a></li>
                    <li><a id="block">Bloquear</a></li>
                    <li><a href="Window/logout" id="logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="taskbar-container">
    <div class="taskbar-menu-button" title="Menu Principal">MENU</div>

    <ul class="taskbar-buttons" id="taskbar-buttons"></ul>

    <div title="Lista de janelas abertas" class="taskbar-buttons-modal" style="display:none;">
        <div class="icon">
            <span class="bar-1"></span>
            <span class="bar-2"></span>
            <span class="bar-3"></span>
        </div>

        <div class="content"></div>
    </div>
</div>

<div id="menu">
    <div class="menu-header">
        <div class="menu-actions">
            <div class="menu-action-home"><span></span></div>
            <div class="menu-action-search">
                <input name="menu-search" id="menu-search"/>
                <span class="menu-search-icon"></span>
            </div>
        </div>

        <div class="menu-breadcrumb selectable">
            <ul id="menu-breadcrumb"></ul>
        </div>

        <div class="menu-resizer"></div>

        <div class="menu-close"><span>&times;</span></div>
    </div>
    <div class="menu-content">
        <div class="menu-pager menu-pager-left disabled">
            <div class="icon"></div>
        </div>

        <div class="menu-list-container">
            <div class="menu-list-title">Instituições</div>
            <div id="instituicoes" class="menu-list"></div>
        </div>

        <div class="menu-list-container divider-left">
            <div class="menu-list-title">Áreas</div>
            <div id="areas" class="menu-list"></div>
        </div>

        <div class="menu-list-container divider-left">
            <div class="menu-list-title">Módulos</div>
            <div id="modulos" class="menu-list"></div>
        </div>

        <div class="menu-pager menu-pager-right">
            <div class="icon"></div>
        </div>
    </div>
</div>

<div id="desktop-loader" class="desktop-loader" style="transition: opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;">
    <div class="loader-wheel"></div>
    <p id="desktop-loader-messager"></p>
</div>
<script type="text/javascript">
    var ECIDADE_REQUEST_PATH = '<?php echo ECIDADE_REQUEST_PATH; ?>';
    var ECIDADE_DESKTOP = true;
</script>
<?php
echo $this->document->renderScripts();
$instituicaoLogada = is_null($this->instituicaoUsuario) ? 1 : $this->instituicaoUsuario;
?>
<script>
    const configFavoritos = document.getElementById("config-favoritos");

    configFavoritos.addEventListener('click', () => {
        var parametros = {
            action: 'web/configuracao/favoritos',
            iInstitId: <?=$instituicaoLogada?>, // Instituição Prefeitura
            iAreaId: 11, // Area Patrimonial
            iModuloId: 1, // Módulo Protocolo
            lAtalhoDesktop: true
        }
        Desktop.Window.create('Favoritos', parametros);
    });

    const btnDocumentos = document.getElementById("documentos-atividades");
    const solicitacoesAssinatura = document.getElementById("documentos-assinaturas");

    btnDocumentos.addEventListener('click', () => {
        var parametros = {
            action: 'con4_atividades_documentos.php',
            iInstitId: <?=$instituicaoLogada?>, // Instituição Prefeitura
            iAreaId: 4, // Area Patrimonial
            iModuloId: 604, // Módulo Protocolo
            lAtalhoDesktop: true
        }

        Desktop.Window.create('Andamento de Documentos', parametros);
        document.querySelector("#documentos-atividades .icon-document").innerHTML = "";

    });

    solicitacoesAssinatura.addEventListener('click', () => {
        var parametros = {
            action: 'web/patrimonial/protocolo/solicitacao-assinatura-documentos',
            iInstitId: <?=$instituicaoLogada?>, // Instituição Prefeitura
            iAreaId: 4, // Area Patrimonial
            iModuloId: 604, // Módulo Protocolo
            lAtalhoDesktop: true
        }
        Desktop.Window.create('Solicitações de assinaturas', parametros);
    });
</script>
</body>
</html>
