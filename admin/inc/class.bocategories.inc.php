<?php
	/**************************************************************************\
	* phpGroupWare - Admin - Global categories                                 *
	* http://www.phpgroupware.org                                              *
	* Written by Bettina Gille [ceb@phpgroupware.org]                          *
	* -----------------------------------------------                          *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: class.bocategories.inc.php,v 1.24 2006/12/19 14:00:33 skwashd Exp $ */
	/* $Source: /sources/phpgroupware/admin/inc/class.bocategories.inc.php,v $ */

	class bocategories
	{
		var $cats;

		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $cat_id;
		var $total;

		var $debug = False;

		function bocategories()
		{
			$appname = phpgw::get_var('appname');
			if ( $appname )
			{
				$this->cats = CreateObject('phpgwapi.categories', -1, $appname);
			}
			else
			{
				$this->cats = CreateObject('phpgwapi.categories', $GLOBALS['phpgw_info']['user']['account_id'], 'phpgw');
			}

			$this->read_sessiondata();

			$start  = phpgw::get_var('start', 'int');
			$query  = phpgw::get_var('query');
			$sort   = phpgw::get_var('sort');
			$order  = phpgw::get_var('order');
			$cat_id = phpgw::get_var('cat_id', 'int');

			if(!empty($start) || $start == 0)
			{
				if($this->debug) { echo '<br>overriding start: "' . $this->start . '" now "' . $start . '"'; }
				$this->start = $start;
			}
			else
			{
				$this->start = 0;
			}

			if((empty($query) && !empty($this->query)) || !empty($query))
			{
				if($this->debug) { echo '<br>setting query to: "' . $query . '"'; }
				$this->query = $query;
			}

			if(isset($cat_id)&& !empty($cat_id))
			{
				$this->cat_id = $cat_id;
			}

			if($cat_id == 0)
			{
//				unset($this->cat_id);
				$this->cat_id ='';
			}

			if(isset($sort) && !empty($sort))
			{
				$this->sort = $sort;
			}

			if(isset($order) && !empty($order))
			{
				$this->order = $order;
			}
		}

		function save_sessiondata($data)
		{
			if($this->debug) { echo '<br>Save:'; _debug_array($data); }
			$GLOBALS['phpgw']->session->appsession('session_data','admin_cats',$data);
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','admin_cats');
			if($this->debug) { echo '<br>Read:'; _debug_array($data); }

			$this->start  = $data['start'];
			$this->query  = $data['query'];
			$this->sort   = $data['sort'];
			$this->order  = $data['order'];
			if(isset($data['cat_id']))
			{
				$this->cat_id = $data['cat_id'];
			}
		}

		function get_list($global_cats=False)
		{
			if($this->debug) { echo '<br>querying: "' . $this->query . '"'; }

			if ($global_cats)
			{
				return $this->cats->return_sorted_array($this->start,True,$this->query,$this->sort,$this->order,True);
			}
			else
			{
				return $this->cats->return_sorted_array($this->start,True,$this->query,$this->sort,$this->order);
			}
		}

		function save_cat($values)
		{
			if ($values['cat_id'] && $values['cat_id'] != 0)
			{
				return $this->cats->edit($values);
			}
			else
			{
				return $this->cats->add($values);
			}
		}

		function exists($data)
		{
			return $this->cats->exists($data);
		}

		function formatted_list($data)
		{
			return $this->cats->formatted_list($data);
		}

		function delete($data)
		{
			$this->cats->delete($data);
		}

		function check_values($values)
		{
			$error = array();
			if (strlen($values['descr']) >= 255)
			{
				$error[] = lang('Description can not exceed 255 characters in length !');
			}

			if (!$values['name'])
			{
				$error[] = lang('Please enter a name');
			}
			else
			{
				if (!$values['parent'])
				{
					$exists = $this->exists(array
					(
						'type'     => 'appandmains',
						'cat_name' => $values['name'],
						'cat_id'   => $values['cat_id']
					));
				}
				else
				{
					$exists = $this->exists(array
					(
						'type'     => 'appandsubs',
						'cat_name' => $values['name'],
						'cat_id'   => $values['cat_id']
					));
				}

				if ($exists == True)
				{
					$error[] = lang('That name has been used already');
				}
			}

			if ( is_array($error)
				&& count($error) )
			{
				return $error;
			}
		}
	}
