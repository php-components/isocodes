<?php
require_once __DIR__ . '/JsonSchema.php';

class InterfaceBuilder
{
    /**
     * @var JsonSchema
     */
    protected $schema;
    
    protected $namespace;
    
    protected $objectName;
    
    protected $buildingInterface = false;
    
    protected $buildSetters = false;
    
    protected $maxVarLength = 0;
    protected $maxNormalizedVarLength = 0;
    
    /**
     * Write the PHP header 
     */
    protected function getPhpHeader()
    {
        $out  = "<?php\n";
        
        // Comments
        $out .= "/**\n";
        $out .= " * " . $this->schema->getTitle() . "\n";
        $out .= " * \n";
        $out .= " * " . $this->schema->getDescription() . "\n";
        $out .= " * \n";
        $out .= " * Copyright (c) 2016 Juan Pedro Gonzalez Gutierrez\n";
        $out .= " * \n";
        $out .= " * This program is free software; you can redistribute it and/or\n";
        $out .= " * modify it under the terms of the GNU Lesser General Public\n";
        $out .= " * License as published by the Free Software Foundation; either\n";
        $out .= " * version 2.1 of the License, or (at your option) any later version.\n";
        $out .= " * \n";
        $out .= " * This program is distributed in the hope that it will be useful,\n";
        $out .= " * but WITHOUT ANY WARRANTY; without even the implied warranty of\n";
        $out .= " * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU\n";
        $out .= " * Lesser General Public License for more details.\n";
        $out .= " * \n";
        $out .= " * You should have received a copy of the GNU Lesser General Public\n";
        $out .= " * License along with this program; if not, write to the Free Software\n";
        $out .= " * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA\n";
        $out .= " */\n";
        
        return $out;
    }
    
    /**
     * Load a schema.
     * 
     * @param unknown $schema
     */
    public function loadSchema(JsonSchema $schema, $namespace = null, $objectName = null)
    {
        $this->schema = $schema;
        
        $normalizedISO = preg_replace('/\-/', '_', $schema->getIso());
        
        if (!$namespace) {
            $namespace = "ISOCodes\\ISO" . $normalizedISO . "\\Model";
        }
        $this->setNamespace($namespace);
        
        if (!$objectName) {
            $objectName = 'ISO' . $normalizedISO;
        }
        $this->objectName = $objectName;
    }
    
    /**
     * Build the interface.
     * 
     * @param unknown $out
     */
    public function build($interface = false, $getters = true, $setters = false)
    {
        // Store if we are building an interface or the object class
        $this->buildingInterface = $interface;
        $this->buildSetters = $setters;
        
        // prepare padding
        $this->getMaxVariableLegth();
        
        $out  = $this->getPhpHeader();
        $out .= "namespace " . $this->namespace . ";\n\n";
        
        if (!$this->buildingInterface) {
            $out .= "use ISOCodes\\I18n\\Translator;\n";
            // TODO: Works on linux?
            $out .= "use " . dirname($this->namespace) . "\\Exception;\n\n";
        }
        
        if ($this->buildingInterface) {
            $out .= "interface " . $this->objectName . "Interface\n";
        } else {
            $out .= "class " . $this->objectName . " implements " . $this->objectName . "Interface\n";
        }
        $out .= "{\n";
        
        // Build the variables
        if (!$this->buildingInterface) {
            // Translator variable
            $out .= "    /**\n";
            $out .= "     * The translator\n";
            $out .= "     * \n";
            $out .= "     * @var Translator\n";
            $out .= "     */\n";
            $out .= "    protected \$_translator;\n\n";
            
            foreach ($this->schema->getProperties() as $name => $value) {
                $out .= $this->buildVariable($name, $value);
            }   
        }
        
        // Build magic
        if (!$this->buildingInterface) {
            $out .= $this->buildMagicGetter();
            $out .= $this->buildMagicIsSet();
            $out .= $this->buildMagicSetter();
            
            // ...and the translate method
            $out .= $this->buildTranslateMethod();
        }
        
        if ($getters) {
            foreach ($this->schema->getProperties() as $name => $value) {
                $out .= $this->buildGetter($name, $value);
            }
        }
        
        if ($setters) {
            foreach ($this->schema->getProperties() as $name => $value) {
                $out .= $this->buildSetter($name, $value);
            }
        }
        
        if (!$this->buildingInterface) {
            // build the '::exhangeArray()' method
            $out .= $this->buildExchangeArrayMethod();
            
            // build the '::getArrayCopy()' method
            $out .= $this->buildGetArrayCopyMethod();
        }
        
        $out .= "}";
        
        return $out;
    }
    
