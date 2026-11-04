<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));

use \Mpdf\Mpdf;

/**
 * Classe para imprimir a consulta do levantamento
 */
class GerarPDFLovrot
{
    /**
     * Nome do PDF
     *
     * @var string|null $name
     */
    private $name;

    /**
     * @var Mpdf $mpdf Classe do MPdf
     */
    private $mpdf;

    /**
     * Formato da folha do PDF
     * 
     * @var string $format default A4
     */
    private $format = 'a4';

    /**
     * Style css
     * 
     * @var string $style
     */
    private $style;

    /**
     * Dados da session
     * 
     * @var array $session
     */
    private $session;

    /**
     * Header Adicional ao PDF
     * 
     * @var array $headerAdd
     */
    private $headerAdd = [];

    /**
     * Função de construção da classe
     * para gerar o PDF com os dados da 
     * consulta do levantamento
     * 
     * @return void
     */
    function __construct($name = null, $format = 'A4', $orientation = 'P')
    {
        $this->name   = $name;
        $this->format = strtoupper($format);

        $this->mpdf  = new Mpdf([
            'tempDir'       => ECIDADE_PATH . 'tmp/',
            'orientation'   => $orientation,
            'margin'        => 0,
            'margin_top'    => 5,
            'margin_left'   => 5,
            'margin_right'  => 5,
            'margin_bottom' => 17,
            'padding'       => 0,
            'format'        => $this->format
        ]);

        $this->style = file_get_contents(__DIR__ . '/style/pdf_lovrot.css');

        $this->config();
    }

    /**
     * Seta o header adicional ao relatorio
     * 
     * @param array $value
     * 
     * @return \GerarPDFLovrot
     */
    public function setHeaderAdd($value)
    {
        if (is_array($value)) {
            $this->headerAdd = $value;
        }

        return $this;
    }
    
    /**
     * Gera um token unico
     *
     * @return string
     */
    private function getTokenUnique()
    {
        return strtoupper(uniqid());
    }

    /**
     * Configurações do PDF
     *
     * @return void
     */
    private function config()
    {
        $this->mpdf->charset_in = 'iso-8859-1';
        $this->mpdf->WriteHTML($this->style, \Mpdf\HTMLParserMode::HEADER_CSS);
    }

    /**
     * Adiciona o header de acordo com o get_session
     * 
     * @return ConsultaLevantamanto
     */
    public function setHeaderInstituicao()
    {
        db_sel_instit();

        global
            $nomeinst,
            $ender,
            $email,
            $munic,
            $telef,
            $cgc,
            $uf,
            $db21_compl,
            $numero,
            $logo,
            $url;

        $cnpj = db_formatar($cgc, 'cnpj');

        $headerInfo = [
            '<b>Exerc&iacute;cio:</b> ' . $this->session['DB_anousu']
        ];

        $headerInfo = array_merge($headerInfo, $this->headerAdd);
        $headerInfo = implode('<br />', $headerInfo);

        $header =<<<HEADER
            <div name="header">
                <div style="float: left; width: 60%;">
                    <img width="78" style="float:left; margin-right: 2em" src="imagens/files/$logo">
                    <h3 style="font-size: 16px">
                        $nomeinst<br>
                        <div style="font-weight: 300; font-size: 12px">
                            $ender, $numero, $db21_compl<br>
                            $munic - $uf<br>
                            $telef - CNPJ: $cnpj<br>
                            $email<br>
                            $url
                        </div>
                    </h3>
                </div>
                <div style="border: solid 1px #000; float: left; width: 39%; height: 115px; background: #eee; border-radius: 1em 1em 1em 0">
                    <div style="padding: 1em 2em; font-size: 12px">
                        {$headerInfo}
                    </div>
                </div>
                <div style="background: #000; height: 1px; margin-top: -1px; width: 90%; margin-right: 10%;">
                </div>
            </div>
HEADER;
 
        $unique = $this->getTokenUnique();

        $this->mpdf->WriteHTML($header);
        
        $deps = mb_convert_encoding($this->session['DB_nomedepto'], 'UTF-8', 'ISO-8859-1');
        $deps = trim($deps);

        $userName = $this->getNameUser();
        $userName = mb_convert_encoding($userName, 'UTF-8', 'ISO-8859-1');

        $footer =<<<FOOTER
            <div style="padding: 0px; margin: 0px;">
                <div style="background: #000; height: 1px; width: 100%;"></div>
                <table style="font-size: 8px; width: 100%; margin: 0px; padding: 0px; border: nome;">
                    <tr style="width: 100%; margin: 0px; padding: 0px; border: nome;">
                        <td style="width: 100%; margin: 0px; padding: 0px; border: nome;">
                            Base: {$this->session['DB_base']} | Sistema: e-Cidade | Usu&aacute;rio: {$userName}
                        </td>
                    </tr>
                </table>
                <table style="width: 100%; margin: 0px; padding: 0px; border: nome; font-size: 8px;">
                    <tr style="margin: 0px; padding: 0px; border: nome;"> 
                        <td style="padding: 0px; margin: 0px; border: nome; text-align: left;">{$deps}</td>
                        <td style="text-align: right; border: nome; padding: 0px; margin: 0px;">
                            {$unique}   - P&aacute;gina {PAGENO} / {nbpg}
                        </td>
                    </tr>
                </table>
            </div>
FOOTER;

        $this->mpdf->SetHTMLFooter($footer);
        $this->mpdf->DefHTMLFooterByName('myFooter', $footer);

        return $this;
    }

    /**
     * Retorna o nome do usuário
     * 
     * @return string
     */
    private function getNameUser()
    {
        $id = db_getsession('DB_id_usuario');
        $result = db_query("select * from configuracoes.db_usuarios where id_usuario = {$id} limit 1;");

        if ($result) {
            $result = pg_fetch_row($result);
            
            return $result[1];
        }

        return '';
    }

    /**
     * Adiciona os dados de session
     * 
     * @param array $data Dados da SessioN
     * 
     * @return \GerarPDFLovrot
     */
    public function setSession($data)
    {
        $this->session = $data;

        return $this;
    }

    /**
     * Aplicar html no PDF
     * 
     * @return \GerarPDFLovrot
     */
    public function montedPageHTML($data)
    {
        $this->mpdf->WriteHTML($data, \Mpdf\HTMLParserMode::HTML_BODY);

        return $this;
    }

    /**
     * Função para imprimir o pdf
     * 
     * @return void
     */
    public function output()
    {
        $this->mpdf->Output();
    }
}
