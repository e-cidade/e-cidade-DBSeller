<?php

namespace App\Domain\Patrimonial\Contratos\Services;

use LicitacaoModalidadeRepository;

class AcordoLicitacaoCompartilhadaService
{
    const NAO_SE_APLICA = '0';
    public function salvar($request)
    {
        $dao = new \cl_acordolicitacaocompartilhada();
        $dao->ac63_acordo = $request->ac63_acordo;
        $dao->ac63_licitacaocompartilhada = $request->ac63_licitacaocompartilhada;
        $dao->ac63_numcgm = $request->ac63_numcgm;
        $dao->ac63_numero = $request->ac63_numero;
        $dao->ac63_ano = $request->ac63_ano;
        $dao->ac63_modalidade = $request->ac63_modalidade;
        $sql = $dao->sql_query_file(null, 'ac63_sequencial', null, 'ac63_acordo = ' . $request->ac63_acordo);
        $rs = $dao->sql_record($sql);

        if (!$rs) {
            throw new \Exception('Erro ao buscar licitação compartilhada');
        }

        if ($request->ac63_numcgm === "") {
            return;
        }

        if ($dao->numrows == 0) {
            $dao->incluir(null);
        } else {
            $codigo = \db_utils::fieldsMemory($rs, 0)->ac63_sequencial;
            $dao->alterar($codigo);
            if ($request->ac63_licitacaocompartilhada === self::NAO_SE_APLICA) {
                try {
                    $this->remover($request->ac63_acordo);
                } catch (\Exception $e) {
                    throw new \Exception('Erro ao remover licitação compartilhada.');
                }
            }
        }

        if ($dao->erro_status == '0') {
            throw new \Exception("Erro ao salvar licitação compartilhada\n{$dao->erro_msg}");
        }
    }

    public function remover($acordo)
    {
        $dao = new \cl_acordolicitacaocompartilhada();
        $sql = $dao->sql_query_file(null, 'ac63_sequencial', null, 'ac63_acordo = ' . $acordo);
        $rs = $dao->sql_record($sql);

        if (!$rs) {
            throw new \Exception('Erro ao buscar licitação compartilhada');
        }

        if ($dao->numrows > 0) {
            $codigo = \db_utils::fieldsMemory($rs, 0)->ac63_sequencial;
            $dao->excluir($codigo);
        }
    }
    public function getModalidade($codigoModalidade)
    {
        return LicitacaoModalidadeRepository::getByCodigo($codigoModalidade);
    }

    public function buscarDados($acordo)
    {
        $acordoLicitacaoCompartilhada = new \cl_acordolicitacaocompartilhada();
        $where = "ac63_acordo = " . $acordo;
        $sql = $acordoLicitacaoCompartilhada->sql_query_file(null, '*', '', $where);
        $rs = db_query($sql);

        return \db_utils::fieldsMemory($rs, 0);
    }
}
