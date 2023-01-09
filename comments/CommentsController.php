<?php

class CommentsController extends Controller{
    public function addCommentForCompany(){
        try{
            $userId=$this->identifyUserId();
            $body=$this->getArrayedJsonBodyRequest();
            if($userId){
                $commentsService=new CommentsService();
                $rows=$commentsService->addCommentForCompany($userId,$body);
                return $rows;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            exit($e->getMessage());
        }
    }

    public function deleteCommentFromCompany(int $commentId){
        try {
            $userId=$this->identifyUserId();
            if($userId){
                $commentsService=new CommentsService();
                $rows=$commentsService->deleteCommentFromCompany($userId,$commentId);
                return $rows;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            exit($e->getMessage());
        }
    }

    public function getAllCompanyComments(int $companyInn){
        try {
            $userId=$this->identifyUserId();
            if($userId){
                $commentsService=new CommentsService();
                $rows=$commentsService->getAllCompanyComments($userId,$companyInn);
                return $rows;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            exit($e->getMessage());
        }
    }
}