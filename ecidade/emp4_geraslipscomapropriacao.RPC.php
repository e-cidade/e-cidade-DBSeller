<?php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('model/slip.model.php');


$parametros = JSON::requestParameters();
$retorno = (object)array('erro' => false, 'mensagem' => '');

try {
    db_inicio_transacao();
    switch ($parametros->acao) {
        case 'buscarSlips':
            $filtros = [
                "k02_tipo = 'O'",
                "k108_sequencial IS NULL",
                "e60_instit = {$parametros->DB_instit}",
            ];

            if (!empty($parametros->dataInical)) {
                $dataInicial = implode('-', array_reverse(explode('/', $parametros->dataInical)));
                $filtros[] = "k107_data >= '{$dataInicial}'";
            }

            if (!empty($parametros->dataFinal)) {
                $dataFinal = implode('-', array_reverse(explode('/', $parametros->dataFinal)));
                $filtros[] = "k107_data <= '{$dataFinal}'";
            }

            if (!empty($parametros->op)) {
                $filtros[] = "e82_codord = {$parametros->op}";
            }

            $where = implode(' and ', $filtros);

            $sql = "
            SELECT
                   k107_sequencial as empagemovslips_id,
                   e60_numemp as empempenho_id,
                   e60_codemp as empenho,
                   e60_anousu as exercicio,
                   e60_instit as instit_empenho,
                   e82_codord as ordem,
                   k02_descr as descricao_retencao,
                   recemp.o15_codigo,
                   fremp.codigo_siconfi as siconfi,
                   fremp.gestao,
                   recemp.o15_recurso as subrecurso,
                   k107_ctacredito as creditar,
                   reduz_cred.c61_instit as instit_cred,
                   credito.k13_descr as creditar_descricao,
                   frcred.codigo_siconfi as credito_siconfi,
                   reccred.o15_recurso as credito_subrecurso,
                   k107_ctadebito as debitar,
                   debito.k13_descr as debitar_descricao,
                   k107_valor as valor
              from empagemovslips
              join retencaoreceitas on e23_sequencial = k107_retencao
              join retencaotiporec on e21_sequencial = e23_retencaotiporec
              join tabrec on k02_codigo = e21_receita
              left join saltes debito on debito.k13_conta = k107_ctadebito
              left join saltes credito on credito.k13_conta = k107_ctacredito
              left join slipempagemovslips on k108_empagemovslips = k107_sequencial
              join empord on k107_empagemov = e82_codmov
              join empagemov on k107_empagemov = e81_codmov
              join empempenho on e60_numemp = e81_numemp
              join orcdotacao on orcdotacao.o58_anousu = empempenho.e60_anousu
                   and orcdotacao.o58_coddot = empempenho.e60_coddot
              join orctiporec recemp on recemp.o15_codigo = o58_codigo
              join fonterecurso fremp on fremp.orctiporec_id = recemp.o15_codigo
                   and fremp.exercicio = o58_anousu
              left join conplanoreduz reduz_cred on reduz_cred.c61_reduz = k107_ctacredito
                   and reduz_cred.c61_anousu = e60_anousu
                   and reduz_cred.c61_instit = e60_instit
              left join orctiporec reccred on reccred.o15_codigo = c61_codigo
              left join fonterecurso frcred on frcred.orctiporec_id = reccred.o15_codigo
                   and frcred.exercicio = c61_anousu
              WHERE {$where}
              order by e60_codemp, e60_anousu
            ";

            $rs = db_query($sql);

            $dados = [];
            while ($state = pg_fetch_object($rs)) {
                /**
                 * Corrige bug que permitiu criar uma retenção em uma conta de outra instituição.
                 * @todo essa lógica pode ser removida em 2024
                 */
                if ($state->instit_empenho != $state->instit_cred) {
                    $state->creditar = 0;
                    $state->creditar_descricao = '';
                    $state->debitar = 0;
                    $state->debitar_descricao = '';
                }

                // corrige bug onde deixava lançar uma conta de outra instituição
                if ($state->siconfi !== $state->credito_siconfi ||
                    $state->subrecurso !== $state->credito_subrecurso) {
                    $state->creditar = 0;
                    $state->creditar_descricao = '';
                    $state->debitar = 0;
                    $state->debitar_descricao = '';
                }

                $dados[] = $state;
            }

            $retorno->dados = $dados;
            break;
        case 'gerarSlips':
            $retencoes = agruparRegistros(JSON::create()->parse($parametros->retencoes), $parametros->agrupar);

            $dataInical = $parametros->dataInical;
            $dataFinal = $parametros->dataFinal;

            $retorno->slipsGerados = [];
            $instituicao = InstituicaoRepository::getInstituicaoSessao();
            foreach ($retencoes as $retencao) {
                $obs = sprintf(
                    'Referente valores transferidos oriundos de retenção sobre o pagamento de %s da fonte de recurso %s no período de %s a %s.',
                    implode(', ', $retencao->stringObs),
                    $retencao->recursoObservacao,
                    $dataInical,
                    !empty($dataFinal) ? $dataFinal : $dataInical
                );

                $slip = new Slip();
                $slip->setContaCredito($retencao->creditar);
                $slip->setContaDebito($retencao->debitar);
                $slip->setCaracteristicaPeculiarCredito("000");
                $slip->setCaracteristicaPeculiarDebito("000");
                $slip->setValor($retencao->valor);
                $slip->setTipoPagamento(3);
                $slip->setSituacao(1);
                $slip->setNumCgm($instituicao->getCgm()->getCodigo());
                $slip->setHistorico(9700);
                $slip->setObservacoes($obs);
                foreach ($retencao->empagemovslips as $codigo) {
                    $slip->adicionarRetencao($codigo);
                }

                $slip->save();
                Slip::vincularTipoOperacaoSlip($slip->getSlip(), 17);

                $retorno->slipsGerados[] = $slip->getSlip();
            }

            $retorno->mensagem = sprintf('Slips gerados: %s', implode(', ', $retorno->slipsGerados));
            break;
    }
} catch (Exception $erro) {
    $retorno->mensagem = $erro->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);
