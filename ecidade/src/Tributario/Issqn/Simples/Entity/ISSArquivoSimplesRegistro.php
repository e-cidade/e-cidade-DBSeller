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

namespace ECidade\Tributario\Issqn\Simples\Entity;

use BusinessException;
use stdClass;

class ISSArquivoSimplesRegistro
{
    public $q23_sequencial;
    public $q23_issarqsimples;
    public $q23_seqreg;
    public $q23_dtarrec;
    public $q23_dtvenc;
    public $q23_cnpj;
    public $q23_tiporec;
    public $q23_vlrprinc;
    public $q23_vlrmul;
    public $q23_vlrjur;
    public $q23_data;
    public $q23_vlraut;
    public $q23_nroaut;
    public $q23_codbco;
    public $q23_codage;
    public $q23_codsiafi;
    public $q23_codserpro;
    public $q23_anousu;
    public $q23_mesusu;
    public $q23_acao;
    public $inscr_matriz;
    public $cgm_matriz;
    public $cnpj_matriz;
    public $cnpj_vinculados;
    private $filiais = [];
    private $inscricoesParaCancelamento = [];
    private $dadosParaPagamento;

    public function __construct($data)
    {
        $this->setUpProperties($data);
        $this->setUpCnpjVinculados();
        $this->filterInscricoesSemVinculo();
        $this->setUpFilialMatriz();
        $this->setUpDadosParaPagamento();
    }

    private function setUpProperties($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    private function setUpCnpjVinculados()
    {
        if (empty($this->cnpj_vinculados)) {
            $this->cnpj_matriz = $this->q23_cnpj;
            $this->cnpj_vinculados = [];
            return;
        }

        $this->cnpj_vinculados = json_decode($this->cnpj_vinculados);
        if (!is_array($this->cnpj_vinculados)) {
            throw new BusinessException("Erro ao configurar cnpj vinculados [campo invalido]");
        }
    }

    private function setUpFilialMatriz()
    {
        foreach ($this->cnpj_vinculados as $item) {
            $identificador = substr($item->cnpj, 8, 4);
            if ($identificador == '0001') {
                $this->cgm_matriz = $item->cgm;
                $this->inscr_matriz = $item->inscr;
                $this->cnpj_matriz = $item->cnpj;
                continue;
            }
            $this->filiais[] = $item;
        }
    }

    public function setUpDadosParaPagamento()
    {
        $dados = new stdClass;
        $dados->cgm = $this->cgm_matriz;
        $dados->cnpj = $this->cnpj_matriz;
        $dados->inscricao = $this->inscr_matriz;
        $dados->matriz = true;

        if (empty($this->filiais)) {
            $this->dadosParaPagamento = $dados;
            return;
        }

        $filiaisComInscricao = array_filter($this->filiais, function ($filial) {
            return !empty($filial->inscr);
        });

        $filiaisComInscricao = array_values($filiaisComInscricao);

        if (count($filiaisComInscricao) >= 1 && empty($this->inscr_matriz)) {
            $dados->cgm = $filiaisComInscricao[0]->cgm;
            $dados->cnpj = $filiaisComInscricao[0]->cnpj;
            $dados->inscricao = $filiaisComInscricao[0]->inscr;
            $dados->matriz = false;

            if ($filiaisComInscricao > 1) {
                $this->inscricoesParaCancelamento = array_slice($filiaisComInscricao, 1);
            }
        }

        if (count($filiaisComInscricao) >= 1 && !empty($this->inscr_matriz)) {
            $this->inscricoesParaCancelamento = $filiaisComInscricao;
        }

        $this->dadosParaPagamento = $dados;
    }

    public function getCgmVinculos()
    {
        $cnpjPagamento = $this->dadosParaPagamento->cnpj;
        $cgmVinculados = [];
        foreach ($this->cnpj_vinculados as $item) {
            if ($item->cnpj == $cnpjPagamento) {
                $cgmVinculados[] = $item->cgm;
            }
        }
        return array_unique($cgmVinculados);
    }

    public function getInscricaoVinculos()
    {
        $cnpjPagamento = $this->dadosParaPagamento->cnpj;
        $iscricaoVinculadas = [];
        foreach ($this->cnpj_vinculados as $item) {
            if ($item->cnpj == $cnpjPagamento) {
                $iscricaoVinculadas[] = $item->inscr;
            }
        }
        return array_unique($iscricaoVinculadas);
    }

    public function getDadosParaPagamento()
    {
        return $this->dadosParaPagamento;
    }

    public function getInscricoesParaCancelamento()
    {
        return $this->inscricoesParaCancelamento;
    }

    public function getFiliais()
    {
        return $this->filiais;
    }

    private function filterInscricoesSemVinculo()
    {
        /**
         * Agrupar cnpjs
         */
        $cnpjs = [];
        foreach ($this->cnpj_vinculados as $item) {
            if (!isset($cnpjs[$item->cnpj])) {
                $cnpjs[$item->cnpj] = [];
            }
            $cnpjs[$item->cnpj][] = $item;
        }

        /**
         * Remover a partir de regra as inscr sem vinculo
         */
        foreach ($cnpjs as $dados) {
            $countVinculo = 0;
            foreach ($dados as $item) {
                if (!empty($item->vinc)) {
                    $countVinculo++;
                }
            }

            if ($countVinculo > 1 || $countVinculo == 0) {
                continue;
            }

            if ($countVinculo == 1) {
                $this->cnpj_vinculados = array_filter($this->cnpj_vinculados, function ($item) use ($dados) {
                    return !($item->cnpj == $dados[0]->cnpj && empty($item->vinc));
                });
            }
        }
    }
}
