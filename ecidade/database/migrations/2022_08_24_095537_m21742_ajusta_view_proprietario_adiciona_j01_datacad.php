<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M21742AjustaViewProprietarioAdicionaJ01Datacad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

--
-- Segundo o Parametro ImpPossPro da tabela CFIPTU, entao imprimier a palavra POSSUIDOR
--
DROP VIEW if exists venal;
DROP VIEW if exists averbacoes;
DROP VIEW if exists proprietario;

--
--
-- VISAO: PROPRIETARIO
--
--
CREATE OR REPLACE VIEW public.proprietario AS
SELECT x.z01_numcgm,
   x.j01_matric,
   x.z01_cgccpf,
        CASE
            WHEN x.totpropri > 0 THEN
            CASE
                WHEN x.totpropri = 1 THEN rtrim(x.proprietario::text)
                WHEN x.totpropri = 2 THEN rtrim(x.proprietario::text) || ' E OUTRO'::text
                ELSE rtrim(x.proprietario::text) || ' E OUTROS'::text
            END::character varying
            ELSE x.proprietario
        END AS proprietario,
    btrim(substr(
        CASE
            WHEN x.totpromi > 0 THEN
            CASE
                WHEN x.totpromi = 1 THEN rtrim(x.z01_nome)
                WHEN x.totpromi = 2 THEN rtrim(x.z01_nome) || ' E OUTRO'::text
                ELSE rtrim(x.z01_nome) || ' E OUTROS'::text
            END
            ELSE x.z01_nome
        END, 1, 40))::character varying AS z01_nome,
    btrim(
        CASE
            WHEN x.totpromi > 0 THEN
            CASE
                WHEN x.totpromi = 1 THEN rtrim(x.z01_nomecompleto)
                WHEN x.totpromi = 2 THEN rtrim(x.z01_nomecompleto) || ' E OUTRO'::text
                ELSE rtrim(x.z01_nomecompleto) || ' E OUTROS'::text
            END
            ELSE x.z01_nomecompleto
        END)::character varying AS z01_nomecompleto,
        CASE
            WHEN length(btrim(x.z01_ender::text)) = 0 AND length(btrim(x.z01_cxpostal::text)) > 0 AND to_number(x.z01_cxpostal::text, '999999'::text) > 0::numeric THEN ('CAIXA POSTAL: '::text || x.z01_cxpostal::text)::character varying
            ELSE x.z01_ender::character varying(80)
        END AS z01_ender,
   x.z01_munic,
   x.z01_bairro,
   x.z01_cep,
   x.z01_uf,
   x.z01_numero,
   x.z01_compl,
   x.codpri,
   x.nomepri::character varying(40) AS nomepri,
   x.tipopri::character varying(40) AS tipopri,
   x.j39_numero,
   x.j39_compl,
   x.j34_setor,
   x.j34_quadra,
   x.j34_lote,
   x.j34_zona,
   x.j34_bairro,
   x.j40_refant,
   x.j01_idbql,
   x.j14_codigo,
   x.j14_nome,
   x.j14_tipo,
   x.j13_codi,
   x.j13_descr,
   x.j01_baixa,
   x.j34_area,
   x.j34_areal,
   x.j44_numcgm,
   x.j41_numcgm,
   x.j43_matric,
   x.j01_datacad,
        CASE
            WHEN x.j43_munic IS NULL THEN x.z01_munic
            ELSE x.j43_munic
        END AS j43_munic,
        CASE
            WHEN x.j43_ender IS NULL THEN x.z01_ender
            ELSE x.j43_ender
        END AS j43_ender,
        CASE
            WHEN x.j43_cep IS NULL THEN x.z01_cep
            ELSE x.j43_cep
        END AS j43_cep,
        CASE
            WHEN x.j43_uf IS NULL THEN x.z01_uf
            ELSE x.j43_uf
        END AS j43_uf,
    x.j43_dest,
        CASE
            WHEN x.j43_numimo IS NULL THEN x.z01_numero
            ELSE x.j43_numimo
        END AS j43_numimo,
        CASE
            WHEN x.j43_cxpost IS NULL THEN x.z01_cxpostal::text
            ELSE to_char(x.j43_cxpost, '99999999999999999999'::text)
        END AS j43_cxpost,
        CASE
            WHEN x.j43_comple IS NULL THEN x.z01_compl
            ELSE x.j43_comple
        END AS j43_comple,
    x.j01_tipoimp::character varying(20) AS j01_tipoimp,
    x.j01_codave,
    x.j37_zona,
    x.z01_cgmpri,
    x.j39_pavim,
    x.j05_codigoproprio,
    x.j06_setorloc,
    x.j06_quadraloc,
    x.j06_lote,
    x.j05_descr,
    x.pql_localizacao,
    cgmpropri.z01_cgccpf AS z01_cgccpfpropri,
    cgmpropri.z01_nomecomple AS z01_nomecomplepri,
    cgmpropri.z01_ender AS z01_enderpri,
    cgmpropri.z01_munic AS z01_municpri,
    cgmpropri.z01_bairro AS z01_bairropri,
    cgmpropri.z01_cep AS z01_ceppri,
    cgmpropri.z01_uf AS z01_ufpri,
    cgmpropri.z01_numero AS z01_numeropri,
    cgmpropri.z01_compl AS z01_complpri,
    x.j40_registrocartografico
   FROM ( SELECT iptubase.j01_matric,
                 iptubase.j01_datacad,
            cgm.z01_numcgm,
          CASE
                    WHEN promite.z01_numcgm IS NULL THEN cgm.z01_cgccpf
                    ELSE cgmpromite.z01_cgccpf
                END AS z01_cgccpf,
          CASE
                    WHEN promite.z01_nome IS NULL THEN cgm.z01_nome::text
                    ELSE (( SELECT COALESCE(btrim(cfiptu.j18_textoprom::text) || ' '::text, ''::text) AS "coalesce"
                       FROM cfiptu
                      ORDER BY cfiptu.j18_anousu DESC
                     LIMIT 1)) || substr(promite.z01_nome, 1, 50)::text
                END AS z01_nome,
          CASE
                    WHEN promite.z01_nome IS NULL THEN cgm.z01_nome::text
                    ELSE (( SELECT COALESCE(btrim(cfiptu.j18_textoprom::text) || ' '::text, ''::text) AS "coalesce"
                       FROM cfiptu
                      ORDER BY cfiptu.j18_anousu DESC
                     LIMIT 1)) || promite.z01_nome::text
                END AS z01_nomecompleto,
                CASE
                    WHEN promite.z01_numcgm IS NULL THEN cgm.z01_numcgm
                    ELSE promite.z01_numcgm
                END AS z01_cgmpri,
            cgm.z01_nome AS proprietario,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_ender
                        ELSE promite.z01_ender
                    END
                    ELSE iptuender.j43_ender
                END AS z01_ender,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_munic
                        ELSE promite.z01_munic
                    END
                    ELSE iptuender.j43_munic
                END AS z01_munic,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_bairro
                        ELSE promite.z01_bairro
                    END
                    ELSE iptuender.j43_bairro
                END AS z01_bairro,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_cep
                        ELSE promite.z01_cep
                    END::bpchar
                    ELSE iptuender.j43_cep
                END AS z01_cep,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_uf
                        ELSE promite.z01_uf
                    END::bpchar
                    ELSE iptuender.j43_uf
                END AS z01_uf,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_numero
                        ELSE promite.z01_numero
                    END
                    ELSE iptuender.j43_numimo
                END AS z01_numero,
                CASE
                    WHEN iptuender.j43_ender IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_compl
                        ELSE promite.z01_compl
                    END::bpchar
                    ELSE iptuender.j43_comple
                END AS z01_compl,
                CASE
                    WHEN iptuender.j43_cxpost IS NULL THEN
                    CASE
                        WHEN promitente.j41_numcgm IS NULL THEN cgm.z01_cxpostal
                        ELSE promite.z01_cxpostal
                    END
                    ELSE iptuender.j43_cxpost::character varying(20)
                END AS z01_cxpostal,
                CASE
                    WHEN rr.j14_codigo IS NULL THEN r.j14_codigo
                    ELSE rr.j14_codigo
                END AS codpri,
                CASE
                    WHEN rr.j14_nome IS NULL THEN r.j14_nome
                    ELSE rr.j14_nome
                END AS nomepri,
                CASE
                    WHEN rr.j14_tipo IS NULL THEN rt.j88_sigla
                    ELSE rrt.j88_sigla
                END AS tipopri,
                CASE
                    WHEN length(btrim(testadanumero.j15_numero::character varying::text)) > 0 AND iptuconstr.j39_matric IS NULL THEN testadanumero.j15_numero
                    ELSE iptuconstr.j39_numero
                END AS j39_numero,
                CASE
                    WHEN length(btrim(testadanumero.j15_compl::text)) > 0 AND iptuconstr.j39_matric IS NULL THEN testadanumero.j15_compl
                    ELSE iptuconstr.j39_compl
                END AS j39_compl,
            lote.j34_setor,
            lote.j34_quadra,
            lote.j34_lote,
            lote.j34_zona,
            lote.j34_bairro,
            iptuant.j40_refant,
            iptubase.j01_idbql,
       r.j14_codigo,
       r.j14_nome,
            rt.j88_sigla::text AS j14_tipo,
            bairro.j13_codi,
            bairro.j13_descr,
            iptubase.j01_baixa,
            lote.j34_area,
            lote.j34_areal,
            imobil.j44_numcgm,
            promitente.j41_numcgm,
            iptuender.j43_matric,
            iptuender.j43_dest,
            iptuender.j43_ender,
            iptuender.j43_numimo,
            iptuender.j43_comple,
            iptuender.j43_bairro,
            iptuender.j43_munic,
            iptuender.j43_uf,
            iptuender.j43_cep,
            iptuender.j43_cxpost,
            iptuant.j40_registrocartografico,
       CASE
                    WHEN iptuconstr.j39_idcons IS NULL THEN 'Territorial'::text
                    ELSE 'Predial'::text
                END AS j01_tipoimp,
            iptubase.j01_codave,
            face.j37_zona,
            iptuconstr.j39_pavim,
            ( SELECT count(propri.j42_matric) AS count
                   FROM propri
                  WHERE propri.j42_matric = iptubase.j01_matric) AS totpropri,
            ( SELECT count(promitente_1.j41_matric) AS count
                   FROM promitente promitente_1
                  WHERE promitente_1.j41_matric = iptubase.j01_matric) AS totpromi,
            setorloc.j05_codigoproprio,
            loteloc.j06_setorloc,
            loteloc.j06_quadraloc,
            loteloc.j06_lote,
            setorloc.j05_descr,
            (((((setorloc.j05_codigoproprio::text || '-'::text) || setorloc.j05_descr::text) || '/'::text) || loteloc.j06_quadraloc::text) || '/'::text) || loteloc.j06_lote::text AS pql_localizacao
           FROM iptubase
             JOIN cgm ON cgm.z01_numcgm = iptubase.j01_numcgm
             LEFT JOIN iptuconstr ON iptuconstr.j39_matric = iptubase.j01_matric AND iptuconstr.j39_idprinc IS TRUE AND iptuconstr.j39_dtdemo IS NULL
             LEFT JOIN ruas rr ON rr.j14_codigo = iptuconstr.j39_codigo
             LEFT JOIN ruastipo rrt ON rrt.j88_codigo = rr.j14_tipo
             LEFT JOIN iptuant ON iptuant.j40_matric = iptubase.j01_matric
             JOIN lote ON iptubase.j01_idbql = lote.j34_idbql
             LEFT JOIN testpri ON testpri.j49_idbql = lote.j34_idbql
             LEFT JOIN testadanumero ON testadanumero.j15_idbql = testpri.j49_idbql AND testadanumero.j15_face = testpri.j49_face
             LEFT JOIN face ON face.j37_face = testpri.j49_face
             LEFT JOIN ruas r ON r.j14_codigo = testpri.j49_codigo
             LEFT JOIN ruastipo rt ON rt.j88_codigo = r.j14_tipo
             LEFT JOIN imobil ON iptubase.j01_matric = imobil.j44_matric
             LEFT JOIN promitente ON iptubase.j01_matric = promitente.j41_matric AND promitente.j41_tipopro IS TRUE
             LEFT JOIN cgm cgmpromite ON promitente.j41_numcgm = cgmpromite.z01_numcgm
             LEFT JOIN cgm promite ON promite.z01_numcgm = promitente.j41_numcgm
             LEFT JOIN bairro ON bairro.j13_codi = lote.j34_bairro
             LEFT JOIN iptuender ON iptubase.j01_matric = iptuender.j43_matric
             LEFT JOIN loteloc ON loteloc.j06_idbql = iptubase.j01_idbql
             LEFT JOIN setorloc ON setorloc.j05_codigo = loteloc.j06_setorloc
             LEFT JOIN setor ON setor.j30_codi = lote.j34_setor) x
     LEFT JOIN cgm cgmpropri ON x.z01_numcgm = cgmpropri.z01_numcgm;

