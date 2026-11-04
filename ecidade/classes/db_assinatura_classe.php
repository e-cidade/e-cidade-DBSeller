<?php

/**
 * Esta é o construtor da classe. Ele permite que seja impresso a assinatura do usuario corrente ou
 * de um tipo de assinatura específica a qual será definida nas tabelas db_paragrafos e db_documentos
 * escolhendo o tipo de assinatura de acordo com a tabela db_tipodoc.
 * $classinatura = new cl_assinatura;
 */
class cl_assinatura
{
    /**
     * Este método é usado gerar a assinatura do usuario que gerou o relatório
     * assinatura_usuario()
     * @return false|string|null
     */
    public function assinatura_usuario()
    {
        $result = db_query("
               select *
           from db_usuarios
           where id_usuario = " . db_getsession("DB_id_usuario")
        );
        $nome = pg_fetch_result($result, 0, "nome");
        return $nome;
    }

    /**
     * assinatura
     * Este método é usado gerar a assinatura de acordo com o cadastro de assinaturas
     * assinatura($codigo,$default)
     * codigo  : codigo do tipo de assinatura da tabela db_tipodoc
     * default : string default que será impressa caso o código não seja encontrado
     * pos_paragrafo : qual será o paragrafo retornado, se retornar somente o primeiro, seria somente o nome da pessoa
     * , por exemplo  ( nome, carlos).  0 = nome, 1 = cargo , passar este parementro sempre entre aspas simples
     * na tabela.
     * @param $codigo
     * @param $default
     * @param $pos_paragrafo
     * @return false|mixed|string|null
     */
    public function assinatura($codigo, $default = '', $pos_paragrafo = "")
    {
        $result = db_query("
            select db_paragrafo.*
              from db_documento
              join db_docparag on db03_docum = db04_docum
              join db_paragrafo on db04_idparag = db02_idparag
               where db03_tipodoc = $codigo
                 and db03_instit  = " . db_getsession('DB_instit') . "
             order by db02_descr
        ");

        $ass = $default;
        if (pg_num_rows($result) > 0) {
            $ass = '';
            $ar = "";
            for ($i = 0; $i < pg_num_rows($result); $i++) {
                $db02_texto = pg_fetch_result($result, $i, "db02_texto");
                $ass .= $ar . $db02_texto;
                $ar = "\r\n";
            }
            /**
             * paragrafo : qual será o paragrafo retornado, se retornar somente o primeiro, seria somente o nome da
             * pessoa por exemplo  ( nome, carlos).  0 = nome, 1 = cargo aqui sobrescrevemos as variáveis
             * acima ( codigo pobre )
             */
            if ($pos_paragrafo != "") {
                $ass = '';
                if (pg_num_rows($result) > $pos_paragrafo) {
                    // retorna da posição paragrafo
                    $db02_texto = pg_fetch_result($result, $pos_paragrafo, "db02_texto");
                    $ass = $db02_texto;
                }
            }
        }
        return $ass;
    }
}
