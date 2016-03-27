<?php

class Faculty{

    public static function getFaculties(){
        $db = LolWut::Instance();
        $qry = "SELECT * FROM FACULTY;";
        $stm = $db->prepare($qry);
        $stm->execute();
        return $stm->fetchAll();
    }

    public static function createFaculty($pt_name){
        $db = LolWut::Instance();
        $qry = "INSERT INTO FACULTY (FACULTY_NAME) VALUES (?);";
        $stm = $db->prepare($qry);
        $stm->execute([$pt_name]);
        return $db->lastInsertId();
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


    public static function createDegreeType($pt_name){
        $db = LolWut::Instance();
        $qry = "INSERT INTO DEGREE_TYPE (DEGREE_TYPE_NAME) VALUES (?);";
        $stm = $db->prepare($qry);
        $stm->execute([$pt_name]);
        return $db->lastInsertId();
    }


}

class Feature{

    public static function getFeatures(){
        $db = LolWut::Instance();
        $qry = "SELECT * FROM FEATURE;";
        $stm = $db->prepare($qry);
        $stm->execute();
        return $stm->fetchAll();
    }

    public static function getForProperty($property_id){
        $db = LolWut::Instance();
        $qry = "SELECT * FROM FEATURE as f
                INNER JOIN PROPERTY_FEATURE_LINK as pfl ON pfl.FEATURE_ID = f.FEATURE_ID
                WHERE pfl.PROPERTY_ID = ?;";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);
        return $stm->fetchAll();
    }

    public static function createNewFeature($feature_name){
        $db = LolWut::Instance();
        $qry = "INSERT INTO FEATURE (FEATURE_NAME) VALUES (?);";
        $stm = $db->prepare($qry);
        $stm->execute([$feature_name]);
        return $db->lastInsertId();
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

class Review {

    public static function createReview($member_id,$booking_id,$reply_comment_id,$rating, $comment_text){
        $db = LolWut::Instance();
        $qry = "INSERT INTO COMMENTS (MEMBER_ID, BOOKING_ID, REPLY_COMMENT_ID, RATING, COMENT_TEXT) VALUES (?,?,?,?,?);";
        $stm = $db->prepare($qry);
        $stm->execute([$member_id,$booking_id,$reply_comment_id,$rating, $comment_text]);
        return $db->lastInsertId();
    }

    public static function getPropertyTopLevelReviews($property_id){
        $db = LolWut::Instance();
        $qry = "SELECT
                    c.COMMENT_ID,
                    c.RATING,
                    c.COMENT_TEXT,
                    c.REPLY_COMMENT_ID,
                    c.BOOKING_ID,
                    c.MEMBER_ID,
                    m.NAME
                FROM COMMENTS AS c
                INNER JOIN BOOKING as b ON b.BOOKING_ID =  c.BOOKING_ID
                INNER JOIN MEMBER as m ON m.MEMBER_ID=  c.MEMBER_ID
                WHERE b.PROPERTY_ID = ?;";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);
        return $stm->fetchAll();


    }


}



