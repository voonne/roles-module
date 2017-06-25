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
use Voonne\Voonne\DuplicateEntryException;
use Voonne\Voonne\Model\Entities\Role;
use Voonne\Voonne\Model\Facades\RoleFacade;


class CreateFormPanel extends FormPanel
{

	/**
	 * @var RoleFacade
	 */
	private $roleFacade;


	public function __construct(RoleFacade $roleFacade)
	{
		parent::__construct();

		$this->roleFacade = $roleFacade;

		$this->setTitle('voonne-rolesModule.createForm.title');
	}


	public function setupForm(Container $container)
	{
		$container->addText('name', 'voonne-rolesModule.createForm.name')
			->setRequired('voonne-form.rules.required');

		$container->addSubmit('submit', 'voonne-rolesModule.createForm.submit');

		$container->onSuccess[] = [$this, 'success'];
	}


	public function success(Container $container, $values)
	{
		try {
			$role = new Role($values->name);

			$this->roleFacade->save($role);

			$this->flashMessage('voonne-rolesModule.createForm.created', FlashMessage::SUCCESS);
			$this->redirect('roles.update', ['id' => $role->getId()]);
		} catch (DuplicateEntryException $e) {
			$container->getForm()->addError('voonne-rolesModule.createForm.duplicateEntry');
		}
	}

}
