<?php
require_once modification("fpdf151/scpdf.php");
require_once modification("fpdf151/impcarne.php");
require_once modification("classes/db_saltes_classe.php");

$clsaltes = new cl_saltes;

parse_str(base64_decode($_SERVER['QUERY_STRING']));

$motivo = "";
// Dados

if (USE_PCASP) {

    $sql = "select k152_sequencial,
                 upper(k152_descricao) as k152_descricao,
                 slip.k17_codigo,
                 slip.k17_data,
                 slip.k17_debito,
                 slip.k17_credito,
                 slip.k17_valor,
                 slip.k17_hist,
                 case when c72_complem <> '' then c72_complem else slip.k17_texto end as k17_texto,
                 slip.k17_dtaut,
                 slip.k17_autent,
                 slip.k17_instit,
                 slip.k17_dtanu,
                 slip.k17_situacao,
                 slip.k17_tipopagamento,
                 slip.k17_dtestorno,
                 slip.k17_motivoestorno,
                 slip.k17_idusuario,
                 z01_numcgm ,
                 z01_nome ,
                 c50_codhist as db_hist,
                 c50_descr   as descr_hist,
                 k18_motivo,
                 k153_slipoperacaotipo as tipo_operacao,
                 coalesce(k18_codigo,0)  as k18_codigo,
                 reduz_debito.c61_codigo as recurso_debito,
                 reduz_credito.c61_codigo as recurso_credito,
                 case when
                   k153_slipoperacaotipo not in (1, 2, 9, 10, 13, 14)
                     then
                       case when
                         k153_slipoperacaotipo in (5, 6, 17, 18)
                           then saltes_debito.k13_descr || ' - ' || conta_debito.c60_estrut
                         else conta_debito.c60_descr || ' - ' || conta_debito.c60_estrut end
                   else conta_debito.c60_descr || ' - ' || conta_debito.c60_estrut end as descr_debito,
                 case when
                   k153_slipoperacaotipo in (1, 2, 5, 6, 9, 10, 13, 14, 17, 18)
                     then saltes_credito.k13_descr || ' - ' || conta_credito.c60_estrut
                   else conta_credito.c60_descr || ' - ' || conta_credito.c60_estrut end as descr_credito,
		 k150_instituicao || ' - ' || db_config.nomeinstabrev as k150_instituicao,
         e151_codigo, e151_descricao
            from slip
                 left join sliptipooperacaovinculo         on sliptipooperacaovinculo.k153_slip = slip.k17_codigo
                 left join sliptipooperacao                on sliptipooperacaovinculo.k153_slipoperacaotipo = sliptipooperacao.k152_sequencial
                 left join slipanul                        on slip.k17_codigo          = slipanul.k18_codigo
                 left join slipnum                         on slip.k17_codigo          = slipnum.k17_codigo
                 left join cgm                             on slipnum.k17_numcgm       = cgm.z01_numcgm
                 left join conhist                         on slip.k17_hist             = conhist.c50_codhist

                 left join conplanoreduz as reduz_debito   on slip.k17_debito          = reduz_debito.c61_reduz
                                                          and reduz_debito.c61_instit  = ".db_getsession('DB_instit')."
                                                          and reduz_debito.c61_anousu  = ".db_getsession("DB_anousu")."
                 left join conplano as conta_debito        on reduz_debito.c61_codcon  = conta_debito.c60_codcon
                                                          and conta_debito.c60_anousu  = ".db_getsession("DB_anousu")."
                 left join saltes saltes_debito            on slip.k17_debito          = saltes_debito.k13_reduz
                 left join conplanoreduz as reduz_credito  on slip.k17_credito           = reduz_credito.c61_reduz
                                                          and reduz_credito.c61_instit  = ".db_getsession('DB_instit')."
                                                          and reduz_credito.c61_anousu  = ".db_getsession("DB_anousu")."
                 left join conplano as conta_credito       on reduz_credito.c61_codcon  = conta_credito.c60_codcon
                                                          and conta_credito.c60_anousu  = ".db_getsession("DB_anousu")."
                 left join saltes saltes_credito           on slip.k17_credito          = saltes_credito.k13_reduz
		         left join transferenciafinanceira on k150_slip = slip.k17_codigo
    		     left join db_config on k150_instituicao = codigo
    		     left join conlancamslip on c84_slip = slip.k17_codigo
                 left join conlancamcompl on c72_codlan = c84_conlancam
                 left join slipfinalidadepagamentofundeb on e153_slip = slip.k17_codigo
                 left join finalidadepagamentofundeb on e151_sequencial = slipfinalidadepagamentofundeb.e153_finalidadepagamentofundeb
           where slip.k17_codigo in ( $numslip ) and k17_instit = ".db_getsession('DB_instit') . " order by slip.k17_codigo";

} else {

    $sql = "select slip.*,
          z01_numcgm ,
          z01_nome ,
          c60_descr as descr_debito,
          p2.k13_descr as descr_credito,
          c50_codhist as db_hist,
          c50_descr as descr_hist,
          k18_motivo,
          coalesce(k18_codigo,0) as k18_codigo,
          e151_codigo, e151_descricao
          from slip
          left outer join slipanul    on slip.k17_codigo = slipanul.k18_codigo
          left outer join slipnum     on slip.k17_codigo = slipnum.k17_codigo
          left outer join cgm     on slipnum.k17_numcgm = cgm.z01_numcgm
          left outer join conplanoreduz   on slip.k17_debito = c61_reduz and
          c61_instit     = ".db_getsession('DB_instit')." and
          c61_anousu = ".db_getsession("DB_anousu")."
          left outer join conplano  on c61_codcon = c60_codcon and
          c60_anousu = ".db_getsession("DB_anousu")."
          left outer join saltes p2   on slip.k17_credito = p2.k13_reduz
          left outer join conhist     on slip.k17_hist = conhist.c50_codhist
          left join slipfinalidadepagamentofundeb on e153_slip = slip.k17_codigo
          left join finalidadepagamentofundeb on e151_sequencial = slipfinalidadepagamentofundeb.e153_finalidadepagamentofundeb
          where slip.k17_codigo in ( $numslip ) and k17_instit = ".db_getsession('DB_instit');
}

