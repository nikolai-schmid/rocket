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
namespace rocket\spec\ei\component\field\impl\relation\model\mag;

use rocket\spec\ei\manage\util\model\EiUtils;

class EntryLabeler {
	private $eiUtils;
	private $genericLabel;
	private $selectedIdentityStrings = array();
	
	public function __construct(EiUtils $eiUtils) {
		$this->eiUtils = $eiUtils;
		$this->genericLabel = $eiUtils->getGenericLabel();
	}
	
	public function getGenericLabel() {
		return $this->genericLabel;
	}
	
	public function getIdentityStringByIdRep(string $idRep): string {
		if (isset($this->selectedIdentityStrings[$idRep])) {
			return $this->selectedIdentityStrings[$idRep];
		}
		
		return $this->eiUtils->createIdentityString(
				$this->eiUtils->lookupEiSelectionById($this->eiUtils->idRepToId($idRep)));
	}
	
	public function setSelectedIdentityString(string $idRep, string $identityString) {
		$this->selectedIdentityStrings[$idRep] = $identityString;
	}
	
	public function getSelectedIdentityStrings(): array {
		return $this->selectedIdentityStrings;
	}
	
	public function getEiSpecLabels() {
		$eiState = $this->eiUtils->getEiState();
		$contextEiMask = $eiState->getContextEiMask();
		$contextEiSpec = $eiState->getContextEiMask()->getEiEngine()->getEiSpec();
		
		$eiSpecLabels = array();
		
		if (!$contextEiSpec->isAbstract()) {
			$eiSpecLabels[$contextEiSpec->getId()] = $contextEiMask->getLabelLstr()->t($eiState->getN2nLocale());
		}
		
		foreach ($contextEiSpec->getAllSubEiSpecs() as $subEiSpec) {
			if ($subEiSpec->isAbstract()) continue;
		
			$eiSpecLabels[$subEiSpec->getId()] = $contextEiMask->determineEiMask($subEiSpec)->getLabelLstr()
					->t($eiState->getN2nLocale());
		}
		
		return $eiSpecLabels;
	}
}
