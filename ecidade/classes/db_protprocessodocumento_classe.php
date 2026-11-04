<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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

class cl_protprocessodocumento
{
    public $rotulo = null;
    public $query_sql = null;
    public $numrows = 0;
    public $numrows_incluir = 0;
    public $numrows_alterar = 0;
    public $numrows_excluir = 0;
    public $erro_status = null;
    public $erro_sql = null;
    public $erro_banco = null;
    public $erro_msg = null;
    public $erro_campo = null;
    public $pagina_retorno = null;
    /**
     * @var int
     */
    public $p01_sequencial = 0;
    /**
     * @var int
     */
    public $p01_protprocesso = null;
    /**
     * @var string
     */
    public $p01_descricao = '';
    public $p01_documento = null;
    /**
     * @var string
     */
    public $p01_nomedocumento = '';
    /**
     * @var int
     */
    public $p01_usuario = null;
    /**
     * @var string
     */
    public $p01_data = null;
    /**
     * @var int
     */
    public $p01_procandamint = null;
    /**
     * @var bool
     */
    public $p01_estorage = null;
    public $p01_ordem = 0;
    public $p01_assinado = null;
    public $p01_assinado_por = null;
    public $p01_documento_hash = null;

    public $p01_c70codlan = null;

    public $p01_upload_name = null;
    public $campos = "p01_sequencial = int4 = Sequencial de Documentos
                      p01_protprocesso = int4 = Número de Controle
                      p01_descricao = varchar(200) = Descrição
                      p01_documento = oid = Documento
                      p01_nomedocumento = varchar(255) = Nome do documento
                      p01_usuario = int8 = Usuário
                      p01_data = date = Data
                      p01_procandamint = int8 = procandamint
                      p01_estorage = bool = e-Storagep01_documento_hash
		              p01_ordem = int4 = Ordem
		              p01_assinado = boolean = Assinado
		              p01_assinado_por = int4 = Assinado Por
                      p01_documento_hash = varchar(250) = Hash Documento
		              ";

