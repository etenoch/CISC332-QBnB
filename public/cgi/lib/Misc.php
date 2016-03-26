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