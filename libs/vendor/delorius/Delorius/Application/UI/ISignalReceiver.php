<?php
namespace Delorius\Application\UI;

/**
 * Component with ability to receive signal.
 */
interface ISignalReceiver
{

	/**
	 * @param  string
	 * @return void
	 */
	function signalReceived($signal); // handleSignal

}
