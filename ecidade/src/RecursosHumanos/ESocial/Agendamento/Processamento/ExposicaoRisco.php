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
 *  junto com este programa; se nao, escreva para a Free Softwareb
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use App\Domain\RecursosHumanos\Pessoal\Repository\Helper\CompetenciaHelper;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ParameterException;
use ServidorRepository;
use DBCompetencia;
use DBPessoal;
use Exception;
use stdClass;

/**
 * Class PagamentosRendimentosTrabalho
 * @package ECidade\RecursosHumanos\ESocial\Agendamento\Processamento
 */
class ExposicaoRisco extends ProcessamentoAbstract implements ProcessamentoInterface
{
    private $cgm;
    private $mes;
    private $ano;

    /**
     * @param integer $mes
     * Seta o mes da competencia informada
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    /**
     * @param integer $ano
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    public function __construct($cgm)
    {
        $this->competenciaAnterior = DBPessoal::getCompetenciaFolha()->getCompetenciaAnterior();
        $this->cgm = $cgm;
    }

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    public function processar()
    {
        ini_set("memory_limit", "1024M");
        $alteracao = true;
        $dados = new stdClass();

        if (!empty($this->selecao)) {
            $this->ano = CompetenciaHelper::get()->getAno();
            $this->mes = CompetenciaHelper::get()->getMes();

            try {
                $this->servidores = ServidorRepository::getServidoresBySelecao(
                    $this->ano,
                    $this->mes,
                    $this->selecao
                );
            } catch (Exception $e) {
                throw new \DBException("Ocorrêu um erro ao buscar as informações da seleção informada.");
            }
        }

        if (!empty($this->servidores) && sizeof($this->servidores) > 0) {
            $this->ano = CompetenciaHelper::get()->getAno();
            $this->mes = CompetenciaHelper::get()->getMes();
        }

        if (empty($this->ano) || empty($this->mes)) {
            throw new ParameterException("Ano ou mês não informados.");
        }

        $competencia = DBCompetencia::folha();

        if (sizeof($this->servidores) == 0) {
            $dados->servidores =
                ServidorRepository::getServidoresPorCompetencia($competencia->getAno(), $competencia->getMes());
        } else {
            $dados->servidores = $this->servidores;
        }

        /**
         * Buscamos os servidores da competencia atual
         * caso nao seja passado por parametro nenhum servidor
         */

        $dados->inscricao_empregador = \CgmRepository::buscarCNPJEmpregador($this->cgm);
        $dados->ano = $this->ano;
        $dados->mes = $this->mes;

        $formatter = FormatterFactory::get(Tipo::S2240);
        $dadosPreenchimentoEmpregador = $formatter->formatar($dados);

        $validaMd5 = true;
        if ($this->envioForcado) {
            $validaMd5 = false;
        }
        foreach ($dadosPreenchimentoEmpregador as $indice => $dados) {
            $evento = new Evento(TIPO::S2240, $this->cgm, $dados->referencia, $dados);
            $evento->iContador = $indice;

            if ($evento->adicionarFila(false, $validaMd5)) {
                $alteracao = true;
            }
        }

        return $alteracao;
    }
}
