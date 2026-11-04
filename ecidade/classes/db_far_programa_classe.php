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

class cl_far_programa
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
    public $fa12_i_codigo = 0; 
    public $fa12_c_descricao = null; 
    public $fa12_c_depadmin = null; 
    public $fa12_i_tipoacao = 0; 
    public $fa12_ativo = 'f'; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 fa12_i_codigo = int4 = Código 
                 fa12_c_descricao = char(40) = Descrição 
                 fa12_c_depadmin = char(2) = Dependência  Administrativa 
                 fa12_i_tipoacao = int4 = Tipo de Ação 
                 fa12_ativo = bool = Ativo 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("far_programa"); 
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
       $this->fa12_i_codigo = ($this->fa12_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa12_i_codigo"]:$this->fa12_i_codigo);
       $this->fa12_c_descricao = ($this->fa12_c_descricao == ""?@$GLOBALS["HTTP_POST_VARS"]["fa12_c_descricao"]:$this->fa12_c_descricao);
       $this->fa12_c_depadmin = ($this->fa12_c_depadmin == ""?@$GLOBALS["HTTP_POST_VARS"]["fa12_c_depadmin"]:$this->fa12_c_depadmin);
       $this->fa12_i_tipoacao = ($this->fa12_i_tipoacao == ""?@$GLOBALS["HTTP_POST_VARS"]["fa12_i_tipoacao"]:$this->fa12_i_tipoacao);
       $this->fa12_ativo = ($this->fa12_ativo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa12_ativo"]:$this->fa12_ativo);
     }else{
       $this->fa12_i_codigo = ($this->fa12_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["fa12_i_codigo"]:$this->fa12_i_codigo);
     }
   }

    public function incluir($fa12_i_codigo)
    {
      $this->atualizacampos();
     if($this->fa12_c_descricao == null ){ 
       $this->erro_sql = " Campo Descrição não informado.";
       $this->erro_campo = "fa12_c_descricao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa12_c_depadmin == null ){ 
       $this->erro_sql = " Campo Dependência  Administrativa não informado.";
       $this->erro_campo = "fa12_c_depadmin";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa12_i_tipoacao == null ){ 
       $this->erro_sql = " Campo Tipo de Ação não informado.";
       $this->erro_campo = "fa12_i_tipoacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->fa12_ativo == null ){ 
       $this->erro_sql = " Campo Ativo não informado.";
       $this->erro_campo = "fa12_ativo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($fa12_i_codigo == "" || $fa12_i_codigo == null ){
       $result = db_query("select nextval('far_programa_fa12_codigo_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: far_programa_fa12_codigo_seq do campo: fa12_i_codigo"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->fa12_i_codigo = pg_result($result,0,0); 
     }else{
       $result = db_query("select last_value from far_programa_fa12_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $fa12_i_codigo)){
         $this->erro_sql = " Campo fa12_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->fa12_i_codigo = $fa12_i_codigo; 
       }
     }
     if(($this->fa12_i_codigo == null) || ($this->fa12_i_codigo == "") ){ 
       $this->erro_sql = " Campo fa12_i_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into far_programa(
                                       fa12_i_codigo 
                                      ,fa12_c_descricao 
                                      ,fa12_c_depadmin 
                                      ,fa12_i_tipoacao 
                                      ,fa12_ativo 
                       )
                values (
                                $this->fa12_i_codigo 
                               ,'$this->fa12_c_descricao' 
                               ,'$this->fa12_c_depadmin' 
                               ,$this->fa12_i_tipoacao 
                               ,'$this->fa12_ativo' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "far_programa ($this->fa12_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "far_programa já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "far_programa ($this->fa12_i_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->fa12_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   } 

    public function alterar($fa12_i_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update far_programa set ";
     $virgula = "";
     if(trim($this->fa12_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa12_i_codigo"])){ 
       $sql  .= $virgula." fa12_i_codigo = $this->fa12_i_codigo ";
       $virgula = ",";
       if(trim($this->fa12_i_codigo) == null ){ 
         $this->erro_sql = " Campo Código não informado.";
         $this->erro_campo = "fa12_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa12_c_descricao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa12_c_descricao"])){ 
       $sql  .= $virgula." fa12_c_descricao = '$this->fa12_c_descricao' ";
       $virgula = ",";
       if(trim($this->fa12_c_descricao) == null ){ 
         $this->erro_sql = " Campo Descrição não informado.";
         $this->erro_campo = "fa12_c_descricao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa12_c_depadmin)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa12_c_depadmin"])){ 
       $sql  .= $virgula." fa12_c_depadmin = '$this->fa12_c_depadmin' ";
       $virgula = ",";
       if(trim($this->fa12_c_depadmin) == null ){ 
         $this->erro_sql = " Campo Dependência  Administrativa não informado.";
         $this->erro_campo = "fa12_c_depadmin";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa12_i_tipoacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa12_i_tipoacao"])){ 
       $sql  .= $virgula." fa12_i_tipoacao = $this->fa12_i_tipoacao ";
       $virgula = ",";
       if(trim($this->fa12_i_tipoacao) == null ){ 
         $this->erro_sql = " Campo Tipo de Ação não informado.";
         $this->erro_campo = "fa12_i_tipoacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->fa12_ativo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["fa12_ativo"])){ 
       $sql  .= $virgula." fa12_ativo = '$this->fa12_ativo' ";
       $virgula = ",";
       if(trim($this->fa12_ativo) == null ){ 
         $this->erro_sql = " Campo Ativo não informado.";
         $this->erro_campo = "fa12_ativo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($fa12_i_codigo!=null){
       $sql .= " fa12_i_codigo = $this->fa12_i_codigo";
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "far_programa não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->fa12_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "far_programa não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->fa12_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->fa12_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($fa12_i_codigo=null, $dbwhere = null)
    {
      
     $sql = " delete from far_programa
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($fa12_i_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " fa12_i_codigo = $fa12_i_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "far_programa não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$fa12_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "far_programa não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$fa12_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$fa12_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:far_programa";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($fa12_i_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from far_programa ";
     $sql .= "      inner join sau_tipoacaoprog  on  sau_tipoacaoprog.s148_i_codigo = far_programa.fa12_i_tipoacao";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($fa12_i_codigo)) {
         $sql2 .= " where far_programa.fa12_i_codigo = $fa12_i_codigo "; 
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

    public function sql_query_file($fa12_i_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from far_programa ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($fa12_i_codigo)){
         $sql2 .= " where far_programa.fa12_i_codigo = $fa12_i_codigo "; 
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
