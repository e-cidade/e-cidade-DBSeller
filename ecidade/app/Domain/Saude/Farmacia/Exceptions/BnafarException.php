<?php

namespace App\Domain\Saude\Farmacia\Exceptions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class BnafarException extends ClientException
{
    /**
     * @var object
     */
    private $erro;

    private $errosAmigaveis = [
        401 => 'Credenciais inválidas!'
    ];

    public function __construct(
        $message,
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $previous = null,
        array $handlerContext = []
    ) {
        $erroJson = $message;
        $erroData = (new \DateTime())->format('Y-m-d h:i:s');
        if ($response !== null) {
            $erroJson = $response->getBody()->getContents();
            $this->erro = (object)json_decode($erroJson);
        }
        file_put_contents('tmp/log_erros_bnafar.log', "[{$erroData}] ERRO: {$erroJson}\n", FILE_APPEND);

        parent::__construct($message, $request, $response, $previous, $handlerContext);
    }

    public function getDetalhes()
    {
        if (property_exists($this->erro, 'status')) {
            return utf8_decode("{$this->erro->status} - {$this->erro->error}\n{$this->erro->message}");
        }

        if (property_exists($this->erro, 'exceptions')) {
            $erro = $this->erro->exceptions[0];
            $campo = '';
            if (property_exists($erro, 'caminho')) {
                $campo = "Campo: {$erro->caminho} ";
            }
            $detalhes = '';
            if (property_exists($erro, 'valorRejeitado')) {
                $detalhes = "Valor rejeitado: {$erro->valorRejeitado}";
            }

            return utf8_decode("{$campo}({$erro->codigo}): {$erro->mensagem} {$detalhes}");
        }

        return $this->getErroAmigavel();
    }

    private function getErroAmigavel()
    {
        if (array_key_exists($this->getResponse()->getStatusCode(), $this->errosAmigaveis)) {
            return sprintf(
                'ERRO %s -- %s. Explicação: %s',
                $this->getResponse()->getStatusCode(),
                $this->getResponse()->getReasonPhrase(),
                $this->errosAmigaveis[$this->getResponse()->getStatusCode()]
            );
        }

        return $this->getMessage();
    }
}
