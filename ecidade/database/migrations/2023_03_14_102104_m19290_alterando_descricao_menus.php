<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M19290AlterandoDescricaoMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql  = <<<SQL
        update configuracoes.db_itensmenu set descricao = 'S-2399-Desligamento Servidor Sem Vínculo', help = 'S2399-Desligamento Servidor Sem Vínculo', libcliente = true where id_item = 10580;
        update configuracoes.db_itensmenu set descricao = 'S-2205-Alteração Cadastral do Servidor Com Vínculo', help = 'S2205-Alteração Cadastral do Servidor Com Vínculo' , libcliente = true where id_item = 10572;
        update configuracoes.db_itensmenu set descricao = 'S-1299-Fechamento dos Eventos Periódicos', help = 'S1299-Fechamento dos Eventos Periódicos', libcliente = true where id_item = 228093;
        update configuracoes.db_itensmenu set descricao = 'S-1005-Tabela de Estabelecimentos,Obras ou Unidades de Órgãos Públicos', help = 'S1005-Tabela de Estabelecimentos,Obras ou Unidades de Órgãos Públicos', libcliente = true where id_item = 228575;
        update configuracoes.db_itensmenu set descricao = 'S-1000-Informações do Empregador', help = 'S1000-Informações do Empregador', libcliente = true where id_item = 10244;
        update configuracoes.db_itensmenu set descricao = 'S-1010-Tabela de Rubricas', help = 'S1010-Tabela de Rubricas', libcliente = true where id_item = 10426;
        update configuracoes.db_itensmenu set descricao = 'S-1020-Tabela Lotação Tributária', help = 'S1020-Tabela Lotação Tributária' where id_item = 10479;
        update configuracoes.db_itensmenu set descricao = 'S-1070-Tabela de Processos Administrativos/Judiciais', help = 'S-1070-Tabela de Processos Administrativos/Judiciai', libcliente = true where id_item = 10486;
        update configuracoes.db_itensmenu set descricao = 'S-2190-Registro Preliminar de Trabalhador', help = 'S2190-Registro Preliminar de Trabalhador', libcliente = true where id_item = 10493;
        update configuracoes.db_itensmenu set descricao = 'S-2200 Conferência Cadastro Servidores com Vínculo', help = 'S2200 Conferência Cadastro Servidores com Vínculo', libcliente = true where id_item = 10427;
        update configuracoes.db_itensmenu set descricao = 'S-2206-Alteração de Contrato de Trabalho/Relação Estatutária', help = 'S2206-Alteração de Contrato de Trabalho/Relação Estatutária', libcliente = true where id_item = 10576;
        update configuracoes.db_itensmenu set descricao = 'S-2298-Reintegração/Outros Provimentos', help = 'S2298-Reintegração/Outros Provimentos', libcliente = true where id_item = 10577;
        update configuracoes.db_itensmenu set descricao = 'S-2299 - Desligamento Servidor com Vínculo', help = 'S-2299 - Desligamento Servidor com Vínculo', libcliente = true where id_item = 10566;
        update configuracoes.db_itensmenu set descricao = 'S-3000-Exclusão de Eventos', help = 'S3000-Exclusão de Eventos', libcliente = true where id_item = 10567;
        update configuracoes.db_itensmenu set descricao = 'S-2300-Cadastro Trabalhador Sem Vínculo', help = ' S-2300-Cadastro Trabalhador Sem Vínculo', libcliente = true where id_item = 10575;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql  = <<<SQL
        update configuracoes.db_itensmenu set descricao = 'Término / Rescisão', help = 'Término / Rescisão, libcliente = true' where id_item = 10580;
        update configuracoes.db_itensmenu set descricao = 'Alteração Cadastral do Trabalhador', help = 'Alteração Cadastral do Trabalhador', libcliente = true where id_item = 10572;
        update configuracoes.db_itensmenu set descricao = 'Fechamento dos Eventos Periódicos', help = 'Fechamento dos Eventos Periódicos', libcliente = true where id_item = 228093;
        update configuracoes.db_itensmenu set descricao = 'Tabela de Estabelecimentos,Obras ou Unidades de Órgãos Públicos', help = 'Tabela de Estabelecimentos,Obras ou Unidades de Órgãos Públicos', libcliente = true where id_item = 228575;
        update configuracoes.db_itensmenu set descricao = 'Informações do Empregador', help = 'Informações do Empregador para o eSocial', libcliente = true where id_item = 10244;
        update configuracoes.db_itensmenu set descricao = 'Tabela de Rubricas', help = 'Tabela de Rubricas', libcliente = true where id_item = 10426;
        update configuracoes.db_itensmenu set descricao = 'Lotação Tributária', help = 'Lotações Tributárias', libcliente = true where id_item = 10479;
        update configuracoes.db_itensmenu set descricao = 'Processos Administrativos/Judiciais', help = 'Processos Administrativos/Judiciais', libcliente = true where id_item = 10486;
        update configuracoes.db_itensmenu set descricao = 'Admissão Preliminar', help = 'Admissão Preliminar', libcliente = true where id_item = 10493;
        update configuracoes.db_itensmenu set descricao = 'Conferência', help = 'Conferência', libcliente = true where id_item = 10427;
        update configuracoes.db_itensmenu set descricao = 'Alteração de Contrato de Trabalho', help = 'S-2206 Alteração de Contrato de Trabalho', libcliente = true where id_item = 10576;
        update configuracoes.db_itensmenu set descricao = 'Reintegração', help = 'Reintegração', libcliente = true where id_item = 10577;
        update configuracoes.db_itensmenu set descricao = 'Desligamento / Rescisão', help = 'Desligamento / Rescisão', libcliente = true where id_item = 10566;
        update configuracoes.db_itensmenu set descricao = 'Exclusão de Eventos', help = 'Exclusão de Eventos', libcliente = true where id_item = 10567;
        update configuracoes.db_itensmenu set descricao = 'Inicial', help = 'Inicial', libcliente = true where id_item = 10575;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
