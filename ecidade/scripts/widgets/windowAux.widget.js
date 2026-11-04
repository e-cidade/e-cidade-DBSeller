require_once('estilos/windowAux.css');
require_once('scripts/widgets/DBMask.widget.js');

/**
 * Cria uma janela interna , podendo criar aplicacoes MDI
 *
 * @version  $Revision: 1.34 $
 * @constructor
 * @author  Iuri Guntchnigg - <iuri@dbseller.com.br>
 *
 * @example
 * // Uma Janela de 400x500
 * var windowTeste = new windowAux('windowTeste', 'Testando Janela', 400, 500);
 *     windowTeste.setContent("<b>Ola Mundo</b>");
 *     windowTeste.show(10,10)
 *
 * @example
 * Uma Janela com HTML node como conteudo
 * var oConteudo = document.createElement('b');
 *     oConteudo.innerHTML = "Olá Mundo";
 *
 * var windowTeste = new windowAux(
 *      'windowTeste',
 *      'Testando Janela',
 *       300,
 *       300
 * );
 * windowTeste.setContent( oConteudo );
 * windowTeste.show(100,200)
 *
 *
 * @param {String}  iIdWindow identificar da janela
 * @param {String}  sTitle titulo da Janela
 * @param {int} iWidth largura em pixeis da janela
 * @param {int} iHeight  largura em pixeis da janela
 */
