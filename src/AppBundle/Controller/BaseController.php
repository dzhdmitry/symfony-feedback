<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController extends Controller
{
    const DEFAULT_LOCALE = "en";
    const DEFAULT_SORT = "createdAt";
    const DEFAULT_ORDER = "desc";

    /**
     * @param Request $request
     * @return array
     */
    protected static function getSort(Request $request)
    {
        $sort = $request->query->get("sort", self::DEFAULT_SORT);
        $sorts = ["author", "email", self::DEFAULT_SORT];

        return in_array($sort, $sorts, true) ? $sort : self::DEFAULT_SORT;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected static function getOrder(Request $request)
    {
        $direction = $request->query->get("direction", self::DEFAULT_ORDER);
        $directions = ["asc", self::DEFAULT_ORDER];

        return in_array($direction, $directions, true) ? $direction : self::DEFAULT_ORDER;
    }
}
