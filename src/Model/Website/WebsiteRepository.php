<?php

declare(strict_types=1);

namespace Archette\CMS\Model\Website;

use Archette\CMS\Model\Website\Exception\WebsiteNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

abstract class WebsiteRepository
{
	/** @var EntityManagerInterface */
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	private function getRepository()
	{
		return $this->entityManager->getRepository(Website::class);
	}

	/**
	 * @throws WebsiteNotFoundException
	 */
	public function get(UuidInterface $id): Website
	{
		/** @var Website $website */
		$website = $this->getRepository()->find($id);

		if ($website === null) {
			throw WebsiteNotFoundException::byId($id);
		}

		return $website;
	}

	/**
	 * @throws WebsiteNotFoundException
	 */
	public function getByDomainHost(string $domainHost): Website
	{
		/** @var Website $website */
		$website = $this->getRepository()->findOneBy([
			'domainHost' => $domainHost
		]);

		if ($website === null) {
			throw WebsiteNotFoundException::byDomainHost($domainHost);
		}

		return $website;
	}

	/**
	 * @return Website[]
	 */
	public function getAll(): array
	{
		return $this->getRepository()->findAll();
	}
}
