<?php
/**
 *  index.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Project : Development package autoload 
 *  Package : Ficx/autoload
 *  Version : 
 * 
 */
namespace Ficx;

use Ficx\Core\Autoload\AutoLoad;
use Sandbox\Testy\testClass;

echo "<center><h1>rs4-Autoloader</h1></center><hr>";

require_once("../AutoLoad.class.php");

$oAutoLoader = new AutoLoad(dirname(__DIR__));
$oAutoLoader->register();

/**
 * Here is a little bug , can't find dummy with addNamespace
 * Look in to it!
 */

//$oAutoLoader->addNamespace("\\Classes\\Dummy\\", "../rs4-autoload/classes/dummy");
//$oAutoLoader->addNamespace("\Test\Foo\bla\ke\jee\\", "sandbox/dir1");
//$oAutoLoader->addNamespace("\Test\Foo\bla", "sandbox/dir2");


$oTest = new testClass();
$oTestFull = new \Classes\Dummy\dummyClass();


//$oTest = new dummyClass();