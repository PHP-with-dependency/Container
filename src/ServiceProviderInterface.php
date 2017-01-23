<?php 
namespace ClanCats\Container;

interface ServiceProviderInterface 
{
	/**
	 * What services are provided by the service provider
	 * 
	 * @return array[string]
	 */
	public function provides() : array;

	/**
	 * Resolve a service with the given name 
	 * 
	 * Returns an array with first argument service and second a bool indicating if 
	 * the service should be shared.
	 * 
	 * @param string 					$serviceName
	 * @param Container 				$container
	 * @return array[string, bool]
	 */
	public function resolve(string $serviceName, Container $container) : array;
}	