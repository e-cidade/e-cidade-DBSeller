<?php
//fis4_fis_levantlotearq.RPC.php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_layouttxt.php"));
require_once(modification("std/DBString.php"));
require_once(modification("std/DBNumber.php"));

$oJson              = new Services_JSON();
$oParametro         = $oJson->decode(str_replace("\\","",$_POST["json"]));
$oRetorno           = new stdClass();
$oRetorno->status   = 1;
$oRetorno->message  = '';
$oRetorno->erro    = false;

try {
    switch($oParametro->exec){

        case 'getDadosLote':

            $codlote = $oParametro->iLote;
            $aDadosContLote = array();

            $sSqlDadosContLote = "SELECT *
                                    FROM
                                      (SELECT y125_levantlotearq AS lotearq,
                                              y125_codauto AS codigo,
                                              'AUTO' AS tplote,
                                              q02_inscr AS inscr,
                                              z01_nome,
                                              3 AS tipopeca,
                                              y41_codtipo,
                                              y41_descr
                                       FROM fis_levantlotearqauto
                                       INNER JOIN fiscalizacao.fis_autoinscr ON y52_codauto = y125_codauto
                                       INNER JOIN issbase ON y52_inscr = q02_inscr
                                       INNER JOIN cgm ON q02_numcgm = z01_numcgm
                                       LEFT JOIN fiscalizacao.fis_autoultandam ON y125_codauto = y16_codauto
                                       LEFT JOIN fiscalizacao.fis_fandam ON y16_codandam = y39_codandam
                                       LEFT JOIN fiscalizacao.fis_tipoandam ON y39_codtipo = y41_codtipo
                                       UNION ALL SELECT y126_levantlotearq,
                                                        y126_codlanc,
                                                        'NOTIF',
                                                        q02_inscr,
                                                        z01_nome,
                                                        6,
                                                        y41_codtipo,
                                                        y41_descr
                                       FROM fis_levantlotearqlanc
                                       INNER JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = y126_codlanc
                                       INNER JOIN issbase ON nl04_inscr = q02_inscr
                                       INNER JOIN cgm ON q02_numcgm = z01_numcgm
                                       LEFT JOIN fiscalizacao.fis_lancultandam ON y126_codlanc = nl20_codlanc
                                       LEFT JOIN fiscalizacao.fis_fandam ON nl20_codandam = y39_codandam
                                       LEFT JOIN fiscalizacao.fis_tipoandam ON y39_codtipo = y41_codtipo) AS x
                                    WHERE lotearq = ".$codlote."
                                    ORDER BY codigo";

            $rsContLote = pg_exec($sSqlDadosContLote);

            for($abc = 0; $abc < pg_num_rows($rsContLote); $abc++){
                db_fieldsmemory($rsContLote,$abc);
                $oDadosLote = new StdClass();
                $oDadosLote->codigo         = $codigo;
                $oDadosLote->tplote         = $tplote;
                $oDadosLote->inscr          = $inscr;
                $oDadosLote->z01_nome       = $z01_nome;
                $oDadosLote->tipopeca       = $tipopeca;
                $oDadosLote->tipoandam      = $y41_codtipo;
                $oDadosLote->descandam      = urlencode($y41_descr);

                $aDadosContLote[] = $oDadosLote;
            }

            $oRetorno->itenslote = $aDadosContLote;

            break;

            case 'getDadosLoteImpressao':

            $codlote = $oParametro->iLote;
            $aDadosContLote = array();

            $sSqlDadosContLote = "SELECT *
                                    FROM
                                      (SELECT y125_levantlotearq AS lotearq,
                                              y125_codauto AS codigo,
                                              'AUTO' AS tplote,
                                              q02_inscr AS inscr,
                                              z01_nome,
                                              3 AS tipopeca,
                                              y41_codtipo,
                                              y41_descr,
                                              y125_procfiscal as procfiscal,
                                              y125_levanta as levanta,
                                              to_char(y125_dataemissao,'dd/mm/YYYY') as sdataemissao
                                       FROM fis_levantlotearqauto
                                       INNER JOIN fiscalizacao.fis_autoinscr ON y52_codauto = y125_codauto
                                       INNER JOIN issbase ON y52_inscr = q02_inscr
                                       INNER JOIN cgm ON q02_numcgm = z01_numcgm
                                       LEFT JOIN fiscalizacao.fis_autoultandam ON y125_codauto = y16_codauto
                                       LEFT JOIN fiscalizacao.fis_fandam ON y16_codandam = y39_codandam
                                       LEFT JOIN fiscalizacao.fis_tipoandam ON y39_codtipo = y41_codtipo
                                       UNION ALL SELECT y126_levantlotearq,
                                                        y126_codlanc,
                                                        'NOTIF',
                                                        q02_inscr,
                                                        z01_nome,
                                                        6,
                                                        y41_codtipo,
                                                        y41_descr,
                                                        y126_procfiscal,
                                                        y126_levanta,
                                                        to_char(y126_dataemissao,'dd/mm/YYYY')
                                       FROM fis_levantlotearqlanc
                                       INNER JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = y126_codlanc
                                       INNER JOIN issbase ON nl04_inscr = q02_inscr
                                       INNER JOIN cgm ON q02_numcgm = z01_numcgm
                                       LEFT JOIN fiscalizacao.fis_lancultandam ON y126_codlanc = nl20_codlanc
                                       LEFT JOIN fiscalizacao.fis_fandam ON nl20_codandam = y39_codandam
                                       LEFT JOIN fiscalizacao.fis_tipoandam ON y39_codtipo = y41_codtipo) AS x
                                    WHERE lotearq = ".$codlote."
                                    ORDER BY codigo";

            $rsContLote = pg_exec($sSqlDadosContLote);

            for($abc = 0; $abc < pg_num_rows($rsContLote); $abc++){
                db_fieldsmemory($rsContLote,$abc);
                $oDadosLote = new StdClass();
                $oDadosLote->codigo         = $codigo;
                $oDadosLote->tplote         = $tplote;
                $oDadosLote->inscr          = $inscr;
                $oDadosLote->z01_nome       = $z01_nome;
                $oDadosLote->tipopeca       = $tipopeca;
                $oDadosLote->procfiscal     = $procfiscal;
                $oDadosLote->levanta        = $levanta;
                $oDadosLote->tipoandam      = $y41_codtipo;
                $oDadosLote->descandam      = urlencode($y41_descr);
                $oDadosLote->dataemissao    = $sdataemissao;

                $aDadosContLote[] = $oDadosLote;
            }

            $oRetorno->itenslote = $aDadosContLote;

            break;

            case 'getVencimentoLote':

            $dtCiencia = $oParametro->iData;
            $sSqlDtVencimento = "select fc_proximo_dia_util(cast('".$dtCiencia."' as date)) as dataciencia";
            $rsDtVencimento = pg_exec($sSqlDtVencimento);

            db_fieldsmemory($rsDtVencimento, 0);
            $dData = explode("-", $dataciencia);

            $oDadosDiaUtil = new StdClass();
            $oDadosDiaUtil->dia = $dData[2];
            $oDadosDiaUtil->mes = $dData[1];
            $oDadosDiaUtil->ano = $dData[0];
            $oRetorno->proximodiautil = $oDadosDiaUtil;

            break;
    }
} catch (Exception $oErro) {
  db_fim_transacao(true);
  $oRetorno->status  = 2;
  $oRetorno->message = urlencode($oErro->getMessage());
  $oRetorno->erro    = true;
}
echo $oJson->encode($oRetorno);
?>
