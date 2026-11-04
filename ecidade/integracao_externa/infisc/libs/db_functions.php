<?php

if (!function_exists("db_logTitulo")) {
    /**
     * Imprime o título do log
     *
     * @param string  $sTitulo
     * @param boolean $iParamLog  Caso seja passado true é exibido na tela
     */
    function db_logTitulo($sTitulo = "", $sArquivoLog = "", $iParamLog = 0)
    {
        db_log("", $sArquivoLog, $iParamLog);
        db_log("//".str_pad($sTitulo, 85, "-", STR_PAD_BOTH)."//", $sArquivoLog, $iParamLog);
        db_log("", $sArquivoLog, $iParamLog);
        db_log("", $sArquivoLog, $iParamLog);
    }
}

if (!function_exists("db_log")) {
    function db_log($sLog = "", $sArquivo = "", $iTipo = 0, $lLogDataHora = true, $lQuebraAntes = true)
    {
        $aDataHora  = getdate();
        $sQuebraAntes = $lQuebraAntes ? "\n" : "";

        if ($lLogDataHora) {
            $sOutputLog = sprintf("%s[%02d/%02d/%04d %02d:%02d:%02d] %s", $sQuebraAntes, $aDataHora ["mday"], $aDataHora ["mon"], $aDataHora ["year"], $aDataHora ["hours"], $aDataHora ["minutes"], $aDataHora ["seconds"], $sLog);
        } else {
            $sOutputLog = sprintf("%s%s", $sQuebraAntes, $sLog);
        }

        // Se habilitado saida na tela...
        if ($iTipo == 0 or $iTipo == 1) {
            echo $sOutputLog;
        }

        // Se habilitado saida para arquivo...
        if ($iTipo == 0 or $iTipo == 2) {
            if (! empty($sArquivo)) {
                $fd = fopen($sArquivo, "a+");
                if ($fd) {
                    fwrite($fd, $sOutputLog);
                    fclose($fd);
                }
            }
        }

        return $aDataHora;
    }
}

function db_query_integracao_externa($pConexao, $sSql, $sArquivoLog = "", $lErroDie = true, $lIgnoreAll = false)
{
    if (!is_resource($pConexao)) {
        db_log("ERRO: db_query - Conexao Invalida", $sArquivoLog);
        if ($lErroDie) {
            die();
        }
        return false;
    }

    if (empty($sSql) or is_null($sSql)) {
        db_log("ERRO: db_query - Sql vazio", $sArquivoLog);
        if ($lErroDie) {
            die();
        }
        return false;
    }

    $rsRetorno = @pg_query($pConexao, $sSql);

    if (!$rsRetorno && !$lIgnoreAll) {
        $sBackTrace = var_export(debug_backtrace(), true);
        db_log("ERRO: db_query - DEBUG BACKTRACE:\n$sBackTrace", $sArquivoLog);
        db_log("ERRO: PostgreSQL (last)   - ".pg_last_error($pConexao)."\n", $sArquivoLog);
        if ($lErroDie) {
            die();
        }
    }

    return $rsRetorno;
}
