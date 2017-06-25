<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\RolesModule\Panels;

use Doctrine\ORM\EntityManagerInterface;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Voonne\Forms\Container;
use Voonne\Messages\FlashMessage;
use Voonne\Panels\Panels\BasicPanel\BasicPanel;
use Voonne\Security\Authorizator;
use Voonne\Voonne\Content\ContentForm;
use Voonne\Voonne\Model\Entities\Privilege;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Repositories\PrivilegeRepository;
use Voonne\Voonne\Model\Repositories\ResourceRepository;
use Voonne\Voonne\Model\Repositories\RoleRepository;


class PrivilegesPanel extends BasicPanel
{

	/**
	 * @var PrivilegeRepository
	 */
	private $privilegeRepository;

	/**
	 * @var RoleRepository
	 */
	private $roleRepository;

	/**
	 * @var ResourceRepository
	 */
	private $resourceRepository;

	/**
	 * @var ContentForm
	 */
	private $contentForm;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var Role
	 */
	private $role;

	/**
	 * @var Cache
	 */
	private $cache;


	public function __construct(
		PrivilegeRepository $privilegeRepository,
		RoleRepository $roleRepository,
		ResourceRepository $resourceRepository,
		ContentForm $contentForm,
		EntityManagerInterface $entityManager,
		IStorage $storage
	) {
		parent::__construct();

		$this->privilegeRepository = $privilegeRepository;
		$this->roleRepository = $roleRepository;
		$this->resourceRepository = $resourceRepository;
		$this->contentForm = $contentForm;
		$this->entityManager = $entityManager;
		$this->cache = new Cache($storage, Authorizator::CACHE_NAMESPACE);

		$this->setTitle('voonne-rolesModule.privileges.title');
	}


	public function beforeRender()
	{
		parent::beforeRender();

		$this->role = $this->roleRepository->find($this->getPresenter()->getParameter('id'));
	}


	public function setupForm(Container $container)
	{
		$container->addCheckboxList('privileges', null, $this->getPrivileges())
			->setDefaultValue($this->getDefaultPrivileges());

		$container->addSubmit('submit', 'voonne-rolesModule.privileges.submit');

		$container->onSuccess[] = [$this, 'success'];
	}


	public function success(Container $container, $values)
	{
		foreach ($this->privilegeRepository->findAll() as $privilege) {
			/** @var Privilege $privilege */
			$this->role->removePrivilege($privilege);
		}

		foreach ($values->privileges as $privilege) {
			$this->role->addPrivilege($this->privilegeRepository->find($privilege));
		}

		$this->entityManager->persist($this->role);
		$this->entityManager->flush();

		$this->cache->remove('permissions');

		$this->flashMessage('voonne-rolesModule.privileges.updated', FlashMessage::SUCCESS);
		$this->redirect('this');
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/PrivilegesPanel.latte');

		$this->template->resources = $this->resourceRepository->findAll();
		$this->template->form = $this->contentForm;

		$this->template->render();
	}


	private function getPrivileges()
	{
		$privileges = [];

		foreach ($this->privilegeRepository->findAll() as $privilege) {
			/** @var Privilege $privilege */
			$privileges[$privilege->getId()] = $privilege->getDescription();
		}

		return $privileges;
	}


	private function getDefaultPrivileges()
	{
		$privileges = [];

		foreach ($this->role->getPrivileges() as $privilege) {
			/** @var Privilege $privilege */
			$privileges[] = $privilege->getId();
		}

		return $privileges;
	}

}
