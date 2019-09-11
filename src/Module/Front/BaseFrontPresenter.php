<?php

declare(strict_types=1);

namespace Archette\CMS\Module\Front;

use Archette\CMS\Model\Website\Website;
use Archette\CMS\Model\Website\WebsiteFacade;
use Nette\Application\UI\Presenter;
use Rixafy\Blog\Blog;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class BaseFrontPresenter extends Presenter
{
	/** @var Website */
	protected $website;

	/** @var Blog */
	protected $blog;

	/** @var WebsiteFacade @inject */
	public $websiteFacade;

	/** @var EventDispatcherInterface @inject */
	public $eventDispatcher;

	public function startup()
	{
		parent::startup();
		$this->website = $this->websiteFacade->getByDomainHost($_SERVER['SERVER_NAME']);
		$this->blog = $this->website->getBlog();
	}

	public function beforeRender(): void
	{
		bdump('test', $this->context->parameters['appDir']);
		$this->setLayout(__DIR__ . '/Templates/@layout.latte');
		$this->getTemplate()->setFile(__DIR__ . '/Templates/' . str_replace('Front:', '', $this->getName()) . '/' . $this->getAction() .'.latte');
	}
}
