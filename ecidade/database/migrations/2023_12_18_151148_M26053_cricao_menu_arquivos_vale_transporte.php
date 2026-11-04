<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26053CricaoMenuArquivosValeTransporte extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
UPDATE db_itensmenu SET id_item = 782401 , descricao = 'Tipos de Vale Transporte' , help = 'Tipos de Vale Transporte' , itemativo = '1' , manutencao = '1' , desctec = 'Tipos de Vale Transporte' , libcliente = 'true' WHERE id_item = 782401;
UPDATE db_itensmenu SET id_item = 804219 , descricao = 'Servidores com VT ' , help = 'Servidores com VT' , itemativo = '1' , manutencao = '1' , desctec = 'Servidores com VT' , libcliente = 'true' WHERE id_item = 804219;
UPDATE db_itensmenu SET id_item = 826054 , descricao = 'Relatório VT' , help = 'Relatório VT' , funcao = 'pes2_rhteutri001.php' , itemativo = '1' , manutencao = '1' , desctec = 'Relatório VT' , libcliente = 'true' WHERE id_item = 826054;

INSERT INTO db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
SELECT 229007 ,'Rio Card' ,'Rio Card' ,'' ,'1' ,'1' ,'Rio Card' ,'true'
WHERE NOT EXISTS (
    SELECT 1 FROM db_itensmenu WHERE id_item = 229007
);

INSERT INTO db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
SELECT 229008 ,'SindPass' ,'SindPass' ,'' ,'1' ,'1' ,'SindPass' ,'true'
WHERE NOT EXISTS (
    SELECT 1 FROM db_itensmenu WHERE id_item = 229008
);

INSERT INTO db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
SELECT 229009 ,'Arquivo de Cadastro de Vale Transporte Rio Card' ,'Arquivo de Cadastro de Vale Transporte Rio Card' ,'pes2_recargarhteutri001.php' ,'1' ,'1' ,'Geração de Arquivo de Cadastro de Vale Transporte Rio Card' ,'true'
WHERE NOT EXISTS (
    SELECT 1 FROM db_itensmenu WHERE id_item = 229009
);

INSERT INTO db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
SELECT 229010 ,'Arquivo de Pedido de Vale Transporte Rio Card' ,'Arquivo de Pedido de Vale Transporte Rio Card' ,'pes2_recargapedidorhteutri001.php' ,'1' ,'1' ,'Geração de Arquivo de Pedido de Vale Transporte Rio Card' ,'true'
WHERE NOT EXISTS (
    SELECT 1 FROM db_itensmenu WHERE id_item = 229010
);

INSERT INTO db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
SELECT 229011 ,'Arquivo de Cadastro de Vale Transporte SindPass' ,'Arquivo de Cadastro de Vale Transporte SindPass' ,'pes2_recargasindpassrhteutri001.php' ,'1' ,'1' ,'Arquivo de Cadastro de Vale Transporte SindPass' ,'true'
WHERE NOT EXISTS (
    SELECT 1 FROM db_itensmenu WHERE id_item = 229011
);

INSERT INTO db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
SELECT 229012 ,'Arquivo de Pedido de Vale Transporte SindPass' ,'Arquivo de Pedido de Vale Transporte SindPass' ,'pes2_pedidosindpassrhteutri001.php' ,'1' ,'1' ,'Arquivo de Pedido de Vale Transporte SindPass' ,'true'
WHERE NOT EXISTS (
    SELECT 1 FROM db_itensmenu WHERE id_item = 229012
);

INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
SELECT 5156 ,782401 ,2 ,952
WHERE NOT EXISTS (
    SELECT 1 FROM db_menu WHERE id_item = 5156 AND id_item_filho = 782401
);

INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
SELECT 5156 ,804219 ,3 ,952
WHERE NOT EXISTS (
    SELECT 1 FROM db_menu WHERE id_item = 5156 AND id_item_filho = 804219
);

INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
SELECT 5156 ,826054 ,4 ,952
WHERE NOT EXISTS (
    SELECT 1 FROM db_menu WHERE id_item = 5156 AND id_item_filho = 826054
);

INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
SELECT 5156 ,229007 ,5 ,952
WHERE NOT EXISTS (
    SELECT 1 FROM db_menu WHERE id_item = 5156 AND id_item_filho = 229007
);

INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
SELECT 5156 ,229008 ,6 ,952
WHERE NOT EXISTS (
    SELECT 1 FROM db_menu WHERE id_item = 5156 AND id_item_filho = 229008
);

INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
SELECT 229007 ,229009 ,1 ,952
WHERE NOT EXISTS (
    SELECT 1 FROM db_menu WHERE id_item = 229007 AND id_item_filho = 229009
);

INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
SELECT 229007 ,229010 ,2 ,952
WHERE NOT EXISTS (
    SELECT 1 FROM db_menu WHERE id_item = 229007 AND id_item_filho = 229010
);

INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
SELECT 229008 ,229011 ,1 ,952
WHERE NOT EXISTS (
    SELECT 1 FROM db_menu WHERE id_item = 229008 AND id_item_filho = 229011
);

INSERT INTO db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
SELECT 229008 ,229012 ,2 ,952
WHERE NOT EXISTS (
    SELECT 1 FROM db_menu WHERE id_item = 229008 AND id_item_filho = 229012
);

DELETE FROM db_itensmenu WHERE id_item = 782400;
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
DELETE FROM db_menu WHERE id_item_filho IN (782401,804219,826054,229007,229008,229009,229010,229011,229012) AND modulo = 952;
DELETE FROM db_itensmenu WHERE id_item IN (229007,229008,229009,229010,229011,229012);

INSERT INTO db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
SELECT 782400 ,'Vale Transporte Integrado' ,'Vale Transporte Integrado' ,'' ,'1' ,'1' ,'Vale Transporte Integrado' ,'true'
WHERE NOT EXISTS (
    SELECT 1 FROM db_itensmenu WHERE id_item = 782400
);

SQL
        );
    }
}
