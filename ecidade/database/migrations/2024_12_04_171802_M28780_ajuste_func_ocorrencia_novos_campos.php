<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M28780AjusteFuncOcorrenciaNovosCampos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            <<<SQL
 CREATE OR REPLACE FUNCTION cadastro.fc_lote_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function$

      declare

         sLog            text default '';
         sLogAux         text default '';
         sAnos           varchar;
         ilote           integer;
         iMatric         integer;
         iIdbql          integer;
         desc_new_bairro varchar;
         desc_old_bairro varchar;
         rLote           record;
         iCodigoOcorrencia integer;
         bCampoProcesso boolean;

         sZonaNova text;
         sZonaAntiga text;

      begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

         if TG_OP = 'INSERT' then
            sLog     = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTE INCLUÍDO: ';
            ilote = new.j34_idbql;
         elseif TG_OP = 'UPDATE' then
            sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTE ALTERADO: ';
            ilote = new.j34_idbql;
         elsif TG_OP = 'DELETE' then
            sLog     = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTE EXCLUÍDO: ';
            ilote = old.j34_idbql;
         end if;

         sLogAux = sLog;

         if TG_OP = 'INSERT' then
            Select j13_descr into desc_new_bairro from bairro where j13_codi =  new.j34_bairro;
         elseif TG_OP = 'UPDATE' then
            Select j13_descr into desc_new_bairro from bairro where j13_codi =  new.j34_bairro;
            Select j13_descr into desc_old_bairro from bairro where j13_codi =  old.j34_bairro;
         elsif TG_OP = 'DELETE' then
            Select j13_descr into desc_old_bairro from bairro where j13_codi =  old.j34_bairro;
         end if;

         if TG_OP = 'INSERT' then
            sLog = sLog  || 'idbql: ' ||  new.j34_idbql ||                                   ' - ';

            if new.j34_setor is not null then
                sLog = coalesce(sLog, '') ||  'setor: '||  new.j34_setor ||  ( select j30_codi from setor where j30_codi = new.j34_setor ) || ' - ';
            end if;

            if new.j34_quadra  is not null then
                sLog = coalesce(sLog, '') || 'quadra: '||  new.j34_quadra   ||                                    ' - ';
            end if;

            if new.j34_lote is not null then
                sLog = coalesce(sLog, '') ||  'lote: ' || new.j34_lote    ||                       ' - ';
            end if;

            if new.j34_area is not null then
                sLog = coalesce(sLog, '') ||  'area: ' || new.j34_area ||                  ' - ';
            end if;

            if new.j34_bairro is not null then
                sLog = coalesce(sLog, '') ||  'bairro: '||  new.j34_bairro  || ' - ' || desc_new_bairro || ' - ';
            end if;

            if new.j34_areal is not null then
                sLog = coalesce(sLog, '') ||  'Area medida: ' || new.j34_areal  ||                       ' - ';
            end if;

            if new.j34_zona is not null then
                select j50_descr into sZonaNova from zonas where j50_zona = new.j34_zona;
                sLog = coalesce(sLog, '') || 'zona: ' || new.j34_zona || ' - ' || sZonaNova || ' - ';
            end if;

            if new.j34_quamat is not null then
                sLog = coalesce(sLog, '') || 'quamat: '||  new.j34_quamat ||                              ' - ';
            end if;

            if new.j34_areapreservada is not null then
                sLog = coalesce(sLog, '') ||'Area preservada: ' || new.j34_areapreservada ||                       ' - ';
            end if;

            if new.j34_areaprivativa is not null then
                sLog = coalesce(sLog, '') ||'Area privativa: ' || new.j34_areaprivativa ||                       ' - ';
            end if;
            if new.j34_areausocomum is not null then
                sLog = coalesce(sLog, '') ||'Area uso Comum: ' || new.j34_areausocomum ||                       ' - ';
            end if;
            if new.j34_zonadesurb is not null then
                sLog = coalesce(sLog, '') ||'Zona de Desenvolvimento Urbano: ' || coalesce(new.j34_zonadesurb::varchar, '') ||                       ' - ';
            end if;
         end if;

         if TG_OP = 'UPDATE' then
            if new.j34_idbql  <> old.j34_idbql  then
                sLog = coalesce(sLog, '') || 'idbql alterado de: ' || old.j34_idbql ||  ' para: ' || new.j34_idbql ||  ' - ';
            end if;

            if new.j34_setor <> old.j34_setor then
                sLog = coalesce(sLog, '') || 'setor alterado de: ' || old.j34_setor || ' para: ' || new.j34_setor || ' - ';
            end if;

            if new.j34_quadra  <> old.j34_quadra  then
                sLog = coalesce(sLog, '') || 'quadra alterada de: ' || old.j34_quadra ||  ' para: ' || new.j34_quadra ||  ' - ';
            end if;

            if new.j34_lote  <> old.j34_lote  then
                sLog = coalesce(sLog, '') || 'lote alterado de: ' || old.j34_lote ||  ' para: ' || new.j34_lote ||  ' - ';
            end if;

            if new.j34_area <> old.j34_area then
                sLog = coalesce(sLog, '') || 'area alterada de: ' || old.j34_area  || ' para: ' || new.j34_area || ' - ';
            end if;

            if new.j34_bairro <> old.j34_bairro then
                sLog = coalesce(sLog, '') || 'bairro: alterado de ' || old.j34_bairro  ||  ' ' || desc_old_bairro || ' para: ' || new.j34_bairro  || ' - ' ||  desc_new_bairro || ' - ';
            end if;

            if new.j34_areal <> old.j34_areal then
                sLog = coalesce(sLog, '') || 'Area Medida alterada de: '||  old.j34_areal || ' para: ' || new.j34_areal || ' - ';
            end if;

            if new.j34_zona <> old.j34_zona then
                select j50_descr into sZonaAntiga from zonas where j50_zona = old.j34_zona;
                select j50_descr into sZonaNova from zonas where j50_zona = new.j34_zona;
                sLog = coalesce(sLog, '') || 'zona alterada de: '||  old.j34_zona || ' - ' || sZonaAntiga ||' para: '||  new.j34_zona  || ' - ' || sZonaNova || ' - ';
            end if;

            if new.j34_quamat <> old.j34_quamat then
                sLog = coalesce(sLog, '') || 'quamat alterado de: ' || old.j34_quamat || ' para: ' || new.j34_quamat || ' - ';
            end if;

            if new.j34_areapreservada <> old.j34_areapreservada then
                sLog = coalesce(sLog, '') || 'Area preservada alterada de: ' || old.j34_areapreservada || ' para: '||  new.j34_areapreservada ||  ' - ';
            end if;

			if new.j34_areaprivativa <> old.j34_areaprivativa then
                sLog = coalesce(sLog, '') || 'Area privativa alterada de: ' || old.j34_areaprivativa || ' para: '||  new.j34_areaprivativa ||  ' - ';
            end if;

			if new.j34_areausocomum <> old.j34_areausocomum then
                sLog = coalesce(sLog, '') || 'Area de uso comum alterada de: ' || old.j34_areausocomum || ' para: '||  new.j34_areausocomum ||  ' - ';
            end if;

			if coalesce(new.j34_zonadesurb::varchar, '') <> coalesce(old.j34_zonadesurb::varchar, '') then
                sLog = coalesce(sLog, '') || 'Zona de Desenvolvimento Urbano alterada de: ' || coalesce(old.j34_zonadesurb::varchar,'') || ' para: '||  coalesce(new.j34_zonadesurb::varchar, '') ||  ' - ';
            end if;
         end if;

         if TG_OP = 'DELETE' then
            sLog = sLog || 'idbql: ' || old.j34_idbql ||                    ' - ';

            if old.j34_setor is not null then
                sLog = coalesce(sLog, '')  ||'setor: ' || old.j34_setor || ' - ';
            end if;

            if old.j34_quadra  is not null then
               sLog = coalesce(sLog, '') || 'quadra: ' || old.j34_quadra ||                  ' - ';
            end if;

            if old.j34_lote is not null then
               sLog = coalesce(sLog, '') || 'lote: ' || old.j34_lote ||                          ' - ';
            end if;

            if old.j34_area is not null then
                sLog = coalesce(sLog, '') || 'area: ' || old.j34_area ||                  ' - ';
            end if;

            if old.j34_bairro is not null then
                sLog = coalesce(sLog, '') || 'bairro: ' || old.j34_bairro  ||' - ' || desc_old_bairro || ' - ';
            end if;

            if old.j34_areal is not null then
                sLog = coalesce(sLog, '') || 'Area real medida: ' || old.j34_areal  ||                       ' - ';
            end if;

            if old.j34_zona is not null then
                select j50_descr into sZonaAntiga from zonas where j50_zona = old.j34_zona;
                sLog = coalesce(sLog, '') || 'zona: '||  old.j34_zona  || ' - ' || sZonaAntiga || ' - ';
            end if;

            if old.j34_quamat is not null then
                sLog = coalesce(sLog, '') || 'quamat: '||  old.j34_quamat||                               ' - ';
            end if;

            if old.j34_areapreservada is not null then
                sLog = coalesce(sLog, '') || 'area preservada: '||  old.j34_areapreservada  ||                      ' - ';
            end if;

            if old.j34_areaprivativa is not null then
                sLog = coalesce(sLog, '') || 'area privativa: '||  old.j34_areaprivativa  ||                      ' - ';
            end if;

            if old.j34_areausocomum is not null then
                sLog = coalesce(sLog, '') || 'area uso comum: '||  old.j34_areausocomum  ||                      ' - ';
            end if;

            if old.j34_zonadesurb is not null then
                sLog = coalesce(sLog, '') || 'zona de desenvolvimento urbano: '||  coalesce(old.j34_zonadesurb::varchar,'')  ||                      ' - ';
            end if;
         end if;

         if sLogAux <> sLog then
            SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

            insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                         ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
               select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                      578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5),
                      1, 'log de alteracoes', sLog;

            if TG_OP = 'INSERT' then
                iIdbql = new.j34_idbql;
            elseif TG_OP = 'UPDATE' then
                iIdbql = new.j34_idbql;
            elseif TG_OP = 'DELETE' then
                iIdbql = old.j34_idbql;
            end if;

            FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iIdbql
            LOOP
                insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                  select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, iCodigoOcorrencia;
            END LOOP;

            IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia )
                  select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
            END IF;

         end if;

         if TG_OP = 'INSERT' then
            return new;
         elseif TG_OP = 'UPDATE' then
            return new;
         elseif TG_OP = 'DELETE' then
            return old;
         end if;
      end;
