<?php

namespace App\Domain\RecursosHumanos\Pessoal\Relatorios;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\RecursosHumanos\Pessoal\Services\ContraChequeService;
use cl_cfpess;
use cl_rhemitecontracheque;
use \db_utils;
use DBPessoal;
use Exception;

class ContraChequePdf
{
    private $matricula;
    private $ano;
    private $mes;
    private $folha;
    private $numero;
    /**
     * @var int
     */
    private $vias = 1;
    /**
     * @var string|null
     */
    private $urlAutenticidade;
    private $instituicao;

    /**
     * @param  mixed  $matricula
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
    }

    /**
     * @return mixed
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * @param  mixed  $folha
     */
    public function setFolha($folha)
    {
        $this->folha = $folha;
    }

    /**
     * @return mixed
     */
    public function getFolha()
    {
        return $this->folha;
    }

    /**
     * @param  mixed  $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param  mixed  $instituicao
     */
    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
    }

    /**
     * @return mixed
     */
    public function getInstituicao()
    {
        return $this->instituicao;
    }


    /**
     * @param $matricula
     * @param $ano
     * @param $mes
     * @param $folha
     * @param $numero
     * @param $instituicao
     */
    public function __construct($matricula, $ano, $mes, $folha, $numero, $instituicao)
    {
        $this->matricula = $matricula;
        $this->ano = $ano;
        $this->mes = $mes;
        $this->folha = $folha;
        $this->numero = $numero;
        $this->instituicao = $instituicao;

        /**
         * Carregar URL do processo eletrônico
         */
        $this->urlAutenticidade = env('BASE_URL_AUTENTICIDADE') . "/contracheque";
    }

    /**
     * @param $mostrar
     * @return string
     * @throws Exception
     */
    public function emitir($mostrar = false)
    {
        $this->dependencias();
        $configTipoFolha = ContraChequeService::buscarConfiguracaoContraCheque($this->folha, $this->numero);
        $idEstorage = $this->verificarContraChequeJaEmitido($configTipoFolha->tipoFolha);
        if ($idEstorage) {
            return $this->downloadArquivo($idEstorage, $mostrar);
        }
        return $this->gerarContraCheque($configTipoFolha, $mostrar);
    }

    /**
     * @return false|integer
     * @throws Exception
     */
    private function verificarContraChequeJaEmitido($tipoFolha)
    {
        $iInstituicao = db_getsession('DB_instit');
        $oDaoEmiteContraCheque = new cl_rhemitecontracheque();
        $whereContraCheque = [
            "rh85_regist = {$this->matricula}",
            "rh85_anousu = {$this->ano}",
            "rh85_mesusu = {$this->mes}",
            "rh85_tipofolha = {$tipoFolha}",
            "rh85_numero = {$this->numero}",
            "rh85_instit = {$iInstituicao}",
            "rh85_estorage is not null"
        ];
        $sqlContraCheque = $oDaoEmiteContraCheque->sql_query_file(
            null,
            'rh85_estorage',
            null,
            implode(' AND ', $whereContraCheque)
        );
        $rsContraCheque = db_query($sqlContraCheque);
        if (!$rsContraCheque) {
            throw new Exception("Erro ao verificar contra cheque existente.");
        }
        if (pg_num_rows($rsContraCheque) == 0) {
            return false;
        }

        return pg_fetch_object($rsContraCheque)->rh85_estorage;
    }

    /**
     * @param $configTipoFolha
     * @param $mostrar
     * @return string
     * @throws Exception
     */
    private function gerarContraCheque($configTipoFolha, $mostrar)
    {
        $oDaoCfPess = new cl_cfpess();
        $iTipoRelatorio = $oDaoCfPess->buscaCodigoRelatorio('contracheque', $this->ano, $this->mes);
        if (!$iTipoRelatorio) {
            throw new Exception('Modelo de impressão invalido, verifique parametros.');
        }

        $oDaoEmiteContraCheque = new cl_rhemitecontracheque();

        $iInstituicao = db_getsession('DB_instit');
        $sSql = "SELECT * FROM db_config WHERE codigo = {$iInstituicao}";

        $rsResult = db_query($sSql);
        $oConfig = db_utils::fieldsMemory($rsResult, 0);

        $sWhereSemest = '';
        $lNovaRotina = false;

        /**
         * Controla se o salário é da nova rotina.
         */
        if ($this->folha == 'salario' && DBPessoal::verificarUtilizacaoEstruturaSuplementar()) {
            $lNovaRotina = true;
            $sWhereSemest = 'AND rh141_codigo = 0';
        }

        if (!empty($this->numero)) {
            if (DBPessoal::verificarUtilizacaoEstruturaSuplementar()) {
                $sWhereSemest = "AND rh141_codigo = {$this->numero}";
                $lNovaRotina = true;
                $aWhere[] = "
            rh143_regist IN (
                SELECT rh143_regist
                FROM rhfolhapagamento
                    INNER JOIN rhhistoricocalculo ON rh143_folhapagamento = rh141_sequencial
                WHERE rh141_mesusu         = {$this->mes}
                AND rh141_anousu         = {$this->ano}
                AND rh141_instit         = {$iInstituicao}
                AND rh141_codigo         = {$this->numero}
                AND rh141_tipofolha      = {$configTipoFolha->tipoFolha}
            )
            ";
            } else {
                $sWhereSemest = " AND r48_semest = {$this->numero}";
                $aWhere[] = "r48_semest = {$this->numero}";
            }
        }
        $sCampo = "{$configTipoFolha->prefixo}_regist";
        if ($lNovaRotina) {
            $sCampo = "rh143_regist";
        }

        $aWhere[] = "{$sCampo} = {$this->matricula}";
        $sWhere = '';
        if (count($aWhere) > 0) {
            $sWhere = 'WHERE ' . implode(' AND ', $aWhere);
        }

        /**
         * Inicio de rotina parametrizada.
         */
        if (DBPessoal::verificarUtilizacaoEstruturaSuplementar() && $lNovaRotina) {
            $sSql = "
        SELECT DISTINCT z01_nome AS nome,
                        r70_descr AS lotacao,
                        rh37_descr AS cargo,
                        rh04_descr AS funcao,
                        cgmfisico.z04_nomesocial as nomesocial,
                        rh44_codban AS banco,
                        rh44_conta AS conta,
                        rh44_agencia AS agencia,
                        rh44_dvconta AS dvconta,   /* Favor atualizar a documentacao. */
                        rh44_dvagencia AS dvagencia, /* Favor atualizar a documentacao. */
                        rh01_admiss AS admissao,
                        rh56_localtrab AS localtrabalho,
                        rh143_regist AS matricula,
                        substr(r70_estrut, 1, 7) AS estrutural,
                        substr(
                        db_fxxx(rh143_regist,
                                {$this->ano},
                                {$this->mes},
                                {$iInstituicao}), 111, 11
                        ) AS salario, /* F010: Salário base com progreção. */
                        substr(
                        db_fxxx(rh143_regist,
                                {$this->ano},
                                {$this->mes},
                                {$iInstituicao}), 222, 20
                        ) AS padrao
        FROM (
            SELECT DISTINCT rh143_regist,
                            rh141_anousu,
                            rh141_mesusu
            FROM rhhistoricocalculo
                INNER JOIN rhfolhapagamento ON rh143_folhapagamento = rh141_sequencial
                INNER JOIN rhpessoalmov ON rh02_anousu = rh141_anousu
                                        AND rh02_mesusu = rh141_mesusu
                                        AND rh02_instit = rh141_instit
                                        AND rh02_regist = rh143_regist
            WHERE rh141_anousu = {$this->ano}
                AND rh141_mesusu = {$this->mes}
                AND rh141_instit = {$iInstituicao}
                AND rh141_tipofolha = {$configTipoFolha->tipoFolha}
            ) AS rhhistoricocalculo
            INNER JOIN rhpessoal ON rh01_regist = rh143_regist
            INNER JOIN rhpessoalmov ON rh02_regist =  rh01_regist
                                    AND rh02_anousu = {$this->ano}
                                    AND rh02_mesusu = {$this->mes}
                                    AND rh02_instit = {$iInstituicao}
            INNER JOIN rhregime ON rh02_codreg = rh30_codreg AND rh02_instit = rh30_instit
            INNER JOIN cgm ON rh01_numcgm = z01_numcgm
            LEFT JOIN cgmfisico ON cgm.z01_numcgm = cgmfisico.z04_numcgm
            LEFT JOIN rhfuncao ON rh37_funcao = rh02_funcao AND rh37_instit = rh02_instit
            LEFT JOIN rhlota ON  r70_codigo = rh02_lota AND r70_instit = rh02_instit
            LEFT JOIN rhpescargo ON rh20_seqpes = rh02_seqpes AND rh20_instit = rh02_instit
            LEFT JOIN rhpesbanco ON rh44_seqpes = rh02_seqpes
            LEFT JOIN rhcargo ON rh04_codigo = rh20_cargo AND rh04_instit = rh02_instit
            LEFT JOIN rhpeslocaltrab ON rh56_seqpes = rh02_seqpes AND rh56_princ  =  true
        {$sWhere}
    ";
        } else {
            $sWhereAuxiliar = bb_condicaosubpesproc(
                $configTipoFolha->prefixo . '_',
                $this->ano . "/" . $this->mes
            );
            $sWhereAuxiliar .= $sWhereSemest;

            $codicaoColuna_semest = "";
            if (trim($configTipoFolha->prefixo) == "r48") {
                $codicaoColuna_semest  = ",{$configTipoFolha->prefixo}_semest";
            }

            $sSql = "
        SELECT DISTINCT z01_nome                 AS nome,
                        r70_descr                AS lotacao,
                        rh37_descr               AS cargo,
                        rh04_descr               AS funcao,
                        cgmfisico.z04_nomesocial as nomesocial,
                        rh44_codban              AS banco,
                        rh44_agencia             AS agencia,
                        rh44_conta               AS conta,
                        rh44_dvagencia           AS dvagencia, /* Favor atualizar a documentacao. */
                        rh44_dvconta             AS dvconta,   /* Favor atualizar a documentacao. */
                        rh01_admiss              AS admissao,
                        rh56_localtrab           AS localtrabalho,
                        {$configTipoFolha->prefixo}_regist        AS matricula,
                        substr(r70_estrut, 1, 7) AS estrutural,
                        substr(
                        db_fxxx({$configTipoFolha->prefixo}_regist,
                                {$this->ano},
                                {$this->mes},
                                {$iInstituicao}), 111, 11
                        ) AS salario, /* F010: Salário base com progreção. */
                        substr(
                        db_fxxx({$configTipoFolha->prefixo}_regist,
                                {$this->ano},
                                {$this->mes},
                                {$iInstituicao}), 222, 20
                        ) AS padrao
        FROM (
            SELECT DISTINCT {$configTipoFolha->prefixo}_regist,
                            {$configTipoFolha->prefixo}_anousu,
                            {$configTipoFolha->prefixo}_mesusu
                            {$codicaoColuna_semest}
                FROM {$configTipoFolha->arquivo}
            {$sWhereAuxiliar}
            ) AS {$configTipoFolha->arquivo}
            INNER JOIN rhpessoal ON rh01_regist = {$configTipoFolha->prefixo}_regist
            INNER JOIN rhpessoalmov ON rh02_regist = rh01_regist
                                           AND rh02_anousu = {$this->ano}
                                           AND rh02_mesusu = {$this->mes}
                                           AND rh02_instit = {$iInstituicao}
            INNER JOIN rhregime ON rh02_codreg = rh30_codreg AND rh02_instit = rh30_instit
            INNER JOIN cgm ON rh01_numcgm = z01_numcgm
            LEFT JOIN cgmfisico ON cgm.z01_numcgm = cgmfisico.z04_numcgm
            LEFT  JOIN  rhfuncao ON rh37_funcao = rh02_funcao AND rh37_instit = rh02_instit
            LEFT  JOIN  rhlota ON  r70_codigo = rh02_lota AND r70_instit = rh02_instit
            LEFT  JOIN  rhpescargo ON rh20_seqpes = rh02_seqpes AND rh20_instit = rh02_instit
            LEFT  JOIN  rhpesbanco ON rh44_seqpes = rh02_seqpes
            LEFT  JOIN  rhcargo ON rh04_codigo = rh20_cargo AND rh04_instit = rh02_instit
            LEFT  JOIN  rhpeslocaltrab ON rh56_seqpes = rh02_seqpes AND rh56_princ = true
        {$sWhere}
    ";
        }

        $sSql = "
        SELECT *
            FROM(
            {$sSql}
            ) AS xxx, generate_series(1, $this->vias)
        ";

        $rsResult = db_query($sSql);
        $iNumRow = pg_num_rows($rsResult);
        if (!$iNumRow) {
            throw new Exception("Não existe Cálculo no período de {$this->mes}/{$this->ano}.");
        }

        /**
         * Grrrrrr... Globals
         */
        global $pdf;
        $pdf = new \scpdf();
        $pdf->setautopagebreak(false, 0.05);
        $pdf->Open();

        $oPDF = new \db_impcarne($pdf, $iTipoRelatorio);
        $oPDF->logo = $oConfig->logo;
        $oPDF->prefeitura = $oConfig->nomeinst;
        $oPDF->enderpref = $oConfig->ender . (isset($oConfig->numero) ? (", {$oConfig->numero}") : "");
        $oPDF->cgcpref = $oConfig->cgc;
        $oPDF->municpref = $oConfig->munic;
        $oPDF->telefpref = $oConfig->telef;
        $oPDF->emailpref = $oConfig->email;
        $oPDF->ano = $this->ano;
        $oPDF->mes = $this->mes;
        $oPDF->mensagem = '';
        $oPDF->qualarquivo = $configTipoFolha->titulo;

        $aServidores = db_utils::getCollectionByRecord($rsResult);
        foreach ($aServidores as $iIndex => $oServidor) {
            $rsResult = db_query("SELECT nextval('rhemitecontracheque_rh85_sequencial_seq') AS sequencial");
            $oSeqContraCheque = db_utils::fieldsMemory($rsResult, 0);
            $iSequencial = str_pad($oSeqContraCheque->sequencial, 6, '0', STR_PAD_LEFT);

            $iMes = str_pad($this->mes, 2, '0', STR_PAD_LEFT);
            $iMatricula = str_pad($oServidor->matricula, 6, '0', STR_PAD_LEFT);
            $iMod1 = db_CalculaDV($iMatricula);
            $iMod2 = db_CalculaDV($iMatricula . $iMod1 . $iMes . $this->ano . $iSequencial);

            $iCodAutent = $iMatricula . $iMod1 . $iMes . $iMod2 . $this->ano . $iSequencial;
            $sDataEmissao = date('Y-m-d', db_getsession('DB_datausu'));
            $sHoraEmissao = db_hora();
            $sIpEmissao = db_getsession('DB_ip');

            $oDaoEmiteContraCheque->rh85_sequencial = $iSequencial;
            $oDaoEmiteContraCheque->rh85_regist = $oServidor->matricula;
            $oDaoEmiteContraCheque->rh85_anousu = $this->ano;
            $oDaoEmiteContraCheque->rh85_mesusu = $this->mes;
            $oDaoEmiteContraCheque->rh85_sigla = $configTipoFolha->prefixo;
            $oDaoEmiteContraCheque->rh85_codautent = $iCodAutent;
            $oDaoEmiteContraCheque->rh85_dataemissao = $sDataEmissao;
            $oDaoEmiteContraCheque->rh85_horaemissao = $sHoraEmissao;
            $oDaoEmiteContraCheque->rh85_ip = $sIpEmissao;
            $oDaoEmiteContraCheque->rh85_externo = 'false';

            if ($iIndex % 2 == 0) {
                $oPDF->seq = 0;
            } else {
                $oPDF->seq = 1;
            }

            if (DBPessoal::verificarUtilizacaoEstruturaSuplementar() && $lNovaRotina) {
                $sSql = "
        SELECT rh143_rubrica              AS rubrica,
                round(rh143_valor,      2) AS valor,
                round(rh143_quantidade, 2) AS quantidade,
                rh27_descr                 AS descricao,
                {$configTipoFolha->sTipo} AS tipo,
                CASE WHEN rh143_tipoevento  = 1 THEN 'P'
                    WHEN rh143_tipoevento  = 2 THEN 'D'
                    ELSE 'B'
                END                        AS tipoevento
            FROM rhhistoricocalculo
            INNER JOIN rhfolhapagamento ON rh143_folhapagamento = rh141_sequencial
            INNER JOIN rhrubricas       ON rh143_rubrica        = rh27_rubric
                                        AND rh141_instit         = rh27_instit
        WHERE rh143_regist    = {$oServidor->matricula}
            AND rh141_anousu    = {$this->ano}
            AND rh141_mesusu    = {$this->mes}
            AND rh141_instit    = {$iInstituicao}
            AND rh141_tipofolha = {$configTipoFolha->tipoFolha}
            {$sWhereSemest}
        ORDER BY rh143_rubrica
        ";
            } else {
                $sSql = "
        SELECT {$configTipoFolha->prefixo}_rubric AS rubrica,
                round({$configTipoFolha->prefixo}_valor, 2) AS valor,
                round({$configTipoFolha->prefixo}_quant, 2) AS quantidade,
                rh27_descr                 AS descricao,
                {$configTipoFolha->sTipo}                   AS tipo,
                CASE WHEN {$configTipoFolha->prefixo}_pd = 1 THEN 'P'
                    WHEN {$configTipoFolha->prefixo}_pd = 2 THEN 'D'
                    ELSE 'B'
                END                        AS tipoevento
            FROM {$configTipoFolha->arquivo}
            INNER JOIN rhrubricas ON rh27_rubric = {$configTipoFolha->prefixo}_rubric
                                AND rh27_instit = {$iInstituicao}
        WHERE {$configTipoFolha->prefixo}_regist = {$oServidor->matricula}
            AND {$configTipoFolha->prefixo}_anousu = {$this->ano}
            AND {$configTipoFolha->prefixo}_mesusu = {$this->mes}
            AND {$configTipoFolha->prefixo}_instit = {$iInstituicao}
            {$sWhereSemest}
        ORDER BY {$configTipoFolha->prefixo}_rubric
        ";
            }

            $rsResult = db_query($sSql);
            // Inicializa os valores de provento e desconto
            $oDaoEmiteContraCheque->rh85_provento = 0;
            $oDaoEmiteContraCheque->rh85_desconto = 0;

            for ($i = 0; $i < pg_num_rows($rsResult); $i++) {
                $oStd = db_utils::fieldsMemory($rsResult, $i);
                switch ($oStd->tipoevento) {
                    case 'P':
                        $oDaoEmiteContraCheque->rh85_provento += $oStd->valor;
                        break;
                    case 'D':
                        $oDaoEmiteContraCheque->rh85_desconto += $oStd->valor;
                        break;
                }
            }
            $liquido = $oDaoEmiteContraCheque->rh85_provento - $oDaoEmiteContraCheque->rh85_desconto;
            $oDaoEmiteContraCheque->rh85_liquido = $liquido;
            $oDaoEmiteContraCheque->rh85_tipofolha = $configTipoFolha->tipoFolha;
            $oDaoEmiteContraCheque->rh85_numero = $this->numero;
            $oDaoEmiteContraCheque->rh85_instit = $iInstituicao;

            $oDaoEmiteContraCheque->incluir($iSequencial);
            if (!$oDaoEmiteContraCheque->erro_status) {
                throw new Exception($oDaoEmiteContraCheque->erro_msg);
            }

            $oPDF->registro = $oServidor->matricula;
            $oPDF->admissao = db_formatar($oServidor->admissao, 'd');
            $oPDF->nome = $oServidor->nome;
            $oPDF->descr_funcao = $oServidor->funcao;
            $oPDF->descr_cargo      = $oServidor->cargo;
            $oPDF->nomesocial = $oServidor->nomesocial;
            $oPDF->descr_lota = "{$oServidor->estrutural}-{$oServidor->lotacao}";
            $oPDF->f010 = $oServidor->salario;
            $oPDF->padrao = $oServidor->padrao;
            $oPDF->banco = $oServidor->banco;
            $oPDF->agencia = "{$oServidor->agencia}-{$oServidor->dvagencia}";
            $oPDF->conta = "{$oServidor->conta}-{$oServidor->dvconta}";
            $oPDF->lotacao_idade = 'quantidade';
            $oPDF->estrutural = $oServidor->estrutural;
            $oPDF->recordenvelope = $rsResult;
            $oPDF->linhasenvelope = pg_num_rows($rsResult);
            $oPDF->valor = 'valor';
            $oPDF->quantidade = 'quantidade';
            $oPDF->tipo = 'tipoevento';
            $oPDF->rubrica = 'rubrica';
            $oPDF->descr_rub = 'descricao';
            $oPDF->numero = $iIndex + 1;
            $oPDF->total = $iNumRow;
            $oPDF->codautent = $iCodAutent;
            $oPDF->url = $this->urlAutenticidade;
            $oPDF->imprimirQRCode = true;
            $oPDF->imprime();
        }

        $nomeArquivo = "tmp/contracheque_{$iCodAutent}_" . time() . ".pdf";
        $oPDF->objpdf->output($nomeArquivo, false, true);
        if (!file_exists($nomeArquivo)) {
            throw new Exception('Erro ao salvar contra-cheque em PDF.');
        }

        $storageConfig = StorageHelper::getStorageConfig();
        $allowed = [];
        if (!empty($storageConfig->client_id_ouvidoria)) {
            $allowed[] = $storageConfig->client_id_ouvidoria;
        }

        $metadata = new \stdClass();
        $metadata->autenticacao = "{$iCodAutent}";
        $idEstorage = StorageHelper::uploadArquivo($nomeArquivo, $allowed, true, $metadata);

        $oDaoEmiteContraCheque->rh85_estorage = $idEstorage;
        $oDaoEmiteContraCheque->alterar($oSeqContraCheque->sequencial);

        if (!$mostrar) {
            unlink($nomeArquivo);
            return '';
        }
        return $nomeArquivo;
    }

    private function dependencias()
    {
        require_once(modification("fpdf151/impcarne.php"));
        require_once(modification("fpdf151/scpdf.php"));
        require_once(modification("libs/db_utils.php"));
        require_once(modification("libs/db_libpessoal.php"));

        if (!empty($this->instituicao)) {
            db_putsession('DB_instit', $this->instituicao);
        }
    }

    private function downloadArquivo($idEstorage, $download)
    {
        if ($download) {
            return StorageHelper::downloadArquivo($idEstorage);
        }
        return '';
    }

    public function getAno()
    {
        return $this->ano;
    }

    public function getMes()
    {
        return $this->mes;
    }
}