    /**
     * Build the ::exchangeArray method.
     * 
     * @return string
     */
    protected function buildExchangeArrayMethod()
    {
        $out  = "    /**\n";
        $out .= "     * Exchange the array with this object\n";
        $out .= "     * \n";
        $out .= "     * @param array \$input\n";
        $out .= "     */\n";
        $out .= "     public function exchangeArray(\$input)\n";
        $out .= "     {\n";
        
        // Calculate padding
        $maxVarLength = 0;
        foreach ($this->schema->getProperties() as $name => $value) {
            if (strlen($name) > $maxVarLength) {
                $maxVarLength = strlen($name);
            }
        }
        
        // Making sure all the data is present...
        $out .= "        // make sure we have all the data\n";
        foreach ($this->schema->getProperties() as $name => $value) {            
            $out .= "        \$input['" . $name . "']" . $this->getPadding($name, false) ." = isset(\$input['" . $name . "'])" . $this->getPadding($name, false) ." ? \$input['" . $name . "']" . $this->getPadding($name, false) ." : null;\n";
        }
        
        $out .= "\n";
        
        if ($this->buildSetters) {
            foreach ($this->schema->getProperties() as $name => $value) {
                $out .= "        \$this->set" . ucfirst($this->normalizeVarName($name)) . "(\$input['" . $name . "']);\n";
            }    
        } else {
            foreach ($this->schema->getProperties() as $name => $value) {
                $hasChecks = false;
                
                if ($this->schema->isRequired($name)) {
                    $out .= "        if (empty(\$input['" . $name . "'])) {\n";
                    $out .= "            throw new Exception\\InvalidArgumentException('" . $name . " is a required property an can not be empty.');\n";
                    $out .= "        }\n";
                    $hasChecks = true;
                }
                
                if (isset($value['pattern']) && !empty($value['pattern'])) {
                    $out .= "        if (!preg_match('/" . $value['pattern'] . "/', \$input['" . $name . "'])) {\n";
                    $out .= "            throw new Exception\\InvalidArgumentException('" . $name . " has an invalid value.');\n";
                    $out .= "        }\n";
                    $hasChecks = true;
                }
                
                if ($hasChecks) {
                    $out .= "\n";
                }
            }
            
            foreach ($this->schema->getProperties() as $name => $value) {                
                $out .= "        \$this->" . $this->normalizeVarName($name) . $this->getPadding($name, true) . " = \$input['" . $name . "'];\n";
            }
        }
        
        
        $out .= "     }\n\n";
        
        return $out;
    }
    
    /**
     * Build the ::getArrayCopy() method
     * 
     * @return string
     */
    protected function buildGetArrayCopyMethod()
    {
        $out  = "    /**\n";
        $out .= "     * Creates a copy of the object as an array.\n";
        $out .= "     * \n";
        $out .= "     * @return array\n";
        $out .= "     */\n";
        $out .= "    public function getArrayCopy()\n";
        $out .= "    {\n";
        $out .= "        return array(\n";
        
        foreach ($this->schema->getProperties() as $name => $value) {
            $out .= "            '" . $name . "'" . $this->getPadding($name, false) . " => \$this->" . $this->normalizeVarName($name) . ",\n";
        }
        
        $out .= "        );\n";
        $out .= "    }\n\n";
        
        return $out;
    }
    
