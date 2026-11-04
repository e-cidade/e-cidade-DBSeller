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

//MODULO: fiscal
//CLASSE DA ENTIDADE procfiscallanc
class cl_fis_procfiscallanc {
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
   public $nl09_sequencial = 0;
   public $nl09_procfiscal = 0;
   public $nl09_lanc = 0;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 nl09_sequencial = int4 = Código
                 nl09_procfiscal = int4 = Sequencial
                 nl09_lanc = int4 = Código da Notificação de Lançamento
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_procfiscallanc");
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
       $this->nl09_sequencial = ($this->nl09_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["nl09_sequencial"]:$this->nl09_sequencial);
       $this->nl09_procfiscal = ($this->nl09_procfiscal == ""?@$GLOBALS["HTTP_POST_VARS"]["nl09_procfiscal"]:$this->nl09_procfiscal);
       $this->nl09_lanc = ($this->nl09_lanc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl09_lanc"]:$this->nl09_lanc);
     }else{
       $this->nl09_sequencial = ($this->nl09_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["nl09_sequencial"]:$this->nl09_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($nl09_sequencial){
      $this->atualizacampos();
     if($this->nl09_procfiscal == null ){
       $this->erro_sql = " Campo Sequencial nao Informado.";
       $this->erro_campo = "nl09_procfiscal";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->nl09_lanc == null ){
       $this->erro_sql = " Campo Código do lancamento de Infração nao Informado.";
       $this->erro_campo = "nl09_lanc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($nl09_sequencial == "" || $nl09_sequencial == null ){
       $result = db_query("select nextval('fis_procfiscallanc_nl09_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: fis_procfiscallanc_nl09_sequencial_seq do campo: nl09_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->nl09_sequencial = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from fis_procfiscallanc_nl09_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $nl09_sequencial)){
         $this->erro_sql = " Campo nl09_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->nl09_sequencial = $nl09_sequencial;
       }
     }
     if(($this->nl09_sequencial == null) || ($this->nl09_sequencial == "") ){
       $this->erro_sql = " Campo nl09_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into fiscalizacao.fis_procfiscallanc(
                                       nl09_sequencial
                                      ,nl09_procfiscal
                                      ,nl09_lanc
                       )
                values (
                                $this->nl09_sequencial
                               ,$this->nl09_procfiscal
                               ,$this->nl09_lanc
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "procfiscallanc ($this->nl09_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "procfiscallanc já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "procfiscallanc ($this->nl09_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl09_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   // funcao para alteracao
   public function alterar ($nl09_sequencial=null) {
      $this->atualizacampos();
     $sql = " update fiscalizacao.fis_procfiscallanc set ";
     $virgula = "";
     if(trim($this->nl09_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl09_sequencial"])){
       $sql  .= $virgula." nl09_sequencial = $this->nl09_sequencial ";
       $virgula = ",";
       if(trim($this->nl09_sequencial) == null ){
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "nl09_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl09_procfiscal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl09_procfiscal"])){
       $sql  .= $virgula." nl09_procfiscal = $this->nl09_procfiscal ";
       $virgula = ",";
       if(trim($this->nl09_procfiscal) == null ){
         $this->erro_sql = " Campo Sequencial nao Informado.";
         $this->erro_campo = "nl09_procfiscal";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->nl09_lanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["nl09_lanc"])){
       $sql  .= $virgula." nl09_lanc = $this->nl09_lanc ";
       $virgula = ",";
       if(trim($this->nl09_lanc) == null ){
         $this->erro_sql = " Campo Código do lancamento de Infração nao Informado.";
         $this->erro_campo = "nl09_lanc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($nl09_sequencial!=null){
       $sql .= " nl09_sequencial = $this->nl09_sequencial";
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "procfiscallanc nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl09_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "procfiscallanc nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl09_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl09_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($nl09_sequencial=null,$dbwhere=null) {
     $sql = " delete from fiscalizacao.fis_procfiscallanc
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($nl09_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " nl09_sequencial = $nl09_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "procfiscallanc nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$nl09_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "procfiscallanc nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$nl09_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$nl09_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:procfiscallanc";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   public function sql_query ( $nl09_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_procfiscallanc ";
     $sql .= "      inner join fiscalizacao.fis_lancamento  on  fis_lancamento.nl01_codlanc = fis_procfiscallanc.nl09_lanc";
     $sql .= "      inner join fiscalizacao.fis_procfiscal  on  fis_procfiscal.y100_sequencial = fis_procfiscallanc.nl09_procfiscal";
     $sql .= "      inner join db_config  on  db_config.codigo = fis_lancamento.nl01_instit";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = fis_lancamento.nl01_setor";
     $sql .= "      inner join fiscalizacao.fis_tipofiscaliza  on  fis_tipofiscaliza.y27_codtipo = fis_lancamento.nl01_codtipo";
     $sql .= "      inner join db_config  as a on   a.codigo = fis_procfiscal.y100_instit";
     $sql .= "      inner join db_depart  as b on   b.coddepto = fis_procfiscal.y100_coddepto";
     $sql .= "      inner join fiscalizacao.fis_procfiscalcadtipo  on  fis_procfiscalcadtipo.y33_sequencial = fis_procfiscal.y100_procfiscalcadtipo";
     $sql2 = "";
     if($dbwhere==""){
       if($nl09_sequencial!=null ){
         $sql2 .= " where fis_procfiscallanc.nl09_sequencial = $nl09_sequencial ";
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
   public function sql_query_file ( $nl09_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fiscalizacao.fis_procfiscallanc ";
     $sql2 = "";
     if($dbwhere==""){
       if($nl09_sequencial!=null ){
         $sql2 .= " where fis_procfiscallanc.nl09_sequencial = $nl09_sequencial ";
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
  /*
    @ Funcao Que retorna a origem do processo fiscal
  */
  public function getTipoProcessoFiscal ( $iProcessoFiscal ){

    $sTipoProc  = " select y103_inscr as q02_inscr, y102_matric as j01_matric, y104_codsani as y80_codsani, y101_numcgm as z01_numcgm  from fiscalizacao.fis_procfiscal ";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalinscr  on y103_procfiscal = y100_sequencial";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalmatric on y102_procfiscal = y100_sequencial";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalsani   on y104_procfiscal = y100_sequencial";
    $sTipoProc .= "     left join fiscalizacao.fis_procfiscalcgm    on y101_procfiscal = y100_sequencial";
    $sTipoProc .= "     where y100_sequencial = ".$iProcessoFiscal;

    return $sTipoProc;
  }

  /*
   * @Busca o processo Administrativo do processo fiscal
   */
  public function getTipoProcessoAdministrativo ( $iProcessoFiscal = null , $sWhere = null ){

    $sSqlGetProcessoAdministrativo  = " select p58_numero || '/' || p58_ano as p58_numero, p58_requer from fiscalizacao.fis_lancamento ";
    $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscallanc on fis_procfiscallanc.nl09_lanc = fis_lancamento.nl01_codlanc ";
    $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscal on fis_procfiscallanc.nl09_procfiscal = fis_procfiscal.y100_sequencial ";
    $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscalprot on fis_procfiscalprot.y105_procfiscal = fis_procfiscal.y100_sequencial ";
    $sSqlGetProcessoAdministrativo .= " inner join protprocesso on fis_procfiscalprot.y105_protprocesso = protprocesso.p58_codproc ";
    if( $sWhere != null ){
      $sSqlGetProcessoAdministrativo .= " where ".$sWhere;
    }else{
      $sSqlGetProcessoAdministrativo .= "     where fis_procfiscal.y100_sequencial = ".$iProcessoFiscal;
    }
    return $sSqlGetProcessoAdministrativo;
  }
}

?>
