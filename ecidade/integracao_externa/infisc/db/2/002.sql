DROP SEQUENCE IF EXISTS integra_infisc.integra_eventuais_sequencial_seq CASCADE;
CREATE SEQUENCE integra_infisc.integra_eventuais_sequencial_seq;
SELECT setval('integra_infisc.integra_eventuais_sequencial_seq', (SELECT max(sequencial) FROM integra_infisc.integra_eventuais));
ALTER TABLE integra_infisc.integra_eventuais ALTER sequencial SET DEFAULT nextval('integra_infisc.integra_eventuais_sequencial_seq');
