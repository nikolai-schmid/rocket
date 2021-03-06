<?php
namespace rocket\spec\ei\manage\util\model;

use rocket\spec\ei\manage\EiState;
use n2n\core\container\N2nContext;
use rocket\spec\ei\manage\ManageException;
use n2n\web\http\HttpContextNotAvailableException;
use rocket\spec\ei\manage\util\model\EiuCtrl;
use n2n\reflection\ArgUtils;
use rocket\spec\ei\manage\EiSelection;
use rocket\spec\ei\manage\mapping\EiMapping;
use rocket\spec\ei\manage\LiveEntry;
use rocket\spec\ei\manage\LiveEiSelection;
use rocket\spec\ei\manage\draft\Draft;
use rocket\spec\ei\manage\DraftEiSelection;
use rocket\spec\ei\manage\model\EntryGuiModel;
use rocket\spec\ei\manage\ManageState;

class EiuFactory {
	const EI_ENTRY_TYPES = array(EiSelection::class, EiMapping::class, LiveEntry::class, Draft::class, EntryGuiModel::class);
	const EI_FRAME_TYPES = array(EiState::class, N2nContext::class);
	const EI_GUI_TYPES = array(EntryGuiModel::class);
	const EI_TYPES = array(EiState::class, N2nContext::class, EiSelection::class, EiMapping::class, LiveEntry::class, 
			Draft::class);
	
	private $eiState;
	private $n2nContext;
	private $eiSelection;
	private $eiMapping;
	private $viewMode;
	
	private $eiuCtrl;
	private $eiuFrame;
	private $eiuEntry;
	private $eiuGui;
	
	public function applyEiArgs(...$eiArgs) {
		foreach ($eiArgs as $key => $eiArg) {
			if ($eiArg instanceof EiState) {
				$this->eiState = $eiArg;
				continue;
			}
	
			if ($eiArg instanceof N2nContext) {
				$this->n2nContext = $eiArg;
				continue;
			}
			
			if (null !== ($eiSelection = self::determineEiSelection($eiArg, $this->eiMapping, $this->viewMode))) {
				$this->eiSelection = $eiSelection;
				continue;
			}
			
			if ($eiArg instanceof EiuGui) {
				$this->viewMode = $eiArg->getViewMode();
				$eiArg = $eiArg->getEiuEntry(false);
			}
			
			if ($eiArg instanceof EiuEntry) {
				$this->eiSelection = $eiArg->getEiSelection();
				$this->eiMapping = $eiArg->getEiMapping(false);
				$eiArg = $eiArg->getEiuFrame(false);
			}
			
			if ($eiArg instanceof EiuFrame) {
				$this->eiuCtrl = null;
				$this->eiuFrame = $eiArg;
				continue;
			}

			if ($eiArg instanceof EiuCtrl) {
				$this->eiuCtrl = $eiArg;
				$this->eiuFrame = $eiArg->getEiuFrame();
				continue;
			}
			
			ArgUtils::valType($eiArg, self::EI_TYPES, true, 'eiArg#' . ($key + 1));
		}
		
	}
	

	public function getEiuCtrl() {
		if ($this->eiuCtrl !== null) {
			return $this->eiuCtrl;
		}
		
		$eiuFrame = $this->getEiuFrame(true);
		try {
			return EiuCtrl::from($eiuFrame->getN2nContext()->getHttpContext(), $eiuFrame);
		} catch (HttpContextNotAvailableException $e) {
			throw new EiuPerimeterException('Can not create EiuCtrl.', 0, $e);
		}
	}
			
	/**
	 * @throws EiuPerimeterException
	 * @return \rocket\spec\ei\manage\util\model\EiuFrame
	 */
	public function getEiuFrame(bool $required) {
		if ($this->eiuFrame !== null) {
			return $this->eiuFrame;
		}
		
		if ($this->eiState !== null) {
			return $this->eiuFrame = new EiuFrame($this->eiState);
		} 
		
		if ($this->n2nContext !== null) {
			try {
				return new EiuFrame($this->n2nContext->lookup(ManageState::class)->peakEiState());
			} catch (ManageException $e) {
				throw new EiuPerimeterException('Can not create EiuFrame in invalid context.', 0, $e);
			}
		}
		
		if (!$required) return null;
		
		throw new EiuPerimeterException(
				'Can not create EiuFrame because non of the following types were provided as eiArgs: ' 
						. implode(', ', self::EI_FRAME_TYPES));
	}
	
	
	public function getEiuEntry(bool $required) {
		if ($this->eiuEntry !== null) {
			return $this->eiuEntry;
		}
		
		$eiuFrame = $this->getEiuFrame(false);
		
		if ($eiuFrame !== null) {
			if (null !== ($eiuEntry = $eiuFrame->getAssignedEiuEntry(false))) {
				return $this->eiuEntry = $eiuEntry;
			}
			
			if ($this->eiMapping !== null) {
				return $this->eiuEntry = $eiuFrame->assignEiuEntry($this->eiMapping);
			}
			
			if ($this->eiSelection !== null) {
				return $this->eiuEntry = $eiuFrame->assignEiuEntry($this->eiSelection);
			}
		} else {
			if ($this->eiMapping !== null) {
				return $this->eiuEntry = new EiuEntry($this->eiMapping);
			}
				
			if ($this->eiSelection !== null) {
				return $this->eiuEntry = new EiuEntry($this->eiSelection);
			}
		}
		
		if (!$required) return null;
		
		throw new EiuPerimeterException(
				'Can not create EiuFrame because non of the following types were provided as eiArgs: '
						. implode(', ', self::EI_ENTRY_TYPES));
	}
	

