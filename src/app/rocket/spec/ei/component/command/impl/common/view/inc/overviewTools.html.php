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

	use n2n\impl\web\ui\view\html\HtmlView;
	use rocket\spec\ei\manage\critmod\filter\impl\controller\FilterAjahHook;
	use rocket\spec\ei\component\command\impl\common\controller\OverviewAjahHook;
	use rocket\spec\ei\manage\critmod\quick\impl\form\QuickSearchForm;
	use rocket\spec\ei\manage\critmod\impl\model\CritmodForm;
	
	$view = HtmlView::view($this);
	$html = HtmlView::html($this);
	$formHtml = HtmlView::formHtml($this);

	$overviewAjahHook = $view->getParam('overviewAjahHook');
	$view->assert($overviewAjahHook instanceof OverviewAjahHook);
	
	$filterAjahHook = $view->getParam('filterAjahHook');
	$view->assert($filterAjahHook instanceof FilterAjahHook);

	$critmodForm = $view->getParam('critmodForm');
	$view->assert($critmodForm instanceof CritmodForm);
	
	$quickSearchForm = $view->getParam('quickSearchForm');
	$view->assert($quickSearchForm instanceof QuickSearchForm);
?>

<div class="rocket-tool-panel rocket-overview-tools" 
		data-content-url="<?php $html->out($overviewAjahHook->getSelectUrl()) ?>"
		data-selected-label="<?php $html->text('common_selected_label')?>"
		data-selected-plural-label="<?php $html->text('common_selected_plural_label')?>"
		data-entries-label="<?php $html->out($view->getParam('label')) ?>"
		data-entries-plural-label="<?php $html->out($view->getParam('pluralLabel')) ?>">
	<?php $view->import('~\spec\ei\manage\critmod\impl\view\critmodForm.html', 
			array('critmodForm' => $critmodForm, 'critmodFormUrl' => $overviewAjahHook->getCritmodFormUrl(),
					'filterAjahHook' => $filterAjahHook)) ?>
	<?php $view->import('~\spec\ei\manage\critmod\quick\impl\view\quickSearchForm.html', 
			array('quickSearchForm' => $quickSearchForm)) ?>
</div>
