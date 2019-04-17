<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'console.command.public_alias.AmoCrm\Command\DealBasicToTargetCommand' shared autowired service.

include_once $this->targetDirs[3].'/vendor/symfony/console/Command/Command.php';
include_once $this->targetDirs[3].'/src/Command/AbstractCommands.php';
include_once $this->targetDirs[3].'/src/Command/DealBasicToTargetCommand.php';

return $this->services['console.command.public_alias.AmoCrm\Command\DealBasicToTargetCommand'] = new \AmoCrm\Command\DealBasicToTargetCommand(($this->privates['AmoCrm\Request\AuthRequest'] ?? $this->load('getAuthRequestService.php')), ($this->privates['AmoCrm\Request\DealRequest'] ?? $this->load('getDealRequestService.php')), ($this->privates['AmoCrm\Request\FunnelRequest'] ?? $this->load('getFunnelRequestService.php')), ($this->privates['monolog.logger'] ?? $this->load('getMonolog_LoggerService.php')));