windowAux = function(iIdWindow, sTitle, iWidth, iHeight) {
    const _this = this;

    if (!iIdWindow) {
        iIdWindow = 'window' + getElementsByClass('windowAux12').length + 1;
    }

    var idWindow = iIdWindow;
    var allowCloseWithEsc = true;

    this.idWindow = iIdWindow;
    this.sWindowTitle = sTitle;
    this.iWidth = iWidth;
    this.allowDrag = true;
    this.iHeight = iHeight;
    this.parent = null;
    this.zIndex = 500;
    this.aChilds = [];
    this.divWindow = document.createElement('div');
    this.oDBMask = null;

    if (!this.iWidth) {
        this.iWidth = document.width - 12;
    }

    if (!this.iHeight) {
        this.iHeight = document.body.scrollHeight;
    }

    /*
     * Criamos a div principal
     */
    this.divWindow.id = idWindow;
    this.divWindow.style.height = this.iHeight + 'px';
    this.divWindow.style.width = this.iWidth + 'px';
    this.divWindow.style.position = 'absolute';
    this.divWindow.style.display = 'none';
    this.divWindow.classList.add('windowAux12', 'shadow');
    this.divWindow.tabIndex = 0;

    var shutDownFunction = function() {
        _this.divWindow.style.display = 'none';
    };
    /*
     * Criamos a div do titulo
     */
    this.divTitleWindow = document.createElement('DIV');
    this.divTitleWindow.id = idWindow + 'TitleBar';
    this.divTitleWindow.classList.add('windowAuxTitle');
    this.divTitleWindow.setAttribute('divParent', idWindow);
    this.divTitleWindow.style.textAlign = 'right';
    this.divTitleWindow.style.color = 'white';
    this.linkWindow = document.createElement('a');
    this.linkWindow.classList.add('close');
    this.linkWindow.id = `window${idWindow}_btnclose`;
    this.linkWindow.innerHTML = `<span aria-hidden="true" title="Fechar">×</span>`;

    _this.h4 = document.createElement('h4');
    _this.h4.id = `${idWindow}_title`;
    _this.h4.classList.add('modal-title', 'dragme');
    _this.h4.innerText = this.sWindowTitle;

    this.divTitleWindow.appendChild(_this.h4);
    this.divTitleWindow.appendChild(this.linkWindow);

    this.divWindow.appendChild(this.divTitleWindow);

    var divWindowContent = document.createElement('div');
    divWindowContent.classList.add('window-content');
    divWindowContent.style.padding = '2.5px';
    divWindowContent.id = 'window' + idWindow + '_content';
    divWindowContent.style.overflow = 'auto';
    divWindowContent.tabIndex = 1;
    _this.divContent = divWindowContent;

    document.body.appendChild(this.divWindow);

    /**
     * mostra a janela nas posicoes passadas
     * @param {int} top  altura da tela
     * @param {int} left altura da tela
     * @param {boolean} lModal
     * @param {int|null} right
     * @param {int|null} bottom
     */
    this.show = function(top, left, lModal, right = null, bottom = null) {
        this.dragElement(this.divWindow);
        if (!top) {
            top = 25;
        }
        if (!left) {
            left = ((window.outerWidth - this.iWidth) / 2);
        }

        this.divWindow.style.top = top === 0 ? top.toString() : `${top}px`;
        this.divWindow.style.bottom = bottom === 0
            ? bottom.toString()
            : `${bottom}px`;
            
      CurrentWindow.fixSize.windowAux[this.divWindow.id] = this.divWindow;
      this.divWindow.style.left = left === 0 ? left.toString() : `${left}px`;
        this.divWindow.style.right = right === 0
            ? right.toString()
            : `${right}px`;

        this.divWindow.style.display = '';
        _this.toFront();

        if (lModal && lModal === true) {
            this.oDBMask = new DBMask();
            this.oDBMask.getMaskElement().appendChild(this.divWindow);
        }
    };

    /**
     * esconde a janela
     * @return {void}
     */
    this.hide = function() {
        this.divWindow.style.display = 'none';
    };

    /**
     * @param {string|HTMLElement} Content
     */
    this.setContent = function(Content) {
        divWindowContent.style.height = (this.iHeight - 32) + 'px';
        this.divWindow.appendChild(divWindowContent);

        divWindowContent.innerHTML = '';

        if (typeof Content === 'string') {
            divWindowContent.innerHTML = Content;
        }
        else {
            divWindowContent.appendChild(Content);
        }
    };

    this.divWindow.addEventListener('keydown', event => {
        if (allowCloseWithEsc) {
            if (event.which == 27) {
                shutDownFunction();
                event.preventDefault();
                event.stopPropagation();
            }
        }
    });

    /**
     * Define se Permite Fazer Drag'Drop da Janela
     *
     * @param {Boolean} lDrag Permite a janela ser arrastada
     */
    this.allowDrag = function(lDrag) {
        if (lDrag) {
            _this.h4.classList.add('dragme');
        }
        else {
            _this.h4.classList.remove('dragme');
        }
    };

    this.getId = function() {
        return _this.idWindow;
    };

    /**
     * Define como Conteudo da janela  um Objeto HTML ja existente na pagina.
     * @param {Object} oDiv Node HTML
     */
    this.setObjectForContent = function(oDiv) {
        oDiv.style.display = '';
        var divWindowContent = oDiv;
        this.divWindow.appendChild(divWindowContent);
        divWindowContent.style.height = (this.iHeight - 32) + 'px';
        divWindowContent.style.border = '2px inset white';
        divWindowContent.style.overflow = 'auto';
    };

    /**
     * Define o Titulo da Janela
     * @param {string} sTitle titulo da janela
     */
    this.setTitle = function(sTitle) {
        this.sWindowTitle = sTitle;

        if (_this.h4) {
            _this.h4.innerHTML = this.sWindowTitle;
        }
    };

    /**
     * Destroi a janela
     * @returns {void}
     */
    this.destroy = function() {
        if (!!this.oDBMask) {
            this.oDBMask.destroy();
        }

        if (this.divWindow.parentNode !== null) {
            this.divWindow.parentNode.removeChild(this.divWindow);
        }
    };

    this.setShutDownFunction = function(sFunction) {
        shutDownFunction = sFunction;
    };

    /**
     * Permite fechar a janela com a tecla esc
     */
    this.allowCloseWithEsc = function(lAllow) {
        allowCloseWithEsc = lAllow;
    };

    /**
     * Retorna o titulo da Janela
     */
    this.getTitle = function() {
        return this.sWindowTitle;
    };

    /**
     * seta a janela como filha de outra
     * @param {windowAux} oWindowAuxObject instancia de windowAux
     */
    this.setChildOf = function(oWindowAuxObject) {
        this.parent = oWindowAuxObject;
        oWindowAuxObject.add(this);
    };

    /**
     * Adiciona um Elemento a Janela
     * @private
     */
    this.add = function(oElement) {
        this.aChilds.push(oElement);
        this.divWindow.appendChild(oElement.divWindow);
    };

    this.toFront = function() {
        if (_this.aChilds.length > 0) {
            return true;
        }
        /**
         * procuramos todos os zIndex dos objetos window, e definos ele como o
         * maior, e diminuio os outros;
         */
        if (!this.divWindow) {
            return false;
        }
        if (this.divWindow.style.zIndex == this.zIndex) {
            return true;
        }

        var zIndexInicial = 1;

        var aWindowns = Array.from(
            document.querySelectorAll('div.windowAuxTitle'));

        aWindowns.forEach(function(oDiv) {
            var parentDiv = oDiv.getAttribute('divParent');

            if (_this.parent && _this.parent.getId() == parentDiv) {
                oDiv.style.backgroundColor = '#2C7AFE';
            }
            else {
                oDiv.style.backgroundColor = 'gray';

                document.getElementById(
                    parentDiv).style.zIndex = zIndexInicial++;
            }
        });

        _this.divTitleWindow.style.backgroundColor = '#2C7AFE';
        _this.divWindow.style.zIndex = this.zIndex;

        this.divWindow.focus();
    };

    /**
     * Seta o zIndex para a windowAux
     * @param {integer} iIndex
     */
    _this.setIndex = function(iIndex) {
        this.zIndex = iIndex;
    };

    _this.divTitleWindow.addEventListener('click', function() {
        _this.toFront();
    });

    this.divWindow.addEventListener('click', function() {
        _this.toFront();
    });

    /**
     * Retorna a Altura da Janela
     *@return {integer}
     */
    this.getHeight = function() {
        return this.iHeight;
    };

    /**
     * Retorna a Largura da Janela
     * @return {integer}
     */
    this.getWidth = function() {
        return this.iWidth;
    };

    this.linkWindow.addEventListener('click', e => {
        e.preventDefault();
        shutDownFunction();
    });

    /**
     * Adiciona um event listener a window
     *
     * @param {string} sEvent Evento que sera 'escutado' sendo o nome do evento
     *     sem as letras 'on'
     * @param {Object} oCallBack  funcao que sera executada ao disparar o
     *     evento
     * @example
     * window1.addEvent('keydown', function(event) {
     *   this.setTitle(this.getTitle()+" - "+toCharCode(event.which));
     * });
     */
    this.addEvent = function(sEvent, oCallBack) {
        this.divWindow.addEventListener(sEvent, oCallBack);
    };

    _this.getContentContainer = function() {
        return _this.divContent;
    };

    this.dragElement = function(element) {
        var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

        this.divTitleWindow.onmousedown = dragMouseDown;

        function dragMouseDown(e) {
            _this.divTitleWindow.style.cursor = 'grab';

            e = e || window.event;
            e.preventDefault();

            pos3 = e.clientX;
            pos4 = e.clientY;
            window.onmouseup = closeDragElement;
            window.onmousemove = elementDrag;
        }

        function elementDrag(e) {
            _this.divTitleWindow.style.cursor = 'grabbing';

            e = e || window.event;
            e.preventDefault();

            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;

            element.style.top = (element.offsetTop - pos2) + 'px';
            element.style.left = (element.offsetLeft - pos1) + 'px';
        }

        function closeDragElement(e) {
            e = e || window.event;
            e.preventDefault();

            if (e.clientY < 10) {
                element.style.top = '25px';
            }

            _this.divTitleWindow.style.cursor = 'grab';
            window.onmouseup = null;
            window.onmousemove = null;
        }
    };
};

windowAux.prototype.getElement = function() {
    return this.divWindow;
};


