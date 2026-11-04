<?php
namespace ECidade\RecursosHumanos\Pessoal\Servidor\Repository;

use ECidade\RecursosHumanos\Pessoal\Servidor\Model\Rescisao as RescisaoModel;
use cl_rhpesrescisao;
use DBCompetencia;
use DBException;
use db_utils;
use Servidor;

class Rescisao extends \BaseClassRepository
{

    /**
     * @var Rescisao
     */
    protected static $oInstance;


    /**
     * Retorno as recisoes por codigo de rescisao
     * @param $codigoRescisao
     * @param \DBCompetencia|null $competencia
     * @param \Instituicao|null $instituicao
     * @throws \BusinessException
     * @throws \DBException
     * @return \ECidade\RecursosHumanos\Pessoal\Servidor\Model\Rescisao|null
     */
    public static function getByCodigoDeRescisao(
        $codigoRescisao,
        \DBCompetencia $competencia = null,
        \Instituicao $instituicao = null
    ) {

        if (empty($competencia)) {
            $competencia = \DBPessoal::getCompetenciaFolha();
        }
        if (empty($instituicao)) {
            $instituicao = \InstituicaoRepository::getInstituicaoSessao();
        }

        $where = array(
            'rh02_anousu = ' . $competencia->getAno(),
            'rh02_mesusu = ' . $competencia->getMes(),
            'rh02_instit = ' . $instituicao->getCodigo(),
            "rh05_codigorescisao = '{$codigoRescisao}'",
        );
        $daoRescisao = new \cl_rhpesrescisao();
        $sqlRescisao = $daoRescisao->sql_query_rescisao(null, '*', null, implode(" and ", $where));
        $rsRescisao = db_query($sqlRescisao);
        if (!$rsRescisao) {
            throw new \DBException("Erro ao pesquisar dados da rescisão.");
        }
        $totalLinhas = pg_num_rows($rsRescisao);
        if ($totalLinhas == 0) {
            return null;
        }

        $dados = \db_utils::fieldsMemory($rsRescisao, 0);
        $dados->competencia = $competencia;
        return self::getInstance()->make($dados);
    }

    /**
     * @param $dados
     * @return RescisaoModel
     * @throws \BusinessException
     *
     */
    protected function make($dados)
    {
        $rescisao = new RescisaoModel();
        $rescisao->setCodigo($dados->rh05_codigorescisao);
        $rescisao->setServidor(\ServidorRepository::getInstanciaByCodigo($dados->rh02_regist));
        $rescisao->setData(new \DateTime($dados->rh05_recis));
        $rescisao->setCompetencia($dados->competencia);
        return $rescisao;
    }

    /**
     * @param Servidor $servidor
     * @return RescisaoModel|null
     */
    protected function getRescisaoByServidor(Servidor $servidor)
    {
        $instancias = self::getInstance()->aColecao;

        foreach ($instancias as $rescisao) {
            if ($rescisao->getServidor()->getMatricula() == $servidor->getMatricula()) {
                return $rescisao;
            }
        }

        return null;
    }

    /**
     * @param Servidor $servidor
     * @return RescisaoModel|null
     * @throws DBException
     * @throws \BusinessException
     */
    public function getRescisaoPorServidor(Servidor $servidor)
    {
        $rescisaoModel = self::getRescisaoByServidor($servidor);
        if ($rescisaoModel instanceof RescisaoModel) {
            return $rescisaoModel;
        }

        $where = array(
          'rh02_anousu = ' . $servidor->getAnoCompetencia(),
          'rh02_mesusu = ' . $servidor->getMesCompetencia(),
          'rh02_instit = ' . $servidor->getInstituicao()->getCodigo(),
          'rh02_regist = ' . $servidor->getMatricula()
        );

        $dao = new cl_rhpesrescisao();
        $sql = $dao->sql_query_rescisao(null, '*', null, implode(" and ", $where));
        $rs = db_query($sql);

        if (!$rs) {
            throw new DBException('Erro ao buscar a rescisão do servidor ' .  $servidor->getMatricula());
        }

        if (pg_num_rows($rs) == 0) {
            return null;
        }

        $dadosRescisao = db_utils::fieldsMemory($rs, 0);
        $dadosRescisao->competencia = new DBCompetencia($servidor->getAnoCompetencia(), $servidor->getMesCompetencia());

        return self::make($dadosRescisao);
    }

    /**
     * @param Servidor $servidor
     * @return RescisaoModel|null
     * @throws DBException
     * @throws \BusinessException
     */
    public function getRescisaoPorServidorEsocial(Servidor $servidor)
    {
        $rescisaoModel = self::getRescisaoByServidor($servidor);
        if ($rescisaoModel instanceof RescisaoModel) {
            return $rescisaoModel;
        }

        $where = array(
          'rh02_anousu = ' . $servidor->getAnoCompetencia(),
          'rh02_mesusu = ' . $servidor->getMesCompetencia(),
          'rh02_instit = ' . $servidor->getInstituicao()->getCodigo(),
          'rh02_regist = ' . $servidor->getMatricula()
        );

        $dao = new cl_rhpesrescisao();
        $sql = $dao->sql_query_rescisao_esocial(null, '*', null, implode(" and ", $where));
        $rs = db_query($sql);

        if (!$rs) {
            throw new DBException('Erro ao buscar a rescisão do servidor ' .  $servidor->getMatricula());
        }

        if (pg_num_rows($rs) == 0) {
            return null;
        }

        $dadosRescisao = db_utils::fieldsMemory($rs, 0);
        $dadosRescisao->competencia = new DBCompetencia($servidor->getAnoCompetencia(), $servidor->getMesCompetencia());

        return self::make($dadosRescisao);
    }
}