    /**
     * Build a getter function for the given variable.
     * 
     * @param string $var
     * @param array $options
     */
    protected function buildGetter($var, $options = []) {
        $var        = $this->normalizeVarName($var);
        $methodName = 'get' . ucfirst($var);
        
        $out  = "    /**\n";
        if (isset($options['description']) && !empty($options['description'])) {
            $out .= "     * Get " . lcfirst($options['description']) . "\n";
            $out .= "     * \n";
        }
        if (isset($options['type']) && !empty($options['type'])) {
            $out .= "     * @return " . $options['type'] . "\n";
        } else {
            $out .= "     * @return mixed\n";
        }
        $out .= "     */\n";
        if ($this->buildingInterface) {
            $out .= "    public function get" . ucfirst($var) . "();\n\n";
        } else {
            $out .= "    public function get" . ucfirst($var) . "()\n";
            $out .= "    {\n";
            $out .= "        return \$this->" . $var . ";\n";
            $out .= "    }\n\n";
        }
        
        return $out;
    }
    
    /**
     * Build the magic __get() method
     */
    protected function buildMagicGetter()
    {
        if ($this->buildingInterface) {
            return;
        }
        
        $out  = "    /**\n";
        $out .= "     * Magic method utilized for reading data from inaccessible properties.\n";
        $out .= "     * \n";
        $out .= "     * @param string \$name\n";
        $out .= "     * @return mixed\n";
        $out .= "     */\n";
        $out .= "    public function __get(\$name)\n";
        $out .= "    {\n";
        $out .= "        switch(\$name)\n";
        $out .= "        {\n";
        
        // The translator
        $out .= "            case '_translator':\n";
        $out .= "                return \$this->_translator;\n";
        $out .= "                break;\n";
        
        // All variables
        foreach ($this->schema->getProperties() as $name => $value) {
            $normalized = $this->normalizeVarName($name);
            
            $out .= "            case '" . $name . "':\n";
            if (strcmp($name, $normalized) !== 0) {
                $out .= "            case '" . $normalized . "':\n";
            }
            $out .= "                return \$this->" . $normalized . ";\n";
            $out .= "                break;\n";
        }
        $out .= "        }\n\n";
        // By default if no property is there it will reply
        // Notice: Undefined property: Algo::$otro in...
        
        $out .= "        trigger_error(sprintf('Undefined property: %s::\$%s', __CLASS__, \$name), E_USER_NOTICE);\n";
        
        $out .= "    }\n\n";
        
        return $out;
    }
    
    /**
     * Build the magic __isset() method
     */
    protected function buildMagicIsSet()
    {
        if ($this->buildingInterface) {
            return;
        }
    
        $out  = "    /**\n";
        $out .= "     * Magic method triggered by calling isset() or empty() on inaccessible properties.\n";
        $out .= "     * \n";
        $out .= "     * @param string \$name\n";
        $out .= "     * @return bool\n";
        $out .= "     */\n";
        $out .= "    public function __isset(\$name)\n";
        $out .= "    {\n";
        $out .= "        switch(\$name)\n";
        $out .= "        {\n";
    
        // The translator
        $out .= "            case '_translator':\n";
        $out .= "                if  (isset(\$this->translator) && (\$this->_translator instanceof Translator)) {\n";
        $out .= "                    return true;\n";
        $out .= "                }\n";
        $out .= "                return false;\n";
        $out .= "                break;\n";
    
        // All variables
        foreach ($this->schema->getProperties() as $name => $value) {
            $normalized = $this->normalizeVarName($name);
    
            $out .= "            case '" . $name . "':\n";
            if (strcmp($name, $normalized) !== 0) {
                $out .= "            case '" . $normalized . "':\n";
            }
            $out .= "                return isset(\$this->" . $normalized . ");\n";
            $out .= "                break;\n";
        }
        $out .= "        }\n\n";
        
        $out .= "        return false;\n";
    
        $out .= "    }\n\n";
    
        return $out;
    }
    
