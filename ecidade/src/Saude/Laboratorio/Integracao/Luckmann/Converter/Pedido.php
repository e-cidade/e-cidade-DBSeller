<?php
/**
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

namespace ECidade\Saude\Laboratorio\Integracao\Luckmann\Converter;

use DBString;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Collection\TagsXML;
use ECidade\Saude\Laboratorio\Integracao\Luckmann\Validator\DadosArquivosPedidos;
use DOMDocument;
use Exception;

/**
 * Class Pedido
 * @package ECidade\Saude\Laboratorio\Integracao\Luckmann\Converter
 */
class Pedido
{
    /**
     * @var TagsXML
     */
    private $tagsXmlCollection;

    /**
     * @var DOMDocument
     */
    private $xml;

    /**
     * Requisicao constructor.
     * @param TagsXML $tagsXmlCollection
     */
    public function __construct(TagsXML $tagsXmlCollection)
    {
        $this->tagsXmlCollection = $tagsXmlCollection;
    }

    /**
     * @param array $dados
     * @return string
     * @throws Exception
     */
    public function gerarArquivoPedidos(array $dados, $hemato = false, $urinalise = false)
    {
        $dadosArquivosPedidos = new DadosArquivosPedidos($dados);
        $dadosArquivosPedidos->validar();
        $this->criarEstruturaXml();
        $this->atualizarValores($dados);

        $arquivo = 'tmp/pedido' . $dados["CodigoLis"] . '_' . date('Ymd_His') . '.xml';

        if ($hemato) {
            $arquivo = 'tmp/pedido' . $dados["CodigoLis"] . '_' . date('Ymd_His') . '.luck';
        }

        if ($urinalise) {
            $arquivo = 'tmp/pedido' . $dados["CodigoLis"] . '_' . date('Ymd_His') . '.uri';
        }

        $this->xml->save($arquivo);

        return $arquivo;
    }

    private function criarEstruturaXml()
    {
        $this->xml = new DOMDocument('1.0', 'UTF-8');

        foreach ($this->tagsXmlCollection->getAll() as $tagXML) {
            $elemento = $this->xml->createElement($tagXML->getNome());

            if ($tagXML->getCampoPai() === '') {
                $this->xml->appendChild($elemento);
                continue;
            }

            if (!$tagXML->isUnico()) {
                continue;
            }

            $elementoPai = $this->xml->getElementsByTagName($tagXML->getCampoPai())->item(0);
            $elementoPai->appendChild($elemento);
        }
    }

    /**
     * @param array $dados
     */
    private function atualizarValores(array $dados)
    {
        foreach ($dados as $indice => $dado) {
            if ($indice === 'Exames') {
                $this->atualizarExames($dado);
                continue;
            }

            $elemento = $this->xml->getElementsByTagName($indice)->item(0);
            $elemento->appendChild($this->xml->createTextNode(DBString::utf8_encode_all($dado)));
        }
    }

    /**
     * Responsável por criar o bloco de elementos referentes a Exames
     * @param $exames
     */
    private function atualizarExames($exames)
    {
        foreach ($exames as $elementosExame) {
            $elementoExame = $this->xml->createElement('Exame');
            $elementoPai = $this->xml->getElementsByTagName('Exames')->item(0);
            $elementoPai->appendChild($elementoExame);

            foreach ($elementosExame as $chave => $valor) {
                $elemento = $this->xml->createElement($chave, $valor);
                $elementoExame->appendChild($elemento);
            }
        }
    }

    /**
     * @param array $dados
     * @return string
     * @throws Exception
     */
    public function gerarArquivoPedidosInfinity(array $dados)
    {
        $dadosArquivosPedidos = new DadosArquivosPedidos($dados);
        $dadosArquivosPedidos->validar();
        $this->criarEstruturaXml();
        $this->atualizarValores($dados);

        $arquivo = 'tmp/pedido' . $dados["CodigoLis"] . '_' . date('Ymd_His') . '.infinity';

        $this->xml->save($arquivo);

        return $arquivo;
    }
}
