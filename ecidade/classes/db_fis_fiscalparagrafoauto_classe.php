<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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
class cl_fis_fiscalparagrafoauto{
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
   public $pl10_codigo = null;
   public $pl10_auto = 0;
   public $pl10_paragrafo = 0;
   public $pl10_texto = '';
   public $pl10_usu = 0;

   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_paragrafo");
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

     // if($this->pl10_codigo == null){
     //   $this->erro_sql = " Campo Codigo nao Informado.";
     //   $this->erro_campo = "pl10_codigo";
     //   $this->erro_banco = "";
     //   $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }

     // if($this->pl10_auto == 0){
     //   $this->erro_sql = " Campo Auto nao Informado.";
     //   $this->erro_campo = "pl10_auto";
     //   $this->erro_banco = "";
     //   $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }

     // if($this->pl10_paragrafo == 0 ){
     //   $this->erro_sql = " Campo Paragrafo nao Informado.";
     //   $this->erro_campo = "pl10_paragrafo";
     //   $this->erro_banco = "";
     //   $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }

     // if($this->pl10_texto == '' ){
     //   $this->erro_sql = " Campo Texto nao Informado.";
     //   $this->erro_campo = "pl10_texto";
     //   $this->erro_banco = "";
     //   $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }

     // if($this->pl10_usu == 0 ){
     //   $this->erro_sql = " Usuário nao Informado.";
     //   $this->erro_campo = "pl10_usu";
     //   $this->erro_banco = "";
     //   $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }

     $sql = "insert into fiscalizacao.fis_paragrafoauto(
                                       pl10_auto
                                      ,pl10_paragrafo
                                      ,pl10_texto
                                      ,pl10_usu
                       )
                values (
                                $this->pl10_auto
                                ,$this->pl10_paragrafo
                                ,'$this->pl10_texto'
                                ,$this->pl10_usu

                      )";
     $result = db_query($sql);

     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = $sql." Paragrafo Auto ($this->pl10_codigo) nao Incluído. Inclusao Abortada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status     = "0";
       $this->numrows_incluir = 0;
       return false;
     }
     $this->erro_banco  = "";
     $this->erro_sql    = "Inclusao efetuada com Sucesso\\n";
     $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }

   // funcao para alteracao
   public function alterar () {
     // if($this->pl10_codigo == null){
     //   $this->erro_sql = " Campo Codigo nao Informado.";
     //   $this->erro_campo = "pl10_codigo";
     //   $this->erro_banco = "";
     //   $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }

     // if($this->pl10_auto == 0){
     //   $this->erro_sql = " Campo Auto nao Informado.";
     //   $this->erro_campo = "pl10_auto";
     //   $this->erro_banco = "";
     //   $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }

     // if($this->pl10_paragrafo == 0 ){
     //   $this->erro_sql = " Campo Paragrafo nao Informado.";
     //   $this->erro_campo = "pl10_paragrafo";
     //   $this->erro_banco = "";
     //   $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }

     // if($this->pl10_texto == '' ){
     //   $this->erro_sql = " Campo Texto nao Informado.";
     //   $this->erro_campo = "pl10_texto";
     //   $this->erro_banco = "";
     //   $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }

     // if($this->pl10_usu == 0 ){
     //   $this->erro_sql = " Usuário nao Informado.";
     //   $this->erro_campo = "pl10_usu";
     //   $this->erro_banco = "";
     //   $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     //   $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     //   $this->erro_status = "0";
     //   return false;
     // }
     $sql = " update fiscalizacao.fis_paragrafoauto set

                 pl10_auto      = '$this->pl10_auto'
                ,pl10_paragrafo =  $this->pl10_paragrafo
                ,pl10_texto     = '$this->pl10_texto'
                ,pl10_usu       =  $this->pl10_usu

              where pl10_codigo = $this->pl10_codigo ";

     $result = db_query($sql);

     if($result==false ){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Paragrafo nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->pl10_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Paragrafo nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->pl10_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->pl10_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir () {
      if($this->pl10_codigo != 0){
       $sql = " delete from fiscalizacao.fis_paragrafoauto
                        where pl10_codigo = $this->pl10_codigo";

      }
      $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Paragrafo nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->pl10_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Paragrafo nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$this->pl10_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->pl10_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao do recordset
   // public function sql_record($sql) {
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
   //   $sql .= " from divermatr ";
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
