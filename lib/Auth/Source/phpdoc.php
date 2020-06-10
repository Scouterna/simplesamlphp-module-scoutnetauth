<?php
declare(strict_types=1);

namespace PHPDoc\Scoutnet\Response;

/**
 * Class AuthenticateError
 * @package PHPDoc\Scoutnet\Response
 * @property string err
 */
class AuthenticateError
{}

/**
 * Class AuthenticateRoot
 * @package PHPDoc\Scoutnet\Response
 *
 * @property string token
 * @property AuthenticateMember member
 */
class AuthenticateRoot
{
}

/**
 * Class AuthenticateMember
 * @package PHPDoc\Scoutnet\Response
 *
 * @property int member_no
 * @property string first_name
 * @property string last_name
 * @property string email
 */
class AuthenticateMember
{
}

/**
 * Class ProfileRoot
 * @package PHPDoc\Scoutnet\Response
 * @property int member_no
 * @property string username
 * @property bool active
 * @property string last_login
 * @property string created_at
 * @property string|null title
 * @property string sex
 * @property string dob
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property bool newsletter
 * @property bool magazine
 * @property string language
 * @property ProfileAddresses addresses
 * @property ProfileMemeberships memberships
 * @property ProfileContactInfos contact_info
 */
class ProfileRoot
{
}

/**
 * Class ProfileAddresses
 * @package PHPDoc\Scoutnet\Response
 * a ProfileAddresses[] but with named keys
 */
class ProfileAddresses
{
    /**
     * @param string $name
     * @return ProfileAddress
     */
    public function __get($name)
    {
        return new ProfileAddress();
    }
}

/**
 * Class ProfileAddress
 * @package PHPDoc\Scoutnet\Response
 * @property int address_type
 * @property null valid_from // type string?
 * @property null valid_until // type string?
 * @property bool is_primary
 * @property string address_line1
 * @property string address_line2
 * @property string address_line3
 * @property string zip_code
 * @property string city
 * @property string country_code
 * @property string country_name
 */
class ProfileAddress
{
}

/**
 * Class ProfileContactInfos
 * @package PHPDoc\Scoutnet\Response
 * a ProfileContactInfo[] but with named keys
 */
class ProfileContactInfos
{
    /**
     * @param string $name
     * @return ProfileContactInfo
     */
    public function __get($name)
    {
        return new ProfileContactInfo();
    }
}

/**
 * Class ProfileContactInfo
 * @package PHPDoc\Scoutnet\Response
 * @property string label
 * @property string value
 * @property string key
 * @property int type_id
 * @property int field_type
 * @property int id
 */
class ProfileContactInfo
{
}

/**
 * Class ProfileContactInfos
 * @package PHPDoc\Scoutnet\Response
 * a ProfileMemebership[] but with named keys
 */
class MembershipsList
{
    /**
     * @param string $name
     * @return ProfileMemebership
     */
    public function __get($name)
    {
        return new ProfileMemebership();
    }
}

/**
 * Class ProfileMemebership
 * @package PHPDoc\Scoutnet\Response
 * @property string joined_at
 * @property ProfileGroupMemebership group
 * @property bool is_primary
 * @property ProfileTroopMemebership|null troop
 * @property ProfilePatrulMemebership|null patrol
 * @property StringList roles
 * @property ProfilePayemntInfo payment_info
 */
class ProfileMemebership
{

}

/**
 * Class ProfileGroupMemebership
 * @package PHPDoc\Scoutnet\Response
 * @property int group_no
 * @property string name
 */
class ProfileGroupMemebership
{
}

/**
 * Class ProfileTroopMemebership
 * @package PHPDoc\Scoutnet\Response
 * @property string name
 * @property int id
 */
class ProfileTroopMemebership
{
}

/**
 * Class ProfilePatrulMemebership
 * @package PHPDoc\Scoutnet\Response
 * @property string name
 * @property int id
 */
class ProfilePatrulMemebership
{
}

/**
 * Class ProfileMemeberships
 * @package PHPDoc\Scoutnet\Response
 * @property MembershipsList group
 */
class ProfileMemeberships
{
}

/**
 * Class ProfilePayemntInfo
 * @package PHPDoc\Scoutnet\Response
 * @property ProfilePayemntTerm previous_term
 * @property ProfilePayemntTerm current_term
 */
class ProfilePayemntInfo
{
}

/**
 * Class ProfilePayemntTerm
 * @package PHPDoc\Scoutnet\Response
 * @property string label
 * @property int status
 * @property string status_text
 * @property string invoice_ref
 */
class ProfilePayemntTerm
{
}

/**
 * Class RolesRoot
 * @package PHPDoc\Scoutnet\Response
 * @property String2DList organisation
 * @property String2DList region
 * @property String2DList project
 * @property String2DList network
 * @property String2DList corps
 * @property String2DList district
 * @property String2DList group
 * @property String2DList troop
 * @property String2DList patrol
 */
class RolesRoot
{}

/**
 * Class StringList
 * @package PHPDoc\Scoutnet\Response
 */
class StringList
{
    /**
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
        return '';
    }
}

/**
 * Class StringList
 * @package PHPDoc\Scoutnet\Response
 */
class String2DList
{
    /**
     * @param string $name
     * @return StringList
     */
    public function __get($name)
    {
        return new StringList();
    }
}
