<?php 

/*
todo add descriptions
/*

require_once('GeneratorClass.php');

// JSON import test
$import_json = '
{
	"className": "CraterSpike",
	"parentName": "",
    "CraterSmokeCustom1": {
		"className": "CraterSmokeCustom1",
		"parentName": "",
		"simulation": "particles",
		"type": "SpikeCraterEffect",
		"intensity": 1,
		"interval": 0.1,
		"lifeTime": 0.5,
		"isAlive": true
  	},
	"CraterSmokeCustom4": {
		"className": "CraterSmokeCustom4",
		"parentName": "",
    	"simulation": "particles",
    	"type": "WoodChippings3",
		"intensity": 1,
		"interval": 1,
		"lifeTime": 0.5
  	}
}';

// have the factory create new arma cfg
echo "<pre>";
var_dump(json_decode($import_json));


// PHP class builder
$parent = ArmaFactory::create('CraterSpike', '');
$parent->isAlive = false;

// test of blind importing arma class 
$external = ArmaFactory::create('Default');
$parent->Default = $external;

// add sub class inside CraterSpike
$child = ArmaFactory::create('CraterSmokeCustom1', '');
$parent->CraterSmokeCustom1 = $child;

// add variables to CraterSmokeCustom1
$child->simulation = "particles";
$child->type = "SpikeCraterEffect"; // CraterSmokeCustom
$child->position = array(0,0,0);
$child->intensity = 1;
$child->interval = 0.1;
$child->lifeTime = 0.5;
$child->isAlive = true;

// add sub class inside CraterSpike
$child = ArmaFactory::create('CraterSmokeCustom4', '');
$parent->CraterSmokeCustom4 = $child;

// add variables to CraterSmokeCustom4
$child->simulation = "particles";
$child->type = "WoodChippings3"; // CraterSmokeCustom
$child->position = array(0,0,0);
$child->intensity = 1;
$child->interval = 1;
$child->lifeTime = 0.5;

// have the factory create new arma config
$parent2 = ArmaFactory::create('CfgAnimationSourceSounds', '');

// add sub class inside CfgAnimationSourceSounds
$class = ArmaFactory::create('jack_pump', '');
$parent2->jack_pump = $class;

// add sub class inside jack_pump
$child = ArmaFactory::create('pumpJack', '');
$class->pumpJack = $child;

// add variables to pumpJack
$child->loop = 0;
$child->terminate = 0;
$child->trigger = "direction * (phase factor[0.01,0.02])";
$child->sound0 = array('\x\addons\a3_epoch_assets\sounds\tools\jack', 1, 1, 20,true,false, array('\test\1234',0,true));
$child->sound = array("sound0", 1,array(true, array("sound0", 1)));


// test getting base classes for building cfgpatches arrays, todo check for scope.
print_r($parent->print_cfgpatches('A3_epoch_cfg', $parent,'units'));

// print first config CraterSpike
print_r($parent->print_class($parent));

// print second config CfgAnimationSourceSounds
print_r($parent2->print_class($parent2));

// test exporting data to JSON 
$import_json = json_encode($parent2, JSON_PRETTY_PRINT);
echo $import_json;

// test PARSE JSON back to Arma config
print_r($parent->print_class(json_decode($import_json)));

?>
