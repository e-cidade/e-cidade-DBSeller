<?php

namespace Fpdf;

use EscolaRepository;
use InstituicaoRepository;

class Pdf extends Fpdf
{
    /**
     * Constantes que definem o header a ser impresso.
     */
    const HEADER_DEFAULT = 0; // header da instituição
    const HEADER_ESCOLA = 1;  // header da escola - Só imprime
    const HEADER_ESTADO_PREFEITURA = 2;  // header com estado e prefeitura( Antigo pdf1)

    protected $lEnableFooter = false;
    protected $lShowPageNumber = false;
    protected $lExibeHeader = false;
    protected $lExibeBrasao = false;

    protected $lShowMenu = false;
    /**
     * @var bool
     */
    protected $exibirProgramaEmissor = false;


    /**
     * Possibilita passar uma função para executar quando o multcell executar a quebra de página
     * @var string
     */
    protected $sFunctionMulticellBreakPage = '';

    /**
     * Se
     * @var int
     */
    protected $tipoHeader = 0;

    /**
     * Para substituir os títulos
     * @var array
     */
    protected $titulos = [];

    /**
     * --------------------------------------------------------------------------------------------------------------
     *                     Desta linha para baixo são propriedades de scripts de terceiros
     *  --------------------------------------------------------------------------------------------------------------
     */
    protected $widths;
    protected $aligns;

    protected $B = 0;
    protected $I = 0;
    protected $U = 0;
    protected $HREF = '';
    /**
     * altura que o header acabou a escrita
     * @var int
     */
    protected $yFimHeader;

    /**
     * Inicializa as dependências para uso da classe FPDF
     *
     * @param string $orientation Default page orientation. Possible values are: P or L
     * @param string $unit User unit. Possible values are: pt, mm, cm or in
     * @param string $size The size used for pages. Possible values are: A3, A4, A5, Letter or Legal
     */
    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        if (!defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', 'fpdf/font/');
        }

