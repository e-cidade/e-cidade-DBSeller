<?php

namespace ECidade\Patrimonial\Protocolo\Servicos;

use App\Domain\Configuracao\Departamento\Models\Departamento;
use App\Domain\Patrimonial\Ouvidoria\Services\ProcessoEletronicoService;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Patrimonial\Protocolo\Services\CapaDoDespacho;
use App\Domain\Patrimonial\Protocolo\Services\NotificarTransferenciaProcessoService;
use BusinessException;
use db_stdClass;
use DBDepartamentoRepository;
use DBString;
use ECidade\File\Arquivo;
use ECidade\Lib\Request\ProcessoEletronico\ProcessoEletronico;
use ECidade\Patrimonial\Protocolo\Modelo\AndamentoProcesso;
use ECidade\Patrimonial\Protocolo\Procedimentos\Parametros\Modelo\MensageriaProcesso;
use ECidade\Patrimonial\Protocolo\Repositorio\ProcessoRepositorio;
use ECidade\Patrimonial\Protocolo\Repositorio\TransferenciaRepositorio;
use ECidade\Patrimonial\Protocolo\Modelo\AndamentoProcessoInterno;
use App\Domain\Patrimonial\Protocolo\Repository\Processo\ProcessoDocumentoRepository;
use ECidade\V3\Extension\Registry;

use App\Domain\Configuracao\Helpers\StorageHelper;
use ECidade\Patrimonial\Protocolo\Repositorio\AndamentoProcessoInternoRepository;
use Exception;
use App\Domain\Patrimonial\Protocolo\Model\Processo\ProcessoDocumento as DocumentoProcesso;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ProcessoDocumento;
use processoProtocolo;
use stdClass;
use UsuarioSistema;
use ProcessoProtocoloNumeracao;

/**
 * Class AndamentoProcessoService
 *
 * @package ECidade\Patrimonial\Protocolo\Servicos
 */
class AndamentoProcessoService
{
    private $parametros;

    private $repositorio;

    private $departamentos;

    private $ordem = 0;

    private $repositorioAndamentoInterno;

    private $repositorioProcessoDocumento;

    public function __construct(stdClass $parametros)
    {
        $this->parametros = $parametros;
        $this->repositorio = new ProcessoRepositorio();
        $this->repositorioAndamentoInterno = new AndamentoProcessoInternoRepository(
            new \cl_procandamint
        );
        $this->repositorioProcessoDocumento = new ProcessoDocumentoRepository();
    }

    /**
     * @param stdClass $parametros
     */
    public function setParametros($parametros)
    {
        $this->parametros = $parametros;
    }

    /**
     * @return stdClass
     */
    public function getParametros()
    {
        return $this->parametros;
    }


    public function buscarInstituicoes()
    {
        $instituicoes = \InstituicaoRepository::getInstituicoes();
        $rs = array();

        foreach ($instituicoes as $instituicao) {
            try {
                $departamento = \DBDepartamento::findByInstituicao(
                    $instituicao->getCodigo(),
                    true
                );
            } catch (Exception $e) {
            }

            $rs[] = (object)array(
                'codigo' => $instituicao->getCodigo(),
                'descricao' => $instituicao->getDescricao(),
                'departamentos' => $departamento,
            );
        }

        return $rs;
    }

    private function buscaDepartamentos()
    {
        $dao = new \cl_db_depart();
        $data = date("Y-m-d", db_getsession("DB_datausu"));
        $instituicao = db_getsession("DB_instit");
        $sql = $dao->sql_query_div(
            null,
            'distinct coddepto as codigo, descrdepto as descricao',
            "coddepto",
            "(limite is null or limite >= '{$data}') and instit = {$instituicao}"
        );

        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception(
                "Não foi possível buscar os departamentos da instituição.\Contate o suporte."
            );
        }

        $departamentos = array();

        while ($departamento = pg_fetch_object($rs)) {
            $departamentos[] = $departamento;
        }

