<?php

declare(strict_types=1);

namespace Archette\CMS\Model\Website;

use Rixafy\Blog\Blog;
use Rixafy\Language\Language;
use Rixafy\Routing\Route\Site\RouteSite;

class WebsiteData
{
	/** @var string */
	public $name;

	/** @var string */
	public $domainHost;

	/** @var Language */
	public $defaultLanguage;

	/** @var bool */
	public $httpsRedirect = false;

	/** @var RouteSite */
	public $routeSite;

	/** @var Blog */
	public $blog;
}
