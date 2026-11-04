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

use BusinessException;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\DadosESocial;
use ECidade\RecursosHumanos\ESocial\Integracao\FormatterFactory;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use Exception;
use Instituicao;

// phpcs:disable
require_once(modification('libs/db_stdlib.php'));
// phpcs:enable
/**
 * Class Cargo
 * @package ECidade\RecursosHumanos\ESocial\Agendamento\Processamento
 */
class AdmissaoPreliminar extends ProcessamentoAbstract implements ProcessamentoInterface
{
    /**
     * @var
     */
    private $cgm;

    /**
     * Rubrica constructor.
     * @param $cgm
     */
    public function __construct($cgm)
    {
        $this->cgm = $cgm;
    }

    /**
     * @return bool|mixed
     * @throws Exception
     */
    public function processar()
    {
        $bAlteracao = false;
        $oDadosESocial = new DadosESocial();
        
        if (isset($this->dataPreenchidaInicio)) {
            $oDadosESocial->setDataPreenchidaInicial($this->dataPreenchidaInicio);
        }

        if (isset($this->dataPreenchidaFim)) {
            $oDadosESocial->setDataPreenchidaFinal($this->dataPreenchidaFim);
        }
        
        if (sizeof($this->servidores) == 0 &&
            (!isset($this->dataPreenchidaInicio) || !isset($this->dataPreenchidaFim))) {
            throw new BusinessException("Nenhuma matrícula ou data de prenchimento não informada.");
        }

        $oDadosESocial->setInstituicao(new Instituicao(db_getsession('DB_instit')));
        $oDadosESocial->setServidores($this->servidores);
        $oDadosESocial->setCgmEmpregador($this->cgm);
        $oDadosESocial->setReponsavelPeloPreenchimento($this->cgm);

        $oDadosPreenchimento = $oDadosESocial->getPorTipo(Tipo::ADMISSAO_PRELIMINAR);

        $oFormatter = FormatterFactory::get(Tipo::S2190);
        $aDadosPreenchimentoEmpregador = $oFormatter->formatar($oDadosPreenchimento);

        foreach ($aDadosPreenchimentoEmpregador as $oDados) {
            $oEvento = new Evento(TIPO::S2190, $this->cgm, $oDados->referencia, $oDados);

            if ($oEvento->adicionarFila()) {
                $bAlteracao = true;
            }
        }

        return $bAlteracao;
    }
}
