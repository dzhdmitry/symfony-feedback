<?php

namespace AppBundle\Util;

use Symfony\Component\HttpFoundation\Request;

class MessagesCriteria
{
    const DEFAULT_SORT = 'createdAt';
    const DEFAULT_DIRECTION = 'desc';

    const FILTER_ALL = 'all';
    const FILTER_APPROVED = 'approved';
    const FILTER_DISAPPROVED = 'disapproved';

    /**
     * @var string
     */
    public $sort;

    /**
     * @var string
     */
    public $direction;

    /**
     * @var string
     */
    public $approved;

    public function __construct($sort, $direction, $approved = self::FILTER_ALL)
    {
        $this->sort = self::filter($sort, ['author', 'email', self::DEFAULT_SORT], self::DEFAULT_SORT);
        $this->direction = self::filter($direction, ['asc', self::DEFAULT_DIRECTION], self::DEFAULT_DIRECTION);
        $this->approved = $approved;
    }

    /**
     * @param string $approved
     * @return MessagesCriteria
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * @param Request $request
     * @return MessagesCriteria
     */
    public static function fromRequest(Request $request)
    {
        $sort = $request->query->get('sort', self::DEFAULT_SORT);
        $direction = $request->query->get('direction', self::DEFAULT_DIRECTION);

        return new self($sort, $direction);
    }

    /**
     * @param $value
     * @param $values
     * @param $default
     * @return mixed
     */
    private static function filter($value, $values, $default)
    {
        return in_array($value, $values, true) ? $value : $default;
    }
}
