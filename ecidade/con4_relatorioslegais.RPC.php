<?php
/**
 * E-cidade Software Publico para Gestão Municipal
 *   Copyright (C) 2009 DBSeller Serviços de Informática Ltda
 *                          www.dbseller.com.br
 *                          e-cidade@dbseller.com.br
 *   Este programa é software livre; você pode redistribuí-lo e/ou
 *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *   publicada pela Free Software Foundation; tanto a versão 2 da
 *   Licença como (a seu critério) qualquer versão mais nova.
 *   Este programa e distribuído na expectativa de ser útil, mas SEM
 *   QUALQUER GARANTIA; sem mesmo a garantia implícita de
 *   COMERCIALIZAÇÃO ou de ADEQUAÇÃO A QUALQUER PROPÓSITO EM
 *   PARTICULAR. Consulte a Licença Pública Geral GNU para obter mais
 *   detalhes.
 *   Você deve ter recebido uma cópia da Licença Pública Geral GNU
 *   junto com este programa; se não, escreva para a Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307, USA.
 *   Cópia da licença no diretório licenca/licenca_en.txt
 *                                 licenca/licenca_pt.txt.
 */

require_once modification('dbforms/db_funcoes.php');
require_once modification('libs/JSON.php');
require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_utils.php');
require_once modification('std/db_stdClass.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_libcontabilidade.php');
require_once modification('libs/db_liborcamento.php');

use ECidade\Configuracao\RelatorioLegal\Enum\OrigemDadosEnum;
use ECidade\Configuracao\RelatorioLegal\Servico\ColunaEstruturalServico;
use ECidade\Configuracao\RelatorioLegal\Servico\Importar;
use ECidade\Financeiro\Contabilidade\Relatorio\RelatoriosLegaisBaseMSC;

$oJson = new services_json();
$oParam = $oJson->decode(str_replace('\\', '', $_POST['json']));
$oRetorno = new stdClass();
$oRetorno->status = 1;
$oRetorno->erro = false;
$oRetorno->message = '';

