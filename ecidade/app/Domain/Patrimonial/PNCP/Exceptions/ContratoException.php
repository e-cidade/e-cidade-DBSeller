<?php

namespace App\Domain\Patrimonial\PNCP\Exceptions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ContratoException extends ClientException
{
    /**
     * @var object
     */
    private $erro;

    public function __construct(
        $message,
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $previous = null,
        array $handlerContext = []
    ) {
        $erroJson = $message;

        if (!empty($response)) {
            $erroJson = $response->getBody()->getContents();
            $this->erro = (object)json_decode($erroJson);
        }

        parent::__construct($this->getMensagemErro(), $request, $response, $previous, $handlerContext);
    }

    public function getMensagemErro()
    {
        if (property_exists($this->erro, 'status')) {
            return mb_convert_encoding("{$this->erro->message}", 'ISO-8859-1');
        }
        if (property_exists($this->erro, 'erros')) {
            return mb_convert_encoding("{$this->erro->erros[0]->mensagem}", 'ISO-8859-1');
        }

        return $this->getMessage();
    }
}
