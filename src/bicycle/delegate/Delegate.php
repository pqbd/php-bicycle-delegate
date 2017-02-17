<?php
namespace bicycle\delegate;

class Delegate
implements IDelegate
{
  private $m_delegateSet;

  public function __construct( $handler = null)
  {
    $this->m_delegateSet = array();
    if ( $handler !== null)
    {
      $this->add( $handler);
    }
  }

  public function delegate( ...$args)
  {
    $result = null;
    foreach ( $this->m_delegateSet as $nIndex => $handler)
    {
      if ( $handler instanceof IDelegate )
      {
        $result = $handler->delegate( ...$args);
      }
      else
      {
        $result = $handler( ...$args);
      }
    }
    return $result;
  }
  public function __invoke( ...$args)
  {
    return $this->delegate( ...$args);
  }
  public function add( $handler)
  {
    $this->throwIfNotSupported( $handler);
    $nIndex = array_search( $handler, $this->m_delegateSet, true);
    if ( $nIndex === false)
      $this->m_delegateSet[] = $handler;
    return $this;
  }
  public function remove( $handler)
  {
    $this->throwIfNotSupported( $handler);
    $nIndex = array_search( $handler, $this->m_delegateSet, true);
    if ( $nIndex !== false)
      unset( $this->m_delegateSet[ $nIndex]);
    return $this;
  }
  private function throwIfNotSupported( $handler)
  {
    if ( !$this->isSupported( $handler))
      throw new Exception( 'not supported handler type: should be callable');
  }
  private function isSupported( $handler)
  {
    if ( is_callable( $handler) || $handler instanceof IDelegate)
    {
      return true;
    }
    else
    {
      return false;
    }
  }
}