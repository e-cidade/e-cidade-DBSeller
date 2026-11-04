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
use ECidade\RecursosHumanos\ESocial\DadosESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use Exception;
use Instituicao;
use ParameterException;
use stdClass;

/**
 * Class AvisoPrevio
 *
 * @package ECidade\RecursosHumanos\ESocial\Agendamento\Processamento
 */
class AfastamentoTemporario extends ProcessamentoAbstract implements ProcessamentoInterface
{
    /**
     * @var
     */
    private $iCgm;

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
     * @param $iCgm
     */
    public function __construct($iCgm)
    {
        $this->iCgm = $iCgm;
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
        $iCgm = $this->iCgm;
        $oInstituicao = new Instituicao(db_getsession('DB_instit'));

        if (empty($this->ano) && empty($this->mes)) {
            throw new ParameterException("Ano e mês da competência não informados.");
        }

        if (empty($this->ano)) {
            throw new ParameterException("Ano da competência não informado.");
        }

        if (empty($this->mes)) {
            throw new ParameterException("Mês da competência não informado.");
        }

        $oDadosESocial = new DadosESocial();
        $oDadosESocial->setReponsavelPeloPreenchimento($iCgm);
        $oDadosESocial->setCgmEmpregador($iCgm);
        $oDadosESocial->setInstituicao($oInstituicao);
        $oDadosESocial->setServidores($this->servidores);
        $oDadosESocial->setAno($this->ano);
        $oDadosESocial->setMes($this->mes);
        $oDadosPreenchimento = $oDadosESocial->getPorTipo(Tipo::AFASTAMENTO_TEMPORARIO);

        $oFormatter = FormatterFactory::get(Tipo::S2230);
        $oFormatter->setEmpregador(\CgmRepository::getByCodigo($iCgm));

        $aDados = $oFormatter->formatar($oDadosPreenchimento);
       
        $validaMd5 = true;
        if ($this->envioForcado) {
            $validaMd5 = false;
        }

        array_map(
            function (stdClass $oDados) use ($iCgm, &$bAlteracao, $validaMd5) {
                $oEvento = new Evento(Tipo::S2230, $iCgm, $oDados->referencia, $oDados);

                $bAlteracaoRetorno = $oEvento->adicionarFila(false, $validaMd5);

                if (!$bAlteracao) {
                    $bAlteracao = $bAlteracaoRetorno;
                }
            },
            $aDados
        );

        return $bAlteracao;
    }
}
