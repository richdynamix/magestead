<?php namespace Magestead\Exceptions;

/**
 * Class AuthSavePermissionsException
 * @package Magestead\Exceptions
 */
class AuthSavePermissionsException extends \RuntimeException
{
    const TPL = "\n\nFile auth.json is not writable. Please add the following content manually: \n%s\n";

    /**
     * @param array $auth
     * @return self
     */
    public function setAuthObject(array $auth)
    {
        $this->message = sprintf(static::TPL, json_encode($auth, JSON_PRETTY_PRINT));
        return $this;
    }
}
