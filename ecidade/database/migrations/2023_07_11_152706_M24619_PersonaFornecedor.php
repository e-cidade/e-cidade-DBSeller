<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24619PersonaFornecedor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upCreatePersona();
        $this->upAtualizarPersonaCgm();
        $this->upTriggerFornecedor();
    }

    public function upCreatePersona()
    {
        DB::connection()->getPdo()->exec(<<<sql
INSERT INTO  persona (p120_sequencial,p120_descricao,p120_objetivo)
VALUES (8,'FORNECEDOR','Fornecedor de bens e serviços');
sql
        );
    }

    public function upAtualizarPersonaCgm()
    {
        DB::connection()->getPdo()->exec(<<<sql
INSERT INTO personacgm (p121_cgm,p121_persona)
SELECT pc60_numcgm,8 FROM pcforne;
sql
        );
    }

    public function upTriggerFornecedor()
    {
        DB::connection()->getPdo()->exec(<<<sql
 CREATE OR REPLACE FUNCTION fc_atualiza_personacgm_fornecedor()
            RETURNS trigger
            LANGUAGE plpgsql
            AS $$
            declare

            sOperacao varchar default lower(TG_OP);
            iCodigoInscricao integer;

            begin

            if sOperacao in ('insert') then

              select p121_cgm
              into iCodigoInscricao
              from personacgm
              where p121_cgm = NEW.pc60_numcgm
                and p121_persona = 8;

              if iCodigoInscricao is null then

                 insert into personacgm (p121_cgm, p121_persona) values ( NEW.pc60_numcgm, 8 );

              end if;
              return new;
            elseif sOperacao in ('update') then

              select count(*)
              into iCodigoInscricao
              from pcforne
              where pc60_numcgm = old.pc60_numcgm;

              if iCodigoInscricao = 0 then
                 delete from personacgm where p121_cgm = OLD.pc60_numcgm and p121_persona  = 8;
              end if;

              select p121_cgm
              into iCodigoInscricao
              from personacgm
              where p121_cgm = NEW.pc60_numcgm
                and p121_persona = 8;

              if iCodigoInscricao is null then

                 insert into personacgm (p121_cgm, p121_persona) values( NEW.pc60_numcgm,8 );

              end if;
              return new;

            elseif sOperacao in ('delete') then

              select count(*)
              into iCodigoInscricao
              from pcforne
              where pc60_numcgm = old.pc60_numcgm;

              if iCodigoInscricao = 0 then

                 delete from personacgm where p121_cgm = OLD.pc60_numcgm
                                             and p121_persona     = 8;
              end if;
              return old;
            else
              return new;
            end if;

            end;
            $$;

            create trigger  tg_atualiza_atualiza_personacgm_fornecedor AFTER INSERT OR UPDATE OR DELETE on pcforne FOR EACH ROW EXECUTE PROCEDURE fc_atualiza_personacgm_fornecedor();

sql
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDropTrigger();
        $this->downDropPersonaCGM();
        $this->downDropPersona();
    }

    public function downDropTrigger()
    {
        DB::connection()->getPdo()->exec(<<<sql
        DROP TRIGGER IF EXISTS tg_atualiza_atualiza_personacgm_fornecedor  ON pcforne;
        DROP FUNCTION IF EXISTS fc_atualiza_personacgm_fornecedor;
sql
        );
    }

    public function downDropPersonaCGM()
    {
        DB::connection()->getPdo()->exec(<<<sql

DELETE FROM personacgm where  p121_persona = 8;

sql
        );
    }

    public function downDropPersona()
    {
        DB::connection()->getPdo()->exec(<<<sql

DELETE FROM persona where  p120_sequencial = 8;

sql
        );
    }
}
