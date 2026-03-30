-- Admin role and relations updates
-- Adds companies, company-user (student) assignments, and batch tracking.

-- 1. Update users table with role for Admin
ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) DEFAULT 'student';

-- 2. Create Companies table
CREATE TABLE IF NOT EXISTS companies (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    batch VARCHAR(100) NOT NULL, -- e.g., "Batch 2025-2025"
    address TEXT NULL,
    contact_person VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

-- 3. Add company_id to users to assign students to companies
ALTER TABLE users ADD COLUMN IF NOT EXISTS company_id INTEGER NULL;
ALTER TABLE users ADD CONSTRAINT fk_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL;

-- 4. (Optional) Create a specific table if many-to-many is needed, 
-- but since the request implies "assign student" (likely 1:1 with a company/batch), 
-- simple foreign key on users is easier to manage for now.

-- Seed an admin if none exist (you'll need to set your own password hash)
-- INSERT INTO users (name, email, password, role, created_at, updated_at) 
-- VALUES ('Admin User', 'admin@example.com', '$2y$12$...', 'admin', NOW(), NOW());
