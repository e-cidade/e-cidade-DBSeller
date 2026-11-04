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

//MODULO: pessoal
//CLASSE DA ENTIDADE padroes
class cl_padroes {
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
   var $r02_instit = 0;
   var $r02_anousu = 0;
   var $r02_mesusu = 0;
   var $r02_regime = 0;
   var $r02_codigo = null;
   var $r02_descr = null;
   var $r02_valor = 0;
   var $r02_hrssem = 0;
   var $r02_hrsmen = 0;
   var $r02_tipo = null;
   var $r02_form = null;
   var $r02_minimo = null;
   var $r02_nivel = null;
   var $r02_classe = null;
   var $r02_padraopai_regime = 0;
   var $r02_padraopai_codigo = 0;
   var $r02_padraopai_instit = 0;
   // cria propriedade com as variaveis do arquivo
   var $campos = "
                 r02_instit = int4 = Cod. Instituição
                 r02_anousu = int4 = Ano do Exercicio
                 r02_mesusu = int4 = Mes do Exercicio
                 r02_regime = int4 = Código do Regime
                 r02_codigo = char(10) = Código de Identificação Padrão
                 r02_descr = char(30) = Descrição do Padrão
                 r02_valor = float8 = Valor do Padrão
                 r02_hrssem = int4 = Horas Semanais
                 r02_hrsmen = float8 = Horas Mensais
                 r02_tipo = char(1) = Tipo
                 r02_form = varchar(40) = Fórmula
                 r02_minimo = varchar(4) = Valor Mínimo
                 r02_nivel = varchar(250) = Nível
                 r02_classe = varchar(250) = Classe
                 r02_padraopai_regime = int4 = Padrão vinculado
                 r02_padraopai_codigo = int4 = Padrão vinculado
                 r02_padraopai_instit = int4 = Padrão vinculado
                 ";
   //funcao construtor da classe
   function cl_padroes() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("padroes");
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }
   //funcao erro
   function erro($mostra,$retorna) {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
   function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->r02_instit = ($this->r02_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_instit"]:$this->r02_instit);
       $this->r02_anousu = ($this->r02_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_anousu"]:$this->r02_anousu);
       $this->r02_mesusu = ($this->r02_mesusu == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_mesusu"]:$this->r02_mesusu);
       $this->r02_regime = ($this->r02_regime == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_regime"]:$this->r02_regime);
       $this->r02_codigo = ($this->r02_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_codigo"]:$this->r02_codigo);
       $this->r02_descr = ($this->r02_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_descr"]:$this->r02_descr);
       $this->r02_valor = ($this->r02_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_valor"]:$this->r02_valor);
       $this->r02_hrssem = ($this->r02_hrssem == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_hrssem"]:$this->r02_hrssem);
       $this->r02_hrsmen = ($this->r02_hrsmen == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_hrsmen"]:$this->r02_hrsmen);
       $this->r02_tipo = ($this->r02_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_tipo"]:$this->r02_tipo);
       $this->r02_form = ($this->r02_form == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_form"]:$this->r02_form);
       $this->r02_minimo = ($this->r02_minimo == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_minimo"]:$this->r02_minimo);
       $this->r02_nivel = ($this->r02_nivel == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_nivel"]:$this->r02_nivel);
       $this->r02_classe = ($this->r02_classe == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_classe"]:$this->r02_classe);
       $this->r02_padraopai_regime = ($this->r02_padraopai_regime == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_padraopai_regime"]:$this->r02_padraopai_regime);
       $this->r02_padraopai_codigo = ($this->r02_padraopai_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_padraopai_codigo"]:$this->r02_padraopai_codigo);
       $this->r02_padraopai_instit = ($this->r02_padraopai_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_padraopai_instit"]:$this->r02_padraopai_instit);
     }else{
       $this->r02_instit = ($this->r02_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_instit"]:$this->r02_instit);
       $this->r02_anousu = ($this->r02_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_anousu"]:$this->r02_anousu);
       $this->r02_mesusu = ($this->r02_mesusu == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_mesusu"]:$this->r02_mesusu);
       $this->r02_regime = ($this->r02_regime == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_regime"]:$this->r02_regime);
       $this->r02_codigo = ($this->r02_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["r02_codigo"]:$this->r02_codigo);
     }
   }
   // funcao para inclusao
   function incluir ($r02_anousu,$r02_mesusu,$r02_regime,$r02_codigo,$r02_instit){
      $this->atualizacampos();
     if($this->r02_descr == null ){
       $this->erro_sql = " Campo Descricao do Padrao nao Informado.";
       $this->erro_campo = "r02_descr";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r02_valor == null ){
       $this->r02_valor = "0";
     }
     if($this->r02_hrssem == null ){
       $this->r02_hrssem = "0";
     }
     if($this->r02_hrsmen == null ){
       $this->r02_hrsmen = "0";
     }
     if($this->r02_padraopai_regime == null ){
       $this->r02_padraopai_regime = "0";
     }
     if($this->r02_padraopai_codigo == null ){
       $this->r02_padraopai_codigo = "0";
     }
     if($this->r02_padraopai_instit == null ){
       $this->r02_padraopai_instit = "0";
     }
       $this->r02_anousu = $r02_anousu;
       $this->r02_mesusu = $r02_mesusu;
       $this->r02_regime = $r02_regime;
       $this->r02_codigo = $r02_codigo;
       $this->r02_instit = $r02_instit;
     if(($this->r02_anousu == null) || ($this->r02_anousu == "") ){
       $this->erro_sql = " Campo r02_anousu nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r02_mesusu == null) || ($this->r02_mesusu == "") ){
       $this->erro_sql = " Campo r02_mesusu nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r02_regime == null) || ($this->r02_regime == "") ){
       $this->erro_sql = " Campo r02_regime nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r02_codigo == null) || ($this->r02_codigo == "") ){
       $this->erro_sql = " Campo r02_codigo nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r02_instit == null) || ($this->r02_instit == "") ){
       $this->erro_sql = " Campo r02_instit nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into padroes(
                                       r02_instit
                                      ,r02_anousu
                                      ,r02_mesusu
                                      ,r02_regime
                                      ,r02_codigo
                                      ,r02_descr
                                      ,r02_valor
                                      ,r02_hrssem
                                      ,r02_hrsmen
                                      ,r02_tipo
                                      ,r02_form
                                      ,r02_minimo
                                      ,r02_nivel
                                      ,r02_classe
                                      ,r02_padraopai_regime
                                      ,r02_padraopai_codigo
                                      ,r02_padraopai_instit
                       )
                values (
                                $this->r02_instit
                               ,$this->r02_anousu
                               ,$this->r02_mesusu
                               ,$this->r02_regime
                               ,'$this->r02_codigo'
                               ,'$this->r02_descr'
                               ,$this->r02_valor
                               ,$this->r02_hrssem
                               ,$this->r02_hrsmen
                               ,'$this->r02_tipo'
                               ,'$this->r02_form'
                               ,'$this->r02_minimo'
                               ,'$this->r02_nivel'
                               ,'$this->r02_classe'
                               ,$this->r02_padraopai_regime
                               ,$this->r02_padraopai_codigo
                               ,$this->r02_padraopai_instit
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Cadastramento dos Padroes ($this->r02_anousu."-".$this->r02_mesusu."-".$this->r02_regime."-".$this->r02_codigo."-".$this->r02_instit) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Cadastramento dos Padroes já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Cadastramento dos Padroes ($this->r02_anousu."-".$this->r02_mesusu."-".$this->r02_regime."-".$this->r02_codigo."-".$this->r02_instit) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->r02_anousu."-".$this->r02_mesusu."-".$this->r02_regime."-".$this->r02_codigo."-".$this->r02_instit;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->r02_anousu,$this->r02_mesusu,$this->r02_regime,$this->r02_codigo,$this->r02_instit  ));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_fetch_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,4074,'$this->r02_anousu','I')");
       $resac = db_query("insert into db_acountkey values($acount,4075,'$this->r02_mesusu','I')");
       $resac = db_query("insert into db_acountkey values($acount,4076,'$this->r02_regime','I')");
       $resac = db_query("insert into db_acountkey values($acount,4077,'$this->r02_codigo','I')");
       $resac = db_query("insert into db_acountkey values($acount,9896,'$this->r02_instit','I')");
       $resac = db_query("insert into db_acount values($acount,567,9896,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4074,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4075,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_mesusu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4076,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_regime'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4077,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4078,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4079,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4080,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_hrssem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4081,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_hrsmen'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4082,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4600,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_form'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,567,4601,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_minimo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,1009703,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_nivel'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,1009704,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_classe'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,1009705,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_padraopai_regime'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,1009706,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_padraopai_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,1009707,'','".AddSlashes(pg_fetch_result($resaco,0,'r02_padraopai_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($r02_anousu=null,$r02_mesusu=null,$r02_regime=null,$r02_codigo=null,$r02_instit=null) {
      $this->atualizacampos();
     $sql = " update padroes set ";
     $virgula = "";
     if(trim($this->r02_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_instit"])){
       $sql  .= $virgula." r02_instit = $this->r02_instit ";
       $virgula = ",";
       if(trim($this->r02_instit) == null ){
         $this->erro_sql = " Campo Cod. Instituição não informado.";
         $this->erro_campo = "r02_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r02_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_anousu"])){
       $sql  .= $virgula." r02_anousu = $this->r02_anousu ";
       $virgula = ",";
       if(trim($this->r02_anousu) == null ){
         $this->erro_sql = " Campo Ano do Exercicio não informado.";
         $this->erro_campo = "r02_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r02_mesusu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_mesusu"])){
       $sql  .= $virgula." r02_mesusu = $this->r02_mesusu ";
       $virgula = ",";
       if(trim($this->r02_mesusu) == null ){
         $this->erro_sql = " Campo Mes do Exercicio nao Informado.";
         $this->erro_campo = "r02_mesusu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r02_regime)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_regime"])){
       $sql  .= $virgula." r02_regime = $this->r02_regime ";
       $virgula = ",";
       if(trim($this->r02_regime) == null ){
         $this->erro_sql = " Campo Código do Regime não informado.";
         $this->erro_campo = "r02_regime";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r02_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_codigo"])){
       $sql  .= $virgula." r02_codigo = '$this->r02_codigo' ";
       $virgula = ",";
       if(trim($this->r02_codigo) == null ){
         $this->erro_sql = " Campo Código de Identificação Padrão não informado.";
         $this->erro_campo = "r02_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r02_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_descr"])){
       $sql  .= $virgula." r02_descr = '$this->r02_descr' ";
       $virgula = ",";
       if(trim($this->r02_descr) == null ){
         $this->erro_sql = " Campo Descricao do Padrao nao Informado.";
         $this->erro_campo = "r02_descr";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r02_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_valor"])){
        if(trim($this->r02_valor)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r02_valor"])){
           $this->r02_valor = "0" ;
        }
       $sql  .= $virgula." r02_valor = $this->r02_valor ";
       $virgula = ",";
     }
     if(trim($this->r02_hrssem)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_hrssem"])){
        if(trim($this->r02_hrssem)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r02_hrssem"])){
           $this->r02_hrssem = "0" ;
        }
       $sql  .= $virgula." r02_hrssem = $this->r02_hrssem ";
       $virgula = ",";
     }
     if(trim($this->r02_hrsmen)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_hrsmen"])){
        if(trim($this->r02_hrsmen)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r02_hrsmen"])){
           $this->r02_hrsmen = "0" ;
        }
       $sql  .= $virgula." r02_hrsmen = $this->r02_hrsmen ";
       $virgula = ",";
     }
     if(trim($this->r02_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_tipo"])){
       $sql  .= $virgula." r02_tipo = '$this->r02_tipo' ";
       $virgula = ",";
     }
     if(trim($this->r02_form)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_form"])){
       $sql  .= $virgula." r02_form = '$this->r02_form' ";
       $virgula = ",";
     }
     if(trim($this->r02_minimo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_minimo"])){
       $sql  .= $virgula." r02_minimo = '$this->r02_minimo' ";
       $virgula = ",";
     }
     if(trim($this->r02_nivel)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_nivel"])){
       $sql  .= $virgula." r02_nivel = '$this->r02_nivel' ";
       $virgula = ",";
     }
     if(trim($this->r02_classe)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_classe"])){
       $sql  .= $virgula." r02_classe = '$this->r02_classe' ";
       $virgula = ",";
     }
     if(trim($this->r02_padraopai_regime)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_padraopai_regime"])){
        if(trim($this->r02_padraopai_regime)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r02_padraopai_regime"])){
           $this->r02_padraopai_regime = "0" ;
        }
       $sql  .= $virgula." r02_padraopai_regime = $this->r02_padraopai_regime ";
       $virgula = ",";
     }
     if(trim($this->r02_padraopai_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_padraopai_codigo"])){
        if(trim($this->r02_padraopai_codigo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r02_padraopai_codigo"])){
           $this->r02_padraopai_codigo = "0" ;
        }
       $sql  .= $virgula." r02_padraopai_codigo = $this->r02_padraopai_codigo ";
       $virgula = ",";
     }
     if(trim($this->r02_padraopai_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r02_padraopai_instit"])){
        if(trim($this->r02_padraopai_instit)=="" && isset($GLOBALS["HTTP_POST_VARS"]["r02_padraopai_instit"])){
           $this->r02_padraopai_instit = "0" ;
        }
       $sql  .= $virgula." r02_padraopai_instit = $this->r02_padraopai_instit ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($r02_anousu!=null){
       $sql .= " r02_anousu = $this->r02_anousu";
     }
     if($r02_mesusu!=null){
       $sql .= " and  r02_mesusu = $this->r02_mesusu";
     }
     if($r02_regime!=null){
       $sql .= " and  r02_regime = $this->r02_regime";
     }
     if($r02_codigo!=null){
       $sql .= " and  r02_codigo = '$this->r02_codigo'";
     }
     if($r02_instit!=null){
       $sql .= " and  r02_instit = $this->r02_instit";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

     $resaco = $this->sql_record($this->sql_query_file($this->r02_anousu,$this->r02_mesusu,$this->r02_regime,$this->r02_codigo,$this->r02_instit));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_fetch_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,4074,'$this->r02_anousu','A')");
         $resac = db_query("insert into db_acountkey values($acount,4075,'$this->r02_mesusu','A')");
         $resac = db_query("insert into db_acountkey values($acount,4076,'$this->r02_regime','A')");
         $resac = db_query("insert into db_acountkey values($acount,4077,'$this->r02_codigo','A')");
         $resac = db_query("insert into db_acountkey values($acount,9896,'$this->r02_instit','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_instit"]))
           $resac = db_query("insert into db_acount values($acount,567,9896,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_instit'))."','$this->r02_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_anousu"]))
           $resac = db_query("insert into db_acount values($acount,567,4074,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_anousu'))."','$this->r02_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_mesusu"]))
           $resac = db_query("insert into db_acount values($acount,567,4075,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_mesusu'))."','$this->r02_mesusu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_regime"]))
           $resac = db_query("insert into db_acount values($acount,567,4076,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_regime'))."','$this->r02_regime',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_codigo"]))
           $resac = db_query("insert into db_acount values($acount,567,4077,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_codigo'))."','$this->r02_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_descr"]))
           $resac = db_query("insert into db_acount values($acount,567,4078,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_descr'))."','$this->r02_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_valor"]))
           $resac = db_query("insert into db_acount values($acount,567,4079,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_valor'))."','$this->r02_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_hrssem"]))
           $resac = db_query("insert into db_acount values($acount,567,4080,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_hrssem'))."','$this->r02_hrssem',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_hrsmen"]))
           $resac = db_query("insert into db_acount values($acount,567,4081,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_hrsmen'))."','$this->r02_hrsmen',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_tipo"]))
           $resac = db_query("insert into db_acount values($acount,567,4082,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_tipo'))."','$this->r02_tipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_form"]))
           $resac = db_query("insert into db_acount values($acount,567,4600,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_form'))."','$this->r02_form',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["r02_minimo"]))
           $resac = db_query("insert into db_acount values($acount,567,4601,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_minimo'))."','$this->r02_minimo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r02_nivel"]) || $this->r02_nivel != "")
             $resac = db_query("insert into db_acount values($acount,567,1009703,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_nivel'))."','$this->r02_nivel',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r02_classe"]) || $this->r02_classe != "")
             $resac = db_query("insert into db_acount values($acount,567,1009704,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_classe'))."','$this->r02_classe',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r02_padraopai_regime"]) || $this->r02_padraopai_regime != "")
             $resac = db_query("insert into db_acount values($acount,567,1009705,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_padraopai_regime'))."','$this->r02_padraopai_regime',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r02_padraopai_codigo"]) || $this->r02_padraopai_codigo != "")
             $resac = db_query("insert into db_acount values($acount,567,1009706,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_padraopai_codigo'))."','$this->r02_padraopai_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r02_padraopai_instit"]) || $this->r02_padraopai_instit != "")
             $resac = db_query("insert into db_acount values($acount,567,1009707,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'r02_padraopai_instit'))."','$this->r02_padraopai_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastramento dos Padroes nao Alterado. Alteracao Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->r02_anousu."-".$this->r02_mesusu."-".$this->r02_regime."-".$this->r02_codigo."-".$this->r02_instit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastramento dos Padroes nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->r02_anousu."-".$this->r02_mesusu."-".$this->r02_regime."-".$this->r02_codigo."-".$this->r02_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->r02_anousu."-".$this->r02_mesusu."-".$this->r02_regime."-".$this->r02_codigo."-".$this->r02_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($r02_anousu=null,$r02_mesusu=null,$r02_regime=null,$r02_codigo=null,$r02_instit=null,$dbwhere=null) {

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

       $resaco = $this->sql_record($this->sql_query_file($r02_anousu,$r02_mesusu,$r02_regime,$r02_codigo,$r02_instit));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,null,null,null,null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_fetch_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,4074,'$r02_anousu','E')");
         $resac = db_query("insert into db_acountkey values($acount,4075,'$r02_mesusu','E')");
         $resac = db_query("insert into db_acountkey values($acount,4076,'$r02_regime','E')");
         $resac = db_query("insert into db_acountkey values($acount,4077,'$r02_codigo','E')");
         $resac = db_query("insert into db_acountkey values($acount,9896,'$r02_instit','E')");
         $resac = db_query("insert into db_acount values($acount,567,9896,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4074,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4075,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_mesusu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4076,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_regime'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4077,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4078,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4079,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4080,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_hrssem'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4081,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_hrsmen'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4082,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4600,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_form'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,567,4601,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_minimo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,567,1009703,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_nivel'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,567,1009704,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_classe'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,567,1009705,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_padraopai_regime'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,567,1009706,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_padraopai_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,567,1009707,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'r02_padraopai_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from padroes
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($r02_anousu != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " r02_anousu = $r02_anousu ";
        }
        if($r02_mesusu != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " r02_mesusu = $r02_mesusu ";
        }
        if($r02_regime != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " r02_regime = $r02_regime ";
        }
        if (!empty($r02_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r02_codigo = '$r02_codigo' ";
        }
        if (!empty($r02_instit)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r02_instit = $r02_instit ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Cadastramento dos Padroes nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$r02_anousu."-".$r02_mesusu."-".$r02_regime."-".$r02_codigo."-".$r02_instit;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Cadastramento dos Padroes nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$r02_anousu."-".$r02_mesusu."-".$r02_regime."-".$r02_codigo."-".$r02_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$r02_anousu."-".$r02_mesusu."-".$r02_regime."-".$r02_codigo."-".$r02_instit;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao do recordset
   function sql_record($sql) {
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
        $this->erro_sql   = "Record Vazio na Tabela:padroes";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

   function sql_query ( $r02_anousu=null,$r02_mesusu=null,$r02_regime=null,$r02_codigo=null,$r02_instit=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from padroes ";
     $sql .= "      inner join db_config  on  db_config.codigo = padroes.r02_instit";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql2 = "";
     if($dbwhere==""){
       if($r02_anousu!=null ){
         $sql2 .= " where padroes.r02_anousu = $r02_anousu ";
       }
       if($r02_mesusu!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_mesusu = $r02_mesusu ";
       }
       if($r02_regime!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_regime = $r02_regime ";
       }
       if($r02_codigo!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_codigo = '$r02_codigo' ";
       }
       if($r02_instit!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_instit = $r02_instit ";
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
   function sql_query_file ( $r02_anousu=null,$r02_mesusu=null,$r02_regime=null,$r02_codigo=null,$r02_instit=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from padroes ";
     $sql2 = "";
     if($dbwhere==""){
       if($r02_anousu!=null ){
         $sql2 .= " where padroes.r02_anousu = $r02_anousu ";
       }
       if($r02_mesusu!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_mesusu = $r02_mesusu ";
       }
       if($r02_regime!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_regime = $r02_regime ";
       }
       if($r02_codigo!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_codigo = '$r02_codigo' ";
       }
       if($r02_instit!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_instit = $r02_instit ";
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
   function sql_query_diversos ( $r02_anousu=null,$r02_mesusu=null,$r02_regime=null,$r02_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from padroes ";
     $sql .= "      left join pesdiver on pesdiver.r07_anousu = padroes.r02_anousu
                                      and pesdiver.r07_mesusu = padroes.r02_mesusu
			              and pesdiver.r07_codigo = padroes.r02_minimo ";
     $sql2 = "";
     if($dbwhere==""){
       if($r02_anousu!=null ){
         $sql2 .= " where padroes.r02_anousu = $r02_anousu ";
       }
       if($r02_mesusu!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_mesusu = $r02_mesusu ";
       }
       if($r02_regime!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_regime = $r02_regime ";
       }
       if($r02_codigo!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_codigo = '$r02_codigo' ";
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
   function sql_query_cgmmovpad ($r02_anousu=null,$r02_mesusu=null,$r02_regime=null,$r02_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from padroes ";
     $sql .= "      left join rhpespadrao   on  rhpespadrao.rh03_anousu  = padroes.r02_anousu
                                           and  rhpespadrao.rh03_mesusu  = padroes.r02_mesusu
                                           and  rhpespadrao.rh03_padrao  = padroes.r02_codigo ";
     $sql .= "      left join rhpessoalmov  on  rhpessoalmov.rh02_seqpes = rhpespadrao.rh03_seqpes ";
     $sql .= "      left join rhpessoal     on  rhpessoal.rh01_regist    = rhpessoalmov.rh02_regist ";
     $sql .= "      left join cgm           on  cgm.z01_numcgm = rhpessoal.rh01_numcgm ";
     $sql .= "      left join rhlota        on  rhlota.r70_codigo = rhpessoalmov.rh02_lota
		                                        and rhlota.r70_instit = rhpessoalmov.rh02_instit ";
     $sql2 = "";
     if($dbwhere==""){
       if($r02_anousu!=null ){
         $sql2 .= " where padroes.r02_anousu = $r02_anousu ";
       }
       if($r02_mesusu!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_mesusu = $r02_mesusu ";
       }
       if($r02_regime!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_regime = $r02_regime ";
       }
       if($r02_codigo!=null ){
         if($sql2!=""){
            $sql2 .= " and ";
         }else{
            $sql2 .= " where ";
         }
         $sql2 .= " padroes.r02_codigo = '$r02_codigo' ";
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
   function atualiza_incluir (){
  	 $this->incluir($this->r02_anousu,$this->r02_mesusu,$this->r02_regime,$this->r02_codigo,$this->r02_instit);
  }


  function sql_reajusteAposentados($iTipoReajuste, $sCampos="*", $sOrdem = null, $sWhere = ""){

    $sSql  = " select  $sCampos                                                           ";
    $sSql .= "  from padroes                                                              ";
    $sSql .= "   inner join rhpespadrao on rh03_anousu = r02_anousu                       ";
    $sSql .= "                         and rh03_mesusu = r02_mesusu                       ";
    $sSql .= "                         and rh03_padrao = r02_codigo                       ";
    $sSql .= "                         and rh03_regime = r02_regime                       ";
    $sSql .= "   where                                                                    ";
    $sSql .= "        exists (select 1                                                    ";
    $sSql .= "             from rhpespadrao                                               ";
    $sSql .=     "                  inner join rhpessoalmov on rh02_seqpes = rh03_seqpes  ";
    $sSql .= "                  left join assenta      on h16_regist  = rh02_regist       ";
    $sSql .= "                  left join tipoasse     on h12_codigo  = h16_assent        ";
    $sSql .= "             where rh03_anousu = r02_anousu                                 ";
    $sSql .= "               and rh03_mesusu = r02_mesusu                                 ";
    $sSql .= "               and rh03_padrao = r02_codigo                                 ";
    $sSql .= "               and rh03_regime = r02_regime                                 ";
    $sSql .= "               and rh02_instit = r02_instit                                 ";
    $sSql .= "               and (h12_tiporeajuste = {$iTipoReajuste}                     ";
    if ($iTipoReajuste == 0) {
      $sSql .= "               or h12_tiporeajuste is null                                ";
    }
    $sSql .= "                    ) )                                                     ";
    $sSql .= "        and                                                                 ";
    $sSql .= "        not exists (                                                        ";
    $sSql .= "           select 1                                                         ";
    $sSql .= "             from rhpespadrao                                               ";
    $sSql .= "                  inner join rhpessoalmov on rh02_seqpes = rh03_seqpes      ";
    $sSql .= "                  left join assenta      on h16_regist  = rh02_regist       ";
    $sSql .= "                  left join tipoasse     on h12_codigo  = h16_assent        ";
    $sSql .= "             where rh03_anousu = r02_anousu                                 ";
    $sSql .= "               and rh03_mesusu = r02_mesusu                                 ";
    $sSql .= "               and rh03_padrao = r02_codigo                                 ";
    $sSql .= "               and rh03_regime = r02_regime                                 ";
    $sSql .= "               and rh02_instit = r02_instit                                 ";
    $sSql .= "               and (h12_tiporeajuste <> {$iTipoReajuste}                    ";
    if ($iTipoReajuste == 0) {
      $sSql .= "             and h12_tiporeajuste is not null                             ";
    } else {
      $sSql .= "             and (h12_tiporeajuste <> 0  or h12_tiporeajuste is null)     ";
    }
    $sSql .= "        ))                                                                  ";

    if (!empty($sWhere)) {
      $sSql .= " and {$sWhere}";
    }

    if (!empty($sOrdem)) {
      $sSql .= " order by $sOrdem ";
    }
    return $sSql;
  }

  /**
   * Retornas os Padrões que possuem servidores inválidos para a Regra de reajuste salarial por Padrão.
   * Todos os servidores de um padrão devem possuir o mesmo Tipo de Reajuste, caso contrário é inválido.
   *
   * @param  integer $iTipoReajuste Tipo de Reajuste
   * @param  integer $iAnoUsu       Ano Competência
   * @param  integer $iMesUsu       Mês Competência
   * @param  integer $iInstituicao  Instituição
   * @return string                 SQL.
   */
  function sql_padroesInvalidos($iTipoReajuste, $iAnoUsu, $iMesUsu, $iInstituicao){

    $sSql  = "select  distinct padroes.*                                                   \n";
    $sSql .= " from padroes                                                                \n";
    $sSql .= "  inner join rhpespadrao as r2 on rh03_anousu = r02_anousu                   \n";
    $sSql .= "                        and rh03_mesusu = r02_mesusu                         \n";
    $sSql .= "                        and rh03_padrao = r02_codigo                         \n";
    $sSql .= "                        and rh03_regime = r02_regime                         \n";
    $sSql .= "  where                                                                      \n";
    $sSql .= "      exists (select 1                                                       \n";
    $sSql .= "            from rhpespadrao                                                 \n";
    $sSql .= "                 inner join rhpessoalmov on rh02_seqpes = rh03_seqpes        \n";
    $sSql .= "                 left join assenta      on h16_regist  = rh02_regist         \n";
    $sSql .= "                 left join tipoasse     on h12_codigo  = h16_assent          \n";
    $sSql .= "            where rh03_anousu = r02_anousu                                   \n";
    $sSql .= "              and rh03_mesusu = r02_mesusu                                   \n";
    $sSql .= "              and rh03_padrao = r02_codigo                                   \n";
    $sSql .= "              and rh03_regime = r02_regime                                   \n";
    $sSql .= "              and rh02_instit = r02_instit                                   \n";
    $sSql .= "              and (h12_tiporeajuste = {$iTipoReajuste}                       \n";
    $sSql .= "                   or (case when $iTipoReajuste = 0                          \n";
    $sSql .= "                          then h12_tiporeajuste is null                      \n";
    $sSql .= "                          else false                                         \n";
    $sSql .= "                      end)                                                   \n";
    $sSql .= "              )                                                              \n";
    $sSql .= "          )                                                                  \n";
    $sSql .= "       and                                                                   \n";
    $sSql .= "       exists (                                                              \n";
    $sSql .= "          select 1                                                           \n";
    $sSql .= "            from rhpespadrao                                                 \n";
    $sSql .= "                 inner join rhpessoalmov on rh02_seqpes = rh03_seqpes        \n";
    $sSql .= "                 left join assenta      on h16_regist  = rh02_regist         \n";
    $sSql .= "                 left join tipoasse     on h12_codigo  = h16_assent          \n";
    $sSql .= "            where rh03_anousu = r02_anousu                                   \n";
    $sSql .= "              and rh03_mesusu = r02_mesusu                                   \n";
    $sSql .= "              and rh03_padrao = r02_codigo                                   \n";
    $sSql .= "              and rh03_regime = r02_regime                                   \n";
    $sSql .= "              and rh02_instit = r02_instit                                   \n";
    $sSql .= "              and (h12_tiporeajuste <> {$iTipoReajuste}                      \n";
    $sSql .= "                   or (case when $iTipoReajuste <> 0                         \n";
    $sSql .= "                          then h12_tiporeajuste is null                      \n";
    $sSql .= "                          else false                                         \n";
    $sSql .= "                      end)                                                   \n";
    $sSql .= "              )                                                              \n";
    $sSql .= "              and not exists (                                               \n";
    $sSql .= "                  select 1                                                   \n";
    $sSql .= "                  from rhpespadrao                                           \n";
    $sSql .= "                       inner join rhpessoalmov on rh02_seqpes = rh03_seqpes  \n";
    $sSql .= "                       left join assenta      on h16_regist  = rh02_regist   \n";
    $sSql .= "                       left join tipoasse     on h12_codigo  = h16_assent    \n";
    $sSql .= "                  where rh03_anousu = r02_anousu                             \n";
    $sSql .= "                    and rh03_mesusu = r02_mesusu                             \n";
    $sSql .= "                    and rh03_padrao = r02_codigo                             \n";
    $sSql .= "                    and rh03_regime = r02_regime                             \n";
    $sSql .= "                    and rh02_instit = r02_instit                             \n";
    $sSql .= "                    and rh02_seqpes = r2.rh03_seqpes                         \n";
    $sSql .= "                    and (h12_tiporeajuste = {$iTipoReajuste})                \n";
    $sSql .= "              )                                                              \n";
    $sSql .= "       )                                                                     \n";

    if( $iTipoReajuste == 0 ){

      $sSql .= "and exists (                                                                                                   ";
      $sSql .= "      select h16_regist, count(distinct h12_tiporeajuste) from rhpespadrao                                     ";
      $sSql .= "          inner join rhpessoalmov rhpm on rh02_seqpes = rh03_seqpes                                            ";
      $sSql .= "             left join assenta      on h16_regist   = rh02_regist                                              ";
      $sSql .= "             left join tipoasse     on h12_codigo   = h16_assent                                               ";
      $sSql .= "        where rh03_anousu = r02_anousu                                                                         ";
      $sSql .= "          and rh03_mesusu = r02_mesusu                                                                         ";
      $sSql .= "          and rh03_padrao = r02_codigo                                                                         ";
      $sSql .= "          and rh03_regime = r02_regime                                                                         ";
      $sSql .= "          and rh02_instit = r02_instit                                                                         ";
      $sSql .= "          and (                                                                                                ";
      $sSql .= "              select distinct h12_tiporeajuste from rhpespadrao                                                ";
      $sSql .= "                inner join rhpessoalmov on rh02_seqpes = rh03_seqpes                                           ";
      $sSql .= "                   left join assenta      on h16_regist   = rh02_regist                                        ";
      $sSql .= "                   left join tipoasse     on h12_codigo   = h16_assent                                         ";
      $sSql .= "              where rh03_anousu = r02_anousu                                                                   ";
      $sSql .= "                and rh03_mesusu = r02_mesusu                                                                   ";
      $sSql .= "                and rh03_padrao = r02_codigo                                                                   ";
      $sSql .= "                and rh03_regime = r02_regime                                                                   ";
      $sSql .= "                and rh02_instit = r02_instit                                                                   ";
      $sSql .= "                and rh02_regist = rhpm.rh02_regist                                                             ";
      $sSql .= "                and (select count(distinct h12_tiporeajuste) from tipoasse                                     ";
      $sSql .= "                        inner join assenta on h16_assent = h12_codigo where h16_regist = rhpm.rh02_regist) = 1 ";
      $sSql .= "            ) = 0                                                                                              ";
      $sSql .= "     group by h16_regist having count(distinct h12_tiporeajuste) = 1                                           ";
      $sSql .= "   )                                                                                                           ";
    }

    $sSql .= "       and                                                                 \n";
    $sSql .= "       exists (                                                            \n";
    $sSql .= "          select 1                                                         \n";
    $sSql .= "            from rhpespadrao                                               \n";
    $sSql .= "                 inner join rhpessoalmov on rh02_seqpes = rh03_seqpes      \n";
    $sSql .= "                 left join assenta       on h16_regist  = rh02_regist      \n";
    $sSql .= "                 left join tipoasse      on h12_codigo  = h16_assent       \n";
    $sSql .= "            where rh03_anousu = r02_anousu                                 \n";
    $sSql .= "              and rh03_mesusu = r02_mesusu                                 \n";
    $sSql .= "              and rh03_padrao = r02_codigo                                 \n";
    $sSql .= "              and rh03_regime = r02_regime                                 \n";
    $sSql .= "              and rh02_instit = r02_instit                                 \n";
    $sSql .= "              and rh02_seqpes <> r2.rh03_seqpes                            \n";
    $sSql .= "              and (h12_tiporeajuste <> {$iTipoReajuste}                    \n";
    $sSql .= "                   or (case when {$iTipoReajuste} <> 0                     \n";
    $sSql .= "                          then h12_tiporeajuste is null                    \n";
    $sSql .= "                          else false                                       \n";
    $sSql .= "                      end)                                                 \n";
    $sSql .= "              )                                                            \n";
    $sSql .= "       )                                                                   \n";
    $sSql .= "  and r02_anousu = {$iAnoUsu}                                              \n";
    $sSql .= "  and r02_mesusu = {$iMesUsu}                                              \n";
    $sSql .= "  and r02_instit = {$iInstituicao}                                         \n";

    return $sSql;
  }

  /**
   * Retorna os servidores que possuem o tipo de reajuste
   * diferente do informado no padrão informado.
   *
   * @param  string  $sPadrao       Padrão
   * @param  integer $iRegime       Regime
   * @param  integer $iAnoUsu       Ano Competência
   * @param  integer $iMesUsu       Mês Competência
   * @param  integer $iTipoReajuste Tipo de Reajuste
   * @return string                 SQL
   */
  function sql_ServidoresInvalidos($sPadrao, $iRegime, $iAnoUsu, $iMesUsu, $iTipoReajuste) {

    $sSql  = " select distinct rh02_regist as matricula,                                 ";
    $sSql .= "        z01_nome as nome                                                   ";
    $sSql .= "   from rhpespadrao                                                        ";
    $sSql .= "        inner join rhpessoalmov on rh02_seqpes = rh03_seqpes               ";
    $sSql .= "        inner join rhpessoal    on rh01_regist = rh02_regist               ";
    $sSql .= "        inner join cgm          on rh01_numcgm = z01_numcgm                ";
    $sSql .= " where rh03_anousu =  {$iAnoUsu}                                           ";
    $sSql .= "   and rh03_mesusu =  {$iMesUsu}                                           ";
    $sSql .= "   and rh03_padrao = '{$sPadrao}'                                          ";
    $sSql .= "   and rh03_regime = '{$iRegime}'                                          ";

    if ($iTipoReajuste <> 0 ) {

      $sSql .= "   and (                                                                   ";
      $sSql .= "         select count(*)                                                   ";
      $sSql .= "         from assenta                                                      ";
      $sSql .= "              inner join tipoasse  on h12_codigo        = h16_assent       ";
      $sSql .= "                                  and h12_tiporeajuste  = {$iTipoReajuste} ";
      $sSql .= "         where h16_regist = rh02_regist                                    ";
      $sSql .= "     ) = 0                                                                 ";
    } else  {

      $sSql .= " and exists(                                                      ";
      $sSql .= "       select count(*)                                            ";
      $sSql .= "         from assenta                                             ";
      $sSql .= "              inner join tipoasse on  h12_codigo = h16_assent     ";
      $sSql .= "                                and h12_tiporeajuste = 0          ";
      $sSql .= "       where h16_regist = rh02_regist                             ";
      $sSql .= "      )                                                           ";
      $sSql .= " and exists(                                                      ";
      $sSql .= "       select h16_regist                                          ";
      $sSql .= "         from assenta                                             ";
      $sSql .= "              inner join tipoasse on  h12_codigo = h16_assent     ";
      $sSql .= "                                 and h12_tiporeajuste <> 0        ";
      $sSql .= "       where h16_regist = rh02_regist                             ";
      $sSql .= "     )                                                            ";
    }
    $sSql .= " order by z01_nome                                                  ";

    return $sSql;
  }

   /**
   * Retorna as matrículas que ocorreram reajuste do código de padrão e competência definidos
   *
   * @param  string  $codigoPadrao  Padrão
   * @param  integer $anousu        Ano Competência
   * @param  integer $mesusu        Mês Competência
   * @param  integer $instit        Instituição         
   * @return string  SQL
   */
  function sqlReajustePadraoByCompetencia($codigoPadrao, $anousu, $mesusu, $instit)
  {

    $sql = "
      SELECT rh02_regist
        FROM padroes
        INNER JOIN rhpespadrao
        ON rh03_anousu = r02_anousu
            AND rh03_mesusu = r02_mesusu
            AND rh03_padrao = r02_codigo
            AND rh03_regime = r02_regime
        INNER JOIN rhpessoalmov
            ON rh02_seqpes = rh03_seqpes
            and r02_instit = rh02_instit
        LEFT JOIN rhpesrescisao on rh05_seqpes = rh02_seqpes
      WHERE EXISTS 
        (SELECT 1
        FROM rhpespadrao
        INNER JOIN rhpessoalmov
            ON rh02_seqpes = rh03_seqpes
        INNER JOIN rhpessoal
            ON rh01_regist = rh02_regist
        WHERE rh03_anousu = r02_anousu
                AND rh03_mesusu = r02_mesusu
                AND rh03_padrao = r02_codigo
                AND rh03_regime = r02_regime
                AND rh02_instit = r02_instit )
            AND r02_codigo = '{$codigoPadrao}'
            AND r02_anousu = $anousu
            AND r02_mesusu = $mesusu
            AND r02_valor <> 0 
            AND rh02_instit = $instit
            AND rh05_seqpes is null";

    return $sql;
  }

  public function sqlReajustePadraoTipoReal($anousu, $mesusu, $instit)
  {
    $sql = "
        SELECT distinct
            r02_codigo as pad,
            r02_regime as reg,
            r02_form,
            r02_valor
            from padroes
            inner join rhpespadrao on rh03_anousu = r02_anousu and rh03_mesusu = r02_mesusu and rh03_padrao = r02_codigo and rh03_regime = r02_regime
            where exists (select 1 from rhpespadrao
                                    inner join rhpessoalmov on rh02_seqpes = rh03_seqpes
                                    inner join rhpessoal on rh01_regist = rh02_regist
                                    where rh03_anousu = r02_anousu
                                        and rh03_mesusu = r02_mesusu
                                        and rh03_padrao = r02_codigo
                                        and rh03_regime = r02_regime
                                        and rh02_instit = r02_instit
                                        and rh01_reajusteparidade = 1
                                        )
                and not exists (select 1 from rhpespadrao
                                inner join rhpessoalmov on rh02_seqpes = rh03_seqpes
                                inner join rhpessoal on rh01_regist = rh02_regist
                                where rh03_anousu = r02_anousu
                                        and rh03_mesusu = r02_mesusu
                                        and rh03_padrao = r02_codigo
                                        and rh03_regime = r02_regime
                                        and rh02_instit = r02_instit
                                        and rh01_reajusteparidade != 1
                                )
            and r02_anousu = $anousu
            and r02_mesusu = $mesusu
            and r02_instit = $instit
            and r02_valor <> 0";

    return $sql;
  }

  public function sqlReajustePadraoTipoParidade($anousu, $mesusu, $instit)
  {
    $sql = "
        SELECT distinct
            r02_codigo as pad,
            r02_regime as reg,
            r02_form,
            r02_valor
            from padroes
            inner join rhpespadrao on rh03_anousu = r02_anousu and rh03_mesusu = r02_mesusu and rh03_padrao = r02_codigo and rh03_regime = r02_regime
            where exists (select 1 from rhpespadrao
                                    inner join rhpessoalmov on rh02_seqpes = rh03_seqpes
                                    inner join rhpessoal on rh01_regist = rh02_regist
                                    where rh03_anousu = r02_anousu
                                        and rh03_mesusu = r02_mesusu
                                        and rh03_padrao = r02_codigo
                                        and rh03_regime = r02_regime
                                        and rh02_instit = r02_instit
                                        and rh01_reajusteparidade = 2
                                        )
                and not exists (select 1 from rhpespadrao
                                inner join rhpessoalmov on rh02_seqpes = rh03_seqpes
                                inner join rhpessoal on rh01_regist = rh02_regist
                                where rh03_anousu = r02_anousu
                                        and rh03_mesusu = r02_mesusu
                                        and rh03_padrao = r02_codigo
                                        and rh03_regime = r02_regime
                                        and rh02_instit = r02_instit
                                        and rh01_reajusteparidade != 2
                                )
            and r02_anousu = $anousu
            and r02_mesusu = $mesusu
            and r02_instit = $instit
            and r02_valor <> 0";

    return $sql;
  }

  public function sqlReajustePadraoSemTipoDeReajuste($anousu, $mesusu, $instit)
  {
    $sql = "
        SELECT distinct
            r02_codigo as pad,
            r02_regime as reg,
            r02_valor
            from padroes
            inner join rhpespadrao on rh03_anousu = r02_anousu and rh03_mesusu = r02_mesusu and rh03_padrao = r02_codigo and rh03_regime = r02_regime
            where exists (select 1 from rhpespadrao
                                    inner join rhpessoalmov on rh02_seqpes = rh03_seqpes
                                    inner join rhpessoal on rh01_regist = rh02_regist
                                    where rh03_anousu = r02_anousu
                                        and rh03_mesusu = r02_mesusu
                                        and rh03_padrao = r02_codigo
                                        and rh03_regime = r02_regime
                                        and rh02_instit = r02_instit
                                        and rh01_reajusteparidade = 0
                          )
            and r02_anousu = $anousu
            and r02_mesusu = $mesusu
            and r02_instit = $instit
            and r02_valor <> 0";

    return $sql;
  }

}
?>
