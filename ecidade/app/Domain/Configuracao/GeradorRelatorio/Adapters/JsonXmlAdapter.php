<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Adapters;

class JsonXmlAdapter
{
    /**
     * @var object
     */
    private $json;

    /**
     * @param object $json
     */
    public function __construct($json)
    {
        $this->json = $json;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getXml()
    {
        $xml = $this->buildXml();

        $this->buildCampos($this->json->campos, $xml);
        $this->buildOrdem($this->json->ordem, $xml);
        $this->buildLayout($this->json->layout, $xml);
        $this->buildVariaveis($this->json->variaveis, $xml);

        $xml->addConsulta();
        $xml->buildXML();

        return $xml->getBuffer();
    }

    /**
     * @return void
     */
    private function requireDependencies()
    {
        require_once modification('libs/db_stdlib.php');
        require_once modification('dbforms/db_funcoes.php');
        require_once modification('model/dbPropriedadeRelatorio.php');
        require_once modification('model/dbVariaveisRelatorio.php');
        require_once modification('model/dbColunaRelatorio.php');
    }

    /**
     * @return \dbGeradorRelatorio
     * @throws \Exception
     */
    private function buildXml()
    {
        $this->requireDependencies();

        $xml = new \dbGeradorRelatorio();
        $xml->setOrigemRelatorio($this->json->origem);
        $xml->addSqlFrom(str_replace(';', '', $this->json->sql));

        return $xml;
    }

    /**
     * @param array $campos
     * @param \dbGeradorRelatorio $xml
     * @return void
     * @throws \Exception
     */
    private function buildCampos($campos, \dbGeradorRelatorio $xml)
    {
        foreach ($campos as $campo) {
            $dbColuna = new \dbColunaRelatorio(
                $campo['codigo'],
                $campo['nome'],
                $campo['alias'],
                $campo['largura'],
                $campo['alinhamento'],
                $campo['alinhamentoCabecalho'],
                $campo['mascara'],
                $campo['totalizar'],
                $campo['quebra']
            );

            $xml->addColuna($dbColuna);
        }
    }

    /**
     * @param array $ordens
     * @param \dbGeradorRelatorio $xml
     * @return void
     * @throws \Exception
     */
    private function buildOrdem($ordens, \dbGeradorRelatorio $xml)
    {
        foreach ($ordens as $ordem) {
            $dbOrdem = new \dbOrdemRelatorio(
                $ordem['codigo'],
                $ordem['nome'],
                $ordem['tipo'],
                $ordem['alias']
            );

            $xml->addOrdem($dbOrdem);
        }
    }

    /**
     * @param object $layout
     * @param \dbGeradorRelatorio $xml
     * @return void
     * @throws \Exception
     */
    private function buildLayout($layout, \dbGeradorRelatorio $xml)
    {
        $dbPropriedade = new \dbPropriedadeRelatorio(
            $layout['nome'],
            $layout['versao'],
            $layout['layout'],
            $layout['formato'],
            $layout['orientacao'],
            $layout['margem']['superior'],
            $layout['margem']['inferior'],
            $layout['margem']['direita'],
            $layout['margem']['esquerda'],
            $layout['tipoSaida'],
            $layout['delimitarTexto'],
            $layout['imprimirCabecalho']
        );

        $xml->addPropriedades($dbPropriedade);
    }

    /**
     * @param array $variaveis
     * @param \dbGeradorRelatorio $xml
     * @return void
     * @throws \Exception
     */
    private function buildVariaveis($variaveis, \dbGeradorRelatorio $xml)
    {
        foreach ($variaveis as $variavel) {
            $dbVariavel = new \dbVariaveisRelatorio(
                $variavel['nome'],
                $variavel['label'],
                $variavel['default'],
                $variavel['tipo'],
                $variavel['sql']
            );

            $xml->addVariavel($variavel['nome'], $dbVariavel);
        }
    }
}
