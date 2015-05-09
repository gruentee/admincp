<?php
/*
 * class.Log.php
 * 
 * Klasse zum Loggen der Aktivitaeten im Admin-Center
 * Loggt Daten wie Referrer, IP-Adresse und Timestamp
 * 
 * Verlangt eine DB-Verbindung (Handle)
 * 
 */
 
class Log
{
    private $IpAdress;      // IP-Adresse des Clients
    private $UserName;      // Username des Clients
    private $PageAddress;   // Adresse der aktuellen Seite
    private $Referrer; 
    private $SessionId;     // Session-ID
    private $Events;        // Array mit Standard-Aktionen des Clients
    
    private $DbConnection;
    private $DbTable = 'log';
    
    public function __construct($cxn)
    {
        if($this->checkDbTable()) // DB-Connection pruefen
        {
            $this->DbConnection = $cxn;
        }
        else
        {
            throw new Exception("Fehlerhafte DB-Verbindung: Tabelle 
                nicht gefunden");
            exit();
        }
        
        $this->UserName = $_SESSION['username'];
        $this->PageAddress = $_SERVER['PHP_SELF'];
        $this->Referrer = $_SERVER['HTTP_REFERER'];
        $this->SessionId = session_id();
        $this->IpAdress = $_SERVER['REMOTE_ADDR']; 
        $this->Events = array(
            'create', 
            'update',
            'delete',
            'list', 
            'login',
            'logout',
        );
    }
    
    private function checkDbTable() 
    {
        $sql = "SHOW TABLES";
        $result = mysql_query($sql);
        $tables = array();
        
        while ($row = mysqli_fetch_fetch_row($result)) {
            $tables[] = $row[0];
        }
        
        if(!in_array($this->DbTable, $tables)) // Tabelle nicht gefunden
        {
            //print_r($tables);
            return false;
        }
        return true;
    }
    
    public function logEvent($event)
    {
        $action = in_array($event, $this->Events) ? $event : False;
        $this->writeLog($action);
    }
    
    public function logVisit() 
    {
        writeLog();
    }
    
    public function logError() 
    {
        writeLog();
    }
    
    private function writeLog($action='')
    {
        $sql = "INSERT INTO log 
                VALUES (timestamp, user_name, url, referrer, 
                        client_address, session_id, action) 
                SET timestamp=`".time()."`, user_name=`$this->UserName`,
                    url=`$this->PageAddress`, referrer=`$this->Referrer`,
                    session_id=`$this->SessionId`, action=`$action`";
        if($query = @mysql_query($sql, $this->DbConnection))
        {
            return true;
        }
        else
        {
            throw new Exception("Log-Eintrag fehlgeschlagen &ndash; DB-Fehler: ".mysql_error($this->DbConnection)."<br />\n".$sql);
        }
    }    
}

?>
