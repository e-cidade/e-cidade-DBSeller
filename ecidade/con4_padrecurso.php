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


class recurso
{
    public $arq = null;

    public function __construct($header)
    {
        umask(74);
        $this->arq = fopen("tmp/RECURSO.TXT", 'w+');
        fputs($this->arq, $header);
        fputs($this->arq, "\r\n");
    }

    public function processa($instit = 1, $data_ini = "", $data_fim = "", $tribinst = "", $subelemento = "")
    {
        global $contador, $nomeinst;

        $sql = "
            select distinct on (o15_recurso)
                   o15_codigo as codigo,
    	           o15_descr  as nome,
                   o15_finali as finalidade,
                   o15_recurso
              from orctiporec
             where o15_codigo > 0
               and trim(o15_descr) != ''
               and o15_recurso != ''
             order by o15_recurso
        ";

        $res = db_query($sql);
        $rows = pg_num_rows($res);

        for ($x = 0; $x < $rows; $x++) {
            $oDadosRecurso = db_utils::fieldsMemory($res, $x);
            $codigo = formatar($oDadosRecurso->o15_recurso, 4, 'n');
            $nome = addslashes($oDadosRecurso->nome);
            $nome = formatar($nome, 80, 'c');

            $fina = addcslashes($oDadosRecurso->finalidade, "\r\n");
            $finalidade = formatar($fina, 160, 'c');

            $line = $codigo . $nome . $finalidade;
            fputs($this->arq, $line);
            fputs($this->arq, "\r\n");

            $contador = $contador + 1; // incrementa contador global
        }

        //  trailer
        $contador = espaco(10 - (strlen($contador)), '0') . $contador;
        $line = "FINALIZADOR" . $contador;
        fputs($this->arq, $line);
        fputs($this->arq, "\r\n");

        fclose($this->arq);

        $teste = "true";
        return $teste;
    }
}
