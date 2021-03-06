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

	use rocket\spec\ei\component\command\impl\common\model\AddModel;
	use rocket\spec\ei\manage\util\model\EntryFormViewModel;
	use rocket\spec\ei\component\command\impl\common\model\EntryCommandViewModel;
	use n2n\web\ui\Raw;
	use n2n\impl\web\ui\view\html\HtmlView;
	use n2n\impl\web\dispatch\ui\Form;

	$view = HtmlView::view($this);
	$html = HtmlView::html($this);
	$formHtml = HtmlView::formHtml($this);
	$request = HtmlView::request($this);

	$addModel = $view->params['addModel'];
	$view->assert($addModel instanceof AddModel);
	
	$entryCommandViewModel = $view->params['entryViewInfo'];
	$view->assert($entryCommandViewModel instanceof EntryCommandViewModel);
 
	$view->useTemplate('~\core\view\template.html', array('title' => $entryCommandViewModel->getTitle()));
?>

<?php $formHtml->open($addModel, Form::ENCTYPE_MULTIPART, 'post', array('class' => 'rocket-edit-form rocket-unsaved-check-form')) ?>
	<div class="rocket-panel">
		<h3><?php $html->l10nText('common_properties_title') ?></h3>
		
		<?php $view->import('~\spec\ei\manage\util\view\entryForm.html', 
				array('entryFormViewModel' => new EntryFormViewModel(
						$formHtml->meta()->createPropertyPath(array('entryForm'))))) ?>
			
		<div id="rocket-page-controls">
			<ul>
				<li>
					<?php $formHtml->buttonSubmit('create', new Raw('<i class="fa fa-save"></i><span>' 
									. $html->getL10nText('common_save_label') . '</span>'),
							array('class' => 'rocket-control-warning rocket-important')) ?>
				</li>
				<li>
					<?php $formHtml->buttonSubmit('createAndRepeate', new Raw('<i class="fa fa-save"></i><span>' 
									. $html->getL10nText('ei_impl_save_and_repeat_label') . '</span>'),
							array('class' => 'rocket-control-warning')) ?>
				</li>
				<li>
					<?php $html->link($entryCommandViewModel->determineCancelUrl($view->getHttpContext()), 
							new Raw('<i class=" icon-remove-circle"></i><span>'
									. $html->getL10nText('common_cancel_label') . '</span>'),
							array('class' => 'rocket-control')) ?>
				</li>
			</ul>
		</div>
	</div>
<?php $formHtml->close() ?>
