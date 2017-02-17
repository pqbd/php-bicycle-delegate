<?php
namespace bicycle\delegate;

class TestHandler
{
  private $m_arArg;

  public function __construct()
  {
    $this->m_arArg = null;
  }
  public function getArgs()
  {
    return $this->m_arArg;
  }
  public function handler( ...$args)
  {
    $this->m_arArg = $args;
  }
}