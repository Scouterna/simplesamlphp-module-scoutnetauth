<?php
class sspmod_scoutnetmodule_Auth_Source_scoutnetauth extends sspmod_core_Auth_UserPassBase {

    protected function login($username, $password) {
			// Get configuration

/*		// CMJ SPECIALARE FÖR FUNKTIONSKONTON
		// SLÄPPER IGENOM OM USER=PASS
		if ($username == 'medlemsregister' && $password == '123445') {
                      $attributes = array(
                         'firstlast' => array($username),
                      );
                      return $attributes;
		} else {
*/

		// AUTH MOT SCOUTNET
		$scoutnetHostname = getenv('SCOUTNET_HOSTNAME');
		$authUrl = 'https://' . $scoutnetHostname . '/api/authenticate';
		$postdata = http_build_query(
		    array(
		        'username' => $username,
		        'password' => $password
		    )
		);
		$opts = array('http' =>
		    array(
		        'method'  => 'POST',
		        'header'  => 'Content-type: application/x-www-form-urlencoded',
		        'content' => $postdata
		    )
		);
		$context  = stream_context_create($opts);
		$authResult = file_get_contents($authUrl, false, $context);
		if ($authResult <>'') { 
			$authResultObj=json_decode($authResult);	
			if (isset($authResultObj->member->member_no)) { 
				/* Inloggningen lyckades */

			$firstlast = $authResultObj->member->first_name .'.'.$authResultObj->member->last_name;
                	$search = Array("Å", "Ä", "Ö", "å", "ä", "ö", " ", "/", "é");
                	$replace = Array("A", "A", "O", "a", "a", "o", ".", "-", "e");
                	$firstlast = strtolower(str_replace($search, $replace, $firstlast));

			//GET ADDITIONAL ATTRIBUTES FROM USER PROFILE
			$profileUrl = 'https://' . $scoutnetHostname . '/api/get/profile';
		
			$options = array(
			  'http'=>array(
			    'method'=>"POST",
			    'header'=>"Authorization: Bearer ".$authResultObj->token."\r\n"
			  )
			);
		
			$context = stream_context_create($options);
			$memberResult = file_get_contents($profileUrl, false, $context);
			$memberResultObj = json_decode($memberResult);

			foreach ($memberResultObj->memberships as $memberships) {
				foreach ($memberships as $group) {
					if ($group->is_primary) { 
						$group_name = $group->group->name;
						$group_no = $group->group->group_no;
					}
				}
			}
			if ($group_no == "1427") { $firstlast .= "@malarscouterna.se";} else
                        if ($group_no == "1416") { $firstlast .= "@hasselbyscout.se";} else
                        if ($group_no == "1441") { $firstlast .= "@spangascouterna.se";}
			else
			{  $firstlast .= "@".$group_no; }
			// TODO: Make dynamic from SP metadata somehow..

	        	$attributes = array(
    	       	 	 'uid' => array($authResultObj->member->member_no),
        	   	 'email' => array($authResultObj->member->email),
                         'firstname' => array($authResultObj->member->first_name),
                         'lastname' => array($authResultObj->member->last_name),
			 'firstlast' => array($firstlast),
           		 'displayName' => array($authResultObj->member->first_name .' '.$authResultObj->member->last_name),
			 'dob' => array($memberResultObj->dob),
                         'group_name' => array($group_name),
                         'group_no' => array($group_no),

/*            	 	 'eduPersonAffiliation' => array('member', 'employee'), */
			);
			/* Return the attributes. */
        		return $attributes;
        	} else if (isset($authResultObj->err)) {
        	    /* inloggningen misslyckades */
        		SimpleSAML_Logger::warning('ScoutnetAuth: Felaktigt användarnamn eller lösenord för ' . var_export($username, TRUE) . '.');
            	throw new SimpleSAML_Error_Error('WRONGUSERPASS');
        	}
		} else { 
            /* uppkopplingen misslyckades */
            SimpleSAML_Logger::warning('ScoutnetAuth: Kunde inte koppla upp mot SCOUTNET. Vänligen försök senare.');
            throw new SimpleSAML_Error_Error('WRONGUSERPASS');
		}
    }
}

/* TODO: rejecta för de som inte är medlemmar i kåre */

?>
