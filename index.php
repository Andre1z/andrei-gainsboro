<?php
/**
 * Archivo: index.php
 *
 * Descripción:
 *   Este archivo es el punto de entrada del sitio público del CMS.
 *   Se encarga de:
 *     - Inicializar la conexión a la base de datos SQLite.
 *     - Crear las tablas necesarias (pages, blog, config, contact, heroes,
 *       social_media, custom_css, etc.) automáticamente si no existen.
 *     - Insertar valores de configuración por defecto.
 *     - Recuperar la configuración almacenada.
 *     - Detectar el tema activo y el CSS personalizado.
 *     - Renderizar dinámicamente el contenido (páginas, blog, contacto, sección hero)
 *       en función del parámetro GET "page".
 *     - Generar la navegación primaria y secundaria.
 *     - Crear un sitemap.xml para la indexación del sitio.
 *     - Incluir el script de analytics.
 *
 * @package CMS-ANDREI
 */

require_once 'config.php';

// Inicializa la base de datos SQLite3
$db = new SQLite3($dbPath);
$db->busyTimeout(5000); // Espera hasta 5 segundos en caso de bloqueo

// ---------------------------------------------------------------------
// Creación de las tablas necesarias
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

$db->exec("CREATE TABLE IF NOT EXISTS custom_css (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    active INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// ---------------------------------------------------------------------
// Inserción de valores por defecto en la tabla de configuración
// ---------------------------------------------------------------------
$db->exec("
    INSERT OR IGNORE INTO config (key, value) VALUES
        ('title', 'andrei | gainsboro'),
        ('logo', 'https://andrei.com/static/logo/andrei%20|%20gainsboro.svg'),
        ('meta_description', 'Descripción por defecto del sitio'),
        ('meta_tags', 'andrei, cms, gainsboro'),
        ('meta_author', 'Andrei'),
        ('active_theme', 'gainsboro'),
        ('footer_image', 'https://andrei.com/static/logo/footer-logo.svg'),
        ('analytics_user', 'andreiUser')
");

// ---------------------------------------------------------------------
// Recuperación de la configuración almacenada
// ---------------------------------------------------------------------
$config = [];
$resultConfig = $db->query("SELECT key, value FROM config");
while ($row = $resultConfig->fetchArray(SQLITE3_ASSOC)) {
    $config[$row['key']] = $row['value'];
}

$title           = htmlspecialchars($config['title'] ?? 'andrei | gainsboro');
$logo            = htmlspecialchars($config['logo'] ?? 'default-logo.svg');
$footerImage     = htmlspecialchars($config['footer_image'] ?? 'default-footer-logo.svg');
$metaDescription = htmlspecialchars($config['meta_description'] ?? 'Descripción por defecto');
$metaTags        = htmlspecialchars($config['meta_tags'] ?? 'andrei, cms, gainsboro');
$metaAuthor      = htmlspecialchars($config['meta_author'] ?? 'Andrei');
$analyticsUser   = htmlspecialchars($config['analytics_user'] ?? 'andreiUser');

// ---------------------------------------------------------------------
// Detección de temas disponibles en la carpeta "css"
// ---------------------------------------------------------------------
$themeFiles = glob(__DIR__ . '/css/*.css');
$availableThemes = [];
if ($themeFiles) {
    foreach ($themeFiles as $themeFile) {
        $availableThemes[] = pathinfo($themeFile, PATHINFO_FILENAME);
    }
}
$activeTheme = $config['active_theme'] ?? 'gainsboro';
if (!in_array($activeTheme, $availableThemes) && count($availableThemes) > 0) {
    $activeTheme = $availableThemes[0];
}

// ---------------------------------------------------------------------
// Recuperación del CSS personalizado activo (si existe)
// ---------------------------------------------------------------------
$activeCustomCss = "";
$cssResult = $db->query("SELECT content FROM custom_css WHERE active = 1");
if ($cssResult) {
    while ($row = $cssResult->fetchArray(SQLITE3_ASSOC)) {
        $activeCustomCss .= $row['content'] . "\n";
    }
}

// ---------------------------------------------------------------------
// Función: Renderiza la navegación primaria
// ---------------------------------------------------------------------
function renderPrimaryNav($db) {
    $navHTML = "<nav class='primary-nav'>";
    
    // Verifica si existe una página con el título 'inicio'
    $stmt = $db->prepare("SELECT COUNT(*) AS count FROM pages WHERE title = 'inicio'");
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row && $row['count'] > 0) {
        $active = (isset($_GET['page']) && $_GET['page'] === 'inicio') ? "active" : "";
        $navHTML .= "<a class='$active' href='?page=inicio'>Inicio</a>";
    }
    
    // Muestra las páginas de primer nivel, excepto "inicio", "blog" y "contacto"
    $stmt = $db->prepare("SELECT * FROM pages WHERE parent_id IS NULL AND title NOT IN ('inicio','blog','contacto') ORDER BY title ASC");
    $result = $stmt->execute();
    while ($page = $result->fetchArray(SQLITE3_ASSOC)) {
         $active = (isset($_GET['page']) && $_GET['page'] === $page['title']) ? "active" : "";
         $navHTML .= "<a class='$active' href='?page=" . urlencode($page['title']) . "'>" . htmlspecialchars($page['title']) . "</a>";
    }
    
    // Enlaces estáticos para Blog y Contacto
    $active = (isset($_GET['page']) && $_GET['page'] === 'blog') ? "active" : "";
    $navHTML .= "<a class='$active' href='?page=blog'>Blog</a>";
    $active = (isset($_GET['page']) && $_GET['page'] === 'contacto') ? "active" : "";
    $navHTML .= "<a class='$active' href='?page=contacto'>Contacto</a>";
    
    $navHTML .= "</nav>";
    return $navHTML;
}

// ---------------------------------------------------------------------
// Función: Obtiene la cadena activa (IDs desde la raíz hasta la página actual)
// ---------------------------------------------------------------------
function getActiveChain($db, $pageTitle) {
    $chain = [];
    $stmt = $db->prepare("SELECT * FROM pages WHERE title = :title");
    $stmt->bindValue(':title', $pageTitle, SQLITE3_TEXT);
    $result = $stmt->execute();
    $page = $result->fetchArray(SQLITE3_ASSOC);
    if (!$page) {
        return $chain;
    }
    while ($page) {
         array_unshift($chain, $page['id']);
         if ($page['parent_id']) {
              $stmt = $db->prepare("SELECT * FROM pages WHERE id = :id");
              $stmt->bindValue(':id', $page['parent_id'], SQLITE3_INTEGER);
              $result = $stmt->execute();
              $page = $result->fetchArray(SQLITE3_ASSOC);
         } else {
              break;
         }
    }
    return $chain;
}

// ---------------------------------------------------------------------
// Función: Renderiza la subnavegación de forma recursiva
// ---------------------------------------------------------------------
function renderSubNav($db, $parentId, $activeChain) {
    $stmt = $db->prepare("SELECT * FROM pages WHERE parent_id = :parent_id ORDER BY title ASC");
    $stmt->bindValue(':parent_id', $parentId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $children = [];
    while ($child = $result->fetchArray(SQLITE3_ASSOC)) {
         $children[] = $child;
    }
    if (empty($children)) {
         return "";
    }
    $subNavHTML = "<nav class='subnav'>";
    foreach ($children as $child) {
         $active = in_array($child['id'], $activeChain) ? "active" : "";
         $subNavHTML .= "<a class='$active' href='?page=" . urlencode($child['title']) . "'>" . htmlspecialchars($child['title']) . "</a>";
    }
    $subNavHTML .= "</nav>";
    // Renderiza de forma recursiva los submenús para los hijos de la cadena activa
    foreach ($children as $child) {
         if (in_array($child['id'], $activeChain)) {
              $subNavHTML .= renderSubNav($db, $child['id'], $activeChain);
         }
    }
    return $subNavHTML;
}

// ---------------------------------------------------------------------
// Función: Obtiene y renderiza la sección "hero" de la página (si existe)
// ---------------------------------------------------------------------
function fetchHeroSection($db, $slug) {
     $stmt = $db->prepare("SELECT * FROM heroes WHERE page_slug = :slug");
     $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
     $result = $stmt->execute();
     $heroData = $result->fetchArray(SQLITE3_ASSOC);
     if (!$heroData) {
          return "";
     }
     $heroTitle = htmlspecialchars($heroData['title']);
     $heroSubtitle = htmlspecialchars($heroData['subtitle']);
     $bgImage = htmlspecialchars($heroData['background_image']);
     
     return "
     <section class='hero' style='background-image: url(\"$bgImage\");'>
         <div class='hero-content'>
              <h2>$heroTitle</h2>
              <p>$heroSubtitle</p>
         </div>
     </section>
     ";
}

// ---------------------------------------------------------------------
// Función: Renderiza la estructura completa del sitio público
// ---------------------------------------------------------------------
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
     $customCssRules
) {
     echo "<!DOCTYPE html>\n";
     echo "<html lang='en'>\n";
     echo "  <head>\n";
     echo "      <meta charset='UTF-8'>\n";
     echo "      <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
     // Cargamos el script de reCAPTCHA para que esté disponible (se usa en la sección de contacto)
     echo "      <script src='https://www.google.com/recaptcha/api.js' async defer></script>\n";
     echo "      <title>$title</title>\n";
     echo "      <meta name='description' content='$metaDescription'>\n";
     echo "      <meta name='keywords' content='$metaTags'>\n";
     echo "      <meta name='author' content='$metaAuthor'>\n";
     echo "      <link rel='stylesheet' href='css/$theme.css'>\n";
     if (!empty($customCssRules)) {
          echo "      <style>\n$customCssRules\n      </style>\n";
     }
     echo "      <link rel='icon' type='image/svg+xml' href='gainsboro.png'>\n";
     echo "  </head>\n";
     echo "  <body>\n";
     echo "      <header>\n";
     echo "          <h1>\n";
     // Se agrega style inline al enlace para remover cualquier decoración (underline)
     echo "              <a href='?page=inicio' style='text-decoration: none;'>\n";
     echo "                  <img src=\"gainsboro.png\" alt=\"Site Logo\"> $title\n";
     echo "              </a>\n";
     echo "          </h1>\n";
     echo "      </header>\n";
     echo "      $primaryNav\n";
     echo "      $subNav\n";
     if (!empty($hero)) {
          echo $hero;
     }
     echo "      <main>\n$content\n      </main>\n";
     echo "      <footer>\n";
     echo "          &copy; " . date('Y') . " <img src=\"gainsboro.png\" alt=\"Footer Logo\"> $title\n";
     echo "      </footer>\n";
     echo "      <script src=\"https://ghostwhite.jocarsa.com/analytics.js?user=$analyticsUser\"></script>\n";
     echo "  </body>\n";
     echo "</html>\n";
}

// ---------------------------------------------------------------------
// Manejo de la solicitud según el parámetro GET "page"
// ---------------------------------------------------------------------
$pageParam = $_GET['page'] ?? 'inicio';

// Obtiene la cadena activa (IDs de las páginas) para la subnavegación.
$activeChain = getActiveChain($db, $pageParam);

// Renderiza la navegación primaria.
$primaryNav = renderPrimaryNav($db);

// Renderiza la subnavegación (si existe).
$subNav = "";
if (!empty($activeChain)) {
     $subNav = renderSubNav($db, $activeChain[0], $activeChain);
}

// ---------------------------------------------------------------------
// Procesamiento de la solicitud basado en el valor de "page"
// ---------------------------------------------------------------------
if ($pageParam === 'blog') {
     // Procesa y renderiza las entradas del blog.
     $blogResult = $db->query("SELECT title, content, created_at FROM blog ORDER BY created_at DESC");
     $blogContent = "<h2>Blog</h2>\n";
     while ($entry = $blogResult->fetchArray(SQLITE3_ASSOC)) {
          $blogContent .= "<article>\n";
          $blogContent .= "  <h3>" . htmlspecialchars($entry['title']) . "</h3>\n";
          $blogContent .= "  <time>" . htmlspecialchars($entry['created_at']) . "</time>\n";
          $blogContent .= "  <div>" . $entry['content'] . "</div>\n";
          $blogContent .= "</article>\n<hr>\n";
     }
     $heroSection = fetchHeroSection($db, 'blog');
     render($heroSection, $blogContent, $primaryNav, $subNav, $activeTheme, $title, $logo, $footerImage, $metaDescription, $metaTags, $metaAuthor, $analyticsUser, $activeCustomCss);
} elseif ($pageParam === 'contacto') {
     // Procesa y renderiza el formulario de contacto.
     $contactContent = "<h2>Contacto</h2>";
     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $name    = trim($_POST['name'] ?? '');
          $email   = trim($_POST['email'] ?? '');
          $subject = trim($_POST['subject'] ?? '');
          $message = trim($_POST['message'] ?? '');
          
          // Verificar respuesta de reCAPTCHA
          $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
          $secretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe"; // Clave secreta de prueba
          $userIP = $_SERVER['REMOTE_ADDR'];
          $url = "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}&remoteip={$userIP}";
          $request = file_get_contents($url);
          $responseData = json_decode($request);
          
          if (!$responseData->success) {
               $contactContent .= "<p style='color:red;'>Por favor, verifica que eres humano.</p>";
          } elseif ($name && $email && $subject && $message) {
               $stmt = $db->prepare("INSERT INTO contact (name, email, subject, message) VALUES (:n, :e, :s, :m)");
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
     // Incluimos el widget de reCAPTCHA en el formulario con la clave de prueba
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
              <div class='g-recaptcha' data-sitekey='6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'></div>
              <br>
              <button type='submit'>Enviar</button>
         </form>";
     $heroSection = fetchHeroSection($db, 'contacto');
     render($heroSection, $contactContent, $primaryNav, $subNav, $activeTheme, $title, $logo, $footerImage, $metaDescription, $metaTags, $metaAuthor, $analyticsUser, $activeCustomCss);
} else {
     // Procesa y renderiza el contenido de una página normal.
     $stmt = $db->prepare("SELECT content FROM pages WHERE title = :title");
     $stmt->bindValue(':title', $pageParam, SQLITE3_TEXT);
     $result = $stmt->execute();
     $pageData = $result->fetchArray(SQLITE3_ASSOC);
     if ($pageData) {
          $pageContent = "<h2>" . htmlspecialchars($pageParam) . "</h2>\n<div>" . $pageData['content'] . "</div>\n";
          $heroSection = fetchHeroSection($db, $pageParam);
          render($heroSection, $pageContent, $primaryNav, $subNav, $activeTheme, $title, $logo, $footerImage, $metaDescription, $metaTags, $metaAuthor, $analyticsUser, $activeCustomCss);
     } else {
          render("", "<h2>Página No Encontrada</h2>", $primaryNav, "", $activeTheme, $title, $logo, $footerImage, $metaDescription, $metaTags, $metaAuthor, $analyticsUser, $activeCustomCss);
     }
}

// ---------------------------------------------------------------------
// Función: Genera el archivo sitemap.xml en cada carga
// ---------------------------------------------------------------------
function generateSitemap($db) {
     $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
                ? "https://"
                : "http://";
     $domain = $protocol . $_SERVER['HTTP_HOST'];
     $urls = [];
     $urls[] = ['loc' => $domain . '/?page=inicio', 'lastmod' => date('Y-m-d')];
     $urls[] = ['loc' => $domain . '/?page=blog', 'lastmod' => date('Y-m-d')];
     $urls[] = ['loc' => $domain . '/?page=contacto', 'lastmod' => date('Y-m-d')];
     $result = $db->query("SELECT title FROM pages");
     while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
          $pageEncoded = urlencode($row['title']);
          $urls[] = ['loc' => $domain . '/?page=' . $pageEncoded, 'lastmod' => date('Y-m-d')];
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
