-- Phase 6 & 7 Database Tables
-- Run this SQL script directly against your database.sqlite file

-- Create analyst_assignments table
CREATE TABLE IF NOT EXISTS analyst_assignments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    analyst_id INTEGER NOT NULL,
    trader_id INTEGER NOT NULL,
    assigned_by INTEGER NOT NULL,
    created_at TEXT,
    updated_at TEXT,
    FOREIGN KEY(analyst_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(trader_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(assigned_by) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(analyst_id, trader_id)
);

CREATE INDEX IF NOT EXISTS idx_analyst_assignments_analyst_id ON analyst_assignments(analyst_id);
CREATE INDEX IF NOT EXISTS idx_analyst_assignments_trader_id ON analyst_assignments(trader_id);

-- Create feedback table
CREATE TABLE IF NOT EXISTS feedback (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    trader_id INTEGER NOT NULL,
    analyst_id INTEGER NOT NULL,
    trade_id INTEGER,
    content TEXT NOT NULL,
    ai_suggestions TEXT,
    status TEXT DEFAULT 'draft' CHECK(status IN ('draft', 'submitted', 'locked')),
    submitted_at TEXT,
    locked_at TEXT,
    created_at TEXT,
    updated_at TEXT,
    deleted_at TEXT,
    FOREIGN KEY(trader_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(analyst_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(trade_id) REFERENCES trades(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_feedback_trader_id ON feedback(trader_id);
CREATE INDEX IF NOT EXISTS idx_feedback_analyst_id ON feedback(analyst_id);
CREATE INDEX IF NOT EXISTS idx_feedback_trade_id ON feedback(trade_id);
CREATE INDEX IF NOT EXISTS idx_feedback_status ON feedback(status);

-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    type TEXT NOT NULL,
    data TEXT NOT NULL,
    read_at TEXT,
    created_at TEXT,
    updated_at TEXT,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_read_at ON notifications(read_at);
CREATE INDEX IF NOT EXISTS idx_notifications_type ON notifications(type);
