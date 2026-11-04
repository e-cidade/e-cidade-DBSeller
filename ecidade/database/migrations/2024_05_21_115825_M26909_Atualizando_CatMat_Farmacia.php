<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26909AtualizandoCatMatFarmacia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
DB::unprepared(<<<SQL
INSERT INTO medicamentos values (541,'BR0461492U0140','AGULHA DESCARTÁVEL 4 MM X 0, 23 MM PARA CANETA APLICADORA DE INSULINA');
INSERT INTO medicamentos values (542,'BR0448845U0009','CETOPROFENO 50 MG/ML SOLUÇÃO INJETÁVEL 2 ML');
INSERT INTO medicamentos values (543,'BR0385424U0041','CYNARA SCOLYMUS (ALCACHOFRA) 24 A 48 MG CÁPSULA');
INSERT INTO medicamentos values (544,'BR0271028U0063','DICLOFENACO RESINATO 15 MG/ML SUSPENSÃO ORAL 10 ML');
INSERT INTO medicamentos values (545,'BR0271028U0086','DICLOFENACO RESINATO 15 MG/ML SUSPENSÃO ORAL 20 ML');
INSERT INTO medicamentos values (546,'BR0272334U0170','DIMENIDRINATO + PIRIDOXINA 50 + 50 MG/ML SOLUÇÃO INJETÁVEL 1 ML');
INSERT INTO medicamentos values (547,'BR0288275U0028','TINIDAZOL + MICONAZOL, NITRATO 30 + 20 MG/G CREME VAGINAL 45 G');
INSERT INTO medicamentos values (548,'BR0288275U0031','TINIDAZOL + MICONAZOL, NITRATO 30 + 20 MG/G CREME VAGINAL 80 G');
INSERT INTO medicamentos values (549,'BR0288275U0027','TINIDAZOL + MICONAZOL, NITRATO 30 MG/G 20MG/G BISNAGA 40G');
INSERT INTO medicamentos values (550,'BR0326010U0015','TRIANCINOLONA, ACETONIDA 1 MG/G ORABASE 10 G');
INSERT INTO medicamentos values (551,'BR0306145U0042','VALSARTANA 160 MG COMPRIMIDO');
INSERT INTO medicamentos values (552,'BR0274438U0042','VALSARTANA 80 MG COMPRIMIDO');

UPDATE medicamentos set fa58_catmat = 'BR0268084U0046' where fa58_codigo = 91; 
UPDATE medicamentos set fa58_catmat = 'BR0442584U0011' where fa58_codigo = 420; 
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

DELETE FROM medicamentos WHERE fa58_codigo in (541,542,543,544,545,546,547,548,549,550,551,552);
UPDATE medicamentos set fa58_catmat = 'BR0268084U0042' where fa58_codigo = 91; 
UPDATE medicamentos set fa58_catmat = 'BR0305718U0011' where fa58_codigo = 420; 
SQL
        );
    }
}
