<?php
	if(get_option('activecampaign_url') && get_option('activecampaign_api')){
		define("ACTIVECAMPAIGN_URL", get_option('activecampaign_url'));
		define("ACTIVECAMPAIGN_API_KEY", get_option('activecampaign_api'));		
	}
?>