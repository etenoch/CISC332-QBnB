<?php

class Member{

    public static function login($email,$password){
        $db = LolWut::Instance();
        $stm = $db->prepare("SELECT MEMBER_ID,NAME,EMAIL,PHONE_NUMBER,FACULTY_ID,DEGREE_TYPE_ID,PASSWORD FROM MEMBER WHERE EMAIL = ?");
        $stm->execute([$email]);

        $results = $stm->fetch();
        if (password_verify($password, $results['PASSWORD'])) {
            return $results['MEMBER_ID'];
        } else {
            return -1;
        }
    }

    public static function getMember($member_id){
        $db = LolWut::Instance();
        $stm = $db->prepare("SELECT MEMBER_ID,NAME,EMAIL,PHONE_NUMBER,FACULTY_ID,DEGREE_TYPE_ID,PASSWORD FROM MEMBER WHERE MEMBER_ID = ?");
        $stm->execute([$member_id]);

        return $stm->fetch();
    }

    public static function signup($name,$phone,$faculty,$degree_type,$email,$password){
        $db = LolWut::Instance();
        $stm = $db->prepare("INSERT INTO MEMBER (NAME,PHONE_NUMBER,FACULTY_ID,DEGREE_TYPE_ID,EMAIL,PASSWORD) VALUES (?,?,?,?,?,?);");
        $stm->execute([$name,$phone,$faculty,$degree_type,$email,password_hash($password, PASSWORD_DEFAULT)]);

        return $db->lastInsertId();
    }



}
