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
use ECidade\RecursosHumanos\Pessoal\Model\ServidorProcessosJudiciaisFolha;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorProcessosJudiciaisFolhaRepository;
use Exception;
use Instituicao;
use stdClass;
use DBPessoal;
use ServidorRepository;

/**
 * Class ServidorProcessosJudiciaisFolhaService
 * @package ECidade\RecursosHumanos\Pessoal\Service
 */
class ServidorProcessosJudiciaisFolhaService
{
    /**
     * @var ServidorProcessosJudiciaisFolhaRepository
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
     * ServidorProcessosJudiciaisFolhaService constructor.
     */
    public function __construct()
    {
        $this->repositorio = new ServidorProcessosJudiciaisFolhaRepository();
        $this->anoCompetencia = DBPessoal::getAnoFolha();
        $this->mesCompetencia = DBPessoal::getMesFolha();
    }

    /**
     * @param int $seqpes
     * @return ServidorProcessosJudiciaisFolha[]
     * @throws Exception
     */
    public function buscarProcessosJudiciaisPorMatriculaCompetencia(stdClass $parametros)
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
        $servidorProcessosJudiciaisFolha = $this->repositorio->scopeAno($competenciaAtual->getAno())
        ->scopeMes($competenciaAtual->getMes())->scopeInstituicao($instituicao)->get();

        foreach ($servidorProcessosJudiciaisFolha as $processoJudicial) {
            $parametros = new stdClass();
            $parametros->mes = $competenciaNova->getMes();
            $parametros->ano = $competenciaNova->getAno();
            $parametros->matricula = $processoJudicial->getServidor()->getMatricula();
            $parametros->codigoIndicativoSuspensao = $processoJudicial->getCodigoIndicativoSuspensao();
            $parametros->tipoProcesso = $processoJudicial->getTipoProcesso();
            $parametros->numeroProcesso = $processoJudicial->getNumeroProcesso();
            $this->salvar($parametros);
        }
    }

    /**
     * @param stdClass $parametros
     * @throws Exception
     */
    public function salvar(stdClass $parametros)
    {
        if (empty($parametros->tipoProcesso)) {
            throw new Exception("Tipo de processo não informado.");
        }

        if (empty($parametros->numeroProcesso)) {
            throw new Exception("Número de processo não informado.");
        }

        if (empty($parametros->matricula)) {
            throw new Exception('Matrícula não informada');
        }

        $sequencial = !empty($parametros->sequencial) ? $parametros->sequencial : null;
        $anoCompetencia = !empty($parametros->ano) ? $parametros->ano : DBPessoal::getAnoFolha();
        $mesCompetencia = !empty($parametros->mes) ? $parametros->mes : DBPessoal::getMesFolha();
        $codigoIndicativoSuspensao = !empty($parametros->codigoIndicativoSuspensao)
        ? $parametros->codigoIndicativoSuspensao : null;

        $servidorProcessosJudiciaisFolha = new ServidorProcessosJudiciaisFolha();
        $servidorProcessosJudiciaisFolha->setSequencial($sequencial);
        $servidorProcessosJudiciaisFolha->setAno($anoCompetencia);
        $servidorProcessosJudiciaisFolha->setMes($mesCompetencia);
        $servidorProcessosJudiciaisFolha->setServidor(ServidorRepository::getInstanciaByCodigo($parametros->matricula));
        $servidorProcessosJudiciaisFolha->setInstituicao(new Instituicao(db_getsession("DB_instit")));
        $servidorProcessosJudiciaisFolha->setTipoProcesso($parametros->tipoProcesso);
        $servidorProcessosJudiciaisFolha->setNumeroProcesso($parametros->numeroProcesso);
        $servidorProcessosJudiciaisFolha->setCodigoIndicativoSuspensao($codigoIndicativoSuspensao);
        
        $this->repositorio->save($servidorProcessosJudiciaisFolha);
    }

    /**
     * @param DBCompetencia $competenciaAtual
     * @throws Exception
     */
    public function voltarCompetencia(DBCompetencia $competenciaAtual, Instituicao $instituicao)
    {
        $servidorProcessosJudiciaisFolha = $this->repositorio
        ->scopeMes($competenciaAtual->getMes())
        ->scopeAno($competenciaAtual->getAno())
        ->scopeInstituicao($instituicao)
        ->get();
        $this->repositorio->resetScopes();

        foreach ($servidorProcessosJudiciaisFolha as $processoJudicial) {
            $parametros = new stdClass();
            $parametros->sequencial = $processoJudicial->getSequencial();

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

        $servidorProcessosJudiciaisFolha = ServidorProcessosJudiciaisFolhaRepository::find($parametros->sequencial);
        $this->repositorio->delete($servidorProcessosJudiciaisFolha);
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
