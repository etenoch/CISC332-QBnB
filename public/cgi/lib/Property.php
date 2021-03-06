<?php
include_once "Misc.php";

class Property{

    private static $PROPERTY_BASE_QRY = "SELECT
                                                p.PROPERTY_ID, p.ADDRESS_1, p.ADDRESS_2, p.DISTRICT_ID, d.DISTRICT_NAME,
                                                p.PROPERTY_TYPE_ID, pt.PROPERTY_TYPE_NAME, p.PRICE,
                                                p.NAME as PROPERTY_NAME, p.DESCRIPTION, p.LAT, p.LNG,
                                                m.MEMBER_ID, m.NAME, m.EMAIL, m.PHONE_NUMBER, m.FACULTY_ID, m.DEGREE_TYPE_ID
                                            FROM PROPERTY as p
                                            INNER JOIN MEMBER as m ON m.MEMBER_ID = p.SUPPLIER_MEMBER_ID
                                            INNER JOIN PROPERTY_TYPE as pt ON pt.PROPERTY_TYPE_ID = p.PROPERTY_TYPE_ID
                                            INNER JOIN DISTRICT as d ON d.DISTRICT_ID = p.DISTRICT_ID ";

    public static function getProperty($property_id){
        $db = LolWut::Instance();

        $qry = Property::$PROPERTY_BASE_QRY." WHERE p.PROPERTY_ID = ? AND p.DELETED=0;";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);

        $results = $stm->fetch();
        $results['IMAGES'] = self::getPictures($property_id);
        return $results;
    }

    public static function getMemberProperties($member_id){
        $db = LolWut::Instance();

        $qry = Property::$PROPERTY_BASE_QRY." where m.MEMBER_ID = ? AND p.DELETED=0;";
        $stm = $db->prepare($qry);
        $stm->execute([$member_id]);

        $results = $stm->fetchAll();
        foreach($results as $r){
            $r['IMAGES'] = self::getPictures($r['PROPERTY_ID']);
        }
        return $results;
    }

    public static function getAllProperties(){
        $db = LolWut::Instance();

        $qry = Property::$PROPERTY_BASE_QRY." WHERE p.DELETED=0;";
        $stm = $db->prepare($qry);
        $stm->execute();

        $results = $stm->fetchAll();
        foreach($results as $key => $field){
            $field['IMAGES'] = self::getPictures($field['PROPERTY_ID']);
            $field['FEATURES'] = Feature::getForProperty($field['PROPERTY_ID']);
            $results[$key] = $field;

        }

        return $results;
    }

    public static function getPictures($property_id){
        $db = LolWut::Instance();

        $qry = "SELECT PICTURE_PATH FROM PROPERTY_PICTURE WHERE PROPERTY_ID=?;";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);

        $results = $stm->fetchAll();

        $just_the_links = [];
        foreach($results as $r){
            $just_the_links[] = $r["PICTURE_PATH"];
        }
        return $just_the_links;
    }


    public static function createNewProperty($data, $pictures = [],$features=[]){
        $db = LolWut::Instance();

        $qry = "INSERT INTO PROPERTY (SUPPLIER_MEMBER_ID, ADDRESS_1, ADDRESS_2, DISTRICT_ID, PROPERTY_TYPE_ID,PRICE,NAME,DESCRIPTION,LAT,LNG)
                              VALUES (?,?,?,?,?,?,?,?,?,?)";
        $stm = $db->prepare($qry);
        $stm->execute([$data['SUPPLIER_MEMBER_ID'],$data['ADDRESS_1'],$data['ADDRESS_2'],$data['DISTRICT_ID'],$data['PROPERTY_TYPE_ID'],$data['PRICE'],$data['NAME'],$data['DESCRIPTION'],$data['LAT'],$data['LNG']]);

        $newPropId = $db->lastInsertId();

        foreach ($pictures as $p){
            $qry = "INSERT INTO PROPERTY_PICTURE (PROPERTY_ID, PICTURE_PATH) VALUES (?,?)";
            $stm = $db->prepare($qry);
            $stm->execute([$newPropId,$p]);
        }

        foreach ($features as $f){
            $qry = "INSERT INTO PROPERTY_FEATURE_LINK (PROPERTY_ID, FEATURE_ID) VALUES (?,?)";
            $stm = $db->prepare($qry);
            $stm->execute([$newPropId,$f]);
        }

        return $newPropId;
    }

    public static function updateProperty($property_id,$data,$pictures = [],$features=[]){
        $db = LolWut::Instance();

        $qry = "UPDATE PROPERTY SET
                    ADDRESS_1=?,
                    ADDRESS_2=?,
                    DISTRICT_ID=?,
                    PROPERTY_TYPE_ID=?,
                    PRICE=?,
                    NAME=?,
                    DESCRIPTION=?,
                    LAT=?,
                    LNG=?
               WHERE PROPERTY_ID = ?;";
        $stm = $db->prepare($qry);
        $stm->execute([
            $data['ADDRESS_1'],
            $data['ADDRESS_2'],
            $data['DISTRICT_ID'],
            $data['PROPERTY_TYPE_ID'],
            $data['PRICE'],
            $data['NAME'],
            $data['DESCRIPTION'],
            $data['LAT'],
            $data['LNG'],
            $property_id]);

        // delete all pictures
        $qry = "DELETE FROM PROPERTY_PICTURE WHERE PROPERTY_ID = ?;";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);
        // then reinsert all pictures
        foreach ($pictures as $p){
            $qry = "INSERT INTO PROPERTY_PICTURE (PROPERTY_ID, PICTURE_PATH) VALUES (?,?)";
            $stm = $db->prepare($qry);
            $stm->execute([$property_id,$p]);
        }

        // delete all features
        $qry = "DELETE FROM PROPERTY_FEATURE_LINK WHERE PROPERTY_ID = ?;";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);
        // reinsert pictures
        foreach ($features as $f){
            $qry = "INSERT INTO PROPERTY_FEATURE_LINK (PROPERTY_ID, FEATURE_ID) VALUES (?,?)";
            $stm = $db->prepare($qry);
            $stm->execute([$property_id,$f]);
        }

        return true;
    }


    public static function deleteProperty($property_id){
        $db = LolWut::Instance();
        $qry = "UPDATE PROPERTY SET DELETED=1 WHERE PROPERTY_ID=?;";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);
        return true;
    }


} //  end class Property


class PropertyType {

    public static function getPropertyTypes(){
        $db = LolWut::Instance();
        $qry = "SELECT * FROM PROPERTY_TYPE;";
        $stm = $db->prepare($qry);
        $stm->execute();
        return $stm->fetchAll();
    }




    public static function createPropertyType($pt_name){
        $db = LolWut::Instance();
        $qry = "INSERT INTO PROPERTY_TYPE (PROPERTY_TYPE_NAME) VALUES (?);";
        $stm = $db->prepare($qry);
        $stm->execute([$pt_name]);
        return $db->lastInsertId();
    }


}

