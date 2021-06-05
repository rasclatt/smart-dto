<?php
namespace Nubersoft\SmartDto;

class Mapper extends Dto
{
    private $array;
    /**
     *	@description	
     *	@param	
     */
    public function __construct(array $array = null, $mapTo = 1)
    {
        $this->setKeyFormat($mapTo);
        $this->array = $this->beforeConstruct($array);
    }
    /**
     *	@description	
     *	@param	
     */
    public function getAttributes($typed = false)
    {
        $new = [];
        $assemble = [];
        foreach($this->array as $key => $value) {

            if(is_numeric($value)) {
                $typeOf = (strpos($value, '.') !== false)? 'float' : 'int';
                $typeOfVal = ($typeOf == 'float')? '0.0' : '0';
            }
            elseif(is_bool($value)) {
                $typeOf = 'bool';
                $typeOfVal = 'false';
            }
            else {
                $typeOf = 'string';
                $typeOfVal = "''";
            }
            
            $new[$key] = $value;
            $assemble[] = 'public '.(($typed)? "{$typeOf} " : '')."\${$key} = {$typeOfVal};";
        }
        $this->array = $new;
        return implode(PHP_EOL, $assemble);
    }
}