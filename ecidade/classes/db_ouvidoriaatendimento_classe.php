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

//MODULO: ouvidoria
//CLASSE DA ENTIDADE ouvidoriaatendimento
class cl_ouvidoriaatendimento {
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
   public $ov01_sequencial = 0;
   public $ov01_situacaoouvidoriaatendimento = 0;
   public $ov01_tipoprocesso = 0;
   public $ov01_formareclamacao = 0;
   public $ov01_tipoidentificacao = 0;
   public $ov01_usuario = 0;
   public $ov01_depart = 0;
   public $ov01_instit = 0;
   public $ov01_numero = 0;
   public $ov01_anousu = 0;
   public $ov01_dataatend_dia = null;
   public $ov01_dataatend_mes = null;
   public $ov01_dataatend_ano = null;
   public $ov01_dataatend = null;
   public $ov01_horaatend = null;
   public $ov01_requerente = null;
   public $ov01_solicitacao = null;
   public $ov01_executado = null;
   public $ov01_status = null;
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 ov01_sequencial = int4 = Sequencial
                 ov01_situacaoouvidoriaatendimento = int4 = Situa��o
                 ov01_tipoprocesso = int4 = Tipo de Processo
                 ov01_formareclamacao = int4 = Forma de Reclama��o
                 ov01_tipoidentificacao = int4 = Tipo de Identifica��o
                 ov01_usuario = int4 = Usu�rio
                 ov01_depart = int4 = Departamento
                 ov01_instit = int4 = Institui��o
                 ov01_numero = int4 = N�mero do Atendimento
                 ov01_anousu = int4 = Ano
                 ov01_dataatend = date = Data Atendimento
                 ov01_horaatend = char(5) = Hora Atendimento
                 ov01_requerente = varchar(100) = Requerente
                 ov01_solicitacao = text = Solicita��o
                 ov01_executado = text = Executado
                 ov01_status = int8 = C�digo do Status
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("ouvidoriaatendimento");
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
     if(!$exclusao){
       $this->ov01_sequencial = ($this->ov01_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_sequencial"]:$this->ov01_sequencial);
       $this->ov01_situacaoouvidoriaatendimento = ($this->ov01_situacaoouvidoriaatendimento == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_situacaoouvidoriaatendimento"]:$this->ov01_situacaoouvidoriaatendimento);
       $this->ov01_tipoprocesso = ($this->ov01_tipoprocesso == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_tipoprocesso"]:$this->ov01_tipoprocesso);
       $this->ov01_formareclamacao = ($this->ov01_formareclamacao == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_formareclamacao"]:$this->ov01_formareclamacao);
       $this->ov01_tipoidentificacao = ($this->ov01_tipoidentificacao == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_tipoidentificacao"]:$this->ov01_tipoidentificacao);
       $this->ov01_usuario = ($this->ov01_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_usuario"]:$this->ov01_usuario);
       $this->ov01_depart = ($this->ov01_depart == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_depart"]:$this->ov01_depart);
       $this->ov01_instit = ($this->ov01_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_instit"]:$this->ov01_instit);
       $this->ov01_numero = ($this->ov01_numero == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_numero"]:$this->ov01_numero);
       $this->ov01_anousu = ($this->ov01_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_anousu"]:$this->ov01_anousu);
       if($this->ov01_dataatend == ""){
         $this->ov01_dataatend_dia = ($this->ov01_dataatend_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_dataatend_dia"]:$this->ov01_dataatend_dia);
         $this->ov01_dataatend_mes = ($this->ov01_dataatend_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_dataatend_mes"]:$this->ov01_dataatend_mes);
         $this->ov01_dataatend_ano = ($this->ov01_dataatend_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_dataatend_ano"]:$this->ov01_dataatend_ano);
         if($this->ov01_dataatend_dia != ""){
            $this->ov01_dataatend = $this->ov01_dataatend_ano."-".$this->ov01_dataatend_mes."-".$this->ov01_dataatend_dia;
         }
       }
       $this->ov01_horaatend = ($this->ov01_horaatend == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_horaatend"]:$this->ov01_horaatend);
       $this->ov01_requerente = ($this->ov01_requerente == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_requerente"]:$this->ov01_requerente);
       $this->ov01_solicitacao = ($this->ov01_solicitacao == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_solicitacao"]:$this->ov01_solicitacao);
       $this->ov01_executado = ($this->ov01_executado == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_executado"]:$this->ov01_executado);
     }else{
       $this->ov01_sequencial = ($this->ov01_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["ov01_sequencial"]:$this->ov01_sequencial);
     }
   }
   // funcao para inclusao
   public function incluir ($ov01_sequencial){
      $this->atualizacampos();
     if($this->ov01_situacaoouvidoriaatendimento == null ){
       $this->erro_sql = " Campo Situa��o nao Informado.";
       $this->erro_campo = "ov01_situacaoouvidoriaatendimento";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_tipoprocesso == null ){
       $this->erro_sql = " Campo Tipo de Processo nao Informado.";
       $this->erro_campo = "ov01_tipoprocesso";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_formareclamacao == null ){
       $this->erro_sql = " Campo Forma de Reclama��o nao Informado.";
       $this->erro_campo = "ov01_formareclamacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_tipoidentificacao == null ){
       $this->erro_sql = " Campo Tipo de Identifica��o nao Informado.";
       $this->erro_campo = "ov01_tipoidentificacao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_usuario == null ){
       $this->erro_sql = " Campo Usu�rio nao Informado.";
       $this->erro_campo = "ov01_usuario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_depart == null ){
       $this->erro_sql = " Campo Departamento nao Informado.";
       $this->erro_campo = "ov01_depart";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_instit == null ){
       $this->erro_sql = " Campo Institui��o nao Informado.";
       $this->erro_campo = "ov01_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_numero == null ){
       $this->erro_sql = " Campo N�mero do Atendimento nao Informado.";
       $this->erro_campo = "ov01_numero";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_anousu == null ){
       $this->erro_sql = " Campo Ano nao Informado.";
       $this->erro_campo = "ov01_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_dataatend == null ){
       $this->erro_sql = " Campo Data Atendimento nao Informado.";
       $this->erro_campo = "ov01_dataatend_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_horaatend == null ){
       $this->erro_sql = " Campo Hora Atendimento nao Informado.";
       $this->erro_campo = "ov01_horaatend";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->ov01_requerente == null ){
       $this->erro_sql = " Campo Requerente nao Informado.";
       $this->erro_campo = "ov01_requerente";
       $this->erro_banco = "";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($ov01_sequencial == "" || $ov01_sequencial == null ){
       $result = db_query("select nextval('ouvidoriaatendimento_ov01_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: ouvidoriaatendimento_ov01_sequencial_seq do campo: ov01_sequencial";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->ov01_sequencial = pg_fetch_result($result,0,0);
     }else{
       $result = db_query("select last_value from ouvidoriaatendimento_ov01_sequencial_seq");
       if(($result != false) && (pg_fetch_result($result,0,0) < $ov01_sequencial)){
         $this->erro_sql = " Campo ov01_sequencial maior que �ltimo n�mero da sequencia.";
         $this->erro_banco = "Sequencia menor que este n�mero.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->ov01_sequencial = $ov01_sequencial;
       }
     }
     if(($this->ov01_sequencial == null) || ($this->ov01_sequencial == "") ){
       $this->erro_sql = " Campo ov01_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into ouvidoriaatendimento(
                                       ov01_sequencial
                                      ,ov01_situacaoouvidoriaatendimento
                                      ,ov01_tipoprocesso
                                      ,ov01_formareclamacao
                                      ,ov01_tipoidentificacao
                                      ,ov01_usuario
                                      ,ov01_depart
                                      ,ov01_instit
                                      ,ov01_numero
                                      ,ov01_anousu
                                      ,ov01_dataatend
                                      ,ov01_horaatend
                                      ,ov01_requerente
                                      ,ov01_solicitacao
                                      ,ov01_executado
                       )
                values (
                                $this->ov01_sequencial
                               ,$this->ov01_situacaoouvidoriaatendimento
                               ,$this->ov01_tipoprocesso
                               ,$this->ov01_formareclamacao
                               ,$this->ov01_tipoidentificacao
                               ,$this->ov01_usuario
                               ,$this->ov01_depart
                               ,$this->ov01_instit
                               ,$this->ov01_numero
                               ,$this->ov01_anousu
                               ,".($this->ov01_dataatend == "null" || $this->ov01_dataatend == ""?"null":"'".$this->ov01_dataatend."'")."
                               ,'$this->ov01_horaatend'
                               ,'$this->ov01_requerente'
                               ,'$this->ov01_solicitacao'
                               ,'$this->ov01_executado'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Cadastro de Atendimentos da Ouvidoria ($this->ov01_sequencial) nao Inclu�do. Inclusao Abortada.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Cadastro de Atendimentos da Ouvidoria j� Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Cadastro de Atendimentos da Ouvidoria ($this->ov01_sequencial) nao Inclu�do. Inclusao Abortada.";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->ov01_sequencial;
     $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->ov01_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_fetch_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,14769,'$this->ov01_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,2600,14769,'','".addslashes(pg_fetch_result($resaco,0,'ov01_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14770,'','".addslashes(pg_fetch_result($resaco,0,'ov01_situacaoouvidoriaatendimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14771,'','".addslashes(pg_fetch_result($resaco,0,'ov01_tipoprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14772,'','".addslashes(pg_fetch_result($resaco,0,'ov01_formareclamacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14773,'','".addslashes(pg_fetch_result($resaco,0,'ov01_tipoidentificacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14782,'','".addslashes(pg_fetch_result($resaco,0,'ov01_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14781,'','".addslashes(pg_fetch_result($resaco,0,'ov01_depart'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14783,'','".addslashes(pg_fetch_result($resaco,0,'ov01_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14774,'','".addslashes(pg_fetch_result($resaco,0,'ov01_numero'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14775,'','".addslashes(pg_fetch_result($resaco,0,'ov01_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14776,'','".addslashes(pg_fetch_result($resaco,0,'ov01_dataatend'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14777,'','".addslashes(pg_fetch_result($resaco,0,'ov01_horaatend'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14778,'','".addslashes(pg_fetch_result($resaco,0,'ov01_requerente'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14779,'','".addslashes(pg_fetch_result($resaco,0,'ov01_solicitacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2600,14780,'','".addslashes(pg_fetch_result($resaco,0,'ov01_executado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($ov01_sequencial=null) {
      $this->atualizacampos();
     $sql = " update ouvidoriaatendimento set ";
     $virgula = "";
     if(trim($this->ov01_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_sequencial"])){
       $sql  .= $virgula." ov01_sequencial = $this->ov01_sequencial ";
       $virgula = ",";
       if(trim($this->ov01_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial nao Informado.";
         $this->erro_campo = "ov01_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_situacaoouvidoriaatendimento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_situacaoouvidoriaatendimento"])){
       $sql  .= $virgula." ov01_situacaoouvidoriaatendimento = $this->ov01_situacaoouvidoriaatendimento ";
       $virgula = ",";
       if(trim($this->ov01_situacaoouvidoriaatendimento) == null ){
         $this->erro_sql = " Campo Situa��o nao Informado.";
         $this->erro_campo = "ov01_situacaoouvidoriaatendimento";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_tipoprocesso)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_tipoprocesso"])){
       $sql  .= $virgula." ov01_tipoprocesso = $this->ov01_tipoprocesso ";
       $virgula = ",";
       if(trim($this->ov01_tipoprocesso) == null ){
         $this->erro_sql = " Campo Tipo de Processo nao Informado.";
         $this->erro_campo = "ov01_tipoprocesso";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_formareclamacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_formareclamacao"])){
       $sql  .= $virgula." ov01_formareclamacao = $this->ov01_formareclamacao ";
       $virgula = ",";
       if(trim($this->ov01_formareclamacao) == null ){
         $this->erro_sql = " Campo Forma de Reclama��o nao Informado.";
         $this->erro_campo = "ov01_formareclamacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_tipoidentificacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_tipoidentificacao"])){
       $sql  .= $virgula." ov01_tipoidentificacao = $this->ov01_tipoidentificacao ";
       $virgula = ",";
       if(trim($this->ov01_tipoidentificacao) == null ){
         $this->erro_sql = " Campo Tipo de Identifica��o nao Informado.";
         $this->erro_campo = "ov01_tipoidentificacao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_usuario"])){
       $sql  .= $virgula." ov01_usuario = $this->ov01_usuario ";
       $virgula = ",";
       if(trim($this->ov01_usuario) == null ){
         $this->erro_sql = " Campo Usu�rio nao Informado.";
         $this->erro_campo = "ov01_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_depart)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_depart"])){
       $sql  .= $virgula." ov01_depart = $this->ov01_depart ";
       $virgula = ",";
       if(trim($this->ov01_depart) == null ){
         $this->erro_sql = " Campo Departamento nao Informado.";
         $this->erro_campo = "ov01_depart";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_instit"])){
       $sql  .= $virgula." ov01_instit = $this->ov01_instit ";
       $virgula = ",";
       if(trim($this->ov01_instit) == null ){
         $this->erro_sql = " Campo Institui��o nao Informado.";
         $this->erro_campo = "ov01_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_numero"])){
       $sql  .= $virgula." ov01_numero = $this->ov01_numero ";
       $virgula = ",";
       if(trim($this->ov01_numero) == null ){
         $this->erro_sql = " Campo N�mero do Atendimento nao Informado.";
         $this->erro_campo = "ov01_numero";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_anousu"])){
       $sql  .= $virgula." ov01_anousu = $this->ov01_anousu ";
       $virgula = ",";
       if(trim($this->ov01_anousu) == null ){
         $this->erro_sql = " Campo Ano nao Informado.";
         $this->erro_campo = "ov01_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_dataatend)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_dataatend_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["ov01_dataatend_dia"] !="") ){
       $sql  .= $virgula." ov01_dataatend = '$this->ov01_dataatend' ";
       $virgula = ",";
       if(trim($this->ov01_dataatend) == null ){
         $this->erro_sql = " Campo Data Atendimento nao Informado.";
         $this->erro_campo = "ov01_dataatend_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_dataatend_dia"])){
         $sql  .= $virgula." ov01_dataatend = null ";
         $virgula = ",";
         if(trim($this->ov01_dataatend) == null ){
           $this->erro_sql = " Campo Data Atendimento nao Informado.";
           $this->erro_campo = "ov01_dataatend_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->ov01_horaatend)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_horaatend"])){
       $sql  .= $virgula." ov01_horaatend = '$this->ov01_horaatend' ";
       $virgula = ",";
       if(trim($this->ov01_horaatend) == null ){
         $this->erro_sql = " Campo Hora Atendimento nao Informado.";
         $this->erro_campo = "ov01_horaatend";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_requerente)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_requerente"])){
       $sql  .= $virgula." ov01_requerente = '$this->ov01_requerente' ";
       $virgula = ",";
       if(trim($this->ov01_requerente) == null ){
         $this->erro_sql = " Campo Requerente nao Informado.";
         $this->erro_campo = "ov01_requerente";
         $this->erro_banco = "";
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->ov01_solicitacao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_solicitacao"])){
       $sql  .= $virgula." ov01_solicitacao = '$this->ov01_solicitacao' ";
       $virgula = ",";
     }
     if(trim($this->ov01_executado)!="" || isset($GLOBALS["HTTP_POST_VARS"]["ov01_executado"])){
       $sql  .= $virgula." ov01_executado = '$this->ov01_executado' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($ov01_sequencial!=null){
       $sql .= " ov01_sequencial = $this->ov01_sequencial";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->ov01_sequencial));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_fetch_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14769,'$this->ov01_sequencial','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_sequencial"]) || $this->ov01_sequencial != "")
           $resac = db_query("insert into db_acount values($acount,2600,14769,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_sequencial'))."','$this->ov01_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_situacaoouvidoriaatendimento"]) || $this->ov01_situacaoouvidoriaatendimento != "")
           $resac = db_query("insert into db_acount values($acount,2600,14770,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_situacaoouvidoriaatendimento'))."','$this->ov01_situacaoouvidoriaatendimento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_tipoprocesso"]) || $this->ov01_tipoprocesso != "")
           $resac = db_query("insert into db_acount values($acount,2600,14771,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_tipoprocesso'))."','$this->ov01_tipoprocesso',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_formareclamacao"]) || $this->ov01_formareclamacao != "")
           $resac = db_query("insert into db_acount values($acount,2600,14772,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_formareclamacao'))."','$this->ov01_formareclamacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_tipoidentificacao"]) || $this->ov01_tipoidentificacao != "")
           $resac = db_query("insert into db_acount values($acount,2600,14773,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_tipoidentificacao'))."','$this->ov01_tipoidentificacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_usuario"]) || $this->ov01_usuario != "")
           $resac = db_query("insert into db_acount values($acount,2600,14782,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_usuario'))."','$this->ov01_usuario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_depart"]) || $this->ov01_depart != "")
           $resac = db_query("insert into db_acount values($acount,2600,14781,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_depart'))."','$this->ov01_depart',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_instit"]) || $this->ov01_instit != "")
           $resac = db_query("insert into db_acount values($acount,2600,14783,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_instit'))."','$this->ov01_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_numero"]) || $this->ov01_numero != "")
           $resac = db_query("insert into db_acount values($acount,2600,14774,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_numero'))."','$this->ov01_numero',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_anousu"]) || $this->ov01_anousu != "")
           $resac = db_query("insert into db_acount values($acount,2600,14775,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_anousu'))."','$this->ov01_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_dataatend"]) || $this->ov01_dataatend != "")
           $resac = db_query("insert into db_acount values($acount,2600,14776,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_dataatend'))."','$this->ov01_dataatend',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_horaatend"]) || $this->ov01_horaatend != "")
           $resac = db_query("insert into db_acount values($acount,2600,14777,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_horaatend'))."','$this->ov01_horaatend',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_requerente"]) || $this->ov01_requerente != "")
           $resac = db_query("insert into db_acount values($acount,2600,14778,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_requerente'))."','$this->ov01_requerente',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_solicitacao"]) || $this->ov01_solicitacao != "")
           $resac = db_query("insert into db_acount values($acount,2600,14779,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_solicitacao'))."','$this->ov01_solicitacao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["ov01_executado"]) || $this->ov01_executado != "")
           $resac = db_query("insert into db_acount values($acount,2600,14780,'".addslashes(pg_fetch_result($resaco,$conresaco,'ov01_executado'))."','$this->ov01_executado',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro de Atendimentos da Ouvidoria nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->ov01_sequencial;
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro de Atendimentos da Ouvidoria nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->ov01_sequencial;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Altera��o efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->ov01_sequencial;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($ov01_sequencial=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($ov01_sequencial));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_fetch_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14769,'$ov01_sequencial','E')");
         $resac = db_query("insert into db_acount values($acount,2600,14769,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14770,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_situacaoouvidoriaatendimento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14771,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_tipoprocesso'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14772,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_formareclamacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14773,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_tipoidentificacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14782,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14781,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_depart'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14783,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14774,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_numero'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14775,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14776,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_dataatend'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14777,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_horaatend'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14778,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_requerente'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14779,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_solicitacao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2600,14780,'','".addslashes(pg_fetch_result($resaco,$iresaco,'ov01_executado'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from ouvidoriaatendimento
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($ov01_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " ov01_sequencial = $ov01_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastro de Atendimentos da Ouvidoria nao Exclu�do. Exclus�o Abortada.\\n";
       $this->erro_sql .= "Valores : ".$ov01_sequencial;
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastro de Atendimentos da Ouvidoria nao Encontrado. Exclus�o n�o Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$ov01_sequencial;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclus�o efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$ov01_sequencial;
         $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
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
       $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_num_rows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:ouvidoriaatendimento";
        $this->erro_msg   = "Usu�rio: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $ov01_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from ouvidoriaatendimento ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = ouvidoriaatendimento.ov01_usuario";
     $sql .= "      inner join db_depart  on  db_depart.coddepto = ouvidoriaatendimento.ov01_depart";
     $sql .= "      inner join tipoproc  on  tipoproc.p51_codigo = ouvidoriaatendimento.ov01_tipoprocesso";
     $sql .= "      inner join tipoidentificacao  on  tipoidentificacao.ov05_sequencial = ouvidoriaatendimento.ov01_tipoidentificacao";
     $sql .= "      inner join formareclamacao  on  formareclamacao.p42_sequencial = ouvidoriaatendimento.ov01_formareclamacao";
     $sql .= "      inner join situacaoouvidoriaatendimento  on  situacaoouvidoriaatendimento.ov18_sequencial = ouvidoriaatendimento.ov01_situacaoouvidoriaatendimento";
     $sql .= "      inner join db_config  on  db_config.codigo = db_depart.instit";
     $sql .= "      inner join db_config  as a on   a.codigo = tipoproc.p51_instit";
     $sql .= "      inner join tipoprocgrupo  on  tipoprocgrupo.p40_sequencial = tipoproc.p51_tipoprocgrupo";
     $sql2 = "";
     if($dbwhere==""){
       if($ov01_sequencial!=null ){
         $sql2 .= " where ouvidoriaatendimento.ov01_sequencial = $ov01_sequencial ";
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
   public function sql_query_file ( $ov01_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from ouvidoriaatendimento ";
     $sql2 = "";
     if($dbwhere==""){
       if($ov01_sequencial!=null ){
         $sql2 .= " where ouvidoriaatendimento.ov01_sequencial = $ov01_sequencial ";
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
   public function sql_query_dados_atendimento($ov01_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

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
    $sql .= " from ouvidoriaatendimento ";
    $sql .= "      inner join tipoproc                      on tipoproc.p51_codigo                                   = ouvidoriaatendimento.ov01_tipoprocesso";
    $sql .= "      inner join tipoidentificacao             on tipoidentificacao.ov05_sequencial                     = ouvidoriaatendimento.ov01_tipoidentificacao";
    $sql .= "      inner join formareclamacao               on formareclamacao.p42_sequencial                        = ouvidoriaatendimento.ov01_formareclamacao";
    $sql .= "      inner join tipoprocgrupo                 on tipoprocgrupo.p40_sequencial                          = tipoproc.p51_tipoprocgrupo";
    $sql .= "      inner join situacaoouvidoriaatendimento  on situacaoouvidoriaatendimento.ov18_sequencial          = ouvidoriaatendimento.ov01_situacaoouvidoriaatendimento";
    $sql .= "      inner join processoouvidoria             on processoouvidoria.ov09_ouvidoriaatendimento           = ouvidoriaatendimento.ov01_sequencial";
    $sql .= "      inner join protprocesso                  on protprocesso.p58_codproc                              = processoouvidoria.ov09_protprocesso ";
    $sql .= "      left  join processoouvidoriaprorrogacao  on processoouvidoriaprorrogacao.ov15_protprocesso        = protprocesso.p58_codproc";
    $sql .= "      left  join ouvidoriaatendimentoretorno   on ouvidoriaatendimentoretorno.ov20_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial";
    $sql .= "      left  join tiporetorno                   on tiporetorno.ov22_sequencial                           = ouvidoriaatendimentoretorno.ov20_tiporetorno";
    $sql .= "      left  join ouvidoriaatendimentolocal     on ouvidoriaatendimentolocal.ov24_ouvidoriaatendimento   = ouvidoriaatendimento.ov01_sequencial";
    $sql .= "      left  join ouvidoriacadlocal             on ouvidoriacadlocal.ov25_sequencial                     = ouvidoriaatendimentolocal.ov24_ouvidoriacadlocal";
    $sql .= "      left  join ouvidoriacadlocalender        on ouvidoriacadlocalender.ov26_ouvidoriacadlocal         = ouvidoriacadlocal.ov25_sequencial";
    $sql .= "      left  join ruas                          on ruas.j14_codigo                                       = ouvidoriacadlocalender.ov26_ruas";
    $sql .= "      left  join ruasbairro                    on ruasbairro.j16_lograd                                 = ruas.j14_codigo";
    $sql .= "      left  join bairro                        on bairro.j13_codi                                       = ruasbairro.j16_bairro";
    $sql .= "      left  join ouvidoriacadlocaldepart       on ouvidoriacadlocaldepart.ov27_ouvidoriacadlocal        = ouvidoriacadlocal.ov25_sequencial";
    $sql .= "      left  join ouvidoriacadlocalgeral        on ouvidoriacadlocalgeral.ov28_ouvidoriacadlocal         = ouvidoriacadlocal.ov25_sequencial";
    $sql .= "      left  join db_depart                     on db_depart.coddepto                                    = protprocesso.p58_coddepto";
    $sql .= "      left  join procandam                     on procandam.p61_codandam                                = protprocesso.p58_codandam";

    $sql2 = "";
    if($dbwhere==""){
      if($ov01_sequencial!=null ){
        $sql2 .= " where ouvidoriaatendimento.ov01_sequencial = $ov01_sequencial ";
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
   public function sql_query_proc( $ov01_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from ouvidoriaatendimento ";
     $sql .= "      inner join tipoproc                      on tipoproc.p51_codigo               = ouvidoriaatendimento.ov01_tipoprocesso";
     $sql .= "      inner join tipoidentificacao             on tipoidentificacao.ov05_sequencial = ouvidoriaatendimento.ov01_tipoidentificacao";
     $sql .= "      inner join formareclamacao               on formareclamacao.p42_sequencial    = ouvidoriaatendimento.ov01_formareclamacao";
     $sql .= "      inner join tipoprocgrupo                 on tipoprocgrupo.p40_sequencial      = tipoproc.p51_tipoprocgrupo";
     $sql .= "      inner join situacaoouvidoriaatendimento  on situacaoouvidoriaatendimento.ov18_sequencial = ouvidoriaatendimento.ov01_situacaoouvidoriaatendimento";
     $sql .= "      left  join processoouvidoria             on processoouvidoria.ov09_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial";
     $sql .= "      left  join protprocesso                  on protprocesso.p58_codproc          = processoouvidoria.ov09_protprocesso ";
     $sql .= "      left  join processoouvidoriaprorrogacao  on processoouvidoriaprorrogacao.ov15_protprocesso = protprocesso.p58_codproc";
     $sql .= "      left  join ouvidoriaatendimentocidadao   on ouvidoriaatendimento.ov01_sequencial = ouvidoriaatendimentocidadao.ov10_ouvidoriaatendimento";
     $sql .= "      left  join  ouvidoriaatendimentocgm      on ouvidoriaatendimentocgm.ov11_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial ";
     $sql2 = "";
     if($dbwhere==""){
       if($ov01_sequencial!=null ){
         $sql2 .= " where ouvidoriaatendimento.ov01_sequencial = $ov01_sequencial ";
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
   public function sql_query_proctitular( $ov01_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from ouvidoriaatendimento
                        inner join processoouvidoria           on ov09_ouvidoriaatendimento = ov01_sequencial
                        inner join protprocesso                on p58_codproc               = ov09_protprocesso
                        inner join cgm                         on z01_numcgm                = p58_numcgm
                        inner join tipoproc                    on p51_codigo                = p58_codigo
                        left  join arqproc                     on p68_codproc               = p58_codproc
                        left  join ouvidoriaatendimentocgm     on ov11_ouvidoriaatendimento = ov01_sequencial
                        left  join ouvidoriaatendimentocidadao on ov10_ouvidoriaatendimento = ov01_sequencial ";


     $sql2 = "";
     if($dbwhere==""){
       if($ov01_sequencial!=null ){
         $sql2 .= " where ouvidoriaatendimento.ov01_sequencial = $ov01_sequencial ";
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
   public function sql_query_retorno( $ov01_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from ouvidoriaatendimento ";
     $sql .= "      inner join tipoproc                      on tipoproc.p51_codigo               = ouvidoriaatendimento.ov01_tipoprocesso";
     $sql .= "      inner join tipoidentificacao             on tipoidentificacao.ov05_sequencial = ouvidoriaatendimento.ov01_tipoidentificacao";
     $sql .= "      inner join formareclamacao               on formareclamacao.p42_sequencial    = ouvidoriaatendimento.ov01_formareclamacao";
     $sql .= "      inner join tipoprocgrupo                 on tipoprocgrupo.p40_sequencial      = tipoproc.p51_tipoprocgrupo";
     $sql .= "      inner join situacaoouvidoriaatendimento  on situacaoouvidoriaatendimento.ov18_sequencial = ouvidoriaatendimento.ov01_situacaoouvidoriaatendimento";
     $sql .= "      left  join processoouvidoria             on processoouvidoria.ov09_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial";
     $sql .= "      left  join protprocesso                  on protprocesso.p58_codproc          = processoouvidoria.ov09_protprocesso ";
     $sql .= "      left  join processoouvidoriaprorrogacao  on processoouvidoriaprorrogacao.ov15_protprocesso = protprocesso.p58_codproc";
     $sql .= "      left  join ouvidoriaatendimentoretorno   on ouvidoriaatendimentoretorno.ov20_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial";
     $sql .= "      left  join tiporetorno                   on tiporetorno.ov22_sequencial = ouvidoriaatendimentoretorno.ov20_tiporetorno";


     $sql2 = "";
     if($dbwhere==""){
       if($ov01_sequencial!=null ){
         $sql2 .= " where ouvidoriaatendimento.ov01_sequencial = $ov01_sequencial ";
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
   public function sql_query_titular( $ov01_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from ouvidoriaatendimento ";
     $sql .= "      inner join db_usuarios                  on db_usuarios.id_usuario = ouvidoriaatendimento.ov01_usuario";
     $sql .= "      inner join db_depart                    on db_depart.coddepto = ouvidoriaatendimento.ov01_depart";
     $sql .= "      inner join tipoproc                     on tipoproc.p51_codigo = ouvidoriaatendimento.ov01_tipoprocesso";
     $sql .= "      inner join tipoidentificacao            on tipoidentificacao.ov05_sequencial = ouvidoriaatendimento.ov01_tipoidentificacao";
     $sql .= "      inner join formareclamacao              on formareclamacao.p42_sequencial = ouvidoriaatendimento.ov01_formareclamacao";
     $sql .= "      inner join situacaoouvidoriaatendimento on situacaoouvidoriaatendimento.ov18_sequencial = ouvidoriaatendimento.ov01_situacaoouvidoriaatendimento";
     $sql .= "      inner join tipoprocgrupo                on tipoprocgrupo.p40_sequencial = tipoproc.p51_tipoprocgrupo";
     $sql .= "      left  join ouvidoriaatendimentolocal    on ouvidoriaatendimentolocal.ov24_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial";
     $sql .= "      left  join ouvidoriacadlocal            on ouvidoriacadlocal. ov25_sequencial = ouvidoriaatendimentolocal.ov24_ouvidoriacadlocal";
     $sql .= "      left  join ouvidoriaatendimentocidadao  on ouvidoriaatendimentocidadao.ov10_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial";
     $sql .= "      left  join cidadao                      on cidadao.ov02_sequencial =  ouvidoriaatendimentocidadao.ov10_cidadao";
     $sql .= "                                             and cidadao.ov02_seq        =  ouvidoriaatendimentocidadao.ov10_seq";
     $sql .= "      left  join ouvidoriaatendimentocgm      on ouvidoriaatendimentocgm.ov11_ouvidoriaatendimento     = ouvidoriaatendimento.ov01_sequencial";
     $sql .= "      left  join cgm                          on cgm.z01_numcgm = ouvidoriaatendimentocgm.ov11_cgm";
     $sql .= "      left  join processoouvidoria            on processoouvidoria.ov09_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial";

     $sql2 = "";
     if($dbwhere==""){
       if($ov01_sequencial!=null ){
         $sql2 .= " where ouvidoriaatendimento.ov01_sequencial = $ov01_sequencial ";
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
  public function sql_query_consultaatendimentos( $ov01_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " FROM ouvidoriaatendimento                                                                                         ";
     $sql .= " INNER JOIN tipoproc ON tipoproc.p51_codigo = ouvidoriaatendimento.ov01_tipoprocesso                               ";
     $sql .= " LEFT JOIN processoouvidoria ON processoouvidoria.ov09_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial ";
     $sql .= " LEFT JOIN protprocesso ON protprocesso.p58_codproc = processoouvidoria.ov09_protprocesso                          ";
     $sql .= " LEFT JOIN db_depart ON db_depart.coddepto = ouvidoriaatendimento.ov01_depart                                      ";

     $sql2 = "";
     if($dbwhere==""){
       if($ov01_sequencial!=null ){
         $sql2 .= " where ouvidoriaatendimento.ov01_sequencial = $ov01_sequencial ";
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

  public function sql_query_atendimento_processo($ov01_sequencial=null,$campos="*",$ordem=null,$dbwhere="") {

     $sql = "select ";
     if ($campos != "*" ) {
       $campos_sql = explode("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     } else {
       $sql .= $campos;
     }
     $sql .= " from ouvidoriaatendimento ";
     $sql .= "      inner join situacaoouvidoriaatendimento on situacaoouvidoriaatendimento.ov18_sequencial        = ouvidoriaatendimento.ov01_situacaoouvidoriaatendimento";
     $sql .= "      inner join tipoproc                     on tipoproc.p51_codigo                                 = ouvidoriaatendimento.ov01_tipoprocesso";
     $sql .= "      inner join db_usuarios                  on db_usuarios.id_usuario                              = ouvidoriaatendimento.ov01_usuario";
     $sql .= "      inner join db_depart                    on db_depart.coddepto                   			      = ouvidoriaatendimento.ov01_depart";
     $sql .= "      left  join ouvidoriaatendimentolocal    on ouvidoriaatendimentolocal.ov24_ouvidoriaatendimento = ouvidoriaatendimento.ov01_sequencial";
     $sql .= "      left  join ouvidoriacadlocal            on ouvidoriacadlocal.ov25_sequencial                   = ouvidoriaatendimentolocal.ov24_ouvidoriacadlocal";
     $sql .= "      left  join processoouvidoria            on processoouvidoria.ov09_ouvidoriaatendimento         = ouvidoriaatendimento.ov01_sequencial";
     $sql .= "      left  join protprocesso                 on protprocesso.p58_codproc                            = processoouvidoria.ov09_protprocesso";

     $sql2 = "";
     if($dbwhere==""){
       if($ov01_sequencial!=null ){
         $sql2 .= " where ouvidoriaatendimento.ov01_sequencial = $ov01_sequencial ";
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

  public function buscarSolicitacaoOuvidoria($sWhere)
  {
      if (empty($sWhere)) throw new Exception("Informe algum filtro");

      $sSQL = "
            SELECT ov01_sequencial as sequencial,
                   ov01_tipoprocesso as tipo_processo,
                   ov02_sequencial,
                   ov01_numero||'/'||ov01_anousu as processo,
                   ov33_informacoesprocesso as metadados,
                   p43_formareclamacao as formareclamacao,
                   ov01_numero as numero_processo,
                   ov01_anousu as ano_processo,
                   ov01_depart as departamento,
                   ov33_client_atendimento_id
              FROM ouvidoria.ouvidoriaatendimento
             INNER JOIN ouvidoriaatendimentoprocessoeletronico
                ON ov33_ouvidoriaatendimento = ov01_sequencial
             INNER JOIN tipoproc
                ON p51_codigo = ov01_tipoprocesso
              LEFT JOIN ouvidoriaatendimentocidadao
                ON ov10_ouvidoriaatendimento = ov01_sequencial
              LEFT JOIN tipoprocformareclamacao
                ON p43_tipoproc = ov01_tipoprocesso
              LEFT JOIN cidadao
                ON ov02_sequencial = ov10_cidadao AND ov02_seq = ov10_seq
              LEFT JOIN processoouvidoria
                ON ov09_ouvidoriaatendimento = ov01_sequencial
             WHERE {$sWhere}
                ORDER BY sequencial";

      $rResult = db_query($sSQL);

      if ($rResult) {
          return \db_utils::fieldsMemory($rResult, 0);
      }

      return null;
  }
}
?>
