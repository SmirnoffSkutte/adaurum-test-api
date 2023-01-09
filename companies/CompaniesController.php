<?php
require_once 'jwt/JwtService.php';

class CompaniesController extends Controller{
    public function getPublicCompanyByInn(string $inn){
        $companiesService=new CompaniesService();
        $query=$companiesService->getPublicCompanyByInn($inn);
        return $query;
    }

    public function getAllPublicCompanies(){
        $companiesService=new CompaniesService();
        $query=$companiesService->getAllPublicCompanies();
        return $query;
    }

    //User companies

    public function getUserCompanyByInn(string $inn){
        try {
            $userId=$this->identifyUserId();
            if($userId){
                $companiesService=new CompaniesService();
                $query=$companiesService->getUserCompanyByInn($userId,$inn);
                return $query;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            return $e->getMessage();
        }
    }

    public function getAllUserCompanies(){
        try {
            $userId=$this->identifyUserId();
            if($userId){
                $companiesService=new CompaniesService();
                $query=$companiesService->getAllUserCompanies($userId);
                return $query;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            return $e->getMessage();
        }
    }

    public function addUserCompany(){
        try {
            $userId=$this->identifyUserId();
            $body=$this->getArrayedJsonBodyRequest();
            if($userId){
                $companiesService=new CompaniesService();
                $rows=$companiesService->addUserCompany($userId,$body);
                return $rows;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            return $e->getMessage();
        }
    }

    public function deleteUserCompanyByInn(string $inn){
        try {
            $userId=$this->identifyUserId();
            if($userId){
                $companiesService=new CompaniesService();
                $query=$companiesService->deleteUserCompanyByInn($userId,$inn);
                return $query;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            return $e->getMessage();
        }
    }

    public function hidePublicCompanyByInnForUser(string $inn){
        try {
            $userId=$this->identifyUserId();
            if($userId){
                $companiesService=new CompaniesService();
                $query=$companiesService->hidePublicCompanyByInnForUser($userId,$inn);
                return $query;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            return $e->getMessage();
        }
    }

    public function unhidePublicCompanyByInnForUser(string $inn){
        try {
            $userId=$this->identifyUserId();
            if($userId){
                $companiesService=new CompaniesService();
                $query=$companiesService->unhidePublicCompanyByInnForUser($userId,$inn);
                return $query;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            return $e->getMessage();
        }
    }

    public function getAllUserUnhiddenCompanies(){
        try {
            $userId=$this->identifyUserId();
            if($userId){
                $companiesService=new CompaniesService();
                $query=$companiesService->getAllUnhiddenCompaniesForUser($userId);
                return $query;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            return $e->getMessage();
        }
    }

    public function getAllUserHiddenCompanies(){
        try {
            $userId=$this->identifyUserId();
            if($userId){
                $companiesService=new CompaniesService();
                $query=$companiesService->getAllHiddenCompaniesForUser($userId);
                return $query;
            } else {
                throw new Exception('Вы не авторизованы');
            }
        } catch (Exception $e){
            http_response_code(401);
            return $e->getMessage();
        }
    }
}