<?php
require_once 'config.php';

// Initialize SQLite3 database
$db = new SQLite3($dbPath);

// ---------------------------------------------------------------------
// Ensure all necessary tables exist (including the pages hierarchy)
// ---------------------------------------------------------------------
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

// Insert default config values if not exists
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

// ---------------------------------------------------------------------
// Retrieve configuration values
// ---------------------------------------------------------------------
$config = [];
$configResult = $db->query("SELECT key, value FROM config");
while ($row = $configResult->fetchArray(SQLITE3_ASSOC)) {
    $config[$row['key']] = $row['value'];
}

$title           = htmlspecialchars($config['title'] ?? 'Default Title');
$logo            = htmlspecialchars($config['logo'] ?? 'default-logo.svg');
$footerImage     = htmlspecialchars($config['footer_image'] ?? 'default-footer-logo.svg');
$metaDescription = htmlspecialchars($config['meta_description'] ?? 'Default description');
$metaTags        = htmlspecialchars($config['meta_tags'] ?? 'default, tags');
$metaAuthor      = htmlspecialchars($config['meta_author'] ?? 'Default Author');
$analyticsUser   = htmlspecialchars($config['analytics_user'] ?? 'defaultUser');

// ---------------------------------------------------------------------
// Detect available themes in the css folder
// ---------------------------------------------------------------------
$themeFiles = glob(__DIR__ . '/css/*.css');
$availableThemes = [];
if ($themeFiles !== false) {
    foreach ($themeFiles as $filePath) {
        $filename = pathinfo($filePath, PATHINFO_FILENAME);
        $availableThemes[] = $filename;
    }
}
$activeTheme = $config['active_theme'] ?? 'gainsboro';
if (!in_array($activeTheme, $availableThemes) && count($availableThemes) > 0) {
    $activeTheme = $availableThemes[0];
}

// ---------------------------------------------------------------------
// Retrieve active custom CSS (allowing multiple active rulesets)
// ---------------------------------------------------------------------
$activeCustomCss = '';
$resultCustomCss = $db->query("SELECT content FROM custom_css WHERE active = 1");
if ($resultCustomCss) {
    while ($cssRow = $resultCustomCss->fetchArray(SQLITE3_ASSOC)) {
        $activeCustomCss .= $cssRow['content'] . "\n";
    }
}

// ---------------------------------------------------------------------
// Navigation functions
// ---------------------------------------------------------------------

// Render primary navigation with "Inicio" first and "Blog" and "Contacto" last.
function renderPrimaryNav($db) {
    $html = "<nav class='primary-nav'>";
    // Always show "Inicio" first.
    $activeClass = (isset($_GET['page']) && $_GET['page'] === 'inicio') ? "active" : "";
    $html .= "<a class='$activeClass' href='?page=inicio'>Inicio</a>";
    
    // Show created level-1 pages except "inicio", "blog" and "contacto"
    $stmt = $db->prepare("SELECT * FROM pages WHERE parent_id IS NULL AND title NOT IN ('inicio','blog','contacto') ORDER BY title ASC");
    $result = $stmt->execute();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
         $activeClass = (isset($_GET['page']) && $_GET['page'] === $row['title']) ? "active" : "";
         $html .= "<a class='$activeClass' href='?page=" . urlencode($row['title']) . "'>" . htmlspecialchars($row['title']) . "</a>";
    }
    
    // Append static links "Blog" and "Contacto" at the end.
    $activeClass = (isset($_GET['page']) && $_GET['page'] === 'blog') ? "active" : "";
    $html .= "<a class='$activeClass' href='?page=blog'>Blog</a>";
    $activeClass = (isset($_GET['page']) && $_GET['page'] === 'contacto') ? "active" : "";
    $html .= "<a class='$activeClass' href='?page=contacto'>Contacto</a>";
    $html .= "</nav>";
    return $html;
}

// Helper to compute the active chain (from root to current active page)
function getActiveChain($db, $activeTitle) {
    $chain = [];
    $stmt = $db->prepare("SELECT * FROM pages WHERE title = :title");
    $stmt->bindValue(':title', $activeTitle, SQLITE3_TEXT);
    $result = $stmt->execute();
    $activePage = $result->fetchArray(SQLITE3_ASSOC);
    if (!$activePage) return $chain;
    while ($activePage) {
         array_unshift($chain, $activePage['id']);
         if ($activePage['parent_id']) {
              $stmt = $db->prepare("SELECT * FROM pages WHERE id = :pid");
              $stmt->bindValue(':pid', $activePage['parent_id'], SQLITE3_INTEGER);
              $result = $stmt->execute();
              $activePage = $result->fetchArray(SQLITE3_ASSOC);
         } else {
              break;
         }
    }
    return $chain;
}

