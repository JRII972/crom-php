<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../Utils/helpers.php';
require_once __DIR__ . '/Utils/helpers.php';

use App\Database\Database;
use App\Database\Types\Utilisateur;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use PDO;
use PDOException;


class BaseController
{
    protected PDO $pdo;
    protected $viewFactory;
    private string $viewsPath = __DIR__ . '/../templates';
    private string $cachePath = __DIR__ . '/../cache';
    protected array $breadcrumbs;

    private array $scripts = [];
    private array $modules = [];
    private array $styles = [];

    public function __construct()
    {
        Carbon::setLocale('fr');
        setlocale(LC_TIME, "fr_FR.UTF-8");
        $this->breadcrumbs = [];
        $this->pdo = Database::getConnection();
        $this->setupBlade();

        $this->addScript("utils.js");
        $this->addModules("api/axiosInstance.js");
        $this->addModules("main.js");
        $this->addStyle("styles.css");
    }

    /**
     * Setup minimal Blade environment
     */
    protected function setupBlade(): void
    {
        // Ensure cache directory exists
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }

        $container = new Container;
        $filesystem = new Filesystem;
        $eventDispatcher = new Dispatcher($container);

        // Configure Blade compiler
        $bladeCompiler = new BladeCompiler($filesystem, $this->cachePath);

        // Set up engine resolver
        $resolver = new EngineResolver;
        $resolver->register('blade', function () use ($bladeCompiler) {
            return new CompilerEngine($bladeCompiler);
        });

        // Set up view finder
        $finder = new FileViewFinder($filesystem, [$this->viewsPath]);

        // Create view factory
        $this->viewFactory = new Factory($resolver, $finder, $eventDispatcher);
        $this->viewFactory->setContainer($container);

        // Share helper functions with Blade templates
        $this->viewFactory->share('route', function ($name, $parameters = []) {
            return route($name, $parameters);
        });

        $this->viewFactory->share('isSessionDisplay', function ($object) {
            return isSessionDisplay($object);
        });
    }

    /**
     * Render a Blade template with given data
     *
     * @param string $view The name of the Blade view (without .blade.php)
     * @param array $data Data to pass to the view
     * @return string Rendered HTML content
     */
    protected function render(string $view, array $data = []): string
    {
        $currentUser = null;

        // Basic authentication check
        $authUser = getAuthenticatedUser();
        if ($authUser) {
            try {
                $currentUser = new Utilisateur($authUser['sub']);
            } catch (PDOException $e) {
                clearJwtCookie();
                $redirectUrl = '/login?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? '/');
                header('Location: ' . $redirectUrl);
                exit;
            }
        }

        // Minimal global data
        $globalData = [
            'currentDateTime' => Carbon::now(),
            'currentDate' => Carbon::today(),
            'appName' => 'LBDR',
            'appVersion' => '1.0.0',
            'breadcrumbs' => $this->breadcrumbs,
            'currentYear' => date('Y'),
            'currentUser' => $currentUser,
            // Liste des scripts JavaScript par défaut
            'scripts' => $this->scripts,
            'modules' => $this->modules,
            'styles' => $this->styles,

            'baseURL' => ''
        ];

        if (isset($data['scripts']) && is_array($data['scripts'])) {
            $globalData['scripts'] = array_merge($globalData['scripts'], $data['scripts']);
            unset($data['scripts']);
        }

        if (isset($data['modules']) && is_array($data['modules'])) {
            $globalData['modules'] = array_merge($globalData['modules'], $data['modules']);
            unset($data['modules']);
        }

        $mergedData = array_merge($globalData, $data);
        return $this->viewFactory->make($view, $mergedData)->render();
    }

    /**
     * Set breadcrumbs for the current page
     *
     * @param array $breadcrumbs Array of breadcrumb items with 'titre' and optionally 'location'
     * @return void
     */
    protected function setBreadcrumbs(array $breadcrumbs): void {
        // Valider la structure des breadcrumbs
        foreach ($breadcrumbs as $index => $breadcrumb) {
            if (!is_array($breadcrumb) || !isset($breadcrumb['titre'])) {
                throw new \InvalidArgumentException  ("Breadcrumb à l'index {$index} doit être un array avec au moins 'titre'");
            }
            
            // S'assurer que les clés sont cohérentes
            if (!isset($breadcrumb['location'])) {
                $breadcrumbs[$index]['location'] = null;
            }
        }
        
        $this->breadcrumbs = $breadcrumbs;
    }
    
    /**
     * Add a single breadcrumb to the existing breadcrumbs
     *
     * @param string $titre Title of the breadcrumb
     * @param string|null $location URL for the breadcrumb (null for current page)
     * @return void
     */
    protected function addBreadcrumb(string $titre, ?string $location = null): void {
        $this->breadcrumbs[] = [
            'titre' => $titre,
            'location' => $location
        ];
    }
    
    /**
     * Clear all breadcrumbs
     *
     * @return void
     */
    protected function clearBreadcrumbs(): void {
        $this->breadcrumbs = [];
    }
    
    /**
     * Get current breadcrumbs
     *
     * @return array
     */
    protected function getBreadcrumbs(): array {
        return $this->breadcrumbs;
    }

    public function addStyle(string $style): void {
        $this->styles[] = $style;
    }
    public function addScript(string $script): void {
        $this->scripts[] = $script;
    }
    public function addModules(string $module): void {
        $this->modules[] = $module;
    }
}