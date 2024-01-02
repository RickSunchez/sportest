HeaderControl for Nette Framework
===============

Author: Ondřej Mirtes (http://ondrej.mirtes.cz/) 2009
License: MIT

===============

Requirements:

- Nette Framework (0.9 or higher)
- WebLoader, CssLoader and JavascriptLoader by Jan Marek (http://janmarek.net/)

===============

This renderable component is ultimate solution for valid and complete HTML headers.

Example of component factory in Presenter:

<?php 
  protected function createComponentHeader()
  {
		$header = new HeaderControl;

		$header->setDocType(HeaderControl::HTML_5);
		$header->setLanguage('en');
		$header->setTitle('Example title');
		
		// facebook xml namespace
		$header->htmlTag->attrs['xmlns:fb'] = 'http://www.facebook.com/2008/fbml';

		$header->setTitleSeparator(' | ')
			->setTitlesReverseOrder(true)
			->addKeywords('one')
			->addKeywords(array('two', 'three'))
			->setDescription('Our example site')
			->setRobots('index,follow') //of course ;o)
			->addRssChannel('News', 'Rss:')
			->addRssChannel('Comments', 'Rss:comments');

		return $header;
	}
?>


===============

Future:
- phpDoc comments