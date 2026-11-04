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

namespace ECidade\RecursosHumanos\ESocial\Mapeadores;

use Avaliacao;
use Exception;

class AfastamentoTemporarioMapeador extends AvaliacaoMapeador
{
    private $camposDinamicos = array(
        "cnpj_entidade_esocial" => "cnpjCess",
        "cnpj_sindicato_esocial" => "cnpjSind",
        "motivo_esocial" => "codMotAfast",
        "observacoes_esocial" => "observacao",
        "tipo_acidente_transito_esocial" => "tpAcidTransito",
        "tipo_cessao_esocial" => "infOnus",
        "tipo_onus_esocial" => "infOnusRemun",
        "dtInicio_esocial" => "dtInicio",
        "dtFim_esocial" => "dtFim",
        "indRemunCargo_esocial" => "indRemunCargo",
        "cnpjMandElet_esocial" => "cnpjMandElet",
        "infoMesmoMtv_esocial" => "infoMesmoMtv"
    );

    private $campos = array(
        "z01_cgccpf" => "cpfTrab",
        "h16_regist" => "matricula",
        "h16_dtconc" => "dtIniAfast",
        "h16_dtterm" => "dtTermAfast",
        "rh220_origemretificacao" => "origRetif",
        "rh220_tipoprocesso" => "tpProc",
        "rh220_numeroprocesso" => "nrProc"
    );


    /**
     * @var integer
     */
    private $assentamentoId;

    /**
     * AfastamentoTemporarioMapeador constructor.
     * @param Avaliacao $avaliacao
     * @param int $assetamentoId
     */
    public function __construct(Avaliacao $avaliacao, $assentamentoId)
    {
        $this->assentamentoId = $assentamentoId;
        $this->avaliacao = $avaliacao;
    }

    private function parseCamposDinamicos()
    {
        $dao = new \cl_assentadb_cadattdinamicovalorgrupo();
        $sql = $dao->sql_campos_dinamicos_por_assentamento($this->assentamentoId);
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar os atributos dinâmicos.");
        }

        $valoresDinamicos = \db_utils::getCollectionByRecord($rs);

        foreach ($valoresDinamicos as $item) {
            if (!empty($item->campo)) {
                $identificadorCampo = $this->camposDinamicos[$item->campo];
                $this->addDePara($identificadorCampo, $identificadorCampo, $item->valor);
            }
        }
    }

    private function parseAssentamento()
    {
        $dao = new \cl_assenta();
        $rs = db_query($dao->sql_dados_afastamento_temporario($this->assentamentoId));

        if (!$rs) {
            throw new Exception("Não foi possível buscar os dados do assentamento.");
        }

        while ($row = pg_fetch_assoc($rs)) {
            foreach ($row as $indice => $valor) {
                $identificadorCampo = $this->campos[$indice];
                $this->addDePara($identificadorCampo, $identificadorCampo, $valor);
            }
        }
    }

    /**
     * @return object
     * @throws Exception
     */
    public function parseAvaliacao()
    {
        $this->parseAssentamento();
        $this->parseCamposDinamicos();
        $this->buscaMesmoMotivo();
        return parent::parseAvaliacao();
    }

    /**
     * @return int
     */
    public function getAssentamentoId()
    {
        return $this->assentamentoId;
    }

    /**
     * @param int $assentamentoId
     */
    public function setAssentamentoId($assentamentoId)
    {
        $this->assentamentoId = $assentamentoId;
    }

    /**
     * @throws Exception
     */
    public function buscaMesmoMotivo()
    {
        $idMotivo = $this->getPropriedadeDePara("codMotAfast", "codMotAfast");
        if (!in_array($idMotivo, array(1, 3))) {
            return;
        }

        $matricula = $this->getPropriedadeDePara("matricula", "matricula");
        $dataInicio = $this->getPropriedadeDePara("dtIniAfast", "dtIniAfast");

        $dao = new \cl_assentadb_cadattdinamicovalorgrupo();
        $where = "h16_codigo <> {$this->assentamentoId}";
        $where .= " AND h16_regist = {$matricula}";
        $where .= " AND db109_nome = 'motivo_esocial'";
        $where .= " AND db_cadattdinamicoatributosvalor.db110_valor = '{$idMotivo}'";
        $where .= " AND h16_dtconc between ('{$dataInicio}'::date - interval '60 day')::date and '{$dataInicio}'";

        $sql = $dao->sql_campos_dinamicos_por_assentamento(null, $where);
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Ocorreu um problema ao buscar motivo do afastamento.");
        }

        $valorMesmoMotivo = "N";
        if (pg_num_rows($rs) > 0) {
            $valorMesmoMotivo = "S";
        }

        $this->addDePara("infoMesmoMtv", "infoMesmoMtv", $valorMesmoMotivo);
    }
}
