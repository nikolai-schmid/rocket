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
namespace rocket\spec\ei\component\field\impl\string\wysiwyg\bbcode\definitionset;

/**
 * Provides a default set of common bbcode definitions.
 *
 * @author jbowens
 */
use rocket\spec\ei\component\field\impl\string\wysiwyg\bbcode\CodeDefinitionBuilder;

use rocket\spec\ei\component\field\impl\string\wysiwyg\bbcode\validators\CssColorValidator;

use rocket\spec\ei\component\field\impl\string\wysiwyg\bbcode\validators\UrlValidator;
use rocket\spec\ei\component\field\impl\string\wysiwyg\bbcode\CodeDefinitionSet;

class DefaultCodeDefinitionSet implements CodeDefinitionSet
{

	/* The default code definitions in this set. */
	protected $definitions = array();

	/**
	 * Constructs the default code definitions.
	 */
	public function __construct()
	{
		/* [b] bold tag */
		$builder = new CodeDefinitionBuilder('b', '<strong>{param}</strong>');
		array_push($this->definitions, $builder->build());

		/* [i] italics tag */
		$builder = new CodeDefinitionBuilder('i', '<em>{param}</em>');
		array_push($this->definitions, $builder->build());

		/* [u] italics tag */
		$builder = new CodeDefinitionBuilder('u', '<u>{param}</u>');
		array_push($this->definitions, $builder->build());

		$urlValidator = new UrlValidator();

		/* [url] link tag */
		$builder = new CodeDefinitionBuilder('url', '<a href="{param}">{param}</a>');
		$builder->setParseContent(false)->setBodyValidator($urlValidator);
		array_push($this->definitions, $builder->build());

		/* [url=http://example.com] link tag */
		$builder = new CodeDefinitionBuilder('url', '<a href="{option}">{param}</a>');
		$builder->setUseOption(true)->setParseContent(true)->setOptionValidator($urlValidator);
		array_push($this->definitions, $builder->build());

		/* [img] image tag */
		$builder = new CodeDefinitionBuilder('img', '<img src="{param}" />');
		$builder->setUseOption(false)->setParseContent(false)->setBodyValidator($urlValidator);
		array_push($this->definitions, $builder->build());

		/* [img=alt text] image tag */
		$builder = new CodeDefinitionBuilder('img', '<img src="{param} alt="{option}" />');
		$builder->setUseOption(true);
		array_push($this->definitions, $builder->build());

		/* [color] color tag */
		$builder = new CodeDefinitionBuilder('color', '<span style="color: {option}">{param}</span>');
		$builder->setUseOption(true)->setOptionValidator(new CssColorValidator());
		array_push($this->definitions, $builder->build());
	}

	/**
	 * Returns an array of the default code definitions.
	 */
	public function getCodeDefinitions()
	{
		return $this->definitions;
	}

}
