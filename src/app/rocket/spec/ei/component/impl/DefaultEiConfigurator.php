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
namespace rocket\spec\ei\component\impl;

use rocket\spec\ei\component\field\indepenent\EiFieldConfigurator;
use n2n\persistence\meta\structure\Column;
use rocket\spec\ei\component\field\indepenent\PropertyAssignation;
use rocket\spec\ei\component\field\indepenent\CompatibilityLevel;
use rocket\spec\ei\component\field\indepenent\IncompatiblePropertyException;

class DefaultEiConfigurator extends EiConfiguratorAdapter implements EiFieldConfigurator {
	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\field\indepenent\EiFieldConfigurator::initAutoEiFieldAttributes($column)
	 */
	public function initAutoEiFieldAttributes(Column $column = null) {
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\field\indepenent\EiFieldConfigurator::getAssignedEntityProperty()
	 */
	public function getAssignedEntityProperty() {
		return null;
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\field\indepenent\EiFieldConfigurator::getAssignedObjectPropertyAccessProxy()
	 */
	public function getAssignedObjectPropertyAccessProxy() {
		return null;
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\field\indepenent\EiFieldConfigurator::isAssignableToEntityProperty()
	 */
	public function isAssignableToEntityProperty(): bool {
		return false;
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\field\indepenent\EiFieldConfigurator::isAssignableToObjectProperty()
	 */
	public function isAssignableToObjectProperty(): bool {
		return false;
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\field\indepenent\EiFieldConfigurator::testCompatibility($propertyAssignation)
	 */
	public function testCompatibility(PropertyAssignation $propertyAssignation): int {
		return CompatibilityLevel::NOT_COMPATIBLE;
	}

	/**
	 * {@inheritDoc}
	 * @see \rocket\spec\ei\component\field\indepenent\EiFieldConfigurator::assignProperty($propertyAssignation)
	 */
	public function assignProperty(PropertyAssignation $propertyAssignation) {
		throw new IncompatiblePropertyException('EiField can not be assigned to a property.');
	}

	
}
