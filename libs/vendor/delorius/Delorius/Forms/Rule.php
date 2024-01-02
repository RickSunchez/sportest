<?php
namespace Delorius\Forms;


use Delorius\Core\Object;

/**
 * Single validation rule or condition represented as value object.
 *
 *
 */
final class Rule extends Object
{
	/** type */
	const CONDITION = 1;

	/** type */
	const VALIDATOR = 2;

	/** type */
	const FILTER = 3;

	/** @var IControl */
	public $control;

	/** @var mixed */
	public $operation;

	/** @var mixed */
	public $arg;

	/** @var int (CONDITION, VALIDATOR, FILTER) */
	public $type;

	/** @var bool */
	public $isNegative = FALSE;

	/** @var string (only for VALIDATOR type) */
	public $message;

	/** @var Rules (only for CONDITION type)  */
	public $subRules;

}
