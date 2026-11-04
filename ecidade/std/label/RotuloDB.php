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

//class rotulo_original {
class RotuloDB
{
    //|00|//rotulo
    //|10|//Esta classe gera as variáveis de controle do sistema de uma determinada tabela
    //|15|//[variavel] = new rotulo($tabela);
    //|20|//tabela  : Nome da tabela a ser pesquisada
    //|40|//Gera todas as variáveis de controle dos campos
    //|99|//
    var $tabela;

    function __construct($tabela)
    {
        $this->tabela = $tabela;
    }

    function rlabel($nome = "")
    {
        //#00#//rlabel
        //#10#//Este método gera o label do campo ou campos para relatório
        //#15#//rlabel($nome);
        //#20#//nome  : Nome do campo a ser gerado o label para relatório
        //#20#//        Se não for informado campo, será gerado de todos os campos
        //#40#//Gera a variável label do relatorio do campo rotulorel
        //#99#//A variável será o "RL" mais o nome do campo
        //#99#//Exemplo : campo z01_nome ficará RLz01_nome
        $sCampoTrim = trim($nome);
        $result = db_query("select c.rotulorel
                            from db_syscampo c
                                 join db_sysarqcamp s on s.codcam = c.codcam
                                 join db_sysarquivo a on a.codarq = s.codarq
                            where a.nomearq = '" . $this->tabela . "'
                        " . ($sCampoTrim != "" ? "and c.nomecam = '${sCampoTrim}'" : ""));
        $numrows = pg_numrows($result);
        for ($i = 0; $i < $numrows; $i++) {
            /// variavel para colocar como label de campo
            $variavel = "RL" . trim(pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = ucfirst(trim(pg_result($result, $i, "rotulorel")));
        }
    }

    function label($nome = "")
    {
        //#00#//label
        //#10#//Este método gera o label do arquivo ou de um campo para os formulários
        //#15#//label($nome);
        //#20#//nome  : Nome do campo a ser gerado as variáveis de controle
        //#20#//        Se não informado o campo, será gerado de todos os campos
        //#99#//Nome das variáveis geradas:
        //#99#//"I" + nome do campo -> Tipo de consistencia javascript a ser gerada no formulário (|aceitatipo|)
        //#99#//"A" + nome do campo -> Variavel para determinar o autocomplete no objeto (!autocompl|)
        //#99#//"U" + nome do campo -> Variavel para preenchimento obrigatorio do campo (|nulo|)
        //#99#//"G" + nome do campo -> Variavel para colocar se letras do objeto devem ser maiusculo ou não (|maiusculo|)
        //#99#//"S" + nome do campo -> Variavel para colocar mensagem de erro do javascript de preenchimento de campo (|rotulo|)
        //#99#//"L" + nome do campo -> Variavel para colocar como label de campo (|rotulo|)
        //#99#//                       Coloca o campo com a primeira letra maiuscula e entre tags strong (negrito) (|rotulo|)
        //#99#//"T" + nome do campo -> Variavel para colocat na tag title dos campos (|descricao|)
        //#99#//"M" + nome do campo -> Variavel para incluir o tamanho da propriedade maxlength dos campos (|tamanho|)
        //#99#//"N" + nome do campo -> Variavel para controle da cor de fundo quando o  campo aceitar nulo (|nulo|)
        //#99#//                       style="background-color:#E6E4F1";
        //#99#//"RL"+ nome do campo -> Variavel para colocar como label de campo nos relatorios
        //#99#//"TC"+ nome do campo -> Variavel com o tipo de campo do banco de dados

        $sCampoTrim = trim($nome);
        $result = db_query("select c.descricao,c.rotulo,c.nomecam,c.tamanho,c.nulo,c.maiusculo,c.autocompl,c.conteudo,c.aceitatipo,c.rotulorel
                            from db_sysarquivo a
                                 join db_sysarqcamp s on s.codarq = a.codarq
                                 join db_syscampo c on c.codcam = s.codcam
                            where a.nomearq = '" . $this->tabela . "'
                        " . ($sCampoTrim != "" ? "and c.nomecam = '${sCampoTrim}'" : ""));
        $numrows = pg_numrows($result);
        for ($i = 0; $i < $numrows; $i++) {
            /// variavel com o tipo de campo
            $variavel = trim("I" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = pg_result($result, $i, "aceitatipo");
            /// variavel para determinar o autocomplete
            $variavel = trim("A" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            if (pg_result($result, $i, "autocompl") == 'f') {
                $$variavel = "off";
            } else {
                $$variavel = "on";
            }
            /// variavel para preenchimento obrigatorio
            $variavel = trim("U" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = pg_result($result, $i, "nulo");
            /// variavel para colocar maiusculo
            $variavel = trim("G" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = pg_result($result, $i, "maiusculo");
            /// variavel para colocar no erro do javascript de preenchimento de campo
            $variavel = trim("S" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = pg_result($result, $i, "rotulo");
            /// variavel para colocar como label de campo
            $variavel = trim("L" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = "<strong>" . ucfirst(pg_result($result, $i, "rotulo")) . ":</strong>";

            /// variavel para colocar como label de campo
            $variavel = trim("LS" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = ucfirst(pg_result($result, $i, "rotulo"));

            /// vaariavel para colocat na tag title dos campos
            $variavel = trim("T" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = ucfirst(pg_result($result, $i, "descricao")) . "\n\nCampo:" . pg_result($result, $i,
                    "nomecam");
            /// variavel para incluir o tamanhoda tag maxlength dos campos
            $variavel = trim("M" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = pg_result($result, $i, "tamanho");
            /// variavel para controle de campos nulos
            $variavel = trim("N" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = pg_result($result, $i, "nulo");
            if ($$variavel == "t") {
                $$variavel = "style=\"background-color:#E6E4F1\"";
            } else {
                $$variavel = "";
            }
            /// variavel para colocar como label de campo nos relatorios
            $variavel = trim("RL" . pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = ucfirst(pg_result($result, $i, "rotulorel"));
            /// variavel para colocar o tipo de campo
            $variavel = "TC" . trim(pg_result($result, $i, "nomecam"));
            global ${$variavel};
            $$variavel = pg_result($result, $i, "conteudo");

        }
    }

    function tlabel($nome = "")
    {
        //#00#//tlabel
        //#10#//Este método gera o label do arquivo
        //#15#//tlabel($nome);
        //#20#//nome  : Nome do arquivo para ser gerado o label
        //#40#//Gera a variável label do arquivo "L" + nome do arquivo
        //#99#//Variáveis geradas:
        //#99#//"L" + nome do arquivo -> Label do arquivo
        //#99#//"T" + nome do arquivo -> Texto para a tag title

        $result = db_query("select c.nomearq,c.descricao,c.nomearq,c.rotulo
                            from db_sysarquivo c
                            where c.nomearq = '" . $this->tabela . "'");
        $numrows = pg_numrows($result);
        if ($numrows > 0) {
            $variavel = trim("L" . pg_result($result, 0, "nomearq"));
            global ${$variavel};
            $$variavel = "<strong>" . pg_result($result, 0, "rotulo") . ":</strong>";
            $variavel = trim("T" . pg_result($result, 0, "nomearq"));
            global ${$variavel};
            $$variavel = pg_result($result, 0, "descricao");
        }
    }
    //|XX|//
}