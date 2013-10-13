<?php
date_default_timezone_set('Australia/Brisbane');

class Config {

	public static $addresses = array(
			'margaret_st' => array(
					'name' => 'Margaret St',
					'address' => '1b/160 Margaret St, Toowoomba QLD 4350',
					'phone' => '(07) 4638 7888',
					'open' => array(
						'6:30-3:00 Mon-Fri',
						'8:00-2:00 Sat'
						)
				),
			'ruthven_st' => array(
					'name' => 'Ruthven St',
					'address' => 'Ruthven St, Toowoomba QLD 4350',
					'phone' => '0419 334 394',
					'open' => array(
						'8:00-4:00 Mon-Fri'
						)
				)
		);
	public static $title = 'Findos Cafe, Toowoomba';
	public static $logo_url = 'https://www.facebook.com/findoscafe';

}