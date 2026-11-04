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

namespace ECidade\Educacao\Escola\Repository;

use AtividadeProfissionalEscola;
use cl_rechumanoativ;
use db_utils;
use ECidade\Educacao\Escola\Model\ProfissionalEscola;
use Exception;

/**
 * Class AtividadeProfissionalEscolaRepository
 * @package ECidade\Educacao\Escola\Repository
 */
class AtividadeProfissionalEscolaRepository extends Repository
{
    /**
     * @return AtividadeProfissionalEscola[]
     * @throws Exception
     */
    public function get()
    {
        $oDaoRecHumanoAtiv = new cl_rechumanoativ();
        $sSqlAtividade     = $oDaoRecHumanoAtiv->sql_query_file(
            null,
            "ed22_i_codigo",
            null,
            implode(' AND ', $this->scopes)
        );

        $rsAtividade       = db_query($sSqlAtividade);

        if (!$rsAtividade) {
            throw new Exception("Erro ao buscar atividades do profissionais da escola.");
        }

        $aAtividades = array();

        if (pg_num_rows($rsAtividade) == 0) {
            return $aAtividades;
        }

        $iLinhas = pg_num_rows($rsAtividade);
        
        for ($i = 0; $i < $iLinhas; $i++) {
            $iCodigo       = db_utils::fieldsMemory($rsAtividade, $i)->ed22_i_codigo;
            $aAtividades[] = new AtividadeProfissionalEscola($iCodigo);
        }

        return $aAtividades;
    }

    /**
     * @param ProfissionalEscola $profissionalEscola
     * @return $this
     */
    public function scopeProfissional(ProfissionalEscola $profissionalEscola)
    {
        $this->scopes['profissional_escola'] = (
            "ed22_i_rechumanoescola = {$profissionalEscola->getCodigoVinculoEscola()}"
        );
        
        return $this;
    }
}