create or replace view averbacoes as
SELECT protprocesso.p58_codproc,
       protprocesso.p58_dtproc,
       averbacao.j75_codigo,
       averbacao.j75_matric,
       averbacao.j75_data,
       averbacao.j75_obs,
       averbacao.j75_tipo,
       averbacao.j75_dttipo,
       averbacao.j75_situacao,
       averbacao.j75_regra,
       proprietario.j40_refant,
       setorloc.j05_codigoproprio,
       loteloc.j06_setorloc,
       loteloc.j06_quadraloc,
       loteloc.j06_lote,
       loteam.j34_descr,
       proprietario.codpri,
       proprietario.nomepri,
       proprietario.z01_cgccpf,
       proprietario.tipopri,
       proprietario.j39_numero,
       proprietario.j39_compl,
       proprietario.j34_area,
       proprietario.j34_setor,
       proprietario.j34_quadra,
       proprietario.j34_lote,
       proprietario.j01_baixa,
       iptuconstr.j39_area,
--       averbacgm.j76_numcgm,

       (select array_to_string(array_accum(z01_numcgm||' - '||z01_nome),',')
          from cgm
               inner join averbacgm on cgm.z01_numcgm = averbacgm.j76_numcgm
         where averbacao.j75_codigo = averbacgm.j76_averbacao ) as z01_nomeadq,

       (select array_to_string(array_accum(z01_numcgm||' - '||z01_nome),',')
          from cgm
               inner join averbacgmold on cgm.z01_numcgm = averbacgmold.j79_numcgm
         where averbacao.j75_codigo = averbacgmold.j79_averbacao ) as z01_nometrans,

