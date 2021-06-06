<?php
namespace SmartDto;

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
     *	@description	        Builds a string for copy/paste into your DTO object instead of manually
     *                          creating those public parameters
     *	@param	$type [bool]    Determines if the parameter builds typing into the return
     */
    public function getAttributes($typed = false): string
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
    /**
    *    @description    Creates an array with regex values based on the values of an input array
    *                    and a target array with the replacement maps
    *
    *    @example        $array = [
    *                        'TEST' => 'best',
    *                        'REST' => 'fest'
    *                    ];
    *                    $target = [
    *                        'key1' => '~TEST~ - ~REST~',
    *                        'key2' => '~REST~'
    *                    ];
    *                    # Map the arrays
    *                    $final = \SmartDto\Mapper::mapster($array, $target);
    *                    # "$final" would produce you a final array of
    *                    [
    *                       'key1' => 'best - fest',
    *                       'key2' => 'fest'
    *                    ]
    *   @param  $array   This is the starting data array
    *   @param  $target  This is the mapping array which will retain the keys but replace the tilde
    *                    values with values from $array
    */
    public static function mapster(array $array, array $target, string $split = '/~[^~]+~/', $trim = '~'): array
    {
        $final = [];
        foreach($target as $key => $value) {
            $value = preg_replace_callback($split, function($v) use ($array, $split, $trim){
                $k = (is_callable($trim))? $trim($v, $split) : trim($v[0], $trim);
                return ($array[$k])?? $v[0];
            },$value);
            # Set to value in final compiled array
            $final[$key]    =    $value;
        }
        # Return compiled array
        return $final;
    }
}