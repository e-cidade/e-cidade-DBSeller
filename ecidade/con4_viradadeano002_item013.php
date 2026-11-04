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

// Para garantir que nao houve erros em outros itens
use ECidade\Financeiro\Contabilidade\PlanoDeContas\Estrutural;
use ECidade\Financeiro\Orcamento\Recurso\Origem;

$sql = "
  select distinct 1
    from contabilidade.conlancam
    join contabilidade.conlancamdoc on c71_codlan = conlancam.c70_codlan
   where c70_anousu = {$anodestino}
    and c71_coddoc in (2032, 2033)
";

$rs = db_query($sql);
if (pg_num_rows($rs) > 0) {
    $sqlerro = true;
    db_msgbox(sprintf(
        'A "Abertura do Exercício" já foi processada em %s. Acesse: %s e cancele os documentos: 2032 e 2033',
        $anodestino,
        'Contabilidade > Procedimentos > Escrituração Contábil > Abertura do Exercício > Abertura Contábil'
    ));
}


if ($sqlerro == false) {
    $iAnoBusca = $anoorigem;
    if (file_exists("config/pcasp.txt")) {
        $aPcasp = file("config/pcasp.txt");
        if ($aPcasp[0] == 2013) {
            $iAnoBusca = $anodestino;
        }
    }

    $sqlTipos = " select * from emprestotipo where e90_estrut != '' order by 1";
    $tiposRP = db_utils::getCollectionByRecord(db_query($sqlTipos));


    // RESTOS A PAGAR
    $sqldelete = "delete from empresto where e91_anousu = $anodestino";
    $resultdelete = db_query($sqldelete);

    $sqlorigem = "select * from empresto where e91_anousu =   $anoorigem limit 1";
    $resultorigem = db_query($sqlorigem);
    $linhasorigem = pg_num_rows($resultorigem);

    $sqldestino = "select * from empresto where e91_anousu = {$anodestino} limit 1";
    $resultdestino = db_query($sqldestino);
    $linhasdestino = pg_num_rows($resultdestino);

    if (($linhasorigem > 0) && ($linhasdestino == 0)) {
        include(modification("classes/db_empresto_classe.php"));
        $clempresto = new cl_empresto;
        $sqlemp = "  select distinct on (e60_numemp) ";
        $sqlemp .= "         e60_numemp, ";
        $sqlemp .= "         e64_codele, ";
        $sqlemp .= "         c60_estrut, ";
        $sqlemp .= "         round(vlremp,2)::float8 as e60_vlremp, ";
        $sqlemp .= "         round(coalesce(vlranu,0),2)::float8 as e60_vlranu, ";
        $sqlemp .= "         round(coalesce(vlrliq,0),2)::float8 as e60_vlrliq, ";
        $sqlemp .= "         round(coalesce(vlrpag,0),2)::float8 as e60_vlrpag, ";
        $sqlemp .= "         (select o206_recurso from origemcomplementorecurso where o206_numero = e60_numemp and o206_origem = 1) as o58_codigo, ";
        $sqlemp .= "         e60_instit ";
        $sqlemp .= "    from empempenho ";
        $sqlemp .= "         inner join orcdotacao  on o58_anousu = e60_anousu and o58_coddot = e60_coddot ";
        $sqlemp .= "         inner join empelemento on e60_numemp = e64_numemp ";
        $sqlemp .= "         inner join conplanoorcamento on c60_codcon  = e64_codele and c60_anousu  = e60_anousu ";
        $sqlemp .= "         inner join (select c75_numemp, ";
        $sqlemp .= "                            round(sum(case when c53_tipo in (10) then round(c70_valor,2) end ),2)    as vlremp, ";
        $sqlemp .= "                            round(sum(case when c53_tipo in (11) then round(c70_valor,2) end ),2)    as vlranu, ";
        $sqlemp .= "                            round(sum(case when c53_tipo in (20) then round(c70_valor,2) ";
        $sqlemp .= "                                           when c53_tipo in (21) then round(c70_valor,2)*-1 end ),2) as vlrliq, ";
        $sqlemp .= "                            round(sum(case when c53_tipo in (30) then round(c70_valor,2) ";
        $sqlemp .= "                                           when c53_tipo in (31) then round(c70_valor,2)*-1 end ),2)    as vlrpag ";
        $sqlemp .= "                       from conlancamemp ";
        $sqlemp .= "                            inner join conlancam    on c70_codlan = c75_codlan ";
        $sqlemp .= "                            inner join conlancamdoc on c70_codlan = c71_codlan ";
        $sqlemp .= "                            inner join conhistdoc   on c53_coddoc = c71_coddoc ";
        $sqlemp .= "                      where c53_tipo in (10, 11,20, 21, 30, 31) ";
        $sqlemp .= "                        and c70_anousu = {$anoorigem} ";
        $sqlemp .= "                        and c71_data < '{$anodestino}-01-01' ";
        $sqlemp .= "                   group by c75_numemp) as vlremp on vlremp.c75_numemp = e60_numemp ";
        $sqlemp .= "   where round(round(round(vlremp,2) - round(coalesce(vlranu,0),2),2)::float8 - round(coalesce(vlrpag,0),2)::float8,2) > 0 ";
        $sqlemp .= "     and e60_anousu = {$anoorigem} ";

        $resultemp = db_query($sqlemp);
        $linhasemp = pg_num_rows($resultemp);

        for ($e = 0; $e < $linhasemp; $e++) {
            db_fieldsmemory($resultemp, $e);
            $dado = db_utils::fieldsMemory($resultemp, $e);


            db_atutermometro($e, $linhasemp, 'termometroitem', 1, $sMensagemTermometroItem);

            $tipo = getCodigoTipo($tiposRP, $dado->c60_estrut);

            $clempresto->e91_anousu = $anodestino;
            $clempresto->e91_numemp = $dado->e60_numemp;
            $clempresto->e91_vlremp = $dado->e60_vlremp;
            $clempresto->e91_vlranu = $dado->e60_vlranu;
            $clempresto->e91_vlrliq = $dado->e60_vlrliq;
            $clempresto->e91_vlrpag = $dado->e60_vlrpag;
            $clempresto->e91_elemento = $dado->c60_estrut;
            $clempresto->e91_recurso = $dado->o58_codigo;
            $clempresto->e91_codtipo = $tipo;
            $clempresto->e91_rpcorreto = "false";
            $clempresto->incluir($anodestino, $dado->e60_numemp);
            if ($clempresto->erro_status == 0) {
                $sqlerro = true;
                $erro_msg .= $clempresto->erro_msg;
                break;
            }

            if ($tipo == 999) {
                $cldb_viradaitemlog->c35_log = "Empenho [{$dado->e60_numemp}] sem tipo para Desdobramento: [{$dado->c60_estrut}] processado utilizando o tipo 999";
                $cldb_viradaitemlog->c35_codarq = 1011;
                $cldb_viradaitemlog->c35_db_viradaitem = $cldb_viradaitem->c31_sequencial;
                $cldb_viradaitemlog->c35_data = date("Y-m-d");
                $cldb_viradaitemlog->c35_hora = date("H:i");
                $cldb_viradaitemlog->incluir(null);
                if ($cldb_viradaitemlog->erro_status == 0) {
                    $sqlerro = true;
                    $erro_msg .= $cldb_viradaitemlog->erro_msg;
                    break;
                }
            }
        }
        if ($sqlerro == false) {
            //echo("Processando restos anteriores a ".$this->anousu);
            $sqlemp4 = "select e91_numemp,e91_vlremp,e91_vlranu,e91_vlrliq,e91_vlrpag,e91_elemento,e91_recurso,e91_codtipo,e91_rpcorreto ";
            $sqlemp4 .= "from empresto where e91_anousu = {$anoorigem}";

            $resultemp4 = db_query($sqlemp4);
            $linhasemp4 = pg_num_rows($resultemp4);

            for ($m = 0; $m < $linhasemp4; $m++) {
                db_fieldsmemory($resultemp4, $m);
                $sqlemp5 = "  select c71_coddoc, ";
                $sqlemp5 .= "         sum(c70_valor) ";
                $sqlemp5 .= "    from empresto ";
                $sqlemp5 .= "         inner join empempenho on e60_numemp = e91_numemp ";
                $sqlemp5 .= "         inner join conlancamemp on c75_numemp = e91_numemp ";
                $sqlemp5 .= "         inner join conlancamdoc on c75_codlan = c71_codlan ";
                $sqlemp5 .= "         inner join conlancam on c70_codlan = c71_codlan ";
                $sqlemp5 .= "   where e91_anousu = {$anoorigem} ";
                $sqlemp5 .= "     and e91_numemp = {$e91_numemp} ";
                $sqlemp5 .= "     and c71_data between '{$anoorigem}-01-01' and '{$anoorigem}-12-31' ";
                $sqlemp5 .= "group by e91_numemp, ";
                $sqlemp5 .= "         c71_coddoc  ";

                $resultemp5 = db_query($sqlemp5);
                $linhasemp5 = pg_num_rows($resultemp5);

                $vlranu = 0;
                $vlrliq = 0;
                $vlrpag = 0;

                for ($v = 0; $v < $linhasemp5; $v++) {
                    db_fieldsmemory($resultemp5, $v);

                    if ($c71_coddoc == 31 || $c71_coddoc == 32) {
                        $vlranu += $sum;
                    }
                    if ($c71_coddoc == 33 || $c71_coddoc == 39) {
                        $vlrliq += $sum;
                    }
                    if ($c71_coddoc == 34 || $c71_coddoc == 40) {
                        $vlrliq -= $sum;
                    }

                    if (in_array($c71_coddoc, [6008, 6010, 35, 37])) {
                        $vlrpag += $sum;
                    }

                    if (in_array($c71_coddoc, [6009, 6011, 38, 36])) {
                        $vlrpag -= $sum;
                    }

                    if ($c71_coddoc == 31) {
                        $vlrliq -= $sum;
                    }
                }

                if (round(round(round($e91_vlremp, 2) - round($e91_vlranu + $vlranu, 2), 2) - round(($e91_vlrpag + $vlrpag), 2), 2) > 0) {
                    $e91_vlranu = ($e91_vlranu + $vlranu);
                    $e91_vlrliq = ($e91_vlrliq + $vlrliq);
                    $e91_vlrpag = ($e91_vlrpag + $vlrpag);

                    $e91_rpcorreto = ($e91_rpcorreto == "t") ? "true" : "false";

                    $clempresto->e91_anousu = $anodestino;
                    $clempresto->e91_numemp = $e91_numemp;
                    $clempresto->e91_vlremp = $e91_vlremp;
                    $clempresto->e91_vlranu = "$e91_vlranu";
                    $clempresto->e91_vlrliq = "$e91_vlrliq";
                    $clempresto->e91_vlrpag = "$e91_vlrpag";
                    $clempresto->e91_elemento = "$e91_elemento";
                    $clempresto->e91_recurso = $e91_recurso;
                    $clempresto->e91_codtipo = $e91_codtipo;
                    $clempresto->e91_rpcorreto = $e91_rpcorreto;
                    $clempresto->incluir($anodestino, $e91_numemp);
                    if ($clempresto->erro_status == 0) {
                        $sqlerro = true;
                        $erro_msg .= $clempresto->erro_msg;
                        break;
                    }
                }
            }
        }

        /**
         * Percorre os empenhos inscritos em RP e cria a origem do recurso do RP
         */
        $sql = "
                select e91_numemp, e91_recurso, o15_complemento
                  from empresto
                  join orctiporec on o15_codigo = e91_recurso
                 where e91_anousu = {$anodestino}
            ";
        $rs = db_query($sql);

        while ($state = pg_fetch_array($rs)) {
            Origem::setEmpenhoRP($state['e91_numemp'], $state['e91_recurso'], $state['o15_complemento']);
        }

    } else {
        if ($linhasorigem == 0) {
            $cldb_viradaitemlog->c35_log = "Não existem dados (empresto) para o exercicio $anoorigem";
        }
        if ($linhasdestino > 0) {
            $cldb_viradaitemlog->c35_log = "Ja existem dados  (empresto) para ano de destino $anodestino";
        }
        $cldb_viradaitemlog->c35_codarq = 1011;
        $cldb_viradaitemlog->c35_db_viradaitem = $cldb_viradaitem->c31_sequencial;
        $cldb_viradaitemlog->c35_data = date("Y-m-d");
        $cldb_viradaitemlog->c35_hora = date("H:i");
        $cldb_viradaitemlog->incluir(null);
        if ($cldb_viradaitemlog->erro_status == 0) {
            $sqlerro = true;
            $erro_msg .= $cldb_viradaitemlog->erro_msg;
        }
    }
}