--       cgmadq.z01_nome AS z01_nomeadq,
--       averbacgmold.j79_numcgm,
--       cgmtrans.z01_nome AS z01_nometrans,

       db_usuarios."login",
       db_usuarios.nome,
       rhpessoal.rh01_regist,
       rhfuncao.rh37_descr
   FROM averbacao

--   JOIN averbacgm           ON averbacao.j75_codigo = averbacgm.j76_averbacao
--   JOIN cgm cgmadq          ON cgmadq.z01_numcgm = averbacgm.j76_numcgm

   LEFT JOIN averbaprocesso ON averbacao.j75_codigo = averbaprocesso.j77_averbacao
   LEFT JOIN protprocesso   ON protprocesso.p58_codproc = averbaprocesso.j77_codproc

--   LEFT JOIN averbacgmold   ON averbacao.j75_codigo = averbacgmold.j79_averbacao
--   LEFT JOIN cgm cgmtrans   ON cgmtrans.z01_numcgm = averbacgmold.j79_numcgm

   JOIN iptubase            ON averbacao.j75_matric = iptubase.j01_matric
   LEFT JOIN loteloc        ON iptubase.j01_idbql = loteloc.j06_idbql
   LEFT JOIN setorloc       ON loteloc.j06_setorloc = setorloc.j05_codigo
   JOIN proprietario        ON iptubase.j01_matric = proprietario.j01_matric
   LEFT JOIN iptuconstr     ON iptubase.j01_matric = iptuconstr.j39_matric
   LEFT JOIN loteloteam     ON proprietario.j01_idbql = loteloteam.j34_idbql
   LEFT JOIN loteam         ON loteloteam.j34_loteam = loteam.j34_loteam
   LEFT JOIN db_usuarios    ON db_usuarios.id_usuario = fc_getsession('db_id_usuario'::text)::integer
   LEFT JOIN db_usuacgm     ON db_usuacgm.id_usuario = db_usuarios.id_usuario
   LEFT JOIN rhpessoal      ON rhpessoal.rh01_numcgm = db_usuacgm.cgmlogin
   LEFT JOIN rhpessoalmov   ON rhpessoalmov.rh02_regist = rhpessoal.rh01_regist
                           AND rhpessoalmov.rh02_anousu = fc_anofolha(fc_getsession('db_instit'::text)::integer)
                           AND rhpessoalmov.rh02_mesusu = fc_mesfolha(fc_getsession('db_instit'::text)::integer)
   LEFT JOIN rhpesrescisao  ON rhpessoalmov.rh02_seqpes = rhpesrescisao.rh05_seqpes
   LEFT JOIN rhfuncao       ON rhpessoalmov.rh02_funcao = rhfuncao.rh37_funcao
                           AND rhpessoalmov.rh02_instit = rhfuncao.rh37_instit
  WHERE     db_usuarios.usuarioativo::text = 1::text
        AND db_usuarios.usuext = 0
        AND rhpessoalmov.rh02_instit::text = fc_getsession('db_instit'::text)
        AND rhpesrescisao.rh05_recis is null;

