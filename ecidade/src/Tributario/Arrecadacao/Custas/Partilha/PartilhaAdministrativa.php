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

namespace ECidade\Tributario\Arrecadacao\Custas\Partilha;

use ECidade\Tributario\Arrecadacao\Custas\Interfaces\Calculo;
use ECidade\Tributario\Juridico\Inicial\Inicial;
use ECidade\Tributario\Juridico\InicialPartilha\Repository;
use \Taxa;

abstract class PartilhaAdministrativa extends Partilha
{
    protected $inicial;

    protected $inicialPartilhaRepository;

    public function __construct(Calculo $calculo, Inicial $inicial)
    {
        parent::__construct($calculo);

        $this->inicial = $inicial;
        $this->inicialPartilhaRepository = Repository\InicialPartilha::getInstance();
    }

    protected function getPartilhasIsentas()
    {
        return $this->inicialPartilhaRepository->getInicialPartilhaIsencao($this->inicial->getCodigo());
    }

    protected function getPartilhasPagas()
    {
        return $this->inicialPartilhaRepository->getInicialPartilhaPago($this->inicial->getCodigo());
    }

    protected function getPartilhasPagasSemHonorarios()
    {
        return $this->inicialPartilhaRepository->getPagoSemHonorariosByInicial($this->inicial);
    }

    protected function getTaxasEmissao()
    {
        return $this->taxaRepository->getTaxasAdministrativas();
    }

    public function processar()
    {
        $partilhasIsentas = $this->getPartilhasIsentas();
        $taxasEmissao = $this->getTaxasEmissao();
        $taxasEmissao = $this->processaRemocaoTaxa($taxasEmissao, $partilhasIsentas);

        return $taxasEmissao;
    }

    public function processaRemocaoTaxa(array $taxas, array $partilhas)
    {
        $taxasRemover = array();

        foreach ($partilhas as $inicialPartilha) {
            foreach ($inicialPartilha->getCustas() as $inicialPartilhaCusta) {
                $taxa = $inicialPartilhaCusta->getTaxa();
                $taxasRemover[] = $taxa->getCodigoTaxa();
            }
        }

        $taxasRemover = array_unique($taxasRemover);

        foreach ($taxas as $i => $taxa) {
            if (in_array($taxa->getCodigoTaxa(), $taxasRemover)) {
                unset($taxas[$i]);
            }
        }

        return $taxas;
    }
}
