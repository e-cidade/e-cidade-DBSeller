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

class cl_fis_paragrafo
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
    public $pl09_codigo = 0;
    public $pl09_tipo = 0;
    public $pl09_descr = null;
    public $pl09_resumo = null;
    public $pl09_texto = null;
    public $pl09_status = 'f';
    public $pl09_paragrafo = 0;
    public $pl09_coddepto = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 pl09_codigo = int4 = Codigo Paragrafo
                 pl09_tipo = int4 = Tipo de parágrafo
                 pl09_descr = text = Descrição parágrafo
                 pl09_resumo = varchar(130) = Resumo parágrafo
                 pl09_texto = text = Texto parágrafo
                 pl09_status = bool = Status do paragrafo
                 pl09_paragrafo = int4 = Tipo de documento
                 pl09_coddepto = int4 = Codigo do departamento
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("fis_paragrafo");
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
       $this->pl09_codigo = ($this->pl09_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["pl09_codigo"]:$this->pl09_codigo);
       $this->pl09_tipo = ($this->pl09_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["pl09_tipo"]:$this->pl09_tipo);
       $this->pl09_descr = ($this->pl09_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["pl09_descr"]:$this->pl09_descr);
       $this->pl09_resumo = ($this->pl09_resumo == ""?@$GLOBALS["HTTP_POST_VARS"]["pl09_resumo"]:$this->pl09_resumo);
       $this->pl09_texto = ($this->pl09_texto == ""?@$GLOBALS["HTTP_POST_VARS"]["pl09_texto"]:$this->pl09_texto);
       $this->pl09_status = ($this->pl09_status == "f"?@$GLOBALS["HTTP_POST_VARS"]["pl09_status"]:$this->pl09_status);
       $this->pl09_paragrafo = ($this->pl09_paragrafo == ""?@$GLOBALS["HTTP_POST_VARS"]["pl09_paragrafo"]:$this->pl09_paragrafo);
       $this->pl09_coddepto = ($this->pl09_coddepto == ""?@$GLOBALS["HTTP_POST_VARS"]["pl09_coddepto"]:$this->pl09_coddepto);
     }else{
       $this->pl09_codigo = ($this->pl09_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["pl09_codigo"]:$this->pl09_codigo);
     }
   }

    public function incluir($pl09_codigo)
    {
      $this->atualizacampos();
     if($pl09_codigo == "" || $pl09_codigo == null ){
       $result = db_query("select nextval('fis_paragrafo_pl09_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: fis_paragrafo_pl09_codigo_seq do campo: pl09_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->pl09_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from fis_paragrafo_pl09_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $pl09_codigo)){
         $this->erro_sql = " Campo pl09_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->pl09_codigo = $pl09_codigo;
       }
     }
     if(($this->pl09_codigo == null) || ($this->pl09_codigo == "") ){
       $this->erro_sql = " Campo pl09_codigo não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_paragrafo(
                                       pl09_codigo
                                      ,pl09_tipo
                                      ,pl09_descr
                                      ,pl09_resumo
                                      ,pl09_texto
                                      ,pl09_status
                                      ,pl09_paragrafo
                                      ,pl09_coddepto
                       )
                values (
                                $this->pl09_codigo
                               ,$this->pl09_tipo
                               ,'$this->pl09_descr'
                               ,'$this->pl09_resumo'
                               ,'$this->pl09_texto'
                               ,'$this->pl09_status'
                               ,$this->pl09_paragrafo
                               ,$this->pl09_coddepto
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "paragrafo ($this->pl09_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "paragrafo já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "paragrafo ($this->pl09_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->pl09_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);

     return true;
   }

    public function alterar($pl09_codigo=null)
    {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_paragrafo set ";
     $virgula = "";
     if(trim($this->pl09_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl09_codigo"])){
       $sql  .= $virgula." pl09_codigo = $this->pl09_codigo ";
       $virgula = ",";
       if(trim($this->pl09_codigo) == null ){
         $this->erro_sql = " Campo Codigo Paragrafo não informado.";
         $this->erro_campo = "pl09_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pl09_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl09_tipo"])){
        if(trim($this->pl09_tipo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["pl09_tipo"])){
           $this->pl09_tipo = "0" ;
        }
       $sql  .= $virgula." pl09_tipo = $this->pl09_tipo ";
       $virgula = ",";
     }
     if(trim($this->pl09_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl09_descr"])){
       $sql  .= $virgula." pl09_descr = '$this->pl09_descr' ";
       $virgula = ",";
     }
     if(trim($this->pl09_resumo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl09_resumo"])){
       $sql  .= $virgula." pl09_resumo = '$this->pl09_resumo' ";
       $virgula = ",";
     }
     if(trim($this->pl09_texto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl09_texto"])){
       $sql  .= $virgula." pl09_texto = '$this->pl09_texto' ";
       $virgula = ",";
     }
     if(trim($this->pl09_status)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl09_status"])){
       $sql  .= $virgula." pl09_status = '$this->pl09_status' ";
       $virgula = ",";
     }
     if(trim($this->pl09_paragrafo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl09_paragrafo"])){
        if(trim($this->pl09_paragrafo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["pl09_paragrafo"])){
           $this->pl09_paragrafo = "0" ;
        }
       $sql  .= $virgula." pl09_paragrafo = $this->pl09_paragrafo ";
       $virgula = ",";
     }
     if(trim($this->pl09_coddepto)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pl09_coddepto"])){
        if(trim($this->pl09_coddepto)=="" && isset($GLOBALS["HTTP_POST_VARS"]["pl09_coddepto"])){
           $this->pl09_coddepto = "0" ;
        }
       $sql  .= $virgula." pl09_coddepto = $this->pl09_coddepto ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($pl09_codigo!=null){
       $sql .= " pl09_codigo = $this->pl09_codigo";
     }


     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "paragrafo não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->pl09_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "paragrafo não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->pl09_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->pl09_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($pl09_codigo=null, $dbwhere = null)
    {

     $sql = " delete from fiscalizacao.fis_paragrafo
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($pl09_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " pl09_codigo = $pl09_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "paragrafo não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$pl09_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "paragrafo não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$pl09_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$pl09_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:paragrafo";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($pl09_codigo = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from fiscalizacao.fis_paragrafo ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($pl09_codigo)) {
         $sql2 .= " where fis_paragrafo.pl09_codigo = $pl09_codigo ";
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

    public function sql_query_file($pl09_codigo = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from fiscalizacao.fis_paragrafo ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($pl09_codigo)){
         $sql2 .= " where fis_paragrafo.pl09_codigo = $pl09_codigo ";
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