    /**
     * Build the magic __get() method
     */
    protected function buildMagicSetter()
    {
        if ($this->buildingInterface) {
            return;
        }
    
        $out  = "    /**\n";
        $out .= "     * Magic method runned when writing data to inaccessible properties.\n";
        $out .= "     * \n";
        $out .= "     * @param string \$name\n";
        $out .= "     * @param mixed \$value\n";
        $out .= "     * @return void\n";
        $out .= "     */\n";
        $out .= "    public function __set(\$name, \$value)\n";
        $out .= "    {\n";
        $out .= "        switch(\$name)\n";
        $out .= "        {\n";
    
        // The translator
        $out .= "            case '_translator':\n";
        $out .= "                if (!\$value instanceof Translator) {\n";
        $out .= "                    throw new Exception\InvalidArgumentException(sprintf(\n";
        $out .= "                        '%s::\$%s must be of the type ISOCodes\\I18n\\Translator, %s given',\n";
        $out .= "                        __CLASS__,\n";
        $out .= "                        \$name,\n";
        $out .= "                        (is_object(\$value) ? get_class(\$value) : gettype(\$value))\n";
        $out .= "                    ));\n";
        $out .= "                }\n";
        $out .= "                \$this->_translator = \$value;\n";
        $out .= "                return;\n";
        $out .= "                break;\n";
    
        // All variables
        foreach ($this->schema->getProperties() as $name => $value) {
            $normalized = $this->normalizeVarName($name);
    
            $out .= "            case '" . $name . "':\n";
            if (strcmp($name, $normalized) !== 0) {
                $out .= "            case '" . $normalized . "':\n";
            }
            if ($this->buildSetters) {
                $out .= "                \$this->set" . ucfirst($normalized) . "(\$value);\n";
                $out .= "                return;\n";
                $out .= "                break;\n";
            }
        }
        
        if (!$this->buildSetters) {
            $out .= "                trigger_error(sprintf('Cannot access protected property %s::\$%s', __CLASS__, \$name), E_USER_ERROR);\n";
            $out .= "                return;\n";
            $out .= "                break;\n";
        }
        
        $out .= "        }\n\n";
        
        // By default if no property is there it will write
        // $out .= "        \$this->\$name = \$value;\n";
        // but we don't want this to happen so:
        $out .= "        trigger_error(sprintf('Undefined property: %s::\$%s', __CLASS__, \$name), E_USER_NOTICE);\n";
    
        $out .= "    }\n\n";
    
        return $out;
    }
    
    /**
     * Build a setter function for the given variable.
     *
     * @param string $var
     * @param array $options
     */
    protected function buildSetter($var, $options = []) {
        $normalized = $this->normalizeVarName($var);
    
        $out  = "    /**\n";
        if (isset($options['description']) && !empty($options['description'])) {
            $out .= "     * Set " . lcfirst($options['description']) . "\n";
            $out .= "     * \n";
        }
        if (isset($options['type']) && !empty($options['type'])) {
            $out .= "     * @param " . $options['type'] . " \$" . $normalized . "\n";
        } else {
            $out .= "     * @param mixed\n";
        }
        $out .= "     */\n";
        if ($this->buildingInterface) {
            $out .= "    public function set" . ucfirst($normalized) . "($" . $normalized . ")\n\n";
        } else {
            $out .= "    public function set" . ucfirst($normalized) . "($" . $normalized . ")\n";
            $out .= "    {\n";
            
            if ($this->schema->isRequired($var)) {
                $out .= "        if (empty(\$" . $normalized . ")) {\n";
                $out .= "            throw new Exception\\InvalidArgumentException('" . $normalized . " is a required property an can not be empty.');\n";
                $out .= "        }\n\n";
            }
            
            if (isset($options['pattern']) && !empty($options['pattern'])) {
                $out .= "        if (!preg_match('/" . $options['pattern'] . "/', \$" . $normalized . ")) {\n";
                $out .= "            throw new Exception\\InvalidArgumentException('" . $normalized . " has an invalid value.');\n";
                $out .= "        }\n\n";
            }
            
            
            $out .= "        \$this->" . $normalized . " = \$" . $normalized . ";\n"; 
            $out .= "        return \$this;\n";
            $out .= "    }\n\n";
        }
    
        return $out;
    }
    
