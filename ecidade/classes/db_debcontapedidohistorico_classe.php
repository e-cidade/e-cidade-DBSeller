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

//MODULO: caixa
//CLASSE DA ENTIDADE debcontapedidohistorico
class cl_debcontapedidohistorico {
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
   var $d83_sequencial = null;
   var $d83_debcontapedido = 0;
   var $d83_instit = 0;
   var $d83_banco = 0;
   var $d83_agencia = null;
   var $d83_conta = null;
   var $d83_datalanc_dia = null;
   var $d83_datalanc_mes = null;
   var $d83_datalanc_ano = null;
   var $d83_datalanc = null;
   var $d83_horalanc = null;
   var $d83_status = 0;
   var $d83_acao = 0;
   var $d83_idempresa = null;
   var $d83_codret = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 d83_sequencial = int4 = Codigo sequencial
                 d83_debcontapedido = int4 = Codigo da DebcontaPedido
                 d83_instit = int4 = codigo da instituicao
                 d83_banco = int4 = codigo do banco
                 d83_agencia = char(4) = Agencia
                 d83_conta = varchar(14) = Conta
                 d83_datalanc = date = Data de lancamento
                 d83_horalanc = char(5) = Hora de lancamento
                 d83_status = int4 = 1 - Pendente, 2 - Ativo, 3- Inativo
                 d83_acao = int4 = 1 - Inclusao, 2 - Alteração, 3- Exclusão
                 d83_idempresa = varchar(25) = Id Empresa
                 d83_codret = int4 = Codigo do Arquivo Retorno
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("debcontapedidohistorico");
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
       $this->d83_sequencial = ($this->d83_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_sequencial"]:$this->d83_sequencial);
       $this->d83_debcontapedido = ($this->d83_debcontapedido == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_debcontapedido"]:$this->d83_debcontapedido);
       $this->d83_instit = ($this->d83_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_instit"]:$this->d83_instit);
       $this->d83_banco = ($this->d83_banco == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_banco"]:$this->d83_banco);
       $this->d83_agencia = ($this->d83_agencia == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_agencia"]:$this->d83_agencia);
       $this->d83_conta = ($this->d83_conta == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_conta"]:$this->d83_conta);
       if($this->d83_datalanc == ""){
         $this->d83_datalanc_dia = ($this->d83_datalanc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_datalanc_dia"]:$this->d83_datalanc_dia);
         $this->d83_datalanc_mes = ($this->d83_datalanc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_datalanc_mes"]:$this->d83_datalanc_mes);
         $this->d83_datalanc_ano = ($this->d83_datalanc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_datalanc_ano"]:$this->d83_datalanc_ano);
         if($this->d83_datalanc_dia != ""){
            $this->d83_datalanc = $this->d83_datalanc_ano."-".$this->d83_datalanc_mes."-".$this->d83_datalanc_dia;
         }
       }
       $this->d83_horalanc = ($this->d83_horalanc == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_horalanc"]:$this->d83_horalanc);
       $this->d83_status = ($this->d83_status == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_status"]:$this->d83_status);
       $this->d83_acao = ($this->d83_acao == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_acao"]:$this->d83_acao);
       $this->d83_idempresa = ($this->d83_idempresa == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_idempresa"]:$this->d83_idempresa);
       $this->d83_codret = ($this->d83_codret == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_codret"]:$this->d83_codret);
     }else{
       $this->d83_sequencial = ($this->d83_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["d83_sequencial"]:$this->d83_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir (){
      $this->atualizacampos();
     if($this->d83_instit == null ){
       $this->erro_sql = " Campo codigo da instituicao nao Informado.";
       $this->erro_campo = "d83_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->d83_banco == null ){
       $this->erro_sql = " Campo codigo do banco nao Informado.";
       $this->erro_campo = "d83_banco";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->d83_agencia == null ){
       $this->erro_sql = " Campo Agencia nao Informado.";
       $this->erro_campo = "d83_agencia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->d83_conta == null ){
       $this->erro_sql = " Campo Conta nao Informado.";
       $this->erro_campo = "d83_conta";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->d83_datalanc == null ){
       $this->erro_sql = " Campo Data de lancamento nao Informado.";
       $this->erro_campo = "d83_datalanc_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->d83_horalanc == null ){
       $this->erro_sql = " Campo Hora de lancamento nao Informado.";
       $this->erro_campo = "d83_horalanc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->d83_status == null ){
       $this->erro_sql = " Campo Status nao Informado.";
       $this->erro_campo = "d83_status";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->d83_debcontapedido == null ){
       $this->erro_sql = " Campo Codigo DebContaPedido nao Informado.";
       $this->erro_campo = "d83_status";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->d83_acao == null ){
       $this->erro_sql = " Campo Acao nao Informado.";
       $this->erro_campo = "d83_acao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->d83_codret == null ){
       $this->erro_sql = " Campo Codigo do Arquivo nao Informado.";
       $this->erro_campo = "d83_codret";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $sql = "insert into debcontapedidohistorico(
                                       d83_debcontapedido
                                      ,d83_instit
                                      ,d83_banco
                                      ,d83_agencia
                                      ,d83_conta
                                      ,d83_datalanc
                                      ,d83_horalanc
                                      ,d83_status
                                      ,d83_acao
                                      ,d83_idempresa
                                      ,d83_codret
                       )
                values (
                                $this->d83_debcontapedido
                               ,$this->d83_instit
                               ,$this->d83_banco
                               ,'$this->d83_agencia'
                               ,'$this->d83_conta'
                               ,".($this->d83_datalanc == "null" || $this->d83_datalanc == ""?"null":"'".$this->d83_datalanc."'")."
                               ,'$this->d83_horalanc'
                               ,$this->d83_status
                               ,$this->d83_acao
                               ,'$this->d83_idempresa'
                               ,$this->d83_codret
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Registro do historico debito em conta ($this->d83_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Registro do historico debito em conta já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Registro do historico do debito em conta ($this->d83_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->d83_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     return true;
   }
   public function excluir ($d83_sequencial=null,$dbwhere=null) {
     $sql = " delete from debcontapedidohistorico
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($d63_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " d83_sequencial = $d83_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Historico do debito em conta nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$d83_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Historico do debito em conta nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$d83_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$d83_sequencial;
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
        $this->erro_sql   = "Record Vazio na Tabela:debcontapedidohistorico";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $d83_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from debcontapedidohistorico ";
     $sql .= "      inner join db_config  on  db_config.codigo = debcontapedido.d83_instit";
     $sql .= "      inner join bancos  on  bancos.codbco = debcontapedido.d83_banco";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($d83_sequencial!=null ){
         $sql2 .= " where debcontapedidohistorico.d83_sequencial = $d83_sequencial ";
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
   // funcao do sql
   public function sql_query_file ( $d83_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from debcontapedidohistorico ";
     $sql2 = "";
     if($dbwhere==""){
       if($d83_sequencial!=null ){
         $sql2 .= " where debcontapedidohistorico.d83_sequencial = $d83_sequencial ";
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
   public function sql_query_info ( $d83_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from debcontapedidohistorico ";
     $sql .= "      inner join debcontapedido on  d63_codigo = d83_debcontapedido";
     $sql .= "      inner join db_config  on  db_config.codigo = debcontapedido.d63_instit";
     $sql .= "      inner join bancos  on  bancos.codbco = debcontapedido.d63_banco";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql .= "      left join debcontapedidocgm on d70_codigo = d63_codigo";
     $sql .= "      left join debcontapedidomatric on d68_codigo = d63_codigo";
     $sql .= "      left join debcontapedidoinscr on d69_codigo = d63_codigo";
     $sql .= "      left join debcontapedidoaguacontrato on d81_codigo = d63_codigo";
     $sql .= "      left join debcontapedidoaguacontratoeconomia on d82_codigo = d63_codigo";
     $sql2 = "";
     if($dbwhere==""){
       if($d83_sequencial!=null ){
         $sql2 .= " where debcontapedidohistorico.d83_sequencial = $d83_sequencial ";
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

  public function sql_query_deb_conta($d83_sequencial=null,$campos="*",$ordem=null,$dbwhere="") {
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
     $sql .= " from debcontapedidohistorico ";
     $sql .= "      inner join debcontapedido on d63_codigo = d83_debcontapedido";
     $sql .= "      inner join debcontapedidotipo on d66_codigo = d63_codigo";
     $sql .= "      inner join arretipo           on k00_tipo   = d66_arretipo";
     $sql2 = "";
     if($dbwhere==""){
       if($d83_sequencial!=null ){
         $sql2 .= " where debcontapedidohistorico.d83_sequencial = $d83_sequencial ";
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

  public function sql_query_pedido_agua($sCampos = '*', $sWhere = null, $sOrder = null)
  {
    $aSql = array();

    $aSql[] = "select {$sCampos}";
    $aSql[] = "from debcontapedidohistorico";
    $asql[] = "inner join debcontapedido on d63_codigo = d83_debcontapedido";
    $aSql[] = "inner join bancos on d83_banco = codbco";
    $aSql[] = "left join debcontapedidoaguacontrato on d81_codigo = d63_codigo";
    $aSql[] = "left join debcontapedidoaguacontratoeconomia on d82_codigo = d63_codigo";

    if ($sWhere) {
      $aSql[] = "where {$sWhere}";
    }

    if ($sOrder) {
      $aSql[] = "order by {$sOrder}";
    }

    return implode(' ', $aSql);
  }
}
