<?php

namespace Hamlet\Http\Entities;

use Hamlet\Http\Template\TemplateRenderer;

abstract class AbstractTemplateEntity extends AbstractEntity
{
    public function getContent(): string
    {
        return $this->getTemplateRenderer()
                    ->render($this->getTemplateData(), $this->getTemplatePath());
    }

    abstract protected function getTemplateRenderer(): TemplateRenderer;

    abstract protected function getTemplateData();

    abstract protected function getTemplatePath(): string;
}