// Recursively render sub-navigation menus. Always keep the subnav open for any parent in the active chain.
function renderSubNav($db, $parentId, $activeChain) {
    $stmt = $db->prepare("SELECT * FROM pages WHERE parent_id = :parent_id ORDER BY title ASC");
    $stmt->bindValue(':parent_id', $parentId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $children = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
         $children[] = $row;
    }
    if (empty($children)) return '';
    
    $html = "<nav class='subnav'>";
    foreach ($children as $child) {
         $activeClass = in_array($child['id'], $activeChain) ? "active" : "";
         $html .= "<a class='$activeClass' href='?page=" . urlencode($child['title']) . "'>" . htmlspecialchars($child['title']) . "</a>";
    }
    $html .= "</nav>";
    
    // For each child in the active chain, render its submenu recursively.
    foreach ($children as $child) {
         if (in_array($child['id'], $activeChain)) {
              $html .= renderSubNav($db, $child['id'], $activeChain);
         }
    }
    return $html;
}

// ---------------------------------------------------------------------
// Other helper functions (hero section, final rendering)
// ---------------------------------------------------------------------

function fetchHeroSection($db, $slug) {
    $stmt = $db->prepare("SELECT * FROM heroes WHERE page_slug = :slug");
    $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
    $res = $stmt->execute();
    $hero = $res->fetchArray(SQLITE3_ASSOC);
    if (!$hero) {
        return '';
    }
    $title    = htmlspecialchars($hero['title']);
    $subtitle = htmlspecialchars($hero['subtitle']);
    $bgImage  = htmlspecialchars($hero['background_image']);
    return "
    <section class='hero' style='background-image: url(\"$bgImage\");'>
        <div class='hero-content'>
            <h2>$title</h2>
            <p>$subtitle</p>
        </div>
    </section>
    ";
}

function render(
    $hero,
    $content,
    $primaryNav,
    $subNav,
    $theme,
    $title,
    $logo,
    $footerImage,
    $metaDescription,
    $metaTags,
    $metaAuthor,
    $analyticsUser,
    $socialMediaLinks,
    $customCssRules
) {
    echo "<!DOCTYPE html>\n";
    echo "<html lang=\"en\">\n";
    echo "    <head>\n";
    echo "        <meta charset=\"UTF-8\">\n";
    echo "        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
    echo "        <title>$title</title>\n";
    echo "        <meta name=\"description\" content=\"$metaDescription\">\n";
    echo "        <meta name=\"keywords\" content=\"$metaTags\">\n";
    echo "        <meta name=\"author\" content=\"$metaAuthor\">\n";
    echo "        <link rel=\"stylesheet\" href=\"css/$theme.css\">\n";
    if (!empty($customCssRules)) {
        echo "        <style>\n";
        echo $customCssRules;
        echo "        </style>\n";
    }
    echo "        <link rel=\"icon\" type=\"image/svg+xml\" href=\"$logo\">\n";
    echo "    </head>\n";
    echo "    <body>\n";
    echo "        <header>\n";
    echo "            <h1>\n";
    echo "                <a href='?page=inicio'>\n";
    echo "                    <img src=\"$logo\" alt=\"Site Logo\"> $title\n";
    echo "                </a>\n";
    echo "            </h1>\n";
    echo "        </header>\n";
    // Output primary and sub-navigation
    echo $primaryNav;
    echo $subNav;
    if (!empty($hero)) {
        echo $hero;
    }
    echo "        <main>\n";
    echo "            $content\n";
    echo "        </main>\n";
    echo "        <footer>\n";
    echo "            &copy; " . date('Y') . " <img src=\"$footerImage\" alt=\"Footer Logo\"> $title\n";
    echo "        </footer>\n";
    echo "        <script src=\"https://ghostwhite.jocarsa.com/analytics.js?user=$analyticsUser\"></script>\n";
    echo "    </body>\n";
    echo "</html>\n";
}

// ---------------------------------------------------------------------
// Handle requests based on the GET parameter "page"
// ---------------------------------------------------------------------
$pageParam = $_GET['page'] ?? 'inicio';

// Compute active chain (an array of page IDs from root to active page)
$activeChain = getActiveChain($db, $pageParam);

// Primary nav is always rendered.
$primaryNav = renderPrimaryNav($db);
// Render sub-navigation only if there is an active chain.
$subNav = '';
if (!empty($activeChain)) {
    // Render subnav starting from the top-level active page
    $subNav = renderSubNav($db, $activeChain[0], $activeChain);
}

