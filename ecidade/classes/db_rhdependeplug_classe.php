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

//MODULO: pessoal
//CLASSE DA ENTIDADE rhpescargo
class cl_rhdependeplug { 
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
   // var $pl05_formula   = 0; 
   var $dp01_rhdepend       = 0;
   var $dp01_regist         = 0;   
   var $dp01_instit         = 0;
   var $dp01_processo       = 0;
   var $dp01_cpf            = 0;
   var $dp01_sexo           = 0;
  



   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("rhdependeplug"); 
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

   // funcao para inclusao
   public function incluir (){
     if($this->dp01_rhdepend == null ){
       $this->erro_sql = " Campo Formula nao Informado.";
       $this->erro_campo = "dp01_rhdepend";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
    
     $sql = "
              insert into rhdependeplug(
                                       dp01_rhdepend 
                                      ,dp01_regist 
                                      ,dp01_instit
                                      ,dp01_processo                                   
                                      ,dp01_cpf                                   
                                      ,dp01_sexo                                   
                                     
                       )
                values (
                                $this->dp01_rhdepend 
                               ,$this->dp01_regist  
                               ,$this->dp01_instit                                 
                               ,$this->dp01_processo                                 
                               ,'$this->dp01_cpf'                                 
                               ,'$this->dp01_sexo'                                 
                      )";
// die($sql);
     $result = db_query($sql);
                                     
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Dependente ($this->dp01_rhdepend) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Dependente ($this->dp01_rhdepend) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
     $this->erro_sql .= "Valores : ".$this->dp01_rhdepend;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar () {
      //$this->atualizacampos();
     //   if(trim($this->dp01_processo) == null || trim($this->dp01_processo) == null ){ 
     //     $this->erro_sql = " Campo Processo Não Informado";
     //     $this->erro_campo = "dp01_processo";
     //     $this->erro_banco = "";
     //     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //     $this->erro_status = "0";
     //     return false;
     // }
     $sql = " update rhdependeplug set 
                                           dp01_processo = $this->dp01_processo 
                                          ,dp01_cpf = '$this->dp01_cpf'
                                          ,dp01_sexo = '$this->dp01_sexo' 
                  where 
                                          dp01_regist   = $this->dp01_regist
                                    and   dp01_rhdepend = $this->dp01_rhdepend ";   
     $result = db_query($sql);
 
     if($result==false ){ 
       $this->erro_banco  = str_replace("\n","",@pg_last_error());
       $this->erro_sql    = "Processo Não Alterado. Alteração Abortada.\\n";
       $this->erro_sql   .= "Valores : ".$this->dp01_processo;
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Processo Não Alterado.\\n";
         $this->erro_sql .= "Valores : ".$this->dp01_processo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->dp01_rhdepend;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir($dp01_codigo = null, $where = null) {

       $filtros = array();
       if  (!empty($dp01_codigo)) {
        $filtros[] = "dp01_codigo = {$dp01_codigo}";
       }

       if (!empty($where)) {
            $filtros[] = $where;
       }

       if (empty($filtros)) {
           $this->erro_sql = "Nenhum filtro para a exclusão informado.";
           $this->erro_msg = "Nenhum filtro para a exclusão informado.";
           $this->erro_status = "0";
           return false;
       }

       $sql = " delete from rhdependeplug 
                    where " . implode(" AND ", $filtros);

       $result = db_query($sql);  

      if(!$result){ 
        $this->erro_banco  = str_replace("\n","",@pg_last_error());
        $this->erro_sql    = "Processo Não Excluido. Exclusão Abortada.\\n";
        $this->erro_sql   .= "Valores : ".$this->dp01_processo;
        $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        $this->numrows_excluir = 0;
        return false;
      }else{
        if(pg_affected_rows($result)==0){
          $this->erro_banco  = "";
          $this->erro_sql    = "Processo Não Excluido. Exclusão Abortada.\\n";
          $this->erro_sql   .= "Valores : ".$this->dp01_processo;
          $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
          $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
          $this->erro_status = "1";
          $this->numrows_excluir = 0;
          return true;
        }else{
          $this->erro_banco = "";
          $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
          $this->erro_sql .= "Valores : ".$this->dp01_rhdepend;
          $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
          $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
          $this->erro_status = "1";
          $this->numrows_excluir = pg_affected_rows($result);
          return true;
        } 
      } 
  }
   // funcao do recordset 
   // function sql_record($sql) { 
   //   $result = db_query($sql);
   //   if($result==false){
   //     $this->numrows    = 0;
   //     $this->erro_banco = str_replace("\n","",@pg_last_error());
   //     $this->erro_sql   = "Erro ao selecionar os registros.";
   //     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
   //     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
   //     $this->erro_status = "0";
   //     return false;
   //   }
   //   $this->numrows = pg_numrows($result);
   //    if($this->numrows==0){
   //      $this->erro_banco = "";
   //      $this->erro_sql   = "Record Vazio na Tabela:divermatr";
   //      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
   //      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
   //      $this->erro_status = "0";
   //      return false;
   //    }
   //   return $result;
   // }
   // public function sql_query ( $pl03_matr=null,$campos="*",$ordem=null,$dbwhere=""){
   //   $sql = "select ";
   //   if($campos != "*" ){
   //     $campos_sql = explode("#",$campos);
   //     $virgula = "";
   //     for($i=0;$i<sizeof($campos_sql);$i++){
   //       $sql .= $virgula.$campos_sql[$i];
   //       $virgula = ",";
   //     }
   //   }else{
   //     $sql .= $campos;
   //   }
   //   $sql .= " from plugins.divermatr ";
   //   $sql2 = "";
   //   if($dbwhere==""){
   //     if($pl03_matr!=null ){
   //       $sql2 .= " where divermatr.pl03_matr = $pl03_matr "; 
   //     } 
   //   }else if($dbwhere != ""){
   //     $sql2 = " where $dbwhere";
   //   }
   //   $sql .= $sql2;
   //   if($ordem != null ){
   //     $sql .= " order by ";
   //     $campos_sql = explode("#",$ordem);
   //     $virgula = "";
   //     for($i=0;$i<sizeof($campos_sql);$i++){
   //       $sql .= $virgula.$campos_sql[$i];
   //       $virgula = ",";
   //     }
   //   }
   // }  
   //   return $sql;
}  

   
   


?>
