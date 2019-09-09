<?php

declare(strict_types=1);

namespace Archette\CMS\Model\Website\Exception;

use Exception;
use Ramsey\Uuid\UuidInterface;

class WebsiteNotFoundException extends Exception
{
	public static function byId(UuidInterface $id): self
	{
		return new self('Website with id "' . $id . '" not found.');
	}

	public static function byDomainHost(string $domainHost): self
	{
		return new self('Website with domainHost "' . $domainHost . '" not found.');
	}
}
