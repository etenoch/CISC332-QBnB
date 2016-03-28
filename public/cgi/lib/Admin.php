<?php

class Admin{

    public static function login($username,$password){
        $db = LolWut::Instance();
        $stm = $db->prepare("SELECT PASSWORD,ADMINISTRATOR_ID FROM ADMINISTRATOR WHERE USERNAME = ?");
        $stm->execute([$username]);

        $results = $stm->fetch();
        if (password_verify($password, $results['PASSWORD'])) {
            return $results['ADMINISTRATOR_ID'];
        } else {
            return -1;
        }
    }

    public static function getAdmin($admin_id){
        $db = LolWut::Instance();
        $stm = $db->prepare("SELECT PASSWORD,ADMINISTRATOR_ID,USERNAME FROM ADMINISTRATOR WHERE ADMINISTRATOR_ID = ?");
        $stm->execute([$admin_id]);

        return $stm->fetch();
    }


    public static function getAllAdmins(){
        $db = LolWut::Instance();
        $stm = $db->prepare("SELECT PASSWORD,ADMINISTRATOR_ID,USERNAME FROM ADMINISTRATOR;");
        $stm->execute();
        return $stm->fetchAll();
    }


    public static function deleteAdmin($admin_id){
        $db = LolWut::Instance();
        $stm = $db->prepare("DELETE FROM ADMINISTRATOR WHERE ADMINISTRATOR_ID=?;");
        $stm->execute([$admin_id]);
        return true;
    }

    public static function createAdmin($username, $password){
        $db = LolWut::Instance();
        $stm = $db->prepare("INSERT INTO ADMINISTRATOR (USERNAME,PASSWORD) VALUES (?,?);");
        $stm->execute([$username,password_hash($password, PASSWORD_DEFAULT)]);
        return true;
    }


}
