<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\RolesModule\Pages;

use Voonne\Messages\FlashMessage;
use Voonne\Pages\Page;
use Voonne\Voonne\Model\Repositories\RoleRepository;


class UpdatePage extends Page
{

	/**
	 * @var RoleRepository
	 */
	private $roleRepository;


	public function __construct(RoleRepository $roleRepository)
	{
		parent::__construct('update', 'voonne-rolesModule.update.title');

		$this->roleRepository = $roleRepository;

		$this->hideFromMenu();
	}


	public function startup()
	{
		parent::startup();

		if ($this->roleRepository->countBy(['id' => $this->getPresenter()->getParameter('id')]) == 0) {
			$this->flashMessage('voonne-rolesModule.update.roleNotFound', FlashMessage::ERROR);
			$this->redirect('users.default');
		}
	}


	public function isAuthorized()
	{
		return $this->getUser()->havePrivilege('admin', 'roles', 'update');
	}

}
