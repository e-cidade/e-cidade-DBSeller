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
namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use stdClass;

class ReintegracaoFormatter extends Formatter
{

     /**
     * @var Servidor
     */
    private $servidorAtual;

    /**
     * Realiza a formatação dos dados para envio da API
     *
     * @param array $dados
     * @return array
     */
    public function formatar($servidores)
    {
        $dadosServidores = [];

        foreach ($servidores as $servidor) {
            $dadosServidores[] = $this->processamento($servidor);
        }

        return $dadosServidores;
    }

    /**
     * Realiza uma consistencia nos dados enviados
     *
     * @param array $dadosFormatado
     * @return array
     */
    private function processamento($servidor)
    {
        $dadoServidor = new stdClass();

        $this->servidorAtual = $servidor;
        $dadoServidor->referencia = $this->servidorAtual->getMatricula();
        $dadoServidor->inscricao_empregador = $this->getEmpregador()->getCnpj();
        $dadoServidor->ideVinculo = new stdClass();
        $dadoServidor->ideVinculo->cpfTrab = $this->servidorAtual->getCgm()->getCpf();
        $dadoServidor->ideVinculo->matricula = $this->servidorAtual->getMatricula();
        $this->dadosReintegracaoServidor($dadoServidor);
        
        return $dadoServidor;
    }
    
    private function dadosReintegracaoServidor(&$dadoServidor)
    {
        
        $oDadosReintegracao = $this->servidorAtual->getDadosReintegracao();

        $dadoServidor->infoReintegr = new stdClass();
        
        $dadoServidor->infoReintegr->tpReint = intval($oDadosReintegracao->h25_tiporeintegracao);

        if (!empty($oDadosReintegracao->h25_numeroprocesso)) {
            $dadoServidor->infoReintegr->nrProcJud = $oDadosReintegracao->h25_numeroprocesso;
        }

        if (!empty($oDadosReintegracao->h25_leianistia)) {
            $dadoServidor->infoReintegr->nrLeiAnistia = $oDadosReintegracao->h25_leianistia;
        }

        $dadoServidor->infoReintegr->dtEfetRetorno = $oDadosReintegracao->h25_dataefetivoretorno;
        $dadoServidor->infoReintegr->dtEfeito = $oDadosReintegracao->h25_datareintegracao;

        return $dadoServidor;
    }
}