create or replace view venal as
SELECT COALESCE((SELECT sum(iptucale.j22_valor) AS sum FROM cadastro.iptucale WHERE ((iptucale.j22_anousu = iptucalc.j23_anousu) AND (iptucale.j22_matric = iptucalc.j23_matric))), (0)::double precision) AS j22_valor, ('R$ '::text || translate(to_char((iptucalc.j23_vlrter + COALESCE((SELECT sum(iptucale.j22_valor) AS sum FROM cadastro.iptucale WHERE ((iptucale.j22_anousu = iptucalc.j23_anousu) AND (iptucale.j22_matric = iptucalc.j23_matric))), (0)::double precision)), '999,999,999,990.99'::text), ',.'::text, '.,'::text)) AS j23_venaltotal,
  proprietario.j40_refant,
  loteloc.j06_setorloc,
  loteloc.j06_quadraloc,
  loteloc.j06_lote,
  loteam.j34_descr,
  proprietario.j01_matric,
  proprietario.codpri,
  proprietario.nomepri,
  proprietario.z01_cgccpf,
  proprietario.tipopri,
  proprietario.j39_numero,
  proprietario.j39_compl,
  proprietario.j34_area,
  iptucalc.j23_anousu,
  iptucalc.j23_matric,
  iptucalc.j23_testad,
  iptucalc.j23_arealo,
  iptucalc.j23_areafr,
  iptucalc.j23_areaed,
  iptucalc.j23_m2terr,
  iptucalc.j23_vlrter,
  iptucalc.j23_aliq,
  iptucalc.j23_vlrisen,
  iptucalc.j23_tipoim,
  iptucalc.j23_manual,
  iptucalc.j23_tipocalculo,
  to_date(fc_getsession('db_datausu'::text), 'YYYY-MM-DD'::text) AS data_sessao,
  fc_dataextenso(to_date(fc_getsession('db_datausu'::text), 'YYYY-MM-DD'::text)) AS data_sessao_extenso,
  db_usuarios."login", db_usuarios.nome,
    rh01_regist,
    rh37_descr
