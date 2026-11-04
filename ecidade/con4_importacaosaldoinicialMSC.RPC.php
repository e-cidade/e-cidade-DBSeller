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

require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));

$oParam = JSON::create()->parse(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->erro = false;
$oRetorno->mensagem = '';

$anoSessao = db_getsession('DB_anousu');
$instituicaoSessao = db_getsession('DB_instit');


try {

    db_inicio_transacao();
    switch ($oParam->exec) {
        case "enviarArquivo":
            $arquivo = $_FILES['arquivo_msc'];
            if ($arquivo['type'] !== 'text/csv') {
                throw new Exception('O arquivo importado deve ser do tipo CSV.');
            }

            if ($arquivo['error'] !== UPLOAD_ERR_OK) {
                throw new FileException('Ocorreu um erro ao fazer envio do arquivo.');
            }

            /* exclui o arquivo importado anteriormente, caso existir. */
            excluirImportacaoArquivo();

            $controle = findCsvControl($arquivo['tmp_name']);
            $file = new SplFileObject($arquivo['tmp_name']);
            $file->setCsvControl($controle);

            while (!$file->eof()) {
                $linha = $file->fgetcsv();

                if ($linha[14] !== 'ending_balance') {
                    continue;
                }

                $stdLinha = (object)array(
                    'hash' => montarHashMatriz($linha),
                    'conta' => $linha[0],
                    'valor' => $linha[13],
                    'tipo_valor' => $linha[14],
                    'natureza' => $linha[15],
                );

                $daoAtributos = new cl_conplanoatributosaldo();
                $daoAtributos->c125_sequencial = null;
                $daoAtributos->c125_anousu = $anoSessao;
                $daoAtributos->c125_hashcontaatributos = $stdLinha->hash;
                $daoAtributos->c125_valor = $stdLinha->valor;
                $daoAtributos->c125_natureza = strtoupper($stdLinha->natureza);
                $daoAtributos->c125_instit = $instituicaoSessao;
                $daoAtributos->c125_mesusu = '0';
                $daoAtributos->c125_conplanosistema = 1;
                $daoAtributos->c125_tipo = 4;
                $daoAtributos->c125_tiposaldo = 1;
                $daoAtributos->incluir(null);
                if ($daoAtributos->erro_status === '0') {
                    throw new Exception("Ocorreu um erro ao incluir o hash {$stdLinha->hash}.");
                }

            }

            $arquivoLock = "======= REALIZADO IMPORTAÇÃO DE SALDO DA MATRIZ =======\n";
            $arquivoLock .= "Usuario : " . db_getsession('DB_id_usuario') . " - " . db_getsession('DB_login') . "\n";
            $arquivoLock .= "Data/Hora do Servidor: " . date('d/m/Y - h:i:s') . "\n";
            $arquivoLock .= "Data/Hora do Sistema: " . date('d/m/Y - h:i:s', db_getsession('DB_datausu')) . "\n";
            file_put_contents('config/financeiro/importacao_saldo_msc.lock', $arquivoLock);

            $mensagem = 'Arquivo importado com sucesso.';

            break;

        case "removerArquivo":
            excluirImportacaoArquivo();
            $mensagem = "Arquivo removido com sucesso.";
            break;
    }

    db_fim_transacao(false);
    $oRetorno->mensagem = $mensagem;

} catch (Exception $eErro) {

    db_fim_transacao(true);
    $oRetorno->erro = true;
    $oRetorno->mensagem = $eErro->getMessage();
}

echo JSON::create()->stringify($oRetorno);

/**
 * @param array $linha
 * @return string
 */
function montarHashMatriz(array $linha)
{
    $colunas = array(1, 3, 5, 7, 9, 11);
    $hash = array($linha[0]);
    foreach ($colunas as $indice) {
        $valorAtributo = $linha[$indice];
        if (!empty($valorAtributo)) {
            $indiceValor = ($indice + 1);
            $hash[] = "{$linha[$indice]}#{$linha[$indiceValor]}";
        }
    }
    return implode('|', $hash);
}

/**
 * @return bool
 * @throws Exception
 */
function excluirImportacaoArquivo()
{
    $where = implode(' and ', array(
            " c125_mesusu = 0 ",
            " c125_tiposaldo = 1 ",
            " c125_tipo = 4 ",
        )
    );
    $daoAtributos = new cl_conplanoatributosaldo();
    $daoAtributos->excluir(null, $where);
    if ($daoAtributos->erro_status === '0') {
        throw new Exception('Ocorreu um erro ao excluir os dados importados anteriormente.');
    }
    unlink('config/financeiro/importacao_saldo_msc.lock');
    return true;
}

function findCsvControl($arquivo)
{

    $file = fopen($arquivo, 'r');
    $linha = fgets($file);
    $control = ',';
    if (strpos($linha, ";") !== false) {
        $control = ";";
    }

    fclose($file);
    return $control;
}
