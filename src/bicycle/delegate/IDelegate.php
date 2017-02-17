<?php
namespace bicycle\delegate;

interface IDelegate
{
  function delegate( ...$args);
  function add( $handler);
  function remove( $handler);
}