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
        $opts = array(
            'http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        $context  = stream_context_create($opts);
        $authResult = file_get_contents($authUrl, false, $context);
        if ($authResult <> '') {
            $authResultObj = json_decode($authResult);
            if (isset($authResultObj->member->member_no)) {
                /* Inloggningen lyckades */

                $firstFormatted = $this->formatNameForEmail($authResultObj->member->first_name);
                $lastFormatted = $this->formatNameForEmail($authResultObj->member->last_name);
                $firstlast = $firstFormatted . '.' . $lastFormatted;

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

                $group_names = [];
                $group_nos = [];
                $group_ids = [];
                foreach ($memberResultObj->memberships as $memberships) {
                    foreach ($memberships as $groupkey => $group) {
                        $group_names[] = $group->group->name;
                        $group_nos[] = $group->group->group_no;
                        $group_ids[] = $groupkey;
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
                $rolesResultObj = json_decode($rolesResult);

                // Calculate age (above or under 15?)
                $bday = new DateTime($memberResultObj->dob);
                $today = new DateTime('00:00:00');
                $diff = $today->diff($bday);
                $age = $diff->y;
                if ($age < 15) $above_15 = 0;
                else $above_15 = 1;

                $attributes = array(
                    'uid' => array($authResultObj->member->member_no),
                    'email' => array($authResultObj->member->email),
                    'firstname' => array($authResultObj->member->first_name),
                    'lastname' => array($authResultObj->member->last_name),
                    'firstlast' => array($firstlast),
                    'displayName' => array($authResultObj->member->first_name . ' ' . $authResultObj->member->last_name),
                    'dob' => array($memberResultObj->dob),
                    'group_name' => $group_names,
                    'group_no' => $group_nos,
                    'group_id' => $group_ids,
                    'above_15' => array($above_15),
                    'roles' => array($rolesResult),
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

    /**
     * Formats a name to be used in an email address.
     * @param string $name
     * @return string
     */
    private function formatNameForEmail($name)
    {
        $name = trim(strtolower($name)); // Convert to lower case and trim spaces.
        $name = preg_replace('/([\s]+/', '.', $name); // Replace empty space with one dot each.
        $name = preg_replace('/[.][\-]/', '-', $name); // Replace .- with -
        $name = preg_replace('/[\-][.]/', '-', $name); // Replace -. with -
        $name = sspmod_scoutnetmodule_helpers::removeDiacritics($name); // Replace special characters with normal ones.
        $name = preg_replace('/[^0-9a-z.\-]/i', '', $name); // Remove remaining non-letter and non-number characters.
        return $name;
    }
}
