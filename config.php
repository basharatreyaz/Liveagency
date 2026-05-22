﻿﻿﻿﻿﻿<?php

define('DB_FILE', __DIR__ . '/cms/data/wpsitedoctors.db');

function ensure_posts_schema(PDO $pdo) {
    $rows = $pdo->query('PRAGMA table_info(posts)')->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        return;
    }

    $columns = array_map('strtolower', array_column($rows, 'name'));

    if (!in_array('featured_image', $columns, true)) {
        $pdo->exec("ALTER TABLE posts ADD COLUMN featured_image TEXT DEFAULT ''");
    }

    if (!in_array('category', $columns, true)) {
        $pdo->exec("ALTER TABLE posts ADD COLUMN category TEXT DEFAULT 'General'");
    }

    if (!in_array('author', $columns, true)) {
        $pdo->exec("ALTER TABLE posts ADD COLUMN author TEXT DEFAULT 'Admin'");
    }

    if (!in_array('status', $columns, true)) {
        $pdo->exec("ALTER TABLE posts ADD COLUMN status TEXT DEFAULT 'published'");
    }

    if (!in_array('meta_title', $columns, true)) {
        $pdo->exec("ALTER TABLE posts ADD COLUMN meta_title TEXT DEFAULT ''");
    }

    if (!in_array('meta_description', $columns, true)) {
        $pdo->exec("ALTER TABLE posts ADD COLUMN meta_description TEXT DEFAULT ''");
    }
}

function ensure_manager_schema(PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS authors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE
    )");

    $rows = $pdo->query('PRAGMA table_info(authors)')->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $columns = array_map('strtolower', array_column($rows, 'name'));
        if (!in_array('email', $columns, true)) {
            $pdo->exec("ALTER TABLE authors ADD COLUMN email TEXT DEFAULT ''");
        }
    }
}

function ensure_seo_schema(PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS seo_meta (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        page_slug TEXT NOT NULL UNIQUE,
        title TEXT DEFAULT '',
        description TEXT DEFAULT ''
    )");
}

function ensure_team_schema(PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS team_members (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        title TEXT NOT NULL,
        experience TEXT DEFAULT '',
        image TEXT DEFAULT '',
        created_at DATETIME DEFAULT (datetime('now'))
    )");

    $rows = $pdo->query('PRAGMA table_info(team_members)')->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $columns = array_map('strtolower', array_column($rows, 'name'));
        if (!in_array('email', $columns, true)) {
            $pdo->exec("ALTER TABLE team_members ADD COLUMN email TEXT DEFAULT ''");
        }
        if (!in_array('details', $columns, true)) {
            $pdo->exec("ALTER TABLE team_members ADD COLUMN details TEXT DEFAULT ''");
        }
        if (!in_array('linkedin', $columns, true)) {
            $pdo->exec("ALTER TABLE team_members ADD COLUMN linkedin TEXT DEFAULT ''");
        }
        if (!in_array('instagram', $columns, true)) {
            $pdo->exec("ALTER TABLE team_members ADD COLUMN instagram TEXT DEFAULT ''");
        }
        if (!in_array('facebook', $columns, true)) {
            $pdo->exec("ALTER TABLE team_members ADD COLUMN facebook TEXT DEFAULT ''");
        }
    }
}

function get_pdo() {
    if (!file_exists(DB_FILE)) {
        throw new Exception('SQLite database file not found at ' . DB_FILE);
    }

    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    ensure_posts_schema($pdo);
    ensure_manager_schema($pdo);
    ensure_team_schema($pdo);
    ensure_seo_schema($pdo);
    return $pdo;
}

function html_escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function ensure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function is_logged_in() {
    ensure_session();
    return (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true);
}

function is_admin_user() {
    static $is_admin = null;
    if ($is_admin !== null) return $is_admin;
    
    ensure_session();
    if (empty($_SESSION['admin_id'])) return $is_admin = false;
    try {
        $pdo = get_pdo();
        $stmt_root = $pdo->query('SELECT id FROM users ORDER BY id ASC LIMIT 1');
        $root_id = $stmt_root->fetchColumn();
        return $is_admin = ($_SESSION['admin_id'] == $root_id);
    } catch (Exception $e) {
        return $is_admin = false;
    }
}

function get_csrf_token() {
    ensure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    ensure_session();
    return hash_equals($_SESSION['csrf_token'] ?? '', (string)$token);
}

function require_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($token)) {
            die('Security Error: CSRF token validation failed. Please return to the previous page and try again.');
        }
    }
}

function require_admin() {
    if (!is_admin_user()) {
        cms_redirect('admin-dashboard.php');
    }
}

function get_categories() {
    $pdo = get_pdo();
    return $pdo->query('SELECT id, name FROM categories ORDER BY name COLLATE NOCASE')->fetchAll();
}

function get_authors() {
    $pdo = get_pdo();
    return $pdo->query('SELECT id, name FROM authors ORDER BY name COLLATE NOCASE')->fetchAll();
}

function cms_redirect($location) {
    header('Location: ' . $location);
    exit;
}
