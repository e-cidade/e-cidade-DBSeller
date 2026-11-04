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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));

class FormaReclamacao
{

    private $request;
    private $response = array("message" => "", "data" => array(), "success" => false);

    public function __construct($request)
    {
        $this->request = (object)$request;
        $this->route();
    }

    private function route()
    {
        switch ($this->request->action) {
            case "forma-reclamacao":
                $this->getFormasReclamacao();
                break;
            case "forma-reclamacao-save":
                $this->saveFormaReclamacao();
                break;
            case "forma-reclamacao-update":
                $this->updateFormaReclamacao();
                break;
            case "forma-reclamacao-delete":
                $this->deleteFormaReclamacao();
                break;
            default:
                echo "Ação inválida";
                break;
        }
    }

    private function saveFormaReclamacao()
    {

        try {
            $formaReclamacaoDao = new cl_formareclamacao();
            $formaReclamacaoDao->p42_descricao = utf8_decode($this->request->p42_descricao);

            if (!empty($this->request->p42_dtfim)) {
                $formaReclamacaoDao->p42_dtfim = $this->request->p42_dtfim;
            }

            if (!empty($this->request->p42_dtinicio)) {
                $formaReclamacaoDao->p42_dtinicio = $this->request->p42_dtinicio;
            }

            $formaReclamacaoDao->incluir();
            if ($formaReclamacaoDao->erro_status == "0") {
                throw new \Exception($formaReclamacaoDao->erro_msg . " - SQL" . $formaReclamacaoDao->erro_sql);
            }

            $this->response["message"] = "Salvo com sucesso!";
            $this->response["success"] = true;

        } catch (\Exception $ex) {
            $this->response["message"] = $ex->getMessage();
        }

        $this->responseJson();
    }

    private function updateFormaReclamacao()
    {

        try {
            $formaReclamacaoDao = new cl_formareclamacao();
            $formaReclamacaoDao->p42_sequencial = $this->request->p42_sequencial;
            $formaReclamacaoDao->p42_descricao = $this->request->p42_descricao;

            if (!empty($this->request->p42_dtfim)) {
                $formaReclamacaoDao->p42_dtfim = $this->request->p42_dtfim;
            }

            if (!empty($this->request->p42_dtinicio)) {
                $formaReclamacaoDao->p42_dtinicio = $this->request->p42_dtinicio;
            }

            $formaReclamacaoDao->alterar($formaReclamacaoDao->p42_sequencial);

            if ($formaReclamacaoDao->erro_status == "0") {
                throw new \Exception($formaReclamacaoDao->erro_msg . " - SQL" . $formaReclamacaoDao->erro_sql);
            }

            $this->response["message"] = "Alterado com sucesso!";
            $this->response["success"] = true;

        } catch (\Exception $ex) {
            $this->response["message"] = $ex->getMessage();
        }

        $this->responseJson();

    }

    private function deleteFormaReclamacao()
    {

        try {
            $formaReclamacaoDao = new cl_formareclamacao();
            $formaReclamacaoDao->p42_sequencial = $this->request->p42_sequencial;
            $formaReclamacaoDao->excluir($formaReclamacaoDao->p42_sequencial);

            if ($formaReclamacaoDao->erro_status == "0") {
                throw new \Exception($formaReclamacaoDao->erro_msg . " - SQL" . $formaReclamacaoDao->erro_sql);
            }

            $this->response["message"] = "Salvo com sucesso!";
            $this->response["success"] = true;
        } catch (\Exception $ex) {
            $this->response["message"] = $ex->getMessage();
        }
        $this->responseJson();
    }

    private function getFormasReclamacao()
    {

        $formaReclamacaoDao = new cl_formareclamacao();
        $sql = $formaReclamacaoDao->sql_query($p42_sequencial = null, $campos = "*", $ordem = "p42_sequencial DESC");
        $rs = $formaReclamacaoDao->sql_record($sql);
        $data = pg_fetch_all($rs);
        $this->response['data'] = $data;
        $this->response['success'] = true;
        $this->responseJson();

    }

    private function responseJson()
    {
        header('Content-Type: application/json');
        echo JSON::create()->stringify($this->response);
    }

}


new FormaReclamacao($_REQUEST);


