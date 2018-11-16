<?php

namespace Hamlet\Http\Entities;

use Hamlet\Http\Templating\TemplateRenderer;
use Hamlet\Http\Templating\TwigRenderer;

abstract class AbstractTwigEntity extends AbstractTemplateEntity
{
    public function getTemplateRenderer(): TemplateRenderer
    {
        return new TwigRenderer();
    }

    public function getMediaType(): string
    {
        return 'text/html;charset=UTF-8';
    }
}
