<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Attachment
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Attachment;

defined('_JEXEC') or die();

use Exception;
use Kunena\Forum\Libraries\Collection\Collection;
use Kunena\Forum\Libraries\Error\KunenaError;
use RuntimeException;
use function defined;

/**
 * Class KunenaAttachmentFinder
 *
 * @since   Kunena 5.0
 */
class Finder extends \Kunena\Forum\Libraries\Database\Object\Finder
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $table = '#__kunena_attachments';

	/**
	 * Get log entries.
	 *
	 * @return  array|Collection
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception|void
	 */
	public function find()
	{
		if ($this->skip)
		{
			return [];
		}

		$query = clone $this->query;
		$this->build($query);
		$query->select('a.*');
		$query->setLimit($this->limit, $this->start);
		$this->db->setQuery($query);

		try
		{
			$results = (array) $this->db->loadObjectList('id');
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		$instances = [];

		if (!empty($results))
		{
			foreach ($results as $id => $result)
			{
				$instances[$id] = AttachmentHelper::get($id);
			}
		}

		$instances = new Collection($instances);

		unset($results);

		return $instances;
	}
}
