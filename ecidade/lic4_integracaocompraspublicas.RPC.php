<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

use ECidade\Patrimonial\Licitacao\ComprasPublicas\ComprasPublicas;
use ECidade\Patrimonial\Licitacao\ComprasPublicas\Model\ComprasPublicasConfiguracao;
use ECidade\Patrimonial\Licitacao\ComprasPublicas\Model\ComprasPublicasImportacao;
use ECidade\Patrimonial\Licitacao\ComprasPublicas\Model\ComprasPublicasLote;
use ECidade\Patrimonial\Licitacao\ComprasPublicas\Model\ComprasPublicasCancelaImportacao;

$parametros = JSON::requestParameters();
$retorno = (object)['erro' => false, 'mensagem' => ''];

try {

  db_inicio_transacao();

  $configuracao              = new ComprasPublicasConfiguracao();
  switch ($parametros->acao) {

    case "buscaModalidades":

      $sqlModalidade = "select l03_codigo
                          from pctipocompratribunal
                         inner join cflicita on l03_pctipocompratribunal = l44_sequencial
                         where l44_sigla in ('PRE', 'PRP', 'PCE', 'PCP', 'CCE')
                           and l03_instit=" . db_getsession("DB_instit");

      $rsModalidade = db_query($sqlModalidade);
      $retorno->modalidade = [];
      $iQuantidade = pg_numrows($rsModalidade);
      for ($iRow = 0; $iRow < $iQuantidade; $iRow++) {

        $oModalidade = db_utils::fieldsMemory($rsModalidade, $iRow);
        $retorno->modalidade[] = $oModalidade->l03_codigo;
      }

      break;

    case "salvarConfiguracao":


      $configuracao->setUrl($parametros->url);
      $configuracao->setToken($parametros->token);
      $configuracao->salvar();
      $retorno->mensagem = "Configuração salva com sucesso";
    break;

    case "buscaConfiguracao":

      $configuracao->ler();
      $dados = new stdClass();
      $dados->url        = $configuracao->getUrl();
      $dados->token      = $configuracao->gettoken();
      $retorno->dados    = $dados;
      $retorno->mensagem = "Configuração salva com sucesso";
    break;

    case "verificaConfiguracao":

      if(empty($parametros->codigolicitacao)) {

        throw new Exception("Código da licitação não encontrado");
      }

      $retorno->habilitaConfiguracao = false;
      $oLicitacao                    = new licitacao($parametros->codigolicitacao);
      $clliclicita                   = new cl_liclicita;
      $result = $clliclicita->sql_record($clliclicita->sql_query_pco($parametros->codigolicitacao));
      if ($result != false && $clliclicita->numrows > 0) {
          throw new Exception("A licitação selecionada possui fornecedores cadastrados, verifique.");
      }
      $atributosLicitacao            = new LicitacaoAtributosDinamicos();
      $atributosLicitacao->setCodigoLicitacao($oLicitacao->getCodigo());
      if($atributosLicitacao->getAtributo('tipobeneficiomicroepp') == "T" && $oLicitacao->getTipoJulgamento() != 2) {
        $retorno->habilitaConfiguracao = true;
      }
    break;

    case "buscaItens":

      $retorno->dadositens   = [];
      $oLicitacao            = new licitacao($parametros->licitacao);
      $atributosLicitacao    = new LicitacaoAtributosDinamicos();
      $atributosLicitacao->setCodigoLicitacao($oLicitacao->getCodigo());
      $itensLicitacao        = $oLicitacao->getItens();
      $configuracaoLicitacao = $atributosLicitacao->getAtributo('tipobeneficiomicroepp') == "T"?true:false;
      foreach ($itensLicitacao as $item) {

        $itensLicitacao                = new stdClass();
        $itensLicitacao->l21_codigo    = $item->getCodigo();
        $itensLicitacao->pc04_descr    = $item->getItemSolicitacao()->getDescricaoMaterial();
        $itensLicitacao->l04_descricao = $item->getLoteLicitacao()->getDescricao();
        $itensLicitacao->selecione     = !$item->hasCota();
        $itensLicitacao->exclusivo     = $configuracaoLicitacao;
        $retorno->dadositens[]         = $itensLicitacao;
      }

    break;

    case "buscaItensLote":

      $retorno->dadositens   = [];
      $oLicitacao            = new licitacao($parametros->licitacao);
      $atributosLicitacao    = new LicitacaoAtributosDinamicos();
      $atributosLicitacao->setCodigoLicitacao($oLicitacao->getCodigo());
      $itensLicitacao        = $oLicitacao->getItens();
      $configuracaoLicitacao = $atributosLicitacao->getAtributo('tipobeneficiomicroepp') == "T"?true:false;
      foreach ($itensLicitacao as $item) {

        $itensLicitacao                = new stdClass();
        $itensLicitacao->l21_codigo    = $item->getCodigo();
        $itensLicitacao->pc04_descr    = $item->getItemSolicitacao()->getDescricaoMaterial();
        $itensLicitacao->l04_descricao = $item->getLoteLicitacao()->getDescricao();
        $retorno->dadositenslote[]     = $itensLicitacao;
      }

    break;

    case "buscaDocumentos":

      $integracaoComprasPublicas = new ComprasPublicas();
      $resposta = $integracaoComprasPublicas->getDocumentos();

      if ($resposta) {

        $response = json_decode($resposta);
      }

      if ($response->success == false) {

        throw new Exception($response->message);
      }

      $retorno->documentos = $response->results;
      $retorno->mensagem   = $response->message;
    break;

    case "deparaSituacao":

      $deparaSituacao    = [];
      $deparaSituacao[3] = [5];
      $deparaSituacao[4] = [3,4];
      $deparaSituacao[5] = [2];
      if(!array_key_exists($parametros->codigoSituacao, $deparaSituacao)) {

        throw new Exception("Não foi identificado situação para o código {$parametros->codigoSituacao}");
      }

      $codigo            = implode(",", $deparaSituacao[$parametros->codigoSituacao]);
      $licsituacao       = new cl_licsituacao();
      $sSqlBuscaSituacao = $licsituacao->sql_query_file(null, "l08_sequencial as codigo, l08_descr as descricao", null, "l08_sequencial in ({$codigo})");
      $rsBuscaSituacao   = $licsituacao->sql_record($sSqlBuscaSituacao);
      if ($licsituacao->erro_status == "0") {

        throw new Exception("Não foi possível verificar as situações");
      }

      $retorno->situacao = [];
      for ($i=0; $i < pg_numrows($rsBuscaSituacao); $i++) {

        $oStdSituacao      = db_utils::fieldsMemory($rsBuscaSituacao, $i);
        $retorno->situacao[] = $oStdSituacao;
      }

    break;

    case "enviarDados":

      $integracaoComprasPublicas = new ComprasPublicas();
      $listaDocumentos  = JSON::create()->parse($parametros->documentos);
      $configuracaoLote = JSON::create()->parse($parametros->configuracao);
      $resposta         = $integracaoComprasPublicas->enviaDadosPregao($parametros->licitacao, $listaDocumentos, $configuracaoLote);
      if ($resposta) {

        $response = json_decode($resposta);
      }

      if ($response->success == false) {

        throw new Exception($response->message);
      }

      $retorno->mensagem = $response->message;
    break;

    case "buscaProcesso":

      $integracaoComprasPublicas = new ComprasPublicas();
      $ano                       = db_getsession("DB_anousu");
      $numero                    = null;
      if (!empty($parametros->licitacao)) {

        $oLicitacao              = new licitacao($parametros->licitacao);
        $ano                     = $oLicitacao->getAno();
        $numero                  = $oLicitacao->getEdital();
      }

      $pagina                    = 1;
      $resposta                  = $integracaoComprasPublicas->getProcessos($ano, $numero, $pagina);
      if ($resposta) {

        $response = json_decode($resposta);
      }

      if (isset($response->success) && $response->success == false) {

        throw new Exception($response->mensagem);
      }

      $dado = [];
      $dadosauxiliar = $response->dadosLicitacoes;
      while (count($dadosauxiliar) < $response->quantidadeTotal) {

        $pagina ++;
        $resposta                  = $integracaoComprasPublicas->getProcessos($ano, $numero, $pagina);
        if ($resposta) {

          $response = json_decode($resposta);
        }

        if (isset($response->success) && $response->success == false) {

          throw new Exception($response->mensagem);
        }

        $dadosauxiliar = array_merge($dadosauxiliar, $response->dadosLicitacoes);
      }

      foreach ($dadosauxiliar as $dados) {

        //Ignora as licitações que não foram enviadas pelo sistema
        if ($dados->_id == null) {

          continue;
        }
        $dados->habilitaConsulta = false;
        $dados->importado        = false;
        $dados->editaImportacao  = false;
        // Se o processo estiver finalizado, habilita para buscar os dados para importação
        $licitacao = new licitacao($dados->_id);
        if ($dados->cdSituacao == 6) {

          if($licitacao->getSituacao()->getCodigo() == 0) {

            $dados->habilitaConsulta = true;
            if($licitacao->getModalidade()->getSiglaTipoCompraTribunal() == "PRP" && $licitacao->getTipoJulgamento() != 1 ) {
              $dados->editaImportacao  = true;
            }
          }

          if($licitacao->getSituacao()->getCodigo() == 1) {

            $dados->importado = true;
          }
        }

        $dado[] = $dados;
      }

      if (count($dado) == 0) {

        throw new Exception("Não existem processos para a unidade compradora");
      }

      $retorno->processos        = $dado;
      $retorno->mensagem         = $response->mensagem;
      break;

      // case "buscaUnidades":

      //   $integracaoComprasPublicas = new ComprasPublicas();
      //   $resposta                  = $integracaoComprasPublicas->getUnidades();

      //   if ($resposta) {

      //     $response = json_decode($resposta);
      //   }

      //   if (isset($response->success) && $response->success == false) {

      //     throw new Exception($response->mensagem);
      //   }

      //   $retorno->unidades         = $response;
      //   $retorno->mensagem         = $response->mensagem;
      //   break;



    case "buscarDadosProcessoEditar":

      $integracaoComprasPublicas = new ComprasPublicas();

      if (empty($parametros->processo)) {

        throw new Exception("Número do processo não encontrado!");
      }

      $resposta = $integracaoComprasPublicas->getDadosProcessos($parametros->processo);
      if ($resposta) {

        $response = json_decode($resposta);
      }

      if (isset($response->success)) {

        throw new Exception($response->message);
      }

      $dadosRetorno               = new stdClass();
      $dadosRetorno->fornecedores = $response->Participantes;
      $dadosRetorno->codlicitacao = $response->_id;
      $dadosRetorno->itens        = [];

      foreach ($response->lotes as $lote) {

          foreach ($lote->Vencedores as $vencedor) {

                // $itemRetorno->Cancelado         = $vencedor->Cancelado;
                // $itemRetorno->IdItem            = $vencedor->IdItem;
                // $itemRetorno->RazaoSocial       = $vencedor->RazaoSocial;
                $fornecedor                        = $vencedor->IdFornecedor;
                $valorUnitarioJulgado              = $vencedor->ValorUnitario;
                // $itemRetorno->ValorTotal        = $vencedor->ValorTotal;
                $valorJulgado                      = $vencedor->ValorTotal;
                // array_push($itemRetorno->itensLote, $itemLote);
          }

          foreach ($lote->itens as $item) {

            $licitacao                      = new licitacao($item->_id);
            $loteLicitacao                  = new ComprasPublicasLote($licitacao);
            $itensLote                      = $loteLicitacao->getItensLote($licitacao->getCodigo(), $item->DS_ITEM);

            foreach ($itensLote as $itemLicitacao) {

              $itemRetorno = new stdClass();
              $itemRetorno->readequarvalor       = count($itensLote)==1?false:true;
              $itemRetorno->codigo               = $itemLicitacao->getCodigo();
              $itemRetorno->valorUnitario        = count($itensLote)==1?$valorUnitarioJulgado:$itemLicitacao->getItemSolicitacao()->getValorUnitario();
              $itemRetorno->quantidade           = $itemLicitacao->getItemSolicitacao()->getQuantidade();
              $itemRetorno->valorTotal           = count($itensLote)==1?$valorJulgado:$itemLicitacao->getItemSolicitacao()->getValorUnitario();
              $itemRetorno->fornecedor           = $fornecedor;
              $itemRetorno->descricao            = $itemLicitacao->getItemSolicitacao()->getDescricaoMaterial();
              $itemRetorno->quantidadeJulgada    = $item->QT_ITENS;
              $itemRetorno->valorUnitarioJulgado = $valorUnitarioJulgado;
              $itemRetorno->valorTotalJulgado    = $valorJulgado;
              $itemRetorno->resultado            = $item->TP_RESULTADO_ITEM;
              $itemRetorno->lote                 = $item->DS_ITEM;
              $itemRetorno->casasdecimais        = $response->casasDecimais ;

              array_push($dadosRetorno->itens, $itemRetorno);

          }

          // $itemRetorno->Cancelado           = $vencedor->Cancelado;
          // $itemRetorno->IdItem              = $vencedor->IdItem;
          // $itemRetorno->RazaoSocial         = $vencedor->RazaoSocial;
          // $itemRetorno->IdFornecedor        = $vencedor->IdFornecedor;
          // $itemRetorno->ValorUnitario       = $vencedor->ValorUnitario;
          // $itemRetorno->ValorTotal          = $vencedor->ValorTotal;
        }
      }
      // dd($dadosRetorno);
      $retorno->dados = $dadosRetorno;
    break;

    case "buscarDadosProcesso":

      $integracaoComprasPublicas = new ComprasPublicas();
      if (empty($parametros->processo)) {



      }

      $resposta = $integracaoComprasPublicas->getDadosProcessos($parametros->processo);
      if ($resposta) {

        $response = json_decode($resposta);
      }

      if (isset($response->success)) {

        throw new Exception($response->message);
      }

      $retorno->dados   = $response;
    break;

    case "buscaItensLote":

      if (empty($parametros->codlicitacao)) {

      }

      if (empty($parametros->descricaoLote)) {

      }
    break;

    case "importarDados":

      $dados           = JSON::create()->parse($parametros->dados);
      $dadosFornecedor = JSON::create()->parse($parametros->dadosFornecedor);
      $importacao      = new ComprasPublicasImportacao($parametros->codlicitacao, $dados, $dadosFornecedor);
      $importacao->validaItens();
      $importacao->importaDados();
      $retorno->mensagem = "Dados importados com sucesso";

    break;

    case "importarDadosEditado":

      $dados           = JSON::create()->parse($parametros->dados);
      $dadosFornecedor = JSON::create()->parse($parametros->dadosFornecedor);
      if(empty($parametros->codlicitacao) || !is_array($dados) || count($dados) == 0
        || !is_array($dadosFornecedor) || count($dadosFornecedor) == 0) {

        throw new Exception("Dados inconsistentes para importação");
      }

      $importacao      = new ComprasPublicasImportacao($parametros->codlicitacao, $dados, $dadosFornecedor);
      $importacao->validaItensEditados();
      $importacao->importaDadosEditados();
      $retorno->mensagem = "Dados importados com sucesso";

    break;

    case "atualizaSituacao":


      $licitacao = new licitacao($parametros->licitacao);
      if(count($licitacao->getItens()) == 0 ) {

        throw new Exception("Licitação {$parametros->licitacao}: dados não encontrados");
      }

      if($licitacao->getSituacao()->getCodigo() == $parametros->codigoSituacao) {

        throw new Exception("Situação já atualizada");
      }

      $licitacao->alterarSituacao($parametros->codigoSituacao, "Retorno do Compras Públicas");
      $retorno->mensagem = "Atualizado situação da licitação";

    break;

    case "cancelaImportacao":

      if (empty($parametros->codigoLicitacao)) {

        throw new Exception("Licitação não encontrada");
      }

      $oLicitacao        = new licitacao($parametros->codigoLicitacao);
      if($oLicitacao->getSituacao()->getCodigo() != 1) {

        throw new Exception("Não é possível cancelar a importação. Verifique a situação da licitação {$parametros->codigoLicitacao}");
      }

      $cancelaImportacao = new ComprasPublicasCancelaImportacao($oLicitacao);
      $cancelaImportacao->cancelar();
      $retorno->mensagem = "Cancelado a importação da licitação {$parametros->codigoLicitacao}";
    break;
  }

  db_fim_transacao(false);
} catch (Exception $erro) {

  db_fim_transacao(true);
  $retorno->mensagem = $erro->getMessage();
  $retorno->erro     = true;
}

echo JSON::create()->stringify($retorno);
