DO
$$
DECLARE
    r RECORD;
BEGIN
    -- Truncate tables in the 'sae' schema
    EXECUTE (
        SELECT string_agg('TRUNCATE TABLE ' || quote_ident(schemaname) || '.' || quote_ident(tablename) || ' CASCADE', '; ')
        FROM pg_tables
        WHERE schemaname = 'sae'
    );

    -- Reset all sequences in the 'sae' schema
    FOR r IN 
        SELECT sequence_schema, sequence_name 
        FROM information_schema.sequences 
        WHERE sequence_schema = 'sae'
    LOOP
        EXECUTE 'ALTER SEQUENCE ' || r.sequence_schema || '.' || r.sequence_name || ' RESTART WITH 1';
    END LOOP;
END;
$$;
