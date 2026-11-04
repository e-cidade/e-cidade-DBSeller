<?php

/**
 * Deletamos as rubricas R913, R914 e R815 das tabelas de calculo, quando o servidor possuir moléstia e o seu
 * vinculo for INATIVO ou PENSIONISTA
 */

LogCalculoFolha::write('Deletando R919, R914 ou R915 para servidores que possuem moléstia e são Inativos ou Pensionistas $opcao_geral:' . $opcao_geral);

$aFolhasNoCalculo = array(1, 3, 4, 5, 8); // Folhas

switch ($opcao_geral) {
    case 1:
        $sTabela = 'gerfsal';
        $sSigla = 'r14';

        $afastamentosDeletar = DBRegistry::get('afastamentosDeletar');
        if (!empty($afastamentosDeletar)) {
            CalculoFolha::deletaAfastamentos($afastamentosDeletar);
        }
        CalculoFolha::reverteAfastamento(DBRegistry::getInstance());

        $matriculasComProrrogacao = DBRegistry::get('matriculasAlteradas');
        if (!empty($matriculasComProrrogacao)) {
            CalculoFolha::proporcionalizaAfastamento(
                $matriculasComProrrogacao,
                InstituicaoRepository::getInstituicaoSessao(),
                DBRegistry::getInstance(),
                $sTabela,
                $sSigla
            );
        }
        CalculoFolha::reverteAfastamento(DBRegistry::getInstance(), true);
        break;
    case 3:

        $sTabela = 'gerffer';
        $sSigla = 'r31';
        break;
    case 4:

        $sTabela = 'gerfres';
        $sSigla = 'r20';
        break;

    case 5:

        $sTabela = 'gerfs13';
        $sSigla = 'r35';
        break;
    case 8:

        $sTabela = 'gerfcom';
        $sSigla = 'r48';
        break;
}


///// CALCULA A MARGEM CONSIGNAVEL

if($opcao_geral == 1 ){

    $sql_margem = "
    update gerfsal set r14_valor = round(margem.margem,2) 
    from
    (
    select r14_regist as regist, ((B033-B034)/100*30) - B035 as margem
    from
    (
    select ger.r14_regist, 
           coalesce((select sum(r14_valor) 
            from gerfsal  fx  
            where fx.r14_regist = ger.r14_regist 
              and fx.r14_anousu = ger.r14_anousu 
              and fx.r14_mesusu = ger.r14_mesusu 
              and fx.r14_rubric in (select r09_rubric 
                                 from basesr 
                                 where r09_anousu = $anousu
                                   and r09_mesusu = $mesusu
                                   and r09_instit = $DB_instit
                                   and r09_base   = 'B033'
                                 )  
           ),0) as B033,
           coalesce((select sum(r14_valor) 
            from gerfsal sal 
            where sal.r14_regist = ger.r14_regist 
              and sal.r14_anousu = ger.r14_anousu 
              and sal.r14_mesusu = ger.r14_mesusu 
              and sal.r14_rubric in (select r09_rubric 
                                 from basesr 
                                 where r09_anousu = $anousu
                                   and r09_mesusu = $mesusu
                                   and r09_instit = $DB_instit
                                   and r09_base   = 'B034'
                                 ) 
           ),0) as B034,
           coalesce((select sum(r14_valor) 
            from gerfsal sal 
            where sal.r14_regist = ger.r14_regist 
              and sal.r14_anousu = ger.r14_anousu 
              and sal.r14_mesusu = ger.r14_mesusu 
              and sal.r14_rubric in (select r09_rubric 
                                 from basesr 
                                 where r09_anousu = $anousu 
                                   and r09_mesusu = $mesusu
                                   and r09_instit = $DB_instit 
                                   and r09_base   = 'B035'
                                 ) 
           ),0) as B035
    from gerfsal as ger
         inner join rhpessoalmov on rh02_regist = r14_regist
                                and rh02_anousu = r14_anousu
                                and rh02_mesusu = r14_mesusu
                                and rh02_instit = $DB_instit
    where ger.r14_anousu = $anousu
      and ger.r14_mesusu = $mesusu 
      and ger.r14_rubric = 'R803'
      and ger.r14_instit = $DB_instit
      $where_regist_fim
    ) as x
    ) as margem
    
    where r14_anousu = $anousu and r14_mesusu = $mesusu and r14_rubric = 'R803' and r14_regist = margem.regist
    ";
    $res_margem = pg_query($sql_margem) or die($sql_margem);

    // Deletamos a R802
    $sql_margem_r802 = "delete from gerfsal 
    where r14_anousu = $anousu 
      and r14_mesusu = $mesusu 
      and r14_rubric = 'R802' 
      and r14_instit = $DB_instit
      $where_regist_fim
      ";
    $res_margem_neg = pg_query($sql_margem_r802) or die($sql_margem_r802);


    // Criando a R802 positiva (Margem negativa)
     $sql_margem_neg = "update gerfsal  set r14_rubric = 'R802', r14_valor = (r14_valor * -1)
                        where r14_anousu = $anousu 
                          and r14_mesusu = $mesusu 
                          and r14_rubric = 'R803' 
                          and r14_instit = $DB_instit
                          and (r14_valor < 0 )
                          $where_regist_fim
                          ";

     $res_margem_neg = pg_query($sql_margem_neg) or die($sql_margem_neg);

    // Recriando a R803 zerada pra quem tem R802
    $sql_margem_r803 = "insert into gerfsal(
        r14_anousu,
        r14_mesusu,
        r14_regist,
        r14_rubric,
        r14_valor,
        r14_pd,
        r14_quant,
        r14_lotac ,
        r14_semest,
        r14_instit
    ) (
        select 
            r14_anousu,
            r14_mesusu,
            r14_regist,
            'R803',
            0,
            r14_pd,
            r14_quant,
            r14_lotac ,
            r14_semest,
            r14_instit
        from
            gerfsal
        where
            r14_anousu = $anousu 
            and r14_mesusu = $mesusu 
            and r14_rubric = 'R802' 
            and r14_instit = $DB_instit
            $where_regist_fim       
    )
    ";

    $res_margem_neg = pg_query($sql_margem_r803) or die($sql_margem_r803);

    
      $sql_arred = "update gerfsal set r14_valor = round(r14_valor,2) 
                    from rhpessoalmov
                    where r14_anousu = rh02_anousu 
                      and r14_mesusu = rh02_mesusu 
                      and r14_regist = rh02_regist
                      and r14_anousu = $anousu 
                      and r14_mesusu = $mesusu
                      and r14_instit = $DB_instit
                      $where_regist_fim ";
      $res_arred = pg_query($sql_arred) or die($sql_arred);
    
    
}


