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

class cl_fis_fiscalparametros
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
    public $pa01_instit = 0;
    public $pa01_autodvenc = 0;
    public $pa01_autodprazo = 0;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 pa01_instit = int4 = Código da instituição
                 pa01_autodvenc = int4 = Qtd. dias vencto
                 pa01_autodprazo = int4 = Qtd. dias recurso
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("fis_fiscalparametros");
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
       $this->pa01_instit = ($this->pa01_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["pa01_instit"]:$this->pa01_instit);
       $this->pa01_autodvenc = ($this->pa01_autodvenc == ""?@$GLOBALS["HTTP_POST_VARS"]["pa01_autodvenc"]:$this->pa01_autodvenc);
       $this->pa01_autodprazo = ($this->pa01_autodprazo == ""?@$GLOBALS["HTTP_POST_VARS"]["pa01_autodprazo"]:$this->pa01_autodprazo);
     }else{
       $this->pa01_instit = ($this->pa01_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["pa01_instit"]:$this->pa01_instit);
     }
   }

    public function incluir($pa01_instit)
    {
      $this->atualizacampos();
     if($this->pa01_autodvenc == null ){
       $this->erro_sql = " Campo Qtd. dias vencto não informado.";
       $this->erro_campo = "pa01_autodvenc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->pa01_autodprazo == null ){
       $this->erro_sql = " Campo Qtd. dias recurso não informado.";
       $this->erro_campo = "pa01_autodprazo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->pa01_instit = $pa01_instit;
     if(($this->pa01_instit == null) || ($this->pa01_instit == "") ){
       $this->erro_sql = " Campo pa01_instit não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_fiscalparametros(
                                       pa01_instit
                                      ,pa01_autodvenc
                                      ,pa01_autodprazo
                       )
                values (
                                $this->pa01_instit
                               ,$this->pa01_autodvenc
                               ,$this->pa01_autodprazo
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "fiscalparametros ($this->pa01_instit) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "fiscalparametros já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "fiscalparametros ($this->pa01_instit) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->pa01_instit;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }

    public function alterar($pa01_instit=null)
    {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_fiscalparametros set ";
     $virgula = "";
     if(trim($this->pa01_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pa01_instit"])){
       $sql  .= $virgula." pa01_instit = $this->pa01_instit ";
       $virgula = ",";
       if(trim($this->pa01_instit) == null ){
         $this->erro_sql = " Campo Código da instituição não informado.";
         $this->erro_campo = "pa01_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pa01_autodvenc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pa01_autodvenc"])){
       $sql  .= $virgula." pa01_autodvenc = $this->pa01_autodvenc ";
       $virgula = ",";
       if(trim($this->pa01_autodvenc) == null ){
         $this->erro_sql = " Campo Qtd. dias vencto não informado.";
         $this->erro_campo = "pa01_autodvenc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->pa01_autodprazo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["pa01_autodprazo"])){
       $sql  .= $virgula." pa01_autodprazo = $this->pa01_autodprazo ";
       $virgula = ",";
       if(trim($this->pa01_autodprazo) == null ){
         $this->erro_sql = " Campo Qtd. dias recurso não informado.";
         $this->erro_campo = "pa01_autodprazo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($pa01_instit!=null){
       $sql .= " pa01_instit = $this->pa01_instit";
     }

     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "fiscalparametros não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->pa01_instit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "fiscalparametros não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->pa01_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->pa01_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir($pa01_instit=null, $dbwhere = null)
    {
     $sql = " delete from fiscalizacao.fis_fiscalparametros
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($pa01_instit)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " pa01_instit = $pa01_instit ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "fiscalparametros não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$pa01_instit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "fiscalparametros não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$pa01_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$pa01_instit;
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
        $this->erro_sql   = "Record Vazio na Tabela:fiscalparametros";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($pa01_instit = null,$campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos}";
     $sql .= "  from fiscalizacao.fis_fiscalparametros ";
     $sql .= "      inner join db_config  on  db_config.codigo = fis_fiscalparametros.pa01_instit";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($pa01_instit)) {
         $sql2 .= " where fis_fiscalparametros.pa01_instit = $pa01_instit ";
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

    public function sql_query_file($pa01_instit = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from fiscalizacao.fis_fiscalparametros ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($pa01_instit)){
         $sql2 .= " where fis_fiscalparametros.pa01_instit = $pa01_instit ";
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
