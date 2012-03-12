<?php
	/**************************************************************************\
	* phpGroupWare - Registration                                              *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* Modified by Jason Wies (Zone) <zone@users.sourceforge.net>               *
	* Modified by Loic Dachary <loic@gnu.org>                                  *
	* Modified by Sigurd Nes <sigurdne@online.no>                                  *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class soreg
	{
		var $reg_id;
		var $db;
		var $config;

		function __construct()
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$c = createobject('phpgwapi.config','registration');
			$c->read();
			$this->config = $c->config_data;
		}

		function account_exists($account_lid)
		{
			$this->db->transaction_begin();
			$this->db->query("SELECT count(*) as cnt FROM phpgw_reg_accounts WHERE reg_lid='$account_lid'",__LINE__,__FILE__);
			$this->db->next_record();

			if ($GLOBALS['phpgw']->accounts->exists($account_lid) || $this->db->f('cnt'))
			{
				$this->db->transaction_commit();
				return True;
			}
			else
			{
				// To prevent race conditions, reserve the account_lid
				$this->db->query("insert into phpgw_reg_accounts values ('','$account_lid','','" . time() . "')",__LINE__,__FILE__);
				$this->db->transaction_commit();
				$GLOBALS['phpgw']->session->appsession('loginid','registration',$account_lid);
				return False;
			}
		}

		function step2($fields)
		{
			$smtp = createobject('phpgwapi.send');

			$this->reg_id = md5(time() . $account_lid . $GLOBALS['phpgw']->common->randomstring(32));
			$account_lid  = $GLOBALS['phpgw']->session->appsession('loginid','registration');

			$this->db->query("UPDATE phpgw_reg_accounts SET reg_id='" . $this->reg_id . "', reg_dla='"
				. time() . "', reg_info='" . base64_encode(serialize($fields))
				. "' WHERE reg_lid='$account_lid'",__LINE__,__FILE__);

			$GLOBALS['phpgw']->template->set_file(array(
				'message' => 'confirm_email.tpl'
			));

			if ($fields['n_given'])
			{
				$GLOBALS['phpgw']->template->set_var ('firstname', $fields['n_given'] . ' ');
			}

			if ($fields['n_family'])
			{
				$GLOBALS['phpgw']->template->set_var ('lastname', $fields['n_family'] . ' ');
			}


			$url = $GLOBALS['phpgw']->link('/registration/main.php',array('menuaction'=> 'registration.boreg.step4', 'reg_id'=> $this->reg_id),false,true);
			$GLOBALS['phpgw']->template->set_var('activate_url',"</br><a href='$url'>Link.</a></br>");

			if ($this->config['support_email'])
			{
				$GLOBALS['phpgw']->template->set_var ('support_email_text', lang ('Report all problems and abuse to'));
				$GLOBALS['phpgw']->template->set_var ('support_email', $this->config['support_email']);
			}

			$subject = $this->config['subject_confirm'] ? lang($this->config['subject_confirm']) : lang('Account registration');
			$noreply = $this->config['mail_nobody'] ? ('No reply <' . $this->config['mail_nobody'] . '>') : ('No reply <noreply@' . $GLOBALS['phpgw_info']['server']['hostname'] . '>');

			$smtp->msg('email',$fields['email'],$subject,$GLOBALS['phpgw']->template->fp('out','message'),'','','',$noreply,'','html');

			return $this->reg_id;
		}

		//
		// username
		//
		function lostpw1($account_lid)
		{
			$error = '';
			//
			// Remember md5 string sent by mail
			//
			$reg_id = md5(time() . $account_lid . $GLOBALS['phpgw']->common->randomstring(32));
			$this->db->query("INSERT INTO phpgw_reg_accounts VALUES ('$reg_id','$account_lid','','" . time() . "')",__LINE__,__FILE__);

			//
			// Send the mail that will allow to change the password
			//

			$user_id = $GLOBALS['phpgw']->accounts->name2id($account_lid);
			$account_info = $GLOBALS['phpgw']->accounts->get($user_id);

			$contacts = CreateObject('phpgwapi.contacts');

			$qcols = array
			(
				'n_given'    => 'n_given',
				'n_family'   => 'n_family',
				'tel_work'   => 'tel_work',
				'tel_home'   => 'tel_home',
				'tel_cell'   => 'tel_cell',
				'title'      => 'title',
				'email'      => 'email',
				'email_home' => 'email_home',
			);

			$fields = $contacts->are_users($account_info->person_id, $qcols);

			$this->boaddressbook  = CreateObject('addressbook.boaddressbook');
			$comms = $this->boaddressbook->get_comm_contact_data($fields[0]['contact_id']);

			if(is_array($comms) && isset($comms[$fields[0]['contact_id']]) )
			{
				$fields[0]['tel_work'] = $comms[$fields[0]['contact_id']]['work phone'];
				$fields[0]['tel_home'] = $comms[$fields[0]['contact_id']]['home phone'];
				$fields[0]['tel_cell'] = $comms[$fields[0]['contact_id']]['mobile (cell) phone'];
				$fields[0]['email_home'] = $comms[$fields[0]['contact_id']]['home email'];
			}

			$info = array(
				'firstname' => $account_info->firstname,
				'lastname' => $account_info->lastname,
				'email' => $comms[$account_info->person_id]['work email']
			);

			if ($info['email'])
			{
				$smtp = createobject('phpgwapi.send');

				$GLOBALS['phpgw']->template->set_file(array(
					'message' => 'lostpw_email.tpl'
				));

				$url = $GLOBALS['phpgw']->link('/registration/main.php',array('menuaction'=> 'registration.boreg.lostpw2', 'reg_id'=> $reg_id),false,true);
				$GLOBALS['phpgw']->template->set_var('firstname',$info['firstname']);
				$GLOBALS['phpgw']->template->set_var('lastname',$info['lastname']);
				$GLOBALS['phpgw']->template->set_var('activate_url',"</br><a href='$url'>Link.</a></br>");

				$subject = $this->config['subject_lostpw'] ? lang($this->config['subject_lostpw']) : lang('Account password retrieval');
				$noreply = $this->config['mail_nobody'] ? ('No reply <' . $this->config['mail_nobody'] . '>') : ('No reply <noreply@' . $GLOBALS['phpgw_info']['server']['hostname'] . '>');

				$smtp->msg('email',$info['email'],$subject,$GLOBALS['phpgw']->template->fp('out','message'),'','','',$noreply,'', 'html');
			}
			else
			{
				$error = "Account $account_lid record could not be found, report to site administrator";
			}

			return $error;
		}

		//
		// link sent by mail
		//
		function lostpw2($account_lid)
		{
			$this->db->query("SELECT account_id FROM phpgw_accounts WHERE account_lid='$account_lid'",__LINE__,__FILE__);
			$this->db->next_record();
			$account_id = $this->db->f('account_id');

			$GLOBALS['phpgw']->session->appsession('loginid','registration',$account_lid);
			$GLOBALS['phpgw']->session->appsession('id','registration',$account_id);
		}

		//
		// new password
		//
		function lostpw3($account_lid, $passwd)
		{
			$auth = createobject('phpgwapi.auth');
			$auth->change_password('supposed to be old password', $passwd, $GLOBALS['phpgw']->session->appsession('id','registration'));

			$this->db->query("DELETE FROM phpgw_reg_accounts WHERE reg_lid='$account_lid'",__LINE__,__FILE__);
		}

		function valid_reg($reg_id)
		{
			$this->db->query("SELECT * FROM phpgw_reg_accounts WHERE reg_id='$reg_id'",__LINE__,__FILE__);
			$this->db->next_record();

			if ($this->db->f('reg_id'))
			{
				return array(
					'reg_id'   => $this->db->f('reg_id'),
					'reg_lid'  => $this->db->f('reg_lid'),
					'reg_info' => $this->db->f('reg_info'),
					'reg_dla'  => $this->db->f('reg_dla')
				);
			}
			else
			{
				echo False;
			}
		}

		function delete_reg_info($reg_id)
		{
			$this->db->query("DELETE FROM phpgw_reg_accounts WHERE reg_id='$reg_id'",__LINE__,__FILE__);
		}

		function create_account($account_lid,$_reg_info)
		{
			$fields             = unserialize(base64_decode($_reg_info));

			$fields['lid'] 		= "*$account_lid*";

			$default_group_id = $this->config['default_group_id'];
			
			$group_id =  $default_group_id ? $default_group_id : $GLOBALS['phpgw']->accounts->name2id('default');

			if (!$GLOBALS['phpgw']->accounts->exists($account_lid) )
			{	
				$account			= new phpgwapi_user();
				$account->lid		= $account_lid;
				$account->firstname	= $fields['n_given'];
				$account->lastname	= $fields['n_family'];
				$account->passwd	= $fields['passwd'];
				$account->enabled	= true;

				if ($this->config['trial_accounts'])
				{
					$account->expires = time() + ((60 * 60) * ($this->config['days_until_trial_account_expires'] * 24));
				}
				else
				{
					$account->expires = -1;
				}

				$account_id =  $GLOBALS['phpgw']->accounts->create($account, array($group_id), array(), array());
				if($account_id)
				{
					$GLOBALS['phpgw']->log->write(array('text'=>'I-Notification, user created %1','p1'=> $account_lid));
				}
			}

			if (!$account_id)
			{
				return False;
			}

			$contacts   = createobject('phpgwapi.contacts');

			$GLOBALS['phpgw']->db->transaction_begin();

			$contact_fields = $fields;

			if ($contact_fields['bday_day'])
			{
				$contact_fields['bday'] = $contact_fields['bday_month'] . '/' . $contact_fields['bday_day'] . '/' . $contact_fields['bday_year'];
			}

			/* There are certain things we don't want stored in contacts */
			unset ($contact_fields['passwd']);
			unset ($contact_fields['passwd_confirm']);
			unset ($contact_fields['bday_day']);
			unset ($contact_fields['bday_month']);
			unset ($contact_fields['bday_year']);

			/* Don't store blank values either */
			reset ($contact_fields);
			while (list ($num, $field) = each ($contact_fields))
			{
				if (!$contact_fields[$num])
				{
					unset ($contact_fields[$num]);
				}
			}

			$contact_fields['work email'] = $contact_fields['email'];
			$contact_fields['mobile (cell) phone'] = $contact_fields['tel_work'];
			
			$contacts->add($account_id,$contact_fields,0,'P');
			//$contacts->add_person($contact_fieldl, $account_id);
			$GLOBALS['phpgw']->db->transaction_commit();

			if(@stat(PHPGW_SERVER_ROOT . '/messenger/inc/hook_registration.inc.php'))
			{
				include(PHPGW_SERVER_ROOT . '/messenger/inc/hook_registration.inc.php');
			}
		}
	}
