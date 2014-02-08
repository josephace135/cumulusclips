<?php

class CommentMapper extends MapperAbstract
{
    public function getCommentById($commentId)
    {
        return $this->getCommentByCustom(array('comment_id' => $commentId));
    }
    
    public function getVideoComments($videoId)
    {
        return $this->getMultipleCommentByCustom(array('video_id' => $videoId));
    }

    public function getCommentByCustom(array $params)
    {
        $db = Registry::get('db');
        $query = 'SELECT * FROM ' . DB_PREFIX . 'comments WHERE ';
        
        $queryParams = array();
        foreach ($params as $fieldName => $value) {
            $query .= "$fieldName = :$fieldName AND ";
            $queryParams[":$fieldName"] = $value;
        }
        $query = rtrim($query, ' AND ');
        
        $dbResults = $db->fetchRow($query, $queryParams);
        if ($db->rowCount() == 1) {
            return $this->_map($dbResults);
        } else {
            return false;
        }
    }
    
    public function getMultipleCommentsByCustom(array $params)
    {
        $db = Registry::get('db');
        $query = 'SELECT * FROM ' . DB_PREFIX . 'comments  WHERE ';
        
        $queryParams = array();
        foreach ($params as $fieldName => $value) {
            $query .= "$fieldName = :$fieldName AND ";
            $queryParams[":$fieldName"] = $value;
        }
        $query = rtrim($query, ' AND ');
        $dbResults = $db->fetchAll($query, $queryParams);
        
        $commentsList = array();
        foreach($dbResults as $record) {
            $commentsList[] = $this->_map($record);
        }
        return $commentsList;
    }

    protected function _map($dbResults)
    {
        $comment = new Comment();
        $comment->commentId = $dbResults['comment_id'];
        $comment->userId = $dbResults['user_id'];
        $comment->videoId = $dbResults['video_id'];
        $comment->comments = $dbResults['comments'];
        $comment->dateCreated = date(DATE_FORMAT, strtotime($dbResults['date_created']));
        $comment->status = $dbResults['status'];
        $comment->email = $dbResults['email'];
        $comment->name = $dbResults['name'];
        $comment->website = $dbResults['website'];
        $comment->ip = $dbResults['ip'];
        $comment->userAgent = $dbResults['user_agent'];
        $comment->released = ($dbResults['released'] == 1) ? true : false;
        return $comment;
    }

    public function save(Comment $comment)
    {
        $comment = Plugin::triggerFilter('video.beforeSave', $comment);
        $db = Registry::get('db');
        if (!empty($comment->commentId)) {
            // Update
            Plugin::triggerEvent('video.update', $comment);
            $query = 'UPDATE ' . DB_PREFIX . 'comments SET';
            $query .= ' user_id = :userId, video_id = :videoId, comments = :comments, date_created = :dateCreated, status = :status, email = :email, name = :name, website = :website, ip = :ip, user_agent = :userAgent, released = :released';
            $query .= ' WHERE comment_id = :commentId';
            $bindParams = array(
                ':commentId' => $comment->commentId,
                ':userId' => (!empty($comment->userId)) ? $comment->userId : 0,
                ':videoId' => $comment->videoId,
                ':comments' => $comment->comments,
                ':dateCreated' => date(DATE_FORMAT, strtotime($comment->dateCreated)),
                ':status' => $comment->status,
                ':email' => (!empty($comment->email)) ? $comment->email : null,
                ':name' => (!empty($comment->name)) ? $comment->name : null,
                ':website' => (!empty($comment->website)) ? $comment->website : null,
                ':ip' => (!empty($comment->ip)) ? $comment->ip : null,
                ':userAgent' => (!empty($comment->userAgent)) ? $comment->userAgent : null,
                ':released' => (isset($comment->released) && $comment->released === true) ? 1 : 0,
            );
        } else {
            // Create
            Plugin::triggerEvent('video.create', $comment);
            $query = 'INSERT INTO ' . DB_PREFIX . 'comments';
            $query .= ' (user_id, video_id, comments, date_created, status, email, name, website, ip, user_agent, released)';
            $query .= ' VALUES (:userId, :videoId, :comments, :dateCreated, :status, :email, :name, :website, :ip, :userAgent, :released)';
            $bindParams = array(
                ':userId' => (!empty($comment->userId)) ? $comment->userId : 0,
                ':videoId' => $comment->videoId,
                ':comments' => $comment->comments,
                ':dateCreated' => gmdate(DATE_FORMAT),
                ':status' => (!empty($comment->status)) ? $comment->status : 'new',
                ':email' => (!empty($comment->email)) ? $comment->email : null,
                ':name' => (!empty($comment->name)) ? $comment->name : null,
                ':website' => (!empty($comment->website)) ? $comment->website : null,
                ':ip' => (!empty($comment->ip)) ? $comment->ip : null,
                ':userAgent' => (!empty($comment->userAgent)) ? $comment->userAgent : null,
                ':released' => (isset($comment->released) && $comment->released === true) ? 1 : 0,
            );
        }
            
        $db->query($query, $bindParams);
        $commentId = (!empty($comment->commentId)) ? $comment->commentId : $db->lastInsertId();
        Plugin::triggerEvent('video.save', $commentId);
        return $commentId;
    }
    
    public function getCommentsFromList(array $commentIds)
    {
        $commentList = array();
        if (empty($commentIds)) return $commentList;
        
        $db = Registry::get('db');
        $inQuery = implode(',', array_fill(0, count($commentIds), '?'));
        $sql = 'SELECT * FROM ' . DB_PREFIX . 'comments WHERE comment_id IN (' . $inQuery . ')';
        $result = $db->fetchAll($sql, $commentIds);

        foreach($result as $commentRecord) {
            $commentList[] = $this->_map($commentRecord);
        }
        return $commentList;
    }
    
    public function delete($commentId)
    {
        $db = Registry::get('db');
        $db->query('DELETE FROM ' . DB_PREFIX . 'comments WHERE comment_id = :commentId', array(':commentId' => $commentId));
    }
}