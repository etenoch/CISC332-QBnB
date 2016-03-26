<?php

class Faculty{

    public static function getFaculties(){
        $db = LolWut::Instance();
        $qry = "SELECT * FROM FACULTY;";
        $stm = $db->prepare($qry);
        $stm->execute();
        return $stm->fetchAll();
    }

}

class DegreeType{

    public static function getDegreeTypes(){
        $db = LolWut::Instance();
        $qry = "SELECT * FROM DEGREE_TYPE;";
        $stm = $db->prepare($qry);
        $stm->execute();
        return $stm->fetchAll();
    }

}

class PointOfInterest {

    public static function getForProperty($property_id){
        $db = LolWut::Instance();
        $qry = "SELECT
                    p.PROPERTY_ID,
                    d.DISTRICT_ID,
                    d.DISTRICT_NAME,
                    poi.POINT_OF_INTEREST_ID,
                    poi.POINT_OF_INTEREST_NAME
                FROM POINT_OF_INTEREST AS poi
                INNER JOIN DISTRICT_POINT_OF_INTEREST_LINK as dpi ON dpi.POINT_OF_INTEREST_ID = poi.POINT_OF_INTEREST_ID
                INNER JOIN DISTRICT as d ON d.DISTRICT_ID = dpi.DISTRICT_ID
                INNER JOIN PROPERTY as p ON p.DISTRICT_ID = d.DISTRICT_ID
                WHERE p.PROPERTY_ID = ?;";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);
        return $stm->fetchAll();
    }

}