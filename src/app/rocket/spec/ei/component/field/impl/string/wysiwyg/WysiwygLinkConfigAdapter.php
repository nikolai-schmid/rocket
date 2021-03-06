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
namespace rocket\spec\ei\component\field\impl\string\wysiwyg;

use rocket\spec\ei\manage\mapping\EiMapping;
use rocket\spec\ei\manage\gui\EntrySourceInfo;
use rocket\spec\ei\manage\gui\FieldSourceInfo;

abstract class WysiwygLinkConfigAdapter implements WysiwygLinkConfig {
	
	/**
	 * @var \rocket\spec\ei\manage\mapping\EiMapping
	 */
	protected $eiMapping;
	
	/**
	 * @var \rocket\spec\ei\manage\gui\EntrySourceInfo
	 */
	protected $fieldSourceInfo;
	
	public function setup(EiMapping $eiMapping = null, 
			FieldSourceInfo $fieldSourceInfo = null){ 
		$this->eiMapping = $eiMapping;
		$this->fieldSourceInfo = $fieldSourceInfo;
	}
	
	public function isOpenInNewWindow() {
		return false;
	}	
}
