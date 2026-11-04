<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Financeiro\Planejamento\Relatorios\Anexos;

use ECidade\Library\SpreadSheet\Template\Parser;

/**
 * Class Xls
 * @package App\Domain\Financeiro\Planejamento\Relatorios\Anexos
 */
abstract class Xls implements XlsAnexo
{
    /**
     * @var Parser
     */
    protected $parser;

    protected $nomeSalvar;

    public function __construct($driveKey, $driveGit, $findSectionsAutomatically = true)
    {
        $this->parser = new Parser();
        $this->parser->findSectionsautomatically($findSectionsAutomatically);
        $this->parser->loadXLS($this->downloadTemplate($driveKey, $driveGit));
    }

    /**
     * PageSetup::ORIENTATION_DEFAULT
     * PageSetup::ORIENTATION_LANDSCAPE
     * PageSetup::ORIENTATION_PORTRAIT
     * @param $orientation
     */
    protected function setOrientation($orientation)
    {
        $this->parser->setOrientation($orientation);
    }

    protected function downloadTemplate($driveKey, $driveGit)
    {
        return getDriveSpreadsheets($driveKey, $driveGit);
    }

    public function setDados(array $dados)
    {
        $this->parser->setData($dados);
    }

    public function addCollection($section, array $dados)
    {
        $this->parser->addCollection($section, $dados);
    }

    public function setVariavel($variavel, $valor)
    {
        $this->parser->addVariable($variavel, utf8_encode($valor));
    }

    public function addImage($image, $position, $options = [])
    {
        $image = "imagens/files/{$image}";
        $this->parser->addImage($image, $position, $options);
    }

    public function gerar()
    {
        $this->parser->parse();
        $this->parser->save($this->nomeSalvar());

        return $this->nomeSalvar();
    }

    public function nomeSalvar()
    {
        if (!$this->nomeSalvar) {
            $nome = str_replace('.xlsx', '_', $this->saveAs);
            $this->nomeSalvar = sprintf('%s_%s.xlsx', $nome, time());
        }

        return $this->nomeSalvar;
    }

    /**
     * @param $enteFederativo
     */
    public function setEnteFederativo($enteFederativo)
    {
        $this->setVariavel('ente_federecao', $enteFederativo);
    }

    /**
     * @param \Instituicao $emissor
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setEmissor(\Instituicao $emissor)
    {
        $endereco = sprintf('%s, %s', $emissor->getLogradouro(), $emissor->getNumero());
        $this->setVariavel('ente_emissor', $emissor->getDescricao());
        $this->setVariavel('endereco_ente', $endereco);
        $this->setVariavel('municipio', $emissor->getMunicipio());
        $this->setVariavel('telefone', $emissor->getTelefone());
        $this->setVariavel('cnpj', $emissor->getCNPJ());
        $this->setVariavel('email', $emissor->getEmail());
        $this->setVariavel('site', $emissor->getSite());

        $this->addImage(
            $emissor->getImagemLogo(),
            'A1',
            ["width" => 100, "height" => 140, 'name' => 'Logo', 'description' => 'Logo municipio', "offsetx" => 5]
        );
    }

    public function setAnoReferencia($ano)
    {
        $this->setVariavel('ano_referencia', 'Ano de referência: ' . $ano);
    }

    public function setNotaExplicativa($notaExplicativa)
    {
        $this->setVariavel('nota_explicativa', $notaExplicativa);
    }

    /**
     * Descrição do período de referência
     * @param $periodoReferencia
     */
    public function setPeriodo($periodoReferencia)
    {
        $this->setVariavel('periodo_referencia', $periodoReferencia);
    }

    /**
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function gerarPdf($filePdf)
    {
        return $this->parser->save($filePdf);
    }
}
