<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M23271MelhoriaCriacaoColunas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

        ALTER TABLE escola.calendario ADD COLUMN ed52_t_obs TEXT;

        ALTER TABLE escola.calendarioescola ADD COLUMN ed38_calendariobase BIGINT NULL;

        ALTER TABLE escola.calendarioescola
            ADD CONSTRAINT calendarioescola_calendariobase_fk
            FOREIGN KEY (ed38_calendariobase)
            REFERENCES escola.calendario (ed52_i_codigo);

        COMMENT ON COLUMN escola.calendario.ed52_t_obs IS '{ "descricao": "Observação",
                                                            "rotulo": "Observação",
                                                            "rotulorel": "Observação",
                                                            "maiusculo": true,
                                                            "autocompl": false,
                                                            "aceitatipo": 0,
                                                            "tipoobj": "text"
                                                        }' ;

        insert into db_syscampo values( 1014770,'ed52_t_obs','text','Observação','',
                                        'Observação',1,'f','t','f',0,'text','Observação');

        insert into db_sysarqcamp values(1010057,1014770,14,0);

        INSERT INTO db_syscampo VALUES(
                                        1014790,'ed38_calendariobase','int8','referencia calendário originário','0',
                                        'Calendário base',20,'t','f','f',1,'text','Calendário base'
                                    );

        INSERT INTO db_sysarqcamp VALUES(1010104,1014790,4,0);

        INSERT INTO db_sysforkey VALUES(1010104,1014790,1,1010057,0);

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
        DB::connection()->getPdo()->exec(<<<SQL

        ALTER TABLE escola.calendario DROP column ed52_t_obs;

        ALTER TABLE escola.calendarioescola DROP column ed38_calendariobase;

        DELETE FROM db_sysarqcamp WHERE codcam = 1014770;

        DELETE FROM db_syscampo WHERE codcam = 1014770;

        DELETE FROM db_sysforkey WHERE codcam = 1014790;

        DELETE FROM db_sysarqcamp WHERE codcam = 1014790;

        DELETE FROM db_syscampo WHERE codcam = 1014790;


SQL
        );
    }
}
