ALTER TABLE users ADD COLUMN analyst_verification_status VARCHAR DEFAULT 'pending';
ALTER TABLE users ADD COLUMN verified_at DATETIME;
ALTER TABLE users ADD COLUMN verified_by INTEGER;
ALTER TABLE users ADD COLUMN application_id INTEGER;
ALTER TABLE users ADD COLUMN specializations TEXT;
ALTER TABLE users ADD COLUMN certifications TEXT;
