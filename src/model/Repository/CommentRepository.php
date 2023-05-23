<?php

namespace Application\model\Repository;

use Application\Lib\DatabaseConnection;
use Application\Model\Comment;
use stdClass;

class CommentRepository extends AbstractRepository
{
    private function fetchComment($row): ?Comment
    {
        $comment = new Comment();
        $comment->identifier = $row['id'];
        $comment->userId = $row['user_id'];
        $comment->postId = $row['post_id'];
        $comment->comment = $row['content'];
        $comment->creationDate = $row['creation_date'];
        $comment->status = $row['status'];

        return $comment;
    }

    public function getComments(string $post): array
    {
        $statement = $this->connection->prepare(
            "SELECT id, user_id, content, status, DATE_FORMAT(creation_date, '%d/%m/%Y à %H:%i:%s') AS creation_date, post_id FROM comments WHERE post_id = ? ORDER BY creation_date DESC"
        );

        $statement->execute([$post]);
        $comments = [];

        while (($row = $statement->fetch())) {
            $comment = $this->fetchComment($row);
            $comments[] = $comment;
        }

        return $comments;
    }

    public function getHiddenComments(): array
    {
        $statement = $this->connection->prepare(
            "SELECT id, user_id, post_id, content, status, DATE_FORMAT(creation_date, '%d/%m/%Y à %H:%i:%s') AS creation_date FROM comments WHERE status = 0 ORDER BY creation_date DESC"
        );

        $statement->execute();
        $comments = [];

        while (($row = $statement->fetch())) {
            $comment = $this->fetchComment($row);
            $comments[] = $comment;
        }

        return $comments;
    }

    public function getHiddenCommentsFromId(int $identifier): array
    {
        $statement = $this->connection->prepare(
            "SELECT id, user_id, post_id, content, status, DATE_FORMAT(creation_date, '%d/%m/%Y à %H:%i:%s') AS creation_date FROM comments WHERE status = 0 AND post_id = ? ORDER BY creation_date DESC"
        );

        $statement->execute([$identifier]);
        $comments = [];

        while (($row = $statement->fetch())) {
            $comment = $this->fetchComment($row);
            $comments[] = $comment;
        }

        return $comments;
    }

    public function getComment(int $identifier): ?Comment
    {
        $statement = $this->connection->prepare(
            "SELECT id, user_id, content, status, DATE_FORMAT(creation_date, '%d/%m/%Y à %H:%i:%s') AS creation_date, post_id FROM comments WHERE id = ?"
        );

        $statement->execute([$identifier]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $this->fetchComment($row);
    }

    public function createComment(int $postId, string $comment, int $userId, int $status): bool
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments(post_id, content, user_id, status, creation_date) VALUES(?, ?, ?, ?, NOW())'
        );

        $affectedLines = $statement->execute([$postId, $comment, $userId, $status]);

        return ($affectedLines > 0);
    }

    public function updateComment(int $identifier, string $comment): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE comments SET content = ?, status = false WHERE id = ?'
        );

        $affectedLines = $statement->execute([$comment, $identifier]);

        return ($affectedLines > 0);
    }

    public function deleteComment(int $identifier): bool
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comments WHERE id = ?'
        );

        $affectedLines = $statement->execute([$identifier]);

        return ($affectedLines > 0);
    }

    public function hideComment(int $identifier): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE comments SET status = false WHERE id = ?'
        );

        $affectedLines = $statement->execute([$identifier]);

        return ($affectedLines > 0);
    }

    public function showComment(int $identifier): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE comments SET status = true WHERE id = ?'
        );
        
        $affectedLines = $statement->execute([$identifier]);

        return ($affectedLines > 0);
    }
}
