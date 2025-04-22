<?php

use CloudCastle\Core\Console\Command\Make\MakeClass;
use CloudCastle\Core\Console\Command\Make\MakeCommand;
use CloudCastle\Core\Console\Command\Make\MakeConfig;
use CloudCastle\Core\Console\Command\Make\MakeController;
use CloudCastle\Core\Console\Command\Make\MakeFactory;
use CloudCastle\Core\Console\Command\Make\MakeFilter;
use CloudCastle\Core\Console\Command\Make\MakeMiddleware;
use CloudCastle\Core\Console\Command\Make\MakeMigrate;
use CloudCastle\Core\Console\Command\Make\MakeModel;
use CloudCastle\Core\Console\Command\Make\MakeObserver;
use CloudCastle\Core\Console\Command\Make\MakeRepository;
use CloudCastle\Core\Console\Command\Make\MakeRequest;
use CloudCastle\Core\Console\Command\Make\MakeResource;
use CloudCastle\Core\Console\Command\Make\MakeRoute;
use CloudCastle\Core\Console\Command\Make\MakeSeed;
use CloudCastle\Core\Console\Command\Make\MakeService;
use CloudCastle\Core\Console\Command\Make\MakeValidator;
use CloudCastle\Core\Console\Command\Route\RouteList;
use CloudCastle\Core\Console\Command\Schedule\ScheduleList;
use CloudCastle\Core\Console\Command\Schedule\ScheduleRun;

return [
    'make:class' => MakeClass::class,
    'make:request' => MakeRequest::class,
    'make:resource' => MakeResource::class,
    'make:service' => MakeService::class,
    'make:repository' => MakeRepository::class,
    'make:model' => MakeModel::class,
    'make:controller' => MakeController::class,
    'make:command' => MakeCommand::class,
    'make:observer' => MakeObserver::class,
    'make:config' => MakeConfig::class,
    'make:migrate' => MakeMigrate::class,
    'make:seed' => MakeSeed::class,
    'make:factory' => MakeFactory::class,
    'make:validator' => MakeValidator::class,
    'make:middleware' => MakeMiddleware::class,
    'make:route' => MakeRoute::class,
    'make:filter' => MakeFilter::class,
    'route:list' => RouteList::class,
    'schedule:run' => ScheduleRun::class,
    'schedule:list' => ScheduleList::class,
];