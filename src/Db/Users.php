<?php


namespace Diginize\WpVereinsflieger\Db;

use Diginize\WpVereinsflieger\DbSchema\DbSchema;
use Diginize\WpVereinsflieger\DbSchema\Users as UsersSchema;
use Diginize\WpVereinsflieger\Options;
use Diginize\WpVereinsflieger\VereinsfliegerApi\Model\IUserDto;
use Diginize\WpVereinsflieger\VereinsfliegerApi\Model\UserDto;


class Users extends AbstractDb {

	/**
	 * @param int $uid
	 * @return object|null
	 */
	public static function getUserByVereinsfliegerId(int $uid) {
		return $GLOBALS['wpdb']->get_row($GLOBALS['wpdb']->prepare(
			'SELECT * FROM `' . UsersSchema::getTableName() . '` WHERE `' . UsersSchema::getColumnVfUserId() . '` = %d LIMIT 1',
			$uid
		));
	}

	/**
	 * @param string $email
	 * @return object|null
	 */
	public static function getUserByEmail(string $email) {
		return $GLOBALS['wpdb']->get_row($GLOBALS['wpdb']->prepare(
			'SELECT * FROM `' . UsersSchema::getTableName() . '` WHERE `user_email` = %s LIMIT 1',
			$email
		));
	}

	public static function addVereinsfliegerUser(IUserDto $user) {
		$userLogin = DbSchema::getTablePrefix() . $user->getUid();
		$displayName = $user->getFirstname() . ' ' . $user->getLastname();
		$userId = wp_insert_user([
			'user_pass' => uniqid('wpvf', true),
			'user_login' => $userLogin,
			'user_nicename' => $userLogin,
			'user_url' => '',
			'user_email' => $user->getEmail(),
			'nickname' => $displayName,
			'user_registered' => date('Y-m-d H:i:s'),
			'role' => Options::getDefaultRole()
		]);

		if (is_a($userId, \WP_Error::class)) {
			return null;
		}

		$GLOBALS['wpdb']->update(
			UsersSchema::getTableName(),
			[ UsersSchema::getColumnVfUserId() => $user->getUid() ],
			[ 'ID' => $userId ]
		);

		return $userId;
	}

	public static function updateMetaData(int $userId, IUserDto $user) {
		wp_update_user([
			'ID' => $userId,
			'user_pass' => uniqid('wpvf', true),
			'user_email' => $user->getEmail(),
			'display_name' => $user->getFirstname() . ' ' . $user->getLastname()
		]);
		update_user_meta($userId, 'first_name', $user->getFirstname());
		update_user_meta($userId, 'last_name', $user->getLastname());
		$GLOBALS['wpdb']->update(
			UsersSchema::getTableName(),
			[ UsersSchema::getColumnVfUserId() => $user->getUid() ],
			[ 'ID' => $userId ]
		);
	}

}
