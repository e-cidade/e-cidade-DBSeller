<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27663ViewEmpresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          DB::connection()->getPdo()->exec(<<<SQL
CREATE OR REPLACE VIEW public.empresa AS
 SELECT issbase.q02_inscr,
    issbase.q02_dtcada,
    issbase.q02_dtinic,
    issbase.q02_dtbaix,
    tabativ.q07_ativ,
    tabativ.q07_perman,
    ativid.q03_descr,
    tabativ.q07_datain,
    tabativ.q07_datafi,
    tabativ.q07_databx,
    tabativ.q07_quant,
    tabativ.q07_tipbx,
    to_ascii(cgm.z01_nome::text, 'LATIN2'::name) AS z01_nome,
    cgm.z01_nomecomple,
    to_ascii(cgm2.z01_nome::text, 'LATIN2'::name) AS q02_escrit,
    cgm.z01_nomefanta,
    cgm.z01_cgccpf,
    cgm.z01_incest,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_ender::bpchar
            ELSE ruas.j14_nome
        END AS z01_ender,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN ''::character varying
            ELSE ruastipo.j88_sigla
        END AS j14_tipo,
        CASE
            WHEN cgm.z01_nomecomple IS NULL OR btrim(cgm.z01_nomecomple::text) = ''::text THEN cgm.z01_nome
            ELSE cgm.z01_nomecomple
        END AS razao,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_numero
            ELSE issruas.q02_numero
        END AS z01_numero,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_compl
            ELSE issruas.q02_compl
        END AS z01_compl,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_cxpostal::bpchar
            ELSE issruas.q02_cxpost
        END AS z01_cxpostal,
        CASE
            WHEN issruas.z01_cep IS NULL THEN cgm.z01_cep
            ELSE issruas.z01_cep
        END AS z01_cep,
        CASE
            WHEN issbairro.q13_inscr IS NULL THEN cgm.z01_bairro
            ELSE bairro.j13_descr
        END AS z01_bairro,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_munic
            ELSE ( SELECT db_config.munic
               FROM db_config
              WHERE db_config.prefeitura = true
             LIMIT 1)
        END AS z01_munic,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_uf::bpchar
            ELSE ( SELECT db_config.uf
               FROM db_config
              WHERE db_config.prefeitura = true
             LIMIT 1)
        END AS z01_uf,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN 0
            ELSE issruas.j14_codigo
        END AS q02_lograd,
        CASE
            WHEN issbairro.q13_inscr IS NULL THEN 0
            ELSE bairro.j13_codi
        END AS q02_bairro,
    cgm.z01_telef,
    issbase.q02_numcgm,
    ativid.q03_atmemo,
    issbase.q02_memo,
        CASE
            WHEN ativprinc.q88_inscr IS NOT NULL THEN 'P'::text
            ELSE 'S'::text
        END AS q88_tipo,
    issbase.q02_inscmu,
    cgm.z01_ident
   FROM issbase
     JOIN cgm ON cgm.z01_numcgm = issbase.q02_numcgm
     LEFT JOIN issruas ON issruas.q02_inscr = issbase.q02_inscr
     LEFT JOIN ruas ON issruas.j14_codigo = ruas.j14_codigo
     LEFT JOIN ruastipo ON ruas.j14_tipo = ruastipo.j88_codigo
     LEFT JOIN issbairro ON issbairro.q13_inscr = issbase.q02_inscr
     LEFT JOIN bairro ON issbairro.q13_bairro = bairro.j13_codi
     LEFT JOIN issmatric ON issmatric.q05_inscr = issbase.q02_inscr
     LEFT JOIN iptubase ON iptubase.j01_matric = issmatric.q05_matric
     LEFT JOIN issprocesso ON issprocesso.q14_inscr = issbase.q02_inscr
     LEFT JOIN tabativ ON tabativ.q07_inscr = issbase.q02_inscr
     LEFT JOIN ativprinc ON ativprinc.q88_inscr = tabativ.q07_inscr AND ativprinc.q88_seq = tabativ.q07_seq
     LEFT JOIN ativid ON tabativ.q07_ativ = ativid.q03_ativ
     LEFT JOIN escrito ON escrito.q10_inscr = issbase.q02_inscr
     LEFT JOIN cgm cgm2 ON escrito.q10_numcgm = cgm2.z01_numcgm
  ORDER BY (
        CASE
            WHEN ativprinc.q88_inscr IS NOT NULL THEN 'P'::text
            ELSE 'S'::text
        END)
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
           DB::connection()->getPdo()->exec(<<<SQLD
CREATE OR REPLACE VIEW public.empresa AS
 SELECT issbase.q02_inscr,
    issbase.q02_dtcada,
    issbase.q02_dtinic,
    issbase.q02_dtbaix,
    tabativ.q07_ativ,
    tabativ.q07_perman,
    ativid.q03_descr,
    tabativ.q07_datain,
    tabativ.q07_datafi,
    tabativ.q07_databx,
    tabativ.q07_quant,
    tabativ.q07_tipbx,
    to_ascii(cgm.z01_nome::text, 'LATIN2'::name) AS z01_nome,
    cgm.z01_nomecomple,
    to_ascii(cgm2.z01_nome::text, 'LATIN2'::name) AS q02_escrit,
    cgm.z01_nomefanta,
    cgm.z01_cgccpf,
    cgm.z01_incest,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_ender::bpchar
            ELSE ruas.j14_nome
        END AS z01_ender,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN ''::character varying
            ELSE ruastipo.j88_sigla
        END AS j14_tipo,
        CASE
            WHEN cgm.z01_nomecomple IS NULL OR btrim(cgm.z01_nomecomple::text) = ''::text THEN cgm.z01_nome
            ELSE cgm.z01_nomecomple
        END AS razao,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_numero
            ELSE issruas.q02_numero
        END AS z01_numero,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_compl
            ELSE issruas.q02_compl
        END AS z01_compl,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_cxpostal::bpchar
            ELSE issruas.q02_cxpost
        END AS z01_cxpostal,
        CASE
            WHEN issruas.z01_cep IS NULL THEN cgm.z01_cep
            ELSE issruas.z01_cep
        END AS z01_cep,
        CASE
            WHEN issbairro.q13_inscr IS NULL THEN cgm.z01_bairro
            ELSE bairro.j13_descr
        END AS z01_bairro,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_munic
            ELSE ( SELECT db_config.munic
               FROM db_config
              WHERE db_config.prefeitura = true
             LIMIT 1)
        END AS z01_munic,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN cgm.z01_uf::bpchar
            ELSE ( SELECT db_config.uf
               FROM db_config
              WHERE db_config.prefeitura = true
             LIMIT 1)
        END AS z01_uf,
        CASE
            WHEN issruas.q02_inscr IS NULL THEN 0
            ELSE issruas.j14_codigo
        END AS q02_lograd,
        CASE
            WHEN issbairro.q13_inscr IS NULL THEN 0
            ELSE bairro.j13_codi
        END AS q02_bairro,
    cgm.z01_telef,
    issbase.q02_numcgm,
    ativid.q03_atmemo,
    issbase.q02_memo,
        CASE
            WHEN ativprinc.q88_inscr IS NOT NULL THEN 'P'::text
            ELSE 'S'::text
        END AS q88_tipo,
    issbase.q02_inscmu,
    cgm.z01_ident
   FROM issbase
     JOIN cgm ON cgm.z01_numcgm = issbase.q02_numcgm
     LEFT JOIN issruas ON issruas.q02_inscr = issbase.q02_inscr
     LEFT JOIN ruas ON issruas.j14_codigo = ruas.j14_codigo
     LEFT JOIN ruastipo ON ruas.j14_tipo = ruastipo.j88_codigo
     LEFT JOIN issbairro ON issbairro.q13_inscr = issbase.q02_inscr
     LEFT JOIN bairro ON issbairro.q13_bairro = bairro.j13_codi
     LEFT JOIN issmatric ON issmatric.q05_inscr = issbase.q02_inscr
     LEFT JOIN iptubase ON iptubase.j01_matric = issmatric.q05_matric
     LEFT JOIN issprocesso ON issprocesso.q14_inscr = issbase.q02_inscr
     JOIN tabativ ON tabativ.q07_inscr = issbase.q02_inscr
     LEFT JOIN ativprinc ON ativprinc.q88_inscr = tabativ.q07_inscr AND ativprinc.q88_seq = tabativ.q07_seq
     JOIN ativid ON tabativ.q07_ativ = ativid.q03_ativ
     LEFT JOIN escrito ON escrito.q10_inscr = issbase.q02_inscr
     LEFT JOIN cgm cgm2 ON escrito.q10_numcgm = cgm2.z01_numcgm
  ORDER BY (
        CASE
            WHEN ativprinc.q88_inscr IS NOT NULL THEN 'P'::text
            ELSE 'S'::text
        END)
SQLD
        );
    }
}
