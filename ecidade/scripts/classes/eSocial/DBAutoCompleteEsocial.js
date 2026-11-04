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
const DBAutoCompleteEsocial = function () {
};

DBAutoCompleteEsocial.gerarAutoComplete = function () {
    const dePara = DBAutoCompleteEsocial.tabelas();

    dePara.map(function (data) {

        var elemento = document.querySelector("input[identificador=" + data.identificador + "]");
        if (elemento) {
            new DBAutoComplete(elemento, data.tabela, data.permiteNulo);
        }
    });
};


DBAutoCompleteEsocial.tabelas = function () {
    return [
        {"identificador": "natRubr", "tabela": "arquivos/esocial/tabelas/tabela03.json", "permiteNulo" : false},
        {"identificador": "paisNascto", "tabela": "arquivos/esocial/tabelas/tabela06.json", "permiteNulo" : false},
        {"identificador": "paisResid", "tabela": "arquivos/esocial/tabelas/tabela06.json", "permiteNulo" : false},
        {"identificador": "tpLograd", "tabela": "arquivos/esocial/tabelas/tabela20.json", "permiteNulo" : false},
        {"identificador": "paisNac", "tabela": "arquivos/esocial/tabelas/tabela06.json", "permiteNulo" : false},
        {"identificador": "codTerc", "tabela": "arquivos/esocial/tabelas/tabela04.json", "permiteNulo" : true},
        {"identificador": "codTercs", "tabela": "arquivos/esocial/tabelas/tabela04.json", "permiteNulo" : true},
        {"identificador": "cnaePrep", "tabela": "arquivos/esocial/tabelas/tabelaRAT.json", "permiteNulo" : false},
        {"identificador": "classTrib", "tabela": "arquivos/esocial/tabelas/tabela08.json", "permiteNulo" : false},
        {"identificador": "codMunic", "tabela": "arquivos/esocial/tabelas/tabelamunicipios.json", "permiteNulo" : true},
        {"identificador": "codMunic_brasil", "tabela": "arquivos/esocial/tabelas/tabelamunicipios.json", "permiteNulo" : true},
        {"identificador": "brasil_codMunic", "tabela": "arquivos/esocial/tabelas/tabelamunicipios.json", "permiteNulo" : true},
        {"identificador": "instEnsino_codMunic", "tabela": "arquivos/esocial/tabelas/tabelamunicipios.json", "permiteNulo" : true},
        {"identificador": "ageIntegracao_codMunic", "tabela": "arquivos/esocial/tabelas/tabelamunicipios.json", "permiteNulo" : true},
        {"identificador": "codCBO", "tabela": "arquivos/esocial/tabelas/tabela_CBO.json", "permiteNulo" : false},
        {"identificador": "CBOCargo", "tabela": "arquivos/esocial/tabelas/tabela_CBO.json", "permiteNulo" : false},
        {"identificador": "tpEvento", "tabela": "arquivos/esocial/tabelas/tabela_tipo_evento_exclusao.json", "permiteNulo" : false},
        {"identificador": "codCateg", "tabela": "arquivos/esocial/tabelas/tabela01.json", "permiteNulo" : true},
        {"identificador": "codMotAfast", "tabela": "arquivos/esocial/tabelas/tabela18.json", "permiteNulo" : true},
        {"identificador": "paisResid_exterior", "tabela": "arquivos/esocial/tabelas/tabela06.json", "permiteNulo" : true},
        {"identificador": "codCargo", "tabela": "eso4_autocompleteesocial.RPC.php?exec=buscarCargos", "permiteNulo" : true},
        {"identificador": "codFuncao", "tabela": "eso4_autocompleteesocial.RPC.php?exec=buscarFuncoes", "permiteNulo" : true},
        {"identificador": "dtBase", "tabela": "arquivos/esocial/tabelas/tabela_mes.json", "permiteNulo" : true},
        {"identificador": "ufVara", "tabela": "arquivos/esocial/tabelas/uf.json", "permiteNulo" : true},
        {"identificador": "tpServico", "tabela": "arquivos/esocial/tabelas/efd_tabela06.json", "permiteNulo" : true},
        {"identificador": "tpInscAnt", "tabela": "arquivos/esocial/tabelas/tabela05.json", "permiteNulo": true },
        {"identificador": "cnpjEFR", "tabela": "arquivos/esocial/tabelas/tabelaCnpjEFR.json", "permiteNulo" : true},
        {"identificador": "tpInsc", "tabela": "arquivos/esocial/tabelas/tabela05.json", "permiteNulo" : true},
        {"identificador": "codSitGeradora", "tabela": "arquivos/esocial/tabelas/tabela15.json", "permiteNulo" : false},
        {"identificador": "codParteAting", "tabela": "arquivos/esocial/tabelas/tabela13.json", "permiteNulo" : false},
        {"identificador": "codAgntCausador", "tabela": "arquivos/esocial/tabelas/tabelas14_15.json", "permiteNulo" : false},
        {"identificador": "dscLesao", "tabela": "arquivos/esocial/tabelas/tabela17.json", "permiteNulo" : false}
    ];
};
