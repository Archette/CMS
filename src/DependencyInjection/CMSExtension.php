<?php

declare(strict_types=1);

namespace Archette\Blog;

use Archette\CMS\Model\Website\WebsiteFacade;
use Archette\CMS\Model\Website\WebsiteFactory;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver;
use Nette\Application\IPresenterFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;

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
	}
}
