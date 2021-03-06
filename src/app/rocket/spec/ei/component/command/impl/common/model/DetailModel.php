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
namespace rocket\spec\ei\component\command\impl\common\model;

use rocket\spec\ei\manage\util\model\EntryInfo;
use rocket\spec\ei\manage\util\model\EntryManager;
use n2n\core\NotYetImplementedException;

class DetailModel {
	private $entryManager;
	private $entryInfo;
	
	public function __construct(EntryManager $entryManager, EntryInfo $entryInfo) {
		$this->entryManager = $entryManager;
		$this->entryInfo = $entryInfo;
	}
// 	/**
// 	 * @return EntryInfo
// 	 */
// 	public function getEntryInfo() {
// 		return $this->entryInfo;
// 	}
	
// 	public function getEiState() {
// 		return $this->entryManager->getEiState();
// 	}
	
	public function publish() {
		throw new NotYetImplementedException();
		if (!$this->eiSelection->isDraft()) return false;
		
		$id =  $this->eiSelection->getId();
		$originalEntry = $this->eiSelection->getLiveEntityObj();
		$draft = $this->eiSelection->getDraft();
		$draftedEntry = $draft->getDraftedEntity();
		
		$draft->setPublished(true);
		$this->historyModel->saveDraft($draft);
		
		$entityModel = $this->getEiSpec()->getEntityModel();
		$entityModel->copy($draftedEntry, $originalEntry);
		$this->em->merge($originalEntry);
		
		if (is_null($this->translationModel)) return true;
		
		$entityTranslationModel = $this->eiSpec->getTranslationModel();
		foreach ($this->translationModel->getTranslationsByElementId($draft->getId(), $draftedEntry) as $translation) {
			$entityTranslationModel->saveTranslation($translation->copy($id));
		}
		
		return true;
	}
	/* (non-PHPdoc)
	 * @see \rocket\spec\ei\component\command\impl\common\model\EntryCommandModel::getEntryModel()
	 */
	public function getCurrentEntryModel() {
		return $this->entryInfo;
	}

}
