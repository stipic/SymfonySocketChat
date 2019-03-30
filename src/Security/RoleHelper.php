<?php 
namespace App\Security;

class RoleHelper 
{
    private $_roleHierarchy;

    public function __construct($roleHierarchy) 
    {
        $this->_roleHierarchy = $roleHierarchy;
    }

    public function getParentRoles(string $role) : array
    {
        $accessLine = false;
        $rolesThatHaveRights = [];
        foreach($this->_roleHierarchy as $singleRole => $childRoles)
        {
            if($singleRole == $role)
            {
                $accessLine = true;
            }

            if($accessLine == true)
            {
                $rolesThatHaveRights[] = $singleRole;
            }
        }

        return $rolesThatHaveRights;
    }
}