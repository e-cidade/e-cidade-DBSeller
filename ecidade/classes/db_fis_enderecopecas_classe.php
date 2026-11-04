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

class cl_fis_enderecopecas{

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
  public $end01_codigo     = null;
  public $end01_codpeca    = 0;
  public $end01_tipopeca   = '';
  public $end01_rua        = '';
  public $end01_numero     = 0;
  public $end01_compl      = '';
  public $end01_bairro     = '';

   public $campos = "
                  end01_codigo   = int = Sequencial
                  end01_codpeca  = int = Chave das peças (Auto, Notificação, Intimação e Levantamento)
                  end01_tipopeca = varchar(2) = Tipos de peças: 'A' = Auto; 'N' =  Notificação; 'I' = Intimação; 'L'= Levantamento
                  end01_rua      = varchar(70) = Endereço Rua
                  end01_numero   = int = Endereço Numero
                  end01_compl    = varchar(70) = Complemento
                                ";

   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_enderecopecas");
     $this->pagina_retorno = basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
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

   // funcao para inclusao
   public function incluir (){

     if( $this->end01_codpeca == 0 ){
       $this->erro_sql    = "Codigo de peças não informada.";
       $this->erro_campo  = "end01_codpeca";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if( $this->end01_tipopeca == '' ){
       $this->erro_sql    = "Tipo de peças não informada.";
       $this->erro_campo  = "end01_tipopeca";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $iSql    = "  insert into fiscalizacao.fis_enderecopecas(                           ";
     $iSql   .= "                       end01_codpeca                   ";
     $iSql   .= "                     , end01_tipopeca                  ";
     $iSql   .= "                     , end01_rua                       ";
     $iSql   .= "                     , end01_numero                    ";
     $iSql   .= "                     , end01_compl                     ";
     $iSql   .= "                     , end01_bairro                    ";
     $iSql   .= "                  )                                    ";
     $iSql   .= "           values (                                    ";
     $iSql   .= "                        $this->end01_codpeca           ";
     $iSql   .= "                     , '$this->end01_tipopeca'         ";
     $iSql   .= "                     , '$this->end01_rua'              ";
     $iSql   .= "                     ,  $this->end01_numero            ";
     $iSql   .= "                     , '$this->end01_compl'            ";
     $iSql   .= "                     , '$this->end01_bairro'           ";
     $iSql   .= "                 )                                     ";

     $rsInsert = db_query($iSql);

    if( $rsInsert == false ){
       $this->erro_banco = str_replace("\n","",@pg_last_error());
       if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
         $this->erro_sql   = $sql."enderecopecas - Erro ao incluir no banco (duplicate key). Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_banco = str_replace("\n","",@pg_last_error());
         $this->erro_msg  .= str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }else{
         $this->erro_sql   = $sql."enderecopecas - Erro ao incluir no banco. Inclusao Abortada.";
         $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       }
       $this->erro_status     = "0";
       $this->numrows_incluir = 0;
       return false;
     }
     $this->erro_banco      = "";
     $this->erro_sql        = "Inclusao efetuada com Sucesso\\n";
     $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status     = "1";
     $this->numrows_incluir = pg_affected_rows($result);
     return true;
   }

   // funcao para alteracao
   public function alterar () {

    if( $this->end01_codpeca == "" ){
       $this->erro_sql    = "Codigo de peças não informada.";
       $this->erro_campo  = "end01_codpeca";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }
     if( $this->end01_tipopeca == "" ){
       $this->erro_sql    = "Tipo de peças não informada.";
       $this->erro_campo  = "end01_tipopeca";
       $this->erro_banco  = "";
       $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status = "0";
       return false;
     }

     $uSql  = " update fiscalizacao.fis_enderecopecas set                          ";
     $uSql .= "              end01_rua    = '$this->end01_rua',           ";
     $uSql .= "              end01_numero =  $this->end01_numero,         ";
     $uSql .= "              end01_bairro = '$this->end01_bairro',        ";
     $uSql .= "              end01_compl  = '$this->end01_compl'          ";
     $uSql .= "          where                                            ";
     $uSql .= "              end01_codpeca      =  $this->end01_codpeca   ";
     $uSql .= "              and end01_tipopeca = '$this->end01_tipopeca' ";
     $rsUpdate = db_query($uSql);

     if( $rsUpdate==false ){
       $this->erro_banco      = str_replace("\n","",@pg_last_error());
       $this->erro_sql        = "enderecopecas não Alterado. Alteração Abortada.\\n";
       $this->erro_sql       .= "Valores : ".$this->end01_codpeca;
       $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status     = "0";
       $this->numrows_alterar = 0;
       return false;
     }else{
       if( pg_affected_rows($rsUpdate) == 0 ){
         $this->erro_banco      = "";
         $this->erro_sql        = "enderecopecas não foi Alterado. Alteração Abortada.\\n";
         $this->erro_sql       .= "Valores : ".$this->end01_codpeca;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_alterar = 0;
         return true;
       }else{
         $this->erro_banco      = "";
         $this->erro_sql        = "Alteração efetuada com Sucesso\\n";
         $this->erro_sql       .= "Valores : ".$this->end01_codpeca;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_alterar = pg_affected_rows($result);
         return true;
       }
     }
   }

   // funcao para exclusao
   public function excluir () {

      $dSql  = " delete from fiscalizacao.fis_enderecopecas  where                  ";
      $dSql .= "              end01_codpeca      =  $this->end01_codpeca   ";
      $dSql .= "              and end01_tipopeca = '$this->end01_tipopeca' ";

      $rsDelete = db_query($dSql);
     if( $rsDelete == false ){
       $this->erro_banco      = str_replace("\n","",@pg_last_error());
       $this->erro_sql        = "enderecopecas não Excluído. Exclusão Abortada.\\n";
       $this->erro_sql       .= "Valores : ".$this->pl36_sequencial;
       $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status     = "0";
       $this->numrows_excluir = 0;
       return false;
     }else{
       if( pg_affected_rows($rsDelete) == 0 ){
         $this->erro_banco      = "";
         $this->erro_sql        = "enderecopecas não Encontrado. Exclusão não Efetuada.\\n";
         $this->erro_sql       .= "Valores : ".$this->pl36_sequencial;
         $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
         $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
         $this->erro_status     = "1";
         $this->numrows_excluir = 0;
         return true;
       }else{
         $this->erro_banco      = "";
         $this->erro_sql        = "Exclusão efetuada com Sucesso\\n";
         $this->erro_sql       .= "Valores : ".$this->pl36_sequencial;
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
