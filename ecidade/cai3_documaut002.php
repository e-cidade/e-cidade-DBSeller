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

include(modification("fpdf151/pdf.php"));
include(modification("libs/db_sql.php"));
include(modification("classes/db_corrente_classe.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$clcorrente = new cl_corrente;
$clrotulo   = new rotulocampo;
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("z01_cgccpf");
$clrotulo->label("z01_ender");
$clrotulo->label("z01_numero");
$clrotulo->label("z01_munic");
$clrotulo->label("pc63_banco");
$clrotulo->label("pc63_agencia");

$dbwhere = "where 1=1 and k12_instit = " . db_getsession("DB_instit");
$data    = str_replace("/", "-", $lista_data);

$vet_data =explode(",", $data);
$virgula  = "";
$data     = "";
for ($i = 0; $i < sizeof($vet_data); $i++){
    $datas =explode("-",$vet_data[$i]);
    $data .= $virgula . "'" . trim($datas[2]) . "-" . trim($datas[1]) . "-" . trim($datas[0]) . "'";
    $virgula = ", ";
}

$vet_estorn =explode(",", $lista_estorn);
$virgula = "";
$estorno = "";
for ($i=0; $i < sizeof($vet_estorn); $i++){
    $estorno .= $virgula."'".trim($vet_estorn[$i])."'";
    $virgula  = ", ";
}

$lista_estorn = $estorno;

if (isset($lista_nfs)&&trim($lista_nfs)!=""){
     $vet_nfs =explode(",",$lista_nfs);
     $virgula = "";
     $notas   = "";
     for($i=0; $i < sizeof($vet_nfs); $i++){
          $notas   .= $virgula."'".trim($vet_nfs[$i])."'";
          $virgula  = ", ";
     }

     $lista_nfs = $notas;
}

$dbwhere .= " and cgmempenho.z01_numcgm in ($lista_cgm)";
$dbwhere .= " and corrente.k12_data     in ($data)";
$dbwhere .= " and coremp.k12_empen      in ($lista_empen)";
$dbwhere .= " and corrente.k12_estorn   in ($lista_estorn)";
$dbwhere .= " and coremp.k12_codord     in ($lista_ordens)";
$dbwhere .= " and corrente.k12_conta    in ($lista_contas)";
$dbwhere .= " and corrente.k12_valor    in ($lista_valores)";

$db_where_deb = $dbwhere;

if ($cancelados == 'f'){
   $dbwhere .= " and e90_cancelado = false";
}

$sql      = "select distinct
                    cgmempenho.z01_numcgm,
                    cgmempenho.z01_nome,
                    cgmempenho.z01_cgccpf,
                    cgmempenho.z01_ender,
                    cgmempenho.z01_numero,
                    cgmempenho.z01_munic,
                    cgmpagordem.z01_numcgm as numcgmpagordem,
                    cgmpagordem.z01_nome   as nomepagordem,
                    pc63_banco,
                    pc63_agencia,
                    pc63_agencia_dig,
                    pc63_conta,
                    pc63_conta_dig,
                    corrente.k12_data,
                    corrente.k12_conta,
                    conplano.c60_descr,
                    db89_db_bancos,
                    db89_codagencia,
                    db89_digito,
                    db83_conta,
                    db83_dvconta,
                    corrente.k12_valor,
                    empempenho.e60_codemp,
                    conlancamcompl.c72_complem,
                    empempenho.e60_anousu,
                    coremp.k12_cheque,
                    coremp.k12_codord,
                    empnota.e69_numero,
                    e81_codmov,
                    coalesce(e150_numeroprocesso,'') as e150_numeroprocesso,
                    case when corrente.k12_estorn is false then
                        case
                            when empageforma.e96_descr = 'DIN' and coremp.k12_cheque = 0 then 'DINHEIRO'
                            when empageforma.e96_descr = 'CHE' or  coremp.k12_cheque > 0 then 'CHEQUE'
                            when empageforma.e96_descr = 'TRA' then 'TRANSMISSAO'
                        end
                    else
                          'ESTORNO'
                    end as e96_descr,
                    empageconfgera.e90_codgera,
                    empageconfgera.e90_cancelado
             from corrente
                  inner join coremp         on coremp.k12_id              = corrente.k12_id   and
                                               coremp.k12_data            = corrente.k12_data and
                                               coremp.k12_autent          = corrente.k12_autent

                  inner join empempenho     on empempenho.e60_numemp      = coremp.k12_empen

                  inner join empempaut      on empempaut.e61_numemp       = coremp.k12_empen

                  inner join cgm cgmempenho on cgmempenho.z01_numcgm      = empempenho.e60_numcgm

                  inner join conplanoreduz  on conplanoreduz.c61_reduz    = corrente.k12_conta and
                                               conplanoreduz.c61_anousu   = ".db_getsession("DB_anousu")."

                  inner join conplano       on conplano.c60_codcon        = conplanoreduz.c61_codcon and
                                               conplano.c60_anousu        = conplanoreduz.c61_anousu

                  inner join pagordem       on pagordem.e50_numemp        = coremp.k12_empen and
                                               pagordem.e50_codord        = coremp.k12_codord

                  inner join empord        on empord.e82_codord           = coremp.k12_codord
                   left join pagordemconta  on empord.e82_codord          = pagordemconta.e49_codord
                   left join cgm cgmpagordem on pagordemconta.e49_numcgm  = cgmpagordem.z01_numcgm
                  left  join empautorizaprocesso on e150_empautoriza      = empempaut.e61_autori

                  left join conplanocontabancaria on conplanocontabancaria.c56_codcon = conplanoreduz.c61_codcon
                                                 and conplanocontabancaria.c56_anousu = conplanoreduz.c61_anousu
                                                 and conplanocontabancaria.c56_reduz  = conplanoreduz.c61_reduz

                  left join contabancaria on contabancaria.db83_sequencial = conplanocontabancaria.c56_contabancaria

                  left join bancoagencia on bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia


                  left  join pagordemnota   on pagordemnota.e71_codord    = pagordem.e50_codord

                  left  join corempagemov   on corempagemov.k12_id        = corrente.k12_id   and
                                               corempagemov.k12_data      = corrente.k12_data and
                                               corempagemov.k12_autent    = corrente.k12_autent

                  left  join empagemov      on empagemov.e81_codmov       = corempagemov.k12_codmov

                  left  join empageconfgera on empageconfgera.e90_codmov  = empagemov.e81_codmov

                  left  join empagemovconta on empagemovconta.e98_codmov  = empagemov.e81_codmov

                  left  join pcfornecon     on pcfornecon.pc63_numcgm     = cgmempenho.z01_numcgm and
                                               pcfornecon.pc63_contabanco = empagemovconta.e98_contabanco

                  left  join empnota        on empnota.e69_numemp         = coremp.k12_empen and
                                               empnota.e69_codnota        = pagordemnota.e71_codnota

                  left  join empagemovforma on empagemovforma.e97_codmov  = empagemov.e81_codmov

                  left  join empageforma    on empageforma.e96_codigo     = empagemovforma.e97_codforma

                  left join corgrupocorrente on  corgrupocorrente.k105_data   = corrente.k12_data
                                             and corgrupocorrente.k105_autent = corrente.k12_autent
                                             and corgrupocorrente.k105_id     = corrente.k12_id
                  left join conlancamcorgrupocorrente on c23_corgrupocorrente = corgrupocorrente.k105_sequencial
                  left join conlancamcompl on c72_codlan = c23_conlancam

                  ".$dbwhere."
             order by cgmempenho.z01_numcgm, corrente.k12_data desc";

//echo $sql; exit;

$resultado = $clcorrente->sql_record($sql);

//db_criatabela($resultado);

if( pg_num_rows($resultado) == 0 ){

   $sql      = "select distinct
                       cgmempenho.z01_numcgm,
                       cgmempenho.z01_nome,
                       cgmempenho.z01_cgccpf,
                       cgmempenho.z01_ender,
                       cgmempenho.z01_numero,
                       cgmempenho.z01_munic,
                       cgmpagordem.z01_numcgm as numcgmpagordem,
                       cgmpagordem.z01_nome   as nomepagordem,
                       pc63_banco,
                       pc63_agencia,
                       pc63_agencia_dig,
                       pc63_conta,
                       pc63_conta_dig,
                       corrente.k12_data,
                       corrente.k12_conta,
                       conplano.c60_descr,
                       db89_db_bancos,
                       db89_codagencia,
                       db89_digito,
                       db83_conta,
                       db83_dvconta,
                       corrente.k12_valor,
                       empempenho.e60_codemp,
                       empempenho.e60_anousu,
                       coremp.k12_cheque,
                       coremp.k12_codord,
                       empnota.e69_numero,
                       e81_codmov,
                       coalesce(e150_numeroprocesso,'') as e150_numeroprocesso,
                       case when corrente.k12_estorn is false then
                            case
                                when empageforma.e96_descr = 'DIN' and coremp.k12_cheque = 0 then 'DINHEIRO'
                                when empageforma.e96_descr = 'CHE' or  coremp.k12_cheque > 0 then 'CHEQUE'
                                when empageforma.e96_descr = 'TRA' then 'TRANSMISSAO'
                                when empageforma.e96_descr = 'DEB' then 'DEBITO'
                            end
                       else
                            'ESTORNO'
                       end as e96_descr,
                       empageconfgera.e90_codgera,
                       empageconfgera.e90_cancelado
                from corrente
                     inner join coremp         on coremp.k12_id              = corrente.k12_id   and
                                                  coremp.k12_data            = corrente.k12_data and
                                                  coremp.k12_autent          = corrente.k12_autent

                     inner join empempenho     on empempenho.e60_numemp      = coremp.k12_empen

                     inner join empempaut      on empempaut.e61_numemp       = coremp.k12_empen

                     inner join cgm cgmempenho           on cgmempenho.z01_numcgm             = empempenho.e60_numcgm

                     inner join conplanoreduz  on conplanoreduz.c61_reduz    = corrente.k12_conta and
                                                  conplanoreduz.c61_anousu   = ".db_getsession("DB_anousu")."

                     inner join conplano       on conplano.c60_codcon        = conplanoreduz.c61_codcon and
                                                  conplano.c60_anousu        = conplanoreduz.c61_anousu

                     inner join pagordem       on pagordem.e50_numemp        = coremp.k12_empen and
                                                  pagordem.e50_codord        = coremp.k12_codord

                     inner join empord         on empord.e82_codord          = coremp.k12_codord
                     left  join pagordemconta  on empord.e82_codord          = pagordemconta.e49_codord
                     left  join cgm cgmpagordem on pagordemconta.e49_numcgm  = cgmpagordem.z01_numcgm
                     left  join empautorizaprocesso on e150_empautoriza      = empempaut.e61_autori

                     left  join conplanocontabancaria on conplanocontabancaria.c56_codcon = conplanoreduz.c61_codcon
                                                     and conplanocontabancaria.c56_anousu = conplanoreduz.c61_anousu
                                                     and conplanocontabancaria.c56_reduz  = conplanoreduz.c61_reduz

                     left join contabancaria on contabancaria.db83_sequencial = conplanocontabancaria.c56_contabancaria

                     left join bancoagencia on bancoagencia.db89_sequencial  = contabancaria.db83_bancoagencia

                     left  join pagordemnota   on pagordemnota.e71_codord    = pagordem.e50_codord

                     left  join corempagemov   on corempagemov.k12_id        = corrente.k12_id   and
                                                  corempagemov.k12_data      = corrente.k12_data and
                                                  corempagemov.k12_autent    = corrente.k12_autent

                     left  join empagemov      on empagemov.e81_codmov       = corempagemov.k12_codmov

                     left  join empageconfgera on empageconfgera.e90_codmov  = empagemov.e81_codmov

                     left  join empagemovconta on empagemovconta.e98_codmov  = empagemov.e81_codmov

                     left  join pcfornecon     on pcfornecon.pc63_numcgm     = cgmempenho.z01_numcgm and
                                                  pcfornecon.pc63_contabanco = empagemovconta.e98_contabanco

                     left  join empnota        on empnota.e69_numemp         = coremp.k12_empen and
                                                  empnota.e69_codnota        = pagordemnota.e71_codnota

                     left  join empagemovforma on empagemovforma.e97_codmov  = empagemov.e81_codmov

                     left  join empageforma    on empageforma.e96_codigo     = empagemovforma.e97_codforma ".$db_where_deb."
                order by cgmempenho.z01_numcgm, corrente.k12_data desc";

   $resultado = $clcorrente->sql_record($sql);

}

	$head3 = "DEMONSTRATIVO DE PAGAMENTO A FORNECEDOR";
	if(isset($periodo)&&trim($periodo)!=""){
	  $head4 = "Periodo a ser demonstrado de ".$periodo;
	}

	$pdf = new PDF();
	$pdf->Open();
	$pdf->AliasNbPages();
	$pdf->setfillcolor(235);
	$pdf->setfont('arial', 'b', 8);
	$alt        = 6;
	$p          = 0;
	$numcgm_ant = "";

	for($x=0; $x < $clcorrente->numrows; $x++){
	     db_fieldsmemory($resultado,$x);

	     if($numcgm_ant != $z01_numcgm){
	       $pdf->AddPage();
     		 $pdf->setfont('arial', 'b', 8);
          if($x==0){
             $pdf->cell(30, $alt, "Documento impresso em:     ".date("d/m/Y",db_getsession("DB_datausu")), 0, 1, "L", 0);
          }
          $pdf->cell(15, $alt, "Dados do credor:", 0, 1, "L", 0);
          $pdf->cell(15, $alt, $RLz01_numcgm.": ",      0, 0, "L", 0);
          $pdf->setfont('arial', '', 8);
          $pdf->cell(15, $alt, $z01_numcgm,        0, 0, "L", 0);
          $pdf->setfont('arial', 'b', 8);
          $pdf->cell(70, $alt, $RLz01_cgccpf.": ",      0, 0, "R", 0);
          $pdf->setfont('arial', '', 8);
          if(strlen($z01_cgccpf) < 14){
             $cgccpf = db_formatar($z01_cgccpf,"cpf");
          } else {
             $cgccpf = db_formatar($z01_cgccpf,"cnpj");
          }
          $pdf->cell(30, $alt, $cgccpf, 0, 1, "R", 0);
          $pdf->setfont('arial', 'b', 8);
          $pdf->cell(15, $alt, $RLz01_nome.": ", 0, 0, "L", 0);
          $pdf->setfont('arial', '', 8);
          $pdf->cell(60, $alt, $z01_nome, 0, 1, "L", 0);
          $pdf->setfont('arial', 'b', 8);
          $pdf->cell(15, $alt, $RLz01_ender.": ", 0, 0, "L", 0);
          $pdf->setfont('arial', '', 8);
          $pdf->cell(52, $alt, substr($z01_ender,0,50), 0, 0, "L", 0);
          $pdf->setfont('arial', 'b', 8);
          $pdf->cell(30, $alt, $RLz01_numero.": ", 0, 0, "R", 0);
          $pdf->setfont('arial', '', 8);
          $pdf->cell(15, $alt, $z01_numero, 0, 1, "R", 0);
          $pdf->setfont('arial', 'b', 8);
          $pdf->cell(15, $alt, $RLz01_munic.": ", 0, 0, "L", 0);
          $pdf->setfont('arial', '', 8);
          $pdf->cell(45, $alt, $z01_munic,  0, 1, "L", 0);
          $pdf->setfont('arial', 'b', 8);
          $pdf->cell(15, $alt, "Banco: ",   0, 0, "L", 0);
          $pdf->setfont('arial', '', 8);
          $pdf->cell(15, $alt, $pc63_banco, 0, 0, "L", 0);
          $pdf->setfont('arial', 'b', 8);
          $pdf->cell(15, $alt, "Agencia: ", 0, 0, "L", 0);
          $pdf->setfont('arial', '', 8);
          $pdf->cell(15, $alt, $pc63_agencia."-".$pc63_agencia_dig, 0, 0, "L", 0);
          $pdf->setfont('arial', 'b', 8);
          $pdf->cell(15, $alt, "Conta: ", 0, 0, "L", 0);
          $pdf->setfont('arial', '', 8);
          $pdf->cell(15, $alt, $pc63_conta."-".$pc63_conta_dig, 0, 1, "L", 0);
          $pdf->cell(195, ($alt-3), "", "T", 1, "R", 0);

          $numcgm_ant = $z01_numcgm;
          $p          = 0;
       }

	     $sBcoAgencConta = '';
	     if ($db89_db_bancos <> ''){
          $sBcoAgencConta = $db89_db_bancos .' / '. $db89_codagencia .'-'. $db89_digito .' / '. $db83_conta .'-'. $db83_dvconta;
	     }

       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(195, $alt, "Dados do Empenho pago:",     0, 1, "L", $p);
       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(20, $alt, "Ordem pgto.: ",               0, 0, "L", $p);
       $pdf->setfont('arial', '', 8);
       $pdf->cell(15, $alt, $k12_codord,                   0, 0, "L", $p);
       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(15, $alt, "Empenho: ",                   0, 0, "L", $p);
       $pdf->setfont('arial', '', 8);
       $pdf->cell(20, $alt, $e60_codemp .'/'. $e60_anousu, 0, 0, "L", $p);
       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(10, $alt, "Nota: ",                      0, 0, "L", $p);
       $pdf->setfont('arial', '', 8);
       $pdf->cell(15, $alt, $e69_numero,                   0, 0, "L", $p);
       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(25, $alt, "Valor pago R$: ",             0, 0, "L", $p);
       $pdf->setfont('arial', '', 8);
       $pdf->cell(40, $alt, db_formatar($k12_valor,"f"),   0, 0, "L", $p);
       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(10, $alt, "Data: ",                      0, 0, "L", $p);
       $pdf->setfont('arial', '', 8);
       $pdf->cell(25, $alt, db_formatar($k12_data,"d"),   0, 1, "L", $p);

	     if($numcgmpagordem != null) {

	       $pdf->setfont('arial', 'b', 8);
	       $pdf->cell(10, $alt, "Nome: ",                      0, 0, "L", $p);
	       $pdf->setfont('arial', '', 8);
	       $pdf->cell(100, $alt, $numcgmpagordem." - ".$nomepagordem,   0, 1, "L", $p);
	     }
       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(25, $alt, "Conta pagadora: ",            0, 0, "L", $p);
       $pdf->setfont('arial', '', 8);
       $pdf->cell(170, $alt, $k12_conta.' - '.$c60_descr,  0, 1, "L", $p);
       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(35, $alt, "Banco / Agência / Conta: ",   0, 0, "L", $p);
       $pdf->setfont('arial', '', 8);
       $pdf->cell(35, $alt, $sBcoAgencConta,               0, 0, "L", $p);
       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(35, $alt, "Processo Administrativo: ",   0, 0, "L", $p);
       $pdf->setfont('arial', '', 8);
       $pdf->cell(90, $alt, $e150_numeroprocesso,          0, 1, "L", $p);
       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(15, $alt, "Forma: ",                     0, 0, "L", $p);
       $pdf->setfont('arial', '', 8);
       $pdf->cell(82, $alt, $e96_descr,                    0, 0, "L", $p);

	     if ($e96_descr == "CHEQUE"){
          $pdf->setfont('arial', 'b', 8);
		      $pdf->cell(15, $alt, "Cheque: ",               0, 0, "L", $p);
          $pdf->setfont('arial', '', 8);
		      $pdf->cell(15, $alt, $k12_cheque,              0, 0, "L", $p);

          $tam = 68;
	     } else {
          $tam = 37;
	     }

	     if ($e90_codgera > 0 && $e96_descr == "TRANSMISSAO"){
          $sDescrCodArq = $e90_codgera;

          if ($e90_cancelado == 't'){
             $sDescrCodArq .= " - Cancelado";
          }

          $pdf->setfont('arial', 'b', 8);
          $pdf->cell(20, $alt, "Codigo arq.: ", 0, 0, "L", $p);
          $pdf->setfont('arial', '', 8);
          $pdf->cell(30, $alt, $sDescrCodArq, 0, 0, "L", $p);

          $tam = 48;
	     }

	     $pdf->cell($tam, $alt, "", 0, 1, "L", $p);

	     // Busca dados de boletos do movimento
	     $cl_empagedadosretornodetalhe = new cl_empagedadosretornodetalhe();
	     $sSql = "select e140_valor,
                             e140_numeroautenticacao,
                             substr(e140_linhaarquivo, 97, 44) as codbarras
                      from empagedadosretornodetalhe
		      where e140_codmov = {$e81_codmov}";

	     $resultadoboletos = $cl_empagedadosretornodetalhe->sql_record($sSql);

	     if($cl_empagedadosretornodetalhe->numrows > 0) {

	      $pdf->setfont('arial', 'b', 8);
	      $pdf->cell(100, $alt, "Dados Boleto(s): ",                     0, 1, "L", $p);
	     }

	     for($i=0; $i < $cl_empagedadosretornodetalhe->numrows; $i++) {

	       db_fieldsmemory($resultadoboletos,$i);
	       $pdf->setfont('arial', 'b', 8);
	       $pdf->cell(30, $alt, "Cod. Autenticação: ",         0, 0, "L", $p);
	       $pdf->setfont('arial', '', 8);
	       $pdf->cell(40, $alt, $e140_numeroautenticacao,      0, 0, "L", $p);

	       $pdf->setfont('arial', 'b', 8);
	       $pdf->cell(10, $alt, "Valor: ",                     0, 0, "L", $p);
	       $pdf->setfont('arial', '', 8);
	       $pdf->cell(20, $alt, db_formatar($e140_valor,"f"),  0, 0, "R", $p);

	       $pdf->setfont('arial', 'b', 8);
	       $pdf->cell(20, $alt, "Cod. Barras: ",               0, 0, "L", $p);
	       $pdf->setfont('arial', '', 8);
	       $pdf->cell(150, $alt, $codbarras,                   0, 1, "L", $p);

	     }

	     if ($cl_empagedadosretornodetalhe->numrows > 0) {
          $pdf->cell(195, ($alt-3), "", "T", 1, "R", $p);
	     }

       $pdf->setfont('arial', 'b', 8);
       $pdf->cell(15, $alt, "Histórico: ", 0, 0, "L", $p);
       $pdf->setfont('arial', '', 8);
       $pdf->MultiCell(82, $alt, $c72_complem, 0, 'L');
       $pdf->cell($pdf->getAvailWidth(), 3, '', 'T', 1);
       $p = 0;
	}

$pdf->Output();
?>
