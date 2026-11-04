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
namespace App\Domain\Tributario\ISSQN\Services\SalaoParceiro;

use App\Domain\Tributario\ISSQN\Repository\SalaoParceiro\SalaoParceiroRepository;

class SalaoParceiroService
{
    protected $salaoParceiroRepository;

    public function __construct(SalaoParceiroRepository $salaoParceiroRepository)
    {
        $this->salaoParceiroRepository = $salaoParceiroRepository;
    }

    public function getParceiros($q202_inscr)
    {
        return $this->salaoParceiroRepository->getParceiros($q202_inscr);
    }

    public function getSalao($q202_inscr)
    {
        return $this->salaoParceiroRepository->getSalao($q202_inscr);
    }

    public function getAll($q202_inscr)
    {
        return $this->salaoParceiroRepository->getAll($q202_inscr);
    }

    public function saveParceiros(array $data)
    {
        return $this->salaoParceiroRepository->saveParceiros(
            $data['q202_inscr'],
            $data['q202_dtinicial'],
            $data['q202_obs'],
            $data['q203_inscrparc'],
            $data['q203_numcgm'],
            $data['q203_dtinicial'],
            $data['q203_obs']
        );
    }

    public function updateParceiro(array $data)
    {

        return $this->salaoParceiroRepository->updateParceiro(
            $data['q203_sequencial'],
            $data['q203_dtinicial'],
            $data['q203_dtfinal'],
            $data['q203_obs']
        );
    }


    public function updateSalao(array $data)
    {

        return $this->salaoParceiroRepository->updateSalao(
            $data['q202_sequencial'],
            $data['q202_dtinicial'],
            $data['q202_dtfinal'],
            $data['q202_obs']
        );
    }


    public function getAtividadesSalao($q202_inscr)
    {
        return $this->salaoParceiroRepository->getAtividadesSalao($q202_inscr);
    }

    public function saveAtividadesSalao($data)
    {
        return $this->salaoParceiroRepository->saveAtividadesSalao($data);
    }


    public function getInscricoesDialog($request)
    {
        $porPagina = $request->porPagina ? $request->porPagina : 10;
        $inscricaoMunicipal = $request->inscricao ? $request->inscricao : "";
        $nome = $request->nome ? $request->nome : "";
        $inscricaoAnterior = $request->inscricaoAnterior ? $request->inscricaoAnterior : "";
        $cgcpf = $request->cgcpf ? $request->cgcpf : "";
        $setorFiscal = $request->setorFiscal ? $request->setorFiscal : "";

        $resultados = $this->salaoParceiroRepository->getInscricoesDialog(
            $porPagina,
            $inscricaoMunicipal,
            $nome,
            $inscricaoAnterior,
            $cgcpf,
            $setorFiscal
        )->toArray();

        return $resultados;
    }
}
