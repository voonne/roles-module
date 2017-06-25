<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\RolesModule\Panels;

use Voonne\Forms\Container;
use Voonne\Messages\FlashMessage;
use Voonne\Panels\Panels\FormPanel\FormPanel;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Facades\RoleFacade;
use Voonne\Voonne\Model\Repositories\RoleRepository;


class UpdateFormPanel extends FormPanel
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
	 * @var Role
	 */
	private $role;


	public function __construct(RoleRepository $roleRepository, RoleFacade $roleFacade)
	{
		parent::__construct();

		$this->roleRepository = $roleRepository;
		$this->roleFacade = $roleFacade;

		$this->setTitle('voonne-rolesModule.updateForm.title');
	}


	public function beforeRender()
	{
		parent::beforeRender();

		$this->role = $this->roleRepository->find($this->getPresenter()->getParameter('id'));
	}


	public function setupForm(Container $container)
	{
		$container->addText('name', 'voonne-rolesModule.updateForm.name')
			->setDefaultValue($this->role->getName())
			->setRequired('voonne-form.rules.required');

		$container->addSubmit('submit', 'voonne-rolesModule.updateForm.submit');

		$container->onSuccess[] = [$this, 'success'];
	}


	public function success(Container $container, $values)
	{
		$this->role->update($values->name);

		$this->roleFacade->save($this->role);

		$this->flashMessage('voonne-rolesModule.updateForm.updated', FlashMessage::SUCCESS);
		$this->redirect('roles.default');
	}

}
