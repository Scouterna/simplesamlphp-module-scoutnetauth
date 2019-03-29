<?php

class sspmod_scoutnetmodule_Auth_Source_scoutnetauth extends sspmod_core_Auth_UserPassBase
{
    protected function login($username, $password)
    {
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
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        $authResult = file_get_contents($authUrl, false, $context);
        if ($authResult === '') {
            /* uppkopplingen misslyckades */
            SimpleSAML_Logger::warning('ScoutnetAuth: Kunde inte koppla upp mot SCOUTNET. Vänligen försök senare.');
            throw new SimpleSAML_Error_Error('WRONGUSERPASS');
        }

        $authResultObj = json_decode($authResult);
        if (!isset($authResultObj->member->member_no)) {
            if (isset($authResultObj->err)) {
                /* inloggningen misslyckades */
                SimpleSAML_Logger::warning('ScoutnetAuth: Felaktigt användarnamn eller lösenord för ' . var_export($username, TRUE) . '.');
                throw new SimpleSAML_Error_Error('WRONGUSERPASS');
            }

            // TODO why do this case not throw an error?
            return;
        }

        /* Inloggningen lyckades */

        $translation = [
            ' ' => '.',
            '/' => '-',
            'é' => 'e',
            'Å' => 'a',
            'å' => 'a',
            'Ä' => 'a',
            'ä' => 'a',
            'Ö' => 'o',
            'ö' => 'o',
        ];
        $firstlast = $authResultObj->member->first_name . '.' . $authResultObj->member->last_name;
        $firstlast = strtolower(strtr($firstlast, $translation));

        //GET ADDITIONAL ATTRIBUTES FROM USER PROFILE
        $profileUrl = 'https://' . $scoutnetHostname . '/api/get/profile';

        $options = array(
            'http' => array(
                'method' => "POST",
                'header' => "Authorization: Bearer " . $authResultObj->token . "\r\n"
            )
        );

        $context = stream_context_create($options);
        $memberResult = file_get_contents($profileUrl, false, $context);
        $memberResultObj = json_decode($memberResult);

        foreach ($memberResultObj->memberships as $memberships) {
            foreach ($memberships as $groupkey => $group) {
                if ($group->is_primary) {
                    $group_name = $group->group->name;
                    $group_no = $group->group->group_no;
                    $group_id = $groupkey;
                }
            }
        }

        //GET USER ROLES
        $roleUrl = 'https://' . $scoutnetHostname . '/api/get/user_roles';

        $options = array(
            'http' => array(
                'method' => "POST",
                'header' => "Authorization: Bearer " . $authResultObj->token . "\r\n"
            )
        );

        $context = stream_context_create($options);
        $rolesResult = file_get_contents($roleUrl, false, $context);

        // Calculate age (above or under 15?)
        $bday = new DateTime($memberResultObj->dob);
        $today = new DateTime('00:00:00');
        $age = $today->diff($bday)->y;
        $above_15 = (int) ($age < 15);

        $attributes = array(
            'uid' => array($authResultObj->member->member_no),
            'email' => array($authResultObj->member->email),
            'firstname' => array($authResultObj->member->first_name),
            'lastname' => array($authResultObj->member->last_name),
            'firstlast' => array($firstlast),
            'displayName' => array($authResultObj->member->first_name . ' ' . $authResultObj->member->last_name),
            'dob' => array($memberResultObj->dob),
            'group_name' => array($group_name),
            'group_no' => array($group_no),
            'group_id' => array($group_id),
            'above_15' => array($above_15),
            'roles' => array($rolesResult),
        );

        /* Return the attributes. */
        return $attributes;
    }
}