if ($pageParam === 'blog') {
    $result = $db->query("SELECT title, content, created_at FROM blog ORDER BY created_at DESC");
    $blogContent = "<h2>Blog</h2>\n";
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $blogContent .= "<article>\n";
        $blogContent .= "    <h3>" . htmlspecialchars($row['title']) . "</h3>\n";
        $blogContent .= "    <time>" . htmlspecialchars($row['created_at']) . "</time>\n";
        $blogContent .= "    <div>" . $row['content'] . "</div>\n";
        $blogContent .= "</article>\n<hr>\n";
    }
    $heroSection = fetchHeroSection($db, 'blog');
    render($heroSection, $blogContent, $primaryNav, $subNav, $activeTheme, $title, $logo, $footerImage, $metaDescription, $metaTags, $metaAuthor, $analyticsUser, [], $activeCustomCss);
} elseif ($pageParam === 'contacto') {
    $contactContent = "<h2>Contacto</h2>";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($name && $email && $subject && $message) {
            $stmt = $db->prepare("INSERT INTO contact (name, email, subject, message)
                                  VALUES (:n, :e, :s, :m)");
            $stmt->bindValue(':n', $name, SQLITE3_TEXT);
            $stmt->bindValue(':e', $email, SQLITE3_TEXT);
            $stmt->bindValue(':s', $subject, SQLITE3_TEXT);
            $stmt->bindValue(':m', $message, SQLITE3_TEXT);
            $stmt->execute();
            $contactContent .= "<p>¡Gracias por tu mensaje, $name! Te responderemos pronto.</p>";
        } else {
            $contactContent .= "<p style='color:red;'>Por favor, rellena todos los campos.</p>";
        }
    }
    $contactContent .= "
    <form method='post'>
        <label for='name'>Nombre Completo:</label><br>
        <input type='text' id='name' name='name' required><br><br>
        <label for='email'>Correo Electrónico:</label><br>
        <input type='email' id='email' name='email' required><br><br>
        <label for='subject'>Asunto:</label><br>
        <input type='text' id='subject' name='subject' required><br><br>
        <label for='message'>Mensaje:</label><br>
        <textarea id='message' name='message' rows='5' required></textarea><br><br>
        <button type='submit'>Enviar</button>
    </form>";
    $heroSection = fetchHeroSection($db, 'contacto');
    render($heroSection, $contactContent, $primaryNav, $subNav, $activeTheme, $title, $logo, $footerImage, $metaDescription, $metaTags, $metaAuthor, $analyticsUser, [], $activeCustomCss);
} else {
    $stmt = $db->prepare("SELECT content FROM pages WHERE title = :title");
    $stmt->bindValue(':title', $pageParam, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row) {
        $pageContent = "<h2>" . htmlspecialchars($pageParam) . "</h2>\n<div>" . $row['content'] . "</div>\n";
        $heroSection = fetchHeroSection($db, $pageParam);
        render($heroSection, $pageContent, $primaryNav, $subNav, $activeTheme, $title, $logo, $footerImage, $metaDescription, $metaTags, $metaAuthor, $analyticsUser, [], $activeCustomCss);
    } else {
        render('', "<h2>Page Not Found</h2>", $primaryNav, '', $activeTheme, $title, $logo, $footerImage, $metaDescription, $metaTags, $metaAuthor, $analyticsUser, [], $activeCustomCss);
    }
}

// ---------------------------------------------------------------------
// Generate sitemap.xml on every load (unchanged)
// ---------------------------------------------------------------------
function generateSitemap($db) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domain = $protocol . $_SERVER['HTTP_HOST'];
    $urls = [];
    $urls[] = ['loc' => $domain . '/?page=inicio', 'lastmod' => date('Y-m-d')];
    $urls[] = ['loc' => $domain . '/?page=blog', 'lastmod' => date('Y-m-d')];
    $urls[] = ['loc' => $domain . '/?page=contacto', 'lastmod' => date('Y-m-d')];
    $result = $db->query("SELECT title FROM pages");
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $pageTitle = urlencode($row['title']);
        $urls[] = ['loc' => $domain . '/?page=' . $pageTitle, 'lastmod' => date('Y-m-d')];
    }
    $result = $db->query("SELECT id, created_at FROM blog");
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $urls[] = [
            'loc' => $domain . '/?page=blog&post=' . $row['id'],
            'lastmod' => date('Y-m-d', strtotime($row['created_at']))
        ];
    }
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    $urlset = $xml->createElement('urlset');
    $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    foreach ($urls as $entry) {
        $url = $xml->createElement('url');
        $loc = $xml->createElement('loc', htmlspecialchars($entry['loc']));
        $url->appendChild($loc);
        $lastmod = $xml->createElement('lastmod', $entry['lastmod']);
        $url->appendChild($lastmod);
        $urlset->appendChild($url);
    }
    $xml->appendChild($urlset);
    $xml->save('sitemap.xml');
}
generateSitemap($db);
?>

