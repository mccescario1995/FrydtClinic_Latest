<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\LoginRedirectServiceProvider::class,

    // Backpack Service Providers
    Backpack\CRUD\BackpackServiceProvider::class,
    Backpack\PermissionManager\PermissionManagerServiceProvider::class,
    Backpack\PageManager\PageManagerServiceProvider::class,
];
