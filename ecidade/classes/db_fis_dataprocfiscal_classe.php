<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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

//MODULO: contabilidade
//CLASSE DA ENTIDADE dataprocfiscal
class cl_fis_dataprocfiscal{
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
   public $ypl01_procfiscal    = 0;
   public $ypl01_dtlanc        = 0;

   // cria propriedade com as variaveis do arquivo
   public $campos = "
                   ypl01_procfiscal  codigo processo fiscal
                   ypl01_dtlanc    - date -  Data de Criaçao,

                 ";
   //funcao construtor da classe
   public function __construct() {
     //classes dos rotulos dos campos
     $this->rotulo = new rotulo("fis_dataprocfiscal");
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
  public function atualizacampos() {

    $this->ypl01_dtlanc_dia = ($this->ypl01_dtlanc_dia == ""?@$GLOBALS["HTTP_POST_VARS"]["ypl01_dtlanc_dia"]:$this->ypl01_dtlanc_dia);
    $this->ypl01_dtlanc_mes = ($this->ypl01_dtlanc_mes == ""?@$GLOBALS["HTTP_POST_VARS"]["ypl01_dtlanc_mes"]:$this->ypl01_dtlanc_mes);
    $this->ypl01_dtlanc_ano = ($this->ypl01_dtlanc_ano == ""?@$GLOBALS["HTTP_POST_VARS"]["ypl01_dtlanc_ano"]:$this->ypl01_dtlanc_ano);
    if($this->ypl01_dtlanc_dia != ""){
      $this->ypl01_dtlanc = "'".$this->ypl01_dtlanc_ano."-".$this->ypl01_dtlanc_mes."-".$this->ypl01_dtlanc_dia."'";
    }

  }
   // funcao para inclusao
  public function incluir (){

    $this->atualizacampos();

    $sSqlInsert = "insert into fiscalizacao.fis_dataprocfiscal(
                                       ypl01_procfiscal
                                      ,ypl01_dtlanc
                       )
                values (
                                      $this->ypl01_procfiscal
                                    , CURRENT_DATE
                      ) ";

    $rsInsert = db_query( $sSqlInsert );

    if( $rsInsert == false ){
      $this->erro_banco = str_replace("\n","",@pg_last_error());

      if( strpos(strtolower( $this->erro_banco ),"duplicate key") != 0 ){
        $this->erro_sql   = "Tipo de Responsavel nao Incluído. Inclusao Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_banco = "Tipo de Responsavel já Cadastrado";
        $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      }else{
        $this->erro_sql   = "Tipo de Responsavel nao Incluído. Inclusao Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg  .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      }

      $this->erro_status     = "0";
      $this->numrows_incluir = 0;

      return false;

    }else{

      $this->erro_banco       = "";
      $this->erro_sql         = "Inclusao efetuada com Sucesso\\n";
      $this->erro_sql        .= "Valores : ".$this->tpr01_codigo;
      $this->erro_msg         = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg        .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status      = "1";
      $this->numrows_incluir  = pg_affected_rows( $rsInsert );

      return true;

    }

  }

// funcao para exclusao
  public function excluir ( $ypl01_procfiscal = null,$dbwhere = null ) {

    $sql = " delete from fiscalizacao.fis_dataprocfiscal
                  where ";
    $sql2 = "";

    if( $dbwhere == null || $dbwhere == "" ){
      if( $ypl01_procfiscal != "" ){
        if( $sql2!= "" ){
          $sql2 .= " and ";
        }
        $sql2 .= " ypl01_procfiscal = $ypl01_procfiscal ";
      }
    }else{
     $sql2 = $dbwhere;
    }

    $result = db_query( $sql.$sql2 );

    if( $result == false ){
     $this->erro_banco  = str_replace("\n","",@pg_last_error());
     $this->erro_sql    = "Responsavel nao Excluído. Exclusão Abortada.\\n";
     $this->erro_sql   .= "Valores : ".$tpr01_sequencial;
     $this->erro_msg    = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
     $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
     $this->erro_status = "0";
     $this->numrows_excluir = 0;

     return false;

    }else{

     if( pg_affected_rows( $result ) == 0 ){
       $this->erro_banco      = "";
       $this->erro_sql        = "Responsavel nao Encontrado. Exclusão não Efetuada.\\n";
       $this->erro_sql       .= "Valores : ".$tpr01_sequencial;
       $this->erro_msg        = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
       $this->erro_msg       .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
       $this->erro_status     = "1";
       $this->numrows_excluir = 0;

       return true;
     }else{
       $this->erro_banco      = "";
       $this->erro_sql        = "Exclusão efetuada com Sucesso\\n";
       $this->erro_sql       .= "Valores : ".$tpr01_sequencial;
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
