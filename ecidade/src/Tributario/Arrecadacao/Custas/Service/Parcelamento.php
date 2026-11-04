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

namespace ECidade\Tributario\Arrecadacao\Custas\Service;

use ECidade\Tributario\Arrecadacao\Custas\Interfaces\Service;
use ECidade\Tributario\Arrecadacao\Custas\Validador\FixaJuridicaParcelamentoEmissao;
use ECidade\Tributario\Divida\Termo\Repository\Termo;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForo;

final class Parcelamento implements Service
{
    private $termo;
    private $termoRepository;
    private $processoForoRepository;

    public function __construct($termo)
    {
        $this->termo = $termo;
        $this->termoRepository = Termo::getInstance();
        $this->processoForoRepository = ProcessoForo::getInstance();

        $this->termoRepository->setReturnFullItem(true);
    }

    public function processar()
    {
        $termo = $this->termoRepository->getByCode($this->termo);

        $termoIniciais = $termo->getTermoIniciais();

        if (!empty($termoIniciais)) {
            $processos = array();

            foreach ($termoIniciais as $termoInicial) {
                $inicial = $termoInicial->getInicial();
                
                $processo = $this->processoForoRepository->getByInicial($inicial->getCodigo());
                
                if (!empty($processo)) {
                    $processos[] = $processo;
                }
            }
            
            $processos = array_unique($processos);
            
            foreach ($processos as $processo) {
                $validador = new FixaJuridicaParcelamentoEmissao($termo, $processo);
                
                $validacao = $validador->processarValidacao();
                
                $termoCodigo = $termo->getCodigo();
                $processoForo = $processo->getCodigo();
                
                $fixa = "false";
                if ($validacao) {
                    $fixa = "true";
                }
                
                $termotaxafixaDao = new \cl_termotaxafixa();
                $termotaxafixaDao->ar42_parcel = $termoCodigo;
                $termotaxafixaDao->ar42_processoforo = $processoForo;
                $termotaxafixaDao->ar42_fixa = $fixa;
                $termotaxafixaDao->incluir();
            }
        }
            
        return true;
    }
}
