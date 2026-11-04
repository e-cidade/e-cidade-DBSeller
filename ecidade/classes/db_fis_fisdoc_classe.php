<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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
 *  junto com este programa; se não, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

class cl_fis_fisdoc{
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

  public $fd01_codigo     = null;
  public $fd01_codtipo    = 0;
  public $fd01_doc        = 0;
  public $fd01_instit     = 0;

   //funcao construtor da classe
  public function __construct() {
    //classes dos rotulos dos campos
    $this->rotulo = new rotulo("fis_fisdoc");
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

   // funcao para Inclusão
  public function incluir (){

    if($this->fd01_doc == ''){
      $this->erro_sql    = " Campo Documento Template não Informado.";
      $this->erro_campo  = "fd01_doc";
      $this->erro_banco  = "";
      $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }

    $sSql  = "insert into fiscalizacao.fis_fisdoc (                 ";
    $sSql .= "                         fd01_codtipo        ";
    $sSql .= "                        ,fd01_doc            ";
    $sSql .= "                        ,fd01_instit )       ";
    $sSql .= "             values (                        ";
    $sSql .= "                         $this->fd01_codtipo ";
    $sSql .= "                        ,$this->fd01_doc     ";
    $sSql .= "                        ,$this->fd01_instit) ";
    $Result = db_query($sSql);

    if($Result == false){
      $this->erro_banco = str_replace("\n","",@pg_last_error());
      if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
        $this->erro_sql   = "Tipo de Fiscal ($this->fd01_codtipo) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_banco =  str_replace("\n","",@pg_last_error());
        $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      }else{
        $this->erro_sql   = " Tipo do Documento ($this->fd01_doc) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      }
      $this->erro_status     = "0";
      $this->numrows_incluir =  0;
      return false;
    }
    $this->erro_banco      = str_replace("\n","",@pg_last_error());
    $this->erro_sql        = "Inclusão efetuada com Sucesso\\n";
    $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
    $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
    $this->erro_status     = "1";
    $this->numrows_incluir = pg_affected_rows($Result);
    return true;
  }

   // funcao para alteracao
  public function alterar () {

    if($this->fd01_doc == ''){
      $this->erro_sql    = " Campo Documento Template não Informado.";
      $this->erro_campo  = "fd01_doc";
      $this->erro_banco  = "";
      $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }

    $sSql  = "  update fiscalizacao.fis_fisdoc set                  ";
    $sSql .= "        fd01_doc = $this->fd01_doc           ";
    $sSql .= "  where fd01_codtipo = $this->fd01_codtipo   ";
    $sSql .= "        and fd01_instit = $this->fd01_instit ";

    $Result = db_query($sSql);

    if($Result == false){
      $this->erro_banco       = str_replace("\n","",@pg_last_error());
      $this->erro_sql         = "Tipo de Fiscal ($this->fd01_codtipo) não Alterado. Alteração Abortada.\\n";
      $this->erro_sql        .= "Valores : ".$this->fd01_codtipo;
      $this->erro_msg         = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg        .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status      = "0";
      $this->numrows_alterar  =  0;
      return false;
    }else{
      if(pg_affected_rows($Result)==0){
        $this->erro_banco      = "";
        $this->erro_sql        = "Tipo do Documento ($this->fd01_doc) não Alterado. Alteração Abortada.\\n";
        $this->erro_sql       .= "Valores : ".$this->fd01_doc;
        $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status     = "1";
        $this->numrows_alterar =  0;
        return true;
      }else{
        $this->erro_banco      = "";
        $this->erro_sql        = "Alteração efetuada com Sucesso\\n";
        $this->erro_sql       .= "Valores : ".$this->pl11_codigo;
        $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status     = "1";
        $this->numrows_alterar = pg_affected_rows($Result);
        return true;
      }
    }
  }

   // funcao para exclusao
  public function excluir () {

    $sSql  = "  delete from fiscalizacao.fis_fisdoc                   ";
    $sSql .= "     where fd01_codtipo = $this->fd01_codtipo  ";
    $sSql .= "   and fd01_instit = $this->fd01_instit        ";

    $Result = db_query($sSql);

    if($Result == false){
      $this->erro_banco      = str_replace("\n","",@pg_last_error());
      $this->erro_sql        = "Documento não Excluído. Exclusão Abortada.\\n";
      $this->erro_sql       .= "Valores : ".$this->fd01_codtipo;
      $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status     = "0";
      $this->numrows_excluir =  0;
      return false;
    }else{
      if(pg_affected_rows($Result)==0){
        $this->erro_banco      = "";
        $this->erro_sql        = "Documento não Encontrado. Exclusão não Efetuada.\\n";
        $this->erro_sql       .= "Valores : ".$this->fd01_doc;
        $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status     = "1";
        $this->numrows_excluir =  0;
        return true;
      }else{
        $this->erro_banco      = "";
        $this->erro_sql        = "Exclusão efetuada com Sucesso\\n";
        $this->erro_sql       .= "Valores : ".$this->fd01_codtipo;
        $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status     = "1";
        $this->numrows_excluir = pg_affected_rows($Result);
        return true;
      }
    }
  }
}

?>
