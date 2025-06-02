<?php
// Base controller with Blade template engine setup
// filepath: /var/www/html/App/controllers/BaseController.php

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

abstract class BaseController {
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
        $this->setupPaths();
        $this->setupTemplateEngine();
    }
    
    /**
     * Setup template and cache paths
     */
    protected function setupPaths() {
        $this->viewsPath = __DIR__ . '/../templates';
        $this->cachePath = __DIR__ . '/../../public/cache';
        
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
        return $this->viewFactory->make($view, $data)->render();
    }
    
    /**
     * Add a custom Blade directive
     *
     * @param string $name Directive name
     * @param callable $handler Directive handler
     */
    protected function addDirective(string $name, callable $handler): void {
        $blade = $this->viewFactory->getEngineResolver()->resolve('blade')->getCompiler();
        $blade->directive($name, $handler);
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
}
