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
namespace rocket\spec\ei\manage\critmod\impl\model;

use rocket\spec\ei\manage\EiState;
use rocket\spec\ei\manage\critmod\filter\impl\form\FilterGroupForm;
use rocket\spec\ei\manage\critmod\filter\data\FilterGroupData;
use rocket\spec\ei\manage\critmod\sort\SortData;
use rocket\spec\ei\manage\critmod\filter\FilterDefinition;
use rocket\spec\ei\manage\critmod\sort\SortDefinition;
use rocket\spec\ei\manage\critmod\sort\impl\form\SortForm;
use n2n\web\dispatch\Dispatchable;
use n2n\web\dispatch\map\bind\BindingDefinition;
use rocket\spec\ei\manage\critmod\CriteriaConstraint;
use rocket\spec\ei\manage\critmod\filter\ComparatorConstraintGroup;

class CritmodForm implements Dispatchable {	
	private $critmodSaveDao;
// 	private $stateKey;
	private $categoryKey;
	private $eiSpecId;
	private $eiMaskId;
	private $active = false;
	
	protected $name;
	protected $selectedCritmodSaveId;
	protected $filterGroupForm;
	protected $sortForm;
	
	public function __construct(FilterDefinition $filterDefinition, SortDefinition $sortDefinition, 
			CritmodSaveDao $critmodSaveDao, string $stateKey, string $eiSpecId, string $eiMaskId = null) {
		$this->critmodSaveDao = $critmodSaveDao;
// 		$this->stateKey = $stateKey;
		$this->categoryKey = CritmodSaveDao::buildCategoryKey($stateKey, $eiSpecId, $eiMaskId);;
		$this->eiSpecId = $eiSpecId;
		$this->eiMaskId = $eiMaskId;
				
		$filterGroupData = null;
		$sortData = null;
		
		if (null !== ($tmpCritmodSave = $this->critmodSaveDao->getTmpCritmodSave($this->categoryKey))) {
			$this->name = $tmpCritmodSave->getName();
			$filterGroupData = $tmpCritmodSave->readFilterGroupData();
			$sortData = $tmpCritmodSave->readSortData();
			$this->active = true;
		} else if (null !== ($selectedCritmodSave = $critmodSaveDao->getSelectedCritmodSave($this->categoryKey))) {
			$this->selectedCritmodSaveId = $selectedCritmodSave->getId();
			$this->name = $selectedCritmodSave->getName() . ' 2';
			$filterGroupData = $selectedCritmodSave->getFilterGroupData();
			$sortData = $selectedCritmodSave->getSortData();
			$this->active = true;
		} else {
			$filterGroupData = new FilterGroupData();
			$sortData = new SortData();
			$this->active = false;
		}
		
		$this->filterGroupForm = new FilterGroupForm($filterGroupData, $filterDefinition);
		$this->sortForm = new SortForm($sortData, $sortDefinition);
	}
	
	public function isActive(): bool {
		return $this->active;
	}
	
// 	public function getStateKey(): string {
// 		return $this->stateKey;
// 	}
		
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string $selectedCritmodSaveId
	 */
	public function getSelectedCritmodSaveId() {
		return $this->selectedCritmodSaveId;
	}

	/**
	 * @param string $selectedCritmodSaveId
	 */
	public function setSelectedCritmodSaveId($selectedCritmodSaveId) {
		$this->selectedCritmodSaveId = $selectedCritmodSaveId;
	}

	public function getSelectedCritmodSaveIdOptions(): array {
		$options = array(null => null);
		foreach ($this->critmodSaveDao->getCritmodSaves($this->eiSpecId, $this->eiMaskId) as $critmodSave) {
			$options[$critmodSave->getId()] = $critmodSave->getName();
		}
		return $options;
	}
	
	/**
	 * @return FilterGroupForm
	 */
	public function getFilterGroupForm(): FilterGroupForm {
		return $this->filterGroupForm;
	}

	/**
	 * @param \rocket\spec\ei\manage\critmod\filter\impl\form\FilterGroupForm $filterGroupForm
	 */
	public function setFilterGroupForm(FilterGroupForm $filterGroupForm) {
		$this->filterGroupForm = $filterGroupForm;
	}

