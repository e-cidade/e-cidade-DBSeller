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

//MODULO: Ambulatorial
//CLASSE DA ENTIDADE sau_agendaveiculo

class cl_sau_agendaveiculo {
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
   var $s121_i_codigo = 0;
   var $s121_i_veiculo = 0;
   var $s121_i_motorista = 0;
   var $s121_i_agendatransporte = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 s121_i_codigo = int4 = Código
                 s121_i_veiculo = int4 = Veículo
                 s121_i_motorista = int4 = Motorista
                 s121_i_agendatransporte = int4 = Transporte
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("sau_agendaveiculo");
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
       $this->s121_i_codigo = ($this->s121_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["s121_i_codigo"]:$this->s121_i_codigo);
       $this->s121_i_veiculo = ($this->s121_i_veiculo == ""?@$GLOBALS["HTTP_POST_VARS"]["s121_i_veiculo"]:$this->s121_i_veiculo);
       $this->s121_i_motorista = ($this->s121_i_motorista == ""?@$GLOBALS["HTTP_POST_VARS"]["s121_i_motorista"]:$this->s121_i_motorista);
       $this->s121_i_agendatransporte = ($this->s121_i_agendatransporte == ""?@$GLOBALS["HTTP_POST_VARS"]["s121_i_agendatransporte"]:$this->s121_i_agendatransporte);
     }else{
       $this->s121_i_codigo = ($this->s121_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["s121_i_codigo"]:$this->s121_i_codigo);
     }
   }
   // funcao para inclusao
   public function incluir ($s121_i_codigo){
      $this->atualizacampos();
     if($this->s121_i_veiculo == null ){
       $this->erro_sql = " Campo Veículo nao Informado.";
       $this->erro_campo = "s121_i_veiculo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->s121_i_motorista == null ){
       $this->s121_i_motorista = "null";
     }
     if($this->s121_i_agendatransporte == null ){
       $this->erro_sql = " Campo Transporte nao Informado.";
       $this->erro_campo = "s121_i_agendatransporte";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($s121_i_codigo == "" || $s121_i_codigo == null ){
       $result = db_query("select nextval('sau_agendaveiculo_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: sau_agendaveiculo_codigo_seq do campo: s121_i_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->s121_i_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from sau_agendaveiculo_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $s121_i_codigo)){
         $this->erro_sql = " Campo s121_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->s121_i_codigo = $s121_i_codigo;
       }
     }
     if(($this->s121_i_codigo == null) || ($this->s121_i_codigo == "") ){
       $this->erro_sql = " Campo s121_i_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into sau_agendaveiculo(
                                       s121_i_codigo
                                      ,s121_i_veiculo
                                      ,s121_i_motorista
                                      ,s121_i_agendatransporte
                       )
                values (
                                $this->s121_i_codigo
                               ,$this->s121_i_veiculo
                               ,$this->s121_i_motorista
                               ,$this->s121_i_agendatransporte
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Agenda Externa Veículo ($this->s121_i_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Agenda Externa Veículo já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Agenda Externa Veículo ($this->s121_i_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->s121_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->s121_i_codigo));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,14300,'$this->s121_i_codigo','I')");
       $resac = db_query("insert into db_acount values($acount,2515,14300,'','".AddSlashes(pg_result($resaco,0,'s121_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2515,14301,'','".AddSlashes(pg_result($resaco,0,'s121_i_veiculo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2515,14302,'','".AddSlashes(pg_result($resaco,0,'s121_i_motorista'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2515,14344,'','".AddSlashes(pg_result($resaco,0,'s121_i_agendatransporte'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($s121_i_codigo=null) {
      $this->atualizacampos();
     $sql = " update sau_agendaveiculo set ";
     $virgula = "";
     if(trim($this->s121_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s121_i_codigo"])){
       $sql  .= $virgula." s121_i_codigo = $this->s121_i_codigo ";
       $virgula = ",";
       if(trim($this->s121_i_codigo) == null ){
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "s121_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->s121_i_veiculo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s121_i_veiculo"])){
       $sql  .= $virgula." s121_i_veiculo = $this->s121_i_veiculo ";
       $virgula = ",";
       if(trim($this->s121_i_veiculo) == null ){
         $this->erro_sql = " Campo Veículo nao Informado.";
         $this->erro_campo = "s121_i_veiculo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->s121_i_motorista)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s121_i_motorista"])){
        if(trim($this->s121_i_motorista)=="" && isset($GLOBALS["HTTP_POST_VARS"]["s121_i_motorista"])){
           $this->s121_i_motorista = "0" ;
        }
       $sql  .= $virgula." s121_i_motorista = $this->s121_i_motorista ";
       $virgula = ",";
     }
     if(trim($this->s121_i_agendatransporte)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s121_i_agendatransporte"])){
       $sql  .= $virgula." s121_i_agendatransporte = $this->s121_i_agendatransporte ";
       $virgula = ",";
       if(trim($this->s121_i_agendatransporte) == null ){
         $this->erro_sql = " Campo Transporte nao Informado.";
         $this->erro_campo = "s121_i_agendatransporte";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($s121_i_codigo!=null){
       $sql .= " s121_i_codigo = $this->s121_i_codigo";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->s121_i_codigo));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14300,'$this->s121_i_codigo','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s121_i_codigo"]) || $this->s121_i_codigo != "")
           $resac = db_query("insert into db_acount values($acount,2515,14300,'".AddSlashes(pg_result($resaco,$conresaco,'s121_i_codigo'))."','$this->s121_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s121_i_veiculo"]) || $this->s121_i_veiculo != "")
           $resac = db_query("insert into db_acount values($acount,2515,14301,'".AddSlashes(pg_result($resaco,$conresaco,'s121_i_veiculo'))."','$this->s121_i_veiculo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s121_i_motorista"]) || $this->s121_i_motorista != "")
           $resac = db_query("insert into db_acount values($acount,2515,14302,'".AddSlashes(pg_result($resaco,$conresaco,'s121_i_motorista'))."','$this->s121_i_motorista',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s121_i_agendatransporte"]) || $this->s121_i_agendatransporte != "")
           $resac = db_query("insert into db_acount values($acount,2515,14344,'".AddSlashes(pg_result($resaco,$conresaco,'s121_i_agendatransporte'))."','$this->s121_i_agendatransporte',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Agenda Externa Veículo nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->s121_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Agenda Externa Veículo nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->s121_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->s121_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($s121_i_codigo=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($s121_i_codigo));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14300,'$s121_i_codigo','E')");
         $resac = db_query("insert into db_acount values($acount,2515,14300,'','".AddSlashes(pg_result($resaco,$iresaco,'s121_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2515,14301,'','".AddSlashes(pg_result($resaco,$iresaco,'s121_i_veiculo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2515,14302,'','".AddSlashes(pg_result($resaco,$iresaco,'s121_i_motorista'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2515,14344,'','".AddSlashes(pg_result($resaco,$iresaco,'s121_i_agendatransporte'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from sau_agendaveiculo
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($s121_i_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " s121_i_codigo = $s121_i_codigo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Agenda Externa Veículo nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$s121_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Agenda Externa Veículo nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$s121_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$s121_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:sau_agendaveiculo";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $s121_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from sau_agendaveiculo ";
     $sql .= "      inner join veiculos  on  veiculos.ve01_codigo = sau_agendaveiculo.s121_i_veiculo";
     $sql .= "      left  join veicmotoristas  on  veicmotoristas.ve05_codigo = sau_agendaveiculo.s121_i_motorista";
     $sql .= "      inner join sau_agendatransporte  on  sau_agendatransporte.s124_i_codigo = sau_agendaveiculo.s121_i_agendatransporte";
     $sql .= "      inner join ceplocalidades  on  ceplocalidades.cp05_codlocalidades = veiculos.ve01_ceplocalidades";
     $sql .= "      inner join veiccadtipo  on  veiccadtipo.ve20_codigo = veiculos.ve01_veiccadtipo";
     $sql .= "      inner join veiccadmarca  on  veiccadmarca.ve21_codigo = veiculos.ve01_veiccadmarca";
     $sql .= "      inner join veiccadmodelo  on  veiccadmodelo.ve22_codigo = veiculos.ve01_veiccadmodelo";
     $sql .= "      inner join veiccadcor  on  veiccadcor.ve23_codigo = veiculos.ve01_veiccadcor";
     $sql .= "      inner join veiccadtipocapacidade  on  veiccadtipocapacidade.ve24_codigo = veiculos.ve01_veiccadtipocapacidade";
     $sql .= "      inner join veiccadcategcnh  on  veiccadcategcnh.ve30_codigo = veiculos.ve01_veiccadcategcnh";
     $sql .= "      inner join veiccadproced  on  veiccadproced.ve25_codigo = veiculos.ve01_veiccadproced";
     $sql .= "      inner join veiccadpotencia  on  veiccadpotencia.ve31_codigo = veiculos.ve01_veiccadpotencia";
     $sql .= "      inner join veiccadcateg  as a on   a.ve32_codigo = veiculos.ve01_veiccadcateg";
     $sql .= "      inner join veictipoabast  on  veictipoabast.ve07_sequencial = veiculos.ve01_veictipoabast";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = veicmotoristas.ve05_numcgm";
     $sql .= "      inner join veiccadcategcnh  as b on   b.ve30_codigo = veicmotoristas.ve05_veiccadcategcnh";
     $sql .= "      inner join veiccadmotoristasit  on  veiccadmotoristasit.ve33_codigo = veicmotoristas.ve05_veiccadmotoristasit";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = sau_agendatransporte.s124_i_login";
     $sql .= "      inner join cgs  on  cgs.z01_i_numcgs = sau_agendatransporte.s124_i_numcgs";
     $sql2 = "";
     if($dbwhere==""){
       if($s121_i_codigo!=null ){
         $sql2 .= " where sau_agendaveiculo.s121_i_codigo = $s121_i_codigo ";
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
   public function sql_query_file ( $s121_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from sau_agendaveiculo ";
     $sql2 = "";
     if($dbwhere==""){
       if($s121_i_codigo!=null ){
         $sql2 .= " where sau_agendaveiculo.s121_i_codigo = $s121_i_codigo ";
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
}
?>
