<?php

class District{

    public static function getDistricts(){
        $db = LolWut::Instance();
        $qry = "SELECT * FROM DISTRICT;";
        $stm = $db->prepare($qry);
        $stm->execute();
        return $stm->fetchAll();
    }

}