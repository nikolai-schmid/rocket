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
namespace rocket\spec\ei\component\field\impl\string;

use n2n\l10n\N2nLocale;
use n2n\impl\web\dispatch\mag\model\StringMag;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\impl\persistence\orm\property\ScalarEntityProperty;
use n2n\persistence\orm\property\EntityProperty;
use n2n\l10n\DynamicTextCollection;
use rocket\spec\ei\manage\gui\EntrySourceInfo;
use rocket\spec\ei\component\field\impl\string\conf\PathPartEiFieldConfigurator;
use rocket\spec\ei\manage\gui\DisplayDefinition;
use rocket\spec\ei\manage\EiObject;
use rocket\spec\ei\component\EiConfigurator;
use n2n\web\dispatch\mag\Mag;
use n2n\reflection\ArgUtils;
use rocket\spec\ei\EiFieldPath;
use rocket\spec\ei\manage\gui\FieldSourceInfo;
use rocket\spec\ei\manage\generic\GenericEiProperty;
use rocket\spec\ei\manage\mapping\Mappable;
use rocket\spec\ei\manage\generic\ScalarEiProperty;
use rocket\spec\ei\component\field\indepenent\EiFieldConfigurator;

class PathPartEiField extends AlphanumericEiField  {
	const URL_COUNT_SEPERATOR = '-';
	
	private $nullAllowed = false;
	private $baseScalarEiProperty;
	private $uniquePerGenericEiProperty;
	private $critical = false;
	private $criticalMessage;
	private $criticalMessageCodeDtc;
	
	private $urlEiCommand;
	
	public function __construct() {
		parent::__construct();
		$this->displayDefinition->setDefaultDisplayedViewModes(DisplayDefinition::VIEW_MODE_BULKY_ADD 
				| DisplayDefinition::VIEW_MODE_LIST_READ);

		$this->getStandardEditDefinition()->setMandatory(false);
	}
	
	/* (non-PHPdoc)
	 * @see \rocket\spec\ei\component\field\impl\EditableEiFieldAdapter::createEiConfigurator()
	 */
	public function createEiFieldConfigurator(): EiFieldConfigurator {
		return new PathPartEiFieldConfigurator($this);
	}
	
	public function getTypeName(): string {
		return 'Path Part';
	}
	
	public function isNullAllowed(): bool {
		return $this->nullAllowed;
	}

	public function setNullAllowed(bool $nullAllowed) {
		$this->nullAllowed = $nullAllowed;
	}

	public function getBaseScalarEiProperty() {
		return $this->baseScalarEiProperty;
	}

	public function setBaseScalarEiProperty(ScalarEiProperty $baseScalarEiProperty = null) {
		$this->baseScalarEiProperty = $baseScalarEiProperty;
	}

	public function getUniquePerGenericEiProperty() {
		return $this->uniquePerGenericEiProperty;
	}

	public function setUniquePerGenericEiProperty(GenericEiProperty $uniquePerCriteriaProperty = null) {
		$this->uniquePerGenericEiProperty = $uniquePerCriteriaProperty;
	}

	public function isCritical(): bool {
		return $this->critical;
	}

	public function setCritical(bool $critical) {
		$this->critical = $critical;
	}

	public function getCriticalMessage() {
		return $this->criticalMessage;
	}

	public function setCriticalMessage(string $criticalMessage = null) {
		$this->criticalMessage = $criticalMessage;
	}

// 	public function getUrlEiCommand() {
// 		return $this->urlEiCommand;
// 	}

// 	public function setUrlEiCommand($urlEiCommand) {
// 		$this->urlEiCommand = $urlEiCommand;
// 	}

	public function setEntityProperty(EntityProperty $entityProperty = null) {
		ArgUtils::assertTrue($entityProperty instanceof ScalarEntityProperty);
		
		parent::setEntityProperty($entityProperty);
	}
	
	public function createOutputUiComponent(HtmlView $view, FieldSourceInfo $entrySourceInfo)  {
		return $view->getHtmlBuilder()->getEsc($entrySourceInfo->getValue(EiFieldPath::from($this)));
	}

	
// 	public function buildMappable(EiObject $eiObject) {
// 		$mappable = parent::buildMappable($eiObject);
// 		$mappable->
// 	}

	private function buildMagInputAttrs(FieldSourceInfo $fieldSourceInfo): array {
		$attrs = array('placeholder' => $this->getLabelLstr());
		
		if ($fieldSourceInfo->isNew() || $fieldSourceInfo->isDraft() || !$this->critical) return $attrs;
	
		$attrs['class'] = 'rocket-critical-input';
		
		if (null !== $this->criticalMessage) {
			$dtc = new DynamicTextCollection('rocket', $fieldSourceInfo->getRequest()->getN2nLocale());
			$attrs['data-confirm-message'] = $this->criticalMessage;
			$attrs['data-edit-label'] =  $dtc->translate('common_edit_label');
			$attrs['data-cancel-label'] =  $dtc->translate('common_cancel_label');
		}
		
		return $attrs;
	}
	
	public function createMag(string $propertyName, FieldSourceInfo $fieldSourceInfo): Mag {
		$attrs = $this->buildMagInputAttrs($fieldSourceInfo);
		
		return new StringMag($propertyName, $this->getLabelLstr(), null,
				$this->isMandatory($fieldSourceInfo), $this->getMaxlength(), false, null, $attrs);
	}
	
	public function buildIdentityString(EiObject $eiObject, N2nLocale $n2nLocale): string {
		return $this->read($eiObject);
	}
}
