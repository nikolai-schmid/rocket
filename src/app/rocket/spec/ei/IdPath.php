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
namespace rocket\spec\ei;

use n2n\reflection\ArgUtils;
use n2n\util\ex\IllegalStateException;
use n2n\util\col\Hashable;

abstract class IdPath implements Hashable {
	const ID_SEPARATOR = '.';
	
	protected $ids;
	
	public function __construct(array $ids) {
		$this->ids = array();
		foreach ($ids as $id) {
			ArgUtils::assertTrue(!$this->constainsSpecialIdChars($id));
			$this->ids[] = $id;
		}	
	}
	
	protected function ensureNotEmpty() {
		if (!$this->isEmpty()) return;
			
		throw new IllegalStateException((new \ReflectionClass($this))->getShortName() . ' is empty.');
	}
	
	public function hasMultipleIds() {
		return count($this->ids) > 1;
	}
	
	public function getFirstId() {
		$this->ensureNotEmpty();
		return reset($this->ids);
	}
	
	public function toArray() {
		return $this->ids;
	}
	
	public function isEmpty() {
		return empty($this->ids);
	}
	
	public function size() {
		return count($this->ids);
	}
	
	public function startsWith(IdPath $idPath): bool {
		$this->ensureNotEmpty();
		
		foreach ($idPath->ids as $key => $id) {
			if (!isset($this->ids[$key]) || $this->ids[$key] !== $id) return false;
		}
		
		return true;
	}
	
	protected function argsToIds(array $args) {
		$ids = array();
		foreach ($args as $arg) {
			if (is_array($arg)) {
				$ids = array_merge($ids, $this->argsToIds($arg));
				continue;
			}
			
			if ($arg instanceof IdPath) {
				$ids = array_merge($ids, $arg->toArray());
				continue;
			}
			
			$ids[] = $arg;
		}
		return $ids;
	}
	
	public static function constainsSpecialIdChars($str) {
		return (boolean) preg_match('#[^a-zA-Z0-9\\-]#', $str);
	}
	
	public static function stripSpecialIdChars($str) {
		return preg_replace('#[^a-zA-Z0-9\\-]#', '', $str);
	}
	
	public function toDbColumnName() {
		$this->ensureNotEmpty();
		return mb_strtolower(implode('_', $this->ids));
	}
	
	public function __toString(): string {
		return implode(self::ID_SEPARATOR, $this->ids);
	}
	
	public function hashCode(): string {
		return (string) $this;
	}
}
