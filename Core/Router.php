<?php

namespace Core;

class Router
{
    private array $routes = [];

    /**
     * Daftarkan Rute HTTP GET
     */
    public function get(string $path, string $controller, string $action, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $controller, $action, $middlewares);
    }

    /**
     * Daftarkan Rute HTTP POST
     */
    public function post(string $path, string $controller, string $action, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $controller, $action, $middlewares);
    }

    /**
     * Tambahkan Rute ke daftar
     */
    public function addRoute(string $method, string $path, string $controller, string $action, array $middlewares = []): void
    {
        $cleanPath = '/' . trim($path, '/');
        if ($cleanPath === '//') $cleanPath = '/';

        $this->routes[] = [
            'method'      => strtoupper($method),
            'path'        => $cleanPath,
            'controller'  => $controller,
            'action'      => $action,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Jalankan dan cocokkan rute yang diminta browser
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri    = $this->getCleanUri();

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                // Ubah format parameter dinamis {id}, {slug}, dll ke regex pattern
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route['path']);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches); // Hapus full match index 0, hanya simpan parameter URL

                    // 1. Eksekusi Middlewares / Guards
                    $this->runMiddlewares($route['middlewares']);

                    // 2. Load Controller
                    $controllerClass = $route['controller'];
                    
                    // Cek di folder app/Controllers/
                    $controllerFile = __DIR__ . '/../app/Controllers/' . $controllerClass . '.php';
                    if (!file_exists($controllerFile)) {
                        $controllerFile = __DIR__ . '/../app/controllers/' . $controllerClass . '.php';
                    }

                    if (file_exists($controllerFile)) {
                        require_once $controllerFile;
                    }

                    // Dukungan jika controller memakai namespace "App\Controllers\" atau tanpa namespace
                    $fullyQualifiedClass = class_exists($controllerClass) ? $controllerClass : "\\App\\Controllers\\" . $controllerClass;

                    if (!class_exists($fullyQualifiedClass) && !class_exists($controllerClass)) {
                        throw new \RuntimeException("Class Controller [{$controllerClass}] tidak ditemukan pada [{$controllerFile}].", 500);
                    }

                    $instance = class_exists($fullyQualifiedClass) ? new $fullyQualifiedClass() : new $controllerClass();
                    $action = $route['action'];

                    if (!method_exists($instance, $action)) {
                        throw new \RuntimeException("Method [{$action}] tidak ditemukan pada Controller [{$controllerClass}].", 500);
                    }

                    // 3. Panggil Controller dan teruskan parameter dinamis (jika ada)
                    call_user_func_array([$instance, $action], $matches);
                    return;
                }
            }
        }

        // Tampilan 404 jika rute tidak ditemukan
        ErrorHandler::renderProductionView(404, "Rute untuk [{$requestMethod}] " . htmlspecialchars($requestUri, ENT_QUOTES, 'UTF-8') . " belum terdaftar.");
        exit;
    }

    /**
     * Eksekusi middleware berantai
     */
    private function runMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                match ($middleware) {
                    'auth'         => Guard::requireLogin(),
                    'guest'        => Guard::requireGuest(),
                    'active'       => Guard::requireActiveAccount(),
                    'super_admin'  => Guard::requireRole('super_admin'),
                    'dosen'        => Guard::requireRole('dosen'),
                    'asdos'        => Guard::requireRole('asdos'),
                    'csrf'         => Guard::verifyCsrf(),
                    default        => is_callable($middleware) ? call_user_func($middleware) : null
                };
            } elseif (is_callable($middleware)) {
                call_user_func($middleware);
            } elseif (class_exists($middleware) && method_exists($middleware, 'handle')) {
                (new $middleware())->handle();
            }
        }
    }

    /**
     * Membersihkan URI dari subfolder Laragon/XAMPP dan Query String
     */
    private function getCleanUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $baseUrl = $this->getBaseUrl();

        // Buang base folder (misal: /absensi_labb) jika ada di awal URI
        if ($baseUrl !== '' && strpos($uri, $baseUrl) === 0) {
            $uri = substr($uri, strlen($baseUrl));
        }

        // Buang /public jika user masih mengakses URL lama yang mengandung /public
        if (strpos($uri, '/public') === 0) {
            $uri = substr($uri, 7);
        }

        // Normalisasi multiple slashes menjadi single slash
        $uri = preg_replace('#/{2,}#', '/', (string)$uri);

        $clean = '/' . trim($uri, '/');
        return $clean === '//' ? '/' : $clean;
    }

    /**
     * Mendapatkan base URL proyek tanpa akhiran /public
     */
    private function getBaseUrl(): string
    {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = str_replace('\\', '/', $scriptDir);

        // Hapus akhiran /public agar URL bersih
        if (str_ends_with($scriptDir, '/public')) {
            $scriptDir = substr($scriptDir, 0, -7);
        }

        return ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') ? '' : rtrim($scriptDir, '/');
    }
}
