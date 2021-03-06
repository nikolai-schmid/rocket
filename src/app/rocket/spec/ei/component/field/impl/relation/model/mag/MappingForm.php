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

use n2n\web\dispatch\Dispatchable;
use n2n\reflection\annotation\AnnoInit;
use n2n\web\dispatch\annotation\AnnoDispObject;
use rocket\spec\ei\manage\util\model\EntryForm;
use n2n\web\dispatch\annotation\AnnoDispScalar;
use n2n\reflection\ArgUtils;
use rocket\spec\ei\manage\mapping\EiMapping;

class MappingForm implements Dispatchable {
	private static function _annos(AnnoInit $ai) {
		$ai->p('entryForm', new AnnoDispObject());
		$ai->p('orderIndex', new AnnoDispScalar());
	}

	private $entryLabel;
	private $eiMapping;
	private $entryForm;
	private $orderIndex;
	
	public function __construct(string $entryLabel, EiMapping $eiMapping = null, EntryForm $entryForm = null, 
			int $orderIndex = null) {
		ArgUtils::assertTrue($eiMapping !== null || $entryForm !== null);
		
		$this->entryLabel = $entryLabel;
		$this->eiMapping = $eiMapping;
		$this->entryForm = $entryForm;
		$this->orderIndex = $orderIndex;
	}
	
	public function isAccessible(): bool {
		return $this->entryForm !== null;
	}
	
	public function getEntryLabel(): string {
		return $this->entryLabel;
	}
	
	public function buildEiMapping() {
		if ($this->entryForm !== null) {
			return $this->entryForm->buildEiMapping();
		}
		
		return $this->eiMapping;
	}
	
	public function getEntryForm() {
		return $this->entryForm;
	}
	
	public function setEntryForm(EntryForm $entryForm) {
		$this->entryForm = $entryForm;
	}

	public function getOrderIndex() {
		return $this->orderIndex;
	}

	public function setOrderIndex($orderIndex) {
		$this->orderIndex = $orderIndex;
	}
	
	private function _validation() {}
}
