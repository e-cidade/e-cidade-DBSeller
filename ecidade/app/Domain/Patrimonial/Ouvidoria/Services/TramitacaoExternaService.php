<?php

namespace App\Domain\Patrimonial\Ouvidoria\Services;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Support\Facades\DB;

class TramitacaoExternaService
{

    /**
     * @param $codigoProcesso
     * @param $cpf_cnpj
     *
     * @return array
     * @throws \BusinessException
     */
    public static function getProcessoTramitacao($codigoProcesso, $cpf_cnpj)
    {
        $processo = self::getProcesso($codigoProcesso);

        if (! $processo) {
            throw new \BusinessException("Processo não encontrado!");
        }

        $cgms = Cgm::cpfCnpj($cpf_cnpj)->get();

        if (! $cgms) {
            throw new \BusinessException("Não possuí cgm", 404);
        }

        $cgms          = $cgms->pluck("z01_numcgm");
        $envolvimentos = self::envolvimento($codigoProcesso, $cgms->toArray());


        if (empty($envolvimentos)) {
            throw new \BusinessException("Não possuí permissão", 404);
        }

        $envolvimentosCidadao = [];
        foreach ($envolvimentos as $envolvimento) {
            $envolvimentosCidadao[] = $envolvimento->envolvimento;
        }
        $processo                  = (array)$processo;
        $processo["envolvimentos"] = $envolvimentosCidadao;
        $processo["anexos"]        = self::getProcessoAnexos($codigoProcesso);
        if (! empty($processo["anexos"])) {
            foreach ($processo["anexos"] as $key => $anexo) {
                $processo["anexos"][$key]->assinantes
                    = self::solicitacaoAssinatura($anexo->documento_id);
            }
        }
        $processo["tramitacoes"] = self::getTramitacao($codigoProcesso);
        foreach ($processo["tramitacoes"] as $keyTramitacao => $tramitacao) {
            $tramitacao                = (array)$tramitacao;
            $tramitacao["recebimento"] = self::recebimeto(
                $tramitacao["transferencia_codigo"]
            );
            if (! empty($tramitacao["recebimento"])) {
                $tramitacao["despachos"] = self::despachos(
                    ! empty($tramitacao["recebimento"]->recebimento_codigo)
                        ? $tramitacao["recebimento"]->recebimento_codigo : null
                );
                if (! empty($tramitacao["despachos"])) {
                    foreach ($tramitacao["despachos"] as $keyDespacho => $despacho
                    ) {
                        $despacho = (array)$despacho;
                        $tramitacao["despachos"][$keyDespacho]->anexos
                                  = self::despachoAnexos(
                                      $despacho["despacho_codigo"]
                                  );

                        if (! empty($tramitacao["despachos"][$keyDespacho]->anexos)) {
                            foreach ($tramitacao["despachos"][$keyDespacho]->anexos as $key => $anexo) {
                                $tramitacao["despachos"][$keyDespacho]
                                    ->anexos[$key]
                                    ->assinantes = self::solicitacaoAssinatura(
                                        $anexo->documento_id
                                    );
                            }
                        }
                    }
                }
            }
            $processo["tramitacoes"][$keyTramitacao] = $tramitacao;
        }

        return $processo;
    }