try {

    $dados = db_query($sql);
    $sEvento = "";

    if (pg_numrows($dados) > 0){

        $aMotivo = db_fieldsMemory($dados,0);
        $motivo  = $k18_motivo;
        if (USE_PCASP) {

            $sEvento = $k152_sequencial . " - " . $k152_descricao;
        }
    }


    // seleciona os recursos envolvidos, ligados a conta recebedora do slip
    $sql = "select k29_recurso,
                 o15_descr,
                 k29_valor
          from sliprecurso
        inner join orctiporec on o15_codigo = k29_recurso
    where k29_slip in ($numslip)
    order by k29_recurso
         ";
    $recursos  = db_query($sql);
    // se houverem registros, monta um array
    $array_recursos =  array();
    if (pg_numrows($recursos)>0){
        for($x=0;$x < pg_numrows($recursos);$x++){
            db_fieldsmemory($recursos,$x);
            $array_recursos[] = "$k29_recurso#$o15_descr#$k29_valor";
        }

    }


    // print_r($array_recursos); exit;


    if (pg_numrows($dados) == 0) {
        throw new Exception('Documento de Slip não Cadastrado.');
    }

    $recursoSlip = null;
    if (FONTE_RECURSO_UNIAO) {
        $exercicio = db_getsession('DB_anousu');
        $sqlRecursoSlip = "select * from sliprecursocontas
                                inner join orctiporec on o15_codigo = k181_recursocredito
                                inner join fonterecurso on orctiporec_id = o15_codigo and exercicio = {$exercicio}
                                where k181_slip in ( {$numslip} )";
        $rsRecursoSlip = db_query($sqlRecursoSlip);
        if (pg_num_rows($rsRecursoSlip) > 0) {
            $recursoSlip = db_utils::fieldsMemory($rsRecursoSlip, 0);
        }
    }
    db_fieldsmemory($dados,0);

    $sqlcai = "select * from caiparametro where k29_instit = ".db_getsession('DB_instit');
    $resultcai = db_query($sqlcai) or die($sqlcai);
    if (pg_numrows($resultcai) == 0) {
        $k29_modslipnormal = 36;
        $k29_modsliptransf = 36;
    } else {
        db_fieldsmemory($resultcai, 0);
        if ($k29_modslipnormal != 36 and $k29_modslipnormal != 37 and $k29_modslipnormal != 381) {
            $k29_modslipnormal = 36;
        }
        if ($k29_modsliptransf != 36 and $k29_modsliptransf != 37 and $k29_modslipnormal != 381) {
            $k29_modsliptransf = 36;
        }
    }

    $quantdeb = 0;
    if ($k17_debito > 0) {
        $clsaltes->sql_record($clsaltes->sql_query_file($k17_debito));
        $quantdeb = $clsaltes->numrows;
    }

    $quantcre = 0;
    if ($k17_credito > 0) {
        $clsaltes->sql_record($clsaltes->sql_query_file($k17_credito));
        $quantcre = $clsaltes->numrows;
    }

    if ($quantdeb > 0 and $quantcre > 0) {
        $codmodelo = $k29_modsliptransf;
    } else {
        $codmodelo = $k29_modslipnormal;
    }

    $codigoUsuario = $k17_idusuario;
    if (empty($codigoUsuario)) {

        /* Comentado código abaixo porque não deve ser consultado
           dados da tabela db_auditoria nas rotinas do e-cidade */

        //$codigoUsuario = Transferencia::retornaEmissorSlip($k17_codigo);
        //if (empty($codigoUsuario)) {
            $codigoUsuario = DB_getsession("DB_id_usuario");
        //}
    }
    $usuario = UsuarioSistemaRepository::getPorCodigo($codigoUsuario);

    $pdf1 = new scpdf();
    $pdf1->Open();
    $pdf = new db_impcarne($pdf1, $codmodelo);
    $pdf->objpdf->AddPage();
    $pdf->objpdf->SetTextColor(0, 0, 0);

    // trecho para relatorio
    $head1 = "Texto numero 1";
    $head2 = "Texto numero 2";
    $head3 = "Texto numero 3";
    $head4 = "Texto numero 4";
    //$head5 = "Texto numero 5";
    $head6 = "Texto numero 6";
    $head7 = "Texto numero 7";
    $head8 = "Texto numero 8";
    $head9 = "Texto numero 9";
    $head10 = "Texto numero 10";
    // trecho para relatorio

    $sql = "select * from db_config where codigo = ".db_getsession('DB_instit');
    $dadospref = db_query($sql);
    db_fieldsmemory($dadospref, 0);

    $pdf->dados        = $dados;
    $pdf->recursos     = $array_recursos;
    $pdf->recurso_slip = $recursoSlip;
    $pdf->nome_usuario = $usuario->getNome();
    /**
     * dados bancarios do credor
     */
    $iNumCgmCredor       = $z01_numcgm;
    $oDaoPcfornecon      = db_utils::getDao('pcfornecon');
    $sCamposDadosCredor  = "pc63_banco, pc63_agencia, pc63_agencia_dig, pc63_conta, pc63_conta_dig,";
    $sCamposDadosCredor .= "(select db90_descr from db_bancos where db90_codban = pc63_banco) as descricrao_banco ";
    $sWhereDadosCredor   = "pc63_numcgm = {$iNumCgmCredor}";
    $sSqlDadosCredor     = $oDaoPcfornecon->sql_query_padrao(null, $sCamposDadosCredor, null, $sWhereDadosCredor);
    $rsDadosCredor       = db_query($sSqlDadosCredor);

    /**
     * Erro no sql
     */
    if ( !$rsDadosCredor ) {

        $sMensagemErro = "Erro ao buscar dados do credor.\n\n" . pg_last_error();
        throw new Exception($sMensagemErro);
    }

    $tipoVinculoLimiteSaque  = array(500, 501, 502, 503);

    /**
     * Dados bancarios da conta padrao
     */
    if ( pg_num_rows($rsDadosCredor) > 0 && !in_array($tipo_operacao, $tipoVinculoLimiteSaque)) {

        $oDadosCredor         = db_utils::fieldsMemory($rsDadosCredor, 0);
        $oDadosBancarioCredor = new StdClass();

        $oDadosBancarioCredor->iBanco         = $oDadosCredor->pc63_banco;
        $oDadosBancarioCredor->sBanco         = $oDadosCredor->descricrao_banco;
        $oDadosBancarioCredor->iAgencia       = $oDadosCredor->pc63_agencia;
        $oDadosBancarioCredor->iAgenciaDigito = $oDadosCredor->pc63_agencia_dig;
        $oDadosBancarioCredor->iConta         = $oDadosCredor->pc63_conta;
        $oDadosBancarioCredor->iContaDigito   = $oDadosCredor->pc63_conta_dig;

        $pdf->oDadosBancarioCredor = $oDadosBancarioCredor;
    }

    $pdf->logo     = $logo;
    $pdf->nomeinst = $nomeinst;
    $pdf->ender    = $ender;
    $pdf->munic    = $munic;
    $pdf->telef    = $telef;
    $pdf->email    = $email;
    $pdf->logo     = $logo;
    $pdf->motivo   = $motivo;
    $pdf->sEvento  = $sEvento;
    $pdf->objpdf->AliasNbPages();
    $pdf->objpdf->settopmargin(1);

    $pdf->imprime();
    $pdf->objpdf->Output();

} catch (Exception $oErro) {

    $sErro = str_replace("\n", '\n', $oErro->getMessage());

    $sMensagemErro .= "<script>                                 ";
    $sMensagemErro .= "  alert('{$sErro}'); ";
    $sMensagemErro .= "  window.close();                        ";
    $sMensagemErro .= "</script>                                ";

    echo $sMensagemErro;
}
