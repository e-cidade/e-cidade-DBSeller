<?php
if (!function_exists('fc_tipocertidaomatricula')) {
    /**
     * Função que busca o tipo certidão consultando a PL no banco de dados
     * @param $iOrigem
     * @param $sTipoOrigem
     * @param $sData
     * @param $iRegraCnd
     * @return string
     */
    function fc_tipocertidaomatricula($iOrigem, $sTipoOrigem, $sData, $iRegraCnd)
    {
        $aRetorno = DB::select("
            SELECT fc_tipocertidao AS tipocertidao
              FROM fc_tipocertidao({$iOrigem}, '{$sTipoOrigem}', '{$sData}', '', {$iRegraCnd})
        ")[0];

        return $aRetorno->tipocertidao;
    }
}

if (!function_exists('fc_iptu_fracionalote')) {
    /**
     * Função que busca a fração ideal da matricula
     * @param $iMatric
     * @param $iAnousu
     * @return integer
     */
    function fc_iptu_fracionalote($iMatric, $iAnousu)
    {
        $aRetorno = DB::select("
            SELECT rnfracao AS fracaoideal
              FROM fc_iptu_fracionalote({$iMatric}, {$iAnousu}, false, false)
        ")[0];

        return $aRetorno->fracaoideal;
    }
}

if (!function_exists('fc_busca_envolvidos')) {
    /**
     * Função que busca os CGMs envolvidos na origem
     * @param $iPrincipal
     * @param $iRegra
     * @param $sTipoOrigem
     * @param $iCodOrigem
     * @return array
     */
    function fc_busca_envolvidos($iPrincipal, $iRegra, $sTipoOrigem, $iCodOrigem)
    {
        $aRetorno = DB::select("
        SELECT *
          FROM fc_busca_envolvidos('{$iPrincipal}', {$iRegra}, '{$sTipoOrigem}', {$iCodOrigem})
        ");

        return $aRetorno;
    }
}

if (!function_exists('fc_executa_baixa_banco')) {
    /**
     * Função que faz a classificação para baixa de banco
     * @param $iCodret
     * @param $sData
     * @return array
     */
    function fc_executa_baixa_banco($iCodret, $sData)
    {
        $rRetorno = db_query("
            SELECT *
              FROM fc_executa_baixa_banco({$iCodret}, '{$sData}')
        ");

        if (!$rRetorno) {
            throw new \Exception("Erro ao executar a PL fc_executa_baixa_banco. ".pg_last_error());
        }

        return \db_utils::fieldsMemory($rRetorno, 0);
    }
}

if (!function_exists('fc_issqn')) {
    /**
     * Função que faz o cálculo do alvará
     * @param $inscricao
     * @param $dataCorrente
     * @param $dataBaixa
     * @param $ano
     * @param $instituicao
     * @param $sequencialAtividades
     * @param $isRecalculo
     * @param $isCalculoGeral
     * @return object
     * @throws Exception
     */
    function fc_issqn(
        $inscricao,
        $dataCorrente,
        $dataBaixa,
        $ano,
        $instituicao,
        $sequencialAtividades,
        $isRecalculo,
        $isCalculoGeral
    ) {
        $idUsuario = db_getsession("DB_id_usuario");
        $retorno = DB::select("
        SELECT fc_issqn(
                {$idUsuario},
                {$inscricao},
                '{$dataCorrente}',
                {$ano},
                '{$dataBaixa}',
                '{$isRecalculo}',
                '{$isCalculoGeral}',
                {$instituicao},
                '{$sequencialAtividades}'
                ) AS retorno
        ")[0];

        if (!$retorno) {
            throw new Exception("Não foi possível processar o cálculo.");
        }

        return (object) [
            "codigo" => substr($retorno->retorno, 0, 2),
            "mensagem" => substr($retorno->retorno, 2, strlen($retorno->retorno))
        ];
    }
}
