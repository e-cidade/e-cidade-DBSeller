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

//MODULO: cadastro
//CLASSE DA ENTIDADE cadimobil
class cl_cadimobil
 {  // cria variaveis de erro
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
   public $j63_numcgm = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 j63_numcgm = int4 = Numcgm imobiliária
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("cadimobil");
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
   public function atualizacampos($exclusao = false)
  {

    if ($exclusao == false) {
      $this->j63_numcgm = ($this->j63_numcgm == "" ? @$_POST["j63_numcgm"] : $this->j63_numcgm);
    } else {
      $this->j63_numcgm = ($this->j63_numcgm == "" ? @$_POST["j63_numcgm"]:$this->j63_numcgm);
     }
   }
   // funcao para inclusao
   public function incluir($j63_numcgm)
  {    $this->atualizacampos();
       $this->j63_numcgm = $j63_numcgm;
     if(($this->j63_numcgm == null) || ($this->j63_numcgm == "") ){
       $this->erro_sql = " Campo j63_numcgm nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into cadimobil(
                                       j63_numcgm
                       )
                values (
                                $this->j63_numcgm
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "cadastro de imobiliárias ($this->j63_numcgm) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "cadastro de imobiliárias já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "cadastro de imobiliárias ($this->j63_numcgm) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;}

     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j63_numcgm;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->j63_numcgm));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,2431,'$this->j63_numcgm','I')");
       $resac = db_query("insert into db_acount values($acount,394,2431,'','".AddSlashes(pg_result($resaco,0,'j63_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar($j63_numcgm = null)
  {
    $this->atualizacampos();
    $sql = " update cadimobil set ";
    $virgula = "";

    if (trim($this->j63_numcgm) != "" || isset($_POST["j63_numcgm"])){
       $sql  .= $virgula." j63_numcgm = $this->j63_numcgm ";
       $virgula = ",";
       if(trim($this->j63_numcgm) == null ){
         $this->erro_sql = " Campo Numcgm imobiliária nao Informado.";
         $this->erro_campo = "j63_numcgm";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($j63_numcgm!=null){
       $sql .= " j63_numcgm = $this->j63_numcgm";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->j63_numcgm));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,2431,'$this->j63_numcgm','A')");
         if(isset($_POST["j63_numcgm"]))
           $resac = db_query("insert into db_acount values($acount,394,2431,'".AddSlashes(pg_result($resaco,$conresaco,'j63_numcgm'))."','$this->j63_numcgm',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "cadastro de imobiliárias nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->j63_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "cadastro de imobiliárias nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->j63_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j63_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir($j63_numcgm = null, $dbwhere = null, $validarMatriculasVinculadas = true)
  {
    $matriculasVinculadas = $this->buscaMatriculasVinculadas($j63_numcgm);
    $possuiMatriculasAtivasVinculadas = count($matriculasVinculadas) > 0;

    if (
      $j63_numcgm != null
      && trim($j63_numcgm) != ""
      && $possuiMatriculasAtivasVinculadas
      && $validarMatriculasVinculadas
    ) {
      $this->erro_sql   = "Cadastro com matriculas vinculadas. Exclusão Abortada.";
      $this->erro_msg   = "Usuário: \\n\\n " . $this->erro_sql . " \\n\\n";
      $this->erro_msg   .=  str_replace('"', "", str_replace("'", "",  "Administrador: \\n\\n " . $this->erro_banco . " \\n"));
      $this->erro_status = "0";
      $this->numrows_excluir = 0;

      return false;
    }
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($j63_numcgm));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,2431,'$j63_numcgm','E')");
         $resac = db_query("insert into db_acount values($acount,394,2431,'','".AddSlashes(pg_result($resaco,$iresaco,'j63_numcgm'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");}
    }

    $sql = " delete from cadimobil
                    where ";
    $sql2 = "";

    if ($dbwhere == null || $dbwhere == "") {
      if ($j63_numcgm != "") {
        if ($sql2 != "") {
          $sql2 .= " and ";
        }
        $sql2 .= " j63_numcgm = $j63_numcgm ";
      }
    } else {
      $sql2 = $dbwhere;
    }

     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "cadastro de imobiliárias nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$j63_numcgm;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "cadastro de imobiliárias nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$j63_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$j63_numcgm;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;}
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
        $this->erro_sql   = "Record Vazio na Tabela:cadimobil";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   public function sql_query ( $j63_numcgm=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from cadimobil ";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = cadimobil.j63_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($j63_numcgm!=null ){
         $sql2 .= " where cadimobil.j63_numcgm = $j63_numcgm ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";}

    $sql .= $sql2;

    if ($ordem != null) {
      $sql .= " order by ";
      $campos_sql = explode("#", $ordem);
      $virgula = "";

      for ($i = 0; $i < sizeof($campos_sql); $i++) {
        $sql .= $virgula . $campos_sql[$i];
        $virgula = ",";
      }
    }

    return $sql;
  }
   public function sql_query_file($j63_numcgm = null, $campos = "*", $ordem = null, $dbwhere = "")
  {
    $sql = "select ";

    if ($campos != "*") {
      $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cadimobil ";
     $sql2 = "";
     if($dbwhere==""){
       if($j63_numcgm!=null ){
         $sql2 .= " where cadimobil.j63_numcgm = $j63_numcgm ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#", $ordem);
      $virgula = "";

      for ($i = 0; $i < sizeof($campos_sql); $i++) {
        $sql .= $virgula . $campos_sql[$i];
        $virgula = ",";
      }
    }

    return $sql;
  }

  /**
   * Sql para buscar matriculas ativas de uma imobiliaria
   *
   * @param integer $j63_numcgm
   * @return string
   */
  public function sql_matriculas_ativas($j63_numcgm)
  {
    $sql  = " select                                                       ";
    $sql .= "     j44_numcgm, j01_baixa                                     ";
    $sql .= " from                                                         ";
    $sql .= "     imobil                                                   ";
    $sql .= "     join iptubase ON iptubase.j01_matric = imobil.j44_matric ";
    $sql .= " where                                                        ";
    $sql .= "     iptubase.j01_baixa is null                               ";
    $sql .= "     and j44_numcgm = {$j63_numcgm}                           ";

    return $sql;
  }

  /**
   * Metodo para buscar matriculas ativas de uma imobiliaria
   *
   * @param integer $j63_numcgm
   * @return array
   */
  public function buscaMatriculasVinculadas($j63_numcgm)
  {
    $sSsqlMatriculasVinculadas = $this->sql_matriculas_ativas($j63_numcgm);
    $rsMatriculasVinculadas = db_query($sSsqlMatriculasVinculadas);

    return db_utils::getCollectionByRecord($rsMatriculasVinculadas);
  }
}
