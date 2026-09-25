<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];

    public function get(string $path, $handler, ?string $name = null): void
    {
        $this->addRoute('GET', $path, $handler, $name);
    }

    public function post(string $path, $handler, ?string $name = null): void
    {
        $this->addRoute('POST', $path, $handler, $name);
    }

    private function addRoute(string $method, string $path, $handler, ?string $name): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => '/' . trim($path, '/'),
            'handler' => $handler,
            'name' => $name,
        ];

        if ($name) {
            $this->namedRoutes[$name] = '/' . trim($path, '/');
        }
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = '/' . trim($request->getPath(), '/');

        // Handle bare root '/'
        if ($path === '/' || $path === '') {
            $currentLocale = I18n::getLocale();
            $response = new Response();
            $response->redirect('/' . $currentLocale, 302);
            return $response;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->compilePattern($route['path']);
            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, function ($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                return $this->invokeHandler($route['handler'], $params, $request);
            }
        }

        // 404 Not Found
        return $this->renderNotFound();
    }

    private function compilePattern(string $routePath): string
    {
        // Replace {param} with named regex capture group
        $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $routePath);
        return '#^' . $pattern . '$#u';
    }

    private function invokeHandler($handler, array $params, Request $request): Response
    {
        $args = array_merge([$request], array_values($params));

        if (is_callable($handler)) {
            $result = call_user_func_array($handler, $args);
        } elseif (is_string($handler) && str_contains($handler, '@')) {
            [$controllerName, $action] = explode('@', $handler);
            $fullControllerClass = 'App\\Controllers\\' . $controllerName;

            if (!class_exists($fullControllerClass)) {
                throw new \RuntimeException("Controller not found: {$fullControllerClass}");
            }

            $controller = new $fullControllerClass();
            if (!method_exists($controller, $action)) {
                throw new \RuntimeException("Action {$action} not found on controller {$fullControllerClass}");
            }

            $result = call_user_func_array([$controller, $action], $args);
        } else {
            throw new \RuntimeException("Invalid route handler format");
        }

        if ($result instanceof Response) {
            return $result;
        }

        if (is_string($result)) {
            return new Response($result);
        }

        if (is_array($result)) {
            return (new Response())->json($result);
        }

        return new Response('', 204);
    }

    private function renderNotFound(): Response
    {
        $locale = I18n::getLocale();
        $isBn = $locale === 'bn';

        $title = $isBn ? 'পৃষ্ঠাটি পাওয়া যায়নি' : 'Page Not Found';
        $desc = $isBn 
            ? 'আপনি যে পৃষ্ঠাটি খুঁজছেন তা স্থানান্তরিত বা মুছে ফেলা হয়েছে।' 
            : 'The page you are looking for may have been moved or removed.';
        $homeText = $isBn ? 'প্রচ্ছদে ফিরে যান' : 'Return to Home';

        $html = View::render('errors/404', [
            'metaTitle' => $title . ' | ' . config('app.short_name', 'SPS'),
            'title' => $title,
            'description' => $desc,
            'homeText' => $homeText,
        ], 'main');

        $html->setStatusCode(404);
        return $html;
    }
}
