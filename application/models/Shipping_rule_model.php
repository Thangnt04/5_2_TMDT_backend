<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_rule_model extends MY_Model {
	var $table = 'shipping_fee_rules';

	function get_list($input = array())
	{
		$input['order'] = array('id', 'ASC');
		return parent::get_list($input);
	}
}