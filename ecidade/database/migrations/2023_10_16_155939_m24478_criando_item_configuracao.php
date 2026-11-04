<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24478CriandoItemConfiguracao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dados = [
            [228978, 'Configuração', 'Configuração', '', '1', '1', 'Configuração Matricula On Line', 'true'],
            [228979, 'Duvidas Frequentes', 'Duvidas Frequentes', 'web/educacao/matricula-online/configuracao/duvidas-frequentes', '1', '1', 'Duvidas Frequentes', 'true'],
            [228980 ,'Mensagens Personalizadas' ,'Mensagens Personalizadas' ,'web/educacao/matricula-online/configuracao/mensagens-personalizadas' ,'1' ,'1' ,'Mensagens Personalizadas' ,'true'],
            [228981 ,'Documentos' ,'Documentos' ,'web/educacao/matricula-online/configuracao/documentos' ,'1' ,'1' ,'Documentos' ,'true'],
            [228982 ,'Imagens Personalizadas' ,'Imagens Personalizadas' ,'web/educacao/matricula-online/configuracao/imagens-personalizadas' ,'1' ,'1' ,'Imagens Personalizadas' ,'true'],
            [228983 ,'Notícias' ,'Notícias' ,'web/educacao/matricula-online/configuracao/noticias' ,'1' ,'1' ,'Cadastro de Notícias' ,'true'],
            [228988 ,'Cores Personalizadas' ,'Cores Personalizadas' ,'web/educacao/matricula-online/configuracao/cores-personalizadas' ,'1' ,'1' ,'Cores Personalizadas' ,'true']
        ];

        foreach ($dados as $dado) {
            DB::table('configuracoes.db_itensmenu')->insert([
                'id_item' => $dado[0],
                'descricao' => $dado[1],
                'help' => $dado[2],
                'funcao' => $dado[3],
                'itemativo' => $dado[4],
                'manutencao' => $dado[5],
                'desctec' => $dado[6],
                'libcliente' => $dado[7]
            ]);
        }

        $dados = [
            [228923 ,228978 ,5 ,228923],
            [228978 ,228979 ,1 ,228923],
            [228978 ,228980 ,2 ,228923],
            [228978 ,228981 ,3 ,228923],
            [228978 ,228982 ,4 ,228923],
            [228978 ,228983 ,5 ,228923],
            [228978 ,228988 ,6 ,228923]
        ];

        foreach ($dados as $dado) {
            DB::table('configuracoes.db_menu')->insert([
                'id_item' => $dado[0],
                'id_item_filho' => $dado[1],
                'menusequencia' => $dado[2],
                'modulo' => $dado[3]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $dados = [228978, 228979, 228980, 228981, 228982, 228983, 228988];
        DB::table('configuracoes.db_itensmenu')->whereIn('id_item', $dados)->delete();
        DB::table('configuracoes.db_menu')->whereIn('id_item_filho', $dados)->delete();
    }
}