    /**
     * @param $codigoProcesso
     *
     * @return mixed
     */
    public static function getProcesso($codigoProcesso)
    {
        return DB::selectOne(
            "
            SELECT
                    protocolo.protprocesso.p58_codproc AS processo_codigo,
                    protocolo.protprocesso.p58_numero as processo_numero,
                    protocolo.protprocesso.p58_ano as processo_ano,
                    protocolo.protprocesso.p58_dtproc AS processo_data,
                    protocolo.protprocesso.p58_hora AS processo_hora,
                    protocolo.cgm.z01_numcgm AS titular_cgm,
                    protocolo.cgm.z01_nome AS titular_nome,
                    protocolo.cgm.z01_cgccpf AS titular_cpfcnpj,
                    protocolo.protprocesso.p58_requer AS processo_requerente,
                    configuracoes.db_config.codigo AS instituicao_codigo,
                    configuracoes.db_config.nomeinst AS instituicao_nome,
                    configuracoes.db_depart.coddepto AS departamento_codigo,
                    configuracoes.db_depart.descrdepto AS departamento_descricao,
                    configuracoes.db_usuarios.id_usuario AS usuario_codigo,
                    configuracoes.db_usuarios.nome AS usuario_nome,
                    protocolo.prottipodocumentoprocesso.p91_descricao AS processo_documento
            FROM protocolo.protprocesso
            INNER JOIN configuracoes.db_depart ON configuracoes.db_depart.coddepto = protocolo.protprocesso.p58_coddepto
            INNER JOIN configuracoes.db_usuarios
                ON configuracoes.db_usuarios.id_usuario = protocolo.protprocesso.p58_id_usuario
            INNER JOIN configuracoes.db_config ON  configuracoes.db_config.codigo  = protocolo.protprocesso.p58_instit
            INNER JOIN protocolo.cgm ON protocolo.cgm.z01_numcgm  =  protocolo.protprocesso.p58_numcgm
            INNER JOIN protocolo.tipoproc  ON protocolo.tipoproc.p51_codigo  = protocolo.protprocesso.p58_codigo
            INNER JOIN protocolo.prottipodocumentoprocesso
                ON protocolo.prottipodocumentoprocesso.p91_sequencial = protocolo.tipoproc.p51_prottipodocumentoprocesso
            WHERE
                p58_codproc = {$codigoProcesso}

        "
        );
    }

    /**
     * @param $codigoProcesso
     *
     * @return array
     */
    public static function getProcessoAnexos($codigoProcesso)
    {
        return DB::select(
            "
           select
                p01_sequencial as documento_id,
                p01_documento  AS documento_codigo,
                p01_nomedocumento  AS documento_nome,
                p01_documento_hash as documento_hash
           from
            protocolo.protprocessodocumento where p01_protprocesso = {$codigoProcesso} AND p01_procandamint = 0
        "
        );
    }

    /**
     * @param $codigoProcesso
     *
     * @return array
     */
    public static function getTramitacao($codigoProcesso)
    {
        return DB::select(
            "
        SELECT
            protocolo.proctransferproc.p63_codproc AS processo_codigo,
            protocolo.proctransfer.p62_codtran AS transferencia_codigo,
            protocolo.proctransfer.p62_dttran AS transferencia_data,
            protocolo.proctransfer.p62_hora AS transferencia_hora,
            instituicao_origem.codigo AS instituicao_origem_codigo,
            instituicao_origem.nomeinst AS instituicao_origem_nome,
            departamento_origem.coddepto AS departamento_origem_codigo,
            departamento_origem.descrdepto AS departamento_origem_descricao,
            usuario_origem.id_usuario AS usuario_origem_codigo,
            usuario_origem.nome AS usuario_origem_nome,
            instituicao_destino.codigo AS instituicao_destino_codigo,
            instituicao_destino.nomeinst AS instituicao_destino_nome,
            departamento_destino.coddepto AS departamento_destino_codigo,
            departamento_destino.descrdepto AS departamento_destino_descricao,
            usuario_destino.id_usuario AS usuario_destino_codigo,
            usuario_destino.nome AS usuario_destino_nome
        FROM
           protocolo.proctransferproc
        INNER JOIN protocolo.proctransfer ON
            protocolo.proctransfer.p62_codtran = protocolo.proctransferproc.p63_codtran
        INNER JOIN configuracoes.db_depart AS departamento_origem ON
            departamento_origem.coddepto = protocolo.proctransfer.p62_coddepto
        INNER JOIN configuracoes.db_config AS instituicao_origem ON
            instituicao_origem.codigo = departamento_origem.instit
        INNER JOIN configuracoes.db_usuarios AS usuario_origem ON
            usuario_origem.id_usuario = protocolo.proctransfer.p62_id_usuario
        INNER JOIN configuracoes.db_depart AS departamento_destino ON
            departamento_destino.coddepto = protocolo.proctransfer.p62_coddeptorec
        INNER JOIN configuracoes.db_config AS instituicao_destino ON
            instituicao_destino.codigo = departamento_destino.instit
        LEFT JOIN configuracoes.db_usuarios AS usuario_destino ON
            usuario_destino.id_usuario = protocolo.proctransfer.p62_id_usorec
        WHERE
            protocolo.proctransferproc.p63_codproc = {$codigoProcesso}
        ORDER BY
            transferencia_data,
            transferencia_hora,
            protocolo.proctransferproc.p63_codtran;
        "
        );
    }

