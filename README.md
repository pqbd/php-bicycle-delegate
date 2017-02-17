#Delegate
the component for [alternative manifest](https://github.com/pqbd/php-bicycle "bicycle")

Delegate is a callable list of callbacks.

## Code sample
```php
class A
{
  public function handler( $nValue, $strValue, $arValue)
  {
    print_r( array( $nValue, $strValue, $arValue));
    return true;
  }
}
$someObject = new A();

$delegate = new \bicycle\delegate\Delegate();
$delegate->add( array( $someObject, 'handler'));
$delegate->add( function( $a, $b, $c){ return 'last_result';});
$result = $deleage->delegate( 1, 'test', array( 1, 2, 3));
print_r( $result);
```