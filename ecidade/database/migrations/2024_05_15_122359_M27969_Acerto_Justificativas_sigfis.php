<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27969AcertoJustificativasSigfis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

DROP TABLE IF EXISTS bkp_empjustificativaatojuridico_E2024050827;
     CREATE TABLE bkp_empjustificativaatojuridico_E2024050827 as
SELECT empjustificativaatojuridico.*
FROM empempenho
         JOIN orcdotacao
              on empempenho.e60_anousu = orcdotacao.o58_anousu and empempenho.e60_coddot = orcdotacao.o58_coddot
         JOIN orcelemento
              on orcdotacao.o58_codele = orcelemento.o56_codele and orcdotacao.o58_anousu = orcelemento.o56_anousu
         JOIN empjustificativaatojuridico on empempenho.e60_numemp = empjustificativaatojuridico.e175_empempenho
where o56_anousu = 2024
  and (substr(o56_elemento, 3, 1) in ('1', '2', '5', '6') OR substr(o56_elemento, 6, 2) in
                                                             ('01', '04', '03', '06', '07', '08', '10', '11', '12',
                                                              '13', '14', '15', '16', '17', '18', '19', '20', '21',
                                                              '22', '23', '24', '25', '26', '27', '28', '29', '41',
                                                              '42', '43', '45', '46', '47', '48', '49', '53', '54',
                                                              '55', '56', '57', '58', '59', '65', '70', '71', '72',
                                                              '73', '74', '75', '76', '77', '81', '91', '93', '94',
                                                              '95', '96', '97', '98'));

DELETE  FROM empjustificativaatojuridico WHERE e175_sequencial in (SELECT e175_sequencial FROM bkp_empjustificativaatojuridico_E2024050827);

DROP TABLE IF EXISTS bkp_empjustificativainstrumentoprevio_E2024050827;
CREATE TABLE bkp_empjustificativainstrumentoprevio_E2024050827 as
SELECT empjustificativainstrumentoprevio.*
FROM empempenho
         JOIN orcdotacao
              on empempenho.e60_anousu = orcdotacao.o58_anousu and empempenho.e60_coddot = orcdotacao.o58_coddot
         JOIN orcelemento
              on orcdotacao.o58_codele = orcelemento.o56_codele and orcdotacao.o58_anousu = orcelemento.o56_anousu
         JOIN empjustificativainstrumentoprevio on empempenho.e60_numemp = empjustificativainstrumentoprevio.e176_empempenho
where o56_anousu = 2024
  and (substr(o56_elemento, 3, 1) in ('1', '2', '5', '6') OR substr(o56_elemento, 6, 2) in
                                                             ('01', '04', '03', '06', '07', '08', '10', '11', '12',
                                                              '13', '14', '15', '16', '17', '18', '19', '20', '21',
                                                              '22', '23', '24', '25', '26', '27', '28', '29', '41',
                                                              '42', '43', '45', '46', '47', '48', '49', '53', '54',
                                                              '55', '56', '57', '58', '59', '65', '70', '71', '72',
                                                              '73', '74', '75', '76', '77', '81', '91', '93', '94',
                                                              '95', '96', '97', '98'));

DELETE  FROM empjustificativainstrumentoprevio WHERE e176_sequencial in (SELECT e176_sequencial FROM bkp_empjustificativainstrumentoprevio_E2024050827);

DROP TABLE IF EXISTS bkp_emptipoatojuridico_E2024050827;
CREATE TABLE bkp_emptipoatojuridico_E2024050827 as
SELECT emptipoatojuridico.*
FROM empempenho
         JOIN orcdotacao
              on empempenho.e60_anousu = orcdotacao.o58_anousu and empempenho.e60_coddot = orcdotacao.o58_coddot
         JOIN orcelemento
              on orcdotacao.o58_codele = orcelemento.o56_codele and orcdotacao.o58_anousu = orcelemento.o56_anousu
         JOIN emptipoatojuridico on empempenho.e60_numemp = emptipoatojuridico.e166_empempenho
where o56_anousu = 2024
--   and e166_tipoatojuridico = 99
  and (substr(o56_elemento, 3, 1) in ('1', '2', '5', '6') OR substr(o56_elemento, 6, 2) in
                                                             ('01', '04', '03', '06', '07', '08', '10', '11', '12',
                                                              '13', '14', '15', '16', '17', '18', '19', '20', '21',
                                                              '22', '23', '24', '25', '26', '27', '28', '29', '41',
                                                              '42', '43', '45', '46', '47', '48', '49', '53', '54',
                                                              '55', '56', '57', '58', '59', '65', '70', '71', '72',
                                                              '73', '74', '75', '76', '77', '81', '91', '93', '94',
                                                              '95', '96', '97', '98'));

DELETE FROM emptipoatojuridico WHERE e166_sequencial IN (SELECT e166_sequencial FROM bkp_emptipoatojuridico_E2024050827);

DELETE FROM empjustificativaatojuridico WHERE e175_sequencial NOT IN (
SELECT e175_sequencial
FROM emptipoatojuridico
         right join empjustificativaatojuridico on e175_empempenho = e166_empempenho
where e166_tipoatojuridico = 99);

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
       INSERT INTO empjustificativainstrumentoprevio
SELECT e176_sequencial, e176_empempenho, e176_justificativainstrumentoprevio
FROM bkp_empjustificativainstrumentoprevio_E2024050827;

INSERT INTO empjustificativaatojuridico SELECT e175_sequencial, e175_empempenho, e175_justificativaatojuridico
FROM bkp_empjustificativaatojuridico_E2024050827;

INSERT INTO emptipoatojuridico SELECT * FROM bkp_emptipoatojuridico_E2024050827;

SQL
        );
    }
}
