<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2014  DBSeller Servicos de Informatica
 *                      www.dbseller.com.br
 *                   e-cidade@dbseller.com.br
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

namespace ECidade\RecursosHumanos\RH\PontoEletronico\Configuracoes\Model;

/**
 * Class RegistraPontoEletronicoHistorico
 * @package ECidade\RecursosHumanos\RH\PontoEletronico\Configuracoes\Model
 */
class RegistraPontoEletronicoHistorico
{
    /**
     * @var int
     */
    private $sequencial;

    /**
     * @var int
     */
    private $matricula;

    /**
     * @var bool
     */
    private $registraPontoEletronico;

    /**
     * @var \DBDate
     */
    private $data;

    /**
     * @return int
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * @param int $sequencial
     */
    public function setSequencial($sequencial)
    {
        $this->sequencial = $sequencial;
    }

    /**
     * @return int
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * @param int $matricula
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
    }

    /**
     * @return bool
     */
    public function registraPontoEletronico()
    {
        return $this->registraPontoEletronico;
    }

    /**
     * @param bool $registraPontoEletronico
     */
    public function setRegistraPontoEletronico($registraPontoEletronico)
    {
        if (is_bool($registraPontoEletronico)) {
            $this->registraPontoEletronico = $registraPontoEletronico;
        }

        if (is_string($registraPontoEletronico)) {
            $this->registraPontoEletronico = $registraPontoEletronico === 't';
        }
    }

    /**
     * @return \DBDate
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DBDate $data
     */
    public function setData(\DBDate $data)
    {
        $this->data = $data;
    }
}
