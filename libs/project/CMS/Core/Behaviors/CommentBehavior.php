<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Entity\Comment;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Exception\OrmValidationError;

class CommentBehavior extends ORMBehavior
{

    /**
     * @return ORM|\Delorius\DataBase\Result
     * @throws \Delorius\Exception\Error
     */
    public function getComments()
    {
        return Comment::model()
            ->sort()
            ->whereByTargetId($this->getOwner()->pk())
            ->whereByTargetType($this->getOwner())
            ->find_all();
    }

    /**
     * @param array $value
     * @return array|bool
     * @throws \Delorius\Exception\Error
     */
    public function addComment(array $value)
    {
        if (!$this->getOwner()->loaded()) {
            return false;
        }

        try {
            $comment = new Comment($value[Comment::model()->primary_key()]);
            if($value['delete'] == 1){
                if ($comment->loaded()) {
                    $comment->delete();
                }
                return true;
            }

            $comment->values($value);
            $comment->target_id = $this->getOwner()->pk();
            $comment->target_type = $this->getOwner()->table_name();
            $comment->save(true);
            return $comment->as_array();
        } catch (OrmValidationError $e) {
            return false;
        }

    }

    public function afterDelete(ORM $orm)
    {
        $comments = $this->getComments();
        foreach ($comments as $comment) {
            $comment->delete();
        }
        Comment::model()->cache_delete();
    }

} 