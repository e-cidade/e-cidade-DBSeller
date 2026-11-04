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

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use ECidade\RecursosHumanos\ESocial\Entity\Servidor;
use ECidade\RecursosHumanos\ESocial\Service\ServidorService;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;

use stdClass;
use DBDate;
use CgmJuridico;

class BeneficioTerminoFormatter extends Formatter
{
    /**
     * @var string
     */
    private $inscricao_empregador;

    /**
     * @var CgmJuridico
     */
    private $empregador;

    /**
     * @param array $dados
     * @return array|\Assentamento[]
     * @throws \DBException
     */
    public function formatar($dados)
    {
        $dadosServidor = [];
        $this->inscricao_empregador = $dados->inscricao_empregador;
        foreach ($dados->servidores as $servidor) {
            $this->servidorAtual = $servidor;
            $dadosServidor[] = $this->processar($servidor);
        }
        return $dadosServidor;
    }

    private function processar($servidor)
    {
        $dadoServidor = new \stdClass();
        $dadoServidor->inscricao_empregador = $this->inscricao_empregador;
        $dadoServidor->referencia = $servidor->getMatricula();
        $dadoServidor->ideBeneficio = new \stdClass();
        $dadoServidor->ideBeneficio->cpfBenef = $servidor->getCgm()->getCpf();
        $dadoServidor->ideBeneficio->nrBeneficio = $servidor->getMatricula();

        if ($servidor->getMatriculaAnterior() != "") {
            $dadoServidor->ideBeneficio->nrBeneficio = $servidor->getMatriculaAnterior();
        }

        // Informações da cessação do benefício.
        $infoBenTermino = new \stdClass();
        $infoBenTermino->dtTermBeneficio = $servidor->getDadosRescisao()->rh05_recis;
        $infoBenTermino->mtvTermino = str_pad($servidor->getDadosRescisao()->cessacaobeneficios, 2, "0", STR_PAD_LEFT);
        // TODO a verficiar
        // $infoBenTermino->cnpjOrgaoSuc = ;
        // $infoBenTermino->novoCPF = ;

        $dadoServidor->infoBenTermino = $infoBenTermino;
        return $dadoServidor;
    }
}
