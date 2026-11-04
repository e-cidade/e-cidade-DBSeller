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


require_once modification("interfaces/GeradorRelatorio.interface.php");

/**
 * @todo refazer essa classe em um estrutura mais moderna quando o Agata for descontinuado
 */
final class dbColunaRelatorio implements iGeradorRelatorio
{

    public $iId = "";

    public $sNome = "";

    public $sAlias = "";

    public $iLargura = "";

    public $sAlinhamento = "";

    public $sAlinhamentoCab = "";

    public $sMascara = "";

    public $sTotalizar = "";

    public $lQuebra = "";

    public $oxml = "";

    /**
     * Método construtor da classe
     *
     * @param string $iId
     * @param string $sNome
     * @param string $sAlias
     * @param string $iLargura
     * @param string $sAlinhamento
     * @param string $sAlinhamentoCab
     * @param string $sMascara
     *
     */

    public function __construct(
        $iId = "",
        $sNome = "",
        $sAlias = "",
        $iLargura = "",
        $sAlinhamento = "",
        $sAlinhamentoCab = "",
        $sMascara = "",
        $sTotalizar = "",
        $lQuebra = false
    ) {
        $this->setId($iId);
        $this->setNome($sNome);
        $this->setAlias($sAlias);
        $this->setLargura($iLargura);
        $this->setAlinhamento($sAlinhamento);
        $this->setAlinhamentoCab($sAlinhamentoCab);
        $this->setMascara($sMascara);
        $this->setTotalizar($sTotalizar);
        $this->setQuebra($lQuebra);
    }

    /**
     * Retorna ID do campo
     *
     * @return integer
     */
    public function getId()
    {
        return $this->iId;
    }

    /**
     * Retorna o nome do campo
     *
     * @return string
     */
    public function getNome()
    {
        if (db_utils::isUTF8($this->sNome)) {
            return mb_convert_encoding($this->sNome, 'ISO-8859-1', 'UTF-8');
        } else {
            return $this->sNome;
        }
    }

    /**
     * Retorna largura do campo
     *
     * @return integer
     */
    public function getLargura()
    {
        return $this->iLargura;
    }

    /**
     * Retorna alias do Campo
     *
     * @return string
     */
    public function getAlias()
    {
        if (db_utils::isUTF8($this->sAlias)) {
            return mb_convert_encoding($this->sAlias, 'ISO-8859-1', 'UTF-8');
        } else {
            return $this->sAlias;
        }
    }

    /**
     * Retorna alinhamento do campo
     *
     * @return string
     */
    public function getAlinhamento()
    {
        return $this->sAlinhamento;
    }

    /**
     * Retorna alinhamento do cabeçalho referente ao campo no relatório
     *
     * @return string
     */
    public function getAlinhamentoCab()
    {
        return $this->sAlinhamentoCab;
    }

    /**
     * Retorna a máscara do campo
     *
     * @return string
     */
    public function getMascara()
    {
        return $this->sMascara;
    }

    /**
     * Retorna a totalizador do campo
     *
     * @return string
     */
    public function getTotalizar()
    {
        return $this->sTotalizar;
    }

    /**
     * @return bool
     */
    public function getQuebra()
    {
        return $this->lQuebra;
    }

    /**
     * @param integer $iId
     */
    public function setId($iId)
    {
        $this->iId = $iId;
    }

    /**
     * @param integer $iLargura
     */
    public function setLargura($iLargura)
    {
        $this->iLargura = $iLargura;
    }

    /**
     * @param string $sAlias
     */
    public function setAlias($sAlias)
    {
        $this->sAlias = $sAlias;
    }

    /**
     * @param string $sAlinhamento
     */
    public function setAlinhamento($sAlinhamento)
    {
        $this->sAlinhamento = $sAlinhamento;
    }

    /**
     * @param string $sAlinhamentoCab
     */
    public function setAlinhamentoCab($sAlinhamentoCab)
    {
        $this->sAlinhamentoCab = $sAlinhamentoCab;
    }

    /**
     * @param string $sMascara
     */
    public function setMascara($sMascara)
    {
        $this->sMascara = $sMascara;
    }

    /**
     * @param string $sNome
     */
    public function setNome($sNome)
    {
        $this->sNome = $sNome;
    }

    /**
     * @param string $sTotalizar
     */
    public function setTotalizar($sTotalizar)
    {
        $this->sTotalizar = $sTotalizar;
    }

    /**
     *
     * @param bool $lQuebra
     */
    public function setQuebra($lQuebra)
    {
        $this->lQuebra = $lQuebra;
    }

    /**
     * Retorna estrutura XML das propriedades da classe
     *
     * @return true
     */
    public function toXml(XMLWriter $oXmlWriter)
    {
        $oXmlWriter->startElement('Campo');

        $oXmlWriter->writeAttribute('id', mb_convert_encoding($this->iId, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('nome', mb_convert_encoding($this->sNome, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('alias', mb_convert_encoding($this->sAlias, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('largura', mb_convert_encoding($this->iLargura, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('alinhamento', mb_convert_encoding($this->sAlinhamento, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute(
            'alinhamentocab',
            mb_convert_encoding($this->sAlinhamentoCab, 'UTF-8', 'ISO-8859-1')
        );
        $oXmlWriter->writeAttribute('mascara', mb_convert_encoding($this->sMascara, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('totalizar', mb_convert_encoding($this->sTotalizar, 'UTF-8', 'ISO-8859-1'));
        $oXmlWriter->writeAttribute('quebra', mb_convert_encoding($this->lQuebra, 'UTF-8', 'ISO-8859-1'));

        $oXmlWriter->endElement();

        return true;
    }
}
