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

require_once modification("interfaces/iPadArquivoTxtBase.interface.php");
require_once modification("model/contabilidade/arquivos/sigfis/SigfisArquivoBase.model.php");

use ECidade\Financeiro\Orcamento\Repository\FonteRecursoSiconfiRepository;

/**
 *
 * Classe Responsável pela geração dos dados necessários para o arquivo Plano de Contas
 * @author Andrio Costa
 */
class SigfisArquivoPlanoConta extends SigfisArquivoBase implements iPadArquivoTXTBase
{
    protected $iCodigoLayout = 204;
    protected $sNomeArquivo = 'ContaCont';
    protected $sIndicadorEmpresa = 'N';

    /**
     *
     * Busca os dados para gerar o Arquivo de Plano de Conta
     *
     */
    public function gerarDados()
    {

        $oDbConfig = new db_stdClass();
        $clConPlano = new cl_conplano;
        $oDadoConfig = $oDbConfig->getDadosInstit();

        if ($oDadoConfig->db21_tipoinstit == 9 || $oDadoConfig->db21_tipoinstit == 10) {
            $this->sIndicadorEmpresa = 'S';
        } else {
            $this->sIndicadorEmpresa = 'N';
        }

        $iAnoSessao = db_getsession("DB_anousu");

        $this->setCodigoLayout(204);
        if ($iAnoSessao < 2013) {
            $this->setCodigoLayout(109);
        }

        $sCampos = " conplano.c60_anousu,                                                                                 ";
        $sCampos .= " conplano.c60_estrut,                                                                                 ";
        $sCampos .= " conplano.c60_codcon,                                                                                 ";
        $sCampos .= " conplano.c60_codsis,                                                                                 ";
        $sCampos .= " conplano.c60_naturezasaldo,                                                                          ";
        $sCampos .= " conplano.c60_descr,                                                                                  ";
        $sCampos .= " conplanoreduz.c61_codigo,                                                                            ";
        $sCampos .= " coalesce(conplanoreduz.c61_reduz,0) as c61_reduz,                                                    ";
        $sCampos .= " conplanoreduz.c61_instit,                                                                            ";
        $sCampos .= " fc_nivel_plano2005(conplano.c60_estrut) as nivel,                                                    ";
        $sCampos .= " case when conplanoreduz.c61_reduz is not null then 1 else 2 end as recebe_lancamento,                ";
        $sCampos .= " case when conplano.c60_codsis = 6 then 1                                                             ";
        $sCampos .= "      when orcelemento.o56_codele is not null then 2                                                  ";
        $sCampos .= "      when orcfontes.o57_codfon is not null then 3                                                    ";
        $sCampos .= "      else 9 end as tipo_conta,                                                                       ";
        $sCampos .= " case when c63_banco   is null then '0' else c63_banco   end as banco,                                  ";
        $sCampos .= " case when c63_agencia is null then '' else c63_agencia end as agencia,                                ";
        $sCampos .= " case when c63_conta   is null then '' else c63_conta   end as conta,                                   ";


        if ($iAnoSessao < 2016) {
            $sCampos .= " c56_sequencial as seq_conta_corrente, ";
        } else {
            $sCampos .= " c56_contabancaria as seq_conta_corrente, ";
        }

        $sCampos .= " case when length(trim(db83_descricao)) > 0 then trim(db83_descricao) else conplano.c60_descr end as descr_conta_corrente ";


        $sql = "";
        $sql .= " select $sCampos ";
        $sql .= " from conplano                             ";
        $sql .= "      left join conplanoreduz on conplano.c60_codcon       =  conplanoreduz.c61_codcon  ";
        $sql .= "                             and conplano.c60_anousu       = conplanoreduz.c61_anousu  ";
        $sql .= "      left join conplanoconta on conplanoreduz.c61_codcon = conplanoconta.c63_codcon  ";
        $sql .= "                             and conplanoreduz.c61_anousu  = conplanoconta.c63_anousu                        ";
        $sql .= "                             and conplanoreduz.c61_reduz  = conplanoconta.c63_reduz                        ";
        $sql .= "      left join orcfontes     on conplanoreduz.c61_anousu  = orcfontes.o57_anousu      ";
        $sql .= "                             and conplanoreduz.c61_codcon = orcfontes.o57_codfon                             ";
        $sql .= "      left join orcelemento   on conplanoreduz.c61_anousu  = orcelemento.o56_anousu    ";
        $sql .= "                             and conplanoreduz.c61_codcon = orcelemento.o56_codele                           ";
        $sql .= "      left join conplanocontabancaria on conplano.c60_codcon = conplanocontabancaria.c56_codcon  ";
        $sql .= "                             and conplano.c60_anousu   = conplanocontabancaria.c56_anousu                        ";
        $sql .= "                             and conplanoreduz.c61_reduz   = conplanocontabancaria.c56_reduz                        ";
        $sql .= "      left join configuracoes.contabancaria on db83_sequencial = c56_contabancaria                               ";
        $sql .= "      where conplano.c60_anousu = $this->iAnoUso order by conplano.c60_estrut";
        $sSqlConPlano = $sql;

        $rsConPlano = $clConPlano->sql_record($sSqlConPlano);

        /*
         * Variáveis de contrele da Classe;
         */
        $ReservadoTCE = " ";
        $iMes = substr($this->dtDataFinal, 5, 2);
        $this->addLog("=====Arquivo" . $this->getNomeArquivo() . " Erros:\n");
        if ($clConPlano->numrows > 0) {

            if (empty($this->sCodigoTribunal)) {
                throw new Exception("O código do tribunal deve ser informado para geração do arquivo");
            }

            for ($i = 0; $i < $clConPlano->numrows; $i++) {
                $oDados = new stdClass();
                $oDadosPlano = db_utils::fieldsMemory($rsConPlano, $i);

                if ($oDadosPlano->c61_reduz > 0 and $oDadosPlano->c61_instit != $oDadoConfig->codigo) {
                    continue;
                }

                $iCodigoContaTCE = '';
                $sNaturezaSaldo = 'M';
                if ($oVinculo = SigfisVinculoConta::getVinculoConta($oDadosPlano->c60_codcon)) {

                    $iCodigoContaTCE = $oVinculo->contatce;
                    $sNaturezaSaldo = $oVinculo->naturezasaldo;


                    if ($oDadosPlano->recebe_lancamento == 1) {
                    } else {
                        $iCodigoContaTCE = "";
                        $sNaturezaSaldo = "";
                    }

                    if ($oDadosPlano->c60_naturezasaldo == 1) {
                        $sNaturezaSaldo = "D";
                    } elseif ($oDadosPlano->c60_naturezasaldo == 2) {
                        $sNaturezaSaldo = "C";
                    } else {
                        $sNaturezaSaldo = "M";
                    }
                    $iSeqPCASP = $iCodigoContaTCE;
                } else {
                    $sErroLog = "Conta {$oDadosPlano->c60_codcon} - {$oDadosPlano->c60_estrut} - {$oDadosPlano->c60_descr} ";
                    $sErroLog .= "sem Vinculo com plano do SIGFIS\n";
                    $this->addLog($sErroLog);
                }

                if (trim($iCodigoContaTCE) == '') {
                    continue;
                }

                /**
                 * Buscando novas informaoes da fonte de recurso
                 */
                $codigoFonte = '';

                if (!empty($oDadosPlano->c61_codigo)) {
                    $repositorySiconfi = new FonteRecursoSiconfiRepository();
                    $repositorySiconfi->scopeCodigoRecurso($oDadosPlano->c61_codigo);
                    $repositorySiconfi->scopeExercicio($iAnoSessao);
                    $novoRecurso = $repositorySiconfi->first();
                    $codigoFonte = $novoRecurso->getCodigoSiconfi();
                }


                $oDados->dt_AnoCriacao = str_pad($oDadosPlano->c60_anousu, 4, " ");
                $oDados->tp_OrigemSaldo = str_pad($sNaturezaSaldo, 1, " "); // 'M'; // Vem do XML;
                $oDados->cd_RecebeLanc = str_pad($oDadosPlano->recebe_lancamento, 1, " ");
                $oDados->ST_EMPRESA = str_pad($this->sIndicadorEmpresa, 1, " ");
                $oDados->dt_AnoMes = str_pad($oDadosPlano->c60_anousu . $iMes, 6, " ");


                $oDadosPlano->c60_descr = preg_replace('/[`^~\'"]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', tirarAcentos($oDadosPlano->c60_descr)));

                $oDados->nu_SequencialTC = str_pad("", 4, ' ', STR_PAD_LEFT); // Vem do XML
                $oDados->cd_Unidade = str_pad($this->sCodigoTribunal, 4, ' ', STR_PAD_LEFT);
                $oDados->cd_ContaContabil = str_pad($oDadosPlano->c60_estrut, 34, ' ', STR_PAD_RIGHT);
                $oDados->tp_ContaContabil = str_pad(trim($oDadosPlano->tipo_conta), 1, ' ', STR_PAD_LEFT);
                $oDados->nm_ContaContabil = str_pad(trim(substr($oDadosPlano->c60_descr, 0, 49)), 50, " ", STR_PAD_RIGHT);
                $oDados->nu_Nivel = str_pad(trim($oDadosPlano->nivel), 4, " ", STR_PAD_LEFT);
                $oDados->cd_Banco = str_pad(substr($oDadosPlano->banco, 0, 4), 4, ' ', STR_PAD_LEFT);
                $oDados->cd_AgenciaBancaria = str_pad(trim(substr($oDadosPlano->agencia, 0, 12)), 12, ' ', STR_PAD_RIGHT);
                $oDados->cd_ContaBancaria = str_pad(trim(substr($oDadosPlano->conta, ($oDadosPlano->banco == 104 ? 2 : 0), 10)), 10, ' ', STR_PAD_RIGHT);
                $oDados->Reservado_tce1 = str_pad($ReservadoTCE, 34, " ", STR_PAD_LEFT);
                $oDados->Reservado_tce2 = str_pad($ReservadoTCE, 4, " ", STR_PAD_LEFT);
                $oDados->cd_FonteGestor = str_pad($oDadosPlano->c61_codigo, 4, " ", STR_PAD_LEFT);

                if ($iAnoSessao < 2013) {
                    $oDados->codigolinha = 396;
                } else {

                    $oDados->Cd_Atrib_ContaCorrente = '0';
                    $oDados->Cd_Conta_Corrente = str_pad(str_repeat(" ", 30), 30, ' ', STR_PAD_RIGHT);
                    $oDados->de_ContaCorrente = str_pad(str_repeat(" ", 100), 100, ' ', STR_PAD_RIGHT);
                    $oDados->nu_Sequencial_PCASP = str_pad(str_repeat(" ", 5), 5, " ", STR_PAD_LEFT);
                    if ($oDadosPlano->tipo_conta == 1) {
                        $oDados->Cd_Atrib_ContaCorrente = '1';
                        $oDados->Cd_Conta_Corrente = str_pad($oDadosPlano->seq_conta_corrente, 30, ' ', STR_PAD_RIGHT);
                        $oDados->de_ContaCorrente = str_pad($oDadosPlano->descr_conta_corrente, 100, ' ', STR_PAD_RIGHT);
                    }
                    $oDados->nu_Sequencial_PCASP = str_pad($iSeqPCASP, 5, " ", STR_PAD_LEFT);
                    $oDados->nu_Sequencial_PCASP = str_pad($iCodigoContaTCE, 5, " ", STR_PAD_LEFT);
                    if ($oDadosPlano->c60_anousu > 2018) {
                        $oDados->Nm_FonteRecurso = str_pad($codigoFonte, 8, " ", STR_PAD_LEFT);
                    } else {
                        $oDados->Nm_FonteRecurso = str_pad(" ", 8, " ", STR_PAD_LEFT);
                    }

                    $oDados->codigolinha = 669;
                }

                $this->aDados[] = $oDados;
            }
        }

        $this->addLog("===== Fim do Arquivo: " . $this->getNomeArquivo() . "\n");

        return $this->aDados;
    }
}
function tirarAcentos($string)
{
    return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(Ç|ç)/"), explode(" ", "a A e E i I o O u U n N C c"), $string);
}
