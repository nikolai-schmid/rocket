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
namespace rocket\spec\ei\component\field\impl\ci\model;

use n2n\web\dispatch\map\PropertyPathPart;
use n2n\web\dispatch\map\bind\BindingErrors;
use n2n\l10n\MessageCode;
use n2n\web\dispatch\map\PropertyPath;
use n2n\web\dispatch\map\val\SimplePropertyValidator;

class ValContentItemOption extends SimplePropertyValidator {
	private $panelConfigs;
	
	public function __construct(array $panelConfigs) {
		$this->panelConfigs = $panelConfigs;
	}
	/* (non-PHPdoc)
	 * @see \n2n\web\dispatch\val\SimplePropertyValidator::validateValue()
	 */
	protected function validateValue($mapValue) {
		return;	
		foreach ($this->panelConfigs as $panelConfig) {
			if (!$panelConfig->isRestricted()) continue;

			$panelName = $panelConfig->getName();
			$allowedContentItemIds = $panelConfig->getAllowedContentItemIds();
			$propertyPath = new PropertyPath(array($pathPart));
			foreach ($mapValue->currentMappingForms as $key => $entryFormMappingResult) {
				if (!$entryFormMappingResult->mainEntryFormPart->MagForm->has('panel')
						|| $entryFormMappingResult->mainEntryFormPart->MagForm->panel != $panelName) continue;
				$this->checkTypeId(
						$propertyPath->ext('currentMappingForms[' . $key . ']')->ext('selectedTypeId'), 
						$entryFormMappingResult->selectedTypeId, $allowedContentItemIds, $bindingErrors);
			}

			foreach ($mapValue->newMappingForms as $key => $entryFormMappingResult) {
				if (!$entryFormMappingResult->mainEntryFormPart->MagForm->has('panel')
						|| $entryFormMappingResult->mainEntryFormPart->MagForm->panel != $panelName) continue;
				$this->checkTypeId(
						$propertyPath->ext('newMappingForms[' . $key . ']')->ext('selectedTypeId'),
						$entryFormMappingResult->selectedTypeId, $allowedContentItemIds, $bindingErrors);
			}
			
		}		
	}
	
	private function checkTypeId($propertyExpression, $selectedTypeId, array $allowedContentItemIds, 
			BindingErrors $be) {
		if (in_array($selectedTypeId, $allowedContentItemIds)) return;
		$be->addError($propertyExpression, new MessageCode('spec_field_contentitem_invalid_panel'));
	}
}
