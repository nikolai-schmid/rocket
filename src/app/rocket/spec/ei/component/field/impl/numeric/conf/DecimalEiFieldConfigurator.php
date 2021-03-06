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
namespace rocket\spec\ei\component\field\impl\numeric\conf;

use n2n\core\container\N2nContext;
use n2n\impl\web\dispatch\mag\model\NumericMag;
use rocket\spec\ei\component\EiSetupProcess;
use n2n\reflection\CastUtils;
use rocket\spec\ei\component\field\impl\numeric\DecimalEiField;
use n2n\web\dispatch\mag\MagDispatchable;
use n2n\util\config\LenientAttributeReader;

class DecimalEiFieldConfigurator extends NumericEiFieldConfigurator {
	const OPTION_DECIMAL_PLACES_KEY = 'decimalPlaces';
	
	public function getTypeName(): string {
		return 'Decimal';
	}
	
	public function createMagDispatchable(N2nContext $n2nContext): MagDispatchable {
		$lar = new LenientAttributeReader($this->attributes);
		
		$magDispatchable = parent::createMagDispatchable($n2nContext);
		$magDispatchable->getMagCollection()->addMag(new NumericMag(self::OPTION_DECIMAL_PLACES_KEY, 
				'Positions after decimal point', $lar->getNumeric(self::OPTION_DECIMAL_PLACES_KEY, 0), true, 0));
		return $magDispatchable;
	}
	
	public function setup(EiSetupProcess $eiSetupProcess) {
		parent::setup($eiSetupProcess);
		
		if ($this->attributes->contains(self::OPTION_DECIMAL_PLACES_KEY)) {
			CastUtils::assertTrue($this->eiComponent instanceof DecimalEiField);
			$this->eiComponent->setDecimalPlaces($this->attributes->get(self::OPTION_DECIMAL_PLACES_KEY));
		}
	}
	
	public function saveMagDispatchable(MagDispatchable $magDispatchable, N2nContext $n2nContext) {
		parent::saveMagDispatchable($magDispatchable, $n2nContext);
	
		$magCollection = $magDispatchable->getMagCollection();
	
		if (null !== ($decimalPlaces = $magCollection->getMagByPropertyName(self::OPTION_DECIMAL_PLACES_KEY)
				->getValue())) {
			$this->attributes->set(self::OPTION_DECIMAL_PLACES_KEY, $decimalPlaces);
		}
	}
}
