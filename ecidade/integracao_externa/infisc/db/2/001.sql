-- Deve permitir ao usuario "integraiss" realizar insert/delete na tabela "integra_cadastro"
SELECT fc_grant('integrainfisc', 'delete,insert', 'public', 'integra_infisc.integra_eventuais');
