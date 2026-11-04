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

//MODULO: compras
//CLASSE DA ENTIDADE pcgrupo
class cl_pcgrupo {
    // cria variaveis de erro
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
    /* Variáveis do Arquivo */
    public $pc03_codgrupo = 0;
    public $pc03_descrgrupo = null;
    public $pc03_ativo = 'f';
    public $pc03_natureza = 1;
    // cria propriedade com as variaveis do arquivo
    public $campos = "
                 pc03_codgrupo = int4 = Código do Grupo
                 pc03_descrgrupo = varchar(40) = Descrição do Grupo
                 pc03_ativo = bool = Ativo
                 pc03_natureza = int8 = Natureza do Grupo
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("pcgrupo");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
        if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
            echo "<script>alert(\"".$this->erro_msg."\")</script>";
            if($retorna==true){
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }

    public function atualizacampos($exclusao = false)
    {
        if($exclusao==false){
            $this->pc03_codgrupo = ($this->pc03_codgrupo == ""?@$GLOBALS["HTTP_POST_VARS"]["pc03_codgrupo"]:$this->pc03_codgrupo);
            $this->pc03_descrgrupo = ($this->pc03_descrgrupo == ""?@$GLOBALS["HTTP_POST_VARS"]["pc03_descrgrupo"]:$this->pc03_descrgrupo);
            $this->pc03_ativo = ($this->pc03_ativo == "f"?@$GLOBALS["HTTP_POST_VARS"]["pc03_ativo"]:$this->pc03_ativo);
            $this->pc03_natureza = ($this->pc03_natureza == ""?@$GLOBALS["HTTP_POST_VARS"]["pc03_natureza"]:$this->pc03_natureza);
        }else{
            $this->pc03_codgrupo = ($this->pc03_codgrupo == ""?@$GLOBALS["HTTP_POST_VARS"]["pc03_codgrupo"]:$this->pc03_codgrupo);
        }
    }

