<?php
/**
 * Created by PhpStorm.
 * User: tsaricam
 * Date: 19/04/2022
 * Time: 08:33
 *
 * @since 1.12.0
 */

namespace WPCCrawler\Objects;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

/**
 * This class is a mediator between Illuminate's View component and the app. This creates a {@link Factory} that can be
 * used to compile Blade templates into HTML. Simply, use {@link view()} method to retrieve the factory. Then, call its
 * {@link Factory::make()} method with the relative path of the Blade view to render it.
 *
 * @since 1.12.0
 */
class Blade {

    /** @var string[] Array containing paths where to look for Blade files */
    public $viewPaths;

    /** @var string Location where to store cached views */
    public $cachePath;

    /** @var Container */
    protected $container;

    /** @var Factory */
    protected $instance;

    /**
     * @param string[] $viewPaths The paths to the directories that store the Blade templates
     * @param string   $cachePath The path to a directory that will be used to store the PHP files created from the
     *                            Blade templates. In other words, the directory will be used to cache files.
     */
    function __construct(array $viewPaths, string $cachePath) {
        $this->viewPaths = $viewPaths;
        $this->cachePath = $cachePath;

        $this->container = new Container();
        $this->instance = $this->createFactory();
    }

    /**
     * @return Factory The view factory that can be used to compile Blade templates into HTML
     * @since 1.12.0
     */
    public function view(): Factory {
        return $this->instance;
    }

    /*
     * HELPERS
     */

    /**
     * @return Factory The view factory that can be used to compile Blade templates into HTML
     * @since 1.12.0
     * @see https://github.com/mattstauffer/Torch/tree/master/components/view
     */
    protected function createFactory(): Factory {
        $fs = new Filesystem();
        $dispatcher = new Dispatcher($this->container);

        // Create a view factory that is capable of rendering PHP and Blade templates
        $viewResolver  = new EngineResolver();
        $bladeCompiler = new BladeCompiler($fs, $this->cachePath);

        $viewResolver->register('blade', function() use ($bladeCompiler) {
            return new CompilerEngine($bladeCompiler);
        });

        $viewFinder = new FileViewFinder($fs, $this->viewPaths);
        $viewFactory = new Factory($viewResolver, $viewFinder, $dispatcher);
        $viewFactory->setContainer($this->container);
        $this->container->instance(\Illuminate\Contracts\View\Factory::class, $viewFactory);
        $this->container->instance(BladeCompiler::class, $bladeCompiler);

        return $viewFactory;
    }

}