function comparaElementoDespesa($elementoEmpenho, $elementoTransacao)
{
    return ($elementoEmpenho >= $elementoTransacao);
}


/**
 *
 * funcao recursiva para buscar o vinculo do estrutural pelo elemento do desdobramento do empenho
 */

function getEmpRestoTipo($oDadosRecursivo)
{
    $oDaoContrans = new cl_contrans;
    $iAnoUsu = $oDadosRecursivo->iAnousu;
    $iPosicao = $oDadosRecursivo->iPosicao;

    $sElemento = str_pad(substr($oDadosRecursivo->sElemento, 0, $iPosicao), 15, '0', STR_PAD_RIGHT);

    //echo "<br>Elemento Procurado: $sElemento<br>";
    // se não achar até o ultimo digito retornará nulo para usar o tipo 999
    if ($sElemento == "000000000000000") {
        return null;
    }

    $sSql = $oDaoContrans->sql_queryVinculoEmpRestoTipoLiquidacao(
        null,
        "c60_estrut",
        null,
        "c114_elemento = '{$sElemento}' and c45_anousu = {$iAnoUsu}"
    );

    $rs = $oDaoContrans->sql_record($sSql);

    // se achou retorna o estrutural para depois buscar na emprestotipo
    if ($oDaoContrans->numrows > 0) {
        $oDados = db_utils::fieldsMemory($rs, 0);
        return $oDados->c60_estrut;
    }

    // caso ainda nao tenha achado o c60_estrut, deveremos chamar novamente porem alterando o elemento
    // reduzindo os digitos e completando com zeros a direita

    $oDadosRecursivo = new stdClass();
    $oDadosRecursivo->sElemento = $sElemento;
    $oDadosRecursivo->iAnousu = $iAnoUsu;
    $oDadosRecursivo->iPosicao = $iPosicao - 1;

    return getEmpRestoTipo($oDadosRecursivo);
}


function getCodigoTipo($tiposRP, $desdobramento)
{
    $estrutural = new Estrutural($desdobramento);
    $nivel = $estrutural->getNivel();

    while ($nivel > 2) {
        foreach ($tiposRP as $tipo) {
            if ($tipo->e90_estrut == $estrutural->getEstruturalAteNivel()) {
                return $tipo->e90_codigo;
            }
        }
        $estrutural = $estrutural->getEstruturalPai();
        $nivel = $estrutural->getNivel();
    }

    return 999;
}
