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
namespace App\Domain\Tributario\Cadastro\Services\Zoneamento;

use App\Domain\Tributario\Cadastro\Repositories\Zoneamento\ZoneamentoRepository;

class ZoneamentoService
{
    protected $zoneamentoRepository;

    public function __construct(ZoneamentoRepository $zoneamentoRepository)
    {
        $this->zoneamentoRepository = $zoneamentoRepository;
    }

    public function getNextSeqMacrozona()
    {
        return $this->zoneamentoRepository->getNextSeqMacrozona();
    }

    public function getNextSeqZona()
    {
        return $this->zoneamentoRepository->getNextSeqZona();
    }

    public function saveMacroZona($sigla, $localizacao, $descricao)
    {
        return $this->zoneamentoRepository->saveMacroZona($sigla, $localizacao, $descricao);
    }


    public function getMacrozonasDialog($request)
    {
        $porPagina = $request->porPagina ? $request->porPagina : 10;
        $sequencial = $request->sequencial ? $request->sequencial : "";
        $sigla = $request->sigla ? $request->sigla : "";
        $localizacao = $request->localizacao ? $request->localizacao : "";
        $descricao = $request->descricao ? $request->descricao : "";

        $resultados = $this->zoneamentoRepository->getMacrozonasDialog(
            $porPagina,
            $sequencial,
            $sigla,
            $localizacao,
            $descricao
        )->toArray();

        return $resultados;
    }

    public function getZonasDialog($request)
    {
        $porPagina = $request->porPagina ? $request->porPagina : 10;
        $sequencial = $request->sequencial ? $request->sequencial : "";
        $sigla = $request->sigla ? $request->sigla : "";
        $localizacao = $request->localizacao ? $request->localizacao : "";
        $descricao = $request->descricao ? $request->descricao : "";

        $resultados = $this->zoneamentoRepository->getZonasDialog(
            $porPagina,
            $sequencial,
            $sigla,
            $localizacao,
            $descricao
        )->toArray();

        return $resultados;
    }

    public function getMacrozonaById($sequencial)
    {
        return $this->zoneamentoRepository->getMacrozonaById($sequencial);
    }


    public function saveZona(
        $j224_sequencial,
        $j225_sigla,
        $j225_descricao
    ) {
        
        return $this->zoneamentoRepository->saveZona(
            $j224_sequencial,
            $j225_sigla,
            $j225_descricao
        );
    }

    public function updateMacrozona($j224_sequencial, $j224_sigla, $j224_localizacao, $j224_descricao)
    {
        return $this->zoneamentoRepository->updateMacrozona(
            $j224_sequencial,
            $j224_sigla,
            $j224_localizacao,
            $j224_descricao
        );
    }

    public function updateZona($j225_sequencial, $j224_sequencial, $j225_sigla, $j225_descricao)
    {
        return $this->zoneamentoRepository->updateZona(
            $j225_sequencial,
            $j224_sequencial,
            $j225_sigla,
            $j225_descricao
        );
    }
}
