<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2014  DBSeller Servicos de Informatica
 *                      www.dbseller.com.br
 *                   e-cidade@dbseller.com.br
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

class cl_historicoproprietarios extends DAOBasica
{

    public function __construct()
    {
        parent::__construct("cadastro.historicoproprietarios");
    }

    public function sqlHistoricoProprietarios($sWhere)
    {
        $sql = "SELECT
                    *
                FROM
                    cadastro.historicoproprietarios
                    INNER JOIN iptubase ON iptubase.j01_matric = cadastro.historicoproprietarios.j172_matric
                    LEFT JOIN cgm ON cgm.z01_numcgm = cadastro.historicoproprietarios.j172_numcgm
                    LEFT JOIN tipoproprietario ON tipoproprietario.j163_tipoproprietario = cadastro.historicoproprietarios.j172_tipoproprietario
                    LEFT JOIN tipopromitente ON tipopromitente.j164_tipopromitente = cadastro.historicoproprietarios.j172_tipopromitente
                WHERE
                    $sWhere
                ORDER BY j172_data DESC";

        return $sql;
    }

    public function sqlHistoricoProprietariosAverbacao($where)
    {
        $sql = "SELECT
                    j75_matric AS matricula,
                    '' AS protocolo,
                    j75_data AS datahistorico,
                    'Averbaчуo' AS tipolancamento,
                    j163_descricao AS tipoproprietario,
                    '' AS tipopromitente,
                    z01_nome AS adquirente,
                    0 AS percentual,
                    j75_obs AS observacao
                FROM
                    averbacao
                    INNER JOIN averbatipo ON j93_codigo = j75_tipo
                    INNER JOIN iptubase ON j01_matric = j75_matric
                    INNER JOIN cgm ON z01_numcgm = j01_numcgm
                    LEFT JOIN tipoproprietario ON j163_tipoproprietario = j01_tipoproprietario
                WHERE
                    j75_situacao = 2
                    AND j75_matric = $where
                UNION
                SELECT
                    j172_matric AS matricula,
                    j172_protocolo AS protocolo,
                    j172_data AS datahistorico,
                    'Manual' AS tipolancamento,
                    j163_descricao AS tipoproprietario,
                    j164_descricao AS tipopromitente,
                    j172_adquirente AS adquirente,
                    j172_percentual AS percentual,
                    j172_observacao AS observacao
                FROM
                    cadastro.historicoproprietarios
                    INNER JOIN iptubase ON iptubase.j01_matric = cadastro.historicoproprietarios.j172_matric
                    LEFT JOIN cgm ON cgm.z01_numcgm = cadastro.historicoproprietarios.j172_numcgm
                    LEFT JOIN tipoproprietario ON tipoproprietario.j163_tipoproprietario = cadastro.historicoproprietarios.j172_tipoproprietario
                    LEFT JOIN tipopromitente ON tipopromitente.j164_tipopromitente = cadastro.historicoproprietarios.j172_tipopromitente
                WHERE
                    j172_matric = $where
                ORDER BY
                    datahistorico DESC;";

        return $sql;
    }

}

?>