if (in_array($opcao_geral, $aFolhasNoCalculo)) { // Não executa para o cálculo de fixo e adiantamento e provisões

    $iInstituicao = db_getsession("DB_instit");
    $sSqlDeletaMolestia = "delete from {$sTabela} ";
    $sSqlDeletaMolestia .= "      where {$sSigla}_rubric in ('R913', 'R914', 'R915', 'R997', 'R999')";
    $sSqlDeletaMolestia .= "        and {$sSigla}_regist in ( select rh02_regist ";
    $sSqlDeletaMolestia .= "                                    from rhpessoalmov";
    $sSqlDeletaMolestia .= "                                   inner join  rhregime on rh30_codreg = rh02_codreg ";
    $sSqlDeletaMolestia .= "                                   where rh30_vinculo <> 'A'";
    $sSqlDeletaMolestia .= "                                     and rh02_anousu = {$anousu}";
    $sSqlDeletaMolestia .= "                                     and rh02_mesusu = {$mesusu}";
    $sSqlDeletaMolestia .= "                                     and rh02_instit = {$iInstituicao}";
    $sSqlDeletaMolestia .= "                                     and rh02_portadormolestia = true ";
    $sSqlDeletaMolestia .= "                                 )";
    $sSqlDeletaMolestia .= "        and {$sSigla}_anousu = $anousu";
    $sSqlDeletaMolestia .= "        and {$sSigla}_mesusu = $mesusu";
    $sSqlDeletaMolestia .= "        and {$sSigla}_instit = $iInstituicao";
    $rsDeletaMolestia = db_query($sSqlDeletaMolestia);
    if (pg_affected_rows($rsDeletaMolestia) > 0) {
        LogCalculoFolha::write("Removido " . pg_affected_rows($rsDeletaMolestia) . " Registros de IRRF.");
    }
    if (!$rsDeletaMolestia) {
        throw new DBException("Ocorreu um erro ao deletar as rubricas de IRRF para portadores de moléstia. =>" . pg_num_rows($rsDeletaMolestia) . " ");
    }
}
