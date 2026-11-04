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
//CLASSE DA ENTIDADE conplanoorcamentogrupo
class cl_conplanoorcamentogrupo
{
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
    public $c21_sequencial = 0;
    public $c21_anousu = 0;
    public $c21_codcon = 0;
    public $c21_congrupo = 0;
    public $c21_instit = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 c21_sequencial = int4 = Sequencial
                 c21_anousu = int4 = Exercício
                 c21_codcon = int4 = Código
                 c21_congrupo = int4 = Grupo
                 c21_instit = int4 = Instituição
                 ";
   //funcao construtor da classe
    public function __construct()
    {
      //classes dos rotulos dos campos
        $this->rotulo = new rotulo("conplanoorcamentogrupo");
        $this->pagina_retorno =  basename($_SERVER["PHP_SELF"]);
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
       $this->c21_sequencial = ($this->c21_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c21_sequencial"]:$this->c21_sequencial);
       $this->c21_anousu = ($this->c21_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["c21_anousu"]:$this->c21_anousu);
       $this->c21_codcon = ($this->c21_codcon == ""?@$GLOBALS["HTTP_POST_VARS"]["c21_codcon"]:$this->c21_codcon);
       $this->c21_congrupo = ($this->c21_congrupo == ""?@$GLOBALS["HTTP_POST_VARS"]["c21_congrupo"]:$this->c21_congrupo);
       $this->c21_instit = ($this->c21_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["c21_instit"]:$this->c21_instit);
     }else{
       $this->c21_sequencial = ($this->c21_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["c21_sequencial"]:$this->c21_sequencial);
     }
   }
   // funcao para inclusao
    public function incluir ($c21_sequencial){
      $this->atualizacampos();
     if($this->c21_anousu == null ){
       $this->erro_sql = " Campo Exercício nao Informado.";
       $this->erro_campo = "c21_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c21_codcon == null ){
       $this->erro_sql = " Campo Código nao Informado.";
       $this->erro_campo = "c21_codcon";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c21_congrupo == null ){
       $this->erro_sql = " Campo Grupo nao Informado.";
       $this->erro_campo = "c21_congrupo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->c21_instit == null ){
       $this->erro_sql = " Campo Instituição nao Informado.";
       $this->erro_campo = "c21_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($c21_sequencial == "" || $c21_sequencial == null ){
       $result = db_query("select nextval('conplanoorcamentogrupo_c21_sequencial_seq')");
       if(!$result) {
                $this->erro_banco = str_replace("\n", "", @pg_last_error());
                $this->erro_sql   = "Verifique o cadastro da sequencia: conplanoorcamentogrupo_c21_sequencial_seq do campo: c21_sequencial";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            }
            $this->c21_sequencial = pg_fetch_result($result, 0, 0);
        } else {
            $result = db_query("select last_value from conplanoorcamentogrupo_c21_sequencial_seq");
            if ($result && (pg_fetch_result($result, 0, 0) < $c21_sequencial)) {
                $this->erro_sql = " Campo c21_sequencial maior que último número da sequencia.";
                $this->erro_banco = "Sequencia menor que este número.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "0";
                return false;
            } else {
                $this->c21_sequencial = $c21_sequencial;
            }
        }
        if (($this->c21_sequencial == null) || ($this->c21_sequencial == "")) {
            $this->erro_sql = " Campo c21_sequencial nao declarado.";
            $this->erro_banco = "Chave Primaria zerada.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $sql = "insert into conplanoorcamentogrupo(
                                       c21_sequencial
                                      ,c21_anousu
                                      ,c21_codcon
                                      ,c21_congrupo
                                      ,c21_instit
                       )
                values (
                                $this->c21_sequencial
                               ,$this->c21_anousu
                               ,$this->c21_codcon
                               ,$this->c21_congrupo
                               ,$this->c21_instit
                      )";
        $result = db_query($sql);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            if (strpos(strtolower($this->erro_banco), "duplicate key") != 0) {
                $this->erro_sql   = "conplanoorcamentogrupo ($this->c21_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_banco = "conplanoorcamentogrupo já Cadastrado";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            } else {
                $this->erro_sql   = "conplanoorcamentogrupo ($this->c21_sequencial) nao Incluído. Inclusao Abortada.";
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            }
            $this->erro_status = "0";
            $this->numrows_incluir= 0;
            return false;
        }
        $this->erro_banco = "";
        $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->c21_sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_incluir= pg_affected_rows($result);
        $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
        return true;
    }
   // funcao para alteracao
    public function alterar ($c21_sequencial=null) {
      $this->atualizacampos();
     $sql = " update conplanoorcamentogrupo set ";
     $virgula = "";
     if(trim($this->c21_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c21_sequencial"])){
       $sql  .= $virgula." c21_sequencial = $this->c21_sequencial ";
       $virgula = ",";
       if(trim($this->c21_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial nao Informado.";
         $this->erro_campo = "c21_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c21_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c21_anousu"])){
       $sql  .= $virgula." c21_anousu = $this->c21_anousu ";
       $virgula = ",";
       if(trim($this->c21_anousu) == null ){
         $this->erro_sql = " Campo Exercício nao Informado.";
         $this->erro_campo = "c21_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c21_codcon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c21_codcon"])){
       $sql  .= $virgula." c21_codcon = $this->c21_codcon ";
       $virgula = ",";
       if(trim($this->c21_codcon) == null ){
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "c21_codcon";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c21_congrupo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c21_congrupo"])){
       $sql  .= $virgula." c21_congrupo = $this->c21_congrupo ";
       $virgula = ",";
       if(trim($this->c21_congrupo) == null ){
         $this->erro_sql = " Campo Grupo nao Informado.";
         $this->erro_campo = "c21_congrupo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->c21_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["c21_instit"])){
       $sql  .= $virgula." c21_instit = $this->c21_instit ";
       $virgula = ",";
       if(trim($this->c21_instit) == null ){
         $this->erro_sql = " Campo Instituição nao Informado.";
         $this->erro_campo = "c21_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($c21_sequencial!=null){
       $sql .= " c21_sequencial = $this->c21_sequencial";
     }
     $result = db_query($sql);
     if(!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "conplanoorcamentogrupo nao Alterado. Alteracao Abortada.\\n";
            $this->erro_sql .= "Valores : ".$this->c21_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_alterar = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "conplanoorcamentogrupo nao foi Alterado. Alteracao Executada.\\n";
                $this->erro_sql .= "Valores : ".$this->c21_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Alteração efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$this->c21_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_alterar = pg_affected_rows($result);
                return true;
            }
        }
    }
   // funcao para exclusao
    public function excluir ($c21_sequencial=null,$dbwhere=null) {
     $sql = " delete from conplanoorcamentogrupo
                    where ";
        $sql2 = "";
        if ($dbwhere==null || $dbwhere =="") {
            if ($c21_sequencial != "") {
                if ($sql2!="") {
                    $sql2 .= " and ";
                }
                $sql2 .= " c21_sequencial = $c21_sequencial ";
            }
        } else {
            $sql2 = $dbwhere;
        }
        $result = db_query($sql.$sql2);
        if (!$result) {
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "conplanoorcamentogrupo nao Excluído. Exclusão Abortada.\\n";
            $this->erro_sql .= "Valores : ".$c21_sequencial;
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            $this->numrows_excluir = 0;
            return false;
        } else {
            if (pg_affected_rows($result)==0) {
                $this->erro_banco = "";
                $this->erro_sql = "conplanoorcamentogrupo nao Encontrado. Exclusão não Efetuada.\\n";
                $this->erro_sql .= "Valores : ".$c21_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                $this->erro_status = "1";
                $this->numrows_excluir = 0;
                return true;
            } else {
                $this->erro_banco = "";
                $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
                $this->erro_sql .= "Valores : ".$c21_sequencial;
                $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
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
            $this->erro_banco = str_replace("\n", "", @pg_last_error());
            $this->erro_sql   = "Erro ao selecionar os registros.";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        $this->numrows = pg_numrows($result);
        if ($this->numrows==0) {
            $this->erro_banco = "";
            $this->erro_sql   = "Record Vazio na Tabela:conplanoorcamentogrupo";
            $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
            $this->erro_msg   .=  str_replace('"', "", str_replace("'", "", "Administrador: \\n\\n ".$this->erro_banco." \\n"));
            $this->erro_status = "0";
            return false;
        }
        return $result;
    }
   // funcao do sql
    public function sql_query ( $c21_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from conplanoorcamentogrupo ";
        $sql .= "      inner join db_config  on  db_config.codigo = conplanoorcamentogrupo.c21_instit";
        $sql .= "      inner join congrupo  on  congrupo.c20_sequencial = conplanoorcamentogrupo.c21_congrupo";
        $sql .= "      inner join conplanoorcamento  on  conplanoorcamento.c60_codcon = conplanoorcamentogrupo.c21_codcon and  conplanoorcamento.c60_anousu = conplanoorcamentogrupo.c21_anousu";
        $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
        $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
        $sql .= "      inner join conclass  on  conclass.c51_codcla = conplanoorcamento.c60_codcla";
        $sql .= "      inner join consistema  on  consistema.c52_codsis = conplanoorcamento.c60_codsis";
        $sql .= "      inner join consistemaconta  on  consistemaconta.c65_sequencial = conplanoorcamento.c60_consistemaconta";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($c21_sequencial!=null) {
                $sql2 .= " where conplanoorcamentogrupo.c21_sequencial = $c21_sequencial ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
   // funcao do sql
    public function sql_query_file ( $c21_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = explode("#", $campos);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        } else {
            $sql .= $campos;
        }
        $sql .= " from conplanoorcamentogrupo ";
        $sql2 = "";
        if ($dbwhere=="") {
            if ($c21_sequencial!=null) {
                $sql2 .= " where conplanoorcamentogrupo.c21_sequencial = $c21_sequencial ";
            }
        } elseif ($dbwhere != "") {
            $sql2 = " where $dbwhere";
        }
        $sql .= $sql2;
        if ($ordem != null) {
            $sql .= " order by ";
            $campos_sql = explode("#", $ordem);
            $virgula = "";
            for ($i=0; $i<sizeof($campos_sql); $i++) {
                $sql .= $virgula.$campos_sql[$i];
                $virgula = ",";
            }
        }
        return $sql;
    }
}
?>
