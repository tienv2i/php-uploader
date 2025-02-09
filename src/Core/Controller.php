<?php

namespace App\Core;

use App\Http\Request;
use App\Http\Response;
/**
 * Base Controller Class
 * Automatically injects the Request object for all controllers.
 */

abstract class Controller
{
    protected Request $request;
    protected Response $response;
    protected string $controller;
    protected string $action;
    protected array $params;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->response = new Response();
        $this->controller = $request->controller;
        $this->action = $request->action;
        $this->params = $request->params;
    }
}
