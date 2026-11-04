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
  require_once(modification("libs/db_conecta.php"));
  require_once(modification("libs/db_sessoes.php"));
  require_once(modification("libs/db_usuariosonline.php"));
  require_once(modification("classes/db_issbase_classe.php"));
  require_once(modification("classes/db_isscadsimples_classe.php"));
  require_once(modification("classes/db_histocorrenciainscr_classe.php"));
  require_once(modification("classes/db_db_config_classe.php"));
  require_once(modification("libs/db_utils.php"));
  include(modification("dbforms/db_funcoes.php"));

  $tipo = null;
  $data_ocorrencia_inicio_ano = '';
  $data_ocorrencia_inicio_mes = '';
  $data_ocorrencia_inicio_dia = '';
  $data_ocorrencia_final_ano  = '';
  $data_ocorrencia_final_mes  = '';
  $data_ocorrencia_final_dia  = '';

?>
<html>
<head>
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
  db_postmemory($_GET,0);

  $cldb_config = new cl_db_config;
  $iInstitSessao = db_getsession('DB_instit');
  $rs_db_config = $cldb_config->sql_record($cldb_config->sql_query_file($iInstitSessao, "cgc, db21_codcli"));
  $oDBConfig = db_utils::fieldsMemory($rs_db_config, 0, null);

	$pesquisaLocalizada = false;

  if ($solicitacao == "Atividades") {
	  $sql = " select q07_ativ as dl_Cod, q07_val_ativ_int, q07_imprimealvara as dl_Imprime_Alvará, q03_descr, q03_atmemo, q12_descr as dl_Classe, rh70_estrutural, q71_estrutural, db121_estrutural as dl_Codigo116,
	           q07_datain, q07_datafi, q07_databx, q07_quant as dl_QTD, case when q88_inscr is null then 'S'::char(1) else 'P'::char(1) end as q88_tipo,
               q07_horaini, q07_horafim, q11_processo as dl_Processo,
               case when q11_oficio = 'true' then 'OFICIO' when q11_oficio = 'false' then 'NORMAL' else '' end as q11_oficio ";

    if ($oDBConfig->db21_codcli == 19985) {
      $sql .= ", q07_processo, q07_dtprocesso";
    }

    $sql .= "
		  	      from tabativ
                   left  join clasativ      on q07_ativ                       = q82_ativ
                   left  join classe        on q82_classe                     = q12_classe
                   inner join ativid        on q07_ativ                       = q03_ativ
                   left  join ativprinc     on ativprinc.q88_inscr            = tabativ.q07_inscr and ativprinc.q88_seq = tabativ.q07_seq
                   left  join tabativbaixa  on tabativ.q07_inscr              = tabativbaixa.q11_inscr and tabativ.q07_seq = tabativbaixa.q11_seq
                   left  join atividcbo     on atividcbo.q75_ativid           = ativid.q03_ativ
                   left  join rhcbo         on rhcbo.rh70_sequencial          = atividcbo.q75_rhcbo
                   left  join atividcnae    on atividcnae.q74_ativid          = ativid.q03_ativ
                   left  join cnaeanalitica on cnaeanalitica.q72_sequencial   = atividcnae.q74_cnaeanalitica
                   left  join cnae          on cnae.q71_sequencial            = cnaeanalitica.q72_cnae
                   left  join issgruposervicoativid on issgruposervicoativid.q127_ativid = ativid.q03_ativ
                   left  join issqn.issgruposervico on issgruposervico.q126_sequencial = issgruposervicoativid.q127_issgruposerviso
                   left  join configuracoes.db_estruturavalor on db_estruturavalor.db121_sequencial = issgruposervico.q126_db_estruturavalor
                   left  join configuracoes.db_estrutura on db_estruturavalor.db121_db_estrutura = db_estrutura.db77_codestrut
              where q07_inscr = $inscricao ";
	  $pesquisaLocalizada = true;
  } else if ($solicitacao == "Socios") {
     $clissbase = new cl_issbase;
     if ($oDBConfig->db21_codcli == 19985) {
       $sql = $clissbase->sqlinscricoes_socios($inscricao,0,"cgmsocio.z01_numcgm#cgmsocio.z01_cgccpf,cgmsocio.z01_nome#cgmsocio.z01_ender#cgmsocio.z01_numero#cgmsocio.z01_compl#cgmsocio.z01_bairro#cgmsocio.z01_munic#cgmsocio.z01_uf#q95_perc#q95_datainc", ' inner ', null);
     } else {
       $sql = $clissbase->sqlinscricoes_socios($inscricao,0,"cgmsocio.z01_numcgm#cgmsocio.z01_cgccpf,cgmsocio.z01_nome#cgmsocio.z01_ender#cgmsocio.z01_numero#cgmsocio.z01_compl#cgmsocio.z01_bairro#cgmsocio.z01_munic#cgmsocio.z01_uf#q95_perc", ' inner ', null);
     }
	   $pesquisaLocalizada = true;

  } else if ($solicitacao == "Simples") {
     $clisscadsimples = new cl_isscadsimples;
     $sql = $clisscadsimples->sql_query_baixa(null,"q38_sequencial, q38_dtinicial, q38_categoria, case when q38_categoria = 1 then 'Micro Empresa' when q38_categoria = 2 then 'Empresa de pequeno porte' when q38_categoria = 3 then 'MEI' when q38_categoria = 4 then 'EIRELI' when q38_categoria = 5 then 'Soc. Profissionais' end as categoria, q39_dtbaixa, q39_issmotivobaixa, q39_obs",null," q38_inscr = $inscricao");
	   $pesquisaLocalizada = true;

  } else if ($solicitacao == "Calculo###") {
    echo "Calculo ainda não implementado";
  	$sql = "";
  	$pesquisaLocalizada = true;

  } else if ($solicitacao == "TiposDeCalculo") {
   	$sql = "
          select distinct q81_codigo, q81_abrev,q81_qiexe,q81_qfexe,q81_valexe
            from tabativ
           left  join tabativbaixa on q11_inscr   = q07_inscr
                                  and q11_seq     = q07_seq
           inner join ativtipo     on q80_ativ    = q07_ativ
           inner join tipcalc      on q80_tipcal  = q81_codigo
           inner join cadcalc      on q81_cadcalc = q85_codigo
	  where (q11_inscr is null and q11_seq is null) and
                q07_inscr = $inscricao

         union all

         select distinct q81_codigo, q81_abrev,q81_qiexe,q81_qfexe,q81_valexe
            from issbase
               left join issbaseporte on q02_inscr      = q45_inscr
               left join tabativ      on q02_inscr      = q07_inscr and
                                         q02_dtbaix     is null
               left join clasativ     on q07_ativ       = q82_ativ
               left join issportetipo on q41_codporte   = q45_codporte and
                                         q41_codclasse  = q82_classe
               left join tipcalc      on q41_codtipcalc = q81_codigo
            where q02_inscr = $inscricao ";
  	$pesquisaLocalizada = true;

  } else if ($solicitacao == "Quantidades") {
     $sql = " select 
                  q30_anousu, 
                  q30_quant,
                  q30_area,
                  q30_mult,
                  q30_tempofuncionamento,
                  q30_areapublicidade,
                  case
                    when q30_graurisco = 'A' then 'ALTO'
                    when q30_graurisco = 'M' then 'MÉDIO'
                    when q30_graurisco = 'B' then 'BAIXO'
                    else ''
                  end as q30_graurisco
                from issquant
               where q30_inscr = $inscricao ";
     $pesquisaLocalizada = true;

  } else if ($solicitacao == "Fixado") {
     $sql = " select * from varfix where q33_inscr = $inscricao	";
     $pesquisaLocalizada = true;

  } else if ($solicitacao == "Observacoes") {
     	$sql = " select q02_obs
           		  from issbase
          		  where q02_inscr = $inscricao	";

	$pesquisaLocalizada = true;

  } else if ($solicitacao == "TextoAlvara") {
	  $sql = " select q02_memo
         		  from issbase
       		  where q02_inscr = $inscricao	";

	$pesquisaLocalizada = true;

  } else if ($solicitacao == "Ocorrencias") {

    $campos = "case when histocorrencia.ar23_tipo = '2' then 'Manual' else 'Automatica' end as Tipo, ";
    $campos .= "histocorrencia.ar23_descricao, ";
    $campos .= "histocorrencia.ar23_ocorrencia, ";
    $campos .= "histocorrencia.ar23_data, ";
    $campos .= "histocorrencia.ar23_hora, ";
    $campos .= "db_usuarios.login, ";
    $campos .= "db_modulos.nome_modulo ";
    $clhistocorrenciainscr = new cl_histocorrenciainscr;

    $where = [
      "histocorrenciainscr.ar26_inscr = " . $inscricao,
      "ar23_instit = " . db_getsession("DB_instit")
    ];

    if (!empty($tipo) and is_numeric($tipo)) {
			$where[] = "histocorrencia.ar23_tipo = {$tipo}";
		}

		if (!empty($data_ocorrencia_inicio) and !empty($data_ocorrencia_final)) {
      $data_ocorrencia_inicio = implode('-', [
        $data_ocorrencia_inicio_dia,
        $data_ocorrencia_inicio_mes,
        $data_ocorrencia_inicio_ano,
      ]);

      $data_ocorrencia_final = implode('-', [
        $data_ocorrencia_final_dia,              
        $data_ocorrencia_final_mes,
        $data_ocorrencia_final_ano
      ]);

			$where[] = "(histocorrencia.ar23_data BETWEEN '{$data_ocorrencia_inicio}' and '{$data_ocorrencia_final}')";

      $data_ocorrencia_inicio = date_format(date_create($data_ocorrencia_inicio), 'd/m/Y');
      $data_ocorrencia_final   = date_format(date_create($data_ocorrencia_final), 'd/m/Y');
		} elseif (!empty($data_ocorrencia_inicio)) {
      $data_ocorrencia_inicio = implode('-', [
        $data_ocorrencia_inicio_dia,
        $data_ocorrencia_inicio_mes,
        $data_ocorrencia_inicio_ano,
      ]);

			$where[] = "histocorrencia.ar23_data >= '{$data_ocorrencia_inicio}'";

      $data_ocorrencia_inicio = date_format(date_create($data_ocorrencia_inicio), 'd/m/Y');
		} elseif (!empty($data_ocorrencia_final)) {
      $data_ocorrencia_final = implode('-', [
        $data_ocorrencia_final_dia,              
        $data_ocorrencia_final_mes,
        $data_ocorrencia_final_ano
      ]);

			$where[] = "histocorrencia.ar23_data <= '{$data_ocorrencia_final}'";

      $data_ocorrencia_final = date_format(date_create($data_ocorrencia_final), 'd/m/Y');
		}

    $sql = $clhistocorrenciainscr->sql_query("", "$campos", "histocorrencia.ar23_data", implode(' and ', $where));
	  $pesquisaLocalizada = true;

  }else if($solicitacao == "SalaoParceiro"){
  $sql = "select 
    ROW_NUMBER() OVER (ORDER BY ibsp.q202_dtinicial DESC) AS q202_sequencial,
    ibsp.q202_dtinicial,
    CASE 
        WHEN ibsp.q202_dtfinal IS NULL THEN '' 
        ELSE TO_CHAR(ibsp.q202_dtfinal, 'DD/MM/YYYY') 
    END AS q202_dtfinal,
    ibsp.q202_obs,
    i.q203_numcgm,
    i.q203_inscrparc,
    i.q203_dtinicial,
    CASE 
        WHEN i.q203_dtfinal IS NULL THEN '' 
        ELSE TO_CHAR(i.q203_dtfinal, 'DD/MM/YYYY') 
    END AS q203_dtfinal,
    i.q203_obs 
FROM 
    issqn.issbasesalaoparceiro ibsp
INNER JOIN 
    issqn.issbaseprofissionalparceiro i 
ON 
    i.q203_inscrsalao = ibsp.q202_inscr 
WHERE 
    ibsp.q202_inscr = {$inscricao}";
  $pesquisaLocalizada = true;
$result = db_query($sql) or die($sql);

  }else if ($solicitacao == "Manual") {
	$sql = " select q01_manual
             from isscalc
      		  where q01_inscr = $inscricao and q01_anousu = " . db_getsession("DB_anousu") . "
        	  limit 1 ";
    $result = db_query($sql);

    if(pg_num_rows($result) > 0){
      db_fieldsmemory($result,0);
    }
?>

  <tr align="center">
    <td>
   	 <textarea class="db_area" rows="13" cols="90"><?=@$q01_manual?></textarea>
    </td>
  </tr>

<?php
  } else if ($solicitacao == "Paralisacoes") {

      $sql = "select
                q140_datainicio as dl_Data_Inicial,
                q140_datafim as dl_Data_Final,
                q140_usuario as dl_Usuário,
                q141_descricao as dl_Motivo,
                q140_observacao as dl_Observação
              from
                issbaseparalisacao
              inner join issmotivoparalisacao on
                q141_sequencial = q140_issmotivoparalisacao
              where q140_issbase = $inscricao";
      $pesquisaLocalizada = true;

    } else if ($solicitacao == "Historico_MEI") {

      $sql = "SELECT distinct on (q113_data, q113_hora, q101_codigo)
                     q104_nomearq,
                     q113_data,
                     q113_hora,
                     q101_codigo,
                     q101_descricao,
                     (select defdescr
                        from db_syscampodef
                  inner join db_syscampo
                          on db_syscampodef.codcam   = db_syscampo.codcam
                       where db_syscampo.nomecam     = 'q112_tipoprocessa'
                         and db_syscampodef.defcampo = q112_tipoprocessa::varchar
                       limit 1
                     ) as q112_tipoprocessa,
                     db_usuarios.nome

              FROM meiimportameireg
              INNER JOIN meievento ON q111_meievento = q101_sequencial
              INNER JOIN meiimportamei ON q111_meiimportamei = q105_sequencial
              INNER JOIN meiimporta ON q105_meiimporta = q104_sequencial
              INNER JOIN meiimportameiregempresa ON q111_meiimportameiregempresa = q107_sequencial
              AND q105_cnpj = q107_cnpj
              INNER JOIN meiprocessareg ON q112_meiimportameireg = q111_sequencial
              INNER JOIN meiprocessa ON q112_meiprocessa = q113_sequencial
              INNER JOIN cgm ON q105_cnpj = z01_cgccpf
              INNER JOIN ISSBASE ON q02_numcgm = z01_numcgm
              INNER JOIN db_usuarios ON meiprocessa.q113_id_usuario = db_usuarios.id_usuario
              WHERE q02_inscr = $inscricao
              ORDER BY q113_data, q113_hora, q101_codigo ";

      $pesquisaLocalizada = true;

  } else if ($solicitacao == "Baixa") {
    $sql = "SELECT  q11_inscr, q11_oficio, q11_oficio, q11_obs, q11_data,
                          (pt.p58_numero || '/' || pt.p58_ano) AS numerodoprocesso,
                           us.nome,
                           array_to_string(array_agg(distinct tba.q07_ativ), ',') as atividades
            FROM issbase AS iss
            JOIN tabativbaixa AS tbab ON iss.q02_inscr = tbab.q11_inscr
            LEFT JOIN protprocesso pt ON pt.p58_codproc = tbab.q11_processo
            LEFT JOIN db_usuarios us ON us.id_usuario = tbab.q11_login
            LEFT JOIN tabativ AS tba ON tba.q07_inscr = tbab.q11_inscr
                AND tba.q07_seq = tbab.q11_seq
            WHERE q11_inscr = $inscricao
            group by 1,  2, 3, 4, 5, 6, 7;";
    $result = db_query($sql);
    $iLinhas = pg_num_rows($result);

    if($iLinhas > 0) {

      echo "<table>";
      for ($i = 0; $i < $iLinhas; $i++) {

        db_fieldsmemory($result, $i);

        $tipo = "Normal";
        if ($q11_oficio=='t'){
          $tipo = "Oficio";
        }
        $data = date("d/m/Y", strtotime($q11_data));
        echo "<tr>";
        echo "  <td class='bold' nowrap='nowrap'>Processo da Baixa: </td>";
        echo "  <td style='background-color:#FFF' class='field-size5'>{$numerodoprocesso}</td>";
        echo "  <td class='bold'>Tipo:</td>";
        echo "  <td style='background-color:#FFF;'>{$tipo}</td>";
        echo "</tr>";
        echo "<tr>";
        echo "  <td class='bold'>Observao:</td>";
        echo "  <td style='background-color:#FFF' colspan='3'>{$q11_obs}</td>";
        echo "</tr>";
        echo "<tr>";
        echo "  <td class='bold' nowrap='nowrap'>Usuario Responsavel: </td>";
        echo "  <td style='background-color:#FFF' class='field-size5'>{$nome}</td>";
        echo "  <td class='bold'>Data da Baixa: </td>";
        echo "  <td style='background-color:#FFF;' class='field-size5'>{$data}</td>";
        echo "</tr>";
        echo "<tr>";
        echo "  <td class='bold'>Cod. Atividade: </td>";
        echo "  <td style='background-color:#FFF;'>{$atividades}</td>";
        echo "</tr>";


      }
      echo "</table>";
      db_fieldsmemory($result,0);
    }

  } else if ($solicitacao == 'OptanteSimples') {

  	/*
	 * SQL que busca as informações sobre a opção de simples da inscrição
	 */
    $sql  = "SELECT isscadsimples.q38_sequencial,                                                                          ";
    $sql .= "       isscadsimples.q38_dtinicial,                                                                           ";
  	$sql .= "       CASE                                                                                                   ";
  	$sql .= "         WHEN isscadsimples.q38_categoria = 1 THEN 'Micro Empresa'                                            ";
   	$sql .= "		      WHEN isscadsimples.q38_categoria = 2 THEN 'Empresa de pequeno porte'                                 ";
  	$sql .= "		      WHEN isscadsimples.q38_categoria = 3 THEN 'MEI'                                                      ";
    $sql .= "		      WHEN isscadsimples.q38_categoria = 4 THEN 'EIRELI'                                                   ";
    $sql .= "		      WHEN isscadsimples.q38_categoria = 5 THEN 'Soc. Profissionais'                                       ";
  	$sql .= "       END AS q38_categoria,                                                                                  ";
    $sql .= "       isscadsimplesbaixa.q39_dtbaixa,                                                                        ";
    $sql .= "       issmotivobaixa.q42_descr,                                                                              ";
    $sql .= "       isscadsimplesbaixa.q39_obs                                                                             ";
    $sql .= "  FROM isscadsimples                                                                                          ";
    $sql .= "       LEFT JOIN isscadsimplesbaixa ON isscadsimples.q38_sequencial  = isscadsimplesbaixa.q39_isscadsimples   ";
    $sql .= "       LEFT JOIN issmotivobaixa     ON issmotivobaixa.q42_sequencial = isscadsimplesbaixa.q39_issmotivobaixa  ";
    $sql .= " WHERE isscadsimples.q38_inscr = {$inscricao}                                                                 ";
	  $pesquisaLocalizada = true;

  	/*
  	 * analiza se a inscrição informada é optante do simples ou não
  	 */
  	$result = db_query($sql) or die($sql);
    if (pg_num_rows($result) == 0) {
      echo '<p style="text-align:center; margin-top:40px;">Sem lançamentos de optante do simples para essa inscrição</p>';
    }
  } else if ($solicitacao == 'Caracteristicas') {

  	/*
	 * SQL que busca as informações de caracteristicas da inscrição
	 */
    $sql  = "select db139_sequencial,                            ";
    $sql .= "       db139_descricao,                             ";
    $sql .= "       db140_sequencial,                            ";
    $sql .= "       db140_descricao                              ";
    $sql .= "  from issbasecaracteristica                        ";
    $sql .= " inner join caracteristica                          ";
    $sql .= "    on q138_caracteristica = db140_sequencial       ";
    $sql .= " inner join grupocaracteristica                     ";
    $sql .= "    on db140_grupocaracteristica = db139_sequencial ";
    $sql .= " where q138_inscr = {$inscricao}                    ";
    $sql .= " order by db139_sequencial,db140_sequencial         ";

	  $pesquisaLocalizada = true;

  	$result = db_query($sql) or die($sql);
    if (pg_num_rows($result) == 0) {
      echo '<p style="text-align:center; margin-top:40px;">Sem lançamentos de caracteristicas para essa inscrição</p>';
    }

  }else if ($solicitacao == 'HistoricoRisco') {
    $sql = "select
              ROW_NUMBER() OVER (ORDER BY q201_dtrisco DESC) AS q201_sequencial,
              case 
              	when q201_risco = 'B' then 'BAIXO'
              	when q201_risco = 'M' then 'MÉDIO'
              	when q201_risco = 'A' then 'ALTO'
              	else q201_risco
              end as q201_risco,
              q201_dtrisco
            FROM
              issqn.histriscoinscr h
            WHERE
              h.q201_inscr = {$inscricao}
            ORDER BY
              q201_dtrisco DESC";
    $pesquisaLocalizada = true;
    $result = db_query($sql) or die($sql);
  }

  if ($pesquisaLocalizada) {
    $result = db_query($sql) or die($sql);

    if (pg_num_rows($result) == 0) {
      echo "<br><center><b>Sem registros a exibir!</b></center>";
    } else { 
      if ($solicitacao == "Ocorrencias"){
      ?>
        <form action="" method="GET">
            <input type="hidden" name="solicitacao" value="Ocorrencias">
            <input type="hidden" name="inscricao" value="<?= $inscricao ?>">
            <table style="margin-bottom: 0.5em">
                <tr>
                    <td>Tipo:</td>
                    <td>
                        <select value="<?= $tipo ?>" name="tipo">
                            <option value="">Todos</option>
                            <option <?= ($tipo == 1) ? 'selected' : '' ?> value="1">Automática</option>
                            <option <?= ($tipo == 2) ? 'selected' : '' ?> value="2">Manual</option>
                        </select>
                    </td>
                    <td>Data da ocorrência:</td>
                    <td style="position: relative;">
                        <?= 
                            db_inputdata(
                                "data_ocorrencia_inicio",
                                $data_ocorrencia_inicio_dia,
                                $data_ocorrencia_inicio_mes,
                                $data_ocorrencia_inicio_ano,
                                false,
                                'text',
                                1
                            ) 
                        ?>
                    </td>
                    <td> até </td>
                    <td>
                        <?= 
                            db_inputdata(
                                "data_ocorrencia_final",
                                $data_ocorrencia_final_dia,
                                $data_ocorrencia_final_mes,
                                $data_ocorrencia_final_ano,
                                false,
                                'text',
                                1
                            )
                        ?>    
                    </td>
                    <td>
                    <input type="submit" value="Pesquisar">
                    </td>
                </tr>
            </table>
        </form>
    <?php
      }
      $tipoLabel = null;

      if ($tipo == 1) {
        $tipoLabel = 'Autom&aacute;tica';
      } elseif ($tipo == 2) {
        $tipo = 'Manual';
      } else {
        $tipoLabel = 'Todos';
      }

      db_lovrot(
        $sql,
        15,
        "()",
        "",
        "",
        "",
        "NoMe",
        array(),
        true,
        array(),
        'V',
        null,
        [
          '<b>Inscri&ccedil;&atilde;o:</b> ' . $inscricao,
          '<b>Tipo: </b> ' . $tipoLabel,
          '<b>Data da ocorr&ecirc;ncia</b>: de ' . 
            $data_ocorrencia_inicio . 
            ' at&eacute; ' . $data_ocorrencia_final
        ]
      );
    }
  }

?>
<script>
function mensagem(mensagem) {
  let iHeight = 350;
  let iWidth  = 950;

  let windowDetalhes = new windowAux('wndDetalhes',
      'Ocorrencia',
      iWidth,
      iHeight
  );

  const detalhes = document.createElement('div');
  detalhes.style = 'padding: 5px;';
    
  const paragrafo = document.createElement('p');
    
  paragrafo.style = 'padding-right: 10px; padding-left: 10px; padding-bottom: 10px; background-color: white';
  paragrafo.innerHTML = mensagem;
  detalhes.appendChild(paragrafo);
  windowDetalhes.setContent(detalhes);
  windowDetalhes.show(null, null, true);
  windowDetalhes.setShutDownFunction(function() {
    windowDetalhes.destroy();
  });
}
</script>
</body>

</html>
