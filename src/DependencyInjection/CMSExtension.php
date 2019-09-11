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
use Rixafy\Blog\BlogFacade;
use Rixafy\Blog\BlogFactory;
use Rixafy\Blog\Category\BlogCategoryFacade;
use Rixafy\Blog\Category\BlogCategoryFactory;
use Rixafy\Blog\Post\BlogPostFacade;
use Rixafy\Blog\Post\BlogPostFactory;
use Rixafy\Blog\Publisher\BlogPublisherFacade;
use Rixafy\Blog\Publisher\BlogPublisherFactory;
use Rixafy\Blog\Tag\BlogTagFacade;
use Rixafy\Blog\Tag\BlogTagFactory;
use Rixafy\Country\CountryFacade;
use Rixafy\Country\CountryFactory;
use Rixafy\Country\CountryRepository;
use Rixafy\IpAddress\IpAddressFacade;
use Rixafy\IpAddress\IpAddressFactory;
use Rixafy\IpAddress\IpAddressRepository;
use Rixafy\IpAddress\IpAddressResolver;
use Rixafy\Language\Command\LanguageUpdateCommand;
use Rixafy\Language\LanguageFacade;
use Rixafy\Language\LanguageFactory;
use Rixafy\Language\LanguageProvider;
use Rixafy\Language\LanguageRepository;
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
		$serviceDefinition->addSetup('addPaths', [['vendor/rixafy/blog']]);
		$serviceDefinition->addSetup('addPaths', [['vendor/rixafy/routing']]);
		$serviceDefinition->addSetup('addPaths', [['vendor/rixafy/language']]);
		$serviceDefinition->addSetup('addPaths', [['vendor/rixafy/ip-address']]);
		$serviceDefinition->addSetup('addPaths', [['vendor/rixafy/currency']]);
		$serviceDefinition->addSetup('addPaths', [['vendor/rixafy/country']]);
	}

	public function loadConfiguration(): void
	{
		$this->loadLanguageExtension();
		$this->loadRoutingExtension();
		$this->loadBlogExtension();
		$this->loadCountryExtension();
		$this->loadIpAddressExtension();

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

	private function loadBlogExtension(): void
	{
		$this->getContainerBuilder()->addDefinition($this->prefix('blogFacade'))
			->setFactory(BlogFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('blogPostFacade'))
			->setFactory(BlogPostFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('blogTagFacade'))
			->setFactory(BlogTagFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('blogPublisherFacade'))
			->setFactory(BlogPublisherFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('blogCategoryFacade'))
			->setFactory(BlogCategoryFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('blogFactory'))
			->setFactory(BlogFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('blogPublisherFactory'))
			->setFactory(BlogPublisherFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('blogCategoryFactory'))
			->setFactory(BlogCategoryFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('blogTagFactory'))
			->setFactory(BlogTagFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('blogPostFactory'))
			->setFactory(BlogPostFactory::class);
	}

	private function loadLanguageExtension(): void
	{
		$this->getContainerBuilder()->addDefinition($this->prefix('languageFacade'))
			->setFactory(LanguageFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('languageRepository'))
			->setFactory(LanguageRepository::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('languageFactory'))
			->setFactory(LanguageFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('languageProvider'))
			->setFactory(LanguageProvider::class)
			->addSetup('provide', [$this->config->defaultLanguage]);

		$this->getContainerBuilder()->addDefinition($this->prefix('languageUpdateCommand'))
			->setFactory(LanguageUpdateCommand::class)
			->addTag('console.command', 'rixafy:language:update');
	}

	private function loadIpAddressExtension(): void
	{
		$this->getContainerBuilder()->addDefinition($this->prefix('ipAddressFactory'))
			->setFactory(IpAddressFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('ipAddressRepository'))
			->setFactory(IpAddressRepository::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('ipAddressFacade'))
			->setFactory(IpAddressFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('ipAddressResolver'))
			->setFactory(IpAddressResolver::class);
	}

	private function loadCountryExtension(): void
	{
		$this->getContainerBuilder()->addDefinition($this->prefix('countryFactory'))
			->setFactory(CountryFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('countryRepository'))
			->setFactory(CountryRepository::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('countryFacade'))
			->setFactory(CountryFacade::class);
	}
}
