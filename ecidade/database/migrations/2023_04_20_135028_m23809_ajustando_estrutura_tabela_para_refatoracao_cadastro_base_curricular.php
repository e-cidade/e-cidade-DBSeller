<?php

use Illuminate\Database\Migrations\Migration;

class M23809AjustandoEstruturaTabelaParaRefatoracaoCadastroBaseCurricular extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        DB::statement("ALTER TABLE escola.base ALTER COLUMN ed31_i_codigo SET DEFAULT nextval('escola.base_ed31_i_codigo_seq')");
        DB::statement("ALTER TABLE escola.escolabase ALTER COLUMN ed77_i_codigo SET DEFAULT nextval('escola.escolabase_ed77_i_codigo_seq')");
        DB::statement("ALTER TABLE escola.baseregimematdiv ALTER COLUMN ed224_i_codigo SET DEFAULT nextval('escola.baseregimematdiv_ed224_i_codigo_seq')");
        DB::statement("ALTER TABLE escola.baseato ALTER COLUMN ed278_i_codigo SET DEFAULT nextval('escola.baseato_codigo')");
        DB::statement("ALTER TABLE escola.baseatoserie ALTER COLUMN ed279_i_codigo SET DEFAULT nextval('escola.baseatoserie_codigo')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        DB::statement("ALTER TABLE escola.base ALTER COLUMN ed31_i_codigo SET DEFAULT 0");
        DB::statement("ALTER TABLE escola.escolabase ALTER COLUMN ed77_i_codigo SET DEFAULT 0");
        DB::statement("ALTER TABLE escola.baseregimematdiv ALTER COLUMN ed224_i_codigo SET DEFAULT 0");
        DB::statement("ALTER TABLE escola.baseato ALTER COLUMN ed278_i_codigo SET DEFAULT 0");
    }

    public function upDicionario()
    {
        DB::table('configuracoes.db_itensfilho')->whereIn('id_item', [1100850, 1100851, 1100852])->delete();
        DB::table('configuracoes.db_itensmenu')->whereIn('id_item', [1100850, 1100851, 1100852])->delete();

        DB::table('configuracoes.db_itensmenu')->where('id_item', 1100849)->update([
            'funcao' => 'web/educacao/escola/cadastros/bases-curriculares'
        ]);
    }

    public function downDicionario()
    {
        $dados = [
            [1100850, 'Inclusão', 'Inclusão de Base' , 'edu1_baseabas001.php', 1, 1, 'Inclusão de Base', true],
            [1100851, 'Alteração', 'Alteração de Base', 'edu1_baseabas002.php', 1, 1, 'Alteração de Base', true],
            [1100852, 'Exclusão', 'Exclusão de Base' , 'edu1_baseabas003.php', 1, 1, 'Exclusão de Base', true]
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
            [1100850, 1008378],
            [1100850, 1008381],
            [1100850, 1008382],
            [1100850, 1008383],
            [1100851, 1008379],
            [1100851, 1008381],
            [1100851, 1008382],
            [1100851, 1008383],
            [1100852, 1008380],
            [1100852, 1008381],
            [1100852, 1008382],
            [1100852, 1008383]
        ];
        foreach ($dados as $dado) {
            DB::table('configuracoes.db_itensfilho')->insert([
                'id_item' => $dado[0],
                'codfilho' => $dado[1]
            ]);
        }

        DB::table('configuracoes.db_itensmenu')->where('id_item', 1100849)->update([
            'funcao' => ''
        ]);
    }
}
