<?php
//MODULO: pessoal
//CLASSE DA ENTIDADE pontoprovf13
class cl_pontoprovf13 { 
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
   var $r92_anousu = 0; 
   var $r92_mesusu = 0; 
   var $r92_regist = 0; 
   var $r92_rubric = null; 
   var $r92_valor = 0; 
   var $r92_quant = 0; 
   var $r92_lotac = null; 
   var $r92_media = 0; 
   var $r92_calc = 0; 
   var $r92_instit = 0; 
   // cria propriedade com as variaveis do arquivo 
   var $campos = "
                 r92_anousu = int4 = Ano do Exercicio 
                 r92_mesusu = int4 = Mes do Exercicio 
                 r92_regist = int4 = Matrícula 
                 r92_rubric = char(4) = Rubrica 
                 r92_valor = float8 = Valor 
                 r92_quant = float8 = Quantidade 
                 r92_lotac = char(4) = Lotação 
                 r92_media = int4 = Numero de meses incidido 
                 r92_calc = int4 = Formula para calculo 
                 r92_instit = int4 = codigo da instituicao 
                 ";
   //funcao construtor da classe 
   public function __construct() { 
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("pontoprovf13"); 
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
       $this->r92_anousu = ($this->r92_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_anousu"]:$this->r92_anousu);
       $this->r92_mesusu = ($this->r92_mesusu == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_mesusu"]:$this->r92_mesusu);
       $this->r92_regist = ($this->r92_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_regist"]:$this->r92_regist);
       $this->r92_rubric = ($this->r92_rubric == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_rubric"]:$this->r92_rubric);
       $this->r92_valor = ($this->r92_valor == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_valor"]:$this->r92_valor);
       $this->r92_quant = ($this->r92_quant == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_quant"]:$this->r92_quant);
       $this->r92_lotac = ($this->r92_lotac == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_lotac"]:$this->r92_lotac);
       $this->r92_media = ($this->r92_media == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_media"]:$this->r92_media);
       $this->r92_calc = ($this->r92_calc == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_calc"]:$this->r92_calc);
       $this->r92_instit = ($this->r92_instit == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_instit"]:$this->r92_instit);
     }else{
       $this->r92_anousu = ($this->r92_anousu == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_anousu"]:$this->r92_anousu);
       $this->r92_mesusu = ($this->r92_mesusu == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_mesusu"]:$this->r92_mesusu);
       $this->r92_regist = ($this->r92_regist == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_regist"]:$this->r92_regist);
       $this->r92_rubric = ($this->r92_rubric == ""?@$GLOBALS["HTTP_POST_VARS"]["r92_rubric"]:$this->r92_rubric);
     }
   }
   // funcao para Inclusão
   public function incluir ($r92_anousu,$r92_mesusu,$r92_regist,$r92_rubric){ 
      $this->atualizacampos();
     if($this->r92_valor == null ){ 
       $this->erro_sql = " Campo Valor não informado.";
       $this->erro_campo = "r92_valor";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r92_quant == null ){ 
       $this->erro_sql = " Campo Quantidade não informado.";
       $this->erro_campo = "r92_quant";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r92_lotac == null ){ 
       $this->erro_sql = " Campo Lotação não informado.";
       $this->erro_campo = "r92_lotac";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r92_media == null ){ 
       $this->erro_sql = " Campo Numero de meses incidido não informado.";
       $this->erro_campo = "r92_media";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r92_calc == null ){ 
       $this->erro_sql = " Campo Formula para calculo não informado.";
       $this->erro_campo = "r92_calc";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if($this->r92_instit == null ){ 
       $this->erro_sql = " Campo codigo da instituicao não informado.";
       $this->erro_campo = "r92_instit";
       $this->erro_banco = "";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
       $this->r92_anousu = $r92_anousu; 
       $this->r92_mesusu = $r92_mesusu; 
       $this->r92_regist = $r92_regist; 
       $this->r92_rubric = $r92_rubric; 
     if(($this->r92_anousu == null) || ($this->r92_anousu == "") ){ 
       $this->erro_sql = " Campo r92_anousu não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r92_mesusu == null) || ($this->r92_mesusu == "") ){ 
       $this->erro_sql = " Campo r92_mesusu não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r92_regist == null) || ($this->r92_regist == "") ){ 
       $this->erro_sql = " Campo r92_regist não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if(($this->r92_rubric == null) || ($this->r92_rubric == "") ){ 
       $this->erro_sql = " Campo r92_rubric não declarado.";
       $this->erro_banco = "Chave Primaria zerada.";
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     $sql = "insert into pontoprovf13(
                                       r92_anousu 
                                      ,r92_mesusu 
                                      ,r92_regist 
                                      ,r92_rubric 
                                      ,r92_valor 
                                      ,r92_quant 
                                      ,r92_lotac 
                                      ,r92_media 
                                      ,r92_calc 
                                      ,r92_instit 
                       )
                values (
                                $this->r92_anousu 
                               ,$this->r92_mesusu 
                               ,$this->r92_regist 
                               ,'$this->r92_rubric' 
                               ,$this->r92_valor 
                               ,$this->r92_quant 
                               ,'$this->r92_lotac' 
                               ,$this->r92_media 
                               ,$this->r92_calc 
                               ,$this->r92_instit 
                      )";
     $result = db_query($sql); 
     if($result==false){ 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = "Ponto de Provisao de 13o. salario ($this->r92_anousu."-".$this->r92_mesusu."-".$this->r92_regist."-".$this->r92_rubric) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = "Ponto de Provisao de 13o. salario já Cadastrado";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = "Ponto de Provisao de 13o. salario ($this->r92_anousu."-".$this->r92_mesusu."-".$this->r92_regist."-".$this->r92_rubric) não Incluído. Inclusão Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status = "0";
       $this->numrows_incluir= 0;
       return false;
     }
     $this->erro_banco = "";
     $this->erro_sql = "Inclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->r92_anousu."-".$this->r92_mesusu."-".$this->r92_regist."-".$this->r92_rubric;
     $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "1";
     $this->numrows_incluir= pg_affected_rows($result);
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->r92_anousu,$this->r92_mesusu,$this->r92_regist,$this->r92_rubric  ));
       if(($resaco!=false)||($this->numrows!=0)){

         $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
         $acount = pg_result($resac,0,0);
         $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
         $resac = db_query("insert into db_acountkey values($acount,13294,'$this->r92_anousu','I')");
         $resac = db_query("insert into db_acountkey values($acount,13295,'$this->r92_mesusu','I')");
         $resac = db_query("insert into db_acountkey values($acount,13296,'$this->r92_regist','I')");
         $resac = db_query("insert into db_acountkey values($acount,13297,'$this->r92_rubric','I')");
         $resac = db_query("insert into db_acount values($acount,2334,13294,'','".AddSlashes(pg_result($resaco,0,'r92_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2334,13295,'','".AddSlashes(pg_result($resaco,0,'r92_mesusu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2334,13296,'','".AddSlashes(pg_result($resaco,0,'r92_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2334,13297,'','".AddSlashes(pg_result($resaco,0,'r92_rubric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2334,13298,'','".AddSlashes(pg_result($resaco,0,'r92_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2334,13299,'','".AddSlashes(pg_result($resaco,0,'r92_quant'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2334,13300,'','".AddSlashes(pg_result($resaco,0,'r92_lotac'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2334,13301,'','".AddSlashes(pg_result($resaco,0,'r92_media'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2334,13302,'','".AddSlashes(pg_result($resaco,0,'r92_calc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         $resac = db_query("insert into db_acount values($acount,2334,13303,'','".AddSlashes(pg_result($resaco,0,'r92_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
       }
     }
     return true;
   } 
   // funcao para alteracao
   public function alterar ($r92_anousu=null,$r92_mesusu=null,$r92_regist=null,$r92_rubric=null) { 
      $this->atualizacampos();
     $sql = " update pontoprovf13 set ";
     $virgula = "";
     if(trim($this->r92_anousu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r92_anousu"])){ 
       $sql  .= $virgula." r92_anousu = $this->r92_anousu ";
       $virgula = ",";
       if(trim($this->r92_anousu) == null ){ 
         $this->erro_sql = " Campo Ano do Exercicio não informado.";
         $this->erro_campo = "r92_anousu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r92_mesusu)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r92_mesusu"])){ 
       $sql  .= $virgula." r92_mesusu = $this->r92_mesusu ";
       $virgula = ",";
       if(trim($this->r92_mesusu) == null ){ 
         $this->erro_sql = " Campo Mes do Exercicio não informado.";
         $this->erro_campo = "r92_mesusu";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r92_regist)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r92_regist"])){ 
       $sql  .= $virgula." r92_regist = $this->r92_regist ";
       $virgula = ",";
       if(trim($this->r92_regist) == null ){ 
         $this->erro_sql = " Campo Matrícula não informado.";
         $this->erro_campo = "r92_regist";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r92_rubric)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r92_rubric"])){ 
       $sql  .= $virgula." r92_rubric = '$this->r92_rubric' ";
       $virgula = ",";
       if(trim($this->r92_rubric) == null ){ 
         $this->erro_sql = " Campo Rubrica não informado.";
         $this->erro_campo = "r92_rubric";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r92_valor)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r92_valor"])){ 
       $sql  .= $virgula." r92_valor = $this->r92_valor ";
       $virgula = ",";
       if(trim($this->r92_valor) == null ){ 
         $this->erro_sql = " Campo Valor não informado.";
         $this->erro_campo = "r92_valor";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r92_quant)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r92_quant"])){ 
       $sql  .= $virgula." r92_quant = $this->r92_quant ";
       $virgula = ",";
       if(trim($this->r92_quant) == null ){ 
         $this->erro_sql = " Campo Quantidade não informado.";
         $this->erro_campo = "r92_quant";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r92_lotac)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r92_lotac"])){ 
       $sql  .= $virgula." r92_lotac = '$this->r92_lotac' ";
       $virgula = ",";
       if(trim($this->r92_lotac) == null ){ 
         $this->erro_sql = " Campo Lotação não informado.";
         $this->erro_campo = "r92_lotac";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r92_media)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r92_media"])){ 
       $sql  .= $virgula." r92_media = $this->r92_media ";
       $virgula = ",";
       if(trim($this->r92_media) == null ){ 
         $this->erro_sql = " Campo Numero de meses incidido não informado.";
         $this->erro_campo = "r92_media";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r92_calc)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r92_calc"])){ 
       $sql  .= $virgula." r92_calc = $this->r92_calc ";
       $virgula = ",";
       if(trim($this->r92_calc) == null ){ 
         $this->erro_sql = " Campo Formula para calculo não informado.";
         $this->erro_campo = "r92_calc";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     if(trim($this->r92_instit)!="" || isset($GLOBALS["HTTP_POST_VARS"]["r92_instit"])){ 
       $sql  .= $virgula." r92_instit = $this->r92_instit ";
       $virgula = ",";
       if(trim($this->r92_instit) == null ){ 
         $this->erro_sql = " Campo codigo da instituicao não informado.";
         $this->erro_campo = "r92_instit";
         $this->erro_banco = "";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "0";
         return false;
       }
     }
     $sql .= " where ";
     if($r92_anousu!=null){
       $sql .= " r92_anousu = $this->r92_anousu";
     }
     if($r92_mesusu!=null){
       $sql .= " and  r92_mesusu = $this->r92_mesusu";
     }
     if($r92_regist!=null){
       $sql .= " and  r92_regist = $this->r92_regist";
     }
     if($r92_rubric!=null){
       $sql .= " and  r92_rubric = '$this->r92_rubric'";
     }
     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       $resaco = $this->sql_record($this->sql_query_file($this->r92_anousu,$this->r92_mesusu,$this->r92_regist,$this->r92_rubric));
       if ($this->numrows > 0) {

         for ($conresaco = 0; $conresaco < $this->numrows; $conresaco++) {

           $resac = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac = db_query("insert into db_acountkey values($acount,13294,'$this->r92_anousu','A')");
           $resac = db_query("insert into db_acountkey values($acount,13295,'$this->r92_mesusu','A')");
           $resac = db_query("insert into db_acountkey values($acount,13296,'$this->r92_regist','A')");
           $resac = db_query("insert into db_acountkey values($acount,13297,'$this->r92_rubric','A')");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r92_anousu"]) || $this->r92_anousu != "")
             $resac = db_query("insert into db_acount values($acount,2334,13294,'".AddSlashes(pg_result($resaco,$conresaco,'r92_anousu'))."','$this->r92_anousu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r92_mesusu"]) || $this->r92_mesusu != "")
             $resac = db_query("insert into db_acount values($acount,2334,13295,'".AddSlashes(pg_result($resaco,$conresaco,'r92_mesusu'))."','$this->r92_mesusu',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r92_regist"]) || $this->r92_regist != "")
             $resac = db_query("insert into db_acount values($acount,2334,13296,'".AddSlashes(pg_result($resaco,$conresaco,'r92_regist'))."','$this->r92_regist',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r92_rubric"]) || $this->r92_rubric != "")
             $resac = db_query("insert into db_acount values($acount,2334,13297,'".AddSlashes(pg_result($resaco,$conresaco,'r92_rubric'))."','$this->r92_rubric',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r92_valor"]) || $this->r92_valor != "")
             $resac = db_query("insert into db_acount values($acount,2334,13298,'".AddSlashes(pg_result($resaco,$conresaco,'r92_valor'))."','$this->r92_valor',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r92_quant"]) || $this->r92_quant != "")
             $resac = db_query("insert into db_acount values($acount,2334,13299,'".AddSlashes(pg_result($resaco,$conresaco,'r92_quant'))."','$this->r92_quant',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r92_lotac"]) || $this->r92_lotac != "")
             $resac = db_query("insert into db_acount values($acount,2334,13300,'".AddSlashes(pg_result($resaco,$conresaco,'r92_lotac'))."','$this->r92_lotac',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r92_media"]) || $this->r92_media != "")
             $resac = db_query("insert into db_acount values($acount,2334,13301,'".AddSlashes(pg_result($resaco,$conresaco,'r92_media'))."','$this->r92_media',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r92_calc"]) || $this->r92_calc != "")
             $resac = db_query("insert into db_acount values($acount,2334,13302,'".AddSlashes(pg_result($resaco,$conresaco,'r92_calc'))."','$this->r92_calc',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           if (isset($GLOBALS["HTTP_POST_VARS"]["r92_instit"]) || $this->r92_instit != "")
             $resac = db_query("insert into db_acount values($acount,2334,13303,'".AddSlashes(pg_result($resaco,$conresaco,'r92_instit'))."','$this->r92_instit',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $result = db_query($sql);
     if (!$result) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ponto de Provisao de 13o. salario não Alterado. Alteração Abortada.\\n";
         $this->erro_sql .= "Valores : ".$this->r92_anousu."-".$this->r92_mesusu."-".$this->r92_regist."-".$this->r92_rubric;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_alterar = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Ponto de Provisao de 13o. salario não foi Alterado. Alteração Executada.\\n";
         $this->erro_sql .= "Valores : ".$this->r92_anousu."-".$this->r92_mesusu."-".$this->r92_regist."-".$this->r92_rubric;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$this->r92_anousu."-".$this->r92_mesusu."-".$this->r92_regist."-".$this->r92_rubric;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       } 
     } 
   } 
   // funcao para exclusao 
   public function excluir ($r92_anousu=null,$r92_mesusu=null,$r92_regist=null,$r92_rubric=null,$dbwhere=null) { 

     $lSessaoDesativarAccount = db_getsession("DB_desativar_account", false);
     if (!isset($lSessaoDesativarAccount) || (isset($lSessaoDesativarAccount)
       && ($lSessaoDesativarAccount === false))) {

       if (empty($dbwhere)) {

         $resaco = $this->sql_record($this->sql_query_file($r92_anousu,$r92_mesusu,$r92_regist,$r92_rubric));
       } else { 
         $resaco = $this->sql_record($this->sql_query_file(null,null,null,null,"*",null,$dbwhere));
       }
       if (($resaco != false) || ($this->numrows!=0)) {

         for ($iresaco = 0; $iresaco < $this->numrows; $iresaco++) {

           $resac  = db_query("select nextval('db_acount_id_acount_seq') as acount");
           $acount = pg_result($resac,0,0);
           $resac  = db_query("insert into db_acountacesso values($acount,".db_getsession("DB_acessado").")");
           $resac  = db_query("insert into db_acountkey values($acount,13294,'$r92_anousu','E')");
           $resac  = db_query("insert into db_acountkey values($acount,13295,'$r92_mesusu','E')");
           $resac  = db_query("insert into db_acountkey values($acount,13296,'$r92_regist','E')");
           $resac  = db_query("insert into db_acountkey values($acount,13297,'$r92_rubric','E')");
           $resac  = db_query("insert into db_acount values($acount,2334,13294,'','".AddSlashes(pg_result($resaco,$iresaco,'r92_anousu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2334,13295,'','".AddSlashes(pg_result($resaco,$iresaco,'r92_mesusu'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2334,13296,'','".AddSlashes(pg_result($resaco,$iresaco,'r92_regist'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2334,13297,'','".AddSlashes(pg_result($resaco,$iresaco,'r92_rubric'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2334,13298,'','".AddSlashes(pg_result($resaco,$iresaco,'r92_valor'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2334,13299,'','".AddSlashes(pg_result($resaco,$iresaco,'r92_quant'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2334,13300,'','".AddSlashes(pg_result($resaco,$iresaco,'r92_lotac'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2334,13301,'','".AddSlashes(pg_result($resaco,$iresaco,'r92_media'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2334,13302,'','".AddSlashes(pg_result($resaco,$iresaco,'r92_calc'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
           $resac  = db_query("insert into db_acount values($acount,2334,13303,'','".AddSlashes(pg_result($resaco,$iresaco,'r92_instit'))."',".db_getsession('DB_datausu').",".db_getsession('DB_id_usuario').")");
         }
       }
     }
     $sql = " delete from pontoprovf13
                    where ";
     $sql2 = "";
     if (empty($dbwhere)) {
        if (!empty($r92_anousu)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r92_anousu = $r92_anousu ";
        }
        if (!empty($r92_mesusu)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r92_mesusu = $r92_mesusu ";
        }
        if (!empty($r92_regist)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r92_regist = $r92_regist ";
        }
        if (!empty($r92_rubric)){
          if (!empty($sql2)) {
            $sql2 .= " and ";
          }
          $sql2 .= " r92_rubric = '$r92_rubric' ";
        }
     } else {
       $sql2 = $dbwhere;
     }
     $result = db_query($sql.$sql2);
     if ($result == false) { 
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       $this->erro_sql   = "Ponto de Provisao de 13o. salario não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql .= "Valores : ".$r92_anousu."-".$r92_mesusu."-".$r92_regist."-".$r92_rubric;
       $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       $this->numrows_excluir = 0;
       return false;
     } else {
       if (pg_affected_rows($result) == 0) {
         $this->erro_banco = "";
         $this->erro_sql = "Ponto de Provisao de 13o. salario não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql .= "Valores : ".$r92_anousu."-".$r92_mesusu."-".$r92_regist."-".$r92_rubric;
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status = "1";
         $this->numrows_excluir = 0;
         return true;
       } else {
         $this->erro_banco = "";
         $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql .= "Valores : ".$r92_anousu."-".$r92_mesusu."-".$r92_regist."-".$r92_rubric;
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
        $this->erro_sql   = "Record Vazio na Tabela:pontoprovf13";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
     return $result;
   }
   // funcao do sql 
   public function sql_query ($r92_anousu = null,$r92_mesusu = null,$r92_regist = null,$r92_rubric = null,$campos = "*", $ordem = null, $dbwhere = "") { 

     $sql  = "select {$campos}";
     $sql .= "  from pontoprovf13 ";
     $sql .= "      inner join db_config  on  db_config.codigo = pontoprovf13.r92_instit";
     $sql .= "      inner join cgm  on  cgm.z01_numcgm = db_config.numcgm";
     $sql .= "      inner join db_tipoinstit  on  db_tipoinstit.db21_codtipo = db_config.db21_tipoinstit";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($r92_anousu)) {
         $sql2 .= " where pontoprovf13.r92_anousu = $r92_anousu "; 
       } 
       if (!empty($r92_mesusu)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovf13.r92_mesusu = $r92_mesusu "; 
       } 
       if (!empty($r92_regist)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovf13.r92_regist = $r92_regist "; 
       } 
       if (!empty($r92_rubric)) {
         if (!empty($sql2)) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovf13.r92_rubric = '$r92_rubric' "; 
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
   // funcao do sql 
   public function sql_query_file ($r92_anousu = null,$r92_mesusu = null,$r92_regist = null,$r92_rubric = null, $campos = "*", $ordem = null, $dbwhere = "") {

     $sql  = "select {$campos} ";
     $sql .= "  from pontoprovf13 ";
     $sql2 = "";
     if (empty($dbwhere)) {
       if (!empty($r92_anousu)){
         $sql2 .= " where pontoprovf13.r92_anousu = $r92_anousu "; 
       } 
       if (!empty($r92_mesusu)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovf13.r92_mesusu = $r92_mesusu "; 
       } 
       if (!empty($r92_regist)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovf13.r92_regist = $r92_regist "; 
       } 
       if (!empty($r92_rubric)){
         if ( !empty($sql2) ) {
            $sql2 .= " and ";
         } else {
            $sql2 .= " where ";
         } 
         $sql2 .= " pontoprovf13.r92_rubric = '$r92_rubric' "; 
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
