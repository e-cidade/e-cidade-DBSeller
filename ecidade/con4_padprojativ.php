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

class projativ {
     var $arq=null;

  public function __construct($header){
     umask(74);
     $this->arq = fopen("tmp/PROJATIV.TXT",'w+');
     fputs($this->arq,$header);
     fputs($this->arq,"\r\n");
  }

  public function processa($instit=1,$data_ini="",$data_fim="",$tribinst="",$subelemento="") {
      global $contador,$nomeinst;

    $where = "";

    $contador=0;
    $exercicios = "";
    $virg = "";

    ///// abre arquivo dos exercícios anteriores

    $clarqpad = new cl_conarquivospad;
    $res =$clarqpad->sql_record(
               $clarqpad->sql_query(null,"*",null," c54_nomearq = 'PROJATIV.TXT' and c54_anousu=".db_getsession("DB_anousu")."  and c54_codtrib = $tribinst "));

    if($clarqpad->numrows > 0){
      $rubant =explode("\r\n",pg_result($res,0,"c54_arquivo"));
      for($yy=0;$yy<sizeof($rubant);$yy++){
         $contador++;
         $line = $rubant[$yy];

         $exercicios .= $virg.substr($rubant[$yy],0,4);
         $virg = ",";

         fputs($this->arq,$line);
         fputs($this->arq,"\r\n");
      }
    }

    //////

      $anoSessao = db_getsession('DB_anousu');
      $anoAnterior = db_getsession('DB_anousu') - 1;

      $sql = "select x.*,'2' as identificador
              from
              (select distinct
                      o55_anousu as anousu,
                      o55_projativ as codigo,
                      o55_descr as nome
                 from orcprojativ
	                  inner join orcdotacao on o58_projativ = o55_projativ
		                                   and o58_anousu   = o55_anousu
                                           and o55_anousu   in ({$anoSessao}, {$anoAnterior})
               union
               select distinct
                      e60_anousu as anousu,
                      o55_projativ as codigo,
                      o55_descr as nome
                 from orcprojativ
                      inner join orcdotacao on o58_projativ = o55_projativ
                                           and o58_anousu   = o55_anousu
                      inner join empempenho on e60_coddot   = o58_coddot
                                           and e60_anousu   = o58_anousu
                      inner join empresto   on e91_numemp   = e60_numemp
                where e91_anousu = {$anoSessao}
	       ) as x ";

  	   if (!empty($exercicios))
	     $sql .= " where not anousu in ($exercicios) ";

  	   $res  = db_query($sql);
       $rows = pg_numrows($res);
       for ($x=0;$x < $rows;$x++){
           $anousu = formatar(pg_result($res,$x,"anousu"),4,'n');
           $codigo  = formatar(pg_result($res,$x,"codigo"),5,'n');
           $nome = formatar(pg_result($res,$x,"nome"),80,'c');
           $identificador = formatar(pg_result($res,$x,"identificador"),2,'n');

           //--
           $line = $anousu.$codigo.$nome.$identificador;
           fputs($this->arq,$line);
           fputs($this->arq,"\r\n");

           $contador = $contador+1; // incrementa contador global
       }

       //  trailer
       $contador = espaco(10-(strlen($contador)),'0').$contador;
       $line = "FINALIZADOR".$contador;
       fputs($this->arq,$line);
       fputs($this->arq,"\r\n");

       fclose($this->arq);

       $teste="true";
       return $teste;

  }
}
?>
