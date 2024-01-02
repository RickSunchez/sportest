<?php
namespace CMS\SEO\Model;

use CMS\SEO\Entity\Search as SearchOrm;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Strings;

class Search
{

    /**
     * @param string $query
     * @param string $type
     * @param int $length
     * @return bool
     * @throws \Delorius\Exception\Error
     */
    public static function add($query, $type, $length = 2)
    {
        $aQuery = Strings::parserKeywords($query, $length);
        if (count($aQuery) == 0) {
            return array();
        }

        sort($aQuery, SORT_STRING);
        $sQuery = implode(",", $aQuery);

        try {
            $hash = md5($sQuery);
            $search = SearchOrm::model()
                ->where('hash', '=', $hash)
                ->where('type', '=', $type)
                ->find();
            $search->type = $type;
            $search->query = $query;
            $search->query_str = $sQuery;
            $search->hash = $hash;
            $search->count += 1;
            $search->save();

            return true;
        } catch (OrmValidationError $e) {
            return false;
        }

    }

}