FROM ((((cadastro.iptucalc JOIN proprietario ON ((iptucalc.j23_matric = proprietario.j01_matric)))
LEFT JOIN cadastro.loteloc ON ((proprietario.j01_idbql = loteloc.j06_idbql)))
LEFT JOIN cadastro.loteloteam ON ((proprietario.j01_idbql = loteloteam.j34_idbql)))
LEFT JOIN cadastro.loteam ON ((loteloteam.j34_loteam = loteam.j34_loteam)))

LEFT JOIN configuracoes.db_usuarios ON ((db_usuarios.id_usuario = (fc_getsession('db_id_usuario'::text))::integer))
left join configuracoes.db_usuacgm on db_usuacgm.id_usuario = db_usuarios.id_usuario
left join pessoal.rhpessoal     on rh01_numcgm = cgmlogin
left join pessoal.rhpessoalmov  on rh02_regist = rh01_regist
                                   and rh02_anousu = fc_anofolha(fc_getsession('db_instit')::integer)
                                   and rh02_mesusu = fc_mesfolha(fc_getsession('db_instit')::integer)
left join pessoal.rhpesrescisao on rh02_seqpes = rh05_seqpes
left join pessoal.rhfuncao      on rh02_funcao = rh37_funcao
                                   and rh02_instit = rh37_instit

WHERE rh05_seqpes is null
  and rh02_instit = cast(fc_getsession('db_instit') as integer)
  and db_usuarios.usuarioativo = 1
  and db_usuarios.usuext = 0
  and (iptucalc.j23_anousu = (fc_getsession('db_anousu'::text))::integer);



SQL
        );
}

    public function down()
    {
        return true;
    }
}
