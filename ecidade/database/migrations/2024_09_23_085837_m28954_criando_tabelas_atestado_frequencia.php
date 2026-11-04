<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M28954CriandoTabelasAtestadoFrequencia extends Migration
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
        $this->downDicionario();
        $this->downEstrutura();        
    }

    public function upEstrutura()
    {
DB::unprepared(<<<SQL
--CRIACAO DA TABELA atestadofrequenciastatus
CREATE SEQUENCE IF NOT EXISTS escola.atestadofrequenciastatus_ed356_codigo_seq
                INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE escola.atestadofrequenciastatus (
    ed356_codigo bigint primary key default nextval('atestadofrequenciastatus_ed356_codigo_seq'),
    ed356_descricao varchar(255) not null,
    ed356_tipo integer not null 
);

select configuracoes.fc_auditoria_cria_funcao('escola.atestadofrequenciastatus');

--CRIACAO DA TABELA atestadofrequencia

CREATE SEQUENCE IF NOT EXISTS escola.atestadofrequencia_ed357_codigo_seq
                INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE escola.atestadofrequencia (
    ed357_codigo bigint primary key default nextval('atestadofrequencia_ed357_codigo_seq'),
    ed357_escola bigint not null ,
    ed357_status bigint,
    ed357_mes integer not null,
    ed357_ano integer not null,
    ed357_created_at timestamp,
    ed357_updated_at timestamp,
    CONSTRAINT atestadofrequencia_ed357_escola_ed357_mes_ed357_ano_in UNIQUE(ed357_escola, ed357_mes, ed357_ano),
    CONSTRAINT atestadofrequencia_ed357_escola_fk FOREIGN KEY (ed357_escola) REFERENCES escola.escola(ed18_i_codigo),
    CONSTRAINT atestadofrequencia_ed357_status_fk FOREIGN KEY (ed357_status) REFERENCES escola.atestadofrequenciastatus(ed356_codigo)
);

select configuracoes.fc_auditoria_cria_funcao('escola.atestadofrequencia');


--CRIACAO DA TABELA atestadofrequenciaresumo

CREATE SEQUENCE IF NOT EXISTS escola.atestadofrequenciaresumo_ed364_codigo_seq
                INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE escola.atestadofrequenciaresumo (
     ed364_codigo bigint primary key default nextval('atestadofrequenciaresumo_ed364_codigo_seq'),
     ed364_rechumanoescola bigint not null,
     ed364_atestado bigint,
     ed364_observacao text,
     ed364_created_at timestamp,
     ed364_updated_at timestamp,
     ed364_dificilacesso boolean not null default false,
     ed364_regencia boolean not null default false,
     CONSTRAINT atestadofrequenciaresumo_ed364_atestado_ed364_rechumano_in UNIQUE (ed364_atestado, ed364_rechumanoescola),
     CONSTRAINT atestadofrequenciaresumo_ed364_atestado_fk FOREIGN KEY (ed364_atestado) REFERENCES atestadofrequencia (ed357_codigo),
     CONSTRAINT atestadofrequenciaresumo_ed364_rechumanoescola_fk FOREIGN KEY (ed364_rechumanoescola) REFERENCES  rechumanoescola(ed75_i_codigo)
);

select configuracoes.fc_auditoria_cria_funcao('escola.atestadofrequenciaresumo');
SQL
        );
    }

    public function downEstrutura()
    {
DB::unprepared(<<<SQL
DROP TABLE IF EXISTS atestadofrequenciaresumo;
DROP TABLE IF EXISTS atestadofrequencia;
DROP TABLE IF EXISTS atestadofrequenciastatus;
DROP SEQUENCE IF EXISTS escola.atestadofrequenciastatus_ed356_codigo_seq;
DROP SEQUENCE IF EXISTS escola.atestadofrequencia_ed357_codigo_seq;
DROP SEQUENCE IF EXISTS escola.atestadofrequenciaresumo_ed364_codigo_seq;
SQL
        );
    } 
    


    public function upDicionario()
    {
        $this->acionaTriggerDicionario();
        $this->upDicionarioAtestadoFrequenciaStatus();
        $this->upDicionarioAtestadoFrequencia();
        $this->upDicionarioAtestadoFrequenciaResumo();        
        $this->geraDicionario();
        $this->geraDicionario();
        $this->desabilitaTriggerDicionario();
    
    }

    public function downDicionario()
    {
DB::unprepared(<<<SQL
select fc_remove_dicionario_tabela('escola', 'atestadofrequenciastatus');
select fc_remove_dicionario_tabela('escola', 'atestadofrequencia');
select fc_remove_dicionario_tabela('escola', 'atestadofrequenciaresumo');
SQL
        );        
    }

    public function geraDicionario()
    {
DB::unprepared(<<<SQL
SELECT fc_gera_dicionario_apartir_tabela('escola', 'atestadofrequenciastatus');
SELECT fc_gera_dicionario_apartir_tabela('escola', 'atestadofrequencia');
SELECT fc_gera_dicionario_apartir_tabela('escola', 'atestadofrequenciaresumo');
SQL
        );
    }    

    public function desabilitaTriggerDicionario()
    {
DB::unprepared(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }

    public function acionaTriggerDicionario()
    {
DB::unprepared(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
SQL
        );
    }

    public function upDicionarioAtestadoFrequenciaStatus()
    {
DB::unprepared(<<<SQL

COMMENT ON TABLE escola.atestadofrequenciastatus IS '{
  "descricao": "Status do atestado de frequência",
  "sigla": "ed356",
  "dataincl": "2024-09-23",
  "rotulo": "Status Atestado Frequência",
  "tipotabela": 0,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';

COMMENT ON COLUMN escola.atestadofrequenciastatus.ed356_codigo IS '{
  "descricao": "Código do status do atestado de frequencia",
  "rotulo": "Código",
  "rotulorel": "Código status do atestado de frequencia",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequenciastatus.ed356_descricao IS '{
  "descricao": "Descrição do status do atestado de frequencia",
  "rotulo": "Descrição",
  "rotulorel": "Descrição Status Atestado Frequencia",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 0,
  "tamanho": 255,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequenciastatus.ed356_tipo IS '{
  "descricao": "Tipo do status do atestado de frequencia",
  "rotulo": "Tipo",
  "rotulorel": "Tipo Status Atestado Frequencia",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';
SQL
        );
    }

    public function upDicionarioAtestadoFrequencia()
    {
DB::unprepared(<<<SQL

COMMENT ON TABLE escola.atestadofrequencia IS '{
  "descricao": "Atestado de Frequência",
  "sigla": "ed357",
  "dataincl": "2024-09-23",
  "rotulo": "Atestado de Frequência",
  "tipotabela": 0,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';

COMMENT ON COLUMN escola.atestadofrequencia.ed357_codigo IS '{
  "descricao": "Código do atestado de frequência",
  "rotulo": "Código do atestado de frequência",
  "rotulorel": "Código do atestado de frequência ",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequencia.ed357_escola IS '{
  "descricao": "Escola do atestado de frequência",
  "rotulo": "Escola do atestado de frequência",
  "rotulorel": "Escola do atestado de frequência ",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequencia.ed357_status IS '{
  "descricao": "Status do atestado de frequência",
  "rotulo": "Status do atestado de frequência",
  "rotulorel": "Status do atestado de frequência ",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequencia.ed357_mes IS '{
  "descricao": "Mês do atestado de frequência",
  "rotulo": "Mês do atestado de frequência",
  "rotulorel": "Mês do atestado de frequência ",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequencia.ed357_ano IS '{
  "descricao": "Ano do atestado de frequência",
  "rotulo": "Ano do atestado de frequência",
  "rotulorel": "Ano do atestado de frequência ",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequencia.ed357_created_at IS '{
  "descricao": "Data de criação do atestado de frequência",
  "rotulo": "Data Criação Atestado Frequência",
  "rotulorel": "Data Criação Atestado Frequência",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequencia.ed357_updated_at IS '{
  "descricao": "Data de atualização do atestado de frequência",
  "rotulo": "Data Atualização Atestado Frequência",
  "rotulorel": "Data Atualização Atestado Frequência",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';
SQL
        );
    }
    
    public function upDicionarioAtestadoFrequenciaResumo()
    {
DB::unprepared(<<<SQL

COMMENT ON TABLE escola.atestadofrequenciaresumo IS '{
  "descricao": "Resumo Atestado de Frequência",
  "sigla": "ed364",
  "dataincl": "2024-09-23",
  "rotulo": "Resumo Atestado de Frequência",
  "tipotabela": 0,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';


COMMENT ON COLUMN escola.atestadofrequenciaresumo.ed364_codigo IS '{
  "descricao": "Código do resumo do atestado de frequência",
  "rotulo": "Código Resumo Atestado Frequência",
  "rotulorel": "Código Resumo Atestado Frequência",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequenciaresumo.ed364_rechumanoescola IS '{
  "descricao": "Recursos humanos vinculado ao resumo do atestado de frequência",
  "rotulo": "Rec. Humanos Resumo Atestado Frequência",
  "rotulorel": "Rec. Humanos Resumo Atestado Frequência",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequenciaresumo.ed364_atestado IS '{
  "descricao": "Atestado vinculado ao resumo do atestado de frequencia",
  "rotulo": "Atestado Resumo Atestado Frequencia",
  "rotulorel": "Atestado Resumo Atestado Frequencia",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequenciaresumo.ed364_observacao IS '{
  "descricao": "Observação do resumo do atestado de frequência",
  "rotulo": "Observação Resumo Atestado Frequencia",
  "rotulorel": "Observação Resumo Atestado Frequencia",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": null,
  "tipoobj": "textarea"
}';

COMMENT ON COLUMN escola.atestadofrequenciaresumo.ed364_created_at IS '{
  "descricao": "Data Criação Resumo Atestado Frequencia",
  "rotulo": "Data Criação Resumo Atestado Frequencia",
  "rotulorel": "Data Criação Resumo Atestado Frequencia",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequenciaresumo.ed364_updated_at IS '{
  "descricao": "Data Atualização Resumo Atestado Frequencia",
  "rotulo": "Data Atualização Resumo Atest.Frequencia",
  "rotulorel": "Data Atualização Resumo Atest.Frequencia",
  "maiusculo": false,
  "autocompl": false,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequenciaresumo.ed364_dificilacesso IS '{
  "descricao": "Flag Acesso Resumo Atestado Frequencia",
  "rotulo": "Flag Acesso Resumo Atestado Frequencia",
  "rotulorel": "Flag Acesso Resumo Atestado Frequencia",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 5,
  "tamanho": 1,
  "tipoobj": "text"
}';

COMMENT ON COLUMN escola.atestadofrequenciaresumo.ed364_regencia IS '{
  "descricao": "Flag Regência Resumo Atestado Frequencia",
  "rotulo": "Flag Regência Resumo Atest. Frequencia",
  "rotulorel": "Flag Regência Resumo Atest. Frequencia",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 5,
  "tamanho": 1,
  "tipoobj": "text"
}';
SQL
        );
    }    
}
