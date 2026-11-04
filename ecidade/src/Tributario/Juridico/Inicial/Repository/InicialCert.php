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


namespace ECidade\Tributario\Juridico\Inicial\Repository;

use ECidade\Tributario\Library\Repository;
use ECidade\Tributario\Juridico\Inicial\InicialCert as Model;

final class InicialCert extends Repository
{
    public function make($object)
    {
        if (empty($object)) {
            return null;
        }

        $model = new Model();

        $model->setInicial($object->v51_inicial);
        $model->setCertidao($object->v51_certidao);

        return $model;
    }

    private function makeCollection($array)
    {
        $collection = array();

        if (empty($array)) {
            return $collection;
        }

        foreach ($array as $value) {
            $collection[] = $this->make((object)$value);
        }

        return $collection;
    }

    public function find($codigoInicial, $codigoCertidao)
    {
        $where = "v51_inicial = {$codigoInicial} and v51_certidao = {$codigoCertidao}";
        $sql = $this->dao->sql_query_file(null, "*", null, $where);

        $result = $this->dataBase->execute($sql);

        $object = $this->dataBase->fetchRow($result);

        return $this->make($object);
    }

    public function findAll($where = "")
    {
        $sql = $this->dao->sql_query_file(null, null, "*", null, $where);

        $result = $this->dataBase->execute($sql);

        $array = $this->dataBase->getCollectionByRecord($result);

        return $this->makeCollection($array);
    }

    public function persist(Model $inicialCert)
    {
        $this->dao->v51_inicial = $inicialCert->getInicial();
        $this->dao->v51_certidao = $inicialCert->getCertidao();

        $result = $this->dao->incluir(null);

        if (!$result) {
            $mensagem = 'Ocorreu um erro ao incluir';
            $mensagem .= ' na tabela inicialmov . ' . $this->dao->erro_msg;

            throw new \Exception($mensagem);
        }

        return true;
    }

    public function update($iInicial, $iCertidao, Model $inicialCert)
    {

        $this->dao->v51_inicial = $inicialCert->getInicial();
        $this->dao->v51_certidao = $inicialCert->getCertidao();

        $sqlTipoDebitoInicialPrimaria = " SELECT v59_numpre,k00_tipo
                                            FROM inicialnumpre
                                            INNER JOIN arrecad
                                                on k00_numpre = v59_numpre
                                            WHERE v59_inicial = {$this->dao->v51_inicial}
                                         ";
        $resultTipoDebitoInicialPrimaria = db_query($sqlTipoDebitoInicialPrimaria);
        $dadosInicialPrimaria = pg_fetch_all($resultTipoDebitoInicialPrimaria);

        $sqlTipoDebitoInicialSecundaria = " SELECT v59_numpre,k00_tipo
                                            FROM inicialnumpre
                                            INNER JOIN arrecad
                                                on k00_numpre = v59_numpre
                                            WHERE v59_inicial = {$iInicial}
                                           ";
        $resultTipoDebitoInicialSecundaria = db_query($sqlTipoDebitoInicialSecundaria);
        $dadosInicialSecundaria = pg_fetch_all($resultTipoDebitoInicialSecundaria);

        if (!empty($dadosInicialPrimaria) and !empty($dadosInicialSecundaria)) {
            if ($dadosInicialPrimaria[0]['k00_tipo'] != $dadosInicialSecundaria[0]['k00_tipo']) {
                foreach ($dadosInicialSecundaria as $valores) {
                    db_query("
                                    UPDATE arrecad
                                    SET k00_tipo = {$dadosInicialPrimaria[0]['k00_tipo']}
                                    WHERE k00_numpre IN ({$valores['v59_numpre']})
                                 ");
                }
            }
        }

        $result = $this->dao->alterar($iInicial, $iCertidao);

        if (!$result) {
            $mensagem = 'Ocorreu um erro ao alterar';
            $mensagem .= ' na tabela inicialcert . ' . $this->dao->erro_msg;

            throw new \Exception($mensagem);
        }

        return true;
    }

    public function delete($where)
    {
        $this->dao->excluir(null, null, $where);

        if ($this->dao->erro_status == 0) {
            throw new \Exception("Erro ao excluir registro da tabela arreforo: " . $this->dao->erro_msg);
        }
    }
}
