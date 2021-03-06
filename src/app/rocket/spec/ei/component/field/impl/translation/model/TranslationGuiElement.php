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
namespace rocket\spec\ei\component\field\impl\translation\model;

use n2n\web\dispatch\Dispatchable;
use rocket\spec\ei\manage\gui\GuiIdPath;
use n2n\impl\web\ui\view\html\HtmlView;
use rocket\spec\ei\manage\gui\ForkedGuiElement;
use rocket\spec\ei\manage\gui\GuiDefinition;
use rocket\spec\ei\manage\gui\Displayable;
use rocket\spec\ei\manage\gui\AssembleResult;
use rocket\spec\ei\manage\gui\GuiElementAssembler;
use n2n\l10n\N2nLocale;
use rocket\spec\ei\component\field\impl\relation\model\ToManyMappable;
use rocket\spec\ei\component\field\impl\relation\model\RelationEntry;
use rocket\spec\ei\manage\mapping\FieldErrorInfo;
use rocket\spec\ei\component\field\impl\translation\conf\N2nLocaleDef;

class TranslationGuiElement implements ForkedGuiElement {
	private $toManyMappable;
	private $guiDefinition;
	private $label;

	private $n2nLocaleDefs = array();
	private $targetRelationEntries = array();
	private $guiElementAssemblers = array();
	private $mandatoryN2nLocaleIds = array();
	
	private $translationForm;
		
	public function __construct(ToManyMappable $toManyMappable, GuiDefinition $guiDefinition, $label) {
		$this->toManyMappable = $toManyMappable;
		$this->guiDefinition = $guiDefinition;
		$this->label = $label;
	}
	
	public function registerN2nLocale(N2nLocaleDef $n2nLocaleDef, RelationEntry $targetRelationEntry, 
			GuiElementAssembler $guiElementAssembler, $mandatory) {
		$n2nLocaleId = $n2nLocaleDef->getN2nLocaleId();
		$this->n2nLocaleDefs[$n2nLocaleId] = $n2nLocaleDef;
		$this->targetRelationEntries[$n2nLocaleId] = $targetRelationEntry;
		$this->guiElementAssemblers[$n2nLocaleId] = $guiElementAssembler;
		if ($mandatory) {
			$this->mandatoryN2nLocaleIds[$n2nLocaleId] = $n2nLocaleId;
		}
	}
	
	private function setupTranslationForm() {
		if ($this->translationForm === null) {
			$this->translationForm = new TranslationForm($this->mandatoryN2nLocaleIds, $this->label);
		}

		foreach ($this->guiElementAssemblers as $n2nLocaleId => $guiElementAssebler) {
			$dispatchable = $guiElementAssebler->getDispatchable();
			$guiElementAssebler->getEntrySourceInfo()->isNew();
			if ($dispatchable !== null) {
				$this->translationForm->putAvailableDispatchable($n2nLocaleId, $dispatchable);
				
				if (!$guiElementAssebler->getEntrySourceInfo()->isNew()) {
					$this->translationForm->putDispatchable($n2nLocaleId, $dispatchable);
				}		
			}
		}
	}
	
	public function assembleGuiElement(GuiIdPath $guiIdPath, $makeEditable): AssembleResult {
		$label = $this->guiDefinition->getGuiFieldByGuiIdPath($guiIdPath)->getDisplayLabel();
		$eiFieldPath = $this->guiDefinition->guiIdPathToEiFieldPath($guiIdPath);

		
		
// 		$fieldErrorInfo = new FieldErrorInfo();
		
		$translationDisplayable = new TranslationDisplayable($label);
		
		$translationMag = null;
		if ($makeEditable) {
			$translationMag = new TranslationMag($guiIdPath->__toString(), $label);
		}
		
		$mandatory = false;
		foreach ($this->guiElementAssemblers as $n2nLocaleId => $guiElementAssembler) {
			$result = $guiElementAssembler->assembleGuiElement($guiIdPath, $makeEditable);
			if ($result === null) continue;
			
			$fieldErrorInfo = $guiElementAssembler->getEntrySourceInfo()->getEiMapping()->getMappingErrorInfo()
					->getFieldErrorInfo($eiFieldPath);
// 			$fieldErrorInfo->addSubFieldErrorInfo($result->getFieldErrorInfo());
			
			if ($this->targetRelationEntries[$n2nLocaleId]->getEiSelection()->isNew()) {
				$translationDisplayable->putDisplayable($n2nLocaleId, new EmptyDisplayable($result->getDisplayable()), 
						$fieldErrorInfo);
			} else {
				$translationDisplayable->putDisplayable($n2nLocaleId, $result->getDisplayable(), $fieldErrorInfo);
			}
			
			if (!$makeEditable) continue;
			
			if (null !== ($magPropertyPath = $result->getMagPropertyPath())) {
				$translationMag->putMagPropertyPath($n2nLocaleId, $magPropertyPath, $fieldErrorInfo);
				if (!$mandatory) $mandatory = $result->isMandatory();
			} else {
				$translationMag->putDisplayable($n2nLocaleId, $result->getDisplayable(), $fieldErrorInfo);
			}
		}
		
		if (!$makeEditable) {
			return new AssembleResult($translationDisplayable);
		}
		
		$this->setupTranslationForm();
				
		return new AssembleResult($translationDisplayable, $this->translationForm
				->registerOption($translationMag), $mandatory);
	}
		
	public function createForkOption($propertyName) {
		if ($this->translationForm === null) {
			return null;
		}
		
		return new ForkOption($propertyName, $this->label, $this->translationForm, $this->n2nLocaleDefs);
	}
	
	public function save() {
		if ($this->translationForm === null) return;
		
		$targetRelationEntries = array();
		foreach ($this->translationForm->getDispatchables() as $n2nLocaleId => $dispatchable) {
			$this->guiElementAssemblers[$n2nLocaleId]->save();
			$targetRelationEntries[$n2nLocaleId] = $this->targetRelationEntries[$n2nLocaleId];
			$targetRelationEntries[$n2nLocaleId]->getEiSelection()->getLiveObject()
					->setN2nLocale(new N2nLocale($n2nLocaleId));
		}
		
		$this->toManyMappable->setValue($targetRelationEntries);
	}
}

class EmptyDisplayable implements Displayable {
	private $displayable;
	
	public function __construct(Displayable $displayable) {
		$this->displayable = $displayable;
	}
	
	public function isMandatory(): bool {
		return $this->displayable->isMandatory();
	}
	
	public function isReadOnly(): bool {
		return $this->displayable->isReadOnly();
	}
	
	public function getUiOutputLabel(): string {
		return $this->displayable->getUiOutputLabel();
	}
	
	public function getOutputHtmlContainerAttrs(): array {
		return array('class' => 'rocket-empty-translation');
	}
	
	public function createOutputUiComponent(HtmlView $view) {
		return $view->getHtmlBuilder()->getText('ei_impl_locale_not_active_label');
	}
}
