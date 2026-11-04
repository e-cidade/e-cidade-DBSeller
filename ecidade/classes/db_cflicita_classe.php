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

class cl_cflicita
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
    public $l03_codigo = 0;
    public $l03_descr = 0;
    public $l03_tipo = 0;
    public $l03_codcom = 0;
    public $l03_instit = 0;
    public $l03_usaregistropreco = 'f';
    public $l03_pctipocompratribunal = 0;
    public $l03_modalidadeativa = 'f';
   // cria propriedade com as variaveis do arquivo
    public $campos = "
                 l03_codigo = int4 = Codigo Sequencial
                 l03_descr = bpchar(40) = Descricao do tipo
                 l03_tipo = bpchar(1) = Tipo da Licitacao
                 l03_codcom = int4 = Codigo compra
                 l03_instit = int4 = Instituição
                 l03_usaregistropreco = bool = Usa Registro de Preço
                 l03_pctipocompratribunal = int4 = Código Tribunal
                 l03_modalidadeativa = bool = Modaidade Ativa
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("cflicita");
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra,$retorna) {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\")</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

    public function atualizacampos($exclusao=false) {
     if($exclusao==false){
       $this->l03_codigo = ($this->l03_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["l03_codigo"]:$this->l03_codigo);
       $this->l03_descr = ($this->l03_descr == ""?@$GLOBALS["HTTP_POST_VARS"]["l03_descr"]:$this->l03_descr);
       $this->l03_tipo = ($this->l03_tipo == ""?@$GLOBALS["HTTP_POST_VARS"]["l03_tipo"]:$this->l03_tipo);
       $this->l03_codcom = ($this->l03_codcom == ""?@$GLOBALS["HTTP_POST_VARS"]["l03_codcom"]:$this->l03_codcom);
       $this->l03_instit = ($this->l03_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["l03_instit"]:$this->l03_instit);
       $this->l03_usaregistropreco = ($this->l03_usaregistropreco == "f"?@$GLOBALS["HTTP_POST_VARS"]["l03_usaregistropreco"]:$this->l03_usaregistropreco);
       $this->l03_pctipocompratribunal = ($this->l03_pctipocompratribunal == ""?@$GLOBALS["HTTP_POST_VARS"]["l03_pctipocompratribunal"]:$this->l03_pctipocompratribunal);
       $this->l03_modalidadeativa = ($this->l03_modalidadeativa == "f"?@$GLOBALS["HTTP_POST_VARS"]["l03_modalidadeativa"]:$this->l03_modalidadeativa);
     }else{
       $this->l03_codigo = ($this->l03_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["l03_codigo"]:$this->l03_codigo);
     }
   }

    public function incluir ($l03_codigo){
      $this->atualizacampos();
     if($this->l03_codcom == null ){
       $this->erro_sql = " Campo Codigo compra não informado.";
       $this->erro_campo = "l03_codcom";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->l03_instit == null ){
       $this->erro_sql = " Campo Instituição não informado.";
       $this->erro_campo = "l03_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->l03_usaregistropreco == null ){
       $this->l03_usaregistropreco = "false";
     }
     if($this->l03_pctipocompratribunal == null ){
       $this->erro_sql = " Campo Código Tribunal não informado.";
       $this->erro_campo = "l03_pctipocompratribunal";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->l03_modalidadeativa == null ){
       $this->erro_sql = " Campo Modaidade Ativa não informado.";
       $this->erro_campo = "l03_modalidadeativa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($l03_codigo == "" || $l03_codigo == null ){
      $result = db_query("select nextval('cflicita_l03_codigo_seq')");
      if($result==false){
        $this->erro_banco = str_replace("\n","",@pg_last_error());
        $this->erro_sql   = "Verifique o cadastro da sequencia: cflicita_l03_codigo_seq do campo: l03_codigo";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
      $this->l03_codigo = pg_result($result,0,0);
    }else{
      $result = db_query("select last_value from cflicita_l03_codigo_seq");
      if(($result != false) && (pg_result($result,0,0) < $l03_codigo)){
        $this->erro_sql = " Campo l03_codigo maior que último número da sequencia.";
        $this->erro_banco = "Sequencia menor que este número.";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }else{
        $this->l03_codigo = $l03_codigo;
      }
    }
    if(($this->l03_codigo == null) || ($this->l03_codigo == "") ){
      $this->erro_sql = " Campo l03_codigo nao declarado.";
      $this->erro_banco = "Chave Primaria zerada.";
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }
     $sql = "insert into cflicita(
                                       l03_codigo
                                      ,l03_descr
                                      ,l03_tipo
                                      ,l03_codcom
                                      ,l03_instit
                                      ,l03_usaregistropreco
                                      ,l03_pctipocompratribunal
                                      ,l03_modalidadeativa
                       )
                values (
                                $this->l03_codigo
                               ,'$this->l03_descr'
                               ,'$this->l03_tipo'
                               ,$this->l03_codcom
                               ,$this->l03_instit
                               ,'$this->l03_usaregistropreco'
                               ,$this->l03_pctipocompratribunal
                               ,'$this->l03_modalidadeativa'
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Parametros de Configuracao de cada tipo de licitac ($this->l03_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Parametros de Configuracao de cada tipo de licitac já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Parametros de Configuracao de cada tipo de licitac ($this->l03_codigo) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->l03_codigo;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->l03_codigo  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,7661,'$this->l03_codigo','I')");
         $resac = db_query("insert into db_acount values($acount,430,7661,'','".AddSlashes(pg_result($resaco,0,'l03_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,430,2667,'','".AddSlashes(pg_result($resaco,0,'l03_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,430,2666,'','".AddSlashes(pg_result($resaco,0,'l03_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,430,5564,'','".AddSlashes(pg_result($resaco,0,'l03_codcom'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,430,7662,'','".AddSlashes(pg_result($resaco,0,'l03_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,430,15269,'','".AddSlashes(pg_result($resaco,0,'l03_usaregistropreco'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,430,17818,'','".AddSlashes(pg_result($resaco,0,'l03_pctipocompratribunal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,430,296209171,'','".AddSlashes(pg_result($resaco,0,'l03_modalidadeativa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }

    public function alterar ($l03_codigo=null) {
      $this->atualizacampos();
     $sql = " update cflicita set ";
     $virgula = "";
     if(trim($this->l03_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["l03_codigo"])){
       $sql  .= $virgula." l03_codigo = $this->l03_codigo ";
       $virgula = ",";
       if(trim($this->l03_codigo) == null ){
         $this->erro_sql = " Campo Modalidade de Compra não informado.";
         $this->erro_campo = "l03_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->l03_descr)!="" || isset($GLOBALS["HTTP_POST_VARS"]["l03_descr"])){
       $sql  .= $virgula." l03_descr = '$this->l03_descr' ";
       $virgula = ",";
     }
     if(trim($this->l03_tipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["l03_tipo"])){
       $sql  .= $virgula." l03_tipo = '$this->l03_tipo' ";
       $virgula = ",";
     }
     if(trim($this->l03_codcom)!="" || isset($GLOBALS["HTTP_POST_VARS"]["l03_codcom"])){
       $sql  .= $virgula." l03_codcom = $this->l03_codcom ";
       $virgula = ",";
       if(trim($this->l03_codcom) == null ){
         $this->erro_sql = " Campo Codigo compra não informado.";
         $this->erro_campo = "l03_codcom";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->l03_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["l03_instit"])){
       $sql  .= $virgula." l03_instit = $this->l03_instit ";
       $virgula = ",";
       if(trim($this->l03_instit) == null ){
         $this->erro_sql = " Campo Instituição não informado.";
         $this->erro_campo = "l03_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->l03_usaregistropreco)!="" || isset($GLOBALS["HTTP_POST_VARS"]["l03_usaregistropreco"])){
       $sql  .= $virgula." l03_usaregistropreco = '$this->l03_usaregistropreco' ";
       $virgula = ",";
     }
     if(trim($this->l03_pctipocompratribunal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["l03_pctipocompratribunal"])){
       $sql  .= $virgula." l03_pctipocompratribunal = $this->l03_pctipocompratribunal ";
       $virgula = ",";
       if(trim($this->l03_pctipocompratribunal) == null ){
         $this->erro_sql = " Campo Código Tribunal não informado.";
         $this->erro_campo = "l03_pctipocompratribunal";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->l03_modalidadeativa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["l03_modalidadeativa"])){
       $sql  .= $virgula." l03_modalidadeativa = '$this->l03_modalidadeativa' ";
       $virgula = ",";
       if(trim($this->l03_modalidadeativa) == null ){
         $this->erro_sql = " Campo Modaidade Ativa não informado.";
         $this->erro_campo = "l03_modalidadeativa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($l03_codigo!=null){
       $sql .= " l03_codigo = $this->l03_codigo";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->l03_codigo));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,7661,'$this->l03_codigo','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["l03_codigo"]) || $this->l03_codigo != "")
             $resac = db_query("insert into db_acount values($acount,430,7661,'".AddSlashes(pg_result($resaco,$conresaco,'l03_codigo'))."','$this->l03_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["l03_descr"]) || $this->l03_descr != "")
             $resac = db_query("insert into db_acount values($acount,430,2667,'".AddSlashes(pg_result($resaco,$conresaco,'l03_descr'))."','$this->l03_descr',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["l03_tipo"]) || $this->l03_tipo != "")
             $resac = db_query("insert into db_acount values($acount,430,2666,'".AddSlashes(pg_result($resaco,$conresaco,'l03_tipo'))."','$this->l03_tipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["l03_codcom"]) || $this->l03_codcom != "")
             $resac = db_query("insert into db_acount values($acount,430,5564,'".AddSlashes(pg_result($resaco,$conresaco,'l03_codcom'))."','$this->l03_codcom',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["l03_instit"]) || $this->l03_instit != "")
             $resac = db_query("insert into db_acount values($acount,430,7662,'".AddSlashes(pg_result($resaco,$conresaco,'l03_instit'))."','$this->l03_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["l03_usaregistropreco"]) || $this->l03_usaregistropreco != "")
             $resac = db_query("insert into db_acount values($acount,430,15269,'".AddSlashes(pg_result($resaco,$conresaco,'l03_usaregistropreco'))."','$this->l03_usaregistropreco',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["l03_pctipocompratribunal"]) || $this->l03_pctipocompratribunal != "")
             $resac = db_query("insert into db_acount values($acount,430,17818,'".AddSlashes(pg_result($resaco,$conresaco,'l03_pctipocompratribunal'))."','$this->l03_pctipocompratribunal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["l03_modalidadeativa"]) || $this->l03_modalidadeativa != "")
             $resac = db_query("insert into db_acount values($acount,430,296209171,'".AddSlashes(pg_result($resaco,$conresaco,'l03_modalidadeativa'))."','$this->l03_modalidadeativa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Parametros de Configuracao de cada tipo de licitac não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->l03_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Parametros de Configuracao de cada tipo de licitac não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->l03_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->l03_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

    public function excluir ($l03_codigo=null,$dbwhere=null) {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($l03_codigo));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,7661,'$l03_codigo','E')");
           $resac  = db_query("insert into db_acount values($acount,430,7661,'','".AddSlashes(pg_result($resaco,$iresaco,'l03_codigo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,430,2667,'','".AddSlashes(pg_result($resaco,$iresaco,'l03_descr'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,430,2666,'','".AddSlashes(pg_result($resaco,$iresaco,'l03_tipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,430,5564,'','".AddSlashes(pg_result($resaco,$iresaco,'l03_codcom'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,430,7662,'','".AddSlashes(pg_result($resaco,$iresaco,'l03_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,430,15269,'','".AddSlashes(pg_result($resaco,$iresaco,'l03_usaregistropreco'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,430,17818,'','".AddSlashes(pg_result($resaco,$iresaco,'l03_pctipocompratribunal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,430,296209171,'','".AddSlashes(pg_result($resaco,$iresaco,'l03_modalidadeativa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from cflicita
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($l03_codigo)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " l03_codigo = $l03_codigo ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) {
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Parametros de Configuracao de cada tipo de licitac não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$l03_codigo;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Parametros de Configuracao de cada tipo de licitac não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$l03_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$l03_codigo;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       }
     }
   }

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
        $this->erro_sql   = "Record Vazio na Tabela:cflicita";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query ( $l03_codigo=null,$campos="*",$ordem=null,$dbwhere=""){

     $sql  = "select {$campos}";
     $sql .= "  from cflicita ";
     $sql .= "      inner join db_config  on  db_config.codigo = cflicita.l03_instit";
     $sql .= "      inner join pctipocompra  on  pctipocompra.pc50_codcom = cflicita.l03_codcom";
     $sql .= "      inner join pctipocompratribunal  on  pctipocompratribunal.l44_sequencial = cflicita.l03_pctipocompratribunal";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql .= "      left  join db_depart  on  db_depart.coddepto = db_config.db21_departamento";
     $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
     $sql .= "      inner join pctipocompratribunal  as a on   a.l44_sequencial = pctipocompra.pc50_pctipocompratribunal";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($l03_codigo)) {
         $sql2 .= " where cflicita.l03_codigo = $l03_codigo ";
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

    public function sql_query_file ( $l03_codigo=null,$campos="*",$ordem=null,$dbwhere=""){

     $sql  = "select {$campos} ";
     $sql .= "  from cflicita ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($l03_codigo)){
         $sql2 .= " where cflicita.l03_codigo = $l03_codigo ";
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
   public function sql_query_liclicita ( $l03_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from liclicita ";
     $sql2 = "";
     if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   public function sql_query_liclicita_numero ( $l03_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from liclicita ";
     $sql .= " inner join cflicita on l03_codigo = l20_codtipocom ";
     $sql .= " left join pccflicitapar on l25_codcflicita=l03_codigo and l25_numero=l20_codigo ";
     $sql2 = "";
     if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   public function sql_query_numeracao ( $l03_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cflicita ";
     $sql .= "      inner join db_config     on  db_config.codigo = cflicita.l03_instit";
     $sql2 = "";
     if($dbwhere==""){
       if($l03_codigo!=null ){
         $sql2 .= " where cflicita.l03_codigo = $l03_codigo ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   public function sql_query_regrageral ( $l03_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from pccflicitanum ";
     $sql2 = "";
     if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
   public function sql_query_regramodalidade ( $l03_codigo=null,$campos="*",$ordem=null,$dbwhere=""){
     $sql = "select ";
     if($campos != "*" ){
       $campos_sql = split("#",$campos);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }else{
       $sql .= $campos;
     }
     $sql .= " from cflicita ";
     $sql .= "      inner join db_config     on  db_config.codigo = cflicita.l03_instit";
     $sql .= "      inner join pccflicitapar on  pccflicitapar.l25_codcflicita = cflicita.l03_codigo";
     $sql2 = "";
     if($dbwhere==""){
       if($l03_codigo!=null ){
         $sql2 .= " where cflicita.l03_codigo = $l03_codigo ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = split("#",$ordem);
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
     }
     return $sql;
  }
}
