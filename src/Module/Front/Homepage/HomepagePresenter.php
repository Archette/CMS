<?php

declare(strict_types=1);

namespace Archette\CMS\Module\Front\Post;

use Archette\CMS\Module\Front\BaseFrontPresenter;
use Archette\CMS\Module\Front\Homepage\Event\HomepageRenderEvent;
use Rixafy\Blog\Post\BlogPostFacade;

final class HomepagePresenter extends BaseFrontPresenter
{
	/** @var BlogPostFacade @inject */
	public $blogPostFacade;

	/** @var int */
	private $page;

	/** @var int */
	private $pages;

	/** @var int */
	private $postCount;

	/** @var int */
	private $postsPerPage = 20;

	public function actionDefault(int $page = 1): void
	{
		$this->page = $page;

		if ($this->page <= 0) {
			$this->redirect('CMS:Homepage:default');
		}

		$this->postCount = $this->blogPostFacade->getCount($this->blog->getId());
		$this->pages = (int) (($this->postCount / $this->postsPerPage) + ($this->postCount % $this->postsPerPage === 0 ? 0 : 1));

		if ($this->page > $this->pages) {
			$this->redirect('CMS:Homepage:default');
		}
	}

	public function beforeRender(): void
	{
		parent::beforeRender();

		$blogPostsQueryBuilder = $this->blogPostFacade->getQueryBuilderForChunk(
			$this->blog->getId(),
			$this->postsPerPage,
			($this->page - 1) * $this->postsPerPage
		);

		$homepageRenderEvent = new HomepageRenderEvent($this->template, $blogPostsQueryBuilder);

		$this->eventDispatcher->dispatch($homepageRenderEvent);

		$this->template->blogPosts = $homepageRenderEvent->blogPostQueryBuilder->getQuery()->execute();
	}
}
