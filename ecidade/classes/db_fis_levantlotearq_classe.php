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

//MODULO: caixa
//CLASSE DA ENTIDADE fis_levantlotearq
class cl_fis_levantlotearq {
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
   public $y122_codigo      = 0;
   public $y122_pecafiscal  = 0;
   public $y122_data        = null;
   public $y122_hora        = null;
   public $y122_tipofiscal  = 0;
   public $y122_usuario     = 0;
   public $y122_observacao  = null;
   public $y122_procedencia = 0;
   public $y122_valor       = 0;
   public $y122_instit      = 0;
   public $y122_nomearq     = null;
   public $y122_md5         = null;

   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 y122_codigo = int4 = Cod. do Levantamento
                 y122_pecafiscal = int4 = Tipo da Peça Fiscal (1 - Auto | 2 - Notificação)
                 y122_data = date = Data do Levantamento
                 y122_hora = char(5) = Hora do Levantamento
                 y122_tipofiscal = int4 = Tipo da Fiscalização
                 y122_usuario = int4 = Cod. Usuário
                 y122_observacao = text = Observação do Levantamento
                 y122_procedencia = int4 = Código da Procedência
                 y122_valor = double = Valor da Procedencia (% ou fixo)
                 y122_instit = int4 = Instituição
                 y122_nomearq = varchar(100) = Nome do Arquivo
                 y122_md5 = char(32) = MD5 do arquivo
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_levantlotearq");
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
       $this->y122_codigo = ($this->y122_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_codigo"]:$this->y122_codigo);
       $this->y122_pecafiscal = ($this->y122_pecafiscal == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_pecafiscal"]:$this->y122_pecafiscal);
       if($this->y122_data == ""){
         $this->y122_data_dia = ($this->y122_data_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_data_dia"]:$this->y122_data_dia);
         $this->y122_data_mes = ($this->y122_data_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_data_mes"]:$this->y122_data_mes);
         $this->y122_data_ano = ($this->y122_data_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_data_ano"]:$this->y122_data_ano);
         if($this->y122_data_dia != ""){
            $this->y122_data = $this->y122_data_ano."-".$this->y122_data_mes."-".$this->y122_data_dia;
         }
       }
       $this->y122_hora = ($this->y122_hora == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_hora"]:$this->y122_hora);
       $this->y122_tipofiscal = ($this->y122_tipofiscal == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_tipofiscal"]:$this->y122_tipofiscal);
       $this->y122_usuario = ($this->y122_usuario == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_usuario"]:$this->y122_usuario);
       $this->y122_observacao = ($this->y122_observacao == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_observacao"]:$this->y122_observacao);
       $this->y122_procedencia = ($this->y122_procedencia == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_procedencia"]:$this->y122_procedencia);
       $this->y122_valor = ($this->y122_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_valor"]:$this->y122_valor);
       $this->y122_instit = ($this->y122_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_instit"]:$this->y122_instit);
       $this->y122_nomearq = ($this->y122_nomearq == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_nomearq"]:$this->y122_nomearq);
       $this->y122_md5 = ($this->y122_md5 == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_md5"]:$this->y122_md5);
     }else{
       $this->y122_codigo = ($this->y122_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["y122_codigo"]:$this->y122_codigo);
     }
   }
   // funcao para inclusao
   public function incluir ($y122_codigo){
     $this->atualizacampos();

     //$clfiscalprocrec = new cl_fis_fiscalprocrec;

     //$verificarPer = $clfiscalprocrec->sql_query_file($this->y122_procedencia);
     //$rsVerificarPer = $clfiscalprocrec->sql_record($verificarPer);

     if($this->y122_pecafiscal == 0 ){
       $this->erro_sql = " Campo Peca Fiscal nao Informado.";
       $this->erro_campo = "y122_pecafiscal";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y122_data == null ){
       $this->erro_sql = " Campo Data nao Informado.";
       $this->erro_campo = "y122_data";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y122_hora == null ){
       $this->erro_sql = " Campo Hora nao Informado.";
       $this->erro_campo = "y122_hora";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y122_tipofiscal == 0 ){
       $this->erro_sql = " Campo Tipo de Fiscalizacao nao Informado.";
       $this->erro_campo = "y122_tipofiscal";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y122_usuario == 0 ){
       $this->erro_sql = " Campo Usuario nao Informado.";
       $this->erro_campo = "y122_usuario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y122_procedencia == 0 ){
       $this->erro_sql = " Campo Procedencia nao Informado.";
       $this->erro_campo = "y122_procedencia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     /*if($clfiscalprocrec->numrows > 0){
        if($this->y122_valor == 0 ){
          $this->erro_sql = " Campo Valor nao Informado.";
          $this->erro_campo = "y122_valor";
          $this->erro_banco = "";
          $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
          $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
          $this->erro_status = "0";
          return false;
        }
     }*/

     if($this->y122_valor == null ){
      $this->y122_valor = 0;
     }

     if($this->y122_instit == 0 ){
       $this->erro_sql = " Campo Instituicao nao Informado.";
       $this->erro_campo = "y122_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y122_nomearq == null ){
       $this->erro_sql = " Campo Arquivo nao Informado.";
       $this->erro_campo = "y122_nomearq";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->y122_md5 == null ){
       $this->erro_sql = " Campo MD5 nao Informado.";
       $this->erro_campo = "y122_md5";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($y122_codigo == "" || $y122_codigo == null ){
       $result = @pg_query("select nextval('fis_levantlotearq_y122_codigo_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: fis_levantlotearq_y122_codigo_seq do campo: y122_codigo";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->y122_codigo = pg_result($result,0,0);
     }else{
       $result = @pg_query("select last_value from fis_levantlotearq_y122_codigo_seq");
       if(($result != false) && (pg_result($result,0,0) < $y122_codigo)){
         $this->erro_sql = " Campo $y122_codigo maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->y122_codigo = $y122_codigo;
       }
     }
     if(($this->y122_codigo == null) || ($this->y122_codigo == "") ){
       $this->erro_sql = " Campo y122_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     /*$valorTrocado = null;
     if($clfiscalprocrec->numrows > 0){
      $valorTrocado = $this->y122_valor;
     }else{
      $valorTrocado = 0;
     }*/

     $sql = "INSERT INTO fis_levantlotearq(
                                      y122_codigo,
                                      y122_pecafiscal,
                                      y122_data,
                                      y122_hora,
                                      y122_tipofiscal,
                                      y122_usuario,
                                      y122_observacao,
                                      y122_procedencia,
                                      y122_valor,
                                      y122_instit,
                                      y122_nomearq,
                                      y122_md5)
                               VALUES ($this->y122_codigo,
                                       $this->y122_pecafiscal,
                                       '$this->y122_data',
                                       '$this->y122_hora',
                                       $this->y122_tipofiscal,
                                       $this->y122_usuario,
                                       '$this->y122_observacao',
                                       $this->y122_procedencia,
                                       $this->y122_valor,
                                       $this->y122_instit,
                                       '$this->y122_nomearq',
                                       '$this->y122_md5'
                                      )";
                                      //echo $sql."<br />";
     $result = db_query($sql);

     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Arquivos de Levantamento Lote ($this->y122_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Arquivo já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{

         $this->erro_sql   = "Arquivos de Levantamento Lote ($this->y122_codigo) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }

       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql   = "Inclusao efetuada com Sucesso\\n";
     $this->erro_sql  .= "Valores : ".$this->y122_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir = pg_affected_rows($result);
     /*


     return true;
     */

     return true;
   }
   // funcao para alteracao
   public function alterar ($y122_codigo=null) {
      $this->atualizacampos();
      /*
     $sql = " update fis_levantlotearq set ";
     $virgula = "";
     if(trim($this->id_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["id_usuario"])){
       $sql  .= $virgula." id_usuario = $this->id_usuario ";
       $virgula = ",";
       if(trim($this->id_usuario) == null ){
         $this->erro_sql = " Campo Cod. Usuário nao Informado.";
         $this->erro_campo = "id_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->k15_codbco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k15_codbco"])){
       $sql  .= $virgula." k15_codbco = $this->k15_codbco ";
       $virgula = ",";
       if(trim($this->k15_codbco) == null ){
         $this->erro_sql = " Campo Banco nao Informado.";
         $this->erro_campo = "k15_codbco";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->k15_codage)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k15_codage"])){
       $sql  .= $virgula." k15_codage = '$this->k15_codage' ";
       $virgula = ",";
       if(trim($this->k15_codage) == null ){
         $this->erro_sql = " Campo Agência nao Informado.";
         $this->erro_campo = "k15_codage";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->codret)!="" || isset($GLOBALS["HTTP_POST_VARS"]["codret"])){
       $sql  .= $virgula." codret = $this->codret ";
       $virgula = ",";
       if(trim($this->codret) == null ){
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "codret";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->arqret)!="" || isset($GLOBALS["HTTP_POST_VARS"]["arqret"])){
       $sql  .= $virgula." arqret = '$this->arqret' ";
       $virgula = ",";
       if(trim($this->arqret) == null ){
         $this->erro_sql = " Campo Arquivo de retorno nao Informado.";
         $this->erro_campo = "arqret";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->dtretorno)!="" || isset($GLOBALS["HTTP_POST_VARS"]["dtretorno_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["dtretorno_dia"] !="") ){
       $sql  .= $virgula." dtretorno = '$this->dtretorno' ";
       $virgula = ",";
       if(trim($this->dtretorno) == null ){
         $this->erro_sql = " Campo Data arquivo nao Informado.";
         $this->erro_campo = "dtretorno_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["dtretorno_dia"])){
         $sql  .= $virgula." dtretorno = null ";
         $virgula = ",";
         if(trim($this->dtretorno) == null ){
           $this->erro_sql = " Campo Data arquivo nao Informado.";
           $this->erro_campo = "dtretorno_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->dtarquivo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["dtarquivo_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["dtarquivo_dia"] !="") ){
       $sql  .= $virgula." dtarquivo = '$this->dtarquivo' ";
       $virgula = ",";
       if(trim($this->dtarquivo) == null ){
         $this->erro_sql = " Campo Data Arquivo nao Informado.";
         $this->erro_campo = "dtarquivo_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["dtarquivo_dia"])){
         $sql  .= $virgula." dtarquivo = null ";
         $virgula = ",";
         if(trim($this->dtarquivo) == null ){
           $this->erro_sql = " Campo Data Arquivo nao Informado.";
           $this->erro_campo = "dtarquivo_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->k00_conta)!="" || isset($GLOBALS["HTTP_POST_VARS"]["k00_conta"])){
       $sql  .= $virgula." k00_conta = $this->k00_conta ";
       $virgula = ",";
       if(trim($this->k00_conta) == null ){
         $this->erro_sql = " Campo Conta nao Informado.";
         $this->erro_campo = "k00_conta";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->autent)!="" || isset($GLOBALS["HTTP_POST_VARS"]["autent"])){
       $sql  .= $virgula." autent = '$this->autent' ";
       $virgula = ",";
       if(trim($this->autent) == null ){
         $this->erro_sql = " Campo Autentica nao Informado.";
         $this->erro_campo = "autent";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($codret!=null){
       $sql .= " codret = $this->codret";
     }


     $result = @pg_exec($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Arquivos de Baixa banco nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->codret;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Arquivos de Baixa banco nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->codret;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->codret;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
     */
   }
   // funcao para exclusao
   public function excluir ($y122_codigo=null,$dbwhere=null) {
    /*
     $sql = " delete from fis_levantlotearq
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($codret != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " codret = $codret ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = @pg_exec($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Arquivos de Baixa banco nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$codret;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Arquivos de Baixa banco nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$codret;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$codret;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
     */
   }
   // funcao do recordset
   public function sql_record($sql) {
     $result = @pg_query($sql);
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
        $this->erro_sql   = "Record Vazio na Tabela:fis_levantlotearq";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $y122_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fis_levantlotearq ";
 //    $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = fis_levantlotearq.id_usuario";
 //   $sql .= "      inner join saltes  on  saltes.k13_conta = fis_levantlotearq.k00_conta";
     $sql2 = "";
     if($dbwhere==""){
       if($y122_codigo!=null ){
         $sql2 .= " where fis_levantlotearq.y122_codigo = $y122_codigo ";
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
   public function sql_query_file ( $y122_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from fis_levantlotearq ";
     $sql2 = "";
     if($dbwhere==""){
       if($y122_codigo!=null ){
         $sql2 .= " where fis_levantlotearq.y122_codigo = $y122_codigo ";
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