    /**
     * @param $tramitacao
     *
     * @return mixed
     */
    public static function recebimeto($tramitacao)
    {
        return DB::selectOne(
            "
        SELECT
            protocolo.proctransand.p64_codtran AS transferencia_codigo,
            protocolo.procandam.p61_codandam AS recebimento_codigo,
            configuracoes.db_usuarios.id_usuario AS recebimento_usuario_codigo,
            configuracoes.db_usuarios.nome AS recebimento_usuario_nome,
            protocolo.procandam.p61_despacho AS recebimento_observacao,
            protocolo.procandam.p61_dtandam AS recebimento_data,
            protocolo.procandam.p61_hora AS recebimento_hora,
            Protocolo.arqandam.p69_codarquiv AS arquivamento_codigo,
            protocolo.arqandam.p69_arquivado  AS arquivamento_status
        FROM
        protocolo.proctransand
        INNER JOIN protocolo.procandam ON
        protocolo.procandam.p61_codandam = protocolo.proctransand.p64_codandam
        INNER JOIN configuracoes.db_usuarios ON
        configuracoes.db_usuarios.id_usuario = protocolo.procandam.p61_id_usuario

        LEFT JOIN 	protocolo.arqandam  ON 	protocolo.arqandam.p69_codandam  =  protocolo.procandam.p61_codandam
        WHERE
        protocolo.proctransand.p64_codtran = {$tramitacao};

        "
        );
    }

    /**
     * @param $codigoRecebimento
     *
     * @return array
     */
    public static function despachos($codigoRecebimento)
    {
        return DB::select(
            "
            SELECT

                configuracoes.db_usuarios.id_usuario  AS usuario_codigo,
                configuracoes.db_usuarios.nome  AS usuario_nome,
                protocolo.procandamint.p78_sequencial  AS despacho_codigo,
                protocolo.procandamint.p78_despacho  AS despacho_mensagem,
                protocolo.procandamint.p78_data  AS despacho_data,
                protocolo.procandamint.p78_hora  AS despacho_hora
            FROM
            protocolo.procandamint
            INNER JOIN configuracoes.db_usuarios
                ON configuracoes.db_usuarios.id_usuario = protocolo.procandamint.p78_usuario
            WHERE
            protocolo.procandamint.p78_codandam  = {$codigoRecebimento}
            ORDER BY protocolo.procandamint.p78_data,
                     protocolo.procandamint.p78_hora,
                     protocolo.procandamint.p78_sequencial ;
        "
        );
    }

    /**
     * @param $despachoCodigo
     *
     * @return array
     */
    public static function despachoAnexos($despachoCodigo)
    {
        return DB::select(
            "
            SELECT
                p01_sequencial as documento_id,
                p01_documento  AS documento_codigo,
                p01_nomedocumento  AS documento_nome,
                p01_documento_hash  AS documento_hash,
                p01_procandamint AS despacho_codigo
            FROM
                protocolo.protprocessodocumento
            WHERE
                p01_procandamint = {$despachoCodigo}
        "
        );
    }

    /**
     * @param $codigoDocumento
     *
     * @return array
     */
    public static function solicitacaoAssinatura($codigoDocumento)
    {
        return DB::select(
            "select
                distinct
                protocolo.documento_solicitacao_assinaturas.id as solicitacao_id,
                protocolo.cgm.z01_cgccpf as cpf_cnpj,
                protocolo.cgm.z01_nome as nome,
                CASE WHEN data_assinatura IS NULL THEN FALSE ELSE TRUE END as assinado
                from protocolo.documento_solicitacao_assinaturas
                inner join protocolo.cgm
                    on protocolo.cgm.z01_numcgm = protocolo.documento_solicitacao_assinaturas.cgm_assinante
                where
                    documento_id = {$codigoDocumento};
       "
        );
    }

    /**
     * @param $codigoProcesso
     * @param $cgm
     *
     * @return array
     */
    public static function envolvimento($codigoProcesso, array $cgms)
    {
        $cgms = join(",", $cgms);

        return DB::select(
            "
                select distinct  * from protocolo.fc_envolvidos(array[{$cgms}]) where  codigo_processo =  ?
                ",
            [$codigoProcesso]
        );
    }
}
