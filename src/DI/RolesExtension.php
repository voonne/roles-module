<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\RolesModule\DI;

use Kdyby\Translation\Translator;
use Nette\DI\CompilerExtension;
use Voonne\Layouts\Layout;
use Voonne\RolesModule\Pages\CreatePage;
use Voonne\RolesModule\Pages\DefaultPage;
use Voonne\RolesModule\Pages\UpdatePage;
use Voonne\RolesModule\Panels\CreateFormPanel;
use Voonne\RolesModule\Panels\RolesTablePanel;
use Voonne\RolesModule\Panels\UpdateFormPanel;
use Voonne\Voonne\InvalidStateException;


class RolesExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('voonne.permissionManager')
			->addSetup('addResource', ['admin', 'roles', 'voonne-rolesModule.permissions.name'])
			->addSetup('addPrivilege', ['admin', 'roles', 'create', 'voonne-rolesModule.permissions.create'])
			->addSetup('addPrivilege', ['admin', 'roles', 'view', 'voonne-rolesModule.permissions.view'])
			->addSetup('addPrivilege', ['admin', 'roles', 'update', 'voonne-rolesModule.permissions.update'])
			->addSetup('addPrivilege', ['admin', 'roles', 'remove', 'voonne-rolesModule.permissions.remove']);

		$builder->getDefinition('voonne.pageManager')
			->addSetup('addGroup', ['roles', 'voonne-rolesModule.title', 'check'])
			->addSetup('addPage', ['roles', '@' . $this->prefix('defaultPage')])
			->addSetup('addPage', ['roles', '@' . $this->prefix('createPage')])
			->addSetup('addPage', ['roles', '@' . $this->prefix('updatePage')]);

		$builder->addDefinition($this->prefix('defaultPage'))
			->setClass(DefaultPage::class)
			->addSetup('addPanel', ['@' . $this->prefix('roleTable'), [Layout::POSITION_CENTER]]);

		$builder->addDefinition($this->prefix('createPage'))
			->setClass(CreatePage::class)
			->addSetup('addPanel', ['@' . $this->prefix('createForm'), [Layout::POSITION_CENTER]]);

		$builder->addDefinition($this->prefix('updatePage'))
			->setClass(UpdatePage::class)
			->addSetup('addPanel', ['@' . $this->prefix('updateForm'), [Layout::POSITION_CENTER]]);

		$builder->addDefinition($this->prefix('createForm'))
			->setClass(CreateFormPanel::class);

		$builder->addDefinition($this->prefix('roleTable'))
			->setClass(RolesTablePanel::class);

		$builder->addDefinition($this->prefix('updateForm'))
			->setClass(UpdateFormPanel::class);
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$translatorName = $builder->getByType(Translator::class);

		if(empty($translatorName)) {
			throw new InvalidStateException('Kdyby/Translation not found. Please register Kdyby/Translation as an extension.');
		}

		$builder->getDefinition($translatorName)
			->addSetup('addResource', ['neon', realpath(__DIR__ . '/../translations/roles.cs.neon'), 'cs', 'voonne-rolesModule'])
			->addSetup('addResource', ['neon', realpath(__DIR__ . '/../translations/roles.en.neon'), 'en', 'voonne-rolesModule']);
	}

}
