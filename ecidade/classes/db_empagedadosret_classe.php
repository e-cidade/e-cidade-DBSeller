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

//MODULO: empenho
//CLASSE DA ENTIDADE empagedadosret
class cl_empagedadosret {
   // cria variaveis de erro
   var $rotulo     = null;
   var $query_sql  = null;
   var $numrows    = 0;
   var $numrows_incluir = 0;
   var $numrows_alterar = 0;
   var $numrows_excluir = 0;
   var $erro_status= null;
   var $erro_sql   = null;
   var $erro_banco = null;
   var $erro_msg   = null;
   var $erro_campo = null;
   var $pagina_retorno = null;
   // cria variaveis do arquivo
   var $e75_codret = 0;
   var $e75_codgera = 0;
   var $e75_arquivoret = null;
   var $e75_febraban = null;
   var $e75_seqarq = 0;
   var $e75_codfebraban = null;
   var $e75_ativo = 'f';
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 e75_codret = int8 = Código do retorno
                 e75_codgera = int4 = Código
                 e75_arquivoret = varchar(20) = Uso do banco
                 e75_febraban = varchar(29) = Uso Febraban - Cnab
                 e75_seqarq = int4 = Sequencial do arquivo
                 e75_codfebraban = varchar(10) = Código do banco FEBRABAN
                 e75_ativo = bool = Retorno Ativo
                 ";
    //funcao construtor da classe
    public function __construct() {
        //classes dos rotulos dos campos
        $this->rotulo = new rotulo("empagedadosret");
        $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
    }
    //funcao erro
    public function erro($mostra,$retorna) {
        if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
            echo "<script>alert(\"".$this->erro_msg."\");</script>";
            if($retorna==true){
                echo "<script>location.href='".$this->pagina_retorno."'</script>";
            }
        }
    }
    // funcao para atualizar campos
    public function atualizacampos($exclusao=false) {
        if($exclusao==false){
            $this->e75_codret = ($this->e75_codret == ""?@$GLOBALS["HTTP_POST_VARS"]["e75_codret"]:$this->e75_codret);
            $this->e75_codgera = ($this->e75_codgera == ""?@$GLOBALS["HTTP_POST_VARS"]["e75_codgera"]:$this->e75_codgera);
            $this->e75_arquivoret = ($this->e75_arquivoret == ""?@$GLOBALS["HTTP_POST_VARS"]["e75_arquivoret"]:$this->e75_arquivoret);
            $this->e75_febraban = ($this->e75_febraban == ""?@$GLOBALS["HTTP_POST_VARS"]["e75_febraban"]:$this->e75_febraban);
            $this->e75_seqarq = ($this->e75_seqarq == ""?@$GLOBALS["HTTP_POST_VARS"]["e75_seqarq"]:$this->e75_seqarq);
            $this->e75_codfebraban = ($this->e75_codfebraban == ""?@$GLOBALS["HTTP_POST_VARS"]["e75_codfebraban"]:$this->e75_codfebraban);
            $this->e75_ativo = ($this->e75_ativo == "f"?@$GLOBALS["HTTP_POST_VARS"]["e75_ativo"]:$this->e75_ativo);
        }else{
            $this->e75_codret = ($this->e75_codret == ""?@$GLOBALS["HTTP_POST_VARS"]["e75_codret"]:$this->e75_codret);
        }
    }
    // funcao para inclusao
    public function incluir ($e75_codret){
        $this->atualizacampos();
        if($this->e75_codgera == null ){
            $this->erro_sql = " Campo Código nao Informado.";
            $this->erro_campo = "e75_codgera";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->e75_seqarq == null ){
            $this->erro_sql = " Campo Sequencial do arquivo nao Informado.";
            $this->erro_campo = "e75_seqarq";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->e75_codfebraban == null ){
            $this->erro_sql = " Campo Código do banco FEBRABAN nao Informado.";
            $this->erro_campo = "e75_codfebraban";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($this->e75_ativo == null ){
            $this->erro_sql = " Campo Retorno Ativo nao Informado.";
            $this->erro_campo = "e75_ativo";
            $this->erro_banco = "";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        if($e75_codret == "" || $e75_codret == null ){
            $result = db_query("select nextval('empagedadosret_e75_codret_seq')");
            if($result==false){
                $this->erro_banco = str_replace("\n","",@pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: empagedadosret_e75_codret_seq do campo: e75_codret";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->e75_codret = pg_result($result,0,0);
        }else{
            $result = db_query("select last_value from empagedadosret_e75_codret_seq");
            if(($result != false) && (pg_result($result,0,0) < $e75_codret)){
                $this->erro_sql = " Campo e75_codret maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }else{
                $this->e75_codret = $e75_codret;
            }
        }
        if(($this->e75_codret == null) || ($this->e75_codret == "") ){
            $this->erro_sql = " Campo e75_codret nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into empagedadosret(
                                       e75_codret
                                      ,e75_codgera
                                      ,e75_arquivoret
                                      ,e75_febraban
                                      ,e75_seqarq
                                      ,e75_codfebraban
                                      ,e75_ativo
                       )
                values (
                                $this->e75_codret
                               ,$this->e75_codgera
                               ,'$this->e75_arquivoret'
                               ,'$this->e75_febraban'
                               ,$this->e75_seqarq
                               ,'$this->e75_codfebraban'
                               ,'$this->e75_ativo'
                      )";
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
                $this->erro_sql   = "Dados do arquivo retorno do banco ($this->e75_codret) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "Dados do arquivo retorno do banco já Cadastrado";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }else{
                $this->erro_sql   = "Dados do arquivo retorno do banco ($this->e75_codret) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->e75_codret;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $resaco = $this->sql_record($this->sql_query_file($this->e75_codret));
        if(($resaco!=false)||($this->numrows!=0)){
            $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
            $acount = pg_result($resac,0,0);
            $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
            $resac = db_query("insert into db_acountkey values($acount,7270,'$this->e75_codret','I')");
            $resac = db_query("insert into db_acount values($acount,1206,7270,'','".AddSlashes(pg_result($resaco,0,'e75_codret'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            $resac = db_query("insert into db_acount values($acount,1206,7271,'','".AddSlashes(pg_result($resaco,0,'e75_codgera'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            $resac = db_query("insert into db_acount values($acount,1206,7272,'','".AddSlashes(pg_result($resaco,0,'e75_arquivoret'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            $resac = db_query("insert into db_acount values($acount,1206,7273,'','".AddSlashes(pg_result($resaco,0,'e75_febraban'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            $resac = db_query("insert into db_acount values($acount,1206,7282,'','".AddSlashes(pg_result($resaco,0,'e75_seqarq'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            $resac = db_query("insert into db_acount values($acount,1206,7287,'','".AddSlashes(pg_result($resaco,0,'e75_codfebraban'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            $resac = db_query("insert into db_acount values($acount,1206,18399,'','".AddSlashes(pg_result($resaco,0,'e75_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
        }
        return true;
    }
    // funcao para alteracao
    public function alterar ($e75_codret=null) {
        $this->atualizacampos();
        $sql = " update empagedadosret set ";
        $virgula = "";
        if(trim($this->e75_codret)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e75_codret"])){
            $sql  .= $virgula." e75_codret = $this->e75_codret ";
            $virgula = ",";
            if(trim($this->e75_codret) == null ){
                $this->erro_sql = " Campo Código do retorno nao Informado.";
                $this->erro_campo = "e75_codret";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->e75_codgera)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e75_codgera"])){
            $sql  .= $virgula." e75_codgera = $this->e75_codgera ";
            $virgula = ",";
            if(trim($this->e75_codgera) == null ){
                $this->erro_sql = " Campo Código nao Informado.";
                $this->erro_campo = "e75_codgera";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->e75_arquivoret)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e75_arquivoret"])){
            $sql  .= $virgula." e75_arquivoret = '$this->e75_arquivoret' ";
            $virgula = ",";
        }
        if(trim($this->e75_febraban)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e75_febraban"])){
            $sql  .= $virgula." e75_febraban = '$this->e75_febraban' ";
            $virgula = ",";
        }
        if(trim($this->e75_seqarq)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e75_seqarq"])){
            $sql  .= $virgula." e75_seqarq = $this->e75_seqarq ";
            $virgula = ",";
            if(trim($this->e75_seqarq) == null ){
                $this->erro_sql = " Campo Sequencial do arquivo nao Informado.";
                $this->erro_campo = "e75_seqarq";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->e75_codfebraban)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e75_codfebraban"])){
            $sql  .= $virgula." e75_codfebraban = '$this->e75_codfebraban' ";
            $virgula = ",";
            if(trim($this->e75_codfebraban) == null ){
                $this->erro_sql = " Campo Código do banco FEBRABAN nao Informado.";
                $this->erro_campo = "e75_codfebraban";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        if(trim($this->e75_ativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["e75_ativo"])){
            $sql  .= $virgula." e75_ativo = '$this->e75_ativo' ";
            $virgula = ",";
            if(trim($this->e75_ativo) == null ){
                $this->erro_sql = " Campo Retorno Ativo nao Informado.";
                $this->erro_campo = "e75_ativo";
                $this->erro_banco = "";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
        }
        $sql .= " where ";
        if($e75_codret!=null){
            $sql .= " e75_codret = $this->e75_codret";
        }
        $resaco = $this->sql_record($this->sql_query_file($this->e75_codret));
        if($this->numrows>0){
            for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac,0,0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,7270,'$this->e75_codret','A')");
                if(isset($GLOBALS["HTTP_POST_VARS"]["e75_codret"]) || $this->e75_codret != "")
                    $resac = db_query("insert into db_acount values($acount,1206,7270,'".AddSlashes(pg_result($resaco,$conresaco,'e75_codret'))."','$this->e75_codret',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["e75_codgera"]) || $this->e75_codgera != "")
                    $resac = db_query("insert into db_acount values($acount,1206,7271,'".AddSlashes(pg_result($resaco,$conresaco,'e75_codgera'))."','$this->e75_codgera',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["e75_arquivoret"]) || $this->e75_arquivoret != "")
                    $resac = db_query("insert into db_acount values($acount,1206,7272,'".AddSlashes(pg_result($resaco,$conresaco,'e75_arquivoret'))."','$this->e75_arquivoret',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["e75_febraban"]) || $this->e75_febraban != "")
                    $resac = db_query("insert into db_acount values($acount,1206,7273,'".AddSlashes(pg_result($resaco,$conresaco,'e75_febraban'))."','$this->e75_febraban',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["e75_seqarq"]) || $this->e75_seqarq != "")
                    $resac = db_query("insert into db_acount values($acount,1206,7282,'".AddSlashes(pg_result($resaco,$conresaco,'e75_seqarq'))."','$this->e75_seqarq',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["e75_codfebraban"]) || $this->e75_codfebraban != "")
                    $resac = db_query("insert into db_acount values($acount,1206,7287,'".AddSlashes(pg_result($resaco,$conresaco,'e75_codfebraban'))."','$this->e75_codfebraban',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                if(isset($GLOBALS["HTTP_POST_VARS"]["e75_ativo"]) || $this->e75_ativo != "")
                    $resac = db_query("insert into db_acount values($acount,1206,18399,'".AddSlashes(pg_result($resaco,$conresaco,'e75_ativo'))."','$this->e75_ativo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        $result = db_query($sql);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Dados do arquivo retorno do banco nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->e75_codret;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        }else{
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Dados do arquivo retorno do banco nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->e75_codret;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            }else{
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->e75_codret;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
    // funcao para exclusao
    public function excluir ($e75_codret=null,$dbwhere=null) {
        if($dbwhere==null || $dbwhere==""){
            $resaco = $this->sql_record($this->sql_query_file($e75_codret));
        }else{
            $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
        }
        if(($resaco!=false)||($this->numrows!=0)){
            for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
                $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
                $acount = pg_result($resac,0,0);
                $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
                $resac = db_query("insert into db_acountkey values($acount,7270,'$e75_codret','E')");
                $resac = db_query("insert into db_acount values($acount,1206,7270,'','".AddSlashes(pg_result($resaco,$iresaco,'e75_codret'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1206,7271,'','".AddSlashes(pg_result($resaco,$iresaco,'e75_codgera'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1206,7272,'','".AddSlashes(pg_result($resaco,$iresaco,'e75_arquivoret'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1206,7273,'','".AddSlashes(pg_result($resaco,$iresaco,'e75_febraban'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1206,7282,'','".AddSlashes(pg_result($resaco,$iresaco,'e75_seqarq'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1206,7287,'','".AddSlashes(pg_result($resaco,$iresaco,'e75_codfebraban'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
                $resac = db_query("insert into db_acount values($acount,1206,18399,'','".AddSlashes(pg_result($resaco,$iresaco,'e75_ativo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
            }
        }
        $sql = " delete from empagedadosret
                    where ";
        $sql2 = "";
        if($dbwhere==null || $dbwhere ==""){
            if($e75_codret != ""){
                if($sql2!=""){
                    $sql2 .= " and ";
                }
                $sql2 .= " e75_codret = $e75_codret ";
            }
        }else{
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if($result==false){
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Dados do arquivo retorno do banco nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$e75_codret;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        }else{
            if(pg_affected_rows($result)==0){
                $this->erro_banco = "";
                $this->erro_sql = "Dados do arquivo retorno do banco nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$e75_codret;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            }else{
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$e75_codret;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = pg_affected_rows($result);
                return true;
            }
        }
    }
    // funcao do recordset
    public function sql_record($sql) {
        $result = db_query($sql);
        if($result==false){
            $this->numrows    = 0;
            $this->erro_banco = str_replace("\n","",@pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_numrows($result);
        if($this->numrows==0){
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:empagedadosret";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
    // funcao do sql
    public function sql_query ( $e75_codret=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from empagedadosret ";
        $sql .= "      inner join empagegera  on  empagegera.e87_codgera = empagedadosret.e75_codgera";
        $sql2 = "";
        if($dbwhere==""){
            if($e75_codret!=null ){
                $sql2 .= " where empagedadosret.e75_codret = $e75_codret ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    public function sql_query_bco ( $e75_codret=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from empagedadosret ";
        $sql .= "      inner join empagedadosretmov  on  empagedadosretmov.e76_codret = empagedadosret.e75_codret ";
        $sql .= "      inner join empagegera  on  empagegera.e87_codgera = empagedadosret.e75_codgera ";
        $sql .= "      inner join db_bancos on db_bancos.db90_codban = empagedadosret.e75_codfebraban ";
        $sql2 = "";
        if($dbwhere==""){
            if($e75_codret!=null ){
                $sql2 .= " where empagedadosret.e75_codret = $e75_codret ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    // funcao do sql
    public function sql_query_file ( $e75_codret=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from empagedadosret ";
        $sql2 = "";
        if($dbwhere==""){
            if($e75_codret!=null ){
                $sql2 .= " where empagedadosret.e75_codret = $e75_codret ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    /**
     *
     * Busca movimentação
     * @param integer $e75_codret
     * @param string $campos
     * @param string $ordem
     * @param string $dbwhere
     */
    public function sql_query_retmov ($e75_codret=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from empagedadosret ";
        $sql .= "      inner join empagedadosretmov           on empagedadosretmov.e76_codret = empagedadosret.e75_codret ";
        $sql .= "      inner join empagedadosretmovocorrencia on empagedadosretmovocorrencia.e02_empagedadosretmov = empagedadosretmov.e76_codmov ";
        $sql .= "                                            and empagedadosretmovocorrencia.e02_empagedadosret    = empagedadosretmov.e76_codret ";
        $sql .= "      inner join empageconfgera              on empageconfgera.e90_codmov                         = empagedadosretmov.e76_codmov ";
        $sql2 = "";
        if($dbwhere==""){
            if($e75_codret!=null ){
                $sql2 .= " where empagedadosret.e75_codret = $e75_codret ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
    public function sql_query_erro_processamento ($e75_codret=null,$campos="*",$ordem=null,$dbwhere=""){
        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from empagedadosret ";
        $sql .= "      inner join empagedadosretmov  on empagedadosretmov.e76_codret = empagedadosret.e75_codret       ";
        $sql .= "      inner join empagedadosretmovocorrencia  on empagedadosretmovocorrencia.e02_empagedadosret    = empagedadosret.e75_codret   ";
        $sql .= "                                             and empagedadosretmovocorrencia.e02_empagedadosretmov = empagedadosretmov.e76_codmov ";
        $sql .= "      inner join errobanco          on errobanco.e92_sequencia      = empagedadosretmovocorrencia.e02_errobanco ";
        $sql .= "      left  join empageslip         on empageslip.e89_codmov        = empagedadosretmov.e76_codmov    ";
        $sql .= "      left  join slip               on slip.k17_codigo              = empageslip.e89_codigo           ";
        $sql .= "      left  join slipnum            on slipnum.k17_codigo           = slip.k17_codigo ";
        $sql .= "      left  join empord             on empord.e82_codmov            = empagedadosretmov.e76_codmov    ";
        $sql .= "      left  join empagemov          on empord.e82_codmov            = empagemov.e81_codmov or e89_codmov =  e81_codmov ";
        $sql .= "      left  join pagordem           on pagordem.e50_codord          = empord.e82_codord               ";
        $sql .= "      left  join empempenho         on empempenho.e60_numemp        = pagordem.e50_numemp             ";
        $sql .= "      left  join cgm                on empempenho.e60_numcgm        = cgm.z01_numcgm or slipnum.k17_numcgm =  cgm.z01_numcgm              ";

        $sql2 = "";
        if($dbwhere==""){
            if($e75_codret!=null ){
                $sql2 .= " where empagedadosret.e75_codret = $e75_codret ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }


    public function sql_query_retorna_arquivos($e75_codret=null,$campos="*",$ordem=null,$dbwhere=""){

        $sql = "select ";
        if($campos != "*" ){
            $campos_sql = explode("#",$campos);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }else{
            $sql .= $campos;
        }
        $sql .= " from empagedadosret ";
        $sql .= "      inner join empagedadosretmov  on empagedadosretmov.e76_codret = empagedadosret.e75_codret       ";
        $sql .= "      inner join empagedadosretmovocorrencia  on empagedadosretmovocorrencia.e02_empagedadosret    = empagedadosret.e75_codret   ";
        $sql .= "                                             and empagedadosretmovocorrencia.e02_empagedadosretmov = empagedadosretmov.e76_codmov ";
        $sql .= "      inner join errobanco          on errobanco.e92_sequencia      = empagedadosretmovocorrencia.e02_errobanco ";
        $sql .= "      inner join empageconfgera     on empageconfgera.e90_codmov                         = empagedadosretmov.e76_codmov ";
        $sql .= "      inner join empagegera         on e87_codgera = e90_codgera ";
        $sql .= "      inner join empagemov          on e81_codmov = e90_codmov ";
        $sql .= "      inner join empage             on e80_codage = e81_codage ";
        $sql .= "      inner join empagepag          on e85_codmov = e81_codmov ";
        $sql .= "      inner join empagetipo         on empagetipo.e83_codtipo = empagepag.e85_codtipo ";
        $sql .= " 	   inner join conplanoreduz on c61_reduz = e83_conta and c61_anousu = ".db_getsession("DB_anousu")." and c61_instit=".db_getsession("DB_instit");
        $sql .= " 	   inner join conplanocontabancaria on c56_anousu = c61_anousu and c56_reduz = c61_reduz ";

        $sql2 = "";

        if($dbwhere==""){
            if($e75_codret!=null ){
                $sql2 .= " where empagedadosret.e75_codret = $e75_codret ";
            }
        }else if($dbwhere != ""){
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if($ordem != null ){
            $sql .= " order by ";
            $campos_sql = explode("#",$ordem);
            $virgula = "";
            for($i=0;$i<sizeof($campos_sql);$i++){
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }




    public function sql_query_retorna_arquivosFiltrados($conta, $data, $concilia){


            $clconcilia = new cl_concilia;
            $sqlMenorDataCaixa  = " select min(menordatacaixa) as menordatacaixa ";
            $sqlMenorDataCaixa .= "   from (  select min(k68_data) as menordatacaixa from concilia ";
            $sqlMenorDataCaixa .= "	       union ";
            $sqlMenorDataCaixa .= "	  	 	    select min(k89_data) as menordatacaixa from conciliapendcorrente ) as x ";
            $rsMenorDataCaixa   = $clconcilia->sql_record($sqlMenorDataCaixa);
            if($clconcilia->numrows > 0){
              $menordatacaixa = db_utils::fieldsMemory($rsMenorDataCaixa,0)->menordatacaixa;
            }else{
              $menordatacaixa = '2006-01-01';
            }
            $sqlAutentica  = " select distinct arquivo as e75_codgera,   ";
            $sqlAutentica .= "'codret: ' || e75_codret || '-' || e75_arquivoret ";
            $sqlAutentica .= "   from ( select distinct ";
            $sqlAutentica .= "                 caixa as caixa, ";
            $sqlAutentica .= "                 autent as autent, ";
            $sqlAutentica .= "                 arquivo as arquivo, ";
            $sqlAutentica .= "                 data as data, ";
            $sqlAutentica .= "                 valor_debito as valor_debito, ";
            $sqlAutentica .= "                 valor_credito as valor_credito, ";
            $sqlAutentica .= "                 receita as receita, ";
            $sqlAutentica .= "                 cheque as cheque, ";
            $sqlAutentica .= "                 credor as credor, ";
            $sqlAutentica .= "                 detalhe as detalhe, ";
            $sqlAutentica .= "                 case ";
            $sqlAutentica .= "                   when x.classe = 'conciliado' then 'conciliado' ";
            $sqlAutentica .= "                   when ( k86_data is not null and k86_documento is not null ) then 'preselecionado' ";
            $sqlAutentica .= "                   else x.classe ";
            $sqlAutentica .= "                 end as classe, ";
            $sqlAutentica .= "                 itemconciliacao, ";
            $sqlAutentica .= "                 justificativa, ";
            $sqlAutentica .= "                 erro as erro ";
            $sqlAutentica .= "            from ( ";

            // pendentes
            $sqlAutentica .= "                   select distinct ";
            $sqlAutentica .= "                          ricaixa           as caixa, ";
            $sqlAutentica .= "                          riautent          as autent, ";
            $sqlAutentica .= "                          ( select e75_codgera
                                                                   from conlancamcorrente
                                                             inner join corlanc on c86_id = k12_id
                                                                               and c86_data = k12_data
                                                                               and c86_autent = k12_autent
                                                             inner join conlancamslip on c84_slip = k12_codigo
                                                                                     and c84_conlancam = c86_conlancam
                                                             inner join empageslip on e89_codigo = k12_codigo
                                                             inner join empagedadosretmov on e76_codmov = e89_codmov
                                                                                         and e76_processado = true
                                                                                         and e76_dataefet = k12_data
                                                             inner join empagedadosret on e75_codret = e76_codret
                                                             inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret
                                                                                                   and e02_empagedadosretmov = e76_codmov
                                                                                                   and e02_errobanco in ( 2, 269)
                                                                  where corlanc.k12_data = ridata
                                                                    and corlanc.k12_id = ricaixa
                                                                    and corlanc.k12_autent = riautent
                                                         union select e75_codgera
                                                                 from conlancamcorgrupocorrente
                                                           inner join corgrupocorrente on k105_sequencial = c23_corgrupocorrente
                                                           inner join corempagemov  on k12_id = k105_id
                                                                                   and k12_data = k105_data
                                                                                   and k12_autent = k105_autent
                                                           inner join empagedadosretmov on e76_codmov = k12_codmov
                                                                                       and e76_processado = true
                                                           inner join empagedadosret on e75_codret = e76_codret
                                                           inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret
                                                                                                 and e02_empagedadosretmov = e76_codmov
                                                                                                 and e02_errobanco in (2, 269)
                                                                where corempagemov.k12_data = ridata
                                                                  and corempagemov.k12_id = ricaixa
                                                                  and corempagemov.k12_autent = riautent ) as arquivo, ";
            $sqlAutentica .= "                          ridata            as data, ";
            $sqlAutentica .= "                          rnvalordebito     as valor_debito, ";
            $sqlAutentica .= "                          rivalorcredito    as valor_credito, ";
            $sqlAutentica .= "                          rireceita         as receita, ";
            $sqlAutentica .= "                          richeque          as cheque, ";
            $sqlAutentica .= "                          rtcredor          as credor, ";
            $sqlAutentica .= "                          rtdetalhe         as detalhe, ";
            $sqlAutentica .= "                          k89_justificativa as justificativa, ";
            $sqlAutentica .= "                          'pendente'        as classe,";
            $sqlAutentica .= "                          0                 as itemconciliacao, ";
            $sqlAutentica .= "                          rberro            as erro ";
            $sqlAutentica .= "                     from conciliapendcorrente ";
            $sqlAutentica .= "                          inner join concilia on k68_sequencial = k89_concilia ";
            $sqlAutentica .= "                          inner join ( select * from fc_extratocaixa(" . db_getsession('DB_instit') . ",$conta,'" . $menordatacaixa . "','" . $data . "',false ) ) as x ";
            $sqlAutentica .= "                                                          on ricaixa  = k89_id ";
            $sqlAutentica .= "                                                         and riautent = k89_autent ";
            $sqlAutentica .= "                                                         and ridata   = k89_data ";
            $sqlAutentica .= "                           left join conciliacor          on ricaixa  = k84_id ";
            $sqlAutentica .= "                                                         and riautent = k84_autent ";
            $sqlAutentica .= "                                                         and ridata   = k84_data ";
            $sqlAutentica .= "                           left join conciliaitem         on k83_sequencial = k84_conciliaitem ";
            $sqlAutentica .= "                                                         and k83_concilia = (select k68_sequencial ";
            $sqlAutentica .= "                                                                               from concilia  ";
            $sqlAutentica .= "                                                                              where k68_contabancaria = {$conta} ";
            $sqlAutentica .= "                                                                                and k68_data = '" . $data . "' ) ";
            $sqlAutentica .= "                     where ( k83_sequencial is null ) ";
            $sqlAutentica .= "                       and k68_sequencial = " . $concilia;
            $sqlAutentica .= "                     union all ";

            // conciliados
            $sqlAutentica .= "                    select distinct ";
            $sqlAutentica .= "                           ricaixa        as caixa, ";
            $sqlAutentica .= "                           riautent       as autent, ";
            $sqlAutentica .= "                           ( select e75_codgera
                                                                    from conlancamcorrente
                                                               inner join corlanc on c86_id = k12_id
                                                                                 and c86_data = k12_data
                                                                                 and c86_autent = k12_autent
                                                               inner join conlancamslip on c84_slip = k12_codigo
                                                                                       and c84_conlancam = c86_conlancam
                                                               inner join empageslip on e89_codigo = k12_codigo
                                                               inner join empagedadosretmov on e76_codmov = e89_codmov
                                                                                           and e76_processado = true
                                                                                           and e76_dataefet = k12_data
                                                               inner join empagedadosret on e75_codret = e76_codret
                                                               inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret
                                                                                                     and e02_empagedadosretmov = e76_codmov
                                                                                                     and e02_errobanco in ( 2, 269)
                                                                    where corlanc.k12_data = ridata
                                                                      and corlanc.k12_id = ricaixa
                                                                      and corlanc.k12_autent = riautent
                                                          union select e75_codgera
                                                                  from conlancamcorgrupocorrente
                                                            inner join corgrupocorrente on k105_sequencial = c23_corgrupocorrente
                                                            inner join corempagemov  on k12_id = k105_id
                                                                                    and k12_data = k105_data
                                                                                    and k12_autent = k105_autent
                                                            inner join empagedadosretmov on e76_codmov = k12_codmov
                                                                                        and e76_processado = true
                                                            inner join empagedadosret on e75_codret = e76_codret
                                                            inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret
                                                                                                  and e02_empagedadosretmov = e76_codmov
                                                                                                  and e02_errobanco in ( 2, 269)
                                                                 where corempagemov.k12_data = ridata
                                                                   and corempagemov.k12_id = ricaixa
                                                                   and corempagemov.k12_autent = riautent ) as arquivo, ";
        $sqlAutentica .= "                           ridata         as data, ";
        $sqlAutentica .= "                           rnvalordebito  as valor_debito, ";
        $sqlAutentica .= "                           rivalorcredito as valor_credito, ";
        $sqlAutentica .= "                           rireceita      as receita, ";
        $sqlAutentica .= "                           richeque       as cheque, ";
        $sqlAutentica .= "                           rtcredor       as credor, ";
        $sqlAutentica .= "                           rtdetalhe      as detalhe, ";
        $sqlAutentica .= "                           ''             as justificativa, ";
        $sqlAutentica .= "                           'conciliado'   as classe, ";
        $sqlAutentica .= "                           k83_sequencial as itemconciliacao, ";
        $sqlAutentica .= "                           rberro         as erro ";
        $sqlAutentica .= "                      from conciliacor ";
        $sqlAutentica .= "                           inner join conciliaitem on k83_sequencial = k84_conciliaitem ";
        $sqlAutentica .= "                           inner join concilia     on k83_concilia   = k68_sequencial ";
        $sqlAutentica .= "                           inner join fc_extratocaixa(".db_getsession('DB_instit').",$conta,'".$menordatacaixa."','".$data."',false ) ";
        $sqlAutentica .= "                                                   on k84_id     = ricaixa ";
        $sqlAutentica .= "                                                  and k84_autent = riautent ";
        $sqlAutentica .= "                                                  and k84_data   = ridata ";
        $sqlAutentica .= "                     where k68_sequencial = ".$concilia;

        $sqlAutentica .= "                     union all ";

                   // registros normais
            $sqlAutentica .= "                    select distinct ";
            $sqlAutentica .= "                           ricaixa        as caixa, ";
            $sqlAutentica .= "                           riautent       as autent, ";
            $sqlAutentica .= "                           ( select e75_codgera
                                                            from conlancamcorrente
                                                         inner join corlanc on c86_id = k12_id
                                                                              and c86_data = k12_data
                                                                              and c86_autent = k12_autent
                                                         inner join conlancamslip on c84_slip = k12_codigo
                                                                                    and c84_conlancam = c86_conlancam
                                                         inner join empageslip on e89_codigo = k12_codigo
                                                         inner join empagedadosretmov on e76_codmov = e89_codmov
                                                                                      and e76_processado = true
                                                                                      and e76_dataefet = k12_data
                                                         inner join empagedadosret on e75_codret = e76_codret
                                                         inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret
                                                                                                and e02_empagedadosretmov = e76_codmov
                                                                                                and e02_errobanco in (2, 269)
                                                            where corlanc.k12_data = ridata
                                                              and corlanc.k12_id = ricaixa
                                                              and corlanc.k12_autent = riautent
                                                         union select e75_codgera from conlancamcorgrupocorrente
                                                            inner join corgrupocorrente on k105_sequencial = c23_corgrupocorrente
                                                            inner join corempagemov  on k12_id = k105_id
                                                                                     and k12_data = k105_data and k12_autent = k105_autent
                                                           inner join empagedadosretmov on e76_codmov = k12_codmov
                                                                                        and e76_processado = true
                                                           inner join empagedadosret on e75_codret = e76_codret
                                                           inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret
                                                                                           and e02_empagedadosretmov = e76_codmov
                                                                                           and e02_errobanco in ( 2, 269)
                                                               where corempagemov.k12_data = ridata
                                                                 and corempagemov.k12_id = ricaixa
                                                                 and corempagemov.k12_autent = riautent ) as arquivo, ";
            $sqlAutentica .= "	 		                     ridata         as data, ";
            $sqlAutentica .= "  			                   rnvalordebito  as valor_debito, ";
            $sqlAutentica .= "  			                   rivalorcredito as valor_credito, ";
            $sqlAutentica .= "			                     rireceita      as receita, ";
            $sqlAutentica .= "			                     richeque       as cheque, ";
            $sqlAutentica .= "			                     rtcredor       as credor, ";
            $sqlAutentica .= "			                     rtdetalhe      as detalhe, ";
            $sqlAutentica .= "                           ''             as justificativa, ";
            $sqlAutentica .= "                           'normal'       as classe, ";
            $sqlAutentica .= "                           0              as itemconciliacao, ";
            $sqlAutentica .= "			                     rberro         as erro ";
            $sqlAutentica .= "                      from fc_extratocaixa(".db_getsession('DB_instit').",$conta,'".$data."','".$data."',false ) ";
            $sqlAutentica .= "                           left join conciliacor          on ricaixa    = k84_id       ";
            $sqlAutentica .= "                                                         and riautent   = k84_autent   ";
            $sqlAutentica .= "                                                         and ridata     = k84_data     ";
            $sqlAutentica .= "                           left join conciliaitem         on k83_sequencial = k84_conciliaitem ";
            $sqlAutentica .= "                                                         and k83_concilia = (select k68_sequencial ";
            $sqlAutentica .= "                                                                               from concilia  ";
            $sqlAutentica .= "                                                                              where k68_contabancaria = {$conta} ";
            $sqlAutentica .= "                                                                                and k68_data = '".$data."' ) ";
            $sqlAutentica .= "                           left join conciliapendcorrente on k89_id     = ricaixa    ";
            $sqlAutentica .= "                                                         and k89_autent = riautent   ";
            $sqlAutentica .= "			                                                   and k89_data   = ridata     ";
            $sqlAutentica .= "                                                         and k89_concilia = (select k68_sequencial ";
            $sqlAutentica .= "                                                                               from concilia  ";
            $sqlAutentica .= "                                                                              where k68_contabancaria = {$conta} ";
            $sqlAutentica .= "                                                                                and k68_data = '".$data."' ) ";
                   //$sqlAutentica .= "	                                                       and conciliaitem.k83_concilia = conciliapendcorrente.k89_concilia ";
            $sqlAutentica .= "                     where ( k89_id is null and k89_autent is null and k89_data is null ) ";
            $sqlAutentica .= "                       and ( k83_sequencial is null ) ";

            $sqlAutentica .= "                       and not exists (select 1  ";
            $sqlAutentica .= "                                         from conciliacor ";
            $sqlAutentica .= "                                         inner join conciliaitem  on k83_sequencial    = k84_conciliaitem ";
            $sqlAutentica .= "                                         inner join concilia      on k68_sequencial    = k83_concilia ";
            $sqlAutentica .= "                                                                and k68_contabancaria = {$conta} ";
            $sqlAutentica .= "                                                                and k68_data          = '".$data."' ";
            $sqlAutentica .= "                                                              where k84_id     = ricaixa ";
            $sqlAutentica .= "                                                                and k84_autent = riautent ";
            $sqlAutentica .= "                                                                and k84_data   = ridata ) ";

            $sqlAutentica .= "                 ) as x ";
            $sqlAutentica .= "                 left join extratolinha         on lpad(trim(x.cheque::varchar),20,'0') = lpad(trim(k86_documento),20,'0') ";
            $sqlAutentica .= "                                               and k86_contabancaria = $conta ";
            $sqlAutentica .= "                                               and (k86_data = x.data or k86_data <= '".$data."') ";
            $sqlAutentica .= "                                               and x.cheque <> 0 ";
            $sqlAutentica .= "                                               and k86_documento <> '0' ";
            $sqlAutentica .= "        ) as x
                                           inner join empagedadosret on arquivo = e75_codgera ";
            $sqlAutentica .= " where not exists (select 1
                                                          from corgrupocorrente
                                                         where k105_autent = autent
                                                           and k105_id     = caixa
                                                           and k105_data   = data
                                                           and k105_corgrupotipo in (2,3,5,6)
                                                           and extract(year from k105_data) <= 2012 )  ";
            $sqlAutentica .= "  order by 1";


        return $sqlAutentica;
    }
}