    public function __construct()
    {
        $this->rotulo = new rotulo('protprocessodocumento');
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if ($this->erro_status == '0' || ($mostra == true && $this->erro_status != null)) {
            echo "<script>alert('{$this->erro_msg}')</script>";
            if ($retorna == true) {
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }
   // funcao para atualizar campos
    function atualizacampos($exclusao = false)
    {
        if (!$exclusao) {
            $this->p01_sequencial = ($this->p01_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_sequencial"]:$this->p01_sequencial);
            $this->p01_protprocesso = ($this->p01_protprocesso == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_protprocesso"]:$this->p01_protprocesso);
            $this->p01_descricao = ($this->p01_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_descricao"]:$this->p01_descricao);
            $this->p01_documento = ($this->p01_documento == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_documento"]:$this->p01_documento);
            $this->p01_nomedocumento = ($this->p01_nomedocumento == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_nomedocumento"]:$this->p01_nomedocumento);
            $this->p01_usuario = ($this->p01_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_usuario"]:$this->p01_usuario);
            $this->p01_assinado = ($this->p01_assinado == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_assinado"]:$this->p01_assinado);
            $this->p01_assinado_por = ($this->p01_assinado_por == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_assinado_por"]:$this->p01_assinado_por);
            $this->p01_documento_hash = ($this->p01_documento_hash == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_documento_hash"]:$this->p01_documento_hash);
            if ($this->p01_data == "") {
                $this->p01_data_dia = ($this->p01_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_data_dia"]:$this->p01_data_dia);
                $this->p01_data_mes = ($this->p01_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_data_mes"]:$this->p01_data_mes);
                $this->p01_data_ano = ($this->p01_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_data_ano"]:$this->p01_data_ano);
                if ($this->p01_data_dia != "") {
                     $this->p01_data = $this->p01_data_ano."-".$this->p01_data_mes."-".$this->p01_data_dia;
                }
            }
            $this->p01_procandamint = ($this->p01_procandamint == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_procandamint"]:$this->p01_procandamint);
            $this->p01_ordem = ($this->p01_ordem == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_ordem"]:$this->p01_ordem);
        } else {
            $this->p01_sequencial = ($this->p01_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["p01_sequencial"]:$this->p01_sequencial);
        }
    }

    public function incluir($p01_sequencial)
    {
        if ($this->p01_protprocesso === '' || $this->p01_protprocesso === null) {
            $this->erro_sql = " Campo Número de Controle não informado.";
            $this->erro_campo = "p01_protprocesso";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p01_descricao === '' || $this->p01_descricao === null) {
            $this->erro_sql = " Campo Descrição não informado.";
            $this->erro_campo = "p01_descricao";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p01_nomedocumento === '' || $this->p01_nomedocumento === null) {
            $this->erro_sql = " Campo Nome do documento não informado.";
            $this->erro_campo = "p01_nomedocumento";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p01_usuario === null || $this->p01_usuario === '') {
            $this->p01_usuario = "0";
        }
        if ($this->p01_procandamint === null || $this->p01_procandamint === '') {
            $this->p01_procandamint = "0";
        }
        if ($this->p01_estorage === '' || $this->p01_estorage === null) {
            $this->erro_sql = " Campo e-Storage não informado.";
            $this->erro_campo = "p01_estorage";
            $this->erro_banco = "";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if ($this->p01_ordem == null) {
            $this->p01_ordem = "0";
        }
        if ($p01_sequencial === '' || $p01_sequencial === null || $p01_sequencial === 0) {
            $result = db_query("select nextval('protprocessodocumento_p01_sequencial_seq')");
            if (!$result) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql = "Verifique o cadastro da sequencia: protprocessodocumento_p01_sequencial_seq do campo: p01_sequencial";
                $this->erro_msg = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
                $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->p01_sequencial = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("SELECT last_value FROM protprocessodocumento_p01_sequencial_seq");
            if ($result && pg_fetch_result($result, 0, 0) < $p01_sequencial) {
                $this->erro_sql = " Campo p01_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->p01_sequencial = $p01_sequencial;
            }
        }
        if ($this->p01_sequencial === null || $this->p01_sequencial === '' || $this->p01_sequencial === 0) {
            $this->erro_sql = " Campo p01_sequencial não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = '0';
            return false;
        }

        $this->p01_descricao =  pg_escape_string(substr($this->p01_descricao, 0, 199));

        $sql = "
            INSERT INTO protprocessodocumento (
                p01_sequencial
                ,p01_protprocesso
                ,p01_descricao
                ,p01_documento
                ,p01_nomedocumento
                ,p01_usuario
                ,p01_data
                ,p01_procandamint
                ,p01_estorage
                ,p01_ordem
                ,p01_assinado
                ,p01_assinado_por
                ,p01_c70codlan
                ,p01_upload_name
            ) VALUES (
                 " . ($this->p01_sequencial === null || $this->p01_sequencial === '' ? 'NULL' : $this->p01_sequencial) . "
                ," . ($this->p01_protprocesso === null || $this->p01_protprocesso === '' ? 'NULL' : $this->p01_protprocesso) . "
                ," . ($this->p01_descricao === null || $this->p01_descricao === '' ? 'NULL' : "'{$this->p01_descricao}'") . "
                ," . ($this->p01_documento === null || $this->p01_documento === '' ? 'NULL' : "'{$this->p01_documento}'") . "
                ," . ($this->p01_nomedocumento === null || $this->p01_nomedocumento === '' ? 'NULL' : "'{$this->p01_nomedocumento}'") . "
                ," . ($this->p01_usuario === null || $this->p01_usuario === '' ? 'NULL' : $this->p01_usuario) . "
                ," . ($this->p01_data === null || $this->p01_data === '' ? 'NULL' : "'{$this->p01_data}'") . "
                ," . ($this->p01_procandamint === null || $this->p01_procandamint === '' ? 'NULL' : $this->p01_procandamint) . "
                ," . ($this->p01_estorage === null || $this->p01_estorage === '' ? 'NULL' : ($this->p01_estorage ? 'TRUE' : 'FALSE')) . "
                ," . ($this->p01_ordem === null || $this->p01_ordem === '' ? '0' : $this->p01_ordem) . "
                ," . ($this->p01_assinado === null || $this->p01_assinado === '' ? 'false' : $this->p01_assinado) . "
                ," . ($this->p01_assinado_por === null || $this->p01_assinado_por === '' ? 'NULL' : $this->p01_assinado_por) . "
                ," . ($this->p01_c70codlan === null || $this->p01_c70codlan === '' ? 'NULL' : $this->p01_c70codlan) . "
                ," . ($this->p01_upload_name === null || $this->p01_upload_name === '' ? 'NULL' : "'{$this->p01_upload_name}'") . "
            )
        ";
        $result = db_query($sql);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql = "protprocessodocumento () não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "protprocessodocumento já cadastrado";
                $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql = "protprocessodocumento () não Incluído. Inclusão Abortada.";
                $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_msg = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        return true;
    }

    public function alterar($p01_sequencial = null)
    {
        $sql = "UPDATE protprocessodocumento SET ";
        $virgula = '';
        if (empty($p01_sequencial)) {
            throw new Exception('Campo p01_sequencial é obrigatório!');
        }
        $this->p01_sequencial = $p01_sequencial;
        if (trim($this->p01_protprocesso) !== '' && $this->p01_protprocesso !== null) {
            $sql .= "{$virgula} p01_protprocesso = {$this->p01_protprocesso} ";
            $virgula = ',';
        }
        if (trim($this->p01_descricao) !== '' && $this->p01_descricao !== null) {
            $sql .= "{$virgula} p01_descricao = '{$this->p01_descricao}' ";
            $virgula = ',';
        }
        if (trim($this->p01_documento) !== '' && $this->p01_documento !== null) {
            $sql .= "{$virgula} p01_documento = {$this->p01_documento} ";
            $virgula = ',';
        }
        if (trim($this->p01_nomedocumento) !== '' && $this->p01_nomedocumento !== null) {
            $sql .= "{$virgula} p01_nomedocumento = '{$this->p01_nomedocumento}' ";
            $virgula = ',';
        }
        if (trim($this->p01_usuario) !== '' && $this->p01_usuario !== null) {
            $sql .= "{$virgula} p01_usuario = {$this->p01_usuario} ";
            $virgula = ',';
        }
        if (trim($this->p01_data) !== '' && $this->p01_data !== null) {
            $sql .= "{$virgula} p01_data = '{$this->p01_data}' ";
            $virgula = ',';
        }
        if (trim($this->p01_procandamint) !== '' && $this->p01_procandamint !== null) {
            $sql .= "{$virgula} p01_procandamint = {$this->p01_procandamint} ";
            $virgula = ',';
        }

        if ($this->p01_estorage !== '' && $this->p01_estorage !== null) {
            $sql .= "{$virgula} p01_estorage = " . ($this->p01_estorage === true ? 'TRUE' : 'FALSE') . " ";
        }

        if ($this->p01_assinado !== '' && $this->p01_assinado !== null) {
            $sql .= "{$virgula} p01_assinado = " . ($this->p01_assinado === true ? 'TRUE' : 'FALSE') . " ";
        }

        if (trim($this->p01_documento_hash) !== '' && $this->p01_documento_hash !== null) {
            $sql .= "{$virgula} p01_documento_hash = '{$this->p01_documento_hash}' ";
            $virgula = ',';
        }

        if (trim($this->p01_assinado_por) !== '' && $this->p01_assinado_por !== null) {
            $sql .= "{$virgula} p01_assinado_por = {$this->p01_assinado_por} ";
            $virgula = ',';
        }


        if (trim($this->p01_ordem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["p01_ordem"])) {
            if (trim($this->p01_ordem)=="" && isset($GLOBALS["HTTP_POST_VARS"]["p01_ordem"])) {
                $this->p01_ordem = "0" ;
            }
            $sql  .= $virgula." p01_ordem = $this->p01_ordem ";
            $virgula = ",";
        }
        if ($p01_sequencial !== '' && $p01_sequencial !== null && $p01_sequencial !== 0) {
            $sql .= ' WHERE';
            $sql .= " p01_sequencial = {$p01_sequencial}";
        }

        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "protprocessodocumento não Alterado. Alteração Abortada.\\n";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "protprocessodocumento não foi Alterado. Alteração Executada.\\n";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($p01_sequencial = null, $dbwhere = null)
    {
        if (empty($p01_sequencial)) {
            throw new Exception('Campo p01_sequencial é obrigatório!');
        }

        $sql = " delete from protprocessodocumento
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($p01_sequencial)) {
                if (!empty($sql2)) {
                     $sql2 .= " and ";
                }
                $sql2 .= " p01_sequencial = $p01_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "protprocessodocumento não Excluído. Exclusão Abortada.\\n";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "protprocessodocumento não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function sql_record($sql)
    {
        $result = db_query($sql);
        if (!$result) {
            $this->numrows    = 0;
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:protprocessodocumento";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($p01_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from protprocessodocumento ";
        $sql .= "      left  join db_usuarios  on  db_usuarios.id_usuario = protprocessodocumento.p01_usuario";
        $sql .= "      inner join protprocesso  on  protprocesso.p58_codproc = protprocessodocumento.p01_protprocesso";
        $sql .= "      left  join procandamint  on  procandamint.p78_sequencial = protprocessodocumento.p01_procandamint";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = protprocesso.p58_numcgm";
        $sql .= "      inner join db_config  on  db_config.codigo = protprocesso.p58_instit";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = protprocesso.p58_id_usuario";
        $sql .= "      inner join db_depart  on  db_depart.coddepto = protprocesso.p58_coddepto";
        $sql .= "      inner join tipoproc  on  tipoproc.p51_codigo = protprocesso.p58_codigo";
        $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = procandamint.p78_usuario";
        $sql .= "      inner join procandam  as a on   a.p61_codandam = procandamint.p78_codandam";
        $sql .= "      inner join tipodespacho  on  tipodespacho.p100_sequencial = procandamint.p78_tipodespacho";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($p01_sequencial)) {
                $sql2 .= " where protprocessodocumento.p01_sequencial = $p01_sequencial ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

   // funcao do sql
    public function sql_query_documento_usuario($p01_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos}";
        $sql .= "  from protprocessodocumento ";
        $sql .= "      left  join db_usuarios  on  db_usuarios.id_usuario = protprocessodocumento.p01_usuario";
        $sql2 = "";

        if (empty($dbwhere)) {
            if (!empty($p01_sequencial)) {
                $sql2 .= " where protprocessodocumento.p01_sequencial = $p01_sequencial ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }

        $sql .= $sql2;

        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }

        return $sql;
    }

   // funcao do sql
    public function sql_query_file($p01_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "")
    {

        $sql  = "select {$campos} ";
        $sql .= "  from protprocessodocumento ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($p01_sequencial)) {
                $sql2 .= " where protprocessodocumento.p01_sequencial = $p01_sequencial ";
            }
        } elseif (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function query_last_order_processo($p01_protprocesso)
    {
        return "select max(p01_ordem) from protprocessodocumento where p01_protprocesso = $p01_protprocesso";
    }
}
