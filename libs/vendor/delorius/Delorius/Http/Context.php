<?php
namespace Delorius\Http;

use Delorius\Core\DateTime;
use Delorius\Core\Object;

/**
 * HTTP-specific tasks.
 * @property-read bool $modified
 * @property-read IRequest $request
 * @property-read IResponse $response
 */
class Context extends Object
{
    /** @var IRequest */
    private $request;

    /** @var IResponse */
    private $response;

    public function __construct(IRequest $request, IResponse $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Attempts to cache the sent entity by its last modification date.
     * @param  string|int|DateTime last modified time
     * @param  string $etag entity tag validator
     * @return bool
     */
    public function isModified($lastModified = NULL, $etag = NULL)
    {
        if ($lastModified) {
            $this->response->setHeader('Last-Modified', Helpers::formatDate($lastModified));
        }
        if ($etag) {
            $this->response->setHeader('ETag', '"' . addslashes($etag) . '"');
        }

        $ifNoneMatch = $this->request->getHeader('If-None-Match');
        if ($ifNoneMatch === '*') {
            $match = TRUE; // match, check if-modified-since

        } elseif ($ifNoneMatch !== NULL) {
            $etag = $this->response->getHeader('ETag');

            if ($etag == NULL || strpos(' ' . strtr($ifNoneMatch, ",\t", '  '), ' ' . $etag) === FALSE) {
                return TRUE;

            } else {
                $match = TRUE; // match, check if-modified-since
            }
        }

        $ifModifiedSince = $this->request->getHeader('If-Modified-Since');
        if ($ifModifiedSince !== NULL) {
            $lastModified = $this->response->getHeader('Last-Modified');
            if ($lastModified != NULL && strtotime($lastModified) <= strtotime($ifModifiedSince)) {
                $match = TRUE;

            } else {
                return TRUE;
            }
        }

        if (empty($match)) {
            return TRUE;
        }

        $this->response->setCode(IResponse::S304_NOT_MODIFIED);
        return FALSE;
    }

    /**
     * @return IRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return IResponse
     */
    public function getResponse()
    {
        return $this->response;
    }
}
