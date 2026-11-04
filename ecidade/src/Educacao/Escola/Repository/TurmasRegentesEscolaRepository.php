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

use Exception;

/**
 * Class TurmasRegentesEscolaRepository
 * @package ECidade\Educacao\Escola\Repository
 */
class TurmasRegentesEscolaRepository extends Repository
{

    public function get($codigoCgm, $codigoEscola, $dataLogin, $campos = [], $orderBy = [], $validarAno = true)
    {
        $campos  = implode(', ', $campos);
        $orderBy = implode(', ', $orderBy);

        $whereAnoLetivo = "";
        if ($validarAno) {
            $whereAnoLetivo = " and ed52_i_ano = extract(year from '{$dataLogin}'::date)
                                and extract (month FROM CURRENT_DATE)
                                between extract(month from ed52_d_inicio) AND extract(month from ed52_d_fim) ";
        }

        $whereCgm = "";
        if (!is_null($codigoCgm)) {
            $whereCgm = " (rhpessoal.rh01_numcgm = {$codigoCgm} or rechumanocgm.ed285_i_cgm = {$codigoCgm}) and ";
        }

        $sql = "
        with profissional as (
            select distinct ed20_i_codigo
                 from rechumano
                 join rechumanoescola on rechumanoescola.ed75_i_rechumano = rechumano.ed20_i_codigo
                                     and rechumanoescola.ed75_i_saidaescola is null
                left join rechumanocgm on rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
                left join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = ed20_i_codigo
                left join rhpessoal on rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
                where {$whereCgm} rechumanoescola.ed75_i_escola = {$codigoEscola}
        ), regencias_normal as (
            select distinct regenciahorario.ed58_i_regencia as codigo
              from profissional
              join regenciahorario on regenciahorario.ed58_i_rechumano = profissional.ed20_i_codigo
              join regencia on regencia.ed59_i_codigo = regenciahorario.ed58_i_regencia
             where ed58_ativo is true
        ), regencias_substituta as  (
            select distinct docentesubstituto.ed322_regencia  as codigo
              from profissional
             join docentesubstituto on docentesubstituto.ed322_rechumano = profissional.ed20_i_codigo
              join regenciahorario on regenciahorario.ed58_i_regencia = docentesubstituto.ed322_regencia
              join regencia on regencia.ed59_i_codigo = regenciahorario.ed58_i_regencia
              join turma on turma.ed57_i_codigo = regencia.ed59_i_turma
              join calendario on calendario.ed52_i_codigo = turma.ed57_i_calendario
             where ed58_ativo is true
               and ed59_c_freqglob <> 'A'
               and '{$dataLogin}' >= ed322_periodoinicial
               and (    (ed322_periodofinal is null and '{$dataLogin}' <= ed52_d_fim)
                     or '{$dataLogin}' <= ed322_periodofinal
                   )
        ), regencias as (
            select * from regencias_normal
            union all
            select * from regencias_substituta
        ) select {$campos}
         from regencias
         join regencia on regencia.ed59_i_codigo = regencias.codigo
         join serie on  serie.ed11_i_codigo = regencia.ed59_i_serie
         join turma on turma.ed57_i_codigo = regencia.ed59_i_turma
         join calendario on calendario.ed52_i_codigo = turma.ed57_i_calendario
         join disciplina on disciplina.ed12_i_codigo = regencia.ed59_i_disciplina
         join caddisciplina on caddisciplina.ed232_i_codigo = disciplina.ed12_i_caddisciplina
         left join turmaserieregimemat on ed220_i_turma = ed57_i_codigo
        where ed57_i_escola = {$codigoEscola}
            {$whereAnoLetivo}
        ";

        if (!empty($orderBy)) {
            $sql .= "order by {$orderBy}";
        }

        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar turmas do regente");
        }

        $dados = [];

        while ($state = pg_fetch_array($rs)) {
            $dados[] = $state;
        }

        return $dados;
    }
}
