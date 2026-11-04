<?php

namespace ECidade\Integracao\Sped\API\Formatter;

use JSON;

class ConsultaFormatter
{
    const LAYOUT_PATH = 'src/Integracao/Sped/API/Resource/Layouts/';

    /**
     * @var \stdClass
     */
    private $data;

    /**
     * @var \stdClass
     */
    private $formattedData;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var string
     */
    private $layout;

    /**
     * @var array
     */
    private $descriptions;

    /**
     * ConsultaFormatter constructor.
     * @param \stdClass $data
     * @param $layout
     */
    public function __construct(\stdClass $data, $layout)
    {
        if (preg_match('/^R.+/i', $layout)) {
            $refactor = new EFDRetornoRefactor($layout, $data);
            $this->data = $refactor->format();
        } else {
            $this->data = $data;
        }

        $this->layout = $layout;
    }

    /**
     * @return \stdClass
     * @throws \Exception
     */
    public function format()
    {
        $this->configureLayoutDescriptions();
        foreach ($this->data as $prop => $value) {
            $this->addProp($prop, $value);
        }
        return $this->formattedData;
    }

    /**
     * @param $parent
     * @param $property
     * @param bool $isArray
     * @param null $index
     * @throws \Exception
     */
    private function addProp($parent, $property, $isArray = false, $index = null)
    {
        if (is_object($property) || is_array($property)) {
            foreach ($property as $prop => $value) {
                if (is_object($value)) {
                    $this->addProp($prop, $value);
                } elseif (is_array($value)) {
                    foreach ($value as $nprop => $nvalue) {
                        $this->addProp($prop, $nvalue, true, $nprop);
                    }
                } else {
                    $this->addToFormattedData($parent, $prop, $value, $isArray, $index);
                }
            }
        } else {
            $this->addToFormattedData($parent, $parent, $property, $isArray, $index);
        }
    }

    /**
     * @throws \Exception
     */
    private function configureLayoutDescriptions()
    {
        $file = sprintf("%s%s%s", self::LAYOUT_PATH, $this->layout, '.json');

        if (!is_file($file)) {
            throw new \Exception(sprintf('O arquivo %s não foi encontrado.', $file));
        }

        $layout = file_get_contents($file);

        $this->descriptions = (array) JSON::create()->parse($layout);
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    private function getDescription($name)
    {
        if (empty($this->descriptions[strtolower($name)])) {
            return strtolower($name);
        }

        return $this->descriptions[strtolower($name)];
    }

    /**
     * @param $parent
     * @param $name
     * @param $prop
     * @param bool $isArray
     * @param null $index
     * @return mixed
     * @throws \Exception
     */
    private function addToFormattedData($parent, $name, $prop, $isArray = false, $index = null)
    {
        $childrenNode = new \stdClass();
        $childrenNode->name = $name;
        $childrenNode->value = $prop;
        $childrenNode->type = 'property';
        $childrenNode->description = $this->getDescription($name);

        // - Buscamos o nome do Servidor da matricula
        if ($name == "matricula") {
            $instituicao = \InstituicaoRepository::getInstituicaoSessao();
            $servidor = new \Servidor($prop);
            if (!empty($servidor)) {
                $prop .= " - " . $servidor->getCgm()->getNome();
                $childrenNode->value = $prop;
            }
        }

        // nome do prestador de servico
        if ($name == 'cnpjPrestador') {
            $cnpj = preg_replace('/\D/', '', $prop);
            $prestador = \CgmRepository::getNomeByCNPJ($cnpj);
            if ($prestador) {
                $prop .= " - " . $prestador;
                $childrenNode->value = $prop;
            }
        }

        if (empty($this->formattedData[$parent])) {
            $parentNode = new \stdClass();
            $parentNode->name = $parent;
            $parentNode->type = 'group';
            $parentNode->description = $this->getDescription($parent);
            $this->formattedData[$parent] = $parentNode;
        } else {
            $parentNode = $this->formattedData[$parent];
        }

        if ($isArray) {
            if (isset($this->formattedData[$parent]->children)) {
                if (empty($this->formattedData[$parent]->children[$index])
                    && !is_array($this->formattedData[$parent]->children[$index])) {
                    $this->formattedData[$parent]->children[$index] = [];
                }

                if (is_object($this->formattedData[$parent]->children[$index])) {
                    $childrenValid = clone $this->formattedData[$parent]->children[$index];

                    $this->formattedData[$parent]->children[$index] = [];

                    $this->formattedData[$parent]->children[$index][] = $childrenValid;
                }

                return $this->formattedData[$parent]->children[$index][] = $childrenNode;
            }
        } else {
            $parentNode->children[] = $childrenNode;
            return $this->formattedData[$parent] = $parentNode;
        }
    }
}
