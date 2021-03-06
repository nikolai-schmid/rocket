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
namespace rocket\spec\ei\component\field\impl\string\wysiwyg;

use n2n\impl\web\ui\view\html\HtmlView;

class WysiwygCssConfigExample implements WysiwygCssConfig {
	public function getContentCssPaths(HtmlView $view) {
		return array('assets/rocket/css/style.css', 'assets/rocket/css/style2.css');
	}
	
	public function getBodyClass() {
		return 'rocket-wysiwyg-test';
	}
	
	public function getBodyId() {
		return 'rocket-wysiwyg-test';
	}

	public function getAdditionalStyles() {
		return array(new WysiwygStyle('Attention Box', 'div', array('class' => 'box-attention')));
	}
	
	public function getFormatTags() {
		return array("address");
	}

}
