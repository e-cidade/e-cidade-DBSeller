<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24614ExcluiConlancamrecursoDuplicado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

      $sql =   <<<SQL

create table w_conlancam as
      select count(c69_codlan) as total_conlancamval,
             c69_codlan
       from conlancamval
       where  c69_data >= '2023-05-19'
      group by c69_codlan;

  create table w_conlacamrecurso as
      select count(c130_conlancam) as tatal_conlancamrecurso,
             c130_conlancam
       from conlancamrecurso
       join w_conlancam on c69_codlan = c130_conlancam
      group by c130_conlancam;

create table w_recursosduplicados as
   select (total_conlancamval * 2 ) as quantidade_correta,
           *
     from w_conlancam
     join w_conlacamrecurso on c69_codlan = c130_conlancam
    where (total_conlancamval * 2 ) < tatal_conlancamrecurso ;

SQL;

      DB::connection()->getPdo()->exec($sql);


        DB::table("w_recursosduplicados")
        ->get()
        ->map( function( $dado ){

            $remover = "delete
                         from conlancamrecurso
                        where c130_sequencial in (select c130_sequencial
                                                    from conlancamrecurso
                                                   where c130_conlancam = {$dado->c69_codlan}
                                                order by c130_sequencial
                                              desc limit {$dado->quantidade_correta} )";
            DB::connection()->getPdo()->exec($remover);

        } );


    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

       $sql = <<<SQL

        drop table w_conlancam;
        drop table w_conlacamrecurso;
        drop table w_recursosduplicados;
SQL;

       DB::connection()->getPdo()->exec($sql);


    }


}
