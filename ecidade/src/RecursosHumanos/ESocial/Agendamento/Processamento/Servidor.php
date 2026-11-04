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
use ECidade\RecursosHumanos\ESocial\DadosESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use Exception;
use Instituicao;
use InstituicaoRepository;
use ServidorRepository;
use CgmRepository;

// phpcs:disable
require_once(modification('libs/db_stdlib.php'));
// phpcs:enable

/**
 * Class Rubrica
 * @package ECidade\RecursosHumanos\ESocial\Agendamento\Processamento
 */
class Servidor extends ProcessamentoAbstract implements ProcessamentoInterface
{
    /**
     * @var
     */
    private $cgm;

    /**
     * @var
     */
    private $instituicao;

    /**
     * Rubrica constructor.
     * @param $cgm
     */
    public function __construct($cgm)
    {
        $this->cgm = (int)$cgm;
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    public function processar()
    {
        $instituicao = InstituicaoRepository::getInstituicaoByCodigo(db_getsession("DB_instit"));

        if (empty($this->getMesCompetencia()) && !empty($this->getAnoCompetencia())) {
            throw new \BusinessException("Mês da competência, não informado.");
        }
        
        if (!empty($this->getMesCompetencia()) && empty($this->getAnoCompetencia())) {
            throw new \BusinessException("Ano da competência, não informado.");
        }
        
        /** Criamos as propriedades internas de ano e mes, devido aos cases de competencia e a necessidade de busca
         *  e necessidade de busca por ano e mes
         */
        $ano = $this->anoCompetencia;
        $mes = $this->mesCompetencia;
        
        if (empty($ano)) {
            $ano = CompetenciaHelper::get()->getAno();
        }
        if (empty($mes)) {
            $mes = CompetenciaHelper::get()->getMes();
        }
        
        if (empty($this->servidores)) {
            if (!empty($this->selecao)) {
                try {
                    $this->servidores = ServidorRepository::getServidoresBySelecao(
                        $ano,
                        $mes,
                        $this->selecao,
                        $this->instituicao
                    );
                } catch (Exception $e) {
                    throw new \DBException("Ocorrêu um erro ao buscar as informações da seleção informada.");
                }
            } else {
                if (!empty($this->anoCompetencia) && !empty($this->mesCompetencia)) {
                    $this->servidores = ServidorRepository::getServidoresPorCompetenciaAdmissao(
                        $ano,
                        $mes,
                        $instituicao
                    );
                } else {
                    $this->servidores = ServidorRepository::getServidoresPorCompetencia($ano, $mes, $instituicao);
                }
            }
        } else {
            $this->setIgnoraValidacao(true);
        }

        if (sizeof($this->servidores) == 0) {
            throw new \BusinessException("Nenhuma matrícula encontrada para o filtro informado");
        }

        $bAlteracao = false;
        $oFormatter = FormatterFactory::get(Tipo::S2200);
        $oFormatter->setEmpregador(CgmRepository::getByCodigo($this->cgm));
        $oFormatter->setIgnoraValidacao($this->getIgnoraValidacao());
        $aDadosPreenchimentoEmpregador = $oFormatter->formatar($this->servidores);
        $validaMd5 = true;
        if ($this->envioForcado) {
            $validaMd5 = false;
        }
        foreach ($aDadosPreenchimentoEmpregador as $iIndice => $oDados) {
            $oEvento = new Evento(Tipo::S2200, $this->cgm, $oDados->referencia, $oDados);
            $oEvento->iContador = $iIndice;

            if ($oEvento->adicionarFila(false, $validaMd5)) {
                $bAlteracao = true;
            }
        }

        return $bAlteracao;
    }

    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
    }
}
