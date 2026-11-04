<?php
/**
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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
ini_set('memory_limit', '-1');

use App\Domain\Financeiro\Contabilidade\Services\Relatorios\MapeamentoPcaspService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\MapeamentoPlanoDespesaService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\MapeamentoPlanoReceitaService;
use ECidade\Financeiro\Contabilidade\ContaCorrente\Services\Processamento;
use ECidade\Financeiro\Contabilidade\Encerramento\PeriodoContabil;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Model\Lancamento;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\ProcessamentoMatriz;

$oJson = new services_json();
$oParametros = JSON::create()->parse(str_replace('\\', "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->iStatus = 1;
$oRetorno->sMessage = '';
define("MENSAGENS", "con4_matrizsaldocontabil.RPC.json");

try {
    db_inicio_transacao();

    switch ($oParametros->sExecucao) {
        case "processarMatriz":
            db_putsession("DB_desativar_account", true);
            $rsDesabilitarAuditoria = db_query("SELECT fc_putsession('__disable_audit__', 'on');");
            if ( ! $rsDesabilitarAuditoria ){
                throw new \Exception("Erro ao desabilitar auditoria");
            }

            $inicio = microtime(true);
            $data = explode("/", $oParametros->competencia);
            $mes = $data[0];
            $ano = $data[1];

            $oParametros->encerramento = $oParametros->encerramento == '0' ? false : true;

            if ($mes != 12) {
                $oParametros->encerramento = null;
            }

            $processamentoMatriz = new ProcessamentoMatriz($mes, $ano, 1, $oParametros->encerramento);
            foreach ($oParametros->instituicoes as $instituicao) {
                $processamentoMatriz->addInstituicao(new Instituicao($instituicao->codigo));
            }

            $oRetorno->filePath = '';
            $oRetorno->fileName = '';
            switch ($oParametros->processamento) {

                case 1:
                    validaContasSemVinculo($ano);
                    $processamentoMatriz->excluirProcessamento();
                    $processamentoMatriz->processar();
                    $processamentoMatriz->setPersistirSaldoFinal(false);
                    $oRetorno->filePath = $processamentoMatriz->emitirMatriz(true);
                    $fileName = explode('/', $oRetorno->filePath);
                    $oRetorno->fileName = end($fileName);
                    break;
                case 2:
                    validaContasSemVinculo($ano);
                    $processamentoMatriz->excluirProcessamento();
                    $processamentoMatriz->processar();

                    break;
                case 3:

                    $processamentoMatriz->setPersistirSaldoFinal(false);
                    $oRetorno->filePath = $processamentoMatriz->emitirMatriz(true);
                    break;
            }

            if ($oParametros->encerrar) {
                $dia = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
                $dbDate = new DBDate("$dia/$mes/$ano");
                $usuario = new UsuarioSistema(db_getsession("DB_id_usuario"));
                $anousu = db_getsession("DB_anousu");

                foreach ($oParametros->instituicoes as $instituicao) {
                    $periodoContabil = new PeriodoContabil(new Instituicao($instituicao->codigo), $dbDate, $usuario,
                        $anousu);
                    $periodoContabil->encerrar();
                }
            }

            $oRetorno->sMessage = "Procedimento concluído com sucesso!";

            db_destroysession("DB_desativar_account");

            if ($processamentoMatriz->getQuantidadeContasInconsistentes() > 0) {
                $oRetorno->sMessage .= "\n\nO sistema identificou inconsistências na estrutura do plano de contas em relação ao elenco padrão do PCASP estendido válido para o exercício corrente.\n";
                $oRetorno->sMessage .= "As inconsistências podem ser verificadas no relatório localizado em 'Relatórios > Validação Plano de Contas MSC'.\n";
                $oRetorno->sMessage .= "Para a resolução dos problemas contate o suporte técnico do sistema.";
            }

            if ($processamentoMatriz->temImportacao()) {
                $oRetorno->sMessage .= "\n\nAtenção: \n";
                $oRetorno->sMessage .= "Para essa competência foi incorporado um arquivo externo a partir da linha {$processamentoMatriz->getTotalLinhasEcidade()}. O e-cidade não é responsável pelos dados externos.";
            }
            $fim = microtime(true);
            file_put_contents("tmp/tempo_msc", ($fim - $inicio));
            break;

        case 'buscarInstituicoesConfiguradas':

            $daoConfiguracaoInsituicao = new cl_configuracaoinstituicaosiconfi();
            $sqlInstituicoes = $daoConfiguracaoInsituicao->sql_query(null, 'codigo, nomeinst');
            $rsInstituicoes = db_query($sqlInstituicoes);

            if (!$rsInstituicoes) {
                throw new DBException("Erro ao buscar as instituições configuradas");
            }

            $oRetorno->instituicoes = db_utils::makeCollectionFromRecord($rsInstituicoes, function ($dadosInstituicao) {

                $intituicao = new stdClass();
                $intituicao->codigo = $dadosInstituicao->codigo;
                $intituicao->nome = $dadosInstituicao->nomeinst;
                return $intituicao;
            });

            break;

        case 'salvarInstituicoesConfiguradas':

            if (empty($oParametros->instituicoes)) {
                throw new ParameterException("Informe ao menos uma instituição.");
            }

            $daoConfiguracaoInsituicao = new cl_configuracaoinstituicaosiconfi();
            $sqlInstituicoes = $daoConfiguracaoInsituicao->sql_query_file();

            $rsInstituicoes = db_query($sqlInstituicoes);
            if (!$rsInstituicoes) {
                throw new DBException("Erro ao buscar a configuração atual.");
            }

            db_utils::makeCollectionFromRecord($rsInstituicoes,
                function ($instituicao) use ($daoConfiguracaoInsituicao) {
                    $daoConfiguracaoInsituicao->excluir($instituicao->c125_sequencial);
                    if ($daoConfiguracaoInsituicao->erro_status == 0) {
                        throw new DBException("Erro ao processar a configuração.");
                    }
                });

            foreach ($oParametros->instituicoes as $instituicao) {
                $daoConfiguracaoInsituicao->c125_sequencial = null;
                $daoConfiguracaoInsituicao->c125_db_config = $instituicao;
                $daoConfiguracaoInsituicao->incluir(null);

                if ($daoConfiguracaoInsituicao->erro_status == 0) {
                    throw new DBException("Erro ao incluir instituição.");
                }
            }

            $oRetorno->sMessage = "Configuração salva com sucesso.";
            break;

        case "retornaUltimaCompetenciaProcessada":

            $oRetorno->ultimaCompetenciaProcessada = ProcessamentoMatriz::getUltimaCompetenciaProcessada();
            break;

        case "verificaDeparaRecursos":

            $ano = db_getsession("DB_anousu");
            $codigosPorAno = file("config/financeiro/siconfi/recursos/recurso_{$ano}.csv");
            if (count($codigosPorAno) === 0) {
                $mensagem = "Não foi importado o arquivo de Vinculação de Recursos para o ano de {$ano}. Para realizar a ";
                $mensagem .= "importação acesse a rotina Procedimentos > Matriz de Saldos Contábeis > Vinculação de ";
                $mensagem .= "Recursos";
                throw new Exception($mensagem);
            }
            break;
    }

    db_fim_transacao(false);


} catch (Exception $oErro) {
    db_fim_transacao(true);
    $oRetorno->iStatus = 2;
    $oRetorno->sMessage = $oErro->getMessage();
}

$oRetorno->erro = $oRetorno->iStatus == 2;
echo JSON::create()->stringify($oRetorno);


/**
 * @param $exercicio
 * @return true
 * @throws Exception
 */
