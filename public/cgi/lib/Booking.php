<?php

class Booking{


    public static function getAllBookings(){
        $db = LolWut::Instance();

        $qry = "SELECT
                    b.BOOKING_ID,
                    from_unixtime(b.BOOKING_PERIOD) as BOOKING_PERIOD_START,
                    b.BOOKING_STATUS,
                    p.PROPERTY_ID,
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
                INNER JOIN DISTRICT as dt ON p.DISTRICT_ID = dt.DISTRICT_ID;";

        $stm = $db->prepare($qry);
        $stm->execute();

        return $stm->fetchAll();

    }

}
