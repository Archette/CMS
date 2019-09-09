<?php

declare(strict_types=1);

namespace Archette\CMS\Model\Website;

use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

class WebsiteFacade extends WebsiteRepository
{
	/** @var EntityManagerInterface */
	private $entityManager;

	/** @var WebsiteFactory */
	private $websiteFactory;

	public function __construct(
		EntityManagerInterface $entityManager,
		WebsiteFactory $websiteFactory
	) {
		parent::__construct($entityManager);
		$this->entityManager = $entityManager;
		$this->websiteFactory = $websiteFactory;
	}

	public function create(WebsiteData $websiteData): Website
	{
		$website = $this->websiteFactory->create($websiteData);

		$this->entityManager->persist($website);
		$this->entityManager->flush();

		return $website;
	}
	/**
	 * @throws Exception\WebsiteNotFoundException
	 */
	public function edit(UuidInterface $id, WebsiteData $websiteData): Website
	{
		$website = $this->get($id);

		$website->edit($websiteData);
		$this->entityManager->flush();

		return $website;
	}

	/**
	 * @throws Exception\WebsiteNotFoundException
	 */
	public function remove(UuidInterface $id): bool
	{
		$website = $this->get($id);

		$website->remove();
		$this->entityManager->flush();

		return $website->isRemoved();
	}
}
