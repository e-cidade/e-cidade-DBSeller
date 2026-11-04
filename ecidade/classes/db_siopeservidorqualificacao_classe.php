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

class cl_siopeservidorqualificacao
{
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
    public $si08_servidor = 0; 
    public $si08_qualificacao = 0; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 si08_servidor = int4 = Matricula do servidor 
                 si08_qualificacao = int4 = Qualificação do Servidor 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("siopeservidorqualificacao"); 
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
       $this->si08_servidor = ($this->si08_servidor == ""?@$GLOBALS["HTTP_POST_VARS"]["si08_servidor"]:$this->si08_servidor);
       $this->si08_qualificacao = ($this->si08_qualificacao == ""?@$GLOBALS["HTTP_POST_VARS"]["si08_qualificacao"]:$this->si08_qualificacao);
     }else{
       $this->si08_servidor = ($this->si08_servidor == ""?@$GLOBALS["HTTP_POST_VARS"]["si08_servidor"]:$this->si08_servidor);
       $this->si08_qualificacao = ($this->si08_qualificacao == ""?@$GLOBALS["HTTP_POST_VARS"]["si08_qualificacao"]:$this->si08_qualificacao);
     }
   }

    public function incluir($si08_servidor,$si08_qualificacao)
    {
      $this->atualizacampos();
       $this->si08_servidor = $si08_servidor; 
       $this->si08_qualificacao = $si08_qualificacao; 
     if(($this->si08_servidor == null) || ($this->si08_servidor == "") ){ 
       $this->erro_sql = " Campo si08_servidor não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->si08_qualificacao == null) || ($this->si08_qualificacao == "") ){ 
       $this->erro_sql = " Campo si08_qualificacao não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into siopeservidorqualificacao(
                                       si08_servidor 
                                      ,si08_qualificacao 
                       )
                values (
                                $this->si08_servidor 
                               ,$this->si08_qualificacao 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "siopeservidorqualificacao ($this->si08_servidor."-".$this->si08_qualificacao) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "siopeservidorqualificacao já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "siopeservidorqualificacao ($this->si08_servidor."-".$this->si08_qualificacao) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->si08_servidor."-".$this->si08_qualificacao;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->si08_servidor,$this->si08_qualificacao  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,305009439,'$this->si08_servidor','I')");
         $resac = db_query("insert into db_acountkey values($acount,184651497,'$this->si08_qualificacao','I')");
         $resac = db_query("insert into db_acount values($acount,90070184,305009439,'','".AddSlashes(pg_result($resaco,0,'si08_servidor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,90070184,184651497,'','".AddSlashes(pg_result($resaco,0,'si08_qualificacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($si08_servidor=null,$si08_qualificacao=null)
    {
      $this->atualizacampos();
     $sql = " update siopeservidorqualificacao set ";
     $virgula = "";
     if(trim($this->si08_servidor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["si08_servidor"])){ 
       $sql  .= $virgula." si08_servidor = $this->si08_servidor ";
       $virgula = ",";
       if(trim($this->si08_servidor) == null ){ 
         $this->erro_sql = " Campo Matricula do servidor não informado.";
         $this->erro_campo = "si08_servidor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->si08_qualificacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["si08_qualificacao"])){ 
       $sql  .= $virgula." si08_qualificacao = $this->si08_qualificacao ";
       $virgula = ",";
       if(trim($this->si08_qualificacao) == null ){ 
         $this->erro_sql = " Campo Qualificação do Servidor não informado.";
         $this->erro_campo = "si08_qualificacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($si08_servidor!=null){
       $sql .= " si08_servidor = $this->si08_servidor";
     }
     if($si08_qualificacao!=null){
       $sql .= " and  si08_qualificacao = $this->si08_qualificacao";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->si08_servidor,$this->si08_qualificacao));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,305009439,'$this->si08_servidor','A')");
           $resac = db_query("insert into db_acountkey values($acount,184651497,'$this->si08_qualificacao','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["si08_servidor"]) || $this->si08_servidor != "")
             $resac = db_query("insert into db_acount values($acount,90070184,305009439,'".AddSlashes(pg_result($resaco,$conresaco,'si08_servidor'))."','$this->si08_servidor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["si08_qualificacao"]) || $this->si08_qualificacao != "")
             $resac = db_query("insert into db_acount values($acount,90070184,184651497,'".AddSlashes(pg_result($resaco,$conresaco,'si08_qualificacao'))."','$this->si08_qualificacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "siopeservidorqualificacao não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->si08_servidor."-".$this->si08_qualificacao;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "siopeservidorqualificacao não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->si08_servidor."-".$this->si08_qualificacao;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->si08_servidor."-".$this->si08_qualificacao;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($si08_servidor=null,$si08_qualificacao=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($si08_servidor,$si08_qualificacao));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,305009439,'$si08_servidor','E')");
           $resac  = db_query("insert into db_acountkey values($acount,184651497,'$si08_qualificacao','E')");
           $resac  = db_query("insert into db_acount values($acount,90070184,305009439,'','".AddSlashes(pg_result($resaco,$iresaco,'si08_servidor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,90070184,184651497,'','".AddSlashes(pg_result($resaco,$iresaco,'si08_qualificacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from siopeservidorqualificacao
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($si08_servidor)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " si08_servidor = $si08_servidor ";
        }
        if (!empty($si08_qualificacao)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " si08_qualificacao = $si08_qualificacao ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "siopeservidorqualificacao não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$si08_servidor."-".$si08_qualificacao;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "siopeservidorqualificacao não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$si08_servidor."-".$si08_qualificacao;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$si08_servidor."-".$si08_qualificacao;
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
        $this->erro_sql   = "Record Vazio na Tabela:siopeservidorqualificacao";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($si08_servidor = null,$si08_qualificacao = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from siopeservidorqualificacao ";
     $sql .= "      inner join rhpessoal  on  rhpessoal.rh01_regist = siopeservidorqualificacao.si08_servidor";
     $sql .= "      inner join siopequalificacao  on  siopequalificacao.si04_id = siopeservidorqualificacao.si08_qualificacao";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = rhpessoal.rh01_numcgm";
     $sql .= "      inner join rhestcivil  on  rhestcivil.rh08_estciv = rhpessoal.rh01_estciv";
     $sql .= "      inner join rhraca  on  rhraca.rh18_raca = rhpessoal.rh01_raca";
     $sql .= "      left  join rhfuncao  on  rhfuncao.rh37_funcao = rhpessoal.rh01_funcao and  rhfuncao.rh37_instit = rhpessoal.rh01_instit";
     $sql .= "      inner join rhinstrucao  on  rhinstrucao.rh21_instru = rhpessoal.rh01_instru";
     $sql .= "      inner join rhnacionalidade  on  rhnacionalidade.rh06_nacionalidade = rhpessoal.rh01_nacion";
     $sql .= "      left  join rhsindicato  on  rhsindicato.rh116_sequencial = rhpessoal.rh01_rhsindicato";
     $sql .= "      inner join rhreajusteparidade  on  rhreajusteparidade.rh148_sequencial = rhpessoal.rh01_reajusteparidade";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($si08_servidor)) {
         $sql2 .= " where siopeservidorqualificacao.si08_servidor = $si08_servidor "; 
       } 
       if (!empty($si08_qualificacao)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " siopeservidorqualificacao.si08_qualificacao = $si08_qualificacao "; 
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

    public function sql_query_file($si08_servidor = null,$si08_qualificacao = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from siopeservidorqualificacao ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($si08_servidor)){
         $sql2 .= " where siopeservidorqualificacao.si08_servidor = $si08_servidor "; 
       } 
       if (!empty($si08_qualificacao)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " siopeservidorqualificacao.si08_qualificacao = $si08_qualificacao "; 
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
