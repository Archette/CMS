<?php

declare(strict_types=1);

namespace Archette\CMS\Module\Front\Post;

use Archette\CMS\Module\Front\BaseFrontPresenter;
use Ramsey\Uuid\Uuid;
use Rixafy\Blog\Post\BlogPostFacade;
use Rixafy\Blog\Post\Exception\BlogPostNotFoundException;

final class BlogPostPresenter extends BaseFrontPresenter
{
	/** @var BlogPostFacade @inject */
	public $blogPostFacade;

	public function actionDefault(string $id): void
	{
		$uuid = Uuid::fromString($id);
		try {
			$this->template->post = $this->blogPostFacade->get($uuid, $this->website->getBlog()->getId());

		} catch (BlogPostNotFoundException $e) {
			$this->redirect('CMS:Homepage');
		}
	}
}
