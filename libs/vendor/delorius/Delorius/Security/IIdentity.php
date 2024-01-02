<?php
namespace Delorius\Security;

interface IIdentity
{
    /**
    * Returns the ID of user.
    * @return mixed
    */
    function getId();

    /**
    * Returns a list of roles that the user is a member of.
    * @return array
    */
    function getRoles();

}
