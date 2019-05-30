<?php

namespace Hamlet\Http\Entities;

use Hamlet\Http\Template\MustacheRenderer;
use Hamlet\Http\Template\TemplateRenderer;

abstract class AbstractMustacheEntity extends AbstractTemplateEntity
{
    public function getTemplateRenderer(): TemplateRenderer
    {
        return new MustacheRenderer();
    }

    public function getMediaType(): string
    {
        return 'text/html;charset=utf-8';
    }
}
