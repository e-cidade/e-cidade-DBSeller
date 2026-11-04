<?php

namespace Ecidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Exporters;

use DOMDocument;

class XMLExport
{
    private $dados;
    private $xsd;
    private $dom;
    private $name;
    private $xml;
    private $path;
    public $errors = false;
    private $tagsAttributes;

    /**
     * Construtor
     *
     * @param object $dados Dados a serem convertidos
     * @param string $sNomeArquivo Nome do elemento root
     * @param string $xsd Caminho para o XSD de validacao
     * @param string $path Caminho onde sera salvo o xml
     */
    public function __construct($dados, $sNomeArquivo, $xsd, $path)
    {
        $this->dados = (array)$dados;
        $this->xsd = $xsd;
        $this->dom = new DOMDocument('1.0', 'utf-8');
        $this->name = $sNomeArquivo;
        $this->path = $path;
    }

    /**
     * Adiciona os elementos de forma recursiva
     *
     * @param DOMNode $parent
     * @param mixed $data
     * @return void
     */
    private function addElement($parent, $data)
    {
        foreach ($data as $key => $value) {
            $element = $this->dom->createElement($key);
            if (is_object($value) || is_array($value)) {
                if (is_object($value)) {
                    $this->addElement($element, $value);
                } elseif (is_array($value) && $this->isAssociativeArray($value)) {
                    $this->addElement($element, $value);
                } else {
                    foreach ($value as $subValue) {
                        $this->addElement($element, $subValue);
                    }
                }
            } else {
                if (is_null($value)) {
                    $element->setAttribute('xsi:nil', 'true');
                }

                /*
                 * Verificamos a string e caso possua a TAG <![CDATA[
                 * a informação será ignorada e o elemento será do tipo CDATA
                 */
                $value = str_replace("<![CDATA[", "", $value, $count);
                if ($count > 0) {
                    $element->appendChild($this->dom->createCDATASection($value));
                } else {
                    $element->appendChild($this->dom->createTextNode($value));
                }
            }

            $parent->appendChild($element);
        }
    }

    /**
     * Gera o xml de acordo com os dados do contrutor
     *
     * @return void
     */
    public function buildXML()
    {
        $root = $this->dom->createElement($this->name);
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->tagsAttributes = $this->getTagAttributes();
        $this->addElement($root, $this->dados);
        $this->dom->appendChild($root);

        $this->dom->formatOutput = true;
        $this->xml = $this->dom->saveXML();
        $this->xml =  str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $this->xml);
        $this->validateXML();
        $this->saveXML();

        return $this->xml;
    }

    /**
     * Salva dados do xml em arquivo
     *
     * @return string path do arquivo
     */
    private function saveXML()
    {
        file_put_contents($this->path, $this->xml);
        return $this->name;
    }

    /**
     * Helper para verificar array associativo
     *
     * @param array $array
     * @return boolean
     */
    private function isAssociativeArray($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     *  Valida o xml gerado
     *
     * @return void
     */
    protected function validateXML()
    {
        libxml_use_internal_errors(true);

        if (!$this->dom->schemaValidate($this->xsd)) {
            $this->errors = libxml_get_errors();
            libxml_clear_errors();
        }

        libxml_use_internal_errors(false);
    }

    protected function getTagAttributes()
    {

        $tagsAttributes = [];

        foreach ($this->dados as $key => $value) {
            foreach ($value as $subValue) {
                foreach ($subValue as $properties) {
                    $aProperties = get_object_vars($properties);
                    foreach ($aProperties as $keyProperty => $valueProperty) {
                        if ($keyProperty == 'attributes') {
                            $tagsAttributes[] = $valueProperty;
                        }
                    }
                }
            }
        }

        return $tagsAttributes;
    }
}
