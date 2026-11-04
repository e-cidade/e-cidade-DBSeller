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

namespace ECidade\RecursosHumanos\Pessoal\Service;

use ECidade\RecursosHumanos\Pessoal\Model\DataPagamentoFolha;
use ECidade\RecursosHumanos\Pessoal\Repository\DataPagamentoFolhaRepository;
use Exception;
use InstituicaoRepository;
use DBDate;
use stdClass;
use DBPessoal;
use DBCompetencia;

/**
 * Class DataPagamentoFolhaService
 * @package ECidade\RecursosHumanos\Pessoal\Service
 */
class DataPagamentoFolhaService
{
    /**
     * @var DataPagamentoFolhaRepository
     */
    private $repositorio;

    /**
     * @var int
     */
    private $anoCompetencia;

    /**
     * @var int
     */
    private $mesCompetencia;

    /**
     * @var int
     */
    private $codigoInstituicao;

    /**
     * DataPagamentoFolhaService constructor.
     */
    public function __construct()
    {
        $this->repositorio = new DataPagamentoFolhaRepository();
        $this->anoCompetencia = DBPessoal::getAnoFolha();
        $this->mesCompetencia = DBPessoal::getMesFolha();
        $this->codigoInstituicao = db_getsession("DB_instit");
    }

    /**
     * @param stdClass $parametros
     * @return DataPagamentoFolha[]
     * @throws Exception
     */
    public function buscarDataPagamentoInstituicaoCompetencia(stdClass $parametros)
    {
        if (empty($parametros->instituicao)) {
            throw new Exception("É necessário informar a instituição.");
        }

        $instituicao = InstituicaoRepository::getInstituicaoByCodigo($parametros->instituicao);
        $anoCompetencia = !empty($parametros->ano) ? $parametros->ano : $this->anoCompetencia;
        $mesCompetencia = !empty($parametros->mes) ? $parametros->mes : $this->mesCompetencia;
        return $this->repositorio->scopeAno($anoCompetencia)->scopeMes($mesCompetencia)->scopeInstituicao($instituicao)
            ->get();
    }

    /**
     * @param DBCompetencia $competenciaAtual
     * @throws Exception
     */
    public function virarCompetencia(DBCompetencia $competenciaAtual, $dataPagamento)
    {
        $parametros = new stdClass();
        $parametros->mes =  $competenciaAtual->getMes();
        $parametros->ano =  $competenciaAtual->getAno();
        $parametros->dataPagamento = "{$dataPagamento}";
        $this->salvar($parametros);
    }

    /**
     * @param stdClass $parametros
     * @throws Exception
     */
    public function salvar(stdClass $parametros)
    {
        if (empty($parametros->dataPagamento)) {
            throw new Exception('Data de pagamento não informado.');
        }

        $sequencial = !empty($parametros->sequencial) ? $parametros->sequencial : null;
        $anoCompetencia = !empty($parametros->ano) ? $parametros->ano : $this->anoCompetencia;
        $mesCompetencia = !empty($parametros->mes) ? $parametros->mes : $this->mesCompetencia;
        $instituicao = !empty($parametros->instituicao) ? $parametros->instituicao : $this->codigoInstituicao;

        $dataPagamentoFolha = new DataPagamentoFolha();
        $dataPagamentoFolha->setSequencial($sequencial);
        $dataPagamentoFolha->setInstituicao(InstituicaoRepository::getInstituicaoByCodigo($instituicao));
        $dataPagamentoFolha->setAno($anoCompetencia);
        $dataPagamentoFolha->setMes($mesCompetencia);
        $dataPagamentoFolha->setDataPagamento(new DBDate($parametros->dataPagamento));

        $this->repositorio->save($dataPagamentoFolha);
    }

    /**
     * @param DBCompetencia $competenciaAnterior
     * @throws Exception
     */
    public function voltarCompetencia(DBCompetencia $competenciaAnterior)
    {
        $datasPagamentosFolha = $this->repositorio
                                            ->scopeMes($competenciaAnterior->getMes())
                                            ->scopeAno($competenciaAnterior->getAno())
                                            ->scopeInstituicao(InstituicaoRepository::getInstituicaoByCodigo(
                                                $this->codigoInstituicao
                                            ))->get();

        $this->repositorio->resetScopes();

        foreach ($datasPagamentosFolha as $dataPagamento) {
            $parametros = new stdClass();
            $parametros->sequencial = $dataPagamento->getSequencial();

            $this->excluir($parametros);
        }
    }

    /**
     * @param stdClass $parametros
     * @throws Exception
     */
    public function excluir(stdClass $parametros)
    {
        if (empty($parametros->sequencial)) {
            throw new Exception('É necessário informar o código sequencial.');
        }

        $dataPagamentoFolha = DataPagamentoFolhaRepository::find($parametros->sequencial);
        $this->repositorio->delete($dataPagamentoFolha);
    }

    /**
     * @param int $anoCompetencia
     */
    public function setAnoCompetencia($anoCompetencia)
    {
        $this->anoCompetencia = $anoCompetencia;
    }

    /**
     * @param int $mesCompetencia
     */
    public function setMesCompetencia($mesCompetencia)
    {
        $this->mesCompetencia = $mesCompetencia;
    }

    /**
     * @param stdClass $parametros
     * @return DataPagamentoFolha[]
     * @throws Exception
     */
    public function buscarDataPagamentoInstituicaoCompetenciaCaixa(stdClass $parametros)
    {
        if (empty($parametros->instituicao)) {
            throw new Exception("É necessário informar a instituição.");
        }

        $instituicao = InstituicaoRepository::getInstituicaoByCodigo($parametros->instituicao);
        $anoCompetencia = !empty($parametros->ano) ? $parametros->ano : $this->anoCompetencia;
        $mesCompetencia = !empty($parametros->mes) ? $parametros->mes : $this->mesCompetencia;
        return $this->repositorio->scopeCompetenciaCaixa($anoCompetencia, $mesCompetencia)
            ->scopeInstituicao($instituicao)
            ->get();
    }
}
