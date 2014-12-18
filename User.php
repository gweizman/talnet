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
        include('gmail.php');
        $mail_transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
            ->setUsername($GMAIL_USERNAME)
            ->setPassword($GMAIL_PASSWORD);

        $mailer = \Swift_Mailer::newInstance($mail_transport);

        $message = \Swift_Message::newInstance($subject)
            ->setFrom(array('talnet.talpiot@gmail.com' => 'Talnet'))
            ->setTo(array($this->EMAIL))
            ->setBody("
            <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional //EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'><html dir='rtl' lang='he'><head>
    <title></title>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
    <style type='text/css'>
body, div, table, tr, td, span, tbody {
    direction: rtl;
}
body {
  direction: rtl;
  margin: 0;
  mso-line-height-rule: exactly;
  padding: 0;
  min-width: 100%;
}
table {
  border-collapse: collapse;
  border-spacing: 0;
}
td {
  padding: 0;
  vertical-align: top;
}
.spacer,
.border {
  font-size: 1px;
  line-height: 1px;
}
.spacer {
  width: 100%;
}
img {
  border: 0;
  -ms-interpolation-mode: bicubic;
}
.image {
  font-size: 0;
  Margin-bottom: 24px;
}
.image img {
  display: block;
}
.logo {
  mso-line-height-rule: at-least;
}
.logo img {
  display: block;
}
strong {
  font-weight: bold;
}
h1,
h2,
h3,
p,
ol,
ul,
li {
  Margin-top: 0;
}
ol,
ul,
li {
  padding-right: 0;
}
blockquote {
  Margin-top: 0;
  Margin-left: 0;
  Margin-bottom: 0;
  padding-left: 0;
}
.column-top {
  font-size: 50px;
  line-height: 50px;
}
.column-bottom {
  font-size: 26px;
  line-height: 26px;
}
.column {
  text-align: right;
}
.contents {
  width: 100%;
}
.padded {
  padding-right: 50px;
  padding-left: 50px;
}
.wrapper {
  display: table;
  table-layout: fixed;
  width: 100%;
  min-width: 620px;
  -webkit-text-size-adjust: 100%;
  -ms-text-size-adjust: 100%;
}
table.wrapper {
  table-layout: fixed;
}
.one-col,
.two-col,
.three-col {
  Margin-right: auto;
  Margin-left: auto;
  width: 600px;
}
.centered {
  Margin-right: auto;
  Margin-left: auto;
}
.two-col .image {
  Margin-bottom: 21px;
}
.two-col .column-bottom {
  font-size: 29px;
  line-height: 29px;
}
.two-col .column {
  width: 300px;
}
.two-col .first .padded {
  padding-right: 50px;
  padding-left: 25px;
}
.two-col .second .padded {
  padding-right: 25px;
  padding-left: 50px;
}
.three-col .image {
  Margin-bottom: 18px;
}
.three-col .column-bottom {
  font-size: 32px;
  line-height: 32px;
}
.three-col .column {
  width: 200px;
}
.three-col .first .padded {
  padding-right: 50px;
  padding-left: 10px;
}
.three-col .second .padded {
  padding-right: 30px;
  padding-left: 30px;
}
.three-col .third .padded {
  padding-right: 10px;
  padding-left: 50px;
}
.wider {
  width: 400px;
}
.narrower {
  width: 200px;
}
@media only screen and (min-width: 0) {
  .wrapper {
    text-rendering: optimizeLegibility;
  }
}
@media only screen and (max-width: 620px) {
  [class=wrapper] {
    min-width: 320px !important;
    width: 100% !important;
  }
  [class=wrapper] .one-col,
  [class=wrapper] .two-col,
  [class=wrapper] .three-col {
    width: 320px !important;
  }
  [class=wrapper] .column,
  [class=wrapper] .gutter {
    display: block;
    float: right;
    width: 320px !important;
  }
  [class=wrapper] .padded {
    padding-right: 20px !important;
    padding-left: 20px !important;
  }
  [class=wrapper] .block {
    display: block !important;
  }
  [class=wrapper] .hide {
    display: none !important;
  }
  [class=wrapper] .image {
    margin-bottom: 24px !important;
  }
  [class=wrapper] .image img {
    height: auto !important;
    width: 100% !important;
  }
}
.wrapper h1 {
  font-weight: 400;
}
.wrapper h2 {
  font-weight: 700;
}
.wrapper h3 {
  font-weight: 400;
}
.wrapper blockquote p,
.wrapper blockquote ol,
.wrapper blockquote ul {
  font-style: italic;
}
td.border {
  width: 1px;
}
tr.border {
  background-color: #e3e3e3;
  height: 1px;
}
tr.border td {
  line-height: 1px;
}
.sidebar {
  width: 600px;
}
.first.wider .padded {
  padding-right: 50px;
  padding-left: 30px;
}
.second.wider .padded {
  padding-right: 30px;
  padding-left: 50px;
}
.first.narrower .padded {
  padding-right: 50px;
  padding-left: 10px;
}
.second.narrower .padded {
  padding-right: 10px;
  padding-left: 50px;
}
.divider {
  Margin-bottom: 24px;
}
.wrapper h1 {
  font-size: 40px;
  Margin-bottom: 20px;
}
.wrapper h2 {
  font-size: 24px;
  Margin-bottom: 16px;
}
.wrapper h3 {
  font-size: 18px;
  Margin-bottom: 12px;
}
.wrapper a {
  text-decoration: none;
}
.wrapper a:hover {
  border-bottom: 0;
  text-decoration: none;
}
.wrapper h1 a,
.wrapper h2 a,
.wrapper h3 a {
  border: none;
}
.wrapper p,
.wrapper ol,
.wrapper ul {
  font-size: 15px;
}
.wrapper ol,
.wrapper ul {
  Margin-right: 20px;
}
.wrapper li {
  padding-right: 2px;
}
.wrapper blockquote {
  Margin: 0;
  padding-right: 18px;
}
.btn {
  Margin-bottom: 27px;
}
.btn a {
  border: 0;
  border-radius: 4px;
  display: inline-block;
  font-size: 14px;
  font-weight: 700;
  line-height: 21px;
  padding: 9px 22px 8px 22px;
  text-align: center;
  text-decoration: none;
}
.btn a:hover {
  Position: relative;
  top: 3px;
}
.one-col,
.two-col,
.three-col,
.sidebar {
  background-color: #ffffff;
}
.one-col .column table:nth-last-child(2) td h1:last-child,
.one-col .column table:nth-last-child(2) td h2:last-child,
.one-col .column table:nth-last-child(2) td h3:last-child,
.one-col .column table:nth-last-child(2) td p:last-child,
.one-col .column table:nth-last-child(2) td ol:last-child,
.one-col .column table:nth-last-child(2) td ul:last-child {
  Margin-bottom: 24px;
}
.wrapper .two-col .column table:nth-last-child(2) td h1:last-child,
.wrapper .wider .column table:nth-last-child(2) td h1:last-child,
.wrapper .two-col .column table:nth-last-child(2) td h2:last-child,
.wrapper .wider .column table:nth-last-child(2) td h2:last-child,
.wrapper .two-col .column table:nth-last-child(2) td h3:last-child,
.wrapper .wider .column table:nth-last-child(2) td h3:last-child,
.wrapper .two-col .column table:nth-last-child(2) td p:last-child,
.wrapper .wider .column table:nth-last-child(2) td p:last-child,
.wrapper .two-col .column table:nth-last-child(2) td ol:last-child,
.wrapper .wider .column table:nth-last-child(2) td ol:last-child,
.wrapper .two-col .column table:nth-last-child(2) td ul:last-child,
.wrapper .wider .column table:nth-last-child(2) td ul:last-child {
  Margin-bottom: 21px;
}
.wrapper .two-col h1,
.wrapper .wider h1 {
  font-size: 28px;
  Margin-bottom: 18px;
}
.wrapper .two-col h2,
.wrapper .wider h2 {
  font-size: 20px;
  Margin-bottom: 14px;
}
.wrapper .two-col h3,
.wrapper .wider h3 {
  font-size: 17px;
  Margin-bottom: 10px;
}
.wrapper .two-col p,
.wrapper .wider p,
.wrapper .two-col ol,
.wrapper .wider ol,
.wrapper .two-col ul,
.wrapper .wider ul {
  font-size: 13px;
}
.wrapper .two-col blockquote,
.wrapper .wider blockquote {
  padding-right: 16px;
}
.wrapper .two-col .divider,
.wrapper .wider .divider {
  Margin-bottom: 21px;
}
.wrapper .two-col .btn,
.wrapper .wider .btn {
  Margin-bottom: 24px;
}
.wrapper .two-col .btn a,
.wrapper .wider .btn a {
  font-size: 12px;
  line-height: 19px;
  padding: 6px 17px 6px 17px;
}
.wrapper .three-col .column table:nth-last-child(2) td h1:last-child,
.wrapper .narrower .column table:nth-last-child(2) td h1:last-child,
.wrapper .three-col .column table:nth-last-child(2) td h2:last-child,
.wrapper .narrower .column table:nth-last-child(2) td h2:last-child,
.wrapper .three-col .column table:nth-last-child(2) td h3:last-child,
.wrapper .narrower .column table:nth-last-child(2) td h3:last-child,
.wrapper .three-col .column table:nth-last-child(2) td p:last-child,
.wrapper .narrower .column table:nth-last-child(2) td p:last-child,
.wrapper .three-col .column table:nth-last-child(2) td ol:last-child,
.wrapper .narrower .column table:nth-last-child(2) td ol:last-child,
.wrapper .three-col .column table:nth-last-child(2) td ul:last-child,
.wrapper .narrower .column table:nth-last-child(2) td ul:last-child {
  Margin-bottom: 18px;
}
.wrapper .three-col h1,
.wrapper .narrower h1 {
  font-size: 24px;
  Margin-bottom: 16px;
}
.wrapper .three-col h2,
.wrapper .narrower h2 {
  font-size: 18px;
  Margin-bottom: 12px;
}
.wrapper .three-col h3,
.wrapper .narrower h3 {
  font-size: 15px;
  Margin-bottom: 8px;
}
.wrapper .three-col p,
.wrapper .narrower p,
.wrapper .three-col ol,
.wrapper .narrower ol,
.wrapper .three-col ul,
.wrapper .narrower ul {
  font-size: 12px;
}
.wrapper .three-col ol,
.wrapper .narrower ol,
.wrapper .three-col ul,
.wrapper .narrower ul {
  Margin-right: 14px;
}
.wrapper .three-col li,
.wrapper .narrower li {
  padding-right: 1px;
}
.wrapper .three-col blockquote,
.wrapper .narrower blockquote {
  padding-right: 12px;
}
.wrapper .three-col .divider,
.wrapper .narrower .divider {
  Margin-bottom: 18px;
}
.wrapper .three-col .btn,
.wrapper .narrower .btn {
  Margin-bottom: 21px;
}
.wrapper .three-col .btn a,
.wrapper .narrower .btn a {
  font-size: 10px;
  line-height: 16px;
  padding: 5px 17px 5px 17px;
}
.wrapper .wider .column-bottom {
  font-size: 29px;
  line-height: 29px;
}
.wrapper .wider .image {
  Margin-bottom: 21px;
}
.wrapper .narrower .column-bottom {
  font-size: 32px;
  line-height: 32px;
}
.wrapper .narrower .image {
  Margin-bottom: 18px;
}
.header {
  Margin-right: auto;
  Margin-left: auto;
  width: 600px;
}
.header .logo {
  font-size: 24px;
  font-weight: 700;
  line-height: 30px;
  padding-bottom: 40px;
  padding-top: 40px;
  text-align: right;
  width: 280px;
}
.header .logo a {
  text-decoration: none;
}
.header .logo .logo-center {
  text-align: center;
}
.header .logo .logo-center img {
  Margin-right: auto;
  Margin-left: auto;
}
.header .preheader {
  padding-bottom: 40px;
  padding-top: 40px;
  text-align: left;
  width: 280px;
}
.preheader,
.footer {
  letter-spacing: 0.01em;
  font-style: normal;
  line-height: 17px;
  font-weight: 400;
}
.preheader a,
.footer a {
  letter-spacing: 0.03em;
  font-style: normal;
  font-weight: 700;
}
.preheader,
.footer,
.footer .social a {
  font-size: 11px;
}
.footer {
  Margin-left: auto;
  Margin-right: auto;
  padding-top: 50px;
  padding-bottom: 40px;
  width: 602px;
}
.footer table {
  Margin-right: auto;
  Margin-left: auto;
}
.footer .social {
  text-transform: uppercase;
}
.footer .social span {
  mso-text-raise: 4px;
}
.footer .social td {
  padding-bottom: 30px;
  padding-right: 20px;
  padding-left: 20px;
}
.footer .social a {
  display: block;
  transition: opacity 0.2s;
}
.footer .social a:hover {
  opacity: 0.75;
}
.footer .address {
  Margin-bottom: 19px;
}
.footer .permission {
  Margin-bottom: 10px;
}
@media only screen and (max-width: 620px) {
  [class=wrapper] .one-col .column:last-child table:nth-last-child(2) td h1:last-child,
  [class=wrapper] .two-col .column:last-child table:nth-last-child(2) td h1:last-child,
  [class=wrapper] .three-col .column:last-child table:nth-last-child(2) td h1:last-child,
  [class=wrapper] .one-col .column:last-child table:nth-last-child(2) td h2:last-child,
  [class=wrapper] .two-col .column:last-child table:nth-last-child(2) td h2:last-child,
  [class=wrapper] .three-col .column:last-child table:nth-last-child(2) td h2:last-child,
  [class=wrapper] .one-col .column:last-child table:nth-last-child(2) td h3:last-child,
  [class=wrapper] .two-col .column:last-child table:nth-last-child(2) td h3:last-child,
  [class=wrapper] .three-col .column:last-child table:nth-last-child(2) td h3:last-child,
  [class=wrapper] .one-col .column:last-child table:nth-last-child(2) td p:last-child,
  [class=wrapper] .two-col .column:last-child table:nth-last-child(2) td p:last-child,
  [class=wrapper] .three-col .column:last-child table:nth-last-child(2) td p:last-child,
  [class=wrapper] .one-col .column:last-child table:nth-last-child(2) td ol:last-child,
  [class=wrapper] .two-col .column:last-child table:nth-last-child(2) td ol:last-child,
  [class=wrapper] .three-col .column:last-child table:nth-last-child(2) td ol:last-child,
  [class=wrapper] .one-col .column:last-child table:nth-last-child(2) td ul:last-child,
  [class=wrapper] .two-col .column:last-child table:nth-last-child(2) td ul:last-child,
  [class=wrapper] .three-col .column:last-child table:nth-last-child(2) td ul:last-child {
    Margin-bottom: 24px !important;
  }
  [class=wrapper] .header,
  [class=wrapper] .preheader,
  [class=wrapper] .logo,
  [class=wrapper] .footer,
  [class=wrapper] .sidebar {
    width: 320px !important;
  }
  [class=wrapper] .header .logo {
    padding-bottom: 32px !important;
    padding-top: 12px !important;
    padding-right: 10px !important;
    padding-left: 10px !important;
  }
  [class=wrapper] .header .logo img {
    max-width: 280px !important;
    height: auto !important;
  }
  [class=wrapper] .header .preheader {
    padding-top: 3px !important;
    padding-bottom: 22px !important;
  }
  [class=wrapper] .header .title {
    display: none !important;
  }
  [class=wrapper] .header .webversion {
    text-align: center !important;
  }
  [class=wrapper] .footer .address,
  [class=wrapper] .footer .permission {
    width: 280px !important;
  }
  [class=wrapper] h1 {
    font-size: 40px !important;
    Margin-bottom: 20px !important;
  }
  [class=wrapper] h2 {
    font-size: 24px !important;
    Margin-bottom: 16px !important;
  }
  [class=wrapper] h3 {
    font-size: 18px !important;
    Margin-bottom: 12px !important;
  }
  [class=wrapper] .column p,
  [class=wrapper] .column ol,
  [class=wrapper] .column ul {
    font-size: 15px !important;
  }
  [class=wrapper] ol,
  [class=wrapper] ul {
    Margin-right: 20px !important;
  }
  [class=wrapper] li {
    padding-right: 2px !important;
  }
  [class=wrapper] blockquote {
    border-right-width: 4px !important;
    padding-right: 18px !important;
  }
  [class=wrapper] .btn,
  [class=wrapper] .two-col .btn,
  [class=wrapper] .three-col .btn,
  [class=wrapper] .narrower .btn,
  [class=wrapper] .wider .btn {
    Margin-bottom: 27px !important;
  }
  [class=wrapper] .btn a,
  [class=wrapper] .two-col .btn a,
  [class=wrapper] .three-col .btn a,
  [class=wrapper] .narrower .btn a,
  [class=wrapper] .wider .btn a {
    display: block !important;
    font-size: 14px !important;
    letter-spacing: 0.04em !important;
    line-height: 21px !important;
    padding: 9px 22px 8px 22px !important;
  }
  [class=wrapper] table.border {
    width: 320px !important;
  }
  [class=wrapper] .divider {
    margin-bottom: 24px !important;
  }
  [class=wrapper] .column-bottom {
    font-size: 26px !important;
    line-height: 26px !important;
  }
  [class=wrapper] .first .column-bottom,
  [class=wrapper] .second .column-top,
  [class=wrapper] .three-col .second .column-bottom,
  [class=wrapper] .third .column-top {
    display: none;
  }
  [class=wrapper] .social td {
    display: block !important;
    text-align: center !important;
  }
}
@media only screen and (max-width: 320px) {
  td[class=border] {
    display: none;
  }
}
@media (-webkit-min-device-pixel-ratio: 1.5), (min-resolution: 144dpi) {
  .one-col ul {
    border-right: 30px solid #ffffff;
  }
}
</style>
  <meta name='robots' content='noindex,nofollow' />
<meta property='og:title' content='Talnet' />
</head>
  <body style='margin: 0;mso-line-height-rule: exactly;padding: 0;min-width: 100%;background-color: #ecf5f3'><style type='text/css'>
body,.wrapper,.emb-editor-canvas{background-color:#ecf5f3}.border{background-color:#d4dddb}h1{color:#3b3e42}.wrapper h1{}.wrapper h1{font-family:sans-serif}h1{}.one-col h1{line-height:48px}.two-col h1,.wider h1{line-height:36px}.three-col h1,.narrower h1{line-height:30px}@media only screen and (max-width: 620px){h1{line-height:48px !important}}h2{color:#3b3e42}.wrapper h2{}.wrapper h2{font-family:sans-serif}h2{}.one-col h2{line-height:32px}.two-col h2,.wider h2{line-height:26px}.three-col h2,.narrower h2{line-height:24px}@media only screen and (max-width: 620px){h2{line-height:32px !important}}h3{color:#3b3e42}.wrapper h3{}.wrapper h3{font-family:sans-serif}h3{}.one-col h3{line-height:24px}.two-col h3,.wider h3{line-height:23px}.three-col h3,.narrower h3{line-height:21px}@media only screen and (max-width: 620px){h3{line-height:24px !important}}p,ol,ul{color:#60666d}.wrapper p,.wrapper
ol,.wrapper ul{}.wrapper p,.wrapper ol,.wrapper ul{font-family:sans-serif}p,ol,ul{}.one-col p,.one-col ol,.one-col ul{line-height:24px;Margin-bottom:24px}.two-col p,.two-col ol,.two-col ul,.wider p,.wider ol,.wider ul{line-height:21px;Margin-bottom:21px}.three-col p,.three-col ol,.three-col ul,.narrower p,.narrower ol,.narrower ul{line-height:18px;Margin-bottom:18px}@media only screen and (max-width: 620px){p,ol,ul{line-height:24px !important;Margin-bottom:24px !important}}.wrapper a{color:#454545}.wrapper a:hover{color:#2b2b2b !important}.wrapper .btn a{color:#fff;background-color:#444;box-shadow:0 3px 0 #363636}.wrapper .btn a{font-family:sans-serif}.wrapper .btn a:hover{box-shadow:inset 0 1px 2px #363636 !important;color:#fff !important}.wrapper p a,.wrapper ol a,.wrapper ul a{border-bottom:1px dotted #454545}.wrapper blockquote{border-right:4px solid #ecf5f3}.wrapper .three-col
blockquote,.wrapper .narrower blockquote{border-right:2px solid #ecf5f3}.logo{}.wrapper .logo{}.wrapper .logo{font-family:sans-serif}@media only screen and (min-width: 0){.wrapper .logo{font-family:Avenir,sans-serif !important}}.wrapper .logo{color:#555}.wrapper .logo a{color:#555}.wrapper .logo a:hover{color:#555 !important}.preheader,.footer{color:#b9b9b9}.preheader,.footer{font-family:sans-serif}.wrapper .preheader a,.wrapper .footer a{color:#b9b9b9}.wrapper .preheader a:hover,.wrapper .footer a:hover{color:#b9b9b9 !important}.footer .social a{}.wrapper .footer .social a{}.wrapper .footer .social a{font-family:sans-serif}.footer .social a{}.footer .social a{font-weight:600}
</style>
    <center class='wrapper' style='display: table;table-layout: fixed;width: 100%;min-width: 620px;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;background-color: #ecf5f3'>
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

              <h1 style='Margin-top: 0;color: #3b3e42;font-weight: 400;font-size: 40px;Margin-bottom: 20px;font-family: sans-serif;line-height: 48px'>" . $title . "</h1><p style='Margin-top: 0;color: #60666d;font-size: 15px;font-family: sans-serif;line-height: 24px;Margin-bottom: 24px'>" . $message . "</p>

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
              </center>
            </td>
          </tr>
        </tbody></table>
      </center>

</body></html>

            ", 'text/html');

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
