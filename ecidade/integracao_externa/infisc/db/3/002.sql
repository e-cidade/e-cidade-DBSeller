
ALTER TABLE integra_infisc.integra_config ADD COLUMN faixa_inicial_numdoc_novo integer;
ALTER TABLE integra_infisc.integra_config ADD COLUMN faixa_final_numdoc_novo integer;

UPDATE integra_infisc.integra_config SET faixa_inicial_numdoc_novo = cast(faixa_inicial_numdoc as integer);
UPDATE integra_infisc.integra_config SET faixa_final_numdoc_novo = cast(faixa_final_numdoc as integer);

ALTER TABLE integra_infisc.integra_config DROP COLUMN faixa_inicial_numdoc;
ALTER TABLE integra_infisc.integra_config DROP COLUMN faixa_final_numdoc;

ALTER TABLE integra_infisc.integra_config RENAME faixa_inicial_numdoc_novo TO faixa_inicial_numdoc;
ALTER TABLE integra_infisc.integra_config RENAME faixa_final_numdoc_novo TO faixa_final_numdoc;
