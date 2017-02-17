<?php
namespace bicycle\delegate;

class DelegateTest
extends \bicycle\TestCase
{
  public function test_delegate_in_delegate()
  {
    $delegate = $this->createDelegate( $this->createDelegate( $this->createDelegateFunction( 'A')));
    $result = $delegate->delegate( 1, 2);
    $this->assertEquals( 'A{1,2}'
                        , $result
                        );
  }
  public function test_add_unsupported_handler()
  {
    $delegate = $this->createDelegate();
    try
    {
      $delegate->add( 'test');
      $this->assertTrue( false);
    }
    catch( Exception $e)
    {
      $this->assertTrue( true);
    }
  }
  public function test_delegate_empty()
  {
    $delegate = $this->createDelegate();
    $result = $delegate->delegate( 1, 2);
    $this->assertNull( $result);
  }
  public function test_delegate_one()
  {
    $delegate = $this->createDelegate( $this->createDelegateFunction( 'A'));
    $result = $delegate->delegate( 1, 3, 6);
    $this->assertEquals( 'A{1,3,6}'
                        , $result
                        );
  }
  public function test_delegate_one_object_method()
  {
    $handlerA1 = new TestHandler();
    $delegate = $this->createDelegate( array( $handlerA1, 'handler'));
    $result = $delegate->delegate( 1, 3, 6);
    $this->assertEquals( array( 1, 3, 6)
                        , $handlerA1->getArgs()
                        );
  }
  public function test_delegate_chain()
  {
    $hashResult = array();
    $delegate = $this->createDelegate();
    $fnB = $this->createDelegateFunction( 'B', $hashResult);
    $delegate->add( $this->createDelegateFunction( 'A', $hashResult))
            ->add( $fnB)
            ->add( $this->createDelegateFunction( 'C', $hashResult))
            ;
    $result = $delegate->delegate( 1, 3, 2);
    $this->assertEquals( 'C{1,3,2}'
                        , $result
                        );
    $this->assertEquals( 'A{1,3,2}'
                        , $hashResult[ 'A']
                        );
    $this->assertEquals( 'B{1,3,2}'
                        , $hashResult[ 'B']
                        );
    $this->assertEquals( 'C{1,3,2}'
                        , $hashResult[ 'C']
                        );
  }
  public function test_remove_one()
  {
    $fnA = $this->createDelegateFunction( 'A');
    $delegate = $this->createDelegate( $fnA);
    $result = $delegate->delegate();
    $this->assertEquals( 'A{}'
                        , $result
                        );
    $result = $delegate->remove( $fnA)->delegate();
    $this->assertNull( $result);
  }
  public function test_remove_from_start_of_chain()
  {
    $hashResult = array();
    $fnA = $this->createDelegateFunction( 'A', $hashResult);
    $fnB = $this->createDelegateFunction( 'B', $hashResult);
    $fnC = $this->createDelegateFunction( 'C', $hashResult);
    $delegate = $this->createDelegate( $fnA)
                    ->add( $fnB)
                    ->add( $fnC)
                    ;
    $result = $delegate->remove( $fnA)->delegate( 'test');
    $this->assertEquals( 'C{test}'
                        , $result
                        );
    $this->assertFalse( isset( $hashResult[ 'A']));
    $this->assertTrue( isset( $hashResult[ 'B']));
    $this->assertTrue( isset( $hashResult[ 'C']));
  }
  public function test_remove_from_end_of_chain()
  {
    $hashResult = array();
    $fnA = $this->createDelegateFunction( 'A', $hashResult);
    $fnB = $this->createDelegateFunction( 'B', $hashResult);
    $fnC = $this->createDelegateFunction( 'C', $hashResult);
    $delegate = $this->createDelegate( $fnA)
                    ->add( $fnB)
                    ->add( $fnC)
                    ;
    $result = $delegate->remove( $fnC)->delegate( 'test');
    $this->assertEquals( 'B{test}'
                        , $result
                        );
    $this->assertTrue( isset( $hashResult[ 'A']));
    $this->assertTrue( isset( $hashResult[ 'B']));
    $this->assertFalse( isset( $hashResult[ 'C']));
  }
  public function test_remove_from_center_of_chain()
  {
    $hashResult = array();
    $fnA = $this->createDelegateFunction( 'A', $hashResult);
    $fnB = $this->createDelegateFunction( 'B', $hashResult);
    $fnC = $this->createDelegateFunction( 'C', $hashResult);
    $delegate = $this->createDelegate( $fnA)
                    ->add( $fnB)
                    ->add( $fnC)
                    ;
    $result = $delegate->remove( $fnB)->delegate( 'test');
    $this->assertEquals( 'C{test}'
                        , $result
                        );
    $this->assertTrue( isset( $hashResult[ 'A']));
    $this->assertFalse( isset( $hashResult[ 'B']));
    $this->assertTrue( isset( $hashResult[ 'C']));
  }
  private function createDelegate( $handler = null)
  {
    return new Delegate( $handler);
  }
  private function createDelegateFunction( $strName, &$hashResult = null)
  {
    return function( ...$args)
    use( $strName, &$hashResult)
    {
      $strResult = $strName.'{'.implode( ',', $args).'}';
      if ( $hashResult !== null)
        $hashResult[ $strName] = $strResult;
      return $strResult;
    };
  }
}