<?php
/**
 * izicms
 * 
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@izicms.com so we can send you a copy immediately.
 * 
 * @copyright	Copyright (c) 2009-2010 izicms Team (http://www.izicms.vn)
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @version 	$Id: Mail.php 5337 2010-09-07 08:18:38Z hoangpt $
 * @since		2.0.6
 */

class Mail_Models_Dao_Pdo_Mysql_Mail extends Izi_Model_Dao 
	implements Mail_Models_Interface_Mail
{
	public function convert($entity) 
	{
		return new Mail_Models_Mail($entity); 
	}
	
	public function getMails($userId, $offset = null, $count = null)
	{
		$select = $this->_conn
						->select()
						->from(array('m' => $this->_prefix . 'mail'))
						->where('m.created_user_id = ?', $userId);
		if (is_int($offset) && is_int($count)) {
			$select->order('m.mail_id DESC')
					->limit($count, $offset);
		}
		$rs = $select->query()->fetchAll();
		return new Izi_Model_RecordSet($rs, $this);
	}
	
	public function count($userId)
	{
		return $this->_conn
					->select()
					->from(array('m' => $this->_prefix . 'mail'), array('num_mails' => 'COUNT(*)'))
					->where('m.created_user_id = ?', $userId)
					->limit(1)
					->query()
					->fetch()
					->num_mails;
	}
	
	public function add($mail)
	{
		$this->_conn->insert($this->_prefix . 'mail', 
							array(
								'template_id' 	  => $mail->template_id,
								'subject' 		  => $mail->subject,
								'content' 		  => $mail->content,
								'created_user_id' => $mail->created_user_id,
								'from_mail' 	  => $mail->from_mail,
								'from_name' 	  => $mail->from_name,
								'reply_to_mail'   => $mail->reply_to_mail,
								'reply_to_name'   => $mail->reply_to_name,
								'to_mail' 		  => $mail->to_mail,
								'to_name'         => $mail->to_name,
								'status' 		  => $mail->status,
								'created_date'    => $mail->created_date,
								'sent_date' 	  => $mail->sent_date,
							));
		return $this->_conn->lastInsertId($this->_prefix . 'mail');
	}
}
