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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');
require_once(modification("classes/db_arquivocdn_classe.php"));
require_once(modification("classes/db_arquivocdniptubase_classe.php"));

$parametros = JSON::requestParameters();

$retorno = new stdClass();
$retorno->status = 1;
$retorno->mensagem = '';

try {
    db_inicio_transacao();

    switch ($parametros->exec) {
        case "salvar":
            if (empty($parametros->j151_descricao)) {
                throw new BusinessException("Campo Descrição é de preenchimento obrigatório");
            }

            if (empty($parametros->db59_sequencial)) {
                if (empty($parametros->j01_matric)) {
                    throw new BusinessException("Campo Matrícula é de preenchimento obrigatório.");
                }

                $data = file_get_contents($parametros->filePath);
                $escaped = bin2hex($data);
                $daoArquivoCdn = new cl_arquivocdn();
                $sequencial = $daoArquivoCdn->getNextVal();
                $nomeArquivo = "arquivo_imovel_{$sequencial}.{$parametros->extension}";

                $sInsert = "INSERT INTO configuracoes.arquivocdn(db59_nome, db59_bucket, db59_arquivobin) ";
                $sInsert .= "      values ('{$nomeArquivo}', 'tributario-iptu', decode('{$escaped}', 'hex'));";
                $rsInsert = db_query($sInsert);

                if (!$rsInsert) {
                    throw new DBException("Erro ao salvar arquivo");
                }

                $daoArquivoCdnIptuBase = new cl_arquivocdniptubase();
                $daoArquivoCdnIptuBase->j151_iptubase = $parametros->j01_matric;
                $daoArquivoCdnIptuBase->j151_arquivocdn = $sequencial;
                $daoArquivoCdnIptuBase->j151_descricao = pg_escape_string($parametros->j151_descricao);
                $daoArquivoCdnIptuBase->incluir();

                if ($daoArquivoCdnIptuBase->erro_status == 0) {
                    throw new DBException("Ocorreu um erro ao salvar arquivo");
                }

                unlink($parametros->filePath);
            } else {
                if (empty($parametros->j01_matric)) {
                    throw new BusinessException("Campo Matrícula é de preenchimento obrigatório.");
                }

                $daoArquivoCdnIptuBase = new cl_arquivocdniptubase();
                $sql = $daoArquivoCdnIptuBase->sql_query_file($parametros->db59_sequencial, $parametros->j01_matric);
                $rs = db_query($sql);

                if (!$rs || pg_num_rows($rs) == 0) {
                    throw new Exception("Documento não encontrado");
                }

                $dados = db_utils::fieldsMemory($rs, 0);

                $daoArquivoCdnIptuBase->j151_iptubase = $dados->j151_iptubase;
                $daoArquivoCdnIptuBase->j151_arquivocdn = $dados->j151_arquivocdn;
                $daoArquivoCdnIptuBase->j151_descricao = $parametros->j151_descricao;
                $daoArquivoCdnIptuBase->alterar();

                if ($daoArquivoCdnIptuBase->erro_status == 0) {
                    throw new DBException("Ocorreu um erro ao salvar o anexo.");
                }
            }

            $retorno->mensagem = "Anexo salvo com sucesso!";
            break;

        case 'listar':
            if (empty($parametros->j01_matric)) {
                throw new BusinessException("Campo matrícula obrigatório!");
            }

            $daoArquivoCdnIptuBase = new cl_arquivocdniptubase();
            $sql = $daoArquivoCdnIptuBase->query(
                array(),
                "db59_sequencial, j151_descricao",
                "j151_iptubase = {$parametros->j01_matric}",
                null,
                'db59_sequencial asc',
                1
            );
            $rs = db_query($sql);
            $arquivos = array();

            while ($arquivo = pg_fetch_array($rs)) {
                $aux = array();
                $aux['db59_sequencial'] = $arquivo['db59_sequencial'];
                $aux['j151_descricao'] = $arquivo['j151_descricao'];
                $arquivos[] = $aux;
            }

            $retorno->arrArquivos = $arquivos;

            break;

        case 'excluir':
            if (empty($parametros->db59_sequencial)) {
                throw new BusinessException("Campo sequencial é obrigatório!");
            }

            if (empty($parametros->j01_matric)) {
                throw new BusinessException("Campo matrícula obrigatório!");
            }

            $sql = "DELETE FROM cadastro.arquivocdniptubase WHERE j151_arquivocdn = {$parametros->db59_sequencial} ";
            $sql .= "and j151_iptubase = $parametros->j01_matric";
            $rs = db_query($sql);
            
            if (!$rs) {
                throw new DBException("Ocorreu um erro a excluir anexo.");
            }

            $sql = "DELETE FROM configuracoes.arquivocdn WHERE db59_sequencial = {$parametros->db59_sequencial}";
            $rs = db_query($sql);

            if (!$rs) {
                throw new DBException("Ocorreu um erro a excluir anexo.");
            }

            $retorno->mensagem = "Anexo excluido com sucesso!";

            break;

        case 'download':
            if (empty($parametros->db59_sequencial)) {
                throw new BusinessException("Campo sequencial é obrigatório!");
            }

            $sql = "SELECT db59_nome, db59_arquivobin ";
            $sql .= "FROM cadastro.arquivocdniptubase ";
            $sql .= "     join arquivocdn on db59_sequencial = j151_arquivocdn ";
            $sql .= "WHERE j151_arquivocdn = {$parametros->db59_sequencial} ";
            $sql .= "  and j151_iptubase = {$parametros->j01_matric}";
            $rs = db_query($sql);
            $arquivo = pg_fetch_array($rs);

            $retorno->arquivo = pg_unescape_bytea($arquivo['db59_arquivobin']);

            $nomeArquivo = "tmp/{$arquivo['db59_nome']}";

            $file_w = fopen("{$nomeArquivo}", 'w+');
            fwrite($file_w, $retorno->arquivo);
            fclose($file_w);

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: $ctype");
            header("Content-Disposition: attachment; filename=\"".basename($nomeArquivo)."\";");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".@filesize($nomeArquivo));
            set_time_limit(0);
            @readfile($nomeArquivo);

            unlink($nomeArquivo);
            
            break;

        case 'multipledownload':
            if (!isset($parametros->sequencialdownload) || empty($parametros->sequencialdownload)) {
                throw new BusinessException('Nenhum arquivo selecionado!');
            }

            $oArquivoCompactado = new ZipArchive();
            $arrArquivosToDelete = array();
            $nomeZip = "tmp/ArquivoMatricula.zip";
            $open = $oArquivoCompactado->open($nomeZip, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
            
            if ($open !== true) {
                throw new Exception("Erro ao gerar arquivo compactado.");
            }

            foreach ($parametros->sequencialdownload as $sequencialdownload) {
                $sql = "SELECT db59_nome, db59_arquivobin ";
                $sql .= "FROM cadastro.arquivocdniptubase ";
                $sql .= "     join arquivocdn on db59_sequencial = j151_arquivocdn ";
                $sql .= "WHERE j151_arquivocdn = {$sequencialdownload} ";
                $sql .= "  and j151_iptubase = {$parametros->j01_matric}";
                $rs = db_query($sql);
                $arquivo = pg_fetch_array($rs);

                $retorno->arquivo = pg_unescape_bytea($arquivo['db59_arquivobin']);

                $nomeArquivo = "tmp/{$arquivo['db59_nome']}";

                $file_w = fopen("{$nomeArquivo}", 'w+');
                fwrite($file_w, $retorno->arquivo);
                fclose($file_w);

                if (!$oArquivoCompactado->addFile($nomeArquivo, substr($nomeArquivo, 3))) {
                    throw new Exception("Erro ao compactar arquivo {$nomeArquivo}.");
                }
                
                $arrArquivosToDelete[] = $nomeArquivo;
            }

            $oArquivoCompactado->close();

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: $ctype");
            header("Content-Disposition: attachment; filename=\"".basename($nomeZip)."\";");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".@filesize($nomeZip));
            set_time_limit(0);
            @readfile($nomeZip);

            foreach ($arrArquivosToDelete as $nomeArquivo) {
                unlink($nomeArquivo);
            }
            unlink($nomeZip);
            break;

        default:
            throw new Exception("Opção inválida!");
    }

    db_fim_transacao(false);
} catch (Exception $erro) {
    db_fim_transacao(true);
    $retorno->status = 2;
    $retorno->mensagem = $erro->getMessage();
}

$retorno->erro = $retorno->status == 2;
echo JSON::create()->stringify($retorno);
