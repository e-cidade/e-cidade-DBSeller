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

namespace ECidade\Tributario\Issqn\Simples\Service;

use ECidade\Tributario\Issqn\Simples\Repository\ISSArquivoSimplesRegistroRepository;
use BusinessException;
use cl_arrecad;
use cl_issarqsimplesregerro;
use cl_issbasecaracteristica;
use cl_isscadsimples;
use cl_isscadsimplesbaixa;
use cl_isscalc;
use cl_issvar;
use db_utils;
use Exception;
use stdClass;

class ValidaArquivoSimplesService
{
    private $arquivo;
    private $erros = [];

    /**
     * Construct
     *
     * @param numeric $arquivo Identificador do arquivo (q17_sequencial)
     */
    public function __construct($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    public function execute()
    {
        if (!db_utils::inTransaction()) {
            throw new BusinessException("Não Existe Transação Ativa");
        }

        $issArqSimplesRegRepository = new ISSArquivoSimplesRegistroRepository();
        $issArqSimplesRegRepository->limparErrosExistentesDoArquivo($this->arquivo);
        $registros = $issArqSimplesRegRepository->getRegistrosByArquivo($this->arquivo);

        foreach ($registros as $registro) {
            $cgmVinculados = $registro->getCgmVinculos();
            $inscricoesVinculadas = $registro->getInscricaoVinculos();
            $dadosParaPagamento = $registro->getDadosParaPagamento();
            $competenciaRegistro = "{$registro->q23_mesusu}/{$registro->q23_anousu}";
            $sequencialRegistro = $registro->q23_sequencial;

            /**
             * ==============================
             * Validacoes do cadastro do CGM
             * ==============================
             */
            if (empty($dadosParaPagamento->cgm)) {
                $message = "Não existe no cadastro do CGM";
                $this->adicionarErro('ERRO', $sequencialRegistro, $dadosParaPagamento, $message, 'semCgm');
                continue;
            }

            if (count($cgmVinculados) > 1) {
                $cgmDuplicados = implode(',', $cgmVinculados);
                $message = "Cadastro nos cgms ({$cgmDuplicados})";
                $this->adicionarErro('ERRO', $sequencialRegistro, $dadosParaPagamento, $message, 'cnpjDuplicado');
                continue;
            }

            if ($registro->q23_acao == 1) {
                $message = "Será Lançado ISSQN Complementar para {$competenciaRegistro}";
                $this->adicionarErro('AVISO', $sequencialRegistro, $dadosParaPagamento, $message, 'avisoComplementar');
                continue;
            }

            /**
             * ====================================
             * Validacoes do cadastro de Inscricao
             * ====================================
             */
            if (empty($dadosParaPagamento->inscricao)) {
                $message = "Sem Inscrição Ativa.";
                $this->adicionarErro('ERRO', $sequencialRegistro, $dadosParaPagamento, $message, 'semInscricaoAtiva');
                continue;
            }

            if (count($inscricoesVinculadas) > 1) {
                $inscricoesDuplicadas = implode(',', $inscricoesVinculadas);
                $message = "Cadastro com Mais de um alvará ({$inscricoesDuplicadas}) ou sem vínculo.";
                $this->adicionarErro('ERRO', $sequencialRegistro, $dadosParaPagamento, $message, 'variasinscricoes');
                continue;
            }

            /**
             * =======================================
             * Validacoes Referente a calculo do ISS
             * =======================================
             */
            $oDaoIssCalc = new cl_isscalc();
            $sWhereIssCalc = " q01_anousu = $registro->q23_anousu ";
            $sWhereIssCalc .= " and q01_inscr in ($dadosParaPagamento->inscricao) ";
            $sWhereIssCalc .= " and q01_cadcal = 3 ";
            $sSqlIssCalc = $oDaoIssCalc->sql_query(null, null, null, null, null, "*", null, $sWhereIssCalc);
            $rsIssCalc = db_query($sSqlIssCalc);

            if (pg_num_rows($rsIssCalc) == 0) {
                $message = "Será lançado ISS Complementar para  {$competenciaRegistro}";
                $this->adicionarErro('AVISO', $sequencialRegistro, $dadosParaPagamento, $message, 'avisoComplementar');
            }

            /**
             * =======================================================================
             * Validacoes da competencia do registro se não for MEI e não tiver baixa
             * (ou baixa posterior ao pagamento) lançar como complementar
             * =======================================================================
             */
            $lMEI = false;
            $oDaoIsscadsimples = new cl_isscadsimples();
            $sWhere = " cgm.z01_cgccpf = '{$dadosParaPagamento->cnpj}' ";
            $sSqlMEI = $oDaoIsscadsimples->sql_query(null, 'q02_inscr, q38_sequencial, q38_categoria', null, $sWhere);
            $rsMEI = db_query($sSqlMEI);

            if (!$rsMEI) {
                throw new \Exception("Erro verificar Inscrição MEI. ".pg_last_error());
            }

            if (pg_num_rows($rsMEI) > 0) {
                $sCategoria = db_utils::fieldsMemory($rsMEI, 0)->q38_categoria;
                $iIsscadsimples = (int) db_utils::fieldsMemory($rsMEI, 0)->q38_sequencial;

                $oDaoIsscadsimplesbaixa = new cl_isscadsimplesbaixa();
                $sWhere = " q39_isscadsimples = {$iIsscadsimples} ";
                $sSqlBaixaMEI = $oDaoIsscadsimplesbaixa->sql_query(null, 'q39_sequencial, q39_dtbaixa', null, $sWhere);
                $rsBaixaMEI = db_query($sSqlBaixaMEI);

                if (!$rsBaixaMEI) {
                    throw new Exception("Erro verificar baixa Inscrição MEI. ".pg_last_error());
                }

                $dDtBaixaMEI = "";
                $dDtPagamento = date('Ymd', strtotime($registro->q23_dtarrec));

                if (pg_num_rows($rsBaixaMEI) > 0) {
                    $dDtBaixaMEI = date('Ymd', strtotime(db_utils::fieldsMemory($rsBaixaMEI, 0)->q39_dtbaixa));
                }

                if ($sCategoria == '3' && ($dDtBaixaMEI == "" || $dDtBaixaMEI > $dDtPagamento)) {
                    $lMEI = true;
                }
            }

            $anoPag = substr($registro->q23_dtarrec, 0, 4);
            $mesPag = substr($registro->q23_dtarrec, 5, 2);
            if ($registro->q23_anousu == $anoPag && $registro->q23_mesusu == $mesPag && (!$lMEI)) {
                $message = "Competência atual. Será lançado ISS Complementar para {$competenciaRegistro}";
                $this->adicionarErro('AVISO', $sequencialRegistro, $dadosParaPagamento, $message, 'avisoComplementar');
            }

            /**
             * =================================================
             * Testando se existe issvar para o mes/competencia
             * =================================================
             */
            $oDaoIssvar = new cl_issvar();
            $sSqlIssvar = $oDaoIssvar->sql_issvar_isscalc_inscr_comp(
                $dadosParaPagamento->inscricao,
                $registro->q23_anousu,
                $registro->q23_mesusu
            );

            $rsVar = db_query($sSqlIssvar);
            if (pg_num_rows($rsVar) < 1) {
                $message = "Sem lançamento ISS variavel para {$competenciaRegistro}";
                $this->adicionarErro('ERRO', $sequencialRegistro, $dadosParaPagamento, $message, 'semInscricaoAtiva');
                continue;
            }

            $oDaoArrecad  = new cl_arrecad();
            $oIssVar = db_utils::fieldsMemory($rsVar, 0);
            $sCamposArrecad = "k00_numpre, k00_numpar, k00_hist, k00_dtoper";
            $sWhereArrecad = " k00_numpre = " . $oIssVar->q05_numpre;
            $sWhereArrecad .= " and k00_numpar = " . $oIssVar->q05_numpar;

            $sSqlArrecad = $oDaoArrecad->sql_query_file(null, $sCamposArrecad, null, $sWhereArrecad);
            $rsArrecad = db_query($sSqlArrecad);

            if (!$rsArrecad) {
                throw new Exception("Erro ao buscar dados do Arrecad:" . pg_last_error());
            }

            if (pg_num_rows($rsArrecad) != 1) {
                $message = "será lançado ISS Complementar para  {$competenciaRegistro}";
                $this->adicionarErro('AVISO', $sequencialRegistro, $dadosParaPagamento, $message, 'avisoComplementar');
            }

            // Verifica se o contribuinte tem caracteristica de emissor de NFS-e
            $oDaoIssbasecaracteristica = new cl_issbasecaracteristica();
            $sSqlVerificaValidadeCaracteristica = $oDaoIssbasecaracteristica->sql_query_valida_caracteristica(
                $dadosParaPagamento->inscricao,
                10
            );
            $rsIssbasecaracteristica = db_query($sSqlVerificaValidadeCaracteristica);
            if (!$rsIssbasecaracteristica) {
                throw new Exception("Erro ao buscar caracteristica de emissor de NFS-e do Alvará.");
            }

            /**
            * Possui caracteristica de emissor de NFS-e
            */
            if (pg_num_rows($rsIssbasecaracteristica) > 0) {
                $oDaoIsscadsimples = new cl_isscadsimples();
                $sCompetencia = "{$registro->q23_anousu}-{$registro->q23_mesusu}";
                $sSqlVerificaValidadeSimples = $oDaoIsscadsimples->sql_query_valida_competencia(
                    $dadosParaPagamento->inscricao,
                    $sCompetencia
                );
                $rsIsscadsimples = db_query($sSqlVerificaValidadeSimples);
                if (!$rsIsscadsimples) {
                    throw new Exception("Erro ao buscar dados do Simples.");
                }

                // Possui cadastro no simples
                if (pg_num_rows($rsIsscadsimples) > 0) {
                    $aRegistrosNoSimples = db_utils::getCollectionByRecord($rsIsscadsimples);
                    $lCompetenciaValida  = false;

                    // Verificamos se está ativo no simples na competencia de pagamento
                    foreach ($aRegistrosNoSimples as $oRegistroNoSimples) {
                        // Cadastro do simples sem data de baixa
                        if ($oRegistroNoSimples->validade == 't') {
                            $lCompetenciaValida = true;
                        }
                    }

                    // Quando não encontrar nenhuma competencia valida para o pagamento
                    if (!$lCompetenciaValida) {
                        $message = "Data de baixa informada no cadastro do Simples ";
                        $message .= "para competência {$competenciaRegistro}";
                        $this->adicionarErro(
                            'ERRO',
                            $sequencialRegistro,
                            $dadosParaPagamento,
                            $message,
                            'semInscricaoAtiva'
                        );
                        continue;
                    }
                }
            }
        }

        $this->salvarErros();
    }

    private function adicionarErro($tipo, $registro, $dados, $detalhe, $erro)
    {
        $message = "";
        if (!empty($dados->cnpj)) {
            $message .= "CNPJ - {$dados->cnpj}";
        }

        if (!empty($dados->cgm)) {
            $message .= ", CGM - {$dados->cgm}";
        }

        if (!empty($dados->inscricao)) {
            $message .= ", Álvara - {$dados->inscricao}";
        }

        $message .= ": {$detalhe}";
        $oErro = new stdClass();
        $oErro->sTipo = $tipo;
        $oErro->iRegistro = $registro;
        $oErro->sCnpj = $dados->cnpj;
        $oErro->sDetalhe = urlencode($message);
        $oErro->sErro = $erro;
        $this->erros[$registro] = $oErro;
    }

    public function getErros()
    {
        return $this->erros;
    }

    private function salvarErros()
    {
        foreach ($this->erros as $oInconsistencia) {
            $oDaoIssArqSimplesRegErro = new cl_issarqsimplesregerro();
            $oDaoIssArqSimplesRegErro->q49_sequencial = $oInconsistencia->iRegistro;
            $oDaoIssArqSimplesRegErro->q49_erro = $oInconsistencia->sDetalhe;
            $oDaoIssArqSimplesRegErro->q49_tipo = $oInconsistencia->sTipo == "AVISO" ? 2 : 1;
            $oDaoIssArqSimplesRegErro->incluir($oDaoIssArqSimplesRegErro->q49_sequencial);

            if ($oDaoIssArqSimplesRegErro->erro_status == "0") {
                throw new Exception($oDaoIssArqSimplesRegErro->erro_msg."");
            }
        }
    }
}