	/**
	 * @return SortForm
	 */
	public function getSortForm(): SortForm {
		return $this->sortForm;
	}

	/**
	 * @param \rocket\spec\ei\manage\critmod\sort\impl\form\SortForm $sortForm
	 */
	public function setSortForm(SortForm $sortForm) {
		$this->sortForm = $sortForm;
	}
	
	private function _validation(BindingDefinition $bd) {
	}

	public function applyToEiState(EiState $eiState, bool $tmp) {
		$critmodSave = $this->critmodSaveDao->getTmpCritmodSave($this->categoryKey);
		if ($critmodSave === null) {
			$critmodSave = $this->critmodSaveDao->getSelectedCritmodSave($this->categoryKey);
			if ($critmodSave === null) return;
		}

		$comparatorConstraint = $this->getFilterGroupForm()->getFilterDefinition()
						->createComparatorConstraint($critmodSave->readFilterGroupData());
		$eiState->getCriteriaConstraintCollection()->add(
				($tmp ? CriteriaConstraint::TYPE_TMP_FILTER : CriteriaConstraint::TYPE_HARD_FILTER),
				new ComparatorConstraintGroup(true, array($comparatorConstraint)));
		
		$sortCriteriaConstraint = $this->getSortForm()->getSortDefinition()
				->builCriteriaConstraint($critmodSave->readSortData(), $tmp);
		if ($sortCriteriaConstraint !== null) {
			$eiState->getCriteriaConstraintCollection()->add(
					($tmp ? CriteriaConstraint::TYPE_TMP_SORT : CriteriaConstraint::TYPE_HARD_SORT),
					$sortCriteriaConstraint);
		}
	}
	
	public function getSelectOptions(): array {
		
	}
	
	public function select() {
		$this->critmodSaveDao->setSelectedCritmodSave($this->categoryKey, $this->critmodSaveDao->getCritmodSaveById(
				$this->eiSpecId, $this->eiMaskId, $this->selectedCritmodSaveId));
	}
	
	public function apply() {
		$critmodSave = new CritmodSave();
		$critmodSave->setEiSpecId($this->eiSpecId);
		$critmodSave->setEiMaskId($this->eiMaskId);
		$critmodSave->writeFilterData($this->filterGroupForm->buildFilterGroupData());
		$critmodSave->writeSortData($this->sortForm->buildSortData());
		$this->critmodSaveDao->setTmpCritmodSave($this->categoryKey, $critmodSave);
	}
	
	public function save() {
		$critmodSave = $this->critmodSaveDao->getSelectedCritmodSave($this->categoryKey);
		if ($critmodSave === null) return;
		
		$critmodSave->writeFilterData($this->filterGroupForm->buildFilterGroupData());
		$critmodSave->writeSortData($this->sortForm->buildSortData());
	}
	
	public function saveAs() {
		$critmodSave = $this->critmodSaveDao->createCritmodSave($this->eiSpecId, $this->eiMaskId,
				$this->critmodSaveDao->buildUniqueCritmodSaveName($this->eiSpecId, $this->eiMaskId, $this->name), 
				$this->filterGroupForm->buildFilterGroupData(), 
				$this->sortForm->buildSortData());
		
		$this->critmodSaveDao->setSelectedCritmodSave($this->categoryKey, $critmodSave);
	}
	
	public function clear() {
		$this->critmodSaveDao->setSelectedCritmodSave($this->categoryKey, null);
		$this->critmodSaveDao->setTmpCritmodSave($this->categoryKey, null);
	}
	
	public function delete() {
		$critmodSave = $this->critmodSaveDao->getSelectedCritmodSave($this->categoryKey);
		if ($critmodSave === null) return;
		
		$this->critmodSaveDao->removeCritmodSave($critmodSave);
	}
	
	public static function create(EiState $eiState, CritmodSaveDao $critmodSaveDao, string $stateKey): CritmodForm {
		$eiMask = $eiState->getContextEiMask();
		
		return new CritmodForm($eiMask->getEiEngine()->createManagedFilterDefinition($eiState), 
				$eiMask->getEiEngine()->createManagedSortDefinition($eiState), 
				$critmodSaveDao, $stateKey, $eiState->getContextEiMask()->getEiEngine()->getEiSpec()->getId(), $eiMask->getId());
	}
}
