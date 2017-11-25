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
use Doctrine\ORM\QueryBuilder;
use Voonne\Messages\FlashMessage;
use Voonne\Model\IOException;
use Voonne\Panels\Panels\TablePanel\Adapters\Doctrine2Adapter;
use Voonne\Panels\Panels\TablePanel\TablePanel;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Facades\RoleFacade;
use Voonne\Voonne\Model\Repositories\RoleRepository;


class RolesTablePanel extends TablePanel
{

	/**
	 * @var RoleRepository
	 */
	private $roleRepository;

	/**
	 * @var RoleFacade
	 */
	private $roleFacade;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;


	public function __construct(
		RoleRepository $roleRepository,
		RoleFacade $roleFacade,
		EntityManagerInterface $entityManager
	)
	{
		parent::__construct();

		$this->roleRepository = $roleRepository;
		$this->roleFacade = $roleFacade;
		$this->entityManager = $entityManager;

		$this->setTitle('voonne-rolesModule.rolesTable.title');
	}

	public function beforeRender()
	{
		parent::beforeRender();

		$this->addColumn('name', 'voonne-rolesModule.rolesTable.name');

		$this->addAction('update', 'voonne-rolesModule.rolesTable.update', function (Role $role) {
			if ($this->getUser()->havePrivilege('admin', 'roles', 'update')) {
				return $this->link('roles.update', ['id' => $role->getId()]);
			} else {
				return null;
			}
		});

		$this->addAction('remove', 'voonne-rolesModule.rolesTable.remove', function (Role $role) {
			if (!in_array($role->getId(), $this->getRoles()) && $this->getUser()->havePrivilege('admin', 'roles', 'remove')) {
				return $this->link('remove!', ['id' => $role->getId()]);
			} else {
				return null;
			}
		});

		$this->setAdapter(new Doctrine2Adapter($this->entityManager->createQueryBuilder()->select('r')->from(Role::class, 'r')));
	}


	public function handleRemove($id)
	{
		try {
			if (!$this->getUser()->havePrivilege('admin', 'roles', 'remove')) {
				$this->flashMessage('voonne-common.authentication.unauthorizedAction', FlashMessage::ERROR);
				$this->redirect('this');
			}

			$this->roleFacade->remove($this->roleRepository->find($id));

			$this->flashMessage('voonne-rolesModule.rolesTable.removed', FlashMessage::SUCCESS);
			$this->redirect('this');
		} catch(IOException $e) {
			$this->flashMessage('voonne-rolesModule.rolesTable.roleNotFound', FlashMessage::ERROR);
			$this->redirect('this');
		}
	}


	private function getRoles()
	{
		$roles = [];

		foreach ($this->getUser()->getUser()->getRoles() as $role) {
			/** @var Role $role */
			$roles[] = $role->getId();
		}

		return $roles;
	}

}
