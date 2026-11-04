<?php

namespace App\Domain\Configuracao\DocumentosTemplate;

class GenericWordDocumentTemplate extends ProcessaDocumentoTemplate
{
    /**
     * @var array
     */
    private $variables = [];

    /**
     * @return array
     */
    public function getVariables($name = null)
    {
        if ($name) {
            return $this->variables[$name];
        }

        return $this->variables;
    }

    /**
     * @param array $variables
     */
    public function setVariable($variableName, $value)
    {
        $this->variables[$variableName] = $value;
    }

    public function setValidate($validate)
    {
        $this->validar = $validate;
    }

    /**
     * Esta classe deve ser utilizada como uma ponte para a classe abstrata,
     * portante deve ser utilizado os métodos getter e setter
     * @deprecated
     */
    public function configuraDadosVariaveis()
    {
        return $this->getVariables();
    }
}
