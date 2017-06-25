<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\RolesModule\Pages;

use Voonne\Pages\Page;


class CreatePage extends Page
{

	public function __construct()
	{
		parent::__construct('create', 'voonne-rolesModule.create.title');
	}


	public function isAuthorized()
	{
		return $this->getUser()->havePrivilege('admin', 'roles', 'create');
	}

}
