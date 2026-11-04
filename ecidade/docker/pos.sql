-- pos.sql
SELECT public.fc_set_pg_search_path();
GRANT ALL ON SCHEMA plugins TO plugin;
SELECT fc_grant_revoke('grant', 'plugin', 'select', '%', '%');
VACUUM VERBOSE ANALYZE;