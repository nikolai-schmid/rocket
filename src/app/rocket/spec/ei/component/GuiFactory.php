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
namespace rocket\spec\ei\component;

use rocket\spec\ei\component\field\EiFieldCollection;
use rocket\spec\ei\component\modificator\EiModificatorCollection;
use n2n\reflection\ArgUtils;
use rocket\spec\ei\manage\gui\GuiDefinition;
use rocket\spec\ei\manage\gui\EntrySourceInfo;
use rocket\spec\ei\manage\gui\GuiElementAssembler;
use rocket\spec\ei\manage\gui\EiSelectionGui;
use rocket\spec\ei\component\field\GuiEiField;
use rocket\spec\ei\manage\gui\EditableInfo;
use rocket\spec\ei\EiFieldPath;
use rocket\spec\ei\manage\gui\GuiFieldFork;
use rocket\spec\ei\manage\gui\GuiField;

class GuiFactory {
	private $eiFieldCollection;
	private $eiModificatorCollection;
	
	public function __construct(EiFieldCollection $eiFieldCollection, EiModificatorCollection $eiModificatorCollection) {
		$this->eiFieldCollection = $eiFieldCollection;
		$this->eiModificatorCollection = $eiModificatorCollection;
	}
	
	public function createGuiDefinition() {
		$guiDefinition = new GuiDefinition();
		
		foreach ($this->eiFieldCollection as $id => $eiField) {
			if (!($eiField instanceof GuiEiField)) continue;
			
			if (null !== ($guiField = $eiField->getGuiField())){
				ArgUtils::valTypeReturn($guiField, GuiField::class, $eiField, 'getGuiField');
			
				$guiDefinition->putGuiField($id, $guiField, EiFieldPath::from($eiField));
			}
			
			if (null !== ($guiFieldFork = $eiField->getGuiFieldFork())){
				ArgUtils::valTypeReturn($guiFieldFork, GuiFieldFork::class, $eiField, 'getGuiFieldFork');
				
				$guiDefinition->putGuiFieldFork($id, $guiFieldFork);
			}
		}
		
		foreach ($this->eiModificatorCollection as $modificator) {
			$modificator->setupGuiDefinition($guiDefinition);
		}
		
		return $guiDefinition;
	}
	
	public function createEiSelectionGui(GuiDefinition $guiDefinition, EntrySourceInfo $entrySourceInfo, 
			bool $makeEditable, array $guiIdPaths): EiSelectionGui {
		ArgUtils::valArrayLike($guiIdPaths, 'rocket\spec\ei\manage\gui\GuiIdPath');
		
		$guiElementAssembler = new GuiElementAssembler($guiDefinition, $entrySourceInfo);
		
		$eiSelectionGui = new EiSelectionGui($guiDefinition, $entrySourceInfo->getViewMode());
		
		foreach ($guiIdPaths as $guiIdPath) {
			$result = $guiElementAssembler->assembleGuiElement($guiIdPath, $makeEditable);
			if ($result === null) continue;
			
			$eiSelectionGui->putDisplayable($guiIdPath, $result->getDisplayable());
			if (null !== ($magPropertyPath = $result->getMagPropertyPath())) {
				$eiSelectionGui->putEditableInfo($guiIdPath, new EditableInfo($result->isMandatory(), 
						$magPropertyPath));
			}
		}
		
		if (null !== ($dispatchable = $guiElementAssembler->getDispatchable())) {
			$eiSelectionGui->setDispatchable($guiElementAssembler->getDispatchable());
			$eiSelectionGui->setForkMagPropertyPaths($guiElementAssembler->getForkedMagPropertyPaths());
			$eiSelectionGui->setSavables($guiElementAssembler->getSavables());
		}
		
		foreach ($this->eiModificatorCollection as $eiModificator) {
			$eiModificator->setupEiSelectionGui($eiSelectionGui);
		}
		
		foreach ($entrySourceInfo->getEiSelectionGuiListeners() as $eiSelectionGuiListener) {
			$eiSelectionGui->registerEiSelectionGuiListener($eiSelectionGuiListener);
		}
		
		return $eiSelectionGui;
	}
}
