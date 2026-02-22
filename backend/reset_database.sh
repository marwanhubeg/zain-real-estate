#!/bin/bash
echo "🔄 جاري تنظيف قاعدة البيانات..."

# الدخول إلى SQLite وتنفيذ الأوامر
sqlite3 database/database.sqlite <<SQL
-- حذف جميع الجداول
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS failed_jobs;
DROP TABLE IF EXISTS job_batches;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS migrations;
DROP TABLE IF EXISTS model_has_permissions;
DROP TABLE IF EXISTS model_has_roles;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS role_has_permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS telescope_entries;
DROP TABLE IF EXISTS telescope_entries_tags;
DROP TABLE IF EXISTS telescope_monitoring;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS locations;
DROP TABLE IF EXISTS amenities;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS personal_access_tokens;

-- التحقق من الحذف
SELECT '✅ تم حذف جميع الجداول' as result;
.tables
SQL

echo "✅ تم تنظيف قاعدة البيانات بنجاح"
echo "🚀 جاري تشغيل الترحيلات..."
php artisan migrate --seed
