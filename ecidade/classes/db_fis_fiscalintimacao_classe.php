<?php
// plugin
// CLASSE DA ENTIDADE fiscalintimacao
class cl_fis_fiscalintimacao{

  // cria variaveis de erro
  public $rotulo          = null;
  public $query_sql       = null;
  public $numrows         = 0;
  public $numrows_incluir = 0;
  public $numrows_alterar = 0;
  public $numrows_excluir = 0;
  public $erro_status     = null;
  public $erro_sql        = null;
  public $erro_banco      = null;
  public $erro_msg        = null;
  public $erro_campo      = null;
  public $pagina_retorno  = null;

  // cria variaveis do arquivo
  public $in01_codnoti   = 0;
  public $in01_intimacao = 'false';

  // funcao construtor da classe
  public function __construct() {
  // classes dos rotulos dos campos
    $this->rotulo = new rotulo("fis_fiscalintimacao");
    $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
  }

  // funcao erro
  public function erro($mostra,$retorna) {
    if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
      echo "<script>alert(\"".$this->erro_msg."\");</script>";
      if($retorna==true){
        echo "<script>location.href='".$this->pagina_retorno."'</script>";
      }
    }
  }

  // funcao para inclusao
  public function incluir(){

    if( $this->in01_codnoti == 0 ){
      $this->erro_sql    = "Codigo não Informado.";
      $this->erro_campo  = "in01_codnoti";
      $this->erro_banco  = "";
      $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .= str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }

    $sql = "insert into fiscalizacao.fis_fiscalintimacao(
                 in01_codnoti
                ,in01_intimacao
              ) values (
                 $this->in01_codnoti
                ,$this->in01_intimacao
            )";

    $result = db_query($sql);

    if($result == false){
      $this->erro_banco = str_replace("\n","",@pg_last_error());
      if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
        $this->erro_sql   = $sql."fiscalintimacao - Descricao ($this->in01_codnoti) não Incluído. Inclusao Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_banco = "Erro!!!!";
        $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      }else{
        $this->erro_sql  = $sql." fiscalintimacao - Texto ($this->in01_codnoti) não Incluído. Inclusao Abortada.";
        $this->erro_msg  = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      }
      $this->erro_status     = "0";
      $this->numrows_incluir = 0;
      return false;
    }

    $this->erro_banco      = "";
    $this->erro_sql        = "Inclusao efetuada com Sucesso\\n";
    $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
    $this->erro_msg       .= str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
    $this->erro_status     = "1";
    $this->numrows_incluir = pg_affected_rows($result);
    return true;
  }

  // funcao para alteracao
  public function alterar(){

    if( $this->in01_codnoti == 0 ){
      $this->erro_sql    = "Codigo não Informado.";
      $this->erro_campo  = "in01_codnoti";
      $this->erro_banco  = "";
      $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .= str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }

    $sql = "update fiscalizacao.fis_fiscalintimacao set
              in01_intimacao     = $this->in01_intimacao
              where in01_codnoti = $this->in01_codnoti ";

    $result = db_query($sql);

    if($result == false ){
      $this->erro_banco      = str_replace("\n","",@pg_last_error());
      $this->erro_sql        = "fiscalintimacao não Alterado. Alteracao Abortada.\\n";
      $this->erro_sql       .= "Valores : ".$this->in01_codnoti;
      $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg       .= str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status     = "0";
      $this->numrows_alterar = 0;
      return false;
    }else{
      if(pg_affected_rows($result) == 0){
        $this->erro_banco      = "";
        $this->erro_sql        = "fiscalintimacao não foi Alterado. Alteracao Executada.\\n";
        $this->erro_sql       .= "Valores : ".$this->in01_codnoti;
        $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg       .= str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status     = "1";
        $this->numrows_alterar = 0;
        return true;
      }else{
        $this->erro_banco      = "";
        $this->erro_sql        = "Alteração efetuada com Sucesso\\n";
        $this->erro_sql       .= "Valores : ".$this->in01_codnoti;
        $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status     = "1";
        $this->numrows_alterar = pg_affected_rows($result);
        return true;
      }
    }
  }

  // funcao para exclusao
  public function excluir(){
    if($this->in01_codnoti != 0){
      $sql = "delete from fiscalizacao.fis_fiscalintimacao where in01_codnoti = $this->in01_codnoti";
    }

    $result = db_query($sql);
    if($result == false){
      $this->erro_banco      = str_replace("\n","",@pg_last_error());
      $this->erro_sql        = "fiscalintimacao não Excluído. Exclusão Abortada.\\n";
      $this->erro_sql       .= "Valores : ".$this->in01_codnoti;
      $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg       .= str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status     = "0";
      $this->numrows_excluir = 0;
      return false;
    }else{
      if(pg_affected_rows($result) == 0){
        $this->erro_banco      = "";
        $this->erro_sql        = "fiscalintimacao não Encontrado. Exclusão não Efetuada.\\n";
        $this->erro_sql       .= "Valores : ".$this->in01_codnoti;
        $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg       .= str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status     = "1";
        $this->numrows_excluir = 0;
        return true;
      }else{
        $this->erro_banco      = "";
        $this->erro_sql        = "Exclusão efetuada com Sucesso\\n";
        $this->erro_sql       .= "Valores : ".$this->in01_codnoti;
        $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status     = "1";
        $this->numrows_excluir = pg_affected_rows($result);
        return true;
      }
    }
  }

}
?>