    /**
     * Since we cannot tell for sure which fields are translatable we add
     * a protected method to perform the translations. This methods sets
     * the text domain for the class.
     */
    protected function buildTranslateMethod()
    {
        if ($this->buildingInterface) {
            return;
        }
        
        // Text domain
        $textDomain = strtolower($this->schema->getIso());
        $textDomain = preg_replace('/_/', '-', $textDomain);
        $textDomain = 'iso-' . $textDomain;
        
        $out  = "    /**\n";
        $out .= "     * Get a translation for a message from the translator.\n";
        $out .= "     * \n";
        $out .= "     * TODO: The class generator cannot tell for sure which fields are\n";
        $out .= "     *       translatable so you must hook them by hand or the translator\n";
        $out .= "     *       will be of no use.\n";
        $out .= "     * \n";
        $out .= "     * @param string      \$message The message to translate\n";
        $out .= "     * @param string|null \$locale  The locale of the translation or null for the default locale.\n";
        $out .= "     * @return string\n";
        $out .= "     */\n";
        $out .= "    protected function _translate(\$message, \$locale = null)\n";
        $out .= "    {\n";
        $out .= "        if (!\$this->_translator instanceof Translator) {\n";
        $out .= "            return \$message;\n";
        $out .= "        }\n\n";
        $out .= "        return \$this->_translator->translate(\$message, '" . $textDomain . "', \$locale);\n";
        $out .= "    }\n";
        
        return $out;
    }
    
    /**
     * Build a variable.
     * 
     * @param string $var
     */
    protected function buildVariable($var, $options = [])
    {
        // Nornalize variable
        $var = $this->normalizeVarName($var);
        
        $out  = "    /**\n";
        if (isset($options['description'])) {
            $out .= "     * " . $options['description'] . "\n";
            $out .= "     * \n";
        }
        if (isset($options['type'])) {
            $out .= "     * @var " . $options['type'] . "\n";
        } else {
            $out .= "     * @var mixed\n";
        }
        $out .= "     */\n";
        $out .= "    protected \$" . $var . ";\n\n";
        
        return $out;
    }
    
    /**
     * Get the maximun variable length (used for padding)
     * 
     * @return int;
     */
    protected function getMaxVariableLegth()
    {
        $this->maxVarLength = 0;
        $this->maxNormalizedVarLength = 0;
        
        foreach($this->schema->getProperties() as $name => $value) {
            if (strlen($name) > $this->maxVarLength) {
                $this->maxVarLength = strlen($name);
            }
            
            $normalized = $this->normalizeVarName($name);
            if (strlen($normalized) > $this->maxNormalizedVarLength) {
                $this->maxNormalizedVarLength = strlen($normalized);
            }
        }    
    }
    
    /**
     * Get padding for a variable.
     * 
     * @param string $var
     * @param bool $normalized
     * @return string
     */
    protected function getPadding($var, $normalized = false)
    {
        $pad = '';
        
        if ($normalized) {
            $var       = $this->normalizeVarName($var);
            $maxLength = $this->maxNormalizedVarLength;
        } else {
            $maxLength = $this->maxVarLength;
        }
        
        
        if (strlen($var) < $maxLength) {
            for($i = 0; $i < ($maxLength - strlen($var)); $i++) {
                $pad .= ' ';
            }
        }
        
        return $pad;
    }
    
    /**
     * Normalize the variable name to camel case and first letter lower case.
     * 
     * @param string $var
     * @return string
     */
    protected function normalizeVarName($var)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $var))));
    }
    
    /**
     * Set the object name
     * 
     * @param string $name
     */
    public function setObjectName($name)
    {
        $this->objectName = $name;
        return $this;
    }
    
    /**
     * Set the namespace.
     * 
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }
}