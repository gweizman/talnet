<?php

namespace talnet;

require_once("Entry.php");

use Exception;

class User extends Entry
{
    protected static $_table = NULL,
        $_id_field = "USER_ID";


    /**
     * @param $keys Array of table information, built as key => value
     * @param bool $new True iff a new user is to be created
     */
    public function __construct($keys, $new = False, $app = null)
    {
        if ($app == null)
            $this->_app = Talnet::getApp();
        else
            $this->_app = $app;
        $this->_keys = (object)$keys;
        if ($new) {
            $data = array(
                "username" => $this->_keys->USERNAME,
                "password" => $this->_keys->PASSWORD,
				"firstName" => $this->_keys->FIRST_NAME,
				"lastName" => $this->_keys->LAST_NAME,
                "displayName" => $this->_keys->DISPLAY_NAME,
                "email" => $this->_keys->EMAIL,
                "year" => $this->_keys->YEAR,
                "room" => $this->_keys->ROOM_NUM,
                "phoneSuf" => $this->_keys->PHONE_SUF,
                "phonePre" => $this->_keys->PHONE_PRE,
            );
            $request = RequestFactory::createUserAction("SIGN_UP", $data, NULL);
            $this->_app->send($request);
        }
        unset($this->_keys->PASSWORD);
        return $this;
    }

	/**
	 * Get value in given column
	 * @param $name - the column whose value we want to retrieve
	 * @return the value in the column
	 */
	public function __get($name) {
		switch ($name) {
			// Calculate the user's year in the program, bigger than 3 values mean they have already finished
			case 'YEAR_IN_PROGRAM':
				return Utilities::getFirstYear() - $this->_keys->YEAR + 1;
				break;
				
			default:
				return parent::__get($name);
				break;
		}
	}

    /**
     * Sets value in the given column
     * @param $name - the column whose value we want to change
     * @param $value - the value to insert into the given column
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        throw new Exception("Cannot set individual parameter");
    }

    public function changeData($data)
    {
        $data['username'] = $this->USERNAME;
        $request = RequestFactory::createUserAction("UPDATE_INFO", $data, NULL);
        $this->_app->send($request);
        Communicate::refresh($this->_app);
    }
    
    public function activate()
    {
    	$data = array();
        $data['userToActivate'] = $this->USERNAME;
        $request = RequestFactory::createUserAction("ACTIVATE", $data, NULL);
        $this->_app->send($request);
        Communicate::refresh($this->_app);
    }
    
    public function deactivate()
    {
    	$data = array();
        $data['userToDeactivate'] = $this->USERNAME;
        $request = RequestFactory::createUserAction("DEACTIVATE", $data, NULL);
        $this->_app->send($request);
        Communicate::refresh($this->_app);
    }

    public function setPass($newPass)
    {
        $data = array(
            'username' => $this->USERNAME,
            'newPassword' => $newPass
        );
        $request = RequestFactory::createUserAction("UPDATE_PASSWORD", $data);
        return $this->_app->send($request);
    }

    /**
     * Delete row from the table
     */
    public function remove()
    {
        $id = $this->USERNAME;
        $data = array(
            'userToDelete' => $id
        );
        $request = RequestFactory::createUserAction("DELETE_USER", $data);
        return $this->_app->send($request);
    }

    public function addPermissionGroup($permission)
    {
        $data = array(
            'username' => $this->USERNAME,
            'permissionGroupName' => $permission->PERMISSION_NAME
        );
        $request = RequestFactory::createUserAction("ADD_PERMISSION", $data);
        return $this->_app->send($request);
    }

    public function removePermissionGroup($permission)
    {
        $data = array(
            'username' => $this->USERNAME,
            'permissionGroupName' => $permission->PERMISSION_NAME
        );
        $request = RequestFactory::createUserAction("REMOVE_PERMISSION", $data);
        return $this->_app->send($request);
    }

    public function getPermissionGroups()
    {
        $request = RequestFactory::createUserAction("GET_GROUPS");
        $answer = $this->_app->send($request);
        $permissions = array();
        foreach ($answer as $permission) {
            array_push($permissions, new Permission($permission, false));
        }
        return $permissions;
    }

    /**
     * @param string $subject
     * @param string $message Lines should be separated with \n\r
     * @return true iff successful
     */
    public function sendMail($subject, $message)
    {
        require 'gmail.php';
        $mail_transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
            ->setUsername($GMAIL_USERNAME)
            ->setPassword($GMAIL_PASSWORD);

        $mailer = \Swift_Mailer::newInstance($mail_transport);

        $message = \Swift_Message::newInstance($subject)
            ->setFrom(array('talnet.talpiot@gmail.com' => 'Talnet'))
            ->setTo(array($this->EMAIL))
            ->setBody("<strong>זוהי הודעה אוטומטית ממערכת תלנט</strong><br /><br />" . $message);

        $result = $mailer->send($message);

        return $result;
        //return mail($this->EMAIL, $subject, $message, 'From: ' . Talnet::getApp()->APP_NAME . '@talpiot');
    }

    public function isInPermissionGroup($name)
    {
        $groups = $this->getPermissionGroups();
        foreach ($groups as $group) {
            if (is_array($name)) {
                foreach ($name as $item) {
                    if ($group->PERMISSION_NAME == $name) {
                        return true;
                    }
                }
            } else {
                if ($group->PERMISSION_NAME == $name) {
                    return True;
                }
            }
        }
        return False;
    }

    public function isAppAdmin($name = NULL)
    {
        if ($name == NULL) {
            $name = $this->_app->_name;
        }
        return $this->isInPermissionGroup(array($name . "_admin", "Super_Admin"));
    }


    public static function getUserByName($name)
    {
        $result = User::get(new BaseCondition('USERNAME', '=', $name), false);
	if (empty($result)) {
		throw new Exception("לא קיים משתמש עם שם זה.");
	}
        return $result[0];
    }
	
	/**
	 * Returns the user with the given ID
	 * @param $ID - the user ID
	 * @throws Exception if no such user found
	 * @return The user matching the ID
	 */
    public static function getUserByID($ID)
    {
        $result = User::get(new BaseCondition('USER_ID', '=', $ID), false);
	if (empty($result)) {
		throw new Exception("לא קיים משתמש עם ID זה.");
	}
        return $result[0];
    }

    /**
     * Returns a list of all the entries matching a given condition
     * @param $condition - given condition
     * @return array- array of entries matching the condition
     */
    public static function get($condition, $active = true, $app = null)
    {
        if ($app == null)
            $app = Talnet::getApp();
    if ($app == null)
        $app = Talnet::getApp();
	// Include active users only
	if($active) {
		$condition = new Condition($condition, new BaseCondition('ACTIVE', '=', 1), 'AND');
	}
		
        $request = RequestFactory::createUserAction("SELECT", NULL, $condition);
        $answer = $app->send($request);
        $retVal = array();
        foreach ($answer as $user) {
            array_push($retVal, new User($user, false));
        }
        return $retVal;
    }
    
    /**
     * Returns the result length of a user select reuest
     * @param $condition - given condition
     * @return array- array of entries matching the condition
     */
    public static function countResult($condition)
    {
        return count(User::get($condition));
    }

    public function setApp($app = null) {
        if ($app == null) {
            $app = Talnet::getApp();
        }
        $this->_app = $app;
    }
} 
