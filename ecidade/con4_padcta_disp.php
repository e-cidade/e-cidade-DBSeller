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

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Factories\DisponibilidadeFactory;

class cta_disp
{
    protected $arq;
    protected $anousu;
    protected $header;

    public function __construct($header)
    {
        $this->anousu = db_getsession("DB_anousu");
        $this->header = $header;
    }

    public function processa($instit = 1, $data_ini = "", $data_fim = "", $tribinst = "", $subelemento = "")
    {

        if ($this->anousu >= 2023) {
            $instituicoes = InstituicaoRepository::getInstituicaoConsolida(db_getsession('DB_instit'));
            $service = DisponibilidadeFactory::getService($this->anousu, $instituicoes, $data_ini, $data_fim);
            $service->setHeader($this->header);
            $service->processa();

            return true;
        }


        $this->arq = fopen("tmp/CTA_DISP.TXT", 'w+');
        umask(74);
        $this->arq = fopen("tmp/CTA_DISP.TXT", 'w+');
        fputs($this->arq, $this->header);
        fputs($this->arq, "\r\n");

        global $db21_idtribunal,
               $contador,
               $instituicoes,
               $contador,
               $codcla,
               $c60_codcla,
               $nomeinst,
               $c60_estrut,
               $c60_identificadorfinanceiro,
               $c61_codigo,
               $c63_banco,
               $c63_agencia,
               $c63_conta,
               $c61_instit,
               $c63_codigooperacao,
               $c63_dvagencia,
               $c63_dvconta;

        $sele = " ($instit) ";

        /*
         0101 - camara
         0201 - pref
        */
        //$sql = $this->getSqlProcessar($sele);
        $sql = $this->getSqlProcessarContasComValores($sele);
        $result = db_query($sql);

        for ($x = 0; $x < pg_num_rows($result); $x++) {
            db_fieldsmemory($result, $x);

            $dados = db_utils::fieldsMemory($result, $x);
            $resintit = db_query("
                select db21_idtribunal
                  from db_config
                  join db_tipoinstit on db21_codtipo = db21_tipoinstit
                 where codigo = $c61_instit
            ");
            if (pg_num_rows($resintit) == 0) {
                echo "Parametro db21_idtribunal não configurado na tabela db_config->db_tipoinstit";
                exit;
            } else {
                db_fieldsmemory($resintit, 0);
            }

            switch ($dados->tipo_instituicao) {
                case 1:
                case 3:
                case 4:
                    $cla = 1;
                    break;
                case 2:
                    $cla = 2;
                    break;
                case 5:
                case 6:
                    $cla = 3;
                    break;
                default:
                    $cla = 9;
            }

            $line = formatar($c60_estrut, 20, 'n');
            $tamanhoConta = 20;
            $alteraConta = false;

            if ($c63_dvconta !== '') {
                $alteraConta = true;
                $tamanhoConta = 19;
            }

            $tamanhoAgencia = 5;
            $alteraAgencia = false;

            if ($c63_dvagencia !== '' && $c63_banco != 104) {
                $alteraAgencia = true;
                $tamanhoAgencia = 4;
            }

            $subrecurso = formatar($dados->recurso, 4, 'n');
            $complementoSiconfi = str_pad($dados->complemento, 4, '0', STR_PAD_LEFT);
            $complementoSubrecurso = $complementoSiconfi;
            // zera subrecurso e complemento se conta não possui saldo anterior
            if ($this->anousu >= 2023 && $dados->tem_saldo_anterior !== 't') {
                $subrecurso = '0000';
                $complementoSubrecurso = '0000';
            }

            $line .= $instituicoes[$c61_instit];
            $line .= $subrecurso;
            $line .= formatar(trim($c63_banco), 5, 'n');
            $line .= formatar(trim($c63_agencia), $tamanhoAgencia, 'n');

            if ($alteraAgencia) {
                $line .= formatar(trim($c63_dvagencia), 1, 'n');
            }

            if ($c63_banco == 104) {
                $t = formatar(trim($c63_codigooperacao), 11, 'n');
                $line .= $t;
                $tamanhoConta = $tamanhoConta - 11;
            }

            $line .= formatar(trim(str_replace(array('-', '.'), array('', ''), trim($c63_conta))), $tamanhoConta, 'n');

            if ($alteraConta) {
                $line .= formatar(trim($c63_dvconta), 1, 'n');
            }

            $sEstrutural = substr($c60_estrut, 0, 7);
            $sEstruturalQuintoNivel = substr($c60_estrut, 0, 5);

            if ($sEstrutural == '1111101') {
                $line .= '1'; // caixa
            } else {
                if ($sEstrutural == '1111106' ||
                    $sEstrutural == '1111116' ||
                    $sEstrutural == '1111130' ||
                    $sEstruturalQuintoNivel == '11131' ||
                    $sEstruturalQuintoNivel == '11112'
                ) {
                    $line .= '2'; // banco conta movimento
                } else {
                    if (($sEstrutural == '1111150' || (substr($c60_estrut, 0, 3) == '114')
                        and $c60_identificadorfinanceiro == 'F')) {
                        $line .= '3'; // banco conta aplicacao
                    } else {
                        if (substr($c60_estrut, 0, 11) == '11251020001' ||
                            substr($c60_estrut, 0, 11) == '11251020002' ||
                            substr($c60_estrut, 0, 11) == '11251020003') {
                            $line .= '4'; // deposito sentencas judiciais
                        } else {
                            if (substr($c60_estrut, 0, 11) == '11251020004' ||
                                substr($c60_estrut, 0, 11) == '11251020005' ||
                                substr($c60_estrut, 0, 11) == '11251020006') {
                                $line .= '5'; // depositos sentencas judiciais rp
                            } else {
                                $line .= '2'; // depositos sentencas judiciais rp
                            }
                        }
                    }
                }
            }

            $line .= formatar($cla, 1, 'n');

            if ($this->anousu >= 2020) {
                $line .= $complementoSubrecurso;
            }

            if ($this->anousu == 2022) {
                $line .= "00000000";
            }

            if ($this->anousu >= 2023) {
                $fonte = str_pad(substr($dados->codigo_siconfi, 1), 4, '0', STR_PAD_LEFT);
                $line .= $fonte;
                $line .= $complementoSiconfi;
            }
            $contador++;
            fputs($this->arq, $line);
            fputs($this->arq, "\r\n");
        }

        // trailer
        $contador = espaco(10 - (strlen($contador)), '0') . $contador;
        $line = "FINALIZADOR" . $contador;

        fputs($this->arq, $line);
        fputs($this->arq, "\r\n");
        fclose($this->arq);

        $teste = "true";

        return $teste;
    }

    private function getSqlProcessar($sele)
    {
        $sSql = "select
                    c60_estrut as c60_estrut,
                    c61_instit,
					o15_recurso as recurso,
					c60_identificadorfinanceiro,
					case when c63_banco is null then '00'
					   else c63_banco end as c63_banco,
					case when c63_agencia is null then '000'
					   else c63_agencia end as c63_agencia,
					case when c63_conta is null then '0000'
					   else c63_conta end as c63_conta,
					c63_dvagencia,
                    c63_dvconta,
		            c60_codcla,
		            c63_codigooperacao,
                    case
                       when o200_tribunal is true then o200_sequencial
                       else 0
                    end as complemento,
                    (select db21_tipoinstit from db_config where codigo = c61_instit) as tipo_instituicao
              from conplano
	          join conplanoreduz on c61_codcon = c60_codcon and c60_anousu = c61_anousu
              join orctiporec on orctiporec.o15_codigo = conplanoreduz.c61_codigo
              join complementofonterecurso on complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
	          left join conplanoconta on c63_codcon = c60_codcon
                                     and c63_anousu=c60_anousu
                                     and c61_reduz = c63_reduz

	          where c61_instit in $sele ";

        if (USE_PCASP) {
            $sSql .= " and ( c60_estrut like '11111%' or c60_estrut like '114%' )";
        } else {
            $sSql .= " and ( c60_estrut like '1111%' or c60_estrut like '115%' )";
        }

        $sSql .= " and c60_identificadorfinanceiro = 'F' ";
        $sSql .= " and c60_anousu=" . db_getsession("DB_anousu") . " order by c60_estrut,c61_instit";
        return $sSql;
    }

    private function getSqlProcessarContasComValores($sele)
    {
        $sSql = "select
                    coalesce(sum(c68_debito + c62_vlrdeb), 0) as c68_debito,
                    coalesce(sum(c68_credito + c62_vlrcre), 0) as c68_credito,
                    case when (c62_vlrcre > 0 or c62_vlrdeb > 0) then true else false end as tem_saldo_anterior,
                    c60_estrut as c60_estrut,
                    c61_instit,
					o15_recurso as recurso,
					codigo_siconfi,
					c60_identificadorfinanceiro,
					case when c63_banco is null then '00'
					   else c63_banco end as c63_banco,
					case when c63_agencia is null then '000'
					   else c63_agencia end as c63_agencia,
					case when c63_conta is null then '0000'
					   else c63_conta end as c63_conta,
					c63_dvagencia,
                    c63_dvconta,
		            c60_codcla,
		            c63_codigooperacao,
                    case
                       when o200_tribunal is true then o200_sequencial
                       else 0
                    end as complemento,
                    (select db21_tipoinstit from db_config where codigo = c61_instit) as tipo_instituicao
              from conplano
	          join conplanoreduz on c61_codcon = c60_codcon and c60_anousu = c61_anousu
              join orctiporec on orctiporec.o15_codigo = conplanoreduz.c61_codigo
              join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                   and fonterecurso.exercicio = conplanoreduz.c61_anousu
              join complementofonterecurso on complementofonterecurso.o200_sequencial = orctiporec.o15_complemento
	          left join conplanoconta on c63_codcon = c60_codcon
                                     and c63_anousu=c60_anousu
                                     and c61_reduz = c63_reduz
               left join conplanoexe on c62_reduz = c61_reduz
                                     and c62_anousu = c61_anousu
               left join conplanoexesaldo on c68_reduz = c62_reduz
                                          and c68_anousu = c62_anousu

	          where c61_instit in $sele ";

        if (USE_PCASP) {
            $sSql .= " and ( c60_estrut like '11111%' or c60_estrut like '114%' or c60_estrut like '11131%' or c60_estrut like '11132%')";
        } else {
            $sSql .= " and ( c60_estrut like '1111%' or c60_estrut like '115%' )";
        }
        $sSql .= " and c60_identificadorfinanceiro = 'F' ";
        $sSql .= " and c60_anousu=" . db_getsession("DB_anousu");
        $sSql .= " and ( ( c62_vlrdeb + c62_vlrcre ) > 0 or c68_debito > 0 or  c68_credito > 0) ";
        $sSql .= "  group by       ";
        $sSql .= "          3, c60_estrut                  , ";
        $sSql .= "          c61_instit                  , ";
        $sSql .= "          recurso                     , ";
        $sSql .= "          codigo_siconfi,               ";
        $sSql .= "          c60_identificadorfinanceiro , ";
        $sSql .= "          c63_banco                   , ";
        $sSql .= "          c63_agencia                 , ";
        $sSql .= "          c63_conta                   , ";
        $sSql .= "          c63_dvagencia               , ";
        $sSql .= "          c63_dvconta                 , ";
        $sSql .= "          c60_codcla                  , ";
        $sSql .= "          c63_codigooperacao          , ";
        $sSql .= "          complemento                 , ";
        $sSql .= "          tipo_instituicao             ";
        $sSql .= "   order by c60_estrut,c61_instit";

        return $sSql;
    }
}
