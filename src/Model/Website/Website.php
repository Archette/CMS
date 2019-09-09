<?php

declare(strict_types=1);

namespace Archette\CMS\Model\Website;

use Ramsey\Uuid\UuidInterface;
use Rixafy\Blog\Blog;
use Doctrine\ORM\Mapping as ORM;
use Rixafy\DoctrineTraits\DateTimeTrait;
use Rixafy\DoctrineTraits\RemovableTrait;
use Rixafy\Language\Language;
use Rixafy\Routing\Route\Site\RouteSite;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="website")
 */
class Website
{
	/**
	 * @var UuidInterface
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", unique=true)
	 */
	protected $id;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	protected $name;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true)
	 */
	protected $domainHost;

	/**
	 * @var Language
	 * @ORM\ManyToOne(targetEntity="\Rixafy\Language\Language")
	 */
	protected $defaultLanguage;

	/**
	 * @ORM\OneToOne(targetEntity="\Rixafy\Blog\Blog", cascade={"persist", "remove"})
	 * @var Blog
	 */
	protected $blog;

	/**
	 * @ORM\OneToOne(targetEntity="\Rixafy\Routing\Route\Website\RouteWebsite", cascade={"persist", "remove"})
	 * @var RouteSite
	 */
	protected $routeSite;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	protected $httpsRedirect = false;

	use RemovableTrait;
	use DateTimeTrait;

	public function __construct(UuidInterface $id, WebsiteData $data)
	{
		$this->id = $id;
		$this->routeSite = $data->routeSite;
		$this->blog = $data->blog;
		$this->edit($data);
	}

	public function edit(WebsiteData $data): void
	{
		$this->name = $data->name;
		$this->domainHost = $data->domainHost;
		$this->defaultLanguage = $data->defaultLanguage;
		$this->httpsRedirect = $data->httpsRedirect;
	}

	public function getData(): WebsiteData
	{
		$data = new WebsiteData();
		$data->name = $this->name;
		$data->domainHost = $this->domainHost;
		$data->defaultLanguage = $this->defaultLanguage;
		$data->httpsRedirect = $this->httpsRedirect;

		return $data;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getDomainHost(): string
	{
		return $this->domainHost;
	}

	public function getDefaultLanguage(): Language
	{
		return $this->defaultLanguage;
	}

	public function getBlog(): Blog
	{
		return $this->blog;
	}

	public function isHttpsRedirect(): bool
	{
		return $this->httpsRedirect;
	}

	public function getRouteSite(): RouteSite
	{
		return $this->routeSite;
	}
}
