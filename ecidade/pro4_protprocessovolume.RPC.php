<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_app::import("protocolo.ProcessoProtocoloNumeracao");

$oParam = JSON::create()->parse(str_replace('\\', '', $_POST['json']));

$oRetorno = new stdClass();
$oRetorno->status = 1;
$oRetorno->erro = false;

try {
    db_inicio_transacao();

    switch ($oParam->exec) {

        case 'salvar':
            if (empty($oParam->p58_processopai)) {
                throw new Exception('Necessário informar o Processo Principal.');
            }

            $clprotprocesso = new cl_protprocesso();
            $clprotparam = new cl_protparam();

            $sqlProcessoPai = $clprotprocesso->sql_query_file($oParam->p58_processopai, 'p58_numero, p58_orgao');
            $postgresObject = db_query($sqlProcessoPai);

            if (pg_num_rows($postgresObject) == 0) {
                throw new Exception('Processo Principal informado não existe');
            }

            $processoPai = pg_fetch_assoc($postgresObject);

            $numeroProcessopai = $processoPai['p58_numero'];
            $orgaoProcessopai = $processoPai['p58_orgao'];

            // Inclusão
            if (!$oParam->p58_codproc) {
                $clprotprocesso->p58_hora = db_hora();
                $clprotprocesso->p58_ano = db_getsession("DB_anousu");
                $clprotprocesso->p58_id_usuario = db_getsession("DB_id_usuario");
                $clprotprocesso->p58_coddepto = db_getsession("DB_coddepto");
                $clprotprocesso->p58_interno = 'false' ;
                $clprotprocesso->p58_publico = 'false' ;
                $clprotprocesso->p58_instit = db_getsession("DB_instit");
                $clprotprocesso->p58_processopai = $oParam->p58_processopai;
                $clprotprocesso->p58_obs = $oParam->p58_obs;
                $clprotprocesso->p58_numcgm = $oParam->p58_numcgm;
                $clprotprocesso->p58_codigo = $oParam->p58_codigo;
                $clprotprocesso->p58_dtproc = date("Y-m-d",  db_getsession("DB_datausu"));
                $clprotprocesso->p58_requer = $oParam->p58_requer;

                // Numeração por Órgão
                if (ProcessoProtocoloNumeracao::getTipoConfiguracao() == ProcessoProtocoloNumeracao::TIPOORGAO) {
                    $clprotprocesso->p58_orgao = $orgaoProcessopai;
                    $clprotprocesso->set_volume();
                }

                $numeracaoProcessopai = (int) substr($numeroProcessopai, -8, 5);
                $numeracao = ProcessoProtocoloNumeracao::formataNumeracaoOrgao(
                    $numeracaoProcessopai,
                    $clprotprocesso->p58_orgao,
                    $oParam->p91_sequencial,
                    $clprotprocesso->p58_volume
                );
                $clprotprocesso->p58_numero = $numeracao;

                $clprotprocesso->incluir(null);
            } else {
                $clprotprocesso->p58_codproc = $oParam->p58_codproc;
                $clprotprocesso->p58_obs = $oParam->p58_obs;
                $clprotprocesso->p58_numcgm = $oParam->p58_numcgm;
                $clprotprocesso->p58_requer = $oParam->p58_requer;

                $clprotprocesso->alterar($oParam->p58_codproc);
            }

            if($clprotprocesso->erro_status === '0') {
                throw new Exception($clprotprocesso->erro_msg);
            }

            /**
             * Salva campos complementares
             */
            if (isset($oParam->docs) && $oParam->docs != "") {
                $clprocprocessodoc = new cl_procprocessodoc();
                if ($lSqlErro == false) {
                    $chaves =explode("#",$oParam->docs);
                    $chave  = count($chaves);
                    for($x = 0; $x < $chave-1; $x++){
                        $clprocprocessodoc->p81_codproc = $clprotprocesso->p58_codproc;
                        $clprocprocessodoc->p81_coddoc = $chaves[$x];
                        $clprocprocessodoc->p81_doc = 't';
                        $clprocprocessodoc->incluir($clprotprocesso->p58_codproc, $chaves[$x]);

                        if ($clprocprocessodoc->erro_status == '0') {
                            throw new Exception($clprocprocessodoc->erro_msg);
                        }
                    }
                }
            }

            if (isset($oParam->ndocs) && $oParam->ndocs != "") {
                $clprocprocessodoc = new cl_procprocessodoc();

                $chaves =explode("#",$oParam->ndocs);
                $chave  = count($chaves);

                for( $i = 0; $i < $chave - 1; $i++){
                    $HTTP_POST_VARS['p81_doc'] = 'f';
                    $clprocprocessodoc->p81_codproc = $clprotprocesso->p58_codproc;
                    $clprocprocessodoc->p81_coddoc = $chaves[$i];
                    $clprocprocessodoc->p81_doc = 'f';
                    $clprocprocessodoc->incluir($clprotprocesso->p58_codproc, $chaves[$i]);

                    if ($clprocprocessodoc->erro_status == '0') {
                        throw new Exception($clprocprocessodoc->erro_msg);
                    }
                }
            }

            $sSql = "select p54_codigo, p54_codcam from procvar where p54_codigo = {$oParam->p58_codigo};";
            $rsSql = db_query($sSql);

            if (pg_num_rows($rsSql) > 0) {
                while ($ln = pg_fetch_array($rsSql)){
                    $sSqlCam = "select nomecam,rotulo from db_syscampo where codcam = ".$ln["p54_codcam"];
                    $rsSqlCam = db_query($sSqlCam);

                    if (pg_numrows($rsSqlCam) > 0) {
                        $nomecam = trim(pg_result($rsSqlCam, 0, "nomecam"));
                        $rotulo = trim(pg_result($rsSqlCam, 0, "rotulo"));

                        $p55_codproc = $clprotprocesso->p58_codproc;
                        $p55_codvar = $ln["p54_codigo"];
                        $p55_codcam = $ln["p54_codcam"];

                        $clproctipovar->p55_conteudo = $$nomecam;
                        $clproctipovar->incluir($p55_codproc, $p55_codvar, $p55_codcam);

                        if ($clproctipovar->erro_status == '0') {
                            throw new Exception("INFORMAR OS DADOS COMPLEMENTARES - Campo: $rotulo");
                        }
                    }
                }
            }

            if ($clprocprocessodoc->erro_status == '0') {
                throw new Exception ($clprocprocessodoc->erro_msg);
            }

            /**
             * Verifica parâmetro de emissão de recibo
             */
            $result_param = $clprotparam->sql_record($clprotparam->sql_query_file(null, 'p90_emiterecib'));

            $p90_emiterecib = 'f';
            if (pg_num_rows($result_param) > 0) {
                db_fieldsmemory($result_param, 0);
            }

            $oRetorno->p90_emiterecib = $p90_emiterecib;
            $oRetorno->p58_codproc = $clprotprocesso->p58_codproc;
            $oRetorno->p58_numero = $clprotprocesso->p58_numero;
            $oRetorno->p58_codigo = $clprotprocesso->p58_codigo;

        break;

        case 'tipoProcesso':
            if (empty($oParam->p58_processopai)) {
                throw new Exception('Processo Principal não informado.');
            }

            $sql = "
                SELECT
                    tipoproc.p51_codigo,
                    tipoproc.p51_descr,
                    prottipodocumentoprocesso.p91_sequencial,
                    prottipodocumentoprocesso.p91_descricao,
                    protprocesso.p58_orgao
                FROM
                    protprocesso
                INNER JOIN tipoproc
                    ON p51_codigo = protprocesso.p58_codigo
                INNER JOIN prottipodocumentoprocesso
                    ON p91_sequencial = tipoproc.p51_prottipodocumentoprocesso
                WHERE
                    protprocesso.p58_codproc = {$oParam->p58_processopai}
            ";

            $postgresObject = db_query($sql);

            if (pg_num_rows($postgresObject) == 0) {
                throw new Exception("Processo Principal {$oParam->p58_processopai} não existe.");
            }

            $rs = pg_fetch_assoc($postgresObject);

            $oRetorno->p58_codigo = $rs['p51_codigo'];
            $oRetorno->p51_descr = $rs['p51_descr'];
            $oRetorno->p91_sequencial = $rs['p91_sequencial'];
            $oRetorno->p91_descricao = $rs['p91_descricao'];
            $oRetorno->p58_orgao = $rs['p58_orgao'];

        break;

        case 'buscaDocumentosProcesso':
            // Documentos tipo de processo
            $clprocdoctipo = new cl_procdoctipo;
            $postgresObject = db_query(
                $clprocdoctipo->sql_query($oParam->p58_codigo, '', 'p56_coddoc, p56_descr')
            );

            if (pg_num_rows($postgresObject) > 0) {
                $ndocs = '';
                $html = '<fieldset>';
                $html .= '<b>DOCUMENTOS</b><br>';

                if (empty($oParam->p58_codproc)) {
                    $i = 0;

                    while ($row = pg_fetch_assoc($postgresObject)) {
                        $html .= "
                            <input type='checkbox' name='doc{$i}' onClick='js_valor()' value='{$row['p56_coddoc']}'>
                            <b>{$row['p56_descr']}</b>
                            <br>
                        ";
                        $ndocs .= $row['p56_coddoc'] . "#";
                        ++$i;
                    }
                } else {
                    $cldoc   = new cl_procprocessodoc;
                    $sqldoc  = "
                        SELECT
                            coalesce(p81_doc, false) as p81_doc,
                            p56_coddoc,
                            p56_descr
                        FROM procdoctipo
                        INNER JOIN procdoc
                            ON p56_coddoc = p57_coddoc
                        LEFT  JOIN procprocessodoc
                            ON p81_coddoc = p57_coddoc
                                and p81_codproc = $oParam->p58_codproc
                        WHERE
                            p57_codigo = {$oParam->p58_codigo}
                    ";

                    $postgresObject = db_query($sqldoc);

                    if (pg_num_rows($postgresObject) > 0) {
                        $docs = "";
                        $ndocs = "";
                        $i = 0;

                        while ($row = pg_fetch_assoc($postgresObject)) {
                            $html .= "
                                <input type='checkbox' name='doc{$i}' " . ($row['p81_doc'] == 't' ? 'checked' : '' ) . "
                                    onClick='js_valor()' value='{$row['p56_coddoc']}'>
                                <b>{$row['p56_descr']}</b>
                                <br>
                            ";

                            if ($row['p81_doc'] == 't') {
                                $docs .= $row['p56_coddoc'] . "#";
                            } else {
                                $ndocs .= $row['p56_coddoc'] . "#";
                            }
                            ++$i;
                        }
                    }
                }

                $html .= '</fieldset>';
                $oRetorno->docs = $docs;
                $oRetorno->ndocs = $ndocs;
                $oRetorno->html = $html;
            }
        break;

        case 'buscarVolumes':
            $where = "p58_processopai = {$oParam->codigoProcesso}";

            if (!empty($oParam->orgao)) {
                $where .= " AND p58_orgao IN (SELECT p58_orgao FROM protprocesso WHERE p58_codproc = {$oParam->codigoProcesso}) ";
            }

            $join = '';
            if (!empty($oParam->arquivamento)) {
                $join = '
                    INNER JOIN tipoproc
                        ON p58_codigo   = p51_codigo
                    INNER JOIN procandam
                        ON p58_codandam = p61_codandam
                    INNER JOIN proctransand
                        ON p64_codandam = p58_codandam
                    INNER JOIN proctransfer
                        ON p62_codtran  = p64_codtran
                    LEFT JOIN arqproc
                        ON p68_codproc = p58_codproc
                ';

                $where .= "
                    AND (   p62_id_usorec   = ".db_getsession("DB_id_usuario")."
                        or p62_coddeptorec = ".db_getsession("DB_coddepto")."
                    )

                    AND  p68_codproc is null
                ";

            } else if (!empty($oParam->desarquivamento)) {

                $join = '
                    INNER JOIN procarquiv
                        ON procarquiv.p67_codproc = protprocesso.p58_codproc
                    INNER JOIN cgm
                        ON protprocesso.p58_numcgm = cgm.z01_numcgm
                    INNER JOIN arqproc
                        ON procarquiv.p67_codproc = arqproc.p68_codproc
                    LEFT JOIN arqandam
                        ON arqandam.p69_codarquiv = p67_codarquiv AND p69_arquivado is false
                ';

                $where  .= "
                    AND p67_coddepto = ".db_getsession("DB_coddepto") ."
                    AND p69_codarquiv is null
                ";
            }

            $sql = "
                SELECT
                    p58_codproc,
                    cast(p58_numero||'/'||p58_ano as varchar) as p58_numero,
                    p58_volume
                FROM
                    protprocesso
                    {$join}
                WHERE
                    {$where}
                ORDER BY
                    p58_volume ASC
            ";

            $postgresObject = db_query($sql);
            $volumes = array();

            while ($row = pg_fetch_assoc($postgresObject)) {
                $volumes[] = $row;
            }

            $oRetorno->volumes = $volumes;

        break;

        case 'desarquivar':
            db_inicio_transacao();

            $codigosProcessos = implode(',', $oParam->codigosProcessos);

            $sql = "
                SELECT DISTINCT
                    p67_codarquiv,
                    p68_codproc,
                    p67_coddepto
                FROM
                    arqproc
                INNER JOIN procarquiv ON
                    p68_codarquiv = p67_codarquiv
                WHERE
                    p68_codproc IN ({$codigosProcessos})
                ORDER BY
                    p68_codproc
            ";

            $postgresObject = db_query($sql);

            if (pg_num_rows($postgresObject) == 0) {
                throw new Exception('Erro, contate o suporte.');
            }

            $hoje = date('Y-m-d');

            while ($row = pg_fetch_assoc($postgresObject)) {
                /**
                 * Insere Transferência
                 */
                $daoTransferencia = new \cl_proctransfer;

                $daoTransferencia->p62_coddepto = $row['p67_coddepto'];
                $daoTransferencia->p62_dttran = $hoje;
                $daoTransferencia->p62_coddeptorec = $row['p67_coddepto'];
                $daoTransferencia->p62_id_usorec = db_getsession("DB_id_usuario");
                $daoTransferencia->p62_id_usuario = db_getsession("DB_id_usuario");
                $daoTransferencia->p62_hora = db_hora();

                $daoTransferencia->incluir(null);

                $codigoTransferencia = $daoTransferencia->p62_codtran;

                if($daoTransferencia->erro_status == 0) {
                    throw new Exception($daoTransferencia->erro_msg);
                }

                /**
                 * Insere vínculo Transferência Processo
                 */
                $daoTransferenciaProcesso = new \cl_proctransferproc;

                $daoTransferenciaProcesso->p63_codtran = $codigoTransferencia;
                $daoTransferenciaProcesso->p63_codproc = $row['p68_codproc'];

                $daoTransferenciaProcesso->incluir($codigoTransferencia, $row['p68_codproc']);

                if($daoTransferenciaProcesso->erro_status == 0) {
                    throw new Exception($daoTransferenciaProcesso->erro_msg);
                }

                /**
                 * Insere Andamento Processo
                 */
                $daoAndamento = new \cl_procandam;

                $daoAndamento->p61_despacho =  "Processo Desarquivado";
                $daoAndamento->p61_codproc = $row['p68_codproc'];
                $daoAndamento->p61_dtandam = $hoje;
                $daoAndamento->p61_hora = db_hora();
                $daoAndamento->p61_id_usuario = db_getsession("DB_id_usuario");
                $daoAndamento->p61_coddepto = $row['p67_coddepto'];
                $daoAndamento->p61_publico =  "t";

                $daoAndamento->incluir(null);

                if($daoAndamento->erro_status == 0) {
                    throw new Exception($daoAndamento->erro_msg);
                }

                /**
                 * Inclui o andamento e o cod. do arquivamento e diz se é arquivamento ou desarquivamento na tabela arqandam
                 */
                $daoArquivoAndamento = new \cl_arqandam;

                $daoArquivoAndamento->p69_codarquiv = $row['p67_codarquiv'];
                $daoArquivoAndamento->p69_codandam  = $daoAndamento->p61_codandam;
                $daoArquivoAndamento->p69_arquivado = 'false';
                $daoArquivoAndamento->incluir(null);

                if ($daoArquivoAndamento->erro_status == 0) {
                    throw new Exception($daoArquivoAndamento->erro_msg);
                }

                /**
                 * Inclui  a transferência e o andamento do processo na tabela proctransand
                 */
                $daoProcessoTransferenciaAndamento = new \cl_proctransand;

                $daoProcessoTransferenciaAndamento->p64_codtran  = $daoTransferencia->p62_codtran;
                $daoProcessoTransferenciaAndamento->p64_codandam = $daoAndamento->p61_codandam;

                $daoProcessoTransferenciaAndamento->incluir(null);

                if ($daoProcessoTransferenciaAndamento->erro_status == 0) {
                    throw new Exception($daoProcessoTransferenciaAndamento->erro_msg);
                }

                /**
                 * Atualiza andamento na tabela protprocesso
                 */
                $daoProcesso = new \cl_protprocesso;

                $daoProcesso->p58_codproc  = $row['p68_codproc'];
                $daoProcesso->p58_codandam = $daoAndamento->p61_codandam;
                $daoProcesso->p58_despacho = " ";

                $daoProcesso->alterar($row['p68_codproc']);

                if ($daoProcesso->erro_status == 0) {
                    throw new Exception($daoProcesso->erro_msg);
                }

                db_query("delete from arqproc where p68_codproc = {$row['p68_codproc']}");
            }

        break;
    }

    db_fim_transacao();

} catch (Exception $e) {
    db_fim_transacao(true);
    $oRetorno->status = 2;
    $oRetorno->erro = true;
    $oRetorno->message = str_replace('\n', "", $e->getMessage());
}

echo JSON::create()->stringify($oRetorno);
