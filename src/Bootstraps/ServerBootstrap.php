<?php

namespace Hamlet\Http\Bootstraps;

use Hamlet\Http\Applications\AbstractApplication;
use Hamlet\Http\Requests\DefaultRequest;
use Hamlet\Http\Writers\DefaultResponseWriter;

final class ServerBootstrap
{
    private function __construct()
    {
    }

    public static function run(AbstractApplication $application): void
    {
        $request = new DefaultRequest();
        $writer = new DefaultResponseWriter();
        $response = $application->run($request);
        $application->output($request, $response, $writer);
    }
}
