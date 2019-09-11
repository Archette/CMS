<?php

declare(strict_types=1);

namespace Affilix\CMS\Router;

use Nette\Application\Routers\RouteList;
use Nette\Application\UI\Presenter;
use Nette\Routing\Router;
use Rixafy\Routing\Route\Group\RouteGroupRepository;
use Rixafy\Routing\Route\RouteRepository;
use Rixafy\Routing\Route\Site\RouteSite;
use Rixafy\Routing\Route\Site\RouteSiteFacade;

final class RouterFactory
{
	/** @var RouteRepository */
	private $routeRepository;

	/** @var RouteGroupRepository */
	private $routeGroupRepository;

	/** @var RouteSiteFacade */
	private $routeSiteFacade;

	public function __construct(
		RouteRepository $routeRepository,
		RouteSiteFacade $routeSiteFacade,
		RouteGroupRepository $routeGroupRepository
	) {
		$this->routeRepository = $routeRepository;
		$this->routeSiteFacade = $routeSiteFacade;
		$this->routeGroupRepository = $routeGroupRepository;
	}

	public function create(): RouteList
	{
		$domainHost = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;

		$parts = explode('.', $domainHost);
		if (count($parts) > 2) {
			$domainHost = $parts[1] . '.' . $parts[2];
		}

		return $this->loadRoutes($domainHost !== null ? $this->routeSiteFacade->getByDomainHost($domainHost) : null);
	}

	public function loadRoutes(?RouteSite $routeSite): RouteList
	{
		$router = new RouteList();

		$router->addRoute('admin', [
			Presenter::PRESENTER_KEY => 'CMS:Admin:Auth',
			Presenter::ACTION_KEY => 'default'
		]);

		$router->addRoute('admin[/<presenter=Auth>][/<customerId>][/<action=default>][/<id>][/<name>]', [
			'module' => 'Admin'
		]);

		$router->addRoute('/', [
			Presenter::PRESENTER_KEY => 'CMS:Homepage:default',
			Presenter::ACTION_KEY => 'default'
		]);

		if ($routeSite !== null) {
			$groups = $this->routeGroupRepository->getQueryBuilderForAll($routeSite->getId())
				->select('e.id, e.prefix, e.previousPrefixes')
				->getQuery()
				->getArrayResult();

			foreach ($groups as $group) {
				$routes = $this->routeRepository->getQueryBuilderForAllInGroup($group['id'], $routeSite->getId())
					->select('e.name, e.module, e.controller, e.action, e.target, e.parameters')
					->addSelect('e.siteNameCounter, e.groupNameCounter')
					->addSelect('e.previousNamesInSite, e.previousNamesInGroup')
					->getQuery()
					->getArrayResult();

				foreach ($routes as $route) {
					if ($group['prefix'] === '/') {
						$routeName = $route['name'] . ($route['siteNameCounter'] !== 1 ? '-' . $route['siteNameCounter'] : '');

					} else {
						$routeName = $route['name'] . ($route['groupNameCounter'] !== 1 ? '-' . $route['groupNameCounter'] : '');
					}

					$router->addRoute('/' . $routeName, [
							Presenter::PRESENTER_KEY => $route['module'] . ':' . $route['controller'],
							Presenter::ACTION_KEY => $route['action'],
							'id' => (string)$route['target']
						] + $route['parameters']);

					if ($group['prefix'] === '/') {
						foreach ($route['previousNamesInSite'] as $previousName) {
							$router->addRoute($previousName, [
									Presenter::PRESENTER_KEY => $route['module'] . ':' . $route['controller'],
									Presenter::ACTION_KEY => $route['action'],
									'id' => (string)$route['target']
								] + $route['parameters'], Router::ONE_WAY);
						}
					} else {
						foreach ($route['previousNamesInGroup'] as $previousName) {
							$router->addRoute($previousName, [
									Presenter::PRESENTER_KEY => $route['module'] . ':' . $route['controller'],
									Presenter::ACTION_KEY => $route['action'],
									'id' => (string)$route['target']
								] + $route['parameters'], Router::ONE_WAY);
						}
					}
				}
			}
		}

		return $router;
	}
}
