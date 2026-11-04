<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class PrimeiroAcessoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::select("select fc_startsession();");

        $usuario = DB::select("select 1 from db_usuarios where id_usuario = 1");
        if ($usuario === null) {
            DB::statement("insert into db_usuarios (id_usuario , nome , login , senha , usuarioativo , email , usuext) values (1,'DBSeller Inform?tica Ltda','dbseller','" . Encriptacao::encriptaSenha('') . "','1','dbseller#dbseller.com.br',0)");
        }

        $cgm = DB::select("select 1 from cgm where z01_numcgm = 1");
        if ($cgm === null) {
            DB::statement("insert into cgm (z01_numcgm , z01_nome ) values (1,'PREFEITURA DBSELLER')");
        }

        $instituicao = DB::select("select 1 from db_config where codigo = 1");
        if ($instituicao === null) {
            DB::statement("insert into db_config (codigo , nomeinst,  prefeitura , numcgm ) values (1, 'PREFEITURA DBSELLER', true, 1)");
        }

        $usuarioInstituicao = DB::select("select 1 from db_userinst where id_instit = 1 and id_usuario = 1");
        if ($usuarioInstituicao === null) {
            DB::statement("insert into db_userinst (id_instit, id_usuario) values (1, 1)");
        }

        $usuarioCgm = DB::select("select 1 from db_usuacgm where id_usuario = 1");
        if ($usuarioCgm === null) {
            DB::statement("insert into db_usuacgm (id_usuario, cgmlogin) values (1, 1)");
        }

        $departamento = DB::select("select 1 from db_depart where coddepto = 1");
        if ($departamento === null) {
            DB::statement("insert into db_depart (coddepto , descrdepto ,instit) values (1,'CPD',1)");
        }

        $departamentoUsuario = DB::select("select 1 from db_depusu where id_usuario = 1 and coddepto = 1");
        if ($departamentoUsuario === null) {
            DB::statement("insert into db_depusu(id_usuario , coddepto) values (1,1)");
        }

        DB::statement("insert into db_sysregrasacesso values (0,'2000-01-01','00:00','2999-01-01','24:00',1,current_date,'implanta??o')");
        DB::statement("insert into db_sysregrasacessoip values (0,'*')");
    }
}
