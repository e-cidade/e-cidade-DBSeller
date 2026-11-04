<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao;

use DateTime;
use cl_esocialenviostatus;

/**
 * Class ESocialEnvio
 * @package ECidade\RecursosHumanos\ESocial\Integracao
 */
final class ESocialEnvioStatus
{
    private $sequencial;
    private $esocialenvio;
    private $data;
    private $descricao;
    private $situacao;
    
    const AGUARDANDO_ENVIO = 1;
    const AGUARDANDO_PROCESSAMENTO = 2;
    const AGUARDANDO_ENVIO_EMPREGADOR = 3;
    const PROCESSADO_SUCESSO = 4;
    const PROCESSADO_ERRO = 5;
    const ERRO_ENVIO = 6;
    const ERRO_PROCESSAMENTO = 7;
    const ERRO_CERTIFICADO = 8;
    const AGUARDANDO_ENVIO_DEPENDENCIAS = 9;
    const PROCESSADO_ADVERTENCIA = 12;
    const AGUARDANDO_ENVIO_CONTRIBUINTE = 10;

    public function __construct()
    {
    }

    /**
     * Retorna descricao do status
     *
     * @param $codigoStatus
     * @return string
     * @throws \Exception
     */
    public static function getStatusDescricao($codigoStatus)
    {
        $data = array(
            self::AGUARDANDO_ENVIO => 'Aguardando envio',
            self::AGUARDANDO_PROCESSAMENTO => 'Aguardando processamento',
            self::AGUARDANDO_ENVIO_EMPREGADOR => 'Aguardando envio layout empregador',
            self::PROCESSADO_SUCESSO => 'Processado com sucesso',
            self::PROCESSADO_ERRO => 'Processado com erros',
            self::ERRO_ENVIO => 'Erro no envio',
            self::ERRO_PROCESSAMENTO => 'Erro no processamento',
            self::ERRO_CERTIFICADO => 'Certificado inválido. Verifique a senha ou a data de expiração.',
            self::AGUARDANDO_ENVIO_DEPENDENCIAS => 'Aguardando envio de eventos precedentes.',
            self::PROCESSADO_ADVERTENCIA => 'Processado com advertência',
            self::AGUARDANDO_ENVIO_CONTRIBUINTE => 'Aguardando envio layout contribuinte'
        );

        if (empty($data[$codigoStatus])) {
            throw new \Exception('Status não encontrado.');
        }

        return $data[$codigoStatus];
    }

    public function updateEventStatus($idFila, $status, $message)
    {
        $oDaoEsocialEnvioStatus = new cl_esocialenviostatus();

        $oDaoEsocialEnvioStatus->excluir(null, "rh214_esocialenvio = {$idFila}");

        if ($oDaoEsocialEnvioStatus->erro_status == 0) {
            throw new \Exception("Não foi possível atualizar o status do evento.");
        }

        $oDaoEsocialEnvioStatus->rh214_esocialenvio = $idFila;
        $oDaoEsocialEnvioStatus->rh214_descricao = pg_escape_string($message);
        $oDaoEsocialEnvioStatus->rh214_situacao = $status ? 'true' : 'false';

        $oDaoEsocialEnvioStatus->incluir(null);
        if ($oDaoEsocialEnvioStatus->erro_status == 0) {
            throw new \Exception("Não foi possível incluir o status do evento.");
        }
    }

    /**
     * Get the value of sequencial
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * Set the value of sequencial
     *
     * @return  self
     */
    public function setSequencial($sequencial)
    {
        $this->sequencial = $sequencial;
    }

    /**
     * Get the value of esocialenvio
     */
    public function getEsocialenvio()
    {
        return $this->esocialenvio;
    }

    /**
     * Set the value of esocialenvio
     *
     * @return  self
     */
    public function setEsocialenvio($esocialenvio)
    {
        $this->esocialenvio = $esocialenvio;
    }

    /**
     * Get the value of data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData($data)
    {
        if (!empty($data)) {
            $this->data = new DateTime($data);
        }
    }

    /**
     * Get the value of descricao
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set the value of descricao
     *
     * @return  self
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * Get the value of situacao
     */
    public function getSituacao()
    {
        return $this->situacao;
    }

    /**
     * Set the value of situacao
     *
     * @return  self
     */
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
    }
}
