<?php
/*
	PHP to Arma Config Generator
	by Aaron Clark - http://epochmod.com
*/

class Arma
{
    public $className;
    public $parentName;
    public $isImport;

    public function __construct($class, $parent, $isImport)
    {
        $this->className = $class;
        $this->parentName = $parent;
        $this->isImport = $isImport;
    }

	private function r_implode( $glue, $pieces )
	{
		foreach( $pieces as $r_pieces )
		{
			if( is_array( $r_pieces ) )
			{
				$retVal[] = '{'. $this->r_implode( $glue, $r_pieces ) . '}';
			}
			else
			{
				$retVal[] = $r_pieces;
			}
		}
		return implode( $glue, $retVal );
	}

	private function my_array_map($function, $arr)
	{
	  $result = array();
	  foreach ($arr as $key => $val)
	  {
		  $result[$key] = (is_array($val) ? $this->my_array_map($function, $val) : $this->$function($val));
	  }
	  return $result;
	}

	private function add_quotes($value) {
		$str = "";
		if (is_string($value)) {
			$str = sprintf('"%s"', $value);
		}
		else
		{
			if (is_bool($value)) {
				$str = sprintf($value ? "true" : "false");
			}
			else
			{
				$str = $value;
			}
		}
		return $str;
	}

	public function import_class($input,$indent = "")
	{
		$str = $indent. 'class '. $input->className .';'."\n";
		return $str;
	}


	public function print_cfgpatches($className,$input,$arrName)
	{
		$str = "class CfgPatches {\n    class ".$className." {\n        ". $arrName."[] = {";
		$patches = [];
		foreach ($input as $key => $value) {
			if (isset($value->className)) {
				array_push($patches, '"'.$value->className.'"');
			}
		}
		$str .= implode (",",$patches);

		$end_string = "};\n    };\n};\n";

		$str .= $end_string;
		return $str;

	}


	public function print_class($input,$indent = "")
	{
		$str = "";
		if (isset($input->isImport) && $input->isImport) {
			$str = $indent. 'class '. $input->className .';';
			$end_string = "\n";
		} else {
			if ($input->parentName == "") {
            	$str = $indent. 'class '. $input->className . "\n$indent{\n";
				$end_string = "\n$indent};\n";
			}
			else
			{
				$str = $indent. 'class '. $input->className .' : ' . $input->parentName . "\n$indent{\n";
				$end_string = "\n$indent};\n";
			}

			$indent .= "    ";
			foreach ($input as $key => $value) {

				$blocked = array("parentName","className","isImport");

				if (!in_array($key, $blocked)) {

					if (is_object($value)) {
						$str .= $this->print_class($value,$indent);

					} else {

						if (is_array($value)) {
							$value = $this->r_implode(",",$this->my_array_map('add_quotes',$value));
							$str .= ($indent.$key.'[] = {'.$value.'};'."\n");

						} else {

							if (is_string($value)) {
								$str .= ($indent.$key.' = "'.$value.'";'."\n");

							}
							else
							{
								if (is_bool($value)) {
									$str .= ($indent.$key.' = '.sprintf($value ? "true" : "false").';'."\n");
								}
								else
								{
									$str .= ("$indent{$key} = {$value};\n");
								}

							}
						}
					}
				}
			}
		}

		$str .= $end_string;
		return $str;

	}

}

class ArmaFactory
{
    public static function create($class, $parent = 'na')
    {
		return new Arma($class, $parent,($parent == "na"));
    }
}
?>
