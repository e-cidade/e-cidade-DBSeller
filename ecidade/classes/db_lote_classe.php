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

//MODULO: Cadastro
//CLASSE DA ENTIDADE lote
class cl_lote {
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
   public $j34_idbql = 0;
   public $j34_setor = null;
   public $j34_quadra = null;
   public $j34_lote = null;
   public $j34_area = 0;
   public $j34_bairro = 0;
   public $j34_areal = 0;
   public $j34_totcon = 0;
   public $j34_zona = 0;
   public $j34_quamat = 0;
   public $j34_areapreservada = 0;
   public $j34_areaprivativa = 0;
   public $j34_areausocomum = 0;
   public $j34_zonadesurb = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 j34_idbql = int4 = Cód. Lote
                 j34_setor = char(4) = Setor
                 j34_quadra = char(4) = Quadra
                 j34_lote = char(4) = Lote
                 j34_area = float8 = Área M2
                 j34_bairro = int4 = Cód. Bairro
                 j34_areal = float8 = Área Medida
                 j34_totcon = float8 = Total construído no lote
                 j34_zona = int8 = Zona Fiscal
                 j34_quamat = int4 = Matrículas cadastradas
                 j34_areapreservada = float8 = Area Preservada
                 j34_areaprivativa = float8 = Área Privativa
                 j34_areausocomum = float8 = Área Uso Comun
                 j34_zonadesurb = int4 = Zona Des. Urbano
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("lote");
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
       $this->j34_idbql = ($this->j34_idbql == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_idbql"]:$this->j34_idbql);
       $this->j34_setor = ($this->j34_setor == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_setor"]:$this->j34_setor);
       $this->j34_quadra = ($this->j34_quadra == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_quadra"]:$this->j34_quadra);
       $this->j34_lote = ($this->j34_lote == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_lote"]:$this->j34_lote);
       $this->j34_area = ($this->j34_area == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_area"]:$this->j34_area);
       $this->j34_bairro = ($this->j34_bairro == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_bairro"]:$this->j34_bairro);
       $this->j34_areal = ($this->j34_areal == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_areal"]:$this->j34_areal);
       $this->j34_totcon = ($this->j34_totcon == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_totcon"]:$this->j34_totcon);
       $this->j34_zona = ($this->j34_zona == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_zona"]:$this->j34_zona);
       $this->j34_quamat = ($this->j34_quamat == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_quamat"]:$this->j34_quamat);
       $this->j34_areapreservada = ($this->j34_areapreservada == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_areapreservada"]:$this->j34_areapreservada);
       $this->j34_areaprivativa = ($this->j34_areaprivativa == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_areaprivativa"]:$this->j34_areaprivativa);
       $this->j34_areausocomum = ($this->j34_areausocomum == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_areausocomum"]:$this->j34_areausocomum);
       $this->j34_zonadesurb = ($this->j34_zonadesurb == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_zonadesurb"]:$this->j34_zonadesurb);
     }else{
       $this->j34_idbql = ($this->j34_idbql == ""?@$GLOBALS["HTTP_POST_VARS"]["j34_idbql"]:$this->j34_idbql);
     }
   }
   // funcao para inclusao
   public function incluir ($j34_idbql){
      $this->atualizacampos();
     if($this->j34_setor == null ){
       $this->erro_sql = " Campo Setor nao Informado.";
       $this->erro_campo = "j34_setor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j34_quadra == null ){
       $this->erro_sql = " Campo Quadra nao Informado.";
       $this->erro_campo = "j34_quadra";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j34_lote == null ){
       $this->erro_sql = " Campo Lote nao Informado.";
       $this->erro_campo = "j34_lote";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j34_area == null ){
       $this->erro_sql = " Campo Área M2 nao Informado.";
       $this->erro_campo = "j34_area";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j34_bairro == null ){
       $this->erro_sql = " Campo Cód. Bairro nao Informado.";
       $this->erro_campo = "j34_bairro";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j34_areal == null ){
       $this->j34_areal = "0";
     }
     if($this->j34_totcon == null ){
       $this->j34_totcon = "0";
     }
     if($this->j34_zona == null ){
       $this->erro_sql = " Campo Zona Fiscal nao Informado.";
       $this->erro_campo = "j34_zona";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j34_quamat == null ){
       $this->j34_quamat = "0";
     }
     if($this->j34_areapreservada == null ){
       $this->j34_areapreservada = "0";
     }

     if($this->j34_areaprivativa == null ){
       $this->erro_sql = " Campo Área Privativa M2 nao Informado.";
       $this->erro_campo = "j34_areaprivativa";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     if($this->j34_areausocomum == null ){
       $this->j34_areausocomum = "0";
     }

     if($this->j34_zonadesurb == null ){
       $this->j34_zonadesurb = "null";
     }

     if($j34_idbql == "" || $j34_idbql == null ){
       $result = db_query("select nextval('lote_j34_idbql_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: lote_j34_idbql_seq do campo: j34_idbql";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->j34_idbql = pg_result($result,0,0);
     }else{
       $result = db_query("select last_value from lote_j34_idbql_seq");
       if(($result != false) && (pg_result($result,0,0) < $j34_idbql)){
         $this->erro_sql = " Campo j34_idbql maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->j34_idbql = $j34_idbql;
       }
     }
     if(($this->j34_idbql == null) || ($this->j34_idbql == "") ){
       $this->erro_sql = " Campo j34_idbql nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into lote(
                                       j34_idbql
                                      ,j34_setor
                                      ,j34_quadra
                                      ,j34_lote
                                      ,j34_area
                                      ,j34_bairro
                                      ,j34_areal
                                      ,j34_totcon
                                      ,j34_zona
                                      ,j34_quamat
                                      ,j34_areapreservada
                                      ,j34_areaprivativa
                                      ,j34_areausocomum
                                      ,j34_zonadesurb
                       )
                values (
                                $this->j34_idbql
                               ,'$this->j34_setor'
                               ,'$this->j34_quadra'
                               ,'$this->j34_lote'
                               ,$this->j34_area
                               ,$this->j34_bairro
                               ,$this->j34_areal
                               ,$this->j34_totcon
                               ,$this->j34_zona
                               ,$this->j34_quamat
                               ,$this->j34_areapreservada
                               ,$this->j34_areaprivativa
                               ,$this->j34_areausocomum
                               ,$this->j34_zonadesurb
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Lotes ($this->j34_idbql) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Lotes já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Lotes ($this->j34_idbql) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j34_idbql;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $resaco = $this->sql_record($this->sql_query_file($this->j34_idbql));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
       $resac = db_query("insert into db_acountkey values($acount,79,'$this->j34_idbql','I')");
       $resac = db_query("insert into db_acount values($acount,19,79,'','".AddSlashes(pg_result($resaco,0,'j34_idbql'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,80,'','".AddSlashes(pg_result($resaco,0,'j34_setor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,81,'','".AddSlashes(pg_result($resaco,0,'j34_quadra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,82,'','".AddSlashes(pg_result($resaco,0,'j34_lote'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,83,'','".AddSlashes(pg_result($resaco,0,'j34_area'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,84,'','".AddSlashes(pg_result($resaco,0,'j34_bairro'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,85,'','".AddSlashes(pg_result($resaco,0,'j34_areal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,2379,'','".AddSlashes(pg_result($resaco,0,'j34_totcon'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,2380,'','".AddSlashes(pg_result($resaco,0,'j34_zona'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,2533,'','".AddSlashes(pg_result($resaco,0,'j34_quamat'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,15148,'','".AddSlashes(pg_result($resaco,0,'j34_areapreservada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,1015681,'','".AddSlashes(pg_result($resaco,0,'j34_areaprivativa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,1015682,'','".AddSlashes(pg_result($resaco,0,'j34_areausocomum'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,19,1015683,'','".AddSlashes(pg_result($resaco,0,'j34_zonadesurb'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($j34_idbql=null) {
      $this->atualizacampos();
     $sql = " update lote set ";
     $virgula = "";
     if(trim($this->j34_idbql)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_idbql"])){
       $sql  .= $virgula." j34_idbql = $this->j34_idbql ";
       $virgula = ",";
       if(trim($this->j34_idbql) == null ){
         $this->erro_sql = " Campo Cód. Lote nao Informado.";
         $this->erro_campo = "j34_idbql";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j34_setor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_setor"])){
       $sql  .= $virgula." j34_setor = '$this->j34_setor' ";
       $virgula = ",";
       if(trim($this->j34_setor) == null ){
         $this->erro_sql = " Campo Setor nao Informado.";
         $this->erro_campo = "j34_setor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j34_quadra)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_quadra"])){
       $sql  .= $virgula." j34_quadra = '$this->j34_quadra' ";
       $virgula = ",";
       if(trim($this->j34_quadra) == null ){
         $this->erro_sql = " Campo Quadra nao Informado.";
         $this->erro_campo = "j34_quadra";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j34_lote)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_lote"])){
       $sql  .= $virgula." j34_lote = '$this->j34_lote' ";
       $virgula = ",";
       if(trim($this->j34_lote) == null ){
         $this->erro_sql = " Campo Lote nao Informado.";
         $this->erro_campo = "j34_lote";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j34_area)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_area"])){
       $sql  .= $virgula." j34_area = $this->j34_area ";
       $virgula = ",";
       if(trim($this->j34_area) == null ){
         $this->erro_sql = " Campo Área M2 nao Informado.";
         $this->erro_campo = "j34_area";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->j34_areausocomum)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_areausocomum"])){
       $sql  .= $virgula." j34_areausocomum = $this->j34_areausocomum ";
       $virgula = ",";
       if(trim($this->j34_areausocomum) == null ){
         $this->erro_sql = " Campo Área Comum M2 nao Informado.";
         $this->erro_campo = "j34_areausocomum";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->j34_areaprivativa)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_areaprivativa"])){
       $sql  .= $virgula." j34_areaprivativa = $this->j34_areaprivativa ";
       $virgula = ",";
       if(trim($this->j34_areaprivativa) == null ){
         $this->erro_sql = " Campo Área Privativa M2 nao Informado.";
         $this->erro_campo = "j34_areaprivativa";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }

     if(trim($this->j34_zonadesurb) != null){
       $sql  .= $virgula." j34_zonadesurb = '$this->j34_zonadesurb' ";
       $virgula = ","; 
     }else{
       $sql  .= $virgula." j34_zonadesurb = null ";
       $virgula = ","; 
     }
       

     if(trim($this->j34_bairro)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_bairro"])){
       $sql  .= $virgula." j34_bairro = $this->j34_bairro ";
       $virgula = ",";
       if(trim($this->j34_bairro) == null ){
         $this->erro_sql = " Campo Cód. Bairro nao Informado.";
         $this->erro_campo = "j34_bairro";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j34_areal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_areal"])){
        if(trim($this->j34_areal)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j34_areal"])){
           $this->j34_areal = "0" ;
        }
       $sql  .= $virgula." j34_areal = $this->j34_areal ";
       $virgula = ",";
     }
     if(trim($this->j34_totcon)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_totcon"])){
        if(trim($this->j34_totcon)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j34_totcon"])){
           $this->j34_totcon = "0" ;
        }
       $sql  .= $virgula." j34_totcon = $this->j34_totcon ";
       $virgula = ",";
     }
     if(trim($this->j34_zona)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_zona"])){
       $sql  .= $virgula." j34_zona = $this->j34_zona ";
       $virgula = ",";
       if(trim($this->j34_zona) == null ){
         $this->erro_sql = " Campo Zona Fiscal nao Informado.";
         $this->erro_campo = "j34_zona";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j34_quamat)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_quamat"])){
        if(trim($this->j34_quamat)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j34_quamat"])){
           $this->j34_quamat = "0" ;
        }
       $sql  .= $virgula." j34_quamat = $this->j34_quamat ";
       $virgula = ",";
     }
     if(trim($this->j34_areapreservada)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j34_areapreservada"])){
        if(trim($this->j34_areapreservada)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j34_areapreservada"])){
           $this->j34_areapreservada = "0" ;
        }
       $sql  .= $virgula." j34_areapreservada = $this->j34_areapreservada ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($j34_idbql!=null){
       $sql .= " j34_idbql = $this->j34_idbql";
     }
     $resaco = $this->sql_record($this->sql_query_file($this->j34_idbql));
     if($this->numrows>0){
       for($conresaco=0;$conresaco<$this->numrows;$conresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,79,'$this->j34_idbql','A')");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_idbql"]) || $this->j34_idbql != "")
           $resac = db_query("insert into db_acount values($acount,19,79,'".AddSlashes(pg_result($resaco,$conresaco,'j34_idbql'))."','$this->j34_idbql',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_setor"]) || $this->j34_setor != "")
           $resac = db_query("insert into db_acount values($acount,19,80,'".AddSlashes(pg_result($resaco,$conresaco,'j34_setor'))."','$this->j34_setor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_quadra"]) || $this->j34_quadra != "")
           $resac = db_query("insert into db_acount values($acount,19,81,'".AddSlashes(pg_result($resaco,$conresaco,'j34_quadra'))."','$this->j34_quadra',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_lote"]) || $this->j34_lote != "")
           $resac = db_query("insert into db_acount values($acount,19,82,'".AddSlashes(pg_result($resaco,$conresaco,'j34_lote'))."','$this->j34_lote',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_area"]) || $this->j34_area != "")
           $resac = db_query("insert into db_acount values($acount,19,83,'".AddSlashes(pg_result($resaco,$conresaco,'j34_area'))."','$this->j34_area',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_bairro"]) || $this->j34_bairro != "")
           $resac = db_query("insert into db_acount values($acount,19,84,'".AddSlashes(pg_result($resaco,$conresaco,'j34_bairro'))."','$this->j34_bairro',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_areal"]) || $this->j34_areal != "")
           $resac = db_query("insert into db_acount values($acount,19,85,'".AddSlashes(pg_result($resaco,$conresaco,'j34_areal'))."','$this->j34_areal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_totcon"]) || $this->j34_totcon != "")
           $resac = db_query("insert into db_acount values($acount,19,2379,'".AddSlashes(pg_result($resaco,$conresaco,'j34_totcon'))."','$this->j34_totcon',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_zona"]) || $this->j34_zona != "")
           $resac = db_query("insert into db_acount values($acount,19,2380,'".AddSlashes(pg_result($resaco,$conresaco,'j34_zona'))."','$this->j34_zona',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_quamat"]) || $this->j34_quamat != "")
           $resac = db_query("insert into db_acount values($acount,19,2533,'".AddSlashes(pg_result($resaco,$conresaco,'j34_quamat'))."','$this->j34_quamat',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_areapreservada"]) || $this->j34_areapreservada != "")
           $resac = db_query("insert into db_acount values($acount,19,15148,'".AddSlashes(pg_result($resaco,$conresaco,'j34_areapreservada'))."','$this->j34_areapreservada',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_areausocomum"]) || $this->j34_areausocomum != "")
           $resac = db_query("insert into db_acount values($acount,19,1015682,'".AddSlashes(pg_result($resaco,$conresaco,'j34_areausocomum'))."','$this->j34_areausocomum',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_areaprivativa"]) || $this->j34_areaprivativa != "")
           $resac = db_query("insert into db_acount values($acount,19,1015681,'".AddSlashes(pg_result($resaco,$conresaco,'j34_areaprivativa'))."','$this->j34_areaprivativa',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         if(isset($GLOBALS["HTTP_POST_VARS"]["j34_zonadesurb"]) || $this->j34_zonadesurb != "")
           $resac = db_query("insert into db_acount values($acount,19,1015683,'".AddSlashes(pg_result($resaco,$conresaco,'j34_zonadesurb'))."','$this->j34_zonadesurb',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");

          

        }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Lotes nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->j34_idbql;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Lotes nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->j34_idbql;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j34_idbql;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($j34_idbql=null,$dbwhere=null) {
     if($dbwhere==null || $dbwhere==""){
       $resaco = $this->sql_record($this->sql_query_file($j34_idbql));
     }else{
       $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
     }
     if(($resaco!=false)||($this->numrows!=0)){
       for($iresaco=0;$iresaco<$this->numrows;$iresaco++){
         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,79,'$j34_idbql','E')");
         $resac = db_query("insert into db_acount values($acount,19,79,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_idbql'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,80,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_setor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,81,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_quadra'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,82,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_lote'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,83,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_area'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,84,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_bairro'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,85,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_areal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,2379,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_totcon'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,2380,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_zona'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,2533,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_quamat'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,15148,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_areapreservada'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,1015681,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_areaprivativa'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,1015682,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_areausocomum'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,19,1015683,'','".AddSlashes(pg_result($resaco,$iresaco,'j34_zonadesurb'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     $sql = " delete from lote
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($j34_idbql != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " j34_idbql = $j34_idbql ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Lotes nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$j34_idbql;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Lotes nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$j34_idbql;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$j34_idbql;
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
        $this->erro_sql   = "Record Vazio na Tabela:lote";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql
   public function sql_query ( $j34_idbql=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from lote ";
     $sql .= "      inner join bairro  on  bairro.j13_codi = lote.j34_bairro";
     $sql .= "      inner join setor  on  setor.j30_codi = lote.j34_setor";
     $sql .= "      inner join zonas  on  zonas.j50_zona = lote.j34_zona";
     $sql2 = "";
     if($dbwhere==""){
       if($j34_idbql!=null ){
         $sql2 .= " where lote.j34_idbql = $j34_idbql ";
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
   public function sql_query_file ( $j34_idbql=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from lote ";
     $sql2 = "";
     if($dbwhere==""){
       if($j34_idbql!=null ){
         $sql2 .= " where lote.j34_idbql = $j34_idbql ";
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
   public function sql_query_refant ( $j34_idbql=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from lote                                                                \n";
     $sql .= " inner join iptubase        on iptubase.j01_idbql  = lote.j34_idbql       \n";
     $sql .= " inner join cgm             on z01_numcgm          = j01_numcgm           \n";
     $sql .= " left outer join iptuconstr on j01_matric          = j39_matric           \n";
     $sql .= "                           and j39_idprinc is true                        \n";
     $sql .= " left outer join testpri    on j49_idbql           = j01_idbql            \n";
     $sql .= " left outer join ruas       on j14_codigo          = j49_codigo           \n";
     $sql .= " left outer join iptuant    on iptubase.j01_matric = iptuant.j40_matric   \n";
     $sql .= " left outer join loteloc    on j06_idbql           = j34_idbql            \n";
     $sql2 = "";
     if($dbwhere==""){
       if($j34_idbql!=null ){
         $sql2 .= " where lote.j34_idbql = $j34_idbql ";
       }
     }else if($dbwhere != ""){
       $sql2 = " where $dbwhere";
     }
     $sql .= $sql2;
     if($ordem != null ){
       $sql .= " order by ";
       $campos_sql = explode("#",$ordem);
       $virgula = "";
       $virgula = "";
       for($i=0;$i<sizeof($campos_sql);$i++){
         $sql .= $virgula.$campos_sql[$i];
         $virgula = ",";
       }
    }
    return $sql;
  }

  public function sql_query_loteloc($j34_idbql=null,$campos="*",$ordem=null,$dbwhere=""){
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
    $sql .= " from lote ";
    $sql .= "      inner join bairro  on  bairro.j13_codi  = lote.j34_bairro";
    $sql .= "      inner join setor   on  setor.j30_codi   = lote.j34_setor";
    $sql .= "      inner join zonas   on  zonas.j50_zona   = lote.j34_zona";
    $sql .= "       left join loteloc on loteloc.j06_idbql = lote.j34_idbql";
    $sql .= "       left join setorloc on setorloc.j05_codigo = loteloc.j06_setorloc ";
    $sql2 = "";

    if($dbwhere==""){
      if($j34_idbql!=null ){
        $sql2 .= " where lote.j34_idbql = $j34_idbql ";
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

  public function sql_query_lote($iCodigoLote) {

    $sSql  = "select lote.j34_setor,                                                      ";
    $sSql .= "       setor.j30_descr,                                                     ";
    $sSql .= "       lote.j34_quadra,                                                     ";
    $sSql .= "       lote.j34_lote,                                                       ";
    $sSql .= "       lote.j34_area,                                                       ";
    $sSql .= "       lote.j34_bairro,                                                     ";
    $sSql .= "       bairro.j13_descr,                                                    ";
    $sSql .= "       lote.j34_areal,                                                      ";
    $sSql .= "       lote.j34_totcon,                                                     ";
    $sSql .= "       lote.j34_zona,                                                       ";
    $sSql .= "       lote.j34_quamat,                                                     ";
    $sSql .= "       lote.j34_areapreservada,                                             ";
    $sSql .= "       lote.j34_areaprivativa,                                              ";
    $sSql .= "       lote.j34_areausocomum,                                               ";
    $sSql .= "       lote.j34_zonadesurb,                                                 ";
    $sSql .= "       lote.j34_idbql,                                                      ";
    $sSql .= "       ruas.j14_codigo,                                                     ";
    $sSql .= "       ruas.j14_nome,                                                       ";
    $sSql .= "       ruascep.j29_cep,                                                     ";
    $sSql .= "       testada.j36_testad,                                                  ";
    $sSql .= "       loteam.j34_loteam,                                                   ";
    $sSql .= "       loteam.j34_descr,                                                    ";
    $sSql .= "       ruastipo.j88_codigo,                                                 ";
    $sSql .= "       ruastipo.j88_sigla            																				";
    $sSql .= "  from lote                                                                 ";
    $sSql .= " inner join setor         on setor.j30_codi          = lote.j34_setor       ";
    $sSql .= " inner join bairro        on bairro.j13_codi         = lote.j34_bairro      ";
    $sSql .= " inner join testada       on testada.j36_idbql       = lote.j34_idbql       ";
    $sSql .= " inner join testpri       on testpri.j49_idbql       = testada.j36_idbql    ";
    $sSql .= "                         and testpri.j49_face        = testada.j36_face     ";
    $sSql .= "  left join testadanumero on testadanumero.j15_idbql = testpri.j49_idbql    ";
    $sSql .= " inner join ruas          on ruas.j14_codigo         = testpri.j49_codigo   ";
    $sSql .= "  left join ruastipo      on ruastipo.j88_codigo     = ruas.j14_tipo        ";
    $sSql .= "  left join ruascep       on ruascep.j29_codigo      = ruas.j14_codigo      ";
    $sSql .= "  left join loteloteam    on loteloteam.j34_idbql    = lote.j34_idbql       ";
    $sSql .= "  left join loteam        on loteam.j34_loteam       = loteloteam.j34_loteam";
    $sSql .= " where lote.j34_idbql = {$iCodigoLote}                                      ";

    return $sSql;
  }

  public function sql_queryGeodados() {

    $sSql  = "select distinct j01_idbql         ";
    $sSql .= "  from iptubase                                ";
    $sSql .= " inner join lote    on j34_idbql  = j01_idbql  ";
    $sSql .= " inner join bairro  on j34_bairro = j13_codi   ";
    $sSql .= " inner join testada on j36_idbql  = j34_idbql  ";
    $sSql .= " inner join testpri on j49_idbql  = j36_idbql  ";
    $sSql .= "                   and j49_face   = j36_face   ";
    $sSql .= " inner join ruas    on j14_codigo = j49_codigo ";
    $sSql .= " where j01_baixa is null                  ";

    return $sSql;

  }


  public function sql_query_dados_lote ($j34_idbql = null, $campos = "*", $ordem = null, $dbwhere = "") {

    $sql = "select ";
    if ($campos != "*" ) {

      $campos_sql = explode("#",$campos);
      $virgula    = "";
      for ($i = 0; $i < sizeof($campos_sql); $i++) {

        $sql     .= $virgula.$campos_sql[$i];
        $virgula  = ",";
      }
    } else {
      $sql .= $campos;
    }
    $sql .= " from lote                                                                  ";
    $sql .= " inner join iptubase           on iptubase.j01_idbql  = lote.j34_idbql      ";
    $sql .= " inner join cgm                on z01_numcgm          = j01_numcgm          ";
    $sql .= " left outer join iptuconstr    on j01_matric          = j39_matric          ";
    $sql .= "                              and j39_idprinc is true                       ";
    $sql .= " left outer join testpri       on j49_idbql           = j01_idbql           ";
    $sql .= " left outer join ruas          on j14_codigo          = j49_codigo          ";
    $sql .= " left outer join iptuant       on iptubase.j01_matric = iptuant.j40_matric  ";
    $sql .= " left outer join loteloc       on j06_idbql           = j34_idbql           ";
    $sql .= " left outer join testada       on j36_idbql           = j34_idbql           ";
    $sql .= " left outer join testadanumero on j15_idbql           = j49_idbql           ";
    $sql .= "                              and j15_face            = j49_face            ";

    $sql2 = "";
    if ($dbwhere == "") {

      if ($j34_idbql != null) {
        $sql2 .= " where lote.j34_idbql = $j34_idbql ";
      }
    } else if($dbwhere != "") {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if ($ordem != null ) {

      $sql       .= " order by ";
      $campos_sql = explode("#",$ordem);
      $virgula    = "";
      $virgula    = "";
      for ($i = 0; $i < sizeof($campos_sql); $i++) {

        $sql    .= $virgula.$campos_sql[$i];
        $virgula = ",";
      }
    }
    return $sql;
  }

  public function sql_query_construcoesLote($lote, $codigosAreaIrregular = [])
  {
    $sqlBuscaAreaIrregular = "true";

    if (count($codigosAreaIrregular) > 0) {
      $codigosCaracteristicas = implode(',',$codigosAreaIrregular);
      $sqlBuscaAreaIrregular = "(
        (
            select
                count(j48_caract)
            from
                carconstr
                inner join caracter ON caracter.j31_codigo = carconstr.j48_caract
            where
                carconstr.j48_idcons = iptuconstr.j39_idcons
                and carconstr.j48_matric = iptuconstr.j39_matric
                and j48_caract in ($codigosCaracteristicas)
          ) = 0
        )";
    }

    $sql = "
    select
        $sqlBuscaAreaIrregular as regular,
        iptubase.j01_matric,
        iptuconstr.*
    from
        lote
        inner join iptubase ON iptubase.j01_idbql = lote.j34_idbql
        inner join iptuconstr ON iptuconstr.j39_matric = iptubase.j01_matric
    where
        iptubase.j01_baixa is null
        and iptuconstr.j39_dtdemo is null
        and lote.j34_idbql = $lote
    ";

    return $sql;
  }
}
?>
