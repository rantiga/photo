<?php

namespace app\router;

class RouterRegistrar
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function routerRegistration(): void
    {
        $routersList = $this->getRoutersList();

        foreach ($routersList as $httpMethod => $routes) {
            foreach ($routes as $key => $routeData) {
                $this->router->addRoute($httpMethod, $routeData['pattern'], $routeData['class']);
            }
        }
    }

    protected function getRoutersList(): array
    {
        $filePath = __DIR__ . '/routerList.json';

        $file = fopen($filePath, 'r');
        $routersList = fread($file, filesize($filePath));
        fclose($file);

        $routersList = json_decode($routersList, true);

        return $routersList;
    }
}