<?php

namespace Predictator\CoVisitationCounts;


class ResultSet implements \Countable, \Iterator, \ArrayAccess
{
	/**
	 * @var VisitedObjectInterface[]
	 */
	private $visitedObjects = array();

	/**
	 * @param VisitedObjectInterface $result
	 */
	public function addVisitedObject(VisitedObjectInterface $result)
	{
		$this->visitedObjects[] = $result;
	}

	/**
	 * @var int
	 */
	protected $currentIndex = 0;

	/**
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current()
	{
		return array_values($this->visitedObjects)[$this->currentIndex];
	}
	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next()
	{
		$this->currentIndex++;
	}
	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key()
	{
		return array_keys($this->visitedObjects)[$this->currentIndex];
	}
	/**
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid()
	{
		$features = array_values($this->visitedObjects);
		return isset($features[$this->currentIndex]);
	}
	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind()
	{
		$this->currentIndex = 0;
	}
	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists($offset)
	{
		return isset($this->visitedObjects[$offset]);
	}
	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return VisitedObjectInterface
	 * @since 5.0.0
	 */
	public function offsetGet($offset)
	{
		return $this->visitedObjects[$offset];
	}
	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param VisitedObjectInterface $value <p>
	 * The value to set.
	 * </p>
	 * @throws \Exception
	 * @since 5.0.0
	 */
	public function offsetSet($offset, $value)
	{
		if (!$value instanceof VisitedObjectInterface) {
			throw new \Exception('The value must be VisitedObjectInterface');
		}
		if (!is_null($offset)) {
			$this->visitedObjects[$offset] = $value;
			return;
		}
		$this->visitedObjects[] = $value;
	}
	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset($offset)
	{
		if (isset($this->visitedObjects[$offset])) {
			unset($this->visitedObjects[$offset]);
		}
	}

	/**
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 * @since 5.1.0
	 */
	public function count()
	{
		return count($this->visitedObjects);
	}
}