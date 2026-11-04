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


require_once  modification("SigfisArquivoBase.model.php");
require_once  modification("model/contabilidade/arquivos/sigfis/SigfisArquivoBase.model.php");
require_once  modification("model/contabilidade/arquivos/sigfis/SigfisVinculoRecurso.model.php");

use ECidade\Financeiro\Orcamento\Repository\FonteRecursoSiconfiRepository;

/**
 * Classe que processa as informações para serem inseridas no
 * arquivo Empenho.txt
 * @author vinicius.silva@dbseller.com.br
 * @package contabilidade
 * @subpackage sigfis
 */

class SigfisArquivoEmpenho extends SigfisArquivoBase implements iPadArquivoTXTBase {

    protected $iCodigoLayout = 203;

    protected $sNomeArquivo  = 'Empenho';

    /**
     * @return array
     * @throws Exception
     */
    public function gerarDados() {

        $oDaoEmpempenho = db_utils::getDao('empempenho');
        $iAnoSessao     = db_getsession('DB_anousu');
        $iInstituicaoSessao = db_getsession('DB_instit');

        $this->setCodigoLayout(203);
        if( $iAnoSessao < 2013 ){
            $this->setCodigoLayout(126);
        }

        if ($iAnoSessao >= 2018) {
            $this->setCodigoLayout(300);
        }

        $sCampos  = " db_config.codtrib,                             ";
        $sCampos .= " orcdotacao.o58_orgao,                          ";
        $sCampos .= " orcdotacao.o58_unidade,                        ";
        $sCampos .= " empempenho.e60_codemp,                         ";

        $instituicao = InstituicaoRepository::getInstituicaoSessao();
        if ((int)$instituicao->getCodigoCliente() === 7107) {

            $sCampos .= "   (SELECT DISTINCT ac16_numero||'/'||ac16_anousu AS acordo
                           FROM plugins.placordoexecucao
                           INNER JOIN plugins.placordoitemdotacaovalormes ON pac01_sequencial = pac02_placordoexecucao
                           INNER JOIN plugins.solicitacordoitemmes ON sac02_acordo = pac01_acordo
                           AND sac02_mes = pac02_mes
                           AND sac02_acordoitem = pac01_item
                           INNER JOIN solicitem ON pc11_codigo = sac02_solicitaitem
                           INNER JOIN acordo ON pac01_acordo = ac16_sequencial
                           INNER JOIN pcprocitem ON pcprocitem.pc81_solicitem = solicitem.pc11_codigo
                           INNER JOIN empautitempcprocitem ON empautitempcprocitem.e73_pcprocitem = pcprocitem.pc81_codprocitem
                           INNER JOIN empautitem ON empautitem.e55_autori = empautitempcprocitem.e73_autori
                           AND empautitem.e55_sequen = empautitempcprocitem.e73_sequen
                           INNER JOIN empautoriza ON empautoriza.e54_autori= empautitem.e55_autori
                           INNER JOIN empempaut ON empempaut.e61_autori = empautitem.e55_autori
                           INNER JOIN empempenho a ON empempenho.e60_numemp = empempaut.e61_numemp
                           WHERE a.e60_numemp = empempenho.e60_numemp
                             AND a.e60_anousu = empempenho.e60_anousu
                             AND a.e60_instit = empempenho.e60_instit ) AS acordo, ";
            $sCampos .= "(SELECT DISTINCT ac16_numero||'/'||ac16_anousu
                           FROM empempenhocontrato
                            inner join acordo on ac16_sequencial = e100_acordo
                           WHERE e100_numemp = empempenho.e60_numemp) AS acordo2, ";
        }

        $sCampos .= " empempenho.e60_numerol,                        ";
        $sCampos .= " empempenho.e60_anousu,                         ";
        $sCampos .= " orcprojativ.o55_tipo,                          ";
        $sCampos .= " orcdotacao.o58_projativ,                       ";
        $sCampos .= " orcdotacao.o58_codigo,                         ";
        $sCampos .= " orcelemento.o56_elemento,                      ";
        $sCampos .= " empempenho.e60_vlremp,                         ";
        $sCampos .= " to_ascii(empempenho.e60_resumo,'latin2') as e60_resumo, ";
        $sCampos .= " CASE                                           ";
        $sCampos .= "   WHEN empempenho.e60_codtipo = 1 THEN 3       ";
        $sCampos .= "   WHEN empempenho.e60_codtipo = 2 THEN 2       ";
        $sCampos .= "   WHEN empempenho.e60_codtipo = 3 THEN 3       ";
        $sCampos .= "   WHEN empempenho.e60_codtipo = 4 THEN 1       ";
        $sCampos .= " END AS e60_codtipo,                            ";
        $sCampos .= " e60_codcom,                                    ";
        $sCampos .= " empempenho.e60_emiss,                          ";
        $sCampos .= " cgm.z01_nome,                                  ";
        $sCampos .= " empempenho.e60_emiss,                          ";
        $sCampos .= " cgm.z01_cgccpf,                                ";
        $sCampos .= " CASE                                           ";
        $sCampos .= "   WHEN char_length(cgm.z01_cgccpf) = 11 THEN 1 ";
        $sCampos .= "   WHEN char_length(cgm.z01_cgccpf) = 14 THEN 2 ";
        $sCampos .= "   ELSE 1                                       ";
        $sCampos .= " END AS tipo_pessoa_credor,                     ";
        $sCampos .= " orcdotacao.o58_orgao,                          ";
        $sCampos .= " orcdotacao.o58_funcao,                         ";
        $sCampos .= " orcdotacao.o58_subfuncao,                      ";
        $sCampos .= " orcdotacao.o58_programa,                       ";
        $sCampos .= " empempenho.e60_numemp                     ";

        $sWhereBuscaEmpenhos  = "     empempenho.e60_anousu = {$iAnoSessao}                                            ";
        $sWhereBuscaEmpenhos .= " and empempenho.e60_emiss between '{$this->dtDataInicial}' and '{$this->dtDataFinal}' ";
        $sWhereBuscaEmpenhos .= " and empempenho.e60_instit = {$iInstituicaoSessao}                                    ";
        $sSqlBuscaEmpenhos    = $oDaoEmpempenho->sql_query_buscaempenhos(null, $sCampos, null, $sWhereBuscaEmpenhos);


        $rsSqlBuscaEmpenhos   = $oDaoEmpempenho->sql_record($sSqlBuscaEmpenhos);
        $oEmpenhos            = db_utils::getCollectionByRecord($rsSqlBuscaEmpenhos);
        $this->addLog($sSqlBuscaEmpenhos);
        if (count($oEmpenhos) > 0) {

            if (empty($this->sCodigoTribunal)) {
                throw new Exception("O código do tribunal deve ser informado para geração do arquivo");
            }

            foreach ($oEmpenhos as $oEmpenho) {

                $numemp = $oEmpenho->e60_numemp;
                $rsProc = $oEmpenho->e60_numerol;

                $rsProcDispensa = '';
                if ( $oEmpenho->e60_codcom == 5 or $oEmpenho->e60_codcom == 7 ) {
                    if ( $oEmpenho->e60_codcom == 5 ) {
                        $rsProcDispensa = $rsProc;
                    }
                    $rsProc = '';
                }

                /**
                 * forçando o decimal nos casos onde o valor do empenho vem inteiro
                 */
                $fValorEmpenhoDecimal = db_formatar($oEmpenho->e60_vlremp, 'p');
                $iValorEmpenhoSemSeparador = str_replace('.', '', $fValorEmpenhoDecimal);

                /**
                 * recuperando ano e mes
                 */
                $aDadosData     = explode('-', $oEmpenho->e60_emiss);
                $sDataFormatada = $aDadosData[0].$aDadosData[1];

                /**
                 * Buscando novas informaoes da fonte de recurso
                 */
                $repositorySiconfi = new FonteRecursoSiconfiRepository();
                $repositorySiconfi->scopeCodigoRecurso($oEmpenho->o58_codigo);
                $repositorySiconfi->scopeExercicio($iAnoSessao);
                $novoRecurso = $repositorySiconfi->first();

                if (empty($novoRecurso)) {
                    $sErroLog  = "Recurso {$oEmpenho->o58_codigo} - {$oEmpenho->o15_descr} ";
                    $sErroLog .= "Sem Vinculo com Dotao {$oEmpenho->o58_coddot} do SIGFIS.\n";
                    $this->addLog($sErroLog);
                }


                $acordo = '';
                if (!empty($oEmpenho->acordo) && !empty($oEmpenho->acordo2)) {

                    if($oEmpenho->acordo != "" ){
                        $acordo = $oEmpenho->acordo;
                    }else{
                        $acordo = $oEmpenho->acordo2;
                    }
                }



                /**
                 * Manipulmos o campo e60_resumo eliminando quebras de linha
                 */
                $sHistorico  = str_replace(array("\n", "\r", "<br>"), " ", trim($oEmpenho->e60_resumo));

                if ($oEmpenho->o55_tipo == 0 or $oEmpenho->o55_tipo == 9) $oEmpenho->o55_tipo = 3;

                $oDadosLinha = new stdClass();
                $oDadosLinha->cd_Unidade                 = str_pad($this->sCodigoTribunal,              4, ' ', STR_PAD_LEFT);

                $oDadosLinha->cd_UnidadeOrcamentaria     = str_pad($oEmpenho->o58_unidade,              4, ' ', STR_PAD_LEFT);
                $oDadosLinha->nu_Empenho                 = str_pad($oEmpenho->e60_codemp,              10, ' ', STR_PAD_RIGHT);

                $oDadosLinha->nu_ProcessoLicitatorio     = str_pad($rsProc, 36, ' ', STR_PAD_RIGHT );
                $oDadosLinha->dt_Ano                     = $oEmpenho->e60_anousu;
                $oDadosLinha->Tp_ProjetoAtividade        = str_pad($oEmpenho->o55_tipo,                 1, ' ', STR_PAD_LEFT);
                $oDadosLinha->nu_ProjetoAtividade        = str_pad(substr($oEmpenho->o58_projativ, 0 ,4),  4, ' ', STR_PAD_LEFT);
                $oDadosLinha->cd_FonteRecurso            = str_pad(' ', 4, ' ', STR_PAD_LEFT);
                if ($oEmpenho->e60_anousu <= 2018) {
                    $oDadosLinha->cd_FonteRecurso            = str_pad($oEmpenho->o58_codigo, 4, ' ', STR_PAD_LEFT);
                }
                $oDadosLinha->Reservado_tce              = str_repeat('0', 14);
                $oDadosLinha->cd_Elemento                = str_pad(substr($oEmpenho->o56_elemento, 1, 8), 8, ' ', STR_PAD_LEFT);
                $oDadosLinha->vl_Empenho                 = str_pad($iValorEmpenhoSemSeparador,         16, ' ', STR_PAD_LEFT);
                $oDadosLinha->de_Historico               = str_pad(substr(trim(addslashes($sHistorico)), 0, 255), 255, ' ', STR_PAD_RIGHT);
                $oDadosLinha->Tp_Empenho                 = $oEmpenho->e60_codtipo;
                $oDadosLinha->dt_Empenho                 = str_replace('/', '', db_formatar($oEmpenho->e60_emiss,"d"));
                $oDadosLinha->nu_Contrato                = str_pad($acordo,                                 16, ' ', STR_PAD_RIGHT); // ?...
                $oDadosLinha->nm_Credor                  = str_pad(substr($oEmpenho->z01_nome, 0, 50), 50, ' ', STR_PAD_RIGHT);
                $oDadosLinha->dt_AnoMes                  = $sDataFormatada;
                $oDadosLinha->nu_CGC_Credor              = str_pad($oEmpenho->z01_cgccpf,              14, ' ', STR_PAD_RIGHT);
                $oDadosLinha->Tp_Pessoa                  = $oEmpenho->tipo_pessoa_credor;
                $oDadosLinha->cd_Orgao                   = str_pad($oEmpenho->o58_orgao,                4, ' ', STR_PAD_LEFT);
                $oDadosLinha->cd_Dispensa                = str_pad($rsProcDispensa,                                 16, ' ', STR_PAD_RIGHT);
                $oDadosLinha->Reservado_tce2             = '0';
                $oDadosLinha->cd_Funcao                  = str_pad($oEmpenho->o58_funcao,               2, ' ', STR_PAD_LEFT);
                $oDadosLinha->cd_Programa                = str_pad($oEmpenho->o58_subfuncao,            4, ' ', STR_PAD_LEFT);
                $oDadosLinha->cd_SubPrograma             = str_pad($oEmpenho->o58_programa,             4, ' ', STR_PAD_LEFT);
                $oDadosLinha->St_contrato_aplicavel      = 'N'; // padrão
                $oDadosLinha->St_licitacao_sujeito       = ( trim($oDadosLinha->nu_ProcessoLicitatorio) == ""?'N':'S' ); // padrão
                $oDadosLinha->NU_CONVENIO                = str_pad('',                                 16, ' ', STR_PAD_RIGHT); // ?...
                $oDadosLinha->NU_TERMOPARCERIA           = str_pad('',                                 16, ' ', STR_PAD_RIGHT); // ?...
                $oDadosLinha->ST_CONVENIO_APLICAVEL      = 'N'; // padrão
                $oDadosLinha->ST_TERMOPARCERIA_APLICAVEL = 'N'; // padrão
                $oDadosLinha->Nu_Aditivo                 = str_pad('',                                 16, ' ', STR_PAD_RIGHT); // ?...

                $oDadosLinha->RESERVADO_TCE_3 = '  ';
                $oDadosLinha->RESERVADO_TCE_4 = ' ';

                $oDadosJustificativaEmpenho = self::getJustificativas( $numemp );
                $oDadosLinha->TPJUSTIFICATIVACONTRATO = str_pad($oDadosJustificativaEmpenho->tipoJustificativaContrato,      2, '0', STR_PAD_LEFT);
                $oDadosLinha->DEJUSTIFICATIVACONTRATO = str_pad($oDadosJustificativaEmpenho->descricaoJustificativaContrato, 255, ' ', STR_PAD_RIGHT);
                $oDadosLinha->TPJUSTIFICATIVALICITA   = str_pad($oDadosJustificativaEmpenho->tipoJustificativaLicitacao,     2, '0', STR_PAD_LEFT);
                $oDadosLinha->DEJUSTIFICATIVALICITA   = str_pad($oDadosJustificativaEmpenho->descricaoJustificativaLicitacao,255, ' ', STR_PAD_RIGHT);

                if($oEmpenho->e60_anousu > 2018){
                   $oDadosLinha->Nm_FonteRecurso = str_pad($novoRecurso->getCodigoSiconfi(), 8, ' ', STR_PAD_RIGHT);
                }else{
                    $oDadosLinha->Nm_FonteRecurso = str_pad(" ",8, ' ', STR_PAD_LEFT);
                    $oDadosLinha->Nm_FonteRecurso = str_pad(" ",8, ' ', STR_PAD_RIGHT);
                }
                $oDadosLinha->COVID_19 = '';
                $oDadosLinha->codigolinha = $iAnoSessao >= 2018 ? 1013 : 668;
                if(db_getsession('DB_anousu') < 2013 ) {
                    $oDadosLinha->codigolinha = 413;
                }

                $this->aDados[] = $oDadosLinha;
            }

        }

        return $this->aDados;
    }

    /**
     * @param $sequencialEmpenho
     * @return stdClass
     * @throws DBException
     */
    protected static function getJustificativas($sequencialEmpenho)
    {

        $daoJustificativa = new cl_empenhojustificativacontratolicitacao();
        $buscaJustificativa = $daoJustificativa->sql_query_file(null, "*", null, "e08_empempenho = {$sequencialEmpenho}");
        $resJustificativa = db_query($buscaJustificativa);
        if (!$resJustificativa) {
            throw new DBException("Ocorreu um erro ao consultar as justificativas de contrato e licitação.");
        }

        $stdDados = (object)array(
            'tipoJustificativaLicitacao'      => '',
            'descricaoJustificativaLicitacao' => '',
            'tipoJustificativaContrato'       => '',
            'descricaoJustificativaContrato'  => ''
        );

        if (pg_num_rows($resJustificativa) === 0) {
            return $stdDados;
        }

        $stdJustificativa = db_utils::fieldsMemory($resJustificativa, 0);
        $stdJustificativa->tipoJustificativaLicitacao      = $stdJustificativa->e08_tipojustificativalicitacao;
        $stdJustificativa->descricaoJustificativaLicitacao = $stdJustificativa->e08_descricaojustificativalicitacao;
        $stdJustificativa->tipoJustificativaContrato       = $stdJustificativa->e08_tipojustificativacontrato;
        $stdJustificativa->descricaoJustificativaContrato  = $stdJustificativa->e08_descricaojustificativacontrato;
        return $stdJustificativa;
    }
}
