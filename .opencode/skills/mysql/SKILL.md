---
name: mysql
description: Use when working with MySQL 8.0 database — queries, schema design, migrations, indexing, performance, stored procedures, Docker MySQL config. Covers MySQL 8.0 specific features.
---

# MySQL 8.0 Skill

## Project Context

MySQL 8.0 running in Docker on port 3306. Database name: `store`, user: `root`, password: `root`.

## Docker Config

```yaml
# docker-compose.yml
MYSQL_ROOT_PASSWORD: root
MYSQL_DATABASE: store
# Do NOT set MYSQL_USER=root — MySQL 8.0 rejects this
```

## Connection Config

```php
// config/database.php
'mysql' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'store'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', 'root'),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
],
```

## Key MySQL 8.0 Features

- **Window Functions**: `ROW_NUMBER()`, `RANK()`, `LEAD()`, `LAG()`, `NTILE()`
- **CTEs**: `WITH cte_name AS (SELECT ...) SELECT ... FROM cte_name`
- **JSON Functions**: `JSON_EXTRACT()`, `->>'$.key'`, `JSON_ARRAYAGG()`, `JSON_OBJECTAGG()`
- **Grouping**: `GROUPING()` with `WITH ROLLUP`
- **Window Frame**: `ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW`
- **Invisible Indexes**: `ALTER TABLE t ALTER INDEX idx INVISIBLE`
- **Descending Indexes**: `CREATE INDEX idx ON t (col DESC)`
- **Role Management**: `CREATE ROLE`, `GRANT`, `SET DEFAULT ROLE`

## Common Queries

```sql
-- Pagination with total count
SELECT COUNT(*) OVER() as total, t.* FROM customers t WHERE t.balance > 0 LIMIT 200 OFFSET 0;

-- Running balance with window function
SELECT id, balance, SUM(balance) OVER (ORDER BY created_at) as running_total FROM movements;

-- Grouped aggregation with rollup
SELECT mosque_id, COUNT(*), SUM(balance) FROM customers GROUP BY mosque_id WITH ROLLUP;

-- JSON column queries
SELECT * FROM orders WHERE metadata->>'$.status' = 'pending';
```

## Performance Tips

- Use `EXPLAIN` / `EXPLAIN ANALYZE` to check query plans
- Add indexes on foreign keys and WHERE clause columns
- Use `utf8mb4_unicode_ci` collation for full Unicode support (Arabic)
- Avoid `SELECT *` — always specify columns
- Use `LIMIT` with `ORDER BY` for consistent pagination
- `DECIMAL(10,4)` for financial amounts (no floating point)

## Laravel Migration MySQL Notes

```php
$table->string('name', 100);           // VARCHAR(100)
$table->decimal('balance', 10, 4);     // DECIMAL(10,4)
$table->text('notes')->nullable();     // TEXT
$table->enum('status', ['active', 'inactive']); // ENUM
$table->json('metadata')->nullable();  // JSON
$table->timestamps();                  // created_at, updated_at
$table->softDeletes();                 // deleted_at
```
