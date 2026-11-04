<?php
/**
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

//MODULO: recursoshumanos
//CLASSE DA ENTIDADE portaria
class cl_portaria {
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
   public $h31_sequencial = 0;
   public $h31_portariatipo = 0;
   public $h31_usuario = 0;
   public $h31_numero = null;
   public $h31_anousu = 0;
   public $h31_dtportaria_dia = null;
   public $h31_dtportaria_mes = null;
   public $h31_dtportaria_ano = null;
   public $h31_dtportaria = null;
   public $h31_dtinicio_dia = null;
   public $h31_dtinicio_mes = null;
   public $h31_dtinicio_ano = null;
   public $h31_dtinicio = null;
   public $h31_dtlanc_dia = null;
   public $h31_dtlanc_mes = null;
   public $h31_dtlanc_ano = null;
   public $h31_dtlanc = null;
   public $h31_amparolegal = null;
   public $h31_portariaassinatura = null;
   public $h31_portaria = null;
   public $h31_status = null;
   // cria propriedade com as variaveis do arquivo
   public $campos = "
                 h31_sequencial = int8 = Sequencial
                 h31_portariatipo = int8 = Tipo
                 h31_usuario = int4 = Usuário
                 h31_numero = varchar(10) = Número
                 h31_anousu = int4 = Ano
                 h31_dtportaria = date = Data da Portaria
                 h31_dtinicio = date = Data inicial
                 h31_dtlanc = date = Data de lançamento
                 h31_amparolegal = text = Amparo legal
                 h31_portariaassinatura = int4 = Código
                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("portaria");
     $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
   }
   //funcao erro
   public function erro($mostra,$retorna) {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\");</script>";
        if($retorna){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }
   // funcao para atualizar campos
    public function atualizacampos($exclusao=false) {
     if(!$exclusao) {
            $this->h31_portaria = ($this->h31_portaria != null) ? $this->h31_portaria : (($GLOBALS["h31_portariaseq"] == "") ? null : $GLOBALS["h31_portariaseq"]);
            $this->h31_status = ($this->h31_status != null) ? $this->h31_status : (isset($_POST['h31_status']) && $_POST['h31_status'] != "" ? $_POST['h31_status'] : null);
            $this->h31_sequencial = ($this->h31_sequencial != "") ? $this->h31_sequencial : (isset($_POST["h31_sequencial"]) ? $_POST["h31_sequencial"] : $this->h31_sequencial);
            $this->h31_portariatipo = ($this->h31_portariatipo != "") ? $this->h31_portariatipo : (isset($_POST["h31_portariatipo"]) ? $_POST["h31_portariatipo"] : $this->h31_portariatipo);
            $this->h31_usuario = ($this->h31_usuario != "") ? $this->h31_usuario : (isset($_POST["h31_usuario"]) ? $_POST["h31_usuario"] : $this->h31_usuario);
            $this->h31_numero = ($this->h31_numero != "") ? $this->h31_numero : (isset($_POST["h31_numero"]) ? $_POST["h31_numero"] : $this->h31_numero);
            $this->h31_anousu = ($this->h31_anousu != "") ? $this->h31_anousu : (isset($_POST["h31_anousu"]) ? $_POST["h31_anousu"] : $this->h31_anousu);
            if ($this->h31_dtportaria == "") {
                $this->h31_dtportaria_dia = ($this->h31_dtportaria_dia != "") ? $this->h31_dtportaria_dia : (isset($_POST["h31_dtportaria_dia"]) ? $_POST["h31_dtportaria_dia"] : $this->h31_dtportaria_dia);
                $this->h31_dtportaria_mes = ($this->h31_dtportaria_mes != "") ? $this->h31_dtportaria_mes : (isset($_POST["h31_dtportaria_mes"]) ? $_POST["h31_dtportaria_mes"] : $this->h31_dtportaria_mes);
                $this->h31_dtportaria_ano = ($this->h31_dtportaria_ano != "") ? $this->h31_dtportaria_ano : (isset($_POST["h31_dtportaria_ano"]) ? $_POST["h31_dtportaria_ano"] : $this->h31_dtportaria_ano);

                if ($this->h31_dtportaria_dia != "") {
                    $this->h31_dtportaria = $this->h31_dtportaria_ano . "-" . $this->h31_dtportaria_mes . "-" . $this->h31_dtportaria_dia;
                }
            }

            if ($this->h31_dtinicio == "") {
                $this->h31_dtinicio_dia = ($this->h31_dtinicio_dia != "") ? $this->h31_dtinicio_dia : (isset($_POST["h31_dtinicio_dia"]) ? $_POST["h31_dtinicio_dia"] : $this->h31_dtinicio_dia);
                $this->h31_dtinicio_mes = ($this->h31_dtinicio_mes != "") ? $this->h31_dtinicio_mes : (isset($_POST["h31_dtinicio_mes"]) ? $_POST["h31_dtinicio_mes"] : $this->h31_dtinicio_mes);
                $this->h31_dtinicio_ano = ($this->h31_dtinicio_ano != "") ? $this->h31_dtinicio_ano : (isset($_POST["h31_dtinicio_ano"]) ? $_POST["h31_dtinicio_ano"] : $this->h31_dtinicio_ano);

                if ($this->h31_dtinicio_dia != "") {
                    $this->h31_dtinicio = $this->h31_dtinicio_ano . "-" . $this->h31_dtinicio_mes . "-" . $this->h31_dtinicio_dia;
                }
            }

            if ($this->h31_dtlanc == "") {
                $this->h31_dtlanc_dia = ($this->h31_dtlanc_dia != "") ? $this->h31_dtlanc_dia : (isset($_POST["h31_dtlanc_dia"]) ? $_POST["h31_dtlanc_dia"] : $this->h31_dtlanc_dia);
                $this->h31_dtlanc_mes = ($this->h31_dtlanc_mes != "") ? $this->h31_dtlanc_mes : (isset($_POST["h31_dtlanc_mes"]) ? $_POST["h31_dtlanc_mes"] : $this->h31_dtlanc_mes);
                $this->h31_dtlanc_ano = ($this->h31_dtlanc_ano != "") ? $this->h31_dtlanc_ano : (isset($_POST["h31_dtlanc_ano"]) ? $_POST["h31_dtlanc_ano"] : $this->h31_dtlanc_ano);

                if ($this->h31_dtlanc_dia != "") {
                    $this->h31_dtlanc = $this->h31_dtlanc_ano . "-" . $this->h31_dtlanc_mes . "-" . $this->h31_dtlanc_dia;
                }
            }

            $this->h31_amparolegal = ($this->h31_amparolegal != "") ? $this->h31_amparolegal : (isset($_POST["h31_amparolegal"]) ? $_POST["h31_amparolegal"] : $this->h31_amparolegal);
            $this->h31_portariaassinatura = ($this->h31_portariaassinatura != "") ? $this->h31_portariaassinatura : (isset($_POST["h31_portariaassinatura"]) ? $_POST["h31_portariaassinatura"] : $this->h31_portariaassinatura);
        } else {
            $this->h31_sequencial = ($this->h31_sequencial != "") ? $this->h31_sequencial : (isset($_POST["h31_sequencial"]) ? $_POST["h31_sequencial"] : $this->h31_sequencial);
        }
    }

    // funcao para inclusao
   public function incluir ($h31_sequencial){
      $this->atualizacampos();
       if (($this->h31_portaria == null) && ($this->h31_status != null)){
           $this->erro_sql = " Campo Portaria ou Status Não Foi Informado. Verifique por favor";
           $this->erro_campo = "h31_portaria";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuario: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
       }else{
           if (($this->h31_portaria != null) && ($this->h31_status == null)){
               $this->erro_sql = " Campo Portaria ou Status Não Foi Informado. Verifique por favor";
               $this->erro_campo = "h31_portaria";
               $this->erro_banco = "";
               $this->erro_msg   = "Usuario: \\n\\n ".$this->erro_sql." \\n\\n";
               $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
               $this->erro_status = "0";
               return false;
           }else{
               if(($this->h31_portaria != null) && ($this->h31_status != null)){
                   if($this->verifica_portaria($this->h31_portaria)){
                       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Portaria a ser Aulada ou Revogada não foi encontrada \\n\\n ".$this->erro_banco." \\n"));
                       $this->erro_status = "0";
                       return false;
                   }
               }
           }
       }
     if($this->h31_portariatipo == null ){
       $this->erro_sql = " Campo Tipo não informado.";
       $this->erro_campo = "h31_portariatipo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->h31_usuario == null ){
       $this->erro_sql = " Campo Usuário não informado.";
       $this->erro_campo = "h31_usuario";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->h31_numero == null ){
       $this->erro_sql = " Campo Número não informado.";
       $this->erro_campo = "h31_numero";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->h31_anousu == null ){
       $this->erro_sql = " Campo Ano não informado.";
       $this->erro_campo = "h31_anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->h31_dtportaria == null ){
       $this->erro_sql = " Campo Data da Portaria não informado.";
       $this->erro_campo = "h31_dtportaria_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->h31_dtinicio == null ){
       $this->erro_sql = " Campo Data inicial não informado.";
       $this->erro_campo = "h31_dtinicio_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->h31_dtlanc == null ){
       $this->erro_sql = " Campo Data de lançamento não informado.";
       $this->erro_campo = "h31_dtlanc_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($h31_sequencial == "" || $h31_sequencial == null ){
       $result = db_query("select nextval('portaria_h31_sequencial_seq')");
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: portaria_h31_sequencial_seq do campo: h31_sequencial";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
       $this->h31_sequencial = pg_fetch_result($result,0,0);
     }else{
       $result = db_query("select last_value from portaria_h31_sequencial_seq");
       if(($result != false) && (pg_fetch_result($result,0,0) < $h31_sequencial)){
         $this->erro_sql = " Campo h31_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->h31_sequencial = $h31_sequencial;
       }
     }
     if(($this->h31_sequencial == null) || ($this->h31_sequencial == "") ){
       $this->erro_sql = " Campo h31_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into portaria(
                                       h31_sequencial
                                      ,h31_portariatipo
                                      ,h31_usuario
                                      ,h31_numero
                                      ,h31_anousu
                                      ,h31_dtportaria
                                      ,h31_dtinicio
                                      ,h31_dtlanc
                                      ,h31_amparolegal
                                      ,h31_portariaassinatura
                       )
                values (
                                $this->h31_sequencial
                               ,$this->h31_portariatipo
                               ,$this->h31_usuario
                               ,'$this->h31_numero'
                               ,$this->h31_anousu
                               ,".($this->h31_dtportaria == "null" || $this->h31_dtportaria == ""?"null":"'".$this->h31_dtportaria."'")."
                               ,".($this->h31_dtinicio == "null" || $this->h31_dtinicio == ""?"null":"'".$this->h31_dtinicio."'")."
                               ,".($this->h31_dtlanc == "null" || $this->h31_dtlanc == ""?"null":"'".$this->h31_dtlanc."'")."
                               ,'$this->h31_amparolegal'
                               ," . ($this->h31_portariaassinatura == "" ? "null" : $this->h31_portariaassinatura) ."
                      )";
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Portaria ($this->h31_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Portaria já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Portaria ($this->h31_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->h31_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->h31_sequencial  ));
       if($resaco ||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_fetch_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,10118,'$this->h31_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1741,10118,'','".AddSlashes(pg_fetch_result($resaco,0,'h31_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1741,10119,'','".AddSlashes(pg_fetch_result($resaco,0,'h31_portariatipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1741,10120,'','".AddSlashes(pg_fetch_result($resaco,0,'h31_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1741,10121,'','".AddSlashes(pg_fetch_result($resaco,0,'h31_numero'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1741,10122,'','".AddSlashes(pg_fetch_result($resaco,0,'h31_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1741,10123,'','".AddSlashes(pg_fetch_result($resaco,0,'h31_dtportaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1741,10124,'','".AddSlashes(pg_fetch_result($resaco,0,'h31_dtinicio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1741,10125,'','".AddSlashes(pg_fetch_result($resaco,0,'h31_dtlanc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1741,10126,'','".AddSlashes(pg_fetch_result($resaco,0,'h31_amparolegal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1741,20484,'','".AddSlashes(pg_fetch_result($resaco,0,'h31_portariaassinatura'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   }
   // funcao para alteracao
   public function alterar ($h31_sequencial=null) {
      $this->atualizacampos();
     $sql = " update portaria set ";
     $virgula = "";
     if ($this->h31_sequencial != null) {
         $rs = $this->atividade_atual($this->h31_sequencial);
         //se a condicao for true significa que possui uma ativida
         //logo ele possui um andamento de documento
         if (pg_num_rows($rs) > 0) {
             $rs = pg_fetch_assoc($rs);
             if (!is_numeric(strpos($rs["p114_atividade"], 'Conferir'))) {
                 $this->erro_sql = "Portaria para poder ser alterada deve estar no Status de Gerado";
                 $this->erro_campo = "";
                 $this->erro_banco = "";
                 $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
                 $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
                 $this->erro_status = "0";
                 return false;
             }
         }
     }
     if(trim($this->h31_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["h31_sequencial"])){
       $sql  .= $virgula." h31_sequencial = $this->h31_sequencial ";
       $virgula = ",";
       if(trim($this->h31_sequencial) == null ){
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "h31_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->h31_portariatipo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["h31_portariatipo"])){
       $sql  .= $virgula." h31_portariatipo = $this->h31_portariatipo ";
       $virgula = ",";
       if(trim($this->h31_portariatipo) == null ){
         $this->erro_sql = " Campo Tipo não informado.";
         $this->erro_campo = "h31_portariatipo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->h31_usuario)!="" || isset($GLOBALS["HTTP_POST_VARS"]["h31_usuario"])){
       $sql  .= $virgula." h31_usuario = $this->h31_usuario ";
       $virgula = ",";
       if(trim($this->h31_usuario) == null ){
         $this->erro_sql = " Campo Usuário não informado.";
         $this->erro_campo = "h31_usuario";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->h31_numero)!="" || isset($GLOBALS["HTTP_POST_VARS"]["h31_numero"])){
       $sql  .= $virgula." h31_numero = '$this->h31_numero' ";
       $virgula = ",";
       if(trim($this->h31_numero) == null ){
         $this->erro_sql = " Campo Número não informado.";
         $this->erro_campo = "h31_numero";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->h31_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["h31_anousu"])){
       $sql  .= $virgula." h31_anousu = $this->h31_anousu ";
       $virgula = ",";
       if(trim($this->h31_anousu) == null ){
         $this->erro_sql = " Campo Ano não informado.";
         $this->erro_campo = "h31_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->h31_dtportaria)!="" || isset($GLOBALS["HTTP_POST_VARS"]["h31_dtportaria_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["h31_dtportaria_dia"] !="") ){
       $sql  .= $virgula." h31_dtportaria = '$this->h31_dtportaria' ";
       $virgula = ",";
       if(trim($this->h31_dtportaria) == null ){
         $this->erro_sql = " Campo Data da Portaria não informado.";
         $this->erro_campo = "h31_dtportaria_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["h31_dtportaria_dia"])){
         $sql  .= $virgula." h31_dtportaria = null ";
         $virgula = ",";
         if(trim($this->h31_dtportaria) == null ){
           $this->erro_sql = " Campo Data da Portaria não informado.";
           $this->erro_campo = "h31_dtportaria_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->h31_dtinicio)!="" || isset($GLOBALS["HTTP_POST_VARS"]["h31_dtinicio_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["h31_dtinicio_dia"] !="") ){
       $sql  .= $virgula." h31_dtinicio = '$this->h31_dtinicio' ";
       $virgula = ",";
       if(trim($this->h31_dtinicio) == null ){
         $this->erro_sql = " Campo Data inicial não informado.";
         $this->erro_campo = "h31_dtinicio_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["h31_dtinicio_dia"])){
         $sql  .= $virgula." h31_dtinicio = null ";
         $virgula = ",";
         if(trim($this->h31_dtinicio) == null ){
           $this->erro_sql = " Campo Data inicial não informado.";
           $this->erro_campo = "h31_dtinicio_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->h31_dtlanc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["h31_dtlanc_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["h31_dtlanc_dia"] !="") ){
       $sql  .= $virgula." h31_dtlanc = '$this->h31_dtlanc' ";
       $virgula = ",";
       if(trim($this->h31_dtlanc) == null ){
         $this->erro_sql = " Campo Data de lançamento não informado.";
         $this->erro_campo = "h31_dtlanc_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{
       if(isset($GLOBALS["HTTP_POST_VARS"]["h31_dtlanc_dia"])){
         $sql  .= $virgula." h31_dtlanc = null ";
         $virgula = ",";
         if(trim($this->h31_dtlanc) == null ){
           $this->erro_sql = " Campo Data de lançamento não informado.";
           $this->erro_campo = "h31_dtlanc_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->h31_amparolegal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["h31_amparolegal"])){
       $sql  .= $virgula." h31_amparolegal = '$this->h31_amparolegal' ";
       $virgula = ",";
     }
     if(trim($this->h31_portariaassinatura)!="" || isset($GLOBALS["HTTP_POST_VARS"]["h31_portariaassinatura"])){
        if(trim($this->h31_portariaassinatura)=="" && isset($GLOBALS["HTTP_POST_VARS"]["h31_portariaassinatura"])){
          $this->h31_portariaassinatura = "null";
        }
       $sql  .= $virgula." h31_portariaassinatura = $this->h31_portariaassinatura ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($h31_sequencial!=null){
       $sql .= " h31_sequencial = $this->h31_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->h31_sequencial));
       if($this->numrows>0){

         for($conresaco=0;$conresaco<$this->numrows;$conresaco++){

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_fetch_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,10118,'$this->h31_sequencial','A')");
           if(isset($GLOBALS["HTTP_POST_VARS"]["h31_sequencial"]) || $this->h31_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1741,10118,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'h31_sequencial'))."','$this->h31_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["h31_portariatipo"]) || $this->h31_portariatipo != "")
             $resac = db_query("insert into db_acount values($acount,1741,10119,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'h31_portariatipo'))."','$this->h31_portariatipo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["h31_usuario"]) || $this->h31_usuario != "")
             $resac = db_query("insert into db_acount values($acount,1741,10120,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'h31_usuario'))."','$this->h31_usuario',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["h31_numero"]) || $this->h31_numero != "")
             $resac = db_query("insert into db_acount values($acount,1741,10121,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'h31_numero'))."','$this->h31_numero',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["h31_anousu"]) || $this->h31_anousu != "")
             $resac = db_query("insert into db_acount values($acount,1741,10122,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'h31_anousu'))."','$this->h31_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["h31_dtportaria"]) || $this->h31_dtportaria != "")
             $resac = db_query("insert into db_acount values($acount,1741,10123,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'h31_dtportaria'))."','$this->h31_dtportaria',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["h31_dtinicio"]) || $this->h31_dtinicio != "")
             $resac = db_query("insert into db_acount values($acount,1741,10124,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'h31_dtinicio'))."','$this->h31_dtinicio',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["h31_dtlanc"]) || $this->h31_dtlanc != "")
             $resac = db_query("insert into db_acount values($acount,1741,10125,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'h31_dtlanc'))."','$this->h31_dtlanc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["h31_amparolegal"]) || $this->h31_amparolegal != "")
             $resac = db_query("insert into db_acount values($acount,1741,10126,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'h31_amparolegal'))."','$this->h31_amparolegal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if(isset($GLOBALS["HTTP_POST_VARS"]["h31_portariaassinatura"]) || $this->h31_portariaassinatura != "")
             $resac = db_query("insert into db_acount values($acount,1741,20484,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'h31_portariaassinatura'))."','$this->h31_portariaassinatura',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Portaria nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->h31_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Portaria nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->h31_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->h31_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }
   // funcao para exclusao
   public function excluir ($h31_sequencial=null,$dbwhere=null) {

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if ($dbwhere==null || $dbwhere=="") {

         $resaco = $this->sql_record($this->sql_query_file($h31_sequencial));
       } else {
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if ($resaco || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_fetch_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,10118,'$h31_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1741,10118,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'h31_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1741,10119,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'h31_portariatipo'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1741,10120,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'h31_usuario'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1741,10121,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'h31_numero'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1741,10122,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'h31_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1741,10123,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'h31_dtportaria'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1741,10124,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'h31_dtinicio'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1741,10125,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'h31_dtlanc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1741,10126,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'h31_amparolegal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1741,20484,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'h31_portariaassinatura'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from portaria
                    where ";
     $sql2 = "";
     if($dbwhere==null || $dbwhere ==""){
        if($h31_sequencial != ""){
          if($sql2!=""){
            $sql2 .= " and ";
          }
          $sql2 .= " h31_sequencial = $h31_sequencial ";
        }
     }else{
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if($result==false){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Portaria nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$h31_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "Portaria nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$h31_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$h31_sequencial;
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
     if(!$result){
       $this->numrows    = 0;
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Erro ao selecionar os registros.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $this->numrows = pg_num_rows($result);
      if($this->numrows==0){
        $this->erro_banco = "";
        $this->erro_sql   = "Record Vazio na Tabela:portaria";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   public function sql_query ( $h31_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from portaria ";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = portaria.h31_usuario";
     $sql .= "      inner join portariatipo  on  portariatipo.h30_sequencial = portaria.h31_portariatipo";
     $sql .= "      left  join portariaassinatura  on  portariaassinatura.rh136_sequencial = portaria.h31_portariaassinatura";
     $sql .= "      inner join tipoasse  on  tipoasse.h12_codigo = portariatipo.h30_tipoasse";
     $sql .= "      inner join portariaenvolv  on  portariaenvolv.h42_sequencial = portariatipo.h30_portariaenvolv";
     $sql .= "      inner join portariatipoato  on  portariatipoato.h41_sequencial = portariatipo.h30_portariatipoato";
     $sql .= "      inner join portariaproced  on  portariaproced.h40_sequencial = portariatipo.h30_portariaproced";
     $sql .= "      left  join portariaassenta on portariaassenta.h33_portaria = portaria.h31_sequencial        ";
     $sql .= "      left  join assenta       on assenta.h16_codigo       = portariaassenta.h33_assenta    ";
     $sql .= "      left  join assentamentosubstituicao  on  assentamentosubstituicao.rh161_assentamento = assenta.h16_codigo";
     $sql2 = "";
     if($dbwhere==""){
       if($h31_sequencial!=null ){
         $sql2 .= " where portaria.h31_sequencial = $h31_sequencial ";
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
   public function sql_query_asse( $h31_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from portaria ";
     $sql .= "      inner join portariaassenta on portariaassenta.h33_portaria = portaria.h31_sequencial     		";
     $sql .= "      inner join assenta 		   on assenta.h16_codigo 		   = portariaassenta.h33_assenta 		";
     $sql .= "      inner join db_usuarios     on db_usuarios.id_usuario       = portaria.h31_usuario			    ";
     $sql .= "      inner join portariatipo    on portariatipo.h30_sequencial  = portaria.h31_portariatipo	  		";
     $sql .= "      inner join tipoasse  	   on tipoasse.h12_codigo          = portariatipo.h30_tipoasse			";

     $sql2 = "";
     if($dbwhere==""){
       if($h31_sequencial!=null ){
         $sql2 .= " where portaria.h31_sequencial = $h31_sequencial ";
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

   public function sql_query_asse_func( $h31_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from portaria ";
     $sql .= "      inner join portariaassenta on portariaassenta.h33_portaria = portaria.h31_sequencial     		";
     $sql .= "      inner join assenta 		   on assenta.h16_codigo 		   = portariaassenta.h33_assenta 		";
     $sql .= "      inner join rhpessoal       on rhpessoal.rh01_regist		   = assenta.h16_regist  	 	 		";
     $sql .= "      inner join cgm             on cgm.z01_numcgm  			   = rhpessoal.rh01_numcgm	 	 		";
     $sql .= "      inner join db_usuarios     on db_usuarios.id_usuario       = portaria.h31_usuario			    ";
     $sql .= "      inner join portariatipo    on portariatipo.h30_sequencial  = portaria.h31_portariatipo	  		";
     $sql .= "      inner join tipoasse  	   on tipoasse.h12_codigo          = portariatipo.h30_tipoasse			";

     $sql2 = "";
     if($dbwhere==""){
       if($h31_sequencial!=null ){
         $sql2 .= " where portaria.h31_sequencial = $h31_sequencial ";
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
   public function sql_query_file ( $h31_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){
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
     $sql .= " from portaria ";
     $sql2 = "";
     if($dbwhere==""){
       if($h31_sequencial!=null ){
         $sql2 .= " where portaria.h31_sequencial = $h31_sequencial ";
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

  public function sql_query_assentamento_funcional ( $h31_sequencial=null,$campos="*",$ordem=null,$dbwhere="", $sVerificaLotacao = null, $iTipoFuncionamento = null){
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
    $sql .= " from portaria ";
    $sql .= "      inner join db_usuarios              on db_usuarios.id_usuario                      = portaria.h31_usuario";
    $sql .= "      inner join portariatipo             on portariatipo.h30_sequencial                 = portaria.h31_portariatipo";
    $sql .= "      left  join portariaassinatura       on portariaassinatura.rh136_sequencial         = portaria.h31_portariaassinatura";
    $sql .= "      inner join tipoasse                 on tipoasse.h12_codigo                         = portariatipo.h30_tipoasse";
    $sql .= "      inner join portariaenvolv           on portariaenvolv.h42_sequencial               = portariatipo.h30_portariaenvolv";
    $sql .= "      inner join portariatipoato          on portariatipoato.h41_sequencial              = portariatipo.h30_portariatipoato";
    $sql .= "      inner join portariaproced           on portariaproced.h40_sequencial               = portariatipo.h30_portariaproced";
    $sql .= "      left  join portariaassenta          on portariaassenta.h33_portaria                = portaria.h31_sequencial        ";
    $sql .= "      left  join assenta                  on assenta.h16_codigo                          = portariaassenta.h33_assenta    ";
    $sql .= "      inner join assentamentofuncional    on rh193_assentamento_funcional                = assenta.h16_codigo ";
    $sql .= "      left  join assentamentosubstituicao on assentamentosubstituicao.rh161_assentamento = assenta.h16_codigo";

    $sql .= "
      LEFT JOIN documentoportaria
          ON documentoportaria.rh235_portaria = portaria.h31_sequencial
      LEFT JOIN arquivoestorage
          ON db177_idestorage = rh235_documento and db177_idestorage_arquivoanterior IS NULL
    ";

    $sql .= " LEFT JOIN documentos_andamento on documentos_andamento.p116_codigo_origem = portaria.h31_sequencial ";
    $sql .= " LEFT JOIN protprocessodocumento ON protprocessodocumento.p01_sequencial = documentos_andamento.p116_protprocessodocumento ";

    $sql2 = "";
    if($dbwhere==""){
      if($h31_sequencial!=null ){
        $sql2 .= " where portaria.h31_sequencial = $h31_sequencial ";
      }
    }else if($dbwhere != ""){
      $sql2 = " where $dbwhere";
    }
    if($iTipoFuncionamento == 1){ //Assentamentos de efetividade
      $sql2 .= " AND assentamentofuncional.rh193_assentamento_funcional is null";
    }
    if($iTipoFuncionamento == 2){ //Assentamentos de vida funcional
      $sql2 .= " AND assentamentofuncional.rh193_assentamento_funcional is not null";
    }
    if(!empty($sVerificaLotacao)) {
      $sql2 .= $sVerificaLotacao;
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

  public function sql_query_assentamento_servidor($sequencialPortaria)
  {
    $sSql = "
        SELECT
            h16_codigo,
            h16_regist,
            h31_numero,
            h31_anousu
        FROM
            portaria
        INNER JOIN portariaassenta
            ON h33_portaria = h31_sequencial
        INNER JOIN assenta
            ON h16_codigo = h33_assenta
        WHERE
            portaria.h31_sequencial = {$sequencialPortaria}
    ";

    return $sSql;
  }

  public function verifica_portaria($h31_portaria, $opcao = 0)
  {
      if ($h31_portaria != null){
          $sql = "select * from portaria where h31_sequencial = ${h31_portaria}";
          $rs = $this->sql_record($sql);
          if($opcao == 0){
              if(!$rs){
                  return true;
              }else{
                  return false;
              }
          }else{
              return pg_fetch_assoc($rs);
          }
      }
  }

    public function altera_status_portaria()
    {
        $arrayResult = $this->verifica_portaria($this->h31_portaria, 1);
        $sql = "update portaria set h31_status =  '{$this->h31_status}',h31_portariaseq = {$this->h31_sequencial}
                where h31_sequencial = {$arrayResult["h31_sequencial"]}";
        $this->sql_record($sql);
    }

    //criar func para verificar se a portaria possui um andamento de documento vinculado a ela
    public function atividade_atual($sequencial)
    {
        $sql = "select distinct p116_descricao, p114_atividade FROM
                     documentos_andamento
                JOIN processo_atividadesexecucao proxima_atividade
                ON proxima_atividade.p118_codigo = documentos_andamento.p116_proxima_atividade
                JOIN processo_usuarios
                ON processo_usuarios.p119_protprocesso = proxima_atividade.p118_protprocesso
                AND proxima_atividade.p118_atividadesexecucao = processo_usuarios.p119_atividadeexecucao
                JOIN protprocesso
                ON  protprocesso.p58_codproc  = documentos_andamento.p116_protprocesso
                join atividadesexecucao on p114_codigo = p118_atividadesexecucao
                where p116_codigo_origem = ${sequencial}";
        return db_query($sql);
    }
}