        return $departamentos;
    }

    private function buscaDespachoPorAndamento($codigoProcesso)
    {
        $campos = array();
        $campos[] = 'p78_sequencial as codigo';
        $campos[] = 'p78_codandam as codigoAndamento';
        $campos[] = 'TO_CHAR(p78_data, \'dd/mm/YYYY\') as data';
        $campos[] = 'p100_descricao as tipo';
        $campos[] = 'nome as usuario';
        $campos[] = 'p78_despacho as despacho';

        $sql = "
        SELECT p78_sequencial AS codigo,
        p78_codandam AS codigoAndamento,
        TO_CHAR(p78_data::date, 'dd/mm/YYYY') AS data,
        p100_descricao AS tipo,
        nome AS usuario,
        p78_despacho AS despacho
        FROM procandamint
        INNER JOIN db_usuarios ON db_usuarios.id_usuario = procandamint.p78_usuario
        LEFT JOIN tipodespacho ON p100_sequencial = p78_tipodespacho
        INNER JOIN protocolo.procandam ON protocolo.procandam.p61_codandam = procandamint.p78_codandam
        INNER JOIN protocolo.protprocesso ON protocolo.protprocesso.p58_codproc = protocolo.procandam.p61_codproc
        WHERE protocolo.protprocesso.p58_codproc = {$codigoProcesso}
        ORDER BY p78_data::date DESC
        ";

        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception(
                "Não foi possível buscar o despacho anteriores do processo {$codigoProcesso}"
            );
        }

        $despachos = array();

        while ($despacho = pg_fetch_object($rs)) {
            $despachos[] = $despacho;
        }

        return $despachos;
    }

    public function buscaNovosProcessos()
    {
        if (empty($this->parametros->ultimaTransferencia)) {
            throw new \Exception(
                "É necessário informar a última transferencia para atualizar a lista de processos.\nContate o suporte."
            );
        }

        $departamentos = $this->buscaDepartamentos();
        $processos = $this->buscarProcessosAReceber(
            array("p62_codtran > {$this->parametros->ultimaTransferencia}")
        );

        foreach ($processos as &$processo) {
            $this->preparaProcesso($processo, $departamentos);
        }

        return $processos;
    }

    private function buscaDocumentosProcesso($codigoProcesso)
    {
        $daoDocumentos = new \cl_protprocessodocumento();
        $sql = $daoDocumentos->sql_query_file(
            null,
            'p01_sequencial as codigo,
            p01_descricao as descricao',
            null,
            "p01_protprocesso = {$codigoProcesso}"
        );

        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception(
                "Não foi possível buscar os documentos do processo {$codigoProcesso}.\nContate o suporte."
            );
        }
        $documentos = array();

        while ($documento = pg_fetch_object($rs)) {
            $documentos[] = $documento;
        }

        return $documentos;
    }

    private function buscarProcessosRecebidosInterno(
        array $where = null,
        $anoProcesso
    ) {
        $codigoUsuario = db_getsession('DB_id_usuario');
        $ano = db_getsession('DB_anousu');
        if (!empty($anoProcesso)) {
            $ano = $anoProcesso;
        }
        $departamento = \DBDepartamentoRepository::getPorCodigo(
            db_getsession('DB_coddepto')
        );
        $whereRestringeVisualizarReceber
            = "AND (p89_usuario = {$codigoUsuario})";
        if ($departamento->getCodigoResponsavel() == $codigoUsuario) {
            $whereRestringeVisualizarReceber = "";
        }

        $aux = "select count(p78_sequencial) from procandamint ";
        $aux .= " where p78_codandam = p58_codandam and p78_transint is false limit 1";

        $campos = array();
        $campos[] = "p58_codproc AS codigo";
        $campos[] = "p51_codigo AS codigoTipoProcesso";
        $campos[] = "z01_numcgm AS numcgm";
        $campos[] = 'z01_nome as titular';
        $campos[] = 'p58_codandam as codigoAndamento';
        $campos[] = 'nomeinst as instituicao';
        $campos[] = 'descrdepto as departamento';
        $campos[] = "p88_codigo AS transferencia";
        $campos[] = "p58_numero || '/' || p58_ano AS processo";
        $campos[] = "p58_requer AS requerente";
        $campos[] = "p58_tipoprocesso as tipoProcesso";
        $campos[] = "p51_descr AS descricao";
        $campos[] = "TO_CHAR(p88_data, 'dd/mm/YYYY') as data";
        $campos[] = "p58_obs AS observacao";
        $campos[] = "(select count(p123_id) from protocolo.mensageria where p123_processo =
        p58_codproc and p123_data_visualizada is null and p123_interna = false) as mensagens_novas";
        $campos[] = "case when (" . $aux
            . ") > 0 then 3 else 2 end as codigoStatus";
        $campos[] = "p58_codproc || '/' || p58_ano AS codigoProcesso";
        $campos[]
            = "CASE p89_usuario WHEN {$codigoUsuario} THEN 1  WHEN 0 THEN  0  ELSE 2 END AS parausuariologado";
        $campos[] = "true as processoInterno";
        $campos[] = "
            CASE
                WHEN p58_tipoprocesso = 2 THEN 1
                ELSE 0
            END flag_processo_eletronico
        ";

        $campos[] = "
            (SELECT
               SUM(CASE  WHEN p113_data_registro IS NULL THEN 1 ELSE 0 END) AS mensagens_nao_lidas
            FROM
               procandam
            INNER JOIN processosvinculados ON
            processosvinculados.p92_processofilho = procandam.p61_codproc
            INNER JOIN procandamint ON
            procandamint.p78_codandam = procandam.p61_codandam
            LEFT JOIN historicovisualizacaoprocandam
                ON  historicovisualizacaoprocandam.p113_procandamint_id = procandamint.p78_sequencial
            WHERE
            p92_processopai = p58_codproc)  AS mensagens_nao_lidas
        ";


        $camposSelect = implode(',', $campos);

        $camposWhere = '';
        $whereAno = "p58_ano = {$ano}";
        if (!empty($where)) {
            if (strripos($where[0], 'p58_codproc') === false) {
                $where[] = $whereAno;
            };
            $camposWhere = ' AND ' . implode('AND ', $where);
        }

        $sql = "SELECT DISTINCT {$camposSelect}
                    FROM protprocesso
                    INNER JOIN cgm ON cgm.z01_numcgm = protprocesso.p58_numcgm
                    INNER JOIN db_config ON codigo = p58_instit
                    INNER JOIN configuracoes.db_depart ON db_depart.coddepto = protprocesso.p58_coddepto
                    INNER JOIN proctransferintand ON p58_codandam = p87_codandam
                    INNER JOIN proctransferint ON p88_codigo = p87_codtransferint
                    INNER JOIN proctransferintusu ON p89_codtransferint = p88_codigo
                    INNER JOIN tipoproc ON p58_codigo = p51_codigo
                    INNER JOIN procandam ON p61_codandam = p87_codandam
                    LEFT JOIN arqproc ON p58_codproc = p68_codproc
                    LEFT JOIN processoouvidoria ON ov09_protprocesso = protprocesso.p58_codproc
                    LEFT JOIN protocolo.mensageria
                    ON protocolo.mensageria.p123_processo = protocolo.protprocesso.p58_codproc
                    WHERE p68_codproc IS NULL
                      AND p61_coddepto = {$departamento->getCodigo()}
                      {$whereRestringeVisualizarReceber}
                      AND EXISTS(SELECT 1 FROM procandamintand WHERE p86_codtrans = p88_codigo)
                      AND NOT EXISTS (
                        SELECT 1 FROM processosapensados
                          WHERE processosapensados.p30_procapensado = protprocesso.p58_codproc
                      )
                    {$camposWhere}
                    ORDER BY paraUsuarioLogado DESC, p88_codigo DESC, p58_codproc DESC
                    ;";

        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception(
                "Não foi possível buscar os processos a receber.\nContate o suporte."
            );
        }

        $processos = array();

        while ($processo = pg_fetch_object($rs)) {
            $processos[] = $processo;
        }

        return $processos;
    }

    private function buscaProcessosRecebidos(
        array $where = null,
        $anoProcesso = null
    ) {
        $departamento = db_getsession('DB_coddepto');
        $codigoUsuario = db_getsession('DB_id_usuario');
        $ano = db_getsession('DB_anousu');
        if (!empty($anoProcesso)) {
            $ano = $anoProcesso;
        }

        $aux
            = "select count(p78_codandam) from procandamint where p78_codandam = p58_codandam limit 1";

        $campos = array();
        $campos[] = 'p58_codproc as codigo';
        $campos[] = "p51_codigo AS codigoTipoProcesso";
        $campos[] = "z01_numcgm AS numcgm";
        $campos[] = 'z01_nome as titular';
        $campos[] = 'p58_codandam as codigoAndamento';
        $campos[] = 'nomeinst as instituicao';
        $campos[] = 'descrdepto as departamento';
        $campos[] = 'p62_codtran as transferencia';
        $campos[] = "p58_numero || '/' || p58_ano as processo";
        $campos[] = "p58_requer as requerente";
        $campos[] = "p58_tipoprocesso as tipoProcesso";
        $campos[] = "p51_descr as descricao";
        $campos[] = "TO_CHAR(p62_dttran, 'dd/mm/YYYY') as data";
        $campos[] = "p58_obs as observacao";
        $campos[] = "(select count(p123_id) from protocolo.mensageria where p123_processo =
        p58_codproc and p123_data_visualizada is null and p123_interna = false) as mensagens_novas";
        $campos[] = "case when (" . $aux
            . ") > 0 then 3 else 2 end as codigoStatus";
        $campos[] = "p58_codproc || '/' || p58_ano as codigoProcesso";
        $campos[]
            = "case p62_id_usorec when {$codigoUsuario} THEN 1  WHEN 0 THEN  0  ELSE 2 END as parausuariologado";
        $campos[] = "false as processoInterno";
        $campos[] = "
            CASE
                WHEN p58_tipoprocesso = 2 THEN 1
                ELSE 0
            END flag_processo_eletronico
        ";

        $campos[] = "
            (SELECT
               SUM(CASE  WHEN p113_data_registro IS NULL THEN 1 ELSE 0 END) AS mensagens_nao_lidas
            FROM
               procandam
            INNER JOIN processosvinculados ON
            processosvinculados.p92_processofilho = procandam.p61_codproc
            INNER JOIN procandamint ON
            procandamint.p78_codandam = procandam.p61_codandam
            LEFT JOIN historicovisualizacaoprocandam
                ON historicovisualizacaoprocandam.p113_procandamint_id = procandamint.p78_sequencial
            WHERE
            p92_processopai = p58_codproc)  AS mensagens_nao_lidas
        ";

        $camposSelect = implode(',', $campos);

        $camposWhere = '';
        $whereAno = "p58_ano = {$ano}";
        if (!empty($where)) {
            if (strripos($where[0], 'p58_codproc') === false) {
                $where[] = $whereAno;
            };
            $camposWhere = ' AND ' . implode('AND ', $where);
        }

        $sql = "SELECT DISTINCT {$camposSelect}
                    FROM protprocesso
                    INNER JOIN cgm ON cgm.z01_numcgm = protprocesso.p58_numcgm
                    INNER JOIN db_config ON codigo = p58_instit
                    INNER JOIN configuracoes.db_depart ON db_depart.coddepto = protprocesso.p58_coddepto
                    INNER JOIN procandam ON p58_codandam = p61_codandam
                    INNER JOIN proctransfer ON p62_codtran = (SELECT p63_codtran
                                                             FROM proctransferproc
                                                             WHERE p63_codproc = p58_codproc
                                                             ORDER BY p63_codtran DESC
                                                             LIMIT 1)
                    INNER JOIN tipoproc ON p58_codigo = p51_codigo
                    INNER JOIN proctransand ON p64_codandam = p58_codandam
                    LEFT JOIN arqproc ON arqproc.p68_codproc = protprocesso.p58_codproc
                    LEFT JOIN processoouvidoria ON ov09_protprocesso = protprocesso.p58_codproc
                    LEFT JOIN protocolo.mensageria
                    ON protocolo.mensageria.p123_processo = protocolo.protprocesso.p58_codproc
                    WHERE p68_codproc IS NULL
                      AND p61_coddepto = {$departamento}
                      AND NOT EXISTS(
                        SELECT p62_codtran AS codtran
                        FROM proctransfer
                            LEFT JOIN proctransand ON p64_codtran = p62_codtran
                            LEFT JOIN proctransferproc
                              ON p63_codtran = p62_codtran
                        WHERE p64_codtran IS NULL AND p63_codproc = p58_codproc
                    )
                      AND NOT EXISTS (
                        SELECT 1 FROM processosapensados
                          WHERE processosapensados.p30_procapensado = protprocesso.p58_codproc
                      )
                    {$camposWhere}
                    ORDER BY paraUsuarioLogado DESC, p62_codtran DESC, p58_codproc DESC;";

        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception(
                "Não foi possível buscar os processos a receber.\nContate o suporte."
            );
        }

        $processos = array();

        while ($processo = pg_fetch_object($rs)) {
            $processos[] = $processo;
        }

        return $processos;
    }

    private function buscarProcessosAReceberInternos(
        array $where = null,
        $anoProcesso = null
    ) {
        $codigoUsuario = db_getsession('DB_id_usuario');
        $ano = db_getsession('DB_anousu');
        $departamento = DBDepartamentoRepository::getPorCodigo(
            db_getsession('DB_coddepto')
        );
        if (!empty($anoProcesso)) {
            $ano = $anoProcesso;
        }

        $whereRestringeVisualizarReceber
            = "AND (p89_usuario = {$codigoUsuario})";
        if ($departamento->getCodigoResponsavel() == $codigoUsuario) {
            $whereRestringeVisualizarReceber = "";
        }

        $campos = array();
        $campos[] = "p58_codproc AS codigo";
        $campos[] = "p51_codigo AS codigoTipoProcesso";
        $campos[] = "z01_numcgm AS numcgm";
        $campos[] = 'z01_nome as titular';
        $campos[] = 'p58_codandam as codigoAndamento';
        $campos[] = 'nomeinst as instituicao';
        $campos[] = 'descrdepto as departamento';
        $campos[] = "p88_codigo AS transferencia";
        $campos[] = "p58_numero || '/' || p58_ano AS processo";
        $campos[] = "p58_requer AS requerente";
        $campos[] = "p58_tipoprocesso as tipoProcesso";
        $campos[] = "p51_descr AS descricao";
        $campos[] = "TO_CHAR(p88_data, 'dd/mm/YYYY') as data";
        $campos[] = "p58_obs AS observacao";
        $campos[] = "(select count(p123_id) from protocolo.mensageria where p123_processo =
        p58_codproc and p123_data_visualizada is null and p123_interna = false) as mensagens_novas";
        $campos[] = "1 AS codigoStatus";
        $campos[] = "p58_codproc || '/' || p58_ano AS codigoProcesso";
        $campos[]
            = "CASE p89_usuario WHEN {$codigoUsuario} THEN 1 WHEN 0 THEN  0  ELSE 2 END AS parausuariologado";
        $campos[] = "true as processoInterno";
        $campos[] = "
            CASE
                WHEN p58_tipoprocesso = 2  THEN 1
                ELSE 0
            END flag_processo_eletronico
        ";

        $campos[] = "
            (SELECT
               SUM(CASE  WHEN p113_data_registro IS NULL THEN 1 ELSE 0 END) AS mensagens_nao_lidas
            FROM
               procandam
            INNER JOIN processosvinculados ON
            processosvinculados.p92_processofilho = procandam.p61_codproc
            INNER JOIN procandamint ON
            procandamint.p78_codandam = procandam.p61_codandam
            LEFT JOIN historicovisualizacaoprocandam
                ON historicovisualizacaoprocandam.p113_procandamint_id = procandamint.p78_sequencial
            WHERE
            p92_processopai = p58_codproc)  AS mensagens_nao_lidas
        ";

        $camposSelect = implode(',', $campos);

        $camposWhere = '';
        $whereAno = "p58_ano = {$ano}";
        if (!empty($where)) {
            if (strripos($where[0], 'p58_codproc') === false) {
                $where[] = $whereAno;
            };
            $camposWhere = ' AND ' . implode('AND ', $where);
        }

        $sql = "SELECT DISTINCT {$camposSelect}
                    FROM protprocesso
                    INNER JOIN cgm ON cgm.z01_numcgm = protprocesso.p58_numcgm
                    INNER JOIN db_config ON codigo = p58_instit
                    INNER JOIN configuracoes.db_depart ON db_depart.coddepto = protprocesso.p58_coddepto
                    INNER JOIN proctransferintand ON p58_codandam = p87_codandam
                    INNER JOIN proctransferint ON p88_codigo = p87_codtransferint
                    INNER JOIN proctransferintusu ON p89_codtransferint = p88_codigo
                    INNER JOIN tipoproc ON p58_codigo = p51_codigo
                    INNER JOIN procandam ON p61_codandam = p87_codandam
                    LEFT JOIN arqproc ON p58_codproc = p68_codproc
                    LEFT JOIN processoouvidoria ON ov09_protprocesso = protprocesso.p58_codproc
                    LEFT JOIN protocolo.mensageria
                    ON protocolo.mensageria.p123_processo = protocolo.protprocesso.p58_codproc
                    WHERE p68_codproc IS NULL
                      AND p61_coddepto = {$departamento->getCodigo()}
                      {$whereRestringeVisualizarReceber}
                      AND NOT EXISTS(SELECT 1 FROM procandamintand WHERE p86_codtrans = p88_codigo)
                      AND NOT EXISTS (
                        SELECT 1 FROM processosapensados
                          WHERE processosapensados.p30_procapensado = protprocesso.p58_codproc
                      )
                    {$camposWhere}
                    ORDER BY paraUsuarioLogado DESC, p88_codigo DESC, p58_codproc DESC
                    ;";
        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception(
                "Não foi possível buscar os processos a receber.\nContate o suporte."
            );
        }

        $processos = array();

        while ($processo = pg_fetch_object($rs)) {
            $processos[] = $processo;
        }

        return $processos;
    }

    private function buscarProcessosAReceber(
        array $where = null,
        $anoProcesso = null
    ) {
        $codigoUsuario = db_getsession('DB_id_usuario');
        $ano = db_getsession('DB_anousu');
        if (!empty($anoProcesso)) {
            $ano = $anoProcesso;
        }
        $departamento = \DBDepartamentoRepository::getPorCodigo(
            db_getsession('DB_coddepto')
        );
        $whereRestringeVisualizarReceber
            = " AND ( p62_id_usorec = {$codigoUsuario} OR p62_id_usorec = 0 )";
        if ($departamento->getCodigoResponsavel() == $codigoUsuario) {
            $whereRestringeVisualizarReceber = "";
        }
        $campos = array();
        $campos[] = 'p58_codproc as codigo';
        $campos[] = "p51_codigo AS codigoTipoProcesso";
        $campos[] = "z01_numcgm AS numcgm";
        $campos[] = 'z01_nome as titular';
        $campos[] = 'p58_codandam as codigoAndamento';
        $campos[] = 'nomeinst as instituicao';
        $campos[] = 'descrdepto as departamento';
        $campos[] = 'p62_codtran as transferencia';
        $campos[] = "p58_numero || '/' || p58_ano as processo";
        $campos[] = "p58_requer as requerente";
        $campos[] = "p58_tipoprocesso as tipoProcesso";
        $campos[] = "p51_descr as descricao";
        $campos[] = "TO_CHAR(p62_dttran, 'dd/mm/YYYY') as data";
        $campos[] = "p58_obs as observacao";
        $campos[] = "(select count(p123_id) from protocolo.mensageria where p123_processo =
        p58_codproc and p123_data_visualizada is null and p123_interna = false) as mensagens_novas";
        $campos[] = "'A receber' as status";
        $campos[] = "1 as codigoStatus";
        $campos[] = "p58_codproc || '/' || p58_ano as codigoProcesso";
        $campos[]
            = "CASE p62_id_usorec WHEN {$codigoUsuario} THEN 1  WHEN 0 THEN  0  ELSE 2 END as parausuariologado";
        $campos[] = "false as processoInterno";
        $campos[] = "
            CASE
                WHEN p58_tipoprocesso = 2  THEN 1
                ELSE 0
            END flag_processo_eletronico
        ";

        $campos[] = "
            (SELECT
               SUM(CASE  WHEN p113_data_registro IS NULL THEN 1 ELSE 0 END) AS mensagens_nao_lidas
            FROM
               procandam
            INNER JOIN processosvinculados ON
            processosvinculados.p92_processofilho = procandam.p61_codproc
            INNER JOIN procandamint ON
            procandamint.p78_codandam = procandam.p61_codandam
            LEFT JOIN historicovisualizacaoprocandam
                ON historicovisualizacaoprocandam.p113_procandamint_id = procandamint.p78_sequencial
            WHERE
            p92_processopai = p58_codproc)  AS mensagens_nao_lidas
        ";

        $camposSelect = implode(',', $campos);

        $camposWhere = '';
        $whereAno = "p58_ano = {$ano}";
        if (!empty($where)) {
            if (strripos($where[0], 'p58_codproc') === false) {
                $where[] = $whereAno;
            };
            $camposWhere = ' AND ' . implode('AND ', $where);
        }

        $sql = "SELECT DISTINCT {$camposSelect}
                    FROM protprocesso
                    INNER JOIN cgm ON cgm.z01_numcgm = protprocesso.p58_numcgm
                    INNER JOIN db_config ON codigo = p58_instit
                    INNER JOIN configuracoes.db_depart ON db_depart.coddepto = protprocesso.p58_coddepto
                    INNER JOIN proctransferproc ON p58_codproc = p63_codproc
                    INNER JOIN proctransfer     ON p62_codtran = p63_codtran
                    INNER JOIN tipoproc ON p58_codigo = p51_codigo
                    LEFT JOIN arqproc ON p58_codproc = p68_codproc
                    LEFT JOIN processoouvidoria ON ov09_protprocesso = p58_codproc
                    LEFT JOIN protocolo.mensageria
                    ON protocolo.mensageria.p123_processo = protocolo.protprocesso.p58_codproc
                    WHERE p68_codproc IS NULL
                    AND p62_coddeptorec = {$departamento->getCodigo()}
                    {$whereRestringeVisualizarReceber}
                    AND NOT EXISTS ( SELECT 1 FROM proctransand WHERE p64_codtran = p62_codtran )
                    {$camposWhere}
                    AND NOT EXISTS (
                        SELECT 1 FROM processosapensados
                          WHERE processosapensados.p30_procapensado = protprocesso.p58_codproc
                    )
                    ORDER BY paraUsuarioLogado DESC, p62_codtran DESC, p58_codproc DESC
                    ;";
        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception(
                "Não foi possível buscar os processos a receber.\nContate o suporte."
            );
        }

        $processos = array();

        while ($processo = pg_fetch_object($rs)) {
            $processos[] = $processo;
        }

        return $processos;
    }

    private function preparaProcesso(
        stdClass $processo,
        $instituicoes,
        $usuariosDepartamento = null
    ) {
        $processo->despachosAnteriores = array();
        $processo->documentos = array();
        $processo->documentosSolicitadosAssinatura = array();

        if ($processo->codigostatus != AndamentoProcesso::STATUS_A_RECEBER) {
            $processo->despachosAnteriores = $this->buscaDespachoPorAndamento(
                $processo->codigo
            );
            foreach ($processo->despachosAnteriores as $despacho) {
                $processo->documentosSolicitadosAssinatura[]
                    = $this->verificaSolicitacaoAssinatura($despacho->codigo);
            }
        }

        $mensagens_nao_lidas = $processo->mensagens_nao_lidas;

        /**
         * Remove a influencia da visualização da mensagem ao processo de comparação de hash
         */
        unset($processo->mensagens_nao_lidas);
        $processo->hash = md5(
            serialize($processo)
        );
        $processo->instituicoes = $instituicoes;
        $processo->mensagens_nao_lidas = $mensagens_nao_lidas;
        $processo->usuariosDepartamento = $usuariosDepartamento;
        $processo->isProcessoRedesim
            = $this->isProcessoRedesim(
                $processo
            );
        $processo->isProcessoRedesimInclusaoInscricao
            = $this->isProcessoRedesimInclusaoInscricao(
                $processo
            );

        return $processo;
    }

    public function buscarProcessos()
    {
        $instituicoes = $this->buscarInstituicoes();
        $departamento = DBDepartamentoRepository::getPorCodigo(
            db_getsession("DB_coddepto")
        );
        $where = null;
        $ano = null;
        if ($this->parametros->filtros) {
            $where = json_decode($this->parametros->filtros);
        }
        if ($this->parametros->anoProcesso) {
            $ano = $this->parametros->anoProcesso;
        }

        $processosAReceber = $this->buscarProcessosAReceber(
            $where,
            $ano
        );
        $processosAReceberInterno = $this->buscarProcessosAReceberInternos(
            $where,
            $ano
        );
        $processosRecebidos = $this->buscaProcessosRecebidos(
            $where,
            $ano
        );
        $processosRecebidosInterno = $this->buscarProcessosRecebidosInterno(
            $where,
            $ano
        );
        $processos = array_merge(
            $processosAReceber,
            $processosAReceberInterno,
            $processosRecebidos,
            $processosRecebidosInterno
        );

        usort($processos, function ($item1, $item2) {
            return $item1->transferencia < $item2->transferencia;
        });

        foreach ($processos as &$processo) {
            $this->preparaProcesso(
                $processo,
                $instituicoes,
                $departamento->getUsuariosParaSelect()
            );
        }

        return $processos;
    }

    public function validarStatusProcesso()
    {
        $resposta = (object)array(
            'houveAlteracao' => false,
            'processo' => null,
            'mensagem' => null,
        );

        $processos = $this->buscarProcessos();

        if (count($processos) == 0) {
            $resposta->mensagem
                = 'O processo acessado foi transferido por outro usuário, ';
            $resposta->mensagem .= 'ele não está mais presente neste departamento.';

            return $resposta;
        }

        $processoAtual = array_pop($processos);

        if ($this->parametros->hash != $processoAtual->hash) {
            $resposta->mensagem
                = 'O processo acessado foi alterado por outro usuário, ';
            $resposta->mensagem .= 'verifique o estado atual do processo com as informações atualizadas.';
            $resposta->processo = $processoAtual;
            $resposta->houveAlteracao = true;
        }

        return $resposta;
    }

    public function apenasReceber()
    {
        $validacao = $this->validarStatusProcesso();
        if ($validacao->houveAlteracao) {
            return $validacao;
        }

        return $this->receber();
    }

    public function receber()
    {
        $daoAndamento = new \cl_procandam();
        $daoProcesso = new \cl_protprocesso();
        $daoTransicao = new \cl_proctransand();

        if (empty($this->parametros->codigoTransferencia)) {
            throw new \ParameterException(
                'É necessário informar o código da transferência para realizar o recebimento.'
            );
        }

        $codigoTransferencia = $this->parametros->codigoTransferencia;

        $processos = TransferenciaRepositorio::buscaProcessosPorTransferencia(
            $codigoTransferencia
        );

        if (empty($processos)) {
            return false;
        }

        foreach ($processos as $processo) {
            $codProcesso = $processo->getCodProcesso();
            $sqlProcesso = $daoProcesso->sql_query_file($codProcesso);
            $rsProcesso = db_query($sqlProcesso);

            if (!$rsProcesso) {
                throw new \Exception(
                    "Não foi possível buscar o andamento do processo {$codProcesso}.\nContate o suporte."
                );
            }

            if (pg_num_rows($rsProcesso) == 0) {
                throw new \Exception(
                    "Não foi possível buscar o andamento do processo {$codProcesso}.\nContate o suporte."
                );
            }

            $statusProcesso = pg_fetch_object($rsProcesso, 0);

            $sqlAndamento = $daoAndamento->sql_query_file(
                null,
                "*",
                null,
                "p61_codproc={$codProcesso} limit 1"
            );
            $rsAndamento = db_query($sqlAndamento);

            if (!$rsAndamento) {
                throw new \Exception(
                    "Não foi possível buscar o andamento do processo {$codProcesso}.\nContate o suporte."
                );
            }

            $textoDespacho = '';
            if (pg_num_rows($rsAndamento) == 0) {
                $textoDespacho = 'Tramite Inicial';
            }

            $daoAndamento->p61_despacho = $textoDespacho;
            $daoAndamento->p61_publico = ($statusProcesso->p58_publico == 'f'
                ? 'false' : 'true');
            $daoAndamento->p61_codproc = $codProcesso;
            $daoAndamento->p61_dtandam = date('Y-m-d');
            $daoAndamento->p61_hora = db_hora();
            $daoAndamento->p61_id_usuario = db_getsession("DB_id_usuario");
            $daoAndamento->p61_coddepto = db_getsession("DB_coddepto");
            $daoAndamento->incluir(null);

            if ($daoAndamento->erro_status == "0") {
                $mensagem
                    = "Não foi possível realizar o recebimento da transferência ";
                $mensagem .= "{$codigoTransferencia}.\nContate o suporte.";
                throw new \Exception($mensagem);
            }

            $daoTransicao->p64_codtran = $codigoTransferencia;
            $daoTransicao->p64_codandam = $daoAndamento->p61_codandam;
            $daoTransicao->incluir(null);

            if ($daoTransicao->erro_status == "0") {
                $mensagem
                    = "Não foi possível realizar o recebimento da transferência ";
                $mensagem .= "{$codigoTransferencia}.\nContate o suporte.";
                throw new \Exception($mensagem);
            }

            $daoProcesso->p58_codproc = $processo->getCodProcesso();
            $daoProcesso->p58_codandam = $daoAndamento->p61_codandam;
            $daoProcesso->p58_despacho = " ";
            $daoProcesso->p58_instit = db_getsession("DB_instit");
            $daoProcesso->alterar($processo->getCodProcesso());
        }

        return true;
    }

    public function despachar($novoDespacho = false)
    {
        $despachoInterno = trim($this->parametros->despachoInterno);

        if (empty($despachoInterno)) {
            throw new \Exception(
                "É necessário preencher o campo Despacho Interno."
            );
        }

        $daoAndamento = new \cl_procandamint();
        $daoParametros = new \cl_protparam();
        $daoProcesso = new \cl_protprocesso();

        // valida os parametros
        $sqlParametros = $daoParametros->sql_query_file(
            null,
            '*',
            null,
            "p90_instit = " . db_getsession('DB_instit')
        );
        $rsParametros = db_query($sqlParametros);
        if (!$rsParametros) {
            throw new \Exception(
                "Não foi possível buscar os parâmetros da instituição.\nContate o suporte."
            );
        }

        if (pg_num_rows($rsParametros) > 0) {
            $parametros = pg_fetch_object($rsParametros, 0);

            $minimoCaracteresDespacho = $parametros->p90_minchardesp;
            $despachoObrigatorio = $parametros->p90_despachoob == "t";
            $quantidadeCaracteresDespacho = strlen(
                $this->parametros->despachoInterno
            );

            if ($minimoCaracteresDespacho > 0) {
                if ($despachoObrigatorio || $quantidadeCaracteresDespacho > 0) {
                    if ($quantidadeCaracteresDespacho
                        < $minimoCaracteresDespacho
                    ) {
                        throw new \Exception(
                            "Mínimo de $minimoCaracteresDespacho caracteres para o despacho."
                        );
                    }
                }
            }
        }

        $processoAtual = new processoProtocolo(
            $this->parametros->codigoProcesso
        );
        if (empty($processoAtual->getNumeroProcesso())) {
            throw new \Exception(
                "Não foi possível buscar o processo de código {$this->parametros->codigoProcesso}"
            );
        }

        $usuarioSistema = new UsuarioSistema(db_getsession("DB_id_usuario"));

        $daoAndamento->p78_codandam = !empty($daoProcesso->p58_codandam)
            ?
            $daoProcesso->p58_codandam
            :
            $processoAtual->getCodigoAndamento();
        $daoAndamento->p78_data = date(
            "Y-m-d",
            db_getsession("DB_datausu")
        );
        $daoAndamento->p78_hora = db_hora();
        $daoAndamento->p78_usuario = $usuarioSistema->getCodigo();
        $daoAndamento->p78_publico = ($this->parametros->despachoPublico
            ? 't' : 'false');
        $daoAndamento->p78_transint = 'false';
        $daoAndamento->p78_tipodespacho = $this->parametros->origemMensagem
            ? 1004 : 1;
        $daoAndamento->p78_despacho = addslashes(
            $this->parametros->despachoInterno
        );
        $daoAndamento->incluir(null);

        if ($daoAndamento->erro_status == "0") {
            throw new \Exception(
                "Não foi possível incluir o despacho para o processo {$processoAtual->getCodProcesso()}"
            );
        }

        $idProcesso = $daoProcesso->p58_codproc;
        if (empty($idProcesso)) {
            $idProcesso = $processoAtual->getCodProcesso();
        }

        $processoProtocolo = new processoProtocolo($idProcesso);

        $this->salvarPdfDespacho(
            $processoProtocolo,
            $daoAndamento,
            $novoDespacho
        );

        $departamentoAtual = $processoProtocolo->getDepartamentoAtual();

        if ($departamentoAtual->getCodigo() != db_getsession("DB_coddepto")) {
            $mensagem
                = "Não foi possível vincular os documentos enviados no processo\n";
            $mensagem .= "O departamento atual difere do departamento no qual o processo se encontra.";
            throw new BusinessException($mensagem);
        }

        if (!empty($this->parametros->despachoAnexos)) {
            $this->ordem = processoDocumento::getLastOrdemProcesso($idProcesso)
                + 1;
            if (is_array($this->parametros->despachoAnexos)) {
                $anexos = json_encode(
                    DBString::utf8_encode_all($this->parametros->despachoAnexos)
                );
                $anexos = json_decode($anexos);
            } else {
                $anexos = json_decode($this->parametros->despachoAnexos);
            }

            foreach ($anexos as $anexo) {
                if (isset($anexo->descricao)) {
                    $nomeArquivo = $anexo->descricao;
                } else {
                    $nomeArquivo = $anexo->nome;
                }

                $processoDocumento = new ProcessoDocumento(null);
                $processoDocumento->setProcandamint(
                    $daoAndamento->p78_sequencial
                );
                $processoDocumento->setDescricao(
                    DBString::removerCaracteresEspeciaisAcentos($nomeArquivo)
                );
                $processoDocumento->setProcessoProtocolo($processoProtocolo);
                $processoDocumento->setUsuario($usuarioSistema);
                $processoDocumento->setCaminhoArquivo(
                    DBString::removerCaracteresEspeciaisAcentos($anexo->caminho)
                );
                $processoDocumento->setOrdem($this->ordem);
                $processoDocumento->setNomeDocumento(
                    DBString::removerCaracteresEspeciaisAcentos($nomeArquivo)
                );

                $storageConfig = StorageHelper::getStorageConfig();
                $allowed = array();

                $metadata = new \stdClass();
                $metadata->tipo_documento = "processo";
                $metadata->numero_do_processo
                    = $processoProtocolo->getNumeroProcesso()
                    . "/"
                    . $processoProtocolo->getAnoProcesso();
                $metadata->requerente
                    = $processoProtocolo->getRequerente();
                $rsProcessoOuvidoria = db_query(
                    "
                    SELECT
                    *
                    FROM
                        processoouvidoria
                    INNER JOIN
                        ouvidoriaatendimento
                    ON processoouvidoria.ov09_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial
                    WHERE
                    ov09_protprocesso = {$processoProtocolo->getCodProcesso()}
                "
                );

                $atendimentoOuvidoria = pg_fetch_object($rsProcessoOuvidoria);

                if (!empty($atendimentoOuvidoria)) {
                    $numeroAtendimento = $atendimentoOuvidoria->ov01_numero;
                    $numeroAtendimento .= "/"
                        . $atendimentoOuvidoria->ov01_anousu;
                    $metadata->numero_atendimento = $numeroAtendimento;
                    $metadata->data_hora = $daoAndamento->p78_data . " "
                        . $daoAndamento->p78_hora;
                }

                $metadata->codigo_usuario_aprovacao
                    = $usuarioSistema->getCodigo();
                $metadata->login_usuario_aprovacao = $usuarioSistema->getLogin();
                $metadata->codigoDespacho
                    = $daoAndamento->p78_sequencial;

                if (isset($storageConfig->client_id_ouvidoria)
                    && !empty($storageConfig->client_id_ouvidoria)
                ) {
                    $allowed[] = $storageConfig->client_id_ouvidoria;
                }

                $processoDocumento->setStorage(true);
                $processoDocumento->setOID(
                    StorageHelper::uploadArquivo(
                        $processoDocumento->getCaminhoArquivo(),
                        $allowed,
                        true,
                        $metadata
                    )
                );

                $processoDocumento->salvar();
                $this->ordem++;
            }
        }

        $this->notificar($processoAtual, 'despacho');

        return (object)array(
            'codigo' => $daoAndamento->p78_sequencial,
            'codigoAndamento' => $daoAndamento->p78_codandam,
            'data' => date("d/m/Y", db_getsession("DB_datausu")),
            'tipo' => 'Despacho',
            'usuario' => $usuarioSistema->getNome(),
            'despacho' => $daoAndamento->p78_despacho,
        );
    }

    public function verificarProcessoPossuiDespacho($idProcesso)
    {
        if (empty($idProcesso)) {
            return false;
        }

        $where = "EXISTS (
            SELECT 1 FROM protprocesso
            JOIN procandamint ON p78_codandam = p58_codandam
            WHERE protprocesso.p58_codproc = {$idProcesso}
        )";

        $cl_protprocesso = new \cl_protprocesso();
        $despacho = $cl_protprocesso->sql_query_file(
            $idProcesso,
            '*',
            null,
            $where
        );
        $rsDespacho = db_query($despacho);
        $despacho = pg_fetch_object($rsDespacho);

        if (!empty($despacho->p58_codproc)) {
            return true;
        }

        return false;
    }

    /**
     * @param $codigoProcesso
     * @param $codigoTransferencia
     *
     * @return array | bool
     * @throws Exception
     */
    public function transferir(
        $codigoProcesso = null,
        $codigoTransferencia = null
    ) {
        if (empty($this->parametros->departamentoDestino)) {
            return false;
        }

        $codigoProcesso = $codigoProcesso ?: $this->parametros->codigoProcesso;
        $processo = new processoProtocolo($codigoProcesso);
        $idUsoRec = $this->parametros->recebimentoDestino;

        $clprotparam = new \cl_protparam();
        $parametrosProtocolo = $clprotparam->sql_record(
            $clprotparam->sql_query(null, '*')
        );
        $parametrosProtocolo = \db_utils::fieldsMemory($parametrosProtocolo, 0);


        if (!$processo->isApensado()) {
            if (!empty($parametrosProtocolo->p90_despachoob)
                && $parametrosProtocolo->p90_despachoob === 't'
            ) {
                if (!$this->verificarProcessoPossuiDespacho($codigoProcesso)) {
                    throw new Exception(
                        'Realize um despacho interno para proceder com a transferência.'
                    );
                }
            }

            $retorno = $this->obrigaAssinarDespacho(
                $processo,
                $parametrosProtocolo
            );

            if ($retorno->erro) {
                return $retorno;
            }

            $retorno = $this->obrigaAssinaturaDeDocumentosSolicitados(
                $processo,
                $parametrosProtocolo
            );

            if ($retorno->erro) {
                return $retorno;
            }
        }

        if (empty($codigoTransferencia)) {
            $daoTransferencia = new \cl_proctransfer();
            $daoTransferencia->p62_hora = db_hora();
            $daoTransferencia->p62_dttran = date(
                'Y-m-d',
                db_getsession('DB_datausu')
            );
            $daoTransferencia->p62_id_usuario = db_getsession("DB_id_usuario");
            $daoTransferencia->p62_coddepto = db_getsession("DB_coddepto");
            $daoTransferencia->p62_coddeptorec
                = $this->parametros->departamentoDestino;
            $daoTransferencia->p62_id_usorec = $idUsoRec;
            $daoTransferencia->incluir(null);

            if ($daoTransferencia->erro_status == 0) {
                throw new \Exception(
                    "Não foi possível incluir uma nova transferência.\Contate o suporte."
                );
            }

            $codigoTransferencia = $daoTransferencia->p62_codtran;
        }

        $processosApensados = $processo->getProcessosApensados();
        foreach ($processosApensados as $processosApensado) {
            $this->transferir(
                $processosApensado->getCodProcesso(),
                $codigoTransferencia
            );
        }

        if ($processo->ultimaTransferenciaPendente() !== null) {
            $mensagem
                = "Processo {$processo->getNumeroProcesso()}/{$processo->getAnoProcesso()}";
            $mensagem .= " já possui uma transferência em aberto.";
            throw new \Exception($mensagem);
        }

        $daoTransferenciaProcesso = new \cl_proctransferproc();
        $daoTransferenciaProcesso->p63_codproc = $processo->getCodProcesso();
        $daoTransferenciaProcesso->p63_codtran = $codigoTransferencia;
        $daoTransferenciaProcesso->incluir(
            $codigoTransferencia,
            $processo->getCodProcesso()
        );

        if ($daoTransferenciaProcesso->erro_status == 0) {
            $mensagem
                = "Não foi possível vincular o processo {$processo->getCodProcesso()}";
            $mensagem .= " à nova transferência.\nContate o suporte.";
            throw new \Exception($mensagem);
        }

        $this->notificar($processo, "transferencia");

        try {
            $departamento = DBDepartamentoRepository::getPorCodigo(
                $this->parametros->departamentoDestino
            );
        } catch (Exception $e) {
            $departamento = null;
        }

        try {
            if ($this->parametros->recebimentoDestino
                && $this->parametros->recebimentoDestino != db_getsession(
                    'DB_id_usuario'
                )
            ) {
                MensageriaProcesso::enviar($processo->getCodProcesso());
            } elseif (!empty($departamento)) {
                MensageriaProcesso::enviar(
                    $processo->getCodProcesso(),
                    false,
                    $departamento
                );
            }
        } catch (\Exception $exception) {
            throw new \Exception(
                "Não foi possível enviar a notificação ao Mensageria.\nContate o suporte."
            );
        }


        return array('transferenciaUnica' => true, 'codigoTransferencia' => $codigoTransferencia);
    }

    public function salvarRespostaCamposDinamicos()
    {
        $daoProcesso = new \cl_protprocesso();
        $sqlProcesso = $daoProcesso->sql_query_file(
            $this->parametros->codigoProcesso
        );
        $rsProcesso = db_query($sqlProcesso);

        if (!$rsProcesso) {
            throw new \Exception(
                "Não foi possível buscar o processo de código {$this->parametros->codigoProcesso}"
            );
        }

        $processo = pg_fetch_object($rsProcesso);

        foreach ($this->parametros->campos as $campo) {
            $sequencial = 'sequencial_' . $campo;
            $codigoCampo = preg_replace('/campo_(\d+)/', "$1", $campo);
            $resposta = $this->parametros->{$campo};
            $codigo = !empty($this->parametros->{$sequencial})
                ? $this->parametros->{$sequencial} : null;

            $daoRespostaCamposDinamicos = new \cl_camposandpadraoresposta;
            $daoRespostaCamposDinamicos->p111_sequencial = $codigo;
            $daoRespostaCamposDinamicos->p111_camposandpadrao = $codigoCampo;
            $daoRespostaCamposDinamicos->p111_resposta = $resposta;
            $daoRespostaCamposDinamicos->p111_codandam
                = $processo->p58_codandam;

            if (empty($daoRespostaCamposDinamicos->p111_sequencial)) {
                $daoRespostaCamposDinamicos->incluir(null);
            } else {
                $daoRespostaCamposDinamicos->alterar(
                    $daoRespostaCamposDinamicos->p111_sequencial
                );
            }

            if ($daoRespostaCamposDinamicos->erro_status == 0) {
                $msg
                    = "Erro ao salvar resposta dos campos dinamicos do processo!\\n";
                $msg .= $daoRespostaCamposDinamicos->erro_msg;
                throw new \Exception($msg);
            }
        }
    }

    public function processar()
    {
        $validacao = $this->validarStatusProcesso();
        if ($validacao->houveAlteracao) {
            return $validacao;
        }

        if (!empty($this->parametros->campos)) {
            $this->parametros->campos = explode(",", $this->parametros->campos);

            if (is_array($this->parametros->campos)
                && sizeof(
                    $this->parametros->campos
                ) > 0
            ) {
                $this->salvarRespostaCamposDinamicos();
            }
        }

        $retorno = (object)array(
            'recebido' => $this->receber(),
            'despachado' => $this->despachar(),
            'transferido' => $this->transferir(),
            'houveAlteracao' => false,
        );

        return $retorno;
    }

    public function prepararDocumentos($getExtArq = true)
    {
        $retorno = array();

        $arquivo = new Arquivo();
        $quantidadeArquivos = sizeof($this->parametros->anexos->name);
        for ($i = 0; $i < $quantidadeArquivos; $i++) {
            $nomeArquivo = $this->parametros->anexos->name[$i];
            $arquivoTemporario = $this->parametros->anexos->tmp_name[$i];
            $codigoErro = $this->parametros->anexos->error[$i];

            if ($codigoErro > 0) {
                throw new \Exception(
                    "Não foi possível enviar o arquivo '{$nomeArquivo}' para o servidor.\nContate o suporte."
                );
            }

            $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);
            $hash = md5(time() . $nomeArquivo);
            $nomeSemAcento = \DBString::removerCaracteresEspeciaisAcentos(
                $nomeArquivo
            );
            if (!$getExtArq) {
                $caminhoArquivo = "tmp/{$hash}-{$nomeSemAcento}";
            } else {
                $caminhoArquivo = "tmp/{$hash}-{$nomeSemAcento}.{$extensao}";
            }
            if (!$arquivo->move($arquivoTemporario, $caminhoArquivo)) {
                throw new \Exception(
                    "Não foi possível enviar o arquivo '{$nomeArquivo}' para o servidor.\nContate o suporte."
                );
            }

            $retorno[] = (object)array(
                'codigo' => time() + $i,
                'descricao' => $nomeSemAcento,
                'caminho' => $caminhoArquivo,
            );
        }

        return $retorno;
    }

    public function upload()
    {
        $arquivo = new Arquivo();

        return false;
    }

    public function salvarPdfDespacho(
        \processoProtocolo $processoProtocolo,
        \cl_procandamint   $procandamint,
        $novoDespacho = false
    ) {
        $mostrarPDF = true;
        $codproc = $processoProtocolo->getCodProcesso();
        $iCodigoAndamentoDepacho = $processoProtocolo->getCodigoAndamento();
        $codprocandamint = $procandamint->p78_sequencial;
        $caminho = "";

        if ($novoDespacho) {
            $caminho = $this->criarSalvarPdfDespacho(
                $codproc,
                $procandamint->p78_despacho,
                $procandamint->p78_sequencial
            );
        } else {
            require_once("pro2_despachointer002.php");
        }


        $storageConfig = StorageHelper::getStorageConfig();
        $allowed = array();

        if (isset($storageConfig->client_id_ouvidoria)
            && !empty($storageConfig->client_id_ouvidoria)
        ) {
            $allowed[] = $storageConfig->client_id_ouvidoria;
        }

        $metadata = new \stdClass();
        $metadata->tipo_documento = "processo";
        $metadata->numero_do_processo = $processoProtocolo->getNumeroProcesso()
            . "/" . $processoProtocolo->getAnoProcesso();
        $metadata->requerente = $processoProtocolo->getRequerente();
        $rsProcessoOuvidoria = db_query(
            "
                    SELECT
                    *
                    FROM processoouvidoria
                    INNER JOIN ouvidoriaatendimento
                    ON processoouvidoria.ov09_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial
                    WHERE
                    ov09_protprocesso = {$processoProtocolo->getCodProcesso()}
         "
        );

        $atendimentoOuvidoria = pg_fetch_object($rsProcessoOuvidoria);

        if (!empty($atendimentoOuvidoria)) {
            $numeroAtendimento = $atendimentoOuvidoria->ov01_numero;
            $numeroAtendimento .= "/"
                . $atendimentoOuvidoria->ov01_anousu;
            $metadata->numero_atendimento = $numeroAtendimento;
            $metadata->data_hora = $procandamint->p78_data . " "
                . $procandamint->p78_hora;
        }

        $usuarioSistema = new UsuarioSistema(
            db_getsession("DB_id_usuario")
        );
        $metadata->codigo_usuario_aprovacao = $usuarioSistema->getCodigo();
        $metadata->login_usuario_aprovacao = $usuarioSistema->getLogin();
        $metadata->codigoDespacho = $procandamint->p78_sequencial;

        $ordem = processoDocumento::getLastOrdemProcesso($codproc)
            + 1;
        $processoDocumento = new ProcessoDocumento(null);
        $processoDocumento->setProcandamint($procandamint->p78_sequencial);
        $processoDocumento->setDescricao("Despacho do processo {$codproc}");
        $processoDocumento->setProcessoProtocolo($processoProtocolo);
        $processoDocumento->setUsuario($usuarioSistema);
        $processoDocumento->setCaminhoArquivo($caminho);
        $processoDocumento->setStorage(true);
        $processoDocumento->setOrdem($ordem);
        $processoDocumento->setNomeDocumento(
            "Despacho do processo {$codproc}.pdf"
        );
        $processoDocumento->setOID(
            StorageHelper::uploadArquivo($caminho, $allowed, true, $metadata)
        );
        $processoDocumento->salvar();

        return true;
    }

    /**
     * @throws Exception
     */
    public function salvarMensagemOuvidoria()
    {
        $daoProcesso = new \cl_protprocesso();
        $processoProtocolo = new \processoProtocolo(
            $this->parametros->codigoProcesso
        );
        $opcaoDeMensagem = array(
            "mensagemPrefeitura" => array(
                "tipo_despacho" => 1003,
                "despacho" => "Mensagem criada pela Prefeitura.",
            ),
            "mensagemCidadao" => array(
                "tipo_despacho" => 1001,
                "despacho" => "Mensagem criada pelo Cidadão.",
            ),
            "respostaCidadao" => array(
                "tipo_despacho" => 1000,
                "despacho" => "Resposta criada pelo Cidadão.",
            ),
            "respostaPrefeitura" => array(
                "tipo_despacho" => 1002,
                "despacho" => "Resposta criada pela Prefeitura.",
            ),
        );

        $mensagemTipo = $opcaoDeMensagem[$this->parametros->acao];
        if (empty($mensagemTipo)) {
            throw new \Exception("Tipo de mensagem é inválida!");
        }
        /**
         * Entra no if caso a mensagem recebida
         * seja uma mensagem criada na Ouvidoria (não é uma resposta a uma mensagem da Prefeitura)
         */
        if (in_array(
            $this->parametros->acao,
            ["mensagemCidadao", "mensagemPrefeitura"]
        )
        ) {
            if (empty($processoProtocolo->getNumeroProcesso())) {
                throw new \Exception(
                    "Não foi possível buscar o processo de código {$this->parametros->codigoProcesso}"
                );
            }

            if ($this->parametros->acao == "mensagemPrefeitura") {
                $this->notificar($processoProtocolo, "mensagem");
            }

            $daoProcesso = $this->incluirMensagemProcessoEletronico(
                $processoProtocolo,
                $mensagemTipo["despacho"]
            );
            $idAndamento = $daoProcesso->p58_codandam;
        }


        if (in_array(
            $this->parametros->acao,
            ["respostaPrefeitura", "respostaCidadao"]
        )
        ) {
            if (empty($this->parametros->codigoAndamento)) {
                throw new \Exception("codigo do andamento é obrigatorio");
            }
            $idAndamento = $this->parametros->codigoAndamento;
        }

        if (empty($idAndamento)) {
            throw new \Exception("Não foi encontrado andamento!");
        }

        if (in_array(
            $this->parametros->acao,
            ["respostaPrefeitura", "mensagemPrefeitura"]
        )
        ) {
            $idUsuario = db_getsession("DB_id_usuario");
        }

        if (in_array(
            $this->parametros->acao,
            ["respostaCidadao", "mensagemCidadao"]
        )
        ) {
            $rs = pg_query(
                "
                SELECT
                    p78_usuario
                FROM
                    procandamint
                WHERE
                    p78_codandam = {$idAndamento}
                ORDER BY p78_sequencial DESC
               "
            );

            $andamentoAnterior = pg_fetch_object($rs);

            $idUsuario = $daoProcesso->p58_id_usuario;

            if (!empty($andamentoAnterior->p78_usuario)) {
                $idUsuario = $andamentoAnterior->p78_usuario;
            }
        }

        if (empty($idUsuario)) {
            throw new \Exception("Usuário não encontrado!");
        }

        $andamentoInterno = new AndamentoProcessoInterno();
        $andamentoInterno->setIdAndamento($idAndamento);
        $andamentoInterno->setDespacho(addslashes($this->parametros->mensagem));
        $andamentoInterno->setPublico(true);
        $andamentoInterno->setTransitoInterno(false);
        $andamentoInterno->setData(date('Y-m-d'));
        $andamentoInterno->setHora(db_hora());
        $andamentoInterno->setIdUsuario($idUsuario);
        $andamentoInterno->setIdTipoDespacho($mensagemTipo["tipo_despacho"]);

        try {
            $andamentoInterno = $this->repositorioAndamentoInterno->save(
                $andamentoInterno
            );
        } catch (Exception $e) {
            throw new Exception('Erro ao cadastrar Andamento Interno.');
        }

        $daoProcandam = new \cl_procandam();

        $sql = $daoProcandam->sql_query_file(
            $andamentoInterno->getIdAndamento(),
            'p61_codproc'
        );
        $rs = db_query($sql);

        $idProcesso = pg_fetch_assoc($rs, 0)['p61_codproc'];

        $processoProtocolo = new processoProtocolo($idProcesso);
        if (!empty($this->parametros->anexos)
            && $this->parametros->anexos != 'null'
        ) {
            $this->salvarAnexosMensagem($processoProtocolo, $andamentoInterno);
        }

        /**
         * SALVA ARQUIVOS NO STORAGE
         */
        if (!empty($this->parametros->despachoAnexos)) {
            $this->salvarDespachoAnexos($processoProtocolo, $andamentoInterno);
        }

        $retorno = (object)array(
            'mensagem' => false,
        );

        return $retorno;
    }

    private function salvarDespachoAnexos(
        \processoProtocolo       $processoProtocolo,
        AndamentoProcessoInterno $andamentoInterno
    ) {
        $anexos = json_decode($this->parametros->despachoAnexos);
        $anexosAux = array();

        foreach ($anexos as $anexo) {
            $storageConfig = StorageHelper::getStorageConfig();
            $allowed = array();

            if (isset($storageConfig->client_id_ouvidoria)
                && !empty($storageConfig->client_id_ouvidoria)
            ) {
                $allowed[] = $storageConfig->client_id_ouvidoria;
            }

            $arquivo = StorageHelper::uploadArquivo(
                $anexo->caminho,
                $allowed
            );
            $anexoAux = new stdClass();
            $anexoAux->id = $arquivo->id;
            $anexoAux->nome = $anexo->descricao;
            $anexosAux[] = $anexoAux;
        }

        try {
            $this->salvarDocumentos(
                $anexosAux,
                $processoProtocolo->getCodProcesso(),
                $andamentoInterno
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao salvar Documentos vindos da Ouvidoria.'
            );
        }
    }


    private function salvarAnexosMensagem(
        \processoProtocolo       $processoProtocolo,
        AndamentoProcessoInterno $andamentoInterno
    ) {
        $anexosAux = array();

        foreach ($this->parametros->anexos as $anexo) {
            $anexo = (object)$anexo;
            $storageConfig = StorageHelper::getStorageConfig();
            $allowed = array();

            if (isset($storageConfig->client_id_ouvidoria)
                && !empty($storageConfig->client_id_ouvidoria)
            ) {
                $allowed[] = $storageConfig->client_id_ouvidoria;
            }

            try {
                $arquivo = StorageHelper::uploadArquivo(
                    $anexo->caminho,
                    $allowed
                );
                $anexoAux = new stdClass();
                $anexoAux->id = $arquivo->id;
                $anexoAux->nome = $anexo->nome;
                $anexosAux[] = $anexoAux;
            } catch (\Exception $ex) {
                foreach ($anexosAux as $anexoEnviado) {
                    try {
                        StorageHelper::deleteArquivo($anexoEnviado->id);
                    } catch (\Exception $ex) {
                        $logger = Registry::get('app.container')->get(
                            'app.logger'
                        );
                        $logger->error($this->curl->getErro());
                    }
                }

                throw new \Exception($ex->getMessage());
            }
        }

        try {
            $this->salvarDocumentos(
                $anexosAux,
                $processoProtocolo->getCodProcesso(),
                $andamentoInterno
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao salvar Documentos vindos da Ouvidoria.'
            );
        }
    }

    private function salvarDocumentos(
        $anexos,
        $idProcesso,
        AndamentoProcessoInterno $andamentoInterno
    ) {
        $ordem = processoDocumento::getLastOrdemProcesso($idProcesso) + 1;
        foreach ($anexos as $anexo) {
            $processoDocumento = new DocumentoProcesso();

            $processoDocumento->setDescricao(
                'Documento da mensagem enviada pela Ouvidoria.'
            );
            $processoDocumento->setProcesso($idProcesso);
            $processoDocumento->setDocumento($anexo->id);
            $processoDocumento->setStorage(true);
            $processoDocumento->setNomeDocumento($anexo->nome);
            $processoDocumento->setData(date('Y-m-d'));
            $processoDocumento->setAndamento($andamentoInterno->getId());
            $processoDocumento->setUsuario($andamentoInterno->getIdUsuario());
            $processoDocumento->setOrdem($ordem++);

            $processoDocumentoRepository = new ProcessoDocumentoRepository();

            $processoDocumentoRepository->persist($processoDocumento);
        }
    }

    /**
     * @throws BusinessException
     */
    private function incluirMensagemProcessoEletronico(
        \processoProtocolo $processo,
        $despacho
    ) {
        $daoProcesso = new \cl_protprocesso;

        // Inclui processo referente a mensagem

        $daoProcesso->p58_dtproc = date("Y-m-d");
        $daoProcesso->p58_id_usuario = db_getsession("DB_id_usuario");
        $daoProcesso->p58_despacho = addslashes($despacho);
        $daoProcesso->p58_hora = date("H:i");
        $daoProcesso->p58_ano = db_getsession("DB_anousu");
        $daoProcesso->p58_publico = 't';
        $daoProcesso->p58_interno = 't';
        $daoProcesso->p58_codigo = $processo->getTipoProcesso();
        $daoProcesso->p58_dtproc = date(
            'Y-m-d',
            db_getsession('DB_datausu')
        );
        $daoProcesso->p58_id_usuario = db_getsession('DB_id_usuario');
        $daoProcesso->p58_numcgm = $processo->getCgm();
        $daoProcesso->p58_requer = $processo->getRequerente();
        $daoProcesso->p58_coddepto = $processo->getDepartamentoAtual()
            ->getCodigo();
        $daoProcesso->p58_obs = $despacho;
        $daoProcesso->p58_despacho = '';
        $daoProcesso->p58_hora = date('H:i');
        $daoProcesso->p58_instit = $processo->getInstituicaoCodigo();
        $daoProcesso->p58_ano = db_getsession('DB_anousu');
        $daoProcesso->p58_tipoprocesso = 4;

        $cgm = $processo->getCgm();
        $sql = "select * from cgm where z01_numcgm = $cgm";

        $result = db_query($sql);
        $camposCgm = \db_utils::fieldsMemory($result, 0);
        $orgao = '';
        $idTipoDocumentoProcesso = '';

        if (ProcessoProtocoloNumeracao::getTipoConfiguracao()
            == ProcessoProtocoloNumeracao::TIPOORGAO
        ) {
            $orgao = DBDepartamentoRepository::getIdOrgaoByCodigo(
                db_getsession('DB_coddepto')
            );
            $daoProcesso->p58_orgao = $orgao;

            $daoTipoProcesso = new \cl_tipoproc();
            $sql = $daoTipoProcesso->sql_query(
                null,
                'p51_prottipodocumentoprocesso',
                null,
                "p51_codigo = {$processo->getTipoProcesso()}"
            );
            $postgresObject = db_query($sql);

            $rsTipoProcesso = pg_fetch_assoc($postgresObject);
            $idTipoDocumentoProcesso
                = $rsTipoProcesso['p51_prottipodocumentoprocesso'];
        }

        $daoProcesso->p58_numero = ProcessoProtocoloNumeracao::getProximoNumero(
            $orgao,
            $idTipoDocumentoProcesso
        );

        $daoProcesso->incluir(null);

        if ($daoProcesso->erro_status == "0") {
            throw new \Exception('Não foi possível criar processo eletrônico.');
        }

        $daoProcessosVinculados = new \cl_processosvinculados;

        $daoProcessosVinculados->setSalvarAccount(false);
        $daoProcessosVinculados->p92_sequencial = null;
        $daoProcessosVinculados->p92_processopai = $processo->getCodProcesso();
        $daoProcessosVinculados->p92_processofilho = $daoProcesso->p58_codproc;
        $daoProcessosVinculados->p92_tipo = 5;

        $daoProcessosVinculados->incluir();

        if ($daoProcessosVinculados->erro_status == "0") {
            throw new Exception(
                "Não foi possível vincular o processo eletrônico.\nContate o suporte."
                . pg_last_error()
            );
        }

        // Inclui andamento para mensagem
        $andamentoProcessoEletronico = new \cl_procandam();

        $andamentoProcessoEletronico->p61_codproc = $daoProcesso->p58_codproc;
        $andamentoProcessoEletronico->p61_coddepto
            = $daoProcesso->p58_coddepto;
        $andamentoProcessoEletronico->p61_dtandam = date("Y-m-d");
        $andamentoProcessoEletronico->p61_publico = 't';
        $andamentoProcessoEletronico->p61_hora = date('H:i');
        $andamentoProcessoEletronico->p61_id_usuario
            = $daoProcesso->p58_id_usuario;
        $andamentoProcessoEletronico->p61_despacho = $despacho;

        $andamentoProcessoEletronico->incluir(null);

        if ($andamentoProcessoEletronico->erro_status == '0') {
            throw new \Exception(
                'Não foi possível criar andamento para o processo eletrônico.'
            );
        }

        // Vincula andamento com processo da mensagem
        $daoProcesso->p58_codandam = $andamentoProcessoEletronico->p61_codandam;
        $daoProcesso->alterar($daoProcesso->p58_codproc);

        if ($daoProcesso->erro_status == '0') {
            throw new \Exception(
                'Não foi possível vincular andamento com processo da mensagem.'
            );
        }

        return $daoProcesso;
    }

    /**
     * @throws Exception
     */
    public function notificar(
        \processoProtocolo $processoAtual,
        $tipoNotificacao,
        $detalhe = ""
    ) {
        //notificar user interno da transferencia
        if (!empty($this->parametros->recebimentoDestino)) {
            $descricaoDepartamento = $this->nomeDepartamentoById(
                $this->parametros->departamentoDestino
            );
            $mensagem
                = "O processo {$processoAtual->getNumeroProcesso()}/{$processoAtual->getAnoProcesso()} ";
            $mensagem .= "foi envido para seu usuário no departamento {$this->parametros->departamentoDestino}";
            $mensagem .= "-$descricaoDepartamento.";
            $notificarTransferenciaService
                = new NotificarTransferenciaProcessoService();
            $notificarTransferenciaService->dispatchJobNotificarTransferencia(
                $this->parametros->recebimentoDestino,
                $mensagem
            );
        }


        if ($processoAtual->isEletronico() === true) {
            $titular = \CgmFactory::getInstanceByCgm(
                $processoAtual->getCgm()
            );
            $atendimentoProcesso = $processoAtual->getProcessoAtendimento();
            if ($atendimentoProcesso) {
                $atendimentoProcessoEletronico
                    = $atendimentoProcesso->getAtendimentoProcessoEletronico();
                $atendimentoCidadao
                    = $atendimentoProcesso->getAtendimentoCidadao();
                if ($atendimentoCidadao) {
                    $cidadao = $atendimentoCidadao->getCidadao();
                }
                $atendimentoOuvidoria
                    = $atendimentoProcesso->getAtendimentoOuvidoria();
            }


            if ($atendimentoProcesso and
                ($atendimentoOuvidoria->getRequerente() !== "ANONIMO"
                    || $atendimentoCidadao)
            ) {
                $message = "";
                switch ($tipoNotificacao) {
                    case "despacho":
                        if ($this->parametros->despachoPublico === true) {
                            $message
                                = "O processo {$processoAtual->getNumeroProcesso()}/";
                            $message .= "{$processoAtual->getAnoProcesso()} do titular";
                            $message .= " {$titular->getNome()}, tem um novo despacho.";
                            $message .= " {$detalhe}";
                        }
                        break;
                    case "mensagem":
                        $message
                            = "O processo {$processoAtual->getNumeroProcesso()}/";
                        $message .= "{$processoAtual->getAnoProcesso()} do titular ";
                        $message .= " {$titular->getNome()}, tem uma nova mensagem.";
                        break;
                    case "transferencia":
                        $message
                            = "O processo {$processoAtual->getNumeroProcesso()}/";
                        $message .= "{$processoAtual->getAnoProcesso()} do titular ";
                        $message .= " {$titular->getNome()}, foi tramitado para outro departamento.";
                        break;
                }

                if (!empty($message)) {
                    $integracaoProcessoEletronico = new ProcessoEletronico();
                    $integracaoProcessoEletronico->notificar(
                        $message,
                        $cidadao->getCnpjCpf()
                    );
                }
            }
        }
    }

    public function isProcessoRedesim($processo)
    {
        switch ($processo->codigotipoprocesso) {
            case env("REDESIM_PROCESSO_INCLUSAO_INSCRICAO"):
            case env("REDESIM_PROCESSO_ATUALIZACAO_INSCRICAO"):
            case env("REDESIM_PROCESSO_BAIXA_INSCRICAO"):
                return true;
            default:
                return false;
        }
    }

    public function isProcessoRedesimInclusaoInscricao($processo)
    {
        if ($processo->codigotipoprocesso == env(
            "REDESIM_PROCESSO_INCLUSAO_INSCRICAO"
        )
        ) {
            return true;
        }

        return false;
    }

    private function nomeDepartamentoById($id)
    {
        $departamento = Departamento::find($id);

        return $departamento->descrdepto;
    }

    public function verificaSolicitacaoAssinatura($idAndamentoInterno)
    {
        $sql = "
            select p01_procandamint as codigo, db_usuacgm.id_usuario, data_assinatura
            from protocolo.documento_solicitacao_assinaturas
            inner join protocolo.protprocessodocumento on
            protocolo.protprocessodocumento.p01_sequencial  = protocolo.documento_solicitacao_assinaturas.documento_id
            inner join configuracoes.db_usuacgm on
            configuracoes.db_usuacgm.cgmlogin = protocolo.documento_solicitacao_assinaturas.cgm_assinante
            where p01_procandamint = {$idAndamentoInterno} and data_assinatura is null
        ";

        $rs = db_query($sql);
        $solicitadosAssinatura = array();

        if (pg_num_rows($rs) > 0) {
            while ($row = pg_fetch_object($rs)) {
                $solicitadosAssinatura[] = $row;
            }

            return $solicitadosAssinatura;
        }

        return $solicitadosAssinatura;
    }

    //funções da migração do RPC para api do andamento de processo
    public function despachosAnteriores($codProc, $perPage)
    {
        $sql = "SELECT distinct p78_sequencial AS codigo,
                p78_codandam AS codigoAndamento,
                TO_CHAR(p78_data::date, 'dd/mm/YYYY') AS data,
                p100_descricao AS tipo,
                nome AS usuario,
                p78_despacho AS despacho,
                p78_data,
                (SELECT
			       db_usuacgm.id_usuario
			    FROM
			        protocolo.documento_solicitacao_assinaturas
			        INNER JOIN protocolo.protprocessodocumento ON
			            protprocessodocumento.p01_sequencial = documento_solicitacao_assinaturas.documento_id
			        INNER JOIN configuracoes.db_usuacgm ON
			            configuracoes.db_usuacgm.cgmlogin = protocolo.documento_solicitacao_assinaturas.cgm_assinante
			    WHERE
			        p01_procandamint = procandamint.p78_sequencial
			    AND db_usuacgm.id_usuario = ?
        		AND data_assinatura IS NULL LIMIT 1) AS codigo_assinante
                FROM procandamint
                INNER JOIN db_usuarios ON db_usuarios.id_usuario = procandamint.p78_usuario
                LEFT JOIN tipodespacho ON p100_sequencial = p78_tipodespacho
                INNER JOIN protocolo.procandam ON protocolo.procandam.p61_codandam = procandamint.p78_codandam
                INNER JOIN protocolo.protprocesso on
                    protocolo.protprocesso.p58_codproc = protocolo.procandam.p61_codproc
                inner join protocolo.protprocessodocumento on
                    protocolo.protprocessodocumento.p01_protprocesso =  protocolo.protprocesso.p58_codproc
                WHERE protocolo.protprocesso.p58_codproc = ?
                ORDER BY p78_data DESC, p78_sequencial DESC";

        $bindings = [db_getsession("DB_id_usuario"), $codProc];
        $results = DB::select($sql, $bindings);

        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            array_slice(
                $results,
                ($page - 1) * $perPage,
                $perPage
            ),
            count($results),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    public function despacharProcesso($request, $processo = null)
    {
        $parametros = new \stdClass();
        $parametros->despachoInterno = $request->get('despachoInterno');
        $parametros->despachoPublico = $request->get('despachoPublico');
        $parametros->origemMensagem = $request->get('origemMensagem');
        $parametros->codigoProcesso =
            $request->get('codigoProcesso') ? $request->get('codigoProcesso') : $processo["codigoProcesso"];
        $parametros->hash = $request->get('hash') ? $request->get('hash') : $processo["hash"];

        if ($request->get('despachoAnexos')) {
            $parametros->despachoAnexos = array();
            foreach ($request->get('despachoAnexos') as $anexo) {
                ProcessoEletronicoService::validaAnexoBase64eAdicionaCaminhoTemporario(
                    $anexo,
                    true
                );
                $parametros->despachoAnexos[] = $anexo;
            }
        }

        $validacao
            = (new \App\Domain\Patrimonial\Protocolo\Services\ProcessoService)
            ->validarStatusProcesso(
                $parametros->hash,
                $parametros->codigoProcesso
            );
        if ($validacao->houveAlteracao) {
            return $validacao;
        }

        $this->setParametros($parametros);

        return $this->despachar(true);
    }

    private function iniciarLaravel()
    {
        $app = require_once ECIDADE_PATH . 'bootstrap/app.php';
        // App já iniciado
        if ($app === true) {
            return;
        }
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
    }

    public function criarSalvarPdfDespacho(
        $codigoProcesso,
        $observacao,
        $codigoAndamento
    ) {
        $processo = Processo::find($codigoProcesso);
        $this->iniciarLaravel();
        $nomeArquivo = "capa_despacho_{$codigoAndamento}.pdf";
        $capaDoDespacho = new CapaDoDespacho(
            $processo,
            $observacao,
            $nomeArquivo
        );

        $caminho = $capaDoDespacho->saveFile();

        return $caminho;
    }

    /**
     * Verifies if there are any unassigned documents related to a specific process.
     *
     * @param int $codigoAndamento The code of the process.
     * @param stdClass $parametrosProtocolo The protocol parameters.
     *
     * @return array Returns an array with the unassigned documents.
     */
    private function verificaDespachosNaoAssinados(
        $codigoAndamento,
        \stdClass $parametrosProtocolo
    ) {
        $dataAtivacao
            = $parametrosProtocolo->p90_data_implan_bloqueio_transferencia;

        $rs = db_query(
            "
                select
                *
                from
                protocolo.protprocessodocumento
                where
                p01_procandamint in (
                select
                p78_sequencial
                from
                procandamint
                where
                p78_codandam =  {$codigoAndamento}
                )
                and p01_descricao ilike '%Despacho do processo%'
                and p01_assinado = 'f'
                and p01_data >= '{$dataAtivacao}';
            "
        );

        return pg_fetch_all($rs);
    }

    private function verificaSolicitaoesDeAssinatura(
        $codigoAndamento,
        stdClass $parametrosProtocolo
    ) {
        $dataAtivacao
            = $parametrosProtocolo->p90_data_implan_bloqueio_transferencia;

        $rs = db_query(
            "
           select *
            from protocolo.documento_solicitacao_assinaturas
            inner join protocolo.protprocessodocumento on
            protocolo.protprocessodocumento.p01_sequencial  = protocolo.documento_solicitacao_assinaturas.documento_id
            where p01_procandamint in  (  select
                p78_sequencial
                from
                procandamint
                where
                p78_codandam =  {$codigoAndamento}
            ) and data_assinatura is null and created_at >= '{$dataAtivacao}'
            "
        );

        return pg_fetch_all($rs);
    }


    private function obrigaAssinarDespacho(
        \processoProtocolo $processoProtocolo,
        stdClass           $parametrosProtocolo
    ) {
        $retorno = new stdClass();
        $retorno->erro = false;
        $retorno->mensagem = "";

        $totalDocumentosNaoAssinados = 0;
        if (!empty($parametrosProtocolo->p90_obriga_assinatura_despacho)
            && $parametrosProtocolo->p90_obriga_assinatura_despacho === 't'
        ) {
            $documentosNaoAssinados = $this->verificaDespachosNaoAssinados(
                $processoProtocolo->getCodigoAndamento(),
                $parametrosProtocolo
            );

            if ($documentosNaoAssinados) {
                $totalDocumentosNaoAssinados = count($documentosNaoAssinados);
            }

            if ($totalDocumentosNaoAssinados > 0) {
                $dataAtivacao = new \DateTime(
                    $parametrosProtocolo->p90_data_implan_bloqueio_transferencia
                );
                $retorno->mensagem = "Para efetuar a transferência de um processo, todos os despachos apartir de
                {$dataAtivacao->format("d/m/Y")} devem ser assinados. Total de
                {$totalDocumentosNaoAssinados} documento(s) não assinado(s).";
                $retorno->erro = true;
            }
        }

        return $retorno;
    }

    private function obrigaAssinaturaDeDocumentosSolicitados(
        \processoProtocolo $processoProtocolo,
        stdClass           $parametrosProtocolo
    ) {
        $retorno = new stdClass();
        $retorno->erro = false;
        $retorno->mensagem = "";
        $totalDocumentosNaoAssinados = 0;
        if ($parametrosProtocolo->p90_permite_transferir_sem_assinatura
            !== 't'
        ) {
            $documentosNaoAssinados = $this->verificaSolicitaoesDeAssinatura(
                $processoProtocolo->getCodigoAndamento(),
                $parametrosProtocolo
            );

            if ($documentosNaoAssinados) {
                $totalDocumentosNaoAssinados = count($documentosNaoAssinados);
            }

            if ($totalDocumentosNaoAssinados > 0) {
                $dataAtivacao = new \DateTime(
                    $parametrosProtocolo->p90_data_implan_bloqueio_transferencia
                );
                $retorno->mensagem = "Para efetuar a transferência de um processo,
                todos os documentos que possuem solicitação apartir de
                {$dataAtivacao->format("d/m/Y")} devem ser assinados.
                Total de {$totalDocumentosNaoAssinados} documento(s) não assinado(s).";
                $retorno->erro = true;
            }
        }

        return $retorno;
    }

    /**
     * @throws Exception
     */
    public function despacharEmMassa($parametros)
    {
        $processos = $parametros->get('processos');

        if (!is_array($processos)) {
            throw new Exception("Processos não é um array!");
        }

        $processosSuccess = array();
        $processosError = array();
        foreach ($processos as $processo) {
            try {
                $this->despacharProcesso($parametros, $processo);
                $processosSuccess[] = $processo;
            } catch (\Exception $e) {
                $processoErro = new \stdClass();
                $processoErro->processo = $processo;
                $processoErro->mensagem = $e->getMessage();

                $processosError[] = $processoErro;
                Log::error($e->getMessage(), $e->getTrace());
            }
        }

        $result = new \stdClass();
        $result->despachados = $processosSuccess;
        $result->erros = $processosError;

        return $result;
    }

    public function getParametrosProtocolo()
    {
        $clprotparam = new \cl_protparam();
        $parametrosProtocolo = $clprotparam->sql_record(
            $clprotparam->sql_query(null, '*')
        );
        $parametrosProtocolo = \db_utils::fieldsMemory($parametrosProtocolo, 0);
        return $parametrosProtocolo;
    }

    public function transferirEmMassa($processos)
    {
        $daoTransferencia = new \cl_proctransfer();
        $daoTransferencia->p62_hora = db_hora();
        $daoTransferencia->p62_dttran = date(
            'Y-m-d',
            db_getsession('DB_datausu')
        );
        $daoTransferencia->p62_id_usuario = db_getsession("DB_id_usuario");
        $daoTransferencia->p62_coddepto = db_getsession("DB_coddepto");
        $daoTransferencia->p62_coddeptorec = $this->parametros->departamentoDestino;
        $daoTransferencia->p62_id_usorec = $this->parametros->recebimentoDestino;
        $daoTransferencia->incluir(null);

        if ($daoTransferencia->erro_status == 0) {
            throw new \Exception(
                "Não foi possível incluir uma nova transferência.\Contate o suporte."
            );
        }

        $codigoTransferencia = $daoTransferencia->p62_codtran;

        $processosSuccess = array();
        $processosError = array();

        foreach ($processos as $processo) {
            try {
                $retorno = $this->transferir($processo["codigoProcesso"], $codigoTransferencia);
                if (isset($retorno->erro) && $retorno->erro === true) {
                    $processoErro = new \stdClass();
                    $processoErro->processo = $processo;
                    $processoErro->mensagem = $retorno->mensagem;
                    $processosError[] = $processoErro;
                } else {
                    $processosSuccess[] = $processo;
                };
            } catch (\Exception $e) {
                $processoErro = new \stdClass();
                $processoErro->processo = $processo;
                $processoErro->mensagem = $e->getMessage();
                $processosError[] = $processoErro;
                Log::error($e->getMessage(), $e->getTrace());
            }
        }
        $result = new \stdClass();
        $result->transferidos = $processosSuccess;
        $result->erros = $processosError;
        $result->codigoTransferencia = $codigoTransferencia;

        return $result;
    }
}
