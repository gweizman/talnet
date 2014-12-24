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
    public function sendMail($subject, $title, $message)
    {
        return User::spam($this, $subject, $title, $message, $this->_app);
    }

    public function isInPermissionGroup($name)
    {
        $groups = $this->getPermissionGroups();
        foreach ($groups as $group) {
            if (is_array($name)) {
                foreach ($name as $item) {
                    if ($group->PERMISSION_NAME == $item) {
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

    /**
     * @param $users
     * @param $subject
     * @param $title
     * @param $message
     */
    public static function spam($users, $subject, $title, $message, $app = null) {
        $currentUser = Communicate::getCurrentUser($app);
        include('gmail.php');
        $mail_transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
            ->setUsername($GMAIL_USERNAME)
            ->setPassword($GMAIL_PASSWORD);
        $mails = Utilities::fieldArray($users, "NAME", "EMAIL");
        $mailer = \Swift_Mailer::newInstance($mail_transport);

        $message = \Swift_Message::newInstance($subject)
            ->setFrom(array($currentUser->MAIL => $currentUser->NAME))
            ->setTo($mails)
            ->setBody("
<div style='margin:0;padding:0;min-width:100%;background-color:#ecf5f3'>
<center class='wrapper' style='display: table;table-layout: fixed;width: 100%;min-width: 620px;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;background-color: #ecf5f3l; height: 100%; width: 100%;' lang='he' dir='rtl'>
        <table class='header centered' style='border-collapse: collapse;border-spacing: 0;Margin-right: auto;Margin-left: auto;width: 600px'>
          <tbody><tr>
            <td style='padding: 0;vertical-align: top'>
              <table style='border-collapse: collapse;border-spacing: 0' align='left'>
                <tbody><tr>
                  <td class='preheader' style='padding: 0;vertical-align: top;letter-spacing: 0.01em;font-style: normal;line-height: 17px;font-weight: 400;font-size: 11px;color: #b9b9b9;font-family: sans-serif;padding-bottom: 40px;padding-top: 40px;text-align: left;width: 280px'>
                    <div class='spacer' style='font-size: 1px;line-height: 2px;width: 100%'>&nbsp;</div>
                    <div class='title'>זוהי הודעה אוטומטית ממערכת Talnet, בשם האפליקציה " . $this->_app->_name . "</div>

                  </td>
                </tr>
              </tbody></table>
              <table style='border-collapse: collapse;border-spacing: 0' align='right'>
                <tbody><tr>
                  <td class='logo' style='padding: 0;vertical-align: top;mso-line-height-rule: at-least;font-size: 24px;font-weight: 700;line-height: 30px;padding-bottom: 40px;padding-top: 40px;text-align: right;width: 280px;font-family: sans-serif;color: #555'>
                    <div class='logo-right' align='right' id='emb-email-header'><img style='border: 0;-ms-interpolation-mode: bicubic;display: block;max-width: 206px' src='http://up413.siz.co.il/up1/ed0wuimzwjdn.png' alt='' width='200' height='39' /></div>
                  </td>
                </tr>
              </tbody></table>
            </td>
          </tr>
        </tbody></table>

            <table class='border' style='border-collapse: collapse;border-spacing: 0;font-size: 1px;line-height: 1px;background-color: #d4dddb;Margin-right: auto;Margin-left: auto' width='602'>
              <tbody><tr><td style='padding: 0;vertical-align: top'>&#8203;</td></tr>
            </tbody></table>

            <table class='centered' style='border-collapse: collapse;border-spacing: 0;Margin-right: auto;Margin-left: auto'>
              <tbody><tr>
                <td class='border' style='padding: 0;vertical-align: top;font-size: 1px;line-height: 1px;background-color: #d4dddb;width: 1px'>&#8203;</td>
                <td style='padding: 0;vertical-align: top'>
                  <table class='one-col' style='border-collapse: collapse;border-spacing: 0;Margin-right: auto;Margin-left: auto;width: 600px;background-color: #ffffff'>
                    <tbody><tr>
                      <td class='column' style='padding: 0;vertical-align: top;text-align: right'>
                        <div><div class='column-top' style='font-size: 50px;line-height: 50px'>&nbsp;</div></div>
                          <table class='contents' style='border-collapse: collapse;border-spacing: 0;width: 100%'>
                            <tbody><tr>
                              <td class='padded' style='padding: 0;vertical-align: top;padding-right: 50px;padding-left: 50px'>

              <h1 style='Margin-top: 0;color: #3b3e42;font-weight: 400;font-size: 40px;Margin-bottom: 20px;font-family: sans-serif;line-height: 48px'>
              " . $title . "
              </h1>
              <p style='Margin-top: 0;color: #60666d;font-size: 15px;font-family: sans-serif;line-height: 24px;Margin-bottom: 24px'>
              " . $message . "
              </p>

                              </td>
                            </tr>
                          </tbody></table>

                        <div class='column-bottom' style='font-size: 26px;line-height: 26px'>&nbsp;</div>
                      </td>
                    </tr>
                  </tbody></table>
                </td>
                <td class='border' style='padding: 0;vertical-align: top;font-size: 1px;line-height: 1px;background-color: #d4dddb;width: 1px'>&#8203;</td>
              </tr>
            </tbody></table>

            <table class='border' style='border-collapse: collapse;border-spacing: 0;font-size: 1px;line-height: 1px;background-color: #d4dddb;Margin-right: auto;Margin-left: auto' width='602'>
              <tbody><tr><td style='padding: 0;vertical-align: top'>&nbsp;</td></tr>
            </tbody></table>

        <table class='centered' style='border-collapse: collapse;border-spacing: 0;Margin-right: auto;Margin-left: auto'>
          <tbody><tr>
            <td class='footer' style='padding: 0;vertical-align: top;letter-spacing: 0.01em;font-style: normal;line-height: 17px;font-weight: 400;font-size: 11px;Margin-left: auto;Margin-right: auto;padding-top: 50px;padding-bottom: 40px;width: 602px;color: #b9b9b9;font-family: sans-serif'>
              <center>
                <div class='address' style='Margin-bottom: 19px'>Talnet - העתיד כבר כאן.</div>
                <div class='permission' style='Margin-bottom: 10px'>זוהי הודעה אוטומטית ממערכת Talnet - אין להשיב למייל זה.</div>
                <div>
                </div>
              </center>
            </td>
          </tr>
        </tbody></table>
      </center>
</div>

            ", 'text/html');

        $result = $mailer->send($message);

        return $result;
    }
} 
