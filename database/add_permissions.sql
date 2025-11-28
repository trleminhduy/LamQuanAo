INSERT INTO `permissions` (`name`, `created_at`, `updated_at`) VALUES
('manage_variants', NOW(), NOW()),
('manage_suppliers', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);
