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

//MODULO: Ambulatorial
//CLASSE DA ENTIDADE sau_agendaexterna
class cl_sau_agendaexterna {
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
   var $s118_i_codigo = 0;
   var $s118_i_numcgs = 0;
   var $s118_c_tipoagenda = null;
   var $s118_i_prestador = 0;
   var $s118_d_preferencia_dia = null;
   var $s118_d_preferencia_mes = null;
   var $s118_d_preferencia_ano = null;
   var $s118_d_preferencia = null;
   var $s118_d_marcada_dia = null;
   var $s118_d_marcada_mes = null;
   var $s118_d_marcada_ano = null;
   var $s118_d_marcada = null;
   var $s118_c_horamarcada = null;
   var $s118_v_encaminhamento = null;
   var $s118_v_protocolo = null;
   var $s118_d_data_dia = null;
   var $s118_d_data_mes = null;
   var $s118_d_data_ano = null;
   var $s118_d_data = null;
   var $s118_i_login = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 s118_i_codigo = int4 = Código
                 s118_i_numcgs = int4 = CGS
                 s118_c_tipoagenda = char(1) = Tipo de Agendamento
                 s118_i_prestador = int4 = Prestador
                 s118_d_preferencia = date = Preferência
                 s118_d_marcada = date = Marcada
                 s118_c_horamarcada = char(5) = Hora Marcada
                 s118_v_encaminhamento = varchar(10) = Encaminhamento
                 s118_v_protocolo = varchar(10) = Protocolo
                 s118_d_data = date = Data
                 s118_i_login = int4 = Login
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("sau_agendaexterna");
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
       $this->s118_i_codigo = ($this->s118_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_i_codigo"]:$this->s118_i_codigo);
       $this->s118_i_numcgs = ($this->s118_i_numcgs == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_i_numcgs"]:$this->s118_i_numcgs);
       $this->s118_c_tipoagenda = ($this->s118_c_tipoagenda == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_c_tipoagenda"]:$this->s118_c_tipoagenda);
       $this->s118_i_prestador = ($this->s118_i_prestador == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_i_prestador"]:$this->s118_i_prestador);
       if($this->s118_d_preferencia == ""){
         $this->s118_d_preferencia_dia = ($this->s118_d_preferencia_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_d_preferencia_dia"]:$this->s118_d_preferencia_dia);
         $this->s118_d_preferencia_mes = ($this->s118_d_preferencia_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_d_preferencia_mes"]:$this->s118_d_preferencia_mes);
         $this->s118_d_preferencia_ano = ($this->s118_d_preferencia_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_d_preferencia_ano"]:$this->s118_d_preferencia_ano);
         if($this->s118_d_preferencia_dia != ""){
            $this->s118_d_preferencia = $this->s118_d_preferencia_ano."-".$this->s118_d_preferencia_mes."-".$this->s118_d_preferencia_dia;
         }
       }
       if($this->s118_d_marcada == ""){
         $this->s118_d_marcada_dia = ($this->s118_d_marcada_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_d_marcada_dia"]:$this->s118_d_marcada_dia);
         $this->s118_d_marcada_mes = ($this->s118_d_marcada_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_d_marcada_mes"]:$this->s118_d_marcada_mes);
         $this->s118_d_marcada_ano = ($this->s118_d_marcada_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_d_marcada_ano"]:$this->s118_d_marcada_ano);
         if($this->s118_d_marcada_dia != ""){
            $this->s118_d_marcada = $this->s118_d_marcada_ano."-".$this->s118_d_marcada_mes."-".$this->s118_d_marcada_dia;
         }
       }
       $this->s118_c_horamarcada = ($this->s118_c_horamarcada == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_c_horamarcada"]:$this->s118_c_horamarcada);
       $this->s118_v_encaminhamento = ($this->s118_v_encaminhamento == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_v_encaminhamento"]:$this->s118_v_encaminhamento);
       $this->s118_v_protocolo = ($this->s118_v_protocolo == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_v_protocolo"]:$this->s118_v_protocolo);
       if($this->s118_d_data == ""){
         $this->s118_d_data_dia = ($this->s118_d_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_d_data_dia"]:$this->s118_d_data_dia);
         $this->s118_d_data_mes = ($this->s118_d_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_d_data_mes"]:$this->s118_d_data_mes);
         $this->s118_d_data_ano = ($this->s118_d_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_d_data_ano"]:$this->s118_d_data_ano);
         if($this->s118_d_data_dia != ""){
            $this->s118_d_data = $this->s118_d_data_ano."-".$this->s118_d_data_mes."-".$this->s118_d_data_dia;
         }
       }
       $this->s118_i_login = ($this->s118_i_login == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_i_login"]:$this->s118_i_login);
     }else{
       $this->s118_i_codigo = ($this->s118_i_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["s118_i_codigo"]:$this->s118_i_codigo);
     }
   }
   // funcao para inclusao
   public function incluir ($s118_i_codigo){
      $this->atualizacampos();
     if($this->s118_i_numcgs == null ){
       $this->erro_sql = " Campo CGS nao Informado.";
       $this->erro_campo = "s118_i_numcgs";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->s118_c_tipoagenda == null ){
       $this->erro_sql = " Campo Tipo de Agendamento nao Informado.";
       $this->erro_campo = "s118_c_tipoagenda";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->s118_i_prestador == null ){
       $this->s118_i_prestador = "null";
     }
     if($this->s118_d_preferencia == null ){
       $this->erro_sql = " Campo Preferência nao Informado.";
       $this->erro_campo = "s118_d_preferencia_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->s118_d_marcada == null ){
       $this->s118_d_marcada = "null";
     }
     if($this->s118_d_data == null ){
       $this->erro_sql = " Campo Data nao Informado.";
       $this->erro_campo = "s118_d_data_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->s118_i_login == null ){
       $this->erro_sql = " Campo Login nao Informado.";
       $this->erro_campo = "s118_i_login";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($s118_i_codigo == "" || $s118_i_codigo == null ){
       $result = db_query("select nextval('sau_agendaexterna_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: sau_agendaexterna_codigo_seq do campo: s118_i_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->s118_i_codigo = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from sau_agendaexterna_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $s118_i_codigo)){
         $this->erro_sql = " Campo s118_i_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->s118_i_codigo = $s118_i_codigo;
       }
     }
     if(($this->s118_i_codigo == null) || ($this->s118_i_codigo == "") ){
       $this->erro_sql = " Campo s118_i_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into sau_agendaexterna(
                                       s118_i_codigo
                                      ,s118_i_numcgs
                                      ,s118_c_tipoagenda
                                      ,s118_i_prestador
                                      ,s118_d_preferencia
                                      ,s118_d_marcada
                                      ,s118_c_horamarcada
                                      ,s118_v_encaminhamento
                                      ,s118_v_protocolo
                                      ,s118_d_data
                                      ,s118_i_login
                       )
                values (
                                $this->s118_i_codigo
                               ,$this->s118_i_numcgs
                               ,'$this->s118_c_tipoagenda'
                               ,$this->s118_i_prestador
                               ,".($this->s118_d_preferencia == "null" || $this->s118_d_preferencia == ""?"null":"'".$this->s118_d_preferencia."'")."
                               ,".($this->s118_d_marcada == "null" || $this->s118_d_marcada == ""?"null":"'".$this->s118_d_marcada."'")."
                               ,'$this->s118_c_horamarcada'
                               ,'$this->s118_v_encaminhamento'
                               ,'$this->s118_v_protocolo'
                               ,".($this->s118_d_data == "null" || $this->s118_d_data == ""?"null":"'".$this->s118_d_data."'")."
                               ,$this->s118_i_login
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Agenda Externa ($this->s118_i_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Agenda Externa já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Agenda Externa ($this->s118_i_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->s118_i_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->s118_i_codigo));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,14287,'$this->s118_i_codigo','I')");
       $resac = db_query("insert into db_acount values($acount,2512,14287,'','".AddSlashes(pg_result($resaco,0,'s118_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2512,14313,'','".AddSlashes(pg_result($resaco,0,'s118_i_numcgs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2512,14316,'','".AddSlashes(pg_result($resaco,0,'s118_c_tipoagenda'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2512,14288,'','".AddSlashes(pg_result($resaco,0,'s118_i_prestador'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2512,14289,'','".AddSlashes(pg_result($resaco,0,'s118_d_preferencia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2512,14290,'','".AddSlashes(pg_result($resaco,0,'s118_d_marcada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2512,14291,'','".AddSlashes(pg_result($resaco,0,'s118_c_horamarcada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2512,14292,'','".AddSlashes(pg_result($resaco,0,'s118_v_encaminhamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2512,14293,'','".AddSlashes(pg_result($resaco,0,'s118_v_protocolo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2512,14314,'','".AddSlashes(pg_result($resaco,0,'s118_d_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,2512,14315,'','".AddSlashes(pg_result($resaco,0,'s118_i_login'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($s118_i_codigo=null) {
      $this->atualizacampos();
     $sql = " update sau_agendaexterna set ";
     $virgula = "";
     if(trim($this->s118_i_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_i_codigo"])){
       $sql  .= $virgula." s118_i_codigo = $this->s118_i_codigo ";
       $virgula = ",";
       if(trim($this->s118_i_codigo) == null ){
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "s118_i_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->s118_i_numcgs)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_i_numcgs"])){
       $sql  .= $virgula." s118_i_numcgs = $this->s118_i_numcgs ";
       $virgula = ",";
       if(trim($this->s118_i_numcgs) == null ){
         $this->erro_sql = " Campo CGS nao Informado.";
         $this->erro_campo = "s118_i_numcgs";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->s118_c_tipoagenda)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_c_tipoagenda"])){
       $sql  .= $virgula." s118_c_tipoagenda = '$this->s118_c_tipoagenda' ";
       $virgula = ",";
       if(trim($this->s118_c_tipoagenda) == null ){
         $this->erro_sql = " Campo Tipo de Agendamento nao Informado.";
         $this->erro_campo = "s118_c_tipoagenda";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->s118_i_prestador)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_i_prestador"])){
        if(trim($this->s118_i_prestador)=="" && isset($GLOBALS["HTTP_POST_VARS"]["s118_i_prestador"])){
           $this->s118_i_prestador = "0" ;
        }
       $sql  .= $virgula." s118_i_prestador = $this->s118_i_prestador ";
       $virgula = ",";
     }
     if(trim($this->s118_d_preferencia)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_d_preferencia_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["s118_d_preferencia_dia"] !="") ){
       $sql  .= $virgula." s118_d_preferencia = '$this->s118_d_preferencia' ";
       $virgula = ",";
       if(trim($this->s118_d_preferencia) == null ){
         $this->erro_sql = " Campo Preferência nao Informado.";
         $this->erro_campo = "s118_d_preferencia_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["s118_d_preferencia_dia"])){
         $sql  .= $virgula." s118_d_preferencia = null ";
         $virgula = ",";
         if(trim($this->s118_d_preferencia) == null ){
           $this->erro_sql = " Campo Preferência nao Informado.";
           $this->erro_campo = "s118_d_preferencia_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->s118_d_marcada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_d_marcada_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["s118_d_marcada_dia"] !="") ){
       $sql  .= $virgula." s118_d_marcada = '$this->s118_d_marcada' ";
       $virgula = ",";
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["s118_d_marcada_dia"])){
         $sql  .= $virgula." s118_d_marcada = null ";
         $virgula = ",";
       }
     }
     if(trim($this->s118_c_horamarcada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_c_horamarcada"])){
       $sql  .= $virgula." s118_c_horamarcada = '$this->s118_c_horamarcada' ";
       $virgula = ",";
     }
     if(trim($this->s118_v_encaminhamento)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_v_encaminhamento"])){
       $sql  .= $virgula." s118_v_encaminhamento = '$this->s118_v_encaminhamento' ";
       $virgula = ",";
     }
     if(trim($this->s118_v_protocolo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_v_protocolo"])){
       $sql  .= $virgula." s118_v_protocolo = '$this->s118_v_protocolo' ";
       $virgula = ",";
     }
     if(trim($this->s118_d_data)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_d_data_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["s118_d_data_dia"] !="") ){
       $sql  .= $virgula." s118_d_data = '$this->s118_d_data' ";
       $virgula = ",";
       if(trim($this->s118_d_data) == null ){
         $this->erro_sql = " Campo Data nao Informado.";
         $this->erro_campo = "s118_d_data_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["s118_d_data_dia"])){
         $sql  .= $virgula." s118_d_data = null ";
         $virgula = ",";
         if(trim($this->s118_d_data) == null ){
           $this->erro_sql = " Campo Data nao Informado.";
           $this->erro_campo = "s118_d_data_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->s118_i_login)!="" || isset($GLOBALS["HTTP_POST_VARS"]["s118_i_login"])){
       $sql  .= $virgula." s118_i_login = $this->s118_i_login ";
       $virgula = ",";
       if(trim($this->s118_i_login) == null ){
         $this->erro_sql = " Campo Login nao Informado.";
         $this->erro_campo = "s118_i_login";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($s118_i_codigo!=null){
       $sql .= " s118_i_codigo = $this->s118_i_codigo";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->s118_i_codigo));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14287,'$this->s118_i_codigo','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_i_codigo"]) || $this->s118_i_codigo != "")
           $resac = db_query("insert into db_acount values($acount,2512,14287,'".AddSlashes(pg_result($resaco,$conresaco,'s118_i_codigo'))."','$this->s118_i_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_i_numcgs"]) || $this->s118_i_numcgs != "")
           $resac = db_query("insert into db_acount values($acount,2512,14313,'".AddSlashes(pg_result($resaco,$conresaco,'s118_i_numcgs'))."','$this->s118_i_numcgs',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_c_tipoagenda"]) || $this->s118_c_tipoagenda != "")
           $resac = db_query("insert into db_acount values($acount,2512,14316,'".AddSlashes(pg_result($resaco,$conresaco,'s118_c_tipoagenda'))."','$this->s118_c_tipoagenda',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_i_prestador"]) || $this->s118_i_prestador != "")
           $resac = db_query("insert into db_acount values($acount,2512,14288,'".AddSlashes(pg_result($resaco,$conresaco,'s118_i_prestador'))."','$this->s118_i_prestador',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_d_preferencia"]) || $this->s118_d_preferencia != "")
           $resac = db_query("insert into db_acount values($acount,2512,14289,'".AddSlashes(pg_result($resaco,$conresaco,'s118_d_preferencia'))."','$this->s118_d_preferencia',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_d_marcada"]) || $this->s118_d_marcada != "")
           $resac = db_query("insert into db_acount values($acount,2512,14290,'".AddSlashes(pg_result($resaco,$conresaco,'s118_d_marcada'))."','$this->s118_d_marcada',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_c_horamarcada"]) || $this->s118_c_horamarcada != "")
           $resac = db_query("insert into db_acount values($acount,2512,14291,'".AddSlashes(pg_result($resaco,$conresaco,'s118_c_horamarcada'))."','$this->s118_c_horamarcada',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_v_encaminhamento"]) || $this->s118_v_encaminhamento != "")
           $resac = db_query("insert into db_acount values($acount,2512,14292,'".AddSlashes(pg_result($resaco,$conresaco,'s118_v_encaminhamento'))."','$this->s118_v_encaminhamento',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_v_protocolo"]) || $this->s118_v_protocolo != "")
           $resac = db_query("insert into db_acount values($acount,2512,14293,'".AddSlashes(pg_result($resaco,$conresaco,'s118_v_protocolo'))."','$this->s118_v_protocolo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_d_data"]) || $this->s118_d_data != "")
           $resac = db_query("insert into db_acount values($acount,2512,14314,'".AddSlashes(pg_result($resaco,$conresaco,'s118_d_data'))."','$this->s118_d_data',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["s118_i_login"]) || $this->s118_i_login != "")
           $resac = db_query("insert into db_acount values($acount,2512,14315,'".AddSlashes(pg_result($resaco,$conresaco,'s118_i_login'))."','$this->s118_i_login',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Agenda Externa nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->s118_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Agenda Externa nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->s118_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->s118_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($s118_i_codigo=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($s118_i_codigo));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,14287,'$s118_i_codigo','E')");
         $resac = db_query("insert into db_acount values($acount,2512,14287,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_i_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2512,14313,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_i_numcgs'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2512,14316,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_c_tipoagenda'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2512,14288,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_i_prestador'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2512,14289,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_d_preferencia'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2512,14290,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_d_marcada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2512,14291,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_c_horamarcada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2512,14292,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_v_encaminhamento'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2512,14293,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_v_protocolo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2512,14314,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_d_data'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2512,14315,'','".AddSlashes(pg_result($resaco,$iresaco,'s118_i_login'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from sau_agendaexterna
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($s118_i_codigo != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " s118_i_codigo = $s118_i_codigo ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Agenda Externa nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$s118_i_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Agenda Externa nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$s118_i_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$s118_i_codigo;
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
        $this->erro_sql   = "Record Vazio na Tabela:sau_agendaexterna";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $s118_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from sau_agendaexterna ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = sau_agendaexterna.s118_i_login";
     $sql .= "      left  join sau_prestadores  on  sau_prestadores.s110_i_codigo = sau_agendaexterna.s118_i_prestador";
     $sql .= "      inner join cgs  on  cgs.z01_i_numcgs = sau_agendaexterna.s118_i_numcgs";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = sau_prestadores.s110_i_numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($s118_i_codigo!=null ){
         $sql2 .= " where sau_agendaexterna.s118_i_codigo = $s118_i_codigo ";
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
   public function sql_query_file ( $s118_i_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from sau_agendaexterna ";
     $sql2 = "";
     if($dbwhere==""){
       if($s118_i_codigo!=null ){
         $sql2 .= " where sau_agendaexterna.s118_i_codigo = $s118_i_codigo ";
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
}
?>
