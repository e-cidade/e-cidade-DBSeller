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
 * Class Cat
 *
 * @package ECidade\RecursosHumanos\ESocial\Agendamento\Processamento
 */
class MonitoriamentoSaude extends ProcessamentoAbstract implements ProcessamentoInterface
{
    /**
     * @var
     */
    private $iCgm;

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
     * @return bool|mixed
     * @throws Exception
     */
    public function processar()
    {
        if (empty($this->dataInicial) && empty($this->dataFinal)) {
            $msg = "Não foi informado o período dos Atestado de Saúde Ocupacional. Por favor preencha as datas.";
            throw new \BusinessException($msg);
        }
        if (empty($this->dataInicial)) {
            $msg = "Não foi informada a data inicial dos Atestado de Saúde Ocupacional. Por favor preencha a data.";
            throw new \BusinessException($msg);
        }
        if (empty($this->dataFinal)) {
            $msg = "Não foi informada a data final dos Atestado de Saúde Ocupacional. Por favor preencha a data.";
            throw new \BusinessException($msg);
        }
        include_once modification('libs/db_stdlib.php');

        $bAlteracao = false;
        $iCgm = $this->iCgm;
        $oInstituicao = new Instituicao(db_getsession('DB_instit'));


        $oDadosESocial = new DadosESocial();
        $oDadosESocial->setReponsavelPeloPreenchimento($iCgm);
        $oDadosESocial->setCgmEmpregador($iCgm);
        $oDadosESocial->setInstituicao($oInstituicao);
        $oDadosESocial->setServidores($this->servidores);
        $oDadosESocial->setDataInicial($this->dataInicial);
        $oDadosESocial->setDataFinal($this->dataFinal);
        $oDadosPreenchimento = $oDadosESocial->getPorTipo(Tipo::MONITORIAMENTO_SAUDE);

        $oFormatter = FormatterFactory::get(Tipo::S2220);
        $aDados = $oFormatter->formatar($oDadosPreenchimento);

        $validaMd5 = true;
        if ($this->envioForcado) {
            $validaMd5 = false;
        }

        array_map(
            function (stdClass $oDados) use ($iCgm, &$bAlteracao, $validaMd5) {
                $oEvento = new Evento(Tipo::S2220, $iCgm, $oDados->referencia, $oDados);

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
