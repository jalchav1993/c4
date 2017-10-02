<?php
class Move{
	public $slot;
	public $isWin;
	public $isDraw;
	public $row;
	public function __construct($slot, $isWin, $isDraw, $row){
		$this->slot = $slot;
		$this->isWin = $isWin;
		$this->isDraw = $isDraw;
		$this->row=$row;
	}
}
class Board{
	public $stackTrace = array();
	public $USE_STRATEGY_OPTION = 'strategy';
	private $rows;
	private $grid;
	private $stack;
	private $drawCount;
	private $step;
	private $rowConstraint;
	private $colConstraint;
	private $rowCount;
	private $attackCount;
	private $attackParity;
	private $attackSlot;
	private $isWin;
	private $isDraw;
	public function __construct($grid){
		$this->grid = $grid;
		$this->stack = array();
		$this->drawCount = 0;
		$this->rows = array("diagonalL", "horizontal", "vertical", "diagonalR");
		$this->step = 0;
		$this->rowConstraint = 0;
		$this->colConstraint = 0; 
		$this->rowCount = 0;
		$this->attackCount = 0;
		$this->attackParity = 'low-row';
		$this->attackSlot = 0;
		$this->isWin = false;
		//echo var_dump($grid);
	}
	public function printStackTrace(){
		$pidFile= fopen("../writable/stacktrace".rand(100,200), "w");
		$item = array_pop($this->stackTrace);
		while($item!= NULL){
			fputs($pidFile, $item."\n");
			$item = array_pop($this->stackTrace);
		}
		fclose($pidFile);
	}
	public function getGrid(){
		return $this->grid;
	}
	public function addToken($slot, $player, $strategy){
		$token = $player;
		if($strategy){
			if($this->grid->strategy == 'Smart'){
				$slot = $this->attackSlot;
			}
				
			else{
				$slot = (int) (rand(0,6));
			}
		}
		$nextSlot = $this->grid->nextSlots[$slot%7];
		$this->grid->nextSlots[$slot%7]+=7;
		if($this->grid->nextSlots[$slot%7]>41){
			$this->grid->nextSlots[$slot%7] = 'full';
			$this->drawCount ++;
			return new Move(rand(0,6),$this->isWin(), $this->isDraw(), $this->getStack());
		}
		if($nextSlot=='full'){
			return new Move(rand(0,6),$this->isWin(), $this->isDraw(), $this->getStack());
		}
		$this->grid->tokenSet[$nextSlot] = $token;
		$this->grid->topSlots[$slot%7]=$nextSlot;
		$this->checkBoard($token, $nextSlot);
		$this->printStackTrace();
		return new Move($slot%7,$this->isWin(), $this->isDraw(), $this->getStack());
	}
	private function checkBoard($token, $slot){
		$this->attackCount = 0;
		$this->stack = array();
		$topSlots = $this->grid->topSlots;
		foreach ($this->rows as $row){
			foreach ($topSlots as $slot){
				if($slot!='_'){
					$last = $this->getLastSlotForRow($row, $slot);
					array_push($this->stackTrace, "last($last) = g etLastSlotForRow(row = $row, slot = $slot)");
					$this->buildStack($last, $token, $slot);
				}
			}
		}
		
	}
	private function saveStackTrace(){
		
	}
	private function buildStack($last, $token, $slot){
		array_push($this->stackTrace, "building b uildStack($last, $token, $slot)" );
		$count = 0;
		$attack = 0;
		$attack_parity= 'even';
		$rowSelect = $this->getRowConstraint();
		$colSelect = $this->getColConstraint();
		$this->setStep($slot);
		$current = $last;
		$stack = array();
		$offset = 8;
		$attackSpace = true;
		while($offset>0 && $current < 41){
			if($this->grid->tokenSet[$current] == $token){
				$count++;
				array_push($this->stackTrace, "b uildStack($last, $token, $slot) count= $count" );
				array_push($stack, $current);
				array_push($stack, (int) ($current/10));
				if($count >=4) break;
				if($count == 3){
					$this->attackParity = $token == $this->grid->cpuPlayer?'odd-high-row':'odd-high-block';
					$this->attackCount = $count;
					$this->attackSlot = $current+7;
					//echo $this->attackSlot;
				}
			} else{
				if($current<$slot&&$attackSpace){
					array_push($this->stackTrace, "b uildStack($last, $token, $slot) current = $current, breaking" );
					$attackSpace = false;
				}else{
					array_push($this->stackTrace, "b uildStack($last, $token, $slot) current = $current, count = $count" );
					$count = 0;
				}
			}
			array_push($this->stackTrace, "current = $current");
			$current+=$rowSelect;
			$offset--;
			if($current%7==$colSelect){
				break;
			}
		};
		if($count>= 4){
			$this->isWin = true;
			$this->stack=$stack;
		}else if($this->attackCount < $count && $count>2){
			$this->attackParity = $token == $this->grid->cpuPlayer?'odd-high-row':'odd-high-block';
			$this->attackCount = $count;
			$this->attackSlot = $current;
			array_push($this->stackTrace, $token);
		}
		array_push($this->stackTrace, $this->attackParity);
	}
	private function setWin($option){
		$this->isWin = $option;
	}
	private function hasSupport(){
		
	}
	private function getLastSlotForRow($row, $slot){
		$this->setStep($slot);
		$this->setConstraints($row);
		$colSelect = $this->getColConstraint();
		$rowSelect = $this->getRowConstraint();
		while ($slot%7!=$colSelect&&$this->getStep() > 0){
			$slot -= $rowSelect;
			$this->setStep('decrement');
		}
		array_push($this->stackTrace, "function g etLastSlotForRow(row = $row, slot = $slot)");
		return $slot;
	}
	private function setConstraints($row){
		if($this->getStep() >= 4||$row =='horizontal') $this->setStep('3');
		if($row == 'diagonalL'){
		 	$colSelect = 0;
		 	$rowSelect = 8;
		 } else if($row == 'horizontal'){
		 	$colSelect = 0;
		 	$rowSelect = 1;
		 } else if($row == 'vertical'){
		 	$colSelect = -1;
		 	$rowSelect = 7;
		 }else if($row == 'diagonalR'){
		 	$colSelect = 6;
		 	$rowSelect = 6;
		 }
		 $this->setColConstraint($colSelect);
		 $this->setRowConstraint($rowSelect);
	}
	private function setRowConstraint($constraint){
		$this->rowConstraint = $constraint;
	}
	private function setColConstraint($constraint){
		$this->colConstraint = $constraint;
	}
	private function setStep($option){
		if($option === 'decrement'){
			$this->step--;
		}else if ($option === '3'){
			$this->step = 3;
		}
		else{
			$this->step= (int)($option/7);
		}
	}
	private function getStep(){
		return $this->step;
	}
	private function isDraw(){
		if($this->drawCount > 6) return true;
		return false;
	}
	private function isWin(){
		return $this->isWin;
	}
	private function getStack(){
		return $this->stack;
	}
	private function getColConstraint(){
		return $this->colConstraint;
	}
	private function getRowConstraint(){
		return $this->rowConstraint;
	}
}
?>