        parent::__construct($orientation, $unit, $size);
        $this->AliasNbPages();
        $this->SetAutoPageBreak(false, 20);
    }

    /**
     * Adiciona um título para impressão no cabeçalho
     * @param $titulo - texto do título
     * @param $key - chave do titulo
     */
    public function addTitulo($titulo, $key = null)
    {
        if ($key == null) {
            $this->titulos[] = $titulo;
        } else {
            $this->titulos[$key] = $titulo;
        }
    }

    /**
     * Edita um título para impressão no cabeçalho pelo texto
     * @param $texto -> String exata do Título a editar
     * @param $novoTexto -> String do novo texto
     */
    public function editTitleByText($texto, $novoTexto)
    {
        foreach ($this->titulos as $key => $titulo) {
            if ($titulo == $texto) {
                $this->titulos[$key] = $novoTexto;
            }
        }
    }

    /* Buscando um título pela chave */
    public function getTitleByKey($key)
    {
        return $this->titulos[$key];
    }

    /**
     * Edita um título para impressão no cabeçalho pela chave
     * @param $key -> Chave do Título a editar
     * @param $novoTexto -> String do novo texto
     */
    public function editTitleByKey($key, $novoTexto)
    {
        $this->titulos[$key] = $novoTexto;
    }

    /**
     * Retorna a altura da pagina
     * @return float|int
     */
    public function getH()
    {
        return $this->h;
    }

    /**
     * Retorna a largura da página
     * @return float|int
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * Retorna a margin esquerda
     * @return * @return float|int
     */
    public function getLeftMargin()
    {
        return $this->lMargin;
    }

    /**
     * Retorna a margin direita
     * @return float|int
     */
    public function getRightMargin()
    {
        return $this->rMargin;
    }

    /**
     * Retorna a margin do topo
     * @return float|int
     */
    public function getTopMargin()
    {
        return $this->tMargin;
    }

    /**
     * Retorna a margin de baixo
     * Margin para quebra de página
     * @return float|int
     */
    public function getBottomMargin()
    {
        return $this->bMargin;
    }

    /**
     * Define uma funcao para ser executada apos uma chamada addPage implícito do multicell
     * @param string $sFunction nome da funcao
     * @example pequise por exemplo de uso no sistema
     *  ->setMulticellBreakPageFunction( array($this, "escreveCabecalho") );
     */
    public function setMulticellBreakPageFunction($sFunction)
    {
        $this->sFunctionMulticellBreakPage = $sFunction;
    }

    public function showPageNumber()
    {
        if ($this->lShowPageNumber) {
            $sString = 'Pág ' . $this->PageNo() . '/{nb}';
            $this->text(($this->w - $this->rMargin) - $this->GetStringWidth($sString) + 1, $this->h - 6, $sString, 0, 1, 'R');
        }
    }

    /**
     * Adapta o tamanho da fonte para que o conteúdo informado caiba na celula
     * @param integer $fonteOriginal tamanho original da fonte
     * @param integer $w tamanho do campo para escrita do conteúdo
     * @param integer $h altura da linha
     * @param string $content
     * @param integer $border
     * @param integer $ln
     * @param string $align
     * @param bool $fill
     * @param string $link
     */
    public function cellAdapt(
        $fonteOriginal,
        $w,
        $h,
        $content,
        $border = 0,
        $ln = 0,
        $align = 'L',
        $fill = false,
        $link = ''
    ) {
        $content = "${content}  ";
        $this->SetFontSize($this->getFonteSizeByWidthCell($w, $fonteOriginal, $content));
        $this->Cell($w, $h, $content, $border, $ln, $align, $fill, $link);
        $this->SetFontSize($fonteOriginal);
    }

    /**
     * @param $width
     * @param $fontSize
     * @param $content
     * @return float|int
     */
    public function getFonteSizeByWidthCell($width, $fontSize, $content)
    {
        $tamanhoString = $this->GetStringWidth($content);
        $content = trim($content);
        if ($tamanhoString > $width) {
            // Deixa a fonte EXATAMENTE no tamanho para caber na célula
            return $fontSize * $width / $tamanhoString;
        }
        return $fontSize;
    }

    /**
     * Permite a impressao de texto com quebras de linhas
     *
     * As quebras podem ser automáticas, (quando o texto chega na borda da celula) ou  explicitas (\n).
     * O método sempre cria uma nova linha.
     * O parametro $borderm pode ser 1 - Terá bordas em toda o mnulticell ou a combinacao dos caracteres:
     * 'T' = Borda no topo
     * 'B' = Borda no Fundo
     * 'L' = Borda na esquerda
     * 'R' = Borda na Direita
     * @param integer $w Tamanho da multicell
     * @param integer $h espaçamento entre linhas
     * @param string $txt texto para ser impresso
     * @param mixed $border bordas em torno do multicell
     * @param string $align Alinhamento do Texto 'J', 'C', 'L', 'R'
     * @param integer $fill se o multicell vai possuir preenchimento de fundo
     * @param integer $indent tamanhodo Recuo de 1 linha
     * @see FPDF::MultiCell()
     */
    function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = 0, $indent = 0)
    {
        $sTopBorder = '';
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wFirst = $w - $indent;
        $wOther = $w;

        $wmaxFirst = ($wFirst - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $wmaxOther = ($wOther - 2 * $this->cMargin) * 1000 / $this->FontSize;

        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n") {
            $nb--;
        }
        $b = 0;
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
                $b = 'LRT';
                $b2 = 'LR';
                $sTopBorder = 'TB';
            } else {
                if (strpos($border, "B") !== false) {
                    $sTopBorder .= 'B';
                }
                if (strpos($border, "T") !== false) {
                    $sTopBorder .= 'T';
                }
                $b2 = '';
                if (is_int(strpos($border, 'L'))) {
                    $b2 .= 'L';
                }
                if (is_int(strpos($border, 'R'))) {
                    $b2 .= 'R';
                }
                $b = is_int(strpos($border, 'T')) ? $b2 . 'T' : $b2;
            }
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        $first = true;
        while ($i < $nb) {
            //Get next character
            $c = $s[$i];
            if ($c == "\n") {
                //Explicit line break
                if ($this->ws > 0) {
                    $this->ws = 0;
                    $this->_out('0 Tw');
                }

                /**
                 * caso nã termine a linha e tenha uma quebra \n ou \r
                 */
                $SaveX = $this->x;
                if ($first and $indent > 0) {
                    $this->SetX($this->x + $indent);
                    $first = false;
                }
                $this->linecell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill, $sTopBorder);
                $this->SetX($SaveX);
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                $first = false;
                if ($border and $nl == 2) {
                    $b = $b2;
                }
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];

            if ($first) {
                $wmax = $wmaxFirst;
                $w = $wFirst;
            } else {
                $wmax = $wmaxOther;
                $w = $wOther;
            }

            if ($l > $wmax) {
                //Automatic line break
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                    if ($this->ws > 0) {
                        $this->ws = 0;
                        $this->_out('0 Tw');
                    }
                    $SaveX = $this->x;
                    if ($first && $indent > 0) {
                        $this->SetX($this->x + $indent);
                        $first = false;
                    }
                    $this->linecell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill, $sTopBorder);
                    $this->SetX($SaveX);
                } else {
                    if ($align == 'J') {
                        $this->ws = ($ns > 1) ? ($wmax - $ls) / 1000 * $this->FontSize / ($ns - 1) : 0;
                        $this->_out(sprintf('%.3f Tw', $this->ws * $this->k));
                    }
                    $SaveX = $this->x;
                    if ($first && $indent > 0) {
                        $this->SetX($this->x + $indent);
                        $first = false;
                    }
                    $this->lineCell($w, $h, substr($s, $j, $sep - $j), $b, 2, $align, $fill, $sTopBorder);
                    $this->SetX($SaveX);
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border && $nl == 2) {
                    $b = $b2;
                }
            } else {
                $i++;
            }
        }
        //Last chunk
        if ($this->ws > 0) {
            $this->ws = 0;
            $this->_out('0 Tw');
        }

        if ($border && is_int(strpos($border, 'B'))) {
            $b .= 'B';
        }

        $SaveX = $this->x;
        if ($first && $indent > 0) {
            $this->SetX($this->x + $indent);
            $first = false;
        }
        $this->lineCell($w, $h, substr($s, $j, $i), $b, 2, $align, $fill, $sTopBorder);
        $this->x = $this->lMargin;
    }

    /**
     * Escreve uma linha de texto método utilizado para escrever as linhas do multicell
     * @param integer $w Tamanho da linha
     * @param integer $h altura da linha
     * @param string $txt texto para ser impresso
     * @param mixed $border bordas em torno do multicell
     * @param integer $ln quebra linha apos escrevcer linha
     * @param string $align Alinhamento do Texto 'J', 'C', 'L', 'R'
     * @param integer $fill se o multicell vai possuir preenchimento de fundo
     * @param string $sParentBorder controle do impressao de bordas apos quebra de página
     */
    protected function lineCell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $sParentBorder = '')
    {
        $k = $this->k;

        /**
         * Última Celula impressa na página
         */
        $lLastCellOfPage = false;

        /**
         * Borda do multicell necessita a impressao na parte de baixo
         */
        $lBottomBorder = strpos($sParentBorder, "B") !== false;

        /**
         * próxima celula deverá estar na página de baixo, marcamos essa celula com a última da página
         */
        if ($this->y + ($h * 2) > $this->PageBreakTrigger) {
            $lLastCellOfPage = true;
        }
        if ($this->y + $h > $this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak()) {
            $lLastCellOfPage = false;
            $x = $this->x;
            $ws = $this->ws;
            if ($ws > 0) {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            if ($txt == "") {
                return;
            }

            $lTopBorder = strpos($sParentBorder, "T") !== false ? true : false;
            $this->AddPage($this->CurOrientation);
            if ($this->sFunctionMulticellBreakPage != "") {
                call_user_func($this->sFunctionMulticellBreakPage);
            }
            if ($lTopBorder) {
                $border .= "T";
            }
            $this->x = $x;
            if ($ws > 0) {
                $this->ws = $ws;
                $this->_out(sprintf('%.3f Tw', $ws * $k));
            }
        }
        if ($lLastCellOfPage && $lBottomBorder) {
            $border .= "B";
        }
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $s = '';
        if ($fill == 1 || $border == 1) {
            if ($fill == 1) {
                $op = ($border == 1) ? 'B' : 'f';
            } else {
                $op = 'S';
            }
            $s = sprintf('%.2f %.2f %.2f %.2f re %s ', $this->x * $k, ($this->h - $this->y) * $k, $w * $k, -$h * $k, $op);
        }
        if (is_string($border)) {
            $x = $this->x;
            $y = $this->y;
            if (is_int(strpos($border, 'L'))) {
                $s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - $y) * $k, $x * $k, ($this->h - ($y + $h)) * $k);
            }
            if (is_int(strpos($border, 'T'))) {
                $s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - $y) * $k);
            }
            if (is_int(strpos($border, 'R'))) {
                $s .= sprintf('%.2f %.2f m %.2f %.2f l S ', ($x + $w) * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
            }
            if (is_int(strpos($border, 'B'))) {
                $s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - ($y + $h)) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
            }
        }
        if ($txt != '') {
            if ($align == 'R') {
                $dx = $w - $this->cMargin - $this->GetStringWidth($txt);
            } elseif ($align == 'C') {
                $dx = ($w - $this->GetStringWidth($txt)) / 2;
            } else {
                $dx = $this->cMargin;
            }
            $txt = str_replace(')', '\\)', str_replace('(', '\\(', str_replace('\\', '\\\\', $txt)));

            if ($this->ColorFlag) {
                $s .= 'q ' . $this->TextColor . ' ';
            }
            $s .= sprintf(
                'BT %.2f %.2f Td (%s) Tj ET',
                ($this->x + $dx) * $k,
                ($this->h - ($this->y + .5 * $h + .3 * $this->FontSize)) * $k,
                $txt
            );

            if ($this->underline) {
                $s .= ' ' . $this->_dounderline($this->x + $dx, $this->y + .5 * $h + .3 * $this->FontSize, $txt);
            }
            if ($this->ColorFlag) {
                $s .= ' Q';
            }
        }
        if ($s) {
            $this->_out($s);
        }
        $this->lasth = $h;
        if ($ln > 0) {
            //Go to next line
            $this->y += $h;
            if ($ln == 1) {
                $this->x = $this->lMargin;
            }
        } else {
            $this->x += $w;
        }
    }

    /**
     * Mostra o Footer na página
     * @param boolean $lShow
     */
    public function mostrarRodape($lShow)
    {
        $this->lEnableFooter = $lShow;
    }

    /**
     * Mostra total de páginas
     * @param boolean $lShow
     */
    public function mostrarTotalDePaginas($lShow)
    {
        $this->lShowPageNumber = $lShow;
    }

    /**
     * Mostra t
     * @param boolean $lShow
     */
    public function mostrarEmissor($lShow)
    {
        $this->lShowMenu = $lShow;
    }

    /**
     * @param $exibirProgramaEmissor
     */
    public function mostrarProgramaEmissor($exibirProgramaEmissor)
    {
        $this->exibirProgramaEmissor = $exibirProgramaEmissor;
    }

    /**
     * Mostra o cabeçalho padrão do sistema
     * @param boolean $lShow
     */
    public function exibeHeader($lShow, $tipo = self::HEADER_DEFAULT)
    {
        $this->lExibeHeader = $lShow;
        if ($lShow) {
            $this->tipoHeader = $tipo;
        }
    }

    /**
     * Define se o brasão deve ser exibido ou não
     * @param boolean $lExibeBrasao
     */
    public function setExibeBrasao($lExibeBrasao)
    {
        $this->lExibeBrasao = $lExibeBrasao;
    }


    function Header()
    {
        if (!$this->lExibeHeader) {
            return false;
        }

        switch ($this->tipoHeader) {
            case self::HEADER_ESCOLA:
                return $this->headerEscola();
            case self::HEADER_ESTADO_PREFEITURA:
                return $this->headerEstadoPrefeitura();
            case self::HEADER_DEFAULT:
            default:
                return $this->headerInstituicao();
        }
    }

    /**
     * Imprime o footer padrão
     */
    public function footer()
    {
        if (!$this->lEnableFooter) {
            return;
        }

        $this->line(($this->lMargin), $this->h - 10, $this->w - $this->rMargin, $this->h - 10);
        if ($this->lShowMenu) {
            $menuAcesssado = $this->buscarMenuAcessado();
            $usuarioEmissor = $this->buscarUsuarioEmissor();
            $nomeBase = $_SESSION['DB_NBASE'];

            //Position at 1.5 cm from bottom
            $this->SetFont('Arial', '', 5);
            $this->text(10, $this->h - 8, 'Base: ' . $nomeBase);
            $this->SetFont('Arial', 'I', 6);
            $this->SetY(-10);

            $arquivoEmissor = '';
            if ($this->exibirProgramaEmissor) {
                $arquivoEmissor = __FILE__;
                $arquivoEmissor = substr($arquivoEmissor, strrpos($arquivoEmissor, "/") + 1);
            }

            $string = sprintf(
                '%s %s Emissor: %s Exerc: %s Data: %s - %s',
                $menuAcesssado,
                $arquivoEmissor,
                $usuarioEmissor,
                db_getsession("DB_anousu"),
                date("d-m-Y", db_getsession("DB_datausu")),
                date("H:i:s")
            );

            $this->text(($this->lMargin) + 15, $this->h - 6, $string, 0, 1, 'R');
        }

        $this->showPageNumber();
    }

    private function buscarMenuAcessado()
    {
        if (empty($this->menuAcessado)) {
            $sql = "
            select trim(modulo.descricao)||'>'||trim(menu.descricao)||'>'||trim(item.descricao) as menu
              from db_menu
             inner join db_itensmenu as modulo on modulo.id_item = db_menu.modulo
             inner join db_itensmenu as menu on menu.id_item = db_menu.id_item
             inner join db_itensmenu as item on item.id_item = db_menu.id_item_filho
             where id_item_filho = " . db_getsession("DB_itemmenu_acessado") . "
               and modulo = " . db_getsession("DB_modulo");

            $rs = db_query($sql);

            if (pg_num_rows($rs)) {
                $this->menuAcessado = substr(pg_result($rs, 0, "menu"), 0, 50);
            }
        }
        return $this->menuAcessado;
    }

    /**
     * @return string|null
     */
    private function buscarUsuarioEmissor()
    {
        if (empty($this->nomeUsuario)) {
            $sql = "select nome as nomeusu from db_usuarios where id_usuario = " . db_getsession("DB_id_usuario");
            $rs = db_query($sql);
            if ($rs && pg_numrows($rs) > 0) {
                $this->nomeUsuario = pg_result($rs, 0, 0);
            } elseif (isset($_SESSION["DB_login"]) && !empty($_SESSION["DB_login"])) {
                $this->nomeUsuario = $_SESSION["DB_login"];
            }
            $this->nomeUsuario = substr(ucwords(mb_strtolower($this->nomeUsuario)), 0, 35);
        }
        return $this->nomeUsuario;
    }

    private function headerEscola()
    {
        $escola = EscolaRepository::getEscolaByCodigo(db_getsession("DB_coddepto"));
        if (is_null($escola->getCodigo())) {
            $this->headerInstituicao();
            return;
        }

        $instituicao = $escola->getDepartamento()->getInstituicao();
        $nomeInstituicao = $instituicao->getDescricao();
        $site = $instituicao->getSite();
        $nomeEscola = $escola->getNome();
        $iCodigoReferencia = $escola->getCodigoReferencia();

        if ($iCodigoReferencia != null) {
            $nomeEscola = "{$iCodigoReferencia} - {$nomeEscola}";
        }

        $TamFonteNome = 9;
        if (strlen($nomeInstituicao) > 42 || strlen($nomeEscola) > 42) {
            $TamFonteNome = 8;
        }

        $logoInstituicao = $instituicao->getImagemLogo();
        $filepathInstituicao = 'imagens/files/' . $logoInstituicao;
        $logoEscola = $escola->getLogoEscola();
        $filepath = 'imagens/' . $logoEscola;
        if ($this->lExibeBrasao && !empty($logoEscola) && file_exists($filepath)) {
            $this->SetXY(1, 1);
            $this->Image($filepath, 7, 3, 20);
        } elseif ($this->lExibeBrasao && !empty($logoInstituicao) && file_exists($filepathInstituicao)) {
            $this->SetXY(1, 1);
            $this->Image($filepathInstituicao, 7, 3, 20);
        }

        $ruaEscola = $escola->getEndereco();
        $numeroEscola = $escola->getNumeroEndereco();
        $bairroEscola = $escola->getBairro();
        $cidadeEscola = $escola->getMunicipio();
        $estadoEscola = $escola->getUf();
        $emailEscola = $escola->getEmail();
        $emailExist = !empty($emailEscola);

        $telefones = $escola->getTelefones();

        $telefone = '';
        if (!empty($telefones)) {
            $telefone = sprintf('(%s) %s', $telefones[0]->iDDD, $telefones[0]->iNumero);
        }

        $this->SetFont('Arial', 'BI', $TamFonteNome);
        $this->Text(33, 9, $nomeInstituicao);
        $this->SetXY(32, 10);
        $this->cellAdapt($TamFonteNome, 90, 3, $nomeEscola);
        $this->SetFont('Arial', 'I', 8);
        $this->Text(33, 16, $ruaEscola . ", " . $numeroEscola . " - " . $bairroEscola);
        $this->Text(33, 20, $cidadeEscola . " - " . $estadoEscola);
        $this->Text(33, 24, $telefone);
        $this->Text(33, 28, ($emailExist ? $emailEscola : ""));
        $this->Text(33, ($emailExist ? 32 : 28), $site);

        $this->imprimeTitulo();

        $this->SetY(35);
        $this->yFimHeader = 35;
    }

    /**
     * Antiga classe do pdf1 , em que se destacou:
     * - O logotipo da prefeitura ficou centralizado;
     * - Os dados da prefeitura ficou resumido ao estado e a prefeitura;
     */
    private function headerEstadoPrefeitura()
    {
        $instituicao = $this->buscaDadosInstituicao();
        $logo = $instituicao->getImagemLogo();

        $filepath = 'imagens/files/' . $logo;
        if ($this->lExibeBrasao && !empty($logo) && file_exists($filepath)) {
            $this->Ln(1);
            $this->Image($filepath, ($this->w / 6) - 15, 8, 20);
        }

        $fonte = "Times";
        $this->Ln(1);
        $this->SetFont($fonte, '', 10);
        $this->MultiCell(0, 4, $instituicao->getUfExtenso(), 0, "C", 0);
        $this->SetFont($fonte, 'B', 13);
        $this->MultiCell(0, 6, $instituicao->getDescricao(), 0, "C", 0);
        $this->SetFont($fonte, 'B', 12);

        $this->Ln(10);
        $this->SetLeftMargin($this->lMargin);
        $this->SetY(40);
        $this->yFimHeader = 40;
    }

    private function headerInstituicao()
    {
        $instituicao = $this->buscaDadosInstituicao();

        $logo = $instituicao->getImagemLogo();
        $filepath = 'imagens/files/' . $logo;
        if ($this->lExibeBrasao && !empty($logo) && file_exists($filepath)) {
            $this->SetXY(1, 1);
            $this->Image($filepath, 7, 3, 20);
        }

        $nome = $instituicao->getDescricao();
        $fonteNomeInstituicao = 9;
        if (strlen($nome) > 42) {
            $fonteNomeInstituicao = 8;
        }

        $this->SetFont('Arial', 'BI', $fonteNomeInstituicao);
        $this->Text(33, 9, $nome);
        $this->SetFont('Arial', 'I', 8);
        $complemento = substr(trim($instituicao->getComplemento()), 0, 20);

        $endereco = sprintf('%s, %s', $instituicao->getLogradouro(), $instituicao->getNumero());
        if (!empty($complemento)) {
            $endereco .= ", {$complemento}";
        }

        $url = $instituicao->getSite();
        $cnpj = db_formatar($instituicao->getCNPJ(), 'cnpj');
        $telefone = trim($instituicao->getTelefone());

        $this->Text(33, 14, $endereco);
        $this->Text(33, 18, "{$instituicao->getMunicipio()} - {$instituicao->getUf()}");
        $this->Text(33, 22, "{$telefone} - {$cnpj}");
        $this->Text(33, 26, trim($instituicao->getEmail()));
        $this->Text(33, 30, $url);

        $this->imprimeTitulo();
        $this->SetY(35);
        $this->yFimHeader = 35;
    }

    /**
     * Função que imprime o quadro dos títulos
     */
    protected function imprimeTitulo()
    {
        $bkpMargin = $this->lMargin;
        $comprim = ($this->w - $this->rMargin - $this->lMargin);
        $marginTitulo = $this->w - 80;

        $this->setleftmargin($marginTitulo);
        $this->sety(6);
        $this->setfillcolor(235);
        $this->roundedrect($marginTitulo - 3, 5, 75, 28, 2, 'DF', '123');
        $this->line(10, 33, $comprim, 33);
        $this->setfillcolor(255);
        $this->SetFont('Arial', '', 7);

        $totalLinhas = 0;
        foreach ($this->titulos as $titulo) {
            $totalLinhas += $this->NbLines(70, $titulo);
            if ($totalLinhas < 10) {
                $this->multicell(70, 3, $titulo, 0, 1, "J", 0);
            }
        }
        $this->setleftmargin($bkpMargin);
    }

    /**
     * @return \Instituicao
     */
    private function buscaDadosInstituicao()
    {
        return InstituicaoRepository::getInstituicaoSessao();
    }

    /**
     * -------------------------------------------------------------------------------------------------------------
     *                               Funções de bibliotecas de terceiros
     * -------------------------------------------------------------------------------------------------------------
     */

    /**
     * @param $x
     * @param $y
     * @param $w
     * @param $h
     * @param $r
     * @param string $style
     */
    public function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));

        $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    public function RectBorderRadius($x, $y, $w, $h, $rtopleft, $rtopright, $rbottomleft, $rbottomright, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        $xc = $x + $w;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);

        if ($rtopleft !== 0 && $rtopright === 0) {
            $this->_out(sprintf('%.2F %.2F m', ($x + $rtopleft) * $k, ($hp - $y) * $k));
            $xc = $x + $w;
        }
        else if ($rtopright !== 0 && $rtopleft === 0) {
            $this->_out(sprintf('%.2F %.2F m', ($x) * $k, ($hp - $y) * $k));
            $xc = $x + $w - $rtopright;
        }
        else if ($rtopleft !== 0 && $rtopright !== 0) {
            $this->_out(sprintf('%.2F %.2F m', ($x + $rtopleft) * $k, ($hp - $y) * $k));
            $xc = $x + $w - $rtopright;
        } else {
            $this->_out(sprintf('%.2F %.2F m', ($x) * $k, ($hp - $y) * $k));
        }

        // Border Radius Top Right
        $yc = $y + $rtopright;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        $this->_Arc($xc + $rtopright * $MyArc, $yc - $rtopright, $xc + $rtopright, $yc - $rtopright * $MyArc, $xc + $rtopright, $yc);

        // Border Radius Bottom Right
        $xc = $x + $w - $rbottomright;
        $yc = $y + $h - $rbottomright;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $rbottomright, $yc + $rbottomright * $MyArc, $xc + $rbottomright * $MyArc, $yc + $rbottomright, $xc, $yc + $rbottomright);

        // Border Radius Bottom Left
        $xc = $x + $rbottomleft;
        $yc = $y + $h - $rbottomleft;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $rbottomleft * $MyArc, $yc + $rbottomleft, $xc - $rbottomleft, $yc + $rbottomleft * $MyArc, $xc - $rbottomleft, $yc);

        // Border Radius Top Left
        $xc = $x + $rtopleft;
        $yc = $y + $rtopleft;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $rtopleft, $yc - $rtopleft * $MyArc, $xc - $rtopleft * $MyArc, $yc - $rtopleft, $xc, $yc - $rtopleft);
        $this->_out($op);
    }

    protected function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c ',
            $x1 * $this->k,
            ($h - $y1) * $this->k,
            $x2 * $this->k,
            ($h - $y2) * $this->k,
            $x3 * $this->k,
            ($h - $y3) * $this->k
        ));
    }

    /**
     * Set the array of column widths
     * @param $w
     */
    function SetWidths($w)
    {
        $this->widths = $w;
    }

    /**
     * Set the array of column alignments
     * @param $a
     */
    function SetAligns($a)
    {
        $this->aligns = $a;
    }

    /**
     * Calculate the height of the row
     * @param $data
     */
    function Row($data)
    {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    /**
     * If the height h would cause an overflow, add a new page immediately
     * @param $h
     */
    function CheckPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    /**
     * Computes the number of lines a MultiCell of width w will take
     * @param $w
     * @param $txt
     * @return int
     */
    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    /**
     * This extension allows to set a dash pattern and draw dashed lines or rectangles.
     *    SetDash([float black, float white])
     *
     *    black: length of dashes
     *    white: length of gaps
     *
     *    Call the function without parameter to restore normal drawing.
     * @param null $black
     * @param null $white
     */
    function SetDash($black = null, $white = null)
    {
        if ($black !== null) {
            $s = sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k);
        } else {
            $s = '[] 0 d';
        }
        $this->_out($s);
    }

    /**
     *  Retorna o altura disponivel para escrita
     * @return float
     */
    public function getAvailableHeight()
    {
        $nAlturaPagina = $this->h;
        $iPosicaoAtual = $this->getY();
        $iMargemBaixa = $this->bMargin;
        $nResultado = ($nAlturaPagina - $iPosicaoAtual) - $iMargemBaixa;
        return (float)$nResultado;
    }

    /**
     * Este método permite a rotação de textos e gráficos
     * A partir da chamada da função todo texto/gráfico gerado será rotacionado
     * conforme o ânglulo e posições.
     * Para voltar a emissão normal passar rotate(0).
     *
     * @param float $angle
     * @param float $x
     * @param float $y
     */
    public function rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1) {
            $x = $this->getX();
        }

        if ($y == -1) {
            $y = $this->getY();
        }

        if ($this->angle != 0) {
            $this->_out('Q');
        }

        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->getH() - $y) * $this->k;
            $this->_out(
                sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy)
            );
        }
    }

    /**
     * Função para emissão de código de barras
     * @param float $xp
     * @param float $yp
     * @param string $text
     * @param float $alt
     * @param float $larg
     *
     */
    public function int25($xp, $yp, $text, $alt, $larg)
    {
        if (empty($text)) {
            return;
        }

        $xpos = $xp;
        $text = strtoupper($text);
        $barcodeheight = $alt;                             // seta a altura das barras
        $barcodethinwidth = $larg;                            // seta a largura da barra estreita
        $barcodethickwidth = $barcodethinwidth * 2.2;          // seta a relacao barra larga/barra estreita

        // seta os codigos dos caracteres, sendo 0 para estreito e 1 para largo
        $codingmap = array(
            "0" => "00110", "1" => "10001",
            "2" => "01001", "3" => "11000",
            "4" => "00101", "5" => "10100",
            "6" => "01100", "7" => "00011",
            "8" => "10010", "9" => "01010"
        );

        // se no. de caracteres impar adiciona 0 no comeco
        if (strlen($text) % 2) {
            $text = "0" . $text;
        }

        $textlen = strlen($text);
        $barcodewidth = ($textlen) * (3 * $barcodethinwidth + 2 * $barcodethickwidth) + ($textlen) * (2.5) +
            (7 * $barcodethinwidth + $barcodethickwidth) + 3;
        // imprime na imagem o codigo de inicio
        $elementwidth = $barcodethinwidth;
        for ($i = 0; $i < 2; $i++) {
            //imagefilledrectangle($im, $xpos, 0, $xpos + $elementwidth - 1 , $barcodeheight, $black);
            $this->Rect($xpos, $yp, $xpos + $elementwidth - $xpos, $barcodeheight, "F");
            $xpos += $elementwidth;
            $xpos += $barcodethinwidth;
            //$elementwidth = $barcodethickwidth;
            //$xpos ++;
        }
        // imprime na imagem o codigo em si
        for ($idx = 0; $idx < $textlen; $idx += 2) {  // a impressao e feita 2 caracteres por vez
            $charimpar = substr($text, $idx, 1);       // pega o caracter impar, que vai ser impresso em preto
            $charpar = substr($text, $idx + 1, 1);   // pega o caracter par, que vai ser impresso em branco
            // interlacamento
            for ($baridx = 0; $baridx < 5; $baridx++) { // a cada bit do codigo dos caracteres
                // imprime a barra coresspondente ao bit do caractere impar (preto)
                $elementwidth = (substr($codingmap[$charimpar], $baridx, 1)) ? $barcodethickwidth : $barcodethinwidth;
                //imagefilledrectangle($im, $xpos,0, $xpos + $elementwidth - 1,$barcodeheight, $black);
                $this->Rect($xpos, $yp, $xpos + $elementwidth - $xpos, $barcodeheight, "F");
                $xpos += $elementwidth;
                // deixa o espaco correspondente ao bit do caractere par (branco)
                $elementwidth = (substr($codingmap[$charpar], $baridx, 1)) ? $barcodethickwidth : $barcodethinwidth;
                $xpos += $elementwidth;
                //$xpos ++;
            }
        }
        // imprime o codigo de final
        $elementwidth = $barcodethickwidth;
        $this->Rect($xpos, $yp, $xpos + $elementwidth - $xpos, $barcodeheight, "F");
        $xpos += $elementwidth;
        $xpos += $barcodethinwidth;
        $elementwidth = $barcodethinwidth;
        $this->Rect($xpos, $yp, $xpos + $elementwidth - $xpos, $barcodeheight, "F");
    }

    /**
     * mudar o angulo do texto
     * @param float $x ;
     * @param float $y ;
     * @param string $txt ;
     * @param string $direction ;
     */
    public function textWithDirection($x, $y, $txt, $direction = 'R')
    {
        $txt = str_replace(')', '\\)', str_replace('(', '\\(', str_replace('\\', '\\\\', $txt)));
        if ($direction == 'R') {
            $s = sprintf(
                'BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
                1,
                0,
                0,
                1,
                $x * $this->k,
                ($this->getH() - $y) * $this->k,
                $txt
            );
        } elseif ($direction == 'L') {
            $s = sprintf(
                'BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
                -1,
                0,
                0,
                -1,
                $x * $this->k,
                ($this->getH() - $y) * $this->k,
                $txt
            );
        } elseif ($direction == 'U') {
            $s = sprintf(
                'BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
                0,
                1,
                -1,
                0,
                $x * $this->k,
                ($this->getH() - $y) * $this->k,
                $txt
            );
        } elseif ($direction == 'D') {
            $s = sprintf(
                'BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
                0,
                -1,
                1,
                0,
                $x * $this->k,
                ($this->getH() - $y) * $this->k,
                $txt
            );
        } else {
            $s = sprintf('BT %.2f %.2f Td (%s) Tj ET', $x * $this->k, ($this->getH() - $y) * $this->k, $txt);
        }

        $this->_out($s);
    }

    /**
     * rotacionar o texto
     * @param float $x ;
     * @param float $y ;
     * @param string $txt ;
     * @param float $txt_angle ;
     * @param float $font_angle ;
     */
    public function textWithRotation($x, $y, $txt, $txt_angle, $font_angle = 0)
    {
        $txt = str_replace(')', '\\)', str_replace('(', '\\(', str_replace('\\', '\\\\', $txt)));

        $font_angle += 90 + $txt_angle;
        $txt_angle *= M_PI / 180;
        $font_angle *= M_PI / 180;

        $txt_dx = cos($txt_angle);
        $txt_dy = sin($txt_angle);
        $font_dx = cos($font_angle);
        $font_dy = sin($font_angle);

        $s = sprintf(
            'BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
            $txt_dx,
            $txt_dy,
            $font_dx,
            $font_dy,
            $x * $this->k,
            ($this->getH() - $y) * $this->k,
            $txt
        );
        $this->_out($s);
    }

    public function writeHTML($html)
    {
        // HTML parser
        $html = str_replace("\n", ' ', $html);
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($a as $i => $e) {
            if ($i % 2 == 0) {
                // Text
                if ($this->HREF) {
                    $this->putLink($this->HREF, $e);
                } else {
                    $this->Write(4, $e);
                }
            } else {
                // Tag
                if ($e[0] == '/') {
                    $this->closeTag(strtoupper(substr($e, 1)));
                } else {
                    // Extract attributes
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach ($a2 as $v) {
                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3)) {
                            $attr[strtoupper($a3[1])] = $a3[2];
                        }
                    }
                    $this->openTag($tag, $attr);
                }
            }
        }
    }

    private function openTag($tag, $attr)
    {
        // Opening tag
        if ($tag == 'B' || $tag == 'I' || $tag == 'U') {
            $this->SetStyle($tag, true);
        }
        if ($tag == 'A') {
            $this->HREF = $attr['HREF'];
        }
        if ($tag == 'BR') {
            $this->Ln(5);
        }
    }

    private function closeTag($tag)
    {
        // Closing tag
        if ($tag == 'B' || $tag == 'I' || $tag == 'U') {
            $this->setStyle($tag, false);
        }
        if ($tag == 'A') {
            $this->HREF = '';
        }
    }

    private function setStyle($tag, $enable)
    {
        // Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach (array('B', 'I', 'U') as $s) {
            if ($this->$s > 0) {
                $style .= $s;
            }
        }
        $this->SetFont('', $style);
    }

    private function putLink($URL, $txt)
    {
        // Put a hyperlink
        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        $this->SetTextColor(0);
    }

    public function quebrarTextoEmLinhas($w, $txt)
    {
        $linhas = [];

        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $string = str_replace("\r", '', $txt);
        $nb = strlen($string);
        if ($nb > 0 and $string[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $string[$i];
            if ($c == "\n") {
                $i++;
                $tamanho = $i - $j;
                $linhas[$nl] = substr($string, $j, $tamanho);
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                    $tamanho = $i - $j;
                    $linhas[$nl] = substr($string, $j, $tamanho);
                    $sep = -1;
                    $j = $i;
                    $l = 0;
                    $nl++;
                }
            } else {
                $i++;
            }
        }
        $tamanho = $i - $j;
        $linhas[$nl] = substr($string, $j, $tamanho);
        return $linhas;
    }

    /**
     * retorna a altura que o header acabou a escrita
     * @return int
     */
    public function yFimHeader()
    {
        return $this->yFimHeader;
    }
}
