<?php
namespace Delorius\Application\UI;

/**
 * Component with ability to save and load its state.
 */
interface IStateControl
{

	/**
	 * Loads state informations.
	 * @return void
	 */
	function loadState(array $params);

    /**
     * Saves state informations for next request.
     * @return void
     */
    function saveState(array & $params);

}
