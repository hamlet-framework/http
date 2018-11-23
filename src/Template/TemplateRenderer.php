<?php

namespace Hamlet\Http\Template;

interface TemplateRenderer
{
    /**
     * @param mixed $data
     * @param string $path
     * @return string
     */
    public function render($data, string $path): string;
}
