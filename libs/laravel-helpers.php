<?php

use Illuminate\Support\Debug\Dumper;

////////////////////////////////////////////////////////////////

if( !function_exists('z') )
{
	/**
	 * Dump the given expression and return it.
	 *
	 * @param  mixed
	 * @return void
	 */
	function z( $x )
	{
		(new Dumper())->dump($x);

		return $x;
	}
}


if( !function_exists('routo') )
{
	/**
	 * Generate a URL to a named route.
	 *
	 * @param  string                     $name
	 * @param  array                      $parameters
	 * @param  bool                       $absolute
	 *
	 * @return string
	 */
	function routo( string$name, array$parameters=[], bool$absolute=true )
	{
		$currentRouteName= app('router')->currentRouteName();

		if( empty($name) )
		{
			$name= $currentRouteName;
		}
		elseif( starts_with( $name, '.' ) )
		{
			$prefix= $currentRouteName;

			do{
				$prefix= preg_replace( '/[^:\\.]+\\.?$/', '', $prefix );

				$name= substr( $name, 1 );
			}
			while( starts_with( $name, '.' ) );

			$name= $prefix.$name;
		}
		elseif( starts_with( $name, ':' ) )
		{
			$name= strtok( $currentRouteName, ':' ).$name;
		}

		$route= app('routes')->getByName($name);

		if( !$route ){
			throw new \Exception("Route [{$name}] is not found");
		}

		$parameterNames= $route->parameterNames();

		$inheritParameters= collect( Route::current()->parameters() )->filter( function( $value, $key )use( $parameterNames ){  return in_array( $key, $parameterNames );  } )->all();

		$parameters+= $inheritParameters;

		return app('url')->route( $name, $parameters, $absolute );
	}
}
