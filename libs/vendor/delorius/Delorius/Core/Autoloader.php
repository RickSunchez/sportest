<?php
namespace Delorius\Core
{

    class Autoloader
    {
        private $class = array();
        private $namespaces = array();
        private $prefixes = array();
        private $namespaceFallbacks = array();
        private $prefixFallbacks = array();


        public function getNamespaces()
        {
            return $this->namespaces;
        }


        public function getPrefixes()
        {
            return $this->prefixes;
        }


        public function getNamespaceFallbacks()
        {
            return $this->namespaceFallbacks;
        }


        public function getPrefixFallbacks()
        {
            return $this->prefixFallbacks;
        }

        /*используется как запастной вариант каталогов */
        public function registerNamespaceFallbacks(array $dirs)
        {
            $this->namespaceFallbacks = $dirs;
        }

        /*используется как запастной вариант каталогов */
        public function registerPrefixFallbacks(array $dirs)
        {
            $this->prefixFallbacks = $dirs;
        }


        /*
        * Если классы которые требуется автоматически загружать используют пространства имён, применяйте методы
        *  $loader->registerNamespace('Symfony', __DIR__.'/vendor/symfony/src');
            $loader->registerNamespaces(array(
                'Symfony' => __DIR__.'/../vendor/symfony/src',
                'Monolog' => __DIR__.'/../vendor/monolog/src',
            ));
        */
        public function registerNamespaces(array $namespaces)
        {
            foreach ($namespaces as $namespace => $locations) {
                $this->namespaces[$namespace] = (array)$locations;
            }
        }


        public function registerNamespace($namespace, $paths)
        {
            $this->namespaces[$namespace] = (array)$paths;
        }


        /*
        * Для классов которые используют соглашения об именовании в стиле PEAR, используйте метод
        *  $loader->registerPrefix('Twig_', __DIR__.'/vendor/twig/lib');
            $loader->registerPrefixes(array(
                'Swift_' => __DIR__.'/vendor/swiftmailer/lib/classes',
                'Twig_'  => __DIR__.'/vendor/twig/lib',
            ));
        */
        public function registerPrefixes(array $classes)
        {
            foreach ($classes as $prefix => $locations) {
                $this->prefixes[$prefix] = (array)$locations;
            }
        }


        public function registerPrefix($prefix, $paths)
        {
            $this->prefixes[$prefix] = (array)$paths;
        }

        /*
        * Для классов которые используют соглашения об именовании в стиле PEAR, используйте метод
        *  $loader->registerClass('CssMin', __DIR__.'/vendor/cssmin/cssmin.php');
            $loader->registerClasss(array(
                'CssMin' => __DIR__.'/vendor/cssmin/cssmin.php',
                'JsMin' => __DIR__.'/vendor/cssmin/jsmin.php',
            ));
        */
        public function registerClasss(array $class)
        {
            foreach ($class as $cl => $locations) {
                $this->class[$cl] = $locations;
            }
        }


        public function registerClass($class, $paths)
        {
            $this->class[$class] = $paths;
        }

        protected function searchClass($file)
        {
            if (count($this->class)) {
                foreach ($this->class as $name => $loc) {
                    if ($name == $file)
                        return $loc;
                }
            }

            return false;
        }

        /* 
         * подключание файла из списков $this->class
         */
        public function importClass($file)
        {
            $filepath = $this->searchClass($file);
            if (file_exists($filepath))
                require_once($filepath);

        }


        public function register($prepend = false)
        {
            spl_autoload_register(array($this, 'loadClass'), true, $prepend);
        }

        public function unregister()
        {
            spl_autoload_unregister(array($this, 'loadClass'));
        }


        public function loadClass($class)
        {
            if ($file = $this->findFile($class)) {
                require $file;
            }
        }


        public function findFile($class)
        {
            $file = self::searchClass($class);
            if ($file)
                return $file;

            if ('\\' == $class[0]) {
                $class = substr($class, 1);
            }

            if (false !== $pos = strrpos($class, '\\')) {
                $namespace = substr($class, 0, $pos);
                foreach ($this->namespaces as $ns => $dirs) {
                    if (0 !== strpos($namespace, $ns)) {
                        continue;
                    }

                    foreach ($dirs as $dir) {
                        $className = substr($class, $pos + 1);
                        $file = $dir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
                        if (file_exists($file)) {
                            return $file;
                        }
                    }
                }

                foreach ($this->namespaceFallbacks as $dir) {
                    $file = $dir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
                    if (file_exists($file)) {
                        return $file;
                    }
                }
            } else {
                foreach ($this->prefixes as $prefix => $dirs) {
                    if (0 !== strpos($class, $prefix)) {
                        continue;
                    }

                    foreach ($dirs as $dir) {
                        $file = $dir . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
                        if (file_exists($file)) {
                            return $file;
                        }
                    }
                }

                foreach ($this->prefixFallbacks as $dir) {
                    $file = $dir . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
                    if (file_exists($file)) {
                        return $file;
                    }
                }
            }
        }
    }
}