function validaContasSemVinculo($exercicio) {

    $pcasp = hasContasSemVinculoPcasp($exercicio);
    $despesa = hasContasDespesaSemVinculoPcasp($exercicio);
    $receita = hasContasReceitaSemVinculoPcasp($exercicio);

    if ($pcasp or $despesa or $receita) {
        $msg = "Existem contas sem o vínculo do mapeamento com o Plano da União.\n";
        if ($pcasp) {
            $msg .= "\n - Plano PCASP: Contabilidade > Cadastros > Plano de Contas > PCASP > Mapear";
        }

        if ($despesa) {
            $msg .= "\n - Despesa: Contabilidade > Cadastros > Plano de Contas > Orçamentário > Mapear Despesa > Por Conta";
        }

        if ($receita) {
            $msg .= "\n - Receita: Contabilidade > Cadastros > Plano de Contas > Orçamentário > Mapear Receita > Por Conta";
        }
        throw new Exception($msg);
    }

    return true;
}

/**
 * @param $exercicio
 * @return bool
 */
function hasContasSemVinculoPcasp($exercicio)
{
    $filtros = [
        'exercicio' => $exercicio,
        'tipoPlano' => 'uniao'
    ];
    $servicePcasp = new MapeamentoPcaspService();
    $servicePcasp->filtros($filtros);
    $pcasp = $servicePcasp->getContasSemVinculoComMovimentacao();
    return count($pcasp) > 0;
}

/**
 * @param $exercicio
 * @return bool
 */
function hasContasDespesaSemVinculoPcasp($exercicio)
{
    $sql = "
    with contas_com_movimento as (
      select c60_codigo
        from conlancam
        join conlancamele on c67_codlan = c70_codlan
        join conplanoorcamento on conplanoorcamento.c60_anousu = c70_anousu
             and conplanoorcamento.c60_codcon = c67_codele
       where c70_anousu = {$exercicio}
      union
      select c60_codigo
        from conlancam
        join conlancamdot on c73_anousu = c70_anousu
             and c73_codlan = c70_codlan
        join orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
        join conplanoorcamento on conplanoorcamento.c60_anousu = c70_anousu
             and conplanoorcamento.c60_codcon = o58_codele
       where c70_anousu = {$exercicio}
    ) select *
    from contas_com_movimento
    where not exists (
        select 1
          from planodespesaconplanoorcamento
          join planodespesa on planodespesa.id = planodespesa_id
         where conplanoorcamento_codigo = c60_codigo
           and uniao is true
     )
    ";
    $rs = db_query($sql);
    return pg_num_rows($rs) > 0;
}

/**
 * @param $exercicio
 * @return bool
 */
function hasContasReceitaSemVinculoPcasp($exercicio)
{
    $dInicio = "$exercicio-01-01";
    $dFim = "$exercicio-12-31";
    $sql = "
    select c60_codigo
      from orcreceita
      join balancete_receita_complemento( o70_anousu, o70_codfon, o70_concarpeculiar, '$dInicio', '$dFim')
           on o70_codfon = fonte
           and o70_anousu = ano
      join conplanoorcamento on (c60_codcon, c60_anousu) = (o70_codfon, o70_anousu)
     where o70_anousu = $exercicio
       and (   previsao_adicional_acumulado != 0
            or valor_a_arrecadar != 0
            or arrecadado_periodo != 0
            or arrecadado_acumulado != 0
            or previsao_atualizada != 0
           )
      and not exists (
        select 1
          from planoreceitaconplanoorcamento
          join planoreceita on planoreceita.id = planoreceita_id
         where conplanoorcamento_codigo = c60_codigo
           and uniao is true
      )
    ";

    $rs = db_query($sql);
    return pg_num_rows($rs) > 0;
}
