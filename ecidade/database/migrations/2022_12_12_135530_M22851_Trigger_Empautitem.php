<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22851TriggerEmpautitem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

create or replace function fc_empautitem_inc() returns trigger as
$$
declare

  sElementoAutoriza text;
  sElementoDotacao  text;

  lRaise            boolean default false;

begin
  -- Busca elemento do Empenho e da Dotacao para comparação
  select e1.o56_elemento,
         e2.o56_elemento
    into sElementoAutoriza,
         sElementoDotacao
    from empautoriza
         inner join empautitem      on e55_autori    = e54_autori

         inner join orcelemento e1  on e1.o56_codele = e55_codele
                                   and e1.o56_anousu = e54_anousu

         inner join empautidot      on e56_anousu = e54_anousu
                                   and e56_autori = e54_autori

         inner join orcdotacao      on o58_anousu = e56_anousu
                                   and o58_coddot = e56_coddot

         inner join orcelemento e2  on e2.o56_codele = o58_codele
                                   and e2.o56_anousu = o58_anousu
   where e55_autori = new.e55_autori
     and e55_codele = new.e55_codele;

  if lRaise is true then
    raise notice 'ElementoAutoriza %  ElementoDotacao %', sElementoAutoriza, sElementoDotacao;
  end if;

  -- Verifica os primeiros 7 digitos do elemento
  if substr(sElementoAutoriza, 1, 7) <> substr(sElementoDotacao, 1, 7) then
    raise exception 'Você não pode escolher um desdobramento (%) incompatível ao Elemento de Despesa (%) constante na dotação.', substr(sElementoAutoriza, 1, 7), substr(sElementoDotacao, 1, 7);
  end if;

  -- verifica se tem elemento duplicado

  if (select count(distinct e55_codele) from empautitem where e55_autori=new.e55_autori ) > 1 then
     raise exception  'Atenção! Autorização com elemento duplicado no(s) iten(s). Procedimento cancelado.';
  end if;

  return new;
end;

$$ language 'plpgsql';

drop trigger   "tg_empautitem_inc" on empautitem;
create trigger "tg_empautitem_inc" after insert on "empautitem" for each row execute procedure "fc_empautitem_inc" () ;


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

create or replace function fc_empautitem_inc() returns trigger as
$$
declare
  sElementoAutoriza text;
  sElementoDotacao  text;

  lRaise boolean default false;
begin
  -- Busca elemento do Empenho e da Dotacao para comparação
  select e1.o56_elemento,
         e2.o56_elemento
    into sElementoAutoriza,
         sElementoDotacao
    from empautoriza
         inner join empautitem      on e55_autori    = e54_autori

         inner join orcelemento e1  on e1.o56_codele = e55_codele
                                   and e1.o56_anousu = e54_anousu

         inner join empautidot      on e56_anousu = e54_anousu
                                   and e56_autori = e54_autori

         inner join orcdotacao      on o58_anousu = e56_anousu
                                   and o58_coddot = e56_coddot

         inner join orcelemento e2  on e2.o56_codele = o58_codele
                                   and e2.o56_anousu = o58_anousu
   where e55_autori = new.e55_autori
     and e55_codele = new.e55_codele;

  if lRaise is true then
    raise notice 'ElementoAutoriza %  ElementoDotacao %', sElementoAutoriza, sElementoDotacao;
  end if;

  -- Verifica os primeiros 7 digitos do elemento
  if substr(sElementoAutoriza, 1, 7) <> substr(sElementoDotacao, 1, 7) then
    raise exception 'Você não pode escolher um desdobramento incompatível ao Elemento de Despesa constante na dotação.';
  end if;

  -- verifica se tem elemento duplicado

  if (select count(distinct e55_codele) from empautitem where e55_autori=new.e55_autori )>1 then
     raise exception  'Atenção! Empenho com elemento diferente no(s) iten(s).Procedimento cancelado.';
  end if;



  return new;
end;
$$ language 'plpgsql';


drop trigger   "tg_empautitem_inc" on empautitem;
create trigger "tg_empautitem_inc" after insert on "empautitem" for each row execute procedure "fc_empautitem_inc" () ;


SQL
);

    }
}