\$function$;

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
        DB::unprepared(
            <<<SQL

CREATE OR REPLACE FUNCTION cadastro.fc_lote_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function$

      declare

         sLog            text default '';
         sLogAux         text default '';
         sAnos           varchar;
         ilote           integer;
         iMatric         integer;
         iIdbql          integer;
         desc_new_bairro varchar;
         desc_old_bairro varchar;
         rLote           record;
         iCodigoOcorrencia integer;
         bCampoProcesso boolean;

         sZonaNova text;
         sZonaAntiga text;

      begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

         if TG_OP = 'INSERT' then
            sLog     = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTE INCLUÍDO: ';
            ilote = new.j34_idbql;
         elseif TG_OP = 'UPDATE' then
            sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTE ALTERADO: ';
            ilote = new.j34_idbql;
         elsif TG_OP = 'DELETE' then
            sLog     = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTE EXCLUÍDO: ';
            ilote = old.j34_idbql;
         end if;

         sLogAux = sLog;

         if TG_OP = 'INSERT' then
            Select j13_descr into desc_new_bairro from bairro where j13_codi =  new.j34_bairro;
         elseif TG_OP = 'UPDATE' then
            Select j13_descr into desc_new_bairro from bairro where j13_codi =  new.j34_bairro;
            Select j13_descr into desc_old_bairro from bairro where j13_codi =  old.j34_bairro;
         elsif TG_OP = 'DELETE' then
            Select j13_descr into desc_old_bairro from bairro where j13_codi =  old.j34_bairro;
         end if;

         if TG_OP = 'INSERT' then
            sLog = sLog  || 'idbql: ' ||  new.j34_idbql ||                                   ' - ';

            if new.j34_setor is not null then
                sLog = sLog ||  'setor: '||  new.j34_setor ||  ( select j30_codi from setor where j30_codi = new.j34_setor ) || ' - ';
            end if;

            if new.j34_quadra  is not null then
                sLog = sLog || 'quadra: '||  new.j34_quadra   ||                                    ' - ';
            end if;

            if new.j34_lote is not null then
                sLog = sLog ||  'lote: ' || new.j34_lote    ||                       ' - ';
            end if;

            if new.j34_area is not null then
                sLog = sLog ||  'area: ' || new.j34_area ||                  ' - ';
            end if;

            if new.j34_bairro is not null then
                sLog = sLog ||  'bairro: '||  new.j34_bairro  || ' - ' || desc_new_bairro || ' - ';
            end if;

            if new.j34_areal is not null then
                sLog = sLog ||  'Area medida: ' || new.j34_areal  ||                       ' - ';
            end if;

            if new.j34_zona is not null then
                select j50_descr into sZonaNova from zonas where j50_zona = new.j34_zona;
                sLog = sLog || 'zona: ' || new.j34_zona || ' - ' || sZonaNova || ' - ';
            end if;

            if new.j34_quamat is not null then
                sLog = sLog || 'quamat: '||  new.j34_quamat ||                              ' - ';
            end if;

            if new.j34_areapreservada is not null then
                sLog = sLog ||'Area preservada: ' || new.j34_areapreservada ||                       ' - ';
            end if;
         end if;

         if TG_OP = 'UPDATE' then
            if new.j34_idbql  <> old.j34_idbql  then
                sLog = sLog || 'idbql alterado de: ' || old.j34_idbql ||  ' para: ' || new.j34_idbql ||  ' - ';
            end if;

            if new.j34_setor <> old.j34_setor then
                sLog = sLog || 'setor alterado de: ' || old.j34_setor || ' para: ' || new.j34_setor || ' - ';
            end if;

            if new.j34_quadra  <> old.j34_quadra  then
                sLog = sLog || 'quadra alterada de: ' || old.j34_quadra ||  ' para: ' || new.j34_quadra ||  ' - ';
            end if;

            if new.j34_lote  <> old.j34_lote  then
                sLog = sLog || 'lote alterado de: ' || old.j34_lote ||  ' para: ' || new.j34_lote ||  ' - ';
            end if;

            if new.j34_area <> old.j34_area then
                sLog = sLog || 'area alterada de: ' || old.j34_area  || ' para: ' || new.j34_area || ' - ';
            end if;

            if new.j34_bairro <> old.j34_bairro then
                sLog = sLog || 'bairro: alterado de ' || old.j34_bairro  ||  ' ' || desc_old_bairro || ' para: ' || new.j34_bairro  || ' - ' ||  desc_new_bairro || ' - ';
            end if;

            if new.j34_areal <> old.j34_areal then
                sLog = sLog || 'Area Medida alterada de: '||  old.j34_areal || ' para: ' || new.j34_areal || ' - ';
            end if;

            if new.j34_zona <> old.j34_zona then
                select j50_descr into sZonaAntiga from zonas where j50_zona = old.j34_zona;
                select j50_descr into sZonaNova from zonas where j50_zona = new.j34_zona;
                sLog = sLog || 'zona alterada de: '||  old.j34_zona || ' - ' || sZonaAntiga ||' para: '||  new.j34_zona  || ' - ' || sZonaNova || ' - ';
            end if;

            if new.j34_quamat <> old.j34_quamat then
                sLog = sLog || 'quamat alterado de: ' || old.j34_quamat || ' para: ' || new.j34_quamat || ' - ';
            end if;

            if new.j34_areapreservada <> old.j34_areapreservada then
                sLog = sLog || 'Area preservada alterada de: ' || old.j34_areapreservada || ' para: '||  new.j34_areapreservada ||  ' - ';
            end if;
         end if;

         if TG_OP = 'DELETE' then
            sLog = sLog || 'idbql: ' || old.j34_idbql ||                    ' - ';

            if old.j34_setor is not null then
                sLog = sLog  ||'setor: ' || old.j34_setor || ' - ';
            end if;

            if old.j34_quadra  is not null then
               sLog = sLog || 'quadra: ' || old.j34_quadra ||                  ' - ';
            end if;

            if old.j34_lote is not null then
               sLog = sLog || 'lote: ' || old.j34_lote ||                          ' - ';
            end if;

            if old.j34_area is not null then
                sLog = sLog || 'area: ' || old.j34_area ||                  ' - ';
            end if;

            if old.j34_bairro is not null then
                sLog = sLog || 'bairro: ' || old.j34_bairro  ||' - ' || desc_old_bairro || ' - ';
            end if;

            if old.j34_areal is not null then
                sLog = sLog || 'Area real medida: ' || old.j34_areal  ||                       ' - ';
            end if;

            if old.j34_zona is not null then
                select j50_descr into sZonaAntiga from zonas where j50_zona = old.j34_zona;
                sLog = sLog || 'zona: '||  old.j34_zona  || ' - ' || sZonaAntiga || ' - ';
            end if;

            if old.j34_quamat is not null then
                sLog = sLog || 'quamat: '||  old.j34_quamat||                               ' - ';
            end if;

            if old.j34_areapreservada is not null then
                sLog = sLog || 'area preservada: '||  old.j34_areapreservada  ||                      ' - ';
            end if;
         end if;

         if sLogAux <> sLog then
            SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

            insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                         ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
               select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                      578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5),
                      1, 'log de alteracoes', sLog;

            if TG_OP = 'INSERT' then
                iIdbql = new.j34_idbql;
            elseif TG_OP = 'UPDATE' then
                iIdbql = new.j34_idbql;
            elseif TG_OP = 'DELETE' then
                iIdbql = old.j34_idbql;
            end if;

            FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iIdbql
            LOOP
                insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                  select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, iCodigoOcorrencia;
            END LOOP;

            IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia )
                  select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
            END IF;

         end if;

         if TG_OP = 'INSERT' then
            return new;
         elseif TG_OP = 'UPDATE' then
            return new;
         elseif TG_OP = 'DELETE' then
            return old;
         end if;
      end;
\$function$;

SQL
        );
    }
}
