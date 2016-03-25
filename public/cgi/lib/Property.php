<?php

class Property{

    private static $PROPERTY_BASE_QRY = "SELECT
                                                p.PROPERTY_ID, p.ADDRESS_1, p.ADDRESS_2, p.DISTRICT_ID, d.DISTRICT_NAME,
                                                p.PROPERTY_TYPE_ID, pt.PROPERTY_TYPE_NAME, p.PRICE,
                                                p.NAME, p.DESCRIPTION, p.LAT, p.LNG,
                                                m.MEMBER_ID, m.NAME, m.EMAIL, m.PHONE_NUMBER, m.FACULTY_ID, m.DEGREE_TYPE_ID
                                            FROM PROPERTY as p
                                            INNER JOIN MEMBER as m ON m.MEMBER_ID = p.SUPPLIER_MEMBER_ID
                                            INNER JOIN PROPERTY_TYPE as pt ON pt.PROPERTY_TYPE_ID = p.PROPERTY_TYPE_ID
                                            INNER JOIN DISTRICT as d ON d.DISTRICT_ID = p.DISTRICT_ID ";

    public static function getProperty($property_id){
        $db = LolWut::Instance();

        $qry = Property::$PROPERTY_BASE_QRY." WHERE p.PROPERTY_ID = ?;";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);

        return $stm->fetch();
    }

    public static function getMemberProperties($member_id){
        $db = LolWut::Instance();

        $qry = Property::$PROPERTY_BASE_QRY." where m.MEMBER_ID = ?;";
        $stm = $db->prepare($qry);
        $stm->execute([$member_id]);

        return $stm->fetchAll();
    }

    public static function getAllProperties(){
        $db = LolWut::Instance();

        $qry = Property::$PROPERTY_BASE_QRY.";";
        $stm = $db->prepare($qry);
        $stm->execute();

        return $stm->fetchAll();
    }


    public static function createNewProperty($data){
        $db = LolWut::Instance();


        $qry = "INSERT INTO PROPERTY (SUPPLIER_MEMBER_ID, ADDRESS_1, ADDRESS_2, DISTRICT_ID, PROPERTY_TYPE_ID,PRICE,NAME,DESCRIPTION,LAT,LNG)
                              VALUES (?,?,?,?,?,?,?,?,?,?)";
        $stm = $db->prepare($qry);
        $stm->execute([$data['SUPPLIER_MEMBER_ID'],$data['ADDRESS_1'],$data['ADDRESS_2'],$data['DISTRICT_ID'],$data['PROPERTY_TYPE_ID'],$data['PRICE'],$data['NAME'],$data['DESCRIPTION'],$data['LAT'],$data['LNG']]);

        return $db->lastInsertId();
    }





}
