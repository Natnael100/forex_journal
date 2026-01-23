-- Quick fix: Mark the duplicate migration as already run
INSERT INTO migrations (migration, batch) 
VALUES ('2025_12_24_140000_create_analyst_requests_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations));
