<?php

class Member{

    public static function login($email,$password){
        $db = LolWut::Instance();
        $stm = $db->prepare("SELECT MEMBER_ID,NAME,EMAIL,PHONE_NUMBER,FACULTY_ID,DEGREE_TYPE_ID,PASSWORD,GRAD_YEAR FROM MEMBER WHERE EMAIL = ? AND DELETED=0;");
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
        $stm = $db->prepare("SELECT
                                    m.MEMBER_ID,m.NAME,m.EMAIL,m.PHONE_NUMBER,m.FACULTY_ID,m.DEGREE_TYPE_ID,m.PASSWORD, m.GRAD_YEAR,
                                    f.FACULTY_NAME, d.DEGREE_TYPE_NAME
                                FROM MEMBER as m
                                INNER JOIN FACULTY as f ON f.FACULTY_ID = m.FACULTY_ID
                                INNER JOIN DEGREE_TYPE as d ON d.DEGREE_TYPE_ID = m.DEGREE_TYPE_ID
                                WHERE MEMBER_ID = ? AND DELETED=0;");
        $stm->execute([$member_id]);

        return $stm->fetch();
    }

    public static function getAllMembers(){
        $db = LolWut::Instance();
        $stm = $db->prepare("SELECT
                                    m.MEMBER_ID,m.NAME,m.EMAIL,m.PHONE_NUMBER,m.FACULTY_ID,m.DEGREE_TYPE_ID,m.PASSWORD, m.GRAD_YEAR,
                                    f.FACULTY_NAME, d.DEGREE_TYPE_NAME
                                FROM MEMBER as m
                                INNER JOIN FACULTY as f ON f.FACULTY_ID = m.FACULTY_ID
                                INNER JOIN DEGREE_TYPE as d ON d.DEGREE_TYPE_ID = m.DEGREE_TYPE_ID
                                WHERE DELETED=0;");
        $stm->execute();

        return $stm->fetchAll();
    }

    public static function update($member_id,$name,$phone,$faculty,$degree_type,$email,$grad_year){
        $db = LolWut::Instance();
        $stm = $db->prepare("UPDATE MEMBER SET NAME = ?, PHONE_NUMBER = ?, FACULTY_ID = ?, DEGREE_TYPE_ID = ?, EMAIL = ?, GRAD_YEAR = ?
                        WHERE MEMBER_ID=?;");
        $stm->execute([$name,$phone,$faculty,$degree_type,$email,$grad_year,$member_id]);

        return true;
    }


    public static function signup($name,$phone,$faculty,$degree_type,$email,$grad_year,$password){
        $db = LolWut::Instance();
        $stm = $db->prepare("INSERT INTO MEMBER (NAME,PHONE_NUMBER,FACULTY_ID,DEGREE_TYPE_ID,EMAIL,GRAD_YEAR,PASSWORD) VALUES (?,?,?,?,?,?,?);");
        $stm->execute([$name,$phone,$faculty,$degree_type,$email,$grad_year,password_hash($password, PASSWORD_DEFAULT)]);

        return $db->lastInsertId();
    }

    public static function deleteMember($member_id){
        $db = LolWut::Instance();
        $qry = "UPDATE MEMBER SET DELETED=1 WHERE MEMBER_ID=?;";
        $stm = $db->prepare($qry);
        $stm->execute([$member_id]);
        return true;
    }


}
