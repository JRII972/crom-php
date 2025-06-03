<?php
// Base controller with Blade template engine setup
// filepath: /var/www/html/App/controllers/BaseController.php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Carbon\Carbon;

abstract class BaseController {
    protected PDO $pdo;
    /**
     * @var Factory View factory instance
     */
    protected $viewFactory;
    
    /**
     * @var string Path to view templates
     */
    protected $viewsPath;
    
    /**
     * @var string Path to compiled templates cache
     */
    protected $cachePath;
    
    /**
     * Initialize the controller with Blade template engine
     */
    public function __construct() {
        // Configuration globale de Carbon pour utiliser le français
        Carbon::setLocale('fr');        
        setlocale(LC_TIME, "fr_FR.UTF-8");
        $this->setupPaths();
        $this->setupTemplateEngine();
        $this->setupHelpers();

        $this->pdo = Database::getConnection();
    }
    
    /**
     * Setup template and cache paths
     */
    protected function setupPaths() {
        $this->viewsPath = __DIR__ . '/../templates';
        $this->cachePath = __DIR__ . '/../cache';
        
        // Ensure cache directory exists and is writable
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }
    
    /**
     * Setup the Blade template engine
     */
    protected function setupTemplateEngine() {
        // Create necessary components
        $filesystem = new Filesystem();
        $eventDispatcher = new Dispatcher(new Container);
        
        // Create the engine resolver
        $resolver = new EngineResolver();
        
        // Register PHP engine
        $resolver->register('php', function () use ($filesystem) {
            return new PhpEngine($filesystem);
        });
        
        // Register Blade engine
        $resolver->register('blade', function () use ($filesystem) {
            $compiler = new BladeCompiler($filesystem, $this->cachePath);
            return new CompilerEngine($compiler, $filesystem);
        });
        
        // Create the view finder
        $finder = new FileViewFinder($filesystem, [$this->viewsPath]);
        
        // Create the view factory
        $this->viewFactory = new Factory($resolver, $finder, $eventDispatcher);
    }
    
    /**
     * Render a view with the given data
     *
     * @param string $view View name
     * @param array $data Data to pass to the view
     * @return string Rendered HTML
     */
    protected function render(string $view, array $data = []): string {
        
        // Ajout de variables globales disponibles dans toutes les vues
        $globalData = [
            'currentDateTime' => Carbon::now(),
            'currentDate' => Carbon::today(),
            'appName' => 'LBDR',
            'appVersion' => '1.0.0',
            'currentYear' => date('Y'),
            // Liste des scripts JavaScript par défaut
            'scripts' => [
                'utils.js',
                'main.js'
            ]
        ];
        
        // Fusion des données spécifiques à la vue avec les données globales
        // Si des scripts supplémentaires sont fournis, les ajouter aux scripts par défaut
        if (isset($data['scripts']) && is_array($data['scripts'])) {
            $globalData['scripts'] = array_merge($globalData['scripts'], $data['scripts']);
            unset($data['scripts']); // Éviter la duplication
        }
        
        // Les données spécifiques ont priorité en cas de conflit de noms
        $mergedData = array_merge($globalData, $data);
        
        return $this->viewFactory->make($view, $mergedData)->render();
    }

    /**
     * Add a custom Blade directive
     *
     * @param string $name Directive name
     * @param callable $handler Directive handler
     */
    protected function addDirective(string $name, callable $handler): void {
        // Note: Cette méthode pourrait nécessiter d'être adaptée selon l'implémentation exacte
        if (method_exists($this->viewFactory->getEngineResolver()->resolve('blade'), 'getCompiler')) {
            $blade = $this->viewFactory->getEngineResolver()->resolve('blade')->getCompiler();
            $blade->directive($name, $handler);
        }
    }
    
    /**
     * Add a shared variable available to all views
     *
     * @param string $key Variable name
     * @param mixed $value Variable value
     */
    protected function share(string $key, $value): void {
        $this->viewFactory->share($key, $value);
    }
    
    
    /**
     * Setup helper functions for Blade
     */
    protected function setupHelpers(): void {
        // Ajout de la fonction route() pour générer des URLs
        $this->viewFactory->share('route', function($name, $parameters = []) {
            // Liste des routes disponibles avec leurs URLs correspondantes
            $routes = [
                'parties.show' => '/parties.php?id=%d',
                'parties.create' => '/parties.php?action=create',
                'parties.edit' => '/parties.php?action=edit&id=%d',
                'receipts.show' => '/receipts.php?id=%d',
                'payments.pay' => '/payments.php?action=pay&id=%d',
                'payments.renew' => '/payments.php?action=renew'
            ];
            
            // Si la route n'existe pas, retourner #
            if (!isset($routes[$name])) {
                return '#';
            }
            
            // Si des paramètres sont fournis, les insérer dans l'URL
            $url = $routes[$name];
            if (!empty($parameters) && is_array($parameters)) {
                // Pour simplifier, nous supposons que les paramètres sont dans l'ordre des %d dans l'URL
                $args = array_values($parameters);
                $url = vsprintf($url, $args);
            }
            
            return $url;
        });
    }
}
