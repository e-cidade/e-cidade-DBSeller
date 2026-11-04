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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

$parametros = JSON::requestParameters();
$retorno = (object)['erro' => false, 'mensagem' => ''];

$iEscola = db_getsession("DB_coddepto");
$iAnoUsu = db_getsession("DB_anousu");
if (isset($parametros->iEscola) && !empty($parametros->iEscola)) {
    $iEscola = $parametros->iEscola;
}
$iModuloEscola = 1100747;
try {
    switch ($parametros->acao) {
        case 'pesquisaEscola':
            $aFiltros = array();

            if (isset($parametros->iEscola) && !empty($parametros->iEscola)) {
                $aFiltros[] = "ed18_i_codigo in ($iEscola) ";
            }
            $sWhere = "";
            if (count($aFiltros) > 0) {
                $sWhere = implode(" and ", $aFiltros);
            }

            $oDaoEscola = new cl_escola();
            $sCamposEscola = "ed18_i_codigo as codigo_escola, ed18_c_nome as nome_escola";
            $sSqlEscola = $oDaoEscola->sql_query_file("", $sCamposEscola, "ed18_i_codigo", $sWhere);
            $rsResultEscola = $oDaoEscola->sql_record($sSqlEscola);

            $retorno->dados = db_utils::getCollectionByRecord($rsResultEscola, false, false, false);

            break;

        case 'getDisciplina':
            if ($parametros->iCodigoEscola != '' && $parametros->iCodigoEscola != 0) {
                $oTurma = TurmaRepository::getTurmasByAnoAtual($parametros->iCodigoEscola);
            } else {
                $oTurma = TurmaRepository::getTurmasByAnoAtual();
            }

            $aDisciplinas = array();

            foreach ($oTurma as $turma) {
                $regenciasTurma = $turma->getDisciplinas();

                foreach ($regenciasTurma as $oRegencia) {
                    $oDisciplina = new stdClass();
                    $oDisciplina->iRegencia = $oRegencia->getCodigo();
                    $oDisciplina->iCodigoCadDisciplina = $oRegencia->getDisciplina()->getCodigoDisciplinaGeral();
                    $oDisciplina->iCodigoDisciplina = $oRegencia->getDisciplina()->getCodigoDisciplina();
                    $oDisciplina->sDescricaoDisciplina = $oRegencia->getDisciplina()->getNomeDisciplina();

                    $aDisciplinas[$oDisciplina->iCodigoCadDisciplina] = $oDisciplina;
                }
            }

            /* Ordena Disciplinas por ordem alfabética */
            usort($aDisciplinas, function ($a, $b) {
                return strcmp($a->sDescricaoDisciplina, $b->sDescricaoDisciplina);
            });

            $retorno->aDisciplinas = $aDisciplinas;

            break;

        case 'getSerie':
            $aFiltros = [];

            if (isset($parametros->iCodigoEscola) && !empty($parametros->iCodigoEscola)) {
                $aFiltros[] = " ed18_i_codigo = ($parametros->iCodigoEscola) ";
            }

            if (isset($parametros->iCodigoDisciplina) && !empty($parametros->iCodigoDisciplina)) {
                $aFiltros[] = " ed232_i_codigo = ($parametros->iCodigoDisciplina) ";
            }

            $aFiltros[] = " ed52_i_ano = {$iAnoUsu}";

            $sWhere = implode(' and ', $aFiltros);
            $sCampos = " distinct ed11_i_codigo, ed11_c_descr, ed10_ordem, ed11_i_sequencia ";
            $sOrdem = " ed10_ordem, ed11_i_sequencia ";

            $oDaoSerie = new cl_serie();
            $sSqlSerie = $oDaoSerie->sql_query_disciplinaescolaserie('', $sCampos, $sOrdem, $sWhere);
            $rsSerie = $oDaoSerie->sql_record($sSqlSerie);

            if ($oDaoSerie->numrows > 0) {
                $retorno->dados = db_utils::getCollectionByRecord($rsSerie, false, false, false);
            } else {
                $retorno->erro = true;
                $retorno->message = "Não foi possível localizar as séries solicitadas.";
            }

            break;

        case 'getVinculos':
            $oDaoVinculo = new cl_tipohoratrabalho();
            $sSqlVinculo = $oDaoVinculo->sql_query(null, '*', null, null);
            $rsSqlVinculo = $oDaoVinculo->sql_record($sSqlVinculo);

            $retorno->dados = db_utils::getCollectionByRecord($rsSqlVinculo, false, false, false);

            break;

        case 'getEscolasByFiltro':
            $aFiltros = [];
            $aFiltrosUnion = [];

            if (isset($parametros->iCodigoTurno) && !empty($parametros->iCodigoTurno)) {
                $aCodigoTurno = " where subquery_turnos.referencia_turno in ($parametros->iCodigoTurno) ";
            }

            if (isset($parametros->iVinculo) && !empty($parametros->iVinculo)) {
                $aFiltros[] = " ed128_codigo = ($parametros->iVinculo) ";
            }

            if (isset($parametros->iEscola) && !empty($parametros->iEscola)) {
                $aFiltros[] = " ed18_i_codigo = ($iEscola) ";
                $aFiltrosUnion[] = " ed18_i_codigo = ($iEscola) ";
            }

            if (isset($parametros->iDisciplina) && !empty($parametros->iDisciplina)) {
                $aFiltros[] = " ed232_i_codigo = ($parametros->iDisciplina) ";
                $aFiltrosUnion[] = " ed232_i_codigo = ($parametros->iDisciplina) ";
            }

            if (isset($parametros->iEtapa) && !empty($parametros->iEtapa)) {
                $aFiltros[] = " ed11_i_codigo IN ($parametros->iEtapa) ";
                $aFiltrosUnion[] = " ed11_i_codigo IN ($parametros->iEtapa) ";
            }

            if (isset($parametros->iDia) && !empty($parametros->iDia)) {
                $aFiltros[] = " ed32_i_codigo = ($parametros->iDia) ";
                $aFiltrosUnion[] = " ed32_i_codigo = ($parametros->iDia) ";
            }

            if (isset($parametros->iPeriodo) && !empty($parametros->iPeriodo)) {
                $aFiltros[] = " ed08_i_codigo = ($parametros->iPeriodo) ";
                $aFiltrosUnion[] = " ed08_i_codigo = ($parametros->iPeriodo) ";
            }

            if (isset($parametros->iFuncionario)
                && !empty($parametros->iFuncionario)
                && $parametros->iFuncionario != 1) {
                $aFiltros[] = " rechumano.ed20_i_codigo = ($parametros->iFuncionario) ";
            }

            if ($parametros->iFuncionario == 1) {
                $aFiltros[] = " rechumano.ed20_i_codigo is null ";
            }

            $aFiltros[] = " ed52_i_ano = {$iAnoUsu} AND ed58_ativo = TRUE ";
            $aFiltrosUnion[] = " ed52_i_ano = {$iAnoUsu} AND ed175_ativo = TRUE ";
            $sWhere = implode(' and ', $aFiltros);
            $sWhereUnion = implode(' and ', $aFiltrosUnion);

            /* iFuncionario 1 = Sem Professor */
            if ($parametros->iFuncionario === '1') {
                $sSqlEscolas = "
                    SELECT
                        DISTINCT codigo_escola,
                        nome_escola
                    FROM
                        (
                            SELECT
                                DISTINCT ed18_i_codigo AS codigo_escola,
                                ed18_c_nome AS nome_escola,
                                (
                                    SELECT
                                        CASE
                                            WHEN array_length(contagem, 1) > 1 THEN 4
                                            ELSE contagem [1]
                                        END AS turnoreferente
                                    FROM
                                        (
                                            SELECT
                                                ed57_i_codigo,
                                                array_agg(ed336_turnoreferente) AS contagem
                                            FROM
                                                turmaturnoreferente
                                            WHERE
                                                turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
                                            GROUP BY
                                                ed57_i_codigo,
                                                ed57_c_descr
                                         ) AS turno_contagem
                                ) AS referencia_turno
                            FROM
                                regenciahorariodiscsemreg
                                INNER JOIN periodoescola ON periodoescola.ed17_i_codigo = regenciahorariodiscsemreg.ed175_periodo
                                INNER JOIN regencia ON regencia.ed59_i_codigo = regenciahorariodiscsemreg.ed175_regencia
                                INNER JOIN diasemana ON diasemana.ed32_i_codigo = regenciahorariodiscsemreg.ed175_diasemana
                                INNER JOIN escola ON escola.ed18_i_codigo = periodoescola.ed17_i_escola
                                INNER JOIN periodoaula ON periodoaula.ed08_i_codigo = periodoescola.ed17_i_periodoaula
                                INNER JOIN turno ON turno.ed15_i_codigo = periodoescola.ed17_i_turno
                                INNER JOIN turnoreferente ON turnoreferente.ed231_i_turno = turno.ed15_i_codigo
                                INNER JOIN disciplina ON disciplina.ed12_i_codigo = regencia.ed59_i_disciplina
                                INNER JOIN caddisciplina ON caddisciplina.ed232_i_codigo = disciplina.ed12_i_caddisciplina
                                INNER JOIN turma ON turma.ed57_i_codigo = regencia.ed59_i_turma
                                INNER JOIN calendario ON calendario.ed52_i_codigo = turma.ed57_i_calendario
                                INNER JOIN serie ON serie.ed11_i_codigo = regencia.ed59_i_serie
                                INNER JOIN ensino ON ensino.ed10_i_codigo = serie.ed11_i_ensino
                ";

                if (!empty($sWhereUnion)) {
                    $sSqlEscolas .= " WHERE {$sWhereUnion} ";
                }

                $sSqlEscolas .= " ) AS subquery_turnos {$aCodigoTurno} ";

                $rsEscolas = db_query($sSqlEscolas);

                if (!$rsEscolas) {
                    throw new Exception("Erro ao buscar escolas." . pg_last_error());
                }

                $retorno->dados = db_utils::getCollectionByRecord(
                    $rsEscolas,
                    false,
                    false,
                    false
                );
            } else {
                $sSqlEscolas = "
                    SELECT
                        DISTINCT codigo_escola,
                        nome_escola
                    FROM
                        (
                            SELECT
                                DISTINCT ed18_i_codigo AS codigo_escola,
                                ed18_c_nome AS nome_escola,
                                (
                                    SELECT
                                        CASE
                                            WHEN array_length(contagem, 1) > 1 THEN 4
                                            ELSE contagem [1]
                                        END AS turnoreferente
                                    FROM
                                        (
                                            SELECT
                                                ed57_i_codigo,
                                                array_agg(ed336_turnoreferente) AS contagem
                                            FROM
                                                turmaturnoreferente
                                            WHERE
                                                turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
                                            GROUP BY
                                                ed57_i_codigo,
                                                ed57_c_descr
                                        ) AS x
                                ) AS referencia_turno
                            FROM
                                regenciahorario
                                INNER JOIN periodoescola ON periodoescola.ed17_i_codigo = regenciahorario.ed58_i_periodo
                                INNER JOIN regencia ON regencia.ed59_i_codigo = regenciahorario.ed58_i_regencia
                                INNER JOIN diasemana ON diasemana.ed32_i_codigo = regenciahorario.ed58_i_diasemana
                                INNER JOIN escola ON escola.ed18_i_codigo = periodoescola.ed17_i_escola
                                INNER JOIN periodoaula ON periodoaula.ed08_i_codigo = periodoescola.ed17_i_periodoaula
                                INNER JOIN disciplina ON disciplina.ed12_i_codigo = regencia.ed59_i_disciplina
                                INNER JOIN caddisciplina ON caddisciplina.ed232_i_codigo = disciplina.ed12_i_caddisciplina
                                INNER JOIN turma ON turma.ed57_i_codigo = regencia.ed59_i_turma
                                INNER JOIN turno ON turno.ed15_i_codigo = turma.ed57_i_turno
                                INNER JOIN calendario ON calendario.ed52_i_codigo = turma.ed57_i_calendario
                                INNER JOIN serie ON serie.ed11_i_codigo = regencia.ed59_i_serie
                                INNER JOIN ensino ON ensino.ed10_i_codigo = serie.ed11_i_ensino
                                INNER JOIN rechumano ON rechumano.ed20_i_codigo = regenciahorario.ed58_i_rechumano
                                LEFT JOIN docenteausencia ON rechumano.ed20_i_codigo = docenteausencia.ed321_rechumano
                                LEFT JOIN docentesubstituto ON docenteausencia.ed321_sequencial = docentesubstituto.ed322_docenteausente
                                LEFT JOIN rechumano AS rechumano_substituto ON docentesubstituto.ed322_rechumano = rechumano_substituto.ed20_i_codigo
                                LEFT JOIN rechumanopessoal AS rechumanopessoal_substituto ON rechumanopessoal_substituto.ed284_i_rechumano = rechumano_substituto.ed20_i_codigo
                                LEFT JOIN rhpessoal AS rhpessoal_substituto ON rhpessoal_substituto.rh01_regist = rechumanopessoal_substituto.ed284_i_rhpessoal
                                LEFT JOIN cgm AS cgmrh_substituto ON cgmrh_substituto.z01_numcgm = rhpessoal_substituto.rh01_numcgm
                                LEFT JOIN rechumanocgm AS rechumanocgm_substituto ON rechumanocgm_substituto.ed285_i_rechumano = rechumano_substituto.ed20_i_codigo
                                LEFT JOIN cgm AS cgmcgm_substituto ON cgmcgm_substituto.z01_numcgm = rechumanocgm_substituto.ed285_i_cgm
                                INNER JOIN rechumanoescola ON rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo
                                INNER JOIN relacaotrabalho ON relacaotrabalho.ed23_i_rechumanoescola = rechumanoescola.ed75_i_codigo
                                AND disciplina.ed12_i_codigo = relacaotrabalho.ed23_i_disciplina
                                INNER JOIN tipohoratrabalho ON relacaotrabalho.ed23_tipohoratrabalho = tipohoratrabalho.ed128_codigo
                                LEFT JOIN rechumanopessoal ON rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
                                LEFT JOIN rhpessoal ON rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
                                LEFT JOIN cgm AS cgmrh ON cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
                                LEFT JOIN rechumanocgm ON rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
                                LEFT JOIN cgm AS cgmcgm ON cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm
                ";

                if (!empty($sWhere)) {
                    $sSqlEscolas .= " WHERE {$sWhere} ";
                }

                $sSqlEscolas .= " ) AS subquery_turnos {$aCodigoTurno} ";

                if ($parametros->iFuncionario === '0') {
                    $sSqlEscolas .= "
                        UNION
                        SELECT
                            DISTINCT codigo_escola,
                            nome_escola
                        FROM
                            (
                                SELECT
                                    DISTINCT ed18_i_codigo AS codigo_escola,
                                    ed18_c_nome AS nome_escola,
                                    (
                                        SELECT
                                            CASE
                                                WHEN array_length(contagem, 1) > 1 THEN 4
                                                ELSE contagem [1]
                                            END AS turnoreferente
                                        FROM
                                            (
                                                SELECT
                                                    ed57_i_codigo,
                                                    array_agg(ed336_turnoreferente) AS contagem
                                                FROM
                                                    turmaturnoreferente
                                                WHERE
                                                    turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
                                                GROUP BY
                                                    ed57_i_codigo,
                                                    ed57_c_descr
                                            ) AS x
                                    ) AS referencia_turno
                                FROM
                                    regenciahorariodiscsemreg
                                    INNER JOIN periodoescola ON periodoescola.ed17_i_codigo = regenciahorariodiscsemreg.ed175_periodo
                                    INNER JOIN regencia ON regencia.ed59_i_codigo = regenciahorariodiscsemreg.ed175_regencia
                                    INNER JOIN diasemana ON diasemana.ed32_i_codigo = regenciahorariodiscsemreg.ed175_diasemana
                                    INNER JOIN escola ON escola.ed18_i_codigo = periodoescola.ed17_i_escola
                                    INNER JOIN periodoaula ON periodoaula.ed08_i_codigo = periodoescola.ed17_i_periodoaula
                                    INNER JOIN disciplina ON disciplina.ed12_i_codigo = regencia.ed59_i_disciplina
                                    INNER JOIN caddisciplina ON caddisciplina.ed232_i_codigo = disciplina.ed12_i_caddisciplina
                                    INNER JOIN turma ON turma.ed57_i_codigo = regencia.ed59_i_turma
                                    INNER JOIN turno ON turno.ed15_i_codigo = turma.ed57_i_turno
                                    INNER JOIN calendario ON calendario.ed52_i_codigo = turma.ed57_i_calendario
                                    INNER JOIN serie ON serie.ed11_i_codigo = regencia.ed59_i_serie
                                    INNER JOIN ensino ON ensino.ed10_i_codigo = serie.ed11_i_ensino
                    ";

                    if (!empty($sWhereUnion)) {
                        $sSqlEscolas .= " WHERE {$sWhereUnion} ";
                    }

                    $sSqlEscolas .= " ) AS subquery_turnos {$aCodigoTurno} ";
                }

                $rsEscolas = db_query($sSqlEscolas);

                if (!$rsEscolas) {
                    throw new Exception("Erro ao buscar escolas." . pg_last_error());
                }


                $retorno->dados = db_utils::getCollectionByRecord($rsEscolas, false, false, false);
            }

            break;

        case 'getDiasSemana':
            $aFiltros = [];

            if (isset($parametros->iCodigoEscola) && !empty($parametros->iCodigoEscola)) {
                $aFiltros[] = "ed04_i_escola = {$parametros->iCodigoEscola}";
            }

            $aFiltros[] = "ed04_c_letivo = 'S'";

            $sWhere = implode(" and ", $aFiltros);
            $sCampos = 'distinct ed32_i_codigo as codigo_dia, ed32_c_descr as descricao_dia';
            $oDaoDiaSemana = new cl_diasemana();
            $sSqlDiaSemana = $oDaoDiaSemana->sql_query_letivo('', $sCampos, 'ed32_i_codigo', $sWhere);
            $rsSqlDiaSemana = $oDaoDiaSemana->sql_record($sSqlDiaSemana);

            $retorno->dados = db_utils::getCollectionByRecord($rsSqlDiaSemana, false, false, false);
            break;

        case 'getDadosQuadroGeralDeHorarios':
            $aFiltros = [];
            $aFiltrosUnion = [];
            $aTurmas = [];
            /* Flag para UNION com a tabela regenciahorariodiscsemreg (disciplina sem regente) */
            $lFazUnion = true;
            $codTurno = "";

            if (isset($parametros->iCodigoTurno) && !empty($parametros->iCodigoTurno)) {
                $codTurno = " where referencia_turno in ($parametros->iCodigoTurno) ";
            }

            if (isset($parametros->iEscola) && !empty($parametros->iEscola)) {
                $aFiltros[] = " ed18_i_codigo = ($iEscola) ";
                $aFiltrosUnion[] = " ed18_i_codigo = ($iEscola) ";
            }

            if (isset($parametros->iEtapa) && !empty($parametros->iEtapa)) {
                $aFiltros[] = " ed11_i_codigo IN ($parametros->iEtapa) ";
                $aFiltrosUnion[] = " ed11_i_codigo IN ($parametros->iEtapa) ";
            }

            if (isset($parametros->iDia) && !empty($parametros->iDia)) {
                $aFiltros[] = " ed32_i_codigo = ($parametros->iDia) ";
                $aFiltrosUnion[] = " ed32_i_codigo = ($parametros->iDia) ";
            }

            if (isset($parametros->iPeriodo) && !empty($parametros->iPeriodo)) {
                $aFiltros[] = " ed08_i_codigo = ($parametros->iPeriodo) ";
                $aFiltrosUnion[] = " ed08_i_codigo = ($parametros->iPeriodo) ";
            }

            $aFiltros[] = " ed58_ativo = true ";

            $aFiltros[] = " ed52_i_ano = {$iAnoUsu} ";
            $aFiltrosUnion[] = " ed52_i_ano = {$iAnoUsu} ";

            $aFiltrosUnion[] = " ed175_ativo = true ";

            $sWhere = implode(' and ', $aFiltros);
            $sWhereUnion = implode(' and ', $aFiltrosUnion);

            $sCampos = "
                DISTINCT ed57_i_codigo AS codigo_turma,
                ed57_c_descr AS descricao_turma,
                ed32_i_codigo AS codigo_diasemana,
                ed32_c_descr AS descr_semana,
                ed08_i_codigo AS codigo_periodo,
                ed08_c_descr AS descricao_periodo,
                ed15_i_codigo AS codigo_turno,
                ed15_c_nome AS descricao_turno,
                ed11_c_descr AS descricao_serie,
                ed18_i_codigo AS codigo_escola,
                ed18_c_nome AS descricao_escola,
                rechumano.ed20_i_codigo AS codigo_regente,
                ed128_codigo AS codigo_tipohora,
                ed128_descricao AS descricao_tipohora,
                ed128_abreviatura AS abreviatura_tipohora,
                CASE
                    WHEN rechumano.ed20_i_tiposervidor = 1 THEN cgmrh.z01_nome
                    ELSE cgmcgm.z01_nome
                END AS z01_nome,
                CASE
                    WHEN rechumano.ed20_i_tiposervidor = 1 THEN rechumanopessoal.ed284_i_rhpessoal
                    ELSE rechumanocgm.ed285_i_cgm
                END AS matricula,
                ed232_i_codigo AS codigo_disciplina,
                ed232_c_descr AS disciplina,
                CASE
                    WHEN rechumano.ed20_i_codigo = docenteausencia.ed321_rechumano
                    AND CURRENT_DATE BETWEEN ed321_inicio
                    AND COALESCE(ed321_final, CURRENT_DATE) THEN TRUE
                    ELSE FALSE
                END AS ausente_hoje,
                ed321_inicio AS ausencia_inicio,
                ed321_final AS ausencia_final,
                CASE
                    WHEN rechumano_substituto.ed20_i_tiposervidor = 1 THEN cgmrh_substituto.z01_nome
                    ELSE cgmcgm_substituto.z01_nome
                END AS substituto,
                docentesubstituto.ed322_periodoinicial AS substituto_inicio,
                docentesubstituto.ed322_periodofinal AS substituto_final,
                (
                    SELECT
                        CASE
                            WHEN array_length(contagem, 1) > 1 THEN 4
                            ELSE contagem [1]
                        END AS turnoreferente
                    FROM
                        (
                            SELECT
                                ed57_i_codigo,
                                array_agg(ed336_turnoreferente) AS contagem
                            FROM
                                turmaturnoreferente
                            WHERE
                                turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
                            GROUP BY
                                ed57_i_codigo,
                                ed57_c_descr
                        ) AS x
                ) AS referencia_turno
            ";

            $sCamposUnion = "
                DISTINCT ed57_i_codigo AS codigo_turma,
                ed57_c_descr AS descricao_turma,
                ed32_i_codigo AS codigo_diasemana,
                ed32_c_descr AS descr_semana,
                ed08_i_codigo AS codigo_periodo,
                ed08_c_descr AS descricao_periodo,
                ed15_i_codigo AS codigo_turno,
                ed15_c_nome AS descricao_turno,
                ed11_c_descr AS descricao_serie,
                ed18_i_codigo AS codigo_escola,
                ed18_c_nome AS descricao_escola,
                NULL :: BIGINT AS codigo_regente,
                NULL :: INTEGER AS codigo_tipohora,
                NULL AS descricao_tipohora,
                NULL AS abreviatura_tipohora,
                NULL AS z01_nome,
                NULL :: BIGINT AS matricula,
                ed232_i_codigo AS codigo_disciplina,
                ed232_c_descr AS disciplina,
                NULL :: BOOLEAN AS ausente,
                NULL :: DATE AS ausencia_inicio,
                NULL :: DATE AS ausencia_final,
                NULL AS substituto,
                NULL :: DATE AS substituto_inicio,
                NULL :: DATE AS substituto_final,
                (
                    SELECT
                        CASE
                            WHEN array_length(contagem, 1) > 1 THEN 4
                            ELSE contagem [1]
                        END AS turnoreferente
                    FROM
                        (
                            SELECT
                                ed57_i_codigo,
                                array_agg(ed336_turnoreferente) AS contagem
                            FROM
                                turmaturnoreferente
                            WHERE
                                turmaturnoreferente.ed336_turma = turma.ed57_i_codigo
                            GROUP BY
                                ed57_i_codigo,
                                ed57_c_descr
                        ) AS x
                ) AS referencia_turno
            ";

            $oDaoRegenciaHorario = new cl_regenciahorario();
            $sSqlRegenciaHorario = $oDaoRegenciaHorario->sql_query_quadrogeraldehorarios(
                '',
                $sCampos,
                $sCamposUnion,
                'codigo_diasemana, codigo_periodo, descricao_turma',
                $sWhere,
                $codTurno,
                $sWhereUnion,
                $codTurno,
                $lFazUnion
            );

            $rsSqlRegenciaHorario = $oDaoRegenciaHorario->sql_record($sSqlRegenciaHorario);
            $numRows = $oDaoRegenciaHorario->numrows;

            for ($i = 0; $i < $numRows; $i++) {
                $resultQuadro = db_utils::fieldsMemory($rsSqlRegenciaHorario, $i);

                $oValores = new stdClass();
                $oValores->iTurma = $resultQuadro->codigo_turma;
                $oValores->sTurma = $resultQuadro->descricao_turma;
                $oValores->sSerie = $resultQuadro->descricao_serie;
                $oValores->iDia = $resultQuadro->codigo_diasemana;
                $oValores->sDia = $resultQuadro->descr_semana;
                $oValores->iPeriodo = $resultQuadro->codigo_periodo;
                $oValores->sPeriodo = $resultQuadro->descricao_periodo;
                $oValores->iTurno = $resultQuadro->codigo_turno;
                $oValores->sTurno = $resultQuadro->descricao_turno;
                $oValores->iEscola = $resultQuadro->codigo_escola;
                $oValores->sEscola = $resultQuadro->descricao_escola;
                $oValores->iDisciplina = $resultQuadro->codigo_disciplina;
                $oValores->sDisciplina = $resultQuadro->disciplina;
                $oValores->iRegente = $resultQuadro->codigo_regente;
                $oValores->sRegente = $resultQuadro->z01_nome;
                $oValores->iMatricula = $resultQuadro->matricula;
                $oValores->iTipoHora = $resultQuadro->codigo_tipohora;
                $oValores->sTipoHora = $resultQuadro->abreviatura_tipohora;
                $oValores->lAusenteHoje = $resultQuadro->ausente_hoje;
                $oValores->dAusenciaInicio = empty($resultQuadro->ausencia_inicio)
                    ? '' : DBDate::converter($resultQuadro->ausencia_inicio);
                $oValores->dAusenciaFinal = empty($resultQuadro->ausencia_final)
                    ? '' : DBDate::converter($resultQuadro->ausencia_final);
                $oValores->sSubstituto = $resultQuadro->substituto;
                $oValores->dSubstitutoInicio = empty($resultQuadro->substituto_inicio)
                    ? '' : DBDate::converter($resultQuadro->substituto_inicio);
                $oValores->dSubstitutoFinal = empty($resultQuadro->substituto_final)
                    ? '' : DBDate::converter($resultQuadro->substituto_final);
                $aTurmas[] = $oValores;
            }

            $retorno->dados = $aTurmas;

            break;

        case 'getTurnos':
            $sWhere = "";

            if (isset($parametros->iCodigoEscola) && !empty($parametros->iCodigoEscola)) {
                $sWhere = " WHERE periodoescola.ed17_i_escola = {$parametros->iCodigoEscola} ";
            }

            $sSql = "
                WITH turnos AS (
                    SELECT
                        ed231_i_turno,
                        ARRAY_AGG(
                            DISTINCT CASE
                                WHEN turnoreferente.ed231_i_referencia = 1 THEN 'MANHA'
                                WHEN turnoreferente.ed231_i_referencia = 2 THEN 'TARDE'
                                WHEN turnoreferente.ed231_i_referencia = 3 THEN 'NOITE'
                            END
                        ) AS turno,
                        CASE
                            WHEN COUNT(DISTINCT turnoreferente.ed231_i_referencia) > 1 THEN 'INTEGRAL'
                        END AS integral
                    FROM
                        turno
                        JOIN turnoreferente ON turnoreferente.ed231_i_turno = turno.ed15_i_codigo";
            $sSql .= "  JOIN periodoescolaturnoreferente ON periodoescolaturnoreferente.ed143_turnoreferente = ";
            $sSql .= "  turnoreferente.ed231_i_codigo ";
            $sSql .= "  JOIN periodoescola ON periodoescola.ed17_i_codigo = ";
            $sSql .= "  periodoescolaturnoreferente.ed143_periodoescola ";
            $sSql .= "  JOIN escola ON escola.ed18_i_codigo = periodoescola.ed17_i_escola
                    $sWhere
                    GROUP BY
                        ed231_i_turno
                    ORDER BY
                        1
                ),
                turnos_distinct AS (
                    SELECT
                        DISTINCT CASE
                            WHEN integral = 'INTEGRAL' THEN 'INTEGRAL'
                            ELSE turnos.turno[1]
                        END AS descricao_turno
                    FROM
                        turnos
                )
                SELECT
                    CASE
                        WHEN turnos_distinct.descricao_turno = 'MANHA' THEN 1
                        WHEN turnos_distinct.descricao_turno = 'TARDE' THEN 2
                        WHEN turnos_distinct.descricao_turno = 'NOITE' THEN 3
                        WHEN turnos_distinct.descricao_turno = 'INTEGRAL' THEN 4
                    END AS id,
                    turnos_distinct.descricao_turno
                FROM
                    turnos_distinct
                ORDER BY
                    1
            ";

            $oDaoTurnos = new cl_turno();
            $rsTurnos = $oDaoTurnos->sql_record($sSql);

            if ($oDaoTurnos->numrows > 0) {
                $retorno->dados = db_utils::getCollectionByRecord($rsTurnos, false, false, false);
            } else {
                $retorno->erro = true;
                $retorno->message = "Não foi possível localizar os turnos solicitadas.";
            }

            break;

        case 'getHorarios':
            $aFiltros = [];

            if (isset($parametros->iCodigoTurno) && !empty($parametros->iCodigoTurno)) {
                $aFiltros[] = " ed231_i_referencia in ({$parametros->iCodigoTurno}) ";
            }

            if (isset($parametros->iCodigoEscola) && !empty($parametros->iCodigoEscola)) {
                $aFiltros[] = " ed17_i_escola = ($parametros->iCodigoEscola) ";
            }

            $oPeriodosEscola = PeriodoEscolaRepository::getPeriodosPorEscolaETurnoReferencia($aFiltros);

            $aPeriodos = [];
            foreach ($oPeriodosEscola as $periodo) {
                $oPeriodo = new stdClass();
                $oPeriodo->iCodigoPeriodo = $periodo->getPeriodoAula();
                $oPeriodo->sDescricaoPeriodo = $periodo->getDescricao();
                $oPeriodo->sHoraInicio = $periodo->getHoraInicio();
                $oPeriodo->sHoraFim = $periodo->getHoraFim();

                $aPeriodos[$oPeriodo->sDescricaoPeriodo] = $oPeriodo;
            }

            $aPeriodos = array_values($aPeriodos);

            $retorno->aPeriodos = $aPeriodos;

            break;

        case 'buscaFuncionarios':
            $aFiltros = [];

            if (isset($parametros->iCodigoEscola) && !empty($parametros->iCodigoEscola)) {
                $aFiltros[] = " ed57_i_escola = {$parametros->iCodigoEscola} ";
            }

            if (isset($parametros->iCodigoDisciplina) && !empty($parametros->iCodigoDisciplina)) {
                $aFiltros[] = " ed232_i_codigo = {$parametros->iCodigoDisciplina} ";
            }

            if (isset($parametros->iCodigoEtapa) && !empty($parametros->iCodigoEtapa) && $parametros->iCodigoEtapa!= "null") {
                $aFiltros[] = " ed11_i_codigo IN ({$parametros->iCodigoEtapa}) ";
            }

            if (isset($parametros->iCodigoDia) && !empty($parametros->iCodigoDia)) {
                $aFiltros[] = " ed32_i_codigo = {$parametros->iCodigoDia} ";
            }

            if (isset($parametros->iCodigoTurno) && !empty($parametros->iCodigoTurno)  && $parametros->iCodigoTurno!= "null") {
                $aFiltros[] = " ed231_i_referencia in ({$parametros->iCodigoTurno}) ";
            }

            if (isset($parametros->iCodigoPeriodo) && !empty($parametros->iCodigoPeriodo)) {
                $aFiltros[] = " ed08_i_codigo = {$parametros->iCodigoPeriodo} ";
            }

            $aFiltros[] = " ed58_ativo is true and ed52_i_ano = {$iAnoUsu}";

            $sWhere = implode(" and ", $aFiltros);

            $sCampos = "
                DISTINCT ed20_i_codigo,
                CASE
                    WHEN cgmrh.z01_numcgm IS NULL THEN cgmcgm.z01_numcgm
                    ELSE cgmrh.z01_numcgm
                END AS z01_numcgm,
                CASE
                    WHEN cgmrh.z01_nome IS NULL THEN cgmcgm.z01_nome
                    ELSE cgmrh.z01_nome
                END AS z01_nome,
                CASE
                    WHEN ed20_i_tiposervidor = 1 THEN rechumanopessoal.ed284_i_rhpessoal
                    ELSE rechumanocgm.ed285_i_cgm
                END AS matricula
            ";

            $sOrder = " z01_nome ";

            $oDaoRegenciaHorario = new cl_regenciahorario();
            $sSqlRegenciaHorario = $oDaoRegenciaHorario->sql_query_turnoreferente(null, $sCampos, $sOrder, $sWhere);
            $rsRegenciaHorario = $oDaoRegenciaHorario->sql_record($sSqlRegenciaHorario);

            $retorno->dados = db_utils::getCollectionByRecord($rsRegenciaHorario, false, false, false);

            break;
    }
} catch (Exception $erro) {
    $retorno->message = $erro->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);
echo JSON::create()->stringify($retorno);
