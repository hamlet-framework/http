<?php

namespace Hamlet\Http\Template;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use Mustache_Template;

class MustacheRenderer implements TemplateRenderer
{
    /** @var Mustache_Engine */
    private $engine;

    /** @var Mustache_Template[] */
    private $templates = [];

    public function __construct()
    {
        $this->engine = new Mustache_Engine([
            'template_class_prefix' => '__mustache_',
            'cache' => sys_get_temp_dir(),
            'cache_file_mode' => 0666,
            'loader' => new Mustache_Loader_FilesystemLoader('/'),
        ]);
    }

    /**
     * @param mixed $data
     * @param string $path
     * @return string
     */
    public function render($data, string $path): string
    {
        return $this->load($path)->render($data);
    }

    private function load(string $path): Mustache_Template
    {
        if (!isset($this->templates[$path])) {
            $this->templates[$path] = $this->engine->loadTemplate($path);
        }
        return $this->templates[$path];
    }
}
