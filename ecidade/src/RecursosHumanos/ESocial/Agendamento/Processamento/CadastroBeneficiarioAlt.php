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
namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Repository\CadastroBeneficiario as CadastroBeneficiarioRepository;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

use Exception;
use ParameterException;
use stdClass;
use DBCompetencia;
use CgmRepository;

/**
 * Class CadastroBeneficiarioAlt
 *
 * @package ECidade\RecursosHumanos\ESocial\Agendamento\Processamento
 */
class CadastroBeneficiarioAlt extends ProcessamentoAbstract implements ProcessamentoInterface
{
    /**
     * @var
     */
    private $cgm;

    /**
     * @var
     */
    private $ano;

    /**
     * @var
     */
    private $mes;

    /**
     * LotacaoTributaria constructor.
     *
     * @param $cgm
     */
    public function __construct($cgm)
    {
        $this->cgm = $cgm;
    }

        /**
     * @param $ano
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    /**
     * @param $mes
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    public function processar()
    {
        include_once modification('libs/db_stdlib.php');
        $bAlteracao = false;

        if (empty($this->ano) && empty($this->mes)) {
            $competencia = \DBPessoal::getCompetenciaFolha();
            $this->ano = $competencia->getAno();
            $this->mes = $competencia->getMes();
        }

        if (empty($this->ano)) {
            throw new ParameterException("Ano da competência não informado.");
        }

        if (empty($this->mes)) {
            throw new ParameterException("Mês da competência não informado.");
        }

        $competencia =  new DBCompetencia($this->ano, $this->mes);
        $dados = new stdClass();
        $dados->ano = $this->ano;
        $dados->mes = $this->mes;
        $dados->beneficiarios = CadastroBeneficiarioRepository::buscarBeneficiarios(
            $competencia,
            $this->servidores,
            $this->selecao
        );

        $formatter = FormatterFactory::get(Tipo::S2405);
        $formatter->setEmpregador(CgmRepository::getByCodigo($this->cgm));
        $formatter->setIgnoraValidacao($this->getIgnoraValidacao());
        $cadastroBeneficiario = $formatter->formatar($dados);
        $validaMd5 = true;
        if ($this->envioForcado) {
            $validaMd5 = false;
        }
        foreach ($cadastroBeneficiario as $iIndice => $oDados) {
            $oEvento = new Evento(Tipo::S2405, $this->cgm, $oDados->referencia, $oDados);
            $oEvento->iContador = $iIndice;

            if ($oEvento->adicionarFila(false, $validaMd5)) {
                $bAlteracao = true;
            }
        }

        return $bAlteracao;
    }
}
