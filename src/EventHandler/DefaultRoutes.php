<?php
namespace App\EventHandler;

use App\Event\BuildRoutes;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DefaultRoutes implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            BuildRoutes::NAME => [
                ['addDefaultMiddleware', 1],
                ['addDefaultRoutes', 0],
            ],
        ];
    }

    protected $routes_path;

    public function __construct($routes_path)
    {
        $this->routes_path = $routes_path;
    }

    public function addDefaultMiddleware(BuildRoutes $event)
    {
        $app = $event->getApp();

        // Get the current user entity object and assign it into the request if it exists.
        $app->add(\App\Middleware\GetCurrentUser::class);

        // Inject the application router into the request object.
        $app->add(\App\Middleware\EnableRouter::class);

        // Inject the session manager into the request object.
        $app->add(\App\Middleware\EnableSession::class);

        // Check HTTPS setting and enforce Content Security Policy accordingly.
        $app->add(\App\Middleware\EnforceSecurity::class);

        // Remove trailing slash from all URLs when routing.
        $app->add(\App\Middleware\RemoveSlashes::class);
    }

    public function addDefaultRoutes(BuildRoutes $event)
    {
        call_user_func(include($this->routes_path), $event->getApp());
    }
}