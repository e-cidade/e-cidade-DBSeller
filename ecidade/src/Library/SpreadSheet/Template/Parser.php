<?php

namespace ECidade\Library\SpreadSheet\Template;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class Parser
 * @package ECidade\Library\SpreadSheet\Template
 */
class Parser
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    /**
     * @var array
     */
    private $collection = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * Variaveis
     * @var array
     */
    private $variables = [];

    private $findSections = false;

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        \PhpOffice\PhpSpreadsheet\Settings::setLocale('pt_br');

        StringHelper::setDecimalSeparator(',');
        StringHelper::setThousandsSeparator('.');
        $this->setSpreadSheet(new Spreadsheet());
    }

    /**
     * @param $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function setSpreadSheet(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
        $this->setMargins();
    }

    /**
     * seta as margins default
     * @param float $top
     * @param float $right
     * @param float $botton
     * @param float $left
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function setMargins($top = 0.5, $right = 0.5, $botton = 0.5, $left = 0.5)
    {
        $this->spreadsheet->getActiveSheet()
            ->getPageMargins()
            ->setTop($top)
            ->setRight($right)
            ->setLeft($botton)
            ->setBottom($left);
    }

    /**
     * @return Spreadsheet
     */
    public function getSpreadSheet()
    {
        return $this->spreadsheet;
    }

    private function getActiveSheet()
    {
        return $this->spreadsheet->getActiveSheet();
    }

    public function loadXLS($pathXls)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($pathXls);
        $this->setSpreadSheet($spreadsheet);
    }

    /**
     * Adiciona uam colecao ao parser
     * @param string $session
     * @param array $collection
     */
    public function addCollection($session, array $collection)
    {
        $this->collection["#" . $session . "#"] = $collection;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function parserVariablesAndAutomaticSections()
    {
        $rowIterator = $this->getActiveSheet()->getRowIterator();

        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $valorDaCelula = str_replace(' ', '', $cell->getValue());
                if (empty($valorDaCelula)) {
                    continue;
                }
                if ($this->hasVariableInCell($cell)) {
                    $this->parseVariableInCells($cell);
                }

                if ($this->findSections) {
                    $section = $this->getSectionInCell($cell);
                    if (!empty($section)) {
                        $this->addCollection($section, $this->data);
                    }
                }
            }
        }
    }

    /**
     * REaliza o parse do dados
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function parse()
    {
        $this->parserVariablesAndAutomaticSections();
        $rowIterator = $this->getActiveSheet()->getRowIterator();

        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $valorDaCelula = str_replace(' ', '', $cell->getValue());
                if (empty($valorDaCelula)) {
                    continue;
                }
                $a = explode("(", $valorDaCelula);
                $valorDaCelula = $a[0];
                $processar = false;
                if (isset($this->collection[$valorDaCelula]) && is_array($this->collection[$valorDaCelula])) {
                    $processar = true;
                }
                if (!empty($this->collection[$valorDaCelula]) || $processar) {
                    $linha = $cell->getRow();
                    $configuracoes = "";
                    $re = '/(\{.*\})/m';
                    preg_match_all($re, $cell->getValue(), $matches, PREG_SET_ORDER, 0);
                    if (is_array($matches) && count($matches) > 0) {
                        $configuracoes = json_decode($matches[0][0]);
                    }
                    $this->parseSection($valorDaCelula, $linha, $configuracoes);
                }
            }
        }
    }

    /**
     * realiza a troca da variavel pelo valor
     */
    protected function parseVariableInCells(Cell $cell)
    {

        $cellValue = $cell->getValue();
        foreach ($this->variables as $variable => $value) {
            $cellValue = str_replace($variable, $value, $cellValue);
        }
        $cell->setValue($cellValue);
    }


    /**
     * Verifica se uma celula possui variaveis.
     * @param Cell $cell
     * @return bool
     */
    protected function hasVariableInCell(Cell $cell)
    {
        $rexExp = '/\*(.*?)\*/m';
        $cellValue = $cell->getValue();
        preg_match_all($rexExp, $cellValue, $matches, PREG_SET_ORDER, 0);
        return count($matches) > 0;
    }

    /**
     * Verifica se uma celula possui secao.
     * @param Cell $cell
     * @return string
     */
    protected function getSectionInCell(Cell $cell)
    {
        $rexExp = '/#(.*?)#/m';
        $cellValue = $cell->getValue();
        preg_match_all($rexExp, $cellValue, $matches, PREG_SET_ORDER, 0);
        if (count($matches) > 0) {
            return $matches[0][1];
        }
        return null;
    }

    /**
     * Realiza o parse de uma secao de dados
     * @param string $section
     * @param integer $linha
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function parseSection($section, $linha, $configuracoes = null)
    {
        $this->getActiveSheet()->removeRow($linha);
        $rowIterator = $this->getActiveSheet()->getRowIterator($linha, $linha);
        $header = [];
        /**
         * Parser das colunas, mapeando o valor de secao
         */
        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            foreach ($cellIterator as $cell) {
                $name = str_replace(['$', '{', '}'], "", $cell->getValue());
                $header[$cell->getColumn()] = $name;
            }
        }

        $this->getActiveSheet()->removeRow($linha);
        $planilha = $this->getActiveSheet();
        $dadosSessao = $this->collection[$section];
        $ultimaLinha = $linha + 1;

        /**
         * Realiza o slice dos dados para demonstrar
         */
        if (!empty($configuracoes) && (!empty($configuracoes->offset) || !empty($configuracoes->limit))) {
            $dados = $dadosSessao;
            $fim = count($dados);
            $inicio = 0;
            if (!empty($configuracoes->limit)) {
                $fim = $configuracoes->limit;
            }
            if (!empty($configuracoes->offset)) {
                $inicio = $configuracoes->offset;
            }
            $dadosSessao = array_slice($dados, $inicio, $fim);
        }

        $totalLinhas = count($dadosSessao);
        if ($totalLinhas == 0) {
            $totalLinhas = 1;
        }

        if (empty($configuracoes) || empty($configuracoes->colunainicial)) {
            $planilha->insertNewRowBefore($ultimaLinha, $totalLinhas);
        }

        foreach ($dadosSessao as $linhaProcessar) {
            foreach ($header as $coluna => $item) {
                $valor = '';
                if (empty($item)) {
                    continue;
                }
                if (isset($linhaProcessar->{$item}) && trim($linhaProcessar->{$item}) != '') {
                    $valor = $linhaProcessar->{$item};
                    $valor = utf8_encode($valor);
                }
                if ($valor == '') {
                    continue;
                }

                $cordenadas = $coluna . $linha;
                // se tem formula continue
                if (substr($planilha->getCell($cordenadas), 0, 1) === "=") {
                    continue;
                }
                $planilha->setCellValue($cordenadas, $valor);
            }
            $linha++;
        }
        return true;
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public function getSheet()
    {
        return $this->getActiveSheet();
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function show()
    {

        $rowIterator = $this->getActiveSheet()->getRowIterator();

        foreach ($rowIterator as $row) {
            echo "Linha -> " . $row->getRowIndex() . " -- ";
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            foreach ($cellIterator as $cell) {
                echo $cell->getValue() . " | ";
            }
            echo " \n ";
        }
    }

    /**
     * @param $path
     * @throws \ParameterException
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function save($path)
    {
        $extension = array_reverse(explode(".", $path));
        $extension = $extension[0];
        $writer = null;
        switch ($extension) {
            case 'xlsx':
                $writer = new Xlsx($this->getSpreadSheet());
                break;
            case 'ods':
                $writer = new Ods($this->getSpreadSheet());
                break;
            case 'pdf':
                $writer = new Mpdf($this->getSpreadSheet());
                $writer->setTempDir(ECIDADE_TEMP_MPDF_PATH);
                $writer->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $writer->setPaperSize(PageSetup::PAPERSIZE_A4);
                break;
            default:
                throw new \ParameterException('O tipo de arquivo. ' . $extension . ' não é suportado.');
        }

        $writer->save($path);
    }

    /**
     * @param string $variable
     * @param string $value
     */
    public function addVariable($variable, $value)
    {
        $this->variables["*{$variable}*"] = $value;
    }

    /**
     *
     */
    public function findSectionsautomatically($find)
    {
        $this->findSections = $find;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param $image
     * @param $position
     * @param array $options
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function addImage($image, $position, $options = [])
    {
        if (!file_exists($image)) {
            return;
        }
        $this->getActiveSheet()->setCellValue($position, '');
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

        $drawing->setPath(ECIDADE_PATH . $image);
        $drawing->setCoordinates($position);
        if (!empty($options["width"])) {
            $drawing->setWidth($options["width"]);
        }
        if (!empty($options["height"])) {
            $drawing->setHeight($options["height"]);
        }
        if (!empty($options["description"])) {
            $drawing->setDescription($options["description"]);
        }
        if (!empty($options["name"])) {
            $drawing->setName($options["name"]);
        }
        if (!empty($options["offsetx"])) {
            $drawing->setOffsetX($options["offsetx"]);
        }
        if (!empty($options["offsetY"])) {
            $drawing->setOffsetY($options["offsetY"]);
        }
        $drawing->setWorksheet($this->getActiveSheet(), true);
    }

    /**
     * Abre janela de download do arquivo gerado, ao invés de selecionar destino específico
     */
    public function download($path)
    {
        $extension = array_reverse(explode(".", $path));
        $extension = $extension[0];
        $writer = null;
        switch ($extension) {
            case 'xlsx':
                $writer = new Xlsx($this->getSpreadSheet());
                break;
            case 'ods':
                $writer = new Ods($this->getSpreadSheet());
                break;
            case 'pdf':
                $writer = new Mpdf($this->getSpreadSheet());
                // $writer->setOrientation('P');
                // $writer->setPaperSize('A4');
                $writer->setPaperSize(PageSetup::PAPERSIZE_A4);
                break;
            default:
                throw new \ParameterException('O tipo de arquivo. ' . $extension . ' não é suportado.');
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($path) . '"');
        header('Content-Disposition: attachment; filename="' . urlencode($path) . '"');
        $writer->save('php://output');
    }

    /**
     * Adiciona célula no XLSX, informando devidas coordenadas
     */
    public function addCell($cordenadas, $valor)
    {
        $this->getActiveSheet()->setCellValue($cordenadas, $valor);
    }

    /**
     * Adiciona estilo na célula no XLSX, informando devidas coordenadas e condição de estilo
     *
     * EXEMPLO:
     * $styleArray = [
     *     'font' => [
     *         'bold' => true,
     *         'italic' => true
     *     ],
     *     'alignment' => [
     *         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
     *     ],
     *     'borders' => [
     *         'top' => [
     *             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
     *         ],
     *     ],
     *     'fill' => [
     *         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
     *         'rotation' => 90,
     *         'startColor' => [
     *             'argb' => 'FFA0A0A0',
     *         ],
     *         'endColor' => [
     *             'argb' => 'FFFFFFFF',
     *         ],
     *     ],
     * ];
     *
     *
     */
    public function addCellStyle($coordinates, $styleArray)
    {
        $this->getActiveSheet()->getStyle($coordinates)->applyFromArray($styleArray);
    }

    /*
     * Mescla células
     */
    public function mergeCells($coordinates)
    {
        $this->getActiveSheet()->mergeCells($coordinates);
    }

    public function setOrientation($orientation)
    {
        $this->spreadsheet->getSheet(0)
            ->getPageSetup()
            ->setOrientation($orientation);
    }
}
