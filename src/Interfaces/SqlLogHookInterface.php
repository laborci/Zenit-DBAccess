<?php namespace Zenit\Bundle\DBAccess\Interfaces;

interface SqlLogHookInterface{
	public function log($sql);
}