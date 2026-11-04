<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28902AdicionarCampoCgmalimentado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downEstrutura();
        $this->downDicionario();
    }

    public function upEstrutura() {
        $sql = <<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
            ---Adicionando campo novo na estrutura
            ALTER TABLE pessoal.pensao ADD COLUMN r52_cgmalimentado integer NOT NULL default 0;
            ALTER TABLE pessoal.pensaocontabancaria ADD COLUMN rh139_cgmalimentado integer NOT NULL default 0;
            ALTER TABLE pessoal.pensaoretencao  ADD COLUMN rh77_cgmalimentado integer NOT NULL default 0;

            --- Dropando FK
            ALTER TABLE pensaocontabancaria DROP CONSTRAINT pensaocontabancaria_ae_mesusu_regist_numcgm_fk;
            ALTER TABLE pensaoretencao DROP CONSTRAINT pensaoretencao_ae_mesusu_regist_numcgm_fk;

            ---Recriando index com o campo novo
            DROP INDEX IF EXISTS pensao_anousu_mesusu_regist_numcgm_in;

            CREATE UNIQUE INDEX pensao_anousu_mesusu_regist_numcgm_cgmalimentado_in
            ON pensao (r52_anousu, r52_mesusu, r52_regist, r52_numcgm, r52_cgmalimentado);

            -- Remover o índice existente
            DROP INDEX IF EXISTS pensaocontabancaria_regist_numcgm_anousu_mesusu_contabancaria_i;

            -- Criar o novo índice com o campo adicional
            CREATE UNIQUE INDEX pensaocontabancaria_regist_numcgm_anousu_mesusu_contabancaria_i
            ON pensaocontabancaria (rh139_regist, rh139_numcgm, rh139_anousu, rh139_mesusu, rh139_contabancaria, rh139_cgmalimentado);


            ALTER TABLE pessoal.pensao DISABLE TRIGGER tg_pensao;
            ALTER TABLE pessoal.pensaocontabancaria DISABLE TRIGGER  tg_pensaocontabancaria;
            UPDATE pessoal.pensao SET r52_cgmalimentado = r52_numcgm;
            UPDATE pessoal.pensaoretencao SET rh77_cgmalimentado = rh77_numcgm;
            UPDATE pessoal.pensaocontabancaria SET rh139_cgmalimentado = rh139_numcgm;
            ALTER TABLE pessoal.pensao ENABLE TRIGGER tg_pensao;
            ALTER TABLE pessoal.pensaocontabancaria ENABLe TRIGGER  tg_pensaocontabancaria;

               --- Adicionando FK com o campo novo
            ALTER TABLE pensaocontabancaria
            ADD CONSTRAINT pensaocontabancaria_ae_mesusu_regist_numcgm_cgmalimentado_fk
            FOREIGN KEY (rh139_anousu, rh139_mesusu, rh139_regist, rh139_numcgm, rh139_cgmalimentado)
            REFERENCES pensao(r52_anousu, r52_mesusu, r52_regist, r52_numcgm, r52_cgmalimentado) ON UPDATE CASCADE;

            ALTER TABLE pensaoretencao
            ADD CONSTRAINT pensaoretencao_ae_mesusu_regist_numcgm_cgmalimentado_fk
            FOREIGN KEY (rh77_anousu, rh77_mesusu, rh77_regist, rh77_numcgm, rh77_cgmalimentado)
            REFERENCES pensao(r52_anousu, r52_mesusu, r52_regist, r52_numcgm, r52_cgmalimentado) ON UPDATE CASCADE;

            ANALYZE pensao;
            ANALYZE pensaocontabancaria;
            ANALYZE pensaoretencao;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    public function upDicionario() {
        $sql = <<<SQL
                SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                        'pessoal.pensao.r52_cgmalimentado',
                        '{
                        "descricao": "CGM Alimentado",
                        "rotulo": "CGM Alimentado",
                        "rotulorel": "CGM Alimentado",
                        "maiusculo": false,
                        "autocompl": false,
                        "aceitatipo": 1,
                        "tamanho": 10,
                        "tipoobj": "text"
                        }'
                    );

                SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                        'pessoal.pensao.r52_numcgm',
                        '{
                        "descricao": "Número de CGM do representante legal.",
                        "rotulo": "CGM Representante Legal",
                        "rotulorel": "CGM Representante Legal",
                        "maiusculo": false,
                        "autocompl": false,
                        "aceitatipo": 1,
                        "tamanho": 10,
                        "tipoobj": "text"
                        }'
                    );

                SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                        'pessoal.pensaoretencao.rh77_cgmalimentado',
                        '{
                        "descricao": "Chave estrangeira da tabela pensao",
                        "rotulo": "CGM Alimentado",
                        "rotulorel": "CGM Alimentado",
                        "maiusculo": false,
                        "autocompl": false,
                        "aceitatipo": 1,
                        "tamanho": 10,
                        "tipoobj": "text"
                        }'
                    );

                SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                        'pessoal.pensaocontabancaria.rh139_cgmalimentado',
                        '{
                        "descricao": "Chave estrangeira da tabela pensao",
                        "rotulo": "CGM Alimentado",
                        "rotulorel": "CGM Alimentado",
                        "maiusculo": false,
                        "autocompl": false,
                        "aceitatipo": 1,
                        "tamanho": 10,
                        "tipoobj": "text"
                        }'
                    );


            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    public function downEstrutura()
    {
        $sql = <<<SQL
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

        ALTER TABLE pensaocontabancaria
        DROP CONSTRAINT pensaocontabancaria_ae_mesusu_regist_numcgm_cgmalimentado_fk;

        ALTER TABLE pensaoretencao
        DROP CONSTRAINT pensaoretencao_ae_mesusu_regist_numcgm_cgmalimentado_fk;


        -- Remover o novo índice
        DROP INDEX IF EXISTS pensaocontabancaria_regist_numcgm_anousu_mesusu_contabancaria_in;

        -- Recriar o índice original
        CREATE UNIQUE INDEX pensaocontabancaria_regist_numcgm_anousu_mesusu_contabancaria_i
        ON pensaocontabancaria (rh139_regist, rh139_numcgm, rh139_anousu, rh139_mesusu, rh139_contabancaria);

        DROP INDEX IF EXISTS pensao_anousu_mesusu_regist_numcgm_cgmalimentado_in;

        CREATE UNIQUE INDEX pensao_anousu_mesusu_regist_numcgm_in
        ON pensao (r52_anousu, r52_mesusu, r52_regist, r52_numcgm);


        ALTER TABLE pensaocontabancaria
        ADD CONSTRAINT pensaocontabancaria_ae_mesusu_regist_numcgm_fk
        FOREIGN KEY (rh139_anousu, rh139_mesusu, rh139_regist, rh139_numcgm)
        REFERENCES pensao(r52_anousu, r52_mesusu, r52_regist, r52_numcgm);

        ALTER TABLE pensaoretencao
        ADD CONSTRAINT pensaoretencao_ae_mesusu_regist_numcgm_fk
        FOREIGN KEY (rh77_anousu, rh77_mesusu, rh77_regist, rh77_numcgm)
        REFERENCES pensao(r52_anousu, r52_mesusu, r52_regist, r52_numcgm);


        ALTER TABLE pessoal.pensaoretencao DROP COLUMN rh77_cgmalimentado;
        ALTER TABLE pessoal.pensaocontabancaria DROP COLUMN rh139_cgmalimentado;

        ALTER TABLE pessoal.pensao DROP COLUMN r52_cgmalimentado;
SQL;

        DB::connection()->getPdo()->exec($sql);
    }

    public function downDicionario()
    {

        $sql = <<<SQL


            SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                        'pessoal.pensao.r52_numcgm',
                        '{
                        "descricao": "Número CGM da pensionista.",
                        "rotulo": "CGM",
                        "rotulorel": "CGM",
                        "maiusculo": false,
                        "autocompl": false,
                        "aceitatipo": 1,
                        "tamanho": 10,
                        "tipoobj": "text"
                        }'
                    );


        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

SQL;
        DB::connection()->getPdo()->exec($sql);

    }


}
