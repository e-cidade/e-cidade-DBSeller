<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27969AlteracoesAtoJurico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL


ALTER TABLE justificativaatojuridico ALTER COLUMN e173_descricao TYPE VARCHAR(255);

UPDATE justificativaatojuridico
SET e173_descricao = 'Dispensa de Licitação em razão de valor (art. 95, I, da ei nº 14.133/2021)'
WHERE e173_sequencial = 1;

UPDATE justificativaatojuridico
SET e173_descricao = 'Compra com integra imediata e integral dos bens adquiridos e dos quais não resultem obrigações futuras, inclusive quanto a assistência técnica, independentemente de seu valor (art. 95, II, da Lei nº 14.133/2021).'
WHERE e173_sequencial = 2;

UPDATE justificativaatojuridico
SET e173_descricao = 'Concessionária de serviços públicos (água, energia elétrica, etc.)'
WHERE e173_sequencial = 3;

UPDATE justificativaatojuridico
SET e173_descricao = 'Taxas, custas, tributos ou emolumentos devidos a outros entes da federação'
WHERE e173_sequencial = 5;

INSERT INTO justificativaatojuridico (e173_sequencial, e173_descricao, e173_ativo) VALUES
  (8, 'Pequenas compras ou prestação de serviços de pronto pagamento, assim entendidos aqueles de valor não superior a R$ 10.000,00 (art. 95, §2º da Lei 14.133/2021).', true);

INSERT INTO justificativaatojuridico (e173_sequencial, e173_descricao, e173_ativo) VALUES
  (9, 'Aluguel social pago diretamente ao beneficiário.', true);

INSERT INTO justificativaatojuridico (e173_sequencial, e173_descricao, e173_ativo) VALUES
  (10, 'Pagamento a Estagiários.', true);

INSERT INTO justificativaatojuridico (e173_sequencial, e173_descricao, e173_ativo) VALUES
  (11, 'Jetons - Remuneração por participação em reuniões ou sessões de conselhos.', true);

INSERT INTO justificativaatojuridico (e173_sequencial, e173_descricao, e173_ativo) VALUES
  (12, 'Honorários advocatícios e ônus de sucumbência.', true);



UPDATE justificativainstrumentoprevio
SET e174_descricao = 'Concessionárias de serviços públicos'
WHERE e174_sequencial = 1;

UPDATE justificativainstrumentoprevio
SET e174_descricao = 'Tarifas e obrigações bancárias'
WHERE e174_sequencial = 2;

UPDATE justificativainstrumentoprevio
SET e174_descricao = 'Taxas, custas, tributos e emolumentos'
WHERE e174_sequencial = 3;

UPDATE justificativainstrumentoprevio
SET e174_descricao = 'Aluguel social pago diretamente ao beneficiário'
WHERE e174_sequencial = 5;

INSERT INTO justificativainstrumentoprevio (e174_sequencial, e174_descricao, e174_ativo)
VALUES (6, 'Pagamento a Estagiários', true);

INSERT INTO justificativainstrumentoprevio (e174_sequencial, e174_descricao, e174_ativo)
VALUES (7, 'Jetons - Remuneração por Participação em reuniões ou sessões de conselho', true);

INSERT INTO justificativainstrumentoprevio (e174_sequencial, e174_descricao, e174_ativo)
VALUES (8, 'Honorários advocatícios e ônus de sucumbência', true);
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

ALTER TABLE justificativaatojuridico ALTER COLUMN e173_descricao TYPE VARCHAR(120);

UPDATE justificativaatojuridico
SET e173_descricao = 'Valor inferior ao previsto para tomada de preços'
WHERE e173_sequencial = 1;

UPDATE justificativaatojuridico
SET e173_descricao = 'Compra com entrega imediata e integral, não resultando em obrigações futuras'
WHERE e173_sequencial = 2;

UPDATE justificativaatojuridico
SET e173_descricao = 'Concessionárias'
WHERE e173_sequencial = 3;

UPDATE justificativaatojuridico
SET e173_descricao = 'Taxas,custas,tributos ou emolumentos devidos a outros entes'
WHERE e173_sequencial = 5;

DELETE FROM justificativaatojuridico WHERE e173_sequencial IN (8,9,10,11,12);


UPDATE justificativainstrumentoprevio
SET e174_descricao = 'Concessionárias'
WHERE e174_sequencial = 1;

UPDATE justificativainstrumentoprevio
SET e174_descricao = 'Tarifas e obrigações'
WHERE e174_sequencial = 2;

UPDATE justificativainstrumentoprevio
SET e174_descricao = 'Taxas,custas,tributos ou emolumentos devidos a outros entes'
WHERE e174_sequencial = 3;

UPDATE justificativainstrumentoprevio
SET e174_descricao = 'Aluguel social pago diretamente ao beneficiário'
WHERE e174_sequencial = 5;

DELETE FROM justificativainstrumentoprevio WHERE e173_sequencial IN (6,7,8);
SQL
        );
    }
}
