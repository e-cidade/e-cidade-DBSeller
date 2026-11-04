<?php
//MODULO: cadastro
//CLASSE DA ENTIDADE iptutaxacalvold
class cl_iptutaxacalvold { 
   // cria variaveis de erro 
   var $rotulo     = null; 
   var $query_sql  = null; 
   var $numrows    = 0; 
   var $erro_status= null; 
   var $erro_sql   = null; 
   var $erro_banco = null;  
   var $erro_msg   = null;  
   var $erro_campo = null;  
   var $pagina_retorno = null; 
   // cria variaveis do arquivo 
   var $j158_sequencial = 0; 
   var $j158_codigo = 0; 
   var $j158_iptutaxanumpold = 0; 
   var $j158_codhis = 0; 
   var $j158_receit = 0; 
   var $j158_valor = 0; 
   var $j158_quant = 0; 
   var $j158_areaed = 0; 
   var $j158_iptucalclog = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 j158_sequencial = int4 = Sequencial 
                 j158_codigo = int4 = Código 
                 j158_iptutaxanumpold = int4 = Código Iptutaxanumpold 
                 j158_codhis = int4 = Código Iptucalh 
                 j158_receit = int4 = Código da Receita 
                 j158_valor = float8 = Valor 
                 j158_quant = float8 = Quantidade 
                 j158_areaed = float8 = Histórico da área edificada 
                 j158_iptucalclog = int4 = Código de vínculo com a iptucalclog 
                 ";
   //funcao construtor da classe 
   function cl_iptutaxacalvold() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("iptutaxacalvold"); 
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
       $this->j158_sequencial = ($this->j158_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j158_sequencial"]:$this->j158_sequencial);
       $this->j158_codigo = ($this->j158_codigo == ""?@$GLOBALS["HTTP_POST_VARS"]["j158_codigo"]:$this->j158_codigo);
       $this->j158_iptutaxanumpold = ($this->j158_iptutaxanumpold == ""?@$GLOBALS["HTTP_POST_VARS"]["j158_iptutaxanumpold"]:$this->j158_iptutaxanumpold);
       $this->j158_codhis = ($this->j158_codhis == ""?@$GLOBALS["HTTP_POST_VARS"]["j158_codhis"]:$this->j158_codhis);
       $this->j158_receit = ($this->j158_receit == ""?@$GLOBALS["HTTP_POST_VARS"]["j158_receit"]:$this->j158_receit);
       $this->j158_valor = ($this->j158_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["j158_valor"]:$this->j158_valor);
       $this->j158_quant = ($this->j158_quant == ""?@$GLOBALS["HTTP_POST_VARS"]["j158_quant"]:$this->j158_quant);
       $this->j158_areaed = ($this->j158_areaed == ""?@$GLOBALS["HTTP_POST_VARS"]["j158_areaed"]:$this->j158_areaed);
       $this->j158_iptucalclog = ($this->j158_iptucalclog == ""?@$GLOBALS["HTTP_POST_VARS"]["j158_iptucalclog"]:$this->j158_iptucalclog);
     }else{
       $this->j158_sequencial = ($this->j158_sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["j158_sequencial"]:$this->j158_sequencial);
     }
   }
   // funcao para inclusao
   function incluir ($j158_sequencial){ 
      $this->atualizacampos();
     if($this->j158_codigo == null ){ 
       $this->erro_sql = " Campo Código nao Informado.";
       $this->erro_campo = "j158_codigo";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j158_iptutaxanumpold == null ){ 
       $this->erro_sql = " Campo Código Iptutaxanumpold nao Informado.";
       $this->erro_campo = "j158_iptutaxanumpold";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j158_codhis == null ){ 
       $this->erro_sql = " Campo Código Iptucalh nao Informado.";
       $this->erro_campo = "j158_codhis";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j158_receit == null ){ 
       $this->erro_sql = " Campo Código da Receita nao Informado.";
       $this->erro_campo = "j158_receit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j158_valor == null ){ 
       $this->erro_sql = " Campo Valor nao Informado.";
       $this->erro_campo = "j158_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->j158_quant == null ){ 
       $this->j158_quant = "0";
     }
     if($this->j158_areaed == null ){ 
       $this->j158_areaed = "0";
     }
     if($this->j158_iptucalclog == null ){ 
       $this->erro_sql = " Campo Código de vínculo com a iptucalclog nao Informado.";
       $this->erro_campo = "j158_iptucalclog";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($j158_sequencial == "" || $j158_sequencial == null ){
       $result = @db_query("select nextval('iptutaxacalvold_j158_sequencial_seq')"); 
       if($result==false){
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_sql   = "Verifique o cadastro da sequencia: iptutaxacalvold_j158_sequencial_seq do campo: j158_sequencial"; 
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false; 
       }
       $this->j158_sequencial = pg_result($result,0,0); 
     }else{
       $result = @db_query("select last_value from iptutaxacalvold_j158_sequencial_seq");
       if(($result != false) && (pg_result($result,0,0) < $j158_sequencial)){
         $this->erro_sql = " Campo j158_sequencial maior que último número da sequencia.";
         $this->erro_banco = "Sequencia menor que este número.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }else{
         $this->j158_sequencial = $j158_sequencial; 
       }
     }
     if(($this->j158_sequencial == null) || ($this->j158_sequencial == "") ){ 
       $this->erro_sql = " Campo j158_sequencial nao declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $result = @db_query("insert into iptutaxacalvold(
                                       j158_sequencial 
                                      ,j158_codigo 
                                      ,j158_iptutaxanumpold 
                                      ,j158_codhis 
                                      ,j158_receit 
                                      ,j158_valor 
                                      ,j158_quant 
                                      ,j158_areaed 
                                      ,j158_iptucalclog 
                       )
                values (
                                $this->j158_sequencial 
                               ,$this->j158_codigo 
                               ,$this->j158_iptutaxanumpold 
                               ,$this->j158_codhis 
                               ,$this->j158_receit 
                               ,$this->j158_valor 
                               ,$this->j158_quant 
                               ,$this->j158_areaed 
                               ,$this->j158_iptucalclog 
                      )");
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "iptutaxacalv ($this->j158_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "iptutaxacalv já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "iptutaxacalv ($this->j158_sequencial) nao Incluído. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusao efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j158_sequencial;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $resaco = $this->sql_record($this->sql_query_file($this->j158_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,1014484,'$this->j158_sequencial','I')");
       $resac = db_query("insert into db_acount values($acount,1010516,1014484,'','".pg_result($resaco,0,'j158_sequencial')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010982,'','".pg_result($resaco,0,'j158_codigo')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010983,'','".pg_result($resaco,0,'j158_iptutaxanumpold')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010984,'','".pg_result($resaco,0,'j158_codhis')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010986,'','".pg_result($resaco,0,'j158_receit')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010988,'','".pg_result($resaco,0,'j158_valor')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010990,'','".pg_result($resaco,0,'j158_quant')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1011791,'','".pg_result($resaco,0,'j158_areaed')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010992,'','".pg_result($resaco,0,'j158_iptucalclog')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     return true;
   } 
   // funcao para alteracao
   function alterar ($j158_sequencial=null) { 
      $this->atualizacampos();
     $sql = " update iptutaxacalvold set ";
     $virgula = "";
     if(trim($this->j158_sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j158_sequencial"])){ 
        if(trim($this->j158_sequencial)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j158_sequencial"])){ 
           $this->j158_sequencial = "0" ; 
        } 
       $sql  .= $virgula." j158_sequencial = $this->j158_sequencial ";
       $virgula = ",";
       if(trim($this->j158_sequencial) == null ){ 
         $this->erro_sql = " Campo Sequencial nao Informado.";
         $this->erro_campo = "j158_sequencial";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j158_codigo)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j158_codigo"])){ 
        if(trim($this->j158_codigo)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j158_codigo"])){ 
           $this->j158_codigo = "0" ; 
        } 
       $sql  .= $virgula." j158_codigo = $this->j158_codigo ";
       $virgula = ",";
       if(trim($this->j158_codigo) == null ){ 
         $this->erro_sql = " Campo Código nao Informado.";
         $this->erro_campo = "j158_codigo";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j158_iptutaxanumpold)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j158_iptutaxanumpold"])){ 
        if(trim($this->j158_iptutaxanumpold)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j158_iptutaxanumpold"])){ 
           $this->j158_iptutaxanumpold = "0" ; 
        } 
       $sql  .= $virgula." j158_iptutaxanumpold = $this->j158_iptutaxanumpold ";
       $virgula = ",";
       if(trim($this->j158_iptutaxanumpold) == null ){ 
         $this->erro_sql = " Campo Código Iptutaxanumpold nao Informado.";
         $this->erro_campo = "j158_iptutaxanumpold";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j158_codhis)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j158_codhis"])){ 
        if(trim($this->j158_codhis)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j158_codhis"])){ 
           $this->j158_codhis = "0" ; 
        } 
       $sql  .= $virgula." j158_codhis = $this->j158_codhis ";
       $virgula = ",";
       if(trim($this->j158_codhis) == null ){ 
         $this->erro_sql = " Campo Código Iptucalh nao Informado.";
         $this->erro_campo = "j158_codhis";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j158_receit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j158_receit"])){ 
        if(trim($this->j158_receit)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j158_receit"])){ 
           $this->j158_receit = "0" ; 
        } 
       $sql  .= $virgula." j158_receit = $this->j158_receit ";
       $virgula = ",";
       if(trim($this->j158_receit) == null ){ 
         $this->erro_sql = " Campo Código da Receita nao Informado.";
         $this->erro_campo = "j158_receit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j158_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j158_valor"])){ 
        if(trim($this->j158_valor)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j158_valor"])){ 
           $this->j158_valor = "0" ; 
        } 
       $sql  .= $virgula." j158_valor = $this->j158_valor ";
       $virgula = ",";
       if(trim($this->j158_valor) == null ){ 
         $this->erro_sql = " Campo Valor nao Informado.";
         $this->erro_campo = "j158_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->j158_quant)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j158_quant"])){ 
        if(trim($this->j158_quant)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j158_quant"])){ 
           $this->j158_quant = "0" ; 
        } 
       $sql  .= $virgula." j158_quant = $this->j158_quant ";
       $virgula = ",";
     }
     if(trim($this->j158_areaed)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j158_areaed"])){ 
        if(trim($this->j158_areaed)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j158_areaed"])){ 
           $this->j158_areaed = "0" ; 
        } 
       $sql  .= $virgula." j158_areaed = $this->j158_areaed ";
       $virgula = ",";
     }
     if(trim($this->j158_iptucalclog)!="" || isset($GLOBALS["HTTP_POST_VARS"]["j158_iptucalclog"])){ 
        if(trim($this->j158_iptucalclog)=="" && isset($GLOBALS["HTTP_POST_VARS"]["j158_iptucalclog"])){ 
           $this->j158_iptucalclog = "0" ; 
        } 
       $sql  .= $virgula." j158_iptucalclog = $this->j158_iptucalclog ";
       $virgula = ",";
       if(trim($this->j158_iptucalclog) == null ){ 
         $this->erro_sql = " Campo Código de vínculo com a iptucalclog nao Informado.";
         $this->erro_campo = "j158_iptucalclog";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where  j158_sequencial = $this->j158_sequencial
";
     $resaco = $this->sql_record($this->sql_query_file($this->j158_sequencial));
     if($this->numrows>0){       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,1014484,'$this->j158_sequencial','A')");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j158_sequencial"]))
         $resac = db_query("insert into db_acount values($acount,1010516,1014484,'".pg_result($resaco,0,'j158_sequencial')."','$this->j158_sequencial',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j158_codigo"]))
         $resac = db_query("insert into db_acount values($acount,1010516,1010982,'".pg_result($resaco,0,'j158_codigo')."','$this->j158_codigo',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j158_iptutaxanumpold"]))
         $resac = db_query("insert into db_acount values($acount,1010516,1010983,'".pg_result($resaco,0,'j158_iptutaxanumpold')."','$this->j158_iptutaxanumpold',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j158_codhis"]))
         $resac = db_query("insert into db_acount values($acount,1010516,1010984,'".pg_result($resaco,0,'j158_codhis')."','$this->j158_codhis',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j158_receit"]))
         $resac = db_query("insert into db_acount values($acount,1010516,1010986,'".pg_result($resaco,0,'j158_receit')."','$this->j158_receit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j158_valor"]))
         $resac = db_query("insert into db_acount values($acount,1010516,1010988,'".pg_result($resaco,0,'j158_valor')."','$this->j158_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j158_quant"]))
         $resac = db_query("insert into db_acount values($acount,1010516,1010990,'".pg_result($resaco,0,'j158_quant')."','$this->j158_quant',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j158_areaed"]))
         $resac = db_query("insert into db_acount values($acount,1010516,1011791,'".pg_result($resaco,0,'j158_areaed')."','$this->j158_areaed',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       if(isset($GLOBALS["HTTP_POST_VARS"]["j158_iptucalclog"]))
         $resac = db_query("insert into db_acount values($acount,1010516,1010992,'".pg_result($resaco,0,'j158_iptucalclog')."','$this->j158_iptucalclog',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     $result = @db_query($sql);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "iptutaxacalv nao Alterado. Alteracao Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->j158_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "iptutaxacalv nao foi Alterado. Alteracao Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->j158_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j158_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   function excluir ($j158_sequencial=null) { 
     $this->atualizacampos(true);
     $resaco = $this->sql_record($this->sql_query_file($this->j158_sequencial));
     if(($resaco!=false)||($this->numrows!=0)){
       $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
       $acount = pg_result($resac,0,0);
       $resac = db_query("insert into db_acountkey values($acount,1014484,'$this->j158_sequencial','E')");
       $resac = db_query("insert into db_acount values($acount,1010516,1014484,'','".pg_result($resaco,0,'j158_sequencial')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010982,'','".pg_result($resaco,0,'j158_codigo')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010983,'','".pg_result($resaco,0,'j158_iptutaxanumpold')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010984,'','".pg_result($resaco,0,'j158_codhis')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010986,'','".pg_result($resaco,0,'j158_receit')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010988,'','".pg_result($resaco,0,'j158_valor')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010990,'','".pg_result($resaco,0,'j158_quant')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1011791,'','".pg_result($resaco,0,'j158_areaed')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       $resac = db_query("insert into db_acount values($acount,1010516,1010992,'','".pg_result($resaco,0,'j158_iptucalclog')."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
     }
     $sql = " delete from iptutaxacalvold
                    where ";
     $sql2 = "";
      if($this->j158_sequencial != ""){
      if($sql2!=""){
        $sql2 .= " and ";
      }
      $sql2 .= " j158_sequencial = $this->j158_sequencial ";
}
     $result = @db_query($sql.$sql2);
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "iptutaxacalv nao Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$this->j158_sequencial;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }else{
       if(pg_affected_rows($result)==0){
         $this->erro_banco = "";
         $this->erro_sql = "iptutaxacalv nao Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$this->j158_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       }else{
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->j158_sequencial;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         return true;
       } 
     } 
   } 
   // funcao do recordset 
   function sql_record($sql) { 
     $result = @db_query($sql);
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
        $this->erro_sql   = "Dados do Grupo nao Encontrado";
        $this->erro_msg   = "Usuário: \n\n ".$this->erro_sql." \n\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   function sql_query ( $j158_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from iptutaxacalvold ";
     $sql .= "      inner join tabrec  on  tabrec.k02_codigo = iptutaxacalvold.j158_receit";
     $sql .= "      inner join iptucalh  on  iptucalh.j17_codhis = iptutaxacalvold.j158_codhis";
     $sql .= "      inner join iptucalclog  on  iptucalclog.j27_codigo = iptutaxacalvold.j158_iptucalclog";
     $sql .= "      inner join iptutaxanumpold  on  iptutaxanumpold.j159_sequencial = iptutaxacalvold.j158_iptutaxanumpold";
     $sql .= "      inner join tabrecjm  on  tabrecjm.k02_codjm = tabrec.k02_codjm";
     $sql .= "      inner join tabrectipo  on  tabrectipo.k116_sequencial = tabrec.k02_tabrectipo";
     $sql .= "      inner join db_usuarios  on  db_usuarios.id_usuario = iptucalclog.j27_usuario";
     $sql .= "      inner join iptubase  on  iptubase.j01_matric = iptutaxanumpold.j159_matric";
     $sql .= "      inner join iptucalclog  as a on   a.j27_codigo = iptutaxanumpold.j159_iptucalclog";
     $sql .= "      inner join iptucadtaxaexe  on  iptucadtaxaexe.j08_iptucadtaxaexe = iptutaxanumpold.j159_iptucadtaxaexe";
     $sql2 = "";
     if($dbwhere==""){
       if($j158_sequencial!=null ){
         $sql2 .= " where iptutaxacalvold.j158_sequencial = $j158_sequencial "; 
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
   // funcao do sql 
   function sql_query_file ( $j158_sequencial=null,$campos="*",$ordem=null,$dbwhere=""){ 
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
     $sql .= " from iptutaxacalvold ";
     $sql2 = "";
     if($dbwhere==""){
       if($j158_sequencial!=null ){
         $sql2 .= " where iptutaxacalvold.j158_sequencial = $j158_sequencial "; 
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

  public function salvarIptutaxacalvOld($iAnousu, $iMatric, $iCalclog, $iIptutaxanumpold) {
 
    if ( !db_utils::inTransaction()) {
       throw new Exception ("Sem Transação Ativa");
    }

    if (empty($iAnousu)) {
       $this->erro_sql = " Campo Exercício não informado para gerar informações anteriores.";
       $this->erro_campo = "anousu";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
    }
    if (empty($iMatric)) {
       $this->erro_sql = " Campo Matrícula não informado para gerar informações anteriores.";
       $this->erro_campo = "matric";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
    }
    if (empty($iCalclog)) {
       $this->erro_sql = " Campo CalcLog não informado para gerar informações anteriores.";
       $this->erro_campo = "j158_iptucalclog";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
    }
    
    $sSqlIptutaxacalv = "select j152_codigo,
                                j152_iptutaxanump,
                                j152_codhis,
                                j152_receit,
                                j152_valor,
                                j152_quant,
                                j152_areaed
                           from iptutaxacalv 
                     inner join iptutaxanump   on j152_iptutaxanump   = j151_codigo 
                     inner join iptucadtaxaexe on j151_iptucadtaxaexe = j08_iptucadtaxaexe 
                          where j08_anousu = $iAnousu and j151_matric = $iMatric limit 1;";
    
    $rsIptutaxacalv = db_query($sSqlIptutaxacalv) or die($sSqlIptutaxacalv);

    if (!$rsIptutaxacalv) {
        throw new DBException("Não foi possivel buscar dados do cálculo da taxa (".pg_last_error().")");
    }
    if (pg_numrows($rsIptutaxacalv) > 0) {

       global $j152_codigo;
       global $j152_iptutaxanump;
       global $j152_codhis;
       global $j152_receit;
       global $j152_valor;
       global $j152_quant;
       global $j152_areaed;
       global $j27_codigo;

       db_fieldsmemory($rsIptutaxacalv,0);

       $this->j158_codigo          = $j152_codigo;
       $this->j158_iptutaxanumpold = $iIptutaxanumpold;
       $this->j158_codhis          = $j152_codhis;
       $this->j158_receit          = $j152_receit;
       $this->j158_valor           = $j152_valor;
       $this->j158_quant           = $j152_quant;
       $this->j158_areaed          = $j152_areaed;
       $this->j158_iptucalclog     = $j27_codigo;
       $this->incluir(null);

       if ($this->erro_status == 0) {
          throw new DBException("Não foi possivel salvar dados do cálculo das taxas (iptutaxacalvold) (".pg_last_error().")");
          return false;
       }
    }
    return true;
  }  

}

?>
