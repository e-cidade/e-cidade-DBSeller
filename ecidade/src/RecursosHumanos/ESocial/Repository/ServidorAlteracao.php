<?php
namespace ECidade\RecursosHumanos\ESocial\Repository;

use InstituicaoRepository;
use ServidorRepository;
use BusinessException;
use DBException;
use db_utils;
use DBDate;
use ECidade\RecursosHumanos\ESocial\Model\ServidorAlteracao as ServidorModel;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

class ServidorAlteracao extends \BaseClassRepository
{

    /**
     * @var []
     */
    protected static $layouts = [
        Tipo::S2205 => Tipo::S2205,
        Tipo::S2206 => Tipo::S2206,
        Tipo::S2306 => Tipo::S2306,
        Tipo::S2405 => Tipo::S2405,
        Tipo::S2416 => Tipo::S2416
    ];

    public function __construct()
    {
    }

    public static function findMatriculaByLayout($matricula, $layout = '', $all = false, $processamento = false)
    {
        self::verificaLayoutAlteracao($layout);

        $limit = " order by eso38_sequencial desc limit 1";
        $where = " where eso38_matricula = {$matricula} ";
        switch ($layout) {
            case Tipo::S2205:
                $where .= ' and eso38_s2205data is not null ';
                if (!$processamento) {
                    $where .= ' and eso38_s2205processado = false ';
                }
                break;
            case Tipo::S2206:
                $where .= ' and eso38_s2206data is not null ';
                if (!$processamento) {
                    $where .= ' and eso38_s2206processado = false ';
                }
                break;
            case Tipo::S2306:
                $where .= ' and eso38_s2306data is not null ';
                if (!$processamento) {
                    $where .= ' and eso38_s2306processado = false ';
                }
                break;
            case Tipo::S2405:
                $where .= ' and eso38_s2405data is not null ';
                if (!$processamento) {
                    $where .= ' and eso38_s2405processado = false ';
                }
                break;
            case Tipo::S2416:
                $where .= ' and eso38_s2416data is not null ';
                if (!$processamento) {
                    $where .= ' and eso38_s2416processado = false ';
                }
                break;
        }

        $sql = "select eso38_sequencial from esocial.servidoralteracao {$where}";

        if (!$all) {
            $sql .= $limit;
        }
        $rs = db_query($sql);

        if (!$rs) {
            throw new DBException("Erro ao buscar informações de datas de alteração da matrícula {$matricula}.");
        }
        $total = pg_numrows($rs);
        $retorno = [];
        for ($i = 0; $i < $total; $i++) {
            $retorno[] = self::get(db_utils::fieldsMemory($rs, $i)->eso38_sequencial);
        }

        if (sizeof($retorno) > 0) {
            if ($all) {
                return $retorno;
            }
            return $retorno[0];
        }
        // Caso tenha buscado todos os registros  e nao tenha econtrado nada, ou seja processamento retorna false
        if ($all || $processamento) {
            return false;
        }
        return new ServidorModel($matricula);
    }

    public static function get($codigo)
    {
        if (empty($codigo)) {
            throw new BusinessException("Código da alteração do servidor não informado.");
        }

        $sql = "select * from esocial.servidoralteracao where eso38_sequencial = {$codigo}";
        $rs = db_query($sql);

        if (!$rs) {
            throw new DBException("Erro ao buscar informações do código {$codigo} da alteração do servidor.");
        }

        $dados = db_utils::fieldsMemory($rs, 0);
        $alteracaoServidor = new ServidorModel();
        $alteracaoServidor->setCodigo($dados->eso38_sequencial);
        $alteracaoServidor->setMatricula($dados->eso38_matricula);
        if (!empty($dados->eso38_s2205data)) {
            $alteracaoServidor->setDataS2205(new \DBDate($dados->eso38_s2205data));
        }
        $alteracaoServidor->setProcessamentoS2205($dados->eso38_s2205processado);
        if (!empty($dados->eso38_s2206data)) {
            $alteracaoServidor->setDataS2206(new \DBDate($dados->eso38_s2206data));
        }
        $alteracaoServidor->setProcessamentoS2206($dados->eso38_s2206processado);
        if (!empty($dados->eso38_s2306data)) {
            $alteracaoServidor->setDataS2306(new \DBDate($dados->eso38_s2306data));
        }
        $alteracaoServidor->setProcessamentoS2306($dados->eso38_s2306processado);
        if (!empty($dados->eso38_s2405data)) {
            $alteracaoServidor->setDataS2405(new \DBDate($dados->eso38_s2405data));
        }
        $alteracaoServidor->setProcessamentoS2405($dados->eso38_s2405processado);
        if (!empty($dados->eso38_s2416data)) {
            $alteracaoServidor->setDataS2416(new \DBDate($dados->eso38_s2416data));
        }
        $alteracaoServidor->setProcessamentoS2416($dados->eso38_s2416processado);

        return $alteracaoServidor;
    }

