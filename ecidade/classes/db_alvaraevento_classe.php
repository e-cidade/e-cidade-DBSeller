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

/**
 * Class cl_alvaraevento
 */
class cl_alvaraevento extends DAOBasica 
{
    public function __construct() 
    {
        parent::__construct('issqn.alvaraevento');
    }

    public function sqlDadosEvento($campos = '*', $where = array())
    {
    	$sql = "
            SELECT {$campos}
                FROM issqn.alvaraevento 
                    INNER JOIN issqn.isstipoalvara on isstipoalvara.q98_sequencial = alvaraevento.q170_tipoalvara
                    INNER JOIN issqn.ordemservico on ordemservico.q168_codigo =  alvaraevento.q170_ordemservico
                     LEFT JOIN protocolo.cgm as cgm_ordemservico on cgm_ordemservico.z01_numcgm = ordemservico.q168_cgm
                     LEFT JOIN issqn.issbase on issbase.q02_inscr = ordemservico.q168_inscricao
                     LEFT JOIN protocolo.cgm as cgm_inscricao on cgm_inscricao.z01_numcgm = issbase.q02_numcgm
        ";

        if (!empty($where)) {
        	$where = implode(" AND ", $where);
        	$sql .= " WHERE {$where} ";
       }
        
        $sql .= " ORDER BY q170_codigo ";

        return $sql;
    }
}
