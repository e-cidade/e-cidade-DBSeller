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

//MODULO: cadastro
//CLASSE DA ENTIDADE iptunumpold
class cl_iptunumpold {
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
    public $j130_sequencial = 0; 
    public $j130_anousu = 0; 
    public $j130_matric = 0; 
    public $j130_numpre = 0; 
    public $j130_iptucalclog = 0; 
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 j130_sequencial = int4 = Sequencial
                 j130_anousu = int4 = Exercício
                 j130_matric = int4 = Matrícula
                 j130_numpre = int4 = Numpre
                 j130_iptucalclog = int4 = Iptucalclogmat 
                 ";
   //funcao construtor da classe
    public function __construct()
    {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("iptunumpold");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
   }

    //funcao erro
    public function erro($mostra, $retorna)
    {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\")</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

    // funcao para atualizar campos
    public function atualizacampos($exclusao = false)
    {
     if($exclusao==false){
       $this->j130_sequencial = ($this->j130_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j130_sequencial"]:$this->j130_sequencial);
       $this->j130_anousu = ($this->j130_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["j130_anousu"]:$this->j130_anousu);
       $this->j130_matric = ($this->j130_matric == ""?@$GLOBALS["HTTP_POST_VARS"]["j130_matric"]:$this->j130_matric);
       $this->j130_numpre = ($this->j130_numpre == ""?@$GLOBALS["HTTP_POST_VARS"]["j130_numpre"]:$this->j130_numpre);
       $this->j130_iptucalclog = ($this->j130_iptucalclog == ""?@$GLOBALS["HTTP_POST_VARS"]["j130_iptucalclog"]:$this->j130_iptucalclog);
     }else{
       $this->j130_sequencial = ($this->j130_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j130_sequencial"]:$this->j130_sequencial);
     }
   }

    // funcao para inclusao
    public function incluir($j130_sequencial)
    {
      $this->atualizacampos();
     if($this->j130_anousu == null ){
       $this->erro_sql = " Campo Exercício não informado.";
       $this->erro_campo = "j130_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j130_matric == null ){
       $this->erro_sql = " Campo Matrícula não informado.";
       $this->erro_campo = "j130_matric";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j130_numpre == null ){
       $this->erro_sql = " Campo Numpre não informado.";
       $this->erro_campo = "j130_numpre";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j130_iptucalclog == null ){ 
       $this->erro_sql = " Campo Iptucalclogmat não informado.";
       $this->erro_campo = "j130_iptucalclog";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($j130_sequencial == "" || $j130_sequencial == null ){
       $result = db_query("select nextval('iptunumpold_j130_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: iptunumpold_j130_sequencial_seq do campo: j130_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->j130_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from iptunumpold_j130_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $j130_sequencial)){
         $this->erro_sql = " Campo j130_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->j130_sequencial = $j130_sequencial;
       }
     }
     if(($this->j130_sequencial == null) || ($this->j130_sequencial == "") ){
       $this->erro_sql = " Campo j130_sequencial não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into iptunumpold(
                                       j130_sequencial
                                      ,j130_anousu
                                      ,j130_matric
                                      ,j130_numpre
                                      ,j130_iptucalclog 
                       )
                values (
                                $this->j130_sequencial
                               ,$this->j130_anousu
                               ,$this->j130_matric
                               ,$this->j130_numpre
                               ,$this->j130_iptucalclog 
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "iptunumpold ($this->j130_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "iptunumpold já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "iptunumpold ($this->j130_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->j130_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

     $resaco = $this->sql_record($this->sql_query_file($this->j130_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){

       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,17979,'$this->j130_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,3177,17979,'','".AddSlashes(pg_result($resaco,0,'j130_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3177,17980,'','".AddSlashes(pg_result($resaco,0,'j130_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3177,17981,'','".AddSlashes(pg_result($resaco,0,'j130_matric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,3177,17982,'','".AddSlashes(pg_result($resaco,0,'j130_numpre'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3177,1014379,'','".AddSlashes(pg_result($resaco,0,'j130_iptucalclog'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    // funcao para alteracao
    public function alterar($j130_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update iptunumpold set ";
     $virgula = "";
     if(trim($this->j130_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j130_sequencial"])){
       $sql  .= $virgula." j130_sequencial = $this->j130_sequencial ";
       $virgula = ",";
       if(trim($this->j130_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "j130_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j130_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j130_anousu"])){
       $sql  .= $virgula." j130_anousu = $this->j130_anousu ";
       $virgula = ",";
       if(trim($this->j130_anousu) == null ){
         $this->erro_sql = " Campo Exercício não informado.";
         $this->erro_campo = "j130_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j130_matric)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j130_matric"])){
       $sql  .= $virgula." j130_matric = $this->j130_matric ";
       $virgula = ",";
       if(trim($this->j130_matric) == null ){
         $this->erro_sql = " Campo Matrícula não informado.";
         $this->erro_campo = "j130_matric";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j130_numpre)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j130_numpre"])){
       $sql  .= $virgula." j130_numpre = $this->j130_numpre ";
       $virgula = ",";
       if(trim($this->j130_numpre) == null ){
         $this->erro_sql = " Campo Numpre não informado.";
         $this->erro_campo = "j130_numpre";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->j130_iptucalclog)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j130_iptucalclog"])){ 
       $sql  .= $virgula." j130_iptucalclog = $this->j130_iptucalclog ";
       $virgula = ",";
       if(trim($this->j130_iptucalclog) == null ){ 
         $this->erro_sql = " Campo Iptucalclogmat não informado.";
         $this->erro_campo = "j130_iptucalclog";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($j130_sequencial!=null){
       $sql .= " j130_sequencial = $this->j130_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

     $resaco = $this->sql_record($this->sql_query_file($this->j130_sequencial));
     if($this->numrows>0){

       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,17979,'$this->j130_sequencial','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j130_sequencial"]) || $this->j130_sequencial != "")
           $resac = db_query("insert into db_acount values($acount,3177,17979,'".AddSlashes(pg_result($resaco,$conresaco,'j130_sequencial'))."','$this->j130_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j130_anousu"]) || $this->j130_anousu != "")
           $resac = db_query("insert into db_acount values($acount,3177,17980,'".AddSlashes(pg_result($resaco,$conresaco,'j130_anousu'))."','$this->j130_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j130_matric"]) || $this->j130_matric != "")
           $resac = db_query("insert into db_acount values($acount,3177,17981,'".AddSlashes(pg_result($resaco,$conresaco,'j130_matric'))."','$this->j130_matric',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j130_numpre"]) || $this->j130_numpre != "")
           $resac = db_query("insert into db_acount values($acount,3177,17982,'".AddSlashes(pg_result($resaco,$conresaco,'j130_numpre'))."','$this->j130_numpre',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if (isset($GLOBALS["HTTP_POST_VARS"]["j130_iptucalclog"]) || $this->j130_iptucalclog != "")
           $resac = db_query("insert into db_acount values($acount,3177,1014379,'".AddSlashes(pg_result($resaco,$conresaco,'j130_iptucalclog'))."','$this->j130_iptucalclog',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "iptunumpold não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->j130_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "iptunumpold não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->j130_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->j130_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

   // funcao para exclusao
    public function excluir($j130_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

       $resaco = $this->sql_record($this->sql_query_file($j130_sequencial));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){

       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,17979,'$j130_sequencial','E')");
         $resac = db_query("insert into db_acount values($acount,3177,17979,'','".AddSlashes(pg_result($resaco,$iresaco,'j130_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3177,17980,'','".AddSlashes(pg_result($resaco,$iresaco,'j130_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3177,17981,'','".AddSlashes(pg_result($resaco,$iresaco,'j130_matric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,3177,17982,'','".AddSlashes(pg_result($resaco,$iresaco,'j130_numpre'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,3177,1014379,'','".AddSlashes(pg_result($resaco,$iresaco,'j130_iptucalclog'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from iptunumpold
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($j130_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " j130_sequencial = $j130_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "iptunumpold não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$j130_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "iptunumpold não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$j130_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$j130_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
   }

   // funcao do recordset
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
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:iptunumpold";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

   // funcao do sql
   public function sql_query ( $j130_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from iptunumpold ";
     $sql .= "      inner join iptucalclogmat  on  iptucalclogmat.j28_codigo = iptunumpold.j130_iptucalclog and  iptucalclogmat.j28_matric = iptunumpold.j130_matric";
     $sql .= "      inner join iptubase  on  iptubase.j01_matric = iptucalclogmat.j28_matric";
     $sql .= "      inner join iptucalclog  on  iptucalclog.j27_codigo = iptucalclogmat.j28_codigo";
     $sql .= "      inner join iptucadlogcalc  on  iptucadlogcalc.j62_codigo = iptucalclogmat.j28_tipologcalc";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($j130_sequencial)) {
         $sql2 .= " where iptunumpold.j130_sequencial = $j130_sequencial ";
       }
     } else if (!empty($dbwhere)) {
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
   public function sql_query_file ( $j130_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from iptunumpold ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($j130_sequencial)){
         $sql2 .= " where iptunumpold.j130_sequencial = $j130_sequencial ";
       }
     } else if (!empty($dbwhere)) {
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

  public function salvarIptunumpOld($iAnousu, $iMatric, $iCalclog) {
 
      if ( !db_utils::inTransaction()) {
         throw new Exception ("Sem Transação Ativa");
      }
  
      if (empty($iAnousu)) {
         $this->erro_sql = " Campo Exercício não informado para gerar informações anteriores.";
         $this->erro_campo = "j130_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
      }
      if (empty($iMatric)) {
         $this->erro_sql = " Campo Matrícula não informado para gerar informações anteriores.";
         $this->erro_campo = "j130_matric";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
      }
      if (empty($iCalclog)) {
         $this->erro_sql = " Campo CalcLog não informado para gerar informações anteriores.";
         $this->erro_campo = "j130_iptucalclog";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
      }
  
      //iptunumpold 
      $sSqlIptunump  = "select * from iptunump where j20_anousu = {$iAnousu} and j20_matric = {$iMatric};";
      $rsIptunump = db_query($sSqlIptunump) or die($sSqlIptunump);

      if (!$rsIptunump) {
          throw new DBException("Não foi possivel buscar dados do cálculo(numpre) (".pg_last_error().")");
      }
      if (pg_numrows($rsIptunump) > 0) {
         global $j20_anousu;
         global $j20_matric;
         global $j20_numpre;
         global $j27_codigo;

         db_fieldsmemory($rsIptunump,0);

         $this->j130_anousu         = $j20_anousu;
         $this->j130_matric         = $j20_matric;
         $this->j130_numpre         = $j20_numpre;
         $this->j130_iptucalclog    = $j27_codigo;
         $this->incluir(null);

         if ($this->erro_status == 0) {
            throw new DBException("Não foi possivel salvar dados do cálculo (numpre) (".pg_last_error().")");
            return false;
         }
      }
      return true;
  }  
}
?>
