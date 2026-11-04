DROP SEQUENCE IF EXISTS integra_infisc.integra_debitos_sequencial_seq CASCADE;
CREATE SEQUENCE integra_infisc.integra_debitos_sequencial_seq;
SELECT setval('integra_infisc.integra_debitos_sequencial_seq', (SELECT max(sequencial) FROM integra_infisc.integra_debitos));
ALTER TABLE integra_infisc.integra_debitos ALTER sequencial SET DEFAULT nextval('integra_infisc.integra_debitos_sequencial_seq');

DROP SEQUENCE IF EXISTS integra_infisc.integra_debitos_movimentos_baixa_sequencial_seq CASCADE;
CREATE SEQUENCE integra_infisc.integra_debitos_movimentos_baixa_sequencial_seq;
SELECT setval('integra_infisc.integra_debitos_movimentos_baixa_sequencial_seq', (SELECT max(sequencial) FROM integra_infisc.integra_debitos_movimentos_baixa));
ALTER TABLE integra_infisc.integra_debitos_movimentos_baixa ALTER sequencial SET DEFAULT nextval('integra_infisc.integra_debitos_movimentos_baixa_sequencial_seq');
