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

    /**
     * @param AbstractApplication $application
     * @return void
     */
    public static function run(AbstractApplication $application)
    {
        $request = new DefaultRequest();
        $writer = new DefaultResponseWriter();
        $response = $application->run($request);
        $application->output($request, $response, $writer);
    }
}
