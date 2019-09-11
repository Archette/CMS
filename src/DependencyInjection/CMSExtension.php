<?php

declare(strict_types=1);

namespace Archette\CMS\DependencyInjection;

use Archette\CMS\Model\Website\WebsiteFacade;
use Archette\CMS\Model\Website\WebsiteFactory;
use Archette\CMS\Router\RouterFactory;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver;
use Nette\Application\IPresenterFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Rixafy\Routing\Route\Group\RouteGroupFacade;
use Rixafy\Routing\Route\Group\RouteGroupFactory;
use Rixafy\Routing\Route\Group\RouteGroupRepository;
use Rixafy\Routing\Route\RouteFacade;
use Rixafy\Routing\Route\RouteFactory;
use Rixafy\Routing\Route\RouteRepository;
use Rixafy\Routing\Route\Site\RouteSiteFacade;
use Rixafy\Routing\Route\Site\RouteSiteFactory;
use Rixafy\Routing\Route\Site\RouteSiteRepository;

class CMSExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		/** @var ServiceDefinition $serviceDefinition */
		$serviceDefinition = $this->getContainerBuilder()->getDefinitionByType(AnnotationDriver::class);
		$serviceDefinition->addSetup('addPaths', [['vendor/archette/cms']]);
	}

	public function loadConfiguration(): void
	{
		$this->loadRoutingExtension();

		$builder = $this->getContainerBuilder();

		/** @var ServiceDefinition $presenterServiceDefinition */
		$presenterServiceDefinition = $builder->getDefinitionByType(IPresenterFactory::class);

		$presenterServiceDefinition->addSetup('setMapping', [[
			'CMS' => [
				'Archette\\Module',
				'*',
				'*\*Presenter'
			]
		]]);

		$this->getContainerBuilder()->addDefinition($this->prefix('websiteFactory'))
			->setFactory(WebsiteFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('websiteFacade'))
			->setFactory(WebsiteFacade::class);

		$this->getContainerBuilder()->addDefinition('routerFactory')
			->setFactory(RouterFactory::class);

		$this->getContainerBuilder()->addDefinition('router')
			->setFactory('@routerFactory::create');
	}

	private function loadRoutingExtension(): void
	{
		$this->getContainerBuilder()->addDefinition($this->prefix('routeFacade'))
			->setFactory(RouteFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('routeRepository'))
			->setFactory(RouteRepository::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('routeFactory'))
			->setFactory(RouteFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('routeGroupFacade'))
			->setFactory(RouteGroupFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('routeGroupRepository'))
			->setFactory(RouteGroupRepository::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('routeGroupFactory'))
			->setFactory(RouteGroupFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('routeSiteFacade'))
			->setFactory(RouteSiteFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('routeSiteRepository'))
			->setFactory(RouteSiteRepository::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('routeSiteFactory'))
			->setFactory(RouteSiteFactory::class);
	}
}
