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

//MODULO: fiscal
//CLASSE DA ENTIDADE sanibaixa
class cl_fis_sanibaixa {
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
   public $y81_codsani = 0;
   public $y81_seq = 0;
   public $y81_data_dia = null;
   public $y81_data_mes = null;
   public $y81_data_ano = null;
   public $y81_data = null;
   public $y81_obs = null;
   public $y81_oficio = 'f';
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y81_codsani = int4 = Código do Alvará sanitário
                 y81_seq = int4 = Sequência
                 y81_data = date = Data da Baixa
                 y81_obs = text = Observação da Baixa
                 y81_oficio = bool = Tipo de baixa
                 ";
   //funcao construtor da classe
public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_sanibaixa");
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
       $this->y81_codsani = ($this->y81_codsani == ""?@$GLOBALS["HTTP_POST_VARS"]["y81_codsani"]:$this->y81_codsani);
       $this->y81_seq = ($this->y81_seq == ""?@$GLOBALS["HTTP_POST_VARS"]["y81_seq"]:$this->y81_seq);
       if($this->y81_data == ""){
         $this->y81_data_dia = ($this->y81_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y81_data_dia"]:$this->y81_data_dia);
         $this->y81_data_mes = ($this->y81_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y81_data_mes"]:$this->y81_data_mes);
         $this->y81_data_ano = ($this->y81_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y81_data_ano"]:$this->y81_data_ano);
         if($this->y81_data_dia != ""){
            $this->y81_data = $this->y81_data_ano."-".$this->y81_data_mes."-".$this->y81_data_dia;
         }
       }
       $this->y81_obs = ($this->y81_obs == ""?@$GLOBALS["HTTP_POST_VARS"]["y81_obs"]:$this->y81_obs);
       $this->y81_oficio = ($this->y81_oficio == "f"?@$GLOBALS["HTTP_POST_VARS"]["y81_oficio"]:$this->y81_oficio);
     }else{
       $this->y81_codsani = ($this->y81_codsani == ""?@$GLOBALS["HTTP_POST_VARS"]["y81_codsani"]:$this->y81_codsani);
       $this->y81_seq = ($this->y81_seq == ""?@$GLOBALS["HTTP_POST_VARS"]["y81_seq"]:$this->y81_seq);
     }
   }
   // funcao para inclusao
public function incluir ($y81_codsani,$y81_seq){
      $this->atualizacampos();
     if($this->y81_data == null ){
       $this->erro_sql = " Campo Data da Baixa nao Informado.";
       $this->erro_campo = "y81_data_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y81_obs == null ){
       $this->erro_sql = " Campo Observação da Baixa nao Informado.";
       $this->erro_campo = "y81_obs";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y81_oficio == null ){
       $this->erro_sql = " Campo Tipo de baixa nao Informado.";
       $this->erro_campo = "y81_oficio";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->y81_codsani = $y81_codsani;
       $this->y81_seq = $y81_seq;
     if(($this->y81_codsani == null) || ($this->y81_codsani == "") ){
       $this->erro_sql = " Campo y81_codsani nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->y81_seq == null) || ($this->y81_seq == "") ){
       $this->erro_sql = " Campo y81_seq nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_sanibaixa(
                                       y81_codsani
                                      ,y81_seq
                                      ,y81_data
                                      ,y81_obs
                                      ,y81_oficio
                       )
                values (
                                $this->y81_codsani
                               ,$this->y81_seq
                               ,".($this->y81_data == "null" || $this->y81_data == ""?"null":"'".$this->y81_data."'")."
                               ,'$this->y81_obs'
                               ,'$this->y81_oficio'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "sanibaixa ($this->y81_codsani."-".$this->y81_seq) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "sanibaixa já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "sanibaixa ($this->y81_codsani."-".$this->y81_seq) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y81_codsani."-".$this->y81_seq;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);


     return true;
   }
   // funcao para alteracao
public function alterar ($y81_codsani=null,$y81_seq=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_sanibaixa set ";
     $virgula = "";
     if(trim($this->y81_codsani)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y81_codsani"])){
       $sql  .= $virgula." y81_codsani = $this->y81_codsani ";
       $virgula = ",";
       if(trim($this->y81_codsani) == null ){
         $this->erro_sql = " Campo Código do Alvará sanitário nao Informado.";
         $this->erro_campo = "y81_codsani";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y81_seq)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y81_seq"])){
       $sql  .= $virgula." y81_seq = $this->y81_seq ";
       $virgula = ",";
       if(trim($this->y81_seq) == null ){
         $this->erro_sql = " Campo Sequência nao Informado.";
         $this->erro_campo = "y81_seq";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y81_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y81_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["y81_data_dia"] !="") ){
       $sql  .= $virgula." y81_data = '$this->y81_data' ";
       $virgula = ",";
       if(trim($this->y81_data) == null ){
         $this->erro_sql = " Campo Data da Baixa nao Informado.";
         $this->erro_campo = "y81_data_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["y81_data_dia"])){
         $sql  .= $virgula." y81_data = null ";
         $virgula = ",";
         if(trim($this->y81_data) == null ){
           $this->erro_sql = " Campo Data da Baixa nao Informado.";
           $this->erro_campo = "y81_data_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->y81_obs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y81_obs"])){
       $sql  .= $virgula." y81_obs = '$this->y81_obs' ";
       $virgula = ",";
       if(trim($this->y81_obs) == null ){
         $this->erro_sql = " Campo Observação da Baixa nao Informado.";
         $this->erro_campo = "y81_obs";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->y81_oficio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["y81_oficio"])){
       $sql  .= $virgula." y81_oficio = '$this->y81_oficio' ";
       $virgula = ",";
       if(trim($this->y81_oficio) == null ){
         $this->erro_sql = " Campo Tipo de baixa nao Informado.";
         $this->erro_campo = "y81_oficio";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($y81_codsani!=null){
       $sql .= " y81_codsani = $this->y81_codsani";
     }
     if($y81_seq!=null){
       $sql .= " and  y81_seq = $this->y81_seq";
     }


     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "sanibaixa nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->y81_codsani."-".$this->y81_seq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "sanibaixa nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->y81_codsani."-".$this->y81_seq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->y81_codsani."-".$this->y81_seq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
public function excluir ($y81_codsani=null,$y81_seq=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_sanibaixa
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($y81_codsani != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y81_codsani = $y81_codsani ";
        }
        if($y81_seq != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " y81_seq = $y81_seq ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "sanibaixa nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$y81_codsani."-".$y81_seq;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "sanibaixa nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$y81_codsani."-".$y81_seq;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$y81_codsani."-".$y81_seq;
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
        $this->erro_sql   = "Record Vazio na Tabela:sanibaixa";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
public function sql_query ( $y81_codsani=null,$y81_seq=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_sanibaixa ";
     $sql .= "      inner join fiscalizacao.fis_saniatividade  on  fis_saniatividade.y83_codsani = fis_sanibaixa.y81_codsani and  fis_saniatividade.y83_seq = fis_sanibaixa.y81_seq";
     $sql .= "      inner join ativid  on  ativid.q03_ativ = fis_saniatividade.y83_ativ";
     $sql .= "      inner join fiscalizacao.fis_sanitario  on  fis_sanitario.y80_codsani = fis_saniatividade.y83_codsani";
     $sql .= "      inner join ativid  as a on   a.q03_ativ = fis_saniatividade.y83_ativ";
     $sql .= "      inner join fiscalizacao.fis_sanitario  as b on   b.y80_codsani = fis_saniatividade.y83_codsani";
     $sql2 = "";
     if($dbwhere==""){
       if($y81_codsani!=null ){
         $sql2 .= " where fis_sanibaixa.y81_codsani = $y81_codsani ";
       }
       if($y81_seq!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_sanibaixa.y81_seq = $y81_seq ";
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
public function sql_query_file ( $y81_codsani=null,$y81_seq=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_sanibaixa ";
     $sql2 = "";
     if($dbwhere==""){
       if($y81_codsani!=null ){
         $sql2 .= " where fis_sanibaixa.y81_codsani = $y81_codsani ";
       }
       if($y81_seq!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " fis_sanibaixa.y81_seq = $y81_seq ";
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
