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

use DBCompetencia;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorOutrosVinculos;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorOutrosVinculosRepository;
use Exception;
use Instituicao;
use stdClass;
use DBPessoal;
use ServidorRepository;

/**
 * Class ServidorOutrosVinculosService
 * @package ECidade\RecursosHumanos\Pessoal\Service
 */
class ServidorOutrosVinculosService
{
    /**
     * @var ServidorOutrosVinculosRepository
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
     * ServidorOutrosVinculosService constructor.
     */
    public function __construct()
    {
        $this->repositorio = new ServidorOutrosVinculosRepository();
        $this->anoCompetencia = DBPessoal::getAnoFolha();
        $this->mesCompetencia = DBPessoal::getMesFolha();
    }

    /**
     * @param int $seqpes
     * @return ServidorOutrosVinculos[]
     * @throws Exception
     */
    public function buscarOutrosVinculosPorMatriculaCompetencia(stdClass $parametros)
    {
        if (empty($parametros->matricula)) {
            throw new Exception("É necessário informar a matrícula.");
        }

        $servidor = ServidorRepository::getInstanciaByCodigo($parametros->matricula);

        return $this->repositorio->scopeAno($this->anoCompetencia)
        ->scopeMes($this->mesCompetencia)->scopeServidor($servidor)->get();
    }

    /**
     * @param DBCompetencia $competenciaAtual
     * @param DBCompetencia $competenciaNova
     * @param Instituicao $instituicao
     * @throws Exception
     */
    public function virarCompetencia(
        DBCompetencia $competenciaAtual,
        DBCompetencia $competenciaNova,
        Instituicao $instituicao
    ) {
        $servidorOutrosVinculos = $this->repositorio->scopeAno($competenciaAtual->getAno())
        ->scopeMes($competenciaAtual->getMes())->scopeInstituicao($instituicao)->get();

        foreach ($servidorOutrosVinculos as $servidorOutroVinculo) {
            $parametros = new stdClass();

            $parametros->tipoContribuicao = $servidorOutroVinculo->getTipoContribuicao();
            $parametros->tipoInscricao = $servidorOutroVinculo->getTipoInscricao();
            $parametros->numeroInscricao = $servidorOutroVinculo->getNumeroInscricao();
            $parametros->codigoCategoria = $servidorOutroVinculo->getCodigoCategoria();
            $parametros->valorRemuneracao = $servidorOutroVinculo->getValorRemuneracao();
            $parametros->mes = $competenciaNova->getMes();
            $parametros->ano = $competenciaNova->getAno();
            $parametros->matricula = $servidorOutroVinculo->getServidor()->getMatricula();
            $this->salvar($parametros);
        }
    }

    /**
     * @param stdClass $parametros
     * @throws Exception
     */
    public function salvar(stdClass $parametros)
    {
        if (empty($parametros->tipoContribuicao)) {
            throw new Exception('Tipo de contribuição não informado.');
        }

        if (empty($parametros->tipoInscricao)) {
            throw new Exception('Tipo de contribuição não informado.');
        }

        if (empty($parametros->numeroInscricao)) {
            throw new Exception('Número da inscrição não informado.');
        }

        if (empty($parametros->codigoCategoria)) {
            throw new Exception('Código da categoria não informado.');
        }

        if (empty($parametros->valorRemuneracao)) {
            throw new Exception('Valor de remuneração não informado.');
        }

        if (empty($parametros->matricula)) {
            throw new Exception('Matrícula não informada');
        }

        $sequencial = !empty($parametros->sequencial) ? $parametros->sequencial : null;
        $anoCompetencia = !empty($parametros->ano) ? $parametros->ano : DBPessoal::getAnoFolha();
        $mesCompetencia = !empty($parametros->mes) ? $parametros->mes : DBPessoal::getMesFolha();

        $servidorOutrosVinculos = new ServidorOutrosVinculos();
        $servidorOutrosVinculos->setSequencial($sequencial);
        $servidorOutrosVinculos->setTipoContribuicao($parametros->tipoContribuicao);
        $servidorOutrosVinculos->setTipoInscricao($parametros->tipoInscricao);
        $servidorOutrosVinculos->setNumeroInscricao($parametros->numeroInscricao);
        $servidorOutrosVinculos->setCodigoCategoria($parametros->codigoCategoria);
        $servidorOutrosVinculos->setValorRemuneracao($parametros->valorRemuneracao);
        $servidorOutrosVinculos->setInstituicao(new Instituicao(db_getsession("DB_instit")));
        $servidorOutrosVinculos->setAno($anoCompetencia);
        $servidorOutrosVinculos->setMes($mesCompetencia);
        $servidorOutrosVinculos->setServidor(ServidorRepository::getInstanciaByCodigo($parametros->matricula));

        $this->repositorio->save($servidorOutrosVinculos);
    }

    /**
     * @param DBCompetencia $competenciaAtual
     * @throws Exception
     */
    public function voltarCompetencia(DBCompetencia $competenciaAtual, Instituicao $instituicao)
    {
        $servidorOutrosVinculos = $this->repositorio
        ->scopeMes($competenciaAtual->getMes())
        ->scopeAno($competenciaAtual->getAno())
        ->scopeInstituicao($instituicao)
        ->get();
        $this->repositorio->resetScopes();

        foreach ($servidorOutrosVinculos as $servidorOutroVinculo) {
            $parametros = new stdClass();
            $parametros->sequencial = $servidorOutroVinculo->getSequencial();

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

        $servidorOutrosVinculos = ServidorOutrosVinculosRepository::find($parametros->sequencial);
        $this->repositorio->delete($servidorOutrosVinculos);
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
}
