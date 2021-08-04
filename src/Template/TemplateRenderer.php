<?php

namespace Hamlet\Http\Template;

interface TemplateRenderer
{
    public function render(mixed $data, string $path): string;
}
