<?php

namespace ECidade\Pdf;

/**
 * Decorator for FPDF
 * Use this class instead of fpdf or other classes that extend from fpdf
 */
class Pdf
{
    /**
     * @var \Fpdf\Pdf
     */
    private $pdf;

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        $this->pdf = new \Fpdf\Pdf($orientation, $unit, $size);
    }

    /**
     * Como basicamente sempre setamos essas funções quando criamos um relatório
     *
     * @param bool $addPage
     * @param bool $exibeHeader
     * @param bool $mostrarRodape
     * @param bool $mostrarEmissor
     * @param bool $mostrarTotalDePaginas
     */
    public function init(
        $addPage = true,
        $exibeHeader = true,
        $mostrarRodape = true,
        $mostrarEmissor = true,
        $mostrarTotalDePaginas = true
    ) {

        if ($exibeHeader) {
            $this->setExibeBrasao(true);
            $this->exibeHeader(true);
        }
        if ($mostrarRodape) {
            $this->mostrarRodape(true);
        }
        if ($mostrarEmissor) {
            $this->mostrarEmissor(true);
        }
        if ($mostrarTotalDePaginas) {
            $this->mostrarTotalDePaginas(true);
        }
        if ($addPage) {
            $this->addPage();
        }

        $this->SetAutoPageBreak(true, 15);
        $this->setFont('Arial', 'B', 8);
        $this->setFillColor(240);
    }


    public function setMargins($left, $top, $right = null)
    {
        $this->pdf->SetMargins($left, $top, $right);
    }

    public function setLeftMargin($margin)
    {
        $this->pdf->SetLeftMargin($margin);
    }

    public function setTopMargin($margin)
    {
        $this->pdf->SetTopMargin($margin);
    }

    public function setRightMargin($margin)
    {
        $this->pdf->SetRightMargin($margin);
    }

    public function setAutoPageBreak($auto, $margin = 0)
    {
        $this->pdf->SetAutoPageBreak($auto, $margin);
    }

    public function setDisplayMode($zoom, $layout = 'default')
    {
        $this->pdf->SetDisplayMode($zoom, $layout);
    }

    public function setCompression($compress)
    {
        $this->pdf->SetCompression($compress);
    }

    public function setTitle($title, $isUTF8 = false)
    {
        $this->pdf->SetTitle($title, $isUTF8);
    }

    public function setAuthor($author, $isUTF8 = false)
    {
        $this->pdf->SetAuthor($author, $isUTF8);
    }

    public function setSubject($subject, $isUTF8 = false)
    {
        $this->pdf->SetSubject($subject, $isUTF8);
    }

    public function setKeywords($keywords, $isUTF8 = false)
    {
        $this->pdf->SetKeywords($keywords, $isUTF8);
    }

    public function setCreator($creator, $isUTF8 = false)
    {
        $this->pdf->SetCreator($creator, $isUTF8);
    }

    public function aliasNbPages($alias = '{nb}')
    {
        $this->pdf->AliasNbPages($alias);
    }

    public function error($msg)
    {
        $this->pdf->Error($msg);
    }

    public function close()
    {
        $this->pdf->Close();
    }

    public function addPage($orientation = '', $size = '', $rotation = 0)
    {
        $this->pdf->AddPage($orientation, $size, $rotation);
    }

    public function pageNo()
    {
        return $this->pdf->PageNo();
    }

    public function setDrawColor($r, $g = null, $b = null)
    {
        $this->pdf->SetDrawColor($r, $g, $b);
    }

    public function setFillColor($r, $g = null, $b = null)
    {
        $this->pdf->SetFillColor($r, $g, $b);
    }

    public function setTextColor($r, $g = null, $b = null)
    {
        $this->pdf->SetTextColor($r, $g, $b);
    }

    public function getStringWidth($s)
    {
        return $this->pdf->GetStringWidth($s);
    }

    public function setLineWidth($width)
    {
        $this->pdf->SetLineWidth($width);
    }

    public function line($x1, $y1, $x2, $y2)
    {
        $this->pdf->Line($x1, $y1, $x2, $y2);
    }

    public function rect($x, $y, $w, $h, $style = '')
    {
        $this->pdf->Rect($x, $y, $w, $h, $style);
    }

    public function addFont($family, $style = '', $file = '')
    {
        $this->pdf->AddFont($family, $style, $file);
    }

    public function setFont($family, $style = '', $size = 0)
    {
        $this->pdf->SetFont($family, $style, $size);
    }

    public function setFontSize($size)
    {
        $this->pdf->SetFontSize($size);
    }

    public function addLink()
    {
        $this->pdf->AddLink();
    }

    public function setLink($link, $y = 0, $page = -1)
    {
        $this->pdf->SetLink($link, $y, $page);
    }

    public function link($x, $y, $w, $h, $link)
    {
        $this->pdf->Link($x, $y, $w, $h, $link);
    }

    public function text($x, $y, $txt)
    {
        $this->pdf->Text($x, $y, $txt);
    }

    public function acceptPageBreak()
    {
        return $this->pdf->AcceptPageBreak();
    }

    public function cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $this->pdf->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }

    public function vcell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false)
    {
        $this->pdf->VCell($w, $h, $txt, $border, $ln, $align, $fill);
    }

    public function multiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false)
    {
        $this->pdf->MultiCell($w, $h, $txt, $border, $align, $fill);
    }

    public function write($h, $txt, $link = '')
    {
        $this->pdf->Write($h, $txt, $link);
    }

    public function ln($h = null)
    {
        $this->pdf->Ln($h);
    }

    public function image($file, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '')
    {
        $this->pdf->Image($file, $x, $y, $w, $h, $type, $link);
    }

    public function getPageWidth()
    {
        return $this->pdf->GetPageWidth();
    }

    public function getPageHeight()
    {
        return $this->pdf->GetPageHeight();
    }

    public function getX()
    {
        return $this->pdf->GetX();
    }

    public function setX($x)
    {
        $this->pdf->SetX($x);
    }

    public function getY()
    {
        return $this->pdf->GetY();
    }

    public function setY($y, $resetX = true)
    {
        $this->pdf->SetY($y, $resetX);
    }

    public function setXY($x, $y)
    {
        $this->pdf->SetXY($x, $y);
    }

    /**
     * @param $dest I - Send to standard output
     *              D - Download file
     *              F - Save to local file
     *              S - Return as a string
     * @param $name
     * @param $isUTF8
     * @return void
     */
    public function output($dest = 'I', $name = '', $isUTF8 = false)
    {
        $this->pdf->Output($dest, $name, $isUTF8);
    }

    /**
     * Adiciona um título para impressão no cabeçalho
     * @param string $titulo texto do título
     * @param string $key chave do título. Usado para substituir a informação em nova página por exemplo
     */
    public function addTitulo($titulo, $key = null)
    {
        $this->pdf->addTitulo($titulo, $key);
    }

    /* Buscando título pela chave */
    public function getTitleByKey($key)
    {
        return $this->pdf->getTitleByKey($key);
    }

    /**
     * Edita um título para impressão no cabeçalho
     * @param $texto -> String exato do título a editar
     * @param $novoTexto -> String do novo texto
     */

    public function editTitleByText($texto, $novoTexto)
    {
        $this->pdf->editTitleByText($texto, $novoTexto);
    }


    /**
     * Edita um título para impressão no cabeçalho pela chave
     * @param $key -> Chave do título a editar
     * @param $novoTexto -> String do novo texto
     */
    public function editTitleByKey($key, $novoTexto)
    {
        $this->pdf->editTitleByKey($key, $novoTexto);
    }

    public function getH()
    {
        return $this->pdf->getH();
    }

    public function getW()
    {
        return $this->pdf->getW();
    }

    public function getLeftMargin()
    {
        return $this->pdf->getLeftMargin();
    }

    public function getRightMargin()
    {
        return $this->pdf->getRightMargin();
    }

    public function getTopMargin()
    {
        return $this->pdf->getTopMargin();
    }

    public function getBottomMargin()
    {
        return $this->pdf->getBottomMargin();
    }

    public function setMulticellBreakPageFunction($sFunction)
    {
        $this->pdf->setMulticellBreakPageFunction($sFunction);
    }

    public function showPageNumber()
    {
        $this->pdf->showPageNumber();
    }

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
        $this->pdf->cellAdapt($fonteOriginal, $w, $h, $content, $border, $ln, $align, $fill, $link);
    }

    /**
     * @param integer $width tamanho da celula
     * @param integer $fontSize tamanho da fonte padrao
     * @param string $content
     * @return float|int
     */
    public function getFonteSizeByWidthCell($width, $fontSize, $content)
    {
        return $this->pdf->getFonteSizeByWidthCell($width, $fontSize, $content);
    }

    public function getAvailableHeight()
    {
        return $this->pdf->getAvailableHeight();
    }

    public function mostrarRodape($lShow = true)
    {
        $this->pdf->mostrarRodape($lShow);
    }

    public function mostrarTotalDePaginas($lShow = true)
    {
        $this->pdf->mostrarTotalDePaginas($lShow);
    }

    public function mostrarEmissor($lShow = true)
    {
        $this->pdf->mostrarEmissor($lShow);
    }

    public function mostrarProgramaEmissor($exibirProgramaEmissor = true)
    {
        $this->pdf->mostrarProgramaEmissor($exibirProgramaEmissor);
    }

    public function exibeHeader($lShow = true, $tipo = \Fpdf\Pdf::HEADER_DEFAULT)
    {
        $this->pdf->exibeHeader($lShow, $tipo);
    }

    public function setExibeBrasao($lExibeBrasao)
    {
        $this->pdf->setExibeBrasao($lExibeBrasao);
    }

    public function roundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $this->pdf->RoundedRect($x, $y, $w, $h, $r, $style);
    }

    public function rectBorderRadius(
        $x,
        $y,
        $w,
        $h,
        $rtopleft = 0,
        $rtopright = 0,
        $rbottomleft = 0,
        $rbottomright = 0,
        $style = ''
    ) {
        $this->pdf->RectBorderRadius($x, $y, $w, $h, $rtopleft, $rtopright, $rbottomleft, $rbottomright, $style);
    }

    public function setWidths($w)
    {
        $this->pdf->SetWidths($w);
    }

    public function setAligns($a)
    {
        $this->pdf->SetAligns($a);
    }

    public function row($data)
    {
        $this->pdf->Row($data);
    }

    public function checkPageBreak($h)
    {
        $this->pdf->CheckPageBreak($h);
    }

    public function nbLines($w, $txt)
    {
        return $this->pdf->NbLines($w, $txt);
    }

    public function quebrarTextoEmLinhas($w, $txt)
    {
        return $this->pdf->quebrarTextoEmLinhas($w, $txt);
    }

    public function setDash($black = null, $white = null)
    {
        $this->pdf->SetDash($black, $white);
    }

    public function rotate($angle = 0, $x = -1, $y = -1)
    {
        $this->pdf->rotate($angle, $x, $y);
    }

    public function int25($xp = 0, $yp = 0, $text = '', $alt = 0, $larg = 0)
    {
        $this->pdf->int25($xp, $yp, $text, $alt, $larg);
    }

    public function textWithDirection($x = 0, $y = 0, $txt = '', $direction = 'R')
    {
        $this->pdf->textWithDirection($x, $y, $txt, $direction);
    }

    public function textWithRotation($x = 0, $y = 0, $txt = '', $txt_angle = 0, $font_angle = 0)
    {
        $this->pdf->textWithRotation($x, $y, $txt, $txt_angle, $font_angle);
    }

    /**
     * @var array
     */
    protected $savePoint;
    protected function savePoint($x = null, $y = null)
    {
        !$x && $x = $this->getX();
        !$y && $y = $this->getY();
        $this->savePoint = [$x, $y];
    }

    protected function rollbackToSavePoint($x = null, $y = null)
    {
        !$x && $x = $this->savePoint[0];
        !$y && $y = $this->savePoint[1];
        $this->setXY($x, $y);
    }

    /**
     * Para facilitar a atualização de alguns pdfs que usam a classe antiga, foi re-implementado
     * Retorna o altura disponivel para escrita
     * @return integer
     */
    public function getAvailHeight()
    {
        $nAlturaPagina = $this->getH();
        $iPosicaoAtual = $this->getY();
        $iMargemBaixa = $this->getBottomMargin();
        $nResultado = ($nAlturaPagina - $iPosicaoAtual) - $iMargemBaixa;
        return (float)$nResultado;
    }

    public function writeHTML($html)
    {
        $this->pdf->writeHTML($html);
    }

    /**
     * retorna o eixo y (altura) que o header acabou a escrita
     * @return int
     */
    public function yFimHeader()
    {
        return $this->pdf->yFimHeader();
    }
}
