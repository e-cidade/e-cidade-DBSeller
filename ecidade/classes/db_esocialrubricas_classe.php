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

class cl_esocialrubricas
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
    public $eso26_sequencial = 0; 
    public $eso26_rubrica = 0; 
    public $eso26_instituicao = 0; 
    public $eso26_natureza = 0; 
    public $eso26_datainicial_dia = null; 
    public $eso26_datainicial_mes = null; 
    public $eso26_datainicial_ano = null; 
    public $eso26_datainicial = null; 
    public $eso26_datafinal_dia = null; 
    public $eso26_datafinal_mes = null; 
    public $eso26_datafinal_ano = null; 
    public $eso26_datafinal = null; 
    public $eso26_subgrupotce = null; 
    public $eso26_codinccp = null; 
    public $eso26_codincirrf = null; 
    public $eso26_codincfgts = null; 
    public $eso26_codinccprp = null; 
    public $eso26_tetoremun = 0; 
    public $eso26_codincpispasep = null; 
    public $eso26_tpproc = 0; 
    public $eso26_nrprocprocessocp = null; 
    public $eso26_extdecisao = 0; 
    public $eso26_codsuspprocessocp = null; 
    public $eso26_nrprocirrf = null; 
    public $eso26_codsuspirrf = null; 
    public $eso26_nrprocfgts = null; 
    public $eso26_nrprocpispasep = null; 
    public $eso26_codsusppispasep = null; 
   // cria propriedade com as variaveis do arquivo 
    public $campos = "
                 eso26_sequencial = int4 = Sequencial 
                 eso26_rubrica = bpchar(4) = Rubrica 
                 eso26_instituicao = int4 = Instituição 
                 eso26_natureza = int4 = Natureza 
                 eso26_datainicial = date = Início de validade 
                 eso26_datafinal = date = Fim de validade 
                 eso26_subgrupotce = varchar(2) = Subgrupo da Natureza 
                 eso26_codinccp = varchar(3) = Incidência de Contrib. Previdenciária 
                 eso26_codincirrf = varchar(5) = Incidência de IRRF 
                 eso26_codincfgts = varchar(3) = Incidência de FGTS 
                 eso26_codinccprp = varchar(3) = Incidência rubrica RPPS/regime militar 
                 eso26_tetoremun = bpchar(1) = Rubrica teto remuneratório específico 
                 eso26_codincpispasep = varchar(3) = Incidência de PIS/PASEP 
                 eso26_tpproc = int4 = Tipo de Processo 
                 eso26_nrprocprocessocp = varchar(21) = Número do Processo 
                 eso26_extdecisao = int4 = Extensão de Decisão 
                 eso26_codsuspprocessocp = varchar(14) = Código do indicativo da suspensão 
                 eso26_nrprocirrf = varchar(20) = Número do Processo IRRF 
                 eso26_codsuspirrf = varchar(14) = Código de Suspensão IRRF 
                 eso26_nrprocfgts = varchar(20) = Número do Processo FGTS 
                 eso26_nrprocpispasep = varchar(20) = Número do Processo PIS/PASEP 
                 eso26_codsusppispasep = varchar(14) = Código de Suspensão PIS/PASEP 
                 ";

    public function __construct()
    {
        $this->rotulo = new rotulo("esocialrubricas"); 
        $this->pagina_retorno = basename($_SERVER['PHP_SELF']);
    }

    public function erro($mostra, $retorna)
    {
     if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
        echo "<script>alert(\"".$this->erro_msg."\")</script>";
        if($retorna==true){
           echo "<script>location.href='".$this->pagina_retorno."'</script>";
        }
     }
   }

    public function atualizacampos($exclusao = false)
    {
     if($exclusao==false){
       $this->eso26_sequencial = ($this->eso26_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_sequencial"]:$this->eso26_sequencial);
       $this->eso26_rubrica = ($this->eso26_rubrica == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_rubrica"]:$this->eso26_rubrica);
       $this->eso26_instituicao = ($this->eso26_instituicao == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_instituicao"]:$this->eso26_instituicao);
       $this->eso26_natureza = ($this->eso26_natureza == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_natureza"]:$this->eso26_natureza);
       if($this->eso26_datainicial == ""){
         $this->eso26_datainicial_dia = ($this->eso26_datainicial_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_datainicial_dia"]:$this->eso26_datainicial_dia);
         $this->eso26_datainicial_mes = ($this->eso26_datainicial_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_datainicial_mes"]:$this->eso26_datainicial_mes);
         $this->eso26_datainicial_ano = ($this->eso26_datainicial_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_datainicial_ano"]:$this->eso26_datainicial_ano);
         if($this->eso26_datainicial_dia != ""){
            $this->eso26_datainicial = $this->eso26_datainicial_ano."-".$this->eso26_datainicial_mes."-".$this->eso26_datainicial_dia;
         }
       }
       if($this->eso26_datafinal == ""){
         $this->eso26_datafinal_dia = ($this->eso26_datafinal_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_datafinal_dia"]:$this->eso26_datafinal_dia);
         $this->eso26_datafinal_mes = ($this->eso26_datafinal_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_datafinal_mes"]:$this->eso26_datafinal_mes);
         $this->eso26_datafinal_ano = ($this->eso26_datafinal_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_datafinal_ano"]:$this->eso26_datafinal_ano);
         if($this->eso26_datafinal_dia != ""){
            $this->eso26_datafinal = $this->eso26_datafinal_ano."-".$this->eso26_datafinal_mes."-".$this->eso26_datafinal_dia;
         }
       }
       $this->eso26_subgrupotce = ($this->eso26_subgrupotce == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_subgrupotce"]:$this->eso26_subgrupotce);
       $this->eso26_codinccp = ($this->eso26_codinccp == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_codinccp"]:$this->eso26_codinccp);
       $this->eso26_codincirrf = ($this->eso26_codincirrf == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_codincirrf"]:$this->eso26_codincirrf);
       $this->eso26_codincfgts = ($this->eso26_codincfgts == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_codincfgts"]:$this->eso26_codincfgts);
       $this->eso26_codinccprp = ($this->eso26_codinccprp == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_codinccprp"]:$this->eso26_codinccprp);
       $this->eso26_tetoremun = ($this->eso26_tetoremun == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_tetoremun"]:$this->eso26_tetoremun);
       $this->eso26_codincpispasep = ($this->eso26_codincpispasep == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_codincpispasep"]:$this->eso26_codincpispasep);
       $this->eso26_tpproc = ($this->eso26_tpproc == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_tpproc"]:$this->eso26_tpproc);
       $this->eso26_nrprocprocessocp = ($this->eso26_nrprocprocessocp == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_nrprocprocessocp"]:$this->eso26_nrprocprocessocp);
       $this->eso26_extdecisao = ($this->eso26_extdecisao == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_extdecisao"]:$this->eso26_extdecisao);
       $this->eso26_codsuspprocessocp = ($this->eso26_codsuspprocessocp == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_codsuspprocessocp"]:$this->eso26_codsuspprocessocp);
       $this->eso26_nrprocirrf = ($this->eso26_nrprocirrf == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_nrprocirrf"]:$this->eso26_nrprocirrf);
       $this->eso26_codsuspirrf = ($this->eso26_codsuspirrf == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_codsuspirrf"]:$this->eso26_codsuspirrf);
       $this->eso26_nrprocfgts = ($this->eso26_nrprocfgts == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_nrprocfgts"]:$this->eso26_nrprocfgts);
       $this->eso26_nrprocpispasep = ($this->eso26_nrprocpispasep == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_nrprocpispasep"]:$this->eso26_nrprocpispasep);
       $this->eso26_codsusppispasep = ($this->eso26_codsusppispasep == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_codsusppispasep"]:$this->eso26_codsusppispasep);
     }else{
       $this->eso26_sequencial = ($this->eso26_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["eso26_sequencial"]:$this->eso26_sequencial);
     }
   }

    public function incluir($eso26_sequencial)
    {
      $this->atualizacampos();
     if($this->eso26_rubrica == null ){
       $this->erro_sql = " Campo Rubrica não informado.";
       $this->erro_campo = "eso26_rubrica";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->eso26_instituicao == null ){ 
       $this->erro_sql = " Campo Instituição não informado.";
       $this->erro_campo = "eso26_instituicao";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->eso26_datainicial == null ){ 
       $this->erro_sql = " Campo Início de validade não informado.";
       $this->erro_campo = "eso26_datainicial_dia";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       if($this->eso26_subgrupotce == null ){
            $this->eso26_subgrupotce = "";
        }
        if(trim($this->eso26_tpproc)==""){
            $this->eso26_tpproc = "null" ;
        }
        if(trim($this->eso26_extdecisao)==""){
            $this->eso26_extdecisao =  "null" ;
        }
       $this->eso26_sequencial = $eso26_sequencial;

     $sql = "insert into esocialrubricas(
                                       eso26_sequencial 
                                      ,eso26_rubrica 
                                      ,eso26_instituicao 
                                      ,eso26_natureza 
                                      ,eso26_datainicial 
                                      ,eso26_datafinal 
                                      ,eso26_subgrupotce 
                                      ,eso26_codinccp 
                                      ,eso26_codincirrf 
                                      ,eso26_codincfgts 
                                      ,eso26_codinccprp 
                                      ,eso26_tetoremun 
                                      ,eso26_codincpispasep 
                                      ,eso26_tpproc 
                                      ,eso26_nrprocprocessocp 
                                      ,eso26_extdecisao 
                                      ,eso26_codsuspprocessocp 
                                      ,eso26_nrprocirrf 
                                      ,eso26_codsuspirrf 
                                      ,eso26_nrprocfgts 
                                      ,eso26_nrprocpispasep 
                                      ,eso26_codsusppispasep 
                       )
                values (
                                " .($this->eso26_sequencial ? $this->eso26_sequencial : " nextval('esocialrubricas_eso26_sequencial_seq')")." 
                               ,'$this->eso26_rubrica' 
                               ,$this->eso26_instituicao 
                               ,$this->eso26_natureza 
                               ,".($this->eso26_datainicial == "null" || $this->eso26_datainicial == ""?"null":"'".$this->eso26_datainicial."'")." 
                               ,".($this->eso26_datafinal == "null" || $this->eso26_datafinal == ""?"null":"'".$this->eso26_datafinal."'")." 
                               ,'$this->eso26_subgrupotce' 
                               ,'$this->eso26_codinccp' 
                               ,'$this->eso26_codincirrf' 
                               ,'$this->eso26_codincfgts' 
                               ,'$this->eso26_codinccprp' 
                               ,'$this->eso26_tetoremun' 
                               ,'$this->eso26_codincpispasep' 
                               ,$this->eso26_tpproc 
                               ,'$this->eso26_nrprocprocessocp' 
                               ,$this->eso26_extdecisao 
                               ,'$this->eso26_codsuspprocessocp' 
                               ,'$this->eso26_nrprocirrf' 
                               ,'$this->eso26_codsuspirrf' 
                               ,'$this->eso26_nrprocfgts' 
                               ,'$this->eso26_nrprocpispasep' 
                               ,'$this->eso26_codsusppispasep' 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Informações da Rubrica ($this->eso26_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Informações da Rubrica já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        }else{
         $this->erro_sql   = "Informações da Rubrica ($this->eso26_sequencial) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->eso26_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->eso26_sequencial  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_fetch_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,1009986,'$this->eso26_sequencial','I')");
         $resac = db_query("insert into db_acount values($acount,1010325,1009986,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1009987,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_rubrica'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1009988,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_instituicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1009994,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_natureza'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1010003,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_datainicial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1010004,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_datafinal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1014222,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_subgrupotce'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1014722,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_codinccp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1014723,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_codincirrf'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1014724,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_codincfgts'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1014727,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_codinccprp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,1014729,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_tetoremun'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,83652111,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_codincpispasep'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,201051558,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_tpproc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,180347237,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_nrprocprocessocp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,115211890,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_extdecisao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,94563963,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_codsuspprocessocp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,290311487,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_nrprocirrf'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,53539259,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_codsuspirrf'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,52461618,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_nrprocfgts'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,169505090,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_nrprocpispasep'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,1010325,231361664,'','".AddSlashes(pg_fetch_result($resaco,0,'eso26_codsusppispasep'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 

    public function alterar($eso26_sequencial=null)
    {
      $this->atualizacampos();
     $sql = " update esocialrubricas set ";
     $virgula = "";
     if(trim($this->eso26_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_sequencial"])){ 
       $sql  .= $virgula." eso26_sequencial = $this->eso26_sequencial ";
       $virgula = ",";
       if(trim($this->eso26_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial não informado.";
         $this->erro_campo = "eso26_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->eso26_rubrica)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_rubrica"])){ 
       $sql  .= $virgula." eso26_rubrica = '$this->eso26_rubrica' ";
       $virgula = ",";
       if(trim($this->eso26_rubrica) == null ){ 
         $this->erro_sql = " Campo Rubrica não informado.";
         $this->erro_campo = "eso26_rubrica";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->eso26_instituicao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_instituicao"])){ 
       $sql  .= $virgula." eso26_instituicao = $this->eso26_instituicao ";
       $virgula = ",";
       if(trim($this->eso26_instituicao) == null ){ 
         $this->erro_sql = " Campo Instituição não informado.";
         $this->erro_campo = "eso26_instituicao";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->eso26_natureza)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_natureza"])){ 
        if(trim($this->eso26_natureza)=="" && isset($GLOBALS["HTTP_POST_VARS"]["eso26_natureza"])){ 
           $this->eso26_natureza = "0" ; 
        } 
       $sql  .= $virgula." eso26_natureza = $this->eso26_natureza ";
       $virgula = ",";
     }
     if(trim($this->eso26_datainicial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_datainicial_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["eso26_datainicial_dia"] !="") ){ 
       $sql  .= $virgula." eso26_datainicial = '$this->eso26_datainicial' ";
       $virgula = ",";
       if(trim($this->eso26_datainicial) == null ){ 
         $this->erro_sql = " Campo Início de validade não informado.";
         $this->erro_campo = "eso26_datainicial_dia";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }     else{ 
       if(isset($GLOBALS["HTTP_POST_VARS"]["eso26_datainicial_dia"])){ 
         $sql  .= $virgula." eso26_datainicial = null ";
         $virgula = ",";
         if(trim($this->eso26_datainicial) == null ){ 
           $this->erro_sql = " Campo Início de validade não informado.";
           $this->erro_campo = "eso26_datainicial_dia";
           $this->erro_banco = "";
           $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
           $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
           $this->erro_status = "0";
           return false;
         }
       }
     }
     if(trim($this->eso26_datafinal)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_datafinal_dia"]) &&  ($GLOBALS["HTTP_POST_VARS"]["eso26_datafinal_dia"] !="") ){ 
       $sql  .= $virgula." eso26_datafinal = '$this->eso26_datafinal' ";
       $virgula = ",";
     }     else{ 
       if(isset($GLOBALS["HTTP_POST_VARS"]["eso26_datafinal_dia"])){ 
         $sql  .= $virgula." eso26_datafinal = null ";
         $virgula = ",";
       }
     }
     if(trim($this->eso26_subgrupotce)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_subgrupotce"])){ 
       $sql  .= $virgula." eso26_subgrupotce = '$this->eso26_subgrupotce' ";
       $virgula = ",";
     }
     if(trim($this->eso26_codinccp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_codinccp"])){ 
       $sql  .= $virgula." eso26_codinccp = '$this->eso26_codinccp' ";
       $virgula = ",";
     }
     if(trim($this->eso26_codincirrf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_codincirrf"])){ 
       $sql  .= $virgula." eso26_codincirrf = '$this->eso26_codincirrf' ";
       $virgula = ",";
     }
     if(trim($this->eso26_codincfgts)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_codincfgts"])){ 
       $sql  .= $virgula." eso26_codincfgts = '$this->eso26_codincfgts' ";
       $virgula = ",";
     }
     if(trim($this->eso26_codinccprp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_codinccprp"])){ 
       $sql  .= $virgula." eso26_codinccprp = '$this->eso26_codinccprp' ";
       $virgula = ",";
     }
     if(trim($this->eso26_tetoremun)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_tetoremun"])){ 
       $sql  .= $virgula." eso26_tetoremun = '$this->eso26_tetoremun' ";
       $virgula = ",";
     }
     if(trim($this->eso26_codincpispasep)=="" || trim($this->eso26_codincpispasep) != "" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_codincpispasep"])){ 
       $sql  .= $virgula." eso26_codincpispasep = '$this->eso26_codincpispasep' ";
       $virgula = ",";
     }
     if(trim($this->eso26_tpproc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_tpproc"])){ 
        if(trim($this->eso26_tpproc)=="" && isset($GLOBALS["HTTP_POST_VARS"]["eso26_tpproc"])){ 
           $this->eso26_tpproc = "0" ; 
        } 
       $sql  .= $virgula." eso26_tpproc = $this->eso26_tpproc ";
       $virgula = ",";
     }
     if(trim($this->eso26_nrprocprocessocp)=="" || trim($this->eso26_nrprocprocessocp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_nrprocprocessocp"])){ 
       $sql  .= $virgula." eso26_nrprocprocessocp = '$this->eso26_nrprocprocessocp' ";
       $virgula = ",";
     }
     if(trim($this->eso26_extdecisao)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_extdecisao"])){ 
        if(trim($this->eso26_extdecisao)=="" && isset($GLOBALS["HTTP_POST_VARS"]["eso26_extdecisao"])){ 
           $this->eso26_extdecisao = "0" ; 
        } 
       $sql  .= $virgula." eso26_extdecisao = $this->eso26_extdecisao ";
       $virgula = ",";
     }
     if(trim($this->eso26_codsuspprocessocp)=="" || trim($this->eso26_codsuspprocessocp)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_codsuspprocessocp"])){ 
       $sql  .= $virgula." eso26_codsuspprocessocp = '$this->eso26_codsuspprocessocp' ";
       $virgula = ",";
     }
     if(trim($this->eso26_nrprocirrf)=="" || trim($this->eso26_nrprocirrf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_nrprocirrf"])){ 
       $sql  .= $virgula." eso26_nrprocirrf = '$this->eso26_nrprocirrf' ";
       $virgula = ",";
     }
     if(trim($this->eso26_codsuspirrf)=="" || trim($this->eso26_codsuspirrf)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_codsuspirrf"])){ 
       $sql  .= $virgula." eso26_codsuspirrf = '$this->eso26_codsuspirrf' ";
       $virgula = ",";
     }
     if(trim($this->eso26_nrprocfgts)=="" || trim($this->eso26_nrprocfgts)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_nrprocfgts"])){ 
       $sql  .= $virgula." eso26_nrprocfgts = '$this->eso26_nrprocfgts' ";
       $virgula = ",";
     }
     if(trim($this->eso26_nrprocpispasep)=="" || trim($this->eso26_nrprocpispasep)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_nrprocpispasep"])){ 
       $sql  .= $virgula." eso26_nrprocpispasep = '$this->eso26_nrprocpispasep' ";
       $virgula = ",";
     }
     if(trim($this->eso26_codsusppispasep)=="" || trim($this->eso26_codsusppispasep)!="" || isset($GLOBALS["HTTP_POST_VARS"]["eso26_codsusppispasep"])){ 
       $sql  .= $virgula." eso26_codsusppispasep = '$this->eso26_codsusppispasep' ";
       $virgula = ",";
     }
     $sql .= " where ";
     if($eso26_sequencial!=null){
       $sql .= " eso26_sequencial = $this->eso26_sequencial";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->eso26_sequencial));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_fetch_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,1009986,'$this->eso26_sequencial','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_sequencial"]) || $this->eso26_sequencial != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1009986,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_sequencial'))."','$this->eso26_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_rubrica"]) || $this->eso26_rubrica != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1009987,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_rubrica'))."','$this->eso26_rubrica',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_instituicao"]) || $this->eso26_instituicao != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1009988,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_instituicao'))."','$this->eso26_instituicao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_natureza"]) || $this->eso26_natureza != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1009994,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_natureza'))."','$this->eso26_natureza',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_datainicial"]) || $this->eso26_datainicial != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1010003,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_datainicial'))."','$this->eso26_datainicial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_datafinal"]) || $this->eso26_datafinal != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1010004,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_datafinal'))."','$this->eso26_datafinal',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_subgrupotce"]) || $this->eso26_subgrupotce != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1014222,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_subgrupotce'))."','$this->eso26_subgrupotce',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_codinccp"]) || $this->eso26_codinccp != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1014722,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_codinccp'))."','$this->eso26_codinccp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_codincirrf"]) || $this->eso26_codincirrf != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1014723,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_codincirrf'))."','$this->eso26_codincirrf',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_codincfgts"]) || $this->eso26_codincfgts != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1014724,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_codincfgts'))."','$this->eso26_codincfgts',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_codinccprp"]) || $this->eso26_codinccprp != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1014727,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_codinccprp'))."','$this->eso26_codinccprp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_tetoremun"]) || $this->eso26_tetoremun != "")
             $resac = db_query("insert into db_acount values($acount,1010325,1014729,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_tetoremun'))."','$this->eso26_tetoremun',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_codincpispasep"]) || $this->eso26_codincpispasep != "")
             $resac = db_query("insert into db_acount values($acount,1010325,83652111,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_codincpispasep'))."','$this->eso26_codincpispasep',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_tpproc"]) || $this->eso26_tpproc != "")
             $resac = db_query("insert into db_acount values($acount,1010325,201051558,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_tpproc'))."','$this->eso26_tpproc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_nrprocprocessocp"]) || $this->eso26_nrprocprocessocp != "")
             $resac = db_query("insert into db_acount values($acount,1010325,180347237,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_nrprocprocessocp'))."','$this->eso26_nrprocprocessocp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_extdecisao"]) || $this->eso26_extdecisao != "")
             $resac = db_query("insert into db_acount values($acount,1010325,115211890,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_extdecisao'))."','$this->eso26_extdecisao',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_codsuspprocessocp"]) || $this->eso26_codsuspprocessocp != "")
             $resac = db_query("insert into db_acount values($acount,1010325,94563963,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_codsuspprocessocp'))."','$this->eso26_codsuspprocessocp',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_nrprocirrf"]) || $this->eso26_nrprocirrf != "")
             $resac = db_query("insert into db_acount values($acount,1010325,290311487,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_nrprocirrf'))."','$this->eso26_nrprocirrf',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_codsuspirrf"]) || $this->eso26_codsuspirrf != "")
             $resac = db_query("insert into db_acount values($acount,1010325,53539259,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_codsuspirrf'))."','$this->eso26_codsuspirrf',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_nrprocfgts"]) || $this->eso26_nrprocfgts != "")
             $resac = db_query("insert into db_acount values($acount,1010325,52461618,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_nrprocfgts'))."','$this->eso26_nrprocfgts',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_nrprocpispasep"]) || $this->eso26_nrprocpispasep != "")
             $resac = db_query("insert into db_acount values($acount,1010325,169505090,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_nrprocpispasep'))."','$this->eso26_nrprocpispasep',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["eso26_codsusppispasep"]) || $this->eso26_codsusppispasep != "")
             $resac = db_query("insert into db_acount values($acount,1010325,231361664,'".AddSlashes(pg_fetch_result($resaco,$conresaco,'eso26_codsusppispasep'))."','$this->eso26_codsusppispasep',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Informações da Rubrica não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->eso26_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Informações da Rubrica não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->eso26_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$this->eso26_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function excluir($eso26_sequencial=null, $dbwhere = null)
    {
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($eso26_sequencial));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_fetch_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,1009986,'$eso26_sequencial','E')");
           $resac  = db_query("insert into db_acount values($acount,1010325,1009986,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_sequencial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1009987,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_rubrica'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1009988,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_instituicao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1009994,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_natureza'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1010003,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_datainicial'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1010004,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_datafinal'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1014222,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_subgrupotce'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1014722,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_codinccp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1014723,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_codincirrf'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1014724,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_codincfgts'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1014727,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_codinccprp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,1014729,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_tetoremun'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,83652111,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_codincpispasep'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,201051558,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_tpproc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,180347237,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_nrprocprocessocp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,115211890,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_extdecisao'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,94563963,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_codsuspprocessocp'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,290311487,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_nrprocirrf'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,53539259,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_codsuspirrf'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,52461618,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_nrprocfgts'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,169505090,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_nrprocpispasep'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,1010325,231361664,'','".AddSlashes(pg_fetch_result($resaco,$iresaco,'eso26_codsusppispasep'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from esocialrubricas
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($eso26_sequencial)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " eso26_sequencial = $eso26_sequencial ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Informações da Rubrica não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$eso26_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Informações da Rubrica não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$eso26_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com sucesso.\\n";
         $this->erro_sql .= "Valores : ".$eso26_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = pg_affected_rows($result);
         return true;
       } 
     } 
   } 

    public function sql_record($sql)
    {
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
        $this->erro_sql   = "Record Vazio na Tabela:esocialrubricas";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }

    public function sql_query($eso26_sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from esocialrubricas ";
     $sql .= "      inner join rhrubricas  on  rhrubricas.rh27_rubric = esocialrubricas.eso26_rubrica and  rhrubricas.rh27_instit = esocialrubricas.eso26_instituicao";
     $sql .= "      inner join db_config  on  db_config.codigo = rhrubricas.rh27_instit";
     $sql .= "      inner join rhtipomedia  on  rhtipomedia.rh29_tipo = rhrubricas.rh27_calc1";
     $sql .= "      left  join rhfundamentacaolegal  on  rhfundamentacaolegal.rh137_sequencial = rhrubricas.rh27_rhfundamentacaolegal";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($eso26_sequencial)) {
         $sql2 .= " where esocialrubricas.eso26_sequencial = $eso26_sequencial "; 
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

    public function sql_query_file($eso26_sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from esocialrubricas ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($eso26_sequencial)){
         $sql2 .= " where esocialrubricas.eso26_sequencial = $eso26_sequencial "; 
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

}
