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

//MODULO: Fiscal
//CLASSE DA ENTIDADE autoexec
class cl_fis_autoexec {
   // cria variaveis de erro
   public $rotulo     = null;
   public $query_sql  = null;
   public $numrows    = 0;
   public $numrows_incluir = 0;
   public $numrows_alterar = 0;
   public $numrows_excluir = 0;
   public $erro_status= null;
   public $erro_sql   = null;
   public $erro_banco = null;
   public $erro_msg   = null;
   public $erro_campo = null;
   public $pagina_retorno = null;
   // cria variaveis do arquivo
   public $y15_codauto = 0;
   public $y15_codigo = 0;
   public $y15_codi = 0;
   public $y15_numero = 0;
   public $y15_compl = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y15_codauto = int4 = Código do Auto de Infração
                 y15_codigo = int4 = cód. Rua/Avenida
                 y15_codi = int4 = Bairro
                 y15_numero = int4 = Número
                 y15_compl = varchar(20) = Complemento
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_autoexec");
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
       $this->y15_codauto = ($this->y15_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y15_codauto"]:$this->y15_codauto);
       $this->y15_codigo = ($this->y15_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y15_codigo"]:$this->y15_codigo);
       $this->y15_codi = ($this->y15_codi == ""?@$GLOBALS["HTTP_POST_VARS"]["y15_codi"]:$this->y15_codi);
       $this->y15_numero = ($this->y15_numero == ""?@$GLOBALS["HTTP_POST_VARS"]["y15_numero"]:$this->y15_numero);
       $this->y15_compl = ($this->y15_compl == ""?@$GLOBALS["HTTP_POST_VARS"]["y15_compl"]:$this->y15_compl);
     }else{
       $this->y15_codauto = ($this->y15_codauto == ""?@$GLOBALS["HTTP_POST_VARS"]["y15_codauto"]:$this->y15_codauto);
     }
   }
   // funcao para inclusao
   public function incluir ($y15_codauto){
      $this->atualizacampos();
     if($this->y15_codigo == null ){
       $this->erro_sql = " Campo cód. Rua/Avenida nao Informado.";
       $this->erro_campo = "y15_codigo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y15_codi == null ){
       $this->erro_sql = " Campo Bairro nao Informado.";
       $this->erro_campo = "y15_codi";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if( false ){
       $this->erro_sql = " Campo Número nao Informado.";
       $this->erro_campo = "y15_numero";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->y15_codauto = $y15_codauto;
     if(($this->y15_codauto == null) || ($this->y15_codauto == "") ){
       $this->erro_sql = " Campo y15_codauto nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

				if($this->y15_numero == ''){
					$this->y15_numero = 'null';
				}
			$sql = "insert into fiscalizacao.fis_autoexec(
                                       y15_codauto
                                      ,y15_codigo
                                      ,y15_codi
                                      ,y15_numero
                                      ,y15_compl
                       )
                values (
                                $this->y15_codauto
                               ,$this->y15_codigo
                               ,$this->y15_codi
                               ,$this->y15_numero
                               ,'$this->y15_compl'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "local onde o auto de infração foi executado ($this->y15_codauto) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "local onde o auto de infração foi executado já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "local onde o auto de infração foi executado ($this->y15_codauto) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y15_codauto;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
   public function alterar ($y15_codauto=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_autoexec set ";
     $virgula = "";
     if(trim($this->y15_codauto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y15_codauto"])){
       $sql  .= $virgula." y15_codauto = $this->y15_codauto ";
       $virgula = ",";
       if(trim($this->y15_codauto) == null ){
         $this->erro_sql = " Campo Código do Auto de Infração nao Informado.";
         $this->erro_campo = "y15_codauto";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y15_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y15_codigo"])){
       $sql  .= $virgula." y15_codigo = $this->y15_codigo ";
       $virgula = ",";
       if(trim($this->y15_codigo) == null ){
         $this->erro_sql = " Campo cód. Rua/Avenida nao Informado.";
         $this->erro_campo = "y15_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y15_codi)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y15_codi"])){
       $sql  .= $virgula." y15_codi = $this->y15_codi ";
       $virgula = ",";
       if(trim($this->y15_codi) == null ){
         $this->erro_sql = " Campo Bairro nao Informado.";
         $this->erro_campo = "y15_codi";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y15_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y15_numero"])){

				if($this->y15_numero == ''){
					$sql  .= $virgula." y15_numero = null ";
				}else{
					$sql  .= $virgula." y15_numero = $this->y15_numero ";
				}

       $virgula = ",";
       if( false ){
         $this->erro_sql = " Campo Número nao Informado.";
         $this->erro_campo = "y15_numero";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y15_compl)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y15_compl"])){
       $sql  .= $virgula." y15_compl = '$this->y15_compl' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($y15_codauto!=null){
       $sql .= " y15_codauto = $this->y15_codauto";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "local onde o auto de infração foi executado nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y15_codauto;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "local onde o auto de infração foi executado nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y15_codauto;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y15_codauto;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($y15_codauto=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_autoexec
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y15_codauto != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y15_codauto = $y15_codauto ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "local onde o auto de infração foi executado nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y15_codauto;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "local onde o auto de infração foi executado nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y15_codauto;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y15_codauto;
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
        $this->erro_sql   = "Record Vazio na Tabela:autoexec";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $y15_codauto=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_autoexec ";
     $sql .= "      inner join bairro  on  bairro.j13_codi = fis_autoexec.y15_codi";
     $sql .= "      inner join ruas  on  ruas.j14_codigo = fis_autoexec.y15_codigo";
     $sql .= "      inner join fiscalizacao.fis_auto  on  fis_auto.y50_codauto = fis_autoexec.y15_codauto";
     $sql .= "      inner join db_config  on  db_config.codigo = fis_auto.y50_instit";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_auto.y50_setor";
     $sql .= "      left join fiscalizacao.fis_procfiscalauto  on  y111_auto = y50_codauto";
     $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = fis_auto.y50_codtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($y15_codauto!=null ){
         $sql2 .= " where fis_autoexec.y15_codauto = $y15_codauto ";
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
   public function sql_query_file ( $y15_codauto=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_autoexec ";
     $sql2 = "";
     if($dbwhere==""){
       if($y15_codauto!=null ){
         $sql2 .= " where fis_autoexec.y15_codauto = $y15_codauto ";
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