    public function incluir($pc03_codgrupo)
    {
        $this->atualizacampos();
        if($this->pc03_descrgrupo == null ){
            $this->erro_sql = " Campo Descrição do Grupo não informado.";
            $this->erro_campo = "pc03_descrgrupo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->pc03_ativo == null ){
            $this->erro_sql = " Campo Ativo não informado.";
            $this->erro_campo = "pc03_ativo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->pc03_natureza == null ){
            $this->erro_sql = " Campo Natureza do Grupo não informado.";
            $this->erro_campo = "pc03_natureza";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($pc03_codgrupo == "" || $pc03_codgrupo == null ){
            $result = db_query("select nextval('pcgrupo_pc03_codgrupo_seq')");
            if($result==false){
                $this->erro_banco = str_replace("\n","",@pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: pcgrupo_pc03_codgrupo_seq do campo: pc03_codgrupo";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->pc03_codgrupo = pg_result($result,0,0);
        }else{
            $result = db_query("select last_value from pcgrupo_pc03_codgrupo_seq");
            if(($result != false) && (pg_result($result,0,0) < $pc03_codgrupo)){
                $this->erro_sql = " Campo pc03_codgrupo maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }else{
                $this->pc03_codgrupo = $pc03_codgrupo;
            }
        }
        if(($this->pc03_codgrupo == null) || ($this->pc03_codgrupo == "") ){
            $this->erro_sql = " Campo pc03_codgrupo não declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into pcgrupo(
                                       pc03_codgrupo
                                      ,pc03_descrgrupo
                                      ,pc03_ativo
                                      ,pc03_natureza
                       )
                values (
                                $this->pc03_codgrupo
                               ,'$this->pc03_descrgrupo'
                               ,'$this->pc03_ativo'
                               ,$this->pc03_natureza
                      )";
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "Grupo ($this->pc03_codgrupo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Grupo já Cadastrado";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Grupo ($this->pc03_codgrupo) não Incluído. Inclusão Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
        $this->erro_sql .= "Valores : ".$this->pc03_codgrupo;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->pc03_codgrupo  ));
            if(($resaco!=false)||($this->numrows!=0)){

                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac,0,0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,5500,'$this->pc03_codgrupo','I')");
                $resac = db_query("insert into db_acount values($acount,854,5500,'','".AddSlashes(pg_result($resaco,0,'pc03_codgrupo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,854,5501,'','".AddSlashes(pg_result($resaco,0,'pc03_descrgrupo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,854,7814,'','".AddSlashes(pg_result($resaco,0,'pc03_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,854,1013447,'','".AddSlashes(pg_result($resaco,0,'pc03_natureza'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        return true;
    }

    public function alterar($pc03_codgrupo=null)
    {
        $this->atualizacampos();
        $sql = " update pcgrupo set ";
        $virgula = "";
        if(trim($this->pc03_codgrupo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc03_codgrupo"])){
            $sql  .= $virgula." pc03_codgrupo = $this->pc03_codgrupo ";
            $virgula = ",";
            if(trim($this->pc03_codgrupo) == null ){
                $this->erro_sql = " Campo Código do Grupo não informado.";
                $this->erro_campo = "pc03_codgrupo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->pc03_descrgrupo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc03_descrgrupo"])){
            $sql  .= $virgula." pc03_descrgrupo = '$this->pc03_descrgrupo' ";
            $virgula = ",";
            if(trim($this->pc03_descrgrupo) == null ){
                $this->erro_sql = " Campo Descrição do Grupo não informado.";
                $this->erro_campo = "pc03_descrgrupo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->pc03_ativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc03_ativo"])){
            $sql  .= $virgula." pc03_ativo = '$this->pc03_ativo' ";
            $virgula = ",";
            if(trim($this->pc03_ativo) == null ){
                $this->erro_sql = " Campo Ativo não informado.";
                $this->erro_campo = "pc03_ativo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->pc03_natureza)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pc03_natureza"])){
            $sql  .= $virgula." pc03_natureza = $this->pc03_natureza ";
            $virgula = ",";
            if(trim($this->pc03_natureza) == null ){
                $this->erro_sql = " Campo Natureza do Grupo não informado.";
                $this->erro_campo = "pc03_natureza";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if($pc03_codgrupo!=null){
            $sql .= " pc03_codgrupo = $this->pc03_codgrupo";
        }
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            $resaco = $this->sql_record($this->sql_query_file($this->pc03_codgrupo));
            if ($this->numrows > 0) {

                for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

                    $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac,0,0);
                    $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac = db_query("insert into db_acountkey values($acount,5500,'$this->pc03_codgrupo','A')");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc03_codgrupo"]) || $this->pc03_codgrupo != "")
                        $resac = db_query("insert into db_acount values($acount,854,5500,'".AddSlashes(pg_result($resaco,$conresaco,'pc03_codgrupo'))."','$this->pc03_codgrupo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc03_descrgrupo"]) || $this->pc03_descrgrupo != "")
                        $resac = db_query("insert into db_acount values($acount,854,5501,'".AddSlashes(pg_result($resaco,$conresaco,'pc03_descrgrupo'))."','$this->pc03_descrgrupo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc03_ativo"]) || $this->pc03_ativo != "")
                        $resac = db_query("insert into db_acount values($acount,854,7814,'".AddSlashes(pg_result($resaco,$conresaco,'pc03_ativo'))."','$this->pc03_ativo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    if (isset($GLOBALS["HTTP_POST_VARS"]["pc03_natureza"]) || $this->pc03_natureza != "")
                        $resac = db_query("insert into db_acount values($acount,854,1013447,'".AddSlashes(pg_result($resaco,$conresaco,'pc03_natureza'))."','$this->pc03_natureza',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Grupo não Alterado. Alteração Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->pc03_codgrupo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Grupo não foi Alterado. Alteração Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->pc03_codgrupo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$this->pc03_codgrupo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }

    public function excluir($pc03_codgrupo=null, $dbwhere = null)
    {
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
                && ($lSessaoDesativarAccount === false))) {

            if (empty($dbwhere)) {

                $resaco = $this->sql_record($this->sql_query_file($pc03_codgrupo));
            } else {
                $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
            }
            if (($resaco != false) || ($this->numrows!=0)) {

                for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

                    $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
                    $acount = pg_result($resac,0,0);
                    $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                    $resac  = db_query("insert into db_acountkey values($acount,5500,'$pc03_codgrupo','E')");
                    $resac  = db_query("insert into db_acount values($acount,854,5500,'','".AddSlashes(pg_result($resaco,$iresaco,'pc03_codgrupo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,854,5501,'','".AddSlashes(pg_result($resaco,$iresaco,'pc03_descrgrupo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,854,7814,'','".AddSlashes(pg_result($resaco,$iresaco,'pc03_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                    $resac  = db_query("insert into db_acount values($acount,854,1013447,'','".AddSlashes(pg_result($resaco,$iresaco,'pc03_natureza'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                }
            }
        }
        $sql = " delete from pcgrupo
                    where ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($pc03_codgrupo)){
                if (!empty($sql2)) {
                    $sql2 .= " and ";
                }
                $sql2 .= " pc03_codgrupo = $pc03_codgrupo ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if ($result == false) {
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Grupo não Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$pc03_codgrupo;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result) == 0) {
                $this->erro_banco = "";
                $this->erro_sql = "Grupo não Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$pc03_codgrupo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
                $this->erro_sql .= "Valores : ".$pc03_codgrupo;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
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
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_num_rows($result);
        if ($this->numrows == 0) {
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:pcgrupo";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }

    public function sql_query($pc03_codgrupo = null,$campos = "*", $ordem = null, $dbwhere = "") {

        $sql  = "select {$campos}";
        $sql .= "  from pcgrupo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($pc03_codgrupo)) {
                $sql2 .= " where pcgrupo.pc03_codgrupo = $pc03_codgrupo ";
            }
        } else if (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

    public function sql_query_file($pc03_codgrupo = null, $campos = "*", $ordem = null, $dbwhere = "") {

        $sql  = "select {$campos} ";
        $sql .= "  from pcgrupo ";
        $sql2 = "";
        if (empty($dbwhere)) {
            if (!empty($pc03_codgrupo)){
                $sql2 .= " where pcgrupo.pc03_codgrupo = $pc03_codgrupo ";
            }
        } else if (!empty($dbwhere)) {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if (!empty($ordem)) {
            $sql .= " order by {$ordem}";
        }
        return $sql;
    }

}
