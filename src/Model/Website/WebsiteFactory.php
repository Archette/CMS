<?php

declare(strict_types=1);

namespace Archette\CMS\Model\Website;

use Ramsey\Uuid\Uuid;

class WebsiteFactory
{
	public function create(WebsiteData $data): Website
	{
		return new Website(Uuid::uuid4(), $data);
	}
}
