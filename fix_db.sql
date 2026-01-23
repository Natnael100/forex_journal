CREATE TABLE IF NOT EXISTS analyst_applications (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    name VARCHAR NOT NULL,
    email VARCHAR NOT NULL UNIQUE,
    country VARCHAR NULL,
    timezone VARCHAR NULL,
    phone VARCHAR NULL,
    years_experience VARCHAR NOT NULL,
    certifications TEXT NULL,
    certificate_files TEXT NULL,
    methodology TEXT NULL,
    specializations TEXT NULL,
    coaching_experience VARCHAR NOT NULL,
    clients_coached VARCHAR NOT NULL,
    coaching_style VARCHAR NULL,
    track_record_url VARCHAR NULL,
    linkedin_url VARCHAR NULL,
    twitter_handle VARCHAR NULL,
    youtube_url VARCHAR NULL,
    website_url VARCHAR NULL,
    why_join TEXT NOT NULL,
    unique_value TEXT NOT NULL,
    max_clients VARCHAR NOT NULL,
    communication_methods TEXT NULL,
    status VARCHAR DEFAULT 'pending' CHECK(status IN ('pending', 'approved', 'rejected')) NOT NULL,
    rejection_reason TEXT NULL,
    reviewed_by INTEGER NULL,
    reviewed_at DATETIME NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY(reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS analyst_applications_status_index ON analyst_applications(status);
CREATE INDEX IF NOT EXISTS analyst_applications_created_at_index ON analyst_applications(created_at);

DELETE FROM migrations WHERE migration = '2026_01_10_172000_create_analyst_applications_table';
INSERT INTO migrations (migration, batch) VALUES ('2026_01_10_172000_create_analyst_applications_table', 99);