    private static function verificaLayoutAlteracao($layout)
    {
        if ($layout !== '') {
            if (!self::$layouts[$layout]) {
                throw new BusinessException("Layout  não suportado para alteracão de dados do servidor.");
            }
        }
    }

    /**
     * @param $iAno
     * @param $iMes
     * @param $layout
     * @param $oInstituicao
     * @return Servidor[]
     * @throws DBException
     */
    public static function getServidoresPorCompetenciaAlteracao($iAno, $iMes, $layout, $oInstituicao = null)
    {

        self::verificaLayoutAlteracao($layout);

        if (empty($oInstituicao)) {
            $oInstituicao = InstituicaoRepository::getInstituicaoSessao();
        }

        $where = '';
        $dadaInicio = "{$iAno}-{$iMes}-01";
        $quantidadeDias = DBDate::getQuantidadeDiasMes($iMes, $iAno);
        $dadaFim = "{$iAno}-{$iMes}-{$quantidadeDias}";
        $between = "between '{$dadaInicio}' and '{$dadaFim}'";

        switch ($layout) {
            case Tipo::S2205:
                $where = " eso38_s2205data {$between}";
                break;
            case Tipo::S2206:
                $where = " eso38_s2206data {$between}";
                break;
            case Tipo::S2306:
                $where = " eso38_s2306data {$between}";
                break;
            case Tipo::S2405:
                $where = " eso38_s2405data {$between}";
                break;
            case Tipo::S2416:
                $where = " eso38_s2416data {$between}";
                break;
        }

        $sql = "select
                    eso38_matricula
                from
                    esocial.servidoralteracao
                inner join
                    rhpessoal
                on
                    eso38_matricula = rh01_regist
                where
                     {$where} and rh01_instit = {$oInstituicao->getCodigo()}";

        $result = db_query($sql);

        if (!$result || pg_num_rows($result) == 0) {
            throw new DBException('Erro ao buscar os servidores da competência alteração.');
        }

        return db_utils::makeCollectionFromRecord($result, function ($oRetorno) {
            return ServidorRepository::getInstanciaByCodigo($oRetorno->eso38_matricula);
        });
    }


    public static function getGrupoAlteracaoContratual($matricula, $dataEfeito)
    {
        $altContratual = [];
        $altContratual['dtAlteracao'] = '';
        $sql = "
        select
            coalesce(eso39_alteracao, eso38_s2206data) as eso39_alteracao,
            eso39_descricao,
            eso39_dataefeito
        from
            esocial.servidoralteracao
        left join rhreajustesalarialesocial on
            eso39_matricula = eso38_matricula
            and eso38_s2206data = eso39_dataefeito
        where
            eso38_matricula = {$matricula} and
            eso38_s2206data = '{$dataEfeito}'
        order by eso39_sequencial desc
        ";
        $resultado = db_query($sql);
        if (pg_num_rows($resultado) > 0) {
            $altContratual['dtAlteracao'] = db_utils::fieldsMemory($resultado, 0)->eso39_alteracao;
            $dtEf = db_utils::fieldsMemory($resultado, 0)->eso39_dataefeito;
            if (!empty($dtEf)) {
                $altContratual['dtEf'] = $dtEf;
            }
            $dscAlt = substr(db_utils::fieldsMemory($resultado, 0)->eso39_descricao, 0, 149);
            if (!empty($dscAlt)) {
                $altContratual['dscAlt'] = $dscAlt;
            }
        }
        return  $altContratual;
    }
}
