<?php
	$db = new SQLite3($dbPath);

// Create tables
$db->exec("CREATE TABLE IF NOT EXISTS pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT UNIQUE NOT NULL,
    content TEXT NOT NULL,
    parent_id INTEGER DEFAULT NULL,
    FOREIGN KEY(parent_id) REFERENCES pages(id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS blog (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS config (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key TEXT UNIQUE NOT NULL,
    value TEXT NOT NULL
)");

$db->exec("CREATE TABLE IF NOT EXISTS contact (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    subject TEXT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS media (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL,
    filepath TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS heroes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    page_slug TEXT UNIQUE NOT NULL,
    title TEXT NOT NULL,
    subtitle TEXT,
    background_image TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS social_media (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category TEXT NOT NULL,
    name TEXT NOT NULL,
    url TEXT NOT NULL,
    logo TEXT NOT NULL
)");

$db->exec("CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
)");

$db->exec("
    INSERT OR IGNORE INTO admins (name, email, username, password)
    VALUES ('Jose Vicente Carratala', 'info@josevicentecarratala.com', 'jocarsa', 'jocarsa')
");

$db->exec("
    INSERT OR IGNORE INTO config (key, value) VALUES
        ('title', 'jocarsa | gainsboro'),
        ('logo', 'https://jocarsa.com/static/logo/jocarsa%20%7C%20gainsboro.svg'),
        ('meta_description', 'Default meta description'),
        ('meta_tags', 'default, tags'),
        ('meta_author', 'Jose Vicente Carratala'),
        ('active_theme', 'gainsboro'),
        ('footer_image', 'https://jocarsa.com/static/logo/footer-logo.svg'),
        ('analytics_user', 'defaultUser')
");

$db->exec("CREATE TABLE IF NOT EXISTS custom_css (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    active INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$defaultSocialMedia = [
    ['Generalistas', 'Facebook', 'facebook.png'],
    ['Generalistas', 'Instagram', 'instagram.png'],
    ['Generalistas', 'Twitter (X)', 'twitter.png'],
    ['Generalistas', 'TikTok', 'tiktok.png'],
    ['Generalistas', 'Snapchat', 'snapchat.png'],
    ['Profesionales y negocios', 'LinkedIn', 'linkedin.png'],
    ['Profesionales y negocios', 'Pinterest', 'pinterest.png'],
    ['Profesionales y negocios', 'GitHub', 'github.png'],
    ['Mensajería instantánea', 'WhatsApp', 'whatsapp.png'],
    ['Mensajería instantánea', 'Telegram', 'telegram.png'],
    ['Mensajería instantánea', 'Discord', 'discord.png'],
    ['Streaming y video', 'YouTube', 'youtube.png'],
    ['Streaming y video', 'Twitch', 'twitch.png'],
    ['Redes sociales emergentes o de nicho', 'Threads', 'threads.png'],
    ['Redes sociales emergentes o de nicho', 'Mastodon', 'mastodon.png'],
    ['Redes sociales emergentes o de nicho', 'BeReal', 'bereal.png']
];
?>
