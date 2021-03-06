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

use n2n\reflection\property\AccessProxy;
use n2n\impl\web\dispatch\mag\model\MagAdapter;
use n2n\impl\web\dispatch\property\ScalarProperty;
use rocket\spec\ei\manage\gui\Displayable;
use n2n\web\dispatch\map\PropertyPath;
use n2n\impl\web\ui\view\html\HtmlView;
use n2n\web\dispatch\map\PropertyPathPart;
use n2n\web\dispatch\map\bind\BindingDefinition;
use n2n\web\dispatch\property\ManagedProperty;
use n2n\web\ui\UiComponent;
use rocket\spec\ei\manage\mapping\FieldErrorInfo;
use n2n\web\dispatch\map\bind\BindingErrors;

class TranslationMag extends MagAdapter {
	private $displayables = array();
	private $magPropertyPaths = array();
	private $fieldErrorInfos = array();

	public function __construct($propertyName, $label) {
		parent::__construct($propertyName, $label);
	}

	public function createManagedProperty(AccessProxy $accessProxy): ManagedProperty {
		return new ScalarProperty($accessProxy, false);
	}

	public function putDisplayable($n2nLocaleId, Displayable $displayable, FieldErrorInfo $fieldErrorInfo) {
		$this->displayables[$n2nLocaleId] = $displayable;
		$this->fieldErrorInfos[$n2nLocaleId] = $fieldErrorInfo;
	}

	public function putMagPropertyPath($n2nLocaleId, PropertyPath $magPropertyPath, FieldErrorInfo $fieldErrorInfo) {
		$this->magPropertyPaths[$n2nLocaleId] = $magPropertyPath;
		$this->fieldErrorInfos[$n2nLocaleId] = $fieldErrorInfo;
	}

	/* (non-PHPdoc)
	 * @see \n2n\web\dispatch\mag\Mag::setupBindingDefinition()
	 */
	public function setupBindingDefinition(BindingDefinition $bd) {
		$basePropertyPath = $bd->getPropertyPath()->reduced(1);
		
		$that = $this;
		$bd->closure(function (BindingErrors $be) use ($that, $basePropertyPath, $bd) {
			foreach ($that->magPropertyPaths as $n2nLocaleId => $magPropertyPath) {
				$propertyPath = $basePropertyPath->ext(new PropertyPathPart('dispatchables', true, $n2nLocaleId))
						->ext($magPropertyPath);
				
				$tPropertyPath = $propertyPath->reduced(1);
				if (!$bd->getBindingTree()->containsPropertyPath($tPropertyPath)) continue;
				
				$transDispBd = $bd->getBindingTree()->lookup($tPropertyPath);
				$be->addErrors($that->propertyName, $transDispBd->getMappingResult()
						->filterErrorMessages($propertyPath->getLast(), true));
			}
		});
	}

	public function createUiField(PropertyPath $propertyPath, HtmlView $view): UiComponent {
		$basePropertyPath = $propertyPath->reduced(2);

		$propertyPaths = array();
		foreach ($this->magPropertyPaths as $n2nLocaleId => $magPropertyPath) {
			$propertyPaths[$n2nLocaleId] = $basePropertyPath->ext(new PropertyPathPart('dispatchables', true, $n2nLocaleId))
					->ext($magPropertyPath);
		}

		return $view->getImport('\rocket\spec\ei\component\field\impl\translation\view\option.html', 
				array('propertyPaths' => $propertyPaths, 'fieldErrorInfos' => $this->fieldErrorInfos));
	}
}
