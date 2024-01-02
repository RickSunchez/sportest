<?php
namespace Delorius\Neon;

/**
 * Simple parser & generator for Delorius Object Notation.
 */
class Neon
{
	const BLOCK = Encoder::BLOCK;
	const CHAIN = '!!chain';


	/**
	 * Returns the NEON representation of a value.
	 * @param  mixed
	 * @param  int
	 * @return string
	 */
	public static function encode($var, $options = NULL)
	{
		$encoder = new Encoder;
		return $encoder->encode($var, $options);
	}


	/**
	 * Decodes a NEON string.
	 * @param  string
	 * @return mixed
	 */
	public static function decode($input)
	{
		$decoder = new Decoder;
		return $decoder->decode($input);
	}

}
