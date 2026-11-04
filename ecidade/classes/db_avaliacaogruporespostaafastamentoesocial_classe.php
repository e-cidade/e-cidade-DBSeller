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
 * Class cl_avaliacaogruporespostaafastamentoesocial
 * @property $eso13_sequencial
 * @property $eso13_avaliacaogruporesposta
 * @property $eso13_afastamentoservidoresocial
 * @property $eso13_empregador
 * @property $eso13_avaliacao
 */
class cl_avaliacaogruporespostaafastamentoesocial extends DAOBasica
{
    /**
     * cl_avaliacaogruporespostaafastamentoesocial constructor.
     */
    public function __construct()
    {
        parent::__construct('esocial.avaliacaogruporespostaafastamentoesocial');
    }

    /**
     * Busca todos os preenchimentos de afastamento seguindo as seguintes regras:
     * Caso o motivo do afastamento seja Férias (15), busca os afastamentos com a data inicial até 60 dias posteriores a competência da folha
     * Nos demais casos, busca os afastamentos somente da competência atual da folha
     * @param $instituicao
     * @param array $where
     * @return string
     */
    public function sql_avaliacao_afastamentos($instituicao, array $where, $ano = null, $mes = null, $cgmEmpregador = null)
    {
        $where = implode(' AND ', $where);

        $anoFolha = "fc_anofolha({$instituicao})";
        $mesFolha = "fc_mesfolha({$instituicao})";
        if (!empty($ano)) {
            $anoFolha = $ano;
        }

        if (!empty($mes)) {
            $mesFolha = $mes;
        }

        $sql = "
            SELECT
               preenchimento.preenchimento,
               preenchimento.identificador,
               preenchimento.matricula,
               preenchimento.inscricao_empregador,
               data_inicial,
               data_retorno
            FROM (
                SELECT DISTINCT
                    db107_sequencial AS preenchimento,
                    eso12_assenta AS identificador,
                    eso12_rhpessoal AS matricula,
                    avaliacaopergunta.db103_descricao,
                    (SELECT
                        avaliacaoresposta.db106_resposta
                    FROM
                        avaliacaopergunta ap
                        JOIN avaliacaoperguntaopcao apo ON apo.db104_avaliacaopergunta = ap.db103_sequencial
                        JOIN avaliacaoresposta AS ar ON ar.db106_avaliacaoperguntaopcao = apo.db104_sequencial
                    WHERE
                        db104_identificadorcampo = 'codMotAfast'
                        AND ar.db106_sequencial = avaliacaoresposta.db106_sequencial
                    ) AS mtv,
                    h16_dtconc AS data_inicial,
                    h16_dtterm AS data_retorno,
                    z01_cgccpf AS inscricao_empregador
                FROM
                    avaliacaogruporespostaafastamentoesocial
                    JOIN afastamentoservidoresocial ON eso12_sequencial = eso13_afastamentoservidoresocial
                    JOIN assenta ON eso12_assenta = h16_codigo
                    JOIN rhpessoal ON rh01_regist = h16_regist
                    JOIN avaliacaogruporesposta ON db107_sequencial = eso13_avaliacaogruporesposta
                    JOIN avaliacaogrupoperguntaresposta ON db108_avaliacaogruporesposta = db107_sequencial
                    JOIN avaliacaoresposta ON db106_sequencial = db108_avaliacaoresposta
                    JOIN avaliacaoperguntaopcao ON db104_sequencial = db106_avaliacaoperguntaopcao
                    JOIN avaliacaopergunta ON db103_sequencial = db104_avaliacaopergunta
                    JOIN cgm ON z01_numcgm = {$cgmEmpregador}
                WHERE {$where}
                ORDER BY db103_descricao
            ) AS preenchimento
            WHERE
                mtv is not null
                AND (
                    extract(MONTH FROM data_inicial) = {$mesFolha}
                    AND extract(YEAR FROM data_inicial) = {$anoFolha}
                ) OR (
                    data_retorno is not null
                    AND  extract(month from data_retorno) = {$mesFolha}
                    AND extract(year from data_retorno) = {$anoFolha}
                )
            order by matricula asc, data_inicial asc;
        ";
        return $sql;
    }
}