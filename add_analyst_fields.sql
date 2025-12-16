-- Add analyst-specific fields to users table
ALTER TABLE users ADD COLUMN years_of_experience INTEGER;
ALTER TABLE users ADD COLUMN analysis_specialization VARCHAR(255);
ALTER TABLE users ADD COLUMN psychology_focus_areas TEXT;
ALTER TABLE users ADD COLUMN feedback_style VARCHAR(255);
ALTER TABLE users ADD COLUMN max_traders_assigned INTEGER DEFAULT 5;