	/**
	 * @param bool $required
	 * @throws EiuPerimeterException
	 * @return \rocket\spec\ei\manage\util\model\EiuGui
	 */
	public function getEiuGui(bool $required) {
		if ($this->eiuGui !== null) {
			return $this->eiuGui;
		}
		
		if ($this->viewMode !== null) {
			$eiuEntry = $this->getEiuEntry(false);
			if ($eiuEntry !== null) {
				return $this->eiuGui = $eiuEntry->assignEiuGui($this->viewMode);
			} 
			
			return $this->eiuGui = new EiuGui($this->viewMode);
		}
		
		if (!$required) return null;
		
		throw new EiuPerimeterException(
				'Can not create EiuGui because non of the following types were provided as eiArgs: '
						. implode(', ', self::EI_GUI_TYPES));
	}
	
	public static function buildEiuFrameFormEiArg($eiArg, string $argName = null, bool $required = false) {
		if ($eiArg instanceof EiuFrame) {
			return $eiArg;
		}
		
		if ($eiArg === null && !$required) {
			return null;
		}
		
		if ($eiArg instanceof EiState) {
			return new EiuFrame($eiArg);
		}
		
		if ($eiArg instanceof N2nContext) {
			try {
				return new EiuFrame($eiArg->lookup(ManageState::class)->preakEiState());
			} catch (ManageException $e) {
				throw new EiuPerimeterException('Can not create EiuFrame in invalid context.', 0, $e);
			}
		}
		
		if ($eiArg instanceof EiuCtrl) {
			return $eiArg->getEiuFrame();
		}
		
		if ($eiArg instanceof EiuEntry) {
			return $eiArg->getEiuFrame($required);
		}
		
		ArgUtils::valType($eiArg, self::EI_FRAME_TYPES, !$required, $argName);
	}
	
	public static function buildEiuEntryFromEiArg($eiArg, EiuFrame $eiuFrame = null, string $argName = null, bool $required = false) {
		if ($eiArg instanceof EiuEntry) {
			return $eiArg;
		}
		
		if ($eiArg instanceof EiuFrame) {
			return $eiArg->getAssignedEiuEntry($required);
		}
		
		if ($eiArg !== null) {
			return new EiuEntry($eiArg, $eiuFrame);
		}
			
		if ($eiuFrame !== null) {
			return $eiuFrame->getAssignedEiuEntry($required);
		}
		
		if (!$required) {
			return null;
		}
		
		ArgUtils::valType($eiArg, self::EI_ENTRY_TYPES);
	}
	
	/**
	 * @param unknown $eiEntryObj
	 * @return rocket\spec\ei\manage\util\model\EiSelection
	 */
	public static function determineEiSelection($eiEntryObj, &$eiMapping, &$viewMode) {
		if ($eiEntryObj instanceof EiSelection) {
			return $eiEntryObj;
		} 
			
		if ($eiEntryObj instanceof EiMapping) {
			$eiMapping = $eiEntryObj;
			return $eiEntryObj->getEiSelection();
		}
		
		if ($eiEntryObj instanceof LiveEntry) {
			return new LiveEiSelection($eiEntryObj);
		}
		
		if ($eiEntryObj instanceof Draft) {
			return new DraftEiSelection($eiEntryObj);
		}
		
		if ($eiEntryObj instanceof EntryGuiModel) {
			$eiMapping = $eiEntryObj->getEiMapping();
			$viewMode = $eiEntryObj->getEiSelectionGui()->getViewMode();
			return $eiMapping->getEiSelection();
		} 
			
		return null;
	}
	
	public static function buildEiSelectionFromEiArg($eiEntryObj, string $argName = null, bool $required = true, &$eiMapping = null, &$viewMode = null) {
		if (!$required && $eiEntryObj === null) {
			return null;
		}
		
		if (null !== ($eiSelection = self::determineEiSelection($eiEntryObj, $eiMapping, $viewMode))) {
			return $eiSelection;
		}
		
		ArgUtils::valType($eiEntryObj, self::EI_ENTRY_TYPES, !$required, $argName);
	}
}