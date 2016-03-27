<?php

class District{

    public static function getDistricts(){
        $db = LolWut::Instance();
        $qry = "SELECT * FROM DISTRICT;";
        $stm = $db->prepare($qry);
        $stm->execute();
        return $stm->fetchAll();
    }

    public static function createDistrict($district_name){
        $db = LolWut::Instance();
        $qry = "INSERT INTO DISTRICT (DISTRICT_NAME) VALUES (?);";
        $stm = $db->prepare($qry);
        $stm->execute([$district_name]);
        return $db->lastInsertId();
    }


}