echo JSON::create()->stringify($retorno);


/**
 * Retorna o hash para agrupar
 * @param integer $tipo do agrupamento
 * @param $dado dados da retenção
 * @return string
 */
function getHash($tipo, $dado)
{
    if ($tipo == 2) {
        return $dado->empagemovslips_id;
    }
    return sprintf('%s#%s#%s#%s', $dado->creditar, $dado->debitar, $dado->siconfi, $dado->subrecurso);
}

function agruparRegistros($retencoes, $tipo)
{
    $agrupadas = [];

    foreach ($retencoes as $retencao) {
        $hash = getHash($tipo, $retencao);
        if (!array_key_exists($hash, $agrupadas)) {
            $std = (object)[
                "creditar" => $retencao->creditar,
                "debitar" => $retencao->debitar,
                "recurso" => $retencao->o15_codigo,
                "recursoObservacao" => sprintf('%s - %s', $retencao->siconfi, $retencao->subrecurso),
                "stringObs" => [],
                "empagemovslips" => [],
                "empenhos" => [],
                "ordens" => [],
            ];
            $std->valor = 0;
            $agrupadas[$hash] = $std;
        }
        $agrupadas[$hash]->valor += $retencao->valor;
        $agrupadas[$hash]->stringObs[] = "OP $retencao->ordem, Empenho {$retencao->empenho}/{$retencao->exercicio}";
        $agrupadas[$hash]->empagemovslips[] = $retencao->empagemovslips_id;
        $agrupadas[$hash]->empenhos[] = $retencao->empempenho_id;
        $agrupadas[$hash]->ordens[] = $retencao->ordem;
    }

    return $agrupadas;
}
