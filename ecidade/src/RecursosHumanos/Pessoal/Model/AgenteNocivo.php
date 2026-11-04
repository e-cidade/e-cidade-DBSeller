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
namespace ECidade\RecursosHumanos\Pessoal\Model;

use ECidade\Configuracao\Instituicao\Model\Instituicao;
use ECidade\RecursosHumanos\Pessoal\Model\EquipamentoProtecao;

class AgenteNocivo
{
    /**
     * @var integer
     */
    private $codigo;

    /**
     * @var integer
     */
    private $codigoLocalTrabalho;

    /**
     * @var integer
     */
    private $codigoInstituicao;

    /**
     * @var string
     */
    private $agente;

    /**
     * @var integer
     */
    private $tipoAvaliacao;

    /**
     * @var string
     */
    private $intensidadeConcentracao;

    /**
     * @var string
     */
    private $toleranciaLimite;

    /**
     * @var string
     */
    private $medida;

    /**
     * @var string
     */
    private $tecnicaMedicao;

    /**
     * @var EquipamentoProtecao
     */
    private $equipamento;

    /**
     * @var string
     */
    private $processo;


    public function __construct($codigo = null)
    {
        if (!empty($codigo)) {
            $instituicao = db_getsession("DB_instit");
            // No sistema esta invertido o cargo/funcao
            $sql = "
                select
                    *
                from
                    pessoal.rhlocaltrabagentesnocivos
                where
                    rh256_sequencial = {$codigo}
                    and rh256_instituicao = {$instituicao}";
            $rs = \db_query($sql);

            if (!$rs) {
                throw new \DBException("Houve um erro ao buscar o agente nocivo código {$codigo}.");
            }

            if (pg_num_rows($rs) == 0) {
                throw new \BusinessException("Agente Nocivo código {$codigo} não encontrado.");
            }

            $agente = \db_utils::fieldsMemory($rs, 0);
            $this->setCodigo($agente->rh256_sequencial);
            $this->setCodigoLocalTrabalho($agente->rh256_rhlocaltrab);
            $this->setCodigoInstituicao($agente->rh256_instituicao);
            $this->setAgente($agente->rh256_agentenocivo);
            $this->setTipoAvaliacao($agente->rh256_tipoavaliacao);
            $this->setIntensidadeConcentracao($agente->rh256_intensidadeconcentracao);
            $this->setToleranciaLimite($agente->rh256_tolerancialimite);
            $this->setMedida($agente->rh256_medida);
            $this->setTecnicaMedicao($agente->rh256_tecnicamedicao);
            $this->setProcesso($agente->rh256_processo);

            $this->setEquipamento(EquipamentoProtecao::getByAgenteNocivo($this->getCodigo()));
        }
    }

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param int $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * @return int
     */
    public function getCodigoLocalTrabalho()
    {
        return $this->codigoLocalTrabalho;
    }

    /**
     * @param int $codigoLocalTrabalho
     */
    public function setCodigoLocalTrabalho($codigoLocalTrabalho)
    {
        $this->codigoLocalTrabalho = $codigoLocalTrabalho;
    }

    /**
     * @return int
     */
    public function getCodigoInstituicao()
    {
        return $this->codigoInstituicao;
    }

    /**
     * @param int $codigoInstituicao
     */
    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    /**
     * @return string
     */
    public function getAgente()
    {
        return $this->agente;
    }

    /**
     * @param string $agente
     */
    public function setAgente($agente)
    {
        $this->agente = $agente;
    }

    /**
     * @return int
     */
    public function getTipoAvaliacao()
    {
        return $this->tipoAvaliacao;
    }

    /**
     * @param int $tipoAvaliacao
     */
    public function setTipoAvaliacao($tipoAvaliacao)
    {
        $this->tipoAvaliacao = $tipoAvaliacao;
    }

    /**
     * @return string
     */
    public function getIntensidadeConcentracao()
    {
        return $this->intensidadeConcentracao;
    }

    /**
     * @param string $intensidadeConcentracao
     */
    public function setIntensidadeConcentracao($intensidadeConcentracao)
    {
        $this->intensidadeConcentracao = $intensidadeConcentracao;
    }

    /**
     * @return string
     */
    public function getToleranciaLimite()
    {
        return $this->toleranciaLimite;
    }

    /**
     * @param string $toleranciaLimite
     */
    public function setToleranciaLimite($toleranciaLimite)
    {
        $this->toleranciaLimite = $toleranciaLimite;
    }

    /**
     * @return string
     */
    public function getMedida()
    {
        return $this->medida;
    }

    /**
     * @param string $medida
     */
    public function setMedida($medida)
    {
        $this->medida = $medida;
    }

    /**
     * @return string
     */
    public function getTecnicaMedicao()
    {
        return $this->tecnicaMedicao;
    }

    /**
     * @param string $tecnicaMedicao
     */
    public function setTecnicaMedicao($tecnicaMedicao)
    {
        $this->tecnicaMedicao = $tecnicaMedicao;
    }


    public static function getAgentesByLocalTrabalho($codigoLocalTrabalho)
    {
        $agentes = [];

        if (!empty($codigoLocalTrabalho)) {
            $instituicao = db_getsession("DB_instit");
            $sql = "
                select
                    rh256_sequencial
                from
                    pessoal.rhlocaltrabagentesnocivos
                where
                    rh256_rhlocaltrab = {$codigoLocalTrabalho}
                    and rh256_instituicao = {$instituicao}";
            $rs = \db_query($sql);
            if (!$rs) {
                $msg = "Houve um erro ao buscar o local de trabalho código {$codigoLocalTrabalho}.";
                throw new \DBException($msg);
            }

            $totalAgente = pg_num_rows($rs);
            for ($i = 0; $i < $totalAgente; $i++) {
                $agente = \db_utils::fieldsMemory($rs, $i);
                $agentes[] = new AgenteNocivo($agente->rh256_sequencial);
            }
        } else {
            throw new \BusinessException("Código do local de trabalho não informado.");
        }

        return $agentes;
    }

    public static function getDescricaoByCodigo($codigoAgente)
    {
        $arquivo = file_get_contents(ECIDADE_PATH_TABELA_ESOCIAL . "eventoS2240_tabela24.json");
        $dados = \JSON::create()->parse(str_replace('\\', '', $arquivo));

        foreach ($dados as $dado) {
            if ($dado->value == $codigoAgente) {
                return $dado->label;
            }
        }
    }

    /**
     * @return EquipamentoProtecao
     */
    public function getEquipamento()
    {
        return $this->equipamento;
    }

    /**
     * @param EquipamentoProtecao $equipamento
     */
    public function setEquipamento($equipamento)
    {
        $this->equipamento = $equipamento;
    }

    /**
     * @param String $processo
     */
    public function setProcesso($processo)
    {
        $this->processo = $processo;
    }

    /**
     * @return String
     */
    public function getProcesso()
    {
        return $this->processo;
    }
}