try {
    switch ($oParam->exec) {
        case 'getVariaveis':
            $oVariaveis = new stdClass();
            $oVariaveis->campos_relatorios = [];
            $oVariaveis->colunas_linha = [];

            switch ($oParam->iOrigemDados) {
                case OrigemDadosEnum::BALANCETE_DESPESA:
                    $oVariaveis->campos_relatorios = RelatoriosLegaisBase::$aCamposDespesa;
                    break;
                case OrigemDadosEnum::BALANCETE_RECEITA:
                    $oVariaveis->campos_relatorios = RelatoriosLegaisBase::$aCamposReceita;
                    break;
                case OrigemDadosEnum::BALANCETE_VERIFICACAO:
                    $oVariaveis->campos_relatorios = RelatoriosLegaisBase::$aCamposVerificacao;
                    break;
                case OrigemDadosEnum::RESTOS_PAGAR:
                    $oVariaveis->campos_relatorios = RelatoriosLegaisBase::$aCamposRestoPagar;
                    break;
                case OrigemDadosEnum::MSC:
                    $oVariaveis->campos_relatorios = RelatoriosLegaisBaseMSC::$camposMSC;
                    break;
            }

            /**
             * Buscamos todas as variaveis que são as Colunas Cadastradas na linha do Relatorio.
             */
            $oLinhaRelatorio = new linhaRelatorioContabil($oParam->iCodigoRelatorio, $oParam->iCodigoLinha);
            foreach ($oLinhaRelatorio->getCols() as $oColuna) {
                if (!in_array($oColuna->o115_nomecoluna, $oVariaveis->colunas_linha)) {
                    $oVariaveis->colunas_linha[] = $oColuna->o115_nomecoluna;
                }
            }
            $oRetorno->oListaVariaveis = $oVariaveis;
            break;

        case 'getRelatorios':
            $oDaoOrcParamRel = new cl_orcparamrel();

            $sWhere = '';
            if (!empty($oParam->iTipo)) {
                $sWhere = " o42_orcparamrelgrupo = {$oParam->iTipo} ";
            }
            $oRetorno->aRelatorios = [];
            $sSqlRelatorios = $oDaoOrcParamRel->sql_query_file(null, '*', 'o42_codparrel', $sWhere);
            $rsRelatorios = $oDaoOrcParamRel->sql_record($sSqlRelatorios);
            if ($rsRelatorios) {
                for ($iRelatorio = 0; $iRelatorio < $oDaoOrcParamRel->numrows; ++$iRelatorio) {
                    $oDadosRelatorio = db_utils::fieldsMemory($rsRelatorios, $iRelatorio);

                    $oStdRelatorio = new stdClass();
                    $oStdRelatorio->iCodigo = $oDadosRelatorio->o42_codparrel;
                    $oStdRelatorio->sNome = urlencode($oDadosRelatorio->o42_descrrel);
                    unset($oDadosRelatorio);
                    $oRetorno->aRelatorios[] = $oStdRelatorio;
                }
            }
            break;

        case 'getPeriodosDoRelatorio':
            $oRetorno->aPeriodos = [];
            $oRelatorio = new relatorioContabil($oParam->iCodigo, false);
            $aPeriodos = $oRelatorio->getPeriodos();
            foreach ($aPeriodos as $oPeriodo) {
                $oStdPeriodo = new stdClass();
                $oStdPeriodo->iCodigo = $oPeriodo->o114_sequencial;
                $oStdPeriodo->sDescricao = urlencode($oPeriodo->o114_descricao);
                $oRetorno->aPeriodos[] = $oStdPeriodo;
            }
            break;

        case 'processarConferencia':
            $oConsistentencia = new ConsistenciaContabil(
                db_getsession('DB_anousu'),
                $oParam->iCodigoRelatorio,
                $oParam->iCodigoPeriodo
            );

            $oConsistentencia->setInstituicoes(implode(',', $oParam->aInstituicoes));
            $aLinhas = $oConsistentencia->getDados();
            $oRetorno->arquivo = urlencode($oConsistentencia->gerarCSV());

            foreach ($aLinhas as $oLinha) {
                $oLinha->descricao = urlencode($oLinha->descricao);
                foreach ($oLinha->colunas as $oColuna) {
                    $oColuna->descricao = urlencode($oColuna->descricao);
                }
            }

            $oRetorno->aLinhasConsistencia = $aLinhas;
            break;

        case 'importarRelatorio':
            db_inicio_transacao();

            if (empty($oParam->sCaminhoArquivo)) {
                throw new ParameterException('Caminho do arquivo não informado.');
            }

            $sCaminhoArquivo = $oParam->sCaminhoArquivo;

            if (!file_exists($sCaminhoArquivo)) {
                throw new Exception('Arquivo não encontrado para o caminho informado.');
            }

            if (pathinfo($sCaminhoArquivo, PATHINFO_EXTENSION) !== 'json') {
                throw new Exception('Arquivo informado deve ser do tipo Json.');
            }


            if (!empty($oParam->iCodigoRelatorio)) {
                excluirRelatorio($oParam->iCodigoRelatorio);
            }
            $oImportacaoRelatorio = new Importar(file_get_contents($sCaminhoArquivo));
            $oImportacaoRelatorio->processar();

            db_fim_transacao();
            $oRetorno->message = 'Relatório importado com sucesso!';
            break;
        case 'buscarContasVinculasColuna':
            if (empty($oParam->coluna)) {
                throw new Exception('Código da coluna não informado.');
            }

            if (empty($oParam->ano)) {
                throw new Exception('Ano não informado.');
            }

            $service = new ColunaEstruturalServico($oParam);
            $contas = $service->buscarColunaEstruturalPorColuna();

            $oRetorno->contas = [];

            foreach ($contas as $conta) {
                $oRetorno->contas[] = $conta->toArray();
            }
            break;
        case 'getContas':
            $anoSessao = db_getsession('DB_anousu');

            $sql = "
                SELECT DISTINCT c60_estrut AS estrutural
                FROM conplano
                 JOIN conplanoreduz ON conplanoreduz.c61_codcon = c60_codcon AND conplanoreduz.c61_anousu = c60_anousu
                 INNER JOIN contabilidade.conplanoatributos
                                    ON conplano.c60_codcon = contabilidade.conplanoatributos.c120_conplano AND
                                       conplano.c60_anousu = contabilidade.conplanoatributos.c120_anousu
                         INNER JOIN conplanosistema
                              ON conplanosistema.c122_sequencial = contabilidade.conplanoatributos.c120_conplanosistema
                WHERE c60_anousu = {$anoSessao}
                  AND c60_estrut ILIKE '{$oParam->estrutural}%'
                  AND c120_conplanosistema > 1
                ORDER BY c60_estrut;
            ";

            $rs = db_query($sql);

            if (!$rs) {
                throw new Exception('Não foi possível buscar as contas.');
            }

            $oRetorno->estruturais = [];

            if (pg_num_rows($rs) === 0) {
                $oRetorno->estruturais[] = $oParam->estrutural;
            } else {
                while ($conta = pg_fetch_object($rs)) {
                    $oRetorno->estruturais[] = $conta->estrutural;
                }
            }

            break;
        case 'buscarPeriodosRelatorio':
            if (empty($oParam->codigoRelatorio)) {
                throw new Exception('Código do relatório não informado.');
            }

            $campos = ' o114_sequencial as codigo, o114_descricao as descricao, ';
            $campos .= " (select 1 from orcparamrelperiodos
                                  where orcparamrelperiodos.o113_periodo = periodo.o114_sequencial
                                    AND o113_orcparamrel = {$oParam->codigoRelatorio} limit 1) as periodo_relatorio ";
            $ordem = 'o114_sequencial, o114_ordem';
            $daoPeriodos = new cl_periodo();
            $sql = $daoPeriodos->sql_query_file(null, $campos, $ordem);
            $rs = db_query($sql);

            if (!$rs) {
                throw new Exception('Erro ao buscar os períodos');
            }

            $oRetorno->periodos = db_utils::getCollectionByRecord($rs);
            break;

        case 'salvarPeriodosRelatorio':
            if (empty($oParam->codigoRelatorio)) {
                throw new Exception('Código do relatório não informado.');
            }

            db_inicio_transacao();

            $daoRelatorioPeriodos = new cl_orcparamrelperiodos();
            $daoRelatorioPeriodos->excluir(null, "o113_orcparamrel = {$oParam->codigoRelatorio}");
            if ($daoRelatorioPeriodos->erro_status == '0') {
                throw new Exception('Erro ao remover o(s) período(s) vinculado(s) ao relatório.');
            }

            foreach ($oParam->periodos as $codigoPeriodo) {
                $daoRelatorioPeriodos->o113_sequencial = null;
                $daoRelatorioPeriodos->o113_periodo = $codigoPeriodo;
                $daoRelatorioPeriodos->o113_orcparamrel = $oParam->codigoRelatorio;
                $daoRelatorioPeriodos->incluir(null);

                if ($daoRelatorioPeriodos->erro_status == '0') {
                    throw new Exception('Erro ao vincular o(s) período(s) selecionado(s) ao relatório.');
                }
            }

            db_fim_transacao();
            $oRetorno->message = 'Período(s) vinculado(s) ao relatório com sucesso.';
            break;
    }
} catch (Exception $oErro) {
    db_fim_transacao(true);
    $oRetorno->erro = true;
    $oRetorno->status = 2;
    $oRetorno->message = $oErro->getMessage();
}

