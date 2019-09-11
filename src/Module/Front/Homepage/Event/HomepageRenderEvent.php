<?php

declare(strict_types=1);

namespace Archette\CMS\Module\Front\Homepage\Event;

use Doctrine\ORM\QueryBuilder;

class HomepageRenderEvent
{
	/** @var object */
	public $template;

	/** @var QueryBuilder */
	public $blogPostQueryBuilder;

	public function __construct(object $template, QueryBuilder $blogPostsQueryBuilder)
	{
		$this->template = $template;
		$this->blogPostQueryBuilder = $blogPostsQueryBuilder;
	}
}
