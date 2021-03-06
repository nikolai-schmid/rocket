<?php
/*
 * Copyright (c) 2012-2016, Hofmänner New Media.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 * This file is part of the n2n module ROCKET.
 *
 * ROCKET is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Lesser General Public License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * ROCKET is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details: http://www.gnu.org/licenses/
 *
 * The following people participated in this project:
 *
 * Andreas von Burg...........:	Architect, Lead Developer, Concept
 * Bert Hofmänner.............: Idea, Frontend UI, Design, Marketing, Concept
 * Thomas Günther.............: Developer, Frontend UI, Rocket Capability for Hangar
 */
namespace rocket\user\model\security;

use rocket\core\model\Rocket;
use rocket\spec\config\CustomSpec;
use rocket\spec\security\SecurityManager;
use rocket\spec\ei\security\EiPermissionManager;
use rocket\user\bo\RocketUser;
use n2n\util\ex\NotYetImplementedException;
use rocket\core\model\MenuItem;

class RocketUserSecurityManager implements SecurityManager {
	private $rocketUser;
	private $eiPermissionManager;
	
	public function __construct(RocketUser $rocketUser) {
		$this->rocketUser = $rocketUser;
		$this->eiPermissionManager = new RocketUserEiPermissionManager($rocketUser);
	}
	
	public function getCustomSpecAttributes(CustomSpec $customSpec) {
		throw new NotYetImplementedException();
// 		foreach ($this->rocketUser->getRocketUserGroups() as $rocketUserGroup) {
// 			foreach ($rocketUserGroup->getCustomGrants() as $customGrant) {
				
// 			}
// 		}
	}
	
	public function isMenuItemAccessible(MenuItem $menuItem): bool {
		foreach ($this->rocketUser->getRocketUserGroups() as $rocketUserGroup) {
			if (!$rocketUserGroup->isMenuItemAccessRestricted() || 
					$rocketUserGroup->containsAccessibleMenuItemId($menuItem->getId())) {
				return true;
			}
		}
		
		return false;
	}
	
	public function getEiPermissionManager(): EiPermissionManager {
		return $this->eiPermissionManager;
	}
}
