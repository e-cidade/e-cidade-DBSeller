<?php

require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));

$parametro = JSON::create()->parse(str_replace("\\", "", $_POST["json"]));

$anoSessao = db_getsession('DB_anousu');
$instituicaoSessao = db_getsession('DB_instit');

$retorno = new stdClass();
$retorno->mensagem = 1;
$retorno->erro = false;

try {

    db_inicio_transacao();
    switch ($parametro->exec) {

        case 'getInformacoes' :

            $dadosContaDebito = buscarInformacoes(implode(',', $parametro->contas_debito), 'D');
            $dadosContaCredito = buscarInformacoes(implode(',', $parametro->contas_credito), 'C');
            $contaDebito = db_utils::getCollectionByRecord($dadosContaDebito);
            foreach ($contaDebito as $conta) {
                if ($conta->codigo_sistema == "1") {
                    $conta->valor = getValorAtributoMSC($conta, $conta->sigla_atributo);
                } else {
                    $conta->valor = getAtributosPadraoContaCorrente($conta, $conta->sigla_atributo);
                }
            }
            $contaCredito = db_utils::getCollectionByRecord($dadosContaCredito);
            foreach ($contaCredito as $conta) {
                if ($conta->codigo_sistema == "1") {
                    $conta->valor = getValorAtributoMSC($conta, $conta->sigla_atributo);
                } else {
                    $conta->valor = getAtributosPadraoContaCorrente($conta, $conta->sigla_atributo);
                }
            }
            $novoArray = array_merge($contaDebito, $contaCredito);
            $retorno->atributos = $novoArray;
            break;
    }

    db_fim_transacao(false);

} catch (Exception $eErro) {
    db_fim_transacao(true);
    $retorno->erro = true;
    $retorno->mensagem = $eErro->getMessage();
}

echo JSON::create()->stringify($retorno);


function buscarInformacoes($conta, $sinal)
{

    $anoSessao = db_getsession('DB_anousu');
    $instituicaoSessao = db_getsession('DB_instit');

    $campos = implode(',', array(
        'c60_codcon as codigo_conta',
        'c61_reduz as codigo_reduzido',
        'c60_descr as descricao_conta',
        'c60_estrut as estrutural_conta',
        'c60_identificadorfinanceiro as identificador_financeiro',
        'c121_sequencial as codigo_atributo',
        'c121_sigla as sigla_atributo',
        'c61_codigo as codigo_recurso',
        'o15_recurso as fonte_recurso',
        'c121_descricao as descricao_atributo',
        'c120_conplanosistema as codigo_sistema',
        'c122_descricao as descricao_sistema',
        'c60_codsis as codigo_sistema',
        'c120_infocomplementar as atributo',
        "'{$sinal}' as sinal_conta"
    ));

    $sqlBuscaInformacoes = "
        select distinct {$campos}
          from conplanoatributos
               join conplanoinfocomplementar on c121_sequencial = c120_infocomplementar
               join conplanosistema on c122_sequencial = c120_conplanosistema
               join conplano on (c60_codcon, c60_anousu) = (c120_conplano, c120_anousu)
               join conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
               join orctiporec on orctiporec.o15_codigo = conplanoreduz.c61_codigo
               left join conplanosistemaatributos on c129_conplanoinfocomplementar = c121_sequencial
         where c120_anousu = {$anoSessao}
           and c61_instit = {$instituicaoSessao}
           and c61_reduz in ({$conta})
         order by c120_conplanosistema, c120_infocomplementar ";

    $executaBusca = db_query($sqlBuscaInformacoes);
    return $executaBusca;
}


function getValorAtributoMSC($conta, $sigla)
{
    switch ($sigla) {
        case 'PO':
            $instituicao = InstituicaoRepository::getInstituicaoSessao();
            return $instituicao->getCodigoTribunal();
            break;
        case 'FR':
            return $conta->fonte_recurso;
            break;
        case 'ND':
            return '999999999999998';
            break;
        case 'NR':
            return '999999999999999';
            break;
        case 'FS':
            return '04122';
            break;
        case 'ES':
            return '0';
            break;
        case 'AI':
            return (int)db_getsession("DB_anousu") - 1;
            break;
        case 'DC':
            return $conta->codigo_sistema == 9 ? '0' : '1';
            break;
        case 'FP':
            switch ($conta->identificador_financeiro) {
                case 'F' :
                    $valor = '1';
                    break;
                case 'P':
                    $valor = '2';
                    break;
                default:
                    $valor = '';
                    break;
            }
            return $valor;
            break;

        default:
            return '';
            break;
    }
}


/**
 * @param $conta
 * @param $sigla
 *
 * @return string
 * @throws Exception
 */
function getAtributosPadraoContaCorrente($conta, $sigla)
{

    switch ($sigla) {
        case "CTE":
        case "GND":
            $valor = "9";
            break;
        case "MOD":
            $valor = "90";
            break;
        case "ELE":
        case "SBELE":
            $valor = "99";
            break;
        case "BCO":
        case "AGE":
        case "CTA":
            $valor = "999";
            break;
        case "CNPJ":
        case "IDE":
            $valor = "99999999999999";
            break;
        case "NREC":
            $valor = "999999999999999";
            break;
        case "CTR":
        case "CRE":
        case "GEN":
        case "EO":
            $valor = "NI";
            break;
        case "FUN":
            $valor = "4";
            break;
        case "SUBF":
            $valor = "122";
            break;
        case "PROG":
        case "AC":
            $valor = "9999";
            break;
        case "SLG":
            $valor = "0";
            break;
        case "FR":
            $valor = $conta->fonte_recurso;
            break;
        case "UG":
            $departamentoSessao = db_getsession("DB_coddepto");
            $where = "k171_departamento = {$departamentoSessao} or k180_depart = {$departamentoSessao}";
            $daoUnidadeGestora = new cl_unidadegestora();
            $sqlUnidadeGestora = $daoUnidadeGestora->sql_query_ug_departamentos('k171_sequencial', $where);
            $resUnidadeGestora = db_query($sqlUnidadeGestora);
            if (!$resUnidadeGestora) {
                throw new Exception("Ocorreu um erro ao consultar a unidade gestora.");
            }
            $valor = "0";
            if (pg_num_rows($resUnidadeGestora) > 0) {
                $valor = db_utils::fieldsMemory($resUnidadeGestora, 0)->k171_sequencial;
            }
            break;
        case "ORG":
            $instituicao = InstituicaoRepository::getInstituicaoSessao();
            $valor = substr($instituicao->getCodigoTribunal(),0 , 2);
            break;
        case "UO":
            $instituicao = InstituicaoRepository::getInstituicaoSessao();
            $valor = substr($instituicao->getCodigoTribunal(),2 , 2);
            break;
        case "IRP":
            $valor = "F";
            break;
        case "IUFR":
            $recurso = RecursoRepository::getRecursoPorCodigo($conta->codigo_recurso);
            $valor = $recurso->getIdentificadorUsoLOA();
            break;
        case "TDFR":
            $recurso = RecursoRepository::getRecursoPorCodigo($conta->codigo_recurso);
            $valor = $recurso->getTipoDetalhamentoLOA();
            break;
        case "GFR":
            $recurso = RecursoRepository::getRecursoPorCodigo($conta->codigo_recurso);
            $valor = $recurso->getGrupoLOA();
            break;
        case "EFR":
            $recurso = RecursoRepository::getRecursoPorCodigo($conta->codigo_recurso);
            $valor = $recurso->getEspecificacaoLOA();
            break;
        case "DFR":
            $valor = "000";
            break;

        default:
            $valor = '';
    }

    return $valor;
}
