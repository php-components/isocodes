<?php
/**
 * schema: http://json-schema.org/draft-04/schema#
 */
class JsonSchema
{
    protected $id;
    protected $schema;
    protected $description;
    protected $definitions;
    protected $type;
    protected $properties;
    protected $dependencies;
    protected $default;
    protected $required;
    protected $title;
    
    protected $iso;
    
    public function __construct($filename, $iso)
    {
        $this->iso = $iso;
        
        $data = json_decode(file_get_contents($filename), true);
        
        // Parse root data
        $this->id           = isset($data['id'])           ? $data['id']           : null;
        $this->schema       = isset($data['schema'])       ? $data['schema']       : null;
        $this->description  = isset($data['description'])  ? $data['description']  : null;
        $this->definitions  = isset($data['definitions'])  ? $data['definitions']  : null;
        $this->type         = isset($data['type'])         ? $data['type']         : null;
        $this->dependencies = isset($data['dependencies']) ? $data['dependencies'] : null;
        $this->default      = isset($data['default'])      ? $data['default']      : null;
        $this->required     = isset($data['required'])     ? $data['required']     : null;
        $this->title        = isset($data['title'])        ? $data['title']        : null;
        
        /**
         * The PKG-ISO Codes doesn't seem to fully comply with the schema.
         * The ISO code is the key in the properties.
         */
        $properties = isset($data['properties']) ? $data['properties']   : null;
        if ($properties === null) {
            echo "\n\n";
            echo "Error: ISO-" . $iso . " has no properties.\n\n";
            die();
        }
    
        // There should only be one entry and that is the current iso key
        if (count($properties) !== 1) {
            echo "\n\n";
            echo "Error: Too many property entries in ISO-" . $iso . ".\n\n";
            die();
        }
        
        if (!isset($properties[$iso])) {
            echo "\n\n";
            echo "Error: Header not found in ISO-" . $iso . ".\n\n";
            die();
        }
        
        $data = $properties[$iso];
        
        if (isset($data['items']['properties'])) {
            $this->properties = $data['items']['properties'];
        }
        
        if (isset($data['items']['required'])) {
            if (is_array($data['items']['required'])) {
                $this->required = $data['items']['required'];
            } else {
                $this->required = array($data['items']['required']);
            }
        } else {
            $this->required = array();
        }
    }
    
    /**
     * Check if a field is required.
     * 
     * @param string $field
     * @return bool
     */
    public function isRequired($field)
    {
        return in_array($field, $this->required);
    }
    
    /**
     * Get the description
     * 
     * @return string
     */
    public function getDescription()
    {
        return (string) $this->description;
    }
    
    /**
     * Get the ISO code
     * 
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }
    
    /**
     * Get the properties
     * 
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }
    
    /**
     * Get required properties
     * 
     * @return array
     */
    public function getRequired()
    {
        return $this->required;
    }
    
    /**
     * Get the title
     *  
     * @return string
     */
    public function getTitle()
    {
        if (empty($this->title)) {
            return 'ISO ' . $this->iso;
        }
        return $this->title;
    }
}