<?php

class Booking{

    public static $BASE_QRY = "SELECT
                    b.BOOKING_ID,
                    b.BOOKING_PERIOD,
                    b.BOOKING_STATUS,
                    p.PROPERTY_ID,
                    p.NAME as PROPERTY_NAME,
                    p.ADDRESS_1,
                    p.ADDRESS_2,
                    dt.DISTRICT_NAME,
                    t.PROPERTY_TYPE_NAME,
                    p.PRICE,
                    m.MEMBER_ID,
                    m.EMAIL,
                    m.PHONE_NUMBER,
                    f.FACULTY_NAME,
                    d.DEGREE_TYPE_NAME
                FROM BOOKING AS b
                INNER JOIN MEMBER AS m ON b.CONSUMER_MEMBER_ID = m.MEMBER_ID
                INNER JOIN PROPERTY AS p ON b.PROPERTY_ID = p.PROPERTY_ID
                INNER JOIN FACULTY as f ON m.FACULTY_ID = f.FACULTY_ID
                INNER JOIN DEGREE_TYPE as d ON m.DEGREE_TYPE_ID = d.DEGREE_TYPE_ID
                INNER JOIN PROPERTY_TYPE as t ON p.PROPERTY_TYPE_ID = t.PROPERTY_TYPE_ID
                INNER JOIN DISTRICT as dt ON p.DISTRICT_ID = dt.DISTRICT_ID";


    public static function getAllBookings(){
        $db = LolWut::Instance();
        $qry = Booking::$BASE_QRY.";";
        $stm = $db->prepare($qry);
        $stm->execute();
        return $stm->fetchAll();
    }

    public static function getSupplierBookings($supplier_member_id){
        $db = LolWut::Instance();
        $qry = Booking::$BASE_QRY." WHERE p.SUPPLIER_MEMBER_ID=?;";
        $stm = $db->prepare($qry);
        $stm->execute([$supplier_member_id]);
        return $stm->fetchAll();
    }

    public static function getMemberBookings($member_id){
        $db = LolWut::Instance();
        $qry = Booking::$BASE_QRY." WHERE m.MEMBER_ID=?;";
        $stm = $db->prepare($qry);
        $stm->execute([$member_id]);
        return $stm->fetchAll();
    }


    public static function getPropertyBookings($property_id){
        $db = LolWut::Instance();
        $qry = Booking::$BASE_QRY." WHERE p.PROPERTY_ID=?;";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);
        return $stm->fetchAll();
    }




    public static function checkDate($year, $month, $day, $property_id){
        $db = LolWut::Instance();
        $qry = "SELECT BOOKING_ID, BOOKING_PERIOD
                FROM BOOKING
                WHERE PROPERTY_ID = ? and BOOKING_STATUS != 'cancelled';";
        $stm = $db->prepare($qry);
        $stm->execute([$property_id]);

        $results = $stm->fetchAll();
        $bookings = [];
        foreach($results as $r){
            $unix_start = strtotime($r['BOOKING_PERIOD']);
            $unix_end = strtotime("+7 day",$unix_start);
            $bookings[] = [$unix_start,$unix_end];
        }

        $target_start = strtotime($year."-".$month."-".$day);
        $target_end = strtotime("+7 day",$target_start);

        foreach ($bookings as $b){

            if ($b[0] <= $target_start && $target_start <= $b[1]) return false;
            if ($b[0] <= $target_end && $target_end <= $b[1]) return false;

        }
        return true;
    }


    public static function getBooking($booking_id){
        $db = LolWut::Instance();
        $qry = "SELECT BOOKING_ID, CONSUMER_MEMBER_ID, PROPERTY_ID, BOOKING_PERIOD, BOOKING_STATUS FROM BOOKING WHERE BOOKING_ID=?";
        $stm = $db->prepare($qry);
        $stm->execute([$booking_id]);
        return $stm->fetch();
    }

    public static function createBooking($member_id,$property_id,$booking_period){
        $db = LolWut::Instance();
        $qry = "INSERT INTO BOOKING (CONSUMER_MEMBER_ID, PROPERTY_ID, BOOKING_PERIOD, BOOKING_STATUS) VALUES (?,?,?,?);";
        $stm = $db->prepare($qry);
        $stm->execute([$member_id,$property_id,$booking_period,"requested"]);
        return $db->lastInsertId();
    }


    public static function updateBookingStatus($booking_id,$status){
        $db = LolWut::Instance();
        $qry = "UPDATE BOOKING SET BOOKING_STATUS=? WHERE BOOKING_ID = ?;";
        $stm = $db->prepare($qry);
        $stm->execute([$status,$booking_id]);
        return true;
    }

}
