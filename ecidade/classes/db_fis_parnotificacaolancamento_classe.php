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

//MODULO: fiscal
//CLASSE DA ENTIDADE Notificação de Lançamento

class cl_fis_parnotificacaolancamento{
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

   public $nl27_instit   	 = null;
   public $nl27_tipo 		 = null;
   public $nl27_historico    = null;
   public $nl27_procbaixaauto = null;
   public $nl27_utilizadocpadrao = null;
   public $nl27_templateautoinfracao   = null;
   public $nl27_autodvenc 	 = null;
   public $nl27_autodprazo 	 = null;

   public $campos = "
                 nl27_instit = int4 = Código da Instituição
                 nl27_tipo = = int4 = Tipo de Débito
                 nl27_historico = int4 = Histórico do Cálculo
                 nl27_procbaixaauto = boolean = Documento Padrão da Notificação de Lançamento
                 nl27_utilizadocpadrao = boolean = Utiliza Documento Padrão
                 nl27_templateautoinfracao = int4 = Template Auto Infração
                 nl27_autodvenc = int4 = Data do Vencimento da Notificação de Lançamento
                 nl27_autodprazo = int(4) = Prazo da Notificação de Lançamento
                 ";

   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     // $this->rotulo = new rotulo("fis_parnotificacaolancamento");
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }

  //funcao erro
   public function erro($mostra=false,$retorna=false) {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

	public function atualizacampos($exclusao=false) {
		if($exclusao == false){
			$this->nl27_instit = ($this->nl27_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["nl27_instit"]:$this->nl27_instit);
			$this->nl27_tipo = ($this->nl27_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl27_tipo"]:$this->nl27_tipo);
			$this->nl27_historico = ($this->nl27_historico == ""?@$GLOBALS["HTTP_POST_VARS"]["nl27_historico"]:$this->nl27_historico);
			$this->nl27_procbaixaauto = ($this->nl27_procbaixaauto == ""?@$GLOBALS["HTTP_POST_VARS"]["nl27_procbaixaauto"]:$this->nl27_procbaixaauto);
			$this->nl27_utilizadocpadrao = ($this->nl27_utilizadocpadrao == ""?@$GLOBALS["HTTP_POST_VARS"]["nl27_utilizadocpadrao"]:$this->nl27_utilizadocpadrao);
			$this->nl27_templateautoinfracao = ($this->nl27_templateautoinfracao == ""?@$GLOBALS["HTTP_POST_VARS"]["nl27_templateautoinfracao"]:$this->nl27_templateautoinfracao);
			$this->nl27_autodvenc = ($this->nl27_autodvenc == ""?@$GLOBALS["HTTP_POST_VARS"]["nl27_autodvenc"]:$this->nl27_autodvenc);
			$this->nl27_autodprazo = ($this->nl27_autodprazo == ""?@$GLOBALS["HTTP_POST_VARS"]["nl27_autodprazo"]:$this->nl27_autodprazo);
		}else{
			$this->nl27_instit = ($this->nl27_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["nl27_instit"]:$this->nl27_instit);
		}
	}

  public function incluir($nl27_instit){

    $this->atualizacampos();
    if($this->nl27_tipo == "" OR $this->nl27_tipo == null) $this->nl27_tipo = 'null';
    if($this->nl27_historico == "" OR $this->nl27_historico == null) $this->nl27_historico = 'null';
    if($this->nl27_procbaixaauto == "" OR $this->nl27_procbaixaauto == null) $this->nl27_procbaixaauto = 'null';
    if($this->nl27_utilizadocpadrao == "" OR $this->nl27_utilizadocpadrao == null) $this->nl27_utilizadocpadrao = 'null';
    if($this->nl27_templateautoinfracao == "" OR $this->nl27_templateautoinfracao == null) $this->nl27_templateautoinfracao = 'null';
    if($this->nl27_autodvenc == "" OR $this->nl27_autodvenc == null) $this->nl27_autodvenc = 'null';
    if($this->nl27_autodprazo == "" OR $this->nl27_autodprazo == null) $this->nl27_autodprazo = 'null';

    $sql = " insert into fiscalizacao.fis_parnotificacaolancamento(nl27_instit,
                                                          nl27_tipo,
                                                          nl27_historico,
                                                          nl27_procbaixaauto,
                                                          nl27_utilizadocpadrao,
                                                          nl27_templateautoinfracao,
                                                          nl27_autodvenc,
                                                          nl27_autodprazo
                                                         ) values($nl27_instit,
                                                                  $this->nl27_tipo,
                                                                  $this->nl27_historico,
                                                                  '$this->nl27_procbaixaauto',
                                                                  '$this->nl27_utilizadocpadrao',
                                                                  $this->nl27_templateautoinfracao,
                                                                  $this->nl27_autodvenc,
                                                                  $this->nl27_autodprazo) ";
    //die($sql);
    $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Parametros do modulo fiscal para Notificação de Lançamento da Instituição ($this->nl27_instit) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Parametros do modulo fiscal já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Parametros do modulo fiscal para Notificação de Lançamento da Instituição ($this->nl27_instit) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
  }

	public function alterar($nl27_instit){

		$this->atualizacampos();
    if($this->nl27_tipo == "" OR $this->nl27_tipo == null) $this->nl27_tipo = 'null';
    if($this->nl27_historico == "" OR $this->nl27_historico == null) $this->nl27_historico = 'null';
    if($this->nl27_procbaixaauto == "" OR $this->nl27_procbaixaauto == null) $this->nl27_procbaixaauto = 'null';
    if($this->nl27_utilizadocpadrao == "" OR $this->nl27_utilizadocpadrao == null) $this->nl27_utilizadocpadrao = 'null';
    if($this->nl27_templateautoinfracao == "" OR $this->nl27_templateautoinfracao == null) $this->nl27_templateautoinfracao = 'null';
    if($this->nl27_autodvenc == "" OR $this->nl27_autodvenc == null) $this->nl27_autodvenc = 'null';
    if($this->nl27_autodprazo == "" OR $this->nl27_autodprazo == null) $this->nl27_autodprazo = 'null';

     $sql = " update fiscalizacao.fis_parnotificacaolancamento set ";
     $virgula = "";

     if(trim($nl27_instit) != "" || isset($GLOBALS["HTTP_POST_VARS"]["nl27_instit"])){
       if(trim($nl27_instit) == null ){
         $this->erro_sql = " Campo Código da Instituição não informado.";
         $this->erro_campo = "nl27_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->nl27_tipo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["nl27_tipo"])){
       $sql  .= $virgula." nl27_tipo = $this->nl27_tipo ";
       $virgula = ",";
       if(trim($this->nl27_tipo) == null ){
         $this->erro_sql = " Campo Tipo de Débito não informado.";
         $this->erro_campo = "nl27_tipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->nl27_historico) != "" || isset($GLOBALS["HTTP_POST_VARS"]["nl27_historico"])){
       $sql  .= $virgula." nl27_historico = $this->nl27_historico ";
       $virgula = ",";
       if(trim($this->nl27_historico) == null ){
         $this->erro_sql = " Campo Histórico do Cálculo não informado.";
         $this->erro_campo = "nl27_historico";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->nl27_procbaixaauto) != "" || isset($GLOBALS["HTTP_POST_VARS"]["nl27_procbaixaauto"])){
       $sql  .= $virgula." nl27_procbaixaauto = '$this->nl27_procbaixaauto' ";
       $virgula = ",";
       if(trim($this->nl27_procbaixaauto) == null ){
         $this->erro_sql = " Campo Processo Baixa de Auto de Infração não informado.";
         $this->erro_campo = "nl27_procbaixaauto";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->nl27_utilizadocpadrao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["nl27_utilizadocpadrao"])){
       $sql  .= $virgula." nl27_utilizadocpadrao = '$this->nl27_utilizadocpadrao' ";
       $virgula = ",";
       if(trim($this->nl27_utilizadocpadrao) == null ){
         $this->erro_sql = " Campo Utiliza Documento Padrão não informado.";
         $this->erro_campo = "nl27_utilizadocpadrao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if($this->nl27_utilizadocpadrao == 'f'){
         if(trim($this->nl27_templateautoinfracao) != "" || isset($GLOBALS["HTTP_POST_VARS"]["nl27_templateautoinfracao"])){
           $sql  .= $virgula." nl27_templateautoinfracao = $this->nl27_templateautoinfracao ";
           $virgula = ",";
           if(trim($this->nl27_templateautoinfracao) == 'null' ||  trim($this->nl27_templateautoinfracao) == null){
             $this->erro_sql = " Campo Documento Template não informado.";
             $this->erro_campo = "nl27_templateautoinfracao";
             $this->erro_banco = "";
             $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
             $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
             $this->erro_status = "0";
             return false;
           }
         }
      }

     if(trim($this->nl27_autodvenc) != "" || isset($GLOBALS["HTTP_POST_VARS"]["nl27_autodvenc"])){
       $sql  .= $virgula." nl27_autodvenc = $this->nl27_autodvenc ";
       $virgula = ",";
       if(trim($this->nl27_autodvenc) == null ){
         $this->erro_sql = " Campo Quantidade de dias para vencimento não informado.";
         $this->erro_campo = "nl27_autodvenc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->nl27_autodprazo) != "" || isset($GLOBALS["HTTP_POST_VARS"]["nl27_autodprazo"])){
       $sql  .= $virgula." nl27_autodprazo = $this->nl27_autodprazo ";
       $virgula = ",";
       if(trim($this->nl27_autodprazo) == null ){
         $this->erro_sql = " Campo Quantidade de dias para recurso não informado.";
         $this->erro_campo = "nl27_autodprazo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     $sql .= " where ";
     if($nl27_instit!=null){
       $sql .= " nl27_instit = $nl27_instit";
     }

     // die($sql);

     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Parametros do modulo Notificação de Lançamento não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl27_instit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Parametros do modulo Notificação de lançamento não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->nl27_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->nl27_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
	}



   public function sql_query ( $nl27_instit=null,$campos="*",$ordem=null,$dbwhere=""){

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
     $sql .= "  from fiscalizacao.fis_parnotificacaolancamento ";
     $sql .= " left join arretipo on fis_parnotificacaolancamento.nl27_tipo = k00_tipo ";
     $sql .= " left join histcalc on fis_parnotificacaolancamento.nl27_historico = k01_codigo ";
     $sql .= " left join db_documentotemplate ON db82_sequencial = nl27_templateautoinfracao ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($nl27_instit)) {
         $sql2 .= " where nl27_instit = $nl27_instit ";
       }
     } else if (!empty($dbwhere)) {
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

   // funcao do recordset
   public function sql_record($sql) {
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
        $this->erro_sql   = "Record Vazio na Tabela:parnotificacaolancamento";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
}
?>
