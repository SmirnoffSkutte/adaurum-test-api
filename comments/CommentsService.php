<?php

class CommentsService {
    public function addCommentForCompany(int $userId,array $body){
        $db=new Database();
        $companyInn=$body['companyInn'];
        $comment=$body['comment'];
        $commentField=$body['commentField'];
        $insert=$db->query("INSERT INTO comments (companyInn,userId,comment,commentField) VALUES (
        :companyInn,:userId,:comment,:commentField)",[
            'companyInn'=>$companyInn,
            'userId'=>$userId,
            'comment'=>$comment,
            'commentField'=>$commentField
        ]);
        return $insert;
    }

    public function deleteCommentFromCompany(int $userId,int $commentId){
        $db=new Database();
        $rows=$db->query("DELETE FROM comments WHERE commentId=$commentId AND userId=$userId ");
        if ($rows===0){
            http_response_code(404);
            exit('Не удалось удалить комментарий');
        }
        return $rows;
    }

    public function getAllCompanyComments(int $userId,int $companyInn){
        $db=new Database();
        $data=$db->query("SELECT * FROM comments WHERE userId=$userId AND companyInn=$companyInn");
        return json_encode($data);
    }
}