<?php

//. ile birleştirme yapılır
$my_name = 'haydar'.' karadeniz';
$message = "merhaba dünya {$my_name} ";
echo $message;


//------------------------------------------------------------------
echo "<br>";

//=== veya !=== kullanırsan tiplerinin de aynı olup olmadığını kontrol eder
$a = 4;
$b = '4';

if ($a !== $b) {
    echo "A, B'ye denk değildir";
} else {
    echo "A, B'ye denktir";
}
//------------------------------------------------------------------
echo "<br>";

// Array tanımlamak için 1. Yöntem
$fruits = array(
'Fruit Name 1' => 'apple',
'Fruit Name 2' => 'kiwi',
'Fruit Name 3' => 'orange',
);

// Array tanımlamak için 2.ci yöntem
$fruits2 = [
'Fruit Name 1' => 'apple',
'Fruit Name 2' => 'kiwi',
'Fruit Name 3' => 'orange',
];

echo $fruits["Fruit Name 1"];

//------------------------------------------------------------------
echo "<br>";

$basic_array = array("a","b","c");
$basic_array2 = ["a","b","c"];

echo $basic_array[0];


//------------------------------------------------------------------
echo "<br>";


class Car {
    function brand(): string {
        $brand_name = 'BMW';
        return 'Araç Markası: ' . $brand_name;
    }
}

$object = new Car();
echo $object->brand();


//------------------------------------------------------------------
echo "<br>";

class CallableClass {
	public static function callableMethod() { //static olmak zorunda degil
		echo 'hello world';
	}
}
function callableFunction(callable $callable) {
	call_user_func($callable);
}

$callable_obj = new CallableClass();
callableFunction([$callable_obj, 'callableMethod']);


//------------------------------------------------------------------
echo "<br>";


function printIterable(iterable $my_iterable) {
	foreach($my_iterable as $item) {
		echo $item .' ';
	}
}

$arr = array('a', 'b', 'c');
printIterable($arr);

//------------------------------------------------------------------
echo "<br>";

define( 'CAR_NAME', 'MERCEDES' );
// define() fonksiyonuyla CAR_NAME adında bir sabit tanımladık ve MERCEDES değerini verdik.
echo CAR_NAME;
// Çıktı: MERCEDES

const COMPANY_NAME = 'Onur Özden Web Çözümleri';
// const anahtar kelimesiyle COMPANY_NAME adında bir sabit tanımladık ve Onur Özden Web Çözümleri değerini verdik.
echo COMPANY_NAME;
// Çıktı: Onur Özden Web Çözümleri

echo "<br>";

define( 'SOFTWARE_VERSIONS', array(
	'alfa version'   => 2,
	'beta version'   => 4,
	'stable version' => 7,
) );
print_r( SOFTWARE_VERSIONS );


//------------------------------------------------------------------
echo "<br>";
$i = 1;

while ($i < 5) {
  echo "Sayı: {$i} <br>";
//  if( $i == 3)
//	  break;
  $i++;
}

//------------------------------------------------------------------
echo "<br>";

$j = 10;
do {
  echo "Sayı: $j <br>";
  $j++;
} while ($j < 13);


//------------------------------------------------------------------
echo "<br>";

$days = array(
  'haftanın ilk günü' => 'pazartesi',
  'haftanın ikinci günü' => 'salı',
  'haftanın üçüncü günü' => 'çarşamba',
  'haftanın dördüncü günü' => 'perşembe',
  'haftanın beşinci günü' => 'cuma',
  'haftasonunun ilk günü' => 'cumartesi',
  'haftasonunun ikinci günü' => 'pazar',
    );

foreach ($days as $key => $value) {
    echo $key . ': ' . $value . '<br>';
}

//------------------------------------------------------------------
echo "<br>";

function addNumbers(float $a, int $b) : float {
  return $a + $b;
}
echo addNumbers(1.2, 6);


//------------------------------------------------------------------
echo "<br>";



/*
$number1 = 10;
$number2 = 5;

echo $number1 + $number2; // 15
echo $number1 - $number2; // 5
echo $number1 * $number2; // 50
echo $number1 / $number2; // 2
*/



?>


