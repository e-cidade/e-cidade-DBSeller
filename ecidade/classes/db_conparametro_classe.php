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

//MODULO: contabilidade
//CLASSE DA ENTIDADE conparametro
class cl_conparametro { 
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
    public $c90_estrutsistema = null; 
    public $c90_estrutcontabil = null; 
    public $c90_codestrut = 0; 
    public $c90_utilcontabancaria = 'f'; 
    public $c90_usapcasp = 'f'; 
    public $c90_confirmadata = 'f'; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 c90_estrutsistema = varchar(40) = Estrutural Sistema 
                 c90_estrutcontabil = varchar(50) = Estrutural Contabilidade 
                 c90_codestrut = int4 = Código 
                 c90_utilcontabancaria = bool = Utiliza Conta Bancária 
                 c90_usapcasp = bool = Usa PCASP 
                 c90_confirmadata = bool = Confirma data antes da escrituração 
                 ";

    public function cl_conparametro()
    {
        $this->rotulo = new rotulo("conparametro"); 
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
       $this->c90_estrutsistema = ($this->c90_estrutsistema == ""?@$GLOBALS["HTTP_POST_VARS"]["c90_estrutsistema"]:$this->c90_estrutsistema);
       $this->c90_estrutcontabil = ($this->c90_estrutcontabil == ""?@$GLOBALS["HTTP_POST_VARS"]["c90_estrutcontabil"]:$this->c90_estrutcontabil);
       $this->c90_codestrut = ($this->c90_codestrut == ""?@$GLOBALS["HTTP_POST_VARS"]["c90_codestrut"]:$this->c90_codestrut);
       $this->c90_utilcontabancaria = ($this->c90_utilcontabancaria == "f"?@$GLOBALS["HTTP_POST_VARS"]["c90_utilcontabancaria"]:$this->c90_utilcontabancaria);
       $this->c90_usapcasp = ($this->c90_usapcasp == "f"?@$GLOBALS["HTTP_POST_VARS"]["c90_usapcasp"]:$this->c90_usapcasp);
       $this->c90_confirmadata = ($this->c90_confirmadata == "f"?@$GLOBALS["HTTP_POST_VARS"]["c90_confirmadata"]:$this->c90_confirmadata);
     }else{
     }
   }

    public function incluir()
    {
      $this->atualizacampos();
     if($this->c90_estrutsistema == null ){ 
       $this->erro_sql = " Campo Estrutural Sistema não informado.";
       $this->erro_campo = "c90_estrutsistema";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c90_estrutcontabil == null ){ 
       $this->erro_sql = " Campo Estrutural Contabilidade não informado.";
       $this->erro_campo = "c90_estrutcontabil";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c90_codestrut == null ){ 
       $this->erro_sql = " Campo Código não informado.";
       $this->erro_campo = "c90_codestrut";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c90_utilcontabancaria == null ){ 
       $this->erro_sql = " Campo Utiliza Conta Bancária não informado.";
       $this->erro_campo = "c90_utilcontabancaria";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c90_usapcasp == null ){ 
       $this->erro_sql = " Campo Usa PCASP não informado.";
       $this->erro_campo = "c90_usapcasp";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c90_confirmadata == null ){ 
       $this->erro_sql = " Campo Confirma data antes da escrituração não informado.";
       $this->erro_campo = "c90_confirmadata";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into conparametro(
                                       c90_estrutsistema 
                                      ,c90_estrutcontabil 
                                      ,c90_codestrut 
                                      ,c90_utilcontabancaria 
                                      ,c90_usapcasp 
                                      ,c90_confirmadata 
                       )
                values (
                                '$this->c90_estrutsistema' 
                               ,'$this->c90_estrutcontabil' 
                               ,$this->c90_codestrut 
                               ,'$this->c90_utilcontabancaria' 
                               ,'$this->c90_usapcasp' 
                               ,'$this->c90_confirmadata' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Parametro Contabilidade () não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Parametro Contabilidade já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Parametro Contabilidade () não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

     }
     return true;
   } 

    public function alterar( $oid=null )
    {
      $this->atualizacampos();
     $sql = " update conparametro set ";
     $virgula = "";
     if(trim($this->c90_estrutsistema)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c90_estrutsistema"])){ 
       $sql  .= $virgula." c90_estrutsistema = '$this->c90_estrutsistema' ";
       $virgula = ",";
       if(trim($this->c90_estrutsistema) == null ){ 
         $this->erro_sql = " Campo Estrutural Sistema não informado.";
         $this->erro_campo = "c90_estrutsistema";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c90_estrutcontabil)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c90_estrutcontabil"])){ 
       $sql  .= $virgula." c90_estrutcontabil = '$this->c90_estrutcontabil' ";
       $virgula = ",";
       if(trim($this->c90_estrutcontabil) == null ){ 
         $this->erro_sql = " Campo Estrutural Contabilidade não informado.";
         $this->erro_campo = "c90_estrutcontabil";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c90_codestrut)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c90_codestrut"])){ 
       $sql  .= $virgula." c90_codestrut = $this->c90_codestrut ";
       $virgula = ",";
       if(trim($this->c90_codestrut) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "c90_codestrut";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c90_utilcontabancaria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c90_utilcontabancaria"])){ 
       $sql  .= $virgula." c90_utilcontabancaria = '$this->c90_utilcontabancaria' ";
       $virgula = ",";
       if(trim($this->c90_utilcontabancaria) == null ){ 
         $this->erro_sql = " Campo Utiliza Conta Bancária não informado.";
         $this->erro_campo = "c90_utilcontabancaria";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c90_usapcasp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c90_usapcasp"])){ 
       $sql  .= $virgula." c90_usapcasp = '$this->c90_usapcasp' ";
       $virgula = ",";
       if(trim($this->c90_usapcasp) == null ){ 
         $this->erro_sql = " Campo Usa PCASP não informado.";
         $this->erro_campo = "c90_usapcasp";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c90_confirmadata)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c90_confirmadata"])){ 
       $sql  .= $virgula." c90_confirmadata = '$this->c90_confirmadata' ";
       $virgula = ",";
       if(trim($this->c90_confirmadata) == null ){ 
         $this->erro_sql = " Campo Confirma data antes da escrituração não informado.";
         $this->erro_campo = "c90_confirmadata";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     //$sql .= " where ";
     //$sql .= "oid = '$oid'";     
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Parametro Contabilidade não Alterado. Alteração Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Parametro Contabilidade não foi Alterado. Alteração Executada.\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir( $oid=null , $dbwhere = null)
    {
     $sql = " delete from conparametro
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
       $sql2 = "oid = '$oid'";
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Parametro Contabilidade não Excluído. Exclusão Abortada.\\n";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Parametro Contabilidade não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
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
        $this->erro_sql   = "Record Vazio na Tabela:conparametro";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($oid = null, $campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from conparametro ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($oid)) {
          $sql2 = " where conparametro.oid = '$oid'";
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

    public function sql_query_file($oid = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from conparametro ";
     $sql2 = "";
     if (empty($dbwhere)) {
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
?>
