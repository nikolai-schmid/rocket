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
namespace rocket\spec\ei\component\field\impl\string\conf;

use rocket\spec\ei\component\EiSetupProcess;
use n2n\util\ex\IllegalStateException;
use n2n\core\container\N2nContext;
use rocket\spec\ei\component\field\impl\string\StringEiField;
use n2n\impl\web\dispatch\mag\model\BoolMag;
use n2n\util\StringUtils;
use n2n\web\dispatch\mag\MagDispatchable;
use n2n\persistence\meta\structure\Column;

class StringEiFieldConfigurator extends AlphanumericEiFieldConfigurator {
	const OPTION_MULTILINE_KEY = 'multiline';
	
	public function setup(EiSetupProcess $setupProcess) {
		parent::setup($setupProcess);
	
		IllegalStateException::assertTrue($this->eiComponent instanceof StringEiField);
		
		if ($this->attributes->contains(self::OPTION_MULTILINE_KEY)) {
			$this->eiComponent->setMultiline($this->attributes->getBool(self::OPTION_MULTILINE_KEY));
		}
	}
	
	private static $multilineNeedles = array('description', 'lead', 'intro', 'content');
	
	public function initAutoEiFieldAttributes(Column $column = null) {
		parent::initAutoEiFieldAttributes($column);
		
		if (StringUtils::contains(self::$multilineNeedles, $this->requirePropertyName(), false)) {
			$this->attributes->set(self::OPTION_MULTILINE_KEY, true);
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\field\impl\string\conf\AlphanumericEiFieldConfigurator::createMagDispatchable($n2nContext)
	 * @return MagDispatchable
	 */
	public function createMagDispatchable(N2nContext $n2nContext): MagDispatchable {
		$magDispatchable = parent::createMagDispatchable($n2nContext);
		
		$magDispatchable->getMagCollection()->addMag(new BoolMag(self::OPTION_MULTILINE_KEY, 'Multiline',
				$this->attributes->getBool(self::OPTION_MULTILINE_KEY, false, $this->eiComponent->isMultiline())));
		
		return $magDispatchable;
	}
	
	public function saveMagDispatchable(MagDispatchable $magDispatchable, N2nContext $n2nContext) {
		parent::saveMagDispatchable($magDispatchable, $n2nContext);
		
		$multilineMag = $magDispatchable->getMagCollection()->getMagByPropertyName(self::OPTION_MULTILINE_KEY);

		$this->attributes->set(self::OPTION_MULTILINE_KEY, $multilineMag->getValue());
	}
}
