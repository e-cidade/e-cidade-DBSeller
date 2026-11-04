<?php

namespace App\Domain\Patrimonial\Ouvidoria\Services;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DocumentoTramitacaoExternaService
{

    public static function tiposDocumentos($cpfCnpj)
    {
        $cgms = Cgm::cpfCnpj($cpfCnpj)->get();

        if (! $cgms) {
            throw new \BusinessException("Não possuí cgm", 404);
        }
        $cgms = $cgms->pluck("z01_numcgm");
        $cgms = join(",", $cgms->toArray());

        return DB::select(
            "
            select
            distinct
            protocolo.prottipodocumentoprocesso.p91_sequencial as codigo,
            protocolo.prottipodocumentoprocesso.p91_descricao as descricao,
            count(*) as total
            from
            protocolo.protprocesso
            inner join protocolo.tipoproc
            on
            protocolo.tipoproc.p51_codigo = protocolo.protprocesso.p58_codigo
            inner join protocolo.prottipodocumentoprocesso
            on
            protocolo.prottipodocumentoprocesso.p91_sequencial = protocolo.tipoproc.p51_prottipodocumentoprocesso
            where
            protocolo.protprocesso.p58_codproc in (
                select distinct  codigo_processo from protocolo.fc_envolvidos(array[{$cgms}])
            )
            group by
            protocolo.prottipodocumentoprocesso.p91_sequencial,
            protocolo.prottipodocumentoprocesso.p91_descricao
        "
        );
    }

    /**
     * @throws \BusinessException
     */
    public static function listaDocumentosTipo(
        $cpfCnpj,
        $tipoDocumento,
        $perPage,
        $filtro
    ) {
        $cgms = Cgm::cpfCnpj($cpfCnpj)->get();

        if (! $cgms) {
            throw new \BusinessException("Não possuí cgm", 404);
        }
        $cgms = $cgms->pluck("z01_numcgm");
        $cgms = join(",", $cgms->toArray());

        if ($filtro === 'semfiltro') {
            $filtro = '';
        }

        $filtro = '%'.$filtro.'%';

        $results = DB::select(
            "
                    SELECT
                        protocolo.protprocesso.p58_codproc_crypt AS codigo_externo,
                        protocolo.protprocesso.p58_codproc AS codigo,
                        protocolo.protprocesso.p58_numero AS numero,
                        protocolo.protprocesso.p58_ano AS ano,
                        configuracoes.db_config.nomeinst AS instituicao,
                        p51_descr AS assunto,
                        p91_descricao AS documento,
                        (
                            SELECT
                                p69_arquivado
                            FROM
                                protocolo.arqproc
                            LEFT JOIN protocolo.procarquiv ON
                                protocolo.arqproc.p68_codarquiv = protocolo.procarquiv.p67_codarquiv
                            LEFT JOIN protocolo.arqandam ON
                                protocolo.arqandam.p69_codarquiv = protocolo.procarquiv.p67_codarquiv
                            WHERE
                                protocolo.procarquiv.p67_codproc = protocolo.protprocesso.p58_codproc
                            GROUP BY
                                protocolo.procarquiv.p67_codproc,
                                protocolo.arqandam.p69_arquivado
                            ORDER BY
                                MAX(protocolo.procarquiv.p67_codarquiv) DESC
                            LIMIT 1
                        ) AS arquivado,
                        p58_dtproc as data_processo
                    FROM
                        protocolo.protprocesso
                    INNER JOIN protocolo.tipoproc ON
                        protocolo.tipoproc.p51_codigo = protocolo.protprocesso.p58_codigo
                    INNER JOIN protocolo.prottipodocumentoprocesso ON
                        protocolo.prottipodocumentoprocesso.p91_sequencial
                        = protocolo.tipoproc.p51_prottipodocumentoprocesso
                    INNER JOIN configuracoes.db_config ON configuracoes.db_config.codigo
                        = protocolo.protprocesso.p58_instit
                    WHERE
                        protocolo.protprocesso.p58_codproc IN (
                        select distinct  codigo_processo from protocolo.fc_envolvidos(array[{$cgms}])
                        )
                    AND protocolo.prottipodocumentoprocesso.p91_sequencial = ?
                    AND (
                        protocolo.protprocesso.p58_numero ILIKE ? OR
                        configuracoes.db_config.nomeinst ILIKE ? OR
                        p51_descr ILIKE ? OR
                        CONCAT(protocolo.protprocesso.p58_numero, '/', protocolo.protprocesso.p58_ano) ILIKE ?
                    )
                    ORDER BY protocolo.protprocesso.p58_dtproc DESC
            ",
            [$tipoDocumento, $filtro, $filtro, $filtro, $filtro]
        );

        if (empty($perPage)) {
            $perPage = 5;
        }

        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            array_slice(
                $results,
                ($page - 1) * $perPage,
                $perPage
            ),
            count($results),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }
}
