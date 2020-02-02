<?php

namespace app\router;

use app\httpSenders\ExceptionResponse;

class Router
{
    protected $routes = [];

    public function debug()
    {
        return $this->routes;
    }

    public function addRoute(string $httpMethod, string $pattern, string $class): void
    {
        if (!$this->validateRoute($class, $httpMethod)) {
            return;
        }

        $pattern = trim($pattern, '/');

        $this->routes[$httpMethod][$pattern] = ['class' => $class];

        return;
    }
    
    public function dispatch(string $httpMethod, string $uri): array
    {
        $uri = trim($uri, '/');

        if (isset($this->routes[$httpMethod][$uri])) {
            return $this->routes[$httpMethod][$uri];
        }

        if (empty($this->routes[$httpMethod])) {
            throw new ExceptionResponse('Route not found', '404');
        }

        foreach ($this->routes[$httpMethod] as $pattern => $classData) {
            $parsedPattern = $this->patternParsing($pattern);

            if (preg_match('#^' . $parsedPattern . '$#', $uri)) {
                $classData['values']['uriValues'] = $this->takeValue($pattern, $uri);

                return $classData;
            }
        }

        throw new ExceptionResponse('Route not found', '404');
    }

    protected function patternParsing(string $pattern): string
    {
        $patternExploded = explode('/', $pattern);

        $matches = [];
        $regExps = [];

        foreach ($patternExploded as $key => $value) {
            if (preg_match('#^{[a-zA-Z0-9_\-\[\]\\\+:.;]+}$#', $value)) {
                $rule = trim($value, '{}');
                $ruleExploded = explode(':', $rule);

                $matches[] = $value;
                $regExps[] = $ruleExploded[1];
            }
        }

        return str_replace($matches, $regExps, $pattern);
    }

    protected function takeValue(string $pattern, string $uri): array
    {
        $uriExploded = explode('/', $uri);
        $patternExploded = explode('/', $pattern);
        $values = [];

        foreach ($patternExploded as $key => $value) {
            if (preg_match('#^{[a-zA-Z0-9_\-\[\]\\\+:.;]+}$#', $value)) {
                $rule = trim($value, '{}');
                $ruleExploded = explode(':', $rule);

                $values[$ruleExploded[0]] = $uriExploded[$key];
            }
        }

        return $values;
    }

    protected function validateRoute(string $class, string $method): bool
    {
        if (class_exists($class) && method_exists($class, strtolower($method))) {
            return true;
        }

        return false;
    }
}