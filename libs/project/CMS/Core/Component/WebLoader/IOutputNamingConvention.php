<?php

namespace CMS\Core\Component\WebLoader;

/**
 * IOutputNamingConvention
 *
 * @author Jan Marek
 */
interface IOutputNamingConvention
{

	public function getFilename(array $files, Compiler $compiler);

}
