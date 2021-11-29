<?php
namespace SmartDto;

class Dto
{
    const DEFAULT_CASE = 1;
    const CAMEL_CASE = 2;
    const PASCAL_CASE = 3;
    const PHP_CASE = 4;
    
    private $mapTo = 1;
    /**
     *	@description	             Builds the Dto object from the input
     *	@param	$array [array|null]  This is the value(s) being set to the Dto
     *  @param  $mapTo [int]         The case to try and convert to (default is php)
     */
    public function __construct($array = null, int $mapTo = 1)
    {
        $this->mapTo = $mapTo;
        # Fetch the parameters
        $Reflection = new \ReflectionObject($this);
        # Convert to array if not an array
        if($array instanceof \SmartDto\Dto)
            $array = $array->toArray();
        # Process before the start of assignments
        $array = $this->beforeConstruct($array, $Reflection);
        # Assign the parameters
        $this->processParameters($Reflection, $array);
        # Process before the start of assignments
        $this->afterConstruct($array, $Reflection);
    }
    /**
     *	@description	Converts key to the correct format
     *	@param	$key    Key from an array
     */
    public function toCase($key)
    {
        # Don't convert if it's a numeric key
        if(is_numeric($key))
            return $key;
        # Regex to replace all non-standard characters
        $rep = '/[^\d\w\s\.\-\_]/';
        # Try and map
        switch($this->mapTo) {
            # Camel Case
            case(2):
                return str_replace(' ', '', lcfirst(ucwords(str_replace(['_','-'], [' ',' '], strtolower(preg_replace($rep, '', $key))))));
            # Pascal Case
            case(3):
                return str_replace(' ', '', ucwords(str_replace(['_','-'], [' ',' '], strtolower(preg_replace($rep, '', $key)))));
            # PHP Case
            case(4):
                return str_replace(' ', '', str_replace([' ','-'], ['_','_'], strtolower(preg_replace($rep, '', $key))));
        }
        # Return whatever was input
        return $key;
    }
    /**
     *	@description	Runs before the main assignment in the construct and rebuilds the input array to match destination case before assignment
     *	@param  $array [any] 
     */
    protected function beforeConstruct($array)
    {
        if(!is_array($array) || empty($array))
            return $array;
        foreach($array as $k => $v) {
            $nk = $this->toCase($k);
            if($nk == $k)
                continue;
            $array[$this->toCase($k)] = $v;
            unset($array[$k]);
        }
        return $array;
    }
    /**
     *	@description    Runs after the parameters are all build, can contain any logic when extended
     */
    protected function afterConstruct()
    {
    }
    /**
     *	@description	Converts this Dto object to an array
     */
    public function toArray()
    {
    	$data = $this->toPropertyArray();
    	ksort($data);
    	return $data;
    }
    /**
     *	@description	
     *	@param	
     */
    public function toObject()
    {
        return json_decode(json_encode($this->toPropertyArray()));
    }
    /**
     *	@description	
     *	@param	
     */
    public function __toString()
    {
        return json_encode($this->toPropertyArray());
    }
    /**
     *	@description	Turn parameters into camel case keys
     *  @note           This ends up destroying the base object identity
     */
    public function toCamelCase($object = true)
    {
        $data = [];
        $this->mapTo = self::CAMEL_CASE;
        foreach($this->toArray() as $k => $v) {
            $data[$this->toCase($k)] = $this->recurseCase($v);
        }
        return ($object)? json_decode(json_encode($data)) : $data;
    }
    /**
     *	@description	
     *	@param	
     */
    protected function recurseCase($value)
    {
        if(is_object($value) || is_array($value)) {
            if($value instanceof \SmartDto\Dto)
                $value = $value->toArray();
            else {
                if(is_object($value))
                    $value  = json_decode(json_encode($value), 1);
            }
            $data = [];
            foreach($value as $key => $val) {
                $data[$this->toCase($key)] = $this->recurseCase($val);
            }
            return $data;
        }
        else {
            return $value;
        }
    }
    /**
     *	@description	Turn parameters into camel case keys
     *  @note           This ends up destroying the base object identity
     */
    public function toPhpCase($object = true)
    {
        $data = [];
        $this->mapTo = self::PHP_CASE;
        foreach($this->toArray() as $k => $v) {
            $data[$this->toCase($k)] = $v;
        }
        return ($object)? json_decode(json_encode($data)) : $data;
    }
    /**
     *	@description	Turn parameters into camel case keys
     *  @note           This ends up destroying the base object identity
     */
    public function toPascalCase($object = true)
    {
        $data = [];
        $this->mapTo = self::PASCAL_CASE;
        foreach($this->toArray() as $k => $v) {
            $data[$this->toCase($k)] = $v;
        }
        return ($object)? json_decode(json_encode($data)) : $data;
    }
    /**
     *	@description	
     *	@param	
     */
    protected function processParameters(\ReflectionObject $Reflection, $array = null)
    {
        # Loop those parameters
        foreach($Reflection->getProperties() as $ref) {
            # Get the name
            $param = $ref->getName();
            # See if a value is set
            if(isset($array[$ref->getName()])) {
                $value = $array[$ref->getName()];
                $this->{$param} = $value;
            }
            # Run any dto modifiers that may exist
            if(method_exists($this, $param)) { 
                $this->{$param}(($value)?? null);
            }
        }
    }
    /**
     *	@description	
     *	@param	
     */
    protected function setKeyFormat(int $type)
    {
        $this->mapTo = $type;
        return $this;
    }
    /**
     *	@description	
     *	@param	
     */
    final function toPropertyArray()
    {
        $obj = new \ReflectionObject($this);
        $objAttr = $obj->getProperties(\ReflectionProperty::IS_PUBLIC);
        $new = [];
        if(empty($objAttr))
            return $new;

        foreach($objAttr as $attr) {
            if(!$attr->isStatic())
                $new[$attr->getName()] = $this->{$attr->getName()};
        }

        return $new;
    }
    /**
     *	@description	
     *	@param	
     */
    public function toJson()
    {
        return $this->__toString();
    }
}
