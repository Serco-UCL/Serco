<?php
/*
 * This file is part of the UCL-Serco
 *
 * Copyright (C) 2018 Université de Louvain-la-Neuve (UCL-TICE)
 *
 * Written by
 *        Erin Dupuis   (erin.dupuis@uclouvain.be)
 *        Arnaud Willame (arnaud.willame@uclouvain.be)
 *        Domenico Palumbo (dominique.palumbo@uclouvain.be)
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 3 of
 * the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
 */

/**
 * DBConnection Class is use to manage database access
 *
 * @author Arnaud Willame (arnaud.willame@uclouvain.be)
 */
class DBConnection 
{
    public $dbh;
    public function __construct(){
        $this->getPDOConnection();        
    }     
    
    private function getPDOConnection() {
        global $config;
        
        $DBhost = $config['DBhost'];
        $DBuser = $config['DBuser'];
        $DBpass = $config['DBpass'];
        $DBname = $config['DBname'];
        try{
            $this->dbh= new PDO('mysql:host='.$DBhost.';dbname='.$DBname, $DBuser, $DBpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
        } catch( PDOException $e ) {
                echo __LINE__.$e->getMessage();
        }
    }
        
    /**
     * Destroy the instance of DBconnection class .
     */
    public function __destruct() {
		$this->dbh = NULL;
	}
    
    /**
     * return the array returned by the query .
     *
     * @param String $tableName Name of the table
     * @param Array $params params use in the WHERE argument in th sql request
     * @param String $orderBy Sort the db result by the collumn name specified here
     * @param String $order ASC|DESC 
     * @param String $offset number of the row where the request begin
     * @param String $limit number of row returned
     *
     * @throws PDOException
     *
     * @return Array 
     */
    public function  getDbResult($tableName,$params=array(),$select="*",$orderBy='', $order='ASC', $offset="0", $limit="18446744073709551615"){   
        global $config;
        try { 
            $dbh = $this->dbh;
            $keys=array_keys($params);   
            $where = ' 1 ';
            if(count($keys)>0){
                $where = "$keys[0] like :$keys[0]";
                for($i=1;$i<count($keys);$i++){
                    $where .= " OR $keys[$i] like :$keys[$i] ";
                }
            }
            if($orderBy!='')
                $sql = 'SELECT '.$select.' FROM '.$config['DBprefix'].$tableName.' WHERE '.$where.' ORDER BY '.$orderBy.' '.$order.' LIMIT '.$offset.', '.$limit.' ';
            else
                $sql = 'SELECT '.$select.' FROM '.$config['DBprefix'].$tableName.' WHERE '.$where.' LIMIT '.$offset.', '.$limit.' ';
            
            $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            foreach ($params as $key => $value) {
                 $sth->bindValue($key, '%'.$value.'%', PDO::PARAM_STR);
            }

            $sth->execute();
            return $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $return['status']=-1;
            $return['error']='Database Connection failed: ' . $e->getMessage();
            return $return;
        }       
    }
    
    /**
     * return the number of rows returned by the query .
     *
     * @param String $tableName Name of the table
     * @param Array $params params use in the WHERE argument in th sql request
     *
     * @throws PDOException
     *
     * @return int 
     */
    public function  getNbreturn($tableName,$params=array()){    
        global $config;
        try {  
            $dbh = $this->dbh;
            $keys=array_keys($params);   
            $where=' 1 ';
            if(count($keys)>0){
                $where="$keys[0] like :$keys[0]";
                for($i=1;$i<count($keys);$i++){
                    $where.=" OR $keys[$i] like :$keys[$i] ";
                }
            }

            $sql = 'SELECT COUNT(*) FROM '.$config['DBprefix'].$tableName.' WHERE '.$where;
            $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            foreach ($params as $key => $value) {
                 $sth->bindValue($key, '%'.$value.'%', PDO::PARAM_STR);
            }
            $sth->execute();
            return $sth->fetchColumn(); 
        } catch (PDOException $e) {
            $return['status']=-1;
            $return['error']='Database Connection failed: ' . $e->getMessage();
            return $return;
        }
    }
    
    /**
     * return the number of rows contained in the table .
     *
     * @param String $table Name of the table
     *
     * @throws PDOException
     *
     * @return int 
     */
    public function getNbTableRows($table){
        global $config;
        try {    
            $dbh = $this->dbh;
            $sql = 'SELECT COUNT(*) FROM '.$config['DBprefix'].$table;
            $sth = $dbh->prepare($sql); 
            $sth->execute(); 
            return $sth->fetchColumn(); 
        } catch (PDOException $e) {
            $return['status']=-1;
            $return['error']='Database Connection failed: ' . $e->getMessage();
            return $return;  
        }
    }   
    /**
     * Insert contact message in database .
     *
     * @param String $name Name of the sender
     * @param String $from Mail of the sender
     * @param String $to mail of the receiver
     * @param String subject subject of the emssage
     * @param String message message sent
     *
     * @throws PDOException
     *
     * @return int 
     */
    public function setMessage($name,$from,$to,$subject,$msg){
        global $config;
        try {    
            $dbh = $this->dbh;
            $sql = 'INSERT INTO '.$config['DBprefix'].'Messages (name,sender, receiver,subject,message) VALUES (:name,:sender, :receiver, :subject, :message)';
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':sender', $from);
            $stmt->bindParam(':receiver', $to);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $msg);
            return $stmt->execute(); 
        } catch (PDOException $e) {
            $return['status']=-1;
            $return['error']='Database Connection failed: ' . $e->getMessage();
            return $return;  
        }
    }   
  
}