$oRetorno->message = urlencode($oRetorno->message);

echo JSON::create()->stringify($oRetorno);

function excluirRelatorio($codigoRelatorio)
{
    $sqlDelete = "delete from orcparamreltemplate where o163_orcparamrel = {$codigoRelatorio}";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela orcparamreltemplate');
    }

    $sqlDelete = "delete from conrelinfo where c83_codrel    = {$codigoRelatorio}";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela conrelinfo');
    }
    $sqlDelete = "delete from orcparamfontes where o43_codparrel = {$codigoRelatorio}";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela orcparamfontes');
    }
    $sqlDelete = "delete from orcparamrelnota where o42_codparrel = {$codigoRelatorio}";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela orcparamrelnota');
    }
    $sqlDelete = "delete from orcparamrelperiodos where o113_orcparamrel = {$codigoRelatorio}";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela orcparamrelperiodos');
    }

    $sqlDelete = ' delete from orcparamseqorcparamseqcolunavalor';
    $sqlDelete .= "  where o117_orcparamseqorcparamseqcoluna in
                        (select o116_sequencial
                             from orcparamseqorcparamseqcoluna where o116_codparamrel = {$codigoRelatorio});";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela orcparamseqorcparamseqcolunavalor');
    }

    $sqlDelete = " delete from orcparamseqfiltroorcamento where o133_orcparamrel = {$codigoRelatorio}; ";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela orcparamseqorcparamseqcolunavalor');
    }

    $sqlDelete = "delete from orcparamseqfiltrousuario where o72_orcparamrel  = {$codigoRelatorio}";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela orcparamseqfiltrousuario');
    }
    $sqlDelete = "delete from orcparamseqorcparamseqcoluna where o116_codparamrel = {$codigoRelatorio}";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela orcparamseqorcparamseqcoluna');
    }
    $sqlDelete = " delete from orcparamseqfiltropadrao      where o132_orcparamrel = {$codigoRelatorio}";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela orcparamseqfiltropadrao');
    }
    $sqlDelete = " delete from orcparamseq where o69_codparamrel = {$codigoRelatorio} ";
    $rsDelete = db_query($sqlDelete);
    if (!$rsDelete) {
        throw new Exception('Erro ao excluir dados do relatório na tabela orcparamseq');
    }
}
