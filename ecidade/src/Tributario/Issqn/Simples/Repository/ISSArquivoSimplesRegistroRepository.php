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

namespace ECidade\Tributario\Issqn\Simples\Repository;

use ECidade\Tributario\Issqn\Simples\Entity\ISSArquivoSimplesRegistro;
use BusinessException;
use cl_issarqsimplesregerro;
use db_utils;

class ISSArquivoSimplesRegistroRepository
{
    /**
     * Retorna os registros do arquivos
     *
     * @param numeric $arquivo Sequencial do arquivo
     * @return ISSArquivoSimplesRegistro[]
     */
    public function getRegistrosByArquivo($arquivo)
    {
        if (!is_numeric($arquivo)) {
            throw new BusinessException("Arquivo id: {$arquivo} inválido");
        }

        $sql = "
            select
            issarqsimplesreg.*,
            (select
                json_agg(json_build_object(
                        'cgm', cgm.z01_numcgm,
                        'inscr', coalesce(regiss.q134_inscr, issbase.q02_inscr),
                        'vinc', regiss.q134_issarqsimplesreg,
                        'cnpj', cgm.z01_cgccpf))
                from cgm
                left join issbase on issbase.q02_numcgm = cgm.z01_numcgm and
                    (issbase.q02_dtbaix is null or issbase.q02_dtbaix >=
                    make_date(issarqsimplesreg.q23_anousu, issarqsimplesreg.q23_mesusu, 1))
                left join issarqsimplesregissbase regiss
                    on regiss.q134_issarqsimplesreg = issarqsimplesreg.q23_sequencial
                    and regiss.q134_inscr = issbase.q02_inscr
                where
                    left(z01_cgccpf,8) = left(issarqsimplesreg.q23_cnpj,8) and length(z01_cgccpf) = 14
            ) as cnpj_vinculados
            from issarqsimplesreg
            where q23_issarqsimples = {$arquivo}
        ";

        $resourceQuery = db_query($sql);
        if (!$resourceQuery) {
            throw new BusinessException("Não foi possível recuperar dados do arquivo {$arquivo}");
        }

        $registros = [];
        $recordSet = db_utils::getCollectionByRecord($resourceQuery);
        if (!empty($recordSet)) {
            foreach ($recordSet as $registro) {
                $registros[] = new ISSArquivoSimplesRegistro($registro);
            }
        }
        return $registros;
    }

    /**
     * Limpar erros existentes do arquivo (issarqsimplesregerro)
     *
     * @param numeric $arquivo Sequencial do arquivo
     * @return void
     */
    public function limparErrosExistentesDoArquivo($arquivo)
    {
        if (!is_numeric($arquivo)) {
            throw new BusinessException("Arquivo id: {$arquivo} inválido");
        }

        $daoIssArqSimplesRegErro = new cl_issarqsimplesregerro();
        $sWhereExclusaoErros = "q49_sequencial in (select q23_sequencial ";
        $sWhereExclusaoErros .= " from issarqsimplesreg ";
        $sWhereExclusaoErros .= " where q23_issarqsimples = {$arquivo})";
        $daoIssArqSimplesRegErro->excluir(null, $sWhereExclusaoErros);

        if ($daoIssArqSimplesRegErro->erro_status == "0") {
            throw new BusinessException("Erro ao deletar inconsistências do arquivo.");
        }
    }

    /**
     * Retorna a data do arquivo simples (issarqsimples)
     *
     * @param nuneric $arquivo
     * @return string
     */
    public function getDataArquivoSimples($arquivo)
    {
        if (!is_numeric($arquivo)) {
            throw new BusinessException("Arquivo id: {$arquivo} inválido");
        }

        $sql = "select q17_data from issarqsimples where q17_sequencial = {$arquivo}";
        $resourceQuery = db_query($sql);
        if (!$resourceQuery) {
            throw new BusinessException("Não foi possível recuperar data do arquivo {$arquivo}");
        }

        $recordSet = pg_fetch_object($resourceQuery, 0);
        return $recordSet->q17_data;
    }
}
