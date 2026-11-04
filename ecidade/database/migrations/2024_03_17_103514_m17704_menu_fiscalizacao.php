<?php

use Illuminate\Database\Migrations\Migration;

class M17704MenuFiscalizacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
            insert into db_sysmodulo values (92,'fiscalizacao','Novo modulo fiscal - Controle das Notificações, Autos de Infração e Levantamento Fiscais e Outros. Produto DBTributos.','2024-03-16','t');
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229038 ,'Fiscalização' ,'Módulo Fiscalização' ,'' ,'2' ,'1' ,'Novo Módulo Fiscalização utilizado para substituir o modulo fiscal. com novas funcionalidades e comportamentos' ,'f');
            insert into db_modulos( id_item ,nome_modulo ,descr_modulo ,imagem ,temexerc ) values ( 229038 ,'Fiscalização' ,'Fiscalização' ,'' ,'true' );
            insert into atendcadareamod ( at26_sequencia ,at26_codarea ,at26_id_item ) values ( 89 ,1 ,229038 );

            -- 29
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229038, 29, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229039, 'Fiscais', 'Fiscais', '', '1', '1', 'Cadastros >> Fiscais', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229039, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229040, 'Inclusão', 'Inclusão de Cadfiscais', 'fis1_fis_cadfiscais001.php', '1', '1', 'Inclusão de Cadfiscais', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229039, 229040, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229041, 'Exclusão', 'Exclusão de Cadfiscais', 'fis1_fis_cadfiscais003.php', '1', '1', 'Exclusão de Cadfiscais', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229039, 229041, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229042, 'Alteração', 'Alteração de Fiscal', 'fis1_fis_cadfiscais002.php', '1', '1', 'Alteração de Fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229039, 229042, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229043, 'Tipo de Processo Fiscal', 'Tipo de Processo Fiscal', '', '1', '1', 'Cadastro Tipo de Processo Fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229043, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229044, 'Inclusão', 'Inclusão de Procfiscalcadtipo', 'fis1_fis_procfiscalcadtipo001.php', '1', '1', 'Inclusão de Procfiscalcadtipo', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229043, 229044, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229045, 'Alteração', 'Alteração de Procfiscalcadtipo', 'fis1_fis_procfiscalcadtipo002.php', '1', '1', 'Alteração de Procfiscalcadtipo', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229043, 229045, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229046, 'Exclusão', 'Exclusão de Procfiscalcadtipo', 'fis1_fis_procfiscalcadtipo003.php', '1', '1', 'Exclusão de Procfiscalcadtipo', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229043, 229046, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229047, 'Gráficas', 'Gráficas', '', '1', '1', 'Cadastros >> Gráficas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229047, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229048, 'Inclusão', 'Inclusão de Gráficas', 'fis1_fis_graficas001.php', '1', '1', 'Inclusão Gráficas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229047, 229048, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229049, 'Exclusão', 'Exclusão de Gráficas', 'fis1_fis_graficas003.php', '1', '1', 'Exclusão de Gráficas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229047, 229049, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229050, 'Alteração', 'Alteração gráfica', 'fis1_fis_graficas002.php', '1', '1', 'Alteração da Gráfica', 'f');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229047, 229050, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229051, 'Procedências', 'Procedências', '', '1', '1', 'Cadastros >> Procedências', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229051, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229052, 'Inclusão', 'Inclusão de Fiscalproc', 'fis1_fis_fiscalproc001.php', '1', '1', 'Inclusão de Fiscalproc', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229051, 229052, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229053, 'Alteração', 'Alteração de Fiscalproc', 'fis1_fis_fiscalproc002.php', '1', '1', 'Alteração de Fiscalproc', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229051, 229053, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229054, 'Exclusão', 'Exclusão de Fiscalproc', 'fis1_fis_fiscalproc003.php', '1', '1', 'Exclusão de Fiscalproc', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229051, 229054, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229055, 'Tipo de Andamento', 'Tipo de Andamento', '', '1', '1', 'Cadastros >> Tipo de Andamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229055, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229056, 'Inclusão', 'Inclusão de Tipoandam', 'fis1_fis_tipoandam001.php', '1', '1', 'Inclusão de Tipoandam', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229055, 229056, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229057, 'Alteração', 'Alteração de Tipoandam', 'fis1_fis_tipoandam002.php', '1', '1', 'Alteração de Tipoandam', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229055, 229057, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229058, 'Exclusão', 'Exclusão de Tipoandam', 'fis1_fis_tipoandam003.php', '1', '1', 'Exclusão de Tipoandam', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229055, 229058, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229059, 'Tipo de Fiscalização', 'Tipo de Fiscalização', '', '1', '1', 'Cadastros >> Tipo de Fiscalização', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229059, 6, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229060, 'Inclusão', 'Inclusão de Tipofiscaliza', 'fis1_fis_tipofiscaliza001.php', '1', '1', 'Inclusão de Tipofiscaliza', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229059, 229060, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229061, 'Alteração', 'Alteração de Tipofiscaliza', 'fis1_fis_tipofiscaliza002.php', '1', '1', 'Alteração de Tipofiscaliza', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229059, 229061, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229062, 'Exclusão', 'Exclusão de Tipofiscaliza', 'fis1_fis_tipofiscaliza003.php', '1', '1', 'Exclusão de Tipofiscaliza', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229059, 229062, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229063, 'Tipo de Vistoria', 'Tipo de Vistoria', '', '1', '1', 'Cadastros >> Tipo de Vistoria', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229063, 7, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229064, 'Inclusão', 'Inclusão de Tipovistorias', 'fis1_fis_tipovistorias001.php', '1', '1', 'Inclusão de Tipovistorias', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229063, 229064, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229065, 'Alteração', 'Alteração de Tipovistorias', 'fis1_fis_tipovistorias002.php', '1', '1', 'Alteração de Tipovistorias', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229063, 229065, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229066, 'Exclusão', 'Exclusão de Tipovistorias', 'fis1_fis_tipovistorias003.php', '1', '1', 'Exclusão de Tipovistorias', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229063, 229066, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229067, 'Cadastro de Parágrafos', 'Cadastro de Parágrafos', '', '1', '1', 'Cadastro de Parágrafos', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229067, 268, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229068, 'Inclusão', 'Inclusão', 'fis1_fis_paragrafo001.php', '1', '1', 'Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229067, 229068, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229069, 'Alteração', 'Alteração', 'fis1_fis_paragrafo002.php', '1', '1', 'Alteração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229067, 229069, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229070, 'Exclusão', 'Exclusão', 'fis1_fis_paragrafo003.php', '1', '1', 'Exclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229067, 229070, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229071, 'Gestor', 'Fiscal Gestor', '', '1', '1', 'Fiscal Gestor', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229071, 271, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229072, 'Inclusão', 'Inclusão de gestor', 'fis1_fis_cadgestorfiscal001.php', '1', '1', 'Inclusão de gestor', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229071, 229072, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229073, 'Exclusão', 'Exclusão de gestor', 'fis1_fis_cadgestorfiscal003.php', '1', '1', 'Exclusão de gestor', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229071, 229073, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229074, 'Taxas', 'Cadastro de Taxas diversas', '', '1', '1', 'Menu para cadastro de grupo de taxas e de taxas diversas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229074, 271, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229075, 'Grupos', 'Grupos de Taxas diversas', 'fis1_fis_grupotaxadiversos001.php', '1', '1', 'Menu para agrupamento de taxas diversas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229074, 229075, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229076, 'Natureza', 'Taxas diversas', 'fis1_fis_taxadiversos001.php', '1', '1', 'Menu para cadastro de taxas diversas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229074, 229076, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229077, 'Grupo de Andamento', 'Grupo de Andamento', '', '1', '1', 'Grupo de Andamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(29, 229077, 272, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229078, 'Inclusão', 'Inclusão', 'fis1_fis_grupotipoandamento001.php', '1', '1', 'Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229077, 229078, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229079, 'Alteração', 'Alteração', 'fis1_fis_grupotipoandamento002.php', '1', '1', 'Alteração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229077, 229079, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229080, 'Exclusão', 'Exclusão', 'fis1_fis_grupotipoandamento003.php', '1', '1', 'Exclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229077, 229080, 3, 229038);


            -- 1822
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229038, 1822, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229081, 'Alvará Sanitário', 'Alvará Sanitário', 'fis3_fis_consultasani001.php', '1', '1', 'consulta do Alvará Sanitário', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229081, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229082, 'Cadastro Municipal', 'Cadastro Municipal', 'iss3_consinscr001.php', '1', '1', 'Consulta >> Cadastro Municipal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229082, 2, 229038);

            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 56, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229083, 'CGM', 'Nova consulta de CGM', 'prot3_consultacgmnovo001.php', '1', '1', 'Nova consulta de CGM para o módulo Protocolo', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229083, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229084, 'Vistorias', 'Vistorias', 'fis3_fis_consultavist001.php', '1', '1', 'Vistorias', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229084, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229085, 'Auto de Infração', 'Auto de Infração', 'fis3_fis_consautoinf001.php', '1', '1', 'Consulta >> Auto de Infração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229085, 6, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229086, 'Notificação', 'Notificação', 'fis3_fis_consnotificinf001.php', '1', '1', 'Consulta Notificação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229086, 7, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229087, 'Consulta Processo Fiscal', 'Consulta Processo Fiscal', 'fis2_fis_conprocfiscal001.php', '1', '1', 'consulta prcosso fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229087, 22, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229088, 'Situação do Auto de Infração', 'Situação do Auto de Infração', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000117', '1', '1', 'Situação do Auto de Infração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229088, 28, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229089, 'Intimação', 'Intimação', 'fis3_fis_consnotificinf001.php?intimacao=1', '1', '1', 'Fiscal >> Consulta >> Intimação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229089, 31, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229090, 'CGM', 'CGM', 'prot3_consultacgm001.php', '1', '1', 'Consulta >> CGM', 'f');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229090, 32, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229091, 'Notificação de Lançamento', 'Notificação de Lançamento', 'fis3_fis_conslancamento001.php', '1', '1', 'Notificação de Lançamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1822, 229091, 34, 229038);


            -- 30
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229038, 30, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229092, 'Fiscais', '', 'fis2_fis_fiscais001.php', '1', '1', 'Relátorio de Fiscais', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229092, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229093, 'Gráficas', 'Gráficas', 'fis2_fis_graficas001.php', '1', '1', 'Relatório >> Gráficas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229093, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229094, 'Tipo de Andamento', 'Tipo de Andamento', 'fis2_fis_tipoandamento001.php', '1', '1', 'Relatórios >> Tipo de Andamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229094, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229095, 'Alvará Sanitário', 'Alvará Sanitário', 'fis2_fis_relatoriosani001.php', '1', '1', 'Relatório >> Alvará Sanitário', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229095, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229096, 'Vistorias', 'Vistorias', 'fis2_fis_relatoriovist001.php', '1', '1', 'Vistorias', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229096, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229097, 'Termo de Vistoria', 'Termo de Vistoria', 'fis2_fis_termofiscal001.php', '1', '1', 'Termo de Vistoria', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229097, 6, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229098, 'Auto de Infração', 'Auto de Infração', 'fis2_fis_autoinf001.php', '1', '1', 'Relatórios >> Auto de Infração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229098, 7, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229099, 'Auto de Infração (Prazo)', 'Auto de Infração (Prazo)', 'fis2_fis_relautosprazo001.php', '1', '1', 'Relatórios >> Auto de Infração (Prazo)', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229099, 8, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229100, 'Notificação', 'Notificação', 'fis2_fis_fiscalinf001.php', '1', '1', 'Relatórios >> Notificação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229100, 9, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229101, 'Notificação (Período)', 'Notificação (Período)', 'fis2_fis_relnotific001.php', '1', '1', 'Relatórios >> Notificação (Período)', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229101, 10, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229102, 'Atividades Baixadas', 'Atividades Baixadas', 'fis2_fis_relatoriobaixa001.php', '1', '1', 'relatório de atividades baixadas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229102, 11, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229103, 'Levantamento', 'Levantamento', 'fis2_fis_levantamento001.php', '1', '1', 'Relatórios >> Levantamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229103, 12, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229104, 'Geral de Levantamento', 'Geral de Levantamentos', 'fis2_fis_periodolevant001.php', '1', '1', 'Relatórios >> Geral de Levantamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229104, 13, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229105, 'AIDOF', 'AIDOF', 'fis2_fis_emiteaidof001.php', '1', '1', 'Relatórios >> AIDOF', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229105, 14, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229106, 'Inscrição por Logradouros', 'Inscrição por Logradouros', 'fis2_fis_lograd001.php', '1', '1', 'Relatórios >> Alvará', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229106, 15, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229107, 'Notas Fiscais', 'Notas Fiscais', 'fis2_fis_relatorionotasliberadas001.php', '1', '1', 'Relatórios >> Notas Fiscais ', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229107, 16, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229108, 'Taxas', 'Taxas', 'fis2_fis_taxadiversos001.php', '1', '1', 'Relatório referente as taxas lançadas.', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229108, 459, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229109, 'Andamento de Auto', 'Andamento de Auto', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000142', '1', '1', 'Andamento de Auto', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229109, 462, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229110, 'Intimação', 'Intimação', 'fis2_fis_fiscalinf001.php?intimacao=1', '1', '1', 'Relatórios >> Intimação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229110, 486, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229111, 'Intimação (Período)', 'Intimação (Período)', 'fis2_fis_relnotific001.php?intimacao=1', '1', '1', 'Fiscal >> Relatórios >> Intimação (Período)', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229111, 487, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229112, 'Notificação Lançamento - Webiss', 'Notificação Lançamento - Webiss', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000471', '1', '1', 'Notificação Lançamento - Webiss', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229112, 533, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229113, 'Autos provisórios', 'Autos provisórios', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000505', '1', '1', 'Autos provisórios', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229113, 547, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229114, 'Notificacoes e Intimacoes / Provisórios', 'Notificacoes e Intimacoes / Provisórios', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000502', '1', '1', 'Notificacoes e Intimacoes / Provisórios', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229114, 548, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229115, 'Auto por Fiscal', 'Auto por Fiscal', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000506', '1', '1', 'Auto por Fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229115, 549, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229116, 'Autos por Tipo de Fiscalização', 'Autos por Tipo de Fiscalização', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000555', '1', '1', 'Autos por Tipo de Fiscalização', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229116, 561, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229117, 'Resultado Gerencial de Autos Implantados por período', 'Resultado Gerencial de Autos Implantados por período', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000587', '1', '1', 'Resultado Gerencial de Autos Implantados por período', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229117, 575, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229118, 'Peças Fiscais', 'Peças Fiscais', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000594', '1', '1', 'Peças Fiscais', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229118, 580, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229119, 'Notificação de Lançamento', 'Notificação de Lançamento', 'fis2_fis_notlanc001.php', '1', '1', 'Notificação de Lançamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229119, 583, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229120, 'Peças Fiscais - módulo Notificação de Lançamento', 'Peças Fiscais - módulo Notificação de Lançamento', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000652', '1', '1', 'Peças Fiscais - módulo Notificação de Lançamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229120, 606, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229121, 'Notificação de Lançamento Em Massa', 'Notificação de Lançamento Em Massa', 'fis2_fis_notlancmassa001.php', '1', '1', 'Notificação de Lançamento Em Massa', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229121, 607, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229122, 'Notificações de Lançamento por Lote', 'Notificações de Lançamento por Lote', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000671', '1', '1', 'Notificações de Lançamento por Lote', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229122, 632, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229123, 'Processos Fiscais (CSV)', 'Processos Fiscais (CSV)', 'sys4_geradorteladinamica001.php?iCodRelatorio=1000800', '1', '1', 'Processos Fiscais (CSV)', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229123, 770, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229124, 'NFS-e', 'NFS-e', '', '1', '1', 'NFS-e', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(30, 229124, 861, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229125, 'Notas Irregulares (Webservice)', 'Notas Irregulares (Webservice)', 'fis2_fis_notasirregulares001.php', '1', '1', 'Notas Irregulares (Webservice)', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229124, 229125, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229126, 'Rps Fora do Prazo', 'Rps Fora do Prazo', 'fis2_fis_rpsforaprazo001.php', '1', '1', 'Rps Fora do Prazo', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229124, 229126, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229127, 'Fiscalização de AIDOF', 'Fiscalização de AIDOF', 'fis2_fis_quantidaderps001.php', '1', '1', 'Fiscalização de AIDOF', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229124, 229127, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229128, 'Inconsistências de Retenções', 'Inconsistências de Retenções', 'fis2_fis_comparativoretencao001.php', '1', '1', 'Inconsistências de Retenções', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229124, 229128, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229129, 'Competências Encerradas', 'Competências Encerradas', 'fis2_fis_competenciasencerradas001.php', '1', '1', 'Competências Encerradas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229124, 229129, 5, 229038);


            -- 1818
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229038, 1818, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229130, 'Alvará Sanitário', 'Alvará Sanitario', '', '1', '1', 'Procedimento >> Alvará Sanitário', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229130, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229131, 'Inclusão', 'Inclusão', 'fis1_fis_sanitario001.php', '1', '1', 'Procedimento >> Alvará Sanitário >> Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229130, 229131, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229132, 'Alteração', 'Alteração', 'fis1_fis_sanitario002.php', '1', '1', 'Procedimento >> Alvará Sanitário >> Alteração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229130, 229132, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229133, 'Exclusão', 'Exclusão', 'fis1_fis_sanitario003.php', '1', '1', 'Procedimento >> Alvará Sanitário >> Exclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229130, 229133, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229134, 'Baixa de Atividade', 'Baixa de Atividade', 'fis4_fis_baixasaniatividade001.php', '1', '1', 'Procedimento >> Alvará Sanitário >> Baixa de Atividade', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229130, 229134, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229135, 'Excluir Baixa', 'Excluir Baixa', 'fis4_fis_baixasaniatividade002.php', '1', '1', '', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229130, 229135, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229136, 'Requisicao Aidof', 'Requisição Aidof', 'fis1_fis_requisicaoaidof001.php', '1', '1', 'Programa de liberação ou bloqueio de requisições', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229136, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229137, 'Libera AIDOF', 'Libera AIDOF', '', '1', '1', 'Procedimentos >> Libera AIDOF (Impressão de Documentos Fiscais)', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229137, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229138, 'Inclusão', 'Inclusão', 'fis4_fis_aidof004.php', '1', '1', 'Procedimentos >> Libera AIDOF >> Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229137, 229138, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229139, 'Alteração', 'Alteração', 'fis4_fis_aidof005.php', '1', '1', 'Procedimentos >> Libera AIDOF >> Alteração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229137, 229139, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229140, 'Cancelar', 'Cancelar', 'fis1_fis_aidofcancalt001.php', '1', '1', 'Procedimentos >> Libera AIDOF >> Cancelar', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229137, 229140, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229141, 'Descancelar', 'Descancelar', 'fis1_fis_aidofdescancalt001.php', '1', '1', 'Procedimentos >> Libera AIDOF >> Descancelar', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229137, 229141, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229142, 'Andamentos', 'Andamentos', '', '1', '1', 'Andamentos', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229142, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229143, 'Auto de Infração', 'Auto de Infração', '', '1', '1', 'Procedimentos >> Andamentos >> Auto de Infração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229142, 229143, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229144, 'Inclusão', 'Inclusão', 'fis3_fis_fandamauto005.php', '1', '1', 'Inclusão de andamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229143, 229144, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229145, 'Alteração', 'Alteração', 'fis3_fis_fandamauto005.php?db_opcao=2', '1', '1', 'Alteração de Andamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229143, 229145, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229146, 'Exclusão', 'Exclusão', 'fis3_fis_fandamauto005.php?db_opcao=3', '1', '1', 'Exclusão de andamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229143, 229146, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229147, 'Notificações', 'Notificações', '', '1', '1', 'Procedimentos >> Andamentos >> Notificações', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229142, 229147, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229148, 'Inclusão', 'Inclusão', 'fis3_fis_fandamnoti005.php', '1', '1', 'Inclusão de andamento de notificação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229147, 229148, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229149, 'Alteração', 'Alteração', 'fis3_fis_fandamnoti005.php?db_opcao=2', '1', '1', 'alteração de andamento de uma notificação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229147, 229149, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229150, 'Exclusão', 'Exclusão', 'fis3_fis_fandamnoti005.php?db_opcao=3', '1', '1', 'exclusão de um andamento de notificação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229147, 229150, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229151, 'Vistorias', 'Vistorias', 'fis3_fis_fandam001.php', '1', '1', 'Procedimentos >> Andamentos >> Vistorias', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229142, 229151, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229152, 'Inclusão', 'Inclusão de Fandam', 'fis3_fis_fandam005.php', '1', '1', 'Inclusão de Fandam', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229151, 229152, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229153, 'Alteração', 'Alteração de Fandam', 'fis3_fis_fandam005.php?db_opcao=2', '1', '1', 'Alteração de Fandam', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229151, 229153, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229154, 'Exclusão', 'Exclusão de Fandam', 'fis3_fis_fandam005.php?db_opcao=3', '1', '1', 'Exclusão de Fandam', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229151, 229154, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229155, 'Intimação', 'Intimação', '', '1', '1', 'Procedimento >> Andamento >> Intimação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229142, 229155, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229156, 'Inclusão', 'Inclusão de Andamento de Intimação', 'fis3_fis_fandamnoti001.php?intimacao=1', '1', '1', 'Procedimento >> Andamento >> Intimação >> Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229155, 229156, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229157, 'Alteração', 'Alteração de Andamento de Intimação', 'fis3_fis_fandamnoti002.php?intimacao=1', '1', '1', 'Procedimento >> Andamento >> Intimação >> Alteração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229155, 229157, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229158, 'Exclusão', 'Exclusão de Andamento de Intimação', 'fis3_fis_fandamnoti003.php?intimacao=1', '1', '1', 'Procedimento >> Andamento >> Intimação >> Exclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229155, 229158, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229159, 'Notificação de Lançamento', 'Notificação de Lançamento', '', '1', '1', 'Notificação de Lançamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229142, 229159, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229160, 'Inclusão', 'Inclusão', 'fis3_fis_fandamlancamento001.php', '1', '1', 'Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229159, 229160, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229161, 'Alteração', 'Alteração', 'fis3_fis_fandamlancamento002.php', '1', '1', 'Alteração', 'f');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229159, 229161, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229162, 'Levantamento', 'Levantamento', '', '1', '1', 'Procedimento >> Levantamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229162, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229163, 'Inclusão', 'Inclusão', 'fis4_fis_levanta001.php', '1', '1', 'Procedimento >> Levantamento >> Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229162, 229163, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229164, 'Alteração', 'Alteração', 'fis4_fis_levanta005.php', '1', '1', 'Alteração de levantamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229162, 229164, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229165, 'Exclusão', 'Exclusão', 'fis4_fis_levanta006.php', '1', '1', 'Exclusão de levantamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229162, 229165, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229166, 'Exportação', 'Exportação', 'fis4_fis_importalevan001.php', '1', '1', 'Procedimento >> Levantamento >> Exporta', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229162, 229166, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229167, 'Cancela Exportação', 'Cancela Exportação', 'fis4_fis_cancelimport001.php', '1', '1', 'Procedimento >> Levantamento >> Cancela Exportação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229162, 229167, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229168, 'Inclusão (Novo)', 'Levantamento - Inclusão (Novo)', '', '1', '1', 'Levantamento - Inclusão (Novo)', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229162, 229168, 7, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229169, 'Inscrição Municipal', 'Levantamento - Inscrição Municipal', 'fis4_fis_levanta001.php?valor=munic', '1', '1', 'Levantamento - Inscrição Municipal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229168, 229169, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229170, 'Nome/Razão Social', 'Levantamento - Nome/Razão Social', 'fis4_fis_levanta001.php?valor=nome', '1', '1', 'Levantamento - Nome/Razão Social', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229168, 229170, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229171, 'Processo Fiscal', 'Levantamento - Processo Fiscal', 'fis4_fis_levanta001.php?valor=proc', '1', '1', 'Levantamento - Processo Fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229168, 229171, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229172, 'Notificações', 'Notificações', '', '1', '1', 'Notificações', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229172, 6, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229173, 'Inclusão', 'Inclusão', '', '1', '1', 'Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229172, 229173, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229174, 'CGM', 'Notificação por CGM', 'fis1_fis_fiscal001.php?como=cgm', '1', '1', 'Procedimentos >> Notificação >> CGM', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229173, 229174, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229175, 'Inscrição', 'Inscrição', 'fis1_fis_fiscal001.php?como=inscr', '1', '1', 'Procedimentos >> Notificação >> Inscrição', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229173, 229175, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229176, 'Matrícula', 'Matrícula', 'fis1_fis_fiscal001.php?como=matric', '1', '1', 'Procedimentos >> Notificação >> Matrícula', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229173, 229176, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229177, 'Alvará Sanitário', 'Alvará Sanitário', 'fis1_fis_fiscal001.php?como=sani', '1', '1', 'Procedimentos >> Notificação >> Alvará Sanitário', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229173, 229177, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229178, 'Vistoria', 'Vistoria', 'fis1_fis_fiscal001.php?como=vist', '1', '1', 'Procedimentos >> Notificação >> Vistoria', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229173, 229178, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229179, 'Processo Fiscal', 'Processo Fiscal', 'fis1_fis_fiscal001.php?como=proc', '1', '1', 'Procedimentos >> Notificações >> Inclusão >> Processo Fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229173, 229179, 6, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229180, 'Alteração', 'Alteração de Fiscal', 'fis1_fis_fiscal002.php', '1', '1', 'Alteração de Fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229172, 229180, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229181, 'Exclusão', 'Exclusão de Fiscal', 'fis1_fis_fiscal003.php', '1', '1', 'Exclusão de Fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229172, 229181, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229182, 'Auto de Infração', 'Auto de Infração', '', '1', '1', 'Auto de Infração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229182, 7, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229183, 'Inclusão', 'Inclusão de Auto', 'fis1_fis_auto001.php', '1', '1', 'Inclusão de Auto', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229182, 229183, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229184, 'Alteração', 'Alteração de Auto', 'fis1_fis_auto002.php', '1', '1', 'Alteração de Auto', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229182, 229184, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229185, 'Exclusão', 'Exclusão de Auto', 'fis1_fis_auto003.php', '1', '1', 'Exclusão de Auto', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229182, 229185, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229186, 'Baixa', 'Baixa', '', '1', '1', 'Procedimento >> Auto de Infração >> Baixa', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229182, 229186, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229187, 'Inclusão', 'Inclusão', 'fis4_fis_autobaixaproc001.php', '1', '1', 'Procedimento >> Auto de Infração >> Baixa >> Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229186, 229187, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229188, 'Exclusão', 'Exclusão', 'fis4_fis_autobaixaproc002.php', '1', '1', 'Procedimento >> Auto de Infração >> Baixa >> Exclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229186, 229188, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229189, 'Inclusão (Novo)', 'Auto de infração - Inclusão (Novo)', '', '1', '1', 'Auto de infração - Inclusão (Novo)', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229182, 229189, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229190, 'CGM', 'Auto de infração - CGM', 'fis1_fis_auto001.php?como=cgm', '1', '1', 'Auto de infração - CGM', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229189, 229190, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229191, 'Inscrição', 'Auto de infração - Inscrição', 'fis1_fis_auto001.php?como=munic', '1', '1', 'Auto de infração - Inscrição', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229189, 229191, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229192, 'Matrícula', 'Auto de infração - Matrícula', 'fis1_fis_auto001.php?como=matric', '1', '1', 'Auto de infração - Matrícula', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229189, 229192, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229193, 'Alvará Sanitário', 'Auto de infração - Alvará Sanitário', 'fis1_fis_auto001.php?como=sani', '1', '1', 'Auto de infração - Alvará Sanitário', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229189, 229193, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229194, 'Notificação', 'Auto de infração - Notificação', 'fis1_fis_auto001.php?como=notif', '1', '1', 'Auto de infração - Notificação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229189, 229194, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229195, 'Processo Fiscal', 'Auto de infração - Processo Fiscal', 'fis1_fis_auto001.php?como=proc', '1', '1', 'Auto de infração - Processo Fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229189, 229195, 6, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229196, 'Auto de Lançamento', 'Auto de Lançamento', 'fis4_fis_autolancamento001.php', '1', '1', 'Procedimentos >> Auto de Lançamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229196, 8, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229197, 'Vistorias', 'Vistorias', '', '1', '1', 'Vistorias', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229197, 9, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229198, 'Inclusão', 'Inclusão de Vistorias', '', '1', '1', 'Inclusão de Vistorias', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229197, 229198, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229199, 'CGM', 'CGM', 'fis1_fis_vistorias001.php?cgm=1', '1', '1', 'Procedimento >> Vistoria >> Inclusão >> CGM', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229198, 229199, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229200, 'Inscrição', 'Inscrição', 'fis1_fis_vistorias001.php?inscr=1', '1', '1', 'Procedimento >> Vistoria >> Inclusão >> Inscrição', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229198, 229200, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229201, 'Matrícula', 'MatrÍcula', 'fis1_fis_vistorias001.php?matric=1', '1', '1', 'Procedimento >> Vistoria >> Inclusão >> MatrÍcula', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229198, 229201, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229202, 'Alvará Sanitário', 'Alvará Sanitário', 'fis1_fis_vistorias001.php?sani=1', '1', '1', 'Procedimento >> Vistoria >> Inclusão >> Alvará Sanitário', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229198, 229202, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229203, 'Processo Fiscal', 'Processo Fiscal', 'fis1_fis_vistorias001.php?proc=1', '1', '1', 'Processo Fiscal', 'f');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229198, 229203, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229204, 'Alteração', 'Alteração de Vistorias', 'fis1_fis_vistorias002.php', '1', '1', 'Alteração de Vistorias', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229197, 229204, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229205, 'Exclusão', 'Exclusão de Vistorias', 'fis1_fis_vistorias003.php', '1', '1', 'Exclusão de Vistorias', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229197, 229205, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229206, 'Anulação', 'Anulação de Vistorias', 'fis1_fis_vistoriasanu001.php', '1', '1', 'Inclusão de Vistoriasanu', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229197, 229206, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229207, 'Vistoria (Geral)', 'Vistoria (Geral)', 'fis4_fis_vistgeral001.php', '1', '1', 'Procedimentos >> Vistoria (Geral)', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229207, 10, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229208, 'ISSQN Variavel', 'ISSQN Variavel', 'div4_importavariavel001.php', '1', '1', 'Importar ISSQN Variavel para dívida', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229208, 11, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229209, 'Parâmetros', 'Parâmetros', 'fis1_fis_parfiscal002.php', '1', '1', 'Manutenção de parametros do modulo fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229209, 12, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229210, 'Processo Fiscal', 'Cadastro de Procfiscal', '', '1', '1', 'Cadastro de Procfiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229210, 13, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229211, 'Inclusão', 'Inclusão de Procfiscal', 'fis1_fis_procfiscal001.php', '1', '1', 'Inclusão de Procfiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229210, 229211, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229212, 'Alteração', 'Alteração de Procfiscal', 'fis1_fis_procfiscal002.php', '1', '1', 'Alteração de Procfiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229210, 229212, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229213, 'Exclusão', 'Exclusão de Procfiscal', 'fis1_fis_procfiscal003.php', '1', '1', 'Exclusão de Procfiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229210, 229213, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229214, 'Prorrogação / Finalização', 'Prorrogação / Finalização', 'fis1_fis_procfiscalfinalizacao001.php', '1', '1', 'Prorrogação / Finalização', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229210, 229214, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229215, 'TIAF', 'TIAF', '', '1', '1', 'Cadastro de Procfiscalfases', 'f');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229215, 14, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229216, 'Inclusão', 'Inclusão de Procfiscalfases', 'fis1_fis_procfiscalfases001.php', '1', '1', 'Inclusão de Procfiscalfases', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229215, 229216, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229217, 'Alteração', 'Alteração de Procfiscalfases', 'fis1_fis_procfiscalfases002.php', '1', '1', 'Alteração de Procfiscalfases', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229215, 229217, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229218, 'Exclusão', 'Exclusão de Procfiscalfases', 'fis1_fis_procfiscalfases003.php', '1', '1', 'Exclusão de Procfiscalfases', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229215, 229218, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229219, 'TFAF', 'TFAF', '', '1', '1', 'TFAF', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229219, 15, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229220, 'Inclusão', 'Inclusão', 'fis1_fis_procfiscalfasesTfaf001.php', '1', '1', '', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229219, 229220, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229221, 'Alteração', 'Alteração', 'fis1_fis_procfiscalfasesTfaf002.php', '1', '1', '', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229219, 229221, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229222, 'Exclusão', 'Exclusão', 'fis1_fis_procfiscalfasesTfaf003.php', '1', '1', '', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229219, 229222, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229223, 'Retificação de auto de infração', 'Retificação de auto de infração', 'fis1_fis_autoretifica001.php', '1', '1', 'Procedimentos >> Retificação de auto de infração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229223, 112, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229224, 'Intimação', 'Intimação', '', '1', '1', 'Procedimentos >> Intimação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229224, 113, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229225, 'Inclusão', 'Inclusão', '', '1', '1', 'Procedimentos >> Intimação >> Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229224, 229225, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229226, 'CGM', 'Intimação por CGM', 'fis1_fis_fiscal001.php?como=cgm&intimacao=1', '1', '1', 'Procedimentos >> Intimação >> Inclusão >> CGM', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229225, 229226, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229227, 'Inscrição', 'Intimação por Inscrição', 'fis1_fis_fiscal001.php?como=inscr&intimacao=1', '1', '1', 'Procedimentos >> Intimação >> Inclusão >> Intimação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229225, 229227, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229228, 'Matrícula', 'Intimação por Matrícula', 'fis1_fis_fiscal001.php?como=matric&intimacao=1', '1', '1', 'Procedimentos >> Intimação >> Inclusão >> Matrícula', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229225, 229228, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229229, 'Alvará Sanitário', 'Intimação por Alvará Sanitário', 'fis1_fis_fiscal001.php?como=sani&intimacao=1', '1', '1', 'Procedimentos >> Intimação >> Inclusão >> Alvará Sanitário', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229225, 229229, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229230, 'Vistoria', 'Intimação por Vistoria', 'fis1_fis_fiscal001.php?como=vist&intimacao=1', '1', '1', 'Procedimentos >> Intimação >> Inclusão >> Vistoria', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229225, 229230, 5, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229231, 'Processo Fiscal', 'Processo Fiscal', 'fis1_fis_fiscal001.php?como=proc&intimacao=1', '1', '1', 'Procedimento >> Intimação >> Inclusão >> Processo Fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229225, 229231, 6, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229232, 'Alteração', 'Alteração de Intimação', 'fis1_fis_fiscal002.php?intimacao=1', '1', '1', 'Procedimentos >> Intimação >> Alteração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229224, 229232, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229233, 'Exclusão', 'Exclusão de Intimação', 'fis1_fis_fiscal003.php?intimacao=1', '1', '1', 'Procedimentos >> Intimação >> Exclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229224, 229233, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229234, 'Taxas', 'Inclusão e cálculo de taxas', '', '1', '1', 'Menu para inclusão de uma taxas para um CGM e cálculo geral de taxas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229234, 115, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229235, 'Lançamento', 'Lança uma taxa', 'fis4_fis_lancamentotaxadiversos.php', '1', '1', 'Menu para lançar uma taxa para um contribuinte.', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229234, 229235, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229236, 'Cálculo Geral', 'Cálculo geral de taxas', 'fis4_fis_calculotaxadiversos.php', '1', '1', 'Menu para cálculo geral de taxas.', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229234, 229236, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229237, 'Notificação de Lançamento', 'Notificação de Lançamento', '', '1', '1', 'Notificação de Lançamento', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229237, 119, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229238, 'Inclusão', 'Inclusão', '', '1', '1', 'Inclusão', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229237, 229238, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229239, 'CGM', 'CGM', 'fis1_fis_lancamento001.php?como=cgm', '1', '1', 'CGM', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229238, 229239, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229240, 'Inscrição', 'Inscrição', 'fis1_fis_lancamento001.php?como=munic', '1', '1', 'Inscrição', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229238, 229240, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229241, 'Matrícula', 'Matrícula', 'fis1_fis_lancamento001.php?como=matric', '1', '1', 'Matrícula', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229238, 229241, 3, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229242, 'Processo Fiscal', 'Processo Fiscal', 'fis1_fis_lancamento001.php?como=proc', '1', '1', 'Processo Fiscal', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229238, 229242, 4, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229243, 'Alteração', 'Alteração', 'fis1_fis_lancamento002.php', '1', '1', 'Alteração', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229237, 229243, 2, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229244, 'Prestação de Contas', 'Prestação de Contas', '', '1', '1', 'Prestação de Contas', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(1818, 229244, 143, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229245, 'Importação', 'Importação', 'fis4_fis_prestacaoimportacao001.php', '1', '1', 'Importação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229244, 229245, 1, 229038);

            INSERT INTO db_itensmenu(id_item,descricao,help,funcao,itemativo, manutencao,desctec,libcliente) values(229246, 'Exportação', 'Exportação', 'fis4_fis_prestacaoexportacao001.php', '1', '1', 'Exportação', 't');
            INSERT INTO db_menu(id_item, id_item_filho, menusequencia, modulo) values(229244, 229246, 2, 229038);
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
        DB::unprepared(<<<SQL
            DELETE FROM atendcadareamod WHERE at26_id_item = 229038;
            DELETE FROM db_modulos WHERE id_item = 229038;
            DELETE FROM db_menu WHERE modulo = 229038;
            DELETE FROM db_itensmenu WHERE id_item BETWEEN 229038 AND 229246;
            DELETE FROM db_sysmodulo WHERE codmod = 92;
SQL
        );
    }
}
