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
 * Class cl_retencaoreceitasadicionais
 */
class cl_retencaoreceitasadicionais extends \DAOBasica {

    public function __construct() {
        parent::__construct("empenho.retencaoreceitasadicionais");
    }


    function sql_query_notascgmretencaoadicionais($where=array(), $campos="") {

        $sql = "SELECT ";
        if ($campos != "") {
            $sql .= "$campos, ";
        }
        $sql .= "
            case
                when emptiposervicoobra.e154_tipo::integer > 0
                then emptiposervicoobra.e154_cno
                else cgmprestador.z01_cgccpf
            end as identificador_prestador,
            cgmprestador.z01_numcgm as cgm_prestador,
            cgmprestador.z01_cgccpf as cnpj_prestador,
            pc60_indicativocprb as indicativo_cprb,
            cgmcontribuinte.z01_cgccpf as cnpj_contribuinte,
            emptiposervicoobra.e154_tipo as indicativo_obra_tipo,
            emptiposervicoobra.e154_cno as indicativo_obra_cno,
            e69_codnota as codigo_nota,
            e69_numero as numero_nota,
            e69_dtnota as data_emissao,
            empnota.e69_serienota as serie_nota,
            e18_referencia as referencia_tipo_servico,
            retencaoreceitas.e23_valorretencao as valor_retencao,
            retencaoreceitas.e23_valorbase as valor_base_retido,
            retencaoreceitas.e23_aliquota as aliquota,
            empnotaele.e70_vlrliq as valor_bruto,
            e19_valornaoretidoprincipal as valor_nao_retido_principal,
            e19_valorservico15 as valor_servicos_15,
            e19_valorservico20 as valor_servicos_20,
            e19_valorservico25 as valor_servicos_25,
            e19_valoradicional as valor_adicional,
            e19_valornaoretidoadicional as valor_nao_retido_adicional,
            e19_indvalorbase as indicativo_valor_base,
            o41_cnpj as cnpj_unidade,
	        (select sum(b.e70_vlrliq)
		        from empnota a
		        inner join empnotaele b on b.e70_codnota = a.e69_codnota
		        left join pagordemnota c on a.e69_codnota = c.e71_codnota and c.e71_anulado is false
		        inner join empempenho d on d.e60_numemp = a.e69_numemp
		        where
		        d.e60_numcgm = cgmprestador.z01_numcgm and
		        a.e69_numero = empnota.e69_numero and
		        a.e69_codnota <> empnota.e69_codnota
	        ) as notas_nao_retidas,
            count(e69_numero) over(partition by e69_numero, cgmprestador.z01_numcgm) as qnt_nota";
        $sql .= "
            FROM
                retencaoreceitasadicionais
                INNER JOIN tiposerviconotafiscal on tiposerviconotafiscal.e18_sequencial = retencaoreceitasadicionais.e19_tiposerviconotafiscal
                INNER JOIN retencaoreceitas on retencaoreceitas.e23_sequencial = retencaoreceitasadicionais.e19_retencaoreceitas
                INNER JOIN retencaotiporec on retencaotiporec.e21_sequencial = retencaoreceitas.e23_retencaotiporec
                INNER JOIN retencaopagordem on retencaopagordem.e20_sequencial = retencaoreceitas.e23_retencaopagordem
                INNER JOIN pagordem on pagordem.e50_codord = retencaopagordem.e20_pagordem
                INNER JOIN pagordemnota on e71_codord = pagordem.e50_codord
                INNER JOIN empnota on empnota.e69_codnota = pagordemnota.e71_codnota and pagordemnota.e71_anulado is false
                INNER JOIN empnotaele on empnotaele.e70_codnota = empnota.e69_codnota
                INNER JOIN empempenho on empempenho.e60_numemp = pagordem.e50_numemp
                INNER JOIN db_config on db_config.codigo = empempenho.e60_instit
                INNER JOIN cgm as cgmcontribuinte on cgmcontribuinte.z01_numcgm = db_config.numcgm
                INNER JOIN orcdotacao on empempenho.e60_coddot = orcdotacao.o58_coddot and empempenho.e60_anousu = orcdotacao.o58_anousu
                INNER JOIN orcunidade on orcdotacao.o58_anousu = orcunidade.o41_anousu and orcdotacao.o58_orgao = orcunidade.o41_orgao and orcdotacao.o58_unidade = orcunidade.o41_unidade
                INNER JOIN retencaoempagemov on e27_retencaoreceitas = e23_sequencial
                INNER JOIN empagemov on e81_codmov = e27_empagemov
                LEFT JOIN emptiposervicoobra on emptiposervicoobra.e154_numemp = empempenho.e60_numemp
                LEFT JOIN pagordemconta on pagordemconta.e49_codord = pagordem.e50_codord
                LEFT JOIN cgm as cgmprestador on cgmprestador.z01_numcgm = coalesce(pagordemconta.e49_numcgm, empempenho.e60_numcgm)
                LEFT JOIN pcforne on pcforne.pc60_numcgm = cgmprestador.z01_numcgm
            WHERE e81_cancelado is null ";
        if(count($where) > 0) {
            $where = implode(" AND ", $where);
            $sql .= " AND {$where} ";
        }
        return $sql